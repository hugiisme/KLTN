<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\OrgLevel;

class OrgLevelController extends Controller
{
    public function index()
    {
        return response()->json(OrgLevel::all());
    }
}
