@extends('layouts.app-master')

@section('title', 'Maintenance Mode')

@section('app-content')
    @include('layouts.app-nav')
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
    <div class="row mt-4">
        <div class="col-md-6 offset-md-3">
            <div class="d-flex justify-content-between align-items-center">
                <h4 class="mb-3">Maintenance Mode</h4>
                <a class="btn btn-sm btn-outline-primary" href="{{ url('/') }}">
                    Back
                </a>
            </div>
            <form action="{{ route('settings.update') }}" method="POST">
                @csrf
                @method('PUT')
                <div class="form-group">
                    <label for="description" class="form-label">Description</label>
                    <textarea name="description" class="form-control" rows="3" placeholder="Enter Description">{{ $setting->description }}</textarea>
                </div>
                <div class="mb-3">
                    <label for="status" class="form-label">Status</label>
                    <select name="status" class="form-control form-select">
                        <option value="ACTIVATED" {{ $setting->status == 'ACTIVATED' ? 'selected' : '' }}>ACTIVATED</option>
                        <option value="DEACTIVATED" {{ $setting->status == 'DEACTIVATED' ? 'selected' : '' }}>DEACTIVATED</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">Edit</button>
            </form>
        </div>
    </div>
@endsection
