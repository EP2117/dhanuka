<?php
namespace App\Services;

use App\User;
use Closure;
use Illuminate\Support\Facades\Auth;

class PermissionService
{
    /**
     * Check if user has permission
     * @param string $permission
    */

    public function checkPermission(string $permission, callable $callback)
    {
        if(User::find(Auth::id())->hasPermission($permission)){
            return $callback();
        }
        return abort(403, 'You do not have permission to access this resource');
    }
}