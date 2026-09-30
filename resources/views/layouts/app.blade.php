<!doctype html>
<html lang="en">
<!--begin::Head-->

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>AdminLTE v4 | Dashboard</title>

    <!--begin::Accessibility Meta Tags-->
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes" />
    <meta name="color-scheme" content="light dark" />
    <meta name="theme-color" content="#007bff" media="(prefers-color-scheme: light)" />
    <meta name="theme-color" content="#1a1a1a" media="(prefers-color-scheme: dark)" />
    <!--end::Accessibility Meta Tags-->

    <!--begin::Primary Meta Tags-->
    <meta name="title" content="AdminLTE v4 | Dashboard" />
    <meta name="author" content="ColorlibHQ" />
    <meta name="description"
        content="AdminLTE is a Free Bootstrap 5 Admin Dashboard, 30 example pages using Vanilla JS. Fully accessible with WCAG 2.1 AA compliance." />
    <meta name="keywords"
        content="bootstrap 5, bootstrap, bootstrap 5 admin dashboard, bootstrap 5 dashboard, bootstrap 5 charts, bootstrap 5 calendar, bootstrap 5 datepicker, bootstrap 5 tables, bootstrap 5 datatable, vanilla js datatable, colorlibhq, colorlibhq dashboard, colorlibhq admin dashboard, accessible admin panel, WCAG compliant" />
    <!--end::Primary Meta Tags-->

    <!--begin::Accessibility Features-->
    <!-- Skip links will be dynamically added by accessibility.js -->
    <meta name="supported-color-schemes" content="light dark" />
    <link rel="stylesheet" href="{{ asset('assets/css/adminlte.css') }}" as="style" />
    <!--end::Accessibility Features-->

    <!--begin::Fonts-->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fontsource/source-sans-3@5.0.12/index.css"
        integrity="sha256-tXJfXfp6Ewt1ilPzLDtQnJV4hclT9XuaZUKyUvmyr+Q=" crossorigin="anonymous" media="print"
        onload="this.media = 'all'" />
    <!--end::Fonts-->

    <!--begin::Third Party Plugin(OverlayScrollbars)-->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/overlayscrollbars@2.11.0/styles/overlayscrollbars.min.css"
        crossorigin="anonymous" />
    <!--end::Third Party Plugin(OverlayScrollbars)-->

    <!--begin::Third Party Plugin(Bootstrap Icons)-->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css"
        crossorigin="anonymous" />
    <!--end::Third Party Plugin(Bootstrap Icons)-->

    <!--begin::Required Plugin(AdminLTE)-->
    <link rel="stylesheet" href="{{ asset('assets/css/adminlte.css') }}" />
    <!--end::Required Plugin(AdminLTE)-->

    <!-- apexcharts -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/apexcharts@3.37.1/dist/apexcharts.css"
        integrity="sha256-4MX+61mt9NVvvuPjUWdUdyfZfxSB1/Rf9WtqRHgG5S0=" crossorigin="anonymous" />

    <!-- jsvectormap -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/jsvectormap@1.5.3/dist/css/jsvectormap.min.css"
        integrity="sha256-+uGLJmmTKOqBr+2E6KDYs/NRsHxSkONXFHUL0fy2O/4=" crossorigin="anonymous" />
</head>
<!--end::Head-->
<!--begin::Body-->

<body class="layout-fixed sidebar-expand-lg bg-body-tertiary">
    <!--begin::App Wrapper-->
    <div class="app-wrapper">
        <!--begin::Header-->
        <header>
            <nav class="app-header navbar navbar-expand bg-body">
                <!--begin::Container-->
                <div class="container-fluid">
                    <!--begin::Start Navbar Links-->
                    <ul class="navbar-nav align-items-center">
                        <li class="nav-item">
                            <a class="nav-link" data-lte-toggle="sidebar" href="#" role="button" title="Toggle Sidebar">
                                <i class="bi bi-list fs-4"></i>
                            </a>
                        </li>
                    </ul>
                    <!--end::Start Navbar Links-->

                    <!--begin::End Navbar Links-->
                    <ul class="navbar-nav ms-auto align-items-center gap-1">
                        {{-- WhatsApp Icon --}}
                        <li class="nav-item">
                            <a class="nav-link text-success px-2" href="https://wa.me/{{ preg_replace('/[^0-9]/', '', config('services.contact.whatsapp', '923000000000')) }}" target="_blank" title="WhatsApp: {{ config('services.contact.whatsapp', '+92 300 0000000') }}">
                                <i class="bi bi-whatsapp fs-5"></i>
                            </a>
                        </li>

                        {{-- Email Icon --}}
                        <li class="nav-item">
                            <a class="nav-link text-primary px-2" href="mailto:{{ config('services.contact.email', auth()->user()->email ?? 'admin@school.com') }}" title="Email: {{ config('services.contact.email', 'admin@school.com') }}">
                                <i class="bi bi-envelope-fill fs-5"></i>
                            </a>
                        </li>

                        {{-- Bell Icon (Shown ONLY when any new admission or fee voucher is issued or updated) --}}
                        @if(isset($notificationsCount) && $notificationsCount > 0)
                            <li class="nav-item dropdown">
                                <a class="nav-link px-2 text-warning position-relative" data-bs-toggle="dropdown" href="#" title="{{ $notificationsCount }} Updates: Admissions & Fee Vouchers">
                                    <i class="bi bi-bell-fill fs-5"></i>
                                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 0.65rem;">
                                        {{ $notificationsCount }}
                                    </span>
                                </a>
                                <div class="dropdown-menu dropdown-menu-lg dropdown-menu-end shadow border-0 py-0 mt-2">
                                    <div class="dropdown-header bg-light fw-bold py-2 border-bottom d-flex justify-content-between align-items-center">
                                        <span><i class="bi bi-bell-fill me-1 text-warning"></i> Updates ({{ $notificationsCount }})</span>
                                        <span class="badge bg-danger-subtle text-danger small">Admissions & Fees</span>
                                    </div>
                                    <div style="max-height: 320px; overflow-y: auto;">
                                        @foreach($systemNotifications as $notif)
                                            <a href="{{ $notif['url'] }}" class="dropdown-item py-2 px-3 border-bottom d-flex align-items-start gap-2">
                                                <i class="bi {{ $notif['icon'] }} {{ $notif['color'] }} fs-5 mt-1"></i>
                                                <div class="flex-grow-1 text-wrap">
                                                    <div class="fw-semibold text-dark small mb-0">{{ $notif['title'] }}</div>
                                                    <div class="text-secondary" style="font-size: 11px;">{{ $notif['subtitle'] }}</div>
                                                    <div class="text-muted" style="font-size: 10px;"><i class="bi bi-clock me-1"></i>{{ $notif['time'] }}</div>
                                                </div>
                                            </a>
                                        @endforeach
                                    </div>
                                    <div class="p-2 bg-light text-center border-top">
                                        <span class="small text-muted">Showing recent admissions & fee updates</span>
                                    </div>
                                </div>
                            </li>
                        @endif

                        <!--begin::User Menu Dropdown-->
                        @auth
                            <li class="nav-item dropdown user-menu ms-1">
                                <a href="#" class="nav-link dropdown-toggle d-flex align-items-center gap-2 py-1 px-2" data-bs-toggle="dropdown">
                                    <img src="{{ auth()->user()->avatar_url }}" class="rounded-circle shadow-sm border object-fit-cover" alt="{{ auth()->user()->name }}" style="width: 34px; height: 34px;" />
                                    <span class="d-none d-md-inline fw-semibold text-dark small">{{ auth()->user()->name }}</span>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end shadow border-0 p-2 mt-2" style="min-width: 230px;">
                                    <li class="px-3 py-2 text-center border-bottom mb-2">
                                        <img src="{{ auth()->user()->avatar_url }}" class="rounded-circle shadow-sm border object-fit-cover mb-2" alt="{{ auth()->user()->name }}" style="width: 60px; height: 60px;" />
                                        <div class="fw-bold text-dark">{{ auth()->user()->name }}</div>
                                        <span class="badge bg-primary-subtle text-primary small text-uppercase">{{ auth()->user()->roles->pluck('name')->join(', ') ?: 'User' }}</span>
                                        <div class="small text-muted text-truncate mt-1">{{ auth()->user()->email }}</div>
                                    </li>
                                    <li>
                                        <a class="dropdown-item rounded-2 py-2" href="{{ route('profile.edit') }}">
                                            <i class="bi bi-person-gear me-2 text-primary"></i> Profile & Picture
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item rounded-2 py-2" href="{{ route('home') }}">
                                            <i class="bi bi-speedometer2 me-2 text-info"></i> Dashboard
                                        </a>
                                    </li>
                                    <li><hr class="dropdown-divider my-1"></li>
                                    <li>
                                        <form action="{{ route('logout') }}" method="POST" class="m-0">
                                            @csrf
                                            <button type="submit" class="dropdown-item text-danger rounded-2 py-2">
                                                <i class="bi bi-box-arrow-right me-2"></i> Sign Out
                                            </button>
                                        </form>
                                    </li>
                                </ul>
                            </li>
                        @endauth
                        <!--end::User Menu Dropdown-->
                    </ul>
                    <!--end::End Navbar Links-->
                </div>
                <!--end::Container-->
            </nav>
        </header>
        <!--end::Header-->
        <!--begin::Sidebar-->
        <aside class="app-sidebar bg-body-secondary shadow" data-bs-theme="dark">
            <!--begin::Sidebar Brand-->
            <div class="sidebar-brand">
                <!--begin::Brand Link-->
                <a href="./index.html" class="brand-link">
                    <!--begin::Brand Image-->
                    <img src="{{ asset('assets/images/107.jpg') }}" alt="AdminLTE Logo"
                        class="brand-image opacity-75 shadow" />
                    <!--end::Brand Image-->
                    <!--begin::Brand Text-->
                    <span class="brand-text fw-light">CMS</span>
                    <!--end::Brand Text-->
                </a>
                <!--end::Brand Link-->
            </div>
            <!--end::Sidebar Brand-->
            <!--begin::Sidebar Wrapper-->
            <div class="sidebar-wrapper">
                <nav class="mt-2">
                    <!--begin::Sidebar Menu-->
                    <ul class="nav sidebar-menu flex-column" data-lte-toggle="treeview" role="navigation"
                        aria-label="Main navigation" data-accordion="false" id="navigation">
                        <li class="nav-item">
                            <a href="{{ route('home') }}" class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}">
                                <i class="nav-icon bi bi-speedometer2"></i>
                                <p>Dashboard</p>
                            </a>
                        </li>

                        @can ('show student')
                            <li class="nav-item">
                                <a href="{{ route('students.index') }}" class="nav-link {{ request()->routeIs('students.*') ? 'active' : '' }}">
                                    <i class="nav-icon bi bi-mortarboard-fill"></i>
                                    <p>Students</p>
                                </a>
                            </li>
                        @endcan

                        @can('show teacher')
                            <li class="nav-item">
                                <a href="{{ route('teachers.index') }}" class="nav-link {{ request()->routeIs('teachers.*') ? 'active' : '' }}">
                                    <i class="nav-icon bi bi-person-workspace"></i>
                                    <p>Teachers</p>
                                </a>
                            </li>
                        @endcan

                        @can('show class')
                            <li class="nav-item">
                                <a href="{{ route('classes.index') }}" class="nav-link {{ request()->routeIs('classes.*') ? 'active' : '' }}">
                                    <i class="nav-icon bi bi-diagram-3-fill"></i>
                                    <p>Classes & Sections</p>
                                </a>
                            </li>
                        @endcan

                        @can('show accountant')
                            <li class="nav-item">
                                <a href="{{ route('accountant.index') }}" class="nav-link {{ request()->routeIs('accountant.*') ? 'active' : '' }}">
                                    <i class="nav-icon bi bi-cash-stack"></i>
                                    <p>Fees & Accounts</p>
                                </a>
                            </li>
                        @endcan

                        @can ('show admin')
                            <li class="nav-item">
                                <a href="{{ route('admin') }}" class="nav-link {{ request()->routeIs('admin*') ? 'active' : '' }}">
                                    <i class="nav-icon bi bi-shield-lock-fill"></i>
                                    <p>Admin Center</p>
                                </a>
                            </li>
                        @endcan

                        @canany(['see roles', 'see permissions', 'see users'])
                            <li class="nav-header text-uppercase fs-7 text-secondary mt-2 px-3">System Access</li>
                        @endcanany

                        @can('see roles')
                            <li class="nav-item">
                                <a href="{{ route('roles.index') }}" class="nav-link {{ request()->routeIs('roles.*') ? 'active' : '' }}">
                                    <i class="nav-icon bi bi-person-badge"></i>
                                    <p>Roles</p>
                                </a>
                            </li>
                        @endcan

                        @can('see permissions')
                            <li class="nav-item">
                                <a href="{{ route('permissions.index') }}" class="nav-link {{ request()->routeIs('permissions.*') ? 'active' : '' }}">
                                    <i class="nav-icon bi bi-key-fill"></i>
                                    <p>Permissions</p>
                                </a>
                            </li>
                        @endcan

                        @can ('see users')
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}" href="{{ route('users.index') }}">
                                    <i class="nav-icon bi bi-people-fill"></i>
                                    <p>Users</p>
                                </a>
                            </li>
                        @endcan

                    </ul>
                    <!--end::Sidebar Menu-->
                </nav>
            </div>
            <!--end::Sidebar Wrapper-->
        </aside>
        <!--end::Sidebar-->


        <main>
            <!-- This is where the main content of each page will be injected -->
            @yield('content')
        </main>

        <!--begin::Footer-->
        <footer class="app-footer">
            <!--begin::To the end-->
            <div class="float-end d-none d-sm-inline">Anything you want</div>
            <!--end::To the end-->
            <!--begin::Copyright-->
            <strong>
                Copyright &copy; 2014-2025&nbsp;
                <a href="https://adminlte.io" class="text-decoration-none">AdminLTE.io</a>.
            </strong>
            All rights reserved.
            <!--end::Copyright-->
        </footer>
        <!--end::Footer-->
    </div>
    <!--end::App Wrapper-->
    <!--begin::Script-->
    <!--begin::Third Party Plugin(OverlayScrollbars)-->
    <script src="https://cdn.jsdelivr.net/npm/overlayscrollbars@2.11.0/browser/overlayscrollbars.browser.es6.min.js"
        crossorigin="anonymous"></script>
    <!--end::Third Party Plugin(OverlayScrollbars)--><!--begin::Required Plugin(popperjs for Bootstrap 5)-->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"
        crossorigin="anonymous"></script>
    <!--end::Required Plugin(popperjs for Bootstrap 5)--><!--begin::Required Plugin(Bootstrap 5)-->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.min.js"
        crossorigin="anonymous"></script>
    <!--end::Required Plugin(Bootstrap 5)--><!--begin::Required Plugin(AdminLTE)-->
    <script src="{{ asset('assets/js/adminlte.js') }}"></script>
    <!--end::Required Plugin(AdminLTE)--><!--begin::OverlayScrollbars Configure-->
    <script>
        const SELECTOR_SIDEBAR_WRAPPER = '.sidebar-wrapper';
        const Default = {
            scrollbarTheme: 'os-theme-light',
            scrollbarAutoHide: 'leave',
            scrollbarClickScroll: true,
        };
        document.addEventListener('DOMContentLoaded', function () {
            const sidebarWrapper = document.querySelector(SELECTOR_SIDEBAR_WRAPPER);

            // Disable OverlayScrollbars on mobile devices to prevent touch interference
            const isMobile = window.innerWidth <= 992;

            if (
                sidebarWrapper &&
                OverlayScrollbarsGlobal?.OverlayScrollbars !== undefined &&
                !isMobile
            ) {
                OverlayScrollbarsGlobal.OverlayScrollbars(sidebarWrapper, {
                    scrollbars: {
                        theme: Default.scrollbarTheme,
                        autoHide: Default.scrollbarAutoHide,
                        clickScroll: Default.scrollbarClickScroll,
                    },
                });
            }
        });
    </script>
    <!--end::OverlayScrollbars Configure-->

    <!-- OPTIONAL SCRIPTS -->

    <!-- sortablejs -->
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js" crossorigin="anonymous"></script>

    <!-- sortablejs -->
    <script>
        const sortableElem = document.querySelector('.connectedSortable');
        if (sortableElem) {
            new Sortable(sortableElem, {
                group: 'shared',
                handle: '.card-header',
            });

            const cardHeaders = document.querySelectorAll('.connectedSortable .card-header');
            cardHeaders.forEach((cardHeader) => {
                cardHeader.style.cursor = 'move';
            });
        }
    </script>

    <!-- apexcharts -->
    <script src="https://cdn.jsdelivr.net/npm/apexcharts@3.37.1/dist/apexcharts.min.js"
        integrity="sha256-+vh8GkaU7C9/wbSLIcwq82tQ2wTf44aOHA8HlBMwRI8=" crossorigin="anonymous"></script>

    @stack('scripts')
    <!--end::Script-->
</body>
<!--end::Body-->

</html>