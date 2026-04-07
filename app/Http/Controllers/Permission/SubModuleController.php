<?php

namespace App\Http\Controllers\Permission;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Module;
use App\Services\PermissionService;
use App\SubModule;

class SubModuleController extends Controller
{
    protected $permissionService;
    public function __construct(PermissionService $permissionService)
    {
        $this->permissionService = $permissionService;
    }
    public function index(Request $request)
    {
        return $this->permissionService->checkPermission('sub_module_lists', function () use ($request) {
            $current = "sub_modules";
            $modules = Module::active()->get();
            $query = SubModule::query();
            if ($request->filled('module_id')) {
                $query->where('module_id', $request->module_id);
            }
            $lists = $query->latest()->paginate(5)->appends($request->except('page'));
            return view('permissions.sub_modules.index', compact('lists', 'current', 'modules'));
        });
    }

    public function create()
    {
        return $this->permissionService->checkPermission('sub_module_create', function () {
            $modules = Module::all();
            return view('permissions.sub_modules.create', compact('modules'));
        });
    }

    public function store(Request $request)
    {
        return $this->permissionService->checkPermission('sub_module_create', function () use ($request) {
            $request->validate([
                'name' => 'required',
                'module_id' => 'required',
            ]);
            SubModule::create($request->all());
            return redirect()->route('sub-modules.index')->with('success', 'Sub Module created successfully');
        });
    }

    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        return $this->permissionService->checkPermission('sub_module_update', function () use ($id) {
            $sub_module = SubModule::find($id);
            if (!$sub_module) {
                return redirect()->route('sub-modules.index')->with('error', 'Sub Module not found');
            }
            $modules = Module::all();
            return view('permissions.sub_modules.edit', compact('sub_module', 'modules'));
        });
    }

    public function update(Request $request, $id)
    {
        return $this->permissionService->checkPermission('sub_module_update', function () use ($request, $id) {
            $sub_module = SubModule::find($id);
            if (!$sub_module) {
                return redirect()->route('sub-modules.index')->with('error', 'Sub Module not found');
            }
            $request->validate([
                'name' => 'required',
                'module_id' => 'required',
            ]);
            $sub_module->update($request->all());
            return redirect()->route('sub-modules.index')->with('success', 'Sub Module updated successfully');
        });
    }

    public function statusChange($id)
    {
        return $this->permissionService->checkPermission('sub_module_status_change', function () use ($id) {
            $sub_module = SubModule::find($id);
            if (!$sub_module) {
                return redirect()->route('sub-module.index')->with('error', 'Sub Module not found');
            }
            $sub_module->status = $sub_module->status == "ACTIVATED" ? "DEACTIVATED" : "ACTIVATED";
            $sub_module->save();
            return redirect()->back()->with('success', 'Sub Module status updated successfully');
        });
    }

    public function destroy($id)
    {
        return $this->permissionService->checkPermission('sub_module_delete', function () use ($id) {
            $sub_module = SubModule::find($id);
            if (!$sub_module) {
                return redirect()->route('sub-modules.index')->with('error', 'Sub Module not found');
            }
            $sub_module->delete();
            return redirect()->route('sub-modules.index')->with('success', 'Sub Module deleted successfully');
        });
    }
}
