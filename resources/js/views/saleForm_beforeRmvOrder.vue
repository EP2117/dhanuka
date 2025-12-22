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
                    <form class="form" id="sale_form" method="post" @submit.prevent="onSubmit" @keydown="form.errors.clear($event.target.name)">
                        <div class="row">
                            <div class="form-group col-md-4">
                                <label for="invoice_no">Invoice No.</label>
                                <input type="text" class="form-control" id="invoice_no" name="invoice_no" v-model="form.invoice_no" readonly>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-md-4">
                                <label for="invoice_date">Date</label>
                                <input type="text" class="form-control datetimepicker" id="invoice_date" name="invoice_date"
                                v-model="form.invoice_date" required :readonly="SOEdit">
                            </div>                            
                        </div>

                        <div class="row mt-3">
                            <div class="form-group col-md-4" v-if="!form.sale_order">
                                <label for="office_sale_man">Sale Man</label>
                                <input type="text" class="form-control" id="office_sale_man" name="office_sale_man"
                                v-model="form.office_sale_man" readonly>        
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
                            </div>

                            <div class="form-group col-md-4" v-if="sale_type == 1">
                                <label for="reference_no">Reference No.</label>
                                 <input type="text" class="form-control" id="reference_no" name="reference_no"
                                v-model="form.reference_no">
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

                                    <option v-for="so in sale_orders" :data-saleman= "so.sale_man == null ? '' : so.sale_man.name" :value="so.id"  >{{so.order_no}}</option>
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

                            <div class="form-group col-md-4">
                                <label for="sale_order">Sale Man</label>
                                <input type="text" class="form-control" id="sale_man" name="sale_man"
                                v-model="form.sale_man" readonly>        
                            </div>

                            <div class="col-md-12 custom-control custom-switch" style="padding-left:10px;">
                              <label style='margin-right:50px;' class="ml-0">Revise Order</label>
                              <input type="checkbox" class="custom-control-input" id="is_revise" name="is_revise" @change="checkRevise($event.target)">                              
                              <label class="custom-control-label" for="is_revise"></label>
                            </div>
                           
                        </div>

                        <div class="row mt-4 mb-3">
                            <div class="col-md-12">
                                <span class="d-none d-sm-inline-block btn-sm btn-primary shadow-sm bg-blue"><i class="fas fa-search-plus text-white"></i> Product Details</span>
                            </div>
                        </div>

                        <div class="row mt-0" v-if="form.sale_order == false || isEdit">
                            <div class="col-md-12 text-right mt-0">
                                <a class='blue-icon' title='Add Product' @click="addProduct()" v-if="((user_role == 'admin' || user_role == 'system') && form.sale_order == false && !isDisabled) || ((user_role == 'admin' || user_role == 'system') && form.revise_order == true && !isDisabled) || (!isEdit)"><i class='fas fa-plus-square' style='font-size: 30px;'></i></a>
                                <div style="display:none;">
                                    <select class="form-control txt_product"
                                        id="txt_product" style="min-width:150px;"
                                    >
                                        <option value="">Select One</option>
                                        <option v-for="product in products" :data-uom="product.uom_name" 
                                        :data-price="product.product_price"
                                        :data-retail1="product.retail1_price"
                                        :data-retail2="product.retail2_price"
                                        :data-wholesale="product.wholesale_price"
                                        :data-uomid="product.uom_id" :value="product.product_id" 
                                        data-pivotid = "0">{{product.product_name}}</option>
                                    </select>
                                </div>
                                <div style="display:none;">
                                    <select class="form-control brands"
                                        id="txt_brand" style="min-width:100px;"
                                    >
                                        <option value="">Select One</option>
                                        <option v-for="brand in brands" :value="brand.id">{{brand.brand_name}}</option>
                                    </select>
                                </div>
                                <div style="display:none;">
                                    <select class="form-control categories"
                                        id="txt_category" style="min-width:100px;"
                                    >
                                        <option value="">Select One</option>
                                        <option v-for="cat in categories" :value="cat.id">{{cat.category_name}}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-12 table-responsive">
                                <table class="table table-bordered" id="product_table">
                                    <thead class="thead-grey">
                                        <tr>
                                            <th scope="col" >Brand</th>
                                            <th scope="col" >Category</th>
                                            <th scope="col" >Product Name</th>
                                            <th scope="col" >Quantity</th>
                                            <th scope="col" >Selling Unit</th>
                                            <th scope="col" >Relation</th>
                                            <th scope="col" >Stock Available</th>
                                            <th scope="col" v-if="sale_type == 2">Unit Price</th>
                                            <th scope="col" v-else>Warehouse UOM <br />Unit Price</th>
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
                                                <select class="form-control brands"
                                                    name="brand[]" style="min-width:100px;" 
                                                >
                                                    <option value="">Select One</option>
                                                    <option v-for="brand in brands" :value="brand.id">{{brand.brand_name}}</option>
                                                </select>
                                            </td>
                                            <td>
                                                <select class="form-control categories"
                                                    name="category[]" style="min-width:100px;" 
                                                >
                                                    <option value="">Select One</option>
                                                    <option v-for="cat in categories" :value="cat.id">{{cat.category_name}}</option>
                                                </select>
                                            </td>
                                            <td>
                                                <select class="form-control txt_product"
                                                    name="product[]" style="min-width:150px;" required
                                                >
                                                    <option value="">Select One</option>
                                                    <option v-for="product in products" :data-uom="product.uom_name" 
                                                    :data-price="product.product_price"
                                                    :data-retail1="product.retail1_price"
                                                    :data-retail2="product.retail2_price"
                                                    :data-wholesale="product.wholesale_price"
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
                                                <!--<input
                                                    type="checkbox"
                                                    name="chk_foc[]"
                                                    @change="checkFoc($event.target)"
                                                >-->
                                                <input type="text" style="min-width:200px;" class="form-control txt_available" name="stock_available[]" readonly />
                                            </td>
                                            <td>
                                                <!-- <input type="text" class="form-control float_num" style="width:100px;" name="unit_price[]" @blur="calTotalAmount($event.target)" required /> -->

                                                <select class="form-control float_num unit_price_select" style="width:100px;" name="unit_price[]" required >
                                                    <option value="">Select One</option>
                                                </select>
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
                                            <td colspan="8" class="text-right">Sub Total</td>
                                            <td colspan="2">
                                                <input type="text" v-model="form.sub_total" class="form-control num_txt" readonly style="width:150px;" required />
                                            </td>
                                        </tr> 
                                        <tr class="total_row">
                                            <td colspan="8" class="text-right">Discount</td>
                                            <td colspan="2">
                                                <input type="text" v-model="form.discount" class="form-control num_txt" style="width:150px;" @keyup="changeDiscount($event.target)" />
                                            </td> 
                                        </tr>                                       
                                        <tr class="total_row">
                                            <td colspan="8" class="text-right">Pay Amount</td>
                                            <td colspan="2" v-if="form.payment_type == 'credit'">
                                                <input type="text" v-model="form.pay_amount" class="form-control num_txt" style="width:150px;" @keyup="calBalance($event.target)" />
                                            </td>
                                            <td colspan="2" v-else>
                                                <input type="text" v-model="form.pay_amount" class="form-control num_txt" style="width:150px;" @keyup="calBalance($event.target)" readonly />
                                            </td>
                                        </tr>
                                        <tr class="total_row">
                                            <td colspan="8" class="text-right">Previous Balance</td>
                                            <td colspan="2">
                                                <input type="text" v-model="form.previous_balance" class="form-control num_txt" readonly style="width:150px;"/>
                                            </td>
                                        </tr>
                                        <tr class="total_row">
                                            <td colspan="8" class="text-right">Balance</td>
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
                                <a class='blue-icon' title='Add Product' @click="addProduct()" v-if="((user_role == 'system' || user_role=='admin') && form.revise_order == true && !isDisabled) || (!isEdit && (form.sale_order == false || form.revise_order == true))"><i class='fas fa-plus-square' style='font-size: 30px;'></i></a>
                                <div style="display:none;">
                                    <select class="form-control txt_product"
                                        id="txt_product" style="min-width:150px;"
                                    >
                                        <option value="">Select One</option>
                                        <option v-for="product in products" :data-uom="product.uom_name" 
                                        :data-price="product.product_price"
                                        :data-retail1="product.retail1_price"
                                        :data-retail2="product.retail2_price"
                                        :data-wholesale="product.wholesale_price"
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
                                            <th scope="col" >Quantity</th>
                                            <th scope="col" >Selling Unit</th>
                                            <th scope="col" >Relation</th>
                                            <th scope="col" >Stock Available</th>
                                            <th scope="col" v-if="sale_type == 2">Unit Price</th>
                                            <th scope="col" v-else>Warehouse UOM <br />Unit Price</th>
                                            <th scope="col" >Total Amount</th>
                                            <th scope="col"></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr class="total_row">
                                            <td colspan="8" class="text-right">Sub Total</td>
                                            <td colspan="2">
                                                <input type="text" v-model="form.sub_total" class="form-control num_txt" readonly style="width:150px;" required />
                                            </td>
                                        </tr>
                                        <tr class="total_row">
                                            <td colspan="8" class="text-right">Discount</td>
                                            <td colspan="2">
                                                <input type="text" v-model="form.discount" class="form-control num_txt"  style="width:150px;" @keyup="changeDiscount($event.target)" />
                                            </td>
                                        </tr>                                        
                                        <tr class="total_row">
                                            <td colspan="8" class="text-right">Pay Amount</td>
                                            <td colspan="2">
                                                <input type="text" v-model="form.pay_amount" class="form-control num_txt" style="width:150px;" @keyup="calBalance($event.target)" :readonly="!required_val" />
                                            </td>
                                        </tr>
                                        <tr class="total_row">
                                            <td colspan="8" class="text-right">Previous Balance</td>
                                            <td colspan="2">
                                                <input type="text" v-model="form.previous_balance" class="form-control num_txt" readonly style="width:150px;"/>
                                            </td>
                                        </tr>
                                        <tr class="total_row">
                                            <td colspan="8" class="text-right">Balance</td>
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
                            <div class="col-md-12" v-if="user_role != 'office_order_user' && !isEdit">
                                <input type="submit" class="btn btn-primary btn-sm" value="Save Entry" id="save_btn" :disabled = "isDisabled">
                            </div>

                            <div class="col-md-12" v-if="(user_role == 'system' || user_role == 'admin') && isEdit && !isDisabled"> 
                                <input type="submit" class="btn btn-primary btn-sm" value="Update" id="save_btn">
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
                office_sale_man: "",
                office_sale_man_id: "",
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
                payment_type: 'cash',
                credit_day: '',
                due_date: '',
                sale_man: '',
                revise_order: false,
                duplicate_ref_no: false,
                discount: '',
                previous_balance: '',

              }),              
              isEdit: false,
              brands: [],
              categories: [],
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
              user_year: '',
              SOEdit: false,
              required_val : false,
              isDisabled: false,
              original_form: '',
              edit_form: '',

            };
        },

        created() {

            this.user_year = document.querySelector("meta[name='user-year-likelink']").getAttribute('content');

            //sale_type = 2 for Van and 1 for Office Sale

            this.sale_type = this.$route.params.sale_type;
            this.user_warehouse = document.querySelector("meta[name='user-wh']").getAttribute('content');

            this.form.office_sale_man = document.querySelector("meta[name='user-name-likelink']").getAttribute('content');
            this.form.office_sale_man_id = document.querySelector("meta[name='user-id-likelink']").getAttribute('content');

            this.user_role = document.querySelector("meta[name='user-role']").getAttribute('content');
            if(this.user_role == "office_order_user") {
                //var url =  window.location.origin;
                //window.location.replace(url);
            }

            if(this.user_role == "admin" && !this.isEdit) {
                this.isDisabled = true;
            }

            this.initProducts();

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
            
            
           // app.initWarehouses();

            app.initCustomers();

            app.initBrands();          
            app.initCategories();

            $(".txt_product").select2();

            $("#sale_order").select2();
            $("#sale_order").on("select2:select", function(e) {  
                app.sale_order_approvals = [];
                app.form.sale_man =  e.target.options[e.target.options.selectedIndex].dataset.saleman;
                $('#order_product_table tbody tr').slice(0, -3).remove();       
                var data = e.params.data;
                axios.get("/sale_order_approval/"+ data.id).then(({ data }) => (app.sale_order_approvals = data.data));
            });

            $("#sale_order_approval").select2();
            $("#sale_order_approval").on("select2:select", function(e) {            
                var data = e.params.data;
                app.form.approval_id = data.id;
                app.getApproval(data.id);
            });

            $("#customer_id").select2();
            $("#customer_id").on("select2:select", function(e) {

                var data = e.params.data;
                app.form.customer_id = data.id;

                //get customer's previous balance
                axios.get("/customer_previous_balance/"+data.id).then(({ data }) => (app.form.previous_balance = data.previous_balance));

                /****app.sale_orders = [];  
                app.sale_order_approvals = [];
                $('#order_product_table tbody tr').slice(0, -3).remove();
                axios.get("/customer_sale_orders/"+data.id).then(({ data }) => (app.sale_orders = data.data));
                $("#sale_order").select2(); *****/
            });

            $(".unit_price_select").select2();

            $(".unit_price_select").on("select2:select", function(e) {

                var data = e.params.data;
                app.calTotalAmount($(this));
            });

            $(".brands").select2();
            $(".brands").on("select2:select", function(e) {
                var data = e.params.data;
                var brand_id = data.id;
                var cat_id = $(this).closest('td').next().find('select').find(':selected').val();
                var product_select_id = $(this).closest('td').next().next().find('select');
                app.filterProducts(brand_id,cat_id,product_select_id);
            });

            $(".categories").select2();
            $(".categories").on("select2:select", function(e) {

                var data = e.params.data;
                var cat_id = data.id;
                var brand_id = $(this).closest('td').prev().find('select').find(':selected').val();
                var product_select_id = $(this).closest('td').next().find('select');
                app.filterProducts(brand_id,cat_id,product_select_id);
            });

            $(".txt_product").on("select2:select", function(e) {

                var data = e.params.data;

                app.selling_uoms = [];

               var uom      = e.target.options[e.target.options.selectedIndex].dataset.uom;
               var uom_id   = e.target.options[e.target.options.selectedIndex].dataset.uomid;
               var price    = e.target.options[e.target.options.selectedIndex].dataset.price;

               var retail1_price    = e.target.options[e.target.options.selectedIndex].dataset.retail1;
               var retail2_price    = e.target.options[e.target.options.selectedIndex].dataset.retail2;
               var wholesale_price  = e.target.options[e.target.options.selectedIndex].dataset.wholesale;

                //$(this).closest('td').next().find('.txt_uom').val(uom);
                //$(this).closest('td').next().find('.txt_uom').attr('data-id',uom_id);

                $(this).closest('td').next().next().find('.txt_uom').attr('data-uom',uom);

                //auto add new product row
                if($(this).closest('tr').next().hasClass("total_row")) {
                    app.addProduct();
                }

               //add Warehouse UOM Selling Price
                //$(this).closest('td').next().next().next().next().next().find('input').val(price);
                var price_selectbox_id = $(this).closest('td').next().next().next().next().next().find('.unit_price_select');
                price_selectbox_id.find('option').remove();
                price_selectbox_id.append('<option value="">Select One</option>'); 
                if(retail1_price != null && retail1_price != '') {
                    price_selectbox_id.append('<option value="'+retail1_price+'" selected>'+retail1_price+'</option>');   
                }
                if(retail2_price != null && retail2_price != '') {
                    price_selectbox_id.append('<option value="'+retail2_price+'">'+retail2_price+'</option>');   
                }
                if(wholesale_price != null && wholesale_price != '') {
                    price_selectbox_id.append('<option value="'+wholesale_price+'">'+wholesale_price+'</option>');   
                }

                var selectbox_id = $(this).closest('td').next().next().find('.txt_uom');

                selectbox_id.find('option').remove();

                //add pre-defined product uom 

                if(selectbox_id.find('option[value="'+uom_id+'"]').text() == "") {
                    selectbox_id.append('<option value="">Select One</option><option value="'+uom_id+'" data-relation="" data-uomqty = "1" data-productuom = "'+uom+'" data-productid="'+data.id+'" data-perprice="'+price+'" data-price="'+price+'" data-retail1="'+retail1_price+'" data-retail2="'+retail2_price+'" data-wholesale="'+wholesale_price+'" selected>'+uom+'</option>'); 
                }
                $(".txt_uom").select2();

                var key = app.products.findIndex(x => x.product_id == data.id);
                var available_qty = parseInt(app.products[key].in_count) - parseInt(app.products[key].out_count);
                var available_id = $(this).closest('td').next().next().next().next().find('.txt_available').val(available_qty);

                app.getSellingUomByProduct(selectbox_id, data.id);
            });

            $(".txt_uom").select2();

            $(".txt_uom").on("select2:select", function(e) {
                app.checkQty(e.target.options[e.target.options.selectedIndex]);
                var uom_relation = e.target.options[e.target.options.selectedIndex].dataset.relation;
                if(app.sale_type == 2) {
                //for van sale
                    var uom_price = e.target.options[e.target.options.selectedIndex].dataset.price;
                    var uom_retail1_price = e.target.options[e.target.options.selectedIndex].dataset.retail1;
                    var uom_retail2_price = e.target.options[e.target.options.selectedIndex].dataset.retail2;
                    var uom_wholesale_price = e.target.options[e.target.options.selectedIndex].dataset.wholesale;
                } else {
                //for office sale
                    var uom_price = e.target.options[e.target.options.selectedIndex].dataset.perprice;
                    var uom_retail1_price = e.target.options[e.target.options.selectedIndex].dataset.retail1;
                    var uom_retail2_price = e.target.options[e.target.options.selectedIndex].dataset.retail2;
                    var uom_wholesale_price = e.target.options[e.target.options.selectedIndex].dataset.wholesale;
                }

                $(this).closest('td').next().find('.txt_relation').val(uom_relation);
                //$(this).closest('td').next().next().next().find('input').val(uom_price);
                var unit_price_selectbox_id = $(this).closest('td').next().next().next().find('.unit_price_select');
                unit_price_selectbox_id.find('option').remove();
                unit_price_selectbox_id.append('<option value="">Select One</option>');
                if(e.params.data.id != '') {
                    if(uom_retail1_price != 'null' && uom_retail1_price != '' && uom_retail1_price !== "undefined") {
                        unit_price_selectbox_id.append('<option value="'+uom_retail1_price+'" selected>'+uom_retail1_price+'</option>');   
                    }
                    if(uom_retail2_price != 'null' && uom_retail2_price != '' && uom_retail2_price !== "undefined") {
                        unit_price_selectbox_id.append('<option value="'+uom_retail2_price+'">'+uom_retail2_price+'</option>');   
                    }
                    if(uom_wholesale_price != 'null' && uom_wholesale_price != '' && uom_wholesale_price !== "undefined") {
                        unit_price_selectbox_id.append('<option value="'+uom_wholesale_price+'">'+uom_wholesale_price+'</option>');   
                    }
                }


                //app.calTotalAmount($(this).closest('td').next().next().next().find('input'));
                app.calTotalAmount(unit_price_selectbox_id);
            });

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
                            sub_total += parseInt(document.getElementsByName('total_amount[]')[i].value);
                        }
                    }

                    app.form.sub_total = sub_total;
                    var discount = 0;
                    if(app.form.discount == '' || sub_total == 0 || sub_total == '') {
                        discount = 0;
                    } else {
                        discount = app.form.discount;
                    }
                    app.form.pay_amount = paresInt((app.form.sub_total) - parseInt(discount));
                    app.form.balance_amount  = sub_total - (parseInt(app.form.pay_amount) + parseInt(discount)); 
   
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
                            var discount = 0;
                            if(app.form.discount == '' || sub_total == 0 || sub_total == '') {
                                discount = 0;
                            } else {
                                discount = app.form.discount;
                            }
                            app.form.pay_amount = paresInt((app.form.sub_total) - parseInt(discount));
                            app.form.balance_amount  = sub_total - (parseInt(app.form.pay_amount) + parseInt(discount)); 

                            //app.form.sub_total = sub_total;
                            //app.form.balance_amount  = sub_total - parseInt(app.form.pay_amount);  
                        }   
                    } else {
                      //
                    }
                });
            });
           
        },

        methods: {

            initBrands() {
              axios.get("/brands").then(({ data }) => (this.brands = data.data));
              $(".brands").select2();
            },

            initCategories() {
              axios.get("/categories").then(({ data }) => (this.categories = data.data));
              $(".categories").select2();
            },

            changePayment() {
                if(this.form.payment_type == 'credit') {
                    this.required_val = true;
                } else {
                    this.required_val = false;
                }
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
                let app = this;
                if(app.form.revise_order && app.form.sale_order && !app.isEdit) {
                    var table=document.getElementById("order_product_table");
                } else {
                    var table=document.getElementById("product_table");
                }
                var row=table.insertRow((table.rows.length)-5);

                var cell1=row.insertCell(0);
                var t1=document.createElement("select");
                    t1.name = "brand[]";
                    t1.className = "form-control brands";
                    t1.style = "min-width:100px;";

                    var option = document.createElement("option");
                    option.value = '';
                    option.text = "Select One";
                    t1.append(option);

                    var html = $('#txt_brand').html();
                    $(t1).html(html);                    
                    cell1.appendChild(t1);

                var cell2=row.insertCell(1);
                var t2=document.createElement("select");
                    t2.name = "category[]";
                    t2.className = "form-control categories";
                    t2.style = "min-width:100px;";

                    var option = document.createElement("option");
                    option.value = '';
                    option.text = "Select One";
                    t2.append(option);

                    var html = $('#txt_category').html();
                    $(t2).html(html);                    
                    cell2.appendChild(t2);

                var cell3=row.insertCell(2);

                var t3=document.createElement("select");
                    t3.name = "product[]";
                    t3.className = "form-control txt_product";
                    t3.style = "min-width:150px;";
                    $(t3).attr("required", true);

                    var option = document.createElement("option");
                    option.value = '';
                    option.text = "Select One";
                    t3.append(option);

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
                    $(t3).html(html);                    
                    cell3.appendChild(t3);

                var cell4=row.insertCell(3);
                var t4=document.createElement("input");
                    t4.name = "qty[]";
                    t4.style = "width:100px;";
                    t4.className ="form-control num_txt";
                    $(t4).attr("required", true);
                    t4.addEventListener('blur', function(){ app.checkQty(t4); });
                    cell4.appendChild(t4);
                   
                var cell5=row.insertCell(4);

                var t5=document.createElement("select");
                    t5.name = "uom[]";
                    t5.className = "form_control txt_uom";
                    t5.style = "min-width:150px;";
                    $(t5).attr("required", true);
                    t5.addEventListener('blur', function(){ app.checkQty(t5); });

                    var option = document.createElement("option");
                    option.value = '';
                    option.text = "Select One";
                    $(option).attr('data-relation',"");
                    $(option).attr('data-price', "");
                    $(option).attr('data-perprice', "");
                    t5.append(option);

                 cell5.appendChild(t5);


                var cell6=row.insertCell(5);
                var t6=document.createElement("input");
                    t6.type = "text";
                    t6.name = "relation[]";
                    t6.style = "min-width:200px;";
                    t6.className ="form-control txt_relation";
                    $(t6).attr('readonly', true);
                    cell6.appendChild(t6);
                

                $(".txt_product").select2();

                $(".txt_uom").select2();

                $(".unit_price_select").select2();

                $("#customer_id").select2();
                $("#customer_id").on("select2:select", function(e) {

                    var data = e.params.data;
                    app.form.customer_id = data.id;

                    //get customer's previous balance
                    axios.get("/customer_previous_balance/"+data.id).then(({ data }) => (app.form.previous_balance = data.previous_balance));

                    app.sale_orders = [];  
                    app.sale_order_approvals = [];
                    $('#order_product_table tbody tr').slice(0, -3).remove();
                    axios.get("/customer_sale_orders/"+data.id).then(({ data }) => (app.sale_orders = data.data));
                    $("#sale_order").select2();
                });

                $(".txt_product").on("select2:select", function(e) {
                    var data = e.params.data;

                    app.selling_uoms = [];

                   var uom = e.target.options[e.target.options.selectedIndex].dataset.uom;
                   var uom_id = e.target.options[e.target.options.selectedIndex].dataset.uomid;
                   var price = e.target.options[e.target.options.selectedIndex].dataset.price;

                    var retail1_price    = e.target.options[e.target.options.selectedIndex].dataset.retail1;
                    var retail2_price    = e.target.options[e.target.options.selectedIndex].dataset.retail2;
                    var wholesale_price  = e.target.options[e.target.options.selectedIndex].dataset.wholesale;

                    //$(this).closest('td').next().find('.txt_uom').val(uom);
                    //$(this).closest('td').next().find('.txt_uom').attr('data-id',uom_id);

                    $(this).closest('td').next().next().find('.txt_uom').attr('data-uom',uom);

                    //auto add new product row
                    if($(this).closest('tr').next().hasClass("total_row")) {
                        app.addProduct();
                    }

                    //add Warehouse UOM Selling Price
                    //$(this).closest('td').next().next().next().next().next().find('input').val(price);
                    var price_selectbox_id = $(this).closest('td').next().next().next().next().next().find('.unit_price_select');
                    price_selectbox_id.find('option').remove();
                    price_selectbox_id.append('<option value="">Select One</option>'); 
                    if(retail1_price != null && retail1_price != '') {
                        price_selectbox_id.append('<option value="'+retail1_price+'" selected>'+retail1_price+'</option>');   
                    }
                    if(retail2_price != null && retail2_price != '') {
                        price_selectbox_id.append('<option value="'+retail2_price+'">'+retail2_price+'</option>');   
                    }
                    if(wholesale_price != null && wholesale_price != '') {
                        price_selectbox_id.append('<option value="'+wholesale_price+'">'+wholesale_price+'</option>');   
                    }
                    

                    var selectbox_id = $(this).closest('td').next().next().find('.txt_uom');

                    selectbox_id.find('option').remove();

                    //add pre-defined product uom 
                    if(selectbox_id.find('option[value="'+uom_id+'"]').text() == "") {
                        selectbox_id.append('<option value="">Select One</option><option value="'+uom_id+'" data-relation="" data-uomqty = "1" data-productuom = "'+uom+'" data-productid="'+data.id+'" data-perprice="'+price+'" data-price="'+price+'" data-retail1="'+retail1_price+'" data-retail2="'+retail2_price+'" data-wholesale="'+wholesale_price+'" selected>'+uom+'</option>'); 
                    }
                    $(".txt_uom").select2();

                    var key = app.products.findIndex(x => x.product_id == data.id);
                    var available_qty = parseInt(app.products[key].in_count) - parseInt(app.products[key].out_count);
                    var available_id = $(this).closest('td').next().next().next().next().find('.txt_available').val(available_qty);

                    app.getSellingUomByProduct(selectbox_id, data.id);
                });                

                $(".txt_uom").select2();

                $(".txt_uom").on("select2:select", function(e) {
                
                    app.checkQty(e.target.options[e.target.options.selectedIndex]);
                    var uom_relation = e.target.options[e.target.options.selectedIndex].dataset.relation;
                    if(app.sale_type == 2) {
                    //for van sale
                        var uom_price = e.target.options[e.target.options.selectedIndex].dataset.price;
                        var uom_retail1_price = e.target.options[e.target.options.selectedIndex].dataset.retail1;
                        var uom_retail2_price = e.target.options[e.target.options.selectedIndex].dataset.retail2;
                        var uom_wholesale_price = e.target.options[e.target.options.selectedIndex].dataset.wholesale;
                    } else {
                    //for office sale
                        var uom_price = e.target.options[e.target.options.selectedIndex].dataset.perprice;
                        var uom_retail1_price = e.target.options[e.target.options.selectedIndex].dataset.retail1;
                        var uom_retail2_price = e.target.options[e.target.options.selectedIndex].dataset.retail2;
                        var uom_wholesale_price = e.target.options[e.target.options.selectedIndex].dataset.wholesale;
                    }

                    $(this).closest('td').next().find('.txt_relation').val(uom_relation);
                    //$(this).closest('td').next().next().next().find('input').val(uom_price);
                    var unit_price_selectbox_id = $(this).closest('td').next().next().next().find('.unit_price_select');
                    unit_price_selectbox_id.find('option').remove();
                    unit_price_selectbox_id.append('<option value="">Select One</option>');
                    if(e.params.data.id != '') {
                        if(uom_retail1_price != 'null' && uom_retail1_price != '' && uom_retail1_price !== "undefined") {
                            unit_price_selectbox_id.append('<option value="'+uom_retail1_price+'" selected>'+uom_retail1_price+'</option>');   
                        }
                        if(uom_retail2_price != 'null' && uom_retail2_price != '' && uom_retail2_price !== "undefined") {
                            unit_price_selectbox_id.append('<option value="'+uom_retail2_price+'">'+uom_retail2_price+'</option>');   
                        }
                        if(uom_wholesale_price != 'null' && uom_wholesale_price != '' && uom_wholesale_price !== "undefined") {
                            unit_price_selectbox_id.append('<option value="'+uom_wholesale_price+'">'+uom_wholesale_price+'</option>');   
                        }
                    }


                    //app.calTotalAmount($(this).closest('td').next().next().next().find('input'));
                    app.calTotalAmount(unit_price_selectbox_id);
                });

                var cell7=row.insertCell(6);
                    /***cell7.className = "text-center";
                var t7=document.createElement("input");
                    t7.type = "checkbox";
                    t7.name = "chk_foc[]";
                    t7.addEventListener('change', function(){ app.checkFoc(t7); });
                    cell7.appendChild(t7);***/

                 var t7=document.createElement("input");
                    t7.name = "stock_available[]";
                    t7.className ="form-control txt_available";
                    $(t7).attr("readonly", true);

                    cell7.appendChild(t7);

                var cell8=row.insertCell(7);
                /*var t6=document.createElement("input");
                    t6.name = "unit_price[]";
                    t6.style = "width:100px;";
                    t6.className ="form-control float_num";
                    $(t6).attr("required", true);
                    t6.addEventListener('blur', function(){ app.calTotalAmount(t6); });
                    cell6.appendChild(t6); */

                var t8=document.createElement("select");
                    t8.name = "unit_price[]";
                    t8.className = "form_control unit_price_select";
                    t8.style = "min-width:100px;";
                    $(t8).attr("required", true);
                    //t3.addEventListener('change', function(){ app.checkQty(t3); });

                    var option = document.createElement("option");
                    option.value = '';
                    option.text = "Select One";
                    t8.append(option);

                 cell8.appendChild(t8);

                var cell9=row.insertCell(8);
                var t9=document.createElement("input");
                    t9.name = "total_amount[]";
                    t9.style = "width:100px;";
                    t9.className ="form-control num_txt";
                    $(t9).attr("required", true);
                    $(t9).attr("readonly", true);
                   // t2.addEventListener('blur', function(){ app.checkQty(t2); });
                    cell9.appendChild(t9);

                var cell10=row.insertCell(9);
                cell10.className = "text-center";
                var row_action = "<a class='remove-row red-icon' title='Remove'><i class='fas fa-times-circle' style='font-size: 25px;'></i></a>";
                $(cell10).append(row_action);

                $(".unit_price_select").select2();

                $(".unit_price_select").on("select2:select", function(e) {

                    var data = e.params.data;
                    app.calTotalAmount($(this));
                });

                $(".brands").select2();
                $(".brands").on("select2:select", function(e) {
                    var data = e.params.data;
                    var brand_id = data.id;
                    var cat_id = $(this).closest('td').next().find('select').find(':selected').val();
                    var product_select_id = $(this).closest('td').next().next().find('select');
                    app.filterProducts(brand_id,cat_id,product_select_id);
                });

                $(".categories").select2();
                $(".categories").on("select2:select", function(e) {

                    var data = e.params.data;
                    var cat_id = data.id;
                    var brand_id = $(this).closest('td').prev().find('select').find(':selected').val();
                    var product_select_id = $(this).closest('td').next().find('select');
                    app.filterProducts(brand_id,cat_id,product_select_id);
                });

            },

            getSale(id) {
              let app = this;
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
                    app.form.reference_no = response.data.sale.reference_no;
                    app.form.payment_type = response.data.sale.payment_type;
                    app.form.previous_balance = response.data.previous_balance;
                    app.form.due_date = response.data.sale.due_date;
                    app.form.credit_day = response.data.sale.credit_day;
                    if(response.data.sale.office_sale_man != null) {
                        app.form.office_sale_man = response.data.sale.office_sale_man.name;
                    } else {
                        app.form.office_sale_man = '';
                    }
                    if(response.data.sale.order != null && response.data.sale.order.sale_man != null && response.data.sale.order.sale_man != '') {
                        app.form.sale_man = response.data.sale.order.sale_man.name;
                    }

                    if(app.form.payment_type == 'credit') {
                        app.required_val = true;
                    } else {
                        app.required_val = false;
                    }
                    $("#customer_id").append('<option value="'+response.data.sale.customer_id+'" selected>'+response.data.sale.customer.cus_name+'</option>');
                    app.form.customer_id = response.data.sale.customer_id;
                    if(response.data.sale.order_id != null) {
                        app.form.sale_order = true;
                        app.form.approval_id = response.data.sale.order_approval_id;
                        app.SOEdit = true;
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
                        //$("#save_btn").attr('disabled', true);

                        //check order is revised or not
                        if(response.data.sale.is_revise == 1) {
                            $("#is_revise").prop("checked", true);
                            $("#is_revise").prop("disabled", true);
                            app.form.revise_order = true;   
                        }
                    }
                    $("#is_so").attr("disabled", true);

                    app.form.sub_total  = response.data.sale.total_amount;
                    app.form.pay_amount = response.data.sale.pay_amount;
                    app.form.discount = response.data.sale.discount;
                    app.form.balance_amount = response.data.sale.balance_amount;

                    $.each(app.ex_products, function( key, value ) {
                        app.form.ex_product_pivot.push(value.pivot.id);
                    });

                    //add products dynamically
                    var subTotal = 0;
                    var balAmount = 0;
                    $.each(app.ex_products, function( key, product ) {
                        if(app.user_role != "Country Head" || (app.user_role == "Country Head" && response.data.access_brands.indexOf(product.brand_id) > -1)) {

                            var table=document.getElementById("product_table");
                            var row=table.insertRow((table.rows.length) - 5);

                            var cell1=row.insertCell(0);

                            var t1=document.createElement("select");
                                t1.name = "brand[]";
                                t1.className = "form-control brands";
                                t1.style = "min-width:100px;";
                                $(t1).attr('readonly', true);

                               var option = document.createElement("option");
                               option.value = product.brand_id;
                               option.className ="form-control";
                               option.text = product.brand.brand_name;
                               t1.append(option);

                                cell1.appendChild(t1);

                            var cell2=row.insertCell(1);

                            var t2=document.createElement("select");
                                t2.name = "category[]";
                                t2.className = "form-control categories";
                                t2.style = "min-width:100px;";
                                $(t2).attr('readonly', true);

                               var option = document.createElement("option");
                               option.value = product.category_id;
                               option.className ="form-control";
                               option.text = product.category.category_name;
                               t2.append(option);

                                cell2.appendChild(t2);

                            var cell3=row.insertCell(2);

                            var t3=document.createElement("select");
                                t3.name = "product[]";
                                t3.className = "form-control txt_product";
                                t3.style = "min-width:150px;";
                                $(t3).attr("required", true);
                                $(t3).attr('readonly', true);

                               var option = document.createElement("option");
                               option.value = product.id;
                               option.className ="form-control";
                               $(option).attr('data-uom',product.uom.uom_name);
                               $(option).attr('data-uomid',product.uom.uom_id);
                               $(option).attr('data-price','');
                               $(option).attr('data-pivotid',product.pivot.id);
                               option.text = product.product_name;
                               t3.append(option);

                                cell3.appendChild(t3);

                            var cell4=row.insertCell(3);
                            var t4=document.createElement("input");
                                t4.name = "qty[]";
                                t4.value = product.pivot.product_quantity;
                                t4.style = "width:100px;";
                                t4.className ="form-control num_txt";
                                $(t4).attr("required", true);
                                t4.addEventListener('blur', function(){ app.checkQty(t4); });
                                if(app.form.sale_order == true && app.form.revise_order == false) {
                                    $(t4).attr("readonly", true);
                                }

                                cell4.appendChild(t4);
                               
                            var cell5=row.insertCell(4);

                            var t5=document.createElement("select");
                                t5.name = "uom[]";
                                t5.className = "form-control txt_uom";
                                t5.style = "width:200px;";
                                $(t5).attr("required", true);                           

                                t5.addEventListener('blur', function(){ app.checkQty(t5); });

                                if(app.form.sale_order == true && app.form.revise_order == false) {
                                    $(t5).attr("disabled", true);
                                }

                                var option = document.createElement("option");
                                option.value = '';
                                option.text = "Select One";
                                $(option).attr('data-relation',"");
                                $(option).attr('data-price', "");
                                $(option).attr('data-perprice', "");
                                t5.append(option);

                                var option = document.createElement("option");
                                option.value = product.uom_id;
                                option.text = product.uom.uom_name;
                                $(option).attr("data-relation","");
                                $(option).attr("data-uomqty","1");
                                $(option).attr("data-price",product.product_price);
                                $(option).attr("data-retail1",product.retail1_price);
                                $(option).attr("data-retail2",product.retail2_price);
                                $(option).attr("data-retail1",product.retail1_price);
                                $(option).attr("data-wholesale",product.wholesale_price);
                                $(option).attr("data-productuom", product.uom.uom_name);
                                $(option).attr("data-productid", product.id);
                                if(product.pivot.uom_id == product.uom_id) {
                                    option.selected = "selected";
                                }
                                t5.append(option);

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
                                    $(option).attr("data-retail1",suom.pivot.retail1_price);
                                    $(option).attr("data-retail2",suom.pivot.retail2_price);
                                    $(option).attr("data-wholesale",suom.pivot.wholesale_price);
                                    $(option).attr("data-perprice",suom.pivot.per_warehouse_uom_price);
                                    $(option).attr("data-productuom",product.uom.uom_name);
                                    $(option).attr("data-productid", product.id);
                                    t5.append(option);
                                });
                                
                                cell5.appendChild(t5);


                            var cell6=row.insertCell(5);
                            var t6=document.createElement("input");
                                t6.type = "text";
                                if(product.pivot.uom_id != product.uom_id) {
                                    t6.value = relation_val;
                                } else {
                                    t6.value = "";
                                }
                                t6.name = "relation[]";
                                t6.style = "min-width:200px;";
                                t6.className ="form-control txt_relation";
                                $(t6).attr('readonly', true);
                                cell6.appendChild(t6);

                            

                            $(".txt_product").select2();

                            $(".txt_uom").select2();

                            $("#customer_id").select2();
                            $("#customer_id").on("select2:select", function(e) {

                                var data = e.params.data;
                                app.form.customer_id = data.id;
                                //get customer's previous balance
                                 axios.get("/customer_previous_balance/"+data.id).then(({ data }) => (app.form.previous_balance = data.previous_balance));

                                app.sale_orders = [];  
                                app.sale_order_approvals = [];
                                $('#order_product_table tbody tr').slice(0, -3).remove();
                                axios.get("/customer_sale_orders/"+data.id).then(({ data }) => (app.sale_orders = data.data));
                                $("#sale_order").select2();
                            });

                            $(".txt_product").on("select2:select", function(e) {

                                var data = e.params.data;

                                app.selling_uoms = [];

                               var uom = e.target.options[e.target.options.selectedIndex].dataset.uom;
                               var uom_id = e.target.options[e.target.options.selectedIndex].dataset.uomid;
                               var price = e.target.options[e.target.options.selectedIndex].dataset.price;

                               var retail1_price    = e.target.options[e.target.options.selectedIndex].dataset.retail1;
                               var retail2_price    = e.target.options[e.target.options.selectedIndex].dataset.retail2;
                               var wholesale_price  = e.target.options[e.target.options.selectedIndex].dataset.wholesale;

                                //$(this).closest('td').next().find('.txt_uom').val(uom);
                                //$(this).closest('td').next().find('.txt_uom').attr('data-id',uom_id);

                                $(this).closest('td').next().next().find('.txt_uom').attr('data-uom',uom);

                                //auto add new product row
                                if($(this).closest('tr').next().hasClass("total_row")) {
                                    app.addProduct();
                                }

                                //add Warehouse UOM Selling Price
                                //$(this).closest('td').next().next().next().next().next().find('input').val(price);
                                var price_selectbox_id = $(this).closest('td').next().next().next().next().next().find('.unit_price_select');
                                price_selectbox_id.find('option').remove();
                                price_selectbox_id.append('<option value="">Select One</option>'); 
                                if(retail1_price != null && retail1_price != '') {
                                    price_selectbox_id.append('<option value="'+retail1_price+'" selected>'+retail1_price+'</option>');   
                                }
                                if(retail2_price != null && retail2_price != '') {
                                    price_selectbox_id.append('<option value="'+retail2_price+'">'+retail2_price+'</option>');   
                                }
                                if(wholesale_price != null && wholesale_price != '') {
                                    price_selectbox_id.append('<option value="'+wholesale_price+'">'+wholesale_price+'</option>');   
                                }

                                var selectbox_id = $(this).closest('td').next().next().find('.txt_uom');

                                selectbox_id.find('option').remove();

                                //add pre-defined product uom 
                                if(selectbox_id.find('option[value="'+uom_id+'"]').text() == "") {
                                    selectbox_id.append('<option value="">Select One</option><option value="'+uom_id+'" data-relation="" data-uomqty = "1" data-productuom = "'+uom+'" data-productid="'+data.id+'" data-perprice="'+price+'" data-price="'+price+'" data-retail1="'+retail1_price+'" data-retail2="'+retail2_price+'" data-wholesale="'+wholesale_price+'" selected>'+uom+'</option>'); 
                                }
                                $(".txt_uom").select2();

                                var key = app.products.findIndex(x => x.product_id == data.id);
                                var available_qty = parseInt(app.products[key].in_count) - parseInt(app.products[key].out_count);
                                var available_id = $(this).closest('td').next().next().next().next().find('.txt_available').val(available_qty);

                                app.getSellingUomByProduct(selectbox_id, data.id);
                            });
                            

                            $(".txt_uom").select2();

                            $(".txt_uom").on("select2:select", function(e) {
                
                                app.checkQty(e.target.options[e.target.options.selectedIndex]);
                                var uom_relation = e.target.options[e.target.options.selectedIndex].dataset.relation;
                                if(app.sale_type == 2) {
                                //for van sale
                                    var uom_price = e.target.options[e.target.options.selectedIndex].dataset.price;
                                    var uom_retail1_price = e.target.options[e.target.options.selectedIndex].dataset.retail1;
                                    var uom_retail2_price = e.target.options[e.target.options.selectedIndex].dataset.retail2;
                                    var uom_wholesale_price = e.target.options[e.target.options.selectedIndex].dataset.wholesale;
                                } else {
                                //for office sale
                                    var uom_price = e.target.options[e.target.options.selectedIndex].dataset.perprice;
                                    var uom_retail1_price = e.target.options[e.target.options.selectedIndex].dataset.retail1;
                                    var uom_retail2_price = e.target.options[e.target.options.selectedIndex].dataset.retail2;
                                    var uom_wholesale_price = e.target.options[e.target.options.selectedIndex].dataset.wholesale;
                                }

                                $(this).closest('td').next().find('.txt_relation').val(uom_relation);
                                //$(this).closest('td').next().next().next().find('input').val(uom_price);
                                var unit_price_selectbox_id = $(this).closest('td').next().next().next().find('.unit_price_select');
                                unit_price_selectbox_id.find('option').remove();
                                unit_price_selectbox_id.append('<option value="">Select One</option>');
                                if(e.params.data.id != '') {
                                    if(uom_retail1_price != 'null' && uom_retail1_price != '' && uom_retail1_price !== "undefined") {
                                        unit_price_selectbox_id.append('<option value="'+uom_retail1_price+'" selected>'+uom_retail1_price+'</option>');   
                                    }
                                    if(uom_retail2_price != 'null' && uom_retail2_price != '' && uom_retail2_price !== "undefined") {
                                        unit_price_selectbox_id.append('<option value="'+uom_retail2_price+'">'+uom_retail2_price+'</option>');   
                                    }
                                    if(uom_wholesale_price != 'null' && uom_wholesale_price != '' && uom_wholesale_price !== "undefined") {
                                        unit_price_selectbox_id.append('<option value="'+uom_wholesale_price+'">'+uom_wholesale_price+'</option>');   
                                    }
                                }


                                //app.calTotalAmount($(this).closest('td').next().next().next().find('input'));
                                app.calTotalAmount(unit_price_selectbox_id);
                            });

                            var cell7=row.insertCell(6);
                                cell7.className = "text-center";
                            /***var t7=document.createElement("input");
                                t7.type = "checkbox";
                                t7.name = "chk_foc[]";
                                if(product.pivot.is_foc == '1') {
                                    $(t7). prop("checked", true);
                                }
                                t7.addEventListener('change', function(){ app.checkFoc(t7); });
                                if(app.form.sale_order == true && app.form.revise_order == false) {
                                    $(t7).attr("disabled", true);
                                }
                                cell7.appendChild(t7); ***/

                                var key = app.products.findIndex(x => x.product_id == product.id);
                                console.log(app.products);
                                var available_qty = parseInt(app.products[key].in_count) - parseInt(app.products[key].out_count);

                                var t7=document.createElement("input");
                                t7.name = "stock_available[]";
                                t7.value = available_qty;
                                t7.className ="form-control txt_available";
                                $(t7).attr("readonly", true);

                                cell7.appendChild(t7);

                            var cell8=row.insertCell(7);
                            /***var t6=document.createElement("input");
                                t6.name = "unit_price[]";
                                t6.style = "width:100px;";
                                if(product.pivot.price != 0 && product.pivot.price != null) {
                                    t6.value = product.pivot.price;
                                }
                                t6.className ="form-control float_num";
                                $(t6).attr("required", true);
                                if(product.pivot.is_foc == '1') {
                                    $(t6).attr("readonly", true);
                                }
                                t6.addEventListener('blur', function(){ app.calTotalAmount(t6); });
                                if(app.form.sale_order == true) {
                                   // $(t6).attr("readonly", true);
                                }
                                cell6.appendChild(t6); ***/

                            var t8=document.createElement("select");
                            t8.name = "unit_price[]";
                            t8.className = "form_control unit_price_select";
                            t8.style = "min-width:100px;";
                            $(t8).attr("required", true);

                            //t3.addEventListener('change', function(){ app.checkQty(t3); });

                            var option = document.createElement("option");
                            option.value = '';
                            option.text = "Select One";
                            t8.append(option);

                            if($(".txt_uom option[value="+product.pivot.uom_id+"]").attr('data-retail1') != '') {
                                var option = document.createElement("option");
                                    option.value = $(".txt_uom option[value="+product.pivot.uom_id+"]").attr('data-retail1');

                                    option.text = $(".txt_uom option[value="+product.pivot.uom_id+"]").attr('data-retail1');

                                    if(product.pivot.price == $(".txt_uom option[value="+product.pivot.uom_id+"]").attr('data-retail1')) {
                                        option.selected = "selected";
                                    }
                                    t8.append(option);
                            }

                            if($(".txt_uom option[value="+product.pivot.uom_id+"]").attr('data-retail2') != '') {
                                var option = document.createElement("option");
                                    option.value = $(".txt_uom option[value="+product.pivot.uom_id+"]").attr('data-retail2');

                                    option.text = $(".txt_uom option[value="+product.pivot.uom_id+"]").attr('data-retail2');
                                    
                                    if(product.pivot.price == $(".txt_uom option[value="+product.pivot.uom_id+"]").attr('data-retail2')) {
                                        option.selected = "selected";
                                    }
                                    t8.append(option);
                            }

                            if($(".txt_uom option[value="+product.pivot.uom_id+"]").attr('data-wholesale') != '') {
                                var option = document.createElement("option");
                                    option.value = $(".txt_uom option[value="+product.pivot.uom_id+"]").attr('data-wholesale');

                                    option.text = $(".txt_uom option[value="+product.pivot.uom_id+"]").attr('data-wholesale');
                                    
                                    if(product.pivot.price == $(".txt_uom option[value="+product.pivot.uom_id+"]").attr('data-wholesale')) {
                                        option.selected = "selected";
                                    }
                                    t8.append(option);
                            }

                         cell8.appendChild(t8);

                            $(".unit_price_select").select2();

                            $(".unit_price_select").on("select2:select", function(e) {

                                var data = e.params.data;
                                app.calTotalAmount($(this));
                            });

                            var cell9=row.insertCell(8);
                            var t9=document.createElement("input");
                                t9.name = "total_amount[]";
                                t9.style = "width:100px;";
                                if(product.pivot.total_amount != 0 && product.pivot.total_amount != null) {
                                    t9.value = product.pivot.total_amount;
                                    subTotal += parseInt(product.pivot.total_amount);
                                }
                                t9.className ="form-control num_txt";
                                $(t9).attr("required", true);
                                $(t9).attr("readonly", true);
                               // t2.addEventListener('blur', function(){ app.checkQty(t2); });
                                cell9.appendChild(t9);

                            var cell10=row.insertCell(9);
                            cell10.className = "text-center";
                            if((app.user_role == 'admin' || app.user_role == 'system') && (app.form.sale_order == false || app.form.revise_order == true) && !app.isDisabled) 
                            {
                                var row_action = "<a class='remove-exrow red-icon' title='Remove'><i class='fas fa-times-circle' style='font-size: 25px;'></i></a>";
                            } else {
                                var row_action = "<a class='remove-exrow red-icon' title='Remove' style='display:none;'><i class='fas fa-times-circle' style='font-size: 25px;'></i></a>";
                            }
                            $(cell10).append(row_action);
                        }
                       /** var disAmount = parseInt(response.data.sale.discount);
                        balAmount = parseInt(subTotal) - (parseInt(response.data.sale.pay_amount) + disAmount);
                        app.form.balance_amount = balAmount;
                        app.form.sub_total = subTotal;**/

                        $(".brands").select2();
                        $(".brands").on("select2:select", function(e) {
                            var data = e.params.data;
                            var brand_id = data.id;
                            var cat_id = $(this).closest('td').next().find('select').find(':selected').val();
                            var product_select_id = $(this).closest('td').next().next().find('select');
                            app.filterProducts(brand_id,cat_id,product_select_id);
                        });

                        $(".categories").select2();
                        $(".categories").on("select2:select", function(e) {

                            var data = e.params.data;
                            var cat_id = data.id;
                            var brand_id = $(this).closest('td').prev().find('select').find(':selected').val();
                            var product_select_id = $(this).closest('td').next().find('select');
                            app.filterProducts(brand_id,cat_id,product_select_id);
                        });
                    });


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
                        console.log(product_qty);
                        console.log("Available"+available_qty);
                        if(parseInt(product_qty) > parseInt(available_qty)) {

                            swal("Warning!", "Not enough quantity! Availabel quantity is "+available_qty+" "+uom_name+".", "warning");

                            $(obj).focus(); obj.value="";
                        }
                    }

                    //claculate total amount
                    var unit_price = $(obj).closest('td').next().next().next().next().find('select').val();
                    var relation_val = $(obj).closest('td').next().find(':selected').attr('data-uomqty');
                    if(obj.value != "" && unit_price != "") {
                        if(app.sale_type == 2) {
                            var total_amount = parseInt(obj.value) * parseFloat(unit_price);
                        } else {
                            var total_amount = parseInt(obj.value) * parseInt(relation_val) * parseFloat(unit_price);
                        }
                        $(obj).closest('td').next().next().next().next().next().find('input').val(Math.round(total_amount));
                    }

                    //get all sub total amount
                    var sub_total = 0;
                    for(var i=0; i<document.getElementsByName('product[]').length; i++) {
                        if(document.getElementsByName('total_amount[]')[i].value != "") {
                            sub_total += parseInt(document.getElementsByName('total_amount[]')[i].value);
                        }
                    }

                    app.form.sub_total = sub_total;
                    if(app.form.discount == '') {
                        var discount = 0;
                    } else {
                        var discount = app.form.discount;
                    }
                    app.form.pay_amount = paresInt((app.form.sub_total) - parseInt(discount));
                    app.form.balance_amount  = sub_total - (parseInt(app.form.pay_amount) + parseInt(discount));
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

                    //get all sub total amount
                    var sub_total = 0;
                    for(var i=0; i<document.getElementsByName('product[]').length; i++) {
                        if(document.getElementsByName('total_amount[]')[i].value != "") {
                            sub_total += parseInt(document.getElementsByName('total_amount[]')[i].value);
                        }
                    }

                    app.form.sub_total = sub_total;
                    if(app.form.discount == '') {
                        var discount = 0;
                    } else {
                        var discount = app.form.discount;
                    }
                    app.form.pay_amount = paresInt((app.form.sub_total) - parseInt(discount));
                    app.form.balance_amount  = sub_total - (parseInt(app.form.pay_amount) + parseInt(discount));
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
               if(price != "" && qty != "") {
                    if(app.sale_type == 2) {
                        var total = parseInt(qty) * parseFloat(price);
                    } else {
                        var relation_val = $(obj).closest('td').prev().prev().prev().find(':selected').attr('data-uomqty');
                        var total = parseInt(qty) * parseInt(relation_val) * parseFloat(price);
                    }
                    $(obj).closest('td').next().find('input').val(Math.round(total));
               } else {
                    $(obj).closest('td').next().find('input').val('');    
               }

               //get all sub total amount
               var sub_total = 0;
               for(var i=0; i<document.getElementsByName('product[]').length; i++) {
                    if(document.getElementsByName('total_amount[]')[i].value != "") {
                        sub_total += parseInt(document.getElementsByName('total_amount[]')[i].value);
                    }
               }

               app.form.sub_total = sub_total;
               var discount = 0;
                if(app.form.discount == '') {
                    discount = 0;
                } else {
                    discount = app.form.discount;
                }
               app.form.pay_amount = paresInt((app.form.sub_total) - parseInt(discount));
               app.form.balance_amount  = sub_total - (parseInt(app.form.pay_amount) + parseInt(discount));
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
                var pay_amount = 0;
                if(app.form.pay_amount != "") {
                    pay_amount = app.form.pay_amount;
                }
                if(obj.value != "") {
                    discount = obj.value;
                } 

                if(discount > app.form.sub_total) {
                    swal("Warning!", "Discount amount is greater than sub total amount", "warning");
                } else {
                    app.form.discount = obj.value;
                    app.form.balance_amount = parseInt(app.form.sub_total) - (parseInt(pay_amount)+parseInt(discount));
                }
            },

            onSubmit: function(event){
                let app = this;
                $('#loading').show();
                app.form.sale_type = app.sale_type;

                if(app.sale_type == 1) {
                    app.form.reference_no = $("#reference_no").val();
                } else {
                    app.form.reference_no = '';
                }
                app.form.payment_type = $("#payment_type").val();
                if(app.form.payment_type == 'credit') {
                    app.form.due_date = $("#due_date").val();
                    app.form.credit_day = $("#credit_day").val();
                } else {
                    app.form.due_date = '';
                    app.form.credit_day = '';
                }

                if(app.form.sale_order == true && !this.isEdit) {
                    app.form.order_id =  $("#sale_order").val();
                    app.form.approval_id = $("#sale_order_approval").val();
                }

                if (!this.isEdit) {

                    app.form.product = [];

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
                   
                    if(app.edit_form == app.original_form) {
                        swal("Warning!", "Please edit at least one field", "warning");
                        $("#loading").hide();
                    } else {
                        app.form.product_pivot = [];

                        app.form.product = [];
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
                    }
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
                    t1.className ="form-control float_num";
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
                        var discount = 0;
                        if(app.form.discount == '' || sub_total == 0 || sub_total == '') {
                            discount = 0;
                        } else {
                            discount = app.form.discount;
                        }
                        app.form.pay_amount = paresInt((app.form.sub_total) - parseInt(discount));
                        app.form.balance_amount  = sub_total - (parseInt(app.form.pay_amount) + parseInt(discount));   
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

            checkRevise(obj) {
                let app = this;
                var is_chk = $(obj).prop("checked");
                
                if(is_chk){
                    if(app.form.approval_id == '') {
                        swal("Warning!", "Please select Sale Order Approvel.", "warning");
                        $("#sale_order_approval").focus();
                        $(obj).prop("checked", false);
                    } else {
                        $("#loading").show();
                        var approval_id = app.form.approval_id;
                        app.form.revise_order = true;
                        $("input[name='qty[]']").attr('readonly',false);
                        //$("input[name='unit_price[]']").attr('readonly',false);
                        $(".txt_uom").attr('disabled',false);
                        $(".txt_uom").attr('readonly',false);
                        $(".remove-exrow").show();
                        $("input[name='chk_foc[]']").attr('disabled',false);
                        $("#so_list").show();
                        app.products = [];
                        axios.get("/productsByUserWarehouse/create_approval_invoice/"+ approval_id).then(({ data }) => (app.products = data.data))
                        .catch(error => {
                          console.log(error);
                        })
                        .finally(() => $("#loading").hide());
                    }

                } else {

                    swal({
                        title: "Are you sure?",
                        text: "Your revised products will be discarded!",
                        icon: "warning",
                        buttons: true,
                        dangerMode: true
                        }).then(willDelete => {
                        if (willDelete) {
                            if(!app.isEdit) {
                                app.form.revise_order = false;
                                var approval_id = app.form.approval_id;
                                if(approval_id != '') {
                                   app.getApproval(approval_id);
                                } 
                            } else {
                                location.reload();
                            }    
                        } else {
                          $(obj).prop("checked", true);
                        }
                    });
                    
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

                            $(t3).attr('disabled', true);

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
                            t6.className ="form-control float_num";
                            $(t6).attr("required", true);
                            if(product.pivot.is_foc == '1') {
                                $(t6).attr("readonly", true);
                            }
                            t6.addEventListener('blur', function(){ app.calTotalAmount(t6); });
                            //$(t6).attr("readonly", true);
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
                        var row_action = "<a class='remove-exrow red-icon' title='Remove' style='display:none;'><i class='fas fa-times-circle' style='font-size: 25px;'></i></a>";
                        $(cell8).append(row_action);


                            $(".txt_uom").select2();

                            $(".txt_uom").on("select2:select", function(e) {
                
                                app.checkQty(e.target.options[e.target.options.selectedIndex]);
                                var uom_relation = e.target.options[e.target.options.selectedIndex].dataset.relation;
                                if(app.sale_type == 2) {
                                //for van sale
                                    var uom_price = e.target.options[e.target.options.selectedIndex].dataset.price;
                                    var uom_retail1_price = e.target.options[e.target.options.selectedIndex].dataset.retail1;
                                    var uom_retail2_price = e.target.options[e.target.options.selectedIndex].dataset.retail2;
                                    var uom_wholesale_price = e.target.options[e.target.options.selectedIndex].dataset.wholesale;
                                } else {
                                //for office sale
                                    var uom_price = e.target.options[e.target.options.selectedIndex].dataset.perprice;
                                    var uom_retail1_price = e.target.options[e.target.options.selectedIndex].dataset.retail1;
                                    var uom_retail2_price = e.target.options[e.target.options.selectedIndex].dataset.retail2;
                                    var uom_wholesale_price = e.target.options[e.target.options.selectedIndex].dataset.wholesale;
                                }

                                $(this).closest('td').next().find('.txt_relation').val(uom_relation);
                                //$(this).closest('td').next().next().next().find('input').val(uom_price);
                                var unit_price_selectbox_id = $(this).closest('td').next().next().next().find('.unit_price_select');
                                unit_price_selectbox_id.find('option').remove();
                                unit_price_selectbox_id.append('<option value="">Select One</option>');
                                if(e.params.data.id != '') {
                                    if(uom_retail1_price != 'null' && uom_retail1_price != '' && uom_retail1_price !== "undefined") {
                                        unit_price_selectbox_id.append('<option value="'+uom_retail1_price+'">'+uom_retail1_price+'</option>');   
                                    }
                                    if(uom_retail2_price != 'null' && uom_retail2_price != '' && uom_retail2_price !== "undefined") {
                                        unit_price_selectbox_id.append('<option value="'+uom_retail2_price+'">'+uom_retail2_price+'</option>');   
                                    }
                                    if(uom_wholesale_price != 'null' && uom_wholesale_price != '' && uom_wholesale_price !== "undefined") {
                                        unit_price_selectbox_id.append('<option value="'+uom_wholesale_price+'">'+uom_wholesale_price+'</option>');   
                                    }
                                }


                                //app.calTotalAmount($(this).closest('td').next().next().next().find('input'));
                                app.calTotalAmount(unit_price_selectbox_id);
                            });

                        
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