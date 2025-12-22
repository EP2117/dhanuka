<template>
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="#!"><i class="feather icon-home"></i></a></li>
                <li class="breadcrumb-item"><a :href="site_path+'/'">Home</a></li>
                <li class="breadcrumb-item"><a :href="site_path+'/inventory'">Inventory</a></li>
                <li class="breadcrumb-item"><router-link tag="span" to="/inventory/receive" class="font-weight-normal"><a href="#">Internal Receive</a></router-link></li>
                <li class="breadcrumb-item active" aria-current="page">Product Transfer Detail</li>

            </ol>
        </nav>
        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h4 class="mb-0 text-gray-800">Product Transfer Detail</h4>
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
                                v-model="form.transfer_date" readonly>
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
                                <input type="text" class="form-control" id="to_warehouse" name="to_warehouse"
                                v-model="form.to_warehouse" readonly>
                            </div>
                        </div>

                        <div class="row mt-4">
                            <div class="col-md-12">
                                <span class="d-none d-sm-inline-block btn-sm btn-primary shadow-sm bg-blue"><i class="fas fa-search-plus text-white"></i> Product Details</span>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-md-12 table-responsive">
                                <table class="table table-bordered" id="product_table">
                                    <thead class="thead-grey">
                                        <tr>
                                            <th scope="col" style="width:40%">Product Name</th>
                                            <th scope="col" >Quantity</th>
                                            <th scope="col" >UOM</th>
                                            <th scope="col" >Relation</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
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
                user_branch: "",
              }),
              isEdit: false,
              products: [],
              transfer_id: '',
              ex_products: [],
              user_warehouse: '',
              warehouses: [],
              selling_uoms: [],
              site_path: '',
              storage_path: '',
            };
        },

        created() {
            this.user_warehouse = document.querySelector("meta[name='user-wh']").getAttribute('content');

            this.site_path = document.querySelector("meta[name='site-path']").getAttribute('content');
            //this.site_path = this.site_path.substring(this.site_path.lastIndexOf('/')+1);
            this.storage_path = document.querySelector("meta[name='storage-path']").getAttribute('content');
            
            if(this.$route.params.id) {
                this.isEdit = true;
                this.transfer_id = this.$route.params.id;
                this.getTransfer(this.transfer_id);
            } 
        },
        mounted() {

            $("#loading").hide();
            let app = this;
            //app.initWarehouses();
           
        },

        methods: {
            initWarehouses() {
              axios.get("/warehouses").then(({ data }) => (this.warehouses = data.data));
              $("#to_warehouse").select2();
            },


            getTransfer(id) {
              let app = this;
              axios
                .get("/transfer/" + id)
                .then(function(response) {                
                    app.form.transfer_date = moment(response.data.transfer.transfer_date).format('YYYY-MM-DD');
                    app.ex_products = response.data.transfer.products;
                    console.log(response.data.transfer.products);
                    app.form.transfer_no = response.data.transfer.transfer_no;
                    app.form.to_warehouse = response.data.transfer.to_warehouse.warehouse_name;
                    app.user_warehouse = response.data.transfer.from_warehouse.warehouse_name;

                    if(response.data.transfer.branch != null) {
                        app.user_branch = response.data.transfer.branch.branch_name;
                    } else {
                        app.user_branch = '';
                    }

                    //add transfer products dynamically
                    $.each(app.ex_products, function( key, product ) {
                        var table=document.getElementById("product_table");
                        var row=table.insertRow(table.rows.length);
                        var cell1=row.insertCell(0);

                        var t1=document.createElement("select");
                            t1.name = "product[]";
                            t1.className = "form-control";
                            t1.style = "min-width:150px;";
                            $(t1).attr("required", true);
                            $(t1).attr('readonly', true);

                           var option = document.createElement("option");
                           option.value = product.id;
                           option.className ="form-control";
                           $(option).attr('data-uom',product.uom.uom_name);
                           $(option).attr('data-uomid',product.uom.uom_id);
                           //$(option).attr('data-pivotid','0');
                           option.text = product.product_name;
                           t1.append(option);

                            cell1.appendChild(t1);

                        var cell2=row.insertCell(1);
                        var t2=document.createElement("input");
                            t2.name = "qty[]";
                            t2.value = product.pivot.product_quantity;
                            t2.style = "width:100px;";
                            t2.className ="form-control num_txt";
                            $(t2).attr('readonly', true);
                            t2.addEventListener('blur', function(){ app.checkQty(t2); });
                            cell2.appendChild(t2);
                           
                        var cell3=row.insertCell(2);

                        var t3=document.createElement("select");
                            t3.name = "uom[]";
                            t3.className = "form-control txt_uom";
                            t3.style = "width:150px;";
                            $(t3).attr('readonly', true);

                            var option = document.createElement("option");
                            option.value = '';
                            option.text = "Select One";
                            $(option).attr('data-relation',"");
                            t3.append(option);

                            var option = document.createElement("option");
                            option.value = product.uom_id;
                            option.text = product.uom.uom_name;
                            $(option).attr("data-relation","");
                            if(product.pivot.uom_id == product.uom_id) {
                                option.selected = "selected";
                            }
                            t3.append(option);

                            var relation_val = "";

                            $.each(product.selling_uoms, function( key, suom ) {
                                var option = document.createElement("option");
                                option.value = suom.pivot.uom_id;
                                option.text = suom.uom_name;
                                var uom_relation = "1 "+product.uom.uom_name+" = "+ suom.pivot.relation +" "+suom.uom_name;
                                if(product.pivot.uom_id == suom.pivot.uom_id) {
                                    option.selected = "selected";
                                    relation_val = "1 "+product.uom.uom_name+" = "+ suom.pivot.relation +" "+suom.uom_name;
                                }
                                $(option).attr("data-relation",uom_relation);
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
                            t4.className ="form-control txt_relation";
                            $(t4).attr('readonly', true);
                            cell4.appendChild(t4);
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
        }
    }
</script>