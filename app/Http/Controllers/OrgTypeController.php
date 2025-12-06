<?php

namespace App\Http\Controllers;

use App\Models\OrgType;
use App\Traits\ApiResponse;

class OrgTypeController extends Controller
{
    use ApiResponse;

    public function index()
    {
        $types = OrgType::all();
        return $this->successResponse($types, 'Danh sách loại tổ chức');
    }
}
