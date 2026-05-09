<div class="sidebar-wrapper" data-layout="stroke-svg">
    <div class="logo-wrapper">
        <a href="/dashboard" class="d-flex align-items-center gap-2 text-decoration-none">
            <img class="img-fluid" style="width: 55px; margin-top: -10px"
                src="{{ asset('dashboard_assets/assets/images/logo/logo.png') }}" alt="">
        </a>
        <div class="back-btn"><i class="fa fa-angle-left"> </i></div>
        <div class="toggle-sidebar">
            <i class="status_toggle middle sidebar-toggle" data-feather="grid"> </i>
        </div>
    </div>

    <div class="logo-icon-wrapper">
        <a href="/dashboard">
            <img class="img-fluid" width="10px" src="{{ asset('dashboard_assets/assets/images/logo/logo-icon.png') }}"
                alt="">
        </a>
    </div>
    <nav class="sidebar-main">
        <div class="left-arrow" id="left-arrow"><i data-feather="arrow-left"></i></div>
        <div id="sidebar-menu">
            <ul class="sidebar-links" id="simple-bar">
                <li class="back-btn"><a href="/dashboard"><img class="img-fluid"
                            src="{{ asset('dashboard_assets/assets/images/logo/logo-icon.png') }}" alt=""></a>
                    <div class="mobile-back text-end"> <span>Back </span><i class="fa fa-angle-right ps-2"
                            aria-hidden="true"></i></div>
                </li>
                <li class="pin-title sidebar-main-title">
                    <div>
                        <h6>Pinned</h6>
                    </div>
                </li>
                <li class="sidebar-main-title">
                    <div>
                        <h6 class="lan-1">General</h6>
                    </div>
                </li>

                <li class="sidebar-list">
                    <a class="sidebar-link sidebar-title link-nav" href="/dashboard">
                        <i class="fa fa-home text-white" aria-hidden="true"></i>
                        <span>Dashboard</span>
                    </a>
                </li>

                <li class="sidebar-main-title">
                    <div>
                        <h6 class="">Data Master</h6>
                    </div>
                </li>

                <li class="sidebar-list">
                    <a class="sidebar-link sidebar-title link-nav" href="/users">
                        <i class="fa fa-users text-white" aria-hidden="true"></i>
                        <span>Users</span>
                    </a>
                </li>

                <li class="sidebar-list">
                    <a class="sidebar-link sidebar-title link-nav" href="/meja">
                        <i class="fa fa-square text-white" aria-hidden="true"></i>
                        <span>Manajemen Meja</span>
                    </a>
                </li>

                <li class="sidebar-list" style="cursor: pointer">
                    <a class="sidebar-link sidebar-title">
                        <i class="fa fa-cubes text-white"></i>
                        <span class="">Menu</span>
                        <div class="according-menu"><i class="fa fa-angle-right"></i></div>
                    </a>
                    <ul class="sidebar-submenu" style="display: none;">
                        <li><a href="/kategori-menu"><i class="fa fa-list me-2"></i> Kategori Menu</a></li>
                        <li><a href="/daftar-menu"><i class="fa fa-cutlery me-2" aria-hidden="true"></i> Daftar Menu</a>
                        </li>
                    </ul>
                </li>

                <li class="sidebar-main-title">
                    <div>
                        <h6 class="">Data Penjualan</h6>
                    </div>
                </li>

                <li class="sidebar-list">
                    <a class="sidebar-link sidebar-title link-nav" href="/statistik-penjualan">
                        <i class="fa fa-line-chart text-white" aria-hidden="true"></i>
                        <span>Statistik Penjualan</span>
                    </a>
                </li>

                <li class="sidebar-list" style="cursor: pointer">
                    <a class="sidebar-link sidebar-title">
                        <i class="fa fa-shopping-cart text-white"></i>
                        <span class="">Order</span>
                        <div class="according-menu"><i class="fa fa-angle-right"></i></div>
                    </a>
                    <ul class="sidebar-submenu" style="display: none;">
                        <li><a href="/daftar-order"><i class="fa fa-shopping-cart text-white me-2"></i> Daftar Order</a>
                        </li>
                        <li><a href="/riwayat-order"><i class="fa fa-history text-white me-2"></i> Riwayat Order</a>
                        </li>
                    </ul>
                </li>

            </ul>
            <div class="right-arrow" id="right-arrow"><i data-feather="arrow-right"></i></div>
        </div>
    </nav>
</div>
