<template>
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="#!"><i class="feather icon-home"></i></a></li>
                <li class="breadcrumb-item"><a :href="site_path+'/'">Home</a></li>
                <li class="breadcrumb-item"><a :href="site_path+'/reminder'">Reminder</a></li>
                <li class="breadcrumb-item active" aria-current="page">Min-Max Report</li>

            </ol>
        </nav>
        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h4 class="mb-0 text-gray-800">Min-Max Report</h4>
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
                    <div class="form-group col-md-4 col-lg-3 mm-txt">
                        <label for="branch_id">Branch</label>
                        <select id="branch_id" class="form-control mm-txt"
                            name="branch_id" v-model="search.branch_id" style="width:100%"
                        >
                            <option value="">Select One</option>
                            <option v-for="branch in branches" :value="branch.id"  >{{branch.branch_name}}</option>
                        </select>
                    </div>

                    <div class="form-group col-md-4 col-lg-3">
                        <label for="warehouse_id">Warehouse</label>
                        <select id="warehouse_id" class="form-control"
                            name="warehouse_id" v-model="search.warehouse_id" style="width:100%" required
                        >
                            <option value="">Select One</option>
                            <option v-for="warehouse in warehouses" :value="warehouse.id"  >{{warehouse.warehouse_name}}</option>
                        </select>
                    </div>
                     <div class="form-group col-md-4 col-lg-3 mm-txt">
                        <label for="brand_id">Brand</label>
                        <select id="brand_id" class="form-control mm-txt"
                            name="brand_id" v-model="search.brand_id" style="width:100%" required
                        >
                            <option value="">Select One</option>
                            <option v-for="brand in brands" :value="brand.id"  >{{brand.brand_name}}</option>
                        </select>
                    </div>
                    <div class="form-group col-md-4 col-lg-3">
                        <label for="product_code">Product Code</label>
                        <input type="text" class="form-control" id="product_code" name="product_code" v-model="search.product_code">
                    </div>                    

                    <div class="form-group col-md-4 col-lg-3">
                        <label for="product_name">Product Name</label>
                        <input type="text" class="form-control" id="product_name" name="product_name"
                        v-model="search.product_name">
                    </div>
                    <!-- Kamlesh Start -->
                    <div class="form-group col-md-4 col-lg-3 mm-txt">
                        <label for="category_id">Category</label>
                        <select id="category_id" class="form-control mm-txt"
                            name="category_id" v-model="search.category_id" style="width:100%" required
                        >
                            <option value="">Select One</option>
                            <option v-for="category in categories" :value="category.id"  >{{category.category_name}}</option>
                        </select>
                    </div>
                    <!-- Kamlesh End -->
                    <div class="form-group col-md-4 col-lg-3 mm-txt">
                        <label for="type_id">Type</label>
                        <select id="type_id" class="form-control mm-txt"
                            name="type_id" v-model="search.type_id" style="width:100%" required
                        >
                            <option value="">Select One</option>
                            <option value="min" >Min</option>
                            <option value="max" >Max</option>
                        </select>
                    </div>

                    <div class="form-group col-md-3 col-lg-2">
                        <label class="small" for="search">&nbsp;</label>
                        <button
                          class="form-control btn btn-primary font-weight-bold"
                          @click="getMinMaxReport(1)"
                        ><i class="fas fa-search"></i> &nbsp;&nbsp;Search </button>
                    </div>
                </div>
            </div>
        </div>
        <!-- table start -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Min-Max Report</h6>
            </div>
            <div class="card-body">

                <!-- sort by -->
                <!-- <div class="form-group float-left pr-2" v-if="min_max.length > 0">
                    <label for="sort_by">Sort By</label>
                    <select id="sort_by" class="form-control"
                        name="sort_by" v-model="search.sort_by" style="width:200px" @change="getMinMaxReport(1)"
                    >
                        <option value="">Select One</option>
                        <option value="order_no">Sale Order No.</option>
                    </select>
                </div>
                <div v-if="min_max.length > 0">

                    <div class="form-group float-left">
                        <select id="order" class="form-control mt-2"
                            name="order" v-model="search.order" style="width:150px; margin-left:10px;" @change="getMinMaxReport(1)"
                        >
                            <option value="">Select One</option>
                            <option value="ASC">Ascending</option>
                            <option value="DESC">Descending</option>
                        </select>
                    </div>
                    <div class="text-right form-group mt-4" >
                        <div class="text-right mb-2" v-if="min_max.length > 0">
                            <button class="btn btn-primary btn-icon btn-sm" @click="exportExcel()"><i class="fas fa-file-excel"></i> &nbsp;Export to Excel</button>
                        </div>
                    </div>
                </div> -->
                <!-- end sort by -->

                <!--<div class="text-right mb-2" v-if="min_max.length > 0">
                    <button class="btn btn-primary btn-icon btn-sm" @click="exportExcel()"><i class="fas fa-file-excel"></i> &nbsp;Export to Excel</button>
                </div>-->
               <div class="text-right form-group mt-4" >
                <div class="text-right mb-2" v-if="min_max.length > 0">
                    <button class="btn btn-primary btn-icon btn-sm" @click="exportExcel()"><i class="fas fa-file-excel"></i> &nbsp;Export to Excel</button>
                </div>
            </div>
               <div class="table-responsive">
                    <table class="table table-bordered table-striped table_no_2" id="dataTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th class="text-center">No.</th>
                                <th class="text-center">Product Code</th>
                                <th class="text-center">Product Name</th>
                                <th class="text-center">Brand</th>
                                <th class="text-center">Category </th>
                                <th class="text-center">Balance</th>
                                <th class="text-center" v-if="search.type_id== 'min'">Min Qty</th>
                                <th class="text-center" v-else-if="search.type_id== 'max'">Max Qty</th>
                                <th class="text-center" v-else>Qty</th>
                                <!-- <th class="text-center" v-if="search.type_id== 'min'">Min Qty</th>
                                <th class="text-center" v-else-if="search.type_id== 'max'">Max Qty</th>
                                <th class="text-center" v-else>Qty</th> -->
                                <th class="text-center">Type</th>
                            </tr>
                        </thead>
                        <tbody>
                            <template v-for="mm in min_max">
                                <template v-if="mm.balance != mm.maximum_qty && mm.balance != mm.minimum_qty">
                                <tr v-if="search.type_id== 'min' && mm.balance<mm.minimum_qty">
                                    <td class="text-right"></td>
                                    <td class="text-center">{{mm.product_code}}</td>
                                    <td class="text-center">{{mm.product_name}}</td>
                                    <td class="text-center">{{mm.brand_name}}</td>
                                    <td class="text-center">{{mm.category_name}}</td>
                                    <td class="text-center" >{{mm.balance}}</td>
                                    <td class="text-center" > {{mm.minimum_qty}}</td>
                                    <td class="text-center" >Short</td>
                                    <!-- <td class="text-center">{{mm.uom_name}}</td> -->
                                </tr>
                                <tr v-if="search.type_id== 'max' && mm.balance>mm.maximum_qty">
                                    <td class="text-right"></td>
                                    <td class="text-center">{{mm.product_code}}</td>
                                    <td class="text-center">{{mm.product_name}}</td>
                                    <td class="text-center">{{mm.brand_name}}</td>
                                    <td class="text-center">{{mm.category_name}}</td>
                                    <td class="text-center" >{{mm.balance}}</td>
                                    <td class="text-center" > {{mm.maximum_qty}}</td>
                                    <!-- <td class="text-center" v-else-if="mm.balance>mm.maximum_qty"> {{mm.maximum_qty}}</td> -->
                                    <td class="text-center" >Excess</td>
                                    <!-- <td class="text-center" v-else-if="mm.balance>mm.maximum_qty">Excess</td> -->
                                    <!-- <td class="text-center">{{mm.uom_name}}</td> -->
                                </tr>
                                <tr v-if="search.type_id != 'max' && search.type_id!= 'min' && (mm.balance<mm.minimum_qty  || mm.balance>mm.maximum_qty)">
                                    <td class="text-right"></td>
                                    <td class="text-center">{{mm.product_code}}</td>
                                    <td class="text-center">{{mm.product_name}}</td>
                                    <td class="text-center">{{mm.brand_name}}</td>
                                    <td class="text-center">{{mm.category_name}}</td>
                                    <td class="text-center" >{{mm.balance}}</td>
                                    <td class="text-center" v-if="mm.balance<mm.minimum_qty"> {{mm.minimum_qty}}</td>
                                    <td class="text-center" v-else-if="mm.balance>mm.maximum_qty"> {{mm.maximum_qty}}</td>
                                    <td class="text-center" v-if="mm.balance<mm.minimum_qty">Short</td>
                                    <td class="text-center" v-else-if="mm.balance>mm.maximum_qty">Excess</td>
                                    <!-- <td class="text-center">{{mm.uom_name}}</td> -->
                                </tr>
                                </template>
                            </template>
                            <!-- <tr>
                                <th colspan="12" class="text-right">Total</th>
                                <th class="text-right">{{priceTotal}}</th>
                            </tr> -->
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
                    order_no: "",
                    customer_id: "",
                    product_code: "",
                    product_name: "",
                    brand_id: "",
                    category_id: "", //Kamlesh
                    type_id: "",
                    sort_by: '',
                    order: '',
                    warehouse_id: "",
                    branch_id: "",
                },
                min_max: [],
                customers:[],
                brands: [],
                categories:[], //Kamlesh
                user_year: '',
                user_role: '',
                sale_mans: [],
                warehouses:[],
                branches: [],
                site_path: '',
                storage_path: '',
                user_role_id: '',
            };
        },

        created() {
            this.user_year = document.querySelector("meta[name='user-year-likelink']").getAttribute('content');

            this.user_role = document.querySelector("meta[name='user-role']").getAttribute('content');

            this.site_path = document.querySelector("meta[name='site-path']").getAttribute('content');
            //this.site_path = this.site_path.substring(this.site_path.lastIndexOf('/')+1);
            this.storage_path = document.querySelector("meta[name='storage-path']").getAttribute('content');

            this.user_role_id = document.querySelector("meta[name='user-role-id']").getAttribute('content');
            
            if(this.user_role != "admin" && this.user_role != "system" && this.user_role_id != 19 && this.user_role_id != 15 && this.user_role_id != 16)
            {
                var url =  window.location.origin;
                window.location.replace(url);
            }
        },

        mounted() {
            $("#loading").hide();
            let app = this;

            app.initBrands();
            app.initCategories(); //kamlesh
            app.initWarehouses();
            app.initBranches();

              $("#type_id").select2();

            $("#type_id").on("select2:select", function(e) {

                var data = e.params.data;
                app.search.type_id = data.id;
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

            $("#warehouse_id").on("select2:select", function(e) {

                var data = e.params.data;
                app.search.warehouse_id = data.id;
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
                //app.search.from_date = moment().format('YYYY-MM-DD');
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
                //app.search.to_date = moment().format('YYYY-MM-DD');
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

            $("#brand_id").on("select2:select", function(e) {

                var data = e.params.data;
                app.search.brand_id = data.id;
            });
            // Kamlesh Start
            $("#category_id").on("select2:select", function(e) {

                var data = e.params.data;
                app.search.category_id = data.id;
            });
            // Kamlesh End
        },

        methods: {

          
            //   initSaleMan() {
            //   axios.get("/sale_men").then(({ data }) => (this.sale_mans = data.data));
            //   $("#type_id").select2();
            // },

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
            // Kamlesh Start
            initCategories() {
              axios.get("/categories").then(({ data }) => (this.categories = data.data));
              $("#category_id").select2();
            },
            // Kamlesh End

            getMinMaxReport(page = 1) {


                $("#loading").show();
                let app = this;

                var search =
                    "&product_code=" +
                    app.search.product_code +
                    "&brand_id=" +
                    app.search.brand_id +
                    "&type_id=" +
                    app.search.type_id +
                    "&product_name=" +
                    app.search.product_name +
                    "&warehouse_id=" +
                    app.search.warehouse_id +
                    "&branch_id=" +
                    app.search.branch_id+
                    "&category_id=" +
                    app.search.category_id; //kamlesh
                    

                axios.get("/reminder/get_min_max?" + search).then( res  => {
                    app.min_max = res.data.min_max
                    console.log(res);
                    })
                .then(function() {
                    $("#loading").hide();
                });
            },

            exportExcel() {    

                let app = this;

              var search =
                 "&product_code=" +
                app.search.product_code +
                "&brand_id=" +
                app.search.brand_id +
                "&type_id=" +
                app.search.type_id +
                "&product_name=" +
                app.search.product_name +
                "&warehouse_id=" +
                app.search.warehouse_id +
                "&branch_id=" +
                app.search.branch_id+
                "&category_id=" +
                app.search.category_id; //kamlesh

                /*axios.get("/so_product_export?" + search)
                .then(function(response) {
                  console.log(response);
                })
                .catch(error => {
                  console.log(error);
                })
                .finally(() => loading.hide());*/

                var baseurl = window.location.origin;
                //window.open(baseurl+'/so_product_export?'+search);
                window.open(this.site_path+'/reminder/min_max_export?'+search);
            },

            dateFormat(d) {
                //return moment(d).format('YYYY-MM-DD');
                return moment(d).format('DD-MM-YYYY');
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

        computed: {
          priceTotal: function(){

            let sum = 0;
            let app = this;
              app.totalcount = 0;
              app.min_max.forEach(function(order) {
                if(order.total_amount != null && order.total_amount != '') {
                    sum += (parseFloat(order.total_amount));
                }

              });


           return this.numberWithCommas(sum);
        },
      }, 
        

    }
</script>