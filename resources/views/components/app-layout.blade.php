@props(['title', 'role', 'sidebar', 'home'])
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
    <div id="main-wrapper">

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

                {{ $slot }}
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
        // Bespoke Modal System
        (function() {
            const modalHtml = `
            <div id="bespoke-modal-wrapper" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; z-index:99999; backdrop-filter: blur(8px); background: rgba(0,0,0,0.5); align-items:center; justify-content:center; opacity:0; transition: opacity 0.3s ease;">
                <div id="bespoke-modal-card" style="background:#fff; width:400px; border-radius:15px; overflow:hidden; transform: scale(0.9); transition: transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1); box-shadow: 0 20px 40px rgba(0,0,0,0.2);">
                    <div id="bespoke-modal-header" style="padding:20px; text-align:center; border-bottom:1px solid #eee;">
                        <h4 id="bespoke-modal-title" style="margin:0; color:#333; font-weight:700;">Alert</h4>
                    </div>
                    <div id="bespoke-modal-body" style="padding:30px 20px; text-align:center; color:#555; font-size:16px;">
                        Hello World
                    </div>
                    <div id="bespoke-modal-footer" style="padding:15px; display:flex; gap:10px; background:#f9f9f9;">
                        <button id="bespoke-modal-cancel" style="flex:1; padding:12px; border:none; background:#eee; color:#666; border-radius:8px; font-weight:600; cursor:pointer; display:none;">Cancel</button>
                        <button id="bespoke-modal-confirm" style="flex:1; padding:12px; border:none; background:#ff0000; color:#fff; border-radius:8px; font-weight:600; cursor:pointer;">OK</button>
                    </div>
                </div>
            </div>`;
            
            document.body.insertAdjacentHTML('beforeend', modalHtml);
            
            const wrapper = document.getElementById('bespoke-modal-wrapper');
            const card = document.getElementById('bespoke-modal-card');
            const title = document.getElementById('bespoke-modal-title');
            const body = document.getElementById('bespoke-modal-body');
            const confirmBtn = document.getElementById('bespoke-modal-confirm');
            const cancelBtn = document.getElementById('bespoke-modal-cancel');
            
            let currentCallback = null;
            let hideTimeout = null;
            
            window.showAppModal = function(currTitle, message, options = {}) {
                title.innerText = currTitle || 'Notification';
                title.style.color = '#333';
                
                if (options.type === 'confirm') {
                    body.innerHTML = message;
                    cancelBtn.style.display = 'block';
                    cancelBtn.innerText = options.cancelText || 'Cancel';
                    confirmBtn.innerText = options.confirmText || 'Confirm';
                    confirmBtn.style.background = options.confirmColor || '#ff0000';
                    currentCallback = options.onConfirm || null;
                } else if (options.type === 'input') {
                    cancelBtn.style.display = 'block';
                    cancelBtn.innerText = options.cancelText || 'Cancel';
                    confirmBtn.innerText = options.confirmText || 'Confirm';
                    confirmBtn.style.background = options.confirmColor || '#ff0000';
                    
                    const placeholder = options.placeholder || 'Enter reason...';
                    body.innerHTML = `
                        <div class="text-start mb-2">${message}</div>
                        <textarea id="bespoke-modal-input-field" class="form-control" rows="3" placeholder="${placeholder}" style="width:100%; border:1px solid #ced4da; border-radius:8px; padding:10px; font-size:14px; color:#333; resize:vertical; margin-top:10px;"></textarea>
                    `;
                    
                    currentCallback = function() {
                        const inputEl = document.getElementById('bespoke-modal-input-field');
                        const val = inputEl ? inputEl.value : '';
                        if (typeof options.onConfirm === 'function') {
                            options.onConfirm(val);
                        }
                    };
                } else {
                    body.innerHTML = message;
                    cancelBtn.style.display = 'none';
                    confirmBtn.innerText = 'OK';
                    confirmBtn.style.background = '#ff0000';
                    currentCallback = null;
                }
                
                showModal();
                if (options.type === 'input') {
                    setTimeout(() => {
                        const inputEl = document.getElementById('bespoke-modal-input-field');
                        if (inputEl) inputEl.focus();
                    }, 200);
                }
            };

            // Keep backward compatibility if needed, or alias them
            window.showAlert = function(message, type = 'info') {
               showAppModal(type.toUpperCase(), message, { type: 'alert' });
            };
            
            window.showConfirm = function(message, callback) {
                showAppModal('Confirm Action', message, { 
                    type: 'confirm', 
                    onConfirm: callback 
                });
            };
            
            function showModal() {
                if (hideTimeout) {
                    clearTimeout(hideTimeout);
                    hideTimeout = null;
                }
                wrapper.style.display = 'flex';
                setTimeout(() => {
                    wrapper.style.opacity = '1';
                    card.style.transform = 'scale(1)';
                }, 10);
            }
            
            function hideModal() {
                wrapper.style.opacity = '0';
                card.style.transform = 'scale(0.9)';
                hideTimeout = setTimeout(() => {
                    wrapper.style.display = 'none';
                    hideTimeout = null;
                }, 300);
            }
            
            confirmBtn.onclick = function() {
                hideModal();
                if (currentCallback) currentCallback();
            };
            
            cancelBtn.onclick = function() {
                hideModal();
            };
            
            // Re-map sidebar initialization for Laravel
            $(document).ready(function() {
                $('.modern-nav-toggle').on('click', function(e) {
                    e.preventDefault();
                    var $group = $(this).closest('.modern-nav-group');
                    if (!$group.hasClass('active')) {
                        $('.modern-nav-group.active').removeClass('active');
                    }
                    $group.toggleClass('active');
                });
                
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
        })();
    </script>

    @include('partials.rejection-modals')

    @stack('scripts')

</body>

</html>
