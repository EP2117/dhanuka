@extends('layouts.app-master')

@section('title', 'Roles')

@section('app-content')
    @include('layouts.app-nav')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h5>Roles</h5>
            <div>
                <form class="d-flex align-items-center" action="{{ route('roles.index') }}" method="GET">
                    @csrf
                    <input class="form-control" 
                    placeholder="Search by name" 
                    type="text" name="role_name">
                    <button type="submit" class="btn btn-sm btn-primary ml-2">Search</button>
                </form>
            </div>
        </div>
        <div>
            @if (Auth::user()->hasPermission('role_create'))
                <a href="{{ route('roles.create') }}" class="btn btn-primary">Create</a>
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
                    <th>Department</th>
                    {{-- <th>Permissions</th> --}}
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @if ($lists->count() === 0)
                    <tr>
                        <td colspan="5">
                            <p class="text-center text-danger">No data available</p>
                        </td>
                    </tr>
                @endif
                @foreach ($lists as $item)
                    <tr>
                        <td>{{ $loop->iteration + ($lists->currentPage() - 1) * $lists->perPage() }}</td>
                        <td>{{ $item->role_name }}</td>
                        <td>{{ $item->department->name ?? '-' }}</td>
                        {{-- <td>
                            @foreach ($item->permissions as $p)
                                <span
                                    class="badge badge-{{ $p->status == 'ACTIVATED' ? 'success' : 'danger' }}">{{ $p->name }}</span>
                            @endforeach
                        </td> --}}
                        <td class="text-center">
                            <div class="d-inline-flex align-items-center">
                                <span class="badge badge-{{ $item->status == 'ACTIVATED' ? 'success' : 'danger' }} mr-2">
                                    {{ $item->status }}
                                </span>
                                @if (Auth::user()->hasPermission('role_status_change'))
                                    <button type="button" class="btn btn-link p-0" style="cursor: pointer;"
                                        onclick="confirmStatusChange({{ $item->id }})">
                                        <i
                                            class="fas fa-{{ $item->status == 'ACTIVATED' ? 'times text-danger' : 'check text-primary' }}"></i>
                                    </button>
                                    <form id="status-form-{{ $item->id }}"
                                        action="{{ route('roles.statusChange', $item->id) }}" method="post"
                                        class="mb-0 d-none">
                                        @csrf
                                        @method('PUT')
                                    </form>
                                @endif
                            </div>
                        </td>
                        <td>
                            <div class="d-flex justify-content-center align-items-center">
                                @if (Auth::user()->hasPermission('role_add_permissions'))
                                    <a href="{{ route('roles.addPermissions', $item->id) }}"
                                        class="btn btn-primary btn-sm mr-2">ADD</a>
                                @endif

                                @if (Auth::user()->hasPermission('role_delete'))
                                    <a href="{{ route('roles.edit', $item->id) }}"
                                        class="btn btn-success btn-sm mr-2">EDIT</a>
                                @endif
                                @if (Auth::user()->hasPermission('role_delete'))
                                    <button type="button" class="btn btn-sm btn-danger"
                                        onclick="confirmDelete({{ $item->id }})">
                                        {{-- <i class="fas fa-trash"></i>  --}}
                                        Delete
                                    </button>
                                    <form id="delete-form-{{ $item->id }}"
                                        action="{{ route('roles.destroy', $item->id) }}" method="POST"
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
