@extends('layouts.in')

@section('body')

<div class="box flex items-center px-20 py-6">
    <div class="nav nav-tabs flex overflow-auto whitespace-nowrap" role="tablist">
        <a href="{{ route('permissions.edit', ['role_id' => $row->role_id ?? 0]) }}"
            class="p-4 {{ ($ROUTE === 'permissions.edit') ? 'active' : '' }}"
            role="tab">
            {{ 'Edit Permission ' . $row->role->name ?? 'Permission for Role #' . ($row->role_id ?? 'Unknown') }}
        </a>
    </div>
</div>

<div class="tab-content">
    <div class="tab-pane active" role="tabpanel">
        @yield('content')

        <!-- Success message -->
        @if (session('success'))
            <div class="alert alert-success p-5">
                {{ session('success') }}
            </div>
        @endif

        <!-- Error messages -->
        @if (isset($errors) && $errors->any())
            <div class="alert alert-danger p-5">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Main form container -->
        <div class="max-w-lg mx-auto p-5 bg-white rounded-lg shadow-md">
            <div class="mb-4">
                <h3 class="text-lg font-semibold text-blue-600 mb-5 border-b border-blue-600 pb-1">Role</h3>
                <p class="p-2 border rounded bg-gray-100 text-gray-800">
                    {{ $row->role_name ?? ($row->role->name ?? 'Unknown Role') }}
                </p>
            </div>

            <!-- Permission edit form -->
            <form method="POST" action="{{ route('permissions.update', ['role_id' => $row->role_id ?? 0]) }}"
                class="space-y-8">
                @csrf
                @method('PUT')

                <!-- Action checkboxes -->
                <div class="form-group">
                    <label class="block font-semibold text-gray-700 mb-1">Actions</label>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 p-1">
                        @foreach ($actions as $action)
                            <label class="flex items-center space-x-8 p-5 bg-gray-50 rounded-md hover:bg-gray-100">
                                    <input type="checkbox" name="actions[]" value="{{ $action['id'] }}"
                                        {{ in_array($action['id'], $selected_actions) ? 'checked' : '' }}
                                        class="form-checkbox h-5 w-5 text-blue-600 accent-blue-600">
                                    <span class="text-gray-800">{{ $action['name'] }}</span>
                                </label>
                        @endforeach
                    </div>
                </div>

                <!-- Submit and Cancel buttons -->
                <div class="flex justify-end space-x-2 mt-5">
                    <button type="submit"
                        class="btn btn-primary bg-blue-600 text-white px-3 py-2 rounded-md text-sm hover:bg-blue-700 transition">
                        Submit
                    </button>
                    <a href="{{ route('permissions.index') }}"
                        class="btn btn-secondary bg-gray-300 text-gray-800 px-3 py-2 rounded-md text-sm hover:bg-gray-400 transition">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

@stop
@push('styles')
    <style>
        .form-group {
            margin-bottom: 1.5rem;
            /* Tăng khoảng cách giữa các phần */
        }

        .form-checkbox {
            border: 1px solid #d2d6de;
            border-radius: 0.25rem;
        }

        .btn-primary,
        .btn-secondary {
            padding: 0.5rem 1rem;
            /* Điều chỉnh padding */
            font-size: 1rem;
        }

        .alert {
            padding: 1.5rem;
            border-radius: 0.5rem;
            margin-bottom: 2rem;
        }

        .alert-success {
            background-color: #d1e7dd;
            color: #0f5132;
            border: 1px solid #badbcc;
        }

        .alert-danger {
            background-color: #f8d7da;
            color: #842029;
            border: 1px solid #f5c2c7;
        }
    </style>
@endpush