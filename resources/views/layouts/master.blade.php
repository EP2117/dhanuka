<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<?php
    //$fy = \App\FinancialYear::where(['id' => Session::get('loginYear')])->first();    
?>

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="user-wh" content="{{ Auth::user()->warehouse->warehouse_name }}">
    @if(Auth::user()->branch)
    <meta name="user-branch" content="{{ Auth::user()->branch->branch_name }}">
    @else
    <meta name="user-branch" content="">
    @endif
    <meta name="user-role" content="{{ Auth::user()->role->role_name }}">
    <meta name="site-path" content="{{ url('/') }}">
    <meta name="storage-path" content="{{ asset('storage') }}">
    <meta name="user-name-likelink" content="{{ Auth::user()->name }}">
    <meta name="user-id-likelink" content="{{ Auth::user()->id }}">
    <meta name="user-year-likelink" content="{{ Session::get('loginYear') }}">
    <!--<meta name="login_from_date" content="">
    <meta name="login_to_date" content="">-->
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Authors Consulting</title>

    <!-- Custom fonts for this template-->
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">

    <!--<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css" rel="stylesheet" />-->
    <!--<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.12/css/select2.min.css" rel="stylesheet" />-->
    <link href="{{ asset('custom/select2.min.css') }}" rel="stylesheet" />

    <!-- Custom styles for this template-->
    <link href="{{ asset('css/sb-admin-2.min.css') }}" rel="stylesheet">

    <!--<script src="https://use.fontawesome.com/releases/v5.7.2/js/all.js" data-auto-replace-svg="nest"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">-->
    <script src="{{ asset('custom/fontawesome_all.js') }}" data-auto-replace-svg="nest"></script>
    <link rel="stylesheet" href="{{ asset('custom/font-awesome.min.css') }}">

    <!-- bootstrap datetime picker styles -->
    <!--<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.8.0/css/bootstrap-datepicker.css" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css" rel="stylesheet" />-->
    <link href="{{ asset('custom/bootstrap-datepicker.css') }}" rel="stylesheet" />
    <link href="{{ asset('custom/bootstrap-datetimepicker.min.css') }}" rel="stylesheet" />


</head>

<body id="page-top">

  <!-- Page Wrapper -->
  <div id="app">
    <div id="wrapper">
        <!-- Sidebar -->
        @include('layouts.leftmenu')
        <!-- End of Sidebar -->

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">

            <!-- Main Content -->
            <div id="content">

                <!-- Topbar -->
                <!-- Kamlesh start -->
                <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow1" style="height:5rem !important;">

                    <!-- Sidebar Toggle (Topbar) -->
                    <!-- <span style="font-size:30px; margin-right:1rem;cursor:pointer; color: blue;">&#9776;</span> -->
                    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                        <i class="fa fa-bars"></i>
                    </button>

                    <h3 class="color-blue"><a href="/" style="text-decoration: none; color: #2E59D9 !important;font-family: 'Merriweather', serif;">Authors Consulting</a></h3>

                    <!-- Topbar Navbar -->
                    <!-- Kamlesh end -->
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
                        @if( Request::path() != '/')
                        <li class="nav-item">
                            <a class="nav-link" href="{{url('/')}}">
                                <span class="mr-2 d-none d-lg-inline text-gray-600 small" style="font-size: 20px;">Home <i class="fas fa-home text-primary" style="font-size: 25px;"></i></span>
                            </a>
                        </li>
                        @endif
                        <!-- Kamlesh start -->
                        <!-- Nav Item - Alerts -->
                        <!-- Nav Item - User Information -->
                        <li class="nav-item dropdown no-arrow">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span class="mr-2 d-none d-lg-inline text-gray-600 small" style="font-size: 20px">{{ Auth::user()->name }}</span>
                                <i class="far fa-user text-primary" style="font-size: 25px;"></i>
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
                <!-- Kamlesh end -->
                <!-- End of Topbar -->

                <!-- Begin Page Content -->
                <div class="container-fluid">

                    @if( Request::path() != '/')
                    <router-view></router-view>
                    @endif
                <!-- /.container-fluid -->
                </div>
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

    <!-- Scroll to Top Button-->
    <!--<a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>-->
    <script src="{{ asset('js/app.js') }}" type="text/javascript"></script>
    <script type="text/javascript" src="{{ asset('js/notify.min.js') }}"></script>
    <!-- Custom scripts for all pages-->
    <script src="{{ asset('js/sb-admin-2.min.js') }}"></script>
    <!--<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.8.0/js/bootstrap-datepicker.js"></script>-->
    <script src="{{ asset('custom/bootstrap-datepicker.js') }}"></script>
    <script>

        // kamlesh Start
        const mobileView = document.querySelector('#sidebarToggleTop');
        mobileView.addEventListener('click', function(){
            document.querySelector('#logo').style.display = "inline";
        })
        let sidebar = document.querySelector('.sidebar');
        sidebar.addEventListener('mouseleave',function(){
            sidebar.classList.add('toggled');
             document.querySelector('.side-log').innerText = '';
             document.querySelector('.sidelog-out').innerHTML = '';
            const collapse = document.querySelectorAll('.collap2');
            collapse.forEach((collapse)=>{
                if (collapse.classList.contains('show')) {
                collapse.classList.remove('show');
            }
            })
            document.querySelector('#logo').style.width = '6rem';
        });
        sidebar.addEventListener('mouseover',function(){
            sidebar.classList.remove('toggled');
            document.querySelector('.side-log').innerText = '{{ Auth::user()->name }}';
            document.querySelector('.sidelog-out').innerHTML = '<i class="fas fa-sign-out-alt" style=" font-size:1.2rem;"></i>';

        });


        // kamlesh end

        $(document).ready(function() {
            const timeout = 1800000;  // 900000 ms = 15 minutes 1800000 ms = 30 minutes
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
                console.log(evt.target);
                //if(evt.target.className != 'collapse-item' && !(evt.target.className.includes('inventory'))) {
                    $('.collapse').collapse('hide');
                //}
            });

            $(document).on('keypress','body .decimal_no',function(evt){
                var $this = $(this);
                if ((event.which != 46 || $this.val().indexOf('.') != -1) &&
                   ((event.which < 48 || event.which > 57) &&
                   (event.which != 0 && event.which != 8))) {
                       event.preventDefault();
                }

                var text = $(this).val();
                if ((event.which == 46) && (text.indexOf('.') == -1)) {
                    setTimeout(function() {
                        if ($this.val().substring($this.val().indexOf('.')).length > 4) {
                            $this.val($this.val().substring(0, $this.val().indexOf('.') + 4));
                        }
                    }, 1);
                }

                if ((text.indexOf('.') != -1) &&
                    (text.substring(text.indexOf('.')).length > 3) &&
                    (event.which != 0 && event.which != 8) &&
                    ($(this)[0].selectionStart >= text.length - 3)) {
                        event.preventDefault();
                }
            });

            //$('.decimal_no').bind("paste", function(e) {
            /**$(document).bind('paste','body .decimal_no',function(e){
            var text = e.originalEvent.clipboardData.getData('Text');
            if ($.isNumeric(text)) {
                if ((text.substring(text.indexOf('.')).length > 4) && (text.indexOf('.') > -1)) {
                    e.preventDefault();
                    $(this).val(text.substring(0, text.indexOf('.') + 4));
               }
            }
            else {
                    e.preventDefault();
                 }
            });**/
            $(document).bind('paste','.decimal_no',function(e){
            var text = e.originalEvent.clipboardData.getData('Text');
            if(e.target.className.includes("decimal_no")) {
                if ($.isNumeric(text)) {
                    if ((text.substring(text.indexOf('.')).length > 4) && (text.indexOf('.') > -1)) {
                        e.preventDefault();
                        $(this).val(text.substring(0, text.indexOf('.') + 4));
                   }
                }
                else {
                        e.preventDefault();
                     }
            }
            });

        });
    </script>
</body>

</html>
