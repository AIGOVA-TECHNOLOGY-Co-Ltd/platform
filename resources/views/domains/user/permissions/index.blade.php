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
            <a href="{{ route('user.permissions.create') }}" class="btn form-control-lg whitespace-nowrap">
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
                <th class="w-1">{{ __('Actions') }}</th>
            </tr>
        </thead>

        <tbody>
            @foreach ($permissions as $permission)
            @php ($link = route('user.permissions.edit', ['role_id' => $permission->role_id]))            <!-- Sử dụng role_id thay vì id -->
            <tr>
                @if ($user_empty)
                    <td><a href="{{ $link }}" class="block">{{ $permission->user->name ?? '-' }}</a></td>
                @endif
                <td class="w-1"><a href="{{ $link }}">{{ $permission->stt }}</a></td>
                <td class="w-1"><a href="{{ $link }}">{{ $permission->role_name }}</a></td>
                <td class="w-1"><a href="{{ $link }}">{{ $permission->actions }}</a></td>
                <td class="w-1" data-table-sort-value="{{ $permission->created_at }}">
                    <a href="{{ $link }}">@dateWithUserTimezone($permission->created_at)</a>
                </td>
                <td class="w-1">
                    <!-- Edit permission -->
                    <a href="{{ route('user.permissions.edit', ['role_id' => $permission->role_id]) }}"
                        class="btn btn-primary btn-sm">
                        {{ __('Edit') }}
                    </a>
                    <!-- Form xóa permission -->
                    <form action="{{ route('user.permissions.delete', ['role_id' => $permission->role_id]) }}"
                        method="POST" style="display:inline;"
                        onsubmit="return confirm('Are you sure you want to delete this permission?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm">
                            {{ __('Delete') }}
                        </button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

@stop