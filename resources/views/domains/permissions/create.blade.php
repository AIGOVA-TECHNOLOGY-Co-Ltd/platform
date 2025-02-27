@extends('layouts.in')

@section('title', __('permissions-create.title'))

@section('body')
    <div class="intro-y box p-5">
        <!-- Alert Success -->
        @if(session('success'))
            <div class="alert alert-success mb-4 p-4">
                {{ session('success') }}
            </div>
        @endif

        <form method="POST" action="{{ route('user.permissions.create') }}">
            @csrf
            <input type="hidden" name="_action" value="create" />

            <!-- Role -->
            <div class="form-group mb-4">
                <label class="form-label required">{{ __('permissions-create.role') }}</label>
                <select name="role_id" class="form-control form-control-lg " required>
                    <option value="">{{ __('permissions-create.select-role') }}</option>
                    @foreach($roles as $role)
                        <option value="{{ $role['id'] }}" {{ old('role_id') == $role['id'] ? 'selected' : '' }}>
                            {{ $role['name'] }}
                        </option>
                    @endforeach
                </select>

            </div>

            <!-- Action -->
            <div class="form-group mb-4">
                <label class="form-label required">{{ __('permissions-create.action') }}</label>
                <select name="action_id" class="form-control form-control-lg " required>
                    <option value="">{{ __('permissions-create.select-action') }}</option>
                    @foreach($actions as $action)
                        <option value="{{ $action['id'] }}" {{ old('action_id') == $action['id'] ? 'selected' : '' }}>
                            {{ $action['name'] }}
                        </option>
                    @endforeach
                </select>

            </div>

            <!-- Buttons -->
            <div class="flex justify-end space-x-2 mt-5">
                <a href="{{ route('user.permissions.index') }}" class="btn bg-white">
                    {{ __('common.Cancel') }}
                </a>
                <button type="submit" class="btn btn-primary">
                    {{ __('permissions-create.save') }}
                </button>
            </div>
        </form>
    </div>
@endsection