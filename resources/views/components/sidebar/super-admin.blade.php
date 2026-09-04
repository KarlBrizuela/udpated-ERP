<nav class="modern-nav-menu">
	<!-- Dashboard -->
	<a href="{{ route('dashboard') }}" class="modern-nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}" data-page="dashboard">
		<div class="modern-nav-icon">
			<i class="flaticon-381-home-2"></i>
		</div>
		<span class="modern-nav-label">Dashboard</span>
	</a>
	
	<!-- User Management -->
	<a href="{{ route('users.index') }}" class="modern-nav-item {{ request()->routeIs('users.index') ? 'active' : '' }}" data-page="users">
		<div class="modern-nav-icon">
			<i class="las la-users"></i>
		</div>
		<span class="modern-nav-label">User Management</span>
	</a>
	
	<!-- Roles & Permissions -->
	<a href="{{ route('roles.index') }}" class="modern-nav-item {{ request()->routeIs('roles.index') ? 'active' : '' }}" data-page="roles">
		<div class="modern-nav-icon">
			<i class="las la-user-shield"></i>
		</div>
		<span class="modern-nav-label">Roles & Permissions</span>
	</a>
	

	<!-- System Settings -->
	<a href="{{ route('settings.index') }}" class="modern-nav-item {{ request()->routeIs('settings.index') ? 'active' : '' }}" data-page="settings">
		<div class="modern-nav-icon">
			<i class="las la-cog"></i>
		</div>
		<span class="modern-nav-label">System Settings</span>
	</a>
</nav>
