<template>
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="#!"><i class="feather icon-home"></i></a></li>
                <li class="breadcrumb-item"><a :href="site_path+'/'">Home</a></li>
                <li class="breadcrumb-item" v-if="sale_type == 1"><a :href="site_path+'/office'">Office Sale</a></li>
                <li class="breadcrumb-item" v-else><a :href="site_path+'/van'">Van Sale</a></li>
                <li class="breadcrumb-item"><router-link tag="span" :to="'/sale/'+sale_type+'/'" class="font-weight-normal"><a href="#">Sale Invoice</a></router-link></li>
                <li class="breadcrumb-item active" aria-current="page">Sale Invoice Form</li>

            </ol>
        </nav>
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
                    <form class="form" id="sale_form" method="post" @submit.prevent="onSubmit" @keydown="form.errors.clear($event.target.name)">
                        <div class="row">
                            <div class="form-group col-md-4">
                                <label for="invoice_no">Invoice No.</label>
                                <input type="text" class="form-control" id="invoice_no" name="invoice_no" v-model="form.invoice_no" readonly>
                            </div>
                            <div class="form-group col-md-4" v-if="sale_type == 1">
                                <label for="reference_no">Reference No.</label>
                                 <input type="text" class="form-control" id="reference_no" name="reference_no"
                                v-model="form.reference_no">
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-md-4">
                                <label for="invoice_date">Date</label>
                                <input type="text" class="form-control datetimepicker" id="invoice_date" name="invoice_date"
                                v-model="form.invoice_date" required :readonly="SOEdit">
                            </div>
                            <div class="form-group col-md-4">
                                <label for="branch_name">Branch</label>
                                 <input type="text" class="form-control" id="branch_name" name="branch_name"
                                v-model="user_branch" readonly>
                            </div>
                        </div>
                        <div class="row">
                            
                            <div class="form-group col-md-4">
                                <label for="sale_man">Sale Man</label>
                                <select id="sale_man" class="mm-txt"
                                    name="sale_man" v-model="form.office_sale_man_id" style="width:100%"
                                >
                                    <option value="">Select One</option>
                                    <option v-for="sale_man in sale_men" :value="sale_man.id"  >{{sale_man.sale_man}}</option>
                                </select>
                            </div>

                            <div class="form-group col-md-4">
                                <label for="customer_id">Customer</label>
                                <select id="customer_id" class="form-control mm-txt"
                                    name="customer_id" v-model="form.customer_id" style="width:100%" required :disabled="SOEdit"
                                >
                                    <option value="">Select One</option>
                                    <option v-for="cus in customers" :value="cus.id"  >{{cus.cus_name}}</option>
                                </select>
                            </div>
                            <!--<div class="form-group col-md-4">
                                <label for="vehicle_warehouse">Vehicle Warehouse</label>
                                 <input type="text" class="form-control" id="vehicle_warehouse" name="vehicle_warehouse"
                                v-model="user_warehouse" readonly>
                            </div>-->

                        </div>

                        <div class="row mt-3">
                             

                            
                            <div class="form-group col-md-4">
                                <label for="payment_type">Payment Type</label>
                                <select id="payment_type" class="form-control"
                                    name="payment_type" v-model="form.payment_type" @change='changePayment()' style="width:100%" required :disabled="SOEdit"
                                >
                                    <option value="">Select One</option>
                                    <option value="cash">Cash</option>
                                    <option value="credit">Credit</option>
                                </select>
                            </div>
                            <div class="form-group col-md-4" v-if="form.payment_type == 'credit'">
                                <label for="due_date">Due Date</label>
                                <input type="text" class="form-control datetimepicker" id="due_date" name="due_date"
                                v-model="form.due_date" :readonly="SOEdit">
                            </div>
                            <div class="form-group col-md-4" style="display:none;" v-else>
                                <label for="due_date">Due Date</label>
                                <input type="text" class="form-control datetimepicker" id="due_date" name="due_date"
                                v-model="form.due_date" :readonly="SOEdit">
                            </div>
                            <div class="form-group col-md-4" v-if="form.payment_type == 'credit'">
                                <label for="credit_day">Credit Days</label>
                                 <input type="text" class="form-control num_txt" id="credit_day" name="credit_day"
                                v-model="form.credit_day" @blur="calcDueDate()" :readonly="SOEdit">
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-md-2 custom-control custom-switch" style="padding-left:10px;">
                              <label style='margin-right:50px;' class="ml-0">Sale Order</label>
                              <input type="checkbox" class="custom-control-input" id="is_so" name="is_so" @change="checkSO($event.target)">                              
                              <label class="custom-control-label" for="is_so"></label>
                            </div>   
                            <div class="form-group col-md-4" style="display:none" id="so_list">
                                <select id="sale_order" class="form-control"
                                    name="sale_order" style="width:100%"
                                >
                                    <option value="">Select Sale Order</option>

                                    <option v-for="so in sale_orders" :data-saleman= "so.sale_man == null ? '' : so.sale_man.name" :value="so.id"  >{{so.order_no}}</option>
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
                                <a class='blue-icon' title='Add Product' @click="addProduct()" v-if="((user_role == 'system' || user_role == 'office_user') && form.sale_order == false && !isDisabled) || (( user_role == 'system' || user_role == 'office_user') && form.revise_order == true && !isDisabled) || (!isEdit)"><i class='fas fa-plus-square' style='font-size: 30px;'></i></a>
                                <div style="display:none;">
                                    <select class="form-control txt_product"
                                        id="txt_product" style="min-width:150px;"
                                    >
                                        <option value="">Select One</option>
                                        <option v-for="product in products" :data-uom="product.uom_name" 
                                        :data-price="product.selling_price"
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
                                            <th scope="col" >UOM</th>
                                            <th scope="col" >Rate</th>
                                            <th scope="col" >Discount (%)</th>
                                            <th scope="col" >Actual Rate</th>
                                            <th scope="col" >FOC</th>
                                            <th scope="col">Other Discount (%)</th>
                                            <th scope="col" >Amount</th>
                                            <th scope="col" class="text-center"></th>
                                        </tr>
                                    </thead>
                                    <tbody> 
                                        <template v-if="isEdit && ex_products.length > 0">
                                        </template>
                                        <template v-if="!isEdit && ex_products.length <= 0">                                    
                                        <tr id="1" v-if="form.sale_order == false">
                                            <td>                                               
                                                <select class="form-control txt_product"
                                                    name="product[]" id="product_1" style="min-width:150px;" required
                                                >
                                                    <option value="">Select One</option>
                                                    <option v-for="product in products" :data-uom="product.uom_name" 
                                                    :data-price="product.selling_price"
                                                    :data-uomid="product.uom_id" :value="product.product_id" 
                                                    data-pivotid = "0">{{product.product_name}}</option>
                                                </select>
                                            </td>                                                
                                            <td>
                                                <input type="text" class="form-control num_txt txt_qty" style="width:100px;" name="qty[]"  id="qty_1" @blur="checkQty($event.target)" required />
                                            </td>
                                            <td>
                                                <select class="form-control txt_uom"
                                                    name="uom[]" id="uom_1" style="min-width:150px;" data-uom="" required
                                                >
                                                    <option value="">Select One</option>
                                                </select>
                                            </td>
                                            <td>
                                                <input type="text" style="min-width:100px;" class="form-control" name="rate[]" id="rate_1" @blur="calTotalAmount($event.target)" required />
                                            </td>
                                            <td>
                                                <input type="text" style="min-width:70px;" class="form-control num_txt" name="discount[]" id="discount_1" @blur="calTotalAmount($event.target)" />
                                            </td>
                                            <td>
                                                <input type="text" style="min-width:100px;" class="form-control" name="actual_rate[]" id="actual_rate_1" readonly required />
                                            </td>
                                            <td class="text-center">
                                                <input
                                                    type="checkbox"
                                                    name="chk_foc[]" id="foc_1"
                                                    @change="checkFoc($event.target)"
                                                >
                                            </td>
                                            <td>
                                                <input type="text" style="width:70px;" class="form-control num_txt" name="other_discount[]" id="other_discount_1" @blur="calTotalAmount($event.target)" />
                                            </td>
                                            <td>
                                                <input type="text" class="form-control num_txt" readonly style="width:100px;" name="total_amount[]" id="total_amount_1" required />
                                            </td>
                                            <td class="text-center">
                                                <a class='remove-row red-icon' title='Remove' v-if="user_role != 'admin'"><i class='fas fa-times-circle' style='font-size: 25px;'></i></a>
                                            </td>
                                        </tr>
                                        </template>
                                        
                                        <tr class="total_row">
                                            <td colspan="7" class="text-right">Total Amount</td>
                                            <td>&nbsp;</td>
                                            <td colspan="2" class="text-right">
                                                <input type="text" v-model="form.sub_total" class="form-control num_txt" readonly style="width:150px;" required />
                                            </td>
                                        </tr>
                                        <tr class="total_row">
                                            <td colspan="7" class="text-right">Cash Discount</td>
                                            <td></td>
                                            <td colspan="2">
                                                <input type="text" v-model="form.cash_discount" class="form-control num_txt" style="width:150px;" @blur="changeCashDiscount()" />
                                            </td>
                                        </tr> 
                                        <tr class="total_row">
                                            <td colspan="7" class="text-right">Net Total</td>
                                            <td></td>
                                            <td colspan="2">
                                                <input type="text" v-model="form.net_total" class="form-control num_txt" readonly style="width:150px;" required />
                                            </td>
                                        </tr> 
                                        <tr class="total_row">
                                            <td colspan="7" class="text-right">Tax</td>
                                            <td><input type="text" v-model="form.tax" class="form-control num_txt" style="width:70px;" placeholder='%' @blur="changeTax()"/></td>
                                            <td colspan="2">
                                                <input type="text" v-model="form.tax_amount" class="form-control num_txt" readonly style="width:150px;" />
                                            </td>
                                        </tr>
                                        <tr class="total_row">
                                            <td colspan="7" class="text-right">Paid Amount</td>
                                            <td></td>
                                            <td colspan="2">
                                                <input type="text" id="pay_amount" v-model="form.pay_amount" class="form-control num_txt" readonly="readonly" style="width:150px;" @blur="changePaidAmount()" required />
                                            </td>
                                        </tr>
                                        <tr class="total_row">
                                            <td colspan="4" class="text-right">Previous Balance :</td>
                                            <td>{{form.previous_balance}}</td>
                                            <td colspan="2" class="text-right">Balance Amount</td>
                                            <td></td>
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
                            <div class="col-md-12 text-right mt-0">
                                <a class='blue-icon' title='Add Product' @click="addProduct()" v-if="((user_role == 'system' || user_role == 'office_user') && form.revise_order == true && !isDisabled) || (!isEdit && (form.sale_order == false || form.revise_order == true))"><i class='fas fa-plus-square' style='font-size: 30px;'></i></a>
                                <div style="display:none;">
                                    <select class="form-control txt_product"
                                        id="txt_product" style="min-width:150px;"
                                    >
                                        <option value="">Select One</option>
                                        <option v-for="product in products" :data-uom="product.uom_name" 
                                        :data-price="product.selling_price"
                                        :data-uomid="product.uom_id" :value="product.product_id" 
                                        data-pivotid = "0">{{product.product_name}}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-12 table-responsive">
                                <table class="table table-bordered" id="order_product_table">
                                    <thead class="thead-grey">
                                        <tr>
                                            <th scope="col" >Product Name</th>
                                            <th scope="col" >SO QTY</th>
                                            <th scope="col" >Accept QTY</th>
                                            <th scope="col" >UOM</th>
                                            <th scope="col" >Rate</th>
                                            <th scope="col" >Discount (%)</th>
                                            <th scope="col" >Actual Rate</th>
                                            <th scope="col" >FOC</th>
                                            <th scope="col">Other Discount (%)</th>
                                            <th scope="col" >Amount</th>
                                            <th scope="col" class="text-center"></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr class="total_row">
                                            <td colspan="8" class="text-right">Total Amount</td>
                                            <td>&nbsp;</td>
                                            <td colspan="2" class="text-right">
                                                <input type="text" v-model="form.sub_total" class="form-control num_txt" readonly style="width:150px;" required />
                                            </td>
                                        </tr>
                                        <tr class="total_row">
                                            <td colspan="8" class="text-right">Cash Discount</td>
                                            <td></td>
                                            <td colspan="2">
                                                <input type="text" v-model="form.cash_discount" class="form-control num_txt" style="width:150px;" @blur="changeCashDiscount()" />
                                            </td>
                                        </tr> 
                                        <tr class="total_row">
                                            <td colspan="8" class="text-right">Net Total</td>
                                            <td></td>
                                            <td colspan="2">
                                                <input type="text" v-model="form.net_total" class="form-control num_txt" readonly style="width:150px;" required />
                                            </td>
                                        </tr> 
                                        <tr class="total_row">
                                            <td colspan="8" class="text-right">Tax</td>
                                            <td><input type="text" v-model="form.tax" class="form-control num_txt" style="width:70px;" placeholder='%' @blur="changeTax()"/></td>
                                            <td colspan="2">
                                                <input type="text" v-model="form.tax_amount" class="form-control num_txt" readonly style="width:150px;" />
                                            </td>
                                        </tr>
                                        <tr class="total_row">
                                            <td colspan="8" class="text-right">Paid Amount</td>
                                            <td></td>
                                            <td colspan="2">
                                                <input type="text" id="pay_amount" v-model="form.pay_amount" class="form-control num_txt" readonly="readonly" style="width:150px;" @blur="changePaidAmount()" />
                                            </td>
                                        </tr>
                                        <tr class="total_row">
                                            <td colspan="5" class="text-right">Previous Balance :</td>
                                            <td>{{form.previous_balance}}</td>
                                            <td colspan="2" class="text-right">Balance Amount</td>
                                            <td></td>
                                            <td colspan="2">
                                                <input type="text" v-model="form.balance_amount" class="form-control num_txt" readonly style="width:150px;" required />
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <!-- end -->

                        <div class="row">
                            <div class="col-md-12" v-if="user_role != 'office_order_user' && !isEdit">
                                <input type="submit" class="btn btn-primary btn-sm" value="Save Entry" id="save_btn" :disabled = "isDisabled">
                            </div>

                            <div class="col-md-12" v-if="(user_role == 'system' || user_role == 'office_user') && isEdit && !isDisabled"> 
                                <input type="submit" class="btn btn-primary btn-sm" value="Update" id="save_btn">
                            </div>

                        </div>

                    </form>                    
                <!-- form end -->  
                </div>

            </div>
        </div>
        <div id="loading" class="text-center"><img :src="storage_path+'/image/loader_2.gif'" /></div>
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
                office_sale_man: "",
                office_sale_man_id: "",
                product: [],
                uom: [],
                qty: [],
                unit_price: [],
                rate: [],
                actual_rate: [],
                discount: [],
                other_discount: [],
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
                payment_type: 'cash',
                credit_day: '',
                due_date: '',
                sale_man: '',
                revise_order: false,
                duplicate_ref_no: false,
                discount: '',
                previous_balance: '',
                cash_discount: '',
                net_total: '',
                tax: '',
                tax_amount: '',    
                order_product_id: [],            

              }),              
              isEdit: false,
              brands: [],
              categories: [],
              products: [],
              uoms: [],
              sale_id: '',
              ex_products: [],
              user_warehouse: '',
              selling_uoms: [],
              customers: [],
              sale_type: '',
              user_role: '',
              user_year: '',
              sale_orders: [],
              is_readonly: false,
              required_val : true,
              isDisabled: false,
              original_form: '',
              edit_form: '',
              site_path: '',
              storage_path: '',
              SOEdit: false,
              user_branch: '',
              sale_men: [],
            };
        },

        created() {


            this.user_year = document.querySelector("meta[name='user-year-likelink']").getAttribute('content');

            //sale_type = 2 for Van and 1 for Office Sale
            this.site_path = document.querySelector("meta[name='site-path']").getAttribute('content');
            //this.site_path = this.site_path.substring(this.site_path.lastIndexOf('/')+1);
            this.storage_path = document.querySelector("meta[name='storage-path']").getAttribute('content');

            this.sale_type = this.$route.params.sale_type;
            this.user_warehouse = document.querySelector("meta[name='user-wh']").getAttribute('content');

            this.user_branch = document.querySelector("meta[name='user-branch']").getAttribute('content');

            //this.form.office_sale_man = document.querySelector("meta[name='user-name-likelink']").getAttribute('content');
            //this.form.office_sale_man_id = document.querySelector("meta[name='user-id-likelink']").getAttribute('content');

            this.user_role = document.querySelector("meta[name='user-role']").getAttribute('content');
            if(this.user_role == "office_order_user") {
                //var url =  window.location.origin;
                //window.location.replace(url);
            }

            if(this.user_role == "admin" && !this.isEdit) {
                this.isDisabled = true;
            }
            if(this.$route.params.id) {
                this.isEdit = true;
                this.sale_id = this.$route.params.id;
                let app = this;
                axios.get("/productsByUserWarehouse/edit/"+ app.$route.params.id).then(({ data }) => (app.products = data.data))
                .then(function() {
                    app.getSale(app.sale_id);
                });
                
            } else {
                //this.getMaxId();
                this.initProducts();
            };

            this.form.invoice_date = moment().format("YYYY-MM-DD");
        },
        mounted() {

            $("#loading").hide();
            let app = this;            
            
           // app.initWarehouses();

            app.initCustomers();

            app.initSaleMen();

            $("#sale_man").select2();

            $("#sale_man").on("select2:select", function(e) {

                var data = e.params.data;
                app.form.office_sale_man_id = data.id;
            });

            app.initBrands();          
            app.initCategories();
            app.initUoms();
            $(".txt_product").select2();

            $("#customer_id").select2();
            $("#customer_id").on("select2:select", function(e) {

                var data = e.params.data;
                app.form.customer_id = data.id;

                //get customer's previous balance
                axios.get("/customer_previous_balance/"+data.id).then(({ data }) => (app.form.previous_balance = data.previous_balance));

                axios.get("/customer_sale_orders/"+data.id).then(({ data }) => (app.sale_orders = data.data));
                    $("#sale_order").select2();

                $('#order_product_table tbody tr').slice(0, -6).remove(); 

                if(app.form.sale_order == true) {
                    app.sale_orders = [];  
                    app.sale_order_approvals = [];
                   // $('#order_product_table tbody tr').slice(0, -3).remove();
                     
                    app.form.cash_discount = '';
                    app.form.net_total = '';
                    app.form.tax = '';
                    app.form.tax_amount = ''; 
                    app.form.sub_total = '';
                    app.form.pay_amount = '';
                    app.form.balance_amount = '';
                    app.form.office_sale_man_id = '';
                    $('#sale_man').val('').trigger('change');
                }

            });

            $(".unit_price_select").select2();

            $(".unit_price_select").on("select2:select", function(e) {

                var data = e.params.data;
                app.calTotalAmount($(this));
            });

            $("#sale_order").select2();
            $("#sale_order").on("select2:select", function(e) {
                //app.form.office_sale_man_id =  e.target.options[e.target.options.selectedIndex].dataset.saleman;
               // $('#order_product_table tbody tr').slice(0, -3).remove(); 

                var data = e.params.data;
                if(data.id != '') {
                    app.getOrder(data.id);
                } else {
                    $('#order_product_table tbody tr').slice(0, -6).remove();  
                    app.form.cash_discount = '';
                    app.form.net_total = '';
                    app.form.tax = '';
                    app.form.tax_amount = ''; 
                    app.form.sub_total = '';
                    app.form.pay_amount = '';
                    app.form.balance_amount = '';
                }
            });

            $(".brands").select2({ width: 'resolve' });
            $(".brands").on("select2:select", function(e) {
                var data = e.params.data;
                var brand_id = data.id;

                var row_id = $(this).closest('tr').attr('id');

                var cat_id = $("#category_"+row_id).find(':selected').val();
                var product_select_id = $("#product_"+row_id);
                if(brand_id != "") {
                    app.filterCategories(brand_id);
                } else {
                    app.initCategories();
                }
                app.filterProducts(brand_id,cat_id,product_select_id);
            });

            $(".categories").select2();
            $(".categories").on("select2:select", function(e) {

                var data = e.params.data;
                var cat_id = data.id;

                var row_id = $(this).closest('tr').attr('id');
                var brand_id = $("#brand_"+row_id).find(':selected').val();
                var product_select_id =  $("#product_"+row_id);
                app.filterProducts(brand_id,cat_id,product_select_id);
            });

            $(".txt_product").on("select2:select", function(e) {
                var data = e.params.data;

                app.selling_uoms = [];

                var row_id = $(this).closest('tr').attr('id');

               var uom      = e.target.options[e.target.options.selectedIndex].dataset.uom;
               var uom_id   = e.target.options[e.target.options.selectedIndex].dataset.uomid;
               var price    = e.target.options[e.target.options.selectedIndex].dataset.price;

                //$(this).closest('td').next().next().find('.txt_uom').attr('data-uom',uom);
                $("#uom_"+row_id).attr('data-uom',uom);

                //auto add new product row
                /**if($(this).closest('tr').next().hasClass("total_row")) {
                    app.addProduct();
                }**/

               //add Warehouse UOM Selling Price
               // $(this).closest('td').next().next().next().next().next().find('input').val(price);
               $("#rate_"+row_id).val(price);
               $("#actual_rate_"+row_id).val(price);

                //var selectbox_id = $(this).closest('td').next().next().find('.txt_uom');
                var selectbox_id = $("#uom_"+row_id);

                selectbox_id.find('option').remove();

                //add pre-defined product uom 

                if(selectbox_id.find('option[value="'+uom_id+'"]').text() == "") {
                    selectbox_id.append('<option value="">Select One</option><option value="'+uom_id+'" data-relation="" data-uomqty = "1" data-productuom = "'+uom+'" data-productid="'+data.id+'" data-perprice="'+price+'" data-price="'+price+'" selected>'+uom+'</option>'); 
                }
                $(".txt_uom").select2();
                app.calTotalAmount($(this));
                //app.getSellingUomByProduct(selectbox_id, data.id);
            });

            $(".txt_uom").select2();

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
                .on("dp.change", function(e) {
                    var formatedValue = e.date.format("YYYY-MM-DD");
                    //console.log(formatedValue);
                    app.form.invoice_date = formatedValue;
                    app.form.due_date = '';
                    app.form.credit_day = '';

                    $('#due_date').data("DateTimePicker").minDate(e.date);
                });

                $("#due_date")
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
                    minDate: app.form.invoice_date,
                })
                .on("dp.change", function(e) {
                    var formatedValue = e.date.format("YYYY-MM-DD");
                    //console.log(formatedValue);
                    app.form.due_date = formatedValue;
                    var date1 = new Date(app.form.invoice_date); 
                    var date2 = new Date(app.form.due_date); 
                    // To calculate the time difference of two dates 
                    var Difference_In_Time = date2.getTime() - date1.getTime(); 
                      
                    // To calculate the no. of days between two dates 
                    var Difference_In_Days = Difference_In_Time / (1000 * 3600 * 24); 
                    app.form.credit_day = Difference_In_Days;
                    
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
                            sub_total += parseFloat(document.getElementsByName('total_amount[]')[i].value);
                        }
                   }
                   var cash_discount = app.form.cash_discount == '' || app.form.cash_discount == null ? 0 : app.form.cash_discount;

                   app.form.sub_total = Math.round(sub_total);

                   app.form.net_total = parseInt(app.form.sub_total) - parseInt(cash_discount);

                    var tax = app.form.tax == '' || app.form.tax == null ? 0 : app.form.tax;
                    var tax_amount = parseInt(tax)/100 * parseInt(app.form.net_total);
                    app.form.tax_amount = tax_amount;
                    var pay_amount = app.form.pay_amount == '' || app.form.pay_amount == null ? 0 : app.form.pay_amount;
                        if(app.form.payment_type == 'cash') {
                            app.form.pay_amount = parseInt(app.form.net_total) + parseInt(app.form.tax_amount);
                            app.form.balance_amount = 0;
                        } else {
                            app.form.balance_amount = (parseInt(app.form.net_total) + parseInt(app.form.tax_amount)) - parseInt(pay_amount);
                        }                    
       
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
                                    sub_total += parseFloat(document.getElementsByName('total_amount[]')[i].value);
                                }
                           }
                           var cash_discount = app.form.cash_discount == '' || app.form.cash_discount == null ? 0 : app.form.cash_discount;

                           app.form.sub_total = Math.round(sub_total);

                           app.form.net_total = parseInt(app.form.sub_total) - parseInt(cash_discount);

                            var tax = app.form.tax == '' || app.form.tax == null ? 0 : app.form.tax;
                            var tax_amount = parseInt(tax)/100 * parseInt(app.form.net_total);
                            app.form.tax_amount = tax_amount;
                            var pay_amount = app.form.pay_amount == '' || app.form.pay_amount == null ? 0 : app.form.pay_amount;
                            if(app.form.payment_type == 'cash') {
                                app.form.pay_amount = parseInt(app.form.net_total) + parseInt(app.form.tax_amount);
                                app.form.balance_amount = 0;
                            } else {
                                app.form.balance_amount = (parseInt(app.form.net_total) + parseInt(app.form.tax_amount)) - parseInt(pay_amount);
                            } 
                        }   
                    } else {
                      //
                    }
                });
            });
           
        },

        methods: {

            initSaleMen() {
              axios.get("/sale_men").then(({ data }) => (this.sale_men = data.data));
              $("#sale_man").select2();
            },

            getOrder(id) {
                $("#loading").show();
                $('#order_product_table tbody tr').slice(0, -6).remove();
              let app = this;
              axios
                .get("/order/" + id)
                .then(function(response) {   
                    app.ex_products = response.data.order.products;

                    //add  products dynamically
                    var subTotal = 0;
                    var row_id = 0;

                    $.each(app.ex_products, function( key, product ) {  
                        row_id = row_id+1;  
                        var bal_product = 0;
                        var p_qty = product.pivot.product_quantity;                        
                        var accept_qty = product.pivot.accepted_quantity == null ? 0 : product.pivot.accepted_quantity;

                        bal_product = parseInt(p_qty) - parseInt(accept_qty); 

                        if((app.user_role != "Country Head" || (app.user_role == "Country Head" && response.data.access_brands.indexOf(product.brand_id) > -1)) && bal_product > 0) {
                            var table=document.getElementById("order_product_table");
                            var row=table.insertRow((table.rows.length) - 6);
                            row.id = row_id;
                            var cell1=row.insertCell(0);

                            var t1=document.createElement("select");
                                t1.name = "product[]";
                                t1.id = "product_"+row_id;
                                t1.className = "form-control txt_product";
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
                                t2.id = "qty_"+row_id;
                                if(product.pivot.accepted_quantity == null) {
                                    var accept_qty = 0;
                                } else {
                                    var accept_qty = product.pivot.accepted_quantity;
                                }
                                var qty = parseInt(product.pivot.product_quantity) - parseInt(accept_qty);
                                t2.value = qty;
                                t2.style = "width:100px;";
                                t2.className ="form-control num_txt";
                                $(t2).attr("required", true);

                                if(app.order_status != 'Draft' && app.order_status != '') {
                                    $(t2).attr('readonly', true);
                                }

                               // t2.addEventListener('blur', function(){ app.checkQty(t2); });
                                cell2.appendChild(t2);

                            var cell_accept_qty=row.insertCell(2);
                            var accept_qty=document.createElement("input");
                                accept_qty.name = "accept_qty[]";
                                accept_qty.id = "accept_qty_"+row_id;
                                accept_qty.value = '';
                                accept_qty.style = "width:100px;";
                                accept_qty.className ="form-control num_txt";
                                $(accept_qty).attr("required", true);

                                accept_qty.addEventListener('blur', function(){ app.checkQty(accept_qty); });
                                cell_accept_qty.appendChild(accept_qty);                            
                               
                            var cell3=row.insertCell(3);

                            var t3=document.createElement("select");
                                t3.name = "uom[]";
                                t3.id = "uom_"+row_id;
                                t3.className = "form-control txt_uom";
                                t3.style = "width:150px;";
                                $(t3).attr("required", true);

                                /**if(app.order_status != 'Draft' && app.order_status != '') {
                                    $(t3).attr('disabled', 'disabled');
                                }**/
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


                            var cell4=row.insertCell(4);
                            var rate=document.createElement("input");
                                rate.name = "rate[]";
                                rate.id = "rate_"+row_id;
                                rate.style = "width:100px;";
                                rate.value = product.pivot.rate;
                                rate.className ="form-control num_txt";
                                $(rate).attr("required", true);
                                if(product.pivot.is_foc == 1) {
                                    $(rate).attr("readonly", true);
                                }
                                rate.addEventListener('blur', function(){ app.calTotalAmount(rate); });
                                cell4.appendChild(rate);

                            var cell_discount=row.insertCell(5);
                            var discount=document.createElement("input");
                                discount.name = "discount[]";
                                discount.id = "discount_"+row_id;
                                discount.value = product.pivot.discount == null ? '' : product.pivot.discount;
                                discount.style = "width:70px;";
                                discount.className ="form-control num_txt";
                                if(product.pivot.is_foc == 1) {
                                    $(discount).attr("readonly", true);
                                }
                                discount.addEventListener('blur', function(){ app.calTotalAmount(discount); });
                                cell_discount.appendChild(discount);

                            var cell_actual=row.insertCell(6);
                            var actual_rate=document.createElement("input");
                                actual_rate.name = "actual_rate[]";
                                actual_rate.id = "actual_rate_"+row_id;
                                actual_rate.value = product.pivot.actual_rate;
                                actual_rate.style = "width:100px;";
                                actual_rate.className ="form-control num_txt";
                                $(actual_rate).attr("required", true);
                                $(actual_rate).attr("readonly", true);
                                cell_actual.appendChild(actual_rate);

                            

                            $(".txt_product").select2();

                            $(".txt_uom").select2();

                            $("#customer_id").select2();
                            $("#customer_id").on("select2:select", function(e) {

                                var data = e.params.data;
                                app.form.customer_id = data.id;

                                //get customer's previous balance
                                axios.get("/customer_previous_balance/"+data.id).then(({ data }) => (app.form.previous_balance = data.previous_balance));
                            });                            

                            var cell5=row.insertCell(7);
                                cell5.className = "text-center";
                            var t5=document.createElement("input");
                                t5.type = "checkbox";
                                t5.name = "chk_foc[]";
                                t5.id = "foc_"+row_id;
                                if(product.pivot.is_foc == '1') {
                                    $(t5). prop("checked", true);
                                }
                                $(t5).attr('disabled','disabled');
                                /**if(app.order_status != 'Draft' && app.order_status != '') {
                                    $(t5).attr('disabled','disabled');
                                }**/
                                t5.addEventListener('change', function(){ app.checkFoc(t5); });
                                cell5.appendChild(t5);

                            var cell_other_disc=row.insertCell(8);
                            var other_discount=document.createElement("input");
                                other_discount.name = "other_discount[]";
                                other_discount.id = "other_discount_"+row_id;
                                other_discount.style = "width:70px;";
                                other_discount.value = product.pivot.other_discount == null ? '' : product.pivot.other_discount;
                                if(product.pivot.is_foc == 1) {
                                    $(other_discount).attr("readonly", true);
                                }
                                other_discount.className ="form-control num_txt";
                                other_discount.addEventListener('blur', function(){ app.calTotalAmount(other_discount); });
                                cell_other_disc.appendChild(other_discount);

                            var cell7=row.insertCell(9);
                            var t7=document.createElement("input");
                                t7.name = "total_amount[]";
                                t7.id = "total_amount_"+row_id;
                                t7.style = "width:100px;";
                                if(product.pivot.total_amount != 0 && product.pivot.total_amount != null) {
                                    t7.value = product.pivot.total_amount;
                                    subTotal += parseInt(product.pivot.total_amount);
                                }
                                t7.className ="form-control num_txt";
                                $(t7).attr("required", true);
                                $(t7).attr("readonly", true);
                               // t2.addEventListener('blur', function(){ app.checkQty(t2); });
                                cell7.appendChild(t7);

                            var cell8=row.insertCell(10);
                            cell8.className = "text-center";
                            /**if(app.user_role != 'admin' && (app.order_status == 'Draft' || app.order_status == ''))
                            {**/
                                var row_action = "<a class='remove-row red-icon' title='Remove'><i class='fas fa-times-circle' style='font-size: 25px;'></i></a>";
                            //}
                            $(cell8).append(row_action);

                            $(".txt_product").on("select2:select", function(e) {

                                var data = e.params.data;

                                app.selling_uoms = [];

                                var row_id = $(this).closest('tr').attr('id');

                               var uom      = e.target.options[e.target.options.selectedIndex].dataset.uom;
                               var uom_id   = e.target.options[e.target.options.selectedIndex].dataset.uomid;
                               var price    = e.target.options[e.target.options.selectedIndex].dataset.price;

                                //$(this).closest('td').next().next().find('.txt_uom').attr('data-uom',uom);
                                $("#uom_"+row_id).attr('data-uom',uom);

                                //auto add new product row
                                /**if($(this).closest('tr').next().hasClass("total_row")) {
                                    app.addProduct();
                                }**/

                               //add Warehouse UOM Selling Price
                               // $(this).closest('td').next().next().next().next().next().find('input').val(price);
                               $("#rate_"+row_id).val(price);
                               $("#actual_rate_"+row_id).val(price);

                                //var selectbox_id = $(this).closest('td').next().next().find('.txt_uom');
                                var selectbox_id = $("#uom_"+row_id);

                                selectbox_id.find('option').remove();

                                //add pre-defined product uom 

                                if(selectbox_id.find('option[value="'+uom_id+'"]').text() == "") {
                                    selectbox_id.append('<option value="">Select One</option><option value="'+uom_id+'" data-relation="" data-uomqty = "1" data-productuom = "'+uom+'" data-productid="'+data.id+'" data-perprice="'+price+'" data-price="'+price+'" selected>'+uom+'</option>'); 
                                }
                                $(".txt_uom").select2();
                                app.calTotalAmount($(this));
                                //app.getSellingUomByProduct(selectbox_id, data.id);
                            });
                        }
                    });

                    $('#sale_man').val(response.data.order.sale_man_id).trigger('change');
                    app.form.office_sale_man_id = response.data.order.sale_man_id;

                    app.form.sub_total  = response.data.order.total_amount;
                    app.form.cash_discount  = response.data.order.cash_discount;
                    app.form.net_total     = response.data.order.net_total;
                    app.form.tax           = response.data.order.tax;
                    app.form.tax_amount    = response.data.order.tax_amount;
                    if(app.form.payment_type == 'cash') {
                        app.form.pay_amount = response.data.order.balance_amount;
                        app.form.balance_amount = 0;
                    } else {
                        app.form.balance_amount= response.data.order.balance_amount;
                    } 
                    
                    //app.form.pay_amount = response.data.order.pay_amount;
                    //app.form.previous_balance = response.data.previous_balance;
                    $("#loading").hide();

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

            initBrands() {
              axios.get("/brands").then(({ data }) => (this.brands = data.data));
              $(".brands").select2({ width: 'resolve' });
            },

            initCategories() {
              axios.get("/categories").then(({ data }) => (this.categories = data.data));
              $(".categories").select2();
            },

            filterCategories(brand_id) {
              axios.get("/categories_bybrand/"+brand_id).then(({ data }) => (this.categories = data.data));
              $(".categories").select2();
            },

            initUoms() {
              axios.get("/uoms").then(({ data }) => (this.uoms = data.data));

            },

            checkSO(obj) {
                let app = this;
                var is_so = $(obj).prop("checked");
                if(is_so){
                    app.form.sale_order = true;
                    $("#so_list").show();
                    $("#sale_order").attr("required", true);
                } else {
                    location.reload();
                }
            },

            changePayment() {
                if(this.form.payment_type == 'credit') {
                    $("#pay_amount").attr('readonly',false);
                    this.form.pay_amount = 0;
                    
                } else {
                    $("#pay_amount").attr('readonly',true);     
                }
                this.changePaidAmount();
            },

            calcDueDate() { 
                var startdate = this.form.invoice_date;
                var new_date = moment(startdate, "YYYY-MM-DD").add('days', this.form.credit_day);

                var day = new_date.format('DD');
                var month = new_date.format('MM');
                var year = new_date.format('YYYY');

                this.form.due_date = year+'-'+month+'-'+day;
            },

            initProducts() {
              let app = this;
              if(app.$route.params.id) {
                axios.get("/productsByUserWarehouse/edit/"+ app.$route.params.id).then(({ data }) => (app.products = data.data));
              } else {
                axios.get("/productsByUserWarehouse/create/null").then(({ data }) => (app.products = data.data));
              }
              $(".txt_product").select2();
            },

            filterProducts(brand_id,cat_id,product_select_id) {
                let app = this;
                var row_id = product_select_id.closest('tr').attr('id');

                //unset values
                $("#qty_"+row_id).val('');
                $("#relation_"+row_id).val('');
                $("#unit_price_"+row_id).val('');
                $("#stock_available_"+row_id).val('');
                $("#uom_"+row_id).find('option').remove();
                $("#uom_"+row_id).append('<option value="">Select One</option>');
                $("#unit_price_"+row_id).find('option').remove();
                $("#unit_price_"+row_id).append('<option value="">Select One</option>'); 
                $("#total_amount"+row_id).val('');

                app.calTotalAmount($("#unit_price_"+row_id));

                if(brand_id == '' && cat_id == '') {
                    product_select_id.find('option').remove();
                    var html = $('#txt_product').html();
                    product_select_id.html(html);
                }
                else {
                    product_select_id.find('option').remove();

                    var data ="&brand_id=" + brand_id + "&cat_id=" + cat_id;

                    if(app.$route.params.id) {

                        axios.get("/filterProductsByUserWarehouse/edit/"+ app.$route.params.id +"?" + data).then(function(response) {
                            var products = response.data.data;
                            var options = "<option value=''>Select One</option>";

                            $.each(products, function( key, prod ) {

                                options += "<option value='"+prod.product_id+"' data-uom='"+prod.uom_name+"' data-price='"+prod.product_price+"' data-retail1='"+prod.retail1_price+"'  data-retail2='"+prod.retail2_price+"' data-wholesale='"+prod.wholesale_price+"' data-uomid='"+prod.uom_id+"' data-pivotid = '0'>"+prod.product_name+"</option>";   
                            });

                            product_select_id.html(options);   
                        });

                    } else {

                        axios.get("/filterProductsByUserWarehouse/create/null?" + data).then(function(response) {
                            var products = response.data.data;
                            var options = "<option value=''>Select One</option>";

                            $.each(products, function( key, prod ) {
                            
                                options += "<option value='"+prod.product_id+"' data-uom='"+prod.uom_name+"' data-price='"+prod.product_price+"' data-retail1='"+prod.retail1_price+"'  data-retail2='"+prod.retail2_price+"' data-wholesale='"+prod.wholesale_price+"' data-uomid='"+prod.uom_id+"' data-pivotid = '0'>"+prod.product_name+"</option>";   
                            });
                            console.log(product_select_id.html);
                            product_select_id.html(options);
                        });
                    }
                }               

            },

            initWarehouses() {
              axios.get("/warehouses").then(({ data }) => (this.warehouses = data.data));
              $("#to_warehouse").select2();
            },

            initCustomers() {
              axios.get("/customers").then(({ data }) => (this.customers = data.data));
              $("#customer_id").select2();
            },

            getSellingUomByProduct(selectbox_id,product_id) {
                let app = this;
              axios.get("/product/selling_uom/"+ product_id).then(function(response) {
                var uom_arr = response.data.s_uoms.selling_uoms;
                var uom_relation = "";
                $.each(uom_arr, function(index, value) {
                    uom_relation = "1 "+value.uom_name+" = "+ value.pivot.relation +" "+ selectbox_id.attr('data-uom');
                    if(selectbox_id.find('option[value="'+value.pivot.uom_id+'"]').text() == "") {
                        selectbox_id.append('<option value="'+value.pivot.uom_id+'" data-relation="'+uom_relation+'" data-uomqty="'+value.pivot.relation+'" data-productuom = "'+selectbox_id.attr('data-uom')+'" data-productid="'+product_id+'" data-perprice="'+value.pivot.per_warehouse_uom_price+'" data-price="'+value.pivot.product_price+'"  data-retail1="'+value.pivot.retail1_price+'" data-retail2="'+value.pivot.retail2_price+'" data-wholesale="'+value.pivot.wholesale_price+'" >'+value.uom_name+'</option>');
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

                var max = 0;
                $('#product_table tbody tr').each(function(){
                    max = $(this).attr('id') > max ? $(this).attr('id') : max;
                });

                //var max = $('#product_table tbody tr').sort(function(a, b) { return +a.id < +b.id })[0].id;
                var row_id = parseInt(max) +1;

                let app = this;
                var table=document.getElementById("product_table");
                var row=table.insertRow((table.rows.length)-6);
                var cell1=row.insertCell(0);
                    row.id = row_id;
                var t1=document.createElement("select");
                    t1.name = "product[]";
                    t1.id = "product_"+row_id;
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
                    t2.id = "qty_"+row_id;
                    t2.style = "width:100px;";
                    t2.className ="form-control num_txt";
                    $(t2).attr("required", true);
                    t2.addEventListener('blur', function(){ app.checkQty(t2); });
                    cell2.appendChild(t2);
                   
                var cell3=row.insertCell(2);

                var t3=document.createElement("select");
                    t3.name = "uom[]";
                    t3.id = "uom_"+row_id;
                    t3.className = "form_control txt_uom";
                    t3.style = "min-width:150px;";
                    $(t3).attr("required", true);
                    //t3.addEventListener('blur', function(){ app.checkQty(t3); });

                    var option = document.createElement("option");
                    option.value = '';
                    option.text = "Select One";
                    $(option).attr('data-relation',"");
                    $(option).attr('data-price', "");
                    $(option).attr('data-perprice', "");
                    t3.append(option);

                 cell3.appendChild(t3);


                var cell4=row.insertCell(3);
                var rate=document.createElement("input");
                    rate.name = "rate[]";
                    rate.id = "rate_"+row_id;
                    rate.style = "width:100px;";
                    rate.className ="form-control num_txt";
                    $(rate).attr("required", true);
                    rate.addEventListener('blur', function(){ app.calTotalAmount(rate); });
                    cell4.appendChild(rate);

                var cell_discount=row.insertCell(4);
                var discount=document.createElement("input");
                    discount.name = "discount[]";
                    discount.id = "discount_"+row_id;
                    discount.style = "width:70px;";
                    discount.className ="form-control num_txt";
                    discount.addEventListener('blur', function(){ app.calTotalAmount(discount); });
                    cell_discount.appendChild(discount);

                var cell_actual=row.insertCell(5);
                var actual_rate=document.createElement("input");
                    actual_rate.name = "actual_rate[]";
                    actual_rate.id = "actual_rate_"+row_id;
                    actual_rate.style = "width:100px;";
                    actual_rate.className ="form-control num_txt";
                    $(actual_rate).attr("required", true);
                    $(actual_rate).attr("readonly", true);
                    actual_rate.addEventListener('blur', function(){ app.calTotalAmount(actual_rate); });
                    cell_actual.appendChild(actual_rate);
                

                $(".txt_product").select2();

                $(".txt_uom").select2();

                $("#customer_id").select2();
                $("#customer_id").on("select2:select", function(e) {

                    var data = e.params.data;
                    app.form.customer_id = data.id;
                });

                var cell5=row.insertCell(6);
                    cell5.className = "text-center";
                var t5=document.createElement("input");
                    t5.type = "checkbox";
                    t5.name = "chk_foc[]";
                    t5.id = "foc_"+row_id;
                    t5.addEventListener('change', function(){ app.checkFoc(t5); });
                    cell5.appendChild(t5);

                var cell_other_disc=row.insertCell(7);
                var other_discount=document.createElement("input");
                    other_discount.name = "other_discount[]";
                    other_discount.id = "other_discount_"+row_id;
                    other_discount.style = "width:70px;";
                    other_discount.className ="form-control num_txt";
                    other_discount.addEventListener('blur', function(){ app.calTotalAmount(other_discount); });
                    cell_other_disc.appendChild(other_discount);

                var cell7=row.insertCell(8);
                var t7=document.createElement("input");
                    t7.name = "total_amount[]";
                    t7.id = "total_amount_"+row_id;
                    t7.style = "width:100px;";
                    t7.className ="form-control num_txt";
                    $(t7).attr("required", true);
                    $(t7).attr("readonly", true);
                   // t2.addEventListener('blur', function(){ app.checkQty(t2); });
                    cell7.appendChild(t7);

                var cell8=row.insertCell(9);
                cell8.className = "text-center";
                var row_action = "<a class='remove-row red-icon' title='Remove'><i class='fas fa-times-circle' style='font-size: 25px;'></i></a>";
                $(cell8).append(row_action);

                $(".txt_product").on("select2:select", function(e) {

                    var data = e.params.data;

                    app.selling_uoms = [];

                    var row_id = $(this).closest('tr').attr('id');

                   var uom      = e.target.options[e.target.options.selectedIndex].dataset.uom;
                   var uom_id   = e.target.options[e.target.options.selectedIndex].dataset.uomid;
                   var price    = e.target.options[e.target.options.selectedIndex].dataset.price;

                    //$(this).closest('td').next().next().find('.txt_uom').attr('data-uom',uom);
                    $("#uom_"+row_id).attr('data-uom',uom);

                    //auto add new product row
                    /**if($(this).closest('tr').next().hasClass("total_row")) {
                        app.addProduct();
                    }**/

                   //add Warehouse UOM Selling Price
                   // $(this).closest('td').next().next().next().next().next().find('input').val(price);
                   $("#rate_"+row_id).val(price);
                   $("#actual_rate_"+row_id).val(price);

                    //var selectbox_id = $(this).closest('td').next().next().find('.txt_uom');
                    var selectbox_id = $("#uom_"+row_id);

                    selectbox_id.find('option').remove();

                    //add pre-defined product uom 

                    if(selectbox_id.find('option[value="'+uom_id+'"]').text() == "") {
                        selectbox_id.append('<option value="">Select One</option><option value="'+uom_id+'" data-relation="" data-uomqty = "1" data-productuom = "'+uom+'" data-productid="'+data.id+'" data-perprice="'+price+'" data-price="'+price+'" selected>'+uom+'</option>'); 
                    }
                    $(".txt_uom").select2();
                    app.calTotalAmount($(this));
                    //app.getSellingUomByProduct(selectbox_id, data.id);
                });

            },

            getSale(id) {
              let app = this;
              $("#loading").show();
              axios
                .get("/sale/" + id)
                .then(function(response) {
                    //prevent to Edit (save button permission)
                    if(app.user_role == "admin" || app.user_role == "system") {
                        if(response.data.sale.collections.length == 0 && response.data.sale.deliveries.length == 0 && response.data.sale.delivery_approve == 0) {
                            app.isDisabled = false;
                        } else {
                            app.isDisabled = true;
                        }                        
                    } else {
                        app.isDisabled = true;
                    }
                    app.form.invoice_date = moment(response.data.sale.invoice_date).format('YYYY-MM-DD');
                    app.ex_products = response.data.sale.products;
                    console.log(response.data.sale.products);
                    app.form.invoice_no = response.data.sale.invoice_no;
                    //app.form.reference_no = response.data.sale.reference_no;
                    app.form.payment_type = response.data.sale.payment_type;

                    if(response.data.sale.payment_type == 'credit') {
                        $("#pay_amount").attr('readonly',false);
                    } else {
                        $("#pay_amount").attr('readonly',true);     
                    }
                   // app.form.previous_balance = response.data.previous_balance;
                    app.form.due_date = response.data.sale.due_date;
                    app.form.credit_day = response.data.sale.credit_day;
                    if(response.data.sale.office_sale_man_id != null) {
                        $('#sale_man').val(response.data.sale.office_sale_man_id).trigger('change');
                        app.form.office_sale_man_id = response.data.sale.office_sale_man_id;
                    } 

                    if(app.form.payment_type == 'credit') {
                        app.required_val = true;
                    } else {
                        app.required_val = false;
                    }
                    $("#customer_id").append('<option value="'+response.data.sale.customer_id+'" selected>'+response.data.sale.customer.cus_name+'</option>');
                    app.form.customer_id = response.data.sale.customer_id; 

                     if(response.data.sale.order_id != null) {
                        app.isDisabled = true;
                        app.form.sale_order = true;
                        app.SOEdit = true;
                        $("#is_so").prop("checked", true);                        
                        $("#so_list").show();

                        $("#sale_order").select2("destroy");
                        $('#sale_order option[value=""]').text(response.data.sale.order.order_no);
                        $('#sale_order').select2();

                        $('#sale_order').prop('disabled', true);
                    }
                    $("#is_so").attr("disabled", true);

                    $.each(app.ex_products, function( key, value ) {
                        app.form.ex_product_pivot.push(value.pivot.id);
                    });

                    //add products dynamically
                    var subTotal = 0;
                    var balAmount = 0;
                    var row_id = 0;
                    $.each(app.ex_products, function( key, product ) {                       
                        row_id = row_id+1;                      
                        if(app.user_role != "Country Head" || (app.user_role == "Country Head" && response.data.access_brands.indexOf(product.brand_id) > -1)) {
                            var table=document.getElementById("product_table");
                            var row=table.insertRow((table.rows.length) - 6);
                            row.id = row_id;
                            var cell1=row.insertCell(0);

                            var t1=document.createElement("select");
                                t1.name = "product[]";
                                t1.id = "product_"+row_id;
                                t1.className = "form-control txt_product";
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
                                t2.id = "qty_"+row_id;
                                t2.value = product.pivot.product_quantity;
                                t2.style = "width:100px;";
                                t2.className ="form-control num_txt";
                                $(t2).attr("required", true);

                                if(response.data.sale.order_id != null) {
                                    $(t2).attr('readonly', true);
                                }

                                t2.addEventListener('blur', function(){ app.calTotalAmount(t2); });
                                cell2.appendChild(t2);                            
                               
                            var cell3=row.insertCell(2);

                            var t3=document.createElement("select");
                                t3.name = "uom[]";
                                t3.id = "uom_"+row_id;
                                t3.className = "form-control txt_uom";
                                t3.style = "width:150px;";
                                $(t3).attr("required", true);

                                if(response.data.sale.order_id != null) {
                                    $(t3).attr('readonly', true);
                                }
                               // t3.addEventListener('blur', function(){ app.checkQty(t3); });

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
                            var rate=document.createElement("input");
                                rate.name = "rate[]";
                                rate.id = "rate_"+row_id;
                                rate.style = "width:100px;";
                                rate.value = product.pivot.rate;
                                rate.className ="form-control num_txt";
                                $(rate).attr("required", true);
                                if(product.pivot.is_foc == 1 || response.data.sale.order_id != null) {
                                    $(rate).attr("readonly", true);
                                }
                                rate.addEventListener('blur', function(){ app.calTotalAmount(rate); });
                                cell4.appendChild(rate);

                            var cell_discount=row.insertCell(4);
                            var discount=document.createElement("input");
                                discount.name = "discount[]";
                                discount.id = "discount_"+row_id;
                                discount.value = product.pivot.discount == null ? '' : product.pivot.discount;
                                discount.style = "width:70px;";
                                discount.className ="form-control num_txt";
                                if(product.pivot.is_foc == 1 || response.data.sale.order_id != null) {
                                    $(discount).attr("readonly", true);
                                }
                                discount.addEventListener('blur', function(){ app.calTotalAmount(discount); });
                                cell_discount.appendChild(discount);

                            var cell_actual=row.insertCell(5);
                            var actual_rate=document.createElement("input");
                                actual_rate.name = "actual_rate[]";
                                actual_rate.id = "actual_rate_"+row_id;
                                actual_rate.value = product.pivot.actual_rate;
                                actual_rate.style = "width:100px;";
                                actual_rate.className ="form-control num_txt";
                                $(actual_rate).attr("required", true);
                                $(actual_rate).attr("readonly", true);
                                actual_rate.addEventListener('blur', function(){ app.calTotalAmount(actual_rate); });
                                cell_actual.appendChild(actual_rate);

                            

                            $(".txt_product").select2();

                            $(".txt_uom").select2();

                            $("#customer_id").select2();
                            $("#customer_id").on("select2:select", function(e) {

                                var data = e.params.data;
                                app.form.customer_id = data.id;

                                //get customer's previous balance
                                axios.get("/customer_previous_balance/"+data.id).then(({ data }) => (app.form.previous_balance = data.previous_balance));
                            });                            

                            var cell5=row.insertCell(6);
                                cell5.className = "text-center";
                            var t5=document.createElement("input");
                                t5.type = "checkbox";
                                t5.name = "chk_foc[]";
                                t5.id = "foc_"+row_id;
                                if(product.pivot.is_foc == '1') {
                                    $(t5). prop("checked", true);
                                }

                                if(response.data.sale.order_id != null) {
                                    $(t5).attr('disabled','disabled');
                                }
                                t5.addEventListener('change', function(){ app.checkFoc(t5); });
                                cell5.appendChild(t5);

                            var cell_other_disc=row.insertCell(7);
                            var other_discount=document.createElement("input");
                                other_discount.name = "other_discount[]";
                                other_discount.id = "other_discount_"+row_id;
                                other_discount.style = "width:70px;";
                                other_discount.value = product.pivot.other_discount == null ? '' : product.pivot.other_discount;
                                if(product.pivot.is_foc == 1 || response.data.sale.order_id != null) {
                                    $(other_discount).attr("readonly", true);
                                }
                                other_discount.className ="form-control num_txt";
                                other_discount.addEventListener('blur', function(){ app.calTotalAmount(other_discount); });
                                cell_other_disc.appendChild(other_discount);

                            var cell7=row.insertCell(8);
                            var t7=document.createElement("input");
                                t7.name = "total_amount[]";
                                t7.id = "total_amount_"+row_id;
                                t7.style = "width:100px;";
                                if(product.pivot.total_amount != 0 && product.pivot.total_amount != null) {
                                    t7.value = product.pivot.total_amount;
                                    subTotal += parseInt(product.pivot.total_amount);
                                }
                                t7.className ="form-control num_txt";
                                $(t7).attr("required", true);
                                $(t7).attr("readonly", true);
                               // t2.addEventListener('blur', function(){ app.checkQty(t2); });
                                cell7.appendChild(t7);

                            var cell8=row.insertCell(9);
                            cell8.className = "text-center";
                            if(app.user_role != 'admin' && response.data.sale.order_id == null)
                            {
                                var row_action = "<a class='remove-row red-icon' title='Remove'><i class='fas fa-times-circle' style='font-size: 25px;'></i></a>";
                            }
                            $(cell8).append(row_action);

                            $(".txt_product").on("select2:select", function(e) {

                                var data = e.params.data;

                                app.selling_uoms = [];

                                var row_id = $(this).closest('tr').attr('id');

                               var uom      = e.target.options[e.target.options.selectedIndex].dataset.uom;
                               var uom_id   = e.target.options[e.target.options.selectedIndex].dataset.uomid;
                               var price    = e.target.options[e.target.options.selectedIndex].dataset.price;

                                //$(this).closest('td').next().next().find('.txt_uom').attr('data-uom',uom);
                                $("#uom_"+row_id).attr('data-uom',uom);

                                //auto add new product row
                                /**if($(this).closest('tr').next().hasClass("total_row")) {
                                    app.addProduct();
                                }**/

                               //add Warehouse UOM Selling Price
                               // $(this).closest('td').next().next().next().next().next().find('input').val(price);
                               $("#rate_"+row_id).val(price);
                               $("#actual_rate_"+row_id).val(price);

                                //var selectbox_id = $(this).closest('td').next().next().find('.txt_uom');
                                var selectbox_id = $("#uom_"+row_id);

                                selectbox_id.find('option').remove();

                                //add pre-defined product uom 

                                if(selectbox_id.find('option[value="'+uom_id+'"]').text() == "") {
                                    selectbox_id.append('<option value="">Select One</option><option value="'+uom_id+'" data-relation="" data-uomqty = "1" data-productuom = "'+uom+'" data-productid="'+data.id+'" data-perprice="'+price+'" data-price="'+price+'" selected>'+uom+'</option>'); 
                                }
                                $(".txt_uom").select2();
                                app.calTotalAmount($(this));
                                //app.getSellingUomByProduct(selectbox_id, data.id);
                            });
                        }
                    });

                    app.form.sub_total  = response.data.sale.total_amount;
                    app.form.cash_discount  = response.data.sale.cash_discount;
                    app.form.net_total     = response.data.sale.net_total;
                    app.form.tax           = response.data.sale.tax;
                    app.form.tax_amount    = response.data.sale.tax_amount;
                    app.form.pay_amount= response.data.sale.pay_amount;
                    app.form.balance_amount= response.data.sale.balance_amount;
                    app.form.previous_balance = response.data.previous_balance;
                    $("#loading").hide();


                })
                .catch(function(error) {
                  // handle error
                  console.log(error);
                })
                .then(function() {
                  // always executed
                  app.original_form = $("#sale_form").serialize();
                });

                $(".txt_uom").select2();
            },

             checkFoc(obj) {
                let app = this;
                var is_foc = $(obj).prop("checked");
                var row_id = $(obj).closest('tr').attr('id');
                if(is_foc) {

                   $("#rate_"+row_id).attr('readonly',true);
                   $("#discount_"+row_id).val('');
                   $("#discount_"+row_id).attr('readonly',true);
                   $("#actual_rate_"+row_id).val('');
                   $("#actual_rate_"+row_id).attr('readonly',true);
                   $("#actual_rate_"+row_id).attr('required',false);                   
                   $("#total_amount_"+row_id).val('');
                   $("#total_amount_"+row_id).attr('readonly',true);
                   $("#other_discount_"+row_id).val('');
                   $("#other_discount_"+row_id).attr('readonly',true);
                   $("#total_amount_"+row_id).attr('required',false);

                } else {

                   $("#rate_"+row_id).attr('readonly',false);
                   $("#discount_"+row_id).val('');
                   $("#discount_"+row_id).attr('readonly',false);
                   $("#actual_rate_"+row_id).val($("#rate_"+row_id).val());
                   $("#actual_rate_"+row_id).attr('required',true);
                   $("#total_amount_"+row_id).val('');
                   $("#other_discount_"+row_id).val('');
                   $("#other_discount_"+row_id).attr('readonly',false);
                   $("#total_amount_"+row_id).attr('required',true);
                }

                app.calTotalAmount(obj);

            },

            checkQty(obj) { 
                let app = this; 
                var row_id = $(obj).closest('tr').attr('id');
                if(app.form.sale_order == true) {
                    var so_qty = parseInt($("#qty_"+row_id).val());
                    var accept_qty = parseInt($("#accept_qty_"+row_id).val());
                    if(accept_qty > so_qty) {
                        swal("Warning!", "Accept quantity is more than Order quantity!", "warning");
                        $("#accept_qty_"+row_id).val('');
                        $("#accept_qty_"+row_id).focus();
                        return false;
                    }
                } 

                if(typeof obj.name !== 'undefined') {

                    //For quantity box onBlur Event

                    var product_id = $("#product_"+row_id).find(':selected').val();
                    var transfer_qty = obj.value;
                    var uom = $("#uom_"+row_id).find(':selected').val();   

                    var product_qty = 0; 
                    var uom_val = "";
                    var uom_name  = "";  
                    var p_uom_val = "";
                    var p_uom_name = "";
                    var p_qty = 0;            

                    if(product_id != "" && transfer_qty != "" && uom != "") {

                        //calculate same products quantity in product list
                        var row_no = '';
                        $(".txt_product").each(function() {
                            row_no = $(this).closest('tr').attr('id');
                            if(product_id == $(this).val()) {
                                p_uom_val  = $("#uom_"+row_no).find(':selected').attr('data-uomqty');
                                p_uom_name = $("#uom_"+row_no).find(':selected').attr('data-productuom');
                                if(app.form.sale_order == true) {
                                    p_qty =  $("#accept_qty_"+row_no).val();
                                } else {
                                    p_qty =  $("#qty_"+row_no).val();
                                }
                                
                                if(typeof p_uom_val !== "undefined" && typeof p_uom_name != "undefined" != "" && p_qty != "") {

                                    product_qty = parseFloat(product_qty) + (parseFloat(p_qty) * parseFloat(p_uom_val));
                                }
                            }
                        });

                        //uom_val  = $(obj).closest('td').next().find(':selected').attr('data-uomqty');
                        uom_name = $("#uom_"+row_id).find(':selected').attr('data-productuom');

                        //product_qty = parseInt(product_qty) + (parseInt(transfer_qty) * parseInt(uom_val));

                        var key = this.products.findIndex(x => x.product_id == product_id);
                        var available_qty = parseFloat(this.products[key].in_count) - parseFloat(this.products[key].out_count);
                        // console.log(product_qty);
                        // console.log("Available"+available_qty);
                        // if(parseInt(product_qty) > parseInt(available_qty)) {
                        //     swal("Warning!", "Not enough quantity! Availabel quantity is "+available_qty+" "+uom_name+".", "warning");
                        //     $(obj).focus(); obj.value='';
                        // }
                    }

                    //claculate total amount
                    app.calTotalAmount(obj);
                } else {

                    //For UOM box select Event

                    var product_qty = 0; 
                    var uom_val = "";
                    var uom_name  = "";  
                    var p_uom_val = "";
                    var p_uom_name = "";
                    var p_qty = 0;  

                    var product_id = $(obj).attr('data-productid');
                    var transfer_qty = $("#qty_"+row_id).val();
                    var uom = obj.value;                    

                    if(product_id != "" && transfer_qty != "" && uom != "") {

                        //calculate same products quantity in product list
                        var row_no = '';
                        $(".txt_product").each(function() {
                            row_no = $(this).closest('tr').attr('id');

                            if(product_id == $(this).val()) {
                                p_uom_val  = $("#uom_"+row_no).find(':selected').attr('data-uomqty');
                                p_uom_name = $("#uom_"+row_no).find(':selected').attr('data-productuom');
                                if(app.form.sale_order == true) {
                                    p_qty =  $("#accept_qty_"+row_no).val();
                                } else {
                                    p_qty =  $("#qty_"+row_no).val();
                                }
                                if(typeof p_uom_val !== "undefined" && typeof p_uom_name != "undefined" != "" && p_qty != "") {

                                    product_qty = parseFloat(product_qty) + (parseFloat(p_qty) * parseFloat(p_uom_val));
                                }
                            }
                        });

                        //var uom_val  = $(obj).attr('data-uomqty');
                        var uom_name = $(obj).attr('data-productuom');

                        //var product_qty = parseInt(transfer_qty) * parseInt(uom_val);

                        var key = this.products.findIndex(x => x.product_id == product_id);
                        var available_qty = parseFloat(this.products[key].in_count) - parseFloat(this.products[key].out_count);
                        if(product_qty > available_qty) {

                            swal("Warning!", "Not enough quantity! Availabel quantity is "+available_qty+" "+uom_name+".", "warning");

                            //$("#qty_"+row_id).focus(); // $("#qty_"+row_id).val('');
                        }
                    }
                    app.calTotalAmount(obj);
                }
            },

            calTotalAmount(obj) {
               let app = this;

               var row_id = $(obj).closest('tr').attr('id');
                if(app.form.sale_order == true && !app.isEdit) {
                    var qty = $("#accept_qty_"+row_id).val() == "" ? 0 : $("#accept_qty_"+row_id).val();
                }
                else {
                    var qty = $("#qty_"+row_id).val() == "" ? 0 : $("#qty_"+row_id).val();
                }
               var rate = $("#rate_"+row_id).val();
               var discount = $("#discount_"+row_id).val();
               var actual_discount = 0;
               var actual_rate = '';
               if(discount != '') {
                    actual_discount = parseInt(discount)/100 * parseInt(rate);
                    actual_rate = parseInt(rate) - actual_discount;
               } else {
                    actual_rate = $("#rate_"+row_id).val() == "" ? 0 : $("#rate_"+row_id).val();
               }
               $("#actual_rate_"+row_id).val(actual_rate);
               var other_discount = $("#other_discount_"+row_id).val();
               var amount = parseInt(qty) * parseInt(actual_rate);
               var discount_amount = 0;
                if(other_discount != "") {
                    discount_amount = parseInt(other_discount)/100 * amount;
                }
                    
               amount = parseInt(amount) - parseInt(discount_amount);

                if($("#foc_"+row_id).prop("checked")) {
                    $("#total_amount_"+row_id).val('0');
                } else {
                    $("#total_amount_"+row_id).val(Math.round(amount));
                }

                //Van Sale
                //var product_price = $(obj).closest('td').prev().prev().prev().find(':selected').attr('data-price');

                //office Sale
                //var product_price = $(obj).closest('td').prev().prev().prev().find(':selected').attr('data-perprice');
                var product_price = $("#uom_"+row_id).find(':selected').attr('data-perprice');

               /***if(product_price != '') {
                    var price_variant =  parseFloat(price) - parseFloat(product_price);
                    price_variant = price_variant.toFixed(2);
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
               }***/

               //get all sub total amount
               var sub_total = 0;
               for(var i=0; i<document.getElementsByName('product[]').length; i++) {
                    if(document.getElementsByName('total_amount[]')[i].value != "") {
                        sub_total += parseFloat(document.getElementsByName('total_amount[]')[i].value);
                    }
               }
               var cash_discount = app.form.cash_discount == '' || app.form.cash_discount == null ? 0 : app.form.cash_discount;

               app.form.sub_total = Math.round(sub_total);

               app.form.net_total = parseInt(app.form.sub_total) - parseInt(cash_discount);

                var tax = app.form.tax == '' || app.form.tax == null ? 0 : app.form.tax;
                var tax_amount = parseInt(tax)/100 * parseInt(app.form.net_total);
                app.form.tax_amount = tax_amount;
                var pay_amount = app.form.pay_amount == '' || app.form.pay_amount == null ? 0 : app.form.pay_amount;
                if(app.form.payment_type == 'cash') {
                    app.form.pay_amount = parseInt(app.form.net_total) + parseInt(app.form.tax_amount);
                    app.form.balance_amount = 0;
                } else {
                    app.form.balance_amount = (parseInt(app.form.net_total) + parseInt(app.form.tax_amount))-parseInt(pay_amount);
                } 
                
            },

            changeCashDiscount() {
                let app = this;
                var cash_discount = app.form.cash_discount == '' || app.form.cash_discount == null ? 0 : app.form.cash_discount;

                if(parseInt(cash_discount) > parseInt(app.form.sub_total)) {
                    swal("Warning!", "Cash discount is greater than total amount", "warning");
                    app.form.cash_discount = 0;
                    return false;
                }

                app.form.net_total = parseInt(app.form.sub_total) - parseInt(cash_discount);

                var tax = app.form.tax == '' || app.form.tax == null ? 0 : app.form.tax;
                var tax_amount = parseInt(tax)/100 * parseInt(app.form.net_total);
                app.form.tax_amount = tax_amount;
                var pay_amount = app.form.pay_amount == '' || app.form.pay_amount == null ? 0 : app.form.pay_amount;
                if(app.form.payment_type == 'cash') {
                    app.form.pay_amount = parseInt(app.form.net_total) + parseInt(app.form.tax_amount);
                    app.form.balance_amount = 0;
                } else {
                    app.form.balance_amount = (parseInt(app.form.net_total) + parseInt(app.form.tax_amount))-parseInt(pay_amount);
                }
            },

            changePaidAmount() {
                let app = this;
                var cash_discount = app.form.cash_discount == '' || app.form.cash_discount == null ? 0 : app.form.cash_discount;

                app.form.net_total = parseInt(app.form.sub_total) - parseInt(cash_discount);

                var tax = app.form.tax == '' || app.form.tax == null ? 0 : app.form.tax;
                var tax_amount = parseInt(tax)/100 * parseInt(app.form.net_total);
                app.form.tax_amount = tax_amount;
                var pay_amount = app.form.pay_amount == '' || app.form.pay_amount == null ? 0 : app.form.pay_amount;

                var total_pay = parseInt(app.form.net_total) + parseInt(app.form.tax_amount);
                if(parseInt(pay_amount) > parseInt(total_pay)) {
                    swal("Warning!", "Paid amount is greater than balance amount", "warning");
                    app.form.pay_amount = 0;
                    return false;
                }
                if(app.form.payment_type == 'cash') {
                    app.form.pay_amount = parseInt(app.form.net_total) + parseInt(app.form.tax_amount);
                    app.form.balance_amount = 0;
                } else {
                    app.form.balance_amount = (parseInt(app.form.net_total) + parseInt(app.form.tax_amount))-parseInt(pay_amount);
                }
            },

            changeTax() {
                let app = this;
                var tax = app.form.tax == '' || app.form.tax == null ? 0 : app.form.tax;
                var tax_amount = parseInt(tax)/100 * parseInt(app.form.net_total);
                app.form.tax_amount = tax_amount;
                var pay_amount = app.form.pay_amount == '' || app.form.pay_amount == null ? 0 : app.form.pay_amount;
                if(app.form.payment_type == 'cash') {
                    app.form.pay_amount = parseInt(app.form.net_total) + parseInt(app.form.tax_amount);
                    app.form.balance_amount = 0;
                } else {
                    app.form.balance_amount = (parseInt(app.form.net_total) + parseInt(app.form.tax_amount))-parseInt(pay_amount);
                }
                app.form.balance_amount = (parseInt(app.form.net_total) + parseInt(app.form.tax_amount)) - parseInt(pay_amount);
            },

            calBalance(obj) {
                let app = this;
                var pay_amount = 0;
                var discount = 0;
                if(obj.value != "") {
                    var pay_amount = obj.value;
                } 

                if(pay_amount > app.form.sub_total) {
                    swal("Warning!", "Pay amount is greater than sub total amount", "warning");
                } else {
                    app.form.pay_amount = obj.value;
                    if(app.form.discount == '') {
                        discount = 0;
                    } else {
                        discount = app.form.discount;
                    }
                    app.form.balance_amount = parseInt(app.form.sub_total) - (parseInt(pay_amount)+parseInt(discount));
                }
            },

            changeDiscount(obj) {
                let app = this;
                var discount = 0;
                var balance  = 0; 

                if(obj.value != "") {
                    discount = obj.value;
                } 
                if(app.form.payment_type == 'cash') {
                    app.form.pay_amount = parseInt(app.form.sub_total) - parseInt(discount);
                }

                if(discount > app.form.sub_total) {
                    swal("Warning!", "Discount amount is greater than sub total amount", "warning");
                } else {
                    app.form.balance_amount = parseInt(app.form.sub_total) - (parseInt(app.form.pay_amount)+parseInt(discount));
                }
            },

            onSubmit: function(event){
                let app = this;
                app.form.office_sale_man_id = $("#sale_man").val();
                $('#loading').show();
                if(app.form.cash_discount > app.form.sub_total || app.form.balance < 0 || app.form.pay_amount<0) {
                    swal("Warning!", "Invalid balance or Invalid cash discount or Invalid pay amount. Please check!", "warning");
                    $('#loading').hide();
                    return false;
                }
                app.form.product = [];
                app.form.uom = [];
                app.form.qty = [];
                app.form.unit_price = [];
                app.form.total_amount = [];

                app.form.rate = [];
                app.form.actual_rate = [];
                app.form.discount = [];
                app.form.other_discount = [];

                app.form.order_product_id = [];

                app.form.sale_type = app.sale_type;

                /**if(app.sale_type == 1) {
                    app.form.reference_no = $("#reference_no").val();
                } else {
                    app.form.reference_no = '';
                }**/

                app.form.payment_type = $("#payment_type").val();
                if(app.form.payment_type == 'credit') {
                    app.form.due_date = $("#due_date").val();
                    app.form.credit_day = $("#credit_day").val();
                } else {
                    app.form.due_date = '';
                    app.form.credit_day = '';
                }

                if (!this.isEdit) {

                    app.form.product = [];

                    for(var i=0; i<document.getElementsByName('product[]').length; i++) {
                        app.form.product.push(document.getElementsByName('product[]')[i].value);
                        app.form.uom.push(document.getElementsByName('uom[]')[i].value);

                        if(app.form.sale_order == true) {
                            app.form.qty.push(document.getElementsByName('accept_qty[]')[i].value);
                            app.form.order_product_id.push(document.getElementsByName('product[]')[i].options[document.getElementsByName('product[]')[i].options.selectedIndex].dataset.pivotid);
                        } else {
                            app.form.qty.push(document.getElementsByName('qty[]')[i].value);
                        }

                        app.form.total_amount.push(document.getElementsByName('total_amount[]')[i].value);

                        app.form.rate.push(document.getElementsByName('rate[]')[i].value);
                        app.form.actual_rate.push(document.getElementsByName('actual_rate[]')[i].value);
                        app.form.discount.push(document.getElementsByName('discount[]')[i].value);
                        app.form.other_discount.push(document.getElementsByName('other_discount[]')[i].value);
                        
                        if(document.getElementsByName('chk_foc[]')[i].checked == true) {
                            app.form.is_foc.push('1');
                        } else {
                            app.form.is_foc.push('0');
                        }
                        
                        if(app.form.sale_order == true) {
                            app.form.order_id = $("#sale_order").val();
                        }
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
                            $('#loading').hide();
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
                                    //window.open(app.site_path+'/generate_invoice/'+data.sale_id);
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
                      var error = '';
                      $("#loading").hide();
                      if(response.errors.reference_no){
                        error += response.errors.reference_no[0];
                        error += '\n';

                        swal({
                            title: "Are you sure?",
                            text: "Reference number already exist!",
                            icon: "warning",
                            buttons: true,
                            dangerMode: true
                            }).then(willDelete => {
                            if (willDelete) {
                                app.form.duplicate_ref_no = true;
                                app.onSubmit(event);  
                            } else {
                               
                            }
                        });

                      }

                      //swal("Warning!", error, "warning");                        

                    });
                } else {
                    //Edit entry details
                    app.edit_form = $("#sale_form").serialize();
                   
                    /**if(app.edit_form == app.original_form) {
                        swal("Warning!", "Please edit at least one field", "warning");
                        $("#loading").hide();
                    } else { **/
                        app.form.product_pivot = [];

                        app.form.product = [];
                        for(var i=0; i<document.getElementsByName('product[]').length; i++) {
                             app.form.product.push(document.getElementsByName('product[]')[i].value);
                            app.form.uom.push(document.getElementsByName('uom[]')[i].value);

                            if(app.form.sale_order == true) {
                                app.form.qty.push(document.getElementsByName('accept_qty[]')[i].value);
                                app.form.order_product_id.push(document.getElementsByName('product[]')[i].options[document.getElementsByName('product[]')[i].options.selectedIndex].dataset.pivotid);
                            } else {
                                app.form.qty.push(document.getElementsByName('qty[]')[i].value);
                            }

                            app.form.total_amount.push(document.getElementsByName('total_amount[]')[i].value);

                            app.form.rate.push(document.getElementsByName('rate[]')[i].value);
                            app.form.actual_rate.push(document.getElementsByName('actual_rate[]')[i].value);
                            app.form.discount.push(document.getElementsByName('discount[]')[i].value);
                            app.form.other_discount.push(document.getElementsByName('other_discount[]')[i].value);
                            
                            if(document.getElementsByName('chk_foc[]')[i].checked == true) {
                                app.form.is_foc.push('1');
                            } else {
                                app.form.is_foc.push('0');
                            }
                            
                            if(app.form.sale_order == true) {
                                app.form.order_id = $("#sale_order").val();
                            }

                            app.form.product_pivot.push(document.getElementsByName('product[]')[i].options[document.getElementsByName('product[]')[i].options.selectedIndex].dataset.pivotid);
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
                                $('#loading').hide();

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
                            var error = '';
                            if(response.errors.reference_no){
                                error += response.errors.reference_no[0];
                                error += '\n';
                                $("#loading").hide();
                                swal({
                                    title: "Are you sure?",
                                    text: "Reference number already exist!",
                                    icon: "warning",
                                    buttons: true,
                                    dangerMode: true
                                    }).then(willDelete => {
                                    if (willDelete) {
                                        app.form.duplicate_ref_no = true;
                                        app.onSubmit(event);   
                                    } else {
                                       
                                    }
                                });
                            }

                            //swal("Warning!", error, "warning");

                        });
                    //}
                }
            },

        }
    }
</script>