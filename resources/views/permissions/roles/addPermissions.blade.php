@extends('layouts.app-master')

@section('title', 'Add Role Permissions')

@section('styles')
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .accordion-item {
            margin-bottom: 10px;
            border: 1px solid #dee2e6;
            border-radius: 0.25rem;
        }

        .accordion-button {
            padding: 1rem 1.25rem 1rem 2rem;
            background-color: #fff;
            font-weight: 500;
        }

        .accordion-button:not(.collapsed) {
            background-color: #e7f1ff;
            color: #0d6efd;
            box-shadow: none;
        }

        .accordion-button:focus {
            box-shadow: none;
            border-color: rgba(0, 0, 0, .125);
        }

        .accordion-button::after {
            margin-left: auto;
        }

        .accordion-body {
            padding: 1.25rem;
        }

        .permission-item {
            padding: 0.5rem 0;
            border-bottom: 1px solid #f0f0f0;
        }

        .permission-item:last-child {
            border-bottom: none;
        }

        .module-header-content {
            display: flex;
            align-items: center;
            gap: 10px;
            width: 100%;
            pointer-events: none;
        }

        .module-checkbox {
            width: 18px;
            height: 18px;
            cursor: pointer;
            pointer-events: auto;
        }

        .permission-checkbox {
            width: 16px;
            height: 16px;
            cursor: pointer;
        }

        .sub-module-section {
            padding: 1rem;
            background-color: #f8f9fa;
            border-radius: 0.375rem;
            margin-bottom: 1rem;
            border: 1px solid #e9ecef;
        }

        .sub-module-section:last-child {
            margin-bottom: 0;
        }

        .sub-module-header {
            display: flex;
            align-items: center;
            gap: 10px;
            font-weight: 500;
            margin-bottom: 0.75rem;
            padding-bottom: 0.5rem;
            padding-left: 2rem;
            border-bottom: 1px solid #dee2e6;
        }

        .permission-label {
            cursor: pointer;
            margin-bottom: 0;
            user-select: none;
        }

        .badge-count {
            font-size: 0.75rem;
            padding: 0.35em 0.65em;
        }

        .permissions-list {
            margin-left: 2rem;
        }

        .select-all-box {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 1rem;
            border-radius: 0.5rem;
            margin-bottom: 1.5rem;
        }

        .select-all-box .form-check-label {
            color: white;
            font-weight: 600;
        }

        .select-all-box .form-check-input {
            width: 20px;
            height: 20px;
            cursor: pointer;
            border: 2px solid white;
            background-color: transparent;
        }

        .select-all-box .form-check-input:checked {
            background-color: white;
            border-color: white;
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 20 20'%3e%3cpath fill='none' stroke='%23667eea' stroke-linecap='round' stroke-linejoin='round' stroke-width='3' d='M6 10l3 3l6-6'/%3e%3c/svg%3e");
        }

        .select-all-box .form-check-input:indeterminate {
            background-color: white;
            border-color: white;
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 20 20'%3e%3cpath fill='none' stroke='%23667eea' stroke-linecap='round' stroke-linejoin='round' stroke-width='3' d='M6 10h8'/%3e%3c/svg%3e");
        }
    </style>
@endsection

@section('app-content')
    <div class="row">
        <div class="col-md-10 offset-md-1">

            {{-- Header --}}
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4 class="mb-0">Add Role Permissions</h4>
                <a href="{{ route('roles.index') }}" class="btn btn-outline-primary btn-sm">
                    ← Back
                </a>
            </div>

            {{-- Form --}}
            <div class="card shadow-sm">
                <div class="card-body p-4">
                    <form action="{{ route('roles.storePermissions', $role->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        {{-- Role Name --}}
                        <div class="mb-4">
                            <label for="role_name" class="form-label fw-bold">Role Name</label>
                            <input type="text" name="role_name"
                                class="form-control @error('role_name') is-invalid @enderror" id="role_name"
                                placeholder="Enter role name" value="{{ old('role_name', $role->role_name) }}">
                            @error('role_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Permissions Section --}}
                        <div class="mb-4">
                            <label class="form-label fw-bold mb-3">Permissions</label>

                            {{-- Select All Option --}}
                            <div class="select-all-box">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="select_all_permissions">
                                    <label class="form-check-label" for="select_all_permissions">
                                        Select All Permissions
                                    </label>
                                </div>
                            </div>

                            {{-- Accordion for Modules --}}
                            <div class="accordion" id="permissionsAccordion">
                                @foreach ($modules as $index => $module)
                                    <div class="accordion-item">
                                        <h2 class="accordion-header" id="heading{{ $index }}">
                                            <button class="accordion-button {{ $index > 0 ? 'collapsed' : '' }}"
                                                type="button" data-bs-toggle="collapse"
                                                data-bs-target="#collapse{{ $index }}"
                                                aria-expanded="{{ $index === 0 ? 'true' : 'false' }}"
                                                aria-controls="collapse{{ $index }}">
                                                <div class="module-header-content">
                                                    <input class="form-check-input module-checkbox module-parent"
                                                        type="checkbox" id="module_{{ $module->id }}"
                                                        data-module-id="{{ $module->id }}"
                                                        onclick="event.stopPropagation();">
                                                    <span class="flex-grow-1">{{ $module->name }}</span>
                                                    <span class="badge bg-primary badge-count">
                                                        <span class="selected-count"
                                                            data-module-id="{{ $module->id }}">0</span>
                                                        /
                                                        <span class="total-count">
                                                            {{ $module->sub_modules->sum(function ($sub) {
                                                                return $sub->permissions->count();
                                                            }) }}
                                                        </span>
                                                    </span>
                                                </div>
                                            </button>
                                        </h2>
                                        <div id="collapse{{ $index }}"
                                            class="accordion-collapse collapse {{ $index === 0 ? 'show' : '' }}"
                                            aria-labelledby="heading{{ $index }}"
                                            data-bs-parent="#permissionsAccordion">
                                            <div class="accordion-body">
                                                @forelse ($module->sub_modules as $subModule)
                                                    <div class="sub-module-section" data-module-id="{{ $module->id }}">
                                                        <div class="sub-module-header">
                                                            <input
                                                                class="form-check-input module-checkbox sub-module-parent"
                                                                type="checkbox" id="sub_module_{{ $subModule->id }}"
                                                                data-sub-module-id="{{ $subModule->id }}"
                                                                data-module-id="{{ $module->id }}">
                                                            <label class="permission-label flex-grow-1"
                                                                for="sub_module_{{ $subModule->id }}">
                                                                {{ $subModule->name }}
                                                            </label>
                                                            <span class="badge bg-secondary">
                                                                {{ $subModule->permissions->count() }} permissions
                                                            </span>
                                                        </div>

                                                        <div class="permissions-list">
                                                            @forelse ($subModule->permissions as $permission)
                                                                <div class="permission-item">
                                                                    <div class="form-check">
                                                                        <input class="form-check-input permission-checkbox"
                                                                            type="checkbox" name="permissions[]"
                                                                            value="{{ $permission->id }}"
                                                                            id="permission_{{ $permission->id }}"
                                                                            data-sub-module-id="{{ $subModule->id }}"
                                                                            data-module-id="{{ $module->id }}"
                                                                            {{ in_array($permission->id, old('permissions', $role->permissions->pluck('id')->toArray() ?? [])) ? 'checked' : '' }}>
                                                                        <label class="form-check-label permission-label"
                                                                            for="permission_{{ $permission->id }}">
                                                                            {{ $permission->name }}
                                                                        </label>
                                                                    </div>
                                                                </div>
                                                            @empty
                                                                <p class="text-muted small mb-0">No permissions available
                                                                </p>
                                                            @endforelse
                                                        </div>
                                                    </div>
                                                @empty
                                                    <p class="text-muted mb-0">No sub-modules available</p>
                                                @endforelse
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            @error('permissions')
                                <div class="text-danger mt-2 small">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Submit Button --}}
                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('roles.index') }}" class="btn btn-outline-secondary">
                                Cancel
                            </a>
                            <button type="submit" class="btn btn-primary px-4">
                                Save Permissions
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        $(document).ready(function() {
            // Function to update counts
            function updateCounts() {
                $('.module-parent').each(function() {
                    const moduleId = $(this).data('module-id');
                    const totalChecked = $(`.permission-checkbox[data-module-id="${moduleId}"]:checked`)
                        .length;
                    $(`.selected-count[data-module-id="${moduleId}"]`).text(totalChecked);
                });
            }

            // Function to update parent checkboxes
            function updateParentCheckboxes() {
                // Update sub-module checkboxes
                $('.sub-module-parent').each(function() {
                    const subModuleId = $(this).data('sub-module-id');
                    const totalPermissions = $(`.permission-checkbox[data-sub-module-id="${subModuleId}"]`)
                        .length;
                    const checkedPermissions = $(
                        `.permission-checkbox[data-sub-module-id="${subModuleId}"]:checked`).length;

                    if (totalPermissions > 0) {
                        $(this).prop('checked', totalPermissions === checkedPermissions);
                        $(this).prop('indeterminate', checkedPermissions > 0 && checkedPermissions <
                            totalPermissions);
                    }
                });

                // Update module checkboxes
                $('.module-parent').each(function() {
                    const moduleId = $(this).data('module-id');
                    const totalPermissions = $(`.permission-checkbox[data-module-id="${moduleId}"]`).length;
                    const checkedPermissions = $(
                        `.permission-checkbox[data-module-id="${moduleId}"]:checked`).length;

                    if (totalPermissions > 0) {
                        $(this).prop('checked', totalPermissions === checkedPermissions);
                        $(this).prop('indeterminate', checkedPermissions > 0 && checkedPermissions <
                            totalPermissions);
                    }
                });

                // Update "Select All" checkbox
                const totalPermissions = $('.permission-checkbox').length;
                const checkedPermissions = $('.permission-checkbox:checked').length;

                if (totalPermissions > 0) {
                    $('#select_all_permissions').prop('checked', totalPermissions === checkedPermissions);
                    $('#select_all_permissions').prop('indeterminate', checkedPermissions > 0 &&
                        checkedPermissions < totalPermissions);
                }
            }

            // Select All functionality
            $('#select_all_permissions').on('change', function() {
                const isChecked = $(this).prop('checked');
                $('.permission-checkbox, .module-parent, .sub-module-parent').prop('checked', isChecked);
                $('.module-parent, .sub-module-parent').prop('indeterminate', false);
                updateCounts();
            });

            // Module parent checkbox
            $('.module-parent').on('change', function(e) {
                e.stopPropagation();
                const moduleId = $(this).data('module-id');
                const isChecked = $(this).prop('checked');

                $(`.permission-checkbox[data-module-id="${moduleId}"]`).prop('checked', isChecked);
                $(`.sub-module-parent[data-module-id="${moduleId}"]`).prop('checked', isChecked);
                $(`.sub-module-parent[data-module-id="${moduleId}"]`).prop('indeterminate', false);

                updateCounts();
                updateParentCheckboxes();
            });

            // Sub-module parent checkbox
            $('.sub-module-parent').on('change', function() {
                const subModuleId = $(this).data('sub-module-id');
                const isChecked = $(this).prop('checked');

                $(`.permission-checkbox[data-sub-module-id="${subModuleId}"]`).prop('checked', isChecked);

                updateCounts();
                updateParentCheckboxes();
            });

            // Individual permission checkbox
            $('.permission-checkbox').on('change', function() {
                updateCounts();
                updateParentCheckboxes();
            });

            // Initialize on page load
            function initializeForm() {
                updateCounts();
                updateParentCheckboxes();

                // Check if all permissions are selected on load
                const totalPermissions = $('.permission-checkbox').length;
                const checkedPermissions = $('.permission-checkbox:checked').length;

                if (totalPermissions > 0 && totalPermissions === checkedPermissions) {
                    $('#select_all_permissions').prop('checked', true);
                } else if (checkedPermissions > 0) {
                    $('#select_all_permissions').prop('indeterminate', true);
                }
            }

            // Run on page load
            initializeForm();
        });
    </script>
@endsection
