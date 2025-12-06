<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Organization;
use App\Models\OrgJoinRequest;
use Illuminate\Http\Request;
use App\Traits\ApiResponse;

class OrgRegistrationController extends Controller
{
    use ApiResponse;

    public function getMyStatus(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return $this->errorResponse('Chưa xác thực', 401);
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

        return $this->successResponse([
            'joined_org_ids' => $joinedOrgs->pluck('id')->toArray(),
            'pending_org_ids' => $pendingOrgIds->toArray(),
            'exclusive_map' => $exclusiveMap,
        ], 'Trạng thái tổ chức của bạn');
    }

    public function sendRequest(Request $request)
    {
        $user = $request->user();

        $data = $request->validate([
            'org_id' => 'required|exists:organizations,id',
            'remark' => 'nullable|string|max:500',
        ]);

        $orgId = $data['org_id'];

        $isJoined = $user->organizations()->where('organizations.id', $orgId)->exists();
        if ($isJoined) {
            return $this->errorResponse('Bạn đã là thành viên của tổ chức này', 422);
        }

        $isPending = OrgJoinRequest::where('user_id', $user->id)
            ->where('org_id', $orgId)
            ->where('status', 'pending')
            ->exists();

        if ($isPending) {
            return $this->errorResponse('Bạn đã gửi yêu cầu, vui lòng chờ duyệt', 422);
        }

        $targetOrg = Organization::with('type')->find($orgId);

        if ($targetOrg->type && $targetOrg->type->is_exclusive) {
            $hasConflict = $user->organizations()
                ->where('organizations.id', '!=', $orgId)
                ->whereHas('type', function ($query) use ($targetOrg) {
                    $query->where('id', $targetOrg->type->id);
                })
                ->exists();

            if ($hasConflict) {
                return $this->errorResponse(
                    'Bạn chỉ được tham gia 1 tổ chức thuộc loại ' . $targetOrg->type->name,
                    422
                );
            }
        }

        OrgJoinRequest::create([
            'user_id' => $user->id,
            'org_id' => $orgId,
            'remark' => $data['remark'],
            'status' => 'pending'
        ]);

        return $this->successMessage('Gửi yêu cầu thành công!', 201);
    }
}
