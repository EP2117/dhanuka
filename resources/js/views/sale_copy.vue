<template>
    <div>
        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h4 class="mb-0 text-gray-800">Sale Invoice Form</h4>
        </div>

        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Entry Details</h6>
            </div>
            <div class="card-body">
                <div class="d-block">
                    <!-- form start -->
                    <form class="form" method="post" @submit.prevent="onSubmit" @keydown="form.errors.clear($event.target.name)">
                        <div class="row">
                            <div class="form-group col-md-4">
                                <label for="invoice_no">Invoice No.</label>
                                <input type="text" class="form-control" id="invoice_no" name="invoice_no" v-model="form.invoice_no" readonly>
                            </div>
                            <div class="form-group col-md-4">
                                <label for="invoice_date">Date</label>
                                <input type="text" class="form-control datetimepicker" id="invoice_date" name="invoice_date"
                                v-model="form.invoice_date" required>
                            </div>
                        </div>

                        <div class="row mt-3">
                             <div class="form-group col-md-4">
                                <label for="customer_id">Customer</label>
                                <select id="customer_id" class="form-control"
                                    name="customer_id" v-model="form.customer_id" style="width:100%" required
                                >
                                    <option value="">Select One</option>
                                    <option v-for="cus in customers" :value="cus.id"  >{{cus.cus_name}}</option>
                                </select>
                            </div>
                            <div class="form-group col-md-4">
                                <label for="vehicle_warehouse">Vehicle Warehouse</label>
                                 <input type="text" class="form-control" id="vehicle_warehouse" name="vehicle_warehouse"
                                v-model="user_warehouse" readonly>
                            </div>

                            <div class="form-group col-md-4" v-if="sale_type == 1">
                                <label for="reference_no">Reference No.</label>
                                 <input type="text" class="form-control" id="reference_no" name="reference_no"
                                v-model="form.reference_no">
                            </div>
                        </div>

                        <div class="row mt-3" v-if="sale_type == 1">
                            <div class="col-md-12 custom-control custom-switch" style="padding-left:10px;">
                              <label style='margin-right:50px;' class="ml-0">Sale Order</label>
                              <input type="checkbox" class="custom-control-input" id="is_so" name="is_so" @change="checkSO($event.target)">                              
                              <label class="custom-control-label" for="is_so"></label>
                            </div>                        
                        </div>

                        <div class="row mt-3" v-else style="display:none">
                            <div class="col-md-12 custom-control custom-switch" style="padding-left:10px;">
                              <label style='margin-right:50px;' class="ml-0">Sale Order</label>
                              <input type="checkbox" class="custom-control-input" id="is_so" name="is_so" @change="checkSO($event.target)">                              
                              <label class="custom-control-label" for="is_so"></label>
                            </div>                        
                        </div>

                        <div class="row mt-3" style="display:none" id="so_list">
                             <div class="form-group col-md-4">
                                <label for="sale_order">Sale Order Approval</label>
                                <select id="sale_order" class="form-control"
                                    name="sale_order" style="width:100%"
                                >
                                    <option value="">Select Sale Order</option>
                                    <option v-for="so in sale_orders" :value="so.id"  >{{so.order_no}}</option>
                                </select>
                            </div>
                            <div class="form-group col-md-4">
                                <label for="sale_order">&nbsp;</label>
                                <select id="sale_order_approval" class="form-control"
                                    name="sale_order_approval" style="width:100%"
                                >
                                    <option value="">Select Approval</option>
                                    <option v-for="approval in sale_order_approvals" :value="approval.id"  >{{approval.approval_no}}</option>
                                </select>        
                            </div>
                           
                        </div>

                        <div class="row mt-4 mb-3">
                            <div class="col-md-12">
                                <span class="d-none d-sm-inline-block btn-sm btn-primary shadow-sm bg-blue"><i class="fas fa-search-plus text-white"></i> Product Details</span>
                            </div>
                        </div>

                        <div class="row mt-0" v-if="form.sale_order == false || isEdit">
                            <div class="col-md-12 text-right mt-0">
                                <a class='blue-icon' title='Add Product' @click="addProduct()" v-if="user_role != 'admin' && form.sale_order == false"><i class='fas fa-plus-square' style='font-size: 30px;'></i></a>
                                <div style="display:none;">
                                    <select class="form-control txt_product"
                                        id="txt_product" style="min-width:150px;"
                                    >
                                        <option value="">Select One</option>
                                        <option v-for="product in products" :data-uom="product.uom_name" 
                                        :data-price="product.product_price"
                                        :data-uomid="product.uom_id" :value="product.product_id" 
                                        data-pivotid = "0">{{product.product_name}}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-12 table-responsive">
                                <table class="table table-bordered" id="product_table">
                                    <thead class="thead-grey">
                                        <tr>
                                            <th scope="col" >Product Name</th>
                                            <th scope="col" >Quantity</th>
                                            <th scope="col" >Selling Unit</th>
                                            <th scope="col" >Relation</th>
                                            <th scope="col" >FOC</th>
                                            <th scope="col" v-if="sale_type == 2">Unit Price</th>
                                            <th scope="col" v-else>Per Warehouse UOM <br />Unit Price</th>
                                            <th scope="col" >Total Amount</th>
                                            <th scope="col" class="text-center"></th>
                                        </tr>
                                    </thead>
                                    <tbody> 
                                        <template v-if="isEdit && ex_products.length > 0">
                                        </template>
                                        <template v-else>                                      
                                        <tr>
                                            <td>
                                                <select class="form-control txt_product"
                                                    name="product[]" style="min-width:150px;" required
                                                >
                                                    <option value="">Select One</option>
                                                    <option v-for="product in products" :data-uom="product.uom_name" 
                                                    :data-price="product.product_price"
                                                    :data-uomid="product.uom_id" :value="product.product_id" 
                                                    data-pivotid = "0">{{product.product_name}}</option>
                                                </select>
                                            </td>                                                
                                            <td>
                                                <input type="text" class="form-control num_txt txt_qty" style="width:100px;" name="qty[]" @blur="checkQty($event.target)" required />
                                            </td>
                                            <td>
                                                <select class="form-control txt_uom"
                                                    name="uom[]" style="min-width:150px;" data-uom="" required
                                                >
                                                    <option value="">Select One</option>
                                                </select>
                                            </td>
                                            <td>
                                                <input type="text" style="min-width:200px;" class="form-control txt_relation" name="relation[]" readonly />
                                            </td>
                                            <td class="text-center">
                                                <input
                                                    type="checkbox"
                                                    name="chk_foc[]"
                                                    @change="checkFoc($event.target)"
                                                >
                                            </td>
                                            <td>
                                                <input type="text" class="form-control num_txt" style="width:100px;" name="unit_price[]" @blur="calTotalAmount($event.target)" required />
                                            </td>
                                            <td>
                                                <input type="text" class="form-control num_txt" readonly style="width:100px;" name="total_amount[]" required />
                                            </td>
                                            <td class="text-center">
                                                <a class='remove-row red-icon' title='Remove' v-if="user_role != 'admin'"><i class='fas fa-times-circle' style='font-size: 25px;'></i></a>
                                            </td>
                                        </tr>
                                        </template>
                                        <tr class="total_row">
                                            <td colspan="6" class="text-right">Sub Total</td>
                                            <td colspan="2">
                                                <input type="text" v-model="form.sub_total" class="form-control num_txt" readonly style="width:150px;" required />
                                            </td>
                                        </tr>                                        
                                        <tr class="total_row">
                                            <td colspan="6" class="text-right">Pay Amount</td>
                                            <td colspan="2">
                                                <input type="text" v-model="form.pay_amount" class="form-control num_txt" style="width:150px;" @keyup="calBalance($event.target)" readonly />
                                            </td>
                                        </tr>
                                        <tr class="total_row">
                                            <td colspan="6" class="text-right">Balance</td>
                                            <td colspan="2">
                                                <input type="text" v-model="form.balance_amount" class="form-control num_txt" readonly style="width:150px;" required />
                                            </td>
                                        </tr>                                        
                                    </tbody>
                                </table>


                            </div>                         

                        </div>

                        <!-- for sale order product -->
                        <div class="row mt-4" v-else>
                            <div class="col-md-12 table-responsive">
                                <table class="table table-bordered" id="order_product_table">
                                    <thead class="thead-grey">
                                        <tr>
                                            <th scope="col" >Product Name</th>
                                            <th scope="col" >Quantity</th>
                                            <th scope="col" >Selling Unit</th>
                                            <th scope="col" >Relation</th>
                                            <th scope="col" >FOC</th>
                                            <th scope="col" v-if="sale_type == 2">Unit Price</th>
                                            <th scope="col" v-else>Per Warehouse UOM <br />Unit Price</th>
                                            <th scope="col" >Total Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr class="total_row">
                                            <td colspan="6" class="text-right">Sub Total</td>
                                            <td colspan="2">
                                                <input type="text" v-model="form.sub_total" class="form-control num_txt" readonly style="width:150px;" required />
                                            </td>
                                        </tr>                                        
                                        <tr class="total_row">
                                            <td colspan="6" class="text-right">Pay Amount</td>
                                            <td colspan="2">
                                                <input type="text" v-model="form.pay_amount" class="form-control num_txt" style="width:150px;" @keyup="calBalance($event.target)" readonly />
                                            </td>
                                        </tr>
                                        <tr class="total_row">
                                            <td colspan="6" class="text-right">Balance</td>
                                            <td colspan="2">
                                                <input type="text" v-model="form.balance_amount" class="form-control num_txt" readonly style="width:150px;" required />
                                            </td>
                                        </tr>
                                    </tbody>
                                    </thead>
                                </table>
                            </div>
                        </div>
                        <!-- end -->

                        <div class="row">
                            <div class="col-md-12" v-if="user_role != 'admin'">
                                <input type="submit" class="btn btn-primary btn-sm" value="Save Entry" id="save_btn">
                            </div>
                        </div>

                    </form>                    
                <!-- form end -->  
                </div>

            </div>
        </div>
        <div id="loading"></div>
    </div>

</template>

<script>
    export default {
        data() {
            return {
              form: new Form({
                invoice_date: "",
                invoice_no: "",
                vehicle_warehouse: "",
                customer_id: "",
                product: [],
                uom: [],
                qty: [],
                unit_price: [],
                total_amount: [],
                sub_total: 0,
                pay_amount: 0,
                balance_amount:0,
                ex_product_pivot: [],
                product_pivot: [],
                is_foc: [],
                uom_id: "",
                price_variants: [],
                sale_type: '',
                sale_order: false,
                order_id: '',
                approval_id: '',
                reference_no: '',

              }),
              isEdit: false,
              products: [],
              sale_id: '',
              ex_products: [],
              user_warehouse: '',
              selling_uoms: [],
              customers: [],
              sale_type: '',
              user_role: '',
              sale_orders: [],
              sale_order_approvals: [],

            };
        },

        created() {
            //sale_type = 2 for Van and 1 for Office Sale

            this.sale_type = this.$route.params.sale_type;
            this.user_warehouse = document.querySelector("meta[name='user-wh']").getAttribute('content');

            this.user_role = document.querySelector("meta[name='user-role']").getAttribute('content');
            if(this.user_role == "office_order_user") {
                var url =  window.location.origin;
                window.location.replace(url);
            }

            if(this.$route.params.id) {
                this.isEdit = true;
                this.sale_id = this.$route.params.id;
                this.getSale(this.sale_id);
            } else {
                //this.getMaxId();
            };

            this.form.invoice_date = moment().format("YYYY-MM-DD");
        },
        mounted() {

            $("#loading").hide();
            let app = this;

            app.initProducts();
           // app.initWarehouses();

            app.initCustomers();

            $(".txt_product").select2();

            $("#sale_order").select2();
            $("#sale_order").on("select2:select", function(e) {  
                app.sale_order_approvals = [];
                $('#order_product_table tbody tr').slice(0, -3).remove();       
                var data = e.params.data;
                axios.get("/sale_order_approval/"+ data.id).then(({ data }) => (app.sale_order_approvals = data.data));
            });

            $("#sale_order_approval").select2();
            $("#sale_order_approval").on("select2:select", function(e) {            
                var data = e.params.data;
                app.getApproval(data.id);
            });

            $("#customer_id").select2();
            $("#customer_id").on("select2:select", function(e) {

                var data = e.params.data;
                app.form.customer_id = data.id;

                app.sale_orders = [];  
                app.sale_order_approvals = [];
                $('#order_product_table tbody tr').slice(0, -3).remove();
                axios.get("/customer_sale_orders/"+data.id).then(({ data }) => (app.sale_orders = data.data));
                $("#sale_order").select2();;
            });

            $(".txt_product").on("select2:select", function(e) {

                var data = e.params.data;

                app.selling_uoms = [];

               var uom      = e.target.options[e.target.options.selectedIndex].dataset.uom;
               var uom_id   = e.target.options[e.target.options.selectedIndex].dataset.uomid;
               var price    = e.target.options[e.target.options.selectedIndex].dataset.price;

                //$(this).closest('td').next().find('.txt_uom').val(uom);
                //$(this).closest('td').next().find('.txt_uom').attr('data-id',uom_id);

                $(this).closest('td').next().next().find('.txt_uom').attr('data-uom',uom);

                //auto add new product row
                if($(this).closest('tr').next().hasClass("total_row")) {
                    app.addProduct();
                }

               //add Warehouse UOM Selling Price
                $(this).closest('td').next().next().next().next().next().find('input').val(price);

                var selectbox_id = $(this).closest('td').next().next().find('.txt_uom');

                selectbox_id.find('option').remove();

                //add pre-defined product uom 

                if(selectbox_id.find('option[value="'+uom_id+'"]').text() == "") {
                    selectbox_id.append('<option value="">Select One</option><option value="'+uom_id+'" data-relation="" data-uomqty = "1" data-productuom = "'+uom+'" data-productid="'+data.id+'" data-perprice="'+price+'" data-price="'+price+'" selected>'+uom+'</option>'); 
                }
                $(".txt_uom").select2();

                app.getSellingUomByProduct(selectbox_id, data.id);
            });

            $(".txt_uom").select2();

            $(".txt_uom").on("select2:select", function(e) {
                app.checkQty(e.target.options[e.target.options.selectedIndex]);
                var uom_relation = e.target.options[e.target.options.selectedIndex].dataset.relation;
                if(app.sale_type == 2) {
                //for van sale
                    var uom_price = e.target.options[e.target.options.selectedIndex].dataset.price;
                } else {
                //for office sale
                    var uom_price = e.target.options[e.target.options.selectedIndex].dataset.perprice;
                }

                $(this).closest('td').next().find('.txt_relation').val(uom_relation);
                $(this).closest('td').next().next().next().find('input').val(uom_price);

                app.calTotalAmount($(this).closest('td').next().next().next().find('input'));
            });

            $(".datetimepicker")
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
                })
                .on("dp.change", function(e) {
                    var formatedValue = e.date.format("YYYY-MM-DD");
                    //console.log(formatedValue);
                    app.form.invoice_date = formatedValue;
                });

            /*$(document).on('click','.add-new',function(evt){
                app.addProduct();
            });*/                


            $(document).on('click','.remove-row',function(evt) {
                if(document.getElementsByName('product[]').length <= 1) {                    
                    swal("Warning!", "At least one product must be added!", "warning")
                } else {
                    $(this).parents("tr").remove();
                    var sub_total = 0; 
                    for(var i=0; i<document.getElementsByName('product[]').length; i++) {
                        if(document.getElementsByName('total_amount[]')[i].value != "") {
                            sub_total += parseInt(document.getElementsByName('total_amount[]')[i].value);
                        }
                    }

                    app.form.sub_total = sub_total;
                    app.form.balance_amount  = sub_total - parseInt(app.form.pay_amount);   
                }
            });

            $(document).on('click','.remove-exrow',function(evt) {
                swal({
                    title: "Are you sure?",
                    text: "Once deleted, you will not be able to recover!",
                    icon: "warning",
                    buttons: true,
                    dangerMode: true
                    }).then(willDelete => {
                    if (willDelete) {
                       if(document.getElementsByName('product[]').length <= 1) {                    
                            swal("Warning!", "At least one product must be added!", "warning")
                        } else {
                            $(this).parents("tr").remove();
                            var sub_total = 0; 
                            for(var i=0; i<document.getElementsByName('product[]').length; i++) {
                                if(document.getElementsByName('total_amount[]')[i].value != "") {
                                    sub_total += parseInt(document.getElementsByName('total_amount[]')[i].value);
                                }
                            }

                            app.form.sub_total = sub_total;
                            app.form.balance_amount  = sub_total - parseInt(app.form.pay_amount);  
                        }   
                    } else {
                      //
                    }
                });
            });
           
        },

        methods: {
            initProducts() {
              axios.get("/productsByUserWarehouse").then(({ data }) => (this.products = data.data));
              $(".txt_product").select2();
            },

            initWarehouses() {
              axios.get("/warehouses").then(({ data }) => (this.warehouses = data.data));
              $("#to_warehouse").select2();
            },

            initCustomers() {
              axios.get("/customers").then(({ data }) => (this.customers = data.data));
              $("#customer_id").select2();
            },

            initSaleOrders() {
              axios.get("/sale_orders").then(({ data }) => (this.sale_orders = data.data));
              $("#sale_order").select2();
            },

            getSellingUomByProduct(selectbox_id,product_id) {
                let app = this;
              axios.get("/product/selling_uom/"+ product_id).then(function(response) {
                var uom_arr = response.data.s_uoms.selling_uoms;
                var uom_relation = "";
                $.each(uom_arr, function(index, value) {
                    uom_relation = "1 "+value.uom_name+" = "+ value.pivot.relation +" "+ selectbox_id.attr('data-uom');
                    if(selectbox_id.find('option[value="'+value.pivot.uom_id+'"]').text() == "") {
                        selectbox_id.append('<option value="'+value.pivot.uom_id+'" data-relation="'+uom_relation+'" data-uomqty="'+value.pivot.relation+'" data-productuom = "'+selectbox_id.attr('data-uom')+'" data-productid="'+product_id+'" data-perprice="'+value.pivot.per_warehouse_uom_price+'" data-price="'+value.pivot.product_price+'" >'+value.uom_name+'</option>');
                    }
                });

                $(".txt_uom").select2();
                  
              });
            },

            getMaxId() {
                let app = this;
                axios.get("/sale/maxid").then(function(response) {
                    var maxid = (response.data.max_id + 1).toString();
                    app.form.invoice_no = maxid.padStart(5, "0");
                });
            },

            addProduct() {
                let app = this;
                var table=document.getElementById("product_table");
                var row=table.insertRow((table.rows.length)-3);
                var cell1=row.insertCell(0);

                var t1=document.createElement("select");
                    t1.name = "product[]";
                    t1.className = "form-control txt_product";
                    t1.style = "min-width:150px;";
                    $(t1).attr("required", true);

                    var option = document.createElement("option");
                    option.value = '';
                    option.text = "Select One";
                    t1.append(option);

                    /*$.each(this.products, function(index, value) {
                       var option = document.createElement("option");
                       option.value = value.product_id;
                       $(option).attr('data-uom',value.uom_name);
                       $(option).attr('data-uomid',value.uom_id);
                       $(option).attr('data-price',value.product_price);
                       $(option).attr('data-pivotid','0');
                       option.text = value.product_name;
                       t1.append(option);
                     }); */

                    var html = $('#txt_product').html();
                    $(t1).html(html);                    
                    cell1.appendChild(t1);

                var cell2=row.insertCell(1);
                var t2=document.createElement("input");
                    t2.name = "qty[]";
                    t2.style = "width:100px;";
                    t2.className ="form-control num_txt";
                    $(t2).attr("required", true);
                    t2.addEventListener('blur', function(){ app.checkQty(t2); });
                    cell2.appendChild(t2);
                   
                var cell3=row.insertCell(2);

                var t3=document.createElement("select");
                    t3.name = "uom[]";
                    t3.className = "form_control txt_uom";
                    t3.style = "min-width:150px;";
                    $(t3).attr("required", true);
                    t3.addEventListener('blur', function(){ app.checkQty(t3); });

                    var option = document.createElement("option");
                    option.value = '';
                    option.text = "Select One";
                    $(option).attr('data-relation',"");
                    $(option).attr('data-price', "");
                    $(option).attr('data-perprice', "");
                    t3.append(option);

                 cell3.appendChild(t3);


                var cell4=row.insertCell(3);
                var t4=document.createElement("input");
                    t4.type = "text";
                    t4.name = "relation[]";
                    t4.style = "min-width:200px;";
                    t4.className ="form-control txt_relation";
                    $(t4).attr('readonly', true);
                    cell4.appendChild(t4);
                

                $(".txt_product").select2();

                $(".txt_uom").select2();

                $("#customer_id").select2();
                $("#customer_id").on("select2:select", function(e) {

                    var data = e.params.data;
                    app.form.customer_id = data.id;
                });

                $(".txt_product").on("select2:select", function(e) {
                    var data = e.params.data;

                    app.selling_uoms = [];

                   var uom = e.target.options[e.target.options.selectedIndex].dataset.uom;
                   var uom_id = e.target.options[e.target.options.selectedIndex].dataset.uomid;
                   var price = e.target.options[e.target.options.selectedIndex].dataset.price;

                    //$(this).closest('td').next().find('.txt_uom').val(uom);
                    //$(this).closest('td').next().find('.txt_uom').attr('data-id',uom_id);

                    $(this).closest('td').next().next().find('.txt_uom').attr('data-uom',uom);

                    //auto add new product row
                    if($(this).closest('tr').next().hasClass("total_row")) {
                        app.addProduct();
                    }

                    //add Warehouse UOM Selling Price
                    $(this).closest('td').next().next().next().next().next().find('input').val(price);
                    

                    var selectbox_id = $(this).closest('td').next().next().find('.txt_uom');

                    selectbox_id.find('option').remove();

                    //add pre-defined product uom 
                    if(selectbox_id.find('option[value="'+uom_id+'"]').text() == "") {
                        selectbox_id.append('<option value="">Select One</option><option value="'+uom_id+'" data-relation="" data-uomqty="1" data-productuom = "'+uom+'" data-productid="'+data.id+'" data-price="'+price+'" data-perprice="'+price+'" selected>'+uom+'</option>'); 
                    }
                    $(".txt_uom").select2();

                    app.getSellingUomByProduct(selectbox_id, data.id);
                });

                $(".txt_uom").select2();

                $(".txt_uom").on("select2:select", function(e) {
                    app.checkQty(e.target.options[e.target.options.selectedIndex]);
                    var uom_relation = e.target.options[e.target.options.selectedIndex].dataset.relation;

                    if(app.sale_type == 2) {
                    //for van sale
                        var uom_price = e.target.options[e.target.options.selectedIndex].dataset.price;
                    } else {
                    //for office sale
                        var uom_price = e.target.options[e.target.options.selectedIndex].dataset.perprice;
                    }

                    $(this).closest('td').next().find('.txt_relation').val(uom_relation);
                    $(this).closest('td').next().next().next().find('input').val(uom_price);

                    app.calTotalAmount($(this).closest('td').next().next().next().find('input'));
                });

                var cell5=row.insertCell(4);
                    cell5.className = "text-center";
                var t5=document.createElement("input");
                    t5.type = "checkbox";
                    t5.name = "chk_foc[]";
                    t5.addEventListener('change', function(){ app.checkFoc(t5); });
                    cell5.appendChild(t5);

                var cell6=row.insertCell(5);
                var t6=document.createElement("input");
                    t6.name = "unit_price[]";
                    t6.style = "width:100px;";
                    t6.className ="form-control num_txt";
                    $(t6).attr("required", true);
                    t6.addEventListener('blur', function(){ app.calTotalAmount(t6); });
                    cell6.appendChild(t6);

                var cell7=row.insertCell(6);
                var t7=document.createElement("input");
                    t7.name = "total_amount[]";
                    t7.style = "width:100px;";
                    t7.className ="form-control num_txt";
                    $(t7).attr("required", true);
                    $(t7).attr("readonly", true);
                   // t2.addEventListener('blur', function(){ app.checkQty(t2); });
                    cell7.appendChild(t7);

                var cell8=row.insertCell(7);
                cell8.className = "text-center";
                var row_action = "<a class='remove-row red-icon' title='Remove'><i class='fas fa-times-circle' style='font-size: 25px;'></i></a>";
                $(cell8).append(row_action);

            },

            getSale(id) {
              let app = this;
              axios
                .get("/sale/" + id)
                .then(function(response) {                
                    app.form.invoice_date = moment(response.data.sale.invoice_date).format('YYYY-MM-DD');
                    app.ex_products = response.data.sale.products;
                    console.log(response.data.sale.products);
                    app.form.invoice_no = response.data.sale.invoice_no;
                    app.form.reference_no = response.data.sale.reference_no;
                    $("#customer_id").append('<option value="'+response.data.sale.customer_id+'" selected>'+response.data.sale.customer.cus_name+'</option>');
                    app.form.customer_id = response.data.sale.customer_id;
                    if(response.data.sale.order_id != null) {
                        app.form.sale_order = true;
                        $("#is_so").prop("checked", true);                        
                        $("#so_list").show();

                        $("#sale_order").select2("destroy");
                        $('#sale_order option[value=""]').text(response.data.sale.order.order_no);
                        $('#sale_order').select2();

                        $("#sale_order_approval").select2("destroy");
                        $('#sale_order_approval option[value=""]').text(response.data.sale.order_approval.approval_no);
                        $('#sale_order_approval').select2();

                        $('#sale_order').prop('disabled', true);
                        $('#sale_order_approval').prop('disabled', true);
                        $("#save_btn").attr('disabled', true);
                    }
                    $("#is_so").attr("disabled", true);

                    app.form.sub_total  = response.data.sale.total_amount;
                    app.form.pay_amount = response.data.sale.pay_amount;
                    app.form.balance_amount = response.data.sale.balance_amount;

                    $.each(app.ex_products, function( key, value ) {
                        app.form.ex_product_pivot.push(value.pivot.id);
                    });

                    //add transfer products dynamically
                    $.each(app.ex_products, function( key, product ) {
                        var table=document.getElementById("product_table");
                        var row=table.insertRow((table.rows.length) - 3);
                        var cell1=row.insertCell(0);

                        var t1=document.createElement("select");
                            t1.name = "product[]";
                            t1.className = "form-control";
                            t1.style = "min-width:150px;";
                            $(t1).attr("required", true);
                            $(t1).attr('readonly', true);

                           var option = document.createElement("option");
                           option.value = product.id;
                           option.className ="form-control";
                           $(option).attr('data-uom',product.uom.uom_name);
                           $(option).attr('data-uomid',product.uom.uom_id);
                           $(option).attr('data-price','');
                           $(option).attr('data-pivotid',product.pivot.id);
                           option.text = product.product_name;
                           t1.append(option);

                            cell1.appendChild(t1);

                        var cell2=row.insertCell(1);
                        var t2=document.createElement("input");
                            t2.name = "qty[]";
                            t2.value = product.pivot.product_quantity;
                            t2.style = "width:100px;";
                            t2.className ="form-control num_txt";
                            $(t2).attr("required", true);
                            t2.addEventListener('blur', function(){ app.checkQty(t2); });
                            if(app.form.sale_order == true) {
                                $(t2).attr("readonly", true);
                            }
                            cell2.appendChild(t2);
                           
                        var cell3=row.insertCell(2);

                        var t3=document.createElement("select");
                            t3.name = "uom[]";
                            t3.className = "form-control txt_uom";
                            t3.style = "width:200px;";
                            $(t3).attr("required", true);                           

                            t3.addEventListener('blur', function(){ app.checkQty(t3); });

                            if(app.form.sale_order == true) {
                                $(t3).attr("disabled", true);
                            }

                            var option = document.createElement("option");
                            option.value = '';
                            option.text = "Select One";
                            $(option).attr('data-relation',"");
                            $(option).attr('data-price', "");
                            $(option).attr('data-perprice', "");
                            t3.append(option);

                            var option = document.createElement("option");
                            option.value = product.uom_id;
                            option.text = product.uom.uom_name;
                            $(option).attr("data-relation","");
                            $(option).attr("data-uomqty","1");
                            $(option).attr("data-price",product.product_price);
                            $(option).attr("data-perprice",product.product_price);
                            $(option).attr("data-productuom", product.uom.uom_name);
                            $(option).attr("data-productid", product.id);
                            if(product.pivot.uom_id == product.uom_id) {
                                option.selected = "selected";
                            }
                            t3.append(option);

                            var relation_val = "";

                            $.each(product.selling_uoms, function( key, suom ) {
                                var option = document.createElement("option");
                                option.value = suom.pivot.uom_id;
                                option.text = suom.uom_name;
                                var uom_relation = "1 "+suom.uom_name+" = "+ suom.pivot.relation +" "+ product.uom.uom_name;
                                if(product.pivot.uom_id == suom.pivot.uom_id) {
                                    option.selected = "selected";
                                    relation_val = "1 "+suom.uom_name+" = "+ suom.pivot.relation +" "+product.uom.uom_name;
                                }
                                $(option).attr("data-relation",uom_relation);
                                $(option).attr("data-uomqty",suom.pivot.relation);
                                $(option).attr("data-price",suom.pivot.product_price);
                                $(option).attr("data-perprice",suom.pivot.per_warehouse_uom_price);
                                $(option).attr("data-productuom",product.uom.uom_name);
                                $(option).attr("data-productid", product.id);
                                t3.append(option);
                            });
                            
                            cell3.appendChild(t3);


                        var cell4=row.insertCell(3);
                        var t4=document.createElement("input");
                            t4.type = "text";
                            if(product.pivot.uom_id != product.uom_id) {
                                t4.value = relation_val;
                            } else {
                                t4.value = "";
                            }
                            t4.name = "relation[]";
                            t4.style = "min-width:200px;";
                            t4.className ="form-control txt_relation";
                            $(t4).attr('readonly', true);
                            cell4.appendChild(t4);

                        

                        $(".txt_product").select2();

                        $(".txt_uom").select2();

                        $("#customer_id").select2();
                        $("#customer_id").on("select2:select", function(e) {

                            var data = e.params.data;
                            app.form.customer_id = data.id;
                        });

                        $(".txt_product").on("select2:select", function(e) {

                            var data = e.params.data;

                            app.selling_uoms = [];

                           var uom = e.target.options[e.target.options.selectedIndex].dataset.uom;
                           var uom_id = e.target.options[e.target.options.selectedIndex].dataset.uomid;
                           var price = e.target.options[e.target.options.selectedIndex].dataset.price;

                            //$(this).closest('td').next().find('.txt_uom').val(uom);
                            //$(this).closest('td').next().find('.txt_uom').attr('data-id',uom_id);

                            $(this).closest('td').next().next().find('.txt_uom').attr('data-uom',uom);

                            //auto add new product row
                            if($(this).closest('tr').next().hasClass("total_row")) {
                                app.addProduct();
                            }

                            //add Warehouse UOM Selling Price
                            $(this).closest('td').next().next().next().next().next().find('input').val(price);

                            var selectbox_id = $(this).closest('td').next().next().find('.txt_uom');

                            selectbox_id.find('option').remove();

                            //add pre-defined product uom 
                            if(selectbox_id.find('option[value="'+uom_id+'"]').text() == "") {
                                selectbox_id.append('<option value="">Select One</option><option value="'+uom_id+'" data-relation="" data-uomqty="1" data-productuom = "'+uom+'" data-productid="'+data.id+'" data-price="'+price+'" data-perprice="'+price+'">'+uom+'</option>'); 
                            }
                            $(".txt_uom").select2();

                            app.getSellingUomByProduct(selectbox_id, data.id);
                        });

                        $(".txt_uom").select2();

                        $(".txt_uom").on("select2:select", function(e) {

                            app.checkQty(e.target.options[e.target.options.selectedIndex]);

                            var uom_relation = e.target.options[e.target.options.selectedIndex].dataset.relation;

                            if(app.sale_type == 2) {
                            //for van sale
                                var uom_price = e.target.options[e.target.options.selectedIndex].dataset.price;
                            } else {
                            //for office sale
                                var uom_price = e.target.options[e.target.options.selectedIndex].dataset.perprice;
                            }

                            $(this).closest('td').next().find('.txt_relation').val(uom_relation);
                            $(this).closest('td').next().next().next().find('input').val(uom_price);

                            app.calTotalAmount($(this).closest('td').next().next().next().find('input'));
                        });

                        var cell5=row.insertCell(4);
                            cell5.className = "text-center";
                        var t5=document.createElement("input");
                            t5.type = "checkbox";
                            t5.name = "chk_foc[]";
                            if(product.pivot.is_foc == '1') {
                                $(t5). prop("checked", true);
                            }
                            t5.addEventListener('change', function(){ app.checkFoc(t5); });
                            if(app.form.sale_order == true) {
                                $(t5).attr("disabled", true);
                            }
                            cell5.appendChild(t5);

                        var cell6=row.insertCell(5);
                        var t6=document.createElement("input");
                            t6.name = "unit_price[]";
                            t6.style = "width:100px;";
                            if(product.pivot.price != 0 && product.pivot.price != null) {
                                t6.value = product.pivot.price;
                            }
                            t6.className ="form-control num_txt";
                            $(t6).attr("required", true);
                            if(product.pivot.is_foc == '1') {
                                $(t6).attr("readonly", true);
                            }
                            t6.addEventListener('blur', function(){ app.calTotalAmount(t6); });
                            if(app.form.sale_order == true) {
                                $(t6).attr("readonly", true);
                            }
                            cell6.appendChild(t6);

                        var cell7=row.insertCell(6);
                        var t7=document.createElement("input");
                            t7.name = "total_amount[]";
                            t7.style = "width:100px;";
                            if(product.pivot.total_amount != 0 && product.pivot.total_amount != null) {
                                t7.value = product.pivot.total_amount;
                            }
                            t7.className ="form-control num_txt";
                            $(t7).attr("required", true);
                            $(t7).attr("readonly", true);
                           // t2.addEventListener('blur', function(){ app.checkQty(t2); });
                            cell7.appendChild(t7);

                        var cell8=row.insertCell(7);
                        cell8.className = "text-center";
                        if(app.user_role != 'admin' && app.form.sale_order == false) 
                        {
                            var row_action = "<a class='remove-row red-icon' title='Remove'><i class='fas fa-times-circle' style='font-size: 25px;'></i></a>";
                        }
                        $(cell8).append(row_action);
                    });


                })
                .catch(function(error) {
                  // handle error
                  console.log(error);
                })
                .then(function() {
                  // always executed
                });

                $(".txt_uom").select2();
            },

            checkQty(obj) { 
                let app = this; 
                if(typeof obj.name !== 'undefined') {

                    //For quantity box onBlur Event                    

                    var product_id = $(obj).closest('td').prev().find(':selected').val();
                    var transfer_qty = obj.value;
                    var uom = $(obj).closest('td').next().find(':selected').val();   

                    var product_qty = 0; 
                    var uom_val = "";
                    var uom_name  = "";  
                    var p_uom_val = "";
                    var p_uom_name = "";
                    var p_qty = 0;            

                    if(product_id != "" && transfer_qty != "" && uom != "") {

                        //calculate same products quantity in product list
                        $(".txt_product").each(function() {
                            if(product_id == $(this).val()) {
                                p_uom_val  = $(this).closest('td').next().next().find(':selected').attr('data-uomqty');
                                p_uom_name = $(this).closest('td').next().next().find(':selected').attr('data-productuom');
                                p_qty =  $(this).closest('td').next().find('input').val();
                                if(typeof p_uom_val !== "undefined" && typeof p_uom_name != "undefined" != "" && p_qty != "") {

                                    product_qty = parseInt(product_qty) + (parseInt(p_qty) * parseInt(p_uom_val));
                                }
                            }
                        });

                        //uom_val  = $(obj).closest('td').next().find(':selected').attr('data-uomqty');
                        uom_name = $(obj).closest('td').next().find(':selected').attr('data-productuom');

                        //product_qty = parseInt(product_qty) + (parseInt(transfer_qty) * parseInt(uom_val));

                        var key = this.products.findIndex(x => x.product_id == product_id);
                        var available_qty = parseInt(this.products[key].in_count) - parseInt(this.products[key].out_count);
                        if(product_qty > available_qty) {

                            swal("Warning!", "Not enough quantity! Availabel quantity is "+available_qty+" "+uom_name+".", "warning");

                            $(obj).focus(); obj.value="";
                        }
                    }

                    //claculate total amount
                    var unit_price = $(obj).closest('td').next().next().next().next().find('input').val();
                    var relation_val = $(obj).closest('td').next().find(':selected').attr('data-uomqty');
                    if(obj.value != "" && unit_price != "") {
                        if(app.sale_type == 2) {
                            var total_amount = parseInt(obj.value) * parseInt(unit_price);
                        } else {
                            var total_amount = parseInt(obj.value) * parseInt(relation_val) * parseInt(unit_price);
                        }
                        $(obj).closest('td').next().next().next().next().next().find('input').val(total_amount);
                    }

                    //get all sub total amount
                    var sub_total = 0;
                    for(var i=0; i<document.getElementsByName('product[]').length; i++) {
                        if(document.getElementsByName('total_amount[]')[i].value != "") {
                            sub_total += parseInt(document.getElementsByName('total_amount[]')[i].value);
                        }
                    }

                    app.form.sub_total = sub_total;
                    app.form.balance_amount  = sub_total - parseInt(app.form.pay_amount);
                } else {

                    //For UOM box select Event

                    var product_qty = 0; 
                    var uom_val = "";
                    var uom_name  = "";  
                    var p_uom_val = "";
                    var p_uom_name = "";
                    var p_qty = 0;  

                    var product_id = $(obj).attr('data-productid');
                    var transfer_qty = $(obj).closest('td').prev().find('input').val();
                    var uom = obj.value;                    

                    if(product_id != "" && transfer_qty != "" && uom != "") {

                        //calculate same products quantity in product list
                        $(".txt_product").each(function() {
                            if(product_id == $(this).val()) {
                                p_uom_val  = $(this).closest('td').next().next().find(':selected').attr('data-uomqty');
                                p_uom_name = $(this).closest('td').next().next().find(':selected').attr('data-productuom');
                                p_qty =  $(this).closest('td').next().find('input').val();
                                if(typeof p_uom_val !== "undefined" && typeof p_uom_name != "undefined" != "" && p_qty != "") {

                                    product_qty = parseInt(product_qty) + (parseInt(p_qty) * parseInt(p_uom_val));
                                }
                            }
                        });

                        //var uom_val  = $(obj).attr('data-uomqty');
                        var uom_name = $(obj).attr('data-productuom');

                        //var product_qty = parseInt(transfer_qty) * parseInt(uom_val);

                        var key = this.products.findIndex(x => x.product_id == product_id);
                        var available_qty = parseInt(this.products[key].in_count) - parseInt(this.products[key].out_count);
                        if(product_qty > available_qty) {

                            swal("Warning!", "Not enough quantity! Availabel quantity is "+available_qty+" "+uom_name+".", "warning");

                            $(obj).closest('td').prev().find('input').focus();  $(obj).closest('td').prev().find('input').val('');
                        }
                    }
                }
            },

            calTotalAmount(obj) {
               let app = this;
               var price = $(obj).val();
               var qty = $(obj).closest('td').prev().prev().prev().prev().find('input').val();

               //check price variant
              /* var product_id = $(obj).closest('td').prev().prev().prev().prev().prev().find(':selected').val();
               var key = app.products.findIndex(x => x.product_id == product_id);               
               var product_price = app.products[key].product_price; */

               if(app.sale_type == 2) {
                //Van Sale
                    var product_price = $(obj).closest('td').prev().prev().prev().find(':selected').attr('data-price');

               } else {
                //office Sale
                    var product_price = $(obj).closest('td').prev().prev().prev().find(':selected').attr('data-perprice');
               }

               if(product_price != '') {
                    var price_variant =  parseInt(price) - parseInt(product_price);
                    const wrapper = document.createElement('div');
                    if(price_variant < 0) {
                        wrapper.innerHTML = "Product Price Variant is <font color='red'>"+price_variant+"</font>";
                        swal({
                          icon: 'info',
                          content: wrapper,
                          timer:1000
                        });
                    }

                    if(price_variant > 0) {
                        wrapper.innerHTML = "Product Price Variant is <font color='green'>"+price_variant+"</font>";
                        swal({
                          icon: 'info',
                          content: wrapper,
                          timer:1000
                        });
                    }
               }

               if(price != "" && qty != "") {
                    if(app.sale_type == 2) {
                        var total = parseInt(qty) * parseInt(price);
                    } else {
                        var relation_val = $(obj).closest('td').prev().prev().prev().find(':selected').attr('data-uomqty');
                        var total = parseInt(qty) * parseInt(relation_val) * parseInt(price);
                    }
                    $(obj).closest('td').next().find('input').val(total);
               }

               //get all sub total amount
               var sub_total = 0;
               for(var i=0; i<document.getElementsByName('product[]').length; i++) {
                    if(document.getElementsByName('total_amount[]')[i].value != "") {
                        sub_total += parseInt(document.getElementsByName('total_amount[]')[i].value);
                    }
               }

               app.form.sub_total = sub_total;
               app.form.balance_amount  = sub_total - parseInt(app.form.pay_amount);
            },

            calBalance(obj) {
                let app = this;
                var pay_amount = 0;
                if(obj.value != "") {
                    var pay_amount = obj.value;
                } 
                app.form.pay_amount = obj.value;
                app.form.balance_amount = parseInt(app.form.sub_total) - parseInt(pay_amount);
            },

            onSubmit: function(event){
                let app = this;
                app.form.sale_type = app.sale_type;

                if(app.sale_type == 1) {
                    app.form.reference_no = $("#reference_no").val();
                } else {
                    app.form.reference_no = '';
                }

                if(app.form.sale_order == true) {
                    app.form.order_id =  $("#sale_order").val();
                    app.form.approval_id = $("#sale_order_approval").val();
                }

                if (!this.isEdit) {
                    for(var i=0; i<document.getElementsByName('product[]').length; i++) {
                        app.form.product.push(document.getElementsByName('product[]')[i].value);
                        app.form.uom.push(document.getElementsByName('uom[]')[i].value);
                        app.form.qty.push(document.getElementsByName('qty[]')[i].value);
                        app.form.unit_price.push(document.getElementsByName('unit_price[]')[i].value);
                        app.form.total_amount.push(document.getElementsByName('total_amount[]')[i].value);
                        if(document.getElementsByName('chk_foc[]')[i].checked == true) {
                            app.form.is_foc.push('1');
                        } else {
                            app.form.is_foc.push('0');
                        }

                        //for  price variant
                       /* var key = app.products.findIndex(x => x.product_id == document.getElementsByName('product[]')[i].value);
                        var product_price = app.products[key].product_price;*/

                        if(app.sale_type == 2) {
                            //van sale
                            var product_price = document.getElementsByName('uom[]')[i].options[document.getElementsByName('uom[]')[i].options.selectedIndex].dataset.price;
                        } else {
                            //office sale
                            var product_price = document.getElementsByName('uom[]')[i].options[document.getElementsByName('uom[]')[i].options.selectedIndex].dataset.perprice;
                        }

                        var price_variant =  parseInt(document.getElementsByName('unit_price[]')[i].value) - parseInt(product_price);

                        app.form.price_variants.push(price_variant);
                        
                    }
                    //console.log(app.form.total_amount);
                    //console.log(app.form.is_foc); return false;

                    this.form
                      .post("/sale")
                      .then(function(data) {
                        console.log(data.data);
                        if(data.status == "success") {

                            //reset form data
                            event.target.reset();
                            $(".txt_product").select2();
                            swal({
                                title: "Success!",
                                text: 'Sale Invoice is saved.',
                                icon: "success",
                                button: true
                            }).then(function() {
                                location.reload();
                                //generate pdf
                                if(app.sale_type == 1)
                                {
                                    var baseurl = window.location.origin;
                                    window.open(baseurl+'/generate_invoice/'+data.sale_id);
                                }
                            });
                        } else {
                          $.notify("Error", {
                            autoHideDelay: 3000,
                            gap: 1,
                            className: "error"
                          });
                        }
                    })
                    .catch(function (response)  {
                      // console.log(response.errors);

                    });
                } else {
                    //Edit entry details

                    app.form.product_pivot = [];


                    for(var i=0; i<document.getElementsByName('product[]').length; i++) {
                        app.form.product.push(document.getElementsByName('product[]')[i].value);
                        app.form.uom.push(document.getElementsByName('uom[]')[i].value);
                        app.form.qty.push(document.getElementsByName('qty[]')[i].value);
                        app.form.unit_price.push(document.getElementsByName('unit_price[]')[i].value);
                        app.form.total_amount.push(document.getElementsByName('total_amount[]')[i].value);
                        if(document.getElementsByName('chk_foc[]')[i].checked == true) {
                            app.form.is_foc.push('1');
                        } else {
                            app.form.is_foc.push('0');
                        }

                        app.form.product_pivot.push(document.getElementsByName('product[]')[i].options[document.getElementsByName('product[]')[i].options.selectedIndex].dataset.pivotid);

                        //for  price variant
                        /*var key = app.products.findIndex(x => x.product_id == document.getElementsByName('product[]')[i].value);
                        var product_price = app.products[key].product_price;*/

                        if(app.sale_type == 2) {
                            //van sale
                            var product_price = document.getElementsByName('uom[]')[i].options[document.getElementsByName('uom[]')[i].options.selectedIndex].dataset.price;
                        } else {
                            //office sale
                            var product_price = document.getElementsByName('uom[]')[i].options[document.getElementsByName('uom[]')[i].options.selectedIndex].dataset.perprice;
                        }

                        var price_variant =  parseInt(document.getElementsByName('unit_price[]')[i].value) - parseInt(product_price);

                        app.form.price_variants.push(price_variant);
                    }
                    //console.log(app.form.ex_product_pivot);
                    //console.log(app.form.product_pivot);

                    //return false;

                    this.form
                      .patch("/sale/" + app.sale_id)
                      .then(function(data) {
                        if(data.status == "success") {

                            //reset form data
                            event.target.reset();
                            $(".txt_product").select2();

                            swal({
                                title: "Success!",
                                text: 'Sale Invoice is updated.',
                                icon: "success",
                                button: true
                            }).then(function() {
                                location.reload();

                            });
                        } else {
                          $.notify("Error", {
                            autoHideDelay: 3000,
                            gap: 1,
                            className: "error"
                          });
                        }
                    })
                    .catch(function (response)  {
                      // console.log(response.errors);

                    });
                }
            },

            checkFoc(obj) {
                let app = this;
                var is_foc = $(obj).prop("checked");
                if(is_foc) {

                    $(obj).closest('td').next().find('input').attr('readonly',true);

                    var t1=document.createElement("input");
                    t1.name = "unit_price[]";
                    t1.style = "width:100px;";
                    t1.className ="form-control num_txt";
                    $(t1).attr("readonly", true);
                    t1.addEventListener('blur', function(){ app.calTotalAmount(t1); });
                    $(obj).closest('td').next().html(t1);

                    var t2=document.createElement("input");
                    t2.name = "total_amount[]";
                    t2.style = "width:100px;";
                    t2.className ="form-control num_txt";
                    $(t2).attr("readonly", true);
                    $(obj).closest('td').next().next().html(t2);

                    $(obj).closest('td').next().find('input').attr('required',false);
                } else {
                    $(obj).closest('td').next().find('input').attr('readonly',false);
                    $(obj).closest('td').next().find('input').attr('required',true);

                    if(app.sale_type == 2) {
                    //Van Sale
                        var product_price = $(obj).closest('td').prev().prev().find(':selected').attr('data-price');

                   } else {
                    //office Sale
                        var product_price = $(obj).closest('td').prev().prev().find(':selected').attr('data-perprice');
                   }

                   $(obj).closest('td').next().find('input').val(product_price);
                }

                app.calTotalAmount($(obj).closest('td').next().find('input'));
            },

            removeProduct(id) {
                let app = this;
                swal({
                    title: "Are you sure?",
                    text: "Once deleted, you will not be able to recover!",
                    icon: "warning",
                    buttons: true,
                    dangerMode: true
                    }).then(willDelete => {
                    if (willDelete) {
                        $("#"+id).remove();
                        var sub_total = 0; 
                        for(var i=0; i<document.getElementsByName('product[]').length; i++) {
                            if(document.getElementsByName('total_amount[]')[i].value != "") {
                                sub_total += parseInt(document.getElementsByName('total_amount[]')[i].value);
                            }
                        }

                        app.form.sub_total = sub_total;
                        app.form.balance_amount  = sub_total - parseInt(app.form.pay_amount);   
                    } else {
                      //
                    }
                });
            },

            checkSO(obj) {
                let app = this;
                var is_so = $(obj).prop("checked");
                if(is_so){
                    app.form.sale_order = true;
                    $("#so_list").show();
                    $("#sale_order").attr("required", true);
                    $("#sale_order_approval").attr("required", true);
                } else {
                    location.reload();
                    /*app.form.sale_order = false;
                    $("#so_list").hide();
                    $("#sale_order").attr("required", false);
                    $("#sale_order_approval").attr("required", false); */
                }
            },

            getApproval(id) {
                $("#loading").show();
                $('#order_product_table tbody tr').slice(0, -3).remove();
                let app = this;
              axios
                .get("/order_approval/" + id)
                .then(function(response) { 
                    app.ex_products = response.data.approval.products;

                    app.form.sub_total  = response.data.approval.total_amount;
                    app.form.balance_amount = response.data.approval.total_amount;

                    /*$.each(app.ex_products, function( key, value ) {
                        app.form.ex_product_pivot.push(value.pivot.id);
                    });*/

                    //add order products dynamically
                    $.each(app.ex_products, function( key, product ) {
                        var table=document.getElementById("order_product_table");
                        var row=table.insertRow((table.rows.length) - 3);
                        var cell1=row.insertCell(0);

                        var t1=document.createElement("select");
                            t1.name = "product[]";
                            t1.className = "form-control";
                            t1.style = "min-width:150px;";
                            $(t1).attr("required", true);
                            $(t1).attr('readonly', true);

                           var option = document.createElement("option");
                           option.value = product.id;
                           option.className ="form-control";
                           $(option).attr('data-uom',product.uom.uom_name);
                           $(option).attr('data-uomid',product.uom.uom_id);
                           $(option).attr('data-price','');
                           $(option).attr('data-pivotid',product.pivot.id);
                           option.text = product.product_name;
                           t1.append(option);

                            cell1.appendChild(t1);

                        var cell2=row.insertCell(1);
                        var t2=document.createElement("input");
                            t2.name = "qty[]";
                            t2.value = product.pivot.approval_qty;
                            t2.style = "width:100px;";
                            t2.className ="form-control num_txt";
                            $(t2).attr("required", true);
                            $(t2).attr('readonly', true);
                            t2.addEventListener('blur', function(){ app.checkQty(t2); });
                            cell2.appendChild(t2);
                           
                        var cell3=row.insertCell(2);

                        var t3=document.createElement("select");
                            t3.name = "uom[]";
                            t3.className = "form-control txt_uom";
                            t3.style = "width:200px;";
                            $(t3).attr("required", true);
                            t3.addEventListener('blur', function(){ app.checkQty(t3); });

                            var option = document.createElement("option");
                            option.value = '';
                            option.text = "Select One";
                            $(option).attr('data-relation',"");
                            $(option).attr('data-price', "");
                            $(option).attr('data-perprice', "");
                            t3.append(option);

                            var option = document.createElement("option");
                            option.value = product.uom_id;
                            option.text = product.uom.uom_name;
                            $(option).attr("data-relation","");
                            $(option).attr("data-uomqty","1");
                            $(option).attr("data-price",product.product_price);
                            $(option).attr("data-perprice",product.product_price);
                            $(option).attr("data-productuom", product.uom.uom_name);
                            $(option).attr("data-productid", product.id);
                            if(product.pivot.uom_id == product.uom_id) {
                                option.selected = "selected";
                            }
                            t3.append(option);

                            var relation_val = "";

                            $.each(product.selling_uoms, function( key, suom ) {
                                var option = document.createElement("option");
                                option.value = suom.pivot.uom_id;
                                option.text = suom.uom_name;
                                var uom_relation = "1 "+suom.uom_name+" = "+ suom.pivot.relation +" "+ product.uom.uom_name;
                                if(product.pivot.uom_id == suom.pivot.uom_id) {
                                    option.selected = "selected";
                                    relation_val = "1 "+product.uom.uom_name+" = "+ suom.pivot.relation +" "+suom.uom_name;
                                }
                                $(option).attr("data-relation",uom_relation);
                                $(option).attr("data-uomqty",suom.pivot.relation);
                                $(option).attr("data-price",suom.pivot.product_price);
                                $(option).attr("data-perprice",suom.pivot.per_warehouse_uom_price);
                                $(option).attr("data-productuom",product.uom.uom_name);
                                $(option).attr("data-productid", product.id);
                                t3.append(option);
                            });

                            $(t3).attr('readonly', true);

                         cell3.appendChild(t3);


                        var cell4=row.insertCell(3);
                        var t4=document.createElement("input");
                            t4.type = "text";
                            if(product.pivot.uom_id != product.uom_id) {
                                t4.value = relation_val;
                            } else {
                                t4.value = "";
                            }
                            t4.name = "relation[]";
                            t4.style = "min-width:200px;";
                            t4.className ="form-control txt_relation";
                            $(t4).attr('readonly', true);
                            cell4.appendChild(t4);

                        var cell5=row.insertCell(4);
                            cell5.className = "text-center";
                        var t5=document.createElement("input");
                            t5.type = "checkbox";
                            t5.name = "chk_foc[]";
                            if(product.pivot.is_foc == '1') {
                                $(t5). prop("checked", true);
                            }
                            t5.addEventListener('change', function(){ app.checkFoc(t5); });
                            $(t5).attr('disabled', true);
                            cell5.appendChild(t5);

                        var cell6=row.insertCell(5);
                        var t6=document.createElement("input");
                            t6.name = "unit_price[]";
                            t6.style = "width:100px;";
                            if(product.pivot.price != 0 && product.pivot.price != null) {
                                t6.value = product.pivot.price;
                            }
                            t6.className ="form-control num_txt";
                            $(t6).attr("required", true);
                            if(product.pivot.is_foc == '1') {
                                $(t6).attr("readonly", true);
                            }
                            t6.addEventListener('blur', function(){ app.calTotalAmount(t6); });
                            $(t6).attr("readonly", true);
                            cell6.appendChild(t6);

                        var cell7=row.insertCell(6);
                        var t7=document.createElement("input");
                            t7.name = "total_amount[]";
                            t7.style = "width:100px;";
                            if(product.pivot.total_amount != 0 && product.pivot.total_amount != null) {
                                t7.value = product.pivot.total_amount;
                            }
                            t7.className ="form-control num_txt";
                            $(t7).attr("required", true);
                            $(t7).attr("readonly", true);
                           // t2.addEventListener('blur', function(){ app.checkQty(t2); });
                            cell7.appendChild(t7);

                        
                    });


                })
                .catch(function(error) {
                  // handle error
                  console.log(error);
                })
                .then(function() {
                  // always executed
                });

                $("#loading").hide();
            },

        }
    }
</script>