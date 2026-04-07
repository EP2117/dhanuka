@extends('layouts.app-master')

@section('title', 'Departments')

@section('app-content')
    @include('layouts.app-nav')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h5>Departments</h5>
        </div>
        <div>
            @if (Auth::user()->hasPermission('department_create'))
                <a href="{{ route('departments.create') }}" class="btn btn-primary">Create</a>
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
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($lists as $item)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $item->name }}</td>
                        <td class="text-center">
                            <div class="d-inline-flex align-items-center">
                                <span class="badge badge-{{ $item->status == 'ACTIVATED' ? 'success' : 'danger' }} mr-2">
                                    {{ $item->status }}
                                </span>
                                <button type="button" class="btn btn-link p-0" style="cursor: pointer;"
                                    onclick="confirmStatusChange({{ $item->id }})">
                                    <i
                                        class="fas fa-{{ $item->status == 'ACTIVATED' ? 'times text-danger' : 'check text-primary' }}"></i>
                                </button>
                                <form id="status-form-{{ $item->id }}"
                                    action="{{ route('departments.statusChange', $item->id) }}" method="post"
                                    class="mb-0 d-none">
                                    @csrf
                                    @method('PUT')
                                </form>
                            </div>
                        </td>
                        <td>
                            <div class="d-flex justify-content-center align-items-center">
                                @if (Auth::user()->hasPermission('department_update'))
                                    <a href="{{ route('departments.edit', $item->id) }}"
                                        class="btn btn-success btn-sm mr-2">EDIT</a>
                                @endif
                                @if (Auth::user()->hasPermission('department_delete'))
                                    <button type="button" class="btn btn-sm btn-danger"
                                        onclick="confirmDelete({{ $item->id }})">
                                        <i class="fas fa-trash"></i> Delete
                                    </button>
                                    <form id="delete-form-{{ $item->id }}"
                                        action="{{ route('departments.destroy', $item->id) }}" method="POST"
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
    </div>
@endsection
