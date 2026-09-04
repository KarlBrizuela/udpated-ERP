@props(['division' => 'super-admin'])

<div class="deznav modern-production-sidebar">
    <div class="deznav-scroll modern-sidebar-scroll">
        @if($division == 'super-admin')
            @include('components.sidebar.super-admin')
        @elseif($division == 'director')
            @include('components.sidebar.director')
        @elseif($division == 'admin-finance')
            @include('components.sidebar.admin-finance')
        @elseif($division == 'marketing')
            @include('components.sidebar.marketing')
        @elseif($division == 'production')
            @include('components.sidebar.production')
        @else
            @include('components.sidebar.unified')
        @endif
    </div>
    <div class="modern-sidebar-footer" style="padding: 14px 20px; border-top: 1px solid rgba(0,0,0,0.07); flex-shrink: 0;">
        <p style="font-size: 11px; color: #999; margin-bottom: 0;"><strong style="color:#555;">Claretian ERP</strong><br>© 2026 All Rights Reserved</p>
    </div>
</div>
