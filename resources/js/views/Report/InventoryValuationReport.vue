<template>
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="#!"><i class="feather icon-home"></i></a></li>
                <li class="breadcrumb-item"><a :href="site_path+'/'">Home</a></li>
                <li class="breadcrumb-item"><a :href="site_path+'/report'">Report</a></li>
                <li class="breadcrumb-item active" aria-current="page">Inventory Valuation Report</li>

            </ol>
        </nav>
        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h4 class="mb-0 text-gray-800">Inventory Valuation Report</h4>
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
                        <label for="brand_id">Brand</label>
                        <select id="brand_id" class="form-control mm-txt"
                            name="brand_id" v-model="search.brand_id" style="width:100%" required >
                            <option value="">Select One</option>
                            <option v-for="brand in brands" :value="brand.id"  >{{brand.brand_name}}</option>
                        </select>
                    </div>
                     <div class="form-group col-md-4 col-lg-3 mm-txt">
                        <label for="brand_id">Internal Category</label>
                        <select id="category_id" class="form-control mm-txt"
                            name="category_id" v-model="search.category_id" style="width:100%" required
                        >
                            <option value="">Select One</option>
                            <option v-for="cat in categories" :value="cat.id"  >{{cat.category_name}}</option>
                        </select>
                    </div>
                      <div class="form-group col-md-4 col-lg-3">
                        <label for="product_code">Product Code</label>
                        <input type="text" class="form-control" id="product_code" name="product_code"
                        v-model="search.product_code">
                    </div>
                      <div class="form-group col-md-4 col-lg-3">
                        <label for="from_date"> Date</label>
                        <input type="text" class="form-control datetimepicker" id="date" name="date"
                        v-model="search.date">
                    </div>
                     <div class="form-group col-md-4 col-lg-3">
                        <label for="product_name">Product Name</label>
                        <input type="text" class="form-control" id="product_name" name="product_name"
                        v-model="search.product_name">
                    </div>
                    <div class="form-group col-md-3 col-lg-2">
                        <label class="small" >&nbsp;</label>
                        <button
                          class="form-control btn btn-primary font-weight-bold"
                          @click="getInventoryValuation(1)"
                        ><i class="fas fa-search"></i> &nbsp;&nbsp;Search </button>
                    </div>
                </div>
            </div>
        </div>
        <!-- table start -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Product List</h6>
            </div>
            <div class="card-body">
                <!-- Kamlesh Start -->
                <div class="text-right form-group mt-4" >
                    <!-- <div class="mb-2" v-if="out_count > 0" style="display:inline-block">
                        <button class="btn btn-primary btn-icon btn-sm" @click="exportExcel()"><i class="fas fa-file-excel"></i> &nbsp;Export to Excel</button>
                        
                    </div> -->
                    <div class="mb-2 pl-2" v-if="products.length> 0" style="display:inline-block;">
                        <button class="btn btn-primary btn-icon btn-sm" @click="exportPdf()"><i class="fas fa-file-pdf"></i> &nbsp;Export to PDF</button>
                    </div>
                </div>
                <!-- Kamlesh End -->
               <div class="table-responsive" v-if="products.length > 0">
                    <table class="table table-bordered table-striped table_num" id="dataTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th class="text-center">No.</th>
                                <th class="text-center">Brand</th>
                                <th class="text-center">Internal Category</th>
                                <th class="text-center">Product Code</th>
                                <th class="text-center">Product Name</th>
                                <th class="text-center">Warehouse UOM</th>
                                <th class="text-center">Balance Qty </th>
                                <th class="text-center">Valuation Amount</th>
                                <th class="text-center">Adjustment In <br />Valuation Amount</th>
                                <th class="text-center">Adjustment Out <br />Valuation Amount</th>
                                <th class="text-center">Before Adjustment <br />Valuation Amount</th>
                                <!-- <th class="text-center">Stock <br />Receive</th> -->
                                <!-- <th class="text-center">Stock <br />Transfer</th> -->
<!--                                <th class="text-center">Sale Order</th>-->
<!--                                <th class="text-center">Approval <br />QTY</th>-->
<!--                                <th class="text-center">Revise <br />QTY</th>-->
<!--                                <th class="text-center">Revise Sale</th>-->
<!--                                <th class="text-center">No Revise Sale</th>-->
                                <!-- <th class="text-center">Direct Sale</th> -->
<!--                                <th class="text-center">Reserve <br />Balance</th>-->
                                <!-- <th class="text-center">Balance</th> -->
                            </tr>
                        </thead>
                        <tbody>
                                <template v-for="product in products">
                                      <tr>
                                        
                                    <td></td>
                                    <td  class="text-center">{{product.brand_name}}</td>
                                    <td  class="text-center">{{product.category_name}}</td>
                                    <td class="text-center">{{product.product_code}}</td>
                                    <td class="text-center">{{product.product_name}}</td>
                                    <td class="text-center">{{product.uom_name}}</td>
                                    <!-- <td class="text-center">{{product.minimum_qty}}</td> -->
                                    <td class="text-center">{{product.balance}}</td>
                                    <!-- <td class="text-center">{{product.valuation_amount==null? 0 : product.p_valuation_amount}}</td> -->
                                    <td class="text-center">{{product.t_valuation_amount}}</td>
                                    <td class="text-center">{{product.adj_in_cost_price}}</td>
                                    <td class="text-center">{{product.adj_out_cost_price}}</td>
                                    <td class="text-center">{{(parseInt(product.t_valuation_amount) + parseInt(product.adj_out_cost_price)) - parseInt(product.adj_in_cost_price)}}</td>
                                    <!-- <td>
                                        {{(parseInt(product.product_opening) + parseInt(product.inQqty)+ parseInt(product.receiveQty) +parseInt(product.add_qty)  )-(parseInt(product.saleQty)  + parseInt(product.transferQty))}}
                                    </td> -->
                                </tr>

                                </template>
                               <tr>
                                   <td colspan ="7" style="text-align: right;"><strong>Total Amt</strong></td>
                                   <td class="text-center">
                                       {{this.total_valuation}}
                                   </td>
                                   <td class="text-center">
                                       {{this.total_adj_in}}
                                   </td>
                                   <td class="text-center">
                                       {{this.total_adj_out}}
                                   </td>
                                   <td class="text-center">
                                       {{this.total_after_valuation}}
                                   </td>
                               </tr>
                            <!-- <template v-else>
                            <tr v-for="product in products">
                                <td></td>
                                    <td>{{product.brand_name}}</td>
                                    <td>{{product.category_name}}</td>
                                    <td>{{product.product_code}}</td>
                                    <td>{{product.product_name}}</td>
                                    <td>{{product.uom_name}}</td>
                                    <td>{{product.min_qty}}</td>
                                    <td>
                                        {{product.balance=(parseInt(product.product_opening) + parseInt(product.inQty) + parseInt(product.add_qty)  + parseInt(product.receiveQty))-(parseInt(product.saleQty) + parseInt(product.transferQty)+parseInt(product.out_qty))}}
                                    </td>
                            </tr>
                            </template> -->
                        </tbody>
                    </table>
                </div>
                 <!-- <div v-else>
                    <h5 class="text-center m-5">No Minimum Qty Product found!</h5>
                </div> -->
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
                    date:'',
                    category_id: "",
                    product_name: "",
                    brand_id: "",
                    product_code: "",
                },
                products: [],
                op_products: [],
                order_products: [],
                brands: [],
                // warehouses:[],
                categories:[],
                user_year: '',
                user_role: '',
                branches: [],
                site_path: '',
                storage_path: '',
                total_valuation:'',
                total_adj_in: '',
                total_adj_out: '',
                total_after_valuation: ''
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

            //app.initWarehouses();
            app.initBrands();
            app.initCategories();
          
              $("#category_id").on("select2:select", function(e) {

                var data = e.params.data;
                app.search.category_id = data.id;
            });

            $("#brand_id").on("select2:select", function(e) {

                var data = e.params.data;
                app.search.brand_id = data.id;
            });
              $("#date")
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
                app.search.date = moment().format('YYYY-MM-DD');
                var y = new Date().getFullYear();
                if(app.user_year < y) { 
                  if(app.search.date == app.user_year+"-12-31" || app.search.from_date == '') {
                    app.search.date = app.user_year+"-12-31";
                  }
                }
            })
            .on("dp.change", function(e) {
                var formatedValue = e.date.format("YYYY-MM-DD");
                //console.log(formatedValue);
                app.search.date = formatedValue;
            });
        },

        methods: {

            initBranches() {
              axios.get("/branches_byuser").then(({ data }) => (this.branches = data.data));
              $("#branch_id").select2();
            },
            initCategories() {
              axios.get("/categories").then(({ data }) => (this.categories = data.data));
              $("#category_id").select2();
            },
            

            initBrands() {
              axios.get("/report_brands").then(({ data }) => (this.brands = data.data));
              $("#brand_id").select2();
            },
           

            // getSO(product_id) {
            //     let app = this;
            //     //var d=moment("28-02-1999","YYYY-MM-DD");
            //     //d.isSame("28-02-1999");
            //     var qty = 0;
            //     /*$.each(app.order_products, function(index, value) {
            //         if(product_id == value.product_id) {
            //             qty = value.order_qty;
            //         }
            //     });*/
            //     var key = app.order_products.findIndex(x => x.product_id == product_id);
            //     if(key != -1) {
            //         qty = app.order_products[key].order_qty;
            //     }

            //     return qty;
            // },


            getInventoryValuation(page = 1) {

                // if(this.search.from_date == "" || this.search.branch_id == "") {
                //     swal("Warning!", "From Date, and Branch must be added!", "warning")
                //     return false;
                // }

                $("#loading").show();
                let app = this;
                var search =
                 "&date=" +
                    app.search.date +
                    "&category_id=" +
                    app.search.category_id +
                    "&brand_id=" +
                    app.search.brand_id +
                    "&product_code=" +
                    app.search.product_code+
                    "&product_name=" +
                    app.search.product_name;

                axios.get("/report/get_valuation?" + search)
                .then(function(response) {
                    app.products = response.data.data;   
                    app.total_valuation = response.data.total_valuation;
                    app.total_adj_in = response.data.total_adj_in;
                    app.total_adj_out = response.data.total_adj_out;
                    app.total_after_valuation = response.data.total_after_valuation;
                    // response.data.data.forEach(dt => {
                    //     console.log(dt.brand_name); 
                    // })
                   
                    // app.op_products = response.data.op_data;
                    // app.order_products = response.data.order_data;
                    // console.log(app.op_products);
                    // console.log(app.order_products);
                    $("#loading").hide();
                });
            },

            getOpening(id) {
                let app = this;
                var count = 0;
                var in_count = 0;
                var out_count = 0;

                var key = app.op_products.findIndex(x => x.product_id == id);
                if(key != -1) {
                    // alert(app.op_products[key].opening)
                    return app.op_products[key].opening;
                    // in_count = in_count + parseFloat(app.op_products[key].in_qty);
                    // out_count = out_count + parseFloat(app.op_products[key].out_qty);
                }else{
                    return 0;
                }
                /***** for (var key in app.op_products) {
                    if(app.op_products[key].product_id == id) {
                        in_count = in_count +parseFloat(app.op_products[key].in_qty);
                        out_count = out_count + parseFloat(app.op_products[key].out_qty);
                    }
                }*****/

                // count = in_count - out_count;
                // alert(count);
                // return count;

            },

            // exportExcel() {

            //     let app = this;
            //     if(this.search.from_date == "") {
            //         swal("Warning!", "From Date must be added!", "warning")
            //         return false;
            //     }

            //   var search =
            //     "&from_date=" +
            //     app.search.from_date +
            //     "&to_date=" +
            //     app.search.to_date +
            //     "&warehouse_id=" +
            //     app.search.warehouse_id +
            //     "&branch_id=" +
            //     app.search.branch_id +
            //     "&brand_id=" +
            //     app.search.brand_id +
            //     "&product_name=" +
            //     app.search.product_name;

            //     var baseurl = window.location.origin;
            //     //window.open(baseurl+'/inventory_export?'+search);
            //     window.open(this.site_path+'/inventory_export?'+search);
            // },
             //Kamlesh Start
         exportPdf() {   

                let app = this;
                if(this.search.from_date == "") {                  
                    swal("Warning!", "From Date must be added!", "warning")
                    return false;
                } 
                else {
                    $("#loading").show();
                }

                var search =
                 "&date=" +
                    app.search.date +
                    "&category_id=" +
                    app.search.category_id +
                    "&brand_id=" +
                    app.search.brand_id +
                    "&product_code=" +
                    app.search.product_code+
                    "&product_name=" +
                    app.search.product_name;

                    

                axios.get("/report/get_valuation_export_pdf?" + search, {responseType: 'blob'}).then(response => {
                    $('#loading').hide();
                    const url = window.URL.createObjectURL(new Blob([response.data], { type: 'application/pdf' }));
                    window.open(url);

                  })
                  .catch(error => {
                    console.log(error);
                  });

            },
        // Kamlesh End

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

        computed: {
          priceTotal: function(){

            let sum = 0;
            let app = this;
              app.totalcount = 0;
              app.sales.forEach(function(sale) {

                if(app.search.product_name == '') {
                    sale.products.forEach(function(product) {

                      if(product.pivot.is_foc == '0') {
                        sum += (parseFloat(product.pivot.total_amount));
                      }

                    });
                } else {
                    sale.products.forEach(function(product) {

                      if(product.product_name.includes(app.search.product_name)) {
                        if(product.pivot.is_foc == '0') {
                            sum += (parseFloat(product.pivot.total_amount));
                        }
                      }

                    });
                }

              });


           return this.numberWithCommas(sum);
        },
      },


    }
</script>
