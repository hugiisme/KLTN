<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Organization;
use App\Models\OrgJoinRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrgRegistrationController extends Controller
{

    public function getMyStatus(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $joinedOrgs = $user->organizations()->with('type')->get();

        $pendingOrgIds = OrgJoinRequest::query()
            ->where('user_id', $user->id)
            ->where('status', 'pending')
            ->pluck('org_id');

        $exclusiveMap = [];
        foreach ($joinedOrgs as $org) {
            if ($org->type && $org->type->is_exclusive) {
                $exclusiveMap[$org->type->id] = $org->id;
            }
        }

        return response()->json([
            'joined_org_ids' => $joinedOrgs->pluck('id')->toArray(),
            'pending_org_ids' => $pendingOrgIds->toArray(),
            'exclusive_map' => $exclusiveMap,
        ]);
    }

    public function sendRequest(Request $request)
    {
        $request->validate([
            'org_id' => 'required|exists:organizations,id',
            'remark' => 'required|string|max:500',
        ]);

        $user = $request->user();
        $orgId = $request->org_id;


        $isJoined = $user->organizations()->where('organizations.id', $orgId)->exists();
        if ($isJoined) {
            return response()->json(['message' => 'Bạn đã là thành viên của tổ chức này.'], 422);
        }

        $isPending = OrgJoinRequest::where('user_id', $user->id)
            ->where('org_id', $orgId)
            ->where('status', 'pending')
            ->exists();

        if ($isPending) {
            return response()->json(['message' => 'Bạn đã gửi yêu cầu, vui lòng chờ duyệt.'], 422);
        }

        $targetOrg = Organization::with('type')->find($orgId);

        if ($targetOrg->type && $targetOrg->type->is_exclusive) {
            $hasConflict = $user->organizations()
                ->where('organizations.id', '!=', $orgId) // Khác tổ chức đang xin vào
                ->whereHas('type', function ($query) use ($targetOrg) {
                    $query->where('id', $targetOrg->type->id); // Cùng loại
                })
                ->exists();

            if ($hasConflict) {
                return response()->json([
                    'message' => 'Bạn chỉ được tham gia 1 tổ chức thuộc loại ' . $targetOrg->type->name
                ], 422);
            }
        }

        OrgJoinRequest::create([
            'user_id' => $user->id,
            'org_id' => $orgId,
            'remark' => $request->remark,
            'status' => 'pending'
        ]);

        return response()->json(['message' => 'Gửi yêu cầu thành công!']);
    }
}
