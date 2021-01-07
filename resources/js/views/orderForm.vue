<template>
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="#!"><i class="feather icon-home"></i></a></li>
                <li class="breadcrumb-item"><a :href="site_path+'/'">Home</a></li>
                <li class="breadcrumb-item"><a :href="site_path+'/office'">Office Sale</a></li>
                <li class="breadcrumb-item"><router-link tag="span" to="/order" class="font-weight-normal"><a href="#">Sale Order</a></router-link></li>
                <li class="breadcrumb-item active" aria-current="page">Sale Order Form</li>

            </ol>
        </nav>
        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h4 class="mb-0 text-gray-800">Sale Order Form</h4>
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
                                <label for="order_no">Sale Order No.</label>
                                <input type="text" class="form-control" id="order_no" name="order_no" v-model="form.order_no" readonly>
                            </div>
                            <div class="form-group col-md-4">
                                <label for="order_date">Date</label>
                                <input type="text" class="form-control datetimepicker" id="order_date" name="order_date"
                                v-model="form.order_date" required readonly>
                            </div>
                        </div>

                        <div class="row mt-3">
                             <div class="form-group col-md-4">
                                <label for="customer_id">Customer</label>
                                <select id="customer_id" class="customer_id mm-txt"
                                    name="customer_id" v-model="form.customer_id" style="width:100%" :disabled="readonly_status" required
                                >
                                    <option value="">Select One</option>
                                    <option v-for="cus in customers" :value="cus.id"  >{{cus.cus_name}}</option>
                                </select>
                            </div>

                            <div class="form-group col-md-4">
                                <label for="sale_man">Sale Man</label>
                                <select id="sale_man" class="mm-txt"
                                    name="sale_man" v-model="form.sale_man_id" style="width:100%"
                                >
                                    <option value="">Select One</option>
                                    <option v-for="sale_man in sale_men" :value="sale_man.id"  >{{sale_man.sale_man}}</option>
                                </select>
                            </div>

                            <!--<div class="form-group col-md-4">
                                <label for="warehouse">Warehouse</label>
                                 <input type="text" class="form-control" id="warehouse" name="warehouse"
                                v-model="user_warehouse" readonly>
                            </div>-->
                           
                        </div>

                        <div class="row mt-3">
                            <!--<div class="form-group col-md-4">
                                <label for="sale_man">Sale Man</label>
                                <input type="text" class="form-control" id="sale_man" name="sale_man"
                                v-model="sale_man" readonly>
                            </div> -->
                            <div class="form-group col-md-4">
                                <label for="branch_name">Branch</label>
                                 <input type="text" class="form-control" id="branch_name" name="branch_name"
                                v-model="user_branch" readonly>
                            </div>                        
                        </div>

                        <div class="row mt-4">
                            <div class="col-md-12">
                                <span class="d-none d-sm-inline-block btn-sm btn-primary shadow-sm bg-blue"><i class="fas fa-search-plus text-white"></i> Product Details</span>
                            </div>
                        </div>

                        <div class="row mt-0">
                            <div class="col-md-12 text-right mt-0">
                                <a class='blue-icon' title='Add Product' @click="addProduct()" v-if="user_role != 'admin' && (order_status == 'Draft' || order_status == '')"><i class='fas fa-plus-square' style='font-size: 30px;'></i></a>
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
                                        <template v-else>                                      
                                        <tr id="1">
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
                                                <input type="text" class="form-control num_txt txt_qty" style="width:100px;" name="qty[]"  id="qty_1" @blur="calTotalAmount($event.target)" required />
                                            </td>
                                            <td>
                                                <select class="form-control txt_uom"
                                                    name="uom[]" id="uom_1" style="min-width:150px;" data-uom="" :disabled="readonly_status"required
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
                                                <input type="text" style="min-width:100px;" class="form-control" name="actual_rate[]" id="actual_rate_1" @blur="calTotalAmount($event.target)" readonly required />
                                            </td>
                                            <td class="text-center">
                                                <input
                                                    type="checkbox"
                                                    name="chk_foc[]" id="foc_1"
                                                    @change="checkFoc($event.target)"
                                                    :disabled="readonly_status"
                                                >
                                            </td>
                                            <td>
                                                <input type="text" style="width:70px;" class="form-control num_txt" name="other_discount[]" id="other_discount_1" @blur="calTotalAmount($event.target)" />
                                            </td>
                                            <td>
                                                <input type="text" class="form-control num_txt" readonly style="width:100px;" name="total_amount[]" id="total_amount_1" required />
                                            </td>
                                            <td class="text-center">
                                                <a class='remove-row red-icon' title='Remove' v-if="user_role != 'admin' && (order_status == 'Draft' || order_status == '')"><i class='fas fa-times-circle' style='font-size: 25px;'></i></a>
                                            </td>
                                        </tr>
                                        </template>
                                        <tr class="total_row">
                                            <td colspan="7" class="text-right">Total Amount</td>
                                            <td></td>
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
                        <div class="row mt-3">
                            <div class="form-group col-md-8 mm-txt">
                                <label for="remark">Remark</label>
                                <textarea rows='3' class="form-control mm-txt" id="remark" name="remark"
                                v-model="form.remark"></textarea>
                            </div>                           
                        </div>

                        <div class="row">
                            <div class="col-md-12" v-if="order_status == 'Draft' || order_status == ''">
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
                order_date: "",
                order_no: "",
                warehouse: "",
                customer_id: "",
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
                ex_product_pivot: [],
                product_pivot: [],
                is_foc: [],
                uom_id: "",
                price_variants: [],
                sale_man_id: '',
                remark: '',
                cash_discount: '',
                net_total: '',
                tax: '',
                tax_amount: '',
                balance_amount: '',
                previous_balance: '',
              }),
              isEdit: false,
              products: [],
              order_id: '',
              ex_products: [],
              user_warehouse: '',
              selling_uoms: [],
              customers: [],
              user_role: '',
              user_id: '',
              order_status: '',
              sale_man: '',
              readonly_status: false,
              isDisabled: false,
              user_brands: [],
              order_products: [],
              user_branch: '',
              site_path: '',
              storage_path: '',
              sale_men: [],
            };
        },

        created() {
            this.user_warehouse = document.querySelector("meta[name='user-wh']").getAttribute('content');

            this.user_role = document.querySelector("meta[name='user-role']").getAttribute('content');

            this.user_branch = document.querySelector("meta[name='user-branch']").getAttribute('content');

            this.user_id = document.querySelector("meta[name='user-id-likelink']").getAttribute('content');

            this.sale_man = document.querySelector("meta[name='user-name-likelink']").getAttribute('content');

            //this.form.sale_man_id = document.querySelector("meta[name='user-id-likelink']").getAttribute('content');

            this.site_path = document.querySelector("meta[name='site-path']").getAttribute('content');
            //this.site_path = this.site_path.substring(this.site_path.lastIndexOf('/')+1);
            this.storage_path = document.querySelector("meta[name='storage-path']").getAttribute('content');
            
            /***** if(this.user_role == "office_order_user" && this.user_role != "admin" && this.user_role != "system") {
                var url =  window.location.origin;
                window.location.replace(url);
            }  *****/

            if(this.user_role == "van_user" ) {
                var url =  window.location.origin;
                window.location.replace(this.site_path);
            }   

            if(this.$route.params.id) {
                this.isEdit = true;
                this.order_id = this.$route.params.id;
                this.getOrder(this.order_id);
            }

            this.form.order_date = moment().format("YYYY-MM-DD");

            if(this.order_status != 'Draft' && this.order_status != '') {
                this.readonly_status = "disabled";
            }
        },
        mounted() {

            $("#loading").hide();
            let app = this;

            app.initProducts();
           // app.initWarehouses();

            app.initCustomers();

            $(".txt_product").select2();

            app.initSaleMen();

            $("#sale_man").select2();

            $("#sale_man").on("select2:select", function(e) {

                var data = e.params.data;
                app.form.sale_man_id = data.id;
            });

            app.initOrders();

            $("#customer_id").select2();
            $("#customer_id").on("select2:select", function(e) {

                var data = e.params.data;
                app.form.customer_id = data.id;

                //get customer's previous balance
                axios.get("/customer_previous_balance/"+data.id).then(({ data }) => (app.form.previous_balance = data.previous_balance));
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
                })
                .on("dp.change", function(e) {
                    var formatedValue = e.date.format("YYYY-MM-DD");
                    //console.log(formatedValue);
                    app.form.order_date = formatedValue;
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
                    app.form.balance_amount = parseInt(app.form.net_total) + parseInt(app.form.tax_amount);
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
                            app.form.balance_amount = parseInt(app.form.net_total) + parseInt(app.form.tax_amount);
                        }   
                    } else {
                      //
                    }
                });
            });
           
        },
        methods: {
            initProducts() {
              axios.get("/order/products/").then(({ data }) => (this.products = data.data));
              console.log(this.products);
              $(".txt_product").select2();
            },

            initOrders() {
               if(this.isEdit) {
                //alert(this.order_id);
                axios.get("/order_products/"+this.order_id).then(({ data }) => (this.order_products = data.order_data));
               } else {
                axios.get("/order_products/").then(({ data }) => (this.order_products = data.order_data));
               }
            },

            initWarehouses() {
              axios.get("/warehouses").then(({ data }) => (this.warehouses = data.data));
              $("#warehouse").select2();
            },

            initSaleMen() {
              axios.get("/sale_men").then(({ data }) => (this.sale_men = data.data));
              $("#sale_man").select2();
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
                        selectbox_id.append('<option value="'+value.pivot.uom_id+'" data-relation="'+uom_relation+'" data-uomqty="'+value.pivot.relation+'" data-productuom = "'+selectbox_id.attr('data-uom')+'" data-productid="'+product_id+'" data-perprice="'+value.pivot.per_warehouse_uom_price+'" data-price="'+value.pivot.product_price+'" >'+value.uom_name+'</option>');
                    }
                });

                $(".txt_uom").select2();
                  
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
                var row=table.insertRow((table.rows.length)-5);
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
                    t2.addEventListener('blur', function(){ app.calTotalAmount(t2); });
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

            getOrder(id) {
              let app = this;
              axios
                .get("/order/" + id)
                .then(function(response) {                
                    app.form.order_date = moment(response.data.order.order_date).format('YYYY-MM-DD');
                    app.ex_products = response.data.order.products;
                    console.log(response.data.order.products);
                    app.form.order_no = response.data.order.order_no;
                    app.form.remark = response.data.order.remark;
                    if(response.data.order.branch != null) {
                        app.user_branch = response.data.order.branch.branch_name;
                    } else {
                        app.user_branch = '';
                    }

                    //for edit permission (save btn)
                    if(app.user_role == 'system' || app.user_id == response.data.order.sale_man_id) {
                        app.isDisabled = false;
                    } else {
                        app.isDisabled = true;
                    }

                    if(response.data.order.sale_man_id == null) {
                        app.form.sale_man_id = '';
                        app.sale_man = '';
                    } else {
                        app.form.sale_man_id = response.data.order.sale_man_id;
                        app.sale_man = response.data.order.sale_man.name;
                    }                    

                    app.order_status = response.data.order.order_status;
                    $("#customer_id").append('<option value="'+response.data.order.customer_id+'" selected>'+response.data.order.customer.cus_name+'</option>');
                    app.form.customer_id = response.data.order.customer_id;

                    if(app.order_status != 'Draft' && app.order_status != '') {
                        app.readonly_status = "disabled";
                    }
                   
                    $.each(app.ex_products, function( key, value ) {
                        app.form.ex_product_pivot.push(value.pivot.id);
                    });

                    //add  products dynamically
                    var subTotal = 0;
                    var row_id = 0;

                    $.each(app.ex_products, function( key, product ) {
                        row_id = row_id+1;                      
                        if(app.user_role != "Country Head" || (app.user_role == "Country Head" && response.data.access_brands.indexOf(product.brand_id) > -1)) {
                            var table=document.getElementById("product_table");
                            var row=table.insertRow((table.rows.length) - 5);
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

                                if(app.order_status != 'Draft' && app.order_status != '') {
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

                                if(app.order_status != 'Draft' && app.order_status != '') {
                                    $(t3).attr('disabled', 'disabled');
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
                                if(product.pivot.is_foc == 1) {
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
                                if(product.pivot.is_foc == 1) {
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

                                if(app.order_status != 'Draft' && app.order_status != '') {
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
                                if(product.pivot.is_foc == 1) {
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
                            if(app.user_role != 'admin' && (app.order_status == 'Draft' || app.order_status == ''))
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

                    app.form.sub_total  = response.data.order.total_amount;
                    app.form.cash_discount  = response.data.order.cash_discount;
                    app.form.net_total     = response.data.order.net_total;
                    app.form.tax           = response.data.order.tax;
                    app.form.tax_amount    = response.data.order.tax_amount;
                    app.form.balance_amount= response.data.order.balance_amount;
                    app.form.previous_balance = response.data.previous_balance

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

            checkQty(obj) { 
                let app = this;
                if(typeof obj.name !== 'undefined') {
                    //For quantity box onBlur Event

                    var row_id = $(obj).closest('tr').attr('id');

                    //var product_id = $(obj).closest('td').prev().find(':selected').val();
                    var product_id = $("#product_"+row_id).find(':selected').val();
                    var transfer_qty = obj.value;
                    //var uom = $(obj).closest('td').next().find(':selected').val(); 
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
                                /***p_uom_val  = $(this).closest('td').next().next().find(':selected').attr('data-uomqty');
                                p_uom_name = $(this).closest('td').next().next().find(':selected').attr('data-productuom');
                                p_qty =  $(this).closest('td').next().find('input').val(); **/

                                p_uom_val  = $("#uom_"+row_no).find(':selected').attr('data-uomqty');
                                p_uom_name = $("#uom_"+row_no).find(':selected').attr('data-productuom');
                                p_qty =  $("#qty_"+row_no).val();

                                if(typeof p_uom_val !== "undefined" && typeof p_uom_name != "undefined" != "" && p_qty != "") {

                                    product_qty = parseInt(product_qty) + (parseInt(p_qty) * parseInt(p_uom_val));
                                }
                            }
                        });

                        //uom_val  = $(obj).closest('td').next().find(':selected').attr('data-uomqty');
                        //uom_name = $(obj).closest('td').next().find(':selected').attr('data-productuom');

                        uom_name = $("#uom_"+row_id).find(':selected').attr('data-productuom');

                        //product_qty = parseInt(product_qty) + (parseInt(transfer_qty) * parseInt(uom_val));

                        var order_qty = app.getSO(product_id);                    

                        var key = this.products.findIndex(x => x.product_id == product_id);
                        //var available_qty = parseInt(this.products[key].in_count) - parseInt(this.products[key].out_count);
                        console.log(this.products);
                        //calculate reserve qty
                        var available_qty = parseInt(this.products[key].in_count)-(parseInt(this.products[key].direct_sale_qty) + parseInt(order_qty) + parseInt(this.products[key].transfer_qty) + parseInt(this.products[key].revise_sale_qty));

                        var min_qty = this.products[key].minimum_qty == null ? 0 : this.products[key].minimum_qty;
                        available_qty = parseInt(available_qty) - parseInt(min_qty);
                        console.log(min_qty+','+this.products[key].in_count+', '+this.products[key].direct_sale_qty+','+order_qty);
                        console.log(product_qty);

                        if(product_qty > available_qty) {
                            
                            swal("Warning!", "Not enough quantity! Availabel quantity is "+available_qty+" "+uom_name+".", "warning");

                            $(obj).focus(); obj.value="";
                        }
                    }
                } else {
                    //For UOM box select Event

                    var product_qty = 0; 
                    var uom_val = "";
                    var uom_name  = "";  
                    var p_uom_val = "";
                    var p_uom_name = "";
                    var p_qty = 0;  

                    var product_id = $(obj).attr('data-productid');
                    //var transfer_qty = $(obj).closest('td').prev().find('input').val();
                     var transfer_qty = $("#qty_"+row_id).val();
                    var uom = obj.value;                    

                    if(product_id != "" && transfer_qty != "" && uom != "") {

                        //calculate same products quantity in product list
                        var row_no = '';
                        $(".txt_product").each(function() {
                            row_no = $(this).closest('tr').attr('id');
                            if(product_id == $(this).val()) {
                                /***p_uom_val  = $(this).closest('td').next().next().find(':selected').attr('data-uomqty');
                                p_uom_name = $(this).closest('td').next().next().find(':selected').attr('data-productuom');
                                p_qty =  $(this).closest('td').next().find('input').val(); ***/

                                p_uom_val  = $("#uom_"+row_no).find(':selected').attr('data-uomqty');
                                p_uom_name = $("#uom_"+row_no).find(':selected').attr('data-productuom');
                                p_qty =  $("#qty_"+row_no).val();

                                if(typeof p_uom_val !== "undefined" && typeof p_uom_name != "undefined" != "" && p_qty != "") {

                                    product_qty = parseInt(product_qty) + (parseInt(p_qty) * parseInt(p_uom_val));
                                }
                            }
                        });

                        //var uom_val  = $(obj).attr('data-uomqty');
                        var uom_name = $(obj).attr('data-productuom');

                        //var product_qty = parseInt(transfer_qty) * parseInt(uom_val);

                        var order_qty = app.getSO(product_id);                    

                        var key = this.products.findIndex(x => x.product_id == product_id);
                        var available_qty = parseInt(this.products[key].in_count)-(parseInt(this.products[key].direct_sale_qty) + parseInt(order_qty) + parseInt(this.products[key].transfer_qty) + parseInt(this.products[key].revise_sale_qty));

                        var min_qty = this.products[key].minimum_qty == null ? 0 : this.products[key].minimum_qty;
                        //var percentage_qty = this.products[key].percentage_qty == null ? 0 : this.products[key].percentage_qty;
                        available_qty = (parseInt(available_qty) - parseInt(order_qty)) - parseInt(min_qty);
                        if(product_qty > available_qty) {

                            /***if(percentage_qty != 0) {
                                available_qty = (available_qty/parseInt(percentage_qty)) * 100;
                                available_qty = Math.round(available_qty)+"%";
                            }***/

                            swal("Warning!", "Not enough quantity! Availabel quantity is "+available_qty+" "+uom_name+".", "warning");

                            /**$(obj).closest('td').prev().find('input').focus();  $(obj).closest('td').prev().find('input').val('');**/

                            $("#qty_"+row_id).focus();
                            $("#qty_"+row_id).val('');
                        }
                    }
                } 

                //claculate total amount
                app.calTotalAmount(obj);
                /**var unit_price = $(obj).closest('td').next().next().next().next().find('input').val();
                var relation_val = $(obj).closest('td').next().find(':selected').attr('data-uomqty');**/

               /*** var actual_rate = $("#actual_rate_"+row_id).val();
                var other_discount = $("#other_discount_"+row_id).val(); 

                if(obj.value != "" && actual_rate != "") {

                    var total_amount = parseInt(obj.value) * parseInt(actual_rate);
                    var discount_amount = 0;
                    if(other_discount != "") {
                        discount_amount = other_discount/100 * total_amount;
                    }
                    
                    total_amount = total_amount - parseInt(discount_amount);

                    $("#total_amount_"+row_id).val(Math.round(total_amount));
                }

                var sub_total = 0;
                for(var i=0; i<document.getElementsByName('product[]').length; i++) {
                    if(document.getElementsByName('total_amount[]')[i].value != "") {
                        sub_total += parseFloat(document.getElementsByName('total_amount[]')[i].value);
                    }
                }

                app.form.sub_total = Math.round(sub_total); ***/
            },   

            getSO(product_id) {
                let app = this;
                //var d=moment("28-02-1999","YYYY-MM-DD");
                //d.isSame("28-02-1999");
                var qty = 0;
                var key = app.order_products.findIndex(x => x.product_id == product_id); 
                if(key != -1) {
                    qty = app.order_products[key].order_qty;
                }

                return qty;
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
                app.form.balance_amount = parseInt(app.form.net_total) + parseInt(app.form.tax_amount);
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
                app.form.balance_amount = parseInt(app.form.net_total) + parseInt(app.form.tax_amount);
            },

            changeTax() {
                let app = this;
                var tax = app.form.tax == '' || app.form.tax == null ? 0 : app.form.tax;
                var tax_amount = parseInt(tax)/100 * parseInt(app.form.net_total);
                app.form.tax_amount = tax_amount;
                app.form.balance_amount = parseInt(app.form.net_total) + parseInt(app.form.tax_amount);
            },

            onSubmit: function(event){

                let app = this;
                app.form.product = [];
                app.form.uom = [];
                app.form.qty = [];
                app.form.unit_price = [];
                app.form.total_amount = [];

                app.form.rate = [];
                app.form.actual_rate = [];
                app.form.discount = [];
                app.form.other_discount = [];

                $("#loading").show();

                if (!this.isEdit) {

                    for(var i=0; i<document.getElementsByName('product[]').length; i++) {

                        app.form.product.push(document.getElementsByName('product[]')[i].value);
                        app.form.uom.push(document.getElementsByName('uom[]')[i].value);
                        app.form.qty.push(document.getElementsByName('qty[]')[i].value);
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
                    //console.log(app.form.total_amount);
                    //console.log(app.form.is_foc); return false;
                    
                    this.form
                      .post("/order")
                      .then(function(data) {
                        if(data.status == "success") {
                            //$("#loading").hide();
                            //reset form data
                            event.target.reset();
                            $(".txt_product").select2();
                            swal({
                                title: "Success!",
                                text: 'Sale Order is saved.',
                                icon: "success",
                                button: true
                            }).then(function() {
                               // $("#loading").show();
                                //location.reload();
                                //app.$router.push("/order/edit/" + data.order_id);
                                var baseurl = window.location.origin;
                                window.open(app.site_path+'/generate_order/'+data.order_id);
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

                    app.form.product_pivot = [];


                    for(var i=0; i<document.getElementsByName('product[]').length; i++) {

                        app.form.product_pivot.push(document.getElementsByName('product[]')[i].options[document.getElementsByName('product[]')[i].options.selectedIndex].dataset.pivotid);

                        app.form.product.push(document.getElementsByName('product[]')[i].value);
                        app.form.uom.push(document.getElementsByName('uom[]')[i].value);
                        app.form.qty.push(document.getElementsByName('qty[]')[i].value);
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
                    //console.log(app.form.ex_product_pivot);
                    //console.log(app.form.product_pivot);

                    //return false;

                    this.form
                      .patch("/order/" + app.order_id)
                      .then(function(data) {
                        if(data.status == "success") {
                            $("#loading").hide();
                            //reset form data
                            event.target.reset();
                            $(".txt_product").select2();

                            swal({
                                title: "Success!",
                                text: 'Sale Order is updated.',
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
                    } else {
                      //
                    }
                });
            },

        },
    }
</script>