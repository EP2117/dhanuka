<?php

namespace App\Http\Controllers\Permission;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Permission;
use App\SubModule;

class PermissionController extends Controller
{
    public function index(Request $request)
    {
        $current = 'permissions';
        $sub_modules = SubModule::active()->get();
        $query = Permission::query();
        if($request->filled('sub_module_id')){
            $query->where('sub_module_id', $request->sub_module_id);
        }
        $lists = $query->latest()->paginate(5)->appends($request->except('page'));
        return view('permissions.permissions.index', compact('lists', 'current', 'sub_modules'));
    }

    public function create()
    {
        $sub_modules = SubModule::active()->get();
        return view('permissions.permissions.create', compact('sub_modules'));
    }

    public function store(Request $request)
    {
        // return $request->all();
        $request->validate([
            'name' => 'required|unique:permissions,name',
            'sub_module_id' => 'required',
        ]);
        Permission::create($request->all());
        return redirect()->route('permissions.index')->with('success', 'Permission created successfully');
    }

    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        $permission = Permission::find($id);
        if(!$permission){
            return redirect()->route('permissions.index')->with('error', 'Permission not found');
        }
        $sub_modules = SubModule::active()->get();
        return view('permissions.permissions.edit', compact('permission', 'sub_modules'));
    }

    public function update(Request $request, $id)
    {
        $permission = Permission::find($id);
        if(!$permission){
            return redirect()->route('permissions.index')->with('error', 'Permission not found');
        }
        $request->validate([
            'name' => 'required|unique:permissions,name,'.$id,
            'sub_module_id' => 'required',
        ]);
        $permission->update($request->all());
        return redirect()->route('permissions.index')->with('success', 'Permission updated successfully');
    }

    public function statusChange($id)
    {
        $permission = Permission::find($id);
        if(!$permission){
            return redirect()->route('permissions.index')->with('error', 'Permission not found');
        }
        $permission->status = $permission->status === "ACTIVATED" ? "DEACTIVATED" : "ACTIVATED";
        $permission->save();
        return redirect()->back()->with('success', 'Permission status changed successfully');
    }

    public function destroy($id)
    {
        $permission = Permission::find($id);
        if(!$permission){
            return redirect()->route('permissions.index')->with('error', 'Permission not found');
        }
        $permission->delete();
        return redirect()->back()->with('success', 'Permission deleted successfully');
    }
}
