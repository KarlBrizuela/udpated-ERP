<nav class="modern-nav-menu">
    <!-- Dashboard -->
    <a href="{{ route('dashboard') }}" class="modern-nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}" data-page="dashboard">
        <div class="modern-nav-icon">
            <i class="flaticon-381-home-2"></i>
        </div>
        <span class="modern-nav-label">Dashboard</span>
    </a>

    <!-- Approval Queue -->
    <a href="{{ route('admin-finance.approval-queue') }}" class="modern-nav-item {{ request()->routeIs('*approval-queue') ? 'active' : '' }}" data-page="approval-queue">
        <div class="modern-nav-icon">
            <i class="las la-clipboard-check"></i>
        </div>
        <span class="modern-nav-label">Approval Queue</span>
    </a>
    
    <!-- Production Division -->
    <a href="{{ route('production.dashboard') }}" class="modern-nav-item {{ request()->routeIs('production.*') ? 'active' : '' }}" data-page="production">
        <div class="modern-nav-icon">
            <i class="las la-industry"></i>
        </div>
        <span class="modern-nav-label">Production</span>
    </a>

    <!-- Marketing Division -->
    <a href="{{ route('marketing.dashboard') }}" class="modern-nav-item {{ request()->routeIs('marketing.*') ? 'active' : '' }}" data-page="marketing">
        <div class="modern-nav-icon">
            <i class="las la-bullhorn"></i>
        </div>
        <span class="modern-nav-label">Marketing</span>
    </a>

    <!-- Admin & Finance Division -->
    <a href="{{ route('admin-finance.dashboard') }}" class="modern-nav-item {{ request()->routeIs('admin-finance.*') ? 'active' : '' }}" data-page="admin-finance">
        <div class="modern-nav-icon">
            <i class="las la-balance-scale"></i>
        </div>
        <span class="modern-nav-label">Admin & Finance</span>
    </a>
    
    <!-- User Management -->
    <a href="{{ route('users.index') }}" class="modern-nav-item {{ request()->routeIs('users.index') ? 'active' : '' }}" data-page="users">
        <div class="modern-nav-icon">
            <i class="las la-users"></i>
        </div>
        <span class="modern-nav-label">User Management</span>
    </a>
</nav>
