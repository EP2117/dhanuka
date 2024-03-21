<template>
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="#!"><i class="feather icon-home"></i></a></li>
                <li class="breadcrumb-item"><a :href="site_path+'/'">Home</a></li>
                <li class="breadcrumb-item"><a :href="site_path+'/office'">Office Sale</a></li>
                <li class="breadcrumb-item"><router-link tag="span" to="/collection" class="font-weight-normal"><a href="#">Collection</a></router-link></li>
                <li class="breadcrumb-item active" aria-current="page">Collection Form</li>

            </ol>
        </nav>
        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h4 class="mb-0 text-gray-800">Collection Form</h4>
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
                                <label for="transfer_no">Collection No.</label>
                                <input type="text" class="form-control" id="collection_no" name="collection_no" v-model="form.collection_no" readonly>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-md-4">
                                <label for="collection_date">Date</label>
                                <input type="text" class="form-control datetimepicker" id="collection_date" name="collection_date"
                                v-model="form.collection_date" required>
                            </div>

                            <!--<div class="form-group col-md-4">
                                <label for="branch_id">Collect Type</label>
                                <select id="collect_type_id" class="form-control mm-txt"
                                    name="collect_type" v-model="form.collect_type" style="width:100%" :disabled="cusReadonly" required
                                >
                                    <option value="">Select One</option>
                                    <option value="cash">Cash</option>
                                    <option value="bank">Bank</option>
                                </select>
                            </div>-->
                            
                        </div>
                        <div class="row">
                            <!--<div class="form-group col-md-4">
                                <label for="branch_name">Branch</label>
                                 <input type="text" class="form-control" id="branch_name" name="branch_name"
                                v-model="user_branch" readonly>
                            </div>-->
                            <div class="form-group col-md-4">
                                <label for="branch_id">Branch</label>
                                <select id="branch_id" class="form-control mm-txt"
                                    name="branch_id" v-model="form.branch_id" style="width:100%" :disabled="cusReadonly" required
                                >
                                    <option value="">Select One</option>
                                    <option v-for="branch in branches" :value="branch.id"  >{{branch.branch_name}}</option>
                                </select>
                            </div>

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
                            
                            <!-- <div class="form-group col-md-4">
                                <label for="collection_date">Date</label>
                                <input type="text" class="form-control datetimepicker" id="collection_date" name="collection_date"
                                v-model="form.collection_date" required>
                            </div> -->
                        </div>

                        <div class="row mt-3">

                            <div class="form-group col-md-4">
                                <label for="customer_id">Customer</label>
                                <select id="customer_id" class="form-control"
                                    name="customer_id" v-model="form.customer_id" style="width:100%" :disabled="cusReadonly" required
                                >
                                    <option value="">Select One</option>
                                    <option v-for="cus in customers" :value="cus.id"  >{{cus.cus_name}}</option>
                                </select>
                            </div>
                            
                            <div class="form-group col-md-4">
                                <label for="invoices">Invoice</label>
                               <!-- <select multiple class="form-control invoices"
                                    name="invoices[]" v-model="form.invoices" :required="isRequired" style="width:100%"
                                >
                                    <option v-for="sale in sale_invoices" :value="sale.id">{{sale.invoice_no}}_{{(sale.total_amount-(sale.discount+sale.collection_amount+sale.pay_amount)).toLocaleString()}}_{{sale_invoice_date(sale.invoice_date)}}</option>
                                </select>-->

                                <select multiple class="form-control invoices"
                                    name="invoices[]" v-model="form.invoices" :required="isRequired" style="width:100%"
                                >
                                    <option v-for="sale in sale_invoices" :value="sale.id">{{sale.invoice_no}}_{{sale.sale_balance.toLocaleString()}}_{{sale_invoice_date(sale.invoice_date)}}</option>
                                </select>

                            </div>
                            
                        </div>

                        <div class="row mt-3" >
                            <div class="col-md-4 custom-control custom-switch" style="padding-left:10px;">
                              <label style='margin-right:50px;' class="ml-0">Auto Payment</label>
                              <input type="checkbox" class="custom-control-input" id="is_auto" name="is_auto" checked @change="checkAuto($event.target)">                              
                              <label class="custom-control-label" for="is_auto"></label>
                            </div> 

                            <div class="form-group col-md-4">
                                <label for="pay_amount">Pay Amount ({{sign}})</label>
                                <input type="text" class="form-control decimal_no" id="pay_amount" name="pay_amount" v-model="form.pay_amount" @blur="calcAutoPay()" :readonly="!form.is_auto" :required="form.is_auto">
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

                        <div class="row mt-4">
                            <div class="col-md-12">
                                <span class="d-none d-sm-inline-block btn-sm btn-primary shadow-sm bg-blue"><i class="fas fa-list text-white"></i> Invoice Details</span>
                            </div>
                        </div>

                        <div class="row mt-4" v-if="!isEdit">
                            <div class="col-md-12 table-responsive">
                                <table class="table table-bordered table_no" id="product_table">
                                    <thead class="thead-grey">
                                        <tr>
                                            <th scope="col" >No.</th>
                                            <th scope="col" >Invoice Date</th>
                                            <th scope="col" >Invoice No.</th>
                                            <th scope="col" >Sale Man</th>
                                            <th scope="col" v-if="!isMMK">Invoice <br />Amount({{sign}})</th>
                                            <th scope="col" >Invoice <br />Amount(MMK)</th>
                                            <th scope="col" v-if="!isMMK">Previous Paid <br />Amount({{sign}})</th>
                                            <th scope="col" >Previous Paid <br />Amount(MMK)</th>
                                            <th scope="col" v-if="!isMMK">Pay <br />Amount({{sign}})</th>
                                            <th scope="col" >Pay <br />Amount(MMK)</th>
                                            <th scope="col" v-if="!isMMK">Discount({{sign}})</th>
                                            <th scope="col" >Discount(MMK)</th>
                                            <th scope="col" v-if="!isMMK">Balance({{sign}})</th>
                                            <th scope="col" >Balance(MMK)</th>
                                            <th scope="col" v-if="!isMMK">Gain <br/>Amount(MMK)</th>
                                            <th scope="col" v-if="!isMMK">Lost <br/>Amount(MMK)</th>
                                            <th scope="col"></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <template v-for="sale in sale_invoices">
                                        <tr :id="sale.id" style="display:none;" data-pivotid="0">
                                            <td class="text-right"></td>
                                            <td>{{sale.invoice_date}}</td>
                                            <td>{{sale.invoice_no}}</td>
                                             <td v-if="sale.sale_man != null">
                                                <input type="text" :id="'sale_man'+sale.id" class="form-control num_txt sale_man" :value="sale.sale_man.sale_man" :readonly="isReadonly" style="min-width:150px;" />
                                            </td>
                                            <td v-else>
                                            </td>
                                            <td v-if="sale.is_opening==1">
                                                <input type="text" :id="'inv_amt'+sale.id" class="form-control num_txt inv_amt" readonly :value="parseInt(sale.total_amount)+parseInt(sale.tax_amt)" />
                                            </td>

                                            <td v-if="!isMMK">
                                                <input type="text" :id="'inv_amt_fx'+sale.id" class="form-control decimal_no inv_amt_fx" readonly :value="parseFloat(sale.net_total_fx) + parseFloat(sale.tax_amt)" />
                                            </td>

                                            <td v-if="sale.is_opening!=1">
                                                <input type="text" :id="'inv_amt'+sale.id" class="form-control num_txt inv_amt" readonly :value="parseInt(sale.net_total) + parseInt(sale.tax_amt_mmk)" />
                                            </td>

                                            <td v-if="!isMMK">
                                                <input type="text" :id="'prev_amt_fx'+sale.id" class="form-control decimal_no prev_amt_fx" readonly :value="decimalFormat(parseFloat(sale.pay_amount_fx) + parseFloat(sale.collection_amount_fx))" />
                                            </td>

                                            <td>
                                                <input type="text" :id="'prev_amt'+sale.id" class="form-control num_txt prev_amt" readonly :value="parseInt(sale.pay_amount) + parseInt(sale.collection_amount) + parseInt(sale.return_amount) + parseInt(sale.cash_return_amount) + parseInt(sale.customer_return_amount)" />

                                                <input type="hidden" :id="'gain_amt'+sale.id" class="form-control num_txt gain_amt" :value="sale.gain_amount" />

                                                <input type="hidden" :id="'loss_amt'+sale.id" class="form-control num_txt loss_amt" :value="sale.loss_amount" />

                                            </td>

                                            <td v-if="!isMMK">
                                                <input type="text" :id="'pay_amt_fx'+sale.id" class="form-control decimal_no pay_amt_fx" :data-rate="sale.currency_rate" :data-id="sale.id" :readonly="isReadonly" @blur="calcPay(sale.id)"/>
                                            </td>
                                            
                                            <td>
                                                <input type="text" :id="'pay_amt'+sale.id" class="form-control num_txt pay_amt" :readonly="isReadonly" @blur="calcPay(sale.id)"/>
                                            </td>
                                            <td v-if="!isMMK">
                                                <input type="text" :id="'discount_amt_fx'+sale.id" class="form-control decimal_no discount_amt_fx" @blur="payDiscountFx(sale.id)" />
                                            </td>
                                            <td>
                                                <input type="text" :id="'discount_amt'+sale.id" class="form-control num_txt discount_amt" @blur="payDiscount(sale.id)" :readonly="!isMMK" />
                                            </td>
                                            <td v-if="!isMMK">
                                                <input type="text" :id="'balance_fx'+sale.id" class="form-control decimal_no balance_amt_fx"  :data-id="sale.id" readonly />
                                            </td>
                                            <td>
                                                <input type="text" :id="'balance'+sale.id" class="form-control num_txt balance_amt"  :data-id="sale.id" readonly />
                                            </td>
                                            <td v-if="!isMMK">
                                                <input type="text" :id="'gain'+sale.id" class="form-control decimal_no gain_amt"  :data-id="sale.id" readonly />
                                            </td>
                                            <td v-if="!isMMK">
                                                <input type="text" :id="'loss'+sale.id" class="form-control decimal_no loss_amt"  :data-id="sale.id" readonly />
                                            </td>
                                            <td class="text-center">
                                                <a class='remove-row red-icon' title='Remove' v-if="user_role != 'admin'"><i class='fas fa-times-circle' style='font-size: 25px;'></i></a>
                                            </td>
                                        </tr>

                                        </template>
                                         <tr v-if="!isMMK">
                                            <td :colspan='total_colspan' class="text-right"> Total Amount</td>
                                            <td>{{sign}} {{total_pay_fx}}</td>
                                            <td>MMK {{total_pay}}</td>
                                            <td></td>
                                            <td></td>
                                        </tr>
                                        <tr v-else>
                                            <td :colspan='total_colspan' class="text-right"> Total Amount(MMK)</td>
                                            <td>{{total_pay}}</td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                        </tr>
                                        <!--<tr>
                                            <td :colspan='6' class="text-right"> Total Amount</td>
                                            <td>{{total_pay}}</td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                        </tr>-->
                                    </tbody>
                                </table>
                            </div>                         

                        </div>


                        <div class="row mt-4" v-else >
                            <div class="col-md-12 table-responsive">
                                <table class="table table-bordered table_no_edit" id="product_table">
                                    <thead class="thead-grey">
                                        <tr>
                                            <th scope="col" >No.</th>
                                            <th scope="col" >Invoice Date</th>
                                            <th scope="col" >Invoice No.</th>
                                            <th scope="col" >Sale Man</th>
                                            <th scope="col" v-if="!isMMK">Invoice <br />Amount({{sign}})</th>
                                            <th scope="col" >Invoice <br />Amount(MMK)</th>
                                            <th scope="col" v-if="!isMMK">Previous Paid <br />Amount({{sign}})</th>
                                            <th scope="col" >Previous Paid <br />Amount(MMK)</th>
                                            <th scope="col" v-if="!isMMK">Pay <br />Amount({{sign}})</th>
                                            <th scope="col" >Pay <br />Amount(MMK)</th>
                                            <th scope="col" v-if="!isMMK">Discount({{sign}})</th>
                                            <th scope="col" >Discount(MMK)</th>
                                            <th scope="col" v-if="!isMMK">Balance({{sign}})</th>
                                            <th scope="col" >Balance(MMK)</th>
                                            <th scope="col" v-if="!isMMK">Gain <br/>Amount(MMK)</th>
                                            <th scope="col" v-if="!isMMK">Lost <br/>Amount(MMK)</th>
                                            <th scope="col"></th>
                                        </tr>
                                    </thead>
                                    <tbody id="edit_invoices">

                                        
                                    </tbody>
                                    <tr v-if="!isMMK">
                                        <td :colspan='total_colspan' class="text-right"> Total Amount</td>
                                        <td>{{sign}} {{total_pay_fx}}</td>
                                        <td>MMK {{total_pay}}</td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                    <tr v-else>
                                        <td :colspan='total_colspan' class="text-right"> Total Amount</td>
                                        <td>{{total_pay}}</td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                </table>
                            </div>                         

                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <input type="submit" class="btn btn-primary btn-sm" value="Save Entry" :disabled="isDisabled">
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
                collection_no: "",
                collection_date: "",
                customer_id: "",
                is_auto: true,
                invoices: [],
                payments: [],
                payments_fx: [],
                discounts: [],
                discounts_fx: [],
                pay_amount: '',
                pay_amount_fx: '',
                total_pay: '',
                total_pay_fx: '',
                collect_type:'',
                remove_pivot_id: [],  
                branch_id: '',
                currency_id: 1,
                currency_rate: '',
                gain: [],
                loss: [],  
                account_group: "",
                cash_bank_account: '',         
              }),
              isEdit: false,
              isReadonly: true,
              isRequired: true,
              customers: [],
              selected_invoices: [],
              sale_invoices: [],
              user_role: '',
              user_year: '',
              total_pay: 0,
              total_pay_fx: 0,
              collection_id: '',
              cusReadonly: false,
              isDisabled: false,
              user_branch: '',
              branches: [],
              site_path: '',
              storage_path: '',
              currency: [],
              sign: 'MMK',
              isMMK: true,
              total_colspan: 6,
              account_group: [],
              cash_bank_accounts: [],
            };
        },

        created() {

            this.user_role = document.querySelector("meta[name='user-role']").getAttribute('content');

            //this.user_branch = document.querySelector("meta[name='user-branch']").getAttribute('content');

            this.site_path = document.querySelector("meta[name='site-path']").getAttribute('content');
            //this.site_path = this.site_path.substring(this.site_path.lastIndexOf('/')+1);
            this.storage_path = document.querySelector("meta[name='storage-path']").getAttribute('content');

            //for save entry button
            /*if(this.user_role == "admin" && !this.isEdit) {
                this.isDisabled = true;
            }*/

            this.user_year = document.querySelector("meta[name='user-year-likelink']").getAttribute('content');

            /*if(this.user_role == "office_order_user")*/
            if(this.user_role != "admin" && this.user_role != "system" && this.user_role != "office_user")
            {
                var url =  window.location.origin;
                window.location.replace(url);
            }

            if(this.$route.params.id) {
                this.isEdit = true;
                this.collection_id = this.$route.params.id;
                this.getCollection(this.collection_id);
                this.cusReadonly = true;
            }
        },
        mounted() {
            $("#loading").hide();
            let app = this;
            app.initCustomers();
            app.initBranches();
            app.initAccountGroup();
            $("#branch_id").on("select2:select", function(e) {
                app.selected_invoices = [];
                var data = e.params.data;
                app.form.branch_id = data.id;
                app.sale_invoices = [];
                $('.invoices').val(app.selected_invoices).trigger('change');
                if(app.form.customer_id != '') {
                    var search ="&branch_id=" + app.form.branch_id+"&currency_id="+app.form.currency_id;
                    axios.get("/customer_credit_sale/"+app.form.customer_id+"?"+search).then(({ data }) => (app.sale_invoices = data.data));
                    $(".invoices").select2();
                }
                $("#is_auto").prop("checked", true);
            });

        app.initCurrency();
        $("#currency_id").select2();
        /***$("#currency_id").on("select2:select", function(e) {
            var data = e.params.data;
            app.form.currency_id = data.id;
        });***/

        $("#currency_id").on("select2:select", function(e) {  
            app.sale_invoices=[];          
            var data = e.params.data;
            app.form.currency_id = data.id;

            app.selected_invoices = [];
            $('.invoices').val(app.selected_invoices).trigger('change');

            var search ="&branch_id=" + app.form.branch_id+"&currency_id="+app.form.currency_id;
                axios.get("/customer_credit_sale/"+app.form.customer_id+"?"+search).then(({ data }) => (app.sale_invoices = data.data));
            $(".invoices").select2();

            var sign = e.target.options[e.target.options.selectedIndex].dataset.sign;
            if(data.id != 1) {
                app.isMMK = false;
                app.total_colspan = 8;
            } else{
                app.isMMK = true;
                app.total_colspan = 6;
            }

            app.sign = sign;    

            $("#is_auto").prop("checked", true);       

            /**$(".sign").html(sign);
            for (let i = 0; i < document.getElementsByClassName("sign").length; i++) {
              document.getElementsByClassName("sign")[i].innerHTML = sign;
            }**/
            
        });

            $("#customer_id").select2();
            $("#customer_id").on("select2:select", function(e) {
                app.selected_invoices = [];
                var data = e.params.data;
                app.form.customer_id = data.id;

                app.sale_invoices = [];
                $('.invoices').val(app.selected_invoices).trigger('change');
                var search ="&branch_id=" + app.form.branch_id+"&currency_id="+app.form.currency_id;
                axios.get("/customer_credit_sale/"+data.id+"?"+search).then(({ data }) => (app.sale_invoices = data.data));
                $(".invoices").select2();
            });
            $(".invoices").select2();
            $(".invoices").on("select2:select", function(e) {
                var data = e.params.data;
                app.isRequired = false;
                app.selected_invoices.push(data.id); 

                var unique_invoices = app.selected_invoices.filter((a, b) => app.selected_invoices.indexOf(a) === b);
                // console.log(unique_invoices);
                app.selected_invoices = unique_invoices;

                $('.invoices').val(app.selected_invoices).trigger('change');
                
                $("#"+data.id).show();
                //console.log($('.invoices').val());
                if(app.form.is_auto) {
                    $('.pay_amt').attr('required',false);
                } else {
                    $('.pay_amt:visible').attr('required',true);
                }

                if($("#"+data.id).attr('data-pivotid') != 0) {
                    var pindex = app.form.remove_pivot_id.indexOf($("#"+data.id).attr('data-pivotid'));
                    if (pindex > -1) {
                      app.form.remove_pivot_id.splice(pindex, 1);
                    }   
                }

                app.calcPay(data.id);

                app.calcAutoPay();

                app.calcTotalPay();
            });

            $(".invoices").on("select2:unselect", function(e) {
                var data = e.params.data;
                var unique_invoices = app.selected_invoices.filter((a, b) => app.selected_invoices.indexOf(a) === b);
                app.selected_invoices = unique_invoices;
                const index = app.selected_invoices.indexOf(data.id);
                if (index > -1) {
                  app.selected_invoices.splice(index, 1);
                }
                $('.invoices').val(app.selected_invoices).trigger('change');

                if(app.selected_invoices.length > 0) {
                    app.isRequired = false;
                } else {
                    app.isRequired = true;
                }

                $("#pay_amt"+data.id).attr('required', false);
               
                $("#"+data.id).hide();

                if($("#"+data.id).attr('data-pivotid') != 0) {
                    app.form.remove_pivot_id.push($("#"+data.id).attr('data-pivotid'));  
                }

                app.calcAutoPay();

                app.calcTotalPay();

            });

            $(document).on('click','.remove-row',function(evt) {

                var unique_invoices = app.selected_invoices.filter((a, b) => app.selected_invoices.indexOf(a) === b);
                app.selected_invoices = unique_invoices;

                var unselect_id =  $(this).parents("tr").attr('id');
                $("#pay_amt"+unselect_id).attr('required', false);
                $(this).parents("tr").hide();
                
                var unselect_option = $('.invoices option[value="'+ unselect_id +'"]');
                unselect_option.prop('selected', false);
                const index = app.selected_invoices.indexOf(unselect_id);
                if (index > -1) {
                  app.selected_invoices.splice(index, 1);
                }

                if(app.selected_invoices.length > 0) {
                    app.isRequired = false;
                } else {
                    app.isRequired = true;
                }
                
                 $('.invoices').val(app.selected_invoices).trigger('change');
                //$('.invoices').trigger('change.select2');

                if($("#"+unselect_id).attr('data-pivotid') != 0) {
                    app.form.remove_pivot_id.push($("#"+unselect_id).attr('data-pivotid'));  
                }

                app.calcAutoPay();

                app.calcTotalPay();
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
                    minDate: app.user_year+"-01-01",
                    maxDate: app.user_year+"-12-31",
                })
                .on("dp.show", function(e) {
                    //app.form.collection_date = moment().format('YYYY-MM-DD');
                    var y = new Date().getFullYear();
                    if(app.user_year < y) { 
                      if(app.form.collection_date == app.user_year+"-12-31" ||  app.form.collection_date == '') {
                         app.form.collection_date = app.user_year+"-12-31";
                      }
                    }
                })
                .on("dp.change", function(e) {
                    var formatedValue = e.date.format("YYYY-MM-DD");
                    //console.log(formatedValue);
                    app.form.collection_date = formatedValue;
                });

                $(document).on('blur',  '.pay-change',function () {
                    var sale_id = $(this).attr('data-id');
                    app.calcPay(sale_id);
                });

                $(document).on('blur',  '.discount-change',function () {
                    var sale_id = $(this).attr('data-id');
                    app.payDiscount(sale_id);
                });

                $(document).on('blur',  '.discount-change-fx',function () {
                    var sale_id = $(this).attr('data-id');
                    app.payDiscountFx(sale_id);
                });

            $(document).on('blur','#currency_rate',function(e) {
                //app.calcAutoPay();
                var currency_rate = app.form.currency_rate == '' ? 0 : app.form.currency_rate;

                $(".balance_amt:visible").each(function() {
                    var sale_id = $(this).attr('data-id');

                    var pay_amt_fx = $('#pay_amt_fx'+sale_id).val() == '' ? 0 : $('#pay_amt_fx'+sale_id).val();

                    var dsc_fx=$('#discount_amt_fx'+sale_id).val() == '' ? 0 : $('#discount_amt_fx'+sale_id).val();

                    var bal_fx=$('#balance_fx'+sale_id).val() == '' ? 0 : $('#balance_fx'+sale_id).val();

                    $('#pay_amt'+sale_id).val(parseFloat(parseFloat(pay_amt_fx) * parseFloat(currency_rate)));
                    //alert($('#pay_amt'+sale_id).val());
                    $('#discount_amt'+sale_id).val(parseFloat(dsc_fx) * parseFloat(currency_rate));

                     /***var invoice_bal_mmk = parseInt($('#inv_amt'+sale_id).val()) - (parseInt($('#prev_amt'+sale_id).val()) + parseInt($('#pay_amt'+sale_id).val())+parseInt($('#discount_amt'+sale_id).val()));
                     $(this).val(invoice_bal_mmk);***/

                    var bal_mmk = (parseInt($('#inv_amt'+sale_id).val()) - (parseInt($('#prev_amt'+sale_id).val()) + parseInt($('#pay_amt'+sale_id).val())+parseInt($('#discount_amt'+sale_id).val()))) + parseInt(parseInt($('#gain_amt'+sale_id).val())) - parseInt(parseInt($('#loss_amt'+sale_id).val()));
                    $(this).val(bal_mmk);

                   // $('#balance'+sale_id).val(parseFloat(bal_fx) * parseFloat(currency_rate));

                });

                app.calcTotalPay();
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

            sale_invoice_date(date){
               return moment(date).format('DD/MM/YY');
            },

            initCurrency() {
                axios.get("/all_currency").then(({ data }) => (this.currency = data.data));
                $("#currency_id").select2();

            },

            decimalFormat(num)
            {
               var decimal_num = Number.isInteger(parseFloat(num))== true ?  parseInt(num) : parseFloat(num).toFixed(3);
               return decimal_num;
            },
            test(){
                alert('k');
            },
            initBranches() {
              axios.get("/branches_byuser").then(({ data }) => (this.branches = data.data));
              $("#branch_id").select2();
            },
            initCustomers() {
              axios.get("/customers").then(({ data }) => (this.customers = data.data));
              $("#customer_id").select2();
            },

            checkAuto(obj) {          
                let app = this;
                var is_auto = $(obj).prop("checked");

                if(app.selected_invoices.length > 0) {
                    app.isRequired = false;
                } else {
                    app.isRequired = true;
                }

                if(is_auto){
                    app.form.is_auto = true;
                    app.isReadonly = true;
                    $('.pay_amt').attr('required',false);
                    if(app.isEdit || !app.isMMK) {
                        $('.pay_amt:visible').attr('readonly',true);
                        $('.pay_amt_fx:visible').attr('readonly',true);
                    }
                } else {
                    $('.pay_amt:visible').attr('required',true);
                    app.form.pay_amount = "";
                    app.form.is_auto = false;
                    app.isReadonly = app.isMMK ? false : true;
                    /**if(app.isEdit) {
                        $('.pay_amt:visible').attr('readonly',false);
                        $('.pay_amt:visible').attr('required',true);
                    }**/
                    if(app.isEdit) {
                        if(app.isMMK) {
                            $('.pay_amt:visible').attr('readonly',false);
                        } else {
                            $('.pay_amt:visible').attr('readonly',true);
                            $('.pay_amt_fx:visible').attr('readonly',false);
                        }
                        
                        $('.pay_amt:visible').attr('required',true);
                    } else {
                        if(app.isMMK) {
                            $('.pay_amt:visible').attr('readonly',false);
                        } else {
                            $('.pay_amt:visible').attr('readonly',true);
                            $('.pay_amt_fx:visible').attr('readonly',false);
                        }
                    }
                }
            },

            OldcalcAutoPay() {

                let app = this;
                if(app.form.is_auto) {
                    if((app.sale_invoices.length > 0 && app.selected_invoices.length > 0) || app.isEdit) {

                        var payment = $("#pay_amount").val();
                        var total_balance = 0;

                        $(".balance_amt:visible").each(function() {
                            var discount = $('#discount_amt'+bal_sale_id).val() == '' ||  $('#discount_amt'+bal_sale_id).val() == null ? 0 : $('#discount_amt'+bal_sale_id).val();

                            var bal_sale_id = $(this).attr('data-id');
                            total_balance += parseInt($('#inv_amt'+bal_sale_id).val()) - (parseInt($('#prev_amt'+bal_sale_id).val()) + parseInt(discount));
                            //total_balance = parseInt(total_balance) + parseInt($(this).val());
                        });
                        console.log('t_balance is ' + total_balance + 'payment' + payment);

                        if(payment >  total_balance) {
                            swal("Warning!", "Your payment is more than total balance."+ total_balance, "warning");
                            this.form.pay_amount='';
                            // $('#pay_amount').val('');
                            // $('#pay_amount').focus();
                            return false;
                        } else {

                            var sale_id = '';
                            var balance = 0;
                            //auto  payment
                            $(".balance_amt:visible").each(function() {
                                sale_id = $(this).attr('data-id');
                                var dsc=$('#discount_amt'+sale_id).val();
                                console.log('desc is' +dsc);
                                if(dsc==''){
                                    dsc=0;
                                }
                                balance = parseInt($('#inv_amt'+sale_id).val()) - (parseInt($('#prev_amt'+sale_id).val())+parseInt(dsc));
                                console.log('Inv ' +parseInt($('#inv_amt'+sale_id).val()) )
                                console.log('Pre ' +parseInt($('#prev_amt'+sale_id).val()) )
                                console.log('dsc ' + parseInt(dsc));
                                console.log('balance' + balance);
                                // alert(balance);
                                if(payment == 0)  {
                                    var dsc=$('#discount_amt'+sale_id).val()
                                    if(dsc==''){
                                        dsc=0;
                                    }
                                    $('#pay_amt'+sale_id).val('');
                                    payment = 0;
                                    var invoice_bal = parseInt($('#inv_amt'+sale_id).val()) - (parseInt($('#prev_amt'+sale_id).val()) + parseInt(payment)+parseInt(dsc));

                                        $(this).val(invoice_bal);
                                } else {
                                    if(parseInt(payment) <= parseInt(balance)) {
                                        var dsc=$('#discount_amt'+sale_id).val();
                                        if(dsc==''){
                                            dsc=0;
                                        }
                                        $('#pay_amt'+sale_id).val(payment);
                                        var invoice_bal = parseInt($('#inv_amt'+sale_id).val()) - (parseInt($('#prev_amt'+sale_id).val()) + parseInt(payment)+parseInt(dsc));
                                        payment = 0;
                                        $(this).val(invoice_bal);
                                    } else {
                                        payment = parseInt(payment) - parseInt(balance);
                                        $('#pay_amt'+sale_id).val(balance);
                                        var invoice_bal = parseInt($('#inv_amt'+sale_id).val()) - (parseInt($('#prev_amt'+sale_id).val()) + parseInt(balance) + parseInt(dsc));
                                        $(this).val(invoice_bal);
                                    }
                                }
                            });
                        }


                    } else {
                        /*swal("Warning!", "There is no invoice to pay.", "warning");
                        $('#pay_amount').val('');
                        $('#pay_amount').focus();
                        return false;*/
                    }
                }

                app.calcTotalPay();
            },

            calcAutoPay() {
                let app = this;
                var currency_rate = app.form.currency_rate == '' ? 0 : app.form.currency_rate;
                if(app.form.is_auto) {
                    if((app.sale_invoices.length > 0 && app.selected_invoices.length > 0) || app.isEdit) {
                        var payment = $("#pay_amount").val() == '' ? 0 : $("#pay_amount").val();
                        var total_balance = 0;
                        var total_balance_fx = 0;
                        // this.calcPay(p_id);

                        $(".balance_amt:visible").each(function() {
                            var bal_sale_id = $(this).attr('data-id');

                            total_balance += parseInt($('#inv_amt'+bal_sale_id).val()) - (parseInt($('#prev_amt'+bal_sale_id).val()));

                            if(!app.isMMK) {
                                total_balance_fx += parseFloat($('#inv_amt_fx'+bal_sale_id).val()) - (parseFloat($('#prev_amt_fx'+bal_sale_id).val()));
                            }

                            //total_balance = parseInt(total_balance) + parseInt($(this).val());
                            // console.log('Total Balance is' +total_balance);
                        });

                        if(!app.isMMK) {
                            total_balance = parseFloat(total_balance_fx).toFixed(3);
                            payment = parseFloat(payment).toFixed(3);
                        } else {
                            total_balance = parseInt(total_balance);
                            payment = Math.round(payment);
                        }
                        console.log('Payment is '+ payment);
                        if(parseFloat(payment) >  parseFloat(total_balance)) {
                            swal("Warning!", "Your payment is more than total balance."+ total_balance, "warning");
                            console.log('T is' +total_balance);
                            this.form.pay_amount='';
                            // $('#pay_amount').val('');
                            // document.getElementById('pay_amount').value = '';
                            // $('#pay_amount').focus();
                            // console.log('Pay is '+$('#pay_amount').val());
                            // return false;
                        } else {
                            // alert('a');
                            var sale_id = '';
                            var balance = 0;
                            var balance_fx = 0;
                            //auto  payment
                            $(".balance_amt:visible").each(function() {
                                sale_id = $(this).attr('data-id');
                                // alert($('#discount_amt'+sale_id).val());
                                var dsc=$('#discount_amt'+sale_id).val();
                                if(dsc=='' || isNaN(dsc)){
                                    dsc=0;
                                }
                                var dsc_fx=$('#discount_amt_fx'+sale_id).val()
                                if(dsc_fx==''){
                                    dsc_fx=0;
                                }
                                balance = parseInt($('#inv_amt'+sale_id).val()) - (parseInt($('#prev_amt'+sale_id).val())+parseInt(dsc));
                                console.log('Balances is '+ balance);

                                balance_fx = parseFloat($('#inv_amt_fx'+sale_id).val()) - (parseFloat($('#prev_amt_fx'+sale_id).val())+parseFloat(dsc_fx));
                                console.log('Balances is '+ balance);
                                balance_fx = app.decimalFormat(balance_fx);

                                if(!app.isMMK) {
                                    balance = parseFloat(balance_fx);
                                } else {
                                    balance = parseInt(balance);
                                }
                                //alert(payment);
                                if(payment == 0)  {
                                    var dsc=$('#discount_amt'+sale_id).val();
                                    if(dsc=='' || isNaN(dsc)){
                                        dsc=0;
                                    }

                                    var dsc_fx=$('#discount_amt_fx'+sale_id).val();
                                    if(dsc_fx=='' || isNaN(dsc_fx)){
                                        dsc_fx=0;
                                    }

                                    $('#pay_amt'+sale_id).val('');
                                    payment = 0;
                                    $('#pay_amt_fx'+sale_id).val('');

                                    var invoice_bal = parseInt($('#inv_amt'+sale_id).val()) - (parseInt($('#prev_amt'+sale_id).val()) + parseInt(dsc)+parseInt(payment));

                                    invoice_bal = (parseInt(invoice_bal) + parseInt($('#gain_amt'+sale_id).val())) - parseInt($('#loss_amt'+sale_id).val());

                                    $(this).val(invoice_bal);

                                    var invoice_bal_fx = parseFloat($('#inv_amt_fx'+sale_id).val()) - (parseFloat($('#prev_amt_fx'+sale_id).val()) + parseFloat(dsc_fx)+parseFloat(payment));
                                    invoice_bal_fx = invoice_bal_fx.toFixed(3);
                                    //alert(app.decimalFormat(parseFloat(invoice_bal_fx)));
                                    $('#balance_fx'+sale_id).val(app.decimalFormat(parseFloat(invoice_bal_fx)));
                                    
                                } else {
                                    if(payment <= balance) {
                                        console.log('a');
                                        var dsc=$('#discount_amt'+sale_id).val();
                                        if(dsc=='' || isNaN(dsc)){
                                            dsc=0;
                                        }

                                        var dsc_fx=$('#discount_amt_fx'+sale_id).val();
                                        if(dsc_fx=='' || isNaN(dsc_fx)){
                                            dsc_fx=0;
                                        }

                                        if(app.isMMK) {
                                            $('#pay_amt'+sale_id).val(parseInt(payment));
                                            var invoice_bal = parseInt($('#inv_amt'+sale_id).val()) - (parseInt($('#prev_amt'+sale_id).val()) + parseInt(payment)+parseInt(dsc));
                                            payment = 0;

                                            $(this).val(invoice_bal);
                                        } else {
                                            dsc = parseFloat(dsc_fx) * parseFloat(currency_rate);
                                            $('#discount_amt'+sale_id).val(app.decimalFormat(dsc));

                                            $('#pay_amt_fx'+sale_id).val(payment);
                                            //alert(payment+','+currency_rate);
                                            var pay_mmk = parseFloat(payment) * parseFloat(currency_rate);
                                            $('#pay_amt'+sale_id).val(Math.round(pay_mmk));

                                            var invoice_bal_fx = parseFloat($('#inv_amt_fx'+sale_id).val()) - (parseFloat($('#prev_amt_fx'+sale_id).val()) + parseFloat(payment)+parseFloat(dsc));
                                            payment = 0;

                                            var invoice_bal = parseInt($('#inv_amt'+sale_id).val()) - (parseInt($('#prev_amt'+sale_id).val()) + parseInt($('#pay_amt'+sale_id).val())+parseInt(dsc));
                                            payment = 0;

                                            invoice_bal = (parseInt(invoice_bal) + parseInt($('#gain_amt'+sale_id).val())) - parseInt($('#loss_amt'+sale_id).val());

                                            //$(this).val(invoice_bal);

                                            //invoice_bal = app.decimalFormat(invoice_bal);

                                            /*** var invoice_bal_mmk = parseInt($('#inv_amt'+sale_id).val()) - (parseInt($('#prev_amt'+sale_id).val()) + parseInt(pay_mmk)+parseInt(dsc));
                                             $(this).val(invoice_bal_mmk);***/

                                             if(parseFloat(invoice_bal_fx) == 0) {
                                                $(this).val(app.decimalFormat(parseFloat(invoice_bal_fx) * parseFloat(currency_rate)));
                                             } else {
                                                $(this).val(invoice_bal);
                                             }
                                            //$(this).val(app.decimalFormat(parseFloat(invoice_bal_fx) * parseFloat(currency_rate)));

                                            //invoice_bal = invoice_bal.toFixed(3);
                                            $('#balance_fx'+sale_id).val(app.decimalFormat(invoice_bal_fx));
                                        }

                                    } else {
                                        var dsc=$('#discount_amt'+sale_id).val();
                                        if(dsc=='' || isNaN(dsc)){
                                            dsc=0;
                                        }

                                        var dsc_fx=$('#discount_amt_fx'+sale_id).val();
                                        if(dsc_fx=='' || isNaN(dsc_fx)){
                                            dsc_fx=0;
                                        }
                                        console.log('b');
                                        if(app.isMMK) {
                                            payment = parseInt(payment) - parseInt(balance);
                                            $('#pay_amt'+sale_id).val(parseInt(balance));
                                            var invoice_bal = parseInt($('#inv_amt'+sale_id).val()) - (parseInt($('#prev_amt'+sale_id).val()) + parseInt($('#pay_amt'+sale_id).val())+parseInt(dsc));
                                            console.log('Invoice VAl is '+invoice_bal);
                                            $(this).val(invoice_bal);
                                        } else {
                                            payment = parseFloat(payment) - parseFloat(balance);

                                            $('#pay_amt_fx'+sale_id).val(balance);
                                            $('#pay_amt'+sale_id).val(Math.round(app.decimalFormat(balance * parseFloat(currency_rate))));

                                            var invoice_bal_fx = parseFloat($('#inv_amt_fx'+sale_id).val()) - (parseFloat($('#prev_amt_fx'+sale_id).val()) + parseFloat(balance)+parseFloat(dsc_fx));

                                            var invoice_bal = parseInt($('#inv_amt'+sale_id).val()) - (parseInt($('#prev_amt'+sale_id).val()) + parseInt($('#pay_amt'+sale_id).val())+parseInt(dsc));

                                            invoice_bal = (parseInt(invoice_bal) + parseInt($('#gain_amt'+sale_id).val())) - parseInt($('#loss_amt'+sale_id).val());

                                            //$(this).val(invoice_bal);
                                            console.log('Invoice VAl is '+invoice_bal);

                                            if(parseFloat(invoice_bal_fx) == 0) {
                                                $(this).val(app.decimalFormat(parseFloat(invoice_bal_fx) * parseFloat(currency_rate)));
                                             } else {
                                                $(this).val(invoice_bal);
                                             }

                                            //$(this).val(app.decimalFormat(parseFloat(invoice_bal_fx) * parseFloat(currency_rate)));

                                            invoice_bal_fx = invoice_bal_fx.toFixed(3);
                                            $('#balance_fx'+sale_id).val(app.decimalFormat(invoice_bal_fx));
                                        }

                                    }
                                }
                            });
                        }


                    } else {
                        /*swal("Warning!", "There is no invoice to pay.", "warning");
                        $('#pay_amount').val('');
                        $('#pay_amount').focus();
                        return false;*/
                    }
                }
                // app.calcPay(data.id);
                app.calcTotalPay();
            },

            payDiscount(sale_id) {
                //var balance  = parseInt($("#balance"+sale_id).val());
                if($("#discount_amt"+sale_id).val() != "") {
                    var discount = parseInt($("#discount_amt"+sale_id).val());
                } else {
                    var discount = 0;
                }
                if($('#pay_amt'+sale_id).val() != "") {
                    var pay_amount = parseInt($('#pay_amt'+sale_id).val());
                } else {
                    var pay_amount = 0;
                }
                
                // console.log('DSC is'+discount);
                var balance = (parseInt($('#inv_amt'+sale_id).val()) - (parseInt($('#prev_amt'+sale_id).val()) + parseInt(pay_amount))) + parseInt($('#gain_amt'+sale_id).val()) - parseInt($('#loss_amt'+sale_id).val());
                if(discount != "") {
                    // if(discount > balance){
                    //     alert(balance);
                    // }else{
                    //     alert(balance);
                    // }
                    if(parseFloat(discount) > parseFloat(balance)) {
                        swal("Warning!", "Discount amount is greater than balance.", "warning");
                        $("#discount_amt"+sale_id).val('');
                        // $("#balance"+sale_id).val(parseInt($('#inv_amt'+sale_id).val()));
                        $("#balance"+sale_id).val('');
                        $("#discount_amt"+sale_id).focus();
                    } else {
                        //balance = parseInt($('#inv_amt'+sale_id).val()) - (parseInt($('#prev_amt'+sale_id).val()) + parseInt(pay_amount) + parseInt(discount));
                        balance = (parseInt($('#inv_amt'+sale_id).val()) - (parseInt($('#prev_amt'+sale_id).val()) + parseInt(pay_amount)  + parseInt(discount))) + parseInt($('#gain_amt'+sale_id).val()) - parseInt($('#loss_amt'+sale_id).val());
                        $("#balance"+sale_id).val(balance);
                    }
                }else{
                    balance = (parseInt($('#inv_amt'+sale_id).val()) - (parseInt($('#prev_amt'+sale_id).val()) + parseInt(pay_amount)  + parseInt(discount)))  + parseInt($('#gain_amt'+sale_id).val()) - parseInt($('#loss_amt'+sale_id).val());
                    $("#balance"+sale_id).val(balance);            
                }
            },

            payDiscountFx(sale_id) {
                let app = this;
                var currency_rate = app.form.currency_rate == '' ? 0 : app.form.currency_rate;
                if($("#discount_amt_fx"+sale_id).val() != "") {
                    var discount_fx = parseFloat($("#discount_amt_fx"+sale_id).val());
                    $("#discount_amt"+sale_id).val(parseFloat(discount_fx) * parseFloat(currency_rate));
                } else {
                    var discount_fx = 0;
                    $("#discount_amt"+sale_id).val('0');
                }

                if($('#pay_amt_fx'+sale_id).val() != "") {
                    var pay_amount = parseFloat($('#pay_amt_fx'+sale_id).val());
                } else {
                    var pay_amount = 0;
                }

                // console.log('DSC is'+discount);
                var balance_fx = parseFloat($('#inv_amt_fx'+sale_id).val()) - (parseFloat($('#prev_amt_fx'+sale_id).val()) + parseFloat(pay_amount));
                //alert(parseFloat(balance_fx.toFixed(3)));
                balance_fx = parseFloat(balance_fx).toFixed(3);
                if(discount_fx != "") {
                    if(parseFloat(discount_fx) > parseFloat(balance_fx)) {
                        swal("Warning!", "Discount amount is greater than balance.", "warning");
                        $("#discount_amt_fx"+sale_id).val('');
                        $("#discount_amt"+sale_id).val('');
                        //$("#balance_fx"+sale_id).val('');
                       // $("#balance"+sale_id).val('');
                        $("#discount_amt_fx"+sale_id).focus();
                    } else {
                        balance_fx = parseFloat($('#inv_amt_fx'+sale_id).val()) - (parseFloat($('#prev_amt_fx'+sale_id).val()) + parseFloat(pay_amount) + parseFloat(discount_fx));
                        balance_fx = balance_fx.toFixed(3);
                        $("#balance_fx"+sale_id).val(app.decimalFormat(balance_fx));

                        /****var balance = parseInt($('#inv_amt'+sale_id).val()) - (parseInt($('#prev_amt'+sale_id).val()) + parseInt($('#pay_amt'+sale_id).val()) + parseInt($('#discount_amt'+sale_id).val()));

                        $("#balance"+sale_id).val(balance);***/
                        
                        if(parseFloat(balance_fx) == 0) {
                            $("#balance"+sale_id).val(parseFloat(balance_fx) * parseFloat(currency_rate));
                        } else {
                            var balance = (parseInt($('#inv_amt'+sale_id).val()) - (parseInt($('#prev_amt'+sale_id).val()) + parseInt($('#pay_amt'+sale_id).val()) + parseInt($('#discount_amt'+sale_id).val()))) + parseInt($('#gain_amt'+sale_id).val()) - parseInt($('#loss_amt'+sale_id).val());

                            $("#balance"+sale_id).val(balance);
                        }                        
                    }
                }else{
                    balance_fx = parseFloat($('#inv_amt_fx'+sale_id).val()) - (parseFloat($('#prev_amt_fx'+sale_id).val()) + parseFloat(pay_amount) + parseFloat(discount_fx));
                    balance_fx = balance_fx.toFixed(3);
                    $("#balance_fx"+sale_id).val(app.decimalFormat(balance_fx));

                    /***var balance = parseInt($('#inv_amt'+sale_id).val()) - (parseInt($('#prev_amt'+sale_id).val()) + parseInt($('#pay_amt'+sale_id).val()) + parseInt($('#discount_amt'+sale_id).val()));

                    $("#balance"+sale_id).val(balance);***/

                    if(parseFloat(balance_fx) == 0) {
                        $("#balance"+sale_id).val(parseFloat(balance_fx) * parseFloat(currency_rate));
                    } else {
                        var balance = (parseInt($('#inv_amt'+sale_id).val()) - (parseInt($('#prev_amt'+sale_id).val()) + parseInt($('#pay_amt'+sale_id).val()) + parseInt($('#discount_amt'+sale_id).val())))  + parseInt($('#gain_amt'+sale_id).val()) - parseInt($('#loss_amt'+sale_id).val());

                        $("#balance"+sale_id).val(balance);
                    }             
                }

                if(!app.isMMK) {
                    var currency_rate = app.form.currency_rate == '' ? 0 : app.form.currency_rate;
                    //$(".pay_amt_fx:visible").each(function() {
                        var inv_currency_rate = $("#pay_amt_fx"+sale_id).attr('data-rate');
                        var payment = $("#pay_amt_fx"+sale_id).val() == '' ? 0 : $("#pay_amt_fx"+sale_id).val();
                        var disc_fx = $("#discount_amt_fx"+sale_id).val() == '' ? 0 : $("#discount_amt_fx"+sale_id).val();
                        /***if($("#pay_amt_fx"+sale_id).val() != "") {
                            total_fx = parseFloat(total_fx) + parseFloat($("#pay_amt_fx"+sale_id).val());
                        }***/

                        if(!app.isMMK) {
                            //calculate gain or loss
                            var bal = (parseFloat(inv_currency_rate) - parseFloat(currency_rate)) * (parseFloat(payment) + parseFloat(disc_fx));
                            if(currency_rate != '' && currency_rate != 0) {
                                if(parseFloat(inv_currency_rate) > parseFloat(currency_rate)) {
                                    $("#loss"+sale_id).val(Math.round(bal) * -1);
                                    $("#gain"+sale_id).val('0');
                                   /** $("#gain"+sale_id).val(Math.round(bal));
                                    $("#loss"+sale_id).val('0'); **/ 
                                } else {
                                   /** $("#loss"+sale_id).val(Math.round(bal));
                                    $("#gain"+sale_id).val('0'); **/ 
                                    $("#gain"+sale_id).val(Math.round(bal) * -1);
                                    $("#loss"+sale_id).val('0');
                                }
                            } else {
                                $("#loss"+sale_id).val('0');
                                $("#gain"+sale_id).val('0'); 
                            }
                        }
                   // });
                }
            },

            OldpayDiscount(sale_id) {
                //var balance  = parseInt($("#balance"+sale_id).val());
                if($("#discount_amt"+sale_id).val() != "") {
                    var discount = parseInt($("#discount_amt"+sale_id).val());
                } else {
                    var discount = 0;
                }
                var pay_amt = $('#pay_amt'+sale_id).val() == "" ? 0 : $('#pay_amt'+sale_id).val();
                var balance = parseInt($('#inv_amt'+sale_id).val()) - (parseInt($('#prev_amt'+sale_id).val()) + parseInt(pay_amt));
                if(discount != "") {
                    if (discount > balance) {
                        swal("Warning!", "Discount amount is greater than balance.", "warning");
                        $("#discount_amt" + sale_id).val('');
                        $("#discount_amt" + sale_id).focus();
                    } else {
                        balance = parseInt($('#inv_amt' + sale_id).val()) - (parseInt($('#prev_amt' + sale_id).val()) + parseInt(pay_amt) + parseInt(discount));
                        $("#balance" + sale_id).val(balance);
                    }
                }else{
                    balance = parseInt($('#inv_amt'+sale_id).val()) - (parseInt($('#prev_amt'+sale_id).val()) + parseInt(pay_amt) + parseInt(discount));
                    $("#balance"+sale_id).val(balance);
                }
            },

            OldcalcPay(sale_id) {

                let app = this;

                var discount = $("#discount_amt"+sale_id).val();
                // alert(discount);
                if(discount == "") {
                    discount = 0;
                }
                var pay = $('#pay_amt'+sale_id).val();
                if(pay == "") {
                    pay = 0;
                }
                var sale_bal = parseInt($('#inv_amt'+sale_id).val()) - (parseInt($('#prev_amt'+sale_id).val()) + parseInt(discount));
                if(parseInt(pay) > parseInt(sale_bal)) {
                    document.getElementById('pay_amt'+sale_id).value = '';
                    swal("Warning!", "Payment is greater than balance.", "warning");
                } else {
                    var prev_pay = $('#prev_amt'+sale_id).val();
                    if(prev_pay == "") {
                        prev_pay = 0;
                    }
                    var balance = parseInt($('#inv_amt'+sale_id).val()) - (parseInt(prev_pay) + parseInt(pay) + parseInt(discount));

                    $("#balance"+sale_id).val(balance);

                    app.calcTotalPay();
                }
            },

            calcPay(sale_id) {

                let app = this;
                var currency_rate = app.form.currency_rate == '' ? 0 : app.form.currency_rate;
                if(app.isMMK) {
                    var discount = $("#discount_amt"+sale_id).val();
                    if(discount == "") {
                        discount = 0;
                    }
                    var pay = $('#pay_amt'+sale_id).val();
                    if(pay == "") {
                        pay = 0;
                    }
                    var sale_bal = (parseInt($('#inv_amt'+sale_id).val()) - (parseInt($('#prev_amt'+sale_id).val()) + parseInt(discount)))  + parseInt($('#gain_amt'+sale_id).val()) - parseInt($('#loss_amt'+sale_id).val());
                    if(parseInt(pay) > parseInt(sale_bal)) {
                       // alert('aaa');
                        swal("Warning!", "Payment is greater than balance.", "warning");
                        document.getElementById('pay_amt'+sale_id).value = '';

                    } else {
                        var prev_pay = $('#prev_amt'+sale_id).val();
                        if(prev_pay == "") {
                            prev_pay = 0;
                        }
                        var balance = (parseInt($('#inv_amt'+sale_id).val()) - (parseInt(prev_pay) + parseInt(pay) + parseInt(discount)))   + parseInt($('#gain_amt'+sale_id).val()) - parseInt($('#loss_amt'+sale_id).val());

                        $("#balance"+sale_id).val(balance);

                        app.calcTotalPay();
                    }
                } else {
                    //for foreign currency payment
                    var discount_fx = $("#discount_amt_fx"+sale_id).val();
                    if(discount_fx == "" || isNaN(discount_fx)) {
                        discount_fx = 0;
                    }
                    var pay_fx = $('#pay_amt_fx'+sale_id).val();
                    if(pay_fx == "") {
                        pay_fx = 0;
                    }

                    var discount = $("#discount_amt"+sale_id).val();
                    if(discount == "" || isNaN(discount)) {
                        discount = 0;
                    }
                    $('#pay_amt'+sale_id).val(Math.round(parseFloat(pay_fx) * parseFloat(currency_rate)));

                    var sale_bal_fx = parseFloat($('#inv_amt_fx'+sale_id).val()) - (parseFloat($('#prev_amt_fx'+sale_id).val()) + parseFloat(discount_fx));

                    pay_fx = parseFloat(pay_fx).toFixed(3);
                    sale_bal_fx = parseFloat(sale_bal_fx).toFixed(3);
                    if(parseFloat(pay_fx) > parseFloat(sale_bal_fx)) {
                        // alert('aaa');
                        swal("Warning!", "Payment is greater than balance.", "warning");
                        document.getElementById('pay_amt_fx'+sale_id).value = '';
                        document.getElementById('pay_amt'+sale_id).value = '';

                    } else {
                        var prev_pay_fx = $('#prev_amt_fx'+sale_id).val();
                        if(prev_pay_fx == "") {
                            prev_pay_fx = 0;
                        }

                        var prev_pay = $('#prev_amt'+sale_id).val();
                        if(prev_pay == "") {
                            prev_pay = 0;
                        }
                        var balance_fx = parseFloat($('#inv_amt_fx'+sale_id).val()) - (parseFloat(prev_pay_fx) + parseFloat(pay_fx) + parseFloat(discount_fx));
                        balance_fx = balance_fx.toFixed(3);
                        $("#balance_fx"+sale_id).val(app.decimalFormat(balance_fx));

                        /***var balance = parseInt($('#inv_amt'+sale_id).val()) - (parseInt($('#prev_amt'+sale_id).val()) + parseInt($('#pay_amt'+sale_id).val()) + parseInt(discount));

                        $("#balance"+sale_id).val(balance);***/

                        var balance = (parseInt($('#inv_amt'+sale_id).val()) - (parseInt(prev_pay) + parseInt($('#pay_amt'+sale_id).val()) + parseFloat(discount))) + parseInt($('#gain_amt'+sale_id).val()) - parseInt($('#loss_amt'+sale_id).val());
                       // alert(parseInt($('#inv_amt'+sale_id).val())+'a'+parseInt(prev_pay)+'b'+parseInt($('#pay_amt'+sale_id).val())+'c'+parseFloat(discount));
                       // $("#balance"+sale_id).val(balance);

                       if(balance_fx == 0) {
                            $("#balance"+sale_id).val(app.decimalFormat(parseFloat(balance_fx) * parseFloat(currency_rate)));
                            
                       } else {
                            $("#balance"+sale_id).val(Math.round(balance));
                       }                       

                        app.calcTotalPay();
                    }
                }
            },
            calcTotalPay() {
                let app = this;
                app.total_pay = 0;
                app.total_pay_fx = 0;
                var total = 0;
                var total_fx = 0;
                $(".pay_amt:visible").each(function() {
                    if($(this).val() != "") {
                        total = parseInt(total) + parseInt($(this).val());
                    }
                });

                if(!app.isMMK) {
                    var currency_rate = app.form.currency_rate == '' ? 0 : app.form.currency_rate;
                    $(".pay_amt_fx:visible").each(function() {
                        var sale_id = $(this).attr('data-id');
                        var inv_currency_rate = $(this).attr('data-rate');
                        var payment = $(this).val() == '' ? 0 : $(this).val();
                        var disc_fx = $("#discount_amt_fx"+sale_id).val() == '' ? 0 : $("#discount_amt_fx"+sale_id).val();
                        if($(this).val() != "") {
                            total_fx = parseFloat(total_fx) + parseFloat($(this).val());
                        }                       
                        if(!app.isMMK) {
                            //calculate gain or loss
                            //var bal = (parseFloat(inv_currency_rate) - parseFloat(currency_rate)) * (parseFloat(payment) + parseFloat(disc_fx));

                            var bal = (parseFloat(inv_currency_rate) - parseFloat(currency_rate)) * (parseFloat(payment) + parseFloat(disc_fx));

                            if(currency_rate != '' && currency_rate != 0) {
                                if(parseFloat(inv_currency_rate) > parseFloat(currency_rate)) {
                                    $("#loss"+sale_id).val(Math.round(bal) * -1);
                                    $("#gain"+sale_id).val('0'); 
                                    /**$("#gain"+sale_id).val(Math.round(bal));
                                    $("#loss"+sale_id).val('0'); **/ 
                                } else {
                                    /**$("#loss"+sale_id).val(Math.round(bal));
                                    $("#gain"+sale_id).val('0');  **/
                                    $("#gain"+sale_id).val(Math.round(bal) * -1);
                                    $("#loss"+sale_id).val('0');
                                }
                            } else {
                                $("#loss"+sale_id).val('0');
                                $("#gain"+sale_id).val('0'); 
                            }
                        }
                    });
                }

                app.total_pay = total;
                app.total_pay_fx = total_fx;            

            },

            OldcalcTotalPay() {
                let app = this;
                app.total_pay = 0;
                var total = 0;
                $(".pay_amt:visible").each(function() {
                    if($(this).val() != "")
                    //total = parseInt(totadiscount_amtl) + parseInt($(this).val());
                    total = parseInt(total) + parseInt($(this).val());
                });

                app.total_pay = total;
            },

            getCollection(id) {
              let app = this;
              axios
                .get("/collection/" + id)
                .then(function(response) {

                    //for save button permission
                    if(app.user_role == "admin" || app.user_role == "system" || app.user_role == "office_user") {
                        app.isDisabled = false;
                    } else {
                        app.isDisabled = true;
                    }
                    
                    //app.sale_invoices = response.data.cus_invoices;
                    app.form.collection_no      = response.data.collection.collection_no;
                    app.form.collection_date    = response.data.collection.collection_date;
                    app.form.customer_id        = response.data.collection.customer_id;
                    $('#customer_id').val(app.form.customer_id).trigger('change');

                    app.form.account_group = response.data.collection.account_group_id;             
                    if(response.data.collection.account_group_id != '' && response.data.collection.account_group_id != null) {
                        axios.get('/sub_account/get_account_group/'+response.data.collection.account_group_id).then(({data})=>(app.cash_bank_accounts=data.sub_accounts));
                    }
                    app.form.cash_bank_account = response.data.collection.sub_account_id;

                    if(response.data.collection.branch != null) {
                        app.form.branch_id = response.data.collection.branch.id;
                    } else {
                        app.form.branch_id = '';
                    }
                    app.form.collect_type = response.data.collection.collect_type;
                    $('#collect_type_id').val(app.form.collect_type).trigger('change');
                    
                    app.total_pay = app.decimalFormat(response.data.collection.total_paid_amount);

                    app.form.currency_id = response.data.collection.currency_id;
                    $('#currency_id').val(app.form.currency_id).trigger('change');

                    if(response.data.collection.auto_payment == 1) {
                        app.form.is_auto = true;
                        $("#is_auto").prop("checked", true);
                        if(app.form.currency_id == 1) {
                            app.form.pay_amount     = app.decimalFormat(response.data.collection.total_paid_amount);
                        } else {
                            app.form.pay_amount     = app.decimalFormat(response.data.collection.total_paid_amount_fx);
                            //alert(response.data.collection.total_paid_amount_fx);
                        }

                    } else {
                        app.form.is_auto = false;
                        $("#is_auto").prop("checked", false);
                    }
                    
                    if(app.form.currency_id != 1) {
                        app.form.currency_rate = response.data.collection.currency_rate;
                        app.isMMK = false;
                        app.sign = response.data.collection.currency.sign;
                        app.total_colspan = 8;
                    } else {
                        app.isMMK = true;
                        app.total_colspan = 6;
                    }
                    
                    if(response.data.collection.currency_id != 1) {
                        app.total_pay_fx = app.decimalFormat(response.data.collection.total_paid_amount_fx);
                    }

                    var s2 = $(".invoices").select2({
                        tags: true
                    });
                     var sales_arr = [];
                     $.each(response.data.collection.sales, function( key, value ) {
                        sales_arr.push(value.id);
                     });
                    var index = '';
                    var html = "";
                    $.each(response.data.cus_invoices, function( key, value ) {
                        index = sales_arr.indexOf(value.id);
                        if (index > -1) {
                          app.selected_invoices.push(String(value.id));
                          html += '<tr id="'+value.id+'" data-pivotid = "'+response.data.collection.sales[index].pivot.id+'">';
                        } else {
                          html += '<tr id="'+value.id+'" style="display:none;" data-pivotid="0">';
                        }
                        //invoice lists
                        html += '<td class="text-right"></td><td>'+value.invoice_date+'</td><td>'+value.invoice_no+'</td>';
                        // html += '<td class="text-right"></td>';
                        if(value.sale_man != null) {
                         html += '<td>'+value.sale_man.sale_man+'</td>';
                        } else {
                         html += '<td></td>';
                        }
                        //html += '<td>'+value.invoice_no+'</td>';

                       /** if(value.sale_man != null) {
                         html += '<td><input type="text" id="sale_man'+value.id+'" class="form-control num_txt sale_man" readonly value="'+value.sale_man.sale_man+'" /></td>';
                        } else {
                         html += '<td></td>';
                        }**/
                        var invAmt = "";
                        var invAmt_fx = "";
                        if(!app.isMMK) {
                            var taxAmt_fx = value.tax_amount_fx == null ? 0 : value.tax_amount_fx;
                            invAmt_fx = parseFloat(value.net_total_fx) + parseFloat(taxAmt_fx);
                            html += '<td><input type="text" id="inv_amt_fx'+value.id+'" class="form-control decimal inv_amt_fx" readonly value="'+invAmt_fx+'" /></td>'
                        }

                        if(value.is_opening == 1) {
                            html += '<td><input type="text" id="inv_amt'+value.id+'" class="form-control num_txt inv_amt" readonly value="'+value.total_amount+'" /></td>';
                        } else {
                            var taxAmt = value.tax_amount == null ? 0 : value.tax_amount;
                            invAmt = parseInt(value.net_total) + parseInt(taxAmt);
                            html += '<td><input type="text" id="inv_amt'+value.id+'" class="form-control num_txt inv_amt" readonly value="'+invAmt+'" /></td>';
                        }

                        console.log(response.data.collection.sales);
                        var key = response.data.collection.sales.findIndex(x => x.id == value.id);
                        if(key > -1) {
                            var paid = parseInt(response.data.collection.sales[key].pivot.paid_amount);
                            if(response.data.collection.sales[key].pivot.discount != null) {
                                var discount = parseInt(response.data.collection.sales[key].pivot.discount);
                            } else {
                                var discount = 0;
                            }
                            var paid_fx = parseFloat(response.data.collection.sales[key].pivot.paid_amount_fx);
                            var discount_fx = parseFloat(response.data.collection.sales[key].pivot.discount_fx);

                            var gain = response.data.collection.sales[key].pivot.gain_amount;
                            var loss = response.data.collection.sales[key].pivot.loss_amount;

                            
                        } else {
                            var paid = 0;
                            var discount = 0;

                            var paid_fx = 0;
                            var discount_fx = 0;

                            var gain = 0;
                            var loss = 0;
                        }

                        var prev_pay = (parseInt(value.pay_amount)+parseInt(value.collection_amount)+parseInt(value.return_amount)+parseInt(value.cash_return_amount)+parseInt(value.customer_return_amount)) - (parseInt(paid) + parseInt(discount));

                        var prev_pay_fx = (parseFloat(value.pay_amount_fx)+parseFloat(value.collection_amount_fx)) - (parseFloat(paid_fx) + parseFloat(discount_fx));
                        prev_pay_fx = prev_pay_fx.toFixed(3);
                        var taxAmt = value.tax_amount == null ? 0 : value.tax_amount;
                        var taxAmt_fx = value.tax_amount_fx == null ? 0 : value.tax_amount_fx;
                        var bal_fx = (((parseFloat(value.net_total_fx) + parseFloat(taxAmt_fx)) - parseFloat(prev_pay_fx)).toFixed(3)) - ((parseFloat(paid_fx) + parseFloat(discount_fx)).toFixed(3));
                        console.log(value.net_amount_fx+'a'+prev_pay_fx+'b');
                        var $netTotal = value.is_opening == 1 ? value.total_amount : value.net_total;
                        
                        //var bal = ((parseInt($netTotal) - parseInt(prev_pay)) - (parseInt(paid) + parseInt(discount)))  + parseInt(response.data.collection.sales[key].gain_amount) - parseInt(response.data.collection.sales[key].loss_amount);

                        var bal = (((parseInt($netTotal) + parseInt(taxAmt)) - parseInt(prev_pay)) - (parseInt(paid) + parseInt(discount)))  + parseInt(value.gain_amount) - parseInt(value.loss_amount);

                       //alert(gain+'a'+loss);
                        if(!app.isMMK) {
                            html += '<td><input type="text" id="prev_amt_fx'+value.id+'" class="form-control decimal_no prev_amt_fx" readonly value="'+prev_pay_fx+'" /></td>';
                        }

                        html += '<td><input type="text" id="prev_amt'+value.id+'" class="form-control num_txt prev_amt" readonly value="'+prev_pay+'" /></td>';

                        if(response.data.collection.auto_payment == 1) {
                            if(!app.isMMK) {
                                html += '<td><input type="text" id="pay_amt_fx'+value.id+'" class="form-control decimal_no pay_amt_fx pay-change" data-rate="'+value.currency_rate+'" pay-change" readonly value="'+paid_fx+'" data-id= "'+value.id+'" /></td>';
                            }

                            html += '<td><input type="text" id="pay_amt'+value.id+'" class="form-control num_txt pay_amt pay-change" readonly value="'+paid+'" data-id= "'+value.id+'" /><input type="hidden" id="gain_amt'+value.id+'"  value="'+value.gain_amount+'" /><input type="hidden" id="loss_amt'+value.id+'"  value="'+value.loss_amount+'" /></td>';
                        } else {
                            if(!app.isMMK) {
                                html += '<td><input type="text" id="pay_amt_fx'+value.id+'" class="form-control decimal_no pay_amt_fx pay-change" data-rate="'+value.currency_rate+'" value="'+paid_fx+'" data-id= "'+value.id+'"  required /></td>';

                                html += '<td><input type="text" id="pay_amt'+value.id+'" class="form-control decimal_no pay_amt pay-change" value="'+paid+'" data-id= "'+value.id+'"  readonly required /><input type="hidden" id="gain_amt'+value.id+'"  value="'+value.gain_amount+'" /><input type="hidden" id="loss_amt'+value.id+'"  value="'+value.loss_amount+'" /></td>';

                            } else {
                                html += '<td><input type="text" id="pay_amt'+value.id+'" class="form-control decimal_no pay_amt pay-change" value="'+paid+'" data-id= "'+value.id+'" required /><input type="hidden" id="gain_amt'+value.id+'"  value="'+value.gain_amount+'" /><input type="hidden" id="loss_amt'+value.id+'"  value="'+value.loss_amount+'" /></td>';    
                            }
                           /** html += '<td><input type="text" id="pay_amt'+value.id+'" class="form-control num_txt pay_amt pay-change" value="'+paid+'" data-id= "'+value.id+'" required /></td>';**/
                        }

                        if(!app.isMMK) {
                            html += '<td><input type="text" id="discount_amt_fx'+value.id+'" class="form-control decimal_no discount_amt_fx discount-change-fx" value="'+discount_fx+'" data-id= "'+value.id+'" /></td>';

                            html += '<td><input type="text" id="discount_amt'+value.id+'" class="form-control decimal_no discount_amt discount-change" value="'+discount+'" data-id= "'+value.id+'" readonly /></td>';

                            html += '<td><input type="text" id="balance_fx'+value.id+'" class="form-control decimal_no balance_amt_fx" value="'+bal_fx+'" data-id= "'+value.id+'" readonly /></td>';
                            //var balMMK = parseFloat(bal_fx) * parseFloat(app.form.currency_rate);
                            html += '<td><input type="text" id="balance'+value.id+'" class="form-control decimal_no balance_amt" value="'+app.decimalFormat(bal)+'" data-id= "'+value.id+'" readonly /></td>';
                        }
                        else {

                            html += '<td><input type="text" id="discount_amt'+value.id+'" class="form-control decimal_no discount_amt discount-change" value="'+discount+'" data-id= "'+value.id+'" /></td>';

                            html += '<td><input type="text" id="balance'+value.id+'" class="form-control decimal_no balance_amt" value="'+bal+'" data-id= "'+value.id+'" readonly /></td>';
                        }

                        if(!app.isMMK) {
                            html+='<td><input type="text" id="gain'+value.id+'"  class="form-control decimal_no gain_amt"  data-id="'+value.id+'" value="'+app.decimalFormat(gain)+'" readonly /></td>';

                            html+='<td><input type="text" id="loss'+value.id+'"  class="form-control decimal_no loss_amt"  data-id="'+value.id+'" value="'+app.decimalFormat(loss)+'" readonly /></td>';
                        }

                       /*** html += '<td><input type="text" id="discount_amt'+value.id+'" class="form-control num_txt discount_amt discount-change" value="'+discount+'" data-id= "'+value.id+'" /></td>';

                        html += '<td><input type="text" id="balance'+value.id+'" class="form-control num_txt balance_amt" value="'+bal+'" data-id= "'+value.id+'" readonly /></td>'; **/

                        html += '<td class="text-center">';
                        if(app.user_role != 'admin') {
                            html += '<a class="remove-row red-icon" title="Remove"><i class="fas fa-times-circle" style="font-size: 25px;"></i></a>';
                        }
                        html += '</td></tr>';

                        if(!s2.find('option[value="'+value.id+'"]').length) {
                        // console.log(s2.find('option[value="'+value.id+'"]').length);
                            s2.append($('<option value="'+value.id+'">').text(value.invoice_no));
                        }
                    });


                    s2.val(app.selected_invoices).trigger("change");
                    
                    $("#edit_invoices").html(html);

                    if(app.selected_invoices.length > 0) {
                        app.isRequired = false;
                    } else {
                        app.isRequired = true;
                    }

                })
                .catch(function(error) {
                  // handle error
                  console.log(error);
                })
                .then(function() {
                  // always executed
                });
            },

            onSubmit: function(event) {
                let app = this; 
                $("#loading").show(); 
                app.form.total_pay = app.total_pay;
                app.form.total_pay_fx = app.total_pay_fx;
                app.form.payments = [];
                app.form.discounts = [];
                app.form.payments_fx = [];
                app.form.discounts_fx = [];
                app.form.gain = [];
                app.form.loss = [];

                if (!this.isEdit) {
                    app.form.invoices = app.selected_invoices.filter((a, b) => app.selected_invoices.indexOf(a) === b);
                    for(var i=0; i<app.form.invoices.length; i++) {
                        app.form.payments.push($("#pay_amt"+app.form.invoices[i]).val());
                        app.form.discounts.push($("#discount_amt"+app.form.invoices[i]).val());
                        if(!app.isMMK) {
                            app.form.payments_fx.push($("#pay_amt_fx"+app.form.invoices[i]).val());
                            app.form.discounts_fx.push($("#discount_amt_fx"+app.form.invoices[i]).val());
                            app.form.gain.push($("#gain"+app.form.invoices[i]).val());
                            app.form.loss.push($("#loss"+app.form.invoices[i]).val());
                        }
                    }

                    this.form
                      .post("/collection")
                      .then(function(data) {
                        // console.log(data.data);
                        if(data.status == "success") {
                            $("#loading").hide(); 
                            swal({
                                title: "Success!",
                                text: 'Collection is saved.',
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
                } else {
                    //Edit entry details

                    app.form.total_pay = app.total_pay;
                    app.form.total_pay_fx = app.total_pay_fx;

                    app.form.invoices = app.selected_invoices.filter((a, b) => app.selected_invoices.indexOf(a) === b);
                    for(var i=0; i<app.form.invoices.length; i++) {
                        app.form.payments.push($("#pay_amt"+app.form.invoices[i]).val());
                        app.form.discounts.push($("#discount_amt"+app.form.invoices[i]).val());
                        if(!app.isMMK) {
                            app.form.payments_fx.push($("#pay_amt_fx"+app.form.invoices[i]).val());
                            app.form.discounts_fx.push($("#discount_amt_fx"+app.form.invoices[i]).val());
                            app.form.gain.push($("#gain"+app.form.invoices[i]).val());
                            app.form.loss.push($("#loss"+app.form.invoices[i]).val());
                        }
                    }

                    //console.log(app.form.invoices);

                    this.form
                      .patch("/collection/" + app.collection_id)
                      .then(function(data) {
                        if(data.status == "success") {
                            //reset form data
                            $('#loading').hide();
                            swal({
                                title: "Success!",
                                text: 'Collection is updated.',
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

            removeProduct(id) {
                swal({
                    title: "Are you sure?",
                    text: "Once deleted, you will not be able to recover!",
                    icon: "warning",
                    buttons: true,
                    dangerMode: true
                    }).then(willDelete => {
                    if (willDelete) {
                        $("#"+id).remove();    
                    } else {
                      //
                    }
                });
            },

        }
    }
</script>