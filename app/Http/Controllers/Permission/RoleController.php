<?php

namespace App\Http\Controllers\Permission;

use App\Department;
use App\Http\Controllers\Controller;
use App\Module;
use App\Permission;
use App\Role;
use App\Services\PermissionService;
use App\SubModule;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    protected $permissionService;
    public function __construct(PermissionService $permissionService)
    {
        $this->permissionService = $permissionService;
    }
    public function index(Request $request)
    {
        return $this->permissionService->checkPermission('role_lists', function () use($request) {
            $current = "roles";
            $query = Role::query();
            if($request->role_name){
                $query->where('role_name', 'like', '%'.$request->role_name.'%');
            }
            $lists = $query->latest()->paginate(5);
            return view('permissions.roles.index', compact('current', 'lists'));
        });
    }

    public function create()
    {
        return $this->permissionService->checkPermission('role_create', function () {
            $departments = Department::active()->get();
            return view('permissions.roles.create', compact('departments'));
        });
    }

    public function store(Request $request)
    {
        return $this->permissionService->checkPermission('role_create', function () use ($request) {
            $request->validate([
                'role_name' => 'required|unique:roles,role_name',
                'department_id' => 'required'
            ], [
                'department_id.required' => 'The department field is required.'
            ]);
            Role::create($request->all());
            return redirect()->route('roles.index')->with('success', 'Role created successfully');
        });
    }

    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        return $this->permissionService->checkPermission('role_update', function () use ($id) {
            $departments = Department::active()->get();
            $role = Role::find($id);
            if (!$role) {
                return redirect()->route('roles.index')->with('error', 'Role not found');
            }
            return view('permissions.roles.edit', compact('departments', 'role'));
        });
    }

    public function update(Request $request, $id)
    {
        return $this->permissionService->checkPermission('role_update', function () use ($request, $id) {
            $role = Role::find($id);
            $request->validate([
                'role_name' => 'required|unique:roles,role_name,' . $id,
                'department_id' => 'required'
            ], [
                'department_id.required' => 'The department field is required.'
            ]);
            if (!$role) {
                return redirect()->route('roles.index')->with('error', 'Role not found');
            }
            $role->update($request->all());
            return redirect()->route('roles.index')->with('success', 'Role updated successfully');
        });
    }

    public function statusChange($id)
    {
        return $this->permissionService->checkPermission('role_status_change', function () use ($id) {
            $role = Role::find($id);
            if (!$role) {
                return redirect()->route('roles.index')->with('error', 'Role not found');
            }
            $role->status = $role->status == "ACTIVATED" ? "DEACTIVATED" : "ACTIVATED";
            $role->save();
            return redirect()->back()->with('success', 'Role status updated successfully');
        });
    }

    public function destroy($id)
    {
        return $this->permissionService->checkPermission("role_delete", function () use ($id) {
            $role = Role::find($id);
            if (!$role) {
                return redirect()->route('roles.index')->with('error', 'Role not found');
            }
            $role->delete();
            return redirect()->route('roles.index')->with('success', 'Role deleted successfully');
        });
    }

    public function addPermissions(Request $request, $id)
    {
        $modules = Module::active()->get();
        $role = Role::find($id);
        $rolePermissions = $role->permissions->pluck('id')->toArray();
        if (!$role) {
            return redirect()
                ->route('roles.index')
                ->with('error', 'Role not found');
        }
        return view("permissions.roles.addPermissions", compact("role", "rolePermissions", "modules"));
    }

    public function storePermissions(Request $request, $id)
    {
        $role = Role::find($id);
        if (!$role) {
            return redirect()
                ->route('roles.index')
                ->with('error', 'Role not found');
        }

        $request->validate([
            'permissions' => 'required|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        // Sync permissions (adds + removes as needed)
        $role->permissions()->sync($request->permissions);

        return redirect()
            ->route('roles.index')
            ->with('success', 'Permissions updated successfully');
    }
}
