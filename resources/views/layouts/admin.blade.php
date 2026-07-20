<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="icon" href="{{ asset('assets/img/favicon.svg') }}" type="image/svg+xml">
    <title>@yield('title', 'SmartKasir Dashboard')</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Stylesheets -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    
    <!-- Admin Template styles -->
    <link href="{{ asset('sb-admin/css/sb-admin-2.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/css/global-overrides.css') }}">
    
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <!-- DataTables JS & CSS bridged from standard paths -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif !important;
            background-color: #0b0b0f !important;
            color: #f4f4f7 !important;
        }
        h1, h2, h3, h4, h5, h6, .sidebar-brand-text {
            font-family: 'Outfit', sans-serif !important;
        }
        
        /* Persistent layout custom overrides */
        #wrapper {
            background-color: #0b0b0f !important;
        }
        #content-wrapper {
            background-color: #0b0b0f !important;
            min-height: 100vh;
        }
        
        .sidebar {
            background: rgba(15, 15, 22, 0.8) !important;
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-right: 1px solid rgba(255, 255, 255, 0.05);
            position: relative;
            z-index: 1000;
        }
        
        nav.navbar.topbar {
            background-color: rgba(15, 15, 22, 0.6) !important;
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        }
        
        .collapse-inner {
            background: rgba(11, 11, 15, 0.95) !important;
            border: 1px solid rgba(255, 255, 255, 0.06);
        }
        
        .collapse-item {
            color: rgba(255, 255, 255, 0.7) !important;
            transition: all 0.2s;
        }
        .collapse-item:hover, .collapse-item.active {
            background: rgba(168, 85, 247, 0.15) !important;
            color: #c084fc !important;
        }
        
        /* Fix sidebar submenu hover gap when collapsed */
        @media (min-width: 768px) {
            .sidebar.toggled .nav-item {
                position: relative !important;
            }
            .sidebar.toggled .nav-item .collapse {
                position: absolute !important;
                left: 100% !important;
                padding-left: 8px !important;
                z-index: 1000 !important;
                display: none !important;
                width: 11rem !important;
            }
            .sidebar.toggled .nav-item:hover .collapse {
                display: block !important;
            }
            .sidebar.toggled .nav-item .collapse .collapse-inner {
                margin: 0 !important;
                box-shadow: 0 10px 30px rgba(0,0,0,0.5) !important;
            }
        }
    </style>
</head>

<body id="page-top">
    <!-- Page Wrapper -->
    <div id="wrapper">

        <!-- Sidebar -->
        <ul class="navbar-nav bg-gradient-success sidebar sidebar-dark accordion" id="accordionSidebar">
            <!-- Sidebar - Brand -->
            <a class="sidebar-brand d-flex align-items-center justify-content-center" href="{{ route('home') }}">
                <div class="sidebar-brand-icon rotate-n-15">
                    <i class="fas fa-cash-register text-primary"></i>
                </div>
                <div class="sidebar-brand-text mx-3">SMARTKASIR</div>
            </a>

            <!-- Divider -->
            <hr class="sidebar-divider my-0">

            <!-- Nav Item - Dashboard -->
            <li class="nav-item {{ !request()->has('page') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('dashboard') }}">
                    <i class="fas fa-fw fa-tachometer-alt"></i>
                    <span>Dasbor</span>
                </a>
            </li>

            @if((int)session('status') === 1)
                <!-- Divider -->
                <hr class="sidebar-divider">

                <!-- Data Master Menu -->
                <li class="nav-item {{ request('page') === 'barang' || request('page') === 'kategori' || request('page') === 'promo' ? 'active' : '' }}">
                    <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="true" aria-controls="collapseTwo">
                        <i class="fas fa-fw fa-database"></i>
                        <span>Data Master</span>
                    </a>
                    <div id="collapseTwo" class="collapse {{ request('page') === 'barang' || request('page') === 'kategori' || request('page') === 'promo' ? 'show' : '' }}" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
                        <div class="py-2 collapse-inner rounded">
                            <a class="collapse-item {{ request('page') === 'barang' ? 'active' : '' }}" href="{{ route('dashboard', ['page' => 'barang']) }}">Barang</a>
                            <a class="collapse-item {{ request('page') === 'kategori' ? 'active' : '' }}" href="{{ route('dashboard', ['page' => 'kategori']) }}">Kategori</a>
                            <a class="collapse-item {{ request('page') === 'promo' ? 'active' : '' }}" href="{{ route('dashboard', ['page' => 'promo']) }}">Promo</a>
                        </div>
                    </div>
                </li>

                <!-- Transaksi Menu -->
                <li class="nav-item {{ request('page') === 'jual' || request('page') === 'laporan' ? 'active' : '' }}">
                    <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#collapse3" aria-expanded="true" aria-controls="collapse3">
                        <i class="fas fa-fw fa-desktop"></i>
                        <span>Transaksi</span>
                    </a>
                    <div id="collapse3" class="collapse {{ request('page') === 'jual' || request('page') === 'laporan' ? 'show' : '' }}" aria-labelledby="heading3" data-parent="#accordionSidebar">
                        <div class="py-2 collapse-inner rounded">
                            <a class="collapse-item {{ request('page') === 'jual' ? 'active' : '' }}" href="{{ route('dashboard', ['page' => 'jual']) }}">Transaksi Jual</a>
                            @if(session('role') === 'manajer')
                                <a class="collapse-item {{ request('page') === 'laporan' ? 'active' : '' }}" href="{{ route('dashboard', ['page' => 'laporan']) }}">Laporan Penjualan</a>
                            @endif
                        </div>
                    </div>
                </li>

                @if(session('role') === 'manajer')
                    <hr class="sidebar-divider">
                    <li class="nav-item {{ request('page') === 'kasir' ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('dashboard', ['page' => 'kasir']) }}">
                            <i class="fas fa-fw fa-users"></i>
                            <span>Manajemen Kasir</span>
                        </a>
                    </li>
                    
                    <hr class="sidebar-divider">
                    <li class="nav-item {{ request('page') === 'pengaturan' ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('dashboard', ['page' => 'pengaturan']) }}">
                            <i class="fas fa-fw fa-cogs"></i>
                            <span>Pengaturan Toko</span>
                        </a>
                    </li>
                    
                    <hr class="sidebar-divider">
                    <li class="nav-item {{ request('page') === 'pesan' ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('dashboard', ['page' => 'pesan']) }}">
                            <i class="fas fa-fw fa-envelope"></i>
                            <span>Pesan Kontak</span>
                        </a>
                    </li>
                @endif
            @endif

            <!-- Divider -->
            <hr class="sidebar-divider d-none d-md-block">

            <!-- Sidebar Toggler -->
            <div class="text-center d-none d-md-inline">
                <button class="rounded-circle border-0" id="sidebarToggle"></button>
            </div>
        </ul>
        <!-- End of Sidebar -->

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">

            <!-- Main Content -->
            <div id="content">

                <!-- Topbar -->
                <nav class="navbar navbar-expand navbar-light topbar mb-4 static-top shadow">
                    <!-- Sidebar Toggle (Topbar) -->
                    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                        <i class="fa fa-bars text-secondary"></i>
                    </button>
                    
                    <h5 class="d-lg-block d-none mt-2 text-white fw-semibold">
                        <b>{{ $toko->nama_toko ?? 'SmartKasir' }}, {{ $toko->alamat_toko ?? 'Indonesia' }}</b>
                    </h5>
                    
                    <!-- Topbar Navbar -->
                    <ul class="navbar-nav ml-auto">
                        <!-- Nav Item - User Information -->
                        <li class="nav-item dropdown no-arrow">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <img class="img-profile rounded-circle me-2" src="{{ asset('assets/img/user/' . (session('admin')['gambar'] ?? 'default.jpg')) }}" width="32" height="32">
                                <span class="d-none d-lg-inline text-white-50 small">{{ session('admin')['nm_member'] ?? 'Admin' }}</span>
                                <i class="fas fa-angle-down ms-2 text-white-50"></i>
                            </a>
                            <!-- Dropdown - User Information -->
                            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="userDropdown">
                                <a class="dropdown-item" href="{{ route('dashboard', ['page' => 'user']) }}">
                                    <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Profil
                                </a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="{{ route('logout') }}">
                                    <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Logout
                                </a>
                            </div>
                        </li>
                    </ul>
                </nav>
                <!-- End of Topbar -->

                <!-- Begin Page Content -->
                <div class="container-fluid py-3">
                    @yield('content')
                </div>
                <!-- /.container-fluid -->

            </div>
            <!-- End of Main Content -->

            <!-- Footer -->
            <footer class="sticky-footer">
                <div class="container my-auto">
                    <div class="copyright text-center my-auto">
                        <span>
                            &copy; {{ date('Y') }} - Aplikasi Kasir {{ $toko->nama_toko ?? 'SmartKasir' }} | BY <b>{{ $toko->nama_pemilik ?? 'Owner' }}</b>
                        </span>
                    </div>
                </div>
            </footer>
            <!-- End of Footer -->

        </div>
        <!-- End of Content Wrapper -->

    </div>
    <!-- End of Page Wrapper -->

    <!-- Scroll to Top Button-->
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

    <!-- Custom Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('sb-admin/js/sb-admin-2.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            $('#sidebarToggle, #sidebarToggleTop').on('click', function(e) {
                setTimeout(function() {
                    if ($(".sidebar").hasClass("toggled")) {
                        localStorage.setItem('sidebar-toggled', 'true');
                    } else {
                        localStorage.setItem('sidebar-toggled', 'false');
                    }
                }, 100);
            });
            if (localStorage.getItem('sidebar-toggled') === 'true') {
                $("body").addClass("sidebar-toggled");
                $(".sidebar").addClass("toggled");
            }
        });
    </script>
</body>
</html>
