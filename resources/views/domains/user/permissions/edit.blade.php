@extends('layouts.in')

@section('body')

<div class="box flex items-center px-20 py-6">
    <div class="nav nav-tabs flex overflow-auto whitespace-nowrap" role="tablist">
        <a href="{{ route('user.permissions.edit', ['role_id' => $row->role_id ?? 0]) }}"
            class="p-4 {{ ($ROUTE === 'user.permissions.edit') ? 'active' : '' }}" role="tab">
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
            <form method="POST" action="{{ route('user.permissions.update', ['role_id' => $row->role_id ?? 0]) }}"
                class="space-y-8">
                @csrf
                @method('PUT')

                <!-- Action switches -->
                <div class="form-group">
                    <label class="block font-semibold text-gray-700 mb-1">Actions</label>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 p-1">
                        @foreach ($actions as $action)
                            <div class="form-check p-2">
                                <input type="checkbox" name="actions[]" value="{{ $action['id'] }}"
                                    class="form-check-switch" id="action-{{ $action['id'] }}" {{ in_array($action['id'], $selected_actions) ? 'checked' : '' }}>
                                <label class="form-check-label text-gray-800" for="action-{{ $action['id'] }}">
                                    {{ $action['name'] }}
                                </label>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Submit and Cancel buttons -->
                <div class="flex justify-end space-x-2 mt-5">
                    <button type="submit"
                        class="btn btn-primary bg-blue-600 text-white px-3 py-2 rounded-md text-sm hover:bg-blue-700 transition">
                        Submit
                    </button>
                    <a href="{{ route('user.permissions.index') }}"
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

        /* Toggle switch styles */
        .form-check {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .form-check-switch {
            display: none;
        }

        .form-check-label {
            cursor: pointer;
            user-select: none;
        }

        /* Custom toggle switch styling */
        .form-check-switch+.form-check-label::before {
            content: '';
            display: inline-block;
            width: 2.5rem;
            /* Width of the switch */
            height: 1.25rem;
            /* Height of the switch */
            background-color: #d1d5db;
            /* Gray when unchecked */
            border-radius: 9999px;
            /* Fully rounded */
            position: relative;
            transition: background-color 0.3s;
        }

        .form-check-switch+.form-check-label::after {
            content: '';
            display: inline-block;
            width: 1rem;
            /* Width of the knob */
            height: 1rem;
            /* Height of the knob */
            background-color: white;
            border-radius: 9999px;
            /* Fully rounded */
            position: absolute;
            top: 0.125rem;
            /* Offset from top */
            left: 0.125rem;
            /* Offset from left */
            transition: transform 0.3s;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .form-check-switch:checked+.form-check-label::before {
            background-color: #3b82f6;
            /* Blue when checked, matching your theme */
        }

        .form-check-switch:checked+.form-check-label::after {
            transform: translateX(1.25rem);
            /* Move the knob to the right */
        }

        /* Existing styles remain the same */
        .btn-primary,
        .btn-secondary {
            padding: 0.5rem 1rem;
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