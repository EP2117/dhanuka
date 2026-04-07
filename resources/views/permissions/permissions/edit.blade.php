@extends('layouts.app-master')

@section('title', 'Edit Permission')

@section('app-content')
    <div class="row">
        <div class="col-md-6 offset-md-3">
            <div class="d-flex justify-content-between align-items-center">
                <h4 class="mb-3">Edit Permission</h4>
                <div>
                    <a href="{{ route('permissions.index') }}" class="btn btn-outline-primary">Back</a>
                </div>
            </div>
            <form action="{{ route('permissions.update', $permission->id) }}" method="post">
                @csrf
                @method('PUT')
                <div class="form-group">
                    <label for="name">Name</label>
                    <input type="text" name="name" 
                    class="form-control" id="name" placeholder="Name"
                    value="{{ $permission->name }}">
                    @error('name')
                        <span class="text-danger">*{{ $message }}</span>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="name">Sub Module</label>
                    <select name="sub_module_id" class="form-control form-select">
                        <option value="">Select Sub Module</option>
                        @foreach ($sub_modules as $item)
                            <option value="{{ $item->id }}" {{ ($item->id == $permission->sub_module_id) ? 'selected' : ''}}>{{ $item->name }}</option>
                        @endforeach
                    </select>
                    @error('sub_module_id')
                        <span class="text-danger">*{{ $message }}</span>
                    @enderror
                </div>
                <button type="submit" class="btn btn-primary">Edit</button>
            </form>
        </div>
    </div>
@endsection
