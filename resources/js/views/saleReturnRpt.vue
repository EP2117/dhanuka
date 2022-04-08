<template>
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="#!"><i class="feather icon-home"></i></a></li>
                <li class="breadcrumb-item"><a :href="site_path+'/'">Home</a></li>
                <li class="breadcrumb-item"><a :href="site_path+'/report'">Report</a></li>
                <li class="breadcrumb-item active" aria-current="page">Sale Return Report</li>

            </ol>
        </nav>
        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h4 class="mb-0 text-gray-800">Sale Return Report</h4>
           <!-- <router-link to="/inventory/transfer/new" class="d-sm-inline-block btn btn-primary shadow-sm inventory">
                <i class="fas fa-plus"></i> Add New Transfer
            </router-link>-->
        </div>

        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Search By</h6>
            </div>
            <div class="card-body">
                <div class="row">

                    <div class="form-group col-md-4 col-lg-3">
                        <label for="invoice_no">Invoice Number</label>
                        <input type="text" class="form-control" id="invoice_no" name="invoice_no" v-model="search.invoice_no">
                    </div>

                    <div class="form-group col-md-4 col-lg-3">
                        <label for="invoice_date">Invoice Date</label>
                        <input type="text" class="form-control datetimepicker" id="invoice_date" name="invoice_date"
                        v-model="search.invoice_date">
                    </div>

                    <div class="form-group col-md-4 col-lg-3">
                        <label for="return_no">Return Number</label>
                        <input type="text" class="form-control" id="return_no" name="return_no" v-model="search.return_no">
                    </div>

                    <div class="form-group col-md-4 col-lg-3">
                        <label for="from_date">Return From Date</label>
                        <input type="text" class="form-control datetimepicker" id="from_date" name="from_date"
                        v-model="search.from_date">
                    </div>

                    <div class="form-group col-md-4 col-lg-3">
                        <label for="to_date">Return To Date</label>
                        <input type="text" class="form-control datetimepicker" id="to_date" name="to_date"
                        v-model="search.to_date">
                    </div>

                    <div class="form-group col-md-4 col-lg-3 mm-txt">
                        <label >Customer</label>
                        <select id="customer_id" class="form-control mm-txt"
                                name="customer_id" v-model="search.customer_id" style="width:100%" required
                        >
                            <option value="">Select One</option>
                            <option v-for="cus in customers" :value="cus.id"  >{{cus.cus_name}}</option>
                        </select>
                    </div>

                    <div class="form-group col-md-4 col-lg-3">
                        <label for="return_method">Return Method</label>
                        <select id="return_method" class="form-control"
                            name="return_method" v-model="search.return_method" style="width:100%" 
                        >
                            <option value="">Select One</option>
                            <option value="with invoice">with Invoice</option>
                            <option value="without invoice">without Invoice</option>
                        </select>
                    </div>

                    <!--

                    <div class="form-group col-md-4 col-lg-3">
                        <label for="landed_cost_no">Landed Cost Number</label>
                        <input type="text" class="form-control" id="landed_cost_no" name="landed_cost_no" v-model="search.landed_cost_no">
                    </div>

                    

                    <div class="form-group col-md-4 col-lg-3">
                        <label for="container_no">Container Number</label>
                        <input type="text" class="form-control" id="container_no" name="container_no" v-model="search.container_no">
                    </div>

                    <div class="form-group col-md-4 col-lg-3">
                        <label for="product_name">Product Name</label>
                        <input type="text" class="form-control" id="product_name" name="product_name"
                               v-model="search.product_name">
                    </div>-->

                    <div class="form-group col-md-3 col-lg-2">
                        <label class="small" for="search">&nbsp;</label>
                        <button
                          class="form-control btn btn-primary font-weight-bold"
                          @click="getSaleReturn(1)"
                        ><i class="fas fa-search"></i> &nbsp;&nbsp;Search </button>
                    </div>
                </div>
            </div>
        </div>
        <!-- table start -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Sale Return List</h6>
            </div>
            <div class="card-body">
                <div class="text-right form-group" >
                        <div class="text-right mb-2" v-if="costings.length > 0">
                            <button class="btn btn-primary btn-icon btn-sm" @click="exportExcel()"><i class="fas fa-file-excel"></i> &nbsp;Export to Excel</button>
                        </div>
                    </div>
                <!-- sort by -->
                <!--<div class="form-group float-left pr-2" v-if="approvals.length > 0">
                    <label for="sort_by">Sort By</label>
                    <select id="sort_by" class="form-control"
                        name="sort_by" v-model="search.sort_by" style="width:200px" @change="getSOProducts(1)"
                    >
                        <option value="">Select One</option>
                        <option value="order_no">Sale Order No.</option>
                    </select>
                </div>
                <div v-if="approvals.length > 0">

                    <div class="form-group float-left">
                        <select id="order" class="form-control mt-2"
                            name="order" v-model="search.order" style="width:150px; margin-left:10px;" @change="getSOProducts(1)"
                        >
                            <option value="">Select One</option>
                            <option value="ASC">Ascending</option>
                            <option value="DESC">Descending</option>
                        </select>
                    </div>
                    <div class="text-right form-group mt-4" >
                        <div class="text-right mb-2" v-if="approvals.length > 0">
                            <button class="btn btn-primary btn-icon btn-sm" @click="exportExcel()"><i class="fas fa-file-excel"></i> &nbsp;Export to Excel</button>
                        </div>
                    </div>
                </div>-->
                <!-- end sort by -->

               <div class="table-responsive">
                    <table class="table bordered" id="border" style="border:solid 1px #ccc;" width="100%" cellspacing="0">
                        <thead class="costing_rpt">
                            <tr>
                                <th class="text-center costing_th">No.</th>
                                <th class="text-center costing_th">Invoice No.</th>
                                <th class="text-center costing_th">Invoice Date</th>
                                <th class="text-center costing_th">Return No.</th>
                                <th class="text-center costing_th">Date</th>
                                <th class="text-center costing_th">Customer</th>
                                <th class="text-center costing_th">Return Amount</th>
                            </tr>
                        </thead>
                        <tbody id="result">
                            
                        </tbody>
                    </table>
                </div>
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
                    invoice_date: "",
                    invoice_no: "",
                    return_no: "",
                    customer_id: "",
                    return_method: "",
                    from_date: "",
                    to_date: "",
                },
                costings: [],
                customers:[],
                user_year: '',
                user_role: '',
                users: [],
                site_path: '',
                storage_path: '',
            };
        },

        created() {
            this.user_year = document.querySelector("meta[name='user-year-likelink']").getAttribute('content');

            this.user_role = document.querySelector("meta[name='user-role']").getAttribute('content');

            this.site_path = document.querySelector("meta[name='site-path']").getAttribute('content');
            //this.site_path = this.site_path.substring(this.site_path.lastIndexOf('/')+1);
            this.storage_path = document.querySelector("meta[name='storage-path']").getAttribute('content');
        },

        mounted() {
            $("#loading").hide();
            let app = this;

            app.initCustomers();
            //app.initBrands();
            //app.initWarehouses();
            //app.initUsers();
            $("#invoice_date")
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
                app.search.invoice_date = moment().format('YYYY-MM-DD');
                if(app.user_year < y) { 
                  if(app.search.invoice_date == app.user_year+"-12-31" || app.search.invoice_date == '') {
                    app.search.invoice_date = app.user_year+"-12-31";
                  }
                }
            })
            .on("dp.change", function(e) {
                var formatedValue = e.date.format("YYYY-MM-DD");
                //console.log(formatedValue);
                app.search.invoice_date = formatedValue;
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
                app.search.from_date = moment().format('YYYY-MM-DD');
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
                app.search.to_date = moment().format('YYYY-MM-DD');
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

            $("#customer_id").on("select2:select", function(e) {
                var data = e.params.data;
                app.search.customer_id = data.id;
            });

            //app.getCurrencyGainLoss();
        },

        methods: {

            initCustomers() {
              axios.get("/customers").then(({ data }) => (this.customers = data.data));
              $("#customer_id").select2();
            },


           /** initUsers() {
              axios.get("/all_users").then(({ data }) => (this.users = data.data));
              $("#user_id").select2();
            },

            initBranches() {
              axios.get("/branches_byuser").then(({ data }) => (this.branches = data.data));
              $("#branch_id").select2();
            }, **/

            localTime(utcTime) 
            {
                var utcDate = moment.utc(utcTime+'Z');
                // Apply a time zone
                var localTimezone = utcDate.tz('Asia/Rangoon');
                return localTimezone.format('YYYY-MM-DD hh:mm:ss');
            },

            dateFormat(d) {
                return moment(d).format('YYYY-MM-DD hh:mm');
            },

            getSaleReturn(page = 1) {

                /*if(this.search.from_date == "") {                  
                    swal("Warning!", "From Date must be added!", "warning")
                    return false;
                }*/

                $("#loading").show();
                let app = this;


                var search =
                    "&invoice_date=" +
                    app.search.invoice_date +
                    "&invoice_no=" +
                    app.search.invoice_no +
                    "&return_no=" +
                    app.search.return_no +
                    "&customer_id=" +
                    app.search.customer_id +
                    "&return_method=" +
                    app.search.return_method +
                    "&from_date=" +
                    app.search.from_date +
                    "&to_date=" +
                    app.search.to_date;

                axios.get("/sale_return_report?" + search)
                .then(function(response) {
                  console.log(response.data.html);
                  $("#result").html(response.data.html);
                  if(response.data.html != "") {
                   app.costings.push('1');
                  } else { app.costings = []; }
                  $('#loading').hide();
                })
                .catch(error => {
                  console.log(error);
                });
            },

            exportExcel() {    

                let app = this;
               /* if(this.search.from_date == "") {                  
                    swal("Warning!", "From Date must be added!", "warning")
                    return false;
                } */

              var search =
                    "&invoice_date=" +
                    app.search.invoice_date +
                    "&invoice_no=" +
                    app.search.invoice_no +
                    "&return_no=" +
                    app.search.return_no +
                    "&customer_id=" +
                    app.search.customer_id +
                    "&return_method=" +
                    app.search.return_method +
                    "&from_date=" +
                    app.search.from_date +
                    "&_to_date=" +
                    app.search.to_date;

                /*axios.get("/so_product_export?" + search)
                .then(function(response) {
                  console.log(response);
                })
                .catch(error => {
                  console.log(error);
                })
                .finally(() => loading.hide());*/

                var baseurl = window.location.origin;
                //window.open(baseurl+'/sale_return_export?'+search);
                window.open(this.site_path+'/sale_return_export?'+search);
            },
        },

    }
</script>