@props(['title' => 'Page Title', 'role' => auth()->user()?->position ?? 'User Role'])

<div class="header">
	<div class="header-content">
		<nav class="navbar navbar-expand">
			<div class="collapse navbar-collapse justify-content-between">
				<div class="header-left">
					<div class="dashboard_bar" data-page-title>
						{{ $title }}
					</div>
				</div>
				<ul class="navbar-nav header-right">
					<li class="nav-item dropdown header-profile">
						<a class="nav-link" href="javascript:void(0);" role="button" data-bs-toggle="dropdown" data-toggle="dropdown">
							<div class="header-info">
								<span>{{ auth()->user()?->name ?? 'User' }}</span>
								<small data-user-role>{{ auth()->user()?->position ?? '' }}</small>
							</div>
						</a>
						<div class="dropdown-menu dropdown-menu-end">
							<a href="{{ route('profile') }}" class="dropdown-item ai-icon">
								<svg id="icon-user1" xmlns="http://www.w3.org/2000/svg" class="text-primary" width="18"
									height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
									stroke-linecap="round" stroke-linejoin="round">
									<path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
									<circle cx="12" cy="7" r="4"></circle>
								</svg>
								<span class="ms-2">Profile </span>
							</a>
							
@php
    $userDivisions = auth()->user()?->divisions ?? collect();
    $isSuperAdmin = auth()->user()?->isSuperAdmin() ?? false;
    $showSwitch = $isSuperAdmin || $userDivisions->count() > 1;
    
    // Divisions for Super Admin to switch between
    $allDivisions = [
        ['division' => 'Marketing Division', 'label' => 'Marketing Division'],
        ['division' => 'Production Division', 'label' => 'Production Division'],
        ['division' => 'Admin & Finance Division', 'label' => 'Admin & Finance Division'],
    ];
@endphp

@if(auth()->check() && $showSwitch)
    <div class="dropdown-divider"></div>
    <h6 class="dropdown-header">Switch Division</h6>
    
    @if($isSuperAdmin)
        <a href="{{ route('dashboard') }}" class="dropdown-item ai-icon {{ request()->is('/') ? 'active' : '' }}">
            <svg id="icon-dashboard" xmlns="http://www.w3.org/2000/svg" class="text-primary"
                width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <rect x="3" y="3" width="7" height="7"></rect>
                <rect x="14" y="3" width="7" height="7"></rect>
                <rect x="14" y="14" width="7" height="7"></rect>
                <rect x="3" y="14" width="7" height="7"></rect>
            </svg>
            <span class="ms-2">Super Admin Dashboard</span>
        </a>

        @foreach($allDivisions as $div)
            @php
                $route = '#';
                $iconClass = 'text-primary';
                $iconId = 'icon-admin';
                $label = $div['label'];

                if ($div['division'] == 'Marketing Division') {
                    $route = route('marketing.dashboard');
                    $iconClass = 'text-success';
                    $iconId = 'icon-marketing';
                } elseif ($div['division'] == 'Production Division') {
                    $route = route('production.dashboard');
                    $iconClass = 'text-warning';
                    $iconId = 'icon-production';
                } elseif ($div['division'] == 'Admin & Finance Division') {
                    $route = route('admin-finance.dashboard');
                    $iconClass = 'text-info';
                    $iconId = 'icon-admin';
                }
            @endphp
            
            <a href="{{ $route }}" class="dropdown-item ai-icon">
                <svg id="{{ $iconId }}" xmlns="http://www.w3.org/2000/svg" class="{{ $iconClass }}"
                    width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"></circle>
                    <line x1="12" y1="8" x2="12" y2="16"></line>
                    <line x1="8" y1="12" x2="16" y2="12"></line>
                </svg>
                <span class="ms-2">{{ $label }}</span>
            </a>
        @endforeach
    @else
        @foreach($userDivisions as $div)
            @php
                $route = '#';
                $iconClass = 'text-primary';
                $iconId = 'icon-admin';
                $label = $div->division;

                if ($div->division == 'Marketing Division') {
                    $route = route('marketing.dashboard');
                    $iconClass = 'text-success';
                    $iconId = 'icon-marketing';
                } elseif ($div->division == 'Production Division') {
                    $route = route('production.dashboard');
                    $iconClass = 'text-warning';
                    $iconId = 'icon-production';
                } elseif ($div->division == 'Admin & Finance Division') {
                    $route = route('admin-finance.dashboard');
                    $iconClass = 'text-info';
                    $iconId = 'icon-admin';
                }
            @endphp
            
            <a href="{{ $route }}" class="dropdown-item ai-icon">
                <svg id="{{ $iconId }}" xmlns="http://www.w3.org/2000/svg" class="{{ $iconClass }}"
                    width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"></circle>
                    <line x1="12" y1="8" x2="12" y2="16"></line>
                    <line x1="8" y1="12" x2="16" y2="12"></line>
                </svg>
                <span class="ms-2">{{ $label }}</span>
            </a>
        @endforeach
    @endif
@endif
							
							<div class="dropdown-divider"></div>
							
							<form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
								@csrf
							</form>
							<a href="#" class="dropdown-item ai-icon" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
								<svg id="icon-logout" xmlns="http://www.w3.org/2000/svg" class="text-danger" width="18"
									height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
									stroke-linecap="round" stroke-linejoin="round">
									<path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
									<polyline points="16 17 21 12 16 7"></polyline>
									<line x1="21" y1="12" x2="9" y2="12"></line>
								</svg>
								<span class="ms-2">Logout </span>
							</a>
						</div>
					</li>
				</ul>
			</div>
		</nav>
	</div>
</div>
