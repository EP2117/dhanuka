<?php

namespace App\Http\Controllers\Permission;

use App\Department;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\PermissionService;

class DepartmentController extends Controller
{
    protected $permissionService;
    public function __construct(PermissionService $permissionService)
    {
        $this->permissionService = $permissionService;
    }
    public function index()
    {
        return $this->permissionService->checkPermission('department_lists', function () {
            $current = "departments";
            $lists = Department::all();
            return view('permissions.departments.index', [
                'lists' => $lists,
                'current' => $current
            ]);
        });
    }

    public function create()
    {
        return $this->permissionService->checkPermission('department_create', function () {
            return view('permissions.departments.create');
        });
    }

    public function store(Request $request)
    {
        return $this->permissionService->checkPermission('department_create', function () use ($request) {
            $request->validate([
                'name' => 'required|unique:departments,name'
            ]);
            Department::create($request->all());
            return redirect()->route('departments.index')->with('success', 'Department created successfully');
        });
    }

    public function edit($id)
    {
        return $this->permissionService->checkPermission('department_update', function () use ($id) {
            $department = Department::find($id);
            return view('permissions.departments.edit', [
                "department" => $department
            ]);
        });
    }

    public function update(Request $request, $id)
    {
        return $this->permissionService->checkPermission('department_update', function () use ($request, $id) {
            $request->validate([
                'name' => 'required|unique:departments,name',
            ]);
            $department = Department::find($id);
            if (!$department) {
                return redirect()->route('departments.index')->with('error', 'Department not found');
            }
            $department->update($request->all());
            return redirect()->route('departments.index')->with('success', 'Department updated successfully');
        });
    }

    public function statusChange($id)
    {
        return $this->permissionService->checkPermission('department_status_change', function () use ($id) {
            $department = Department::find($id);
            if (!$department) {
                return redirect()->route('departments.index')->with('error', 'Department not found');
            }
            $department->status = $department->status == "ACTIVATED" ? "DEACTIVATED" : "ACTIVATED";
            $department->save();
            return redirect()->back()->with('success', 'Department status updated successfully');
        });
    }

    public function destroy($id)
    {
        return $this->permissionService->checkPermission('department_delete', function () use ($id) {
            $department = Department::find($id);
            if (!$department) {
                return redirect()->route('departments.index')->with('error', 'Department not found');
            }
            $department->delete();
            return redirect()->route('departments.index')->with('success', 'Department deleted successfully');
        });
    }
}
