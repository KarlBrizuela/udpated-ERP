<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="keywords" content="admin, dashboard, erp, claretian" />
    <meta name="author" content="Claretian" />
    <meta name="robots" content="index, follow" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Claretian Bookstore Management System - Dashboard" />
    <meta property="og:title" content="Claretian - Dashboard" />
    <meta property="og:description" content="Claretian Bookstore Management System - Dashboard" />
    <meta name="format-detection" content="telephone=no">
    <title>{{ $title ?? 'Claretian - Dashboard' }}</title>
    <!-- Favicon icon -->
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('images/claeritian_logo.png') }}">
    <link rel="stylesheet" href="{{ asset('vendor/chartist/css/chartist.min.css') }}">
    <link href="{{ asset('vendor/bootstrap-select/dist/css/bootstrap-select.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">
    <link href="{{ asset('css/custom-fix.css') }}" rel="stylesheet">
    <link href="{{ asset('css/responsive.css') }}" rel="stylesheet">
    <style>
        @keyframes hidePreloader {
            0% { opacity: 1; visibility: visible; }
            99% { opacity: 0; visibility: visible; }
            100% { opacity: 0; visibility: hidden; pointer-events: none; }
        }
        #preloader {
            animation: hidePreloader 0.5s forwards;
            animation-delay: 2s; /* Fallback: hide after 2 seconds */
        }
    </style>
    @stack('styles')
</head>

<body>

    <!--*******************
        Preloader start
    ********************-->
    <div id="preloader">
        <div class="sk-three-bounce">
            <div class="sk-child sk-bounce1"></div>
            <div class="sk-child sk-bounce2"></div>
            <div class="sk-child sk-bounce3"></div>
        </div>
    </div>
    <!--*******************
        Preloader end
    ********************-->

    <!--**********************************
        Main wrapper start
    ***********************************-->
    <div id="main-wrapper" class="show" data-sidebar-style="full" data-header-position="fixed" data-sidebar-position="fixed" data-layout="vertical">

        <x-nav-header :home="$home ?? '/'" />

        <x-header :title="$title ?? 'Dashboard'" :role="$role ?? 'User Role'" />

        <x-sidebar :division="$sidebar ?? 'super-admin'" />

        <!--**********************************
            Content body start
        ***********************************-->
        <div class="content-body">
            <div class="container-fluid">
                @if(session('success'))
                <div class="alert alert-success solid alert-dismissible fade show">
                    <svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="me-2"><polyline points="9 11 12 14 22 4"></polyline><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"></path></svg>	
                    <strong>Success!</strong> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="btn-close"></button>
                </div>
                @endif

                @if(session('error'))
                <div class="alert alert-danger solid alert-dismissible fade show">
                    <svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="me-2"><polygon points="7.86 2 16.14 2 22 7.86 22 16.14 16.14 22 7.86 22 2 16.14 2 7.86 7.86 2"></polygon><line x1="15" y1="9" x2="9" y2="15"></line><line x1="9" y1="9" x2="15" y2="15"></line></svg>
                    <strong>Error!</strong> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="btn-close"></button>
                </div>
                @endif

                @if($errors->any())
                <div class="alert alert-danger solid alert-dismissible fade show">
                    <strong>Validation Error!</strong>
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="btn-close"></button>
                </div>
                @endif

                @yield('content')
            </div>
        </div>
        <!--**********************************
            Content body end
        ***********************************-->

        @stack('modals')


    </div>
    <!--**********************************
        Main wrapper end
    ***********************************-->

    <!--**********************************
        Scripts
    ***********************************-->
    <!-- Required vendors -->
    <script src="{{ asset('vendor/global/global.min.js') }}"></script>
    <script src="{{ asset('vendor/bootstrap-select/dist/js/bootstrap-select.min.js') }}"></script>
    <script src="{{ asset('vendor/chart.js/Chart.bundle.min.js') }}"></script>

    <!-- Chart piety plugin files -->
    <script src="{{ asset('vendor/peity/jquery.peity.min.js') }}"></script>

    <!-- Apex Chart -->
    <script src="{{ asset('vendor/apexchart/apexchart.js') }}"></script>

    <script src="{{ asset('js/custom.min.js') }}"></script>
    <script src="{{ asset('js/deznav-init.js') }}"></script>

    <script>
        // Custom sidebar initialization for Laravel
        (function($) {
            "use strict";
            
            $(document).ready(function() {
                // Handle submenus if they are not handled by custom.min.js
                $('.modern-nav-toggle').on('click', function(e) {
                    e.preventDefault();
                    var $group = $(this).closest('.modern-nav-group');
                    
                    if (!$group.hasClass('active')) {
                        $('.modern-nav-group.active').removeClass('active');
                    }
                    
                    $group.toggleClass('active');
                });
                
                // Ensure preloader hides
                setTimeout(function() {
                    $('#preloader').fadeOut(500);
                    $('#main-wrapper').addClass('show');
                }, 500);
            });
            
            $(window).on('load', function() {
                $('#preloader').fadeOut(500);
                $('#main-wrapper').addClass('show');
            });

            // Session Keep-Alive Ping (every 2 minutes)
            setInterval(function() {
                fetch('{{ route("session-keep-alive") }}')
                    .then(response => response.json())
                    .then(data => {
                        console.log('Session keep-alive status:', data.status);
                    })
                    .catch(err => {
                        console.warn('Session keep-alive ping failed:', err);
                    });
            }, 120000);
        })(jQuery);
    </script>

    @include('partials.rejection-modals')

    @stack('scripts')

</body>

</html>
