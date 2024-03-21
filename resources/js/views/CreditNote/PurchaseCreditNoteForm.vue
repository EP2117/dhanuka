<template>
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="#!"><i class="feather icon-home"></i></a></li>
                <li class="breadcrumb-item"><a :href="site_path+'/'">Home</a></li>
                <li class="breadcrumb-item"><a :href="site_path+'/purchase_office'">Purchase</a></li>
                <li class="breadcrumb-item"><router-link tag="span" to="/purchase_credit_note" class="font-weight-normal"><a href="#">Debit Note</a></router-link></li>
                <li class="breadcrumb-item active" aria-current="page">Debit Note Form</li>

            </ol>
        </nav>
        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h4 class="mb-0 text-gray-800">Credit Note Form</h4>
        </div>

        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Entry Details</h6>
            </div>
            <div class="card-body">
                <div class="d-block">
                    <!-- form start -->
                    <form class="form" method="post" @submit.prevent="onSubmit" >
                        <div class="row">
                            <div class="form-group col-md-4">
                                <label for="transfer_no">Debit Note No</label>
                                <input type="text" class="form-control" id="credit_note_no" name="credit_note_no" v-model="form.credit_note_no" readonly>
                            </div>
                            <div class="form-group col-md-4">
                                <label for="credit_note_date">Debit Note Date</label>
                                <input type="text" class="form-control datetimepicker" 
                                id="credit_note_date" autocomplete="off"
                                v-model="form.credit_note_date" required>
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
                                    <option v-for="branch in branches" :value="branch.id"  :selected="branch.name==user_branch" >{{branch.branch_name}}</option>
                                </select>
                                
                            </div>
                             <div class="form-group col-md-4 ">
                                <label for="warehouse_id">Warehouse</label>
                                <select id="warehouse_id" class="form-control"
                                    name="warehouse_id" v-model="form.warehouse_id" style="width:100%" required
                                    :disabled="cusReadonly"
                                >
                                    <option value="">Select One</option>
                                    <option v-for="warehouse in warehouses" :value="warehouse.id"  >{{warehouse.warehouse_name}}</option>
                                </select>
                            </div>
                            
                          
                        </div>

                        <div class="row mt-3">
                            <div class="form-group col-md-4">
                                <label for="supplier_id">Supplier</label>
                                <select id="supplier_id" class="form-control"
                                    name="supplier_id" v-model="form.supplier_id" style="width:100%" :disabled="cusReadonly" required
                                >
                                    <option value="">Select One</option>
                                    <option v-for="sup in suppliers" :value="sup.id"  >{{sup.name}}</option>
                                </select>
                            </div>
                             <div class="form-group col-md-4">
                                <label for="branch_id">Debit Note Type</label>
                                <select id="credit_note_type_id" class="form-control mm-txt"
                                    name="credit_note_type_id" v-model="form.credit_note_type_id" style="width:100%"  required
                                    :disabled="cusReadonly"
                                                                    >
                                    <option value="">Select One</option>
                                    <option value="0">Cash Return</option>
                                    <option value="1">Returnable Stock</option>
                                    <option value="2">Price Difference</option>
                                    <!-- <option v-for="branch in branches" :value="branch.id"  >{{branch.branch_name}}</option> -->
                                </select>
                            </div>
                           
                            <!-- <div class="form-group col-md-4">
                                <label for="branch_id">Collect Type</label>
                                <select id="collect_type_id" class="form-control mm-txt"
                                    name="collect_type" v-model="form.collect_type" style="width:100%" :disabled="cusReadonly" required
                                >
                                    <option value="">Select One</option>
                                    <option value="cash">Cash</option>
                                    <option value="bank">Bank</option>
                                </select>
                            </div> -->
                        </div>

                        <div class="row mt-3" >
                             <div class="form-group col-md-4" >
                                <label for="invoices">Invoice</label>
                                <select class="form-control invoices"
                                id="invoices_id"
                                    name="invoices[]"  :required="isRequired" style="width:100%" :disabled="cusReadonly"
                                >
                                    <option value="">Select One</option>
                                    <option v-for="sale in purchase_invoices" :key="sale.id" :value="sale.id" :data-bal="(sale.total_amount - ((sale.discount == null ? 0 : sale.discount) + sale.collection_amount + sale.pay_amount+sale.credit_note_amount)) + cn_amount" :selected="(isEdit && sale.id==form.purchase_id)">{{sale.invoice_no}}_{{(sale.total_amount - ((sale.discount == null ? 0 : sale.discount) + sale.collection_amount + sale.pay_amount+sale.credit_note_amount)) + cn_amount}}_{{sale.invoice_date}}</option>
                                </select>
                            </div>
                            
                              <div class="form-group col-md-4" v-show="form.credit_note_type_id==2 && form.purchase_id!=''">
                                <label for="pay_amount"> Amount</label>
                                <input type="text" class="form-control" id="amount" name="amount" v-model="form.amount"  required>
                            </div>   

                          
                        </div>
                     
                        <div class="row mt-3" v-if="form.credit_note_type_id == 0">
                             <div class="form-group col-md-4">
                                <label for="">Payment Method</label>
                                <select class="form-control" id="account_group"
                                        v-model="form.account_group" style="width:100%" @change="changeAccountGroup()">
                                    <option value="">Select One</option>
                                    <option v-for="at in account_group" :value="at.id"  >{{at.name}}</option>
                                </select>
                            </div>
                            <div class="form-group col-md-4">
                                <label for="">&nbsp;</label>
                                <select class="form-control" id="cash_bank_account" 
                                        v-model="form.cash_bank_account" style="width:100%">
                                    <option value="">Select One</option>
                                    <option v-for="at in cash_bank_accounts" :value="at.id"  >{{at.sub_account_name}}</option>
                                </select>
                            </div>
                        </div>

                        <div class="row mt-4" v-show="form.credit_note_type_id!=2 ">
                            <div class="col-md-12">
                                <span class="d-none d-sm-inline-block btn-sm btn-primary shadow-sm bg-blue"><i class="fas fa-list text-white"></i> Invoice Details</span>
                            </div>
                        </div>

                        <div class="row mt-4" v-if="!isEdit" >
                            <div class="col-md-12 table-responsive" v-show="form.credit_note_type_id!=2">
                               <!-- <table class="table table-bordered" id="product_table">
                                    <thead class="thead-grey">
                                        <tr>
                                            <th scope="col" >Name</th>
                                            <th scope="col" >QTY</th>
                                            <th scope="col" >Selling Unit</th>
                                            <th scope="col" >Unit Price</th>
                                            <th scope="col" >Total Amount</th>
                                            <th scope="col" >Remark</th>
                                        </tr>
                                    </thead>
                                    <tbody>-->
                                        <template v-for="sale in purchase_invoices">
                                            <div :id="'sale'+sale.id" class='sale_product' style="display:none;">
                                            <table class="table table-bordered" id="product_table">
                                                <thead class="thead-grey">
                                                    <tr>
                                                        <!-- <th scope="col" >No.</th> -->
                                                        <th scope="col" >Name</th>
                                                        <th scope="col" >QTY</th>
                                                        <th scope="col" >Selling Unit</th>
                                                        <th scope="col" >Unit Price</th>
                                                        <th scope="col" >Total Amount</th>
                                                        <!-- <th scope="col" >Discount</th> -->
                                                        <th scope="col" >Remark</th>
                                                    </tr>
                                                </thead>
                                            <tbody>
                                            <template v-for="product in sale.products">
                                                <tr :id="product.pivot.id"  data-pivotid="0" >
                                                    <td>{{product.product_name}}</td>
                                                    <td>
                                                        <input type="text" :id="'qty'+product.pivot.id" class="form-control num_txt" required @blur="calTotalAmt(product.pivot.id)"  :value="Number.isInteger(parseFloat(product.pivot.product_quantity))== true ?  (parseInt(product.pivot.product_quantity)-parseInt(product.pivot.debit_note_quantity)) : (product.pivot.product_quantity-product.pivot.debit_note_quantity)" />
                                                    </td>
                                                     <!-- <td> -->
                                                        <input type="hidden" :id="'old_qty'+product.pivot.id" class="form-control num_txt old_qty"  :value="Number.isInteger(parseFloat(product.pivot.product_quantity))== true ?  (parseInt(product.pivot.product_quantity)-parseInt(product.pivot.debit_note_quantity)) : (product.pivot.product_quantity-product.pivot.debit_note_quantity)" />
                                                    <!-- </td> -->
                                                    <!--<td>
                                                        <input type="text" :id="'unit'+product.pivot.id" class="form-control num_txt unit" readonly :value="product.uom.uom_name" />
                                                    </td>-->
                                                    <td>
                                                        <select class="form-control txt_uom"
                                                            name="uom[]" :id="'uom'+product.pivot.id" style="min-width:150px;" readonly
                                                        >
                                                            <option :value="product.uom_id" v-if="product.uom_id == product.pivot.uom_id" selected >{{product.uom.uom_name}}</option>
                                                            <template v-for="suom in product.selling_uoms">
                                                            <option :value="suom.pivot.uom_id" v-if="suom.pivot.uom_id == product.pivot.uom_id" selected >{{suom.uom_name}}</option>
                                                            </template>
                                                        </select>
                                                    </td>
                                                    <td>
                                                        <input type="text" :id="'unit_price'+product.pivot.id" class="form-control num_txt unit_price" readonly :value="product.pivot.price"  />
                                                    </td>
                                                     <td>
                                                        <input type="text" :id="'total_amt'+product.pivot.id" class="form-control num_txt total_amt" readonly :value="((parseInt(product.pivot.product_quantity)-parseInt(product.pivot.debit_note_quantity))*product.pivot.price)" />
                                                    </td>
                                                     
                                                     <!-- <td>
                                                        <input type="text" :id="'discount_amt'+product.pivot.id" class="form-control num_txt discount_amt"
                                                        @blur="calDiscount(product.pivot.id)" 
                                                          />
                                                    </td> -->
                                                     <td>
                                                        <input type="text" :id="'remark'+product.pivot.id" class="form-control mm-text remark" />
                                                    </td>
                                                    <td class="text-center">
                                                        <a class='remove-row red-icon' :id='product.pivot.id' title='Remove' ><i class='fas fa-times-circle' style='font-size: 25px;'></i></a>
                                                    </td> 
                                                </tr>
                                                </template>

                                                 </tbody>
                                                </table>
                                             </div>

                                          </template>     
                                     
                            </div>                         

                        </div>


                        <div class="row mt-4" v-else >
                            <div class="col-md-12 table-responsive">
                                <table class="table table-bordered table_no_edit" id="product_table">
                                    <thead class="thead-grey">
                                        <tr>
                                             <th scope="col" >Name</th>
                                            <th scope="col" >QTY</th>
                                            <th scope="col" >Selling Unit</th>
                                            <th scope="col" >Unit Price</th>
                                            <th scope="col" >Total Amount</th>
                                            <!-- <th scope="col" >Discount</th> -->
                                            <th scope="col" >Remark</th>
                                        </tr>
                                    </thead>
                                    <tbody id="edit_invoices">

                                        <template v-for="product in cn_products">
                                        <tr :id="product.pivot.id"   :data-pivotid = "product.pivot.id" >
                                            <td>{{product.product_name}}</td>
                                            <td>
                                                <input type="hidden" :id="'ps_pivot'+product.pivot.id" :value="product.pivot.product_purchase_pivot_id" />
                                                <input type="text" :id="'qty'+product.pivot.id" class="form-control num_txt" required @blur="calTotalAmt(product.pivot.id)"  :value="product.pivot.qty" />
                                            </td>
                                            <td>
                                                <select class="form-control txt_uom"
                                                    name="uom[]" :id="'uom'+product.pivot.id" style="min-width:150px;" readonly
                                                >
                                                    <option :value="product.uom_id" v-if="product.uom_id == product.pivot.uom_id" selected >{{product.uom.uom_name}}</option>
                                                    <template v-for="suom in product.selling_uoms">
                                                    <option :value="suom.pivot.uom_id" v-if="suom.pivot.uom_id == product.pivot.uom_id" selected >{{suom.uom_name}}</option>
                                                    </template>
                                                </select>
                                            </td>
                                            <td>
                                                <input type="text" :id="'unit_price'+product.pivot.id" class="form-control num_txt unit_price" readonly :value="product.pivot.rate"  />
                                            </td>
                                             <td>
                                                <input type="text" :id="'total_amt'+product.pivot.id" class="form-control num_txt total_amt" readonly :value="product.pivot.total_amount" />
                                            </td>
                                             
                                             <!-- <td>
                                                <input type="text" :id="'discount_amt'+product.pivot.id" class="form-control num_txt discount_amt"
                                                @blur="calDiscount(product.pivot.id)" 
                                                  />
                                            </td> -->
                                             <td>
                                                <input type="text" :id="'remark'+product.pivot.id" class="form-control mm-text remark" :value="product.pivot.remark" />
                                            </td>
                                            <td class="text-center">
                                                <a class='remove-row red-icon' :id='product.pivot.id' title='Remove'><i class='fas fa-times-circle' style='font-size: 25px;'></i></a>
                                            </td> 
                                        </tr>
                                        </template>
                                    </tbody>
                                    <!-- <tr>
                                            <td colspan='6' class="text-right"> Total Amount</td>
                                            <td>{{total_pay}}</td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                        </tr> -->
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
                credit_note_no: "",
                credit_note_date: "",
                supplier_id: "",
                is_auto: true,
                products: [],
                product_purchase_ids: [],
                uoms:[],
                qty:[],
                old_qty:[],
                units:[],
                total_amounts:[],
                total_amount:'',
                payments: [],
                discounts: [], 
                remarks:[],
                pivots_id:[],
                amount:0,
                warehouse_id:'',
                branch_id: '',     
                credit_note_type_id:'',    

                pay_amount: '', 
                collect_type:'',
                purchase_id:'',
                total_pay: '', 
                remove_pivot_id: [], 
                account_group: "",
                cash_bank_account: '',
                 
              }),
              isEdit: false,
              isReadonly: true,
              isRequired: true,
              suppliers: [],
              invoice_balance: 0,
              invoice_total_amount: 0,
              selected_invoices:'',
              warehouses:[],
              purchase_invoices: [],
              invoices:[],
              cn_products: [],
              user_role: '',
              user_year: '',
              total_pay: 0,
              credit_note_id: '',
              cusReadonly: false,
              isDisabled: false,
              user_branch: '',
              branches: [],
              site_path: '',
              storage_path: '',
              cn_amount: 0,
              account_group: [],
              cash_bank_accounts: [],
              user_role_id: '',
            };
        },

        created() {

            this.user_role = document.querySelector("meta[name='user-role']").getAttribute('content');

            //this.user_branch = document.querySelector("meta[name='user-branch']").getAttribute('content');
              this.user_warehouse = document.querySelector("meta[name='user-wh']").getAttribute('content');
            this.user_branch = document.querySelector("meta[name='user-branch']").getAttribute('content');
            this.site_path = document.querySelector("meta[name='site-path']").getAttribute('content');
            //this.site_path = this.site_path.substring(this.site_path.lastIndexOf('/')+1);
            this.storage_path = document.querySelector("meta[name='storage-path']").getAttribute('content');

            //for save entry button
            /*if(this.user_role == "admin" && !this.isEdit) {
                this.isDisabled = true;
            }*/

            this.user_year = document.querySelector("meta[name='user-year-likelink']").getAttribute('content');

            this.user_role_id = document.querySelector("meta[name='user-role-id']").getAttribute('content');
            
            if(this.user_role != "admin" && this.user_role != "system")
            {
                var url =  window.location.origin;
                window.location.replace(url);
            }
            if(this.$route.params.id){
                this.isEdit = true;
                this.cusReadonly = true;
                this.credit_note_id = this.$route.params.id;                
                this.getCreditNote(this.credit_note_id);
              
            }
        },
        mounted() {
            if(!this.$route.params.id){
                $("#loading").hide();
            }
            let app = this;
            app.initAccountGroup();
            $("#credit_note_date")
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
            }).on("dp.show", function(e) {
                    app.form.credit_note_date= moment().format('YYYY-MM-DD');
                    var y = new Date().getFullYear();
                    if(app.user_year < y) { 
                      if(app.form.credit_note_date == app.user_year+"-12-31" ||  app.form.credit_note_date == '') {
                         app.form.credit_note_date = app.user_year+"-12-31";
                      }
                    }
                })
                .on("dp.change", function(e) {
                    var formatedValue = e.date.format("YYYY-MM-DD");
                    //console.log(formatedValue);
                    app.form.credit_note_date = formatedValue;
                });

            app.initSuppliers();
            app.initBranches();
            app.initWarehouses();

            $("#branch_id").on("select2:select", function(e) {
                app.selected_invoices = [];
                var data = e.params.data;
                app.form.branch_id = data.id;
                // app.purchase_invoices = [];
                // $('.invoices').val(app.selected_invoices).trigger('change');
                // if(app.form.supplier_id != '') {
                //     var search ="&branch_id=" + app.form.branch_id;
                //     axios.get("/customer_credit_sale/"+app.form.supplier_id+"?"+search).then(({ data }) => (app.purchase_invoices = data.data));
                //     $(".invoices").select2();
                // }
            });
            
            $("#supplier_id").select2();
            $("#credit_note_type_id").select2();
            $("#credit_note_type_id").on("select2:select", function(e) {
                app.selected_invoices = [];
                var data = e.params.data;
                app.form.credit_note_type_id = data.id;
                app.purchase_invoices=[];
                $('.invoices').val(app.selected_invoices).trigger('change');
                var search ="&branch_id=" + app.form.branch_id + "&supplier_id=" + app.form.supplier_id + "&warehouse_id=" + app.form.warehouse_id;
                // getInvoice(search,data.id);
                $("#loading").show();
                  //axios.get("/get_purchase_invoice_by_credit_note/"+data.id+"?"+search).then(({ data }) => (app.purchase_invoices = data.data));

                  axios.get("/get_purchase_invoice_by_credit_note/"+data.id+"?"+search).then(function(response) {
                    app.purchase_invoices = response.data.data;
                    $("#loading").hide();
                  });
                $(".invoices").select2();
             });
            $("#supplier_id").on("select2:select", function(e) {
                app.selected_invoices = [];
                var data = e.params.data;
                app.form.supplier_id = data.id;
                app.purchase_invoices = [];
                $('.invoices').val(app.selected_invoices).trigger('change');
                var search ="&branch_id=" + app.form.branch_id + "&supplier_id=" + data.id + "&warehouse_id=" + app.form.warehouse_id;
                // getInvoice(search,app.form.credit_note_type_id);
                if(app.form.credit_note_type_id!=''){  
                    $("#loading").show(); 
                     //axios.get("/get_purchase_invoice_by_credit_note/"+app.form.credit_note_type_id+"?"+search).then(({ data }) => (app.purchase_invoices = data.data));

                     axios.get("/get_purchase_invoice_by_credit_note/"+app.form.credit_note_type_id+"?"+search).then(function(response) {
                    app.purchase_invoices = response.data.data;
                    $("#loading").hide();
                  });
                    $(".invoices").select2();
                }
               
            });
            $("#warehouse_id").select2();

              $("#warehouse_id").on("select2:select", function(e) {
                // app.selected_invoices = [];
                var data = e.params.data;
                app.form.warehouse_id = data.id;

                // app.purchase_invoices = [];
                // $('.invoices').val(app.selected_invoices).trigger('change');
                // var search ="&branch_id=" + app.form.branch_id;
                // axios.get("/customer_credit_sale/"+data.id+"?"+search).then(({ data }) => (app.purchase_invoices = data.data));
                // $(".invoices").select2();
            });
            $(".invoices").select2();
            $(".invoices").on("select2:select", function(e) {
                var data = e.params.data;
                $(".sale_product").hide();
                app.invoices=app.purchase_invoices.filter(f=>f.id ==data.id);
                if(app.form.credit_note_type_id==2){
                    app.form.amount=app.invoices[0].total_amount-((app.invoices[0].discount == null ? 0 : app.invoices[0].discount) + app.invoices[0].collection_amount+app.invoices[0].pay_amount+app.invoices[0].credit_note_amount);
                }
                app.invoice_balance=app.invoices[0].total_amount-((app.invoices[0].discount == null ? 0 : app.invoices[0].discount) +app.invoices[0].collection_amount+app.invoices[0].pay_amount+app.invoices[0].credit_note_amount);
                app.invoices=app.invoices[0].products;
                if(app.form.credit_note_type_id!=2) {
                    app.invoice_total_amount = 0;
                    //calculate all total product

                    $.each(app.invoices, function( key, p ) {
                        var uid = p.pivot.id;
                        var qty = $("#qty"+uid).val() == '' ? 0 : $("#qty"+uid).val();
                        var uprice = $("#unit_price"+uid).val() == '' ? 0 : $("#unit_price"+uid).val();
                        app.invoice_total_amount += Math.round(parseFloat(qty) * parseFloat(uprice));
                       //app.invoice_total_amount += p.pivot.total_amount;
                    });
                }
                //alert(app.invoice_total_amount);
                app.isRequired = false;
                app.selected_invoices=data.id;
                app.form.purchase_id=data.id;
                $("#sale"+data.id).show();

                if($("#"+data.id).attr('data-pivotid') != 0) {
                    var pindex = app.form.remove_pivot_id.indexOf($("#"+data.id).attr('data-pivotid'));
                    console.log(pindex);
                    if (pindex > -1) {
                      app.form.remove_pivot_id.splice(pindex, 1);
                    }   
                }
            });
             $(document).on('click','.remove-row',function(evt) {

                var unselect_id =  $(this).attr('id');
                console.log(unselect_id);
                $(this).parents("tr").hide();

                if(app.isEdit) {
                    app.invoices = app.cn_products.filter(e=>e.pivot.id!=unselect_id);
                    app.invoice_total_amount = 0;
                    console.log('rmv');console.log(app.invoices);
                    $.each(app.invoices, function( key, value ) {
                        var uid = value.pivot.id;
                        var qty = $("#qty"+uid).val() == '' ? 0 : $("#qty"+uid).val();
                        var uprice = $("#unit_price"+uid).val() == '' ? 0 : $("#unit_price"+uid).val();
                        app.invoice_total_amount += Math.round(parseFloat(qty) * parseFloat(uprice));
                    });

                } else {
                    app.invoices = app.invoices.filter(e=>e.pivot.id!=unselect_id);
                    app.invoice_total_amount = 0;
                    console.log('rmv');console.log(app.invoices);
                    $.each(app.invoices, function( key, value ) {
                        var uid = value.pivot.id;
                        var qty = $("#qty"+uid).val() == '' ? 0 : $("#qty"+uid).val();
                        var uprice = $("#unit_price"+uid).val() == '' ? 0 : $("#unit_price"+uid).val();
                        app.invoice_total_amount += Math.round(parseFloat(qty) * parseFloat(uprice));
                    });
                }
            });
            // 


            

                $(document).on('blur', '.qty_change',function () {
                    //alert($(this).attr('data-id'));
                    console.log('q');
                    // var purchase_id = $(this).attr('data-id');
                    var pivot_id=$(this).attr('data-id');
                   // app.calTotalAmt(pivot_id);
                });

                // $(document).on('blur',  '.discount_change',function () {
                //     // var purchase_id = $(this).attr('data-id');
                //     var pivot_id=$(this).attr('data-id');
                //     // app.calDiscount(pivot_id);
                // });
              $(document).on('blur',  '#amount',function () {
                // var purchase_id = $(this).attr('data-id');
               /** var invoices= Array();
                console.log(invoices);
                 invoices=app.purchase_invoices.filter(f=>f.id ==app.form.purchase_id);
                const amt=invoices[0].total_amount-((invoices[0].discount == null ? 0 : invoices[0].discount) +invoices[0].collection_amount+invoices[0].pay_amount+invoices[0].credit_note_amount);
                var new_amt=$(this).val();
                var new_amt=$(this).val();
                $(this).val(amt);**/
                var amt = document.getElementById('invoices_id').options[document.getElementById('invoices_id').selectedIndex].dataset.bal;
                var new_amt=$(this).val();
                $(this).val(amt);
                if(amt<new_amt){
                    swal("Warning!", "Amount must be less than "+ amt, "warning");
                }                
                
            });
           
        },

        methods: {
            test(){
                alert('k');
            },

            initSuppliers() {
               axios.get("/supplier").then(({ data }) => (this.suppliers = data.data));
              $("#supplier_id").select2();
            },

            initAccountGroup(){
                axios.get('/sub_account/get_account_group').then(({data})=>(this.account_group=data.account_group));
                // $("#financial_type2_id").select2();
            },

            changeAccountGroup(id) {
                var ag_id = this.form.account_group;
                axios.get('/sub_account/get_account_group/'+ag_id).then(({data})=>(this.cash_bank_accounts=data.sub_accounts));
            },

            // initBranches() {
            //   axios.get("/branches_byuser").then(({ data }) => (this.branches = data.data));
            //   $("#branch_id").select2();
            // },
             initBranches() {
            let app = this;
               axios.get("/branches_byuser").then(
                  (data)=>{
                    app.branches = data.data.data;
                     const branch=app.branches.find(a=>a.branch_name==app.user_branch);
                      app.form.branch_id=branch.id;
                    //   app.def_branch_id=branch.id;
                      $('branch_id').val(branch.id).trigger('change');
                      $("#branch_id").select2();
                  });
            },
             getInvoice(search,credit_note_type_id){
                let app=this;
                // var search ="&branch_id=" + app.form.branch_id + "&supplier_id=" + data.id + "&warehouse_id=" + app.form.warehouse_id;
                if(credit_note_type_id!=''){
                      axios.get("/get_invoice_by_credit_note/"+credit_note_type_id+"?"+search).then(({ data }) => (app.purchase_invoices = data.data));
                    $(".invoices").select2();
                }
            },
            initWarehouses() {
                let app = this;
               axios.get("/warehouses").then(
                //   ({ data }) => (this.warehouses = data.data)
                (data)=>{
                    app.warehouses=data.data.data;
                     const id=app.warehouses.find(a=>a.branch.branch_name=='Main Branch');
                     app.form.warehouse_id=id.id;
                      $('#warehouse_id').val(id.id).trigger('change');
                      $("#warehouse_id").select2();
                }
                  );
            //   $("#warehouse_id").select2();
            },
            calTotalAmt(id){
                let app=this;
                if(app.$route.params.id) {
                    app.isEdit = true;
                }
                console.log('ee');
                console.log(app.invoices);
                var qty=$('#qty'+id).val();
                var price=$('#unit_price'+id).val();    
                // var discount=parseInt($('#discount_amt'+id).val());
                //    if(discount=='' || isNaN(discount)){ 
                //        discount=0;
                //    }
                  
                // var invoice=app.purchase_invoices.filter(e=>e.id==app.selected_invoices);
                if(app.isEdit==true){

                   // var product=app.invoices;
                    var product=app.invoices.filter(i=>i.pivot.id==id);
                    
                    console.log(product[0]);
                    /**var p_qty=Number.isInteger(parseFloat(product[0].pivot.old_qty))== true ?  parseInt(product[0].pivot.old_qty) : parseInt(product[0].pivot.old_qty);
                    var org_qty=Number.isInteger(parseFloat(product[0].pivot.qty))== true ?  parseInt(product[0].pivot.qty) : parseInt(product[0].pivot.qty); **/

                    var p_qty=Number.isInteger(parseFloat(product[0].pivot.product_quantity))== true ?  (parseInt(product[0].pivot.product_quantity)-parseInt(product[0].pivot.debit_note_quantity)) : product[0].pivot.product_quantity-product[0].pivot.debit_note_quantity;
                    p_qty = parseFloat(p_qty) + parseFloat(parseInt(product[0].pivot.qty));
                    var total_amt=(price*qty);
                        $('#total_amt'+id).val(total_amt);
                         if(qty==''){
                            $('#total_amt'+id).val(product[0].pivot.total_amount);
                        }
              

                       /**if ((p_qty+org_qty) < qty ){
                        $('#total_amt'+id).val(product[0].pivot.total_amount);
                        // this.calDiscount(id);
                        swal("Warning!", "Your quantity must be less than "+ p_qty, "warning");
                        $('#qty'+id).val(org_qty)

                       }**/

                       if( parseFloat(p_qty) < parseFloat(qty)){
                        $('#qty'+id).val(p_qty)
                        $('#total_amt'+id).val(product[0].pivot.total_amount);
                        // this.calDiscount(id);
                        swal("Warning!", "Your quantity must be less than "+ p_qty, "warning");
                       }

                   
                }else{
                     console.log('Not Edit ');
                    var product=app.invoices.filter(i=>i.pivot.id==id);

                    var p_qty=Number.isInteger(parseFloat(product[0].pivot.product_quantity))== true ?  (parseInt(product[0].pivot.product_quantity)-parseInt(product[0].pivot.debit_note_quantity)) : product[0].pivot.product_quantity-product[0].pivot.debit_note_quantity;
                      var total_amt=(price*qty);
                        $('#total_amt'+id).val(total_amt);
                         if(qty==''){
                            $('#total_amt'+id).val(product[0].pivot.total_amount);
                        }
                       if( p_qty < qty ){
                        $('#qty'+id).val(p_qty)
                        $('#total_amt'+id).val(product[0].pivot.total_amount);
                        // this.calDiscount(id);
                        swal("Warning!", "Your quantity must be less than "+ p_qty, "warning");
                       }
                    
                }

                
                app.invoice_total_amount = 0;
                $(".total_amt:visible").each(function() {
                    if($(this).val() != "")
                    app.invoice_total_amount += parseInt($(this).val());
                });
            },
            // calDiscount(id){
            // let app=this;
            // // var invoice=app.purchase_invoices.filter(e=>e.id==app.selected_invoices);
            // var product=app.invoices.filter(i=>i.pivot.id==id);
            //    var total_amt= product[0].pivot.total_amount;
            //    var rate= product[0].pivot.price;
            //    var discount=parseInt($('#discount_amt'+id).val());
            //    if(discount=='' || isNaN(discount)){
            //        discount=0;
            //    }
            //    var qty=$('#qty'+id).val();
            //    console.log('Qty is =' + qty);
            //    if(qty==''){
            //     $('#total_amt'+id).val(total_amt);
            //     $('#discount_amt'+id).val('')
            //    }
            //    var total=(qty*rate)-discount;
            //    if(parseInt(discount)>parseInt(total_amt)){
            //     swal("Warning!", "Your discount must be less than total amount", "warning");
            //     $('#discount_amt'+id).val('')
            //     $('#total_amt'+id).val(total_amt);
            //    }else{
            //     $('#total_amt'+id).val(total);
            //    }
            // },
            getCreditNoteOld(id) {
              let app = this;
              app.invoices = []; 

              axios
                .get("/purchase_credit_note/edit/" + id)
                .then(function(response) {
                    console.log(response.data.credit_note);
                    //for save button permission
                    if(app.user_role == "admin" || app.user_role == "system") {
                        app.isDisabled = false;
                    } else {
                        app.isDisabled = true;
                    }
                    console.log('jk');
                    app.invoices=response.data.credit_note.products;
                    console.log(app.invoices);
                    //app.purchase_invoices = response.data.cus_invoices;
                    app.form.credit_note_no      = response.data.credit_note.credit_note_no;
                    app.form.credit_note_date    = response.data.credit_note.credit_note_date;
                    app.form.supplier_id         = response.data.credit_note.supplier_id;
                    app.form.warehouse_id        = response.data.credit_note.warehouse_id;
                    app.form.credit_note_type_id = response.data.credit_note.credit_note_type_id;
                    app.form.purchase_id             = response.data.credit_note.purchase_id;
                    app.form.account_group = response.data.credit_note.account_group_id;             
                    if(response.data.credit_note.account_group_id != '' && response.data.credit_note.account_group_id != null) {
                        axios.get('/sub_account/get_account_group/'+response.data.credit_note.account_group_id).then(({data})=>(app.cash_bank_accounts=data.sub_accounts));
                    }
                    app.form.cash_bank_account = response.data.credit_note.sub_account_id;

                    $('#supplier_id').val(app.form.supplier_id).trigger('change');
                    $('#warehouse_id').val(app.form.warehouse_id).trigger('change');
                    $('#credit_note_type_id').val(app.form.credit_note_type_id).trigger('change');
                    if(response.data.credit_note.branch != null) {
                        app.form.branch_id = response.data.credit_note.branch.id;
                    } else {
                        app.form.branch_id = '';
                    }
                    if(response.data.credit_note.credit_note_type_id == 2) {
                        app.form.amount = response.data.credit_note.amount;
                    }
                    var search ="&branch_id=" + app.form.branch_id + "&supplier_id=" + app.form.supplier_id + "&warehouse_id=" + app.form.warehouse_id;
                     axios.get("/get_invoice_by_credit_note/"+app.form.credit_note_type_id+"?"+search).then(({ data }) => (app.purchase_invoices = data.data));
                     console.log('Sale Id is =' +app.form.purchase_id)

                     var sales_arr = [];
                     $.each(response.data.credit_note.sales, function( key, value ) {
                        sales_arr.push(value.id);
                     });
                    var index = '';
                    var html = "";
                    // $('#invoices_id').val(app.form.purchase_id).trigger('change');
                    // $.each(response.data.credit_note, function( k, v) {

                    $.each(response.data.credit_note.products, function( key, value) {
                        // console.log(response.data.credit_note);
                        // console.log();
                        // console.log(v.products);
                        // $.each(v.products, function( key, value ) {
                            console.log('Product Name is  ' +value.product_name);
                            // alert('value.id')
                                // index = sales_arr.indexOf(value.id);
                                // app.selected_invoices=push(String(value.id));
                                html += '<tr id="'+app.form.purchase_id+'" data-pivotid = "'+app.form.purchase_id+'">';
                              
                                html += '<td>'+ value.product_name+'</td>';

                                html += '<td><input type="hidden" id="ps_pivot'+value.pivot.id+'" value="'+value.pivot.product_purchase_pivot_id+'" /><input type="text" required id="qty'+value.pivot.id+'" class="form-control num_txt qty_change qty" data-id="'+value.pivot.id+'" value="'+value.pivot.qty+'" /></td>';
                                html += '<input type="hidden"   id="old_qty'+value.pivot.id+'" class="form-control num_txt old_qty" data-id="'+value.pivot.id+'" value="'+value.pivot.qty+'" />';

                                /**html += '<td><input type="text" id="unit'+value.pivot.id+'" class="form-control num_txt unit" readonly value="'+value.uom.uom_name+'" /></td>';**/

                                html += '<td><select class="form-control txt_uom" name="uom[]" id="uom'+value.pivot.id+'"  style="min-width:150px;" readonly>';
                                if(value.uom_id == value.pivot.uom_id) {
                                    html += '<option value="'+value.uom_id+'" selected >'+ value.uom.uom_name +'</option>';
                                }
                                $.each(value.selling_uoms, function( key, suom ) {  
                                    if(suom.pivot.uom_id == value.pivot.uom_id) {
                                        html += '<option value="'+suom.pivot.uom_id+'" selected >'+ suom.uom_name +'</option>';
                                    }
                                });

                                html += '</select>';

                                html += '<td><input type="text" id="unit_price'+value.pivot.id+'" class="form-control num_txt unit" readonly value="'+value.pivot.price+'" /></td>';

                                html += '<td><input type="text" id="total_amt'+value.pivot.id+'" class="form-control num_txt unit total_amt" readonly value="'+value.pivot.total_amount+'" /></td>';

                                // html += '<td><input type="text" id="discount_amt'+value.pivot.id+'" class="form-control num_txt discount_change unit" data-id="'+value.pivot.id + '" value="'+value.pivot.discount+'" /></td>';
                                var rmk = value.pivot.remark == null ? "" : value.pivot.remark;
                                html += '<td><input type="text" id="remark'+value.pivot.id+'" class="form-control remark"  value="'+rmk+'" /></td>';

                                // html += '<td><input type="text" id="remark'+value.pivot.id+'" class="form-control num_txt remark"'+value.pivot.remark == null ? '' : value.pivot.remark +'" /></td>';

                        //         var key = response.data.credit_note.sales.findIndex(x => x.id == value.id);
                        //         if(key > -1) {
                        //             var paid = parseInt(response.data.credit_note.sales[key].pivot.paid_amount);
                        //             if(response.data.credit_note.sales[key].pivot.discount != null) {
                        //                 var discount = parseInt(response.data.credit_note.sales[key].pivot.discount);
                        //             } else {
                        //                 var discount = 0;
                        //             }
                                    
                        //         } else {
                        //             var paid = 0;
                        //             var discount = 0;
                        //         }

                        //         var prev_pay = (parseInt(value.pay_amount)+parseInt(value.credit_note_amount)) - (parseInt(paid) + parseInt(discount));
                        //         var bal = (parseInt(value.total_amount) - parseInt(prev_pay)) - (parseInt(paid) + parseInt(discount));
                            
                        //         html += '<td><input type="text" id="prev_amt'+value.id+'" class="form-control num_txt prev_amt" readonly value="'+prev_pay+'" /></td>';

                        //         if(response.data.credit_note.auto_payment == 1) {
                        //             html += '<td><input type="text" id="pay_amt'+value.id+'" class="form-control num_txt pay_amt pay-change" readonly value="'+paid+'" data-id= "'+value.id+'" /></td>';
                        //         } else {
                        //             html += '<td><input type="text" id="pay_amt'+value.id+'" class="form-control num_txt pay_amt pay-change" value="'+paid+'" data-id= "'+value.id+'" required /></td>';
                        //         }

                        //         html += '<td><input type="text" id="discount_amt'+value.id+'" class="form-control num_txt discount_amt discount-change" value="'+discount+'" data-id= "'+value.id+'" /></td>';

                        //         html += '<td><input type="text" id="balance'+value.id+'" class="form-control num_txt balance_amt" value="'+bal+'" data-id= "'+value.id+'" readonly /></td>';

                                html += '<td class="text-center">';
                                //if(app.user_role != 'admin') {
                                    html += '<a class="remove-row red-icon" id="'+value.pivot.id +'" title="Remove"><i class="fas fa-times-circle" style="font-size: 25px;"></i></a>';
                                //}
                                html += '</td></tr>';

                                // if(!s2.find('option[value="'+value.id+'"]').length) {
                                //     s2.append($('<option value="'+value.id+'">').text(value.invoice_no));
                                // }
                            app.purchase_invoices=app.purchase_invoices.filter(e=>e.id==app.form.purchase_id);
                            // $('#invoices_id').val(app.form.purchase_id).trigger('change');
                            $("#edit_invoices").html(html);
                            // if(app.selected_invoices.length > 0) {
                            //     app.isRequired = false;
                            // } else {
                            //     app.isRequired = true;
                            // }
                        // });

                    });
                    app.isEdit = true;
                    console.log('tk');
                    console.log('tt'+app.invoices);

                })
                .catch(function(error) {
                  // handle error
                  console.log(error);
                })
                .then(function() {
                  // always executed
                });
            },
            getCreditNote(id) {
              let app = this;
              app.invoices = [];  
              $("#loading").show();
              axios
                .get("/purchase_credit_note/edit/" + id)
                .then(function(response) {
                    console.log(response.data.credit_note);
                    //for save button permission
                    if(app.user_role == "admin" || app.user_role == "system") {
                        app.isDisabled = false;
                    } else {
                        app.isDisabled = true;
                    }
                    console.log('jk');
                    app.invoices=response.data.credit_note.products;
                    console.log(app.invoices);
                    app.form.credit_note_no      = response.data.credit_note.credit_note_no;
                    app.form.credit_note_date    = response.data.credit_note.credit_note_date;
                    app.form.supplier_id         = response.data.credit_note.supplier_id;
                    app.form.warehouse_id        = response.data.credit_note.warehouse_id;
                    app.form.credit_note_type_id = response.data.credit_note.credit_note_type_id;
                    app.form.purchase_id             = response.data.credit_note.purchase_id;

                    /*var inv = "";
                    inv += '<option value="'+response.data.credit_note.purchase.id+'">'+response.data.credit_note.purchase.invoice_no+'</option>';
                    $("#invoices_id").innerHTML = inv;*/
                    $('#supplier_id').val(app.form.supplier_id).trigger('change');
                    $('#warehouse_id').val(app.form.warehouse_id).trigger('change');
                    $('#credit_note_type_id').val(app.form.credit_note_type_id).trigger('change');
                    if(response.data.credit_note.branch != null) {
                        app.form.branch_id = response.data.credit_note.branch.id;
                    } else {
                        app.form.branch_id = '';
                    }
                    if(response.data.credit_note.credit_note_type_id == 2) {
                        app.form.amount = response.data.credit_note.amount;
                    }

                    app.form.account_group = response.data.credit_note.account_group_id;             
                    if(response.data.credit_note.account_group_id != '' && response.data.credit_note.account_group_id != null) {
                        axios.get('/sub_account/get_account_group/'+response.data.credit_note.account_group_id).then(({data})=>(app.cash_bank_accounts=data.sub_accounts));
                    }
                    app.form.cash_bank_account = response.data.credit_note.sub_account_id;
                    
                    var search ="&cn_id="+id+"&branch_id=" + app.form.branch_id + "&supplier_id=" + app.form.supplier_id + "&warehouse_id=" + app.form.warehouse_id;
                     //axios.get("/get_invoice_by_credit_note/"+app.form.credit_note_type_id+"/"+id+"?"+search).then(({ data }) => (app.purchase_invoices = data.data));

                    axios.get("/get_purchase_invoice_by_credit_note/"+app.form.credit_note_type_id+"?"+search).then(function(response) {
                        app.cn_amount = response.data.cn_amount;
                        app.purchase_invoices = response.data.data;
                        app.invoice_balance = response.data.inv_bal;
                    });


                     var sales_arr = [];
                     $.each(response.data.credit_note.sales, function( key, value ) {
                        sales_arr.push(value.id);
                     });
                    var index = '';
                    var html = "";
                    app.cn_products = response.data.credit_note.products;
                    app.purchase_invoices=app.purchase_invoices.filter(e=>e.id==app.form.purchase_id);
                    app.isEdit = true;
                    $("#loading").hide();

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
                
                if(app.form.credit_note_type_id == 0) {
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
                if(app.form.credit_note_type_id==2) {
                    var amt = document.getElementById('invoices_id').options[document.getElementById('invoices_id').selectedIndex].dataset.bal;
                    var new_amt = app.form.amount;
                    if(amt<new_amt){
                        app.form.amount = '';
                        swal("Warning!", "Amount must be less than "+ amt, "warning");
                        return false;
                    } 
                }
                $("#loading").show();                
                if (!this.isEdit) {
                    //alert(app.invoice_total_amount); alert('b'+app.invoice_balance);
                    if(app.form.credit_note_type_id==1) {
                        if(app.invoice_total_amount > app.invoice_balance) {
                            swal("Warning!", "Credit Total Amount is greater than invoice balance amount!", "warning");
                            $("#loading").hide();
                            return false;
                        }
                    }
                    app.form.credit_note_date=$('#credit_note_date').val();
                    if(app.form.credit_note_type_id!=2){
                        for(var i=0; i<app.invoices.length; i++) {
                            app.form.products.push(app.invoices[i].id);
                            app.form.product_purchase_ids.push(app.invoices[i].pivot.id);
                            app.form.qty.push($("#qty"+app.invoices[i].pivot.id).val());
                            app.form.uoms.push($("#uom"+app.invoices[i].pivot.id).val());
                            app.form.old_qty.push($("#old_qty"+app.invoices[i].pivot.id).val());
                            // app.form.discounts.push($("#discount_amt"+app.invoices[i].pivot.id).val());
                            app.form.remarks.push($("#remark"+app.invoices[i].pivot.id).val());
                            app.form.units.push($("#unit_price"+app.invoices[i].pivot.id).val());
                            app.form.total_amounts.push($("#total_amt"+app.invoices[i].pivot.id).val());
                        }
                        app.form.amount=app.form.total_amounts.reduce(function (a, b) {
                            return parseInt(a) + parseInt(b);
                        });
                    }
                    // console.log(app.form.pivots_id);
                    // console.log(app.form.qty);
                    // console.log(app.form.discounts);
                    this.form
                      .post("/purchase_credit_note/store")
                      .then(function(data) {
                        if(data.status == "success") {
                            app.invoices = [];
                            $("#loading").hide(); 
                            swal({
                                title: "Success!",
                                text: 'Credit Note is saved.',
                                icon: "success",
                                button: true
                            }).then(function() {
                                app.$router.push({name:'purchase_credit_note'})
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
                        console.log(response);
                    });
                } else {
                    //alert(app.invoice_total_amount); alert('b'+app.invoice_balance);
                    //Edit entry details
                    if(app.form.credit_note_type_id==1) {
                        if(app.invoice_total_amount > app.invoice_balance) {
                            swal("Warning!", "Credit Total Amount is greater than invoice balance amount!", "warning");
                            $("#loading").hide();
                            return false;
                        }
                    }
                    // app.form.total_pay = app.total_pay;
                    // app.invoices = app.selected_invoices.filter((a, b) => app.selected_invoices.indexOf(a) === b);
                    app.form.credit_note_date=$('#credit_note_date').val();
                    if(app.form.credit_note_type_id!=2){

                        for(var i=0; i<app.invoices.length; i++) {
                            app.form.pivots_id.push(app.invoices[i].pivot.id);
                            app.form.product_purchase_ids.push($("#ps_pivot"+app.invoices[i].pivot.id).val());
                            app.form.products.push(app.invoices[i].id);
                            app.form.qty.push($("#qty"+app.invoices[i].pivot.id).val());
                            app.form.uoms.push($("#uom"+app.invoices[i].pivot.id).val());
                            app.form.old_qty.push($("#old_qty"+app.invoices[i].pivot.id).val());
                            // app.form.discounts.push($("#discount_amt"+app.invoices[i].pivot.id).val());
                            app.form.remarks.push($("#remark"+app.invoices[i].pivot.id).val());
                            app.form.units.push($("#unit_price"+app.invoices[i].pivot.id).val());
                            app.form.total_amounts.push($("#total_amt"+app.invoices[i].pivot.id).val());
                            app.form.amount=app.form.total_amounts.reduce(function (a, b) {
                                return parseInt(a) + parseInt(b);
                            });
                        }
                    }                      
                    
                    //alert(app.form.amount); return false;
                    //console.log(app.invoices); return false;

                    this.form
                      .patch("/purchase_credit_note/update/" + app.credit_note_id)
                      .then(function(data) {
                        if(data.status == "success") {
                            //reset form data
                            app.invoices = [];
                            $('#loading').hide();
                            swal({
                                title: "Success!",
                                text: 'Credit Note is updated.',
                                icon: "success",
                                button: true
                            }).then(function() {
                                app.$router.push({name:'purchase_credit_note'})


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