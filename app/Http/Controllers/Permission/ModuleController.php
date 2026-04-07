<?php

namespace App\Http\Controllers\Permission;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Module;
use App\Services\PermissionService;

class ModuleController extends Controller
{
    protected $permissionService;
    public function __construct(PermissionService $permissionService)
    {
        $this->permissionService = $permissionService;
    }
    public function index()
    {
        return $this->permissionService->checkPermission('module_lists', function () {
            $current = "modules";
            $lists = Module::latest()->paginate(5);
            // return $modules;
            return view('permissions.modules.index', compact('lists', 'current'));
        });
    }

    public function create()
    {
        return $this->permissionService->checkPermission('module_create', function () {
            return view("permissions.modules.create");
        });
    }

    public function store(Request $request)
    {
        return $this->permissionService->checkPermission('module_create', function () use ($request) {
            $request->validate([
                "name" => "required|unique:modules",
            ]);
            Module::create($request->all());
            return redirect()->route('modules.index')
                ->with("success", "Module created successfully");
        });
    }

    public function edit($id)
    {
        return $this->permissionService->checkPermission('module_update', function () use ($id) {
            $module = Module::find($id);
            if (!$module) {
                return redirect()->route('modules.index')
                    ->with("error", "Module not found");
            }
            return view("permissions.modules.edit", compact('module'));
        });
    }

    public function update(Request $request, $id)
    {
        return $this->permissionService->checkPermission('module_update', function () use ($request, $id) {
            $request->validate([
                "name" => "required|unique:modules,name,$id",
            ]);
            $module = Module::find($id);
            $module->update($request->all());
            return redirect()->route('modules.index')
                ->with("success", "Module updated successfully");
        });
    }

    public function statusChange($id)
    {
        return $this->permissionService->checkPermission('module_update', function () use ($id) {
            $module = Module::find($id);
            if (!$module) {
                return redirect()->route('modules.index')->with('error', 'Module not found');
            }
            $module->status = $module->status == "ACTIVATED" ? "DEACTIVATED" : "ACTIVATED";
            $module->save();
            return redirect()->back()->with('success', 'Module status updated successfully');
        });
    }

    public function destroy($id)
    {
        return $this->permissionService->checkPermission('module_delete', function () use ($id) {
            $module = Module::find($id);
            if (!$module) {
                return redirect()->route('modules.index')
                    ->with("error", "Module not found");
            }
            $module->delete();
            return redirect()->route('modules.index')
                ->with("success", "Module deleted successfully");
        });
    }
}
