<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\OrgType;

class OrgTypeController extends Controller
{
    public function index()
    {
        return response()->json(OrgType::all());
    }
}
