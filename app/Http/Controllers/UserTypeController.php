<?php

namespace App\Http\Controllers;

use App\Models\UserType;
use App\Traits\ApiResponse;

class UserTypeController extends Controller
{
    use ApiResponse;
    public function index()
    {
        $types = UserType::all();
        return $this->successResponse($types, 'Danh sách loại người dùng');
    }
}
