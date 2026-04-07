@extends('layouts.app-master')

@section('title', 'Sub Modules')

@section('app-content')
    @include('layouts.app-nav')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h5>Sub Modules</h5>
            <form action="{{ route('sub-modules.index') }}" method="GET">
                @csrf
                <div class="d-flex">
                    <div class="mb-3 mr-2">
                        <label for="" class="form-label">Module</label>
                        <select name="module_id" class="form-control form-select form-select-sm">
                            <option value="">Select Module</option>
                            @foreach ($modules as $module)
                                <option value="{{ $module->id }}" {{ request()->get('module_id') == $module->id ? 'selected' : '' }}>{{ $module->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="" class="form-label">-</label>
                        <button type="submit" class="btn btn-sm btn-primary d-block">Search</button>
                    </div>
                </div>
            </form>
        </div>
        <div>
            @if (Auth::user()->hasPermission('sub_module_create'))
                <a href="{{ route('sub-modules.create') }}" class="btn btn-primary">Create</a>
            @endif
        </div>
    </div>
        {{-- alert section --}}
        @if (Session::has('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <strong>Success!</strong> {{ Session::get('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif
    @if (Session::has('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong>Error!</strong> {{ Session::get('error') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif
    <div class="table-responsive">
        <table class="table text-center">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Name</th>
                    <th>Module</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($lists as $item)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $item->name }}</td>
                        <td>{{ $item->module->name }}</td>
                        <td class="text-center">
                            <div class="d-inline-flex align-items-center">
                                <span class="badge badge-{{ $item->status == 'ACTIVATED' ? 'success' : 'danger' }} mr-2">
                                    {{ $item->status }}
                                </span>
                                @if (Auth::user()->hasPermission('sub_module_status_change'))
                                    <button type="button" class="btn btn-link p-0" style="cursor: pointer;"
                                        onclick="confirmStatusChange({{ $item->id }})">
                                        <i
                                            class="fas fa-{{ $item->status == 'ACTIVATED' ? 'times text-danger' : 'check text-primary' }}"></i>
                                    </button>
                                    <form id="status-form-{{ $item->id }}"
                                        action="{{ route('sub-modules.statusChange', $item->id) }}" method="post"
                                        class="mb-0 d-none">
                                        @csrf
                                        @method('PUT')
                                    </form>
                                @endif
                            </div>
                        </td>
                        <td>
                            <div class="d-flex justify-content-center align-items-center">
                                @if (Auth::user()->hasPermission('sub_module_update'))
                                    <a href="{{ route('sub-modules.edit', $item->id) }}"
                                        class="btn btn-success btn-sm mr-2">EDIT</a>
                                @endif
                                @if (Auth::user()->hasPermission('sub_module_delete'))
                                    <button type="button" class="btn btn-sm btn-danger"
                                        onclick="confirmDelete({{ $item->id }})">
                                        {{-- <i class="fas fa-trash"></i>  --}}
                                        Delete
                                    </button>
                                    <form id="delete-form-{{ $item->id }}"
                                        action="{{ route('sub-modules.destroy', $item->id) }}" method="POST"
                                        style="display: none;">
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div class="d-flex justify-content-between align-items-center mt-5">
            <div class="text-muted">
                {{ $lists->total() }} items
            </div>
            <div>
                {{ $lists->links() }}
            </div>
        </div>
    </div>
@endsection
