@extends('layouts.in')

@section('body')
<div class="intro-y box p-5">
    <form method="POST" action="{{ route('role.update', $role->id) }}">
        @csrf
        @method('PUT')

        <!-- Role Name -->
        <div class="form-group mb-4">
            <label class="form-label required">{{ __('role-create.Name') }}</label>
            <input type="text"
                name="name"
                class="form-control form-control-lg {{ $errors->has('name') ? 'border-red-500' : '' }}"
                value="{{ old('name', $role->name) }}"
                required>
            @if($errors->has('name'))
            <div class="text-red-500 mt-1">{{ $errors->first('name') }}</div>
            @endif
        </div>

        <!-- Alias (ẩn hoặc read-only nếu tự động tạo) -->
        <div class="form-group mb-4">
            <label class="form-label">{{ __('role-create.Alias') }}</label>
            <input type="text"
                name="alias"
                class="form-control form-control-lg {{ $errors->has('alias') ? 'border-red-500' : '' }}"
                value="{{ old('alias', $role->alias) }}"
                readonly> <!-- Thêm readonly nếu muốn tự động tạo -->
            @if($errors->has('alias'))
            <div class="text-red-500 mt-1">{{ $errors->first('alias') }}</div>
            @endif
        </div>

        <!-- Description -->
        <div class="form-group mb-4">
            <label class="form-label">{{ __('role-create.Description') }}</label>
            <textarea name="description"
                class="form-control form-control-lg {{ $errors->has('description') ? 'border-red-500' : '' }}"
                rows="3">{{ old('description', $role->description) }}</textarea>
            @if($errors->has('description'))
            <div class="text-red-500 mt-1">{{ $errors->first('description') }}</div>
            @endif
        </div>

        <!-- Buttons -->
        <div class="flex justify-end space-x-2 mt-5">
            <a href="{{ route('role.index') }}" class="btn bg-white">
                {{ __('common.Cancel') }}
            </a>
            <button type="submit" class="btn btn-primary">
                {{ __('role-update.Save') }}
            </button>
        </div>
    </form>
</div>
@endsection