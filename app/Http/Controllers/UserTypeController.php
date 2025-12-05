<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserType;

class UserTypeController extends Controller
{
    public function index()
    {
        $userTypes = UserType::all();
        return response()->json($userTypes);
    }
}
