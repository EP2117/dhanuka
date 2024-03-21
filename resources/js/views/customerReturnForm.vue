<template>
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="#!"><i class="feather icon-home"></i></a></li>
                <li class="breadcrumb-item"><a :href="site_path+'/'">Home</a></li>
                <li class="breadcrumb-item"><a :href="site_path+'/office'">Office Sale</a></li>
                <li class="breadcrumb-item"><router-link tag="span" :to="'/customer_return/'" class="font-weight-normal"><a href="#">Customer Return</a></router-link></li>
                <li class="breadcrumb-item active" aria-current="page">Customer Return Form</li>

            </ol>
        </nav>
        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h4 class="mb-0 text-gray-800">Customer Return Form</h4>
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
                                <label for="return_no">Customer Return No.</label>
                                <input type="text" class="form-control" id="return_no" name="return_no" v-model="form.return_no" readonly>
                            </div>
                            <div class="form-group col-md-4">
                                <label for="return_date">Date</label>
                                <input type="text" class="form-control datetimepicker" id="return_date" name="return_date"
                                v-model="form.return_date" required>
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
                                <label for="invoices">Invoice</label>
                                <select multiple class="form-control invoices"
                                    name="invoices[]" id="invoices" required style="width:100%"
                                >
                                    <option v-for="sale in sale_invoices" :value="sale.id" :data-balance="sale.balance_amount-(sale.collection_amount+sale.pay_amount+sale.return_amount+sale.customer_return_amount)">{{sale.invoice_no}}_{{(sale.balance_amount-(sale.collection_amount+sale.pay_amount+sale.return_amount+sale.customer_return_amount)).toLocaleString()}}</option>
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            <div class="form-group col-md-4">
                                <label for="pay_amount">Pay Amount</label>
                                <input type="text" class="form-control num_txt" id="pay_amount" name="pay_amount" v-model="form.pay_amount" required>
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

                        <div class="row">
                            <div class="col-md-12" v-if="!isEdit">
                                <input type="submit" class="btn btn-primary btn-sm" value="Save Entry" id="save_btn" :disabled = "isDisabled">
                            </div>

                            <div class="col-md-12" v-if="isEdit"> 
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
                customer_id: "",
                invoices: [],
                sale_bals: [],
                pay_amount: "",  
                account_group: "",
                cash_bank_account: '', 

              }),              
              isEdit: false,
              return_id: '',
              customers: [],
              sale_type: '',
              user_role: '',
              user_year: '',
              sale_invoices: [],
              selected_invoices: [],
              selected_balance: [],
              is_readonly: false,
              isDisabled: false,
              site_path: '',
              storage_path: '',
              returnReadonly: true,
              canEdit: false,
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
           app.initAccountGroup();

            app.initCustomers();

            $(".invoices").select2();

            $(".invoices").on("select2:select", function(e) {
                var data = e.params.data;
                app.selected_invoices.push(data.id); 

                var unique_invoices = app.selected_invoices.filter((a, b) => app.selected_invoices.indexOf(a) === b);
                // console.log(unique_invoices);
                app.selected_invoices = unique_invoices;

                $('.invoices').val(app.selected_invoices).trigger('change');
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
            });

            $(".txt_product").select2();

            $("#customer_id").select2();
            $("#customer_id").on("select2:select", function(e) {

                var data = e.params.data;
                app.form.customer_id = data.id;

                //get customer's previous balance
                /***axios.get("/customer_credit_sale/"+data.id).then(({ data }) => (app.form.previous_balance = data.previous_balance)); ***/
                if(data.id != '') {
                    axios.get("/customer_credit_sale/"+data.id+"?sale_return=true").then(function(response) {
                        app.sale_invoices = response.data.data;
                        //app.form.outstanding_amount = response.data.previous_balance;
                        $(".invoices").select2();

                    });
                }
            });

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

            initCustomers() {
              axios.get("/customers").then(({ data }) => (this.customers = data.data));
              $("#customer_id").select2();
            },

            getReturn(id) {
              let app = this;
              $("#loading").show();
              axios
                .get("/customer_return/" + id)
                .then(function(response) {
                    app.form.return_no = response.data.data.customer_return_no; 
                    app.form.return_date = response.data.data.return_date;
                    app.form.customer_id = response.data.data.customer_id;
                    app.form.pay_amount = response.data.data.return_amount;

                    app.form.account_group = response.data.data.account_group_id;             
                    if(response.data.data.account_group_id != '' && response.data.data.account_group_id != null) {
                        axios.get('/sub_account/get_account_group/'+response.data.data.account_group_id).then(({data})=>(app.cash_bank_accounts=data.sub_accounts));
                    }
                    app.form.cash_bank_account = response.data.data.sub_account_id;

                    $('#customer_id').val(response.data.data.customer_id).trigger('change');

                    var s2 = $(".invoices").select2({
                        tags: true
                    });
                     var sales_arr = [];
                     var sr_amt_arr = [];
                     $.each(response.data.data.sales, function( key, value ) {
                        sales_arr.push(value.id);
                        sr_amt_arr.push(value.pivot.return_amount);
                     });
                    var index = '';
                    var html = "";
                    console.log(response.data.cus_invoices);
                    $.each(response.data.cus_invoices, function( key, value ) {
                        index = sales_arr.indexOf(value.id);                        
                        if (index > -1) {
                          app.selected_invoices.push(String(value.id));
                          var bal_amount = parseInt(value.balance) + parseInt(sr_amt_arr[index]);             
                        } else {
                            var bal_amount = value.balance;
                        }

                        if(!s2.find('option[value="'+value.id+'"]').length) {
                        // console.log(s2.find('option[value="'+value.id+'"]').length);
                            s2.append($('<option data-balance="'+bal_amount+'" value="'+value.id+'">').text(value.invoice_no+'_'+bal_amount));
                        }
                    });


                    s2.val(app.selected_invoices).trigger("change");

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


            onSubmit: function(event){
                let app = this;
                var pay_amt = $("#pay_amount").val();
                var bal_amt = 0;
                var pay_amt_bal  = pay_amt; 
                var valid = true;
                app.form.invoices = [];
                app.form.sale_bals = [];
                $('#invoices :selected').each(function() { 
                    if(pay_amt_bal <= 0) {                        
                        valid = false;
                    }                 
                    pay_amt_bal = parseInt(pay_amt_bal) - parseInt($(this).attr('data-balance'));
                    bal_amt += parseInt($(this).attr('data-balance'));                    
                    app.form.invoices.push($(this).val());
                    app.form.sale_bals.push($(this).attr('data-balance'));
                   
                });
                //app.form.invoices = app.form.invoices.reverse();
                //alert(pay_amt_bal);
                //alert(valid);
                if(pay_amt > bal_amt || valid == false) {
                    if(valid == false) {
                        swal("Warning!", "Pay amount is not enough to return total balance amount!", "warning");
                        return false;
                    } else {
                        swal("Warning!", "Pay amount is more than total balance amount!", "warning");
                         return false;
                    }                    
                    
                } else {
                    $("#loading").show();
                }

                if (!this.isEdit) {

                    this.form
                      .post("/customer_return")
                      .then(function(data) {
                        console.log(data.data);
                        if(data.status == "success") {
                            $('#loading').hide();
                            swal({
                                title: "Success!",
                                text: 'Customer Return is saved.',
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

                      //swal("Warning!", error, "warning");                        

                    });
                } else {
                    //Edit entry details
                    

                        this.form
                          .patch("/customer_return/" + app.return_id)
                          .then(function(data) {
                            if(data.status == "success") {

                                //reset form data
                                event.target.reset();
                                $(".txt_product").select2();
                                $('#loading').hide();

                                swal({
                                    title: "Success!",
                                    text: 'Customer Return is updated.',
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
                            //swal("Warning!", error, "warning");

                        });
                    //}
                }
            },

        }
    }
</script>