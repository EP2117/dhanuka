<template>
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="#!"><i class="feather icon-home"></i></a></li>
                <li class="breadcrumb-item"><a :href="site_path+'/'">Home</a></li>
                <li class="breadcrumb-item"><a :href="site_path+'/inventory'">Inventory</a></li>
                <li class="breadcrumb-item"><router-link tag="span" to="/inventory/transfer" class="font-weight-normal"><a href="#">Internal Transfer</a></router-link></li>
                <li class="breadcrumb-item active" aria-current="page">Product Transfer Form</li>

            </ol>
        </nav>
        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h4 class="mb-0 text-gray-800">Product Transfer Form</h4>
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
                                <label for="transfer_no">Transfer No.</label>
                                <input type="text" class="form-control" id="transfer_no" name="transfer_no" v-model="form.transfer_no" readonly>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-md-4">
                                <label for="branch_name">Branch</label>
                                 <input type="text" class="form-control" id="branch_name" name="branch_name"
                                v-model="user_branch" readonly>
                            </div>
                            <div class="form-group col-md-4">
                                <label for="trasfer_date">Date</label>
                                <input type="text" class="form-control datetimepicker" id="transfer_date" name="transfer_date"
                                v-model="form.transfer_date" required>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="form-group col-md-4">
                                <label for="from_warehouse">From Warehouse</label>
                                 <input type="text" class="form-control" id="from_warehouse" name="from_warehouse"
                                v-model="user_warehouse" readonly>
                            </div>
                            <div class="form-group col-md-4">
                                <label for="to_warehouse">To Warehouse</label>
                                <select id="to_warehouse" class="form-control"
                                    name="to_warehouse" v-model="form.to_warehouse" style="width:100%" required
                                >
                                    <option value="">Select One</option>
                                    <option v-for="warehouse in warehouses" v-if="warehouse.warehouse_name != user_warehouse" :value="warehouse.id"  >{{warehouse.warehouse_name}}</option>
                                </select>
                            </div>
                        </div>

                        <div class="row mt-4">
                            <div class="col-md-12">
                                <span class="d-none d-sm-inline-block btn-sm btn-primary shadow-sm bg-blue"><i class="fas fa-search-plus text-white"></i> Product Details</span>
                            </div>
                        </div>

                        <div class="row mt-0">
                            <div class="col-md-12 text-right mt-0">
                                <a class='blue-icon' title='Add Product' @click="addProduct()"><i class='fas fa-plus-square' style='font-size: 30px;' v-if="user_role != 'admin'"></i></a>
                                <div style="display:none;">
                                    <select class="form-control txt_product"
                                        id="txt_product" style="min-width:150px;"
                                    >
                                        <option value="">Select One</option>
                                        <option v-for="product in products" :data-uom="product.uom_name" 
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
                                            <th scope="col" >Relation</th>
                                            <th scope="col" class="text-center"></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <template v-if="isEdit && ex_products.length > 0">
                                        </template>
                                        <template v-else>                                        
                                            <tr>
                                                <td>
                                                    <select class="form-control txt_product"
                                                        name="product[]" id="txt_product" style="min-width:150px;" required
                                                    >
                                                        <option value="">Select One</option>
                                                        <option v-for="product in products" :data-uom="product.uom_name" 
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
                                                    <a class='remove-row red-icon' title='Remove' v-if="user_role != 'admin'"><i class='fas fa-times-circle' style='font-size: 25px;'></i></a>
                                                </td>
                                            </tr>
                                        </template>
                                    </tbody>
                                </table>
                            </div>                         

                        </div>
                        <div class="row">
                            <div class="col-md-12" v-if="user_role != 'admin'">
                                <input type="submit" class="btn btn-primary btn-sm" value="Save Entry">
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
                transfer_date: "",
                from_warehouse: "",
                to_warehouse: "",
                product: [],
                uom: [],
                qty: [],
                ex_product_pivot: [],
                product_pivot: [],
                transfer_no: '',
                uom_id: "",
              }),
              isEdit: false,
              products: [],
              transfer_id: '',
              ex_products: [],
              user_warehouse: '',
              warehouses: [],
              selling_uoms: [],
              user_role: '',
              user_year: '',
              user_branch: '',
              site_path: '',
              storage_path: '',
            };
        },

        created() {

            this.user_role = document.querySelector("meta[name='user-role']").getAttribute('content');

            this.user_year = document.querySelector("meta[name='user-year-likelink']").getAttribute('content');

            this.user_branch = document.querySelector("meta[name='user-branch']").getAttribute('content');

            this.site_path = document.querySelector("meta[name='site-path']").getAttribute('content');
            //this.site_path = this.site_path.substring(this.site_path.lastIndexOf('/')+1);
            this.storage_path = document.querySelector("meta[name='storage-path']").getAttribute('content');

            if(this.user_role == "office_order_user") {
                var url =  window.location.origin;
                window.location.replace(url);
            }

            this.user_warehouse = document.querySelector("meta[name='user-wh']").getAttribute('content');
            if(this.$route.params.id) {
                this.isEdit = true;
                this.transfer_id = this.$route.params.id;
                this.getTransfer(this.transfer_id);
            } else {
                //this.getMaxId();
            }
        },
        mounted() {

            $("#loading").hide();
            let app = this;

            app.initProducts();
            app.initWarehouses();

            $("#to_warehouse").select2();

            $(".txt_product").select2();

            $(".txt_product").on("select2:select", function(e) {

                var data = e.params.data;

                app.selling_uoms = [];

               var uom = e.target.options[e.target.options.selectedIndex].dataset.uom;
               var uom_id = e.target.options[e.target.options.selectedIndex].dataset.uomid;

                //$(this).closest('td').next().find('.txt_uom').val(uom);
                //$(this).closest('td').next().find('.txt_uom').attr('data-id',uom_id);

                $(this).closest('td').next().next().find('.txt_uom').attr('data-uom',uom);

                //auto add new product row
                if($(this).closest('tr').next().length == 0) {
                    app.addProduct();
                }

                var selectbox_id = $(this).closest('td').next().next().find('.txt_uom');

                selectbox_id.find('option').remove();

                //add pre-defined product uom 

                if(selectbox_id.find('option[value="'+uom_id+'"]').text() == "") {
                    selectbox_id.append('<option value="">Select One</option><option value="'+uom_id+'" data-relation="" data-uomqty = "1" data-productuom = "'+uom+'" data-productid="'+data.id+'">'+uom+'</option>'); 
                }
                $(".txt_uom").select2();

                app.getSellingUomByProduct(selectbox_id, data.id);
            });

            $(".txt_uom").select2();

            $(".txt_uom").on("select2:select", function(e) {
                app.checkQty(e.target.options[e.target.options.selectedIndex]);
                var uom_relation = e.target.options[e.target.options.selectedIndex].dataset.relation;

                $(this).closest('td').next().find('.txt_relation').val(uom_relation);
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
                    var y = new Date().getFullYear();
                    if(app.user_year < y) { 
                      if(app.form.transfer_date == app.user_year+"-12-31" ||  app.form.transfer_date == '') {
                         app.form.transfer_date = app.user_year+"-12-31";
                      }
                    }
                })
                .on("dp.change", function(e) {
                    var formatedValue = e.date.format("YYYY-MM-DD");
                    //console.log(formatedValue);
                    app.form.transfer_date = formatedValue;
                });

            /*$(document).on('click','.add-new',function(evt){
                app.addProduct();
            });*/                


            $(document).on('click','.remove-row',function(evt) {
                if(document.getElementsByName('product[]').length <= 1) {                    
                    swal("Warning!", "At least one product must be added!", "warning")
                } else {
                    $(this).parents("tr").remove();
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
                        }   
                    } else {
                      //
                    }
                });
            });
           
        },

        methods: {
            /*** initProducts() {
              axios.get("/productsByUserWarehouse").then(({ data }) => (this.products = data.data));
              $(".txt_product").select2();
            }, ***/

            initProducts() {
              let app = this;
              if(app.$route.params.id) {
                axios.get("/productsByUserWarehouse/transfer_edit/"+ app.$route.params.id).then(({ data }) => (app.products = data.data));
              } else {
                axios.get("/productsByUserWarehouse/create/null").then(({ data }) => (app.products = data.data));
              }
              $(".txt_product").select2();
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
                        selectbox_id.append('<option value="'+value.pivot.uom_id+'" data-relation="'+uom_relation+'" data-uomqty="'+value.pivot.relation+'" data-productuom = "'+selectbox_id.attr('data-uom')+'" data-productid="'+product_id+'">'+value.uom_name+'</option>');
                    }
                });

                $(".txt_uom").select2();
                  
              });
            },

            getMaxId() {
                let app = this;
                axios.get("/transfer/maxid").then(function(response) {
                    var maxid = (response.data.max_id + 1).toString();
                    app.form.transfer_no = maxid.padStart(5, "0");
                });
            },

            addProduct() {
                let app = this;
                var table=document.getElementById("product_table");
                var row=table.insertRow(table.rows.length);
                var cell1=row.insertCell(0);

                var t1=document.createElement("select");
                    t1.name = "product[]";
                    t1.className = "form_control txt_product";
                    t1.style = "min-width:150px;";
                    $(t1).attr("required", true);

                    var option = document.createElement("option");
                    option.value = '';
                    option.text = "Select One";
                    t1.append(option);

                    /*$.each(this.products, function(index, value) {
                       var option = document.createElement("option");
                       option.value = value.product_id;
                       option.className ="form-control txt_product";
                       $(option).attr('data-uom',value.uom_name);
                       $(option).attr('data-uomid',value.uom_id);
                       $(option).attr('data-pivotid','0');
                       option.text = value.product_name;
                       t1.append(option);
                     });*/

                    var html = $('#txt_product').html();
                    $(t1).html(html);                  
                    cell1.appendChild(t1);

                var cell2=row.insertCell(1);
                var t2=document.createElement("input");
                    t2.name = "qty[]";
                    t2.style = "width:100px;";
                    t2.className ="form-control num_txt";
                    $(t2).attr("required", true);
                    t2.addEventListener('blur', function(){ app.checkQty(t2); });
                    cell2.appendChild(t2);
                   
                var cell3=row.insertCell(2);

                var t3=document.createElement("select");
                    t3.name = "uom[]";
                    t3.className = "form_control txt_uom";
                    t3.style = "min-width:150px;";
                    $(t3).attr("required", true);
                    t3.addEventListener('blur', function(){ app.checkQty(t3); });

                    var option = document.createElement("option");
                    option.value = '';
                    option.text = "Select One";
                    t3.append(option);

                 cell3.appendChild(t3);


                var cell4=row.insertCell(3);
                var t4=document.createElement("input");
                    t4.type = "text";
                    t4.name = "relation[]";
                    t4.style = "min-width:200px;";
                    t4.className ="form-control txt_relation";
                    $(t4).attr('readonly', true);
                    cell4.appendChild(t4);

                

                $(".txt_product").select2();

                $(".txt_uom").select2();

                $(".txt_product").on("select2:select", function(e) {

                    var data = e.params.data;

                    app.selling_uoms = [];

                   var uom = e.target.options[e.target.options.selectedIndex].dataset.uom;
                   var uom_id = e.target.options[e.target.options.selectedIndex].dataset.uomid;

                    //$(this).closest('td').next().find('.txt_uom').val(uom);
                    //$(this).closest('td').next().find('.txt_uom').attr('data-id',uom_id);

                    $(this).closest('td').next().next().find('.txt_uom').attr('data-uom',uom);

                    //auto add new product row
                    if($(this).closest('tr').next().length == 0) {
                        app.addProduct();
                    }

                    var selectbox_id = $(this).closest('td').next().next().find('.txt_uom');

                    selectbox_id.find('option').remove();

                    //add pre-defined product uom 
                    if(selectbox_id.find('option[value="'+uom_id+'"]').text() == "") {
                        selectbox_id.append('<option value="">Select One</option><option value="'+uom_id+'" data-relation="" data-uomqty="1" data-productuom = "'+uom+'" data-productid="'+data.id+'">'+uom+'</option>'); 
                    }
                    $(".txt_uom").select2();

                    app.getSellingUomByProduct(selectbox_id, data.id);
                });

                $(".txt_uom").select2();

                $(".txt_uom").on("select2:select", function(e) {
                    app.checkQty(e.target.options[e.target.options.selectedIndex]);
                    var uom_relation = e.target.options[e.target.options.selectedIndex].dataset.relation;

                    $(this).closest('td').next().find('.txt_relation').val(uom_relation);
                });

                var cell5=row.insertCell(4);
                cell5.className = "text-center";
                var row_action = "<a class='remove-row red-icon' title='Remove'><i class='fas fa-times-circle' style='font-size: 25px;'></i></a>";
                $(cell5).append(row_action);

            },

            getTransfer(id) {
              let app = this;
              $("#loading").show();
              axios
                .get("/transfer/" + id)
                .then(function(response) {  
                    $("#loading").hide();              
                    app.form.transfer_date = moment(response.data.transfer.transfer_date).format('YYYY-MM-DD');
                    app.ex_products = response.data.transfer.products;
                    console.log(response.data.transfer.products);
                    app.form.transfer_no = response.data.transfer.transfer_no;
                    app.user_warehouse = response.data.transfer.from_warehouse.warehouse_name;
                    app.form.to_warehouse = response.data.transfer.to_warehouse_id;

                    if(response.data.transfer.branch != null) {
                        app.user_branch = response.data.transfer.branch.branch_name;
                    } else {
                        app.user_branch = '';
                    }

                    $.each(app.ex_products, function( key, value ) {
                        app.form.ex_product_pivot.push(value.pivot.id);
                    });

                    //add transfer products dynamically
                    $.each(app.ex_products, function( key, product ) {
                        var table=document.getElementById("product_table");
                        var row=table.insertRow(table.rows.length);
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
                           $(option).attr('data-pivotid',product.pivot.id);
                           option.text = product.product_name;
                           t1.append(option);

                            cell1.appendChild(t1);

                        var cell2=row.insertCell(1);
                        var t2=document.createElement("input");
                            t2.name = "qty[]";
                            t2.value = product.pivot.product_quantity;
                            t2.style = "width:100px;";
                            t2.className ="form-control num_txt";
                            $(t2).attr("required", true);
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
                            t3.append(option);

                            var option = document.createElement("option");
                            option.value = product.uom_id;
                            option.text = product.uom.uom_name;
                            $(option).attr("data-relation","");
                            $(option).attr("data-uomqty","1");
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
                                $(option).attr("data-productuom",product.uom.uom_name);
                                $(option).attr("data-productid", product.id);
                                t3.append(option);
                            });

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

                        

                        $(".txt_product").select2();

                        $(".txt_uom").select2();

                        $(".txt_product").on("select2:select", function(e) {

                            var data = e.params.data;

                            app.selling_uoms = [];

                           var uom = e.target.options[e.target.options.selectedIndex].dataset.uom;
                           var uom_id = e.target.options[e.target.options.selectedIndex].dataset.uomid;

                            //$(this).closest('td').next().find('.txt_uom').val(uom);
                            //$(this).closest('td').next().find('.txt_uom').attr('data-id',uom_id);

                            $(this).closest('td').next().next().find('.txt_uom').attr('data-uom',uom);

                            //auto add new product row
                            if($(this).closest('tr').next().length == 0) {
                                app.addProduct();
                            }

                            var selectbox_id = $(this).closest('td').next().next().find('.txt_uom');

                            selectbox_id.find('option').remove();

                            //add pre-defined product uom 
                            if(selectbox_id.find('option[value="'+uom_id+'"]').text() == "") {
                                selectbox_id.append('<option value="">Select One</option><option value="'+uom_id+'" data-relation="" data-uomqty="1" data-productuom = "'+uom+'" data-productid="'+data.id+'">'+uom+'</option>'); 
                            }
                            $(".txt_uom").select2();

                            app.getSellingUomByProduct(selectbox_id, data.id);
                        });

                        $(".txt_uom").select2();

                        $(".txt_uom").on("select2:select", function(e) {

                            app.checkQty(e.target.options[e.target.options.selectedIndex]);

                            var uom_relation = e.target.options[e.target.options.selectedIndex].dataset.relation;

                            $(this).closest('td').next().find('.txt_relation').val(uom_relation);
                        });

                        var cell5=row.insertCell(4);
                        cell5.className = "text-center";
                        if(app.user_role != 'admin') {
                        var row_action = "<a class='remove-exrow red-icon' title='Remove'><i class='fas fa-times-circle' style='font-size: 25px;'></i></a>";
                        }
                        $(cell5).append(row_action);
                    });


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

                       // var uom_val  = $(obj).closest('td').next().find(':selected').attr('data-uomqty');
                        uom_name = $(obj).closest('td').next().find(':selected').attr('data-productuom');

                       // var product_qty = parseInt(transfer_qty) * parseInt(uom_val);
                                                
                        var key = this.products.findIndex(x => x.product_id == product_id);
                        var available_qty = parseInt(this.products[key].in_count) - parseInt(this.products[key].out_count);
                        if(product_qty > available_qty) {

                            swal("Warning!", "Not enough quantity! Availabel quantity is "+available_qty+" "+uom_name+".", "warning");

                            $(obj).focus(); obj.value="";
                        }
                    }
                } else {

                    //For UOM box select Event

                    var product_id = $(obj).attr('data-productid');
                    var transfer_qty = $(obj).closest('td').prev().find('input').val();
                    var uom = obj.value;   

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
                       // var uom_val  = $(obj).attr('data-uomqty');
                        uom_name = $(obj).attr('data-productuom');

                        //var product_qty = parseInt(transfer_qty) * parseInt(uom_val);

                        var key = this.products.findIndex(x => x.product_id == product_id);
                        var available_qty = parseInt(this.products[key].in_count) - parseInt(this.products[key].out_count);
                        if(product_qty > available_qty) {

                            swal("Warning!", "Not enough quantity! Availabel quantity is "+available_qty+" "+uom_name+".", "warning");

                            $(obj).closest('td').prev().find('input').focus();  $(obj).closest('td').prev().find('input').val('');
                        }
                    }
                }
            },

            onSubmit: function(event){

                let app = this;
                $("#loading").show();
                app.form.to_warehouse = $("#to_warehouse").val();

                if (!this.isEdit) {

                    for(var i=0; i<document.getElementsByName('product[]').length; i++) {
                        app.form.product.push(document.getElementsByName('product[]')[i].value);
                        app.form.uom.push(document.getElementsByName('uom[]')[i].value);
                        app.form.qty.push(document.getElementsByName('qty[]')[i].value);
                    }

                   // console.log(app.form.uom); return false;

                    this.form
                      .post("/transfer")
                      .then(function(data) {
                        console.log(data.data);
                        if(data.status == "success") {
                            $("#loading").hide();
                            //reset form data
                            event.target.reset();
                            $(".txt_product").select2();

                            swal({
                                title: "Success!",
                                text: 'Transfer is added.',
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

                    app.form.product_pivot = [];

                    for(var i=0; i<document.getElementsByName('product[]').length; i++) {
                        app.form.product.push(document.getElementsByName('product[]')[i].value);
                        app.form.uom.push(document.getElementsByName('uom[]')[i].value);
                        app.form.qty.push(document.getElementsByName('qty[]')[i].value);

                        app.form.product_pivot.push(document.getElementsByName('product[]')[i].options[document.getElementsByName('product[]')[i].options.selectedIndex].dataset.pivotid);
                    }
                    //console.log(app.form.ex_product_pivot);
                    //console.log(app.form.product_pivot);

                    //return false;

                    this.form
                      .patch("/transfer/" + app.transfer_id)
                      .then(function(data) {
                        if(data.status == "success") {
                            //reset form data
                            event.target.reset();
                            $(".txt_product").select2();

                            swal({
                                title: "Success!",
                                text: 'Transfer is updated.',
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
                $("#loading").show();
                swal({
                    title: "Are you sure?",
                    text: "Once deleted, you will not be able to recover!",
                    icon: "warning",
                    buttons: true,
                    dangerMode: true
                    }).then(willDelete => {
                    if (willDelete) {
                        $("#"+id).remove(); 
                        $("#loading").hide();   
                    } else {
                      //
                    }
                });
            },

        }
    }
</script>