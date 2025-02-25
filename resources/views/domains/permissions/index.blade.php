@extends ('layouts.in')

@section ('body')

<!-- Hiển thị thông báo thành công nếu có -->
@if (session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif

<form method="get">
    <div class="sm:flex sm:space-x-4">
        <!-- Ô tìm kiếm -->
        <div class="flex-grow mt-2 sm:mt-0">
            <input type="search" class="form-control form-control-lg" placeholder="{{ __('permissions-index.filter') }}"
                data-table-search="#permissions-list-table" />
        </div>

        <!-- Nút tạo mới permission -->
        <div class="sm:ml-4 mt-2 sm:mt-0 bg-white">
            <a href="{{ route('permissions.create') }}" class="btn form-control-lg whitespace-nowrap">
                {{ __('permissions-index.create') }}
            </a>
        </div>
    </div>
</form>

<div class="overflow-auto scroll-visible header-sticky">
    <table id="permissions-list-table"
        class="table table-report sm:mt-2 font-medium font-semibold text-center whitespace-nowrap" data-table-sort
        data-table-pagination data-table-pagination-limit="10">

        <thead>
            <tr>
                @if ($user_empty)
                    <th>{{ __('permissions-index.user') }}</th>
                @endif

                <th class="w-1">{{ __(key: 'ID') }}</th>
                <th class="w-1">{{ __('Role Name') }}</th>
                <th class="w-1">{{ __('Action') }}</th>
                <th class="w-1">{{ __('Created At') }}</th>

            </tr>
        </thead>

        <tbody>
            @foreach ($permissions as $permission)
                <tr>
                    @if ($user_empty)
                        <td><a href="{{ route('permissions.edit', $permission->id) }}"
                                class="block">{{ $permission->user->name ?? '-' }}</a></td>
                    @endif

                    <td class="w-1">{{ $permission->stt }}</td> <!-- Hiển thị số thứ tự -->
                    <td class="w-1">{{ $permission->role_name }}</td>
                    <td class="w-1">{{ $permission->actions }}</td>
                    <td class="w-1" data-table-sort-value="{{ $permission->created_at }}">
                        @dateWithUserTimezone($permission->created_at)
                    </td>

                </tr>
            @endforeach
        </tbody>
    </table>
</div>

@stop