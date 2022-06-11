<!-- Sidebar -->
<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

    <!-- Sidebar - Brand -->
    <!--<a class="sidebar-brand d-flex align-items-center justify-content-center" href="index.html">
        <div class="sidebar-brand-icon rotate-n-15">
            <i class="fas fa-grin-beam"></i>
        </div>
    <!--<div class="sidebar-brand-text mx-3">Like Link</div>-->
    <!--</a>-->
        <!-- kamlesh start -->
        <div class="sidebar-brand-icon text-center" style="height: 96px; overflow: hidden;">
            <div style="width: 6rem; ">
                <a href="/">
                    <img id="logo" src="{{ asset('storage/image/logo.jpg') }}" style="width:6rem;padding:5px;" />
                </a>
            </div>
            <div class="logout"style="position:absolute;left:110px;top: 30px;">
               <span style="font-size:1.5rem;color: white;" class="side-log"></span>
               <a href="{{ route('logout') }}" class="sidelog-out" onclick="event.preventDefault();
                                    document.getElementById('logout-form').submit();">
                </a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                @csrf
            </form>
           </div>
        </div>
    <!-- Kamlesh end -->
  <!-- Divider -->
  <!--<hr class="sidebar-divider my-0">

    <router-link  tag="li" to="/" exact class="nav-item">
        <a class="nav-link">
            <i class="fas fa-tachometer-alt"></i>
            <span>Home</span>
        </a>
    </router-link>-->

  <!-- Divider -->

  <hr class="sidebar-divider">

    @if( Request::path() != '/')

        @if(Request::path() == 'master')

        <!-- Kaamlesh start -->
        <router-link  tag="li" to="/master" class="nav-item">
            <li class="pcoded-hasmenu nav-item">
                <a class="nav-link  collapsed" href="#" data-toggle="collapse" data-target="#collapseMenu" aria-expanded="true" aria-controls="collapseTwo">
                    <i class="fab fa-medium module_logo_sm text-lable icon-box"></i>
                    <span class="text-lable pcoded-mtext">Master</span>
                 </a>

            <div id="collapseMenu" class="collapse collap2" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
                <div class="bg-white py-2 collapse-inner rounded">
                    <router-link  tag="span" to="/import/uom">
                        <a class="collapse-item">Van Sale</a>
                    </router-link>
                    <router-link  tag="span" to="/import/brand" >
                        <a class="collapse-item">Office Sale</a>
                    </router-link>
                    <router-link  tag="span" to="/import/category" >
                        <a class="collapse-item">Inventory</a>
                    </router-link>
                    <router-link  tag="span" to="/import/product" >
                        <a class="collapse-item">Report</a>
                    </router-link>
                </div>
            </div>
        </router-link>

        <!-- Kamlesh end -->

        <hr class="sidebar-divider">

        <router-link  tag="li" to="/brand" class="nav-item">
            <a class="nav-link" >
                <i class="fas fa-database"></i>
                <span>Brand</span>
            </a>
        </router-link>

        <hr class="sidebar-divider">

        <router-link  tag="li" to="/category" class="nav-item">
            <a class="nav-link" >
                <i class="fas fa-database"></i>
                <span>Category</span>
            </a>
        </router-link>

        <hr class="sidebar-divider">

        <router-link  tag="li" to="/uom" class="nav-item">
            <a class="nav-link" >
                <i class="fas fa-database"></i>
                <span>UOM</span>
            </a>
        </router-link>

        <hr class="sidebar-divider">

        <router-link  tag="li" to="/product" class="nav-item">
            <a class="nav-link" >
                <i class="fas fa-database"></i>
                <span>Product</span>
            </a>
        </router-link>

        <hr class="sidebar-divider">

        <router-link  tag="li" to="/customer" class="nav-item">
            <a class="nav-link" >
                <i class="fas fa-users"></i>
                <span>Customer</span>
            </a>
        </router-link>

            <hr class="sidebar-divider">

            <router-link  tag="li" to="/supplier" class="nav-item">
                <a class="nav-link" >
                    <i class="fas fa-users"></i>
                    <span>Supplier</span>
                </a>
            </router-link>
            @if(Auth::user()->role->id == 1)

            <hr class="sidebar-divider">
            <router-link  tag="li" to="/supplier_ob" class="nav-item">
                <a class="nav-link" >
                    <i class="fas fa-users"></i>
                    <span>Supplier Opening Balance</span>
                </a>
            </router-link>
            <hr class="sidebar-divider">
            <router-link  tag="li" to="/customer_ob" class="nav-item">
                <a class="nav-link" >
                    <i class="fas fa-users"></i>
                    <span>Customer Opening Balance</span>
                </a>
            </router-link>
            @endif
            

        <!--<hr class="sidebar-divider">-->

        @if(Auth::user()->role->role_name == 'system')

        <!-- Divider -->
        @if(Auth::user()->email != "demo@mail.com")
        <hr class="sidebar-divider">

        <router-link  tag="li" to="/import" class="nav-item">
            <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseImport" aria-expanded="true" aria-controls="collapseImport">
                <i class="fas fa-file-import"></i>
                <span>Import</span>
            </a>
            <div id="collapseImport" class="collapse" aria-labelledby="headingImport" data-parent="#accordionSidebar">
                <div class="bg-white py-2 collapse-inner rounded">
                    <router-link  tag="span" to="/import/uom">
                        <a class="collapse-item">UOM</a>
                    </router-link>
                    <router-link  tag="span" to="/import/brand" >
                        <a class="collapse-item">Brand</a>
                    </router-link>
                    <router-link  tag="span" to="/import/category" >
                        <a class="collapse-item">Category</a>
                    </router-link>
                    <router-link  tag="span" to="/import/product" >
                        <a class="collapse-item">Product</a>
                    </router-link>
                    <router-link  tag="span" to="/import/product_qty" >
                        <a class="collapse-item">Product Min & % QTY</a>
                    </router-link>
                    <router-link  tag="span" to="/import/warehouse" >
                        <a class="collapse-item">Warehouse</a>
                    </router-link>
                    <router-link  tag="span" to="/import/state" >
                        <a class="collapse-item">State</a>
                    </router-link>
                    <router-link  tag="span" to="/import/township" >
                        <a class="collapse-item">Township</a>
                    </router-link>
                    <router-link  tag="span" to="/import/customer_type" >
                        <a class="collapse-item">Customer Type</a>
                    </router-link>
                    <router-link  tag="span" to="/import/customer" >
                        <a class="collapse-item">Customer</a>
                    </router-link>
                </div>
            </div>
        </router-link>
        @endif
        <hr class="sidebar-divider">

        <router-link  tag="li" to="/branch" class="nav-item">
            <a class="nav-link" >
                <i class="far fa-building"></i>
                <span>Branch</span>
            </a>
        </router-link>

        <hr class="sidebar-divider">

        <router-link  tag="li" to="/warehouse" class="nav-item">
            <a class="nav-link" >
                <i class="fas fa-warehouse"></i>
                <span>Warehouse</span>
            </a>
        </router-link>

        <hr class="sidebar-divider">

        <router-link  tag="li" to="/sale-man" class="nav-item">
            <a class="nav-link" >
                <i class="fas fa-users"></i>
                <span>Sale Man</span>
            </a>
        </router-link>
        @if(Auth::user()->email != "demo@mail.com")
        <hr class="sidebar-divider">

        <router-link  tag="li" to="/users" class="nav-item">
            <a class="nav-link" >
                <i class="fas fa-user-cog"></i>
                <span>User Setting</span>
            </a>
        </router-link>
        @endif
        @endif

        @if(Auth::user()->role->role_name == 'system')

        <hr class="sidebar-divider">

        <router-link  tag="li" to="/currency" class="nav-item">
            <a class="nav-link" >
                <i class="fas fa-money-bill-alt"></i>
                <span>Currency</span>
            </a>
        </router-link>
        @endif

        @endif

    <!-- Nav Item - Pages Collapse Menu -->
        @if(Request::path() == 'inventory')
        <li class="text-center">
            <i class="fas fa-boxes module_logo_sm"></i>
            <h6 class="text-white">Inventory</h6>
        </li>

        <hr class="sidebar-divider">

        <!--<router-link  tag="li" to="/inventory" class="nav-item">
            <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseTwo" aria-expanded="true" aria-controls="collapseTwo">
                <i class="fas fa-boxes"></i>
                <span>Inventory</span>
            </a>
            <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
                <div class="bg-white py-2 collapse-inner rounded">
                    <router-link  tag="span" to="/inventory/main-warehouse" >
                        <a class="collapse-item">Main Warehouse</a>
                    </router-link>
                    <router-link  tag="span" to="/inventory/transfer" >
                        <a class="collapse-item">Internal Transfer</a>
                    </router-link>
                    <router-link  tag="span" to="/inventory/receive" >
                        <a class="collapse-item">Internal Receive</a>
                    </router-link>
                </div>
            </div>
        </router-link>-->
        @if(Auth::user()->role->role_name != 'van_user')
        <router-link  tag="li" to="/inventory/main-warehouse" class="nav-item">
            <a class="nav-link" >
                <i class="fas fa-warehouse"></i>
                <span>Main Warehouse</span>
            </a>
        </router-link>
        <hr class="sidebar-divider">
        @endif



        <router-link  tag="li" to="/inventory/transfer" class="nav-item">
            <a class="nav-link" >
                <i class="fas fa-sign-out-alt"></i>
                <span>Internal Transfer</span>
            </a>
        </router-link>

        <hr class="sidebar-divider">

        <router-link  tag="li" to="/inventory/receive" class="nav-item">
            <a class="nav-link" >
                <i class="fas fa-sign-in-alt"></i>
                <span>Internal Receive</span>
            </a>
        </router-link>  
        <hr class="sidebar-divider">

        <router-link  tag="li" to="/inventory/adjustment" class="nav-item">
            <a class="nav-link" >
                <i class="fas fa-sign-in-alt"></i>
                <span>Inventory Adjustment</span>
            </a>
        </router-link>


        <!-- Divider -->
        <!--<hr class="sidebar-divider">-->
        @endif

        @if(Request::path() == 'van' || Request::path() == 'office')
            @if(Request::path() == 'office')
            <li class="text-center">
                <i class="fas fa-building module_logo_sm"></i>
                <h6 class="text-white">Sale</h6>
            </li>
            @else
            @endif

       

        @if(Auth::user()->role->role_name != 'office_order_user' && Auth::user()->role->role_name != 'delivery' && Auth::user()->role->id != 6 && Auth::user()->role->id != 7)
         <hr class="sidebar-divider">

        <router-link  tag="li" to="/order" class="nav-item">
            <a class="nav-link" href="#">
                <i class="fas fa-clipboard-list"></i>
                <span>Sale Order</span>
            </a>
        </router-link>

        <hr class="sidebar-divider">

        <!-- Van Sale == 2; Office Sale == 1; -->
        <router-link  tag="li" to="/sale_advance/" class="nav-item">
            <a class="nav-link" href="#">
                <i class="fas fa-coins"></i>
                <span>Sale Advance</span>
            </a>
        </router-link>

        <hr class="sidebar-divider">

        <!-- Van Sale == 2; Office Sale == 1; -->
        <router-link  tag="li" to="/sale/<?php echo $role = Request::path() == 'van' ? '2' : '1'; ?>/" class="nav-item">
            <a class="nav-link" href="#">
                <i class="fas fa-chart-line"></i>
                <span>Sale Invoice</span>
            </a>
        </router-link>

        <!--if(Request::path() != 'van' && Auth::user()->role->id != 6 && Auth::user()->role->id != 7) -->
        <!--for only system and admin role -->
        @if(Request::path() != 'van' && (Auth::user()->role->id == 1 || Auth::user()->role->id == 2 || Auth::user()->role->id == 3))
            <hr class="sidebar-divider">
            <router-link  tag="li" to="/collection" class="nav-item">
                <a class="nav-link" href="#">
                    <i class="fas fa-coins"></i>
                    <span>Credit Collection</span>
                </a>
            </router-link>
         @endif
        @endif

        <hr class="sidebar-divider">
        <router-link  tag="li" to="/sale_return" class="nav-item">
            <a class="nav-link" href="#">
                <i class="fas fa-coins"></i>
                <span>Sale Return</span>
            </a>
        </router-link>

        <hr class="sidebar-divider">
        <router-link  tag="li" to="/return_payment" class="nav-item">
            <a class="nav-link" href="#">
                <i class="fas fa-coins"></i>
                <span>Return Payment</span>
            </a>
        </router-link>

        <hr class="sidebar-divider">
        <router-link  tag="li" to="/customer_return" class="nav-item">
            <a class="nav-link" href="#">
                <i class="fas fa-coins"></i>
                <span>Customer Return</span>
            </a>
        </router-link>
        <!-- Divider -->
        <!--<hr class="sidebar-divider">

        <router-link  tag="li" to="/sale" class="nav-item">
            <a class="nav-link" href="#">
                <i class="fas fa-hand-holding-usd"></i>
                <span>Collection</span>
            </a>
        </router-link>-->

        @if(Request::path() != 'van' && (Auth::user()->role->id == 1 || Auth::user()->role->id == 2 || Auth::user()->role->id == 3 || Auth::user()->role->id == 8))
        <!-- only admin, system, office user and delivery roles -->
        <hr class="sidebar-divider">
        <router-link  tag="li" to="/delivery" class="nav-item">
            <a class="nav-link" href="#">
                <i class="fas fa-shipping-fast"></i>
                <span>Delivery</span>
            </a>
        </router-link>
        @endif

        @endif
            @if(Request::path() == 'van' || Request::path() == 'purchase_office')
                @if(Request::path() == 'purchase_office')
                    <li class="text-center">
                        <i class="fas fa-building module_logo_sm"></i>
                        <h6 class="text-white">Purchase</h6>
                    </li>
                @else
                @endif


                <hr class="sidebar-divider">

                @if(Auth::user()->role->role_name != 'office_order_user' && Auth::user()->role->role_name != 'delivery' && Auth::user()->role->id != 6 && Auth::user()->role->id != 7)

                <!-- Van Sale == 2; Office Sale == 1; -->
                <router-link  tag="li" to="/purchase_advance/" class="nav-item">
                    <a class="nav-link" href="#">
                        <i class="fas fa-coins"></i>
                        <span>Purchase Advance</span>
                    </a>
                </router-link>

                <hr class="sidebar-divider">

                <!-- Van Sale == 2; Office Sale == 1; -->
                    <router-link  tag="li" to="/purchase/<?php echo $role = Request::path() == 'van' ? '2' : '1'; ?>/" class="nav-item">
                        <a class="nav-link" href="#">
                            <i class="fas fa-chart-line"></i>
                            <span>Purchase Invoice</span>
                        </a>
                    </router-link>
                    <!--if(Request::path() != 'van' && Auth::user()->role->id != 6 && Auth::user()->role->id != 7) -->
                    <!--for only system and admin role -->

                @endif
                    @if(Request::path() != 'van' && (Auth::user()->role->id == 1 || Auth::user()->role->id == 2  || Auth::user()->role->role_name == 'office_user'))
                        <hr class="sidebar-divider">
                        <router-link  tag="li" to="/purchase_collection" class="nav-item">
                            <a class="nav-link" href="#">
                                <i class="fas fa-coins"></i>
                                <span>Credit Payment</span>
                            </a>
                        </router-link>
                    @endif


                <!-- Divider -->
                <!--<hr class="sidebar-divider">

                <router-link  tag="li" to="/sale" class="nav-item">
                    <a class="nav-link" href="#">
                        <i class="fas fa-hand-holding-usd"></i>
                        <span>Collection</span>
                    </a>
                </router-link>-->
            <hr class="sidebar-divider">

            <router-link  tag="li" to="/product_costing" class="nav-item">
                <a class="nav-link" href="#">
                    <i class="fas fa-coins"></i>
                    <span>Product Costing Entry</span>
                </a>
            </router-link>

            @endif
            @if(Request::path() == 'van' || Request::path() == 'account')
                @if(Request::path() == 'account')
                    <li class="text-center">
                        <i class="fas fa-building module_logo_sm"></i>
                        <h6 class="text-white">Account</h6>
                    </li>
                @else
                @endif


                <hr class="sidebar-divider">

                @if( Auth::user()->role->role_name != 'delivery' && Auth::user()->role->id != 6 && Auth::user()->role->id != 7)

                <!-- Van Sale == 2; Office Sale == 1; -->
                    <router-link  tag="li" to="/account_head" class="nav-item">
                        <a class="nav-link" href="#">
                            <i class="fas fa-chart-line"></i>
                            <span>Account Head</span>
                        </a>
                    </router-link>
                    <!--if(Request::path() != 'van' && Auth::user()->role->id != 6 && Auth::user()->role->id != 7) -->
                    <!--for only system and admin role -->

                @endif
                @if(Request::path() != 'van' && (Auth::user()->role->id == 1 || Auth::user()->role->id == 2 || Auth::user()->role->id == 3))
                    <hr class="sidebar-divider">
                    <router-link  tag="li" to="/sub_account" class="nav-item">
                        <a class="nav-link" href="#">
                            <i class="fas fa-coins"></i>
                            <span>Sub Account</span>
                        </a>
                    </router-link>
                @endif
                @if(Request::path() != 'van' && (Auth::user()->role->id == 1 || Auth::user()->role->id == 2 || Auth::user()->role->id == 3))
                    <hr class="sidebar-divider">
                    <router-link  tag="li" to="/receipt" class="nav-item">
                        <a class="nav-link" href="#">
                            <i class="fas fa-receipt"></i>
                            <span>Receipt</span>
                        </a>
                    </router-link>
                @endif
                @if(Request::path() != 'van' && (Auth::user()->role->id == 1 || Auth::user()->role->id == 2 || Auth::user()->role->id == 3))
                    <hr class="sidebar-divider">
                    <router-link  tag="li" to="/payment" class="nav-item">
                        <a class="nav-link" href="#">
                            <i class="fas fa-receipt"></i>
                            <span>Payment</span>
                        </a>
                    </router-link>
                @endif
                    @if(Request::path() != 'van' && (Auth::user()->role->id == 1 || Auth::user()->role->id == 2 || Auth::user()->role->id == 3))
                        <hr class="sidebar-divider">
                        <router-link  tag="li" to="/cashbook" class="nav-item">
                            <a class="nav-link" href="#">
                                <i class="fas fa-receipt"></i>
                                <span>Cashbook</span>
                            </a>
                        </router-link>
                        <hr class="sidebar-divider">
                        <router-link  tag="li" to="/ledger" class="nav-item">
                            <a class="nav-link" href="#">
                                <i class="fas fa-receipt"></i>
                                <span>Ledger</span>
                            </a>
                        </router-link>
                    @endif



            <!-- Divider -->
                <!--<hr class="sidebar-divider">

                <router-link  tag="li" to="/sale" class="nav-item">
                    <a class="nav-link" href="#">
                        <i class="fas fa-hand-holding-usd"></i>
                        <span>Collection</span>
                    </a>
                </router-link>-->

            @endif

        @if(Request::path() == 'report')
        <li class="text-center">
            <i class="far fa-chart-bar module_logo_sm"></i>
            <h6 class="text-white">Report</h6>
        </li>

        <hr class="sidebar-divider">
        <!--<router-link  tag="li" to="/report" class="nav-item">
            <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseRpt" aria-expanded="true" aria-controls="collapseTwo">
                <i class="fas fa-chart-bar"></i>
                <span>Reports</span>
            </a>
            <div id="collapseRpt" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
                <div class="bg-white py-2 collapse-inner rounded">
                    <router-link  tag="span" to="/report/daily-sale-rpt" >
                        <a class="collapse-item">Daily Sale Report</a>
                    </router-link>
                    <router-link  tag="span" to="/report/inventory-rpt" >
                        <a class="collapse-item">Inventory Report</a>
                    </router-link>
                </div>
            </div>
        </router-link>-->
        @if(Auth::user()->role->id != 6)
        <router-link  tag="li" to="/report/daily-sale-rpt" class="nav-item">
            <a class="nav-link" href="#">
                <i class="fas fa-chart-bar"></i>
                <span>Daily Sale Report</span>
            </a>
        </router-link>
        @endif
        <hr class="sidebar-divider">
        <router-link  tag="li" to="/report/daily-sale-product-rpt" class="nav-item">
            <a class="nav-link" href="#">
                <i class="fas fa-chart-bar"></i>
                <span>Daily Sale Product Wise Report</span>
            </a>
        </router-link>
                <hr class="sidebar-divider">
                <router-link  tag="li" to="/report/credit_payment_report" class="nav-item">
                    <a class="nav-link" href="#">
                        <i class="fas fa-chart-bar"></i>
                        <span>Credit Payment Report</span>
                    </a>
                </router-link>
                <hr class="sidebar-divider">
                <router-link  tag="li" to="/report/daily_purchase_product_report" class="nav-item">
                    <a class="nav-link" href="#">
                        <i class="fas fa-chart-bar"></i>
                        <span>Daily Purchase Product Wise Report</span>
                    </a>
                </router-link>
                {{-- @if(Auth::user()->role->id == 1) --}}
                    <hr class="sidebar-divider">
                    <router-link  tag="li" to="/report/purchase_outstanding" class="nav-item" >
                        <a class="nav-link" href="#">
                            <i class="fas fa-chart-bar"></i>
                            <span>Purchase Outstanding Report</span>
                        </a>
                    </router-link>
                    <hr class="sidebar-divider" >
                    <router-link  tag="li" to="/report/sale_outstanding" class="nav-item">
                        <a class="nav-link" href="#">
                            <i class="fas fa-chart-bar"></i>
                            <span>Sale Outstanding Report</span>
                        </a>
                    </router-link>
                {{-- @endif --}}
                <hr class="sidebar-divider">
                <router-link  tag="li" to="/report/credit_collection" class="nav-item">
                    <a class="nav-link" href="#">
                        <i class="fas fa-chart-bar"></i>
                        <span>Credit Collection Report</span>
                    </a>
                </router-link>
                <hr class="sidebar-divider">
                <router-link  tag="li" to="/report/valuation" class="nav-item">
                    <a class="nav-link" href="#">
                        <i class="fas fa-chart-bar"></i>
                        <span>Inventory Valuation Report</span>
                    </a>
                </router-link>
                <hr class="sidebar-divider">
                <router-link  tag="li" to="/report/profit_and_loss" class="nav-item">
                    <a class="nav-link" href="#">
                        <i class="fas fa-chart-bar"></i>
                        <span>Profit & Loss Report</span>
                    </a>
                </router-link>

        @if(Auth::user()->role->id != 6 && Auth::user()->role->id != 7)
       <!-- <router-link  tag="li" to="/report/ara-daily-sale-rpt" class="nav-item">
            <a class="nav-link" href="#">
                <i class="fas fa-chart-bar"></i>
                <span>ERP Daily Sale Report</span>
            </a>
        </router-link>

        <router-link  tag="li" to="/report/ara-daily-sale-product-rpt" class="nav-item">
            <a class="nav-link" href="#">
                <i class="fas fa-chart-bar"></i>
                <span>ERP Daily Sale Product Wise Report</span>
            </a>
        </router-link>-->
        @endif
        <!-- Divider -->
        <hr class="sidebar-divider">
        <router-link  tag="li" to="/report/inventory-rpt" class="nav-item">
            <a class="nav-link" href="#">
                <i class="fas fa-chart-bar"></i>
                <span>Inventory Report</span>
            </a>
        </router-link>

        <!-- Divider -->
       <!-- <hr class="sidebar-divider">

        <router-link  tag="li" to="/report/so-product-rpt" class="nav-item">
            <a class="nav-link" href="#">
                <i class="fas fa-chart-bar"></i>
                <span>Sale Order Product Wise Report</span>
            </a>
        </router-link>

        @if(Auth::user()->role->id != 6)

        <hr class="sidebar-divider">

        <router-link  tag="li" to="/report/sale-comparison-rpt" class="nav-item">
            <a class="nav-link" href="#">
                <i class="fas fa-chart-bar"></i>
                <span>Sale Edit Comparison Report</span>
            </a>
        </router-link>
        @endif -->

        <!-- Divider -->
        <!--<hr class="sidebar-divider">

        <router-link  tag="li" to="/report/pending-approval-rpt" class="nav-item">
            <a class="nav-link" href="#">
                <i class="fas fa-chart-bar"></i>
                <span>Pending Approval Report</span>
            </a>
        </router-link>-->

        <hr class="sidebar-divider">
        <router-link  tag="li" to="/report/blacklist-rpt" class="nav-item">
            <a class="nav-link" href="#">
                <i class="fas fa-chart-bar"></i>
                <span>Black List Report</span>
            </a>
        </router-link>

        <hr class="sidebar-divider">
        <router-link  tag="li" to="/report/customer-wise-rpt" class="nav-item">
            <a class="nav-link" href="#">
                <i class="fas fa-chart-bar"></i>
                <span>Category and Product Name Wise Report</span>
            </a>
        </router-link>

        <hr class="sidebar-divider">
        <router-link  tag="li" to="/report/product-costing-rpt" class="nav-item">
            <a class="nav-link" href="#">
                <i class="fas fa-chart-bar"></i>
                <span>Product Costing Report</span>
            </a>
        </router-link>

        <hr class="sidebar-divider">
        <router-link  tag="li" to="/report/currency-gain-loss-rpt" class="nav-item">
            <a class="nav-link" href="#">
                <i class="fas fa-chart-bar"></i>
                <span>Purchase Currency Gain/Loss Report</span>
            </a>
        </router-link>

        <hr class="sidebar-divider">
        <router-link  tag="li" to="/report/sale-currency-gain-loss-rpt" class="nav-item">
            <a class="nav-link" href="#">
                <i class="fas fa-chart-bar"></i>
                <span>Sale Currency Gain/Loss Report</span>
            </a>
        </router-link>

        <hr class="sidebar-divider">
        <router-link  tag="li" to="/report/sale-return-rpt" class="nav-item">
            <a class="nav-link" href="#">
                <i class="fas fa-chart-bar"></i>
                <span>Sale Return Report</span>
            </a>
        </router-link>

        <hr class="sidebar-divider">
        <router-link  tag="li" to="/report/sale-return-product-rpt" class="nav-item">
            <a class="nav-link" href="#">
                <i class="fas fa-chart-bar"></i>
                <span>Sale Return Product Report</span>
            </a>
        </router-link>

        <hr class="sidebar-divider">
        <router-link  tag="li" to="/report/sale-return-payment-rpt" class="nav-item">
            <a class="nav-link" href="#">
                <i class="fas fa-chart-bar"></i>
                <span>Return Payment Report</span>
            </a>
        </router-link>

        <hr class="sidebar-divider">
        <router-link  tag="li" to="/report/return-os-rpt" class="nav-item">
            <a class="nav-link" href="#">
                <i class="fas fa-chart-bar"></i>
                <span>Return OS Report</span>
            </a>
        </router-link>

        <hr class="sidebar-divider">
        <router-link  tag="li" to="/report/sale-analyst-rpt" class="nav-item">
            <a class="nav-link" href="#">
                <i class="fas fa-chart-bar"></i>
                <span>Sale Analyst Report</span>
            </a>
        </router-link>

        @endif



    <!-- Divider -->
    @if(Auth::user()->role->role_name != 'office_order_user')
    <hr class="sidebar-divider d-none d-md-block">
    @endif

    <!-- Sidebar Toggler (Sidebar) -->
    <!--<div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
    </div>-->

    @endif

</ul>
<!-- End of Sidebar -->
