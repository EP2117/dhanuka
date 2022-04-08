<template>
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="#!"><i class="feather icon-home"></i></a></li>
                <li class="breadcrumb-item"><a :href="site_path+'/'">Home</a></li>
                <li class="breadcrumb-item"><a :href="site_path+'/office'">Office Sale</a></li>
                <li class="breadcrumb-item"><router-link tag="span" to="/collection" class="font-weight-normal"><a href="#">Return Payment</a></router-link></li>
                <li class="breadcrumb-item active" aria-current="page">Return Payment Form</li>

            </ol>
        </nav>
        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h4 class="mb-0 text-gray-800">Return Payment Form</h4>
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
                                <label>Return Payment No.</label>
                                <input type="text" class="form-control" id="payment_no" name="payment_no" v-model="form.payment_no" readonly>
                            </div>
                            <div class="form-group col-md-4">
                                <label for="paymentdate">Date</label>
                                <input type="text" class="form-control datetimepicker" id="payment_date" name="payment_date"
                                v-model="form.payment_date" required>
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
                                    name="branch_id" v-model="form.branch_id" style="width:100%"  required
                                >
                                    <option value="">Select One</option>
                                    <option v-for="branch in branches" :value="branch.id"  >{{branch.branch_name}}</option>
                                </select>
                            </div>

                            <div class="form-group col-md-4">
                                <label for="customer_id">Customer</label>
                                <select id="customer_id" class="form-control"
                                    name="customer_id" v-model="form.customer_id" style="width:100%" required
                                >
                                    <option value="">Select One</option>
                                    <option v-for="cus in customers" :value="cus.id"  >{{cus.cus_name}}</option>
                                </select>
                            </div>
                        </div>

                        <div class="row mt-3">
                            
                            <div class="form-group col-md-4">
                                <label for="return_id">Return Invoice</label>
                                <select class="form-control sale_returns" id="return_id"
                                    name="return_id" v-model="form.return_id" required style="width:100%"
                                >
                                    <option v-for="sr in sale_returns" :value="sr.id">{{sr.return_no}}</option>
                                </select>
                            </div>
                            <div class="form-group col-md-4">
                                <label for="payment_type">Payment Type</label>
                                <select id="payment_type" class="form-control mm-txt"
                                    name="payment_type" v-model="form.payment_type" style="width:100%" required
                                >
                                    <option value="">Select One</option>
                                    <option value="cash">Cash</option>
                                    <option value="bank">Bank</option>
                                </select>
                            </div>
                        </div>

                        <div class="row mt-4">
                            <div class="col-md-12">
                                <span class="d-none d-sm-inline-block btn-sm btn-primary shadow-sm bg-blue"><i class="fas fa-list text-white"></i> Return Invoice Details</span>
                            </div>
                        </div>

                        <div class="row mt-4">
                            <div class="col-md-12 table-responsive">
                                <table class="table table-bordered " id="product_table">
                                    <thead class="thead-grey">
                                        <tr>
                                            <th scope="col" >Return No.</th>
                                            <th scope="col" >Return Date</th>
                                            <th scope="col" >Return Amount</th>
                                            <th scope="col" >Previous Return Amount</th>
                                            <th scope="col" >Return Amount</th>
                                            <th scope="col" >Return Balance Amount</th>
                                        </tr>
                                        <template v-for="sreturn in sale_returns" id="payment_div">
                                        <tr :id="'r'+sreturn.id" class="sr_detail" style="display:none;" data-pivotid="0">
                                            <td>{{sreturn.return_no}}</td>
                                            <td>{{sreturn.return_date}}</td>
                                            <td>
                                                <input type="text" :id="'total_return_amt'+sreturn.id" class="form-control num_txt total_return_amt" readonly :value="sreturn.total_amount" />
                                            </td>
                                            <td>
                                                <input type="text" :id="'prev_amt'+sreturn.id" class="form-control num_txt prev_amt" readonly :value="sreturn.total_payment_amount" />
                                            </td>
                                            <td>
                                                <input type="text" :id="'return_amt'+sreturn.id" class="form-control num_txt" @blur="calcBalance(sreturn.id)" />
                                            </td>
                                            <td>
                                                <input type="text" :id="'balance_amt'+sreturn.id" class="form-control num_txt balance_amt" :value="parseInt(sreturn.total_amount) - parseInt(sreturn.total_payment_amount)"  :data-id="sreturn.id" readonly />
                                            </td>
                                        </tr>
                                        </template>
                                    </thead>
                                    <tbody id="payment_edit_div">
                                    </tbody>
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
                payment_no: "",
                payment_date: "",
                customer_id: "",
                return_id: "", 
                return_amount: '', 
                payment_type:'', 
                branch_id: '',           
              }),
              isEdit: false,
              isReadonly: true,
              isRequired: true,
              customers: [],
              sale_returns: [],
              user_role: '',
              user_year: '',
              payment_id: '',
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
            /**if(this.user_role != "admin" && this.user_role != "system" && this.user_role != "office_user")
            {
                var url =  window.location.origin;
                window.location.replace(url);
            }**/

            if(this.$route.params.id) {
                this.isEdit = true;
                this.payment_id = this.$route.params.id;
                this.getPayment(this.payment_id);
            }
        },
        mounted() {
            $("#loading").hide();
            let app = this;
            app.initCustomers();
            app.initBranches();
            $("#branch_id").on("select2:select", function(e) {
                var data = e.params.data;
                app.form.branch_id = data.id;
                //app.sale_returns = [];
               // $('#return_id').val(app.form.return_id).trigger('change');
                /***if(app.form.customer_id != '') {
                    app.sale_returns = [];
                    var search ="&branch_id=" + app.form.branch_id;
                    axios.get("/customer_sale_return/"+app.form.customer_id).then(({ data }) => (app.sale_returns = data.data));
                    $("#return_id").select2();
                }***/
            });
            $("#customer_id").select2();
            $("#customer_id").on("select2:select", function(e) {
                var data = e.params.data;
                app.form.customer_id = data.id;

                app.sale_returns = [];
                // $('#return_id').val(app.form.return_id).trigger('change');
                var search ="&branch_id=" + app.form.branch_id;
                axios.get("/customer_sale_return/"+data.id).then(({ data }) => (app.sale_returns = data.data));
                $("#return_id").select2();
            });
            $("#return_id").select2();
            $("#return_id").on("select2:select", function(e) {
                var data = e.params.data;
                app.form.return_id = data.id;
                if(data.id != '') {
                    $(".sr_detail").hide();
                    $("#r"+data.id).show();
                    $("#return_amt"+data.id).val('');
                }
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
                    app.form.payment_date = moment().format('YYYY-MM-DD');
                    var y = new Date().getFullYear();
                    if(app.user_year < y) { 
                      if(app.form.payment_date == app.user_year+"-12-31" ||  app.form.payment_date == '') {
                         app.form.payment_date = app.user_year+"-12-31";
                      }
                    }
                })
                .on("dp.change", function(e) {
                    var formatedValue = e.date.format("YYYY-MM-DD");
                    //console.log(formatedValue);
                    app.form.payment_date = formatedValue;
                });

            $(document).on('blur',  '.return_amt',function () {
                var return_id =app.form.return_id;
                app.calcBalance(return_id);
            });   
        },

        methods: {
            sale_invoice_date(date){
               return moment(date).format('DD/MM/YY');
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

            calcBalance(rid) {

                if($("#return_amt"+rid).val() != "") {

                    if($("#return_amt"+rid).val() > (parseInt($("#total_return_amt"+rid).val()) - parseInt($("#prev_amt"+rid).val()))) {
                        swal("Warning!", "Your return amount is more than balance amount.", "warning");
                        $("#return_amt"+rid).val('');
                        $("#balance_amt"+rid).val(parseInt($("#total_return_amt"+rid).val()) - parseInt($("#prev_amt"+rid).val()));
                    } else {
                        var bal = parseInt($("#total_return_amt"+rid).val()) - ( parseInt($("#prev_amt"+rid).val()) +  parseInt($("#return_amt"+rid).val()));
                        $("#balance_amt"+rid).val(bal);
                    }
                }               
            },

            getPayment(id) {
              let app = this;
              axios
                .get("/return_payment/" + id)
                .then(function(response) {
                    
                    //app.sale_invoices = response.data.cus_invoices;
                    app.form.payment_no      = response.data.payment.return_payment_no;
                    app.form.payment_date    = response.data.payment.return_payment_date;
                    app.form.customer_id        = response.data.payment.customer_id;
                    $('#customer_id').val(app.form.customer_id).trigger('change');
                    $("#customer_id").attr('disabled',true);
                    app.form.branch_id = response.data.payment.branch.id;
                    $('#branch_id').val(app.form.branch_id).trigger('change');

                    app.form.return_id = response.data.payment.return_id;

                    var newOption = new Option(response.data.payment.sale_return.return_no, response.data.payment.sale_return.id, true, true);
                    // Append it to the select
                    $('#return_id').append(newOption).trigger('change');
                    $("#return_id").attr('disabled',true);
                    
                    app.form.return_amount = response.data.payment.total_amount;
                    app.form.payment_type = response.data.payment.return_payment_type;

                    var html = '';
                    var value = response.data.payment.sale_return;

                    html += '<td>'+value.return_no+'</td><td>'+value.return_date+'</td>';

                    html += '<td><input type="text" id="total_return_amt'+app.form.return_id+'" class="form-control num_txt total_amt" readonly value="'+value.total_amount+'" /></td>';

                    var $prev_amt = parseInt(value.total_payment_amount) - parseInt(response.data.payment.total_amount);
                    html += '<td><input type="text" id="prev_amt'+app.form.return_id+'" class="form-control num_txt prev_amt" readonly value="'+$prev_amt+'" /></td>';

                    html += '<td><input type="text" id="return_amt'+app.form.return_id+'" class="form-control num_txt return_amt" value="'+response.data.payment.total_amount+'" /></td>';

                    var $bal_amt = parseInt(value.total_amount) - ($prev_amt + parseInt(response.data.payment.total_amount));
                    html += '<td><input type="text" id="balance_amt'+app.form.return_id+'" class="form-control num_txt balance_amt" readonly value="'+$bal_amt+'" /></td>';

                    html += '</tr>';
                    $("#payment_edit_div").html(html);
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
                var rid = app.form.return_id;
                 
                if($("#return_amt"+rid).val() == "") {
                    swal("Warning!", "Please add return amount.", "warning");
                    $("#return_amt"+rid).focus();
                    return false;
                }
                app.form.return_amount = $("#return_amt"+rid).val();
                $("#loading").show();
                if (!this.isEdit) {

                    this.form
                      .post("/return_payment")
                      .then(function(data) {
                        // console.log(data.data);
                        if(data.status == "success") {
                            $("#loading").hide(); 
                            swal({
                                title: "Success!",
                                text: 'Return Payment is saved.',
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

                    //console.log(app.form.invoices);

                    this.form
                      .patch("/return_payment/" + app.payment_id)
                      .then(function(data) {
                        if(data.status == "success") {
                            //reset form data
                            $('#loading').hide();
                            swal({
                                title: "Success!",
                                text: 'Return payment is updated.',
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