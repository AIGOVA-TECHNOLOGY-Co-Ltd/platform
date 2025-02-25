@extends('layouts.in')

@section('title', __('permissions-create.title'))

@section('body')
    <div class="intro-y box p-5">
        <form method="POST" action="{{ route('permissions.store') }}">
            @csrf
            <input type="hidden" name="_action" value="create" />

            <!-- Role -->
            <div class="form-group mb-4">
                <label class="form-label required">{{ __('permissions-create.role') }}</label>
                <select name="role_id"
                    class="form-control form-control-lg {{ $errors->has('role_id') ? 'border-red-500' : '' }}" required>
                    <option value="">{{ __('permissions-create.select-role') }}</option>
                    @foreach($roles as $role)
                        <option value="{{ $role['id'] }}" {{ old('role_id') == $role['id'] ? 'selected' : '' }}>
                            {{ $role['name'] }}
                        </option>
                    @endforeach
                </select>
                @if($errors->has('role_id'))
                    <div class="text-red-500 mt-1">{{ $errors->first('role_id') }}</div>
                @endif
            </div>

            <!-- Action -->
            <div class="form-group mb-4">
                <label class="form-label required">{{ __('permissions-create.action') }}</label>
                <select name="action_id"
                    class="form-control form-control-lg {{ $errors->has('action_id') ? 'border-red-500' : '' }}" required>
                    <option value="">{{ __('permissions-create.select-action') }}</option>
                    @foreach($actions as $action)
                        <option value="{{ $action['id'] }}" {{ old('action_id') == $action['id'] ? 'selected' : '' }}>
                            {{ $action['name'] }}
                        </option>
                    @endforeach
                </select>
                @if($errors->has('action_id'))
                    <div class="text-red-500 mt-1">{{ $errors->first('action_id') }}</div>
                @endif
            </div>
            <!-- Enterprise -->
            <div class="form-group mb-4">
                <label class="form-label">{{ __('permissions-create.enterprise') }}</label>
                <select name="enterprise_id"
                    class="form-control form-control-lg {{ $errors->has('enterprise_id') ? 'border-red-500' : '' }}">
                    <option value="">{{ __('permissions-create.select-enterprise') }}</option>
                    @foreach ($enterprises as $enterprise)
                        <option value="{{ $enterprise['id'] }}" {{ old('enterprise_id') == $enterprise['id'] ? 'selected' : '' }}>
                            {{ $enterprise['name'] }}
                        </option>
                    @endforeach
                </select>

                @if($errors->has('enterprise_id'))
                    <div class="text-red-500 mt-1">{{ $errors->first('enterprise_id') }}</div>
                @endif
            </div>


            <!-- Buttons -->
            <div class="flex justify-end space-x-2 mt-5">
                <a href="{{ route('permissions.index') }}" class="btn bg-white">
                    {{ __('common.Cancel') }}
                </a>
                <button type="submit" class="btn btn-primary">
                    {{ __('permissions-create.save') }}
                </button>
            </div>
        </form>
    </div>
@endsection