<?php

use Illuminate\Support\Facades\Route;


Route::get('/auth-user', 'UserController@authUser');
Route::prefix('system')->group(function () {
    // Route::get('', function(){
    //     $current = "";
    //     return view('system', compact('current'));
    // });
    // Route::resource('departments', 'Permission\DepartmentController');
    // Route::put('/departments/{id}/statusChange', 'Permission\DepartmentController@statusChange')->name('departments.statusChange');

    // Route::resource('roles', 'Permission\RoleController');
    // Route::put('/roles/{id}/statusChange', 'Permission\RoleController@statusChange')->name('roles.statusChange');
    // Route::get("/roles/{id}/addPermissions", 'Permission\RoleController@addPermissions')->name('roles.addPermissions');
    // Route::put("/roles/{id}/storePermissions", 'Permission\RoleController@storePermissions')->name('roles.storePermissions');

    // Route::resource('modules', 'Permission\ModuleController');
    // Route::put('/modules/{id}/statusChange', 'Permission\ModuleController@statusChange')->name('modules.statusChange');

    // Route::resource('sub-modules', 'Permission\SubModuleController');
    // Route::put('/sub-modules/{id}/statusChange', 'Permission\SubModuleController@statusChange')->name('sub-modules.statusChange');

    // Route::resource('permissions', 'Permission\PermissionController');
    // Route::put('/permissions/{id}/statusChange', 'Permission\PermissionController@statusChange')->name('permissions.statusChange');

    Route::get('settings', 'Permission\SettingController@index')->name('settings.index');
    Route::put('settings', 'Permission\SettingController@update')->name('settings.update');
});

// Route::get('/get-modules', function () {
//     $modules = [
//         'System',
//         'Master',
//         'Purchase',
//         'Sale',
//         'Inventory',
//         'Account',
//         'Report',
//         'Reminder',
//     ];

//     foreach ($modules as $name) {
//         App\Module::create(['name' => $name]);
//     }
//     return 'DONE';
// });

// Route::get('/get-sub-modules', function () {
//     $subModules = [
//         [
//             "name" => 'Department',
//             "module_id" => 1,
//         ],
//         [
//             "name" => 'Role',
//             "module_id" => 1,
//         ],
//         [
//             "name" => 'Module',
//             "module_id" => 1,
//         ],
//         [
//             "name" => 'Sub Module',
//             "module_id" => 1,
//         ],
//         [
//             "name" => 'Permission',
//             "module_id" => 1,
//         ],
//     ];

//     foreach ($subModules as $item) {
//         App\SubModule::create([
//             'name' => $item['name'],
//             'module_id' => $item['module_id'],
//         ]);
//     }
//     return 'DONE';
// });
