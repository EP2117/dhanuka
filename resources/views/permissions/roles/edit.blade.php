@extends('layouts.app-master')

@section('title', 'Edit Role')

@section('app-content')
    <div class="row">
        <div class="col-md-6 offset-md-3">
            <div class="d-flex justify-content-between align-items-center">
                <h4 class="mb-3">Edit Role</h4>
                <div>
                    <a href="{{ route('roles.index') }}" class="btn btn-outline-primary">Back</a>
                </div>
            </div>
            <form action="{{ route('roles.update', $role->id) }}" method="post">
                @csrf
                @method('PUT')
                <div class="form-group">
                    <label for="name">Name</label>
                    <input type="text" 
                    name="role_name" class="form-control" 
                    id="name" placeholder="Name"
                    value="{{ $role->role_name }}">
                    @error('role_name')
                        <span class="text-danger">*{{ $message }}</span>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="name">Department</label>
                    <select name="department_id" id="" class="form-control form-select">
                        <option value="">Select Department</option>
                        @foreach ($departments as $item)
                            <option value="{{ $item->id }}" {{ $role->department_id == $item->id ? 'selected' : ''}}>{{ $item->name }}</option>
                        @endforeach
                    </select>
                    @error('department_id')
                        <span class="text-danger">*{{ $message }}</span>
                    @enderror
                </div>
                <button type="submit" class="btn btn-primary">Edit</button>
            </form>
        </div>
    </div>
@endsection
