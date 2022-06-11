<template>
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="#!"><i class="feather icon-home"></i></a></li>
                <li class="breadcrumb-item"><a :href="site_path+'/'">Home</a></li>
                <li class="breadcrumb-item"><a :href="site_path+'/report'">Report</a></li>
                <li class="breadcrumb-item active" aria-current="page">Sale Analyst Report</li>

            </ol>
        </nav>
        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h4 class="mb-0 text-gray-800">Daily Sale Analyst Report</h4>
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

                    <!--<div class="form-group col-md-4 col-lg-3">
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

                    <div class="form-group col-md-4 col-lg-3 mm-txt">
                        <label for="entry_date">Customer</label>
                        <select id="customer_id" class="form-control mm-txt"
                            name="customer_id" v-model="search.customer_id" style="width:100%" required
                        >
                            <option value="">Select One</option>
                            <option v-for="cus in customers" :value="cus.id"  >{{cus.cus_name}}</option>
                        </select>
                    </div>--> 

                    <div class="form-group col-md-4 col-lg-3 mm-txt">
                        <label for="brand_id">Brand</label>
                        <select id="brand_id" class="form-control mm-txt"
                            name="brand_id" v-model="search.brand_id" style="width:100%" required
                        >
                            <option value="">Select One</option>
                            <option v-for="brand in brands" :value="brand.id"  >{{brand.brand_name}}</option>
                        </select>
                    </div>

                    <div class="form-group col-md-4 col-lg-3 mm-txt">
                        <label for="category_id">Category</label>
                        <select id="category_id" class="form-control"
                            name="category_id" v-model="search.category_id" style="width:100%"
                        >
                            <option value="">Select One</option>
                            <option v-for="cat in categories" :value="cat.id">{{cat.category_name}}</option>
                        </select>
                    </div>

                    <div class="form-group col-md-4 col-lg-3">
                        <label for="product_code">Product Code</label>
                        <input type="text" class="form-control" id="product_code" name="product_code"
                        v-model="search.product_code">
                    </div>

                    <div class="form-group col-md-4 col-lg-3">
                        <label for="product_name">Product Name</label>
                        <input type="text" class="form-control" id="product_name" name="product_name"
                        v-model="search.product_name">
                    </div>

                    <div class="form-group col-md-3 col-lg-2">
                        <label class="small" for="search">&nbsp;</label>
                        <button
                          class="form-control btn btn-primary font-weight-bold"
                          @click="getSaleAnalyst(1)"
                        ><i class="fas fa-search"></i> &nbsp;&nbsp;Search </button>
                    </div>
                </div>
            </div>
        </div>
        <!-- table start -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Sale Product List</h6>
            </div>
            <div class="card-body">
                <!-- sort by -->
                <!--<div class="form-group float-left pr-2" v-if="products.length > 0">
                    <label for="sort_by">Sort By</label>
                    <select id="sort_by" class="form-control"
                        name="sort_by" v-model="search.sort_by" style="width:200px" @change="getDailySales(1)"
                    >
                        <option value="">Select One</option>
                        <option value="invoice_no">Invoice No.</option>
                    </select>
                </div>
                <div v-if="products.length > 0">

                    <div class="form-group float-left">
                        <select id="order" class="form-control mt-2"
                            name="order" v-model="search.order" style="width:150px; margin-left:10px;" @change="getDailySales(1)"
                        >
                            <option value="">Select One</option>
                            <option value="ASC">Ascending</option>
                            <option value="DESC">Descending</option>
                        </select>
                    </div>
                </div>-->
                <!-- end sort by -->
                <!--<div class="text-right mb-2" v-if="products.length > 0">
                    <button class="btn btn-primary btn-icon btn-sm" @click="exportExcel()"><i class="fas fa-file-excel"></i> &nbsp;Export to Excel</button>
                </div>-->
                <div class="text-right form-group mt-4" >
                    <div class="mb-2" v-if="products.length > 0" style="display:inline-block">
                        <button class="btn btn-primary btn-icon btn-sm" @click="exportExcel()"><i class="fas fa-file-excel"></i> &nbsp;Export to Excel</button>
                    </div>

                    <div class="mb-2 pl-2" v-if="products.length > 0" style="display:inline-block;">
                        <button class="btn btn-primary btn-icon btn-sm" @click="exportPdf()"><i class="fas fa-file-pdf"></i> &nbsp;Export to PDF</button>
                    </div>
                </div>

               <div class="table-responsive">
                    <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">  <!--kamlesh-->
                        <thead>
                            <tr>
                                <th class="text-center">No.</th>
                                <th class="text-center">Brand</th>
                                <th class="text-center">Category</th>
                                <th class="text-center">Product Code</th>
                                <th class="text-center">Product Name</th>
                                <th class="text-center">QTY</th>
                                <th class="text-center">Selling UOM</th>
                                <th class="text-center">Total Amount</th>
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
                    from_date: "",
                    to_date: "",
                    category_id: "",
                    product_name: "",
                    brand_id: "",
                    product_code: "",
                },
                products: [],
                customers:[],
                brands: [],
                warehouses:[],
                user_year: '',
                user_role: '',
                sale_mans: [],
                office_sale_mans: [],
                branches: [],
                categories: [],
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

            //app.initCustomers();
            app.initBrands();
            app.initCategories();
           // app.initWarehouses();
            //app.initBranches();

            //app.initSaleMan();
            //app.initOfficeSaleMan();

            $("#category_id").on("select2:select", function(e) {
                var data = e.params.data;
                app.search.category_id = data.id;
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


            $("#brand_id").on("select2:select", function(e) {

                var data = e.params.data;
                app.search.brand_id = data.id;
            });
        },

        methods: {

            initCategories() {
              axios.get("/categories").then(({ data }) => (this.categories = data.data));
              $("#category_id").select2();
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

            initBrands() {
              axios.get("/report_brands").then(({ data }) => (this.brands = data.data));
              $("#brand_id").select2();
            },

            getSaleAnalyst(page = 1) {

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
                    "&brand_id=" +
                    app.search.brand_id +
                    "&product_name=" +
                    app.search.product_name +
                    "&product_code=" +
                    app.search.product_code +
                    "&category_id=" +
                    app.search.category_id;

                axios.get("/sale_analyst_report?" + search)
                .then(function(response) {
                  console.log(response.data.html);
                  $("#result").html(response.data.html);
                  if(response.data.html != "") {
                   app.products.push('1');
                  } else { app.products = []; }
                  $('#loading').hide();
                })
                .catch(error => {
                  console.log(error);
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
                    "&brand_id=" +
                    app.search.brand_id +
                    "&product_name=" +
                    app.search.product_name +
                    "&product_code=" +
                    app.search.product_code +
                    "&category_id=" +
                    app.search.category_id;

                var baseurl = window.location.origin;
                //window.open(baseurl+'/daily_sale_product_export?'+search);
                window.open(this.site_path+'/sale_analyst_export?'+search);
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
                    "&brand_id=" +
                    app.search.brand_id +
                    "&product_name=" +
                    app.search.product_name +
                    "&product_code=" +
                    app.search.product_code +
                    "&category_id=" +
                    app.search.category_id;


                axios.get("/sale_analyst_export_pdf?" + search, {responseType: 'blob'}).then(response => {
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
            },

            dateFormat(d) {
                return moment(d).format('YYYY-MM-DD');
            },

            getSellingUom(product,uom_id) {
                var key = product.selling_uoms.findIndex(x => x.pivot.uom_id == uom_id);
                if(key == -1) {
                    return product.uom.uom_name;
                } else {
                    return product.selling_uoms[key].uom_name;
                }
            },

            numberWithCommas(x) {
                if(x != null) {
                  return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
                } else {
                  return x;
                }
            },
        },

    }
</script>