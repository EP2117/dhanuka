@extends('layouts.app-master')

@section('title', 'Edit Module')

@section('app-content')
    <div class="row">
        <div class="col-md-6 offset-md-3">
            <div class="d-flex justify-content-between align-items-center">
                <h4 class="mb-3">Edit Module</h4>
                <div>
                    <a href="{{ route('modules.index') }}" class="btn btn-outline-primary">Back</a>
                </div>
            </div>
            <form action="{{ route('modules.update', $module->id) }}" method="post">
                @csrf
                @method('PUT')
                <div class="form-group">
                    <label for="name">Name</label>
                    <input type="text" name="name" 
                    class="form-control" id="name" placeholder="Name"
                    value="{{ $module->name }}">
                    @error('name')
                        <span class="text-danger">*{{ $message }}</span>
                    @enderror
                </div>
                <button type="submit" class="btn btn-primary">Edit</button>
            </form>
        </div>
    </div>
@endsection
