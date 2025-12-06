<?php

namespace App\Http\Controllers;

use App\Models\OrgLevel;
use App\Traits\ApiResponse;

class OrgLevelController extends Controller
{
    use ApiResponse;

    public function index()
    {
        $levels = OrgLevel::all();
        return $this->successResponse($levels, 'Danh sách cấp bậc tổ chức');
    }
}
