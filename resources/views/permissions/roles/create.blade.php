@extends('layouts.app-master')

@section('title', 'Create Role')

@section('app-content')
    <div class="row">
        <div class="col-md-6 offset-md-3">
            <div class="d-flex justify-content-between align-items-center">
                <h4 class="mb-3">Create Role</h4>
                <div>
                    <a href="{{ route('roles.index') }}" class="btn btn-outline-primary">Back</a>
                </div>
            </div>
            <form action="{{ route('roles.store') }}" method="post">
                @csrf
                <div class="form-group">
                    <label for="name">Name</label>
                    <input type="text" name="role_name" class="form-control" id="name" placeholder="Name">
                    @error('name')
                        <span class="text-danger">*{{ $message }}</span>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="name">Department</label>
                    <select name="department_id" id="" class="form-control form-select">
                        <option value="">Select Department</option>
                        @foreach ($departments as $item)
                            <option value="{{ $item->id }}">{{ $item->name }}</option>
                        @endforeach
                    </select>
                    @error('department_id')
                        <span class="text-danger">*{{ $message }}</span>
                    @enderror
                </div>

                <button type="submit" class="btn btn-primary">Create</button>
            </form>
        </div>
    </div>
@endsection
