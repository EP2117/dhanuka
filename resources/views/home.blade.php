<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="user-wh" content="{{ Auth::user()->warehouse->warehouse_name }}">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Dhanuka - Dashboard</title>
    <link rel="icon" href="{{ asset('storage/images/logo.jpg') }}">

    <!-- Custom fonts for this template-->
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Merriweather&display=swap" rel="stylesheet">
    <link href="{{ asset('custom/select2.min.css') }}" rel="stylesheet" />

    <!-- Custom styles for this template-->
    <link href="{{ asset('css/sb-admin-2.min.css') }}" rel="stylesheet">

    <script src="{{ asset('custom/fontawesome_all.js') }}" data-auto-replace-svg="nest"></script>
    <link rel="stylesheet" href="{{ asset('custom/font-awesome.min.css') }}">

    <!-- bootstrap datetime picker styles -->
    <link href="{{ asset('custom/bootstrap-datepicker.css') }}" rel="stylesheet" />
    <link href="{{ asset('custom/bootstrap-datetimepicker.min.css') }}" rel="stylesheet" />

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Nunito', sans-serif;
            background: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 100%);
            min-height: 100vh;
            color: #e0e0e0;
            margin: 0;
            padding: 0;
        }

        #wrapper {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        #content-wrapper {
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        #content {
            flex: 1;
        }

        /* Topbar Styles */
        .topbar {
            background: linear-gradient(135deg, #2d2d2d 0%, #3a3a3a 100%) !important;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.3) !important;
            padding: 1rem 2rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 2px solid #4a4a4a;
        }

        .topbar .p-2 {
            display: flex;
            align-items: center;
        }

        .topbar .p-2 img {
            border-radius: 8px;
        }

        .topbar h3 {
            color: #ffffff !important;
            font-weight: 700;
            margin: 0;
            font-size: 1.5rem;
        }

        .topbar .color-blue {
            color: #ffffff !important;
        }

        .navbar-nav {
            display: flex;
            align-items: center;
            list-style: none;
            gap: 1rem;
            margin: 0;
        }

        .nav-link {
            color: #e0e0e0 !important;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .nav-link:hover {
            background: rgba(255, 255, 255, 0.1);
            color: #ffffff !important;
        }

        .nav-link i {
            font-size: 1.5rem;
        }

        .nav-link .text-gray-600 {
            color: #e0e0e0 !important;
        }

        .nav-link .text-primary {
            color: #6c63ff !important;
        }

        .dropdown-menu {
            background: #3a3a3a !important;
            border: 1px solid #4a4a4a !important;
            border-radius: 8px;
            padding: 0.5rem;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.4) !important;
        }

        .dropdown-item {
            color: #e0e0e0 !important;
            padding: 0.5rem 1rem;
            text-decoration: none;
            display: block;
            border-radius: 4px;
            transition: all 0.3s ease;
        }

        .dropdown-item:hover {
            background: rgba(255, 255, 255, 0.1) !important;
            color: #ffffff !important;
        }

        /* Container */
        /* .container-fluid {
            padding: 2rem;
            max-width: 1400px;
            margin: 0 auto;
            min-height: calc(100vh - 250px);
        } */

        .home-main {
            display: flex;
            flex-wrap: wrap;
            margin-top: 2rem;
            margin-left: -15px;
            margin-right: -15px;
            margin-bottom: 3rem;
        }

        /* Card Styles */
        .carhover {
            background: linear-gradient(135deg, #3a3a3a 0%, #2d2d2d 100%);
            border-radius: 16px;
            padding: 2rem;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.3);
            transition: all 0.4s ease;
            border: 1px solid #4a4a4a;
            position: relative;
            overflow: hidden;
            height: 100%;
            display: flex;
            flex-direction: column;
        }

        .carhover::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.05) 0%, transparent 100%);
            opacity: 0;
            transition: opacity 0.4s ease;
        }

        .carhover:hover {
            transform: translateY(-8px);
            box-shadow: 0 12px 24px rgba(0, 0, 0, 0.5);
            border-color: #6a6a6a;
        }

        .carhover:hover::before {
            opacity: 1;
        }

        .carhover a {
            text-decoration: none;
            color: inherit;
        }

        .main-col {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            position: relative;
            z-index: 1;
            min-height: 180px;
        }

        .col1-1 {
            flex: 1;
        }

        .col1-1 h3 {
            color: #ffffff;
            font-size: 1.8rem;
            margin-bottom: 1rem;
            margin-top: 0;
            font-weight: 700;
        }

        .col1-1 ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .col1-1 ul li {
            color: #b0b0b0;
            padding: 0.5rem 0;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            font-size: 0.95rem;
            transition: all 0.3s ease;
        }

        .col1-1 ul li:hover {
            color: #ffffff;
            padding-left: 0.5rem;
        }

        .col1-1 ul li i {
            color: #ffffff;
            font-size: 1rem;
            min-width: 20px;
        }

        .col1-2 {
            font-size: 4rem;
            opacity: 0.15;
            transition: all 0.4s ease;
            flex-shrink: 0;
            margin-left: 1rem;
            align-self: center;
        }

        .carhover:hover .col1-2 {
            opacity: 0.25;
            transform: scale(1.1) rotate(5deg);
        }

        /* Specific card icon colors */
        .card-1 .col1-2 {
            color: #6c63ff;
        }

        .card-3 .col1-2, .office-sale {
            color: #4ecdc4;
        }

        .card-4 .col1-2, .inventory {
            color: #ff6b6b;
        }

        .card-5 .col1-2, .report {
            color: #ffd93d;
        }

        /* Footer */
        .sticky-footer {
            background: linear-gradient(135deg, #2d2d2d 0%, #3a3a3a 100%) !important;
            padding: 1.5rem 0;
            border-top: 2px solid #4a4a4a;
            margin-top: auto;
        }

        .sticky-footer .copyright {
            color: #b0b0b0;
            text-align: center;
            font-size: 0.9rem;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .topbar {
                flex-direction: column;
                gap: 1rem;
                padding: 1rem;
            }

            .topbar h3 {
                font-size: 1.2rem;
            }

            .home-main {
                grid-template-columns: 1fr;
                gap: 1.5rem;
            }

            .container-fluid {
                padding: 1rem;
            }

            .col1-2 {
                font-size: 3rem;
            }
        }

        /* Override row styles */
        .row.home-main {
            display: flex !important;
            flex-wrap: wrap;
        }

        .col-lg-4, .col-md-4 {
            padding: 15px;
            flex: 0 0 33.333333%;
            max-width: 33.333333%;
            margin-bottom: 2rem;
        }

        @media (max-width: 991px) {
            .col-lg-4, .col-md-4 {
                flex: 0 0 50%;
                max-width: 50%;
            }
        }

        @media (max-width: 767px) {
            .col-lg-4, .col-md-4 {
                flex: 0 0 100%;
                max-width: 100%;
            }
        }
    </style>

</head>

<body id="page-top">

    <!-- Page Wrapper -->
    <div id="app">
        <div id="wrapper">
            <!-- Content Wrapper -->
            <div id="content-wrapper" class="d-flex flex-column">

                <!-- Main Content -->
                <div id="content">

                    <!-- Topbar -->
                    <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow1">

                        <div class="p-2">
                            <img width="100" src="{{ asset('storage/images/logo.jpg') }}">
                        </div>

                        <h3 class="color-blue"><b>Dhanuka - Dashboard</b></h3>

                        <!-- Topbar Navbar -->
                        <ul class="navbar-nav ml-auto">

                            <!-- Nav Item - Search Dropdown (Visible Only XS) -->
                            <li class="nav-item dropdown no-arrow d-sm-none">
                                <a class="nav-link dropdown-toggle" href="#" id="searchDropdown" role="button"
                                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="fas fa-search fa-fw"></i>
                                </a>
                                <!-- Dropdown - Messages -->
                                <div class="dropdown-menu dropdown-menu-right p-3 shadow animated--grow-in"
                                    aria-labelledby="searchDropdown">
                                    <form class="form-inline mr-auto w-100 navbar-search">
                                        <div class="input-group">
                                            <input type="text" class="form-control bg-light border-0 small"
                                                placeholder="Search for..." aria-label="Search"
                                                aria-describedby="basic-addon2">
                                            <div class="input-group-append">
                                                <button class="btn btn-primary" type="button">
                                                    <i class="fas fa-search fa-sm"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </li>

                            <!-- Nav Item - User Information -->
                            <li class="nav-item dropdown no-arrow">
                                <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <span class="mr-2 d-none d-lg-inline text-gray-600 small"
                                        style="font-size: 20px">{{ Auth::user()->name }}</span>
                                    <i class="far fa-user text-primary" style="font-size: 25px;"></i>
                                </a>
                                <!-- Dropdown - User Information -->
                                <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in"
                                    aria-labelledby="userDropdown">

                                    <a class="dropdown-item" href="{{ route('logout') }}"
                                        onclick="event.preventDefault();
                                    document.getElementById('logout-form').submit();">
                                        {{ __('Logout') }}
                                    </a>

                                    <form id="logout-form" action="{{ route('logout') }}" method="POST"
                                        style="display: none;">
                                        @csrf
                                    </form>
                                </div>
                            </li>

                        </ul>

                    </nav>
                    <!-- End of Topbar -->

                    <!-- Begin Page Content -->
                    <div class="container-fluid">
                        <div class="row">
                            @if (Auth::user()->role->role_name == 'admin' ||
                                    Auth::user()->role->role_name == 'system' ||
                                    Auth::user()->role->role_name == 'office_user' ||
                                    Auth::user()->role->id == 9)
                                <div class="col-lg-4 col-md-4">
                                    <div class="card-1 carhover">
                                        <a href="{{ url('/master') }}">
                                            <div class="main-col">
                                                <div class="col1-1">
                                                    <h3>Master</h3>
                                                    <ul>
                                                        <li><i class="fas fa-pen"></i>Products</li>
                                                        <li><i class="fas fa-users"></i>Customer</li>
                                                        @if (Auth::user()->role->role_name == 'system')
                                                            <li><i class="fas fa-file-import"></i>Import</li>
                                                        @endif

                                                    </ul>
                                                </div>
                                                <div class="col1-2">
                                                    <i class="fab fa-medium-m"></i>
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                </div>
                            @endif

                            @if (Auth::user()->role->role_name == 'system' ||
                                    Auth::user()->role->role_name == 'admin' ||
                                    Auth::user()->role->role_name == 'office_user' ||
                                    Auth::user()->role->role_name == 'office_order_user' ||
                                    Auth::user()->role->id == 6 ||
                                    Auth::user()->role->id == 7 ||
                                    Auth::user()->role->id == 8 ||
                                    Auth::user()->role->id == 9)
                                <div class="col-lg-4 col-md-4">
                                    <div class="card-3 carhover">
                                        <a href="{{ url('/office') }}">
                                            <div class="main-col">
                                                <div class="col1-1">
                                                    <h3>Sale</h3>
                                                    <ul>
                                                        <li><i class="fas fa-file-invoice-dollar"></i>Sale Invoice</li>
                                                        <li><i class="fas fa-file-invoice-dollar"></i>Credit Collection
                                                        </li>
                                                    </ul>
                                                </div>
                                                <div class="col1-2 office-sale">
                                                    <i class="fas fa-building"></i>
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                </div>
                            @endif

                            @if (Auth::user()->role->role_name != 'office_order_user' &&
                                    Auth::user()->role->id != 6 &&
                                    Auth::user()->role->id != 7 &&
                                    Auth::user()->role->id != 8 &&
                                    Auth::user()->role->id != 9)
                                <div class="col-lg-4 col-md-4">
                                    <div class="card-4 carhover">
                                        <a href="{{ url('/inventory') }}">
                                            <div class="main-col">
                                                <div class="col1-1">
                                                    <h3>Inventory</h3>
                                                    <ul>
                                                        <li><i class="fas fa-warehouse"></i>Main WareHouse</li>
                                                        <li><i class="fas fa-random"></i>Internal Transfer</li>
                                                        <li><i class="fas fa-receipt"></i>Internal Receive</li>

                                                    </ul>
                                                </div>
                                                <div class="col1-2 inventory">
                                                    <i class="fas fa-boxes"></i>
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                </div>
                            @endif

                            @if (Auth::user()->role->role_name == 'admin' ||
                                    Auth::user()->role->role_name == 'system' ||
                                    Auth::user()->role->id == 9)
                                <div class="col-lg-4 col-md-4">
                                    <div class="card-5 carhover">
                                        <a href="{{ url('/report') }}">
                                            <div class="main-col">
                                                <div class="col1-1">
                                                    <h3>Report</h3>
                                                    <ul>
                                                        <li><i class="fas fa-scroll"></i>Daily Sale Report</li>
                                                        <li><i class="fas fa-scroll"></i>Product Wise Report</li>
                                                        <li><i class="fas fa-scroll"></i>Inventory Report</li>
                                                    </ul>
                                                </div>
                                                <div class="col1-2 report">
                                                    <i class="fas fa-paste"></i>
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                </div>
                            @elseif(Auth::user()->role->role_name == 'office_user')
                                <div class="col-lg-4 col-md-4">
                                    <div class="card-5 carhover">
                                        <a href="{{ url('/report') }}">
                                            <div class="main-col">
                                                <div class="col1-1">
                                                    <h3>Report</h3>
                                                    <ul>
                                                        <li><i class="fas fa-scroll"></i>Minimun Qty Report</li>
                                                    </ul>
                                                </div>
                                                <div class="col1-2 report">
                                                    <i class="fas fa-paste"></i>
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                </div>
                            @endif

                            @if (Auth::user()->role->role_name == 'admin' ||
                                    Auth::user()->role->role_name == 'system' ||
                                    Auth::user()->role->id == 9)
                                <div class="col-lg-4 col-md-4">
                                    <div class="card-5 carhover">
                                        <a href="{{ url('/reminder') }}">
                                            <div class="main-col">
                                                <div class="col1-1">
                                                    <h3>Reminder</h3>
                                                    <ul>
                                                        @if (Auth::user()->role->id != 18)
                                                            <li><i class="fas fa-scroll"></i>Sale Over Due</li>
                                                            <li><i class="fas fa-scroll"></i>Purchase Over Due</li>
                                                        @endif
                                                        <li><i class="fas fa-scroll"></i>Sale Order Pending</li>
                                                    </ul>
                                                </div>
                                                <div class="col1-2 report">
                                                    <i class="fas fa-paste"></i>
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                </div>
                            @endif

                            @if (Auth::user()->role->role_name == 'system' ||
                                    Auth::user()->role->role_name == 'admin' ||
                                    Auth::user()->role->role_name == 'office_user')
                                <div class="col-lg-4 col-md-4">
                                    <div class="card-5 carhover">
                                        <a href="{{ url('/purchase_office') }}">
                                            <div class="main-col">
                                                <div class="col1-1">
                                                    <h3>Purchase</h3>
                                                    <ul>
                                                        <li><i class="fas fa-file-invoice-dollar"></i>Purchase Invoice
                                                        </li>
                                                        <li><i class="fas fa-file-invoice-dollar"></i>Credit Payment
                                                        </li>
                                                    </ul>
                                                </div>
                                                <div class="col1-2 report">
                                                    <i class="fas fa-paste"></i>
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                </div>
                            @endif
                            @if (Auth::user()->role->role_name == 'admin' ||
                                    Auth::user()->role->role_name == 'system' ||
                                    Auth::user()->role->id == 9)
                                <div class="col-lg-4 col-md-4">
                                    <div class="card-5 carhover">
                                        <a href="{{ url('/account') }}">
                                            <div class="main-col">
                                                <div class="col1-1">
                                                    <h3>Account</h3>
                                                    <ul>
                                                        <li><i class="fas fa-file-invoice-dollar"></i>Account Head</li>
                                                        <li><i class="fas fa-file-invoice-dollar"></i>Sub Account</li>
                                                    </ul>
                                                </div>
                                                <div class="col1-2 report">
                                                    <i class="fas fa-paste"></i>
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                </div>
                            @endif

                        </div>

                    </div>
                    <!-- /.container-fluid -->
                </div>
                <!-- End of Main Content -->

                <!-- Footer -->
                <footer class="sticky-footer bg-white">
                    <div class="container my-auto">
                        <div class="copyright text-center my-auto">
                            <span>Copyright &copy; Authors Consulting 2020</span>
                        </div>
                    </div>
                </footer>
                <!-- End of Footer -->

            </div>
            <!-- End of Content Wrapper -->

        </div>
        <!-- End of Page Wrapper -->
    </div>

    <script src="{{ asset('js/app.js') }}" type="text/javascript"></script>
    <script type="text/javascript" src="/js/notify.min.js"></script>
    <!-- Custom scripts for all pages-->
    <script src="{{ asset('js/sb-admin-2.min.js') }}"></script>
    <script src="{{ asset('custom/bootstrap-datepicker.js') }}"></script>
    <script>
        $(document).ready(function() {
            const timeout = 5400000; // 900000 ms = 15 minutes 1800000 ms = 30 minutes
            var idleTimer = null;
            $('*').bind(
                'mousemove click mouseup mousedown keydown keypress keyup submit change mouseenter scroll resize dblclick',
                function() {
                    clearTimeout(idleTimer);

                    idleTimer = setTimeout(function() {
                        document.getElementById('logout-form').submit();
                    }, timeout);
                });
            $("body").trigger("mousemove");

            $(document).on('keypress', 'body .num_txt', function(evt) {

                evt = (evt) ? evt : window.event;
                var charCode = (evt.which) ? evt.which : evt.keyCode;

                if ((charCode > 31 && (charCode < 48 || charCode > 57)) && charCode !== 46) {
                    evt.preventDefault();;
                } else {
                    return true;
                }

            });

            $(".sidebar").on('click', function(evt) {
                $('.collapse').collapse('hide');
            })
        });
    </script>
</body>

</html>