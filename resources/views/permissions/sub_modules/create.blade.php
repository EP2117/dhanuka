@extends('layouts.app-master')

@section('title', 'Create Module')

@section('app-content')
    <div class="row">
        <div class="col-md-6 offset-md-3">
            <div class="d-flex justify-content-between align-items-center">
                <h4 class="mb-3">Create Sub Module</h4>
                <div>
                    <a href="{{ route('sub-modules.index') }}" class="btn btn-outline-primary">Back</a>
                </div>
            </div>
            <form action="{{ route('sub-modules.store') }}" method="post">
                @csrf
                <div class="form-group">
                    <label for="name">Name</label>
                    <input type="text" name="name" class="form-control" id="name" placeholder="Name">
                    @error('name')
                        <span class="text-danger">*{{ $message }}</span>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="name">Module</label>
                    <select name="module_id" class="form-control form-select">
                        <option value="">Select Module</option>
                        @foreach ($modules as $item)
                            <option value="{{ $item->id }}">{{ $item->name }}</option>
                        @endforeach
                    </select>
                    @error('module_id')
                        <span class="text-danger">*{{ $message }}</span>
                    @enderror
                </div>

                <button type="submit" class="btn btn-primary">Create</button>
            </form>
        </div>
    </div>
@endsection
