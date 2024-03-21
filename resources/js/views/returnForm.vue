<template>
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="#!"><i class="feather icon-home"></i></a></li>
                <li class="breadcrumb-item"><a :href="site_path+'/'">Home</a></li>
                <li class="breadcrumb-item"><a :href="site_path+'/office'">Office Sale</a></li>
                <li class="breadcrumb-item"><router-link tag="span" :to="'/sale_return/'" class="font-weight-normal"><a href="#">Sale Return</a></router-link></li>
                <li class="breadcrumb-item active" aria-current="page">Sale Return Form</li>

            </ol>
        </nav>
        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h4 class="mb-0 text-gray-800">Sale Return Form</h4>
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
                                <label for="return_no">Return No.</label>
                                <input type="text" class="form-control" id="return_no" name="return_no" v-model="form.return_no" readonly>
                            </div>
                            <!--<div class="form-group col-md-4">
                                <label for="reference_no">Reference No.</label>
                                 <input type="text" class="form-control" id="reference_no" name="reference_no"
                                v-model="form.reference_no">
                            </div>-->
                        </div>
                        <div class="row">
                            <div class="form-group col-md-4">
                                <label for="return_date">Date</label>
                                <input type="text" class="form-control datetimepicker" id="return_date" name="return_date"
                                v-model="form.return_date" required>
                            </div>
                            <div class="form-group col-md-4">
                                <label for="branch_name">Branch</label>
                                 <input type="text" class="form-control" id="branch_id" name="branch_id"
                                v-model="user_branch" readonly>
                            </div>
                        </div>
                        <div class="row">
                            
                            <div class="form-group col-md-4">
                                <label for="office_sale_man_id">Sale Man</label>
                                <select id="office_sale_man_id" class="mm-txt"
                                    name="office_sale_man_id" v-model="form.office_sale_man_id" style="width:100%"
                                >
                                    <option value="">Select One</option>
                                    <option v-for="sale_man in sale_men" :value="sale_man.id"  >{{sale_man.sale_man}}</option>
                                </select>
                            </div>
                           
                            <div class="form-group col-md-4">
                                <label for="warehouse_id">Vehicle Warehouse</label>
                                 <input type="text" class="form-control" id="warehouse_id" name="vehicle_warehouse"
                                v-model="user_warehouse" readonly>
                            </div>

                        </div>

                        <div class="row mt-3">
                             <div class="form-group col-md-4">
                                <label for="customer_id">Customer</label>
                                <select id="customer_id" class="form-control mm-txt"
                                    name="customer_id" v-model="form.customer_id" style="width:100%" required 
                                >
                                    <option value="">Select One</option>
                                    <option v-for="cus in customers" :value="cus.id"  >{{cus.cus_name}}</option>
                                </select>
                            </div>
                            <div class="form-group col-md-4">
                                <label for="payment_type">Payment Type</label>
                                <select id="payment_type" class="form-control"
                                    name="payment_type" v-model="form.payment_type" @change='changePayment()' style="width:100%" required
                                >
                                    <option value="">Select One</option>
                                    <option value="cash">Cash</option>
                                    <option value="credit">Credit</option>
                                </select>
                            </div>
                            <!--<div class="form-group col-md-4" v-if="form.payment_type == 'credit'">
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
                            </div>-->
                        </div>

                        <div class="row">
                            <div class="form-group col-md-4">
                                <label for="return_method">Return Method</label>
                                <select id="return_method" class="form-control"
                                    name="return_method" v-model="form.return_method" @change='changeMethod()' style="width:100%" required 
                                >
                                    <option value="">Select One</option>
                                    <option value="with invoice">with Invoice</option>
                                    <option value="without invoice">without Invoice</option>
                                </select>
                            </div>
                            <div class="form-group col-md-4 sale_div" style="display:none">
                                <label for="sale_id">Sale Invoice</label>
                                <select class="form-control invoices"
                                    name="sale_id" id="sale_id" v-model="form.sale_id" style="width:100%"
                                >
                                    <option v-for="sale in invoices" :value="sale.id" :data-ptype="sale.payment_type" :data-balance="sale.total_amount-((sale.cash_discount == null || sale.cash_discount == '' ? 0 : sale.cash_discount)+sale.collection_amount+sale.pay_amount+sale.return_amount+sale.customer_return_amount)">{{sale.invoice_no}}</option>
                                </select>
                            </div>
                            <div class="form-group col-md-4 ost_div" style="display:none">
                                <label for="outstanding_amount">Outstanding Amount</label>
                                <input type="text" class="form-control num_txt" id="outstanding_amount" name="outstanding_amount"
                                v-model="form.outstanding_amount" readonly>
                            </div>
                        </div>

                        <div class="row mt-3" >
                             <div class="form-group col-md-4">
                                <label for="">Payment Method</label>
                                <select class="form-control" required
                                        v-model="form.account_group" style="width:100%" @change="changeAccountGroup()">
                                    <option value="">Select One</option>
                                    <option v-for="at in account_group" :value="at.id"  >{{at.name}}</option>
                                </select>
                            </div>
                            <div class="form-group col-md-4">
                                <label for="">&nbsp;</label>
                                <select class="form-control" required
                                        v-model="form.cash_bank_account" style="width:100%">
                                    <option value="">Select One</option>
                                    <option v-for="at in cash_bank_accounts" :value="at.id"  >{{at.sub_account_name}}</option>
                                </select>
                            </div>
                        </div>

                        <div class="row mt-4 mb-3"  v-if="form.return_method!=''">
                            <div class="col-md-12">
                                <span class="d-none d-sm-inline-block btn-sm btn-primary shadow-sm bg-blue"><i class="fas fa-search-plus text-white"></i> Product Details</span>
                            </div>
                        </div>                        

                        <div class="row mt-0 p_container"  id="p_container" style="display:none;" v-if="!isEdit">
                            <div class="col-md-12 text-right mt-0">
                                <a class='blue-icon' title='Add Product' v-if="form.return_method == 'without invoice'" @click="addProduct()"><i class='fas fa-plus-square' style='font-size: 30px;'></i></a>
                                <div style="display:none;">
                                    <select class="form-control txt_product"
                                        id="txt_product" style="min-width:150px;"
                                    >
                                        <option value="">Select One</option>
                                        <option v-for="product in products" :data-uom="product.uom_name" 
                                        :data-price="product.selling_price"
                                        :data-costprice="product.cost_price"
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
                                        <template v-if="isEdit">
                                        </template>
                                        <template v-if="!isEdit">                                    
                                        <!--<tr id="1">
                                            <td>                                               
                                                <select class="form-control txt_product"
                                                    name="product[]" id="product_1" style="min-width:150px;" required
                                                >
                                                    <option value="">Select One</option>
                                                    <option v-for="product in products" :data-uom="product.uom_name" 
                                                    :data-price="product.selling_price"
                                                    :data-purchaseprice="product.purchase_price"
                                                    :data-costprice="product.cost_price"
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
                                        </tr>-->
                                        </template>
                                        
                                        <tr class="total_row">
                                            <td colspan="8" class="text-right">Total</td>
                                            <td colspan="2" class="text-right">
                                                <input type="text" v-model="form.all_total_amount" class="form-control num_txt" readonly style="width:150px;" required />
                                            </td>
                                        </tr>
                                        <tr class="total_row">
                                            <td colspan="8" class="text-right">Return Amount </td>
                                            <td colspan="2">
                                                <input type="text" v-model="form.return_amount" class="form-control num_txt" style="width:150px;" @blur="changePaidAmount()" :readonly="returnReadonly" />
                                            </td>
                                        </tr> 
                                        <tr class="total_row">
                                            <td colspan="8" class="text-right">Balance Amount</td>
                                            <td colspan="2">
                                                <input type="text" v-model="form.balance_amount" class="form-control num_txt" readonly style="width:150px;" required />
                                            </td>
                                        </tr>                                    
                                    </tbody>
                                </table>
                            </div>                         
                        </div>
                        <!-- for sale product -->
                        <div class="row mt-4" v-if="form.return_method=='with invoice' && !isEdit">
                            <div class="col-md-12 text-right mt-0">
                                <a class='blue-icon' title='Add Product' @click="addProduct()" v-if="form.return_method == 'without invoice'"><i class='fas fa-plus-square' style='font-size: 30px;'></i></a>
                                <div style="display:none;">
                                    <select class="form-control txt_product"
                                        id="txt_product" style="min-width:150px;"
                                    >
                                        <option value="">Select One</option>
                                        <option v-for="product in products" :data-uom="product.uom_name" 
                                        :data-price="product.selling_price"
                                        :data-purchaseprice="product.purchase_price"
                                        :data-costprice="product.cost_price"
                                        :data-uomid="product.uom_id" :value="product.product_id" 
                                        data-pivotid = "0">{{product.product_name}}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-12 table-responsive">
                                <table class="table table-bordered" id="sale_product_table">
                                    <thead class="thead-grey">
                                        <tr>
                                            <th scope="col" style="min-width:150px;">Product Name</th>
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
                                        <tr class="total_row">
                                            <td colspan="8" class="text-right">Total</td>
                                            <td colspan="2" class="text-right">
                                                <input type="text" v-model="form.all_total_amount" class="form-control num_txt" readonly style="width:150px;" required />
                                            </td>
                                        </tr>
                                        <tr class="total_row">
                                            <td colspan="8" class="text-right">Discount</td>
                                            <td colspan="2" class="text-right">
                                                <input type="text" v-model="form.sale_discount" class="form-control num_txt" style="width:150px;" @blur="changePaidAmount()" />
                                            </td>
                                        </tr>
                                        <tr class="total_row">
                                            <td colspan="8" class="text-right">Return Amount </td>
                                            <td colspan="2">
                                                <input type="text" id="return_amount" v-model="form.return_amount" class="form-control num_txt" style="width:150px;" @blur="changePaidAmount()" :readonly="returnReadonly" />
                                            </td>
                                        </tr>
                                        <tr class="total_row">
                                            <td colspan="8" class="text-right">Balance Amount</td>
                                            <td colspan="2">
                                                <input type="text" v-model="form.balance_amount" class="form-control num_txt" readonly style="width:150px;" required />
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <!-- end -->

                        <!-- for return product -->
                        <div class="row mt-4" v-if="isEdit">
                            <div class="col-md-12 text-right mt-0">
                                <a class='blue-icon' title='Add Product' @click="addProduct()" v-if="form.return_method == 'without invoice'"><i class='fas fa-plus-square' style='font-size: 30px;'></i></a>
                                <div style="display:none;">
                                    <select class="form-control txt_product"
                                        id="txt_product" style="min-width:150px;"
                                    >
                                        <option value="">Select One</option>
                                        <option v-for="product in products" :data-uom="product.uom_name" 
                                        :data-price="product.selling_price"
                                        :data-purchaseprice="product.purchase_price"
                                        :data-costprice="product.cost_price"
                                        :data-uomid="product.uom_id" :value="product.product_id" 
                                        data-pivotid = "0">{{product.product_name}}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-12 table-responsive">
                                <table class="table table-bordered" id="return_product_table">
                                    <thead class="thead-grey">
                                        <tr>
                                            <th scope="col" style="min-width:150px;">Product Name</th>
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
                                        <tr class="total_row">
                                            <td colspan="8" class="text-right">Total</td>
                                            <td colspan="2" class="text-right">
                                                <input type="text" v-model="form.all_total_amount" class="form-control num_txt" readonly style="width:150px;" required />
                                            </td>
                                        </tr>
                                        <tr class="total_row" v-if="form.return_method == 'with invoice'">
                                            <td colspan="8" class="text-right">Discount</td>
                                            <td colspan="2" class="text-right">
                                                <input type="text" class="form-control num_txt" style="width:150px;" v-model="form.sale_discount" @blur="changePaidAmount()" />
                                            </td>
                                        </tr>
                                        <tr class="total_row">
                                            <td colspan="8" class="text-right">Return Amount </td>
                                            <td colspan="2">
                                                <input type="text" id="return_amount" v-model="form.return_amount" class="form-control num_txt" style="width:150px;" @blur="changePaidAmount()" :readonly="returnReadonly" />
                                            </td>
                                        </tr>
                                        <tr class="total_row">
                                            <td colspan="8" class="text-right">Balance Amount</td>
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

                            <div class="col-md-12" v-if="isEdit && canEdit"> 
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
                return_date: "",
                return_no: "",
                vehicle_warehouse: "",
                warehouse_id: "",
                branch_id: "",
                customer_id: "",
                sale_id: "",
                office_sale_man: "",
                office_sale_man_id: "",
                product: [],
                product_sale_id: [],
                uom: [],
                qty: [],
                unit_price: [],
                rate: [],
                actual_rate: [],
                discount: [],
                other_discount: [],
                total_amount: [],                
                is_foc: [],
                uom_id: "",
                reference_no: '',
                return_method: '',
                payment_type: 'cash',
                credit_day: '',
                due_date: '',
                sale_man: '',
                all_total_amount: 0,
                return_amount: 0,
                balance_amount:0, 
                outstanding_amount: '', 
                account_group: "",
                cash_bank_account: '', 
                sale_discount: 0,         

              }),              
              isEdit: false,
              brands: [],
              categories: [],
              products: [],
              uoms: [],
              return_id: '',
              return_status: '',
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
              invoices: [],
              returnReadonly: true,
              canEdit: false,
              return_total_amount: 0,
              account_group: [],
              cash_bank_accounts: [],
              sale_discount: 0,

            };
        },

        created() {


            this.user_year = document.querySelector("meta[name='user-year-likelink']").getAttribute('content');

            //sale_type = 2 for Van and 1 for Office Sale
            this.site_path = document.querySelector("meta[name='site-path']").getAttribute('content');
            //this.site_path = this.site_path.substring(this.site_path.lastIndexOf('/')+1);
            this.storage_path = document.querySelector("meta[name='storage-path']").getAttribute('content');

            //this.sale_type = this.$route.params.sale_type;
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
                this.return_id = this.$route.params.id;
                let app = this;
                app.getReturn(app.return_id);
                
            } else {
                //this.getMaxId();
                //this.initProducts();
            };

            this.form.invoice_date = moment().format("YYYY-MM-DD");
        },
        mounted() {

            $("#loading").hide();
            let app = this;            
            
           // app.initWarehouses();

            app.initProducts();

            app.initCustomers();

            app.initSaleMen();

            app.initAccountGroup();

            $("#office_sale_man_id").select2();

            $("#office_sale_man_id").on("select2:select", function(e) {

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
                /***axios.get("/customer_previous_balance/"+data.id).then(({ data }) => (app.form.previous_balance = data.previous_balance)); ***/
                if(data.id != '') {
                    axios.get("/customer_all_sale/"+data.id).then(function(response) {
                        app.invoices = response.data.data;
                        app.form.outstanding_amount = response.data.previous_balance;
                        $("#sale_id").select2();

                        $("#sale_id").on("select2:select", function(e) {

                            var data = e.params.data;
                            app.form.sale_id = data.id;
                        });

                    });
                }
            });

            $(".unit_price_select").select2();

            $(".unit_price_select").on("select2:select", function(e) {

                var data = e.params.data;
                app.calTotalAmount($(this));
            });

            $("#sale_id").select2();
            $("#sale_id").on("select2:select", function(e) {
                //app.form.office_sale_man_id =  e.target.options[e.target.options.selectedIndex].dataset.saleman;
               // $('#order_product_table tbody tr').slice(0, -3).remove();
                var data = e.params.data;
                if(data.id != '') {
                    app.getSale(data.id);
                } else {
                    /***$('#order_product_table tbody tr').slice(0, -6).remove();  
                    app.form.cash_discount = '';
                    app.form.net_total = '';
                    app.form.tax = '';
                    app.form.tax_amount = ''; 
                    app.form.sub_total = '';
                    app.form.balance_amount = '';***/
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
                var id=data.id;
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

            $("#return_date")
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
                    app.form.return_date = formatedValue;
                    app.form.due_date = '';
                    app.form.credit_day = '';

                    //$('#return_date').data("DateTimePicker").minDate(e.date);
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
                    swal("Warning!", "At least one product must be added!", "warning");

                } else {
                    $(this).parents("tr").remove();

                    
                }

                var sub_total = 0;
                   for(var i=0; i<document.getElementsByName('product[]').length; i++) {
                        if(document.getElementsByName('total_amount[]')[i].value != "") {
                            sub_total += parseFloat(document.getElementsByName('total_amount[]')[i].value);
                            //alert(sub_total);
                        }
                   }
                    app.form.all_total_amount = Math.round(sub_total);
                    var disc = app.form.sale_discount == '' ? 0 : app.form.sale_discount;
                    if(app.form.payment_type == "cash") {
                        var return_amount = app.form.all_total_amount;
                        app.form.return_amount = parseInt(return_amount) - parseInt(disc);                 
                        app.form.balance_amount = parseInt(app.form.all_total_amount) - parseInt(return_amount);
                    } else {
                        var return_amount = app.form.return_amount == '' || app.form.return_amount == null ? 0 : app.form.return_amount;
                                          
                        app.form.balance_amount = (parseInt(app.form.all_total_amount) - parseInt(return_amount)) - parseInt(disc);
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

                                if(pay_amount == 0) {
                                   app.form.pay_amount = parseInt(app.form.net_total) + parseInt(app.form.tax_amount);
                                    app.form.balance_amount = 0;
                                } else {

                                    app.form.balance_amount = (parseInt(app.form.net_total) + parseInt(app.form.tax_amount)) - parseInt(pay_amount);
                                }
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

            initAccountGroup(){
                axios.get('/sub_account/get_account_group').then(({data})=>(this.account_group=data.account_group));
                // $("#financial_type2_id").select2();
            },

            changeAccountGroup(id) {
                var ag_id = this.form.account_group;
                axios.get('/sub_account/get_account_group/'+ag_id).then(({data})=>(this.cash_bank_accounts=data.sub_accounts));
            },

            changeMethod() {

                let app = this;
                if(app.form.return_method == 'without invoice') {
                    app.form.all_total_amount = 0;
                    app.form.return_amount = 0;
                    app.form.balance_amount = 0;
                    this.user_warehouse = document.querySelector("meta[name='user-wh']").getAttribute('content');

                    this.user_branch = document.querySelector("meta[name='user-branch']").getAttribute('content');

                    $('#office_sale_man_id').val('').trigger('change');

                   $('#product_table tbody tr').slice(0, -3).remove(); 
                   $('#sale_product_table tbody tr').slice(0, -3).remove();
                   $('#return_product_table tbody tr').slice(0, -3).remove(); 

                   app.addProduct();
                   $('.ost_div').show();
                   $('.sale_div').hide();
                   $('.p_container').show(); 
                } else {
                    $('.p_container').hide();
                    $('.sale_div').show();
                    $('#sale_id').val('').trigger('change');
                    $('.ost_div').hide();
                }

            },

            initSaleMen() {
              axios.get("/sale_men").then(({ data }) => (this.sale_men = data.data));
              $("#sale_man").select2();
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

            changePayment() {
                var disc = this.form.sale_discount == '' ? 0 : this.form.sale_discount;
                if(this.form.payment_type == 'credit') {
                    this.returnReadonly = false;
                    this.form.return_amount = 0;
                    
                } else {
                    this.returnReadonly = true;   
                    this.form.return_amount = parseInt(this.form.all_total_amount) - parseInt(disc); 
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

            /**initProducts() {
              let app = this;
              if(app.$route.params.id) {
                axios.get("/productsByUserWarehouse/edit/"+ app.$route.params.id).then(({ data }) => (app.products = data.data));
              } else {
                axios.get("/productsByUserWarehouse/create/null").then(({ data }) => (app.products = data.data));
              }
              $(".txt_product").select2();
            },**/
            initProducts() {
              axios.get("/order/products/").then(({ data }) => (this.products = data.data));
              console.log(this.products);
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
                let app = this;
                var max = 0;
                // $('#product_table tbody tr').each(function(){
                //     max = $(this).attr('id') > max ? $(this).attr('id') : max;
                // });
                if(!app.isEdit) {
                    $('#product_table tbody tr').each(function(){
                        max = parseInt($(this).attr('id')) > max ? parseInt($(this).attr('id')) : max;
                    });
                } else {
                    $('#return_product_table tbody tr').each(function(){
                        max = parseInt($(this).attr('id')) > max ? parseInt($(this).attr('id')) : max;
                    });
                }
                    
                //var max = $('#product_table tbody tr').sort(function(a, b) { return +a.id < +b.id })[0].id;
                var row_id = parseInt(max) +1;

                /***$('#product_table tbody tr').slice(0, -3).remove(); 
                $('#sale_product_table tbody tr').slice(0, -3).remove();
                $('#return_product_table tbody tr').slice(0, -3).remove(); ***/
                if(!app.isEdit) {
                    var table=document.getElementById("product_table");
                } else {
                    var table=document.getElementById("return_product_table");
                }
                var row=table.insertRow((table.rows.length)-3);
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
                    
                    app.calTotalAmount($(this));
                    //app.getSellingUomByProduct(selectbox_id, data.id);
                });

            },

            getReturn(id) {
              let app = this;
              app.return_status = '';
              $("#loading").show();
              axios
                .get("/sale_return/" + id)
                .then(function(response) {
                    if(response.data.data.office_sale_man_id != null) {

                        $('#office_sale_man_id').val(response.data.data.office_sale_man_id).trigger('change');
                        app.form.office_sale_man_id = response.data.data.office_sale_man_id;
                    }
                    app.form.return_no = response.data.data.return_no; 
                    app.return_status = response.data.data.return_status;
                    app.form.return_date = response.data.data.return_date;
                    app.form.branch_id = response.data.data.branch_id;
                    app.form.sale_discount = response.data.data.discount;
                    app.form.warehouse_id = response.data.data.warehouse_id;
                    app.user_branch = response.data.data.branch.branch_name;
                    app.user_warehouse = response.data.data.warehouse.warehouse_name;
                    app.form.return_method = response.data.data.return_method;
                    app.form.payment_type = response.data.data.return_type;

                    app.form.account_group = response.data.data.account_group_id;             
                    if(response.data.data.account_group_id != '' && response.data.data.account_group_id != null) {
                        axios.get('/sub_account/get_account_group/'+response.data.data.account_group_id).then(({data})=>(app.cash_bank_accounts=data.sub_accounts));
                    }
                    app.form.cash_bank_account = response.data.data.sub_account_id;

                    if(response.data.data.return_method == 'with invoice')
                    {
                        var cash_discount = response.data.data.sale.cash_discount == null || response.data.data.sale.cash_discount == '' ? 0 : response.data.data.sale.cash_discount;
                        var total_discount = parseInt(cash_discount) - parseInt(response.data.data.sale.return_discount);
                        var sale_discount = parseInt(total_discount) + parseInt(response.data.data.discount);
                        app.sale_discount = sale_discount;

                        if((response.data.data.payment_amount == response.data.data.total_payment_amount && response.data.data.sale.extra_return_amount == 0) || (response.data.data.return_status == 'extra' && response.data.data.payment_amount == response.data.data.total_payment_amount)) {
                            app.canEdit = true;
                        } else {
                            app.canEdit = false;
                        }
                    } else {
                        if(response.data.data.payment_amount == response.data.data.total_payment_amount) {
                            app.canEdit = true;
                        } else {
                            app.canEdit = false;
                        }
                    }

                    $('#customer_id').val(response.data.data.customer_id).trigger('change');
                        app.form.customer_id = response.data.data.customer_id;
                    $("#return_method").attr('readonly', true);
                    if(app.form.return_method == 'with invoice') {                        
                        $('.sale_div').show();
                        app.invoices = response.data.sale;
                        $('#sale_id').val(response.data.data.sale_id).trigger('change');
                        app.form.sale_id = response.data.data.sale_id;
                    } else {
                        $('.ost_div').show(); 
                        app.form.outstanding_amount  = response.data.previous_balance;
                    }

                    if(response.data.data.return_type == 'credit') {
                        $("#return_amount").attr('readonly', false);
                        app.required_val = true;
                    } else {
                        $("#return_amount").attr('readonly', true);
                        app.required_val = false;
                    }
                    app.ex_products = response.data.data.products;

                    //add products dynamically
                    var subTotal = 0;
                    var balAmount = 0;
                    var row_id = 0;
                    $('#product_table tbody tr').slice(0, -3).remove(); 
                    $('#sale_product_table tbody tr').slice(0, -3).remove(); 
                    $('#return_product_table tbody tr').slice(0, -3).remove(); 
                    $.each(app.ex_products, function( key, product ) { 
                        row_id = row_id+1; 
                            var table=document.getElementById("return_product_table");
                            var row=table.insertRow((table.rows.length) - 3);
                            row.id = row_id;
                            var cell1=row.insertCell(0);

                            var t1=document.createElement("select");
                                t1.name = "product[]";
                                t1.id = "product_"+row_id;
                                t1.className = "form-control txt_product";
                                t1.style = "min-width:150px;";
                                $(t1).attr("required", true);
                                $(t1).attr('disabled', true);

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

                            if(response.data.data.return_method == 'with invoice') {

                                var hidInput=document.createElement("input");
                                    hidInput.type = 'hidden';
                                    hidInput.name = "valid_qty[]";
                                    hidInput.id = "valid_qty_"+row_id;
                                    hidInput.value = (parseInt(product.sale_product_quantity) - parseInt(product.return_quantity)) + parseInt(product.pivot.product_quantity);
                                    cell1.appendChild(hidInput);

                                 var hidInput1=document.createElement("input");
                                    hidInput1.type = 'hidden';
                                    hidInput1.name = "product_sale_id[]";
                                    hidInput1.id = "product_sale_id_"+row_id;
                                    hidInput1.value = product.pivot.sale_product_pivot_id;
                                    cell1.appendChild(hidInput1);
                            }

                            var cell2=row.insertCell(1);
                            var t2=document.createElement("input");
                                t2.name = "qty[]";
                                t2.id = "qty_"+row_id;
                                t2.value = product.pivot.product_quantity;
                                t2.style = "width:100px;";
                                t2.className ="form-control num_txt";
                                $(t2).attr("required", true);
                                t2.addEventListener('blur', function(){ app.checkQty(t2); });
                                cell2.appendChild(t2);                            
                               
                            var cell3=row.insertCell(2);

                            var t3=document.createElement("select");
                                t3.name = "uom[]";
                                t3.id = "uom_"+row_id;
                                t3.className = "form-control txt_uom";
                                t3.style = "width:150px;";
                                $(t3).attr("required", true);
                                if(response.data.data.return_method == 'with invoice') {
                                    $(t3).attr('disabled', true);
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
                                if(response.data.data.return_method == 'with invoice') {
                                    $(rate).attr('readonly', true);
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
                                discount.addEventListener('blur', function(){ app.calTotalAmount(discount); });
                                if(response.data.data.return_method == 'with invoice') {
                                    $(discount).attr('readonly', true);
                                }
                                cell_discount.appendChild(discount);

                            var cell_actual=row.insertCell(5);
                            var actual_rate=document.createElement("input");
                                actual_rate.name = "actual_rate[]";
                                actual_rate.id = "actual_rate_"+row_id;
                                actual_rate.value = product.pivot.actual_rate;
                                actual_rate.style = "width:100px;";
                                actual_rate.className ="form-control num_txt";
                                $(actual_rate).attr("required", true);
                                if(response.data.data.return_method == 'with invoice') {
                                    $(actual_rate).attr("readonly", true);
                                }
                                actual_rate.addEventListener('blur', function(){ app.calTotalAmount(actual_rate); });
                                cell_actual.appendChild(actual_rate);

                            

                            $(".txt_product").select2();

                            $(".txt_uom").select2();

                            $("#customer_id").select2();
                            $("#customer_id").on("select2:select", function(e) {

                                var data = e.params.data;
                                app.form.customer_id = data.id;

                                 axios.get("/customer_previous_balance/"+data.id).then(function(response) {
                                    app.form.previous_balance = response.data.previous_balance;
                                    app.form.pay_amount = response.data.customer_advance;
                                });
                            });                            

                            var cell5=row.insertCell(6);
                                cell5.className = "text-center";
                            var t5=document.createElement("input");
                                t5.type = "checkbox";
                                t5.name = "chk_foc[]";
                                t5.id = "foc_"+row_id;

                                $(t5).attr('disabled', true);
                                if(product.pivot.is_foc == '1') {
                                    $(t5). prop("checked", true);
                                }

                                t5.addEventListener('change', function(){ app.checkFoc(t5); });
                                cell5.appendChild(t5);

                            var cell_other_disc=row.insertCell(7);
                            var other_discount=document.createElement("input");
                                other_discount.name = "other_discount[]";
                                other_discount.id = "other_discount_"+row_id;
                                other_discount.style = "width:70px;";
                                other_discount.value = product.pivot.other_discount == null ? '' : product.pivot.other_discount;
                                other_discount.className ="form-control num_txt";
                                other_discount.addEventListener('blur', function(){ app.calTotalAmount(other_discount); });
                                if(response.data.data.return_method == 'with invoice') {
                                    $(other_discount).attr('readonly', true);
                                }
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
                    });

                    app.form.all_total_amount  = response.data.data.total_amount;
                    app.return_total_amount = response.data.data.total_amount;
                    app.form.return_amount= response.data.data.payment_amount;
                    app.form.balance_amount= response.data.data.balance_amount;
                    
                    $("#loading").hide();


                })
                .catch(function(error) {
                  // handle error
                  console.log(error);
                })
                .then(function() {
                  // always executed
                  //app.original_form = $("#sale_form").serialize();
                });

                $(".txt_uom").select2();
            },

            getSale(id) {
              let app = this;
              $("#loading").show();
              axios
                .get("/sale/" + id)
                .then(function(response) {
                    if(response.data.sale.office_sale_man_id != null) {
                        $('#office_sale_man_id').val(response.data.sale.office_sale_man_id).trigger('change');
                        app.form.office_sale_man_id = response.data.sale.office_sale_man_id;
                    } 
                    app.form.branch_id = response.data.sale.branch_id;
                    app.form.warehouse_id = response.data.sale.warehouse_id;
                    app.user_branch = response.data.sale.branch.branch_name;
                    app.user_warehouse = response.data.sale.warehouse.warehouse_name;
                    var sale_discount = response.data.sale.cash_discount == null ? 0 : response.data.sale.cash_discount;
                    app.form.sale_discount =  parseInt(sale_discount) - parseInt(response.data.sale.return_discount);
                    app.sale_discount = parseInt(sale_discount) - parseInt(response.data.sale.return_discount);

                    if(app.form.payment_type == 'credit') {
                        app.required_val = true;
                    } else {
                        app.required_val = false;
                    }
                    app.ex_products = response.data.sale.products;

                    //add products dynamically
                    var subTotal = 0;
                    var balAmount = 0;
                    var row_id = 0;
                    $('#product_table tbody tr').slice(0, -4).remove(); 
                    $('#sale_product_table tbody tr').slice(0, -4).remove();
                    $('#return_product_table tbody tr').slice(0, -4).remove();

                    $.each(app.ex_products, function( key, product ) {                       
                        row_id = row_id+1; 
                            if(!app.isEdit) {
                                var table=document.getElementById("sale_product_table");
                            } else {
                                var table=document.getElementById("return_product_table");
                            }
                            var row=table.insertRow((table.rows.length) - 4);
                            row.id = row_id;
                            var cell1=row.insertCell(0);

                            var t1=document.createElement("select");
                                t1.name = "product[]";
                                t1.id = "product_"+row_id;
                                t1.className = "form-control txt_product";
                                t1.style = "min-width:150px;";
                                $(t1).attr("required", true);
                                $(t1).attr('disabled', true);

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

                            var hidInput=document.createElement("input");
                                hidInput.type = 'hidden';
                                hidInput.name = "valid_qty[]";
                                hidInput.id = "valid_qty_"+row_id;
                                hidInput.value = parseInt(product.pivot.product_quantity) - parseInt(product.pivot.return_quantity);
                                cell1.appendChild(hidInput);

                             var hidInput1=document.createElement("input");
                                hidInput1.type = 'hidden';
                                hidInput1.name = "product_sale_id[]";
                                hidInput1.id = "product_sale_id_"+row_id;
                                hidInput1.value = product.pivot.id;
                                cell1.appendChild(hidInput1);

                            var cell2=row.insertCell(1);
                            var t2=document.createElement("input");
                                t2.name = "qty[]";
                                t2.id = "qty_"+row_id;
                                t2.value = parseInt(product.pivot.product_quantity) - parseInt(product.pivot.return_quantity);
                                t2.style = "width:100px;";
                                t2.className ="form-control num_txt";
                                $(t2).attr("required", true);

                                if(response.data.sale.order_id != null) {
                                    $(t2).attr('readonly', true);
                                }

                                t2.addEventListener('blur', function(){ app.checkQty(t2); });
                                cell2.appendChild(t2);                            
                               
                            var cell3=row.insertCell(2);

                            var t3=document.createElement("select");
                                t3.name = "uom[]";
                                t3.id = "uom_"+row_id;
                                t3.className = "form-control txt_uom";
                                t3.style = "width:150px;";
                                $(t3).attr("required", true);
                                $(t3).attr('disabled', true);
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
                                $(rate).attr('readonly', true);
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
                                $(discount).attr('readonly', true);
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

                            var sale_total_amt = (parseInt(product.pivot.product_quantity) - parseInt(product.pivot.return_quantity)) * parseInt(product.pivot.actual_rate);

                            $(".txt_product").select2();

                            $(".txt_uom").select2();

                            $("#customer_id").select2();
                            $("#customer_id").on("select2:select", function(e) {

                                var data = e.params.data;
                                app.form.customer_id = data.id;

                                 axios.get("/customer_previous_balance/"+data.id).then(function(response) {
                                    app.form.previous_balance = response.data.previous_balance;
                                    app.form.pay_amount = response.data.customer_advance;
                                });
                            });                            

                            var cell5=row.insertCell(6);
                                cell5.className = "text-center";
                            var t5=document.createElement("input");
                                t5.type = "checkbox";
                                t5.name = "chk_foc[]";
                                t5.id = "foc_"+row_id;
                                $(t5).attr('disabled', true);
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
                                $(other_discount).attr('readonly', true);
                                cell_other_disc.appendChild(other_discount);

                            var cell7=row.insertCell(8);
                            var t7=document.createElement("input");
                                t7.name = "total_amount[]";
                                t7.id = "total_amount_"+row_id;
                                t7.style = "width:100px;";
                                if(product.pivot.total_amount != 0 && product.pivot.total_amount != null) {
                                   // t7.value = product.pivot.total_amount;
                                   // subTotal += parseInt(product.pivot.total_amount);
                                   t7.value = sale_total_amt;
                                   subTotal += parseInt(sale_total_amt);
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

                            if(row_id == app.ex_products.length) {
                                app.calAllTotal(subTotal);
                            }
                    });

                    /*app.form.all_total_amount  = response.data.sale.total_amount;
                    if(app.form.payment_type == 'cash') {
                        app.form.return_amount= parseInt(response.data.sale.total_amount)- parseInt(app.sale_discount);
                        app.form.balance_amount= 0;
                    } else {
                        app.form.balance_amount= response.data.sale.balance_amount;
                        app.form.return_amount= 0;
                    }
                    
                    $("#loading").hide();*/


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

            calAllTotal($subTotal) {
                let app = this;
                app.form.all_total_amount  = $subTotal;
                if(app.form.payment_type == 'cash') {
                    app.form.return_amount= parseInt($subTotal)- parseInt(app.sale_discount);
                    app.form.balance_amount= 0;
                } else {
                    app.form.balance_amount= parseInt($subTotal)- parseInt(app.sale_discount);
                    app.form.return_amount= 0;
                }
                
                $("#loading").hide();
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
                //alert($("#valid_qty_"+row_id).val());
                if(app.form.return_method == 'with invoice') {
                    var r_qty = parseInt($("#qty_"+row_id).val());
                    var valid_qty = parseInt($("#valid_qty_"+row_id).val());
                    if(r_qty > valid_qty) {
                        swal("Warning!", "Return quantity is more than sale quantity!", "warning");
                        $("#qty_"+row_id).val('');
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

                        /**var key = this.products.findIndex(x => x.product_id == product_id);
                        var available_qty = parseFloat(this.products[key].in_count) - parseFloat(this.products[key].out_count);
                        if(product_qty > available_qty) {

                            swal("Warning!", "Not enough quantity! Availabel quantity is "+available_qty+" "+uom_name+".", "warning");
                        }***/
                    }
                    app.calTotalAmount(obj);
                }
            },

            calTotalAmount(obj) {
               let app = this;

               var row_id = $(obj).closest('tr').attr('id');
               var qty = $("#qty_"+row_id).val() == "" ? 0 : $("#qty_"+row_id).val();
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
                               //get all sub total amount
               var sub_total = 0;
               for(var i=0; i<document.getElementsByName('product[]').length; i++) {
                    if(document.getElementsByName('total_amount[]')[i].value != "") {
                        sub_total += parseFloat(document.getElementsByName('total_amount[]')[i].value);
                    }
               }

               app.form.all_total_amount = Math.round(sub_total);
               var disc = this.form.sale_discount == '' ? 0 : this.form.sale_discount;

               if(this.form.payment_type == 'credit') {
                    this.returnReadonly = false;
                    
                } else {
                    this.returnReadonly = true;  
                    this.form.return_amount = parseInt(sub_total) - parseInt(disc); 
                }

               app.form.balance_amount = (parseInt(app.form.all_total_amount) - parseInt(app.form.return_amount)) - parseInt(disc);
                
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
                    if(pay_amount == 0) {
                        app.form.pay_amount = parseInt(app.form.net_total) + parseInt(app.form.tax_amount);
                        app.form.balance_amount = 0;
                    } else {
                        app.form.balance_amount = (parseInt(app.form.net_total) + parseInt(app.form.tax_amount))-parseInt(pay_amount);
                    }
                    
                } else {
                    app.form.balance_amount = (parseInt(app.form.net_total) + parseInt(app.form.tax_amount))-parseInt(pay_amount);
                }
            },

            changePaidAmount() {
                let app = this;
                var total_amount = app.form.all_total_amount == '' || app.form.all_total_amount == null ? 0 : app.form.all_total_amount;
                var return_amount = app.form.return_amount == '' || app.form.return_amount == null ? 0 : app.form.return_amount;
                var sale_discount = app.form.sale_discount == null || app.form.sale_discount == '' ? 0 : app.form.sale_discount;

                if(parseInt(app.form.sale_discount) > parseInt(app.sale_discount)) {
                    swal("Warning!", "Invalid! Your discount is more than sale discount.", "warning");
                    app.form.sale_discount = app.sale_discount;
                }
                if(app.form.payment_type == 'cash') {
                    app.form.return_amount = parseInt(total_amount) - parseInt(sale_discount);
                    app.form.balance_amount = 0;
                } else {
                    app.form.balance_amount = (parseInt(total_amount) - parseInt(sale_discount)) - parseInt(return_amount);
                }
                
            },

            changeTax() {
                let app = this;
                var tax = app.form.tax == '' || app.form.tax == null ? 0 : app.form.tax;
                var tax_amount = parseInt(tax)/100 * parseInt(app.form.net_total);
                app.form.tax_amount = tax_amount;
                var pay_amount = app.form.pay_amount == '' || app.form.pay_amount == null ? 0 : app.form.pay_amount;
                if(app.form.payment_type == 'cash') {
                    if(pay_amount == 0) {
                        app.form.pay_amount = parseInt(app.form.net_total) + parseInt(app.form.tax_amount);
                        app.form.balance_amount = 0;
                    } else {
                        app.form.balance_amount = (parseInt(app.form.net_total) + parseInt(app.form.tax_amount))-parseInt(pay_amount);
                    }
                    
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
                    if(app.form.pay_amount == 0) {
                        app.form.pay_amount = parseInt(app.form.sub_total) - parseInt(discount);
                    }
                }

                if(discount > app.form.sub_total) {
                    swal("Warning!", "Discount amount is greater than sub total amount", "warning");
                } else {
                    app.form.balance_amount = parseInt(app.form.sub_total) - (parseInt(app.form.pay_amount)+parseInt(discount));
                }
            },

            onSubmit: function(event){
                let app = this;
                app.form.payment_type = $("#payment_type").val();

                if(app.form.payment_type == 'cash') {
                    if(app.form.balance_amount > 0 || app.form.balance_amount < 0) {
                        swal("Warning!", "Balance must be zero for cash payment!", "warning");
                        return false;
                    }
                }

                if(app.form.return_method == 'with invoice') {
                    var payment_type = document.getElementById('sale_id').options[document.getElementById('sale_id').options.selectedIndex].dataset.ptype;

                    var sale_bal = parseInt(document.getElementById('sale_id').options[document.getElementById('sale_id').options.selectedIndex].dataset.balance) + parseInt(app.return_total_amount);
                    
                    if(sale_bal > 0 && app.return_status == '') {
                        if(payment_type == "credit") {
                            if((parseInt(app.form.all_total_amount) - parseInt(app.form.sale_discount)) > sale_bal) {
                                swal("Warning!", "Total amount is more than balance amount. Please check!", "warning");
                                return false;
                            }
                        }
                    }
                }
                //return false;
                $('#loading').show();

                if( app.form.balance_amount < 0 || app.form.return_amount<0) {
                    swal("Warning!", "Invalid balance amount. Please check!", "warning");
                    $('#loading').hide();
                    return false;
                }

                if(app.form.return_method == 'without invoice' && app.form.balance_amount > app.form.outstanding_amount) {
                    swal("Warning!", "Balance amount is more than outstanding amount. Please check!", "warning");
                    $('#loading').hide();
                    return false;
                }

                //return false;
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
                app.form.reference_no = $("#reference_no").val();
                
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
                        if(app.form.return_method=="with invoice") {
                            app.form.product_sale_id.push(document.getElementsByName('product_sale_id[]')[i].value);
                        }
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
                    }
                    //console.log(app.form.all_total_amount);
                    //console.log(app.form.is_foc); return false;

                    this.form
                      .post("/sale_return")
                      .then(function(data) {
                        console.log(data.data);
                        if(data.status == "success") {

                            //reset form data
                            event.target.reset();
                            $(".txt_product").select2();
                            $('#loading').hide();
                            swal({
                                title: "Success!",
                                text: 'Sale Return is saved.',
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
                    //app.edit_form = $("#sale_form").serialize();
                   
                    /**if(app.edit_form == app.original_form) {
                        swal("Warning!", "Please edit at least one field", "warning");
                        $("#loading").hide();
                    } else { **/
                        app.form.product = [];

                    for(var i=0; i<document.getElementsByName('product[]').length; i++) {
                        app.form.product.push(document.getElementsByName('product[]')[i].value);
                        if(app.form.return_method=="with invoice") {
                            app.form.product_sale_id.push(document.getElementsByName('product_sale_id[]')[i].value);
                        }
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
                    }

                        this.form
                          .patch("/sale_return/" + app.return_id)
                          .then(function(data) {
                            if(data.status == "success") {

                                //reset form data
                                event.target.reset();
                                $(".txt_product").select2();
                                $('#loading').hide();

                                swal({
                                    title: "Success!",
                                    text: 'Sale Return is updated.',
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
                        .catch(function (response) {
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