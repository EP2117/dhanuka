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

    <title>Author Consulting</title>

    <!-- Custom fonts for this template-->    
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">

    <!--<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css" rel="stylesheet" />-->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.12/css/select2.min.css" rel="stylesheet" />

    <!-- Custom styles for this template-->
    <link href="{{ asset('css/sb-admin-2.min.css') }}" rel="stylesheet">    

    <script src="https://use.fontawesome.com/releases/v5.7.2/js/all.js" data-auto-replace-svg="nest"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

    <!-- bootstrap datetime picker styles -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.8.0/css/bootstrap-datepicker.css" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css" rel="stylesheet" />
    
   

</head>

<body id="page-top">

  <!-- Page Wrapper -->
  <div id="app">
    <div id="wrapper">        
        <!-- Sidebar -->
        <!-- Sidebar -->
        <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

            <!-- Sidebar - Brand -->
            <!--<a class="sidebar-brand d-flex align-items-center justify-content-center" href="index.html">
                <div class="sidebar-brand-icon rotate-n-15">
                    <i class="fas fa-grin-beam"></i>
                </div>
            <!--<div class="sidebar-brand-text mx-3">Like Link</div>-->
            <!--</a>-->
                <div class="sidebar-brand-icon text-center">
                     <img id="logo"src="{{ asset('storage/image/logo.jpg') }}">
                </div>
          <!-- Divider -->
          <hr class="sidebar-divider my-0">
        </ul>
        <!-- End of Sidebar -->

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">

            <!-- Main Content -->
            <div id="content">

                <!-- Topbar -->
                <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">

                    <!-- Sidebar Toggle (Topbar) -->
                    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                        <i class="fa fa-bars"></i>
                    </button>

                    <h3 class="color-blue">Author Consulting</h3>

                    <!-- Topbar Navbar -->
                    <ul class="navbar-nav ml-auto">

                        <!-- Nav Item - Search Dropdown (Visible Only XS) -->
                        <li class="nav-item dropdown no-arrow d-sm-none">
                            <a class="nav-link dropdown-toggle" href="#" id="searchDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fas fa-search fa-fw"></i>
                            </a>
                            <!-- Dropdown - Messages -->
                            <div class="dropdown-menu dropdown-menu-right p-3 shadow animated--grow-in" aria-labelledby="searchDropdown">
                                <form class="form-inline mr-auto w-100 navbar-search">
                                    <div class="input-group">
                                        <input type="text" class="form-control bg-light border-0 small" placeholder="Search for..." aria-label="Search" aria-describedby="basic-addon2">
                                        <div class="input-group-append">
                                            <button class="btn btn-primary" type="button">
                                                <i class="fas fa-search fa-sm"></i>
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </li>

                         @if(Auth::user()->role->role_name == 'admin' || Auth::user()->role->role_name == 'system' )
                        <li class="nav-item dropdown no-arrow">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">

                                <i class="fas fa-external-link-alt text-primary" style="font-size: 25px;"></i>

                            </a>
                            <!-- Dropdown - User Information -->
                            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="userDropdown">
                            
                                <!--<a class="dropdown-item" href="#" data-toggle="modal" data-target="#logoutModal">
                                    <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Logout
                                </a>-->
                                <a class="dropdown-item" href="{{ url('/dashboard') }}">                                
                                    <span class="" style="font-size: 14px;"><i class="fas fa-tachometer-alt text-primary" style="font-size: 19px;"></i> Dashboard</span>
                                </a>
                            </div>
                        </li>
                        @endif

                        <!-- Nav Item - Alerts -->
                        <!-- Nav Item - User Information -->
                        <li class="nav-item dropdown no-arrow">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span class="mr-2 d-none d-lg-inline text-gray-600 small">{{ Auth::user()->name }}</span>
                                <i class="far fa-user text-primary" style="font-size: 20px;"></i>
                            </a>
                            <!-- Dropdown - User Information -->
                            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="userDropdown">
                            
                                <!--<a class="dropdown-item" href="#" data-toggle="modal" data-target="#logoutModal">
                                    <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Logout
                                </a>-->
                                <a class="dropdown-item" href="{{ route('logout') }}"
                                    onclick="event.preventDefault();
                                    document.getElementById('logout-form').submit();">
                                    {{ __('Logout') }}
                                </a>

                                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
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
                            @if(Auth::user()->role->role_name == 'admin' || Auth::user()->role->role_name == 'system' || Auth::user()->role->role_name == 'office_user')
                            <div class="col-md-4 col-sm-6 offset-sm-3 offset-md-4 block mt-4">
                                <div class="circle">
                                    <p><a href="{{url('/master')}}"><i class="fab fa-medium module_logo"></i> <br />Master</a></p>
                                </div>
                            </div> 
                            @endif

                            @if((Auth::user()->role->role_name == 'system' || Auth::user()->role->role_name == 'admin' || Auth::user()->role->role_name == 'van_user') && Auth::user()->role->id != 6 && Auth::user()->role->id != 7)
                            <div class="col-md-6 col-sm-6 block">
                                <div class="circle">
                                    <p><a href="{{url('/van')}}"><i class="fas fa-shipping-fast module_logo"></i> <br />Van Sale</a></p>
                                </div>
                            </div>
                            @endif

                             @if(Auth::user()->role->role_name == 'system' || Auth::user()->role->role_name == 'admin' || Auth::user()->role->role_name == 'office_user' || Auth::user()->role->role_name == 'office_order_user' || Auth::user()->role->id == 6 || Auth::user()->role->id == 7 || Auth::user()->role->id == 8)
                            <div class="col-md-6 col-sm-6 block">
                                <div class="circle">
                                    <p><a href="{{url('/office')}}"><i class="fas fa-building module_logo"></i> <br />Office Sale</a></p>
                                </div>
                            </div>
                            @endif

                            @if(Auth::user()->role->role_name != 'office_order_user' &&  Auth::user()->role->id != 6 && Auth::user()->role->id != 7 && Auth::user()->role->id != 8)
                            <div class="col-md-6 col-sm-6 block">
                                <div class="circle">
                                    <p><a href="{{url('/inventory')}}"><i class="fas fa-boxes module_logo"></i> <br />Inventory</a></p>
                                </div>
                            </div>
                            @endif

                            @if((Auth::user()->role->role_name != 'van_user' && Auth::user()->role->role_name != 'delivery' && Auth::user()->role->role_name != 'office_order_user') || Auth::user()->role->id == 6 || Auth::user()->role->id == 7)
                            <div class="col-md-6 col-sm-6 block">
                                <div class="circle">
                                    <p><a href="{{url('/report')}}"><i class="far fa-chart-bar module_logo"></i> <br />Report</a>

                                    </p>
                                </div>
                            </div>
                            @endif

                      </div>
                <!-- /.container-fluid -->
                </div>
            </div>
            <!-- End of Main Content -->

            <!-- Footer -->
            <footer class="sticky-footer bg-white">
                <div class="container my-auto">
                    <div class="copyright text-center my-auto">
                        <span>Copyright &copy; Like-Link 2020</span>
                    </div>
                </div>
            </footer>
          <!-- End of Footer -->

        </div>
        <!-- End of Content Wrapper -->

    </div>
    <!-- End of Page Wrapper -->
</div>

    <!-- Scroll to Top Button-->
    <!--<a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>-->

    <script src="{{ asset('js/app.js') }}" type="text/javascript"></script>
    <script type="text/javascript" src="/js/notify.min.js"></script>
    <!-- Custom scripts for all pages-->    
    <script src="{{ asset('js/sb-admin-2.min.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.8.0/js/bootstrap-datepicker.js"></script>
    <script>
        $(document).ready(function() {
            const timeout = 5400000;  // 900000 ms = 15 minutes 1800000 ms = 30 minutes
            var idleTimer = null;
            $('*').bind('mousemove click mouseup mousedown keydown keypress keyup submit change mouseenter scroll resize dblclick', function () {
                clearTimeout(idleTimer);

                idleTimer = setTimeout(function () {
                    document.getElementById('logout-form').submit();
                }, timeout);
            });
            $("body").trigger("mousemove");
            
           $(document).on('keypress','body .num_txt',function(evt){

                evt = (evt) ? evt : window.event;
                var charCode = (evt.which) ? evt.which : evt.keyCode;

                if ((charCode > 31 && (charCode < 48 || charCode > 57)) && charCode !== 46) {
                evt.preventDefault();;
                } else {
                return true;
                }

                /*if ((charCode > 31 && (charCode < 48 || charCode > 57)) && charCode !== 46) {
                evt.preventDefault();;
                }*/
            });

            $(".sidebar").on('click',function(evt){
                //console.log(evt.target);
                //if(evt.target.className != 'collapse-item' && !(evt.target.className.includes('inventory'))) {
                    $('.collapse').collapse('hide');
                //}
            })
        });
    </script>
</body>

</html>
