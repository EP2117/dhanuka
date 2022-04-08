<template>
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="#!"><i class="feather icon-home"></i></a></li>
                <li class="breadcrumb-item"><a :href="site_path+'/'">Home</a></li>
                <li class="breadcrumb-item"><a :href="site_path+'/report'">Report</a></li>
                <li class="breadcrumb-item active" aria-current="page">Inventory Report</li>
            </ol>
        </nav>
        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h4 class="mb-0 text-gray-800">Inventory Report</h4>
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

                    <div class="form-group col-md-4 col-lg-3 mm-txt">
                        <label for="branch_id">Branch</label>
                        <select id="branch_id" class="form-control mm-txt"
                            name="branch_id" v-model="search.branch_id" style="width:100%"
                        >
                            <option value="">Select One</option>
                            <option v-for="branch in branches" :value="branch.id"  :key="branch.id">{{branch.branch_name}}</option>
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

                     <div class="form-group col-md-4 col-lg-3">
                        <label for="product_name">Product Name</label>
                        <input type="text" class="form-control" id="product_name" name="product_name"
                        v-model="search.product_name">
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

                    <div class="form-group col-md-3 col-lg-2">
                        <label class="small" for="search">&nbsp;</label>
                        <button
                          class="form-control btn btn-primary font-weight-bold"
                          @click="getInventoryRpt(1)"
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

                <div class="text-right mb-2">
                    <button class="btn btn-primary btn-icon btn-sm" @click="exportExcel()"><i class="fas fa-file-excel"></i> &nbsp;Export to Excel</button>
                </div>

               <div class="table-responsive">
                    <table class="table table-bordered table-striped table_num" id="dataTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th class="text-center">No.</th>
                                <th class="text-center">Brand</th>
                                <th class="text-center">Internal Category</th>
                                <th class="text-center">Product Code</th>
                                <th class="text-center">Product Name</th>
                                <th class="text-center">Warehouse UOM</th>
                                <!--<th class="text-center">Photo</th>-->
                                <th class="text-center">Opening</th>
                                <th class="text-center">In</th>
                                <th class="text-center">Stock <br />Receive</th>
                                <th class="text-center">Stock <br />Transfer</th>
<!--                                <th class="text-center">Sale Order</th>-->
<!--                                <th class="text-center">Approval <br />QTY</th>-->
<!--                                <th class="text-center">Revise <br />QTY</th>-->
<!--                                <th class="text-center">Revise Sale</th>-->
<!--                                <th class="text-center">No Revise Sale</th>-->
                                <th class="text-center">Direct Sale</th>
<!--                                <th class="text-center">Reserve <br />Balance</th>-->
                                <th class="text-center">Balance</th>
                            </tr>
                        </thead>
                        <tbody>
                            <template v-if="user_role == 'Country Head'">
                                <template v-for="product in products">
                                    <template>
                                  <td style="display:none">{{p_add_qty=product.add_qty ==null ? 0 : parseInt(product.add_qty)}}</td>
                                  <td style="display:none">{{p_out_qty=product.out_qty ==null ? 0 : parseInt(product.out_qty)}}</td>
                                </template>
                                <tr v-if="brands.findIndex(x => x.id == product.brand_id) > -1">
                                    <td></td>
                                    <td>{{product.brand_name}}</td>
                                    <td>{{product.category_name}}</td>
                                    <td>{{product.product_code}}</td>
                                    <td>{{product.product_name}}</td>
                                    <td>{{product.uom_name}}</td>

                                    <!--<td v-if="product.photo_ids != null && product.photo_ids != ''">
                                            <template v-for="p,i in product.photo_ids.split(',')">
                                                <a href="#" @click="showPhoto(p)">Photo{{i+1}}</a><span v-if="i != product.photo_ids.split(',').length-1">, </span>
                                            </template>
                                    </td>
                                    <td v-else></td>-->

                                    <td>{{product.product_opening = getOpening(product.product_id)}}</td>
                                    <td>{{ product.inQty=product.in_qty==null? 0 :parseInt(product.in_qty)}}</td>
<!--                                    <td>{{// product.inQty = product.in_qty == null ? '0' : product.in_qty}}</td>-->
                                    <td>{{product.receiveQty = product.receive_qty == null ? '0' : product.receive_qty}}</td>
                                    <td>{{product.transferQty = product.transfer_qty == null ? '0' : product.transfer_qty}}</td>
<!--                                    <td>{{product.saleOrder = getSO(product.product_id)}}</td>-->
<!--                                    <td>{{product.approvalQty = product.approval_qty == null ? '0' : product.approval_qty}}</td>-->
<!--                                    <td>{{product.reviseQty = product.revise_qty == null ? '0' : product.revise_qty}}</td>-->
<!--                                    <td>{{product.reviseSaleQty = product.revise_sale_qty == null ? '0' : product.revise_sale_qty}}</td>-->
<!--                                    <td>{{product.approvalSaleQty = product.approval_sale_qty == null ? '0' : product.approval_sale_qty}}</td>-->
                                    <td>{{product.saleQty = product.sale_qty == null ? '0' : product.sale_qty}}</td>
<!--                                    <td>{{(parseFloat(product.product_opening) + parseFloat(product.inQty)+parseInt(product.in_purchase_qty)+parseFloat(product.product_opening) + parseFloat(product.receiveQty) ) - (parseFloat(product.saleQty)  + parseFloat(product.transferQty))}}</td>-->
                                    <td>
                                        <!-- 1{{(parseFloat(product.product_opening) + p_add_qty+ parseFloat(product.inQty)+ parseFloat(product.receiveQty) )-(parseFloat(product.saleQty) + parseFloat(product.transferQty)+p_out_qty)}} 
                                        2{{(parseFloat(product.product_opening) + parseFloat(product.inQty)+ parseFloat(product.receiveQty) )-(parseFloat(product.saleQty) + parseFloat(product.transferQty))}}-->

                                        <!-- ep code -->
                                        {{(parseFloat(product.product_opening) + parseFloat(product.inQty)+ parseFloat(product.receiveQty) )-parseFloat(product.out_qty)}}

                                    </td>
                                </tr>
                                </template>
                            </template>
                            <template v-else>
                            <tr v-for="product in products">
                                <template>
                                  <td style="display:none">{{p_add_qty=product.add_qty ==null ? 0 : parseInt(product.add_qty)}}</td>
                                  <td style="display:none">{{p_out_qty=product.out_qty ==null ? 0 : parseInt(product.out_qty)}}</td>
                                   <td style="display:none">{{p_adjust_out_qty=product.adjust_out_qty ==null ? 0 : parseInt(product.adjust_out_qty)}}</td>
                                </template>
                                <td></td>
                                    <td>{{product.brand_name}}</td>
                                    <td>{{product.category_name}}</td>
                                    <td>{{product.product_code}}</td>
                                    <td>{{product.product_name}}</td>
                                    <td>{{product.uom_name}}</td>

                                     <!--<td v-if="product.photo_ids != null && product.photo_ids != ''">
                                            <template v-for="p,i in product.photo_ids.split(',')">
                                                <a href="#" @click="showPhoto(p)">Photo{{i+1}}</a><span v-if="i != product.photo_ids.split(',').length-1">, </span>
                                            </template>
                                    </td>
                                    <td v-else></td>-->

                                    <td>{{product.product_opening = getOpening(product.product_id)}}</td>
                                <td>{{ product.inQty=product.in_qty==null? 0 :parseInt(product.in_qty)}}</td>

                                <!--                                    <td>{{product.inQty = product.in_qty == null ? '0' : product.in_qty}}</td>-->
                                    <td>{{product.receiveQty = product.receive_qty == null ? '0' : product.receive_qty}}</td>
                                    <td>{{product.transferQty = product.transfer_qty == null ? '0' : product.transfer_qty}}</td>
<!--                                    <td>{{product.saleOrder = getSO(product.product_id)}}</td>-->
<!--                                    <td>{{product.approvalQty = product.approval_qty == null ? '0' : product.approval_qty}}</td>-->
<!--                                    <td>{{product.reviseQty = product.revise_qty == null ? '0' : product.revise_qty}}</td>-->
<!--                                    <td>{{product.reviseSaleQty = product.revise_sale_qty == null ? '0' : product.revise_sale_qty}}</td>-->
<!--                                    <td>{{product.approvalSaleQty = product.approval_sale_qty == null ? '0' : product.approval_sale_qty}}</td>-->
                                    <td>{{product.saleQty = product.sale_qty == null ? '0' : product.sale_qty}}</td>
<!--                                    <td>{{(parseFloat(product.product_opening) + parseFloat(product.inQty) + parseFloat(product.receiveQty) + parseFloat(product.reviseQty)) - (parseFloat(product.saleQty) + parseFloat(product.saleOrder) + parseFloat(product.reviseSaleQty) + parseFloat(product.transferQty))}}</td>-->

                                    <td>
                                        <!--2{{(parseFloat(product.product_opening) + parseFloat(product.inQty)+ parseFloat(product.receiveQty) )-(parseFloat(product.saleQty) + parseFloat(product.transferQty))}}
                                        1{{(parseFloat(product.product_opening) + parseFloat(product.in_qty) + parseFloat(product.receiveQty))-(parseFloat(product.saleQty)+ parseFloat(product.transferQty)+parseInt(product.out_qty))}} -->

                                        <!-- ep code -->
                                        {{(parseFloat(product.product_opening) + parseFloat(product.inQty)+ parseFloat(product.receiveQty) )-parseFloat(product.out_qty)}}
                                    </td>
                            </tr>
                            </template>
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
                    warehouse_id: "",
                    product_name: "",
                    brand_id: "",
                    branch_id: "",
                },
                products: [],
                op_products: [],
                order_products: [],
                brands: [],
                def_branch_id:'',
                warehouses:[],
                user_year: '',
                user_role: '',
                branches: [],
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

            app.initBrands();
            app.initBranches();
            app.initWarehouses();

            // console.log(this.search.branch_id);
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
            // $(document).ready(function(e){
            //     var branch_id=$('#branch_id').val();
            //     if(branch_id) {
            //         axios.get("warehouses_bybranch/"+ branch_id).then(
            //             // ({ data }) => (app.warehouses = data.data));
            //             (data)=>{
            //                 this.warehouses=data.data.data;
            //                 const id=this.warehouses.map(a=>a.id);
            //                 console.log('id is '+id[0]);
            //                 app.search.warehouse_id=id[0];
            //                 // console.log(app.search.warehouse_id);
            //                 $('#warehouse_id').val(id[0]).trigger('change');
            //             }
            //         );
            //     } else {
            //         alert('a');
            //         axios.get("warehouses_bybranch/null").then(({ data }) => (app.warehouses = data.data));
            //     }
            // });
            // $(document).on("select2:select",'#branch_id', function(e) {
            //     var data=e.params.data;
            //     this.search.branch_id=data.id;
            //     app.warehouses = [];
                // if(data.id != "") {
                //     axios.get("warehouses_bybranch/"+ data.id).then(({ data }) => (app.warehouses = data.data));
                // } else {
                //     axios.get("warehouses_bybranch/null").then(({ data }) => (app.warehouses = data.data));
                // }
            // });

            $("#warehouse_id").on("select2:select", function(e) {

                var data = e.params.data;
                app.search.warehouse_id = data.id;
            });

            $("#brand_id").on("select2:select", function(e) {

                var data = e.params.data;
                app.search.brand_id = data.id;
            });
        },

        methods: {

            showPhoto(p) {

                let app = this;
                $("#loading").show();
                axios.get("/product_photo/"+p).then(function (response){
                    $("#loading").hide();
                    var photo = response.data.photo;
                    var wi = window.open();
                    var html = "<div style='text-align:center'><img style='max-width:500px' src='"+photo+"' /></div>";
                    $(wi.document.body).html(html);
                });
            },

            initBranches() {
            let app = this;
              axios.get("/branches_byuser").then(
                  (data)=>{
                    app.branches = data.data.data;
                     const branch=app.branches.find(a=>a.branch_name=='Main Branch');
                      app.search.branch_id=branch.id;
                      app.def_branch_id=branch.id;
                      $('branch_id').val(branch.id).trigger('change');
                      $("#branch_id").select2();
                  });
            },
            initWarehouses() {
                let app = this;
                // console.log(this.search.branch_id);
              axios.get("/warehouses").then(
                //   ({ data }) => (this.warehouses = data.data)
                (data)=>{
                    app.warehouses=data.data.data;
                     const id=app.warehouses.find(a=>a.branch.branch_name=='Main Branch');
                     app.search.warehouse_id=id.id;
                      $('#warehouse_id').val(id.id).trigger('change');
                      $("#warehouse_id").select2();
                }
                  );
            //   $("#warehouse_id").select2();
            },
            initBrands() {
              axios.get("/report_brands").then(({ data }) => (this.brands = data.data));
              $("#brand_id").select2();
            },

            getSO(product_id) {
                let app = this;
                //var d=moment("28-02-1999","YYYY-MM-DD");
                //d.isSame("28-02-1999");
                var qty = 0;
                /*$.each(app.order_products, function(index, value) {
                    if(product_id == value.product_id) {
                        qty = value.order_qty;
                    }
                });*/
                var key = app.order_products.findIndex(x => x.product_id == product_id);
                if(key != -1) {
                    qty = app.order_products[key].order_qty;
                }

                return qty;
            },

            getInventoryRpt(page = 1) {

                if(this.search.from_date == "" || this.search.branch_id == "") {
                    swal("Warning!", "From Date, and Branch must be added!", "warning")
                    return false;
                }

                $("#loading").show();
                let app = this;



                var search =
                    "&from_date=" +
                    app.search.from_date +
                    "&to_date=" +
                    app.search.to_date +
                    "&warehouse_id=" +
                    app.search.warehouse_id +
                    "&branch_id=" +
                    app.search.branch_id +
                    "&brand_id=" +
                    app.search.brand_id +
                    "&product_name=" +
                    app.search.product_name;

                axios.get("/inventory_report?" + search)
                .then(function(response) {
                    app.products = response.data.data;
                    app.op_products = response.data.op_data;
                    app.order_products = response.data.order_data;
                    console.log(app.op_products);
                    console.log(app.order_products);
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
                    in_count = in_count + parseFloat(app.op_products[key].in_qty);
                    out_count = out_count + parseFloat(app.op_products[key].out_qty);
                }else{
                    return 0;
                }
                /***** for (var key in app.op_products) {
                    if(app.op_products[key].product_id == id) {
                        in_count = in_count +parseFloat(app.op_products[key].in_qty);
                        out_count = out_count + parseFloat(app.op_products[key].out_qty);
                    }
                }*****/

                count = in_count - out_count;
                // alert(count);

                return count;

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
                "&warehouse_id=" +
                app.search.warehouse_id +
                "&branch_id=" +
                app.search.branch_id +
                "&brand_id=" +
                app.search.brand_id +
                "&product_name=" +
                app.search.product_name;

                var baseurl = window.location.origin;
                //window.open(baseurl+'/inventory_export?'+search);
                window.open(this.site_path+'/inventory_export?'+search);
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
