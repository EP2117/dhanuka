<template>
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="#!"><i class="feather icon-home"></i></a></li>
                <li class="breadcrumb-item"><a :href="site_path+'/'">Home</a></li>
                <li class="breadcrumb-item" v-if="purchase_type == 1"><a :href="site_path+'/purchase_office'">Purchase Office</a></li>
<!--                <li class="breadcrumb-item" v-else><a :href="site_path+'/van'">Van Sale</a></li>-->
                <li class="breadcrumb-item"><router-link tag="span" :to="'/purchase/1/'" class="font-weight-normal"><a href="#">Purchase Invoice</a></router-link></li>
                <li class="breadcrumb-item active" aria-current="page">Purchase Invoice Form</li>
            </ol>
        </nav>
        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h4 class="mb-0 text-gray-800">Purchase Invoice Form</h4>
        </div>

        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Entry Details</h6>
            </div>
            <div class="card-body">
                <div class="d-block">
                    <!-- form start -->
                    <form class="form" id="purchase_form" method="post" @submit.prevent="onSubmit" @keydown="form.errors.clear($event.target.name)">
                        <div class="row">
                            <div class="form-group col-md-4">
                                <label for="invoice_no">Invoice No.</label>
                                <input type="text" class="form-control" id="invoice_no" name="invoice_no" v-model="form.invoice_no" readonly>
                            </div>
                            <div class="form-group col-md-4">
                                <label for="invoice_date">Date</label>
                                <input type="text" class="form-control datetimepicker" id="invoice_date" name="invoice_date"
                                       v-model="form.invoice_date" required :readonly="is_readonly">
                            </div>
                            <div class="form-group col-md-4">
                                <label >User</label>
                                <input type="text" class="form-control" id="office_purchase_man" name="office_purchase_man"
                                       v-model="form.office_purchase_man" readonly>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="form-group col-md-4">
                                <label for="supplier_id">Supplier</label>
                                <select id="supplier_id" class="form-control mm-txt"
                                        name="supplier_id" v-model="form.supplier_id" style="width:100%" required :disabled="is_readonly">
                                    <option value="">Select One</option>
                                    <option v-for="sup in suppliers" :value="sup.id"  >{{sup.name}}</option>
                                </select>
                            </div>
                            <div class="form-group col-md-4">
                                <label for="vehicle_warehouse">Vehicle Warehouse</label>
                                <input type="text" class="form-control" id="vehicle_warehouse" name="vehicle_warehouse"
                                       v-model="user_warehouse" readonly>
                            </div>
                            <div class="form-group col-md-4" >
                                <label for="reference_no">Reference No.</label>
                                <input type="text" class="form-control" id="reference_no" name="reference_no"
                                       v-model="form.reference_no">
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="form-group col-md-4">
                                <label for="payment_type">Payment Type</label>
                                <select id="payment_type" class="form-control"
                                        name="payment_type" v-model="form.payment_type" @change='changePayment()' style="width:100%" required :disabled="is_readonly"
                                >
                                    <option value="">Select One</option>
                                    <option value="cash">Cash</option>
                                    <option value="credit">Credit</option>
                                </select>
                            </div>
                            <div class="form-group col-md-4" v-if="form.payment_type == 'credit'">
                                <label for="due_date">Due Date</label>
                                <input type="text" class="form-control datetimepicker" id="due_date" name="due_date"
                                       v-model="form.due_date" :readonly="is_readonly">
                            </div>
                            <div class="form-group col-md-4" style="display:none;" v-else>
                                <label for="due_date">Due Date</label>
                                <input type="text" class="form-control datetimepicker" id="due_date" name="due_date"
                                       v-model="form.due_date" :readonly="is_readonly">
                            </div>
                            <div class="form-group col-md-4" v-if="form.payment_type == 'credit'">
                                <label for="credit_day">Credit Days</label>
                                <input type="text" class="form-control num_txt" id="credit_day" name="credit_day"
                                       v-model="form.credit_day" @blur="calcDueDate()" :readonly="is_readonly">
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="form-group col-md-4">
                                <label for="currency_id">Currency</label>
                                <select class="form-control"
                                        name="currency_id" id="currency_id" style="min-width:100px;" v-model="form.currency_id"
                                >
                                    <option v-for="c in currency" :value="c.id" :data-sign="c.sign">{{c.name}}</option>
                                </select>
                            </div>
                            <div class="form-group col-md-4">
                                <label>&nbsp;</label>
                                <div id="currency_div" v-if="!isMMK"> <label class="sign">{{sign}}</label> 1 = ( <input type="text" style="width:100px;display:inline-block;" class="form-control decimal_no" id="currency_rate" name="currency_rate" v-model="form.currency_rate"> ) MMK</span></div>
                            </div>
                        </div>
                        <div class="row mt-4 mb-3">
                            <div class="col-md-12">
                                <span class="d-none d-sm-inline-block btn-sm btn-primary shadow-sm bg-blue"><i class="fas fa-search-plus text-white"></i> Product Details</span>
                            </div>
                        </div>

                        <div class="row mt-0">
                            <div class="col-md-12 text-right mt-0">
                                <!--<a class="d-sm-inline-block btn btn-sm btn-primary shadow-sm bg-blue text-white"  v-if="((user_role == 'admin' || user_role == 'system') && !isDisabled) || (!isEdit)"  data-toggle="modal" data-target="#mix_form">
                                    <i class="fas fa-plus"></i> Add Mixed Product
                                </a>-->
                                <a class='d-sm-inline-block btn btn-sm btn-primary shadow-sm bg-blue text-white' title='Add Product' @click="addProduct()" v-if="((user_role == 'admin' || user_role == 'system' || user_role == 'office_user') && !isDisabled) || (!isEdit)" style="verticle-align:middle"><i class='fas fa-plus'></i></a>
                                <!--<a class='blue-icon' title='Add Product' @click="addProduct()" v-if="((user_role == 'admin' || user_role == 'system') && !isDisabled) || (!isEdit)" style="verticle-align:middle"><i class='fas fa-plus-square' style='font-size: 30px;'></i></a>-->
                                <div style="display:none;">
                                    <select class="form-control txt_product"
                                            id="txt_product" style="min-width:150px;">
                                        <option value="">Select One</option>
                                        <option v-for="product in products" :data-uom="product.uom_name"
                                                :data-price="product.product_price"
                                                :data-retail1="product.retail1_price"
                                                :data-retail2="product.retail2_price"
                                                :data-wholesale="product.wholesale_price"
                                                :data-purchase="product.purchase_price"
                                                :data-uomid="product.uom_id" :value="product.product_id"
                                                data-pivotid = "0">{{product.product_name}}</option>
                                    </select>
                                </div>
                                <div style="display:none;">
                                    <select class="form-control brands"
                                            id="txt_brand" style="min-width:150px;"
                                    >
                                        <option value="">Select One</option>
                                        <option v-for="brand in brands" :value="brand.id">{{brand.brand_name}}</option>
                                    </select>
                                </div>
                                <div style="display:none;">
                                    <select class="form-control categories"
                                            id="txt_category" style="min-width:150px;"
                                    >
                                        <option value="">Select One</option>
                                        <option v-for="cat in categories" :value="cat.id">{{cat.category_name}}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-12 table-responsive mt-3">
                                <table class="table table-bordered" id="product_table">
                                    <thead class="thead-grey">
                                    <tr>
<!--                                        <th scope="col">Brand</th>-->
<!--                                        <th scope="col" >Category</th>-->
                                        <th scope="col" class="mm-txt">အမျိုးအမည်</th>
                                        <th scope="col" class="mm-txt">အရေအတွက်</th>
                                        <th scope="col" >Purchase Unit</th>
<!--                                        <th scope="col" >Stock Available</th>-->
                                        <th scope="col" class="mm-txt" v-if="!isMMK">စျေးနှုန်း(<label class="sign">{{sign}}</label>)</th>
                                        <th scope="col" class="mm-txt">စျေးနှုန်း(MMK)</th>
                                        <th scope="col" class="mm-txt" v-if="!isMMK">သင့်ငွေ(<label class="sign">{{sign}}</label>)</th>
                                        <th scope="col" class="mm-txt">သင့်ငွေ(MMK)</th>
                                        <th scope="col" class="text-center"></th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <template v-if="isEdit && ex_products.length > 0">
                                    </template>
                                    <template v-else>
                                        <tr id="1">
<!--                                            <td>-->
<!--                                                <select class="form-control brands"-->
<!--                                                        name="brand[]" id="brand_1" style="min-width:150px"-->
<!--                                                >-->
<!--                                                    <option value="">Select One</option>-->
<!--                                                    <option v-for="brand in brands" :value="brand.id">{{brand.brand_name}}</option>-->
<!--                                                </select>-->
<!--                                            </td>-->
<!--                                            <td>-->
<!--                                                <select class="form-control categories"-->
<!--                                                        name="category[]" id="category_1" style="min-width:150px;"-->
<!--                                                >-->
<!--                                                    <option value="">Select One</option>-->
<!--                                                    <option v-for="cat in categories" :value="cat.id">{{cat.category_name}}</option>-->
<!--                                                </select>-->
<!--                                            </td>-->
                                            <td>
                                                <select class="form-control txt_product"
                                                        name="product[]" id="product_1" style="min-width:200px;" required>
                                                    <option value="">Select One</option>
                                                    <option v-for="product in products" :data-uom="product.uom_name"
                                                            :data-price="product.product_price"
                                                            :data-retail1="product.retail1_price"
                                                            :data-retail2="product.retail2_price"
                                                            :data-wholesale="product.wholesale_price"
                                                            :data-purchase="product.purchase_price"
                                                            :data-uomid="product.uom_id" :value="product.product_id"
                                                            data-pivotid = "0">{{product.product_name}}</option>
                                                </select>
                                            </td>
                                            <td>
                                                <input type="text" class="form-control decimal_no txt_qty" name="qty[]" style="width:150px;"   id="qty_1"  required />
                                            </td>
                                            <td>
                                                <select class="form-control txt_uom"
                                                        name="uom[]" id="uom_1" style="min-width:100px;" data-uom="" required
                                                >
                                                    <option value="">Select One</option>
                                                </select>
                                            </td>
<!--                                            <td>-->
<!--                                                <input type="text" style="min-width:120px;" class="form-control txt_relation" name="relation[]" id="relation_1" readonly />-->
<!--                                            </td>-->
<!--                                            <td class="text-center">-->

<!--                                                <input type="text" style="min-width:100px;" class="form-control txt_available" name="stock_available[]" id="stock_available_1" readonly />-->
<!--                                            </td>-->

                                            <td v-if="!isMMK">
                                                <input type="text" class="form-control decimal_no unit_price_fx_select" style="width:90px;" name="unit_price_fx[]" id="unit_price_fx_1" :required="!isMMK"  />
                                            </td>
                                            <td>
                                                <!-- <input type="text" class="form-control float_num" style="width:100px;" name="unit_price[]" @blur="calTotalAmount($event.target)" required /> -->
                                                <input type="text" class="form-control decimal_no unit_price_select" style="width:90px;" name="unit_price[]" id="unit_price_1"  :readonly="!isMMK" required @input="calAmt($event.target.id)" />
<!--                                                <select class="form-control float_num unit_price_select" style="width:90px;" name="unit_price[]" id="unit_price_1" required>-->
<!--                                                    <option value="">Select One</option>-->
<!--                                                </select>-->
                                            </td>
                                            <td v-if="!isMMK">
                                                <input type="text" class="form-control decimal_no" readonly style="width:100px;" name="total_amount_fx[]" id="total_amount_fx_1" />
                                            </td>
                                            <td>
                                                <input type="text" class="form-control num_txt" readonly style="width:100px;" name="total_amount[]" id="total_amount_1" required  :readonly="!isMMK" />
                                            </td>
                                            <td class="text-center">
                                                <a class='remove-row red-icon' title='Remove' v-if="user_role != 'admin'"><i class='fas fa-times-circle' style='font-size: 25px;'></i></a>
                                            </td>
                                        </tr>
                                    </template>
                                    <tr class="total_row total_row_first">
                                        <td :colspan="total_colspan" class="text-right mm-txt">စုစုပေါင်း</td>
                                        <td v-if="!isMMK" class="p-0 m-0 pt-2">
                                            <div style="display:inline-block;">
                                                <label style="display:inline-block;">{{sign}}</label>
                                                <input type="text" v-model="form.sub_total_fx" class="form-control decimal_no" readonly style="width:100px;display:inline-block;" />
                                            </div>
                                        </td>

                                        <td colspan="2" class="p-0 m-0 pt-2">
                                            <div style="display:inline-block;">
                                                <label style="display:inline-block;">MMK</label>
                                                <input type="text" v-model="form.sub_total" class="form-control num_txt" readonly style="width:100px;display:inline-block;" required />
                                            </div>
                                        </td>
                                    </tr>
                                    <tr class="total_row">
                                        <td :colspan="total_colspan" class="text-right mm-txt">လျော့ငွေ</td>
                                        <td v-if="!isMMK" class="p-0 m-0 pt-2">
                                            <div style="display:inline-block;">
                                                <label style="display:inline-block;">{{sign}}</label>
                                                <input type="text" v-model="form.discount_fx" class="form-control decimal_no" style="width:100px;display:inline-block;" @keyup="changeDiscountFx($event.target)" />
                                            </div>
                                        </td>
                                        </td>
                                        <td colspan="2" class="p-0 m-0 pt-2">
                                            <div style="display:inline-block;">
                                                <label style="display:inline-block;">MMK</label>
                                                <input type="text" v-model="form.discount" class="form-control num_txt" style="width:100px;display:inline-block;" @keyup="changeDiscount($event.target)" :readonly="!isMMK" />
                                            </div>
                                        </td>
                                    </tr>
                                    <tr class="total_row">
                                        <td :colspan="total_colspan" class="text-right mm-txt">လက်ခံရရှိငွေ</td>
                                        <td v-if="!isMMK" class="p-0 m-0 pt-2">
                                            <div style="display:inline-block;">
                                                <label style="display:inline-block;">{{sign}}</label>
                                                <input type="text" v-model="form.pay_amount_fx" class="form-control decimal_no" style="width:100px;display:inline-block;" @keyup="calBalance($event.target)" />
                                            </div>
                                        </td>
                                        <td colspan="2" v-if="form.payment_type == 'credit'" class="p-0 m-0 pt-2">
                                            <div style="display:inline-block;">
                                                <label style="display:inline-block;">MMK</label>
                                                <input type="text" v-model="form.pay_amount" class="form-control num_txt" style="width:100px;display:inline-block;" @keyup="calBalance($event.target)" :readonly="!isMMK" />
                                            </div>
                                        </td>
                                        <td colspan="2" class="p-0 m-0 pt-2" v-else>
                                            <div style="display:inline-block;">
                                                <label style="display:inline-block;">MMK</label>
                                                <input type="text" v-model="form.pay_amount" class="form-control num_txt" style="width:100px;display:inline-block;" @keyup="calBalance($event.target)" :readonly="!isMMK"  />
                                            </div>
                                        </td>
                                    </tr>
                                    <tr class="total_row">                                        
                                        <td :colspan="total_colspan" class="text-right mm-txt">လက်ကျန်ငွေ စုစုပေါင်း</td>
                                        <td v-if="!isMMK" class="p-0 m-0 pt-2">
                                            <div style="display:inline-block;">
                                                <label style="display:inline-block;">{{sign}}</label>
                                                <input type="text" v-model="form.balance_amount_fx" class="form-control decimal_no" readonly style="width:100px; display:inline-block;" />
                                            </div>
                                        </td>
                                        <td colspan="2" class="p-0 m-0 pt-2">
                                            <div style="display:inline-block;">
                                                <label style="display:inline-block;">MMK</label>
                                                <input type="text" v-model="form.balance_amount" class="form-control num_txt" readonly style="width:100px;display:inline-block;" required />
                                            </div>
                                        </td>
                                    </tr>

                                    <tr class="total_row" v-show="form.payment_type =='cash' || (form.payment_type =='credit' && form.pay_amount != 0 && form.pay_amount != null)">
                                        <td colspan="2" class="text-right mm-txt">Payment Method</td>
                                        <td colspan="2">
                                            <select class="form-control" id="account_group"
                                                    v-model="form.account_group" style="width:100%" @change="changeAccountGroup()">
                                                <option value="">Select One</option>
                                                <option v-for="at in account_group" :value="at.id"  >{{at.name}}</option>
                                            </select>
                                        </td>
                                        <td colspan="2">
                                            <select class="form-control"
                                                    v-model="form.cash_bank_account" style="width:100%" id="cash_bank_account">
                                                <option value="">Select One</option>
                                                <option v-for="at in cash_bank_accounts" :value="at.id"  >{{at.sub_account_name}}</option>
                                            </select>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>


                            </div>

                        </div>

                        <div class="row" >
                            <div class="col-md-12" v-if="!isEdit">
                                <input type="submit" class="btn btn-primary btn-sm" value="Save Entry"  :disabled = "isDisabled">
                            </div>

                            <div class="col-md-12" v-if="(user_role == 'system' || user_role == 'admin' || user_role == 'office_user') && isEdit && !isDisabled">
                                <input type="submit" class="btn btn-primary btn-sm" value="Update">
                            </div>

                        </div>

                    </form>
                    <!-- form end -->
                </div>
                <!-- Mix Product form Modal Start -->
                <div class="modal" id="mix_form">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">

                            <!-- Modal Header -->
                            <div class="modal-header">
                                <h4 class="modal-title">Create Mixed Product</h4>
                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                            </div>

                            <!-- Modal body -->
                            <div class="modal-body mix-body">
                                <div class="col-md-12 table-responsive">
                                    <table class="table table-bordered" id="mixed_product_tbl">
                                        <thead class="thead-grey">
                                        <tr>
                                            <td colspan='5' style="background-color:#bddffc;"><strong>Mixed Product Details</strong></td>
                                        </tr>
                                        <tr>
                                            <th scope="col" >Description</th>
                                            <th scope="col" >Qty</th>
                                            <th scope="col" >UOM</th>
                                            <th scope="col" >Unit Price</th>
                                            <th scope="col" >Total Amount</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <tr>
                                            <td>
                                                <input type="text" class="form-control" style="min-width:200px;" id="mix_description" required />
                                            </td>
                                            <td>
                                                <input type="text" class="form-control num_txt" style="width:100px;" id="mix_qty" required />
                                            </td>
                                            <td>
                                                <select class="form-control txt_uom"
                                                        id="mix_uom" style="min-width:100px;" data-uom="" required>
                                                    <option value="">Select One</option>
                                                </select>
                                            </td>
                                            <td>
                                                <input type="text" class="form-control num_txt" style="width:100px;" id="mix_unit_price" required />
                                            </td>
                                            <td>
                                                <input type="text" class="form-control num_txt" style="width:100px;" id="mix_total_amount" readonly required />
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <!-- used product start -->
                                <div class="col-md-12 table-responsive mt-2">
                                    <table class="table table-bordered" id="used_product_tbl">
                                        <thead class="thead-grey">
                                        <tr>
                                            <td colspan='3' style="background-color:#bddffc;">
                                                <div class="float-left">
                                                    <strong>Used Product Details</strong>
                                                </div>
                                                <div class="text-right">
                                                    <a class='d-sm-inline-block btn btn-sm btn-primary shadow-sm bg-blue text-white' title='Add Product' @click="addUsedProduct()"><i class='fas fa-plus'></i></a>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th scope="col" >Product</th>
                                            <th scope="col" >Qty</th>
                                            <th scope="col" >UOM</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <tr>
                                            <td>
                                                <input type="text" class="form-control" style="min-width:200px;" id="mix_description" required />
                                            </td>
                                            <td>
                                                <input type="text" class="form-control num_txt" style="width:100px;" id="mix_qty" required />
                                            </td>
                                            <td>
                                                <select class="form-control txt_uom"
                                                        id="mix_uom" style="min-width:150px;" data-uom="" required
                                                >
                                                    <option value="">Select One</option>
                                                </select>
                                            </td>
                                        </tr>

                                        </tbody>
                                    </table>
                                </div>
                                <!-- used product end -->
                            </div>

                            <!-- Modal footer -->
                            <div class="modal-footer">
                                <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                            </div>

                        </div>
                    </div>
                </div>
                <!-- mix product form END -->

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
                supplier_id: "",
                office_purchase_man: "",
                office_purchase_man_id: "",
                product: [],
                uom: [],
                qty: [],
                unit_price: [],
                unit_price_fx: [],
                total_amount: [],
                total_amount_fx: [],
                sub_total: 0,
                sub_total_fx: 0,
                pay_amount: 0,
                pay_amount_fx: 0,
                balance_amount:0,
                balance_amount_fx:0,
                ex_product_pivot: [],
                product_pivot: [],
                is_foc: [],
                uom_id: "",
                price_variants: [],
                purchase_type: '',
                reference_no: '',
                payment_type: 'cash',
                credit_day: '',
                due_date: '',
                duplicate_ref_no: false,
                discount: 0,
                discount_fx: 0,
                previous_balance: '',
                currency_id: 1,
                currency_rate: '',
                account_group: '',
                cash_bank_account: '',

            }),
            isEdit: false,
            isMMK: true,
            brands: [],
            currency: [],
            categories: [],
            products: [],
            uoms: [],
            purchase_id: '',
            ex_products: [],
            user_warehouse: '',
            selling_uoms: [],
            suppliers: [],
            purchase_type: '',
            user_role: '',
            user_year: '',
            is_readonly: false,
            required_val : false,
            isDisabled: false,
            original_form: '',
            edit_form: '',
            site_path: '',
            storage_path: '',
            sign: '',
            total_colspan : 4,
            prev_pay_amount: 0,
            currency_type: 1,
            account_group: [],
            cash_bank_accounts: [],
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

        this.form.office_purchase_man = document.querySelector("meta[name='user-name-likelink']").getAttribute('content');
        // this.form.office_sale_man_id = document.querySelector("meta[name='user-id-likelink']").getAttribute('content');

        this.user_role = document.querySelector("meta[name='user-role']").getAttribute('content');
         if(this.user_role != "admin" && this.user_role != "system" && this.user_role != "office_user") {
            var url =  window.location.origin;
            window.location.replace(url);
        }
        // if(this.user_role == "office_order_user") {
        // }
        // if(this.user_role == "admin" && !this.isEdit) {
        //     this.isDisabled = true;
        // }
        if(this.$route.params.id) {
            this.isEdit = true;
            this.purchase_id = this.$route.params.id;
            let app = this;
            axios.get("/get_product_for_purchase/edit/"+ app.$route.params.id).then(({ data }) => (app.products = data.data))
                .then(function() {
                    // console.log(app);
                    app.getPurchase(app.purchase_id);
                });

        } else {
            //this.getMaxId();
            this.initProducts();
            // console.log(this);
        };
        this.form.invoice_date = moment().format("YYYY-MM-DD");
    },
    mounted() {
        // console.log($('#product_table tr').length);

        $("#loading").hide();
        let app = this;
        app.initWarehouses();
        app.initAccountGroup();
        app.initSuppliers();
        // app.initBrands();
        // app.initCategories();
        app.initUoms();
        app.initCurrency();

        $("#currency_id").select2();
       /*** $("#currency_id").on("select2:select", function(e) {
            var data = e.params.data;
            app.form.currency_id = data.id;
        }); ***/

        $("#currency_id").on("select2:select", function(e) {            
            var data = e.params.data;
            app.form.currency_id = data.id;
            var sign = e.target.options[e.target.options.selectedIndex].dataset.sign;
            $("#product_table .total_row_first").prevAll().remove();
            if(data.id != 1) {
                app.isMMK = false;
                app.total_colspan = 5;
                app.addProduct();
            } else{
                app.isMMK = true;
                app.total_colspan = 4;
                app.addProduct();
            }

            app.sign = sign;
           
            if(app.prev_pay_amount != 0 || app.form.pay_amount != 0) {
                if(app.prev_pay_amount != 0) {
                    var currency_rate = app.form.currency_rate == "" ? 0 : app.form.currency_rate;
                    if(app.currency_type == 1) {
                        if(currency_rate != 0) {
                            var prev_pay_amt_fx = parseInt(app.prev_pay_amount)/parseFloat(currency_rate);
                            app.form.pay_amount = app.prev_pay_amount;
                            app.form.pay_amount_fx = app.decimalFormat(prev_pay_amt_fx);
                        } else {
                            app.form.pay_amount = app.prev_pay_amount;
                            app.form.pay_amount_fx = 0;
                        }
                    } else {
                        if(currency_rate != 0) {
                            var prev_pay_amt_fx = app.prev_pay_amount;
                            app.form.pay_amount = Math.round(parseFloat(app.prev_pay_amount) * parseFloat(currency_rate));
                            app.form.pay_amount_fx = app.decimalFormat(prev_pay_amt_fx);
                        } else {
                            app.form.pay_amount =0;
                            app.form.pay_amount_fx = app.decimalFormat(app.prev_pay_amount);
                        }
                    }
                } else {
                    //app.form.pay_amount = app.prev_pay_amount;
                    //app.form.pay_amount_fx = 0;
                }
            } else {
                app.form.pay_amount = 0;
                app.form.pay_amount_fx = 0;
            }
            var discount = 0;
            var discount_fx = 0;
            if(app.isMMK) {
                if(app.form.discount == '') {
                    discount = 0;
                } else {
                    discount = app.form.discount;
                }
                app.form.balance_amount = parseInt(app.form.sub_total) - (parseInt(pay_amount)+parseInt(discount));
            } else {
                if(app.form.discount_fx == '') {
                    discount = 0;
                    discount_fx = 0;
                    app.form.discount = 0;
                } else {                        
                    discount_fx = app.form.discount_fx;
                    discount = app.decimalFormat(parseFloat(app.form.discount_fx) * parseFloat(currency_rate));
                    app.form.discount = parseInt(discount)
                }
                app.form.balance_amount = parseInt(app.form.sub_total) - (parseInt(app.form.pay_amount)+parseInt(discount));
                app.form.balance_amount_fx = parseFloat(app.form.sub_total_fx) - (parseFloat(app.form.pay_amount_fx)+parseFloat(discount_fx));
            }

            /***app.form.balance_amount  = sub_total - (parseInt(app.form.pay_amount) + parseInt(discount));
            var balance_amount_fx  = sub_total_fx - (parseFloat(app.form.pay_amount_fx) + parseFloat(discount_fx));
            app.form.balance_amount_fx = app.decimalFormat(balance_amount_fx);***/
            /**$(".sign").html(sign);
            for (let i = 0; i < document.getElementsByClassName("sign").length; i++) {
              document.getElementsByClassName("sign")[i].innerHTML = sign;
            }**/
            
        });

        $(".txt_product").select2();
        $("#supplier_id").select2();
        $("#supplier_id").on("select2:select", function(e) {
            var data = e.params.data;
            app.form.supplier_id = data.id;
            //get customer's previous balance
           /** axios.get("/purchase/"+data.id+"/get_previous_balance").then(({ data }) => (app.form.previous_balance = data.previous_balance));**/
           axios.get("/purchase/"+data.id+"/get_previous_balance").then(function(response) {
                    app.form.previous_balance = response.data.previous_balance;
                    app.prev_pay_amount = response.data.supplier_advance;
                    app.currency_type = response.data.currency_type;
                    app.form.pay_amount = response.data.supplier_advance;
                    /***if(app.isMMK) {
                        app.prev_pay_amount = response.data.supplier_advance
                        app.form.pay_amount = response.data.supplier_advance;
                    } else {
                        if(response.data.supplier_advance != 0) {
                            var currency_rate = app.form.currency_rate == "" ? 0 : app.form.currency_rate;
                            if(currency_rate != 0) {
                                var prev_pay_amt_fx = parseInt(app.prev_pay_amount)/parseFloat(currency_rate);
                                app.form.pay_amount = response.data.supplier_advance;
                                app.form.pay_amount_fx = app.decimalFormat(prev_pay_amt_fx);
                            } else {
                                app.form.pay_amount = response.data.supplier_advance;
                                app.form.pay_amount_fx = 0;
                            }
                        } else {
                            app.form.pay_amount = 0;
                            app.form.pay_amount_fx = 0;
                        }
                    }***/

                    var currency_rate = app.form.currency_rate == "" ? 0 : app.form.currency_rate;

                    if(app.prev_pay_amount != 0 || app.form.pay_amount != 0) {
                        if(app.prev_pay_amount != 0) {
                            /**var currency_rate = app.form.currency_rate == "" ? 0 : app.form.currency_rate;
                            if(currency_rate != 0) {
                                var prev_pay_amt_fx = parseInt(app.prev_pay_amount)/parseFloat(currency_rate);
                                app.form.pay_amount = app.prev_pay_amount;
                                app.form.pay_amount_fx = app.decimalFormat(prev_pay_amt_fx);
                            } else {
                                app.form.pay_amount = app.prev_pay_amount;
                                app.form.pay_amount_fx = 0;
                            }**/

                            if(app.currency_type == 1) {
                                if(currency_rate != 0) {
                                    var prev_pay_amt_fx = parseInt(app.prev_pay_amount)/parseFloat(currency_rate);
                                    app.form.pay_amount = app.prev_pay_amount;
                                    app.form.pay_amount_fx = app.decimalFormat(prev_pay_amt_fx);
                                } else {
                                    app.form.pay_amount = app.prev_pay_amount;
                                    app.form.pay_amount_fx = 0;
                                }
                            } else {
                                if(currency_rate != 0) {
                                    var prev_pay_amt_fx = app.prev_pay_amount;
                                    app.form.pay_amount = Math.round(parseFloat(app.prev_pay_amount) * parseFloat(currency_rate));
                                    app.form.pay_amount_fx = app.decimalFormat(prev_pay_amt_fx);
                                } else {
                                    app.form.pay_amount =0;
                                    app.form.pay_amount_fx = app.decimalFormat(app.prev_pay_amount);
                                }
                            }
                        } else {
                            //app.form.pay_amount = app.prev_pay_amount;
                            //app.form.pay_amount_fx = 0;
                        }
                    } else {
                        app.form.pay_amount = 0;
                        app.form.pay_amount_fx = 0;
                    }
                    var discount = 0;
                    var discount_fx =0;
                    if(app.isMMK) {
                        if(app.form.discount == '') {
                            discount = 0;
                        } else {
                            discount = app.form.discount;
                        }
                        app.form.balance_amount = parseInt(app.form.sub_total) - (parseInt(pay_amount)+parseInt(discount));
                    } else {
                        if(app.form.discount_fx == '') {
                            discount = 0;
                            discount_fx = 0;
                            app.form.discount = 0;
                        } else {                        
                            discount_fx = app.form.discount_fx;
                            discount = app.decimalFormat(parseFloat(app.form.discount_fx) * parseFloat(currency_rate));
                            app.form.discount = parseInt(discount)
                        }
                        app.form.balance_amount = parseInt(app.form.sub_total) - (parseInt(app.form.pay_amount)+parseInt(discount));
                        app.form.balance_amount_fx = parseFloat(app.form.sub_total_fx) - (parseFloat(app.form.pay_amount_fx)+parseFloat(discount_fx));
                    }
                    /****app.form.balance_amount  = sub_total - (parseInt(app.form.pay_amount) + parseInt(discount));
                    var balance_amount_fx  = sub_total_fx - (parseFloat(app.form.pay_amount_fx) + parseFloat(discount_fx));
                    app.form.balance_amount_fx = app.decimalFormat(balance_amount_fx);***/
                    
                });
        });
        // console.log(this);
        // $(".unit_price_select").select2();
        $(".unit_price_select").on("keyup", function(e) {
            // var data = e.params.data;
            app.calTotalAmount($(this));
        });
        $(".unit_price_fx_select").on("keyup", function(e) {
            //var data = e.params.data;
            app.calTotalAmountFx($(this));
        });
        $(".txt_qty").on("keyup", function(e) {
            //var data = e.params.data;
            if(app.isMMK) {
                app.calTotalAmount($(this));
            } else {
                app.calTotalAmountFx($(this));
            }
        });
        $(document).on('keyup','#currency_rate',function(e) {
            var sub_total = 0;
            var sub_total_fx = 0;
            var qty = 0;
            var c_rate = $("#currency_rate").val() == '' ? 0 : $("#currency_rate").val();            
            for(var i=0; i<document.getElementsByName('product[]').length; i++) {
                if(document.getElementsByName('total_amount[]')[i].value != "") {
                    document.getElementsByName('unit_price[]')[i].value = parseInt(parseFloat(document.getElementsByName('unit_price_fx[]')[i].value) * parseFloat(c_rate));

                    qty = document.getElementsByName('qty[]')[i].value == '' ? 0 : document.getElementsByName('qty[]')[i].value;

                    document.getElementsByName('total_amount[]')[i].value = parseInt(qty) * parseInt(document.getElementsByName('unit_price[]')[i]);

                    //document.getElementsByName('total_amount[]')[i].value = parseInt(parseFloat(document.getElementsByName('total_amount_fx[]')[i].value) * parseFloat(c_rate));

                    sub_total += parseInt(document.getElementsByName('total_amount[]')[i].value);
                    sub_total_fx += parseFloat(document.getElementsByName('total_amount_fx[]')[i].value);


                }
            }
            app.form.currency_rate = c_rate;
            app.form.sub_total = sub_total;
            app.form.sub_total_fx= sub_total_fx;
            var discount = 0;
            var discount_fx = 0;
            if(app.form.discount_fx == '') {
                discount = 0;
                discount_fx = 0;
            } else {
                discount_fx = app.form.discount_fx;
                discount = parseFloat(app.form.discount_fx) * parseFloat(app.form.currency_rate);
                app.form.discount = parseInt(discount);
            }

            /***if(app.form.payment_type == 'cash') {
                if(app.form.pay_amount_fx == 0) {
                    app.form.pay_amount_fx = parseFloat(app.form.sub_total_fx) - parseFloat(discount_fx);
                    app.form.pay_amount = app.decimalFormat(parseFloat(app.form.pay_amount_fx) * parseFloat(app.form.currency_rate));
                } else {
                    app.form.pay_amount = app.decimalFormat(parseFloat(app.form.pay_amount_fx) * parseFloat(app.form.currency_rate));   
                }
            } else {
                if(app.form.pay_amount_fx == '') {
                    app.form.pay_amount_fx = 0;
                    app.form.pay_amount = 0;
                } else {
                    app.form.pay_amount = app.decimalFormat(parseFloat(app.form.pay_amount_fx) * parseFloat(app.form.currency_rate));
                }
            }***/

            if(app.prev_pay_amount != 0 || app.form.pay_amount != 0) {
                if(app.prev_pay_amount != 0) {
                    var currency_rate = app.form.currency_rate == "" ? 0 : app.form.currency_rate;
                    /**if(currency_rate != 0) {
                        var prev_pay_amt_fx = parseInt(app.prev_pay_amount)/parseFloat(currency_rate);
                        app.form.pay_amount = app.prev_pay_amount;
                        app.form.pay_amount_fx = app.decimalFormat(prev_pay_amt_fx);
                    } else {
                        app.form.pay_amount = app.prev_pay_amount;
                        app.form.pay_amount_fx = 0;
                    }**/
                    if(app.currency_type == 1) {
                        if(currency_rate != 0) {
                            var prev_pay_amt_fx = parseInt(app.prev_pay_amount)/parseFloat(currency_rate);
                            app.form.pay_amount = app.prev_pay_amount;
                            app.form.pay_amount_fx = app.decimalFormat(prev_pay_amt_fx);
                        } else {
                            app.form.pay_amount = app.prev_pay_amount;
                            app.form.pay_amount_fx = 0;
                        }
                    } else {
                        if(currency_rate != 0) {
                            var prev_pay_amt_fx = app.prev_pay_amount;
                            app.form.pay_amount = Math.round(parseFloat(app.prev_pay_amount) * parseFloat(currency_rate));
                            app.form.pay_amount_fx = app.decimalFormat(prev_pay_amt_fx);
                        } else {
                            app.form.pay_amount =0;
                            app.form.pay_amount_fx = app.decimalFormat(app.prev_pay_amount);
                        }
                    }
                } else {
                    //app.form.pay_amount = app.prev_pay_amount;
                    //app.form.pay_amount_fx = 0;
                }
            } else {
                app.form.pay_amount = 0;
                app.form.pay_amount_fx = 0;
            }

            app.form.balance_amount  = sub_total - (parseInt(app.form.pay_amount) + parseInt(discount));
            var balance_amount_fx  = sub_total_fx - (parseFloat(app.form.pay_amount_fx) + parseFloat(discount_fx));
            app.form.balance_amount_fx = app.decimalFormat(balance_amount_fx);
        });
        // $(".txt_qty").select2();
        // $(".txt_qty").on("keyup", function() {
        //     // var data = e.params.data;
        //     app.calTotalAmount($(this));
        // });

        // $(".brands").select2({ width: 'resolve' });
        // $(".brands").on("select2:select", function(e) {
        //     var data = e.params.data;
        //     var brand_id = data.id;
        //     var row_id = $(this).closest('tr').attr('id');
        //     var cat_id = $("#category_"+row_id).find(':selected').val();
        //     var product_select_id = $("#product_"+row_id);
        //     if(brand_id != "") {
        //         app.filterCategories(brand_id);
        //     } else {
        //         app.initCategories();
        //     }
        //     app.filterProducts(brand_id,cat_id,product_select_id);
        // });
        // $(".categories").select2();
        // $(".categories").on("select2:select", function(e) {
        //     var data = e.params.data;
        //     var cat_id = data.id;
        //     var row_id = $(this).closest('tr').attr('id');
        //     var brand_id = $("#brand_"+row_id).find(':selected').val();
        //     var product_select_id =  $("#product_"+row_id);
        //     app.filterProducts(brand_id,cat_id,product_select_id);
        // });

        // branch and category no need

        // this.productSelect();
        // start product select
        $(".txt_product").select2();
        $(".txt_product").on("select2:select", function(e) {
            var data = e.params.data;
            // console.log(data);
            console.log(e.target.options[e.target.options.selectedIndex].dataset);
            var product_key = app.products.findIndex(x => x.product_id == data.id);
            var product_brand = parseInt(app.products[product_key].brand_id);
            var product_category = parseInt(app.products[product_key].category_id);
            var row_id = $(this).closest('tr').attr('id');
            $('#brand_'+row_id).val(product_brand).trigger('change');
            $('#category_'+row_id).val(product_category).trigger('change');
            app.selling_uoms = [];
            var uom      = e.target.options[e.target.options.selectedIndex].dataset.uom;
            // console.log(uom);
            var uom_id   = e.target.options[e.target.options.selectedIndex].dataset.uomid;

            var price    = e.target.options[e.target.options.selectedIndex].dataset.price;

            var retail1_price    = e.target.options[e.target.options.selectedIndex].dataset.retail1;
            var retail2_price    = e.target.options[e.target.options.selectedIndex].dataset.retail2;
            var wholesale_price  = e.target.options[e.target.options.selectedIndex].dataset.wholesale;
            var purchase_price  = e.target.options[e.target.options.selectedIndex].dataset.purchase;
            // console.log('Purchase Price ' ,purchase_price);

            $("#uom_"+row_id).attr('data-uom',uom);

            //auto add new product row
            /* if($(this).closest('tr').next().hasClass("total_row")) {
                 app.addProduct();
             } */
            //add Warehouse UOM Selling Price
          if(app.isMMK) {
           $("#unit_price_"+row_id).val(purchase_price);
          }
            // price_selectbox_id.find('option').remove();
            // price_selectbox_id.append('<option value="">Select One</option>');
            // if(retail1_price != null && retail1_price != "null" && retail1_price != '') {
            //     price_selectbox_id.append('<option value="'+retail1_price+'" selected>'+retail1_price+'</option>');
            // }
            // if(retail2_price != null && retail2_price != "null" && retail2_price != '') {
            //     price_selectbox_id.append('<option value="'+retail2_price+'">'+retail2_price+'</option>');
            // }
            // if(wholesale_price != null && wholesale_price != "null" && wholesale_price != '') {
            //     price_selectbox_id.append('<option value="'+wholesale_price+'">'+purchase_price+'</option>');
            // }
            // if(purchase_price!=null){
            //     price_selectbox_id.append('<option value="'+purchase_price+'">'+wholesale_price+'</option>');
            //
            // }
            var selectbox_id = $("#uom_"+row_id);
            selectbox_id.find('option').remove();
            //add pre-defined product uom

            if(selectbox_id.find('option[value="'+uom_id+'"]').text() == "") {
                selectbox_id.append('<option value="">Select One</option><option value="'+uom_id+'" data-relation="" data-uomqty = "1" data-productuom = "'+uom+'" data-productid="'+data.id+'" data-perprice="'+price+'" data-price="'+price+'" data-retail1="'+retail1_price+'" data-retail2="'+retail2_price+'" data-wholesale="'+wholesale_price+'" data-purchase="'+purchase_price+'" selected>'+uom+'</option>');
            }
            $(".txt_uom").select2();

            var key = app.products.findIndex(x => x.product_id == data.id);
            var available_qty = parseFloat(app.products[key].in_count) - parseFloat(app.products[key].out_count);
            var available_id = $("#stock_available_"+row_id).val(available_qty);

            app.getSellingUomByProduct(selectbox_id, data.id);
            app.calTotalAmount($("#unit_price_"+row_id));
        });
       // end product select

        $(".txt_uom").select2();
        $(".txt_uom").on("select2:select", function(e) {
            // alert('a');
            // app.checkQty(e.target.options[e.target.options.selectedIndex]);
            var uom_relation = e.target.options[e.target.options.selectedIndex].dataset.relation;
            // console.log(e.target.options[e.target.options.selectedIndex].dataset);
            // console.log('UOM RL '+uom_relation);
            // if(app.purchase_type == 2) {
            //     //for van sale
            //     var uom_price = e.target.options[e.target.options.selectedIndex].dataset.price;
            //     var uom_retail1_price = e.target.options[e.target.options.selectedIndex].dataset.retail1;
            //     var uom_retail2_price = e.target.options[e.target.options.selectedIndex].dataset.retail2;
            //     var uom_wholesale_price = e.target.options[e.target.options.selectedIndex].dataset.wholesale;
            //     var uom_purchase_price = e.target.options[e.target.options.selectedIndex].dataset.purchase;
            // } else {
                //for office sale
                var uom_price = e.target.options[e.target.options.selectedIndex].dataset.perprice;
                var uom_retail1_price = e.target.options[e.target.options.selectedIndex].dataset.retail1;
                var uom_retail2_price = e.target.options[e.target.options.selectedIndex].dataset.retail2;
                var uom_wholesale_price = e.target.options[e.target.options.selectedIndex].dataset.wholesale;
                var uom_purchase_price = e.target.options[e.target.options.selectedIndex].dataset.purchase_price;
            // }

            var row_id = $(this).closest('tr').attr('id');
            $('#unit_price_'+row_id).val(uom_purchase_price);
            $("#relation_"+row_id).val(uom_relation);
            //$(this).closest('td').next().next().next().find('input').val(uom_price);
            var unit_price_selectbox_id = $("#unit_price_"+row_id);
            // unit_price_selectbox_id.find('option').remove();
            // unit_price_selectbox_id.append('<option value="">Select One</option>');
            // if(e.params.data.id != '') {
            //     if(uom_retail1_price != 'null' && uom_retail1_price != '' && uom_retail1_price !== "undefined") {
            //         unit_price_selectbox_id.append('<option value="'+uom_retail1_price+'" selected>'+uom_retail1_price+'</option>');
            //     }
            //     if(uom_retail2_price != 'null' && uom_retail2_price != '' && uom_retail2_price !== "undefined") {
            //         unit_price_selectbox_id.append('<option value="'+uom_retail2_price+'">'+uom_retail2_price+'</option>');
            //     }
            //     if(uom_wholesale_price != 'null' && uom_wholesale_price != '' && uom_wholesale_price !== "undefined") {
            //         unit_price_selectbox_id.append('<option value="'+uom_wholesale_price+'">'+uom_wholesale_price+'</option>');
            //     }
            // }


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
                var sub_total_fx = 0;

                for(var i=0; i<document.getElementsByName('product[]').length; i++) {
                    if(document.getElementsByName('total_amount[]')[i].value != "") {
                        if(app.isMMK) {
                            sub_total += parseInt(document.getElementsByName('total_amount[]')[i].value);
                        } else {
                            sub_total += parseInt(document.getElementsByName('total_amount[]')[i].value);
                            sub_total_fx += parseFloat(document.getElementsByName('total_amount_fx[]')[i].value);
                        }
                        
                    }
                }

                app.form.sub_total = sub_total;
                app.form.sub_total_fx = sub_total_fx;
                var discount = 0;
                var discount_fx = 0;
                if(!app.isMMK) {
                    discount_fx = app.form.discount_fx == '' ? 0 : app.form.discount_fx;
                }
                if(app.form.discount == '' || sub_total == 0 || sub_total == '') {
                    discount = 0;
                } else {
                    discount = app.form.discount;
                }

                if(app.isMMK) {
                    /***if(app.form.payment_type == 'cash') {
                        if(app.form.pay_amount == 0) {
                            app.form.pay_amount = parseInt((app.form.sub_total) - parseInt(discount));
                        }
                    } else {
                        if(app.form.pay_amount == '') {
                            app.form.pay_amount = 0;
                        }
                    }***/

                    if(app.prev_pay_amount != 0 || app.form.pay_amount != 0) {
                        if(app.prev_pay_amount != 0) {
                            var currency_rate = app.form.currency_rate == "" ? 0 : app.form.currency_rate;
                            /**if(currency_rate != 0) {
                                var prev_pay_amt_fx = parseInt(app.prev_pay_amount)/parseFloat(currency_rate);
                                app.form.pay_amount = app.prev_pay_amount;
                                app.form.pay_amount_fx = app.decimalFormat(prev_pay_amt_fx);
                            } else {
                                app.form.pay_amount = app.prev_pay_amount;
                                app.form.pay_amount_fx = 0;
                            }**/
                            if(app.currency_type == 1) {
                                if(currency_rate != 0) {
                                    var prev_pay_amt_fx = parseInt(app.prev_pay_amount)/parseFloat(currency_rate);
                                    app.form.pay_amount = app.prev_pay_amount;
                                    app.form.pay_amount_fx = app.decimalFormat(prev_pay_amt_fx);
                                } else {
                                    app.form.pay_amount = app.prev_pay_amount;
                                    app.form.pay_amount_fx = 0;
                                }
                            } else {
                                if(currency_rate != 0) {
                                    var prev_pay_amt_fx = app.prev_pay_amount;
                                    app.form.pay_amount = Math.round(parseFloat(app.prev_pay_amount) * parseFloat(currency_rate));
                                    app.form.pay_amount_fx = app.decimalFormat(prev_pay_amt_fx);
                                } else {
                                    app.form.pay_amount =0;
                                    app.form.pay_amount_fx = app.decimalFormat(app.prev_pay_amount);
                                }
                            }
                        } else {
                            //app.form.pay_amount = app.prev_pay_amount;
                            //app.form.pay_amount_fx = 0;
                        }
                    } else {
                        app.form.pay_amount = 0;
                        app.form.pay_amount_fx = 0;
                    }

                    app.form.balance_amount  = sub_total - (parseInt(app.form.pay_amount) + parseInt(discount));
                } else {
                    /****if(app.form.payment_type == 'cash') {
                        if(app.form.pay_amount == 0) {
                            app.form.pay_amount = parseInt((app.form.sub_total) - parseInt(discount));
                        }
                        if(app.form.pay_amount_fx == 0) {
                            app.form.pay_amount_fx = parseFloat(app.form.sub_total_fx) - parseFloat(discount_fx);
                        }
                    } else {
                        if(app.form.pay_amount == '') {
                            app.form.pay_amount = 0;
                        }
                        if(app.form.pay_amount_fx == '') {
                            app.form.pay_amount_fx = 0;
                        }
                    }*****/

                    if(app.prev_pay_amount != 0 || app.form.pay_amount != 0) {
                        if(app.prev_pay_amount != 0) {
                            var currency_rate = app.form.currency_rate == "" ? 0 : app.form.currency_rate;
                            /***if(currency_rate != 0) {
                                var prev_pay_amt_fx = parseInt(app.prev_pay_amount)/parseFloat(currency_rate);
                                app.form.pay_amount = app.prev_pay_amount;
                                app.form.pay_amount_fx = app.decimalFormat(prev_pay_amt_fx);
                            } else {
                                app.form.pay_amount = app.prev_pay_amount;
                                app.form.pay_amount_fx = 0;
                            }***/
                            if(app.currency_type == 1) {
                                if(currency_rate != 0) {
                                    var prev_pay_amt_fx = parseInt(app.prev_pay_amount)/parseFloat(currency_rate);
                                    app.form.pay_amount = app.prev_pay_amount;
                                    app.form.pay_amount_fx = app.decimalFormat(prev_pay_amt_fx);
                                } else {
                                    app.form.pay_amount = app.prev_pay_amount;
                                    app.form.pay_amount_fx = 0;
                                }
                            } else {
                                if(currency_rate != 0) {
                                    var prev_pay_amt_fx = app.prev_pay_amount;
                                    app.form.pay_amount = Math.round(parseFloat(app.prev_pay_amount) * parseFloat(currency_rate));
                                    app.form.pay_amount_fx = app.decimalFormat(prev_pay_amt_fx);
                                } else {
                                    app.form.pay_amount =0;
                                    app.form.pay_amount_fx = app.decimalFormat(app.prev_pay_amount);
                                }
                            }
                        } else {
                            //app.form.pay_amount = app.prev_pay_amount;
                            //app.form.pay_amount_fx = 0;
                        }
                    } else {
                        app.form.pay_amount = 0;
                        app.form.pay_amount_fx = 0;
                    }

                    app.form.balance_amount_fx  = sub_total_fx - (parseFloat(app.form.pay_amount_fx) + parseFloat(discount_fx));

                    app.form.balance_amount  = sub_total - (parseInt(app.form.pay_amount) + parseInt(discount));
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
                        var sub_total_fx = 0;
                        for(var i=0; i<document.getElementsByName('product[]').length; i++) {
                            if(document.getElementsByName('total_amount[]')[i].value != "") {
                                sub_total += parseInt(document.getElementsByName('total_amount[]')[i].value);
                                sub_total_fx += parseFloat(document.getElementsByName('total_amount_fx[]')[i].value);
                            }
                        }

                        app.form.sub_total = sub_total;
                        app.form.sub_total_fx = sub_total_fx;
                        var discount_fx = 0;
                        if(!app.isMMK) {
                            discount_fx = app.form.discount_fx == '' ? 0 : app.form.discount_fx;
                        }
                        var discount = 0;
                        if(app.form.discount == '' || sub_total == 0 || sub_total == '') {
                            discount = 0;
                        } else {
                            discount = app.form.discount;
                        }
                        if(app.isMMK) {
                           /*** if(app.form.payment_type == 'cash') {
                                if(app.form.pay_amount==0) {
                                    app.form.pay_amount = parseInt((app.form.sub_total) - parseInt(discount));
                                }
                            } else {
                                if(app.form.pay_amount == '') {
                                    app.form.pay_amount = 0;
                                }
                            } **/

                            if(app.prev_pay_amount != 0 || app.form.pay_amount != 0) {
                                if(app.prev_pay_amount != 0) {
                                    var currency_rate = app.form.currency_rate == "" ? 0 : app.form.currency_rate;
                                    /***if(currency_rate != 0) {
                                        var prev_pay_amt_fx = parseInt(app.prev_pay_amount)/parseFloat(currency_rate);
                                        app.form.pay_amount = app.prev_pay_amount;
                                        app.form.pay_amount_fx = app.decimalFormat(prev_pay_amt_fx);
                                    } else {
                                        app.form.pay_amount = app.prev_pay_amount;
                                        app.form.pay_amount_fx = 0;
                                    }***/
                                    if(app.currency_type == 1) {
                                        if(currency_rate != 0) {
                                            var prev_pay_amt_fx = parseInt(app.prev_pay_amount)/parseFloat(currency_rate);
                                            app.form.pay_amount = app.prev_pay_amount;
                                            app.form.pay_amount_fx = app.decimalFormat(prev_pay_amt_fx);
                                        } else {
                                            app.form.pay_amount = app.prev_pay_amount;
                                            app.form.pay_amount_fx = 0;
                                        }
                                    } else {
                                        if(currency_rate != 0) {
                                            var prev_pay_amt_fx = app.prev_pay_amount;
                                            app.form.pay_amount = Math.round(parseFloat(app.prev_pay_amount) * parseFloat(currency_rate));
                                            app.form.pay_amount_fx = app.decimalFormat(prev_pay_amt_fx);
                                        } else {
                                            app.form.pay_amount =0;
                                            app.form.pay_amount_fx = app.decimalFormat(app.prev_pay_amount);
                                        }
                                    }
                                } else {
                                    //app.form.pay_amount = app.prev_pay_amount;
                                    //app.form.pay_amount_fx = 0;
                                }
                            } else {
                                app.form.pay_amount = 0;
                                app.form.pay_amount_fx = 0;
                            }

                            app.form.balance_amount  = sub_total - (parseInt(app.form.pay_amount) + parseInt(discount));
                        } else {

                            /****if(app.form.payment_type == 'cash') {
                                if(app.form.pay_amount == 0) {
                                    app.form.pay_amount = parseInt((app.form.sub_total) - parseInt(discount));
                                }
                                if(app.form.pay_amount_fx == 0) {
                                    app.form.pay_amount_fx = parseFloat(app.form.sub_total_fx) - parseFloat(discount_fx);
                                }
                            } else {
                                if(app.form.pay_amount == '') {
                                    app.form.pay_amount = 0;
                                }
                                if(app.form.pay_amount_fx == '') {
                                    app.form.pay_amount_fx = 0;
                                }
                            }***/

                            if(app.prev_pay_amount != 0 || app.form.pay_amount != 0) {
                                if(app.prev_pay_amount != 0) {
                                    var currency_rate = app.form.currency_rate == "" ? 0 : app.form.currency_rate;
                                    /***if(currency_rate != 0) {
                                        var prev_pay_amt_fx = parseInt(app.prev_pay_amount)/parseFloat(currency_rate);
                                        app.form.pay_amount = app.prev_pay_amount;
                                        app.form.pay_amount_fx = app.decimalFormat(prev_pay_amt_fx);
                                    } else {
                                        app.form.pay_amount = app.prev_pay_amount;
                                        app.form.pay_amount_fx = 0;
                                    }***/
                                    if(app.currency_type == 1) {
                                        if(currency_rate != 0) {
                                            var prev_pay_amt_fx = parseInt(app.prev_pay_amount)/parseFloat(currency_rate);
                                            app.form.pay_amount = app.prev_pay_amount;
                                            app.form.pay_amount_fx = app.decimalFormat(prev_pay_amt_fx);
                                        } else {
                                            app.form.pay_amount = app.prev_pay_amount;
                                            app.form.pay_amount_fx = 0;
                                        }
                                    } else {
                                        if(currency_rate != 0) {
                                            var prev_pay_amt_fx = app.prev_pay_amount;
                                            app.form.pay_amount = Math.round(parseFloat(app.prev_pay_amount) * parseFloat(currency_rate));
                                            app.form.pay_amount_fx = app.decimalFormat(prev_pay_amt_fx);
                                        } else {
                                            app.form.pay_amount =0;
                                            app.form.pay_amount_fx = app.decimalFormat(app.prev_pay_amount);
                                        }
                                    }
                                } else {
                                    //app.form.pay_amount = app.prev_pay_amount;
                                    //app.form.pay_amount_fx = 0;
                                }
                            } else {
                                app.form.pay_amount = 0;
                                app.form.pay_amount_fx = 0;
                            }
                            app.form.balance_amount_fx  = sub_total_fx - (parseFloat(app.form.pay_amount_fx) + parseFloat(discount_fx));

                            app.form.balance_amount  = sub_total - (parseInt(app.form.pay_amount) + parseInt(discount));
                        }

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
        initAccountGroup(){
            axios.get('/sub_account/get_account_group').then(({data})=>(this.account_group=data.account_group));
            // $("#financial_type2_id").select2();
        },

        changeAccountGroup(id) {
            var ag_id = this.form.account_group;
            axios.get('/sub_account/get_account_group/'+ag_id).then(({data})=>(this.cash_bank_accounts=data.sub_accounts));
        },
        initBrands() {
            axios.get("/brands").then(({ data }) => (this.brands = data.data));
            $(".brands").select2({ width: 'resolve' });
        },
        initSuppliers() {
            axios.get("/supplier").then(({ data }) => (this.suppliers = data.data));
            $("#supplier_id").select2();
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
            $(".txt_uom").select2();

        },

        initCurrency() {
            axios.get("/all_currency").then(({ data }) => (this.currency = data.data));
            $("#currency_id").select2();

        },

        changePayment() {
            let app = this;
            if(this.form.discount == '') {
                var discount = 0;
            } else {
                var discount = this.form.discount;
            }
            var discount_fx = this.form.discount_fx == '' ? 0 : this.form.discount_fx;

            if(this.prev_pay_amount != 0 || this.form.pay_amount != 0) {
                if(this.prev_pay_amount != 0) {
                    var currency_rate = this.form.currency_rate == "" ? 0 : this.form.currency_rate;
                   /*** if(currency_rate != 0) {
                        var prev_pay_amt_fx = parseInt(this.prev_pay_amount)/parseFloat(currency_rate);
                        this.form.pay_amount = this.prev_pay_amount;
                        this.form.pay_amount_fx = this.decimalFormat(prev_pay_amt_fx);
                    } else {
                        this.form.pay_amount = this.prev_pay_amount;
                        this.form.pay_amount_fx = 0;
                    }***/
                    if(app.currency_type == 1) {
                        if(currency_rate != 0) {
                            var prev_pay_amt_fx = parseInt(app.prev_pay_amount)/parseFloat(currency_rate);
                            app.form.pay_amount = app.prev_pay_amount;
                            app.form.pay_amount_fx = app.decimalFormat(prev_pay_amt_fx);
                        } else {
                            app.form.pay_amount = app.prev_pay_amount;
                            app.form.pay_amount_fx = 0;
                        }
                    } else {
                        if(currency_rate != 0) {
                            var prev_pay_amt_fx = app.prev_pay_amount;
                            app.form.pay_amount = Math.round(parseFloat(app.prev_pay_amount) * parseFloat(currency_rate));
                            app.form.pay_amount_fx = app.decimalFormat(prev_pay_amt_fx);
                        } else {
                            app.form.pay_amount =0;
                            app.form.pay_amount_fx = app.decimalFormat(app.prev_pay_amount);
                        }
                    }
                } else {
                    //this.form.pay_amount = this.prev_pay_amount;
                    //this.form.pay_amount_fx = 0;
                }
            } else {
                this.form.pay_amount = 0;
                this.form.pay_amount_fx = 0;
            }

            if(this.isMMK) 
            {
                if(this.form.payment_type == 'credit') {
                    this.required_val = true;
                    //this.form.pay_amount = 0;
                    this.form.balance_amount = parseInt(this.form.sub_total) - (parseInt(this.form.pay_amount) + parseInt(discount));
                } else {
                    this.required_val = false;
                    if(this.form.payment_type == 'cash') {
                        /**if(this.form.pay_amount == 0) {
                            this.form.pay_amount = parseInt(this.form.sub_total) - parseInt(discount);
                        }**/
                        this.form.balance_amount = parseInt(this.form.sub_total) - (parseInt(this.form.pay_amount) + parseInt(discount));
                    }
                }
            } else {
                var currency_rate = this.form.currency_rate == '' ? 0 : this.form.currency_rate;
                 if(this.form.payment_type == 'credit') {
                    this.required_val = true;
                    $("#pay_amount").attr('readonly', false);
                    //this.form.pay_amount = 0;
                    this.form.balance_amount_fx = parseFloat(this.form.sub_total_fx) - (parseFloat(this.form.pay_amount_fx) + parseFloat(discount_fx));
                    this.form.balance_amount_fx = this.decimalFormat(this.form.balance_amount_fx);
                    this.form.balance_amount = parseInt(parseFloat(this.form.balance_amount_fx) * parseFloat(currency_rate));

                } else {
                    this.required_val = false;
                    if(this.form.payment_type == 'cash') {
                        /***if(this.form.pay_amount_fx == 0) {
                            this.form.pay_amount_fx = parseFloat(this.form.sub_total_fx) - parseFloat(discount_fx);
                            this.form.pay_amount_fx = this.decimalFormat(this.form.pay_amount_fx);
                            this.form.pay_amount = parseInt(parseFloat(this.form.pay_amount_fx) * parseFloat(current_rate));
                        }***/
                        this.form.balance_amount_fx = parseFloat(this.form.sub_total_fx) - (parseFloat(this.form.pay_amount_fx) + parseFloat(discount_fx));
                    }
                }
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
                axios.get("/get_product_for_purchase/edit/"+ app.$route.params.id).then(({ data }) => (app.products = data.data));
            } else {
                axios.get("/get_product_for_purchase/create/null").then(({ data }) => (
                    app.products = data.data));
            }

            $(".txt_product").select2();
        },

        filterProducts(brand_id,cat_id,product_select_id){
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

                            options += "<option value='"+prod.product_id+"' data-uom='"+prod.uom_name+"' data-price='"+prod.product_price+"' data-retail1='"+prod.retail1_price+"'  data-retail2='"+prod.retail2_price+"' data-wholesale='"+prod.wholesale_price+"' data-uomid='"+prod.uom_id+"' data-purchase='"+prod.purchase_price+"' data-pivotid = '0'>"+prod.product_name+"</option>";
                        });

                        product_select_id.html(options);
                    });

                } else {
                    axios.get("/filterProductsByUserWarehouse/create/null?" + data).then(function(response) {
                        var products = response.data.data;
                        var options = "<option value=''>Select One</option>";

                        $.each(products, function( key, prod ) {
                            options += "<option value='"+prod.product_id+"' data-uom='"+prod.uom_name+"' data-price='"+prod.product_price+"' data-retail1='"+prod.retail1_price+"'  data-retail2='"+prod.retail2_price+"' data-wholesale='"+prod.wholesale_price+"' data-uomid='"+prod.uom_id+"'  data-purchase='"+prod.purchase_price+"' data-pivotid = '0'>"+prod.product_name+"</option>";
                        });
                        product_select_id.html(options);
                    });
                }
            }

        },

        initWarehouses() {
            axios.get("/warehouses").then(({ data }) => (this.warehouses = data.data));
            $("#to_warehouse").select2();
        },



        getSellingUomByProduct(selectbox_id,product_id) {
            let app = this;
            axios.get("/product/selling_uom/"+ product_id).then(function(response) {
                var uom_arr = response.data.s_uoms.selling_uoms;
                var uom_relation = "";
                $.each(uom_arr, function(index, value) {
                    uom_relation = "1 "+value.uom_name+" = "+ value.pivot.relation +" "+ selectbox_id.attr('data-uom');
                    if(selectbox_id.find('option[value="'+value.pivot.uom_id+'"]').text() == "") {
                        selectbox_id.append('<option value="'+value.pivot.uom_id+'" data-relation="'+uom_relation+'" data-uomqty="'+value.pivot.relation+'" data-productuom = "'+selectbox_id.attr('data-uom')+'" data-productid="'+product_id+'" data-perprice="'+value.pivot.per_warehouse_uom_price+'" data-price="'+value.pivot.product_price+'"  data-retail1="'+value.pivot.retail1_price+'" data-retail2="'+value.pivot.retail2_price+'" data-wholesale="'+value.pivot.wholesale_price+'" data-purchase_price="'+value.pivot.warehouse_uom_purchase_price+'" >'+value.uom_name+'</option>');
                    }
                });
                //
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
                // console.log($(this).attr('id'));
                max = parseInt($(this).attr('id')) > max ? parseInt($(this).attr('id')) : max;
            });
            //var max = $('#product_table tbody tr').sort(function(a, b) { return +a.id < +b.id })[0].id;
            var row_id = parseInt(max) +1;
            // console.log("Row Id =" +row_id);
            let app = this;
            var table=document.getElementById("product_table");
            var row=table.insertRow((table.rows.length)-5);
            // var cell1=row.insertCell(0);
            row.id = row_id;
            // // brand select
            // var t1=document.createElement("select");
            // t1.name = "brand[]";
            // t1.id = "brand_"+row_id;
            // t1.className = "form-control brands";
            // t1.style = "min-width:100px;";
            //
            // var option = document.createElement("option");
            // option.value = '';
            // option.text = "Select One";
            // t1.append(option);
            // var html = $('#txt_brand').html();
            // $(t1).html(html);
            // cell1.appendChild(t1);
            // end branch select

            // start branch select
            // var cell2=row.insertCell(1);
            // var t2=document.createElement("select");
            // t2.name = "category[]";
            // t2.id = "category_"+row_id;
            // t2.className = "form-control categories";
            // t2.style = "min-width:150px;";
            // var option = document.createElement("option");
            // option.value = '';
            // option.text = "Select One";
            // t2.append(option);
            //
            // var html = $('#txt_category').html();
            // $(t2).html(html);
            // cell2.appendChild(t2);
            // end category select

            var cell3=row.insertCell(0);
            var t3=document.createElement("select");
            t3.name = "product[]";
            t3.id = "product_"+row_id;
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

            var cell4=row.insertCell(1);
            var t4=document.createElement("input");
            t4.name = "qty[]";
            t4.id = "qty_"+row_id;
            t4.style = "width:150px;";
            t4.className ="form-control decimal_no txt_qty";
            $(t4).attr("required", true);
            // t4.addEventListener('blur', function(){ app.checkQty(t4); });
            cell4.appendChild(t4);

            var cell5=row.insertCell(2);

            var t5=document.createElement("select");
            t5.name = "uom[]";
            t5.id = "uom_"+row_id;
            t5.className = "form_control txt_uom";
            t5.style = "min-width:100px;";
            $(t5).attr("required", true);
            // t5.addEventListener('blur', function(){ app.checkQty(t5); });

            var option = document.createElement("option");
            option.value = '';
            option.text = "Select One";
            $(option).attr('data-relation',"");
            $(option).attr('data-price', "");
            $(option).attr('data-perprice', "");
            t5.append(option);

            cell5.appendChild(t5);


            // var cell6=row.insertCell(3);
            // var t6=document.createElement("input");
            // t6.type = "text";
            // t6.name = "relation[]";
            // t6.id = "relation_"+row_id;
            // t6.style = "min-width:120px;";
            // t6.className ="form-control txt_relation";
            // $(t6).attr('readonly', true);
            // cell6.appendChild(t6);
            $(".txt_product").select2();
            $(".txt_uom").select2();
            // $(".unit_price_select").select2();
            $("#supplier_id").select2();
            $("#supplier_id").on("select2:select", function(e) {

                var data = e.params.data;
                app.form.supplier_id = data.id;

                //get customer's previous balance
                axios.get("/customer_previous_balance/"+data.id).then(({ data }) => (app.form.previous_balance = data.previous_balance));
            });
            $(".txt_product").on("select2:select", function(e) {
                var data = e.params.data;
                var product_key = app.products.findIndex(x => x.product_id == data.id);
                var product_brand = parseInt(app.products[product_key].brand_id);
                var product_category = parseInt(app.products[product_key].category_id);
                var row_id = $(this).closest('tr').attr('id');
                $('#brand_'+row_id).val(product_brand).trigger('change');
                $('#category_'+row_id).val(product_category).trigger('change');
                app.selling_uoms = [];
                var uom = e.target.options[e.target.options.selectedIndex].dataset.uom;
                var uom_id = e.target.options[e.target.options.selectedIndex].dataset.uomid;
                var price = e.target.options[e.target.options.selectedIndex].dataset.price;

                var retail1_price    = e.target.options[e.target.options.selectedIndex].dataset.retail1;
                var retail2_price    = e.target.options[e.target.options.selectedIndex].dataset.retail2;
                var wholesale_price  = e.target.options[e.target.options.selectedIndex].dataset.wholesale;
                var purchase_price  = e.target.options[e.target.options.selectedIndex].dataset.purchase ;
                // console.log('Purchase price '+ purchase_price);
                var row_id = $(this).closest('tr').attr('id');
                $("#uom_"+row_id).attr('data-uom',uom);
                if(app.isMMK) {
                    $("#unit_price_"+row_id).val(purchase_price);
                }
                //auto add new product row
                /*if($(this).closest('tr').next().hasClass("total_row")) {
                    app.addProduct();
                }*/

                //add Warehouse UOM Selling Price
                //$(this).closest('td').next().next().next().next().next().find('input').val(price);
                // price_selectbox_id.find('option').remove();
                // price_selectbox_id.append('<option value="">Select One</option>');
                // if(retail1_price != null && retail1_price != "null" && retail1_price != '') {
                //     price_selectbox_id.append('<option value="'+retail1_price+'" selected>'+retail1_price+'</option>');
                // }
                // if(retail2_price != null && retail2_price != "null" && retail2_price != '') {
                //     price_selectbox_id.append('<option value="'+retail2_price+'">'+retail2_price+'</option>');
                // }
                // if(wholesale_price != null && wholesale_price != "null" && wholesale_price != '') {
                //     price_selectbox_id.append('<option value="'+wholesale_price+'">'+wholesale_price+'</option>');
                // }
                var selectbox_id = $("#uom_"+row_id);
                selectbox_id.find('option').remove();

                //add pre-defined product uom
                if(selectbox_id.find('option[value="'+uom_id+'"]').text() == "") {
                    // console.log('aaaa');
                    selectbox_id.append('<option value="">Select One</option><option value="'+uom_id+'" data-relation="" data-uomqty = "1" data-productuom = "'+uom+'" data-productid="'+data.id+'" data-perprice="'+price+'" data-price="'+price+'" data-retail1="'+retail1_price+'" data-retail2="'+retail2_price+'" data-wholesale="'+wholesale_price+'" data-purchase="'+purchase_price+'" selected>'+uom+'</option>');
                }
                $(".txt_uom").select2();
                var key = app.products.findIndex(x => x.product_id == data.id);
                var available_qty = parseFloat(app.products[key].in_count) - parseFloat(app.products[key].out_count);
                var available_id = $("#stock_available_"+row_id).val(available_qty);
                // console.log( selectbox_id)
                app.getSellingUomByProduct(selectbox_id, data.id);
                app.calTotalAmount($("#unit_price_"+row_id));
            });

            $(".txt_uom").select2();
            $(".txt_uom").on("select2:select", function(e) {
                // app.checkQty(e.target.options[e.target.options.selectedIndex]);
                var uom_relation = e.target.options[e.target.options.selectedIndex].dataset.relation;
                // if(app.purchase_type == 2) {
                //     //for van sale
                //     var uom_price = e.target.options[e.target.options.selectedIndex].dataset.price;
                //     var uom_retail1_price = e.target.options[e.target.options.selectedIndex].dataset.retail1;
                //     var uom_retail2_price = e.target.options[e.target.options.selectedIndex].dataset.retail2;
                //     var uom_wholesale_price = e.target.options[e.target.options.selectedIndex].dataset.wholesale;
                // } else {
                    //for office sale
                    var uom_price = e.target.options[e.target.options.selectedIndex].dataset.perprice;
                    var uom_retail1_price = e.target.options[e.target.options.selectedIndex].dataset.retail1;
                    var uom_retail2_price = e.target.options[e.target.options.selectedIndex].dataset.retail2;
                    var uom_wholesale_price = e.target.options[e.target.options.selectedIndex].dataset.wholesale;
                    var uom_purchase_price = e.target.options[e.target.options.selectedIndex].dataset.purchase_price;
                // }

                var row_id = $(this).closest('tr').attr('id');
                $("#relation_"+row_id).val(uom_relation);
                $("#unit_price_"+row_id).val(uom_purchase_price);
                //$(this).closest('td').next().next().next().find('input').val(uom_price);
                var unit_price_selectbox_id = $("#unit_price_"+row_id);
                // unit_price_selectbox_id.find('option').remove();
                // unit_price_selectbox_id.append('<option value="">Select One</option>');
                // if(e.params.data.id != '') {
                //     if(uom_retail1_price != 'null' && uom_retail1_price != '' && uom_retail1_price !== "undefined") {
                //         unit_price_selectbox_id.append('<option value="'+uom_retail1_price+'" selected>'+uom_retail1_price+'</option>');
                //     }
                //     if(uom_retail2_price != 'null' && uom_retail2_price != '' && uom_retail2_price !== "undefined") {
                //         unit_price_selectbox_id.append('<option value="'+uom_retail2_price+'">'+uom_retail2_price+'</option>');
                //     }
                //     if(uom_wholesale_price != 'null' && uom_wholesale_price != '' && uom_wholesale_price !== "undefined") {
                //         unit_price_selectbox_id.append('<option value="'+uom_wholesale_price+'">'+uom_wholesale_price+'</option>');
                //     }
                // }


                //app.calTotalAmount($(this).closest('td').next().next().next().find('input'));
                app.calTotalAmount(unit_price_selectbox_id);
            });

            /***cell7.className = "text-center";
             var t7=document.createElement("input");
             t7.type = "checkbox";
             t7.name = "chk_foc[]";
             t7.addEventListener('change', function(){ app.checkFoc(t7); });
             cell7.appendChild(t7);***/

            // start stock
            // var cell7=row.insertCell(6);
            //
            // var t7=document.createElement("input");
            // t7.name = "stock_available[]";
            // t7.id = "stock_available_"+row_id;
            // t7.className ="form-control txt_available";
            // $(t7).attr("readonly", true);
            //
            // cell7.appendChild(t7);
            //
            /*var t6=document.createElement("input");
                t6.name = "unit_price[]";
                t6.style = "width:100px;";
                t6.className ="form-control decimal_no";
                $(t6).attr("required", true);
                t6.addEventListener('blur', function(){ app.calTotalAmount(t6); });
                cell6.appendChild(t6); */

            if(app.isMMK) {
                var cell8=row.insertCell(3);
                var t8=document.createElement("input");
                t8.name = "unit_price[]";
                t8.id = "unit_price_"+row_id;
                t8.style = "width:91px;";
                t8.className ="form-control decimal_no unit_price_select";
                $(t8).attr("required", true);
                cell8.appendChild(t8);

                var cell9=row.insertCell(4);
                var t9=document.createElement("input");
                t9.name = "total_amount[]";
                t9.id = "total_amount_"+row_id;
                t9.style = "width:100px;";
                t9.className ="form-control num_txt";
                $(t9).attr("required", true);
                $(t9).attr("readonly", true);
                // t2.addEventListener('blur', function(){ app.checkQty(t2); });
                cell9.appendChild(t9);
                var cell10=row.insertCell(5);
                cell10.className = "text-center";
                var row_action = "<a class='remove-row red-icon' title='Remove'><i class='fas fa-times-circle' style='font-size: 25px;'></i></a>    ";
                $(cell10).append(row_action);
            } else {

                var cell8fx=row.insertCell(3);
                var t8fx=document.createElement("input");
                t8fx.name = "unit_price_fx[]";
                t8fx.id = "unit_price_fx_"+row_id;
                t8fx.style = "width:91px;";
                t8fx.className ="form-control decimal_no unit_price_fx_select";
                $(t8fx).attr("required", true);
                cell8fx.appendChild(t8fx);

                var cell8=row.insertCell(4);
                var t8=document.createElement("input");
                t8.name = "unit_price[]";
                t8.id = "unit_price_"+row_id;
                t8.style = "width:91px;";
                t8.className ="form-control decimal_no unit_price_select";
                $(t8).attr("required", true);
                $(t8).attr("readonly", true);
                cell8.appendChild(t8);

                var cell9fx=row.insertCell(5);
                var t9fx=document.createElement("input");
                t9fx.name = "total_amount_fx[]";
                t9fx.id = "total_amount_fx_"+row_id;
                t9fx.style = "width:100px;";
                t9fx.className ="form-control decimal_no";
                //$(t9fx).attr("required", true);
                $(t9fx).attr("readonly", true);
                // t2.addEventListener('blur', function(){ app.checkQty(t2); });
                cell9fx.appendChild(t9fx);

                var cell9=row.insertCell(6);
                var t9=document.createElement("input");
                t9.name = "total_amount[]";
                t9.id = "total_amount_"+row_id;
                t9.style = "width:100px;";
                t9.className ="form-control num_txt";
                $(t9).attr("required", true);
                $(t9).attr("readonly", true);
                // t2.addEventListener('blur', function(){ app.checkQty(t2); });
                cell9.appendChild(t9);

                var cell10=row.insertCell(7);
                cell10.className = "text-center";
                var row_action = "<a class='remove-row red-icon' title='Remove'><i class='fas fa-times-circle' style='font-size: 25px;'></i></a>    ";
                $(cell10).append(row_action);
            }

            $(".txt_qty").on("keyup", function(e) {
                if(app.isMMK) {
                    app.calTotalAmount($('#qty_'+row_id));
                } else {
                    app.calTotalAmountFx($('#qty_'+row_id));
                }
            });
            $(".unit_price_select").on("keyup", function(e) {
                app.calTotalAmount($('#unit_price_'+row_id));
            });
            $(".unit_price_fx_select").on("keyup", function(e) {
                // var data = e.params.data;
                app.calTotalAmountFx($(this));
            });
            // app.calTotalAmount($("#unit_price_"+row_id));



            // $(".brands").select2();
            // $(".brands").on("select2:select", function(e) {
            //     var data = e.params.data;
            //     var brand_id = data.id;
            //     var row_id = $(this).closest('tr').attr('id');
            //
            //     var cat_id = $("#category_"+row_id).find(':selected').val();
            //     var product_select_id = $("#product_"+row_id);
            //     if(brand_id != "") {
            //         app.filterCategories(brand_id);
            //     } else {
            //         app.initCategories();
            //     }
            //     app.filterProducts(brand_id,cat_id,product_select_id);
            // });
            //
            // $(".categories").select2();
            // $(".categories").on("select2:select", function(e) {
            //
            //     var data = e.params.data;
            //     var cat_id = data.id;
            //     var row_id = $(this).closest('tr').attr('id');
            //     var brand_id = $("#brand_"+row_id).find(':selected').val();
            //     var product_select_id =  $("#product_"+row_id);
            //     app.filterProducts(brand_id,cat_id,product_select_id);
            // });
        },
        getPurchase(id) {
            let app = this;
            axios.get("/purchase/" + id+'/edit')
                .then(function(response) {
                    //prevent to Edit (save button permission)
                    if(app.user_role == "admin" || app.user_role == "system" || app.user_role == "office_user") {
                        app.isDisabled = false;
                        console.log(app.isDisabled);
                        // if(response.data.purchase.collections.length == 0 && response.data.purchase.deliveries.length == 0 && response.data.purchase.delivery_approve == 0) {
                        //     app.isDisabled = false;
                        // } else {
                        //     app.isDisabled = true;
                        // }
                    } else {
                        app.isDisabled = true;
                    }

                    app.form.account_group = response.data.purchase.account_group_id;                
                    if(response.data.purchase.account_group_id != '' && response.data.purchase.account_group_id != null) {
                        axios.get('/sub_account/get_account_group/'+response.data.purchase.account_group_id).then(({data})=>(app.cash_bank_accounts=data.sub_accounts));
                    }

                    app.form.cash_bank_account = response.data.purchase.sub_account_id;

                    app.form.currency_id = response.data.purchase.currency_id;
                    $("#currency_id").val(app.form.currency_id).trigger('change');
                    app.sign = response.data.purchase.currency.sign;
                    app.form.currency_rate = response.data.purchase.currency_rate;
                    app.isMMK = app.form.currency_id == 1 ? true : false;
                    app.form.invoice_date = moment(response.data.purchase.invoice_date).format('YYYY-MM-DD');
                    app.ex_products = response.data.purchase.products;
                    app.form.invoice_no = response.data.purchase.invoice_no;
                    app.form.reference_no = response.data.purchase.reference_no;
                    app.form.payment_type = response.data.purchase.payment_type;
                    app.form.previous_balance = response.data.previous_balance;
                    app.form.due_date = response.data.purchase.due_date;
                    app.form.credit_day = response.data.purchase.credit_day;
                    if(response.data.purchase.office_purchase_man != null) {
                        // console.log('A is ' +  response.data.purchase.office_purchase_man.name)
                        app.form.office_purchase_man = response.data.purchase.office_purchase_man.name;
                    } else {
                        // console.log('b');
                        app.form.office_purchase_man = '';
                    }
                    app.user_warehouse = response.data.purchase.warehouse.warehouse_name;
                    
                    // console.log(app);

                    if(app.form.payment_type == 'credit') {
                        app.required_val = true;
                    } else {
                        app.required_val = false;
                    }
                    /**$("#supplier_id").append('<option value="'+response.data.purchase.supplier_id+'" selected>'+response.data.purchase.supplier.name+'</option>');**/
                    app.form.supplier_id = response.data.purchase.supplier_id;
                    $('#supplier_id').val(app.form.supplier_id).trigger('change');
                   
                    app.form.sub_total  = response.data.purchase.total_amount;
                    app.form.pay_amount = response.data.purchase.pay_amount;
                    app.form.discount = response.data.purchase.discount;
                    app.form.balance_amount = response.data.purchase.balance_amount;

                    if(!app.isMMK) {
                        app.total_colspan = 5;
                        app.form.sub_total_fx  = response.data.purchase.total_amount_fx;
                        app.form.pay_amount_fx = response.data.purchase.pay_amount_fx;
                        app.form.discount_fx = response.data.purchase.discount_fx;
                        app.form.balance_amount_fx = response.data.purchase.balance_amount_fx;
                    }

                     if(response.data.purchase.collection_amount!=0  && app.form.payment_type=='credit'){
                        app.isDisabled=true;
                    }
                    $.each(app.ex_products, function( key, value ) {
                        app.form.ex_product_pivot.push(value.pivot.id);
                    });
                    //add products dynamically
                    var subTotal = 0;
                    var balAmount = 0;
                    var subTotalFx = 0;
                    var balAmountFx = 0;
                    var row_id = 0;
                    $.each(app.ex_products, function( key, product ) {
                        row_id = row_id+1;
                        if(app.user_role != "Country Head" || (app.user_role == "Country Head")) {

                            var table=document.getElementById("product_table");
                            var row=table.insertRow((table.rows.length) - 5);
                            row.id = row_id;

                            // var cell1=row.insertCell(0);
                            // var t1=document.createElement("select");
                            // t1.name = "brand[]";
                            // t1.id = "brand_"+row_id;
                            // t1.className = "form-control brands";
                            // t1.style = "min-width:150px;";
                            // $(t1).attr('readonly', true);
                            // var option = document.createElement("option");
                            // option.value = product.brand_id;
                            // option.className ="form-control";
                            // option.text = product.brand.brand_name;
                            // t1.append(option);
                            // cell1.appendChild(t1);
                            //
                            // var cell2=row.insertCell(1);
                            // var t2=document.createElement("select");
                            // t2.name = "category[]";
                            // t2.id = "category_"+row_id;
                            // t2.className = "form-control categories";
                            // t2.style = "min-width:150px;";
                            // $(t2).attr('readonly', true);
                            // var option = document.createElement("option");
                            // option.value = product.category_id;
                            // option.className ="form-control";
                            // option.text = product.category.category_name;
                            // t2.append(option);
                            //
                            // cell2.appendChild(t2);

                            var cell3=row.insertCell(0);
                            var t3=document.createElement("select");
                            t3.name = "product[]";
                            t3.id = "product_"+row_id;
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
                            var cell4=row.insertCell(1);
                            var t4=document.createElement("input");
                            t4.name = "qty[]";
                            t4.id = "qty_"+row_id;
                            t4.value = parseInt(product.pivot.product_quantity);
                            t4.style = "width:150px;";
                            t4.className ="form-control decimal_no txt_qty";
                            $(t4).attr("required", true);
                            // t4.addEventListener('blur', function(){ app.checkQty(t4); });
                            cell4.appendChild(t4);
                            var cell5=row.insertCell(2);
                            var t5=document.createElement("select");
                            t5.name = "uom[]";
                            t5.id = "uom_"+row_id;
                            t5.className = "form-control txt_uom";
                            t5.style = "width:100px;";
                            $(t5).attr("required", true);

                            // t5.addEventListener('blur', function(){ app.checkQty(t5); });

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
                            $(option).attr("data-purchase",product.purchase_price);
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
                                $(option).attr("data-purchase",suom.pivot.warehouse_uom_purchase_price);
                                $(option).attr("data-perprice",suom.pivot.per_warehouse_uom_price);
                                $(option).attr("data-productuom",product.uom.uom_name);
                                $(option).attr("data-productid", product.id);
                                t5.append(option);
                            });

                            cell5.appendChild(t5);


                            // var cell6=row.insertCell(3);
                            // var t6=document.createElement("input");
                            // t6.type = "text";
                            // if(product.pivot.uom_id != product.uom_id) {
                            //     t6.value = relation_val;
                            // } else {
                            //     t6.value = "";
                            // }
                            // t6.name = "relation[]";
                            // t6.id = "relation_"+row_id;
                            // t6.style = "min-width:120px;";
                            // t6.className ="form-control txt_relation";
                            // $(t6).attr('readonly', true);
                            // cell6.appendChild(t6);

                            $(".txt_product").select2();

                            $(".txt_uom").select2();

                            $("#supplier_id").select2();
                            $("#supplier_id").on("select2:select", function(e) {

                                var data = e.params.data;
                                app.form.supplier_id = data.id;
                                //get customer's previous balances
                                axios.get("/purchase/"+data.id+"/get_previous_balance/").then(({ data }) => (app.form.previous_balance = data.previous_balance));
                            });
                            // console.log(app.form.previous_balance);
                           
                            $(".txt_qty").on("keyup", function(e) {
                                if(app.isMMK) {
                                    app.calTotalAmount($(this));
                                } else {
                                    app.calTotalAmountFx($(this));
                                }
                            });
                            $(".txt_product").on("select2:select", function(e) {
                                var data = e.params.data;
                                var product_key = app.products.findIndex(x => x.product_id == data.id);
                                // var product_brand = parseInt(app.products[product_key].brand_id);
                                // var product_category = parseInt(app.products[product_key].category_id);

                                var row_id = $(this).closest('tr').attr('id');
                                $('#brand_'+row_id).val(product_brand).trigger('change');
                                $('#category_'+row_id).val(product_category).trigger('change');

                                app.selling_uoms = [];
                                // console.log(e.target.options[e.target.options.selectedIndex].dataset);
                                var uom = e.target.options[e.target.options.selectedIndex].dataset.uom;
                                var uom_id = e.target.options[e.target.options.selectedIndex].dataset.uomid;
                                var price = e.target.options[e.target.options.selectedIndex].dataset.price;
                                var retail1_price    = e.target.options[e.target.options.selectedIndex].dataset.retail1;
                                var retail2_price    = e.target.options[e.target.options.selectedIndex].dataset.retail2;
                                var wholesale_price  = e.target.options[e.target.options.selectedIndex].dataset.wholesale;
                                var purchase_price  = e.target.options[e.target.options.selectedIndex].dataset.purchase;
                                // var wholesale_price  = e.target.options[e.target.options.selectedIndex].dataset.;


                                var row_id = $(this).closest('tr').attr('id');

                                $("#uom_"+row_id).attr('data-uom',uom);

                                //auto add new product row
                                /*if($(this).closest('tr').next().hasClass("total_row")) {
                                    app.addProduct();
                                }*/

                                //add Warehouse UOM Selling Price
                                //$(this).closest('td').next().next().next().next().next().find('input').val(price);

                                var price_selectbox_id = $("#unit_price"+row_id);

                                // price_selectbox_id.find('option').remove();
                                // price_selectbox_id.append('<option value="">Select One</option>');
                                // if(retail1_price != null && retail1_price != "null" && retail1_price != '') {
                                //     price_selectbox_id.append('<option value="'+retail1_price+'" selected>'+retail1_price+'</option>');
                                // }
                                // if(retail2_price != null && retail2_price != "null" && retail2_price != '') {
                                //     price_selectbox_id.append('<option value="'+retail2_price+'">'+retail2_price+'</option>');
                                // }
                                // if(wholesale_price != null && wholesale_price != "null" && wholesale_price != '') {
                                //     price_selectbox_id.append('<option value="'+wholesale_price+'">'+wholesale_price+'</option>');
                                // }

                                var selectbox_id = $("#uom_"+row_id);

                                selectbox_id.find('option').remove();

                                //add pre-defined product uom
                                if(selectbox_id.find('option[value="'+uom_id+'"]').text() == "") {
                                    selectbox_id.append('<option value="">Select One</option><option value="'+uom_id+'" data-relation="" data-uomqty = "1" data-productuom = "'+uom+'" data-productid="'+data.id+'" data-perprice="'+price+'" data-price="'+price+'" data-retail1="'+retail1_price+'" data-retail2="'+retail2_price+'" data-wholesale="'+wholesale_price+'" selected>'+uom+'</option>');
                                }
                                $(".txt_uom").select2();

                                var key = app.products.findIndex(x => x.product_id == data.id);
                                var available_qty = parseFloat(app.products[key].in_count) - parseFloat(app.products[key].out_count);
                                var available_id = $("#stock_available_"+row_id).val(available_qty);

                                app.getSellingUomByProduct(selectbox_id, data.id);
                                app.calTotalAmount($("#unit_price_"+row_id));
                            });
                            $(".txt_uom").select2();

                            $(".txt_uom").on("select2:select", function(e) {
                                // app.checkQty(e.target.options[e.target.options.selectedIndex]);
                                // console.log(e.target.options[e.target.options.selectedIndex].dataset);
                                var uom_relation = e.target.options[e.target.options.selectedIndex].dataset.relation;
                                if(app.purchase_type == 2) {
                                    //for van sale
                                    var uom_price = e.target.options[e.target.options.selectedIndex].dataset.price;
                                    var uom_retail1_price = e.target.options[e.target.options.selectedIndex].dataset.retail1;
                                    var uom_retail2_price = e.target.options[e.target.options.selectedIndex].dataset.retail2;
                                    var uom_wholesale_price = e.target.options[e.target.options.selectedIndex].dataset.wholesale;
                                    var uom_purchase_price = e.target.options[e.target.options.selectedIndex].dataset.purchase;
                                } else {
                                    //for office sale
                                    var uom_price = e.target.options[e.target.options.selectedIndex].dataset.perprice;
                                    var uom_retail1_price = e.target.options[e.target.options.selectedIndex].dataset.retail1;
                                    var uom_retail2_price = e.target.options[e.target.options.selectedIndex].dataset.retail2;
                                    var uom_wholesale_price = e.target.options[e.target.options.selectedIndex].dataset.wholesale;
                                    var uom_purchase_price = e.target.options[e.target.options.selectedIndex].dataset.purchase;

                                }

                                var row_id = $(this).closest('tr').attr('id');
                                $("#relation_"+row_id).val(uom_relation);
                                //$(this).closest('td').next().next().next().find('input').val(uom_price);
                                var unit_price_selectbox_id = $("#unit_price_"+row_id);
                               $("#unit_price_"+row_id).val(uom_purchase_price);
                                // unit_price_selectbox_id.find('option').remove();
                                // unit_price_selectbox_id.append('<option value="">Select One</option>');
                                // if(e.params.data.id != '') {
                                //     if(uom_retail1_price != 'null' && uom_retail1_price != '' && uom_retail1_price !== "undefined") {
                                //         unit_price_selectbox_id.append('<option value="'+uom_retail1_price+'" selected>'+uom_retail1_price+'</option>');
                                //     }
                                //     if(uom_retail2_price != 'null' && uom_retail2_price != '' && uom_retail2_price !== "undefined") {
                                //         unit_price_selectbox_id.append('<option value="'+uom_retail2_price+'">'+uom_retail2_price+'</option>');
                                //     }
                                //     if(uom_wholesale_price != 'null' && uom_wholesale_price != '' && uom_wholesale_price !== "undefined") {
                                //         unit_price_selectbox_id.append('<option value="'+uom_wholesale_price+'">'+uom_wholesale_price+'</option>');
                                //     }
                                // }
                                //app.calTotalAmount($(this).closest('td').next().next().next().find('input'));
                                app.calTotalAmount(unit_price_selectbox_id);
                            });
                            /***var t7=document.createElement("input");
                             t7.type = "checkbox";
                             t7.name = "chk_foc[]";
                             if(product.pivot.is_foc == '1') {
                                    $(t7). prop("checked", true);
                                }
                             t7.addEventListener('change', function(){ app.checkFoc(t7); });
                             cell7.appendChild(t7); ***/
                            // var cell7=row.insertCell(4);
                            // cell7.className = "text-center";
                            // var key = app.products.findIndex(x => x.product_id == product.id);
                            // console.log('list');
                            // console.log(app.products);
                            // var available_qty = parseFloat(app.products[key].in_count) - parseFloat(app.products[key].out_count);
                            //
                            // var t7=document.createElement("input");
                            // t7.name = "stock_available[]";
                            // t7.id = "stock_available_"+row_id;
                            // t7.value = available_qty;
                            // t7.className ="form-control txt_available";
                            // $(t7).attr("readonly", true);
                            //
                            // cell7.appendChild(t7);
                            /***var t6=document.createElement("input");
                             t6.name = "unit_price[]";
                             t6.style = "width:90px;";
                             if(product.pivot.price != 0 && product.pivot.price != null) {
                                    t6.value = product.pivot.price;
                                }
                             t6.className ="form-control float_num";
                             $(t6).attr("required", true);
                             if(product.pivot.is_foc == '1') {
                                    $(t6).attr("readonly", true);
                                }
                             t6.addEventListener('blur', function(){ app.calTotalAmount(t6); });
                             cell6.appendChild(t6); ***/
                            // var cell8=row.insertCell(4);
                            // var t8=document.createElement("input");
                            // t8.name = "unit_price[]";
                            // t8.id = "unit_price_"+row_id;
                            // t8.className = "form_control unit_price_select";
                            // t8.style = "min-width:91px;";
                            // $(t8).attr("required", true);
                            // cell8.appendChild(t8);
                            if(app.isMMK) {
                                var cell8=row.insertCell(3);
                                var t8=document.createElement("input");
                                t8.name = "unit_price[]";
                                t8.id = "unit_price_"+row_id;
                                t8.style = "width:91px;";
                                t8.value=product.pivot.price;
                                t8.className ="form-control decimal_no unit_price_select";
                                t8.addEventListener('blur', function(){ app.calTotalAmount(t8); });
                                $(t8).attr("required", true);
                                cell8.appendChild(t8);

                                var cell9=row.insertCell(4);
                                var t9=document.createElement("input");
                                t9.name = "total_amount[]";
                                t9.id = "total_amount_"+row_id;
                                t9.style = "width:100px;";
                                if(product.pivot.total_amount != 0 && product.pivot.total_amount != null) {
                                    t9.value = product.pivot.total_amount;
                                    subTotal += parseInt(product.pivot.total_amount);
                                }
                                t9.className ="form-control decimal_no";
                                $(t9).attr("required", true);
                                $(t9).attr("readonly", true);
                                // t2.addEventListener('blur', function(){ app.checkQty(t2); });
                                cell9.appendChild(t9);

                                var cell10=row.insertCell(5);
                                cell10.className = "text-center";
                                if((app.user_role == 'admin' || app.user_role == 'system' || app.user_role == 'office_user') && !app.isDisabled)
                                {
                                    var row_action = "<a class='remove-exrow red-icon' title='Remove'><i class='fas fa-times-circle' style='font-size: 25px;'></i></a>";
                                } else {
                                    var row_action = "<a class='remove-exrow red-icon' title='Remove' style='display:none;'><i class='fas fa-times-circle' style='font-size: 25px;'></i></a>";
                                }
                                $(cell10).append(row_action);
                            } else {

                                var cell8fx=row.insertCell(3);
                                var t8fx=document.createElement("input");
                                t8fx.name = "unit_price_fx[]";
                                t8fx.id = "unit_price_fx_"+row_id;
                                t8fx.style = "width:91px;";
                                t8fx.value=product.pivot.price_fx;
                                t8fx.className ="form-control decimal_no unit_price_fx_select";
                                t8fx.addEventListener('blur', function(){ app.calTotalAmount(t8fx); });
                                $(t8fx).attr("required", true);
                                cell8fx.appendChild(t8fx);

                                var cell8=row.insertCell(4);
                                var t8=document.createElement("input");
                                t8.name = "unit_price[]";
                                t8.id = "unit_price_"+row_id;
                                t8.style = "width:91px;";
                                t8.value=product.pivot.price;
                                t8.className ="form-control decimal_no unit_price_select";
                                t8.addEventListener('blur', function(){ app.calTotalAmount(t8); });
                                $(t8).attr("required", true);
                                cell8.appendChild(t8);

                                var cell9fx=row.insertCell(5);
                                var t9fx=document.createElement("input");
                                t9fx.name = "total_amount_fx[]";
                                t9fx.id = "total_amount_fx_"+row_id;
                                t9fx.style = "width:100px;";
                                if(product.pivot.total_amount_fx != 0 && product.pivot.total_amount_fx != null) {
                                    t9fx.value = product.pivot.total_amount_fx;
                                    subTotalFx += parseFloat(product.pivot.total_amount_fx);
                                }
                                t9fx.className ="form-control decimal_no";
                                $(t9fx).attr("required", true);
                                $(t9fx).attr("readonly", true);
                                // t2.addEventListener('blur', function(){ app.checkQty(t2); });
                                cell9fx.appendChild(t9fx);

                                var cell9=row.insertCell(6);
                                var t9=document.createElement("input");
                                t9.name = "total_amount[]";
                                t9.id = "total_amount_"+row_id;
                                t9.style = "width:100px;";
                                if(product.pivot.total_amount != 0 && product.pivot.total_amount != null) {
                                    t9.value = product.pivot.total_amount;
                                    subTotal += parseInt(product.pivot.total_amount);
                                }
                                t9.className ="form-control decimal_no";
                                $(t9).attr("required", true);
                                $(t9).attr("readonly", true);
                                // t2.addEventListener('blur', function(){ app.checkQty(t2); });
                                cell9.appendChild(t9);

                                var cell10=row.insertCell(7);
                                cell10.className = "text-center";
                                if((app.user_role == 'admin' || app.user_role == 'system' || app.user_role == 'office_user') && !app.isDisabled)
                                {
                                    var row_action = "<a class='remove-exrow red-icon' title='Remove'><i class='fas fa-times-circle' style='font-size: 25px;'></i></a>";
                                } else {
                                    var row_action = "<a class='remove-exrow red-icon' title='Remove' style='display:none;'><i class='fas fa-times-circle' style='font-size: 25px;'></i></a>";
                                }
                                $(cell10).append(row_action);
                            }
                        }

                        $(".unit_price_select").on("keyup", function(e) {
                            app.calTotalAmount($(this));
                        });
                        $(".unit_price_fx_select").on("keyup", function(e) {
                            // var data = e.params.data;
                            app.calTotalAmountFx($(this));
                        });
                        /** var disAmount = parseInt(response.data.sale.discount);
                         balAmount = parseInt(subTotal) - (parseInt(response.data.sale.pay_amount) + disAmount);
                         app.form.balance_amount = balAmount;
                         app.form.sub_total = subTotal;**/

                        // $(".brands").select2();
                        // $(".brands").on("select2:select", function(e) {
                        //     var data = e.params.data;
                        //     var brand_id = data.id;
                        //     var row_id = $(this).closest('tr').attr('id');
                        //
                        //     var cat_id = $("#category_"+row_id).find(':selected').val();
                        //     var product_select_id = $("#product_"+row_id);
                        //     if(brand_id != "") {
                        //         app.filterCategories(brand_id);
                        //     } else {
                        //         app.initCategories();
                        //     }
                        //     app.filterProducts(brand_id,cat_id,product_select_id);
                        // });
                        //
                        // $(".categories").select2();
                        // $(".categories").on("select2:select", function(e) {
                        //
                        //     var data = e.params.data;
                        //     var cat_id = data.id;
                        //     var row_id = $(this).closest('tr').attr('id');
                        //     var brand_id = $("#brand_"+row_id).find(':selected').val();
                        //     var product_select_id =  $("#product_"+row_id);
                        //     app.filterProducts(brand_id,cat_id,product_select_id);
                        // });
                    });


                })
                .catch(function(error) {
                    // handle error
                    console.log(error);
                })
                .then(function() {
                    // always executed
                    app.original_form = $("#purchase_form").serialize();
                });

            $(".txt_uom").select2();
        },
        checkQty(obj) {
            // console.log(obj);
            let app = this;
            if(typeof obj.name !== 'undefined') {
                alert('qty');
                //For quantity box onBlur Event
                var row_id = $(obj).closest('tr').attr('id');

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
                            p_qty =  $("#qty_"+row_no).val();
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
                    console.log(product_qty);
                    console.log("Available"+available_qty);
                    if(parseFloat(product_qty) > parseFloat(available_qty)) {
                        swal("Warning!", "Not enough quantity! Availabel quantity is "+available_qty+" "+uom_name+".", "warning");
                        //$(obj).focus(); //obj.value='';
                    }
                }

                //claculate total amount
                var unit_price = $("#unit_price_"+row_id).val();
                var relation_val = $("#uom_"+row_id).find(':selected').attr('data-uomqty');
                if(obj.value != "" && unit_price != "") {

                    /***if(app.sale_type == 2) {
                            var total_amount = parseInt(obj.value) * parseFloat(unit_price);
                        } else {
                            var total_amount = parseInt(obj.value) * parseInt(relation_val) * parseFloat(unit_price);
                        }***/

                    var total_amount = parseFloat(obj.value) * parseInt(unit_price);
                    $("#total_amount_"+row_id).val(Math.round(total_amount));
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
                if(app.prev_pay_amount != 0 || app.form.pay_amount != 0) {
                    if(app.prev_pay_amount != 0) {
                        var currency_rate = app.form.currency_rate == "" ? 0 : app.form.currency_rate;
                        /**if(currency_rate != 0) {
                            var prev_pay_amt_fx = parseInt(app.prev_pay_amount)/parseFloat(currency_rate);
                            app.form.pay_amount = app.prev_pay_amount;
                            app.form.pay_amount_fx = app.decimalFormat(prev_pay_amt_fx);
                        } else {
                            app.form.pay_amount = app.prev_pay_amount;
                            app.form.pay_amount_fx = 0;
                        }**/
                        if(app.currency_type == 1) {
                            if(currency_rate != 0) {
                                var prev_pay_amt_fx = parseInt(app.prev_pay_amount)/parseFloat(currency_rate);
                                app.form.pay_amount = app.prev_pay_amount;
                                app.form.pay_amount_fx = app.decimalFormat(prev_pay_amt_fx);
                            } else {
                                app.form.pay_amount = app.prev_pay_amount;
                                app.form.pay_amount_fx = 0;
                            }
                        } else {
                            if(currency_rate != 0) {
                                var prev_pay_amt_fx = app.prev_pay_amount;
                                app.form.pay_amount = Math.round(parseFloat(app.prev_pay_amount) * parseFloat(currency_rate));
                                app.form.pay_amount_fx = app.decimalFormat(prev_pay_amt_fx);
                            } else {
                                app.form.pay_amount =0;
                                app.form.pay_amount_fx = app.decimalFormat(app.prev_pay_amount);
                            }
                        }
                    } else {
                        //app.form.pay_amount = app.prev_pay_amount;
                        //app.form.pay_amount_fx = 0;
                    }
                } else {
                    app.form.pay_amount = 0;
                    app.form.pay_amount_fx = 0;
                }
                //alert(app.prev_pay_amount);
                /***if(app.form.payment_type == 'cash') {
                    if(app.form.pay_amount == 0) {
                        app.form.pay_amount = parseInt((app.form.sub_total) - parseInt(discount));
                    }
                } else {
                    if(app.form.pay_amount == '') {
                        app.form.pay_amount = 0;
                    }
                }***/
                app.form.balance_amount  = sub_total - (parseInt(app.form.pay_amount) + parseInt(discount));
            } else {

                //For UOM box select Event
                alert('uom');
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
                            p_qty =  $("#qty_"+row_no).val();
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

                if(app.prev_pay_amount != 0 || app.form.pay_amount != 0) {
                    if(app.prev_pay_amount != 0) {
                        var currency_rate = app.form.currency_rate == "" ? 0 : app.form.currency_rate;
                        /***if(currency_rate != 0) {
                            var prev_pay_amt_fx = parseInt(app.prev_pay_amount)/parseFloat(currency_rate);
                            app.form.pay_amount = app.prev_pay_amount;
                            app.form.pay_amount_fx = app.decimalFormat(prev_pay_amt_fx);
                        } else {
                            app.form.pay_amount = app.prev_pay_amount;
                            app.form.pay_amount_fx = 0;
                        }**/
                        if(app.currency_type == 1) {
                            if(currency_rate != 0) {
                                var prev_pay_amt_fx = parseInt(app.prev_pay_amount)/parseFloat(currency_rate);
                                app.form.pay_amount = app.prev_pay_amount;
                                app.form.pay_amount_fx = app.decimalFormat(prev_pay_amt_fx);
                            } else {
                                app.form.pay_amount = app.prev_pay_amount;
                                app.form.pay_amount_fx = 0;
                            }
                        } else {
                            if(currency_rate != 0) {
                                var prev_pay_amt_fx = app.prev_pay_amount;
                                app.form.pay_amount = Math.round(parseFloat(app.prev_pay_amount) * parseFloat(currency_rate));
                                app.form.pay_amount_fx = app.decimalFormat(prev_pay_amt_fx);
                            } else {
                                app.form.pay_amount =0;
                                app.form.pay_amount_fx = app.decimalFormat(app.prev_pay_amount);
                            }
                        }
                    } else {
                        //app.form.pay_amount = app.prev_pay_amount;
                        //app.form.pay_amount_fx = 0;
                    }
                } else {
                    app.form.pay_amount = 0;
                    app.form.pay_amount_fx = 0;
                }

                /***if(app.prev_pay_amount != 0) {
                    var currency_rate = app.form.currency_rate == "" ? 0 : app.form.currency_rate;
                    if(currency_rate != 0) {
                        var prev_pay_amt_fx = parseInt(app.prev_pay_amount)/parseFloat(currency_rate);
                        app.form.pay_amount = app.prev_pay_amount;
                        app.form.pay_amount_fx = app.decimalFormat(prev_pay_amt_fx);
                    } else {
                        app.form.pay_amount = app.prev_pay_amount;
                        app.form.pay_amount_fx = 0;
                    }
                } else {
                    app.form.pay_amount = 0;
                    app.form.pay_amount_fx = 0;
                }***/
                /***if(app.form.payment_type == 'cash') {
                    if(app.form.pay_amount == 0) {
                        app.form.pay_amount = parseInt((app.form.sub_total) - parseInt(discount));
                    }
                } else {
                    if(app.form.pay_amount == '') {
                        app.form.pay_amount = 0;
                    }
                }***/
                app.form.balance_amount  = sub_total - (parseInt(app.form.pay_amount) + parseInt(discount));
            }
        },
        calTotalAmount(obj) {
            let app = this;
            console.log(this);
            var row_id = $(obj).closest('tr').attr('id');
            var qty = $("#qty_"+row_id).val();
            var price = $("#unit_price_"+row_id).val();
            var total=parseInt(qty)* parseInt(price);
            if(qty !=null && price !=null && qty != '' && price!= ''){
                var total=parseInt(qty)* parseInt(price);
                $('#total_amount_'+row_id).val(total);
            }else{
                $('#total_amount_'+row_id).val('');
            }

            // if(app.purchase_type == 2) {
            //     var product_price = $("#uom_"+row_id).find(':selected').attr('data-price');
            // } else {
            //     var product_price = $("#uom_"+row_id).find(':selected').attr('data-perprice');
            // }

            /***if(product_price != '') {
                    var price_variant =  parseFloat(price) - parseFloat(product_price);
                    price_variant = price_variant.toFixed(3);
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
            // if(price != "" && qty != "") {
            //     /****if(app.sale_type == 2) {
            //             var total = parseInt(qty) * parseFloat(price);
            //         } else {
            //             var relation_val = $("#uom_"+row_id).find(':selected').attr('data-uomqty');
            //             var total = parseInt(qty) * parseInt(relation_val) * parseFloat(price);
            //         }****/
            //
            //     var total = parseFloat(qty) * parseInt(price);
            //     $("#total_amount_"+row_id).val(Math.round(total));
            // } else {
            //     $("#total_amount_"+row_id).val('');
            // }

            //get all sub total amount
            // console.log(document.getElementsByName('product[]').length);
            var sub_total = 0;
            console.log('Row count '+row_id);
            for(var i=0; i<document.getElementsByName('product[]').length; i++) {
                if(document.getElementsByName('total_amount[]')[i].value != "") {
                    sub_total += parseInt(document.getElementsByName('total_amount[]')[i].value);
                }
            }
            // for(var i=1; i<=row_id; i++) {
            //     if($('#total_amount_'+i).val()!=''){
            //         sub_total+=parseInt($('#total_amount_'+i).val());
            //     }
            //     else{
            //         sub_total +=0;
            //     }
            // }


            app.form.sub_total = sub_total;
            var discount = 0;
            if(app.form.discount == '') {
                discount = 0;
            } else {
                discount = app.form.discount;
            }
            /***if(app.form.payment_type == 'cash') {
                if(app.form.pay_amount == 0) {
                    app.form.pay_amount = parseInt((app.form.sub_total) - parseInt(discount));
                }
            } else {
                if(app.form.pay_amount == '') {
                    app.form.pay_amount = 0;
                }
            }***/

            if(app.prev_pay_amount != 0 || app.form.pay_amount != 0) {
                if(app.prev_pay_amount != 0) {
                    var currency_rate = app.form.currency_rate == "" ? 0 : app.form.currency_rate;
                    /**if(currency_rate != 0) {
                        var prev_pay_amt_fx = parseInt(app.prev_pay_amount)/parseFloat(currency_rate);
                        app.form.pay_amount = app.prev_pay_amount;
                        app.form.pay_amount_fx = app.decimalFormat(prev_pay_amt_fx);
                    } else {
                        app.form.pay_amount = app.prev_pay_amount;
                        app.form.pay_amount_fx = 0;
                    }**/
                    if(app.currency_type == 1) {
                        if(currency_rate != 0) {
                            var prev_pay_amt_fx = parseInt(app.prev_pay_amount)/parseFloat(currency_rate);
                            app.form.pay_amount = app.prev_pay_amount;
                            app.form.pay_amount_fx = app.decimalFormat(prev_pay_amt_fx);
                        } else {
                            app.form.pay_amount = app.prev_pay_amount;
                            app.form.pay_amount_fx = 0;
                        }
                    } else {
                        if(currency_rate != 0) {
                            var prev_pay_amt_fx = app.prev_pay_amount;
                            app.form.pay_amount = Math.round(parseFloat(app.prev_pay_amount) * parseFloat(currency_rate));
                            app.form.pay_amount_fx = app.decimalFormat(prev_pay_amt_fx);
                        } else {
                            app.form.pay_amount =0;
                            app.form.pay_amount_fx = app.decimalFormat(app.prev_pay_amount);
                        }
                    }
                } else {
                    //app.form.pay_amount = app.prev_pay_amount;
                    //app.form.pay_amount_fx = 0;
                }
            } else {
                app.form.pay_amount = 0;
                app.form.pay_amount_fx = 0;
            }
            app.form.balance_amount  = sub_total - (parseInt(app.form.pay_amount) + parseInt(discount));
        },

        calTotalAmountFx(obj) {
            let app = this;
            console.log(this);

            var row_id = $(obj).closest('tr').attr('id');
            $("#unit_price_"+row_id).attr('readonly',true);
            var currency_rate = app.form.currency_rate == '' ? 0 : app.form.currency_rate;
            if(currency_rate == "") {
                currency_rate = 0;
                swal("Warning!", "Currency rate is empty, please check!", "warning");
                $("#currency_rate").focus();
                $("#unit_price_fx_"+row_id).val('');
                return false;
            } else {
                currency_rate = parseFloat(app.form.currency_rate);
            }            
            var qty = $("#qty_"+row_id).val();            
            var price_fx = $("#unit_price_fx_"+row_id).val() == '' ? 0 : $("#unit_price_fx_"+row_id).val();
            var price = parseFloat(price_fx) * parseFloat(currency_rate);
            $('#unit_price_'+row_id).val(Math.round(price));
            var total=Math.round(parseInt(qty)* parseFloat(price));
            var total_fx =parseInt(qty)* parseFloat(price_fx);
            if(qty !=null && price !=null && price_fx != '') {
                var total=Math.round(parseInt(qty)* parseFloat(price));
                $('#total_amount_'+row_id).val(total);

                var total_fx=parseInt(qty)* parseFloat(price_fx);
                $('#total_amount_fx_'+row_id).val(app.decimalFormat(total_fx));
            }else{
                $('#total_amount_'+row_id).val('');
                $('#total_amount_fx_'+row_id).val('');
            }

            //get all sub total amount
            var sub_total = 0;
            var sub_total_fx = 0;
            console.log('Row count '+row_id);
            for(var i=0; i<document.getElementsByName('product[]').length; i++) {
                if(document.getElementsByName('total_amount[]')[i].value != "") {
                    sub_total += parseInt(document.getElementsByName('total_amount[]')[i].value);
                    sub_total_fx += parseFloat(document.getElementsByName('total_amount_fx[]')[i].value);
                }
            }

            app.form.sub_total = sub_total;
            app.form.sub_total_fx= sub_total_fx;
            var discount = 0;
            var discount_fx = 0;
            if(app.form.discount_fx == '') {
                discount = 0;
                discount_fx = 0;
            } else {
                discount_fx = app.form.discount_fx;
                discount = parseFloat(app.form.discount_fx) * parseFloat(app.form.currency_rate);
            }
           /*** if(app.form.payment_type == 'cash') {
                if(app.form.pay_amount_fx == 0) {
                    app.form.pay_amount_fx = parseFloat(app.form.sub_total_fx) - parseFloat(discount_fx);
                    app.form.pay_amount = app.decimalFormat(parseFloat(app.form.pay_amount_fx) * parseFloat(app.form.currency_rate));
                }
            } else {
                if(app.form.pay_amount_fx == '') {
                    app.form.pay_amount_fx = 0;
                    app.form.pay_amount = 0;
                }
            }***/

            if(app.prev_pay_amount != 0 || app.form.pay_amount != 0) {
                if(app.prev_pay_amount != 0) {
                    var currency_rate = app.form.currency_rate == "" ? 0 : app.form.currency_rate;
                    /**if(currency_rate != 0) {
                        var prev_pay_amt_fx = parseInt(app.prev_pay_amount)/parseFloat(currency_rate);
                        app.form.pay_amount = app.prev_pay_amount;
                        app.form.pay_amount_fx = app.decimalFormat(prev_pay_amt_fx);
                    } else {
                        app.form.pay_amount = app.prev_pay_amount;
                        app.form.pay_amount_fx = 0;
                    }**/
                    if(app.currency_type == 1) {
                        if(currency_rate != 0) {
                            var prev_pay_amt_fx = parseInt(app.prev_pay_amount)/parseFloat(currency_rate);
                            app.form.pay_amount = app.prev_pay_amount;
                            app.form.pay_amount_fx = app.decimalFormat(prev_pay_amt_fx);
                        } else {
                            app.form.pay_amount = app.prev_pay_amount;
                            app.form.pay_amount_fx = 0;
                        }
                    } else {
                        if(currency_rate != 0) {
                            var prev_pay_amt_fx = app.prev_pay_amount;
                            app.form.pay_amount = Math.round(parseFloat(app.prev_pay_amount) * parseFloat(currency_rate));
                            app.form.pay_amount_fx = app.decimalFormat(prev_pay_amt_fx);
                        } else {
                            app.form.pay_amount =0;
                            app.form.pay_amount_fx = app.decimalFormat(app.prev_pay_amount);
                        }
                    }
                } else {
                    //app.form.pay_amount = app.prev_pay_amount;
                    //app.form.pay_amount_fx = 0;
                }
            } else {
                app.form.pay_amount = 0;
                app.form.pay_amount_fx = 0;
            }

            app.form.balance_amount  = sub_total - (parseInt(app.form.pay_amount) + parseInt(discount));
            var balance_amount_fx  = sub_total_fx - (parseFloat(app.form.pay_amount_fx) + parseFloat(discount_fx));
            app.form.balance_amount_fx = app.decimalFormat(balance_amount_fx);
        },

        calBalance(obj) {
            let app = this;
            var pay_amount = 0;
            var discount = 0;
            var pay_amount_fx = 0;
            var discount_fx = 0;
            if(!app.isMMK) {
                var currency_rate = app.form.currency_rate == '' ? 0 : app.form.currency_rate;
            } 
            if(obj.value != "") {
                if(app.isMMK) {
                    pay_amount = obj.value;
                } else {                    
                    pay_amount_fx = app.form.pay_amount_fx;
                    pay_amount = app.decimalFormat(parseFloat(pay_amount_fx) * parseFloat(currency_rate));

                }
            }
            if(parseInt(pay_amount) > (parseInt(app.form.sub_total) - parseInt(app.form.discount))) {
                swal("Warning!", "Pay amount is greater than sub total amount", "warning");
            } else {
                app.form.pay_amount = parseInt(pay_amount);
                if(app.isMMK) {
                    if(app.form.discount == '') {
                        discount = 0;
                    } else {
                        discount = app.form.discount;
                    }
                    app.form.balance_amount = parseInt(app.form.sub_total) - (parseInt(pay_amount)+parseInt(discount));
                } else {
                    if(app.form.discount_fx == '') {
                        discount = 0;
                        discount_fx = 0;
                        app.form.discount = 0;
                    } else {                        
                        discount_fx = app.form.discount_fx;
                        discount = app.decimalFormat(parseFloat(app.form.discount_fx) * parseFloat(currency_rate));
                        app.form.discount = parseInt(discount)
                    }
                    app.form.balance_amount = parseInt(app.form.sub_total) - (parseInt(pay_amount)+parseInt(discount));
                    app.form.balance_amount_fx = parseFloat(app.form.sub_total_fx) - (parseFloat(pay_amount_fx)+parseFloat(discount_fx));
                }
            }
        },
        changeDiscount(obj) {
            let app = this;
            var discount = 0;
            var balance  = 0;

            if(obj.value != "") {
                discount = obj.value;
            }
            /**if(app.form.payment_type == 'cash') {
                if(app.form.pay_amount == 0) {
                    app.form.pay_amount = parseInt(app.form.sub_total) - parseInt(discount);
                }
            }**/

            /***if(app.prev_pay_amount != 0) {
                var currency_rate = app.form.currency_rate == "" ? 0 : app.form.currency_rate;
                if(currency_rate != 0) {
                    var prev_pay_amt_fx = parseInt(app.prev_pay_amount)/parseFloat(currency_rate);
                    app.form.pay_amount = app.prev_pay_amount;
                    app.form.pay_amount_fx = app.decimalFormat(prev_pay_amt_fx);
                } else {
                    app.form.pay_amount = app.prev_pay_amount;
                    app.form.pay_amount_fx = 0;
                }
            } else {
                app.form.pay_amount = 0;
                app.form.pay_amount_fx = 0;
            }***/

            if(discount > app.form.sub_total) {
                swal("Warning!", "Discount amount is greater than sub total amount", "warning");
            } else {
                app.form.balance_amount = parseInt(app.form.sub_total) - (parseInt(app.form.pay_amount)+parseInt(discount));
            }
        },

        changeDiscountFx(obj) {
            let app = this;
            var discount = 0;
            var balance  = 0;
            var discount_fx = 0;
            var balance_fx = 0;
            if(app.isMMK) {
                if(obj.value != "") {
                    discount = obj.value;
                }
                /***if(app.form.payment_type == 'cash') {
                    if(app.form.pay_amount == 0) {
                        app.form.pay_amount = parseInt(app.form.sub_total) - parseInt(discount);
                    }
                }***/

               /*** if(app.prev_pay_amount != 0) {
                    var currency_rate = app.form.currency_rate == "" ? 0 : app.form.currency_rate;
                    if(currency_rate != 0) {
                        var prev_pay_amt_fx = parseInt(app.prev_pay_amount)/parseFloat(currency_rate);
                        app.form.pay_amount = app.prev_pay_amount;
                        app.form.pay_amount_fx = app.decimalFormat(prev_pay_amt_fx);
                    } else {
                        app.form.pay_amount = app.prev_pay_amount;
                        app.form.pay_amount_fx = 0;
                    }
                } else {
                    app.form.pay_amount = 0;
                    app.form.pay_amount_fx = 0;
                }***/

                if(discount > app.form.sub_total) {
                    swal("Warning!", "Discount amount is greater than sub total amount", "warning");
                } else {
                    app.form.balance_amount = parseInt(app.form.sub_total) - (parseInt(app.form.pay_amount)+parseInt(discount));
                }
            } else {
                var currency_rate = app.form.currency_rate == '' ? 0 : app.form.currency_rate;
                if(obj.value != "") {
                    discount_fx = obj.value;
                    discount = parseFloat(discount_fx) * parseFloat(currency_rate);
                    app.form.discount = parseInt(discount);
                }
                /***if(app.form.payment_type == 'cash') {
                    if(app.form.pay_amount == 0) {
                        app.form.pay_amount = parseInt(app.form.sub_total) - parseInt(discount);
                    }

                    if(app.form.pay_amount_fx == 0) {
                        app.form.pay_amount_fx = parseFloat(app.form.sub_total_fx) - parseFloat(discount_fx);
                    }
                }***/

                /****if(app.prev_pay_amount != 0) {
                    var currency_rate = app.form.currency_rate == "" ? 0 : app.form.currency_rate;
                    if(currency_rate != 0) {
                        var prev_pay_amt_fx = parseInt(app.prev_pay_amount)/parseFloat(currency_rate);
                        app.form.pay_amount = app.prev_pay_amount;
                        app.form.pay_amount_fx = app.decimalFormat(prev_pay_amt_fx);
                    } else {
                        app.form.pay_amount = app.prev_pay_amount;
                        app.form.pay_amount_fx = 0;
                    }
                } else {
                    app.form.pay_amount = 0;
                    app.form.pay_amount_fx = 0;
                }****/

                if(discount > app.form.sub_total) {
                    swal("Warning!", "Discount amount is greater than sub total amount", "warning");
                } else {
                    app.form.balance_amount = parseInt(app.form.sub_total) - (parseInt(app.form.pay_amount)+parseInt(discount));
                    app.form.balance_amount_fx = parseFloat(app.form.sub_total_fx) - (parseFloat(app.form.pay_amount_fx)+parseFloat(discount_fx));
                }
            }
        },
        onSubmit: function(event){
            let app = this;
           
            if(app.form.payment_type =='cash' || (app.form.payment_type =='credit' && app.form.pay_amount != 0 && app.form.pay_amount != null)) {
                if(app.form.account_group == "" || app.form.account_group == null ) {
                     swal("Warning!", "Payment Method is required", "warning");
                     $("#account_group").focus();
                     return false;
                } else if(app.form.cash_bank_account == "" || app.form.cash_bank_account == null) {
                     swal("Warning!", "Payment Method is required", "warning");
                     $("#cash_bank_account").focus();
                     return false;
                } else {}
            }

            //EP added
            if(app.form.payment_type == 'cash') {
                if(app.form.balance_amount > 0 ) {
                    swal("Warning!", "Balance must be zero for cash payment!", "warning");
                    return false;
                }
            }

            if(app.form.discount > app.form.sub_total || app.form.balance_amount < 0 || app.form.pay_amount<0) {
                swal("Warning!", "Invalid balance or Invalid discount or Invalid pay amount. Please check!", "warning");
                $('#loading').hide();
                return false;
            }
            //End EP

             $('#loading').show();

                app.form.reference_no = $("#reference_no").val();
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
                    app.form.qty.push(document.getElementsByName('qty[]')[i].value);
                    app.form.unit_price.push(document.getElementsByName('unit_price[]')[i].value);
                    // app.form.unit_price.push($('#unit_price_'+i).val());

                    app.form.total_amount.push(document.getElementsByName('total_amount[]')[i].value);

                    if(!app.isMMK) {
                        app.form.unit_price_fx.push(document.getElementsByName('unit_price_fx[]')[i].value);

                        app.form.total_amount_fx.push(document.getElementsByName('total_amount_fx[]')[i].value);
                    }

                    /****if(document.getElementsByName('chk_foc[]')[i].checked == true) {
                            app.form.is_foc.push('1');
                        } else {
                            app.form.is_foc.push('0');
                        }***/

                    //for  price variant
                    /* var key = app.products.findIndex(x => x.product_id == document.getElementsByName('product[]')[i].value);
                     var product_price = app.products[key].product_price;*/

                    if(app.purchase_type == 2) {
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
                    .post("/purchase/create")
                    .then(function(data) {
                        // console.log(data.data);
                        if(data.status == "success") {
                            //reset form data
                            event.target.reset();
                            $(".txt_product").select2();
                            $('#loading').hide();
                            swal({
                                title: "Success!",
                                text: 'Purchase Invoice is saved.',
                                icon: "success",
                                button: true
                            }).then(function() {
                                app.$router.push({name:'purchase'})
                                //generate pdf
                                // if(app.purchase_type == 1)
                                // {
                                //     var baseurl = window.location.origin;
                                //     window.open(baseurl+'/generate_invoice/'+data.sale_id);
                                // }
                            });
                        } else {
                            $('#loading').hide();
                            $.notify("Something Wrong", {
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

                if(app.form.payment_type =='credit' && (app.form.pay_amount == 0 || app.form.pay_amount == null)) {
                    app.form.account_group = '';
                    app.form.cash_bank_account = '';
                }

                app.edit_form = $("#purchase_form").serialize();
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


                        app.form.product_pivot.push(document.getElementsByName('product[]')[i].options[document.getElementsByName('product[]')[i].options.selectedIndex].dataset.pivotid);

                        if(!app.isMMK) {
                            app.form.unit_price_fx.push(document.getElementsByName('unit_price_fx[]')[i].value);

                            app.form.total_amount_fx.push(document.getElementsByName('total_amount_fx[]')[i].value);
                        }
                        //for  price variant
                        /*var key = app.products.findIndex(x => x.product_id == document.getElementsByName('product[]')[i].value);
                        var product_price = app.products[key].product_price;*/

                        if(app.purchase_type == 2) {
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
                        .patch("/purchase/" + app.purchase_id+"/update")
                        .then(function(data) {
                            if(data.status == "success") {

                                //reset form data
                                event.target.reset();
                                $(".txt_product").select2();
                                $('#loading').hide();

                                swal({
                                    title: "Success!",
                                    text: 'Purchase Invoice is updated.',
                                    icon: "success",
                                    button: true
                                }).then(function() {
                                    app.$router.push({name:'purchase'})
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
        // productSelect(){
        //    var app=this;
        //     $(".txt_product").on("select2:select", function(e) {
        //         var data = e.params.data;
        //         console.log(data);
        //         var product_key = app.products.findIndex(x => x.product_id == data.id);
        //         var product_brand = parseInt(app.products[product_key].brand_id);
        //         var product_category = parseInt(app.products[product_key].category_id);
        //         var row_id = $(this).closest('tr').attr('id');
        //         $('#brand_'+row_id).val(product_brand).trigger('change');
        //         $('#category_'+row_id).val(product_category).trigger('change');
        //         app.selling_uoms = [];
        //
        //         var uom      = e.target.options[e.target.options.selectedIndex].dataset.uom;
        //         var uom_id   = e.target.options[e.target.options.selectedIndex].dataset.uomid;
        //         var price    = e.target.options[e.target.options.selectedIndex].dataset.price;
        //
        //         var retail1_price    = e.target.options[e.target.options.selectedIndex].dataset.retail1;
        //         var retail2_price    = e.target.options[e.target.options.selectedIndex].dataset.retail2;
        //         var wholesale_price  = e.target.options[e.target.options.selectedIndex].dataset.wholesale;
        //
        //         $("#uom_"+row_id).attr('data-uom',uom);
        //
        //         //auto add new product row
        //         /* if($(this).closest('tr').next().hasClass("total_row")) {
        //              app.addProduct();
        //          } */
        //
        //         //add Warehouse UOM Selling Price
        //         var price_selectbox_id = $("#unit_price_"+row_id);
        //         price_selectbox_id.find('option').remove();
        //         price_selectbox_id.append('<option value="">Select One</option>');
        //         if(retail1_price != null && retail1_price != "null" && retail1_price != '') {
        //             price_selectbox_id.append('<option value="'+retail1_price+'" selected>'+retail1_price+'</option>');
        //         }
        //         if(retail2_price != null && retail2_price != "null" && retail2_price != '') {
        //             price_selectbox_id.append('<option value="'+retail2_price+'">'+retail2_price+'</option>');
        //         }
        //         if(wholesale_price != null && wholesale_price != "null" && wholesale_price != '') {
        //             price_selectbox_id.append('<option value="'+wholesale_price+'">'+wholesale_price+'</option>');
        //         }
        //
        //         var selectbox_id = $("#uom_"+row_id);
        //
        //         selectbox_id.find('option').remove();
        //
        //         //add pre-defined product uom
        //
        //         if(selectbox_id.find('option[value="'+uom_id+'"]').text() == "") {
        //             selectbox_id.append('<option value="">Select One</option><option value="'+uom_id+'" data-relation="" data-uomqty = "1" data-productuom = "'+uom+'" data-productid="'+data.id+'" data-perprice="'+price+'" data-price="'+price+'" data-retail1="'+retail1_price+'" data-retail2="'+retail2_price+'" data-wholesale="'+wholesale_price+'" selected>'+uom+'</option>');
        //         }
        //         $(".txt_uom").select2();
        //
        //         var key = app.products.findIndex(x => x.product_id == data.id);
        //         var available_qty = parseFloat(app.products[key].in_count) - parseFloat(app.products[key].out_count);
        //         var available_id = $("#stock_available_"+row_id).val(available_qty);
        //
        //         app.getSellingUomByProduct(selectbox_id, data.id);
        //         app.calTotalAmount($("#unit_price_"+row_id));
        //     });
        // },

        decimalFormat(num)
        {
           var decimal_num = Number.isInteger(parseFloat(num))== true ?  parseInt(num) : num.toFixed(3);
           return decimal_num;
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
                    /***if(app.form.payment_type == 'cash') {
                        if(app.form.pay_amount == 0) {
                            app.form.pay_amount = parseInt((app.form.sub_total) - parseInt(discount));
                        }
                    } else {
                        if(app.form.pay_amount == '') {
                            app.form.pay_amount = 0;
                        }
                    }***/

                    if(app.prev_pay_amount != 0 || app.form.pay_amount != 0) {
                        if(app.prev_pay_amount != 0) {
                            var currency_rate = app.form.currency_rate == "" ? 0 : app.form.currency_rate;
                            /**if(currency_rate != 0) {
                                var prev_pay_amt_fx = parseInt(app.prev_pay_amount)/parseFloat(currency_rate);
                                app.form.pay_amount = app.prev_pay_amount;
                                app.form.pay_amount_fx = app.decimalFormat(prev_pay_amt_fx);
                            } else {
                                app.form.pay_amount = app.prev_pay_amount;
                                app.form.pay_amount_fx = 0;
                            }**/
                            if(app.currency_type == 1) {
                                if(currency_rate != 0) {
                                    var prev_pay_amt_fx = parseInt(app.prev_pay_amount)/parseFloat(currency_rate);
                                    app.form.pay_amount = app.prev_pay_amount;
                                    app.form.pay_amount_fx = app.decimalFormat(prev_pay_amt_fx);
                                } else {
                                    app.form.pay_amount = app.prev_pay_amount;
                                    app.form.pay_amount_fx = 0;
                                }
                            } else {
                                if(currency_rate != 0) {
                                    var prev_pay_amt_fx = app.prev_pay_amount;
                                    app.form.pay_amount = Math.round(parseFloat(app.prev_pay_amount) * parseFloat(currency_rate));
                                    app.form.pay_amount_fx = app.decimalFormat(prev_pay_amt_fx);
                                } else {
                                    app.form.pay_amount =0;
                                    app.form.pay_amount_fx = app.decimalFormat(app.prev_pay_amount);
                                }
                            }
                        } else {
                            //app.form.pay_amount = app.prev_pay_amount;
                            //app.form.pay_amount_fx = 0;
                        }
                    } else {
                        app.form.pay_amount = 0;
                        app.form.pay_amount_fx = 0;
                    }

                    app.form.balance_amount  = sub_total - (parseInt(app.form.pay_amount) + parseInt(discount));
                } else {
                    //
                }
            });
        },
        calAmt(e){
            /**var qty=$('#qty_1').val();
            var price=$('#unit_price_1').val();
            var total=parseInt(qty) * parseInt(price);
            $('#total_amount_1').val(total);**/
            // console.log(total);
        }

    }
}
</script>

<style scoped>

</style>
