@extends('layouts.in')

@section('body')
<div class="intro-y box p-5">
    <form method="POST" action="{{ route('user.role.store') }}">
        @csrf
        <input type="hidden" name="_action" value="create" />

        <!-- Role Name -->
        <div class="form-group mb-4">
            <label class="form-label required">{{ __('role-create.Name') }}</label>
            <input type="text" name="name"
                class="form-control form-control-lg {{ $errors->has('name') ? 'border-red-500' : '' }}"
                value="{{ old('name') }}" required>
            @if($errors->has('name'))
            <div class="text-red-500 mt-1">{{ $errors->first('name') }}</div>
            @endif
        </div>

        <!-- Alias (ẩn hoặc read-only nếu tự động tạo) -->
        <div class="form-group mb-4">
            <label class="form-label">{{ __('role-create.Alias') }}</label>
            <input type="text" name="alias"
                class="form-control form-control-lg {{ $errors->has('alias') ? 'border-red-500' : '' }}"
                value="{{ old('alias') }}" readonly>
            @if($errors->has('alias'))
            <div class="text-red-500 mt-1">{{ $errors->first('alias') }}</div>
            @endif
        </div>

        <!-- Description -->
        <div class="form-group mb-4">
            <label class="form-label">{{ __('role-create.Description') }}</label>
            <textarea name="description"
                class="form-control form-control-lg {{ $errors->has('description') ? 'border-red-500' : '' }}"
                rows="3">{{ old('description') }}</textarea>
            @if($errors->has('description'))
            <div class="text-red-500 mt-1">{{ $errors->first('description') }}</div>
            @endif
        </div>

        <!-- Features Selection -->
        <div class="form-group mb-4">
            <label class="form-label">{{ __('role-create.Features') }}</label>
            @foreach ($features as $feature)
            <div class="flex items-center mb-2">
                <input type="checkbox" name="feature_ids[]"
                    value="{{ $feature->id }}"
                    class="form-check-input">
                <label class="ml-2">{{ $feature->name }}</label>
            </div>
            @endforeach
            @if($errors->has('feature_ids'))
            <div class="text-red-500 mt-1">{{ $errors->first('feature_ids') }}</div>
            @endif
        </div>

        <!-- Buttons -->
        <div class="flex justify-end space-x-2 mt-5">
            <a href="{{ route('user.role.index') }}" class="btn bg-white">
                {{ __('common.Cancel') }}
            </a>
            <button type="submit" class="btn btn-primary">
                {{ __('role-create.Save') }}
            </button>
        </div>
    </form>
</div>
@endsection