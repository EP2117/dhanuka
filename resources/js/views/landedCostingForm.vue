<template>
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="#!"><i class="feather icon-home"></i></a></li>
                <li class="breadcrumb-item"><a :href="site_path+'/'">Home</a></li>
                <li class="breadcrumb-item"><a :href="site_path+'/purchase_office'">Purchase Office</a></li>
                <li class="breadcrumb-item"><router-link tag="span" to="/product_costing" class="font-weight-normal"><a href="#">Product Costing Entry</a></router-link></li>
                <li class="breadcrumb-item active" aria-current="page">Product Costing Entry Form</li>
            </ol>
        </nav>
        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h4 class="mb-0 text-gray-800">Product Costing Entry Form</h4>
        </div>

        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Entry Details</h6>
            </div>
            <div class="card-body">
                <div class="d-block">
                    <!-- form start -->
                    <form class="form" id="costing_form" method="post" @submit.prevent="onSubmit" @keydown="form.errors.clear($event.target.name)">
                        <div class="row">
                            <div class="form-group col-md-4">
                                <label for="cost_no">Landed Cost No.</label>
                                <input type="text" class="form-control" id="cost_no" name="cost_no" v-model="form.cost_no" readonly>
                            </div>
                            <div class="form-group col-md-4">
                                <label for="shipping_method">Shipping Method</label>
                                <select id="shipping_method" class="form-control mm-txt"
                                        name="shipping_method" v-model="form.shipping_method" @change="changeShippingMethod()" style="width:100%" required>
                                    <option value="">Select One</option>
                                    <option value="container">Container Shipping</option>
                                    <option value="border">Border Shipping</option>
                                </select>
                            </div>
                        </div>
                        <div class="row mt-3" v-if="isContainer">
                            <div class="form-group col-md-4">
                                <label for="bill_date">Bill Date</label>
                                <input type="text" class="form-control datetimepicker" id="bill_date" name="bill_date" v-model="form.bill_date" :required="isContainer">
                            </div>
                            <div class="form-group col-md-4" >
                                <label for="bill_no">Bill No.</label>
                                <input type="text" class="form-control" id="bill_no" name="bill_no" v-model="form.bill_no" :required="isContainer">
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="form-group col-md-4">
                                <label for="container_no">Container No.</label>
                                <input type="text" class="form-control" id="container_no" name="container_no" v-model="form.container_no" required>
                            </div>
                            <div class="form-group col-md-4">
                                <label for="supplier_id">Supplier</label>
                                <select id="supplier_id" class="form-control mm-txt"
                                        name="supplier_id" v-model="form.supplier_id" style="width:100%" required>
                                    <option value="">Select One</option>
                                    <option v-for="sup in suppliers" :value="sup.id"  >{{sup.name}}</option>
                                </select>
                            </div>                            
                        </div>
                        <div class="row">
                            <div class="form-group col-md-4">
                                <label for="split_method">Split Method</label>
                                <select id="split_method" class="form-control"
                                        name="split_method" v-model="form.split_method" @change="changeSplitMethod()" style="width:100%" required>
                                    <option value="">Select One</option>
                                    <option value="by_equal">By Equal</option>
                                    <option value="by_quantity">By QTY</option>
                                </select>
                            </div>
                            <div class="form-group col-md-4">
                                <label for="remark">Remark</label>
                                <textarea class="form-control" id="remark" name="remark" v-model="form.remark" rows="2" required></textarea>
                            </div>                            
                        </div>
                        <div class="row mt-4 mb-3">
                            <div class="col-md-12">
                                <span class="d-none d-sm-inline-block btn-sm btn-primary shadow-sm bg-blue"><i class="fas fa-search-plus text-white"></i> Product Details</span>
                            </div>
                        </div>

                        <div class="row mt-0">
                            <div class="col-md-12 text-right mt-0">
                                <a class='d-sm-inline-block btn btn-sm btn-primary shadow-sm bg-blue text-white' title='Add Product' @click="addProduct()" v-if="((user_role == 'admin' || user_role == 'system' || user_role == 'office_user') && !isDisabled) || (!isEdit)" style="verticle-align:middle"><i class='fas fa-plus'></i></a>
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
                            </div>
                            <div class="col-md-12 table-responsive mt-3">
                                <table class="table costing_table" style="border-collapse:collapse; border:solid 1px #ccc" id="product_table">
                                    <thead class="thead-grey">
                                    <tr>
                                        <th scope="col" class="text-center">Product Name</th>
                                        <th scope="col" class="text-center" style="min-width:100px;">Total CTN</th>
                                        <th scope="col" class="text-center" style="min-width:120px;">1CTN = PCS</th>
                                        <th scope="col" class="text-center" style="min-width:100px;">Total PCS</th>
                                        <th scope="col" class="text-center" style="min-width:120px;">RMB Rate</th>
                                        <th scope="col" class="text-center" style="min-width:120px;">Total RMB <br /> Amount</th>
                                        <th scope="col" class="text-center" style="min-width:130px;">1RMB = Kyat</th>
                                        <th scope="col" class="text-center" style="min-width:110px;">MMK Rate</th>
                                        <th scope="col" class="text-center" style="min-width:140px;">Duty Charges</th>
                                        <th scope="col" class="text-center" style="min-width:120px;">Landed Cost <br />Per Product</th>
                                        <th scope="col" class="text-center">Cost</th>
                                        <th scope="col" class="text-center" style="min-width:120px;">Total Cost</th>
                                        <th scope="col" class="text-center"></th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <template v-if="isEdit && ex_products.length > 0">
                                    </template>
                                    <template v-else>
                                        <tr id="1">
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
                                                <input type="text" class="form-control num_txt total_ctn" name="total_ctn[]" data-id="1" id="total_ctn_1" @blur="allTotalCtn()"  required />
                                            </td>
                                            <td>
                                                <input type="text" class="form-control num_txt pcs_per_ctn" name="pcs_per_ctn[]" data-id="1" id="pcs_per_ctn_1"  required />
                                            </td>
                                            <td>
                                                <input type="text" class="form-control num_txt total_pcs" data-id="1" name="total_pcs[]"  id="total_pcs_1" readonly required @blur="allTotalPcs()" />
                                            </td>
                                            <td>
                                                <input type="text" class="form-control num_txt rmb_rate" data-id="1" name="rmb_rate[]" id="rmb_rate_1"  required />
                                            </td>
                                            <td>
                                                <input type="text" class="form-control num_txt total_rmb"  data-id="1" name="total_rmb[]" id="total_rmb_1"  required readonly @blur="allTotalRmb()" />
                                            </td>
                                            <td>
                                                <input type="text" class="form-control num_txt mmk_per_rmb" data-id="1" name="mmk_per_rmb[]" id="mmk_per_rmb_1"  required />
                                            </td>
                                            <td>
                                                <input type="text" class="form-control num_txt mmk_rate" data-id="1" name="mmk_rate[]" id="mmk_rate_1"  required readonly />
                                            </td>
                                            <td>
                                                <input type="text" class="form-control num_txt duty_charges" data-id="1" name="duty_charges[]" id="duty_charges_1"  required />
                                            </td>
                                            <td>
                                                <input type="text" class="form-control num_txt landed_cost_per_product" data-id="1" name="landed_cost_per_product[]" id="landed_cost_per_product_1"  required />
                                            </td>
                                            <td>
                                                <input type="text" class="form-control num_txt cost" name="cost[]" data-id="1" id="cost_1" style="width:120px;" required readonly />
                                            </td>
                                            <td>
                                                <input type="text" class="form-control num_txt total_cost" name="total_cost[]" data-id="1" id="total_cost_1" style="width:120px;"  required readonly />
                                            </td>
                                        </tr>
                                    </template>
                                    <tr class="total_row">
                                        <td class="text-right mm-txt">Total</td>
                                        <td>
                                            <input type="text" v-model="form.all_total_ctn" class="form-control num_txt" style="width:100px;" readonly required />
                                        </td>
                                        <td></td>
                                        <td>
                                            <input type="text" v-model="form.all_total_pcs" class="form-control num_txt" style="width:150px;" readonly required />
                                        </td>
                                        <td></td>
                                        <td>
                                            <input type="text" v-model="form.all_total_rmb" class="form-control num_txt" style="width:150px;" readonly required />
                                        </td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                    <tr class="total_row">
                                        <td colspan="7" class="text-right mm-txt">CONTAINER FRIGHT</td>
                                        <td colspan="6">
                                            <input type="text" v-model="form.container_freight" class="form-control num_txt container_freight" style="width:150px;" required />
                                        </td>
                                    </tr>                                    
                                    <tr class="total_row">
                                        <td colspan="7" class="text-right mm-txt">Agent Fees</td>
                                        <td colspan="6">
                                            <input type="text" v-model="form.agent_fees" class="form-control num_txt agent_fees" style="width:150px;" required />
                                        </td>
                                    </tr>
                                    <tr class="total_row">
                                        <td colspan="7" class="text-right mm-txt">Bank Service Fees For Payment Order   </td>
                                        <td colspan="6">
                                            <input type="text" v-model="form.bank_service_fees" class="form-control num_txt bank_service_fees" style="width:150px;" required />
                                        </td>
                                    </tr>
                                    <tr class="total_row">
                                        <td colspan="7" class="text-right mm-txt">Maersk Line Myanmar Shipping Line Charges</td>
                                        <td colspan="6">
                                            <input type="text" v-model="form.shipping_line_charges" class="form-control num_txt shipping_line_charges" style="width:150px;" required />
                                        </td>
                                    </tr> 
                                    <tr class="total_row">
                                        <td colspan="7" class="text-right mm-txt">Port Charges</td>
                                        <td colspan="6">
                                            <input type="text" v-model="form.port_charges" class="form-control num_txt port_charges" style="width:150px;" required />
                                        </td>
                                    </tr>
                                    <tr class="total_row">
                                        <td colspan="7" class="text-right mm-txt">Valution Charges</td>
                                        <td colspan="6">
                                            <input type="text" v-model="form.valuation_charges" class="form-control num_txt valuation_charges" style="width:150px;" required />
                                        </td>
                                    </tr>
                                    <tr class="total_row">
                                        <td colspan="7" class="text-right mm-txt">Insurance Charges</td>
                                        <td colspan="6">
                                            <input type="text" v-model="form.insurance_charges" class="form-control num_txt insurance_charges" style="width:150px;" required />
                                        </td>
                                    </tr>
                                    <tr class="total_row">
                                        <td colspan="7" class="text-right mm-txt">Labour Charges</td>
                                        <td colspan="6">
                                            <input type="text" v-model="form.labour_charges" class="form-control num_txt labour_charges" style="width:150px;" required />
                                        </td>
                                    </tr>
                                    <tr class="total_row">
                                        <td colspan="7" class="text-right mm-txt">Document Charges</td>
                                        <td colspan="6">
                                            <input type="text" v-model="form.document_charges" class="form-control num_txt document_charges" style="width:150px;" required />
                                        </td>
                                    </tr>
                                    <tr class="total_row">
                                        <td colspan="7" class="text-right mm-txt">Port Exam Charges</td>
                                        <td colspan="6">
                                            <input type="text" v-model="form.port_exam_charges" class="form-control num_txt port_exam_charges" style="width:150px;" required />
                                        </td>
                                    </tr>
                                    <tr class="total_row">
                                        <td colspan="7" class="text-right mm-txt">Tracking Charges(MIP-SHWEPYITHAR-MOTTAMAL LOGISTICS)</td>
                                        <td colspan="6">
                                            <input type="text" v-model="form.tracking_charges" class="form-control num_txt tracking_charges" style="width:150px;" required />
                                        </td>
                                    </tr>                                    
                                    
                                    <tr class="total_row">
                                        <td colspan="7" class="text-right mm-txt">Total</td>
                                        <td colspan="6">
                                            <input type="text" v-model="form.total" class="form-control num_txt total" id="total" style="width:150px;" required readonly />
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

                           <!--<div class="col-md-12" v-if="(user_role == 'system' || user_role == 'admin' || user_role == 'office_user') && isEdit && !isDisabled">
                                <input type="submit" class="btn btn-primary btn-sm" value="Update">
                            </div>-->

                            <div class="col-md-12" v-if="isEdit">
                                <input type="submit" class="btn btn-primary btn-sm" value="Update">
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
                cost_no: "",
                shipping_method: "",
                bill_date: "",
                bill_no: "",
                supplier_id: "",
                container_no: "",
                split_method: "",
                remark: "",
                product: [],
                total_ctn: [],
                pcs_per_ctn: [],
                total_pcs: [],
                rmb_rate: [],
                total_rmb: [],
                mmk_per_rmb: [],
                mmk_rate: [],
                duty_charges: [],
                landed_cost_per_product: [],
                cost: [],
                total_cost: [],
                all_total_ctn: 0,
                all_total_pcs: 0,
                all_total_rmb: 0,
                container_freight: 0,
                agent_fees: 0,
                bank_service_fees: 0,
                shipping_line_charges: 0,
                port_charges: 0,
                valuation_charges: 0,
                insurance_charges: 0,
                labour_charges: 0,
                document_charges: 0,
                port_exam_charges: 0,
                tracking_charges: 0,
                //tax: 0,  
                //ygn_charge: 0,
                //other_charges: 0,
                total: 0,
            }),
            temp_total_ctn: 0,
            temp_total_pcs: 0,
            temp_total_rmb: 0,
            isEdit: false,
            isContainer: true,
            uoms: [],
            costing_id: '',
            ex_products: [],
            products: [],
            user_warehouse: '',
            suppliers: [],
            user_role: '',
            user_year: '',
            is_readonly: false,
            isDisabled: false,
            original_form: '',
            edit_form: '',
            site_path: '',
            storage_path: '',
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
            this.costing_id = this.$route.params.id;
            let app = this;
            axios.get("/get_product_for_purchase/edit/"+ app.$route.params.id).then(({ data }) => (app.products = data.data))
                .then(function() {
                    // console.log(app);
                    app.getCosting(app.costing_id);
                });

        } else {
            //this.getMaxId();
            this.initProducts();
            // console.log(this);
        };
        //this.form.invoice_date = moment().format("YYYY-MM-DD");
    },
    mounted() {
        // console.log($('#product_table tr').length);

        $("#loading").hide();
        let app = this;
        app.initWarehouses();
        app.initSuppliers();

        $(".txt_product").select2();
        $("#supplier_id").select2();
        $("#supplier_id").on("select2:select", function(e) {
            var data = e.params.data;
            app.form.supplier_id = data.id;
        });        

        $(".txt_product").select2();
        $(".txt_product").on("select2:select", function(e) {
            var data = e.params.data;
        });
       // end product select

        $("#bill_date")
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
                app.form.bill_date = formatedValue;
            });

        $(document).on('click','.remove-row',function(evt) {

            if(document.getElementsByName('product[]').length <= 1) {
                swal("Warning!", "At least one product must be added!", "warning");
                //return false;
            } else {
                $(this).parents("tr").remove();                
            }
            var all_total_ctn = 0;
            var all_total_pcs = 0;
            var all_total_rmb = 0;

            var product_count = document.getElementsByName('product[]').length;
            var total = app.form.total == '' ? 0 : app.form.total;
            var landed_cost_per_product = parseFloat(total)/parseInt(product_count);

            for(var i=0; i<document.getElementsByName('total_ctn[]').length; i++) {
                if(document.getElementsByName('total_ctn[]')[i].value != "") {
                    all_total_ctn += parseFloat(document.getElementsByName('total_ctn[]')[i].value);
                }
                if(document.getElementsByName('total_pcs[]')[i].value != "") {
                    all_total_pcs += parseFloat(document.getElementsByName('total_pcs[]')[i].value);
                }
                if(document.getElementsByName('total_rmb[]')[i].value != "") {
                    all_total_rmb += parseFloat(document.getElementsByName('total_rmb[]')[i].value);
                }

                if(app.form.split_method == "by_equal") {                        

                    /***var landed_cost_per_product  =  document.getElementsByName('landed_cost_per_product[]')[i].value == "" ? 0 : document.getElementsByName('landed_cost_per_product[]')[i].value;***/

                    document.getElementsByName('landed_cost_per_product[]')[i].value = app.decimalFormat(landed_cost_per_product);

                    var duty_charges =  document.getElementsByName('duty_charges[]')[i].value == "" ? 0 :document.getElementsByName('duty_charges[]')[i].value;

                    var total_pcs =  document.getElementsByName('total_pcs[]')[i].value == "" ? 0 : document.getElementsByName('total_pcs[]')[i].value;

                    if(total_pcs > 0) {
                        var cost = (parseFloat(landed_cost_per_product) + parseFloat(duty_charges))/parseFloat(total_pcs);
                    } else {
                        var cost = 0;
                    }

                    document.getElementsByName('cost[]')[i].value = app.decimalFormat(cost);

                    var mmk_rate = document.getElementsByName('mmk_rate[]')[i].value == "" ? 0 : document.getElementsByName('mmk_rate[]')[i].value;

                    var total_cost = parseFloat(cost) + parseFloat(mmk_rate);

                    document.getElementsByName('total_cost[]')[i].value = app.decimalFormat(total_cost);
                }
            }

            app.form.all_total_ctn = app.decimalFormat(all_total_ctn);
            app.form.all_total_pcs = app.decimalFormat(all_total_pcs);
            app.form.all_total_rmb = app.decimalFormat(all_total_rmb);
        });

        $(document).on('keyup','.total_ctn, .pcs_per_ctn',function(evt) {
            var rid = $(this).attr('data-id'); 
            var total_ctn   =  $("#total_ctn_"+rid).val() == '' ? 0 : $("#total_ctn_"+rid).val();
            var pcs_per_ctn =  $("#pcs_per_ctn_"+rid).val() == '' ? 0 : $("#pcs_per_ctn_"+rid).val(); 
            if(total_ctn != "" && pcs_per_ctn != "") {
                var total_pcs = parseFloat(total_ctn) * parseFloat(pcs_per_ctn);
                $("#total_pcs_"+rid).val(app.decimalFormat(total_pcs));

                var rmb_rate   =  $("#rmb_rate_"+rid).val() == "" ? 0 : $("#rmb_rate_"+rid).val();
                if(rmb_rate != "" && total_pcs != "") {
                    var total_rmb = parseFloat(total_pcs) * parseFloat(rmb_rate);
                    $("#total_rmb_"+rid).val(app.decimalFormat(total_rmb));
                }

            }

            if(app.form.split_method == "by_equal") {
                var product_count = document.getElementsByName('product[]').length;
                var total = app.form.total == '' ? 0 : app.form.total;
                var landed_cost_per_product = parseFloat(total)/parseInt(product_count);


                for(var i=0; i<document.getElementsByName('product[]').length; i++) {

                    /***var landed_cost_per_product  =  document.getElementsByName('landed_cost_per_product[]')[i].value == "" ? 0 : document.getElementsByName('landed_cost_per_product[]')[i].value;***/

                    document.getElementsByName('landed_cost_per_product[]')[i].value = app.decimalFormat(landed_cost_per_product);

                    var duty_charges =  document.getElementsByName('duty_charges[]')[i].value == "" ? 0 : document.getElementsByName('duty_charges[]')[i].value;

                    var total_pcs =  document.getElementsByName('total_pcs[]')[i].value == "" ? 0 : document.getElementsByName('total_pcs[]')[i].value;

                    if(total_pcs > 0) {
                        var cost = (parseFloat(landed_cost_per_product) + parseFloat(duty_charges))/parseFloat(total_pcs);
                    } else {
                        var cost = 0;
                    }

                    document.getElementsByName('cost[]')[i].value = app.decimalFormat(cost);

                    var mmk_rate = document.getElementsByName('mmk_rate[]')[i].value == "" ? 0 : document.getElementsByName('mmk_rate[]')[i].value;

                    var total_cost = parseFloat(cost) + parseFloat(mmk_rate);

                    document.getElementsByName('total_cost[]')[i].value = app.decimalFormat(total_cost);
                }
            } else {
                var landed_cost_per_product   =  $("#landed_cost_per_product_"+rid).val() == '' ? 0 : $("#landed_cost_per_product_"+rid).val();
                var duty_charges   =  $("#duty_charges_"+rid).val() == '' ? 0 : $("#duty_charges_"+rid).val();
                var cost = (parseFloat(landed_cost_per_product) + parseFloat(duty_charges))/parseFloat(total_pcs);

                $("#cost_"+rid).val(app.decimalFormat(cost));

                var mmk_rate = $("#mmk_rate_"+rid).val() == "" ? 0 : $("#mmk_rate_"+rid).val();
                var total_cost = parseFloat(cost) + parseFloat(mmk_rate);
                $("#total_cost_"+rid).val(app.decimalFormat(total_cost));
            }

            app.allTotalPcs();
            app.allTotalRmb();
        });

        $(document).on('keyup','.rmb_rate',function(evt) {
            var rid = $(this).attr('data-id'); 
            var rmb_rate   =  $("#rmb_rate_"+rid).val() == "" ? 0 : $("#rmb_rate_"+rid).val();
            var total_pcs =  $("#total_pcs_"+rid).val() == "" ? 0 : $("#total_pcs_"+rid).val(); 
            if(rmb_rate != "" && total_pcs != "") {
                var total_rmb = parseFloat(total_pcs) * parseFloat(rmb_rate);
                $("#total_rmb_"+rid).val(app.decimalFormat(total_rmb));
            }

            var mmk_per_rmb   =  $("#mmk_per_rmb_"+rid).val() == "" ? 0 : $("#mmk_per_rmb_"+rid).val();
            if(rmb_rate != "" && mmk_per_rmb != "") {
                var mmk_rate = parseFloat(mmk_per_rmb) * parseFloat(rmb_rate);
                $("#mmk_rate_"+rid).val(app.decimalFormat(mmk_rate));

                var cost = $("#cost_"+rid).val() == "" ? 0 : $("#cost_"+rid).val();
                var total_cost = parseFloat(cost) + parseFloat(mmk_rate);
                $("#total_cost_"+rid).val(app.decimalFormat(total_cost));
            }

            app.allTotalRmb();
        });

        $(document).on('keyup','.mmk_per_rmb',function(evt) {
            var rid = $(this).attr('data-id'); 
            var mmk_per_rmb   =  $("#mmk_per_rmb_"+rid).val() == "" ? 0 : $("#mmk_per_rmb_"+rid).val();
            var rmb_rate =  $("#rmb_rate_"+rid).val() == "" ? 0 : $("#rmb_rate_"+rid).val();
            if(rmb_rate != "" && mmk_per_rmb != "") {
                var mmk_rate = parseFloat(mmk_per_rmb) * parseFloat(rmb_rate);
                $("#mmk_rate_"+rid).val(app.decimalFormat(mmk_rate));

                var cost = $("#cost_"+rid).val() == "" ? 0 : $("#cost_"+rid).val();
                var total_cost = parseFloat(cost) + parseFloat(mmk_rate);
                $("#total_cost_"+rid).val(app.decimalFormat(total_cost));
            }
        });

        $(document).on('blur','.landed_cost_per_product, .duty_charges, .total_pcs',function(evt) {
            var rid = $(this).attr('data-id'); 
            var landed_cost_per_product  =  $("#landed_cost_per_product_"+rid).val() == "" ? 0 : $("#landed_cost_per_product_"+rid).val();
            var duty_charges =  $("#duty_charges_"+rid).val() == "" ? 0 : $("#duty_charges_"+rid).val();
            var total_pcs =  $("#total_pcs_"+rid).val() == "" ? 0 : $("#total_pcs_"+rid).val();

            var cost = (parseFloat(landed_cost_per_product) + parseFloat(duty_charges))/parseFloat(total_pcs);

            $("#cost_"+rid).val(app.decimalFormat(cost));

            var mmk_rate = $("#mmk_rate_"+rid).val() == "" ? 0 : $("#mmk_rate_"+rid).val();
            var total_cost = parseFloat(cost) + parseFloat(mmk_rate);
            $("#total_cost_"+rid).val(app.decimalFormat(total_cost));
        });

        $(document).on('keyup','.container_freight, .agent_fees, .bank_service_fees, .shipping_line_charges, .port_charges, .valuation_charges, .insurance_charges, .labour_charges, .document_charges, .port_exam_charges, .tracking_charges',function(evt) {

            var container_freight = $(".container_freight").val() == "" ? 0 : $(".container_freight").val();

            var agent_fees = $(".agent_fees").val() == "" ? 0 : $(".agent_fees").val();

            var bank_service_fees = $(".bank_service_fees").val() == "" ? 0 : $(".bank_service_fees").val();

            var shipping_line_charges = $(".shipping_line_charges").val() == "" ? 0 : $(".shipping_line_charges").val();

            var port_charges = $(".port_charges").val() == "" ? 0 : $(".port_charges").val();

            var valuation_charges = $(".valuation_charges").val() == "" ? 0 : $(".valuation_charges").val();

            var insurance_charges = $(".insurance_charges").val() == "" ? 0 : $(".insurance_charges").val();

            var labour_charges = $(".labour_charges").val() == "" ? 0 : $(".labour_charges").val();

            var document_charges = $(".document_charges").val() == "" ? 0 : $(".document_charges").val();

            var port_exam_charges = $(".port_exam_charges").val() == "" ? 0 : $(".port_exam_charges").val();

            var tracking_charges = $(".tracking_charges").val() == "" ? 0 : $(".tracking_charges").val();

            /***var tax = $(".tax").val() == "" ? 0 : $(".tax").val();

            var ygn_charge = $(".ygn_charge").val() == "" ? 0 : $(".ygn_charge").val();

            var other_charges = $(".other_charges").val() == "" ? 0 : $(".other_charges").val();

            var total = parseFloat(container_freight) + parseFloat(tax) + parseFloat(ygn_charge) + parseFloat(other_charges);**/

            var total = parseFloat(container_freight) + parseFloat(agent_fees) + parseFloat(bank_service_fees) + parseFloat(shipping_line_charges) + parseFloat(port_charges) + parseFloat(valuation_charges) + parseFloat(insurance_charges) + parseFloat(labour_charges) + parseFloat(document_charges) + parseFloat(port_exam_charges) + parseFloat(tracking_charges);

            app.form.total = app.decimalFormat(total);
            //$("#total").val(app.decimalFormat(total));

            if(app.form.split_method == "by_equal") {
                var product_count = document.getElementsByName('product[]').length;
                var total = app.form.total == '' ? 0 : app.form.total;
                var landed_cost_per_product = parseFloat(total)/parseInt(product_count);


                for(var i=0; i<document.getElementsByName('product[]').length; i++) {

                    /***var landed_cost_per_product  =  document.getElementsByName('landed_cost_per_product[]')[i].value == "" ? 0 : document.getElementsByName('landed_cost_per_product[]')[i].value;***/

                    document.getElementsByName('landed_cost_per_product[]')[i].value = app.decimalFormat(landed_cost_per_product);

                    var duty_charges =  document.getElementsByName('duty_charges[]')[i].value == "" ? 0 : document.getElementsByName('duty_charges[]')[i].value;

                    var total_pcs =  document.getElementsByName('total_pcs[]')[i].value == "" ? 0 : document.getElementsByName('total_pcs[]')[i].value;

                    if(total_pcs > 0) {
                        var cost = (parseFloat(landed_cost_per_product) + parseFloat(duty_charges))/parseFloat(total_pcs);
                    } else {
                        var cost = 0;
                    }

                    document.getElementsByName('cost[]')[i].value = app.decimalFormat(cost);

                    var mmk_rate = document.getElementsByName('mmk_rate[]')[i].value == "" ? 0 : document.getElementsByName('mmk_rate[]')[i].value;

                    var total_cost = parseFloat(cost) + parseFloat(mmk_rate);

                    document.getElementsByName('total_cost[]')[i].value = app.decimalFormat(total_cost);
                }
            }

        });
    },
    methods: {
        decimalFormat(num)
        {
           var decimal_num = Number.isInteger(parseFloat(num))== true ?  parseInt(num) : num;
           return num;
        },

        allTotalCtn() {
            var all_total_ctn = 0;
            for(var i=0; i<document.getElementsByName('total_ctn[]').length; i++) {
                if(document.getElementsByName('total_ctn[]')[i].value != "") {
                    all_total_ctn += parseFloat(document.getElementsByName('total_ctn[]')[i].value);
                }
           }

           this.form.all_total_ctn = this.decimalFormat(all_total_ctn);
        },

        allTotalPcs() {
            var all_total_pcs = 0;
            for(var i=0; i<document.getElementsByName('total_pcs[]').length; i++) {
                if(document.getElementsByName('total_pcs[]')[i].value != "") {
                    all_total_pcs += parseFloat(document.getElementsByName('total_pcs[]')[i].value);
                }
           }

           this.form.all_total_pcs = this.decimalFormat(all_total_pcs);
        },

        allTotalRmb() {
            var all_total_rmb = 0;
            for(var i=0; i<document.getElementsByName('total_rmb[]').length; i++) {
                if(document.getElementsByName('total_rmb[]')[i].value != "") {
                    all_total_rmb += parseFloat(document.getElementsByName('total_rmb[]')[i].value);
                }
           }

           this.form.all_total_rmb = this.decimalFormat(all_total_rmb);
        },

        changeShippingMethod() {
            if(this.form.shipping_method == 'border') {
                this.isContainer = false;
                
            } else {
                this.isContainer = true;   
            }
        },

        changeSplitMethod() {
            if(this.form.split_method == 'by_equal') {

                $(".landed_cost_per_product").attr('readonly',true);

                var product_count = document.getElementsByName('product[]').length;
                var total = this.form.total == '' ? 0 : this.form.total;
                var landed_cost_per_product = parseFloat(total)/parseInt(product_count);


                for(var i=0; i<document.getElementsByName('product[]').length; i++) {

                    /***var landed_cost_per_product  =  document.getElementsByName('landed_cost_per_product[]')[i].value == "" ? 0 : document.getElementsByName('landed_cost_per_product[]')[i].value;***/

                    document.getElementsByName('landed_cost_per_product[]')[i].value = this.decimalFormat(landed_cost_per_product);

                    var duty_charges =  document.getElementsByName('duty_charges[]')[i].value == "" ? 0 : document.getElementsByName('duty_charges[]')[i].value;

                    var total_pcs =  document.getElementsByName('total_pcs[]')[i].value == "" ? 0 : document.getElementsByName('total_pcs[]')[i].value;

                    if(total_pcs > 0) {
                        var cost = (parseFloat(landed_cost_per_product) + parseFloat(duty_charges))/parseFloat(total_pcs);
                    } else {
                        var cost = 0;
                    }

                    document.getElementsByName('cost[]')[i].value = this.decimalFormat(cost);

                    var mmk_rate = document.getElementsByName('mmk_rate[]')[i].value == "" ? 0 : document.getElementsByName('mmk_rate[]')[i].value;

                    var total_cost = parseFloat(cost) + parseFloat(mmk_rate);

                    document.getElementsByName('total_cost[]')[i].value = this.decimalFormat(total_cost);
                }
                
            } else {
                $(".landed_cost_per_product").attr('readonly',false); 
            }
        },

        initSuppliers() {
            axios.get("/supplier").then(({ data }) => (this.suppliers = data.data));
            $("#supplier_id").select2();
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

        initWarehouses() {
            axios.get("/warehouses").then(({ data }) => (this.warehouses = data.data));
            $("#to_warehouse").select2();
        },

        addProduct() {
            var max = 0;
            $('#product_table tbody tr').each(function(){
                max = parseInt($(this).attr('id')) > max ? parseInt($(this).attr('id')) : max;
            });
            //var max = $('#product_table tbody tr').sort(function(a, b) { return +a.id < +b.id })[0].id;
            var row_id = parseInt(max) +1;
            let app = this;
            var table=document.getElementById("product_table");
            var row=table.insertRow((table.rows.length)-13);
            row.id = row_id;
            var cell1=row.insertCell(0);
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

            var html = $('#txt_product').html();
            $(t1).html(html);
            cell1.appendChild(t1);

            var cell2=row.insertCell(1);
            var t2=document.createElement("input");
            t2.name = "total_ctn[]";
            t2.id = "total_ctn_"+row_id;
            t2.className ="form-control num_txt total_ctn";
            $(t2).attr("required", true);
            $(t2).attr("data-id", row_id);
            t2.addEventListener('blur', function(){ app.allTotalCtn(); });
            cell2.appendChild(t2);

            var cell3=row.insertCell(2);
            var t3=document.createElement("input");
            t3.name = "pcs_per_ctn[]";
            t3.id = "pcs_per_ctn_"+row_id;
            t3.className ="form-control num_txt pcs_per_ctn";
            $(t3).attr("required", true);
            $(t3).attr("data-id", row_id);
            // t2.addEventListener('blur', function(){ app.checkQty(t4); });
            cell3.appendChild(t3);

            var cell4=row.insertCell(3);
            var t4=document.createElement("input");
            t4.name = "total_pcs[]";
            t4.id = "total_pcs_"+row_id;
            t4.className ="form-control num_txt total_pcs";
            $(t4).attr("required", true);
            $(t4).attr("readonly", true);
            $(t4).attr("data-id", row_id);
            t4.addEventListener('blur', function(){ app.allTotalPcs(t4); });
            cell4.appendChild(t4);

            var cell5=row.insertCell(4);
            var t5=document.createElement("input");
            t5.name = "rmb_rate[]";
            t5.id = "rmb_rate_"+row_id;
            $(t5).attr("data-id", row_id);
            t5.className ="form-control num_txt rmb_rate";
            $(t5).attr("required", true);
            // t2.addEventListener('blur', function(){ app.checkQty(t4); });
            cell5.appendChild(t5);

            var cell6=row.insertCell(5);
            var t6=document.createElement("input");
            t6.name = "total_rmb[]";
            t6.id = "total_rmb_"+row_id;
            t6.className ="form-control num_txt total_rmb";
            $(t6).attr("data-id", row_id);
            $(t6).attr("required", true);
            $(t6).attr("readonly", true);
            t6.addEventListener('blur', function(){ app.allTotalRmb(t4); });
            cell6.appendChild(t6);

            var cell7=row.insertCell(6);
            var t7=document.createElement("input");
            t7.name = "mmk_per_rmb[]";
            t7.id = "mmk_per_rmb_"+row_id;
            t7.className ="form-control num_txt mmk_per_rmb";
            $(t7).attr("required", true);
            $(t7).attr("data-id", row_id);
            // t2.addEventListener('blur', function(){ app.checkQty(t4); });
            cell7.appendChild(t7);

            var cell8=row.insertCell(7);
            var t8=document.createElement("input");
            t8.name = "mmk_rate[]";
            t8.id = "mmk_rate_"+row_id;
            t8.className ="form-control num_txt mmk_rate";
            $(t8).attr("required", true);
            $(t8).attr("readonly", true);
            $(t8).attr("data-id", row_id);
            // t2.addEventListener('blur', function(){ app.checkQty(t4); });
            cell8.appendChild(t8);

            var cell9=row.insertCell(8);
            var t9=document.createElement("input");
            t9.name = "duty_charges[]";
            t9.id = "duty_charges_"+row_id;
            t9.className ="form-control num_txt duty_charges";
            $(t9).attr("required", true);
            $(t9).attr("data-id", row_id);
            // t2.addEventListener('blur', function(){ app.checkQty(t4); });
            cell9.appendChild(t9);

            var cell10=row.insertCell(9);
            var t10=document.createElement("input");
            t10.name = "landed_cost_per_product[]";
            t10.id = "landed_cost_per_product_"+row_id;
            $(t10).attr("data-id", row_id);
            t10.className ="form-control num_txt landed_cost_per_product";
            $(t10).attr("required", true);

            if(this.form.split_method == "by_equal") {
                $(t10).attr("readonly", true);
            } else {
                $(t10).attr("readonly", false);
            }
            // t2.addEventListener('blur', function(){ app.checkQty(t4); });
            cell10.appendChild(t10);

            var cell11=row.insertCell(10);
            var t11=document.createElement("input");
            t11.name = "cost[]";
            t11.id = "cost_"+row_id;
            t11.className ="form-control num_txt cost";
            $(t11).attr("data-id", row_id);
            $(t11).attr("required", true);
            $(t11).attr("readonly", true);
            // t2.addEventListener('blur', function(){ app.checkQty(t4); });
            cell11.appendChild(t11);

            var cell12=row.insertCell(11);
            var t12=document.createElement("input");
            t12.name = "total_cost[]";
            t12.id = "total_cost_"+row_id;
            t12.className ="form-control num_txt total_cost";
            $(t12).attr("data-id", row_id);
            $(t12).attr("required", true);
            $(t12).attr("readonly", true);
            // t2.addEventListener('blur', function(){ app.checkQty(t4); });
            cell12.appendChild(t12);

            $(".txt_product").select2();
            var cell13=row.insertCell(12);
            cell13.className = "text-center";
            var row_action = "<a class='remove-row red-icon' title='Remove'><i class='fas fa-times-circle' style='font-size: 25px;'></i></a>    ";
            $(cell13).append(row_action);
            $(".txt_qty").on("keyup", function(e) {
                app.calTotalAmount($('#qty_'+row_id));
            });
            // app.calTotalAmount($("#unit_price_"+row_id));
        },

        getCosting(id) {
            let app = this;
            axios.get("/landed_costing/" + id)
                .then(function(response) {
                    if(response.data.costing.shipping_method == "container") {
                        app.isContainer = true;
                        app.form.bill_date = moment(response.data.costing.bill_date).format('YYYY-MM-DD');
                        app.form.bill_no = response.data.costing.bill_no;
                    } else {
                        app.isContainer = false;
                    }
                    app.form.shipping_method = response.data.costing.shipping_method;
                    app.form.container_no = response.data.costing.container_no;
                    app.form.supplier_id = response.data.costing.supplier_id;
                    $("#supplier_id").val(response.data.costing.supplier_id).trigger('change');
                    app.form.split_method = response.data.costing.split_method;
                    app.form.remark = response.data.costing.remark;
                    app.ex_products = response.data.costing.products;
                    
                   /** $("#supplier_id").append('<option value="'+response.data.purchase.supplier_id+'" selected>'+response.data.purchase.supplier.name+'</option>');
                    app.form.supplier_id = response.data.purchase.supplier_id;**/

                    app.form.all_total_ctn  = app.decimalFormat(parseFloat(response.data.costing.total_ctn));
                    app.form.all_total_pcs  = app.decimalFormat(parseFloat(response.data.costing.total_pcs));
                    app.form.all_total_rmb  = app.decimalFormat(parseFloat(response.data.costing.total_rmb));

                    app.form.container_freight = app.decimalFormat(parseFloat(response.data.costing.container_freight));

                    /**app.form.tax  = app.decimalFormat(parseFloat(response.data.costing.tax));
                    app.form.ygn_charge  = app.decimalFormat(parseFloat(response.data.costing.ygn_charge));
                    app.form.other_charges  = app.decimalFormat(parseFloat(response.data.costing.other_charges));**/

                    app.form.agent_fees = app.decimalFormat(parseFloat(response.data.costing.agent_fees));

                    app.form.bank_service_fees = app.decimalFormat(parseFloat(response.data.costing.bank_service_fees));

                    app.form.shipping_line_charges = app.decimalFormat(parseFloat(response.data.costing.shipping_line_charges));

                    app.form.port_charges = app.decimalFormat(parseFloat(response.data.costing.port_charges));

                    app.form.valuation_charges = app.decimalFormat(parseFloat(response.data.costing.valuation_charges));

                    app.form.insurance_charges = app.decimalFormat(parseFloat(response.data.costing.insurance_charges));

                    app.form.labour_charges = app.decimalFormat(parseFloat(response.data.costing.labour_charges));

                    app.form.document_charges = app.decimalFormat(parseFloat(response.data.costing.document_charges));

                    app.form.port_exam_charges = app.decimalFormat(parseFloat(response.data.costing.port_exam_charges));

                    app.form.tracking_charges = app.decimalFormat(parseFloat(response.data.costing.tracking_charges));

                    app.form.total  = app.decimalFormat(parseFloat(response.data.costing.total));

                    //add products dynamically
                    var row_id = 0;
                    $.each(app.ex_products, function( key, product ) {

                        row_id = row_id+1;

                        var table=document.getElementById("product_table");
                        var row=table.insertRow((table.rows.length) - 13);
                        row.id = row_id;

                        var cell1=row.insertCell(0);
                        var t1=document.createElement("select");
                        t1.name = "product[]";
                        t1.id = "product_"+row_id;
                        t1.className = "form-control txt_product";
                        t1.style = "min-width:150px;";
                        $(t1).attr("required", true);

                        var option = document.createElement("option");
                        option.value = product.id;
                        option.text = product.product_name;
                        t1.append(option);

                        var html = $('#txt_product').html();
                        $(t1).append(html);
                        cell1.appendChild(t1);

                        $(".txt_product").select2();
                        //$("#product_"+row_id).val(product.id).trigger('change');

                        var cell2=row.insertCell(1);
                        var t2=document.createElement("input");
                        t2.name = "total_ctn[]";
                        t2.id = "total_ctn_"+row_id;
                        t2.value = app.decimalFormat(parseFloat(product.pivot.total_ctn));
                        t2.className ="form-control num_txt total_ctn";
                        $(t2).attr("required", true);
                        $(t2).attr("data-id", row_id);
                        t2.addEventListener('blur', function(){ app.allTotalCtn(); });
                        cell2.appendChild(t2);

                        var cell3=row.insertCell(2);
                        var t3=document.createElement("input");
                        t3.name = "pcs_per_ctn[]";
                        t3.id = "pcs_per_ctn_"+row_id;
                        t3.value = app.decimalFormat(parseFloat(product.pivot.pcs_per_ctn));
                        t3.className ="form-control num_txt pcs_per_ctn";
                        $(t3).attr("required", true);
                        $(t3).attr("data-id", row_id);
                        // t2.addEventListener('blur', function(){ app.checkQty(t4); });
                        cell3.appendChild(t3);

                        var cell4=row.insertCell(3);
                        var t4=document.createElement("input");
                        t4.name = "total_pcs[]";
                        t4.id = "total_pcs_"+row_id;
                        t4.className ="form-control num_txt total_pcs";
                        t4.value = app.decimalFormat(parseFloat(product.pivot.total_pcs));
                        $(t4).attr("required", true);
                        $(t4).attr("readonly", true);
                        $(t4).attr("data-id", row_id);
                        t4.addEventListener('blur', function(){ app.allTotalPcs(t4); });
                        cell4.appendChild(t4);

                        var cell5=row.insertCell(4);
                        var t5=document.createElement("input");
                        t5.name = "rmb_rate[]";
                        t5.id = "rmb_rate_"+row_id;
                        $(t5).attr("data-id", row_id);
                        t5.value = app.decimalFormat(parseFloat(product.pivot.rmb_rate));
                        t5.className ="form-control num_txt rmb_rate";
                        $(t5).attr("required", true);
                        // t2.addEventListener('blur', function(){ app.checkQty(t4); });
                        cell5.appendChild(t5);

                        var cell6=row.insertCell(5);
                        var t6=document.createElement("input");
                        t6.name = "total_rmb[]";
                        t6.id = "total_rmb_"+row_id;
                        t6.className ="form-control num_txt total_rmb";
                        t6.value = app.decimalFormat(parseFloat(product.pivot.total_rmb));
                        $(t6).attr("data-id", row_id);
                        $(t6).attr("required", true);
                        $(t6).attr("readonly", true);
                        t6.addEventListener('blur', function(){ app.allTotalRmb(t4); });
                        cell6.appendChild(t6);

                        var cell7=row.insertCell(6);
                        var t7=document.createElement("input");
                        t7.name = "mmk_per_rmb[]";
                        t7.id = "mmk_per_rmb_"+row_id;
                        t7.className ="form-control num_txt mmk_per_rmb";
                        t7.value = app.decimalFormat(parseFloat(product.pivot.mmk_per_rmb));
                        $(t7).attr("required", true);
                        $(t7).attr("data-id", row_id);
                        // t2.addEventListener('blur', function(){ app.checkQty(t4); });
                        cell7.appendChild(t7);

                        var cell8=row.insertCell(7);
                        var t8=document.createElement("input");
                        t8.name = "mmk_rate[]";
                        t8.id = "mmk_rate_"+row_id;
                        t8.className ="form-control num_txt mmk_rate";
                        t8.value = app.decimalFormat(parseFloat(product.pivot.mmk_rate));
                        $(t8).attr("required", true);
                        $(t8).attr("readonly", true);
                        $(t8).attr("data-id", row_id);
                        // t2.addEventListener('blur', function(){ app.checkQty(t4); });
                        cell8.appendChild(t8);

                        var cell9=row.insertCell(8);
                        var t9=document.createElement("input");
                        t9.name = "duty_charges[]";
                        t9.id = "duty_charges_"+row_id;
                        t9.className ="form-control num_txt duty_charges";
                        t9.value = app.decimalFormat(parseFloat(product.pivot.duty_charges));
                        $(t9).attr("required", true);
                        $(t9).attr("data-id", row_id);
                        // t2.addEventListener('blur', function(){ app.checkQty(t4); });
                        cell9.appendChild(t9);

                        var cell10=row.insertCell(9);
                        var t10=document.createElement("input");
                        t10.name = "landed_cost_per_product[]";
                        t10.id = "landed_cost_per_product_"+row_id;
                        t10.value = app.decimalFormat(parseFloat(product.pivot.landed_cost_per_product));
                        $(t10).attr("data-id", row_id);
                        t10.className ="form-control num_txt landed_cost_per_product";
                        $(t10).attr("required", true);
                        if(response.data.costing.split_method == "by_equal") {
                            $(t10).attr("readonly", true);
                        }
                        if(app.form.split_method == "by_equal") {
                            $(t10).attr("readonly", true);
                        } else {
                            $(t10).attr("readonly", false);
                        }
                        // t2.addEventListener('blur', function(){ app.checkQty(t4); });
                        cell10.appendChild(t10);

                        var cell11=row.insertCell(10);
                        var t11=document.createElement("input");
                        t11.name = "cost[]";
                        t11.id = "cost_"+row_id;
                        t11.className ="form-control num_txt cost";
                        t11.value = app.decimalFormat(parseFloat(product.pivot.cost));
                        $(t11).attr("data-id", row_id);
                        $(t11).attr("required", true);
                        $(t11).attr("readonly", true);
                        // t2.addEventListener('blur', function(){ app.checkQty(t4); });
                        cell11.appendChild(t11);

                        var cell12=row.insertCell(11);
                        var t12=document.createElement("input");
                        t12.name = "total_cost[]";
                        t12.id = "total_cost_"+row_id;
                        t12.className ="form-control num_txt total_cost";
                        t12.value = app.decimalFormat(parseFloat(product.pivot.total_cost));
                        $(t12).attr("data-id", row_id);
                        $(t12).attr("required", true);
                        $(t12).attr("readonly", true);
                        // t2.addEventListener('blur', function(){ app.checkQty(t4); });
                        cell12.appendChild(t12);
                        
                        var cell13=row.insertCell(12);
                        cell13.className = "text-center";
                        var row_action = "<a class='remove-row red-icon' title='Remove'><i class='fas fa-times-circle' style='font-size: 25px;'></i></a>    ";
                        $(cell13).append(row_action);

                        /****var cell10=row.insertCell(5);
                        cell10.className = "text-center";
                        if((app.user_role == 'admin' || app.user_role == 'system' || app.user_role == 'office_user') && !app.isDisabled)
                        {
                            var row_action = "<a class='remove-exrow red-icon' title='Remove'><i class='fas fa-times-circle' style='font-size: 25px;'></i></a>";
                        } else {
                            var row_action = "<a class='remove-exrow red-icon' title='Remove' style='display:none;'><i class='fas fa-times-circle' style='font-size: 25px;'></i></a>";
                        }
                        $(cell10).append(row_action);****/
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

        onSubmit: function(event){
            let app = this;           

            //EP added
           /*** if(app.form.payment_type == 'cash') {
                if(app.form.balance_amount > 0 ) {
                    swal("Warning!", "Balance must be zero for cash payment!", "warning");
                    return false;
                }
            }***/
            //End EP

             $('#loading').show();

            app.form.product = [];
            app.form.total_ctn = [];
            app.form.pcs_per_ctn = [];
            app.form.total_pcs = [];
            app.form.rmb_rate = [];
            app.form.total_rmb = [];
            app.form.mmk_per_rmb = [];
            app.form.mmk_rate = [];
            app.form.duty_charges = [];
            app.form.landed_cost_per_product = [];
            app.form.cost = [];
            app.form.total_cost = [];

            if (!this.isEdit) {
                app.form.product = [];

                var landed_cost_total = 0;

                for(var i=0; i<document.getElementsByName('product[]').length; i++) {

                    landed_cost_total = parseFloat(landed_cost_total) + parseFloat(document.getElementsByName('landed_cost_per_product[]')[i].value);

                    app.form.product.push(document.getElementsByName('product[]')[i].value);
                    app.form.total_ctn.push(document.getElementsByName('total_ctn[]')[i].value);
                    app.form.pcs_per_ctn.push(document.getElementsByName('pcs_per_ctn[]')[i].value);
                    app.form.total_pcs.push(document.getElementsByName('total_pcs[]')[i].value);
                    app.form.rmb_rate.push(document.getElementsByName('rmb_rate[]')[i].value);
                    app.form.total_rmb.push(document.getElementsByName('total_rmb[]')[i].value);
                    app.form.mmk_per_rmb.push(document.getElementsByName('mmk_per_rmb[]')[i].value);
                    app.form.mmk_rate.push(document.getElementsByName('mmk_rate[]')[i].value);
                    app.form.duty_charges.push(document.getElementsByName('duty_charges[]')[i].value);
                    app.form.landed_cost_per_product.push(document.getElementsByName('landed_cost_per_product[]')[i].value);
                    app.form.cost.push(document.getElementsByName('cost[]')[i].value);
                    app.form.total_cost.push(document.getElementsByName('total_cost[]')[i].value);
                }

                if(parseFloat(landed_cost_total) != parseFloat(app.form.total)) {
                    swal("Warning!", "Total Landed Cost Per Product must be the same with Total!", "warning");
                    $('#loading').hide();
                    return false;
                }
                //console.log(app.form.landed_cost_per_product); return false;
                this.form
                    .post("/landed_costing/create")
                    .then(function(data) {
                        // console.log(data.data);
                        if(data.status == "success") {
                            //reset form data
                            event.target.reset();
                            $(".txt_product").select2();
                            $('#loading').hide();
                            swal({
                                title: "Success!",
                                text: 'Product Costing Entry is saved.',
                                icon: "success",
                                button: true
                            }).then(function() {
                                location.reload();
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
                        var error = response;
                        console.log(error);
                        $("#loading").hide();
                        //swal("Warning!", error, "warning");

                    });
            } else {
            //Edit entry details
            app.form.product = [];

            var landed_cost_total = 0;

            for(var i=0; i<document.getElementsByName('product[]').length; i++) {

                landed_cost_total = parseFloat(landed_cost_total) + parseFloat(document.getElementsByName('landed_cost_per_product[]')[i].value);

                app.form.product.push(document.getElementsByName('product[]')[i].value);
                app.form.total_ctn.push(document.getElementsByName('total_ctn[]')[i].value);
                app.form.pcs_per_ctn.push(document.getElementsByName('pcs_per_ctn[]')[i].value);
                app.form.total_pcs.push(document.getElementsByName('total_pcs[]')[i].value);
                app.form.rmb_rate.push(document.getElementsByName('rmb_rate[]')[i].value);
                app.form.total_rmb.push(document.getElementsByName('total_rmb[]')[i].value);
                app.form.mmk_per_rmb.push(document.getElementsByName('mmk_per_rmb[]')[i].value);
                app.form.mmk_rate.push(document.getElementsByName('mmk_rate[]')[i].value);
                app.form.duty_charges.push(document.getElementsByName('duty_charges[]')[i].value);
                app.form.landed_cost_per_product.push(document.getElementsByName('landed_cost_per_product[]')[i].value);
                app.form.cost.push(document.getElementsByName('cost[]')[i].value);
                app.form.total_cost.push(document.getElementsByName('total_cost[]')[i].value);
            }

            if(parseFloat(landed_cost_total) != parseFloat(app.form.total)) {
                swal("Warning!", "Total Landed Cost Per Product must be the same with Total!", "warning");
                $('#loading').hide();
                return false;
            }

            this.form
                .patch("/landed_costing/" + app.costing_id)
                .then(function(data) {
                    if(data.status == "success") {

                        //reset form data
                        event.target.reset();
                        $(".txt_product").select2();
                        $('#loading').hide();

                        swal({
                            title: "Success!",
                            text: 'Product Costing Entry is updated.',
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

                });
            }
        },

    }
}
</script>

<style scoped>

</style>
