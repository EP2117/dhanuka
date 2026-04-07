<?php

namespace App\Http\Controllers\Permission;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Session;
// use App\Services\PermissionService;
use App\Setting;
use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

// use Illuminate\Support\Facades\Session;

class SettingController extends Controller
{
    // protected $permissionService;
    // public function __construct(PermissionService $permissionService)
    // {
    //     $this->permissionService = $permissionService;
    // }
    public function index()
    {
        // return $this->permissionService->checkPermission('maintenance_mode', function () {
        if (Auth::user()->role->role_name == 'system') {
            $current = "settings";
            $setting = Setting::latest()->first();
            return view('permissions.settings.index', compact('current', 'setting'));
        }
        // });
    }

    public function update(Request $request)
    {
        // return $this->permissionService->checkPermission('maintenance_mode', function () use ($request) {
        if (Auth::user()->role->role_name == 'system') {
            $setting = Setting::latest()->first();
            $setting->update($request->all());

            if ($setting->status === "ACTIVATED") {
                $this->logoutAllUserSessions();
                return redirect()->route('login');
            }

            return redirect()->route('settings.index')->with('success', 'Settings updated successfully');
        }
        // });
    }

    private function logoutAllUserSessions()
    {
        try {
            Session::query()->delete();
            Log::info("Maintenance mode activated. All sessions cleared.");
            return redirect()->route('login');
        } catch (\Exception $e) {
            Log::error("Failed to logout all users: " . $e->getMessage());
            return 0;
        }
    }
}
