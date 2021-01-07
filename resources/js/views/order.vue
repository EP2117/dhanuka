<template>
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="#!"><i class="feather icon-home"></i></a></li>
                <li class="breadcrumb-item"><a :href="site_path+'/'">Home</a></li>
                <li class="breadcrumb-item"><a :href="site_path+'/office'">Office Sale</a></li>
                <li class="breadcrumb-item active" aria-current="page">Sale Order</li>

            </ol>
        </nav>
        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h4 class="mb-0 text-gray-800">Sale Order</h4>
            <router-link :to="'/order/new'" class="d-sm-inline-block btn btn-primary shadow-sm inventory" v-if='user_role == "system" || user_role == "office_user" || user_role == "office_order_user" || user_role == "Local Supervisor"'>
                <i class="fas fa-plus"></i> Add New Order
            </router-link>
        </div>

        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Search By</h6>
            </div>
            <div class="card-body">
                <div class="row">

                    <div class="form-group col-md-4 col-lg-3">
                        <label for="from_date">From Date</label>
                        <input type="text" class="form-control datetimepicker" id="from_date" name="from_date"
                        v-model="search.from_date">
                    </div>

                    <div class="form-group col-md-4 col-lg-3">
                        <label for="to_date">To Date</label>
                        <input type="text" class="form-control datetimepicker" id="to_date" name="to_date"
                        v-model="search.to_date">
                    </div>

                    <div class="form-group col-md-4 col-lg-3">
                        <label for="order_no">Order No.</label>
                        <input type="text" class="form-control" id="order_no" name="order_no" v-model="search.order_no">
                    </div>

                    <div class="form-group col-md-4 col-lg-3 mm-txt" v-if="(user_role == 'system' || user_role == 'admin' || user_role == 'Country Head')">
                        <label for="branch_id">Branch</label>
                        <select id="branch_id" class="form-control mm-txt"
                            name="branch_id" v-model="search.branch_id" style="width:100%"
                        >
                            <option value="">Select One</option>
                            <option v-for="branch in branches" :value="branch.id"  >{{branch.branch_name}}</option>
                        </select>
                    </div>

                    <div class="form-group col-md-4 col-lg-3 mm-txt">
                        <label for="customer_id">Customer</label>
                        <select id="customer_id" class="form-control mm-txt"
                            name="customer_id" v-model="search.customer_id" style="width:100%" required
                        >
                            <option value="">Select One</option>
                            <option v-for="cus in customers" :value="cus.id"  >{{cus.cus_name}}</option>
                        </select>
                    </div>

                    <div class="form-group col-md-4 col-lg-3 mm-txt" v-if="user_role != 'van_user' && user_role != 'office_order_user'">
                        <label for="sale_man_id">Sale Man</label>
                        <select id="sale_man_id" class="form-control mm-txt"
                            name="sale_man_id" v-model="search.sale_man_id" style="width:100%" required
                        >
                            <option value="">Select One</option>
                            <option v-for="sale_man in sale_mans" :value="sale_man.id"  >{{sale_man.sale_man}}</option>
                        </select>
                    </div> 

                    <div class="form-group col-md-3 col-lg-2">
                        <label class="small" for="search">&nbsp;</label>
                        <button
                          class="form-control btn btn-primary font-weight-bold"
                          @click="getOrders(1)"
                        ><i class="fas fa-search"></i> &nbsp;&nbsp;Search </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- table start -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Order List</h6>
            </div>
            <div class="card-body">
              <div class="table-responsive" v-if="order_count > 0">
                    <table class="table table-bordered table-striped" id="dataTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th class="text-center">No.</th>
                                <th class="text-center">Order No.</th>
                                <th class="text-center">Order Date</th>
                                <th class="text-center">Branch</th>
                                <th class="text-center">Customer</th>
                                <th class="text-center">Sale Man</th>
                                <th class="text-center">Sub Total</th>
                                <th class="text-center">Remark</th>
                                <th class="text-center">Created Time</th>
                                <th class="text-center">Updated Time</th>
                                <th class="text-center"></th>
                            </tr>
                        </thead>
                        <tfoot>
                            <tr>
                                <th class="text-center">No.</th>
                                <th class="text-center">Order No.</th>
                                <th class="text-center">Order Date</th>
                                <th class="text-center">Branch</th>
                                <th class="text-center">Customer</th>
                                <th class="text-center">Sale Man</th>
                                <th class="text-center">Sub Total</th>
                                <th class="text-center">Remark</th>
                                <th class="text-center">Created Time</th>
                                <th class="text-center">Updated Time</th>
                                <th class="text-center"></th>
                            </tr>
                        </tfoot>
                        <tbody>
                            <tr v-for="order,index in orders.data">
                                <td class="text-right">{{((currentPage * perPage) - perPage) + (index+1)}}</td>
                                <td class="textalign">{{order.order_no}}</td>
                                <td class="textalign">{{order.order_date}}</td>
                                <td v-if="order.branch != null" class="textalign">{{order.branch.branch_name}}</td>
                                <td v-else></td>
                                <td class="mm-txt">{{order.customer.cus_name}}</td>
                                <td class="mm-txt">{{order.sale_man != null ? order.sale_man.sale_man : ''}}</td>
                                <td class="text-right">{{order.net_total}}</td>
                                <td class="mm-txt">{{order.remark}}</td>
                                <td>{{localTime(order.created_at)}}</td>
                                <td>{{localTime(order.updated_at)}}</td>

                                <!--<td class="text-center" v-if="order.order_status == 'Draft'">
                                    <router-link tag="span" :to="'/order/edit/' + order.id" >
                                        <a href="#" title="Edit/View" class="">
                                            <i class="fas fa-edit"></i>
                                        </a>&nbsp;
                                    </router-link>

                                    <a title="Print" class="text-primary" @click="generatePDF(order.id)" v-if="user_role == 'admin' || user_role == 'system'">
                                        <i class="fas fa-print"></i>
                                    </a>
                                        
                                    <a title="Delete" class="text-danger" @click="removeOrder(order.id)" v-if="user_role == 'system' || user_id == order.sale_man_id ">
                                        <i class="fas fa-trash"></i>
                                    </a>                              
                                </td>

                                <td class="text-center" v-else>
                                    <router-link tag="span" :to="'/order/edit/' + order.id" >
                                        <a href="#" title="Edit/View" class="">
                                            <i class="fas fa-edit"></i>
                                        </a>&nbsp;
                                    </router-link>

                                    <a title="Print" class="text-primary" @click="generatePDF(order.id)" v-if="user_role == 'admin' || user_role == 'system'">
                                        <i class="fas fa-print"></i>
                                    </a>
                                </td>-->

                                <!--Kamlesh Start-->
                                <td class="text-center" v-if="order.order_status == 'Draft'">
                                    <div class="dropdown">
                                        <a class="btn btn-sm btn-icon-only text-danger " href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <i class="fas fa-ellipsis-v"></i>
                                        </a>
                                        <div class="dropdown-menu dropdown-menu-right dropdown-menu-arrow">
                                            <a class="dropdown-item">
                                                <router-link tag="span" :to="'/order/edit/' + order.id" >
                                                    <a href="#" title="Edit/View" class="">
                                                        <i class="fas fa-edit"></i>
                                                    </a>&nbsp;
                                                </router-link>
                                            </a>
                                            <a class="dropdown-item">
                                                <a title="Print" class="text-primary" @click="generatePDF(order.id)" v-if="user_role == 'admin' || user_role == 'system'">
                                                    <i class="fas fa-print"></i>
                                                </a>
                                            </a>
                                            <a class="dropdown-item">    
                                                <a title="Delete" class="text-danger" @click="removeOrder(order.id)" v-if="user_role == 'system' || user_id == order.sale_man_id ">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                            </a>
                                        </div>
                                    </div>

                                </td>
                                <!-- Kamlesh End-->

                                <!--Kamlesh Start-->
                                <td class="text-center" v-else>
                                    <div class="dropdown">
                                        <a class="btn btn-sm btn-icon-only text-danger " href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <i class="fas fa-ellipsis-v"></i>
                                        </a>
                                        <div class="dropdown-menu dropdown-menu-right dropdown-menu-arrow">
                                            <a class="dropdown-item">
                                                <router-link tag="span" :to="'/order/edit/' + order.id" >
                                                    <a href="#" title="Edit/View" class="">
                                                        <i class="fas fa-edit"></i>
                                                    </a>&nbsp;
                                                </router-link>
                                            </a>
                                            <a class="dropdown-item">
                                                <a title="Print" class="text-primary" @click="generatePDF(order.id)" v-if="user_role == 'admin' || user_role == 'system'">
                                                    <i class="fas fa-print"></i>
                                                </a>
                                            </a>
                                        </div>
                                    </div>

                                </td>
                                <!-- Kamlesh End-->
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div v-else>
                    <h5 class="text-center m-5">No record found!</h5>
                </div>

            </div>

            <div class="card-footer text-center">
          
              <!-- pagination start -->
              <div class="row" style="overflow:auto">
                <div class="col-12">
                  <template v-if="order_count > 0">
                    <div class="overflow-auto text-center" style="display:inline-block">
                      <!-- Use text in props -->
                      <b-pagination
                        v-model="currentPage"
                        :total-rows="rows"
                        :per-page="perPage"
                        first-text="First"
                        prev-text="Prev"
                        next-text="Next"
                        last-text="Last"
                      >
                        <template v-slot:first-text><span class="text-success" v-on:click="getOrders(1)">First</span></template>
                        <template v-slot:prev-text><span class="text-danger" v-on:click="getOrders(currentPage)">Prev</span></template>
                        <template v-slot:next-text><span class="text-warning" v-on:click="getOrders(currentPage)">Next</span></template>
                        <template v-slot:last-text><span class="text-info" v-on:click="getOrders(pagination.last_page)">Last</span></template>
                        <template v-slot:ellipsis-text>
                        </template>
                        <template v-slot:page="{ page, active }">
                          <span v-if="active"><b>{{ page }}</b></span>
                          <span v-else v-on:click="getOrders(page)">{{ page }}</span>
                        </template>
                      </b-pagination>
                    </div>
                  </template>
                </div>
              </div>
              <!-- pagination end -->
            </div>

        </div>
        <!-- table end -->
        <div id="loading" class="text-center"><img :src="storage_path+'/image/loader_2.gif'" /></div>
    </div>

</template>

<script>
    export default {

        data() {
            return {
                search: {
                    from_date: "",
                    to_date: "",
                    order_no: "",
                    customer_id: "",
                    sale_man_id: "",
                    branch_id: "",
                },
                pagination: {
                    total: "",
                    next: "",
                    prev: "",
                    last_page: "",
                    current_page: "",
                    next_page_url:""
                },
                orders: [],
                customers: [],
                sale_mans: [],
                user_role: '',
                user_id: '',
                user_year: "",
                rows: "",
                perPage: "15",
                currentPage: 1,
                order_count: 0,
                branches: [],
                site_path: '',
                storage_path: '',
            };
        },

        created() {
            this.user_role = document.querySelector("meta[name='user-role']").getAttribute('content');
            this.user_id   = document.querySelector("meta[name='user-id-likelink']").getAttribute('content');
            this.user_year = document.querySelector("meta[name='user-year-likelink']").getAttribute('content'); 

            this.site_path = document.querySelector("meta[name='site-path']").getAttribute('content');
            //this.site_path = this.site_path.substring(this.site_path.lastIndexOf('/')+1);
            this.storage_path = document.querySelector("meta[name='storage-path']").getAttribute('content');
            if( this.user_role == "van_user") {
                var url =  window.location.origin;
                window.location.replace(this.site_path);
            }
            this.getOrders();   

            console.log(localStorage.getItem('storedData200')); 
        },
        mounted() {
            let app = this;
            app.initSaleMan();
            app.initCustomers();
            app.initBranches();

            $("#customer_id").on("select2:select", function(e) {

                var data = e.params.data;
                app.search.customer_id = data.id;
            });

            $("#sale_man_id").on("select2:select", function(e) {

                var data = e.params.data;
                app.search.sale_man_id = data.id;
            });

            $("#branch_id").on("select2:select", function(e) {

                var data = e.params.data;
                app.search.branch_id = data.id;
            });

            $("#from_date")
                .datetimepicker({
            icons: {
                    time: "fa fa-clock-o",
                    date: "fa fa-calendar",
                    up: "fa fa-chevron-up",
                    down: "fa fa-chevron-down",
                    previous: "fa fa-chevron-left",
                    next: "fa fa-chevron-right",
                    today: "fa fa-screenshot",
                    clear: "fa fa-trash",
                    close: "fa fa-remove"
                },
                format:"YYYY-MM-DD",
                minDate: app.user_year+"-01-01",
                maxDate: app.user_year+"-12-31",
            })
            .on("dp.show", function(e) {
                var y = new Date().getFullYear();
                if(app.user_year < y) { 
                  if(app.search.from_date == app.user_year+"-12-31" || app.search.from_date == '') {
                    app.search.from_date = app.user_year+"-12-31";
                  }
                }
            })
            .on("dp.change", function(e) {
                var formatedValue = e.date.format("YYYY-MM-DD");
                //console.log(formatedValue);
                app.search.from_date = formatedValue;
            });

            $("#to_date")
                .datetimepicker({
            icons: {
                    time: "fa fa-clock-o",
                    date: "fa fa-calendar",
                    up: "fa fa-chevron-up",
                    down: "fa fa-chevron-down",
                    previous: "fa fa-chevron-left",
                    next: "fa fa-chevron-right",
                    today: "fa fa-screenshot",
                    clear: "fa fa-trash",
                    close: "fa fa-remove"
                },
                format:"YYYY-MM-DD",
                minDate: app.user_year+"-01-01",
                maxDate: app.user_year+"-12-31",
            })
            .on("dp.show", function(e) {
                var y = new Date().getFullYear();
                if(app.user_year < y) { 
                  if(app.search.to_date == app.user_year+"-12-31" || app.search.to_date == '') {
                    app.search.to_date = app.user_year+"-12-31";
                  }
                }
            })
            .on("dp.change", function(e) {
                var formatedValue = e.date.format("YYYY-MM-DD");
                //console.log(formatedValue);
                app.search.to_date = formatedValue;
            });

            app.initCustomers();
            $("#customer_id").on("select2:select", function(e) {

                var data = e.params.data;
                app.search.customer_id = data.id;
            });
        },

        methods: {

            initCustomers() {
              axios.get("/customers").then(({ data }) => (this.customers = data.data));
              $("#customer_id").select2();
            },

            initBranches() {
              axios.get("/branches_byuser").then(({ data }) => (this.branches = data.data));
              $("#branch_id").select2();
            },

            initSaleMan() {
              axios.get("/sale_men").then(({ data }) => (this.sale_mans = data.data));
              $("#sale_man_id").select2();
            },

            getOrders(page = 1) {
                $("#loading").show();
                let app = this;

                var search =
                    "&from_date=" +
                    app.search.from_date +
                    "&to_date=" +
                    app.search.to_date +
                    "&order_no=" +
                    app.search.order_no +
                    "&customer_id=" +
                    app.search.customer_id +
                    "&branch_id=" +
                    app.search.branch_id +
                    "&sale_man_id=" +
                    app.search.sale_man_id;

                /*axios.get("/order?" + search).then(({ data }) => (app.orders = data.data))
                .then(function() {
                    $("#loading").hide();
                });*/

                axios.get("/order?page=" + page + search).then(function(response) {
                    $("#loading").hide();
                    let data = response.data.data;
                    app.orders = data;
                    app.order_count = app.orders.data.length;
                    app.pagination.last_page = data.last_page;
                    app.pagination.next = data.next_page_url;
                    app.pagination.prev = data.prev_page_url;
                    app.pagination.total = data.total;
                    app.pagination.current_page = data.current_page;
                    app.pagination.next_page_url = data.next_page_url;


                    app.rows = data.total;
                    app.currentPage = data.current_page;
                    console.log(app.currentPage);
                });
            },

            generatePDF(order_id)
            {
                var baseurl = window.location.origin;
                //window.open(baseurl+'/generate_order/'+order_id);
                window.open(this.site_path+'/generate_order/'+order_id);
            },

            removeOrder(id) {
                let app = this;
                swal({
                    title: "Are you sure?",
                    text: "Once deleted, you will not be able to recover!",
                    icon: "warning",
                    buttons: true,
                    dangerMode: true
                    }).then(willDelete => {
                    if (willDelete) {
                        axios.delete("/order/" + id).then(function(response) {
                            //console.log(response);
                            app.getOrders(app.currentPage);
                            swal(response.data.message, {
                                icon: "success"
                            }); 
                        });
                          
                    } else {
                      //
                    }
                });
            },

            dateFormat(d) {
                return moment(d).format('YYYY-MM-DD');
            },

            numberWithCommas(x) {
                if(x != null) {
                  return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
                } else {
                  return x;
                }
            },

            localTime(utcTime) 
            {
                var utcDate = moment.utc(utcTime+'Z');
                // Apply a time zone
                var localTimezone = utcDate.tz('Asia/Rangoon');
                return localTimezone.format('YYYY-MM-DD HH:mm:ss');
            },
        },

        
    }
</script>