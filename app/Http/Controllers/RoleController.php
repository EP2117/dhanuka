<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Role;

class RoleController extends Controller
{
    public function index()
    {
        $data = Role::orderBy('role_name', 'ASC')->get();
        return response(compact('data'), 200);
    }
}
