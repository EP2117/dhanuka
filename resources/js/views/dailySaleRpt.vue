<template>
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="#!"><i class="feather icon-home"></i></a></li>
                <li class="breadcrumb-item"><a :href="site_path+'/'">Home</a></li>
                <li class="breadcrumb-item"><a :href="site_path+'/report'">Report</a></li>
                <li class="breadcrumb-item active" aria-current="page">Daily Sale Report</li>

            </ol>
        </nav>
        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h4 class="mb-0 text-gray-800">Daily Sale Report</h4>
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
                        <label for="invoice_no">Invoice No.</label>
                        <input type="text" class="form-control" id="invoice_no" name="invoice_no" v-model="search.invoice_no">
                    </div>
                    <div class="form-group col-md-4 col-lg-3 mm-txt">
                        <label for="branch_id">Branch</label>
                        <select id="branch_id" class="form-control mm-txt"
                            name="branch_id" v-model="search.branch_id" style="width:100%"
                        >
                            <option value="">Select One</option>
                            <option v-for="branch in branches" :value="branch.id"  >{{branch.branch_name}}</option>
                        </select>
                    </div>

                    <!--<div class="form-group col-md-4 col-lg-3">
                        <label for="warehouse_id">Warehouse</label>
                        <select id="warehouse_id" class="form-control"
                            name="warehouse_id" v-model="search.warehouse_id" style="width:100%" required
                        >
                            <option value="">Select One</option>
                            <option v-for="warehouse in warehouses" :value="warehouse.id"  >{{warehouse.warehouse_name}}</option>
                        </select>
                    </div>-->
                    <div class="form-group col-md-4 col-lg-3 mm-txt">
                        <label >State</label>
                        <select id="state_id" class="form-control mm-txt"
                                 v-model="search.state_id" style="width:100%" required
                        >
                            <option value="">Select One</option>
                            <option v-for="s in states" :value="s.id"  >{{s.state_name}}</option>
                        </select>
                    </div>
                    <div class="form-group col-md-4 col-lg-3">
                        <label for="township_id">Township</label>
                        <select id="township_id" class="form-control mm-txt"
                            name="township_id" v-model="search.township_id" style="width:100%" required
                        >
                            <option value="">Select One</option>
                            <option v-for="tsp in townships" :value="tsp.id"  >{{tsp.township_name}}</option>
                        </select>
                    </div>                    

                    <div class="form-group col-md-4 col-lg-3">
                        <label for="customer_id">Customer</label>
                        <select id="customer_id" class="form-control mm-txt"
                            name="customer_id" v-model="search.customer_id" style="width:100%" required
                        >
                            <option value="">Select One</option>
                            <option v-for="cus in customers" :value="cus.id"  >{{cus.cus_name}}</option>
                        </select>
                    </div>

                    <div class="form-group col-md-4 col-lg-3">
                        <label for="cus_type">Customer Type</label>
                        <select id="cus_type" class="form-control mm-txt"
                            name="cus_type" v-model="search.cus_type" style="width:100%" required
                        >
                            <option value="">Select One</option>
                            <option v-for="ctype in cus_types" :value="ctype.id"  >{{ctype.customer_type_name}}</option>
                        </select>
                    </div>

                    <div class="form-group col-md-4 col-lg-3 mm-txt">
                        <label for="sale_man_id">Sale Man</label>
                        <select id="sale_man_id" class="form-control mm-txt"
                            name="sale_man_id" v-model="search.sale_man_id" style="width:100%" required
                        >
                            <option value="">Select One</option>
                            <option v-for="sale_man in sale_mans" :value="sale_man.id"  >{{sale_man.sale_man}}</option>
                        </select>
                    </div>

                    <!--<div class="form-group col-md-4 col-lg-3 mm-txt">
                        <label for="office_sale_man_id">Office Sale Man</label>
                        <select id="office_sale_man_id" class="form-control mm-txt"
                            name="office_sale_man_id" v-model="search.office_sale_man_id" style="width:100%" required
                        >
                            <option value="">Select One</option>
                            <option v-for="office_sale_man in office_sale_mans" :value="office_sale_man.id"  >{{office_sale_man.name}}</option>
                        </select>
                    </div>-->

                    <!--<div class="form-group col-md-4 col-lg-3">
                        <label for="ref_no">Reference No.</label>
                        <input type="text" class="form-control" id="ref_no" name="ref_no" v-model="search.ref_no">
                    </div>-->

                    <div class="form-group col-md-3 col-lg-2">
                        <label class="small" for="search">&nbsp;</label>
                        <button
                          class="form-control btn btn-primary font-weight-bold"
                          @click="getDailySales(1)"
                        ><i class="fas fa-search"></i> &nbsp;&nbsp;Search </button>
                    </div>
                </div>
            </div>
        </div>
        <!-- table start -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Sale Invoice List</h6>
            </div>
            <div class="card-body">

                <!-- sort by -->
                <div class="form-group float-left pr-2" v-if="sales.length > 0">
                    <label for="sort_by">Sort By</label>
                    <select id="sort_by" class="form-control"
                        name="sort_by" v-model="search.sort_by" style="width:200px" @change="getDailySales(1)"
                    >
                        <option value="">Select One</option>
                        <option value="invoice_no">Invoice No.</option>
                    </select>
                </div>
                <div v-if="sales.length > 0">

                    <div class="form-group float-left">
                        <select id="order" class="form-control mt-2"
                            name="order" v-model="search.order" style="width:150px; margin-left:10px;" @change="getDailySales(1)"
                        >
                            <option value="">Select One</option>
                            <option value="ASC">Ascending</option>
                            <option value="DESC">Descending</option>
                        </select>
                    </div>
                    <div class="text-right form-group mt-4" >
                        <div class="mb-2" v-if="sales.length > 0" style="display:inline-block">
                            <button class="btn btn-primary btn-icon btn-sm" @click="exportExcel()"><i class="fas fa-file-excel"></i> &nbsp;Export to Excel</button>
                        </div>

                        <div class="mb-2 pl-2" v-if="sales.length > 0" style="display:inline-block;">
                            <button class="btn btn-primary btn-icon btn-sm" @click="exportPdf()"><i class="fas fa-file-pdf"></i> &nbsp;Export to PDF</button>
                        </div>
                    </div>
                </div>
                <!-- end sort by -->

                <!-- <div class="text-right" >
                    <div class="mb-2" v-if="sales.length > 0" style="display:inline-block">
                        <button class="btn btn-primary btn-icon btn-sm" @click="exportExcel()"><i class="fas fa-file-excel"></i> &nbsp;Export to Excel</button>
                    </div>

                    <div class="mb-2 pl-2" v-if="sales.length > 0" style="display:inline-block;">
                        <button class="btn btn-primary btn-icon btn-sm" @click="exportPdf()"><i class="fas fa-file-pdf"></i> &nbsp;Export to PDF</button>
                    </div>
                </div> -->
                <div style="clear:both;"></div>

                <div class="table-responsive">
                    <table class="table table-bordered table-striped table_no" id="dataTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th class="text-center">No.</th>
                                <th class="text-center">Invoice No.</th>
                                <!--<th class="text-center">Reference No.</th>-->
                                <th class="text-center">Date</th>
                                <th class="text-center">Branch</th>
                                <th class="text-center">Customer</th>
                                <th class="text-center">Sale Man</th>
                                <!--<th class="text-center">Office Sale Man</th>-->
                                <th class="text-center">Total Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            <template v-for="sale in sales">
                                <tr>
                                    <td class="text-right"></td>
                                    <td class="textalign">{{sale.invoice_no}}</td>
                                    <!--<td class="textalign">{{sale.reference_no}}</td>-->
                                    <td class="textalign">{{sale.invoice_date}}</td>
                                    <td v-if="sale.branch != null" class="textalign">{{sale.branch.branch_name}}</td>
                                    <td v-else></td>
                                    <td class="mm-txt">{{sale.customer.cus_name}}</td>
                                    <!--<td class="mm-txt textalign" v-if="sale.order != null && sale.order.sale_man != null">{{sale.order.sale_man.name}}</td>
                                    <td v-else></td>-->
                                    <td class="mm-txt textalign" v-if="sale.office_sale_man_id != null">{{sale.sale_man.sale_man}}</td>
                                            <td v-else></td>
                                    <td class="text-right">{{sale.net_total}}</td>
                                </tr>
                            </template>
                            <tr>
                                <th colspan="6" class="text-right">Total</th>
                                <th class="text-right">{{priceTotal}}</th>
                            </tr>
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
                    from_date: "",
                    to_date: "",
                    invoice_no: "",
                    ref_no: "",
                    customer_id: "",
                    warehouse_id: "",
                    township_id: "",
                    cus_type: "",
                    sale_man_id: "",
                    state_id:'',
                    office_sale_man_id: "",
                    sort_by: '',
                    order: '',
                    branch_id: '',
                },
                sales: [],
                townships: [],
                cus_types: [],
                customers:[],
                warehouses:[],
                user_year: '',
                sale_mans: [],
                states:[],
                office_sale_mans: [],
                branches: [],
                site_path: '',
                storage_path: '',
            };
        },

        created() {
            this.user_year = document.querySelector("meta[name='user-year-likelink']").getAttribute('content');

            this.site_path = document.querySelector("meta[name='site-path']").getAttribute('content');
            //this.site_path = this.site_path.substring(this.site_path.lastIndexOf('/')+1);
            this.storage_path = document.querySelector("meta[name='storage-path']").getAttribute('content');
        },

        mounted() {
            $("#loading").hide();
            let app = this;

            //app.initCustomers();
           // app.initWarehouses();
            app.initTownships();
            app.initTypes();
            app.initBranches();
            app.initStates();

            app.initSaleMan();

            $("#sale_man_id").on("select2:select", function(e) {

                var data = e.params.data;
                app.search.sale_man_id = data.id;
            });

            app.initOfficeSaleMan();

            $("#office_sale_man_id").on("select2:select", function(e) {

                var data = e.params.data;
                app.search.office_sale_man_id = data.id;
            });

            $("#branch_id").on("select2:select", function(e) {

                var data = e.params.data;
                app.search.branch_id = data.id;
                app.warehouses = []; 
                if(data.id != "") {
                    axios.get("warehouses_bybranch/"+ data.id).then(({ data }) => (app.warehouses = data.data));
                } else {
                    axios.get("warehouses_bybranch/null").then(({ data }) => (app.warehouses = data.data));
                }
            });
             $("#state_id").select2();
            $("#state_id").on("select2:select", function(e) {
                app.townships=[];
                var data = e.params.data;
                app.search.state_id = data.id;
                axios.get("/township_by_state/"+ data.id).then(({ data }) => (app.townships = data.data));

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
                app.search.from_date = moment().format('YYYY-MM-DD');
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
                app.search.to_date = moment().format('YYYY-MM-DD');
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

            /*$("#customer_id").on("select2:select", function(e) {

                var data = e.params.data;
                app.search.customer_id = data.id;
            });*/

            $("#customer_id").select2({
                allowClear: true,
                placeholder: "Select One",
                minimumInputLength: 3,
                ajax: {
                    url: app.site_path+'/search_customers',
                    data: function (params) {
                    var query = {
                        term: params.term,           
                    }

                    return query;
                    },
                    processResults: function (data) {

                        return {
                            results: $.map(data, function (obj) {
                                return { 
                                    'id': obj.id, 
                                    'text': obj.cus_name,
                                    'datatsp': obj.id,
                                };
                            })
                        };
                    }
                }
            });
            $("#customer_id").on("select2:select", function(e) {
                    var data = e.params.data;
                    
                    app.search.customer_id = data.id;
                });
            $("#customer_id").on("select2:unselecting", function(e) {
                    app.search.customer_id = '';
                });

            $("#township_id").on("select2:select", function(e) {

                var data = e.params.data;
                app.search.township_id = data.id;
            });

            $("#cus_type").on("select2:select", function(e) {

                var data = e.params.data;
                app.search.cus_type = data.id;
            });

            $("#warehouse_id").on("select2:select", function(e) {

                var data = e.params.data;
                app.search.warehouse_id = data.id;
            });
        },

        methods: {

            /**initSaleMan() {
              axios.get("/sale_man").then(({ data }) => (this.sale_mans = data.data));
              $("#sale_man_id").select2();
            },**/
            initStates() {
                    axios.get("/state").then(({ data }) => (this.states = data.data));
                    $("#state_id").select2();
            },
            initSaleMan() {
              axios.get("/sale_men").then(({ data }) => (this.sale_mans = data.data));              
              $("#sale_man_id").select2();
            },

            initOfficeSaleMan() {
              axios.get("/office_sale_man").then(({ data }) => (this.office_sale_mans = data.data));
              $("#office_sale_man_id").select2();
            },

            initBranches() {
              axios.get("/branches_byuser").then(({ data }) => (this.branches = data.data));
              $("#branch_id").select2();
            },

            initWarehouses() {
              axios.get("/warehouses").then(({ data }) => (this.warehouses = data.data));
              $("#warehouse_id").select2();
            },

            initCustomers() {
              axios.get("/customers").then(({ data }) => (this.customers = data.data));
              $("#customer_id").select2();
            },

            initTypes() {
              axios.get("/customer_type").then(({ data }) => (this.cus_types = data.data));
              //console.log(this.cus_types);
              $("#cus_type").select2();
            },

            initTownships() {
              axios.get("/township").then(({ data }) => (this.townships = data.data));
              $("#township_id").select2();
            },

            getDailySales(page = 1) {

                if(this.search.from_date == "") {                  
                    swal("Warning!", "From Date must be added!", "warning")
                    return false;
                } 

                $("#loading").show();
                let app = this;
                var search =
                    "&from_date=" +
                    app.search.from_date +
                    "&to_date=" +
                    app.search.to_date +
                    "&invoice_no=" +
                    app.search.invoice_no +
                    "&ref_no=" +
                    app.search.ref_no +
                    "&warehouse_id=" +
                    app.search.warehouse_id +
                    "&customer_id=" +
                    app.search.customer_id +
                    "&township_id=" +
                    app.search.township_id +
                    "&sale_man_id=" +
                    app.search.sale_man_id +
                    "&office_sale_man_id=" +
                    app.search.office_sale_man_id +
                    "&cus_type=" +
                    app.search.cus_type +
                    "&order=" +
                    app.search.order +
                    "&branch_id=" +
                    app.search.branch_id +
                    "&state_id=" +
                    app.search.state_id +
                    "&sort_by=" +
                    app.search.sort_by;

                axios.get("/daily_sale_report?" + search).then(({ data }) => (app.sales = data.data))
                .then(function() {
                    console.log(app.sales);
                    $("#loading").hide();
                });
            },

            exportExcel() {   

                let app = this;
                if(this.search.from_date == "") {                  
                    swal("Warning!", "From Date must be added!", "warning")
                    return false;
                } 

                var search =
                    "&from_date=" +
                    app.search.from_date +
                    "&to_date=" +
                    app.search.to_date +
                    "&invoice_no=" +
                    app.search.invoice_no +
                    "&ref_no=" +
                    app.search.ref_no +
                    "&warehouse_id=" +
                    app.search.warehouse_id +
                    "&customer_id=" +
                    app.search.customer_id +
                    "&township_id=" +
                    app.search.township_id +
                    "&sale_man_id=" +
                    app.search.sale_man_id +
                    "&office_sale_man_id=" +
                    app.search.office_sale_man_id +
                    "&cus_type=" +
                    app.search.cus_type +
                    "&order=" +
                    app.search.order +
                    "&branch_id=" +
                    app.search.branch_id +
                    "&sort_by=" +
                    app.search.sort_by;

                /*axios.get("/daily_sales_export?" + search)
                .then(function(response) {
                  console.log(response);
                })
                .catch(error => {
                  console.log(error);
                })
                .finally(() => loading.hide());*/

                var baseurl = window.location.origin;
                //window.open(baseurl+'/daily_sale_export?'+search);
                window.open(this.site_path+'/daily_sale_export?'+search);
            },

            exportPdf() {   

                let app = this;
                if(this.search.from_date == "") {                  
                    swal("Warning!", "From Date must be added!", "warning")
                    return false;
                } else {
                    $("#loading").show();
                }

                var search =
                    "&from_date=" +
                    app.search.from_date +
                    "&to_date=" +
                    app.search.to_date +
                    "&invoice_no=" +
                    app.search.invoice_no +
                    "&ref_no=" +
                    app.search.ref_no +
                    "&warehouse_id=" +
                    app.search.warehouse_id +
                    "&customer_id=" +
                    app.search.customer_id +
                    "&township_id=" +
                    app.search.township_id +
                    "&sale_man_id=" +
                    app.search.sale_man_id +
                    "&office_sale_man_id=" +
                    app.search.office_sale_man_id +
                    "&cus_type=" +
                    app.search.cus_type +
                    "&order=" +
                    app.search.order +
                    "&branch_id=" +
                    app.search.branch_id +
                    "&sort_by=" +
                    app.search.sort_by;


                axios.get("/daily_sale_export_pdf?" + search, {responseType: 'blob'}).then(response => {
                    $('#loading').hide();
                    const url = window.URL.createObjectURL(new Blob([response.data], { type: 'application/pdf' }));
                    window.open(url);
                    /*const link = document.createElement('a');
                    link.href = url;
                    link.setAttribute('download', 'daily_sale_rpt.pdf'); //or any other extension
                    document.body.appendChild(link);
                    link.click();*/

                  })
                  .catch(error => {
                    console.log(error);
                  });

                /*axios.get("/daily_sale_export_pdf?" + search).then(response => {
                  $("#loading").hide();

                  let blob = new Blob([response.data], { type: 'application/pdf' }),
                    url = window.URL.createObjectURL(blob)

                  window.open(url) // Mostly the same, I was just experimenting with different approaches, tried link.click, iframe and other solutions
                });*/
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
        },

        computed: {
          priceTotal: function(){

            let sum = 0;
            let app = this;
              app.sales.forEach(function(sale) {

                if(sale.net_total != null && sale.net_total != '') {
                  sum += (parseFloat(sale.net_total));  
                }

              });


           return this.numberWithCommas(sum);
        },
      }, 
        

    }
</script>