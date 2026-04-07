@extends('layouts.app-master')

@section('title', 'Edit Sub Module')

@section('app-content')
    <div class="row">
        <div class="col-md-6 offset-md-3">
            <div class="d-flex justify-content-between align-items-center">
                <h4 class="mb-3">Edit Sub Module</h4>
                <div>
                    <a href="{{ route('sub-modules.index') }}" class="btn btn-outline-primary">Back</a>
                </div>
            </div>
            <form action="{{ route('sub-modules.update', $sub_module->id) }}" method="post">
                @csrf
                @method('PUT')
                <div class="form-group">
                    <label for="name">Name</label>
                    <input type="text" name="name" 
                    class="form-control" id="name" placeholder="Name"
                    value="{{ $sub_module->name }}">
                    @error('name')
                        <span class="text-danger">*{{ $message }}</span>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="name">Module</label>
                    <select name="module_id" class="form-control form-select">
                        <option value="">Select Module</option>
                        @foreach ($modules as $item)
                            <option value="{{ $item->id }}" {{ ($item->id == $sub_module->module_id) ? 'selected' : ''}}>{{ $item->name }}</option>
                        @endforeach
                    </select>
                    @error('module_id')
                        <span class="text-danger">*{{ $message }}</span>
                    @enderror
                </div>
                <button type="submit" class="btn btn-primary">Edit</button>
            </form>
        </div>
    </div>
@endsection
