<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Organization;
use App\Models\OrgJoinRequest;
use Illuminate\Http\Request;
use App\Traits\ApiResponse;
use Illuminate\Support\Facades\DB;

class OrgRegistrationController extends Controller
{
    use ApiResponse;

    public function getMyStatus(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return $this->errorResponse('Chưa xác thực', 401);
        }

        // tổ chức đã tham gia (approved & nằm trong bảng user_orgs)
        $joinedOrgIds = $user->organizations()->pluck('organizations.id');

        // yêu cầu đang chờ duyệt
        $pendingOrgIds = OrgJoinRequest::where('user_id', $user->id)
            ->where('status', 'pending')
            ->pluck('org_id');

        // yêu cầu bị từ chối
        $rejectedOrgIds = OrgJoinRequest::where('user_id', $user->id)
            ->where('status', 'rejected')
            ->pluck('org_id');

        // khóa loại exclusive
        $exclusiveMap = [];
        foreach ($user->organizations()->with('type')->get() as $org) {
            if ($org->type && $org->type->is_exclusive == 1) {
                $exclusiveMap[$org->type->id] = $org->id;
            }
        }

        return $this->successResponse([
            'joined_org_ids'    => $joinedOrgIds->toArray(),
            'pending_org_ids'   => $pendingOrgIds->toArray(),
            'rejected_org_ids'  => $rejectedOrgIds->toArray(),   // 🔥 bổ sung bắt buộc
            'exclusive_map'     => $exclusiveMap,
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

    public function getPendingRequests($orgId)
    {
        $requests = OrgJoinRequest::with('user')
            ->where('org_id', $orgId)
            ->where('status', 'pending')
            ->get();

        return $this->successResponse($requests, "Danh sách chờ duyệt");
    }

    public function approveRequest($requestId)
    {
        $request = OrgJoinRequest::findOrFail($requestId);

        DB::transaction(function () use ($request) {
            $request->update(['status' => 'approved']);

            DB::table('user_orgs')->insertOrIgnore([
                'user_id' => $request->user_id,
                'org_id'  => $request->org_id
            ]);
        });

        return $this->successMessage('Duyệt thành công');
    }

    public function rejectRequest($requestId)
    {
        $request = OrgJoinRequest::findOrFail($requestId);
        $request->update(['status' => 'rejected']);

        return $this->successMessage('Đã từ chối yêu cầu');
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,approved,rejected'
        ]);

        $join = OrgJoinRequest::findOrFail($id);

        if ($join->status === 'approved') {
            return response()->json(['message' => 'Approved request cannot be modified'], 400);
        }

        $join->update(['status' => $request->status]);

        // Nếu approved → gán user vào tổ chức
        if ($request->status === 'approved') {
            DB::table('user_orgs')->updateOrInsert([
                'user_id' => $join->user_id,
                'org_id'  => $join->org_id
            ]);
        }

        return response()->json([
            'message' => 'Status updated successfully',
            'data'    => $join
        ]);
    }
}
