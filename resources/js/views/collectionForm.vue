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
                            <div class="form-group col-md-4">
                                <label for="collection_date">Date</label>
                                <input type="text" class="form-control datetimepicker" id="collection_date" name="collection_date"
                                v-model="form.collection_date" required>
                            </div>
                            
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
                                <label for="customer_id">Customer</label>
                                <select id="customer_id" class="form-control"
                                    name="customer_id" v-model="form.customer_id" style="width:100%" :disabled="cusReadonly" required
                                >
                                    <option value="">Select One</option>
                                    <option v-for="cus in customers" :value="cus.id"  >{{cus.cus_name}}</option>
                                </select>
                            </div>
                            <!-- <div class="form-group col-md-4">
                                <label for="collection_date">Date</label>
                                <input type="text" class="form-control datetimepicker" id="collection_date" name="collection_date"
                                v-model="form.collection_date" required>
                            </div> -->
                        </div>

                        <div class="row mt-3">
                            
                            <div class="form-group col-md-4">
                                <label for="invoices">Invoice</label>
                                <select multiple class="form-control invoices"
                                    name="invoices[]" v-model="form.invoices" :required="isRequired" style="width:100%"
                                >
                                    <option v-for="sale in sale_invoices" :value="sale.id">{{sale.invoice_no}}_{{sale.total_amount-(sale.discount+sale.collection_amount+sale.pay_amount)}}_{{sale.invoice_date}}</option>
                                </select>
                            </div>
                            <div class="form-group col-md-4">
                                <label for="branch_id">Collect Type</label>
                                <select id="collect_type_id" class="form-control mm-txt"
                                    name="collect_type" v-model="form.collect_type" style="width:100%" :disabled="cusReadonly" required
                                >
                                    <option value="">Select One</option>
                                    <option value="cash">Cash</option>
                                    <option value="bank">Bank</option>
                                    <!-- <option v-for="branch in branches" :value="branch.id"  >{{branch.branch_name}}</option> -->
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
                                <label for="pay_amount">Pay Amount</label>
                                <input type="text" class="form-control" id="pay_amount" name="pay_amount" v-model="form.pay_amount" @blur="calcAutoPay()" :readonly="!form.is_auto" :required="form.is_auto">
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
                                            <th scope="col" >Invoice Amount</th>
                                            <th scope="col" >Previous Paid Amount</th>
                                            <th scope="col" >Pay Amount</th>
                                            <th scope="col" >Discount</th>
                                            <th scope="col" >Balance</th>
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
                                                <input type="text" :id="'sale_man'+sale.id" class="form-control num_txt sale_man" :value="sale.sale_man.sale_man" :readonly="isReadonly" />
                                            </td>
                                            <td v-else>
                                            </td>
                                            <td>
                                                <input type="text" :id="'inv_amt'+sale.id" class="form-control num_txt inv_amt" readonly :value="sale.total_amount" />
                                            </td>
                                            <td>
                                                <input type="text" :id="'prev_amt'+sale.id" class="form-control num_txt prev_amt" readonly :value="parseInt(sale.pay_amount) + parseInt(sale.collection_amount)" />
                                            </td>
                                            
                                            <td>
                                                <input type="text" :id="'pay_amt'+sale.id" class="form-control num_txt pay_amt" :readonly="isReadonly" @blur="calcPay(sale.id)"/>
                                            </td>
                                            <td>
                                                <input type="text" :id="'discount_amt'+sale.id" class="form-control num_txt discount_amt" @blur="payDiscount(sale.id)" />
                                            </td>
                                            <td>
                                                <input type="text" :id="'balance'+sale.id" class="form-control num_txt balance_amt"  :data-id="sale.id" readonly />
                                            </td>
                                            <td class="text-center">
                                                <a class='remove-row red-icon' title='Remove' v-if="user_role != 'admin'"><i class='fas fa-times-circle' style='font-size: 25px;'></i></a>
                                            </td>
                                        </tr>

                                        </template>
                                        <tr>
                                            <td colspan='6' class="text-right"> Total Amount</td>
                                            <td>{{total_pay}}</td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                        </tr>
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
                                            <th scope="col" >Invoice Amount</th>
                                            <th scope="col" >Previous Paid Amount</th>
                                            <th scope="col" >Pay Amount</th>
                                            <th scope="col" >Discount</th>
                                            <th scope="col" >Balance</th>
                                            <th scope="col"></th>
                                        </tr>
                                    </thead>
                                    <tbody id="edit_invoices">

                                        
                                    </tbody>
                                    <tr>
                                            <td colspan='6' class="text-right"> Total Amount</td>
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
                discounts: [], 
                pay_amount: '', 
                collect_type:'',
                total_pay: '', 
                remove_pivot_id: [],  
                branch_id: '',           
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
              collection_id: '',
              cusReadonly: false,
              isDisabled: false,
              user_branch: '',
              branches: [],
              site_path: '',
              storage_path: '',
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
            if(this.user_role != "admin" && this.user_role != "system")
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

            $("#branch_id").on("select2:select", function(e) {
                app.selected_invoices = [];
                var data = e.params.data;
                app.form.branch_id = data.id;
                app.sale_invoices = [];
                $('.invoices').val(app.selected_invoices).trigger('change');
                if(app.form.customer_id != '') {
                    var search ="&branch_id=" + app.form.branch_id;
                    axios.get("/customer_credit_sale/"+app.form.customer_id+"?"+search).then(({ data }) => (app.sale_invoices = data.data));
                    $(".invoices").select2();
                }
            });

            $("#customer_id").select2();
            $("#customer_id").on("select2:select", function(e) {
                app.selected_invoices = [];
                var data = e.params.data;
                app.form.customer_id = data.id;

                app.sale_invoices = [];
                $('.invoices').val(app.selected_invoices).trigger('change');
                var search ="&branch_id=" + app.form.branch_id;
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
                    app.form.collection_date = moment().format('YYYY-MM-DD');
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
           
        },

        methods: {
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
                    if(app.isEdit) {
                        $('.pay_amt:visible').attr('readonly',true);
                    }
                } else {
                    $('.pay_amt:visible').attr('required',true);
                    app.form.pay_amount = "";
                    app.form.is_auto = false;
                    app.isReadonly = false;
                    if(app.isEdit) {
                        $('.pay_amt:visible').attr('readonly',false);
                        $('.pay_amt:visible').attr('required',true);
                    }
                }
            },

            calcAutoPay() {

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

            payDiscount(sale_id) {
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

            calcPay(sale_id) {

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

            calcTotalPay() {
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
                    if(app.user_role == "admin" || app.user_role == "system") {
                        app.isDisabled = false;
                    } else {
                        app.isDisabled = true;
                    }
                    
                    //app.sale_invoices = response.data.cus_invoices;
                    app.form.collection_no      = response.data.collection.collection_no;
                    app.form.collection_date    = response.data.collection.collection_date;
                    app.form.customer_id        = response.data.collection.customer_id;
                    $('#customer_id').val(app.form.customer_id).trigger('change');

                    if(response.data.collection.branch != null) {
                        app.form.branch_id = response.data.collection.branch.id;
                    } else {
                        app.form.branch_id = '';
                    }
                        app.form.collect_type = response.data.collection.collect_type;
                    $('#collect_type_id').val(app.form.collect_type).trigger('change');
                    
                    app.total_pay = response.data.collection.total_paid_amount;
                    if(response.data.collection.auto_payment == 1) {
                        app.form.is_auto = true;
                        $("#is_auto").prop("checked", true);
                        app.form.pay_amount     = response.data.collection.total_paid_amount;

                    } else {
                        app.form.is_auto = false;
                        $("#is_auto").prop("checked", false);
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
                        html += '<td>'+value.invoice_no+'</td>';

                        if(value.sale_man != null) {
                         html += '<td><input type="text" id="sale_man'+value.id+'" class="form-control num_txt sale_man" readonly value="'+value.sale_man.sale_man+'" /></td>';
                        } else {
                         html += '<td></td>';
                        }

                        html += '<td><input type="text" id="inv_amt'+value.id+'" class="form-control num_txt inv_amt" readonly value="'+value.total_amount+'" /></td>';

                        //console.log(response.data.collection.sales);
                        var key = response.data.collection.sales.findIndex(x => x.id == value.id);
                        if(key > -1) {
                            var paid = parseInt(response.data.collection.sales[key].pivot.paid_amount);
                            if(response.data.collection.sales[key].pivot.discount != null) {
                                var discount = parseInt(response.data.collection.sales[key].pivot.discount);
                            } else {
                                var discount = 0;
                            }
                            
                        } else {
                            var paid = 0;
                            var discount = 0;
                        }

                        var prev_pay = (parseInt(value.pay_amount)+parseInt(value.collection_amount)) - (parseInt(paid) + parseInt(discount));
                        var bal = (parseInt(value.total_amount) - parseInt(prev_pay)) - (parseInt(paid) + parseInt(discount));
                       
                        html += '<td><input type="text" id="prev_amt'+value.id+'" class="form-control num_txt prev_amt" readonly value="'+prev_pay+'" /></td>';

                        if(response.data.collection.auto_payment == 1) {
                            html += '<td><input type="text" id="pay_amt'+value.id+'" class="form-control num_txt pay_amt pay-change" readonly value="'+paid+'" data-id= "'+value.id+'" /></td>';
                        } else {
                            html += '<td><input type="text" id="pay_amt'+value.id+'" class="form-control num_txt pay_amt pay-change" value="'+paid+'" data-id= "'+value.id+'" required /></td>';
                        }

                        html += '<td><input type="text" id="discount_amt'+value.id+'" class="form-control num_txt discount_amt discount-change" value="'+discount+'" data-id= "'+value.id+'" /></td>';

                        html += '<td><input type="text" id="balance'+value.id+'" class="form-control num_txt balance_amt" value="'+bal+'" data-id= "'+value.id+'" readonly /></td>';

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
                if (!this.isEdit) {
                    app.form.invoices = app.selected_invoices.filter((a, b) => app.selected_invoices.indexOf(a) === b);
                    for(var i=0; i<app.form.invoices.length; i++) {
                        app.form.payments.push($("#pay_amt"+app.form.invoices[i]).val());
                        app.form.discounts.push($("#discount_amt"+app.form.invoices[i]).val());
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

                    app.form.invoices = app.selected_invoices.filter((a, b) => app.selected_invoices.indexOf(a) === b);
                    for(var i=0; i<app.form.invoices.length; i++) {
                        app.form.payments.push($("#pay_amt"+app.form.invoices[i]).val());
                        app.form.discounts.push($("#discount_amt"+app.form.invoices[i]).val());
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