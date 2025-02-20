<nav class="side-nav bg-white rounded-2xl" style="border-radius: 1rem">
    <ul>
        <li>
            <a href="{{ route('dashboard.index') }}" class="logo {{ str_starts_with($ROUTE, 'dashboard.') ? 'active' : '' }}">
                <img src="/build/images/logo.png" alt="Logo">
            </a>
        </li>

        @include ('layouts.molecules.in-sidebar-menu')
    </ul>
</nav>
