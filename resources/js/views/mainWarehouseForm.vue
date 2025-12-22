<template>
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="#!"><i class="feather icon-home"></i></a></li>
                <li class="breadcrumb-item"><a :href="site_path+'/'">Home</a></li>
                <li class="breadcrumb-item"><a :href="site_path+'/inventory'">Inventory</a></li>
                <li class="breadcrumb-item"><router-link tag="span" to="/inventory/main-warehouse" class="font-weight-normal"><a href="#">Main Warehouse</a></router-link></li>
                <li class="breadcrumb-item active" aria-current="page">Main Warehouse Product Entry Form</li>

            </ol>
        </nav>
        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h4 class="mb-0 text-gray-800">Main Warehouse Product Entry Form</h4>
        </div>

        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Entry Details</h6>
            </div>
            <div class="card-body">
                <div class="d-block">
                    <!-- form start -->
                    <form class="form" method="post" @submit.prevent="onSubmit" @keydown="form.errors.clear($event.target.name)">
                        <div class="form-group row" v-if="isEdit && ex_products.length > 0">
                            <label for="staticEmail" class="col-sm-2 mr-0">Entry No.</label>
                            <div class="col-sm-10">
                                <input type="text" readonly class="form-control-plaintext" v-model="form.entry_no">
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-md-4">
                                <label for="warehouse">Warehouse</label>
                                <input type="text" class="form-control" id="warehouse" name="warehouse" v-model="user_warehouse" readonly>
                            </div>
                            <div class="form-group col-md-4">
                                <label for="branch_name">Branch</label>
                                 <input type="text" class="form-control" id="branch_name" name="branch_name"
                                v-model="user_branch" readonly>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-md-4">
                                <label for="entry_date">Date</label>
                                <input type="text" class="form-control datetimepicker" id="entry_date" name="entry_date"
                                v-model="form.entry_date" required>
                            </div>
                            <div class="form-group col-md-4">
                                <label for="reference_no">Reference No.</label>
                                <input type="text" class="form-control" id="reference_no" name="reference_no" v-model="form.reference_no" required>
                            </div>
                        </div>

                        <div class="row mt-4">
                            <div class="col-md-12">
                                <span class="d-none d-sm-inline-block btn-sm btn-primary shadow-sm bg-blue"><i class="fas fa-search-plus text-white"></i> Product Details</span>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12 text-right">
                                <a class='blue-icon' title='Add Product' @click="addProduct()" v-if="user_role == 'office_user' || user_role == 'system'"><i class='fas fa-plus-square' style='font-size: 30px;'></i></a>
                                <div style="display:none;">
                                    <select class="form-control txt_product"
                                        id="product_master" style="min-width:150px;" 
                                    >
                                        <option value="">Select One</option>
                                        <option v-for="product in products" :data-uom="product.uom.uom_name" 
                                        :data-uomid="product.uom.id" :value="product.id" data-pivotid = "0">{{product.product_name}}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-12 table-responsive">
                                <table class="table table-bordered" id="product_table">
                                    <thead class="thead-grey">
                                        <tr>
                                            <th scope="col" style="width:40%">Product Name</th>
                                            <th scope="col" >Warehouse UOM</th>
                                            <th scope="col" >Quantity</th>
                                            <th scope="col" class="text-center"></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <template v-if="isEdit && ex_products.length > 0">
                                            <tr v-for="ex_prod in ex_products" :id="ex_prod.pivot.product_id">
                                                <td style="width:40%">
                                                    <select class="form-control txt_product"
                                                        name="product[]" style="min-width:150px;" readonly
                                                    >
                                                        <option :value="ex_prod.id" :data-pivotid="ex_prod.pivot.id">{{ ex_prod.product_name }}</option>
                                                    </select>
                                                </td>
                                                <td>
                                                    <input type="text" class="form-control txt_uom" name="uom[]" :data-id="ex_prod.uom.id" :value = "ex_prod.uom.uom_name" readonly />
                                                </td>
                                                <td>
                                                    <input type="text" class="form-control num_txt" name="qty[]" :value = "ex_prod.pivot.product_quantity" required />
                                                </td>
                                                <td class="text-center">
                                                    <a class='red-icon' title='Remove' @click="removeProduct(ex_prod.pivot.product_id)" v-if="user_role == 'office_user' || user_role == 'system'"><i class='fas fa-times-circle' style='font-size: 25px;'></i></a>
                                                </td>
                                            </tr>   
                                        </template>
                                        <template v-else>                                        
                                            <tr>
                                                <td style="width:40%">
                                                    <select class="form-control txt_product"
                                                        name="product[]" style="min-width:150px;" required
                                                    >
                                                        <option value="">Select One</option>
                                                        <option v-for="product in products" :data-uom="product.uom.uom_name" 
                                                        :data-uomid="product.uom.id" :value="product.id" 
                                                        data-pivotid = "0">{{product.product_name}}</option>
                                                    </select>
                                                </td>
                                                <td>
                                                    <input type="text" class="form-control txt_uom" name="uom[]" data-id="" readonly />
                                                </td>
                                                <td>
                                                    <input type="text" class="form-control num_txt" name="qty[]" required />
                                                </td>
                                                <td class="text-center" v-if="user_role == 'office_user' || user_role == 'system'">
                                                    <a class='remove-row red-icon' title='Remove'><i class='fas fa-times-circle' style='font-size: 25px;'></i></a>
                                                </td>
                                            </tr>
                                        </template>
                                    </tbody>
                                </table>
                            </div>                         

                        </div>
                        <div class="row">
                            <div class="col-md-12" v-if="user_role == 'office_user' || user_role == 'system'">
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
                entry_date: "",
                product: [],
                uom: [],
                qty: [],
                ex_product_pivot: [],
                product_pivot: [],
                entry_no: '',
                reference_no: '',

              }),
              isEdit: false,
              products: [],
              entry_id: '',
              ex_products: [],  
              user_role: '',  
              user_year: '', 
              user_branch: '',  
              user_warehouse: '', 
              site_path: '',
              storage_path: '',      
            };
        },

        created() {

            this.user_role = document.querySelector("meta[name='user-role']").getAttribute('content');

            this.user_year = document.querySelector("meta[name='user-year-likelink']").getAttribute('content');

            this.user_branch = document.querySelector("meta[name='user-branch']").getAttribute('content');

            this.user_warehouse = document.querySelector("meta[name='user-wh']").getAttribute('content');

            this.site_path = document.querySelector("meta[name='site-path']").getAttribute('content');
            //this.site_path = this.site_path.substring(this.site_path.lastIndexOf('/')+1);
            this.storage_path = document.querySelector("meta[name='storage-path']").getAttribute('content');

            if(this.user_role != "admin" && this.user_role != "office_user" && this.user_role != 'system') {
                var url =  window.location.origin;
                window.location.replace(url);
            }

            if(this.$route.params.id) {
                this.isEdit = true;
                this.entry_id = this.$route.params.id;
                this.getEntry(this.entry_id);
            }
        },
        mounted() {

            $("#loading").show();
            let app = this;

            app.initProducts();

            $(".txt_product").select2();

            $(".txt_product").on("select2:select", function(e) {

                var data = e.params.data;

                var uom = e.target.options[e.target.options.selectedIndex].dataset.uom;
                var uom_id = e.target.options[e.target.options.selectedIndex].dataset.uomid;

                $(this).closest('td').next().find('.txt_uom').val(uom);
                $(this).closest('td').next().find('.txt_uom').attr('data-id',uom_id);

                //auto add new product row
                if($(this).closest('tr').next().length == 0) {
                    app.addProduct();
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
                    var y = new Date().getFullYear();
                    if(app.user_year < y) { 
                      if(app.form.entry_date == app.user_year+"-12-31" || app.form.entry_date == '') {
                        app.form.entry_date = app.user_year+"-12-31";
                      }
                    }
                })
                .on("dp.change", function(e) {
                    var formatedValue = e.date.format("YYYY-MM-DD");
                    //console.log(formatedValue);
                    app.form.entry_date = formatedValue;
                });

            /*$(document).on('click','.add-new',function(evt){
                app.addProduct();
            });*/                


             $(document).on('click','.remove-row',function(evt){
                if(document.getElementsByName('product[]').length <= 1) {                    
                    swal("Warning!", "At least one product must be added!", "warning")
                } else {
                    $(this).parents("tr").remove();
                }
            });
           
        },

        methods: {

            initProducts() {
             let app = this;
             // axios.get("/products").then(({ data }) => (this.products = data.data));
              axios.get("/products").then(function(response) {
                app.products = response.data.data;
                $('#loading').hide();
              });
              $(".txt_product").select2();
              
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
                       option.value = value.id;
                       option.className ="form-control txt_product";
                       $(option).attr('data-uom',value.uom.uom_name);
                       $(option).attr('data-uomid',value.uom.id);
                       $(option).attr('data-pivotid','0');
                       option.text = value.product_name;
                       t1.append(option);
                     });
                    var html = "<option value=''>Select One</option>";
                    $.each(this.products, function(index, value) {
                        html += "<option value='"+value.id+"' class='form-control txt_product' data-uom='"+value.uom.uom_name+"' data-uomid='"+value.uom.id+"' data-pivotid='0'>"+value.product_name+"</option>";
                    });
                    $(t1).html(html);*/

                    var html = $('#product_master').html();
                    $(t1).html(html);
                    cell1.appendChild(t1);
                   

                var cell2=row.insertCell(1);
                var t2=document.createElement("input");
                    t2.type = "text";
                    t2.name = "uom[]";
                    t2.className ="form-control txt_uom";
                    $(t2).attr('readonly', true);
                    $(t2).attr('data-id', '');
                    cell2.appendChild(t2);

                var cell3=row.insertCell(2);
                var t3=document.createElement("input");
                    t3.name = "qty[]";
                    t3.className ="form-control num_txt";
                    $(t3).attr("required", true);
                    cell3.appendChild(t3);

                $(".txt_product").select2();

                $(".txt_product").on("select2:select", function(e) {
                    var data = e.params.data;

                    var uom = e.target.options[e.target.options.selectedIndex].dataset.uom;
                    var uom_id = e.target.options[e.target.options.selectedIndex].dataset.uomid;

                    $(this).closest('td').next().find('.txt_uom').val(uom);
                    $(this).closest('td').next().find('.txt_uom').attr('data-id',uom_id);

                    //auto add new product row
                    if($(this).closest('tr').next().length == 0) {
                        app.addProduct();
                    }
                });

                var cell4=row.insertCell(3);
                cell4.className = "text-center";
                var row_action = "<a class='remove-row red-icon' title='Remove'><i class='fas fa-times-circle' style='font-size: 25px;'></i></a>";
                $(cell4).append(row_action);

                /*var row_action = "<a class='blue-icon add-new' title='Add Product'><i class='fas fa-plus-circle' style='font-size: 25px;'></i></a>&nbsp;&nbsp;<a class='remove-row red-icon' title='Remove'><i class='fas fa-times-circle' style='font-size: 25px;'></i></a>";
                $(cell4).append(row_action);*/

            },

            getEntry(id) {
              let app = this;
              axios
                .get("/mainwarehouse_entry/" + id)
                .then(function(response) {
                    app.form.entry_date = moment(response.data.entry.entry_date).format('YYYY-MM-DD');
                    app.ex_products = response.data.entry.products;
                    app.form.entry_no = response.data.entry.entry_no;
                    app.form.reference_no = response.data.entry.reference_no;
                    app.user_warehouse = response.data.entry.warehouse.warehouse_name;
                    if(response.data.entry.branch != null) {
                        app.user_branch = response.data.entry.branch.branch_name;
                    } else {
                        app.user_branch = '';
                    }
                    $.each(app.ex_products, function( key, value ) {
                        app.form.ex_product_pivot.push(value.pivot.id);
                    });

                })
                .catch(function(error) {
                  // handle error
                  console.log(error);
                })
                .then(function() {
                  // always executed
                });
            },

            onSubmit: function(event){

                let app = this;
                $('#loading').show();

                if (!this.isEdit) {

                    for(var i=0; i<document.getElementsByName('product[]').length; i++) {
                        app.form.product.push(document.getElementsByName('product[]')[i].value);
                        app.form.uom.push(document.getElementsByName('uom[]')[i].getAttribute('data-id'));
                        app.form.qty.push(document.getElementsByName('qty[]')[i].value);
                    }

                    this.form
                      .post("/mainwarehouse_entry")
                      .then(function(data) {
                        console.log(data.data);
                        if(data.status == "success") {
                            //reset form data
                            event.target.reset();
                            $(".txt_product").select2();

                            swal({
                                title: "Success!",
                                text: 'Entry is added.',
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
                        console.log(response);
                        if(response.errors.reference_no){
                            error += response.errors.reference_no[0];
                            error += '\n';
                        }

                        swal("Warning!", error, "warning");

                    });
                } else {
                    //Edit entry details
                    app.form.product_pivot = [];
                    for(var i=0; i<document.getElementsByName('product[]').length; i++) {
                        app.form.product.push(document.getElementsByName('product[]')[i].value);
                        app.form.uom.push(document.getElementsByName('uom[]')[i].getAttribute('data-id'));
                        app.form.qty.push(document.getElementsByName('qty[]')[i].value);
                        app.form.product_pivot.push(document.getElementsByName('product[]')[i].options[document.getElementsByName('product[]')[i].options.selectedIndex].dataset.pivotid);
                    }
                    //console.log(app.form.ex_product_pivot);
                    //console.log(app.form.product_pivot);

                    //return false;

                    this.form
                      .patch("/mainwarehouse_entry/" + app.entry_id)
                      .then(function(data) {
                        if(data.status == "success") {
                            //reset form data
                            event.target.reset();
                            $(".txt_product").select2();

                            swal({
                                title: "Success!",
                                text: 'Entry is updated.',
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
                        console.log(response);
                        var error = '';
                        if(response.errors.reference_no){
                            error += response.errors.reference_no[0];
                            error += '\n';
                        }

                        swal("Warning!", error, "warning");

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
                        if(document.getElementsByName('product[]').length <= 1) { 
                            swal("Warning!", "At least one product must be added!", "warning")
                        } else
                        {
                            $("#"+id).remove();    
                        }
                    } else {
                      //
                    }
                });
            },

        }
    }
</script>