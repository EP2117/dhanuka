<template>
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="#!"><i class="feather icon-home"></i></a></li>
                <li class="breadcrumb-item"><a :href="site_path+'/'">Home</a></li>
                <li class="breadcrumb-item"><a :href="site_path+'/master'">Master</a></li>
                <li class="breadcrumb-item"><router-link tag="span" to="/product" class="font-weight-normal"><a href="#">Product</a></router-link></li>
                <li class="breadcrumb-item active" aria-current="page">Product Form</li>

            </ol>
        </nav>
        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h4 class="mb-0 text-gray-800" v-if="!isEdit">Create New Product</h4>
            <h4 class="mb-0 text-gray-800" v-else>Edit Product</h4>

             <a onClick="history.go(-1);" class="btn btn-primary btn text-white text-right" value="Back"><i class="fas fa-angle-double-left"></i> Back</a>
        </div>

        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Product Details</h6>
            </div>
            <div class="card-body">
                <div class="d-block">
                    <!-- form start -->
                    <form class="form" method="post" @submit.prevent="onSubmit" @keydown="form.errors.clear($event.target.name)">

                        <div class="form-group row">
                            <label class="col-lg-3 text-right col-form-label form-control-label">Product Name</label>
                            <div class="col-lg-6 col-md-offset-2">
                                <input class="form-control" type="text"
                                    id="product_name" name="product_name"
                                    v-model="form.product_name" required >
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-lg-3 text-right col-form-label form-control-label">Product Code Type</label>
                            <div class="col-lg-6">
                                <select id="product_code_type_id" class="form-control form-control-alternative"
                                         v-model="form.product_code_type" style="width:100%" @change="codeType()">
                                    <option value="">Select One</option>
                                    <option value="manual">Manual</option>
                                    <option value="automatic">Automatic</option>
<!--                                    <option v-for="brand in brands" :value="brand.id">{{brand.brand_name}}</option>-->
                                </select>
                            </div>
                        </div>
                        <div class="form-group row" id="product_code_div">
                            <label class="col-lg-3 col-form-label text-right form-control-label">Product Code</label>
                            <div class="col-lg-6">
                                <input class="form-control" type="text"
                                    id="product_code" name="product_code"
                                    v-model="form.product_code"  v-on:blur="checkProductCode">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-lg-3 text-right col-form-label form-control-label">Brand</label>
                            <div class="col-lg-6">
                                <select id="brand_id" class="form-control form-control-alternative"
                                    name="brand_id" v-model="form.brand_id" style="width:100%"
                                >
                                    <option value="">Select One</option>
                                    <option v-for="brand in brands" :value="brand.id">{{brand.brand_name}}</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-lg-3 text-right col-form-label form-control-label">Category</label>
                            <div class="col-lg-6">
                                <select id="category_id" class="form-control"
                                    name="category_id" v-model="form.category_id" style="width:100%">
                                    <option value="">Select One</option>
                                    <option v-for="cat in categories" :value="cat.id">{{cat.category_name}}</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-lg-3 text-right col-form-label form-control-label">Warehouse UOM</label>
                            <div class="col-lg-6">
                                <select id="uom_id" class="form-control"
                                    name="uom_id" v-model="form.uom_id" style="width:100%"
                                >
                                    <option value="">Select One</option>
                                    <option v-for="uom in uoms" :value="uom.id" :data-uom="uom.uom_name" >{{uom.uom_name}}</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row ">
                            <label class="col-lg-3 text-right col-form-label form-control-label">Warehouse UOM Selling Price</label>
                            <div class="col-lg-6">
                                <input class="form-control" type="text"
                                       v-model="form.selling_price" required >
                            </div>
                        </div>
                        <div class="form-group row ">
                            <label class="col-lg-3 text-right col-form-label form-control-label">Cost Price</label>
                            <div class="col-lg-6">
                                <input class="form-control" type="text"
                                       v-model="form.cost_price" readonly >
                            </div>
                        </div>
                        <div class="form-group row" >
                            <label class="col-lg-3 col-form-label text-right form-control-label">Warehouse UMO Purchase Price</label>
                            <div class="col-lg-6" v-if="!isEdit">
                                <input class="form-control" type="text"
                                       v-model="form.purchase_price" required >
                            </div>
                            <div class="col-lg-6" v-else>
                                <input class="form-control" type="text"
                                       v-model="form.purchase_price" required disabled>
                            </div>
                        </div>
<!--                        <div class="form-group row">-->
<!--                            <label class="col-lg-3 text-right col-form-label form-control-label">Selling UOM</label>-->
<!--                            <div class="col-lg-6">-->
<!--                                <select multiple class="form-control selling_uom_id"-->
<!--                                    name="selling_uom_id[]" v-model="form.selling_uom_id" :required="isRequired" style="width:100%"-->
<!--                                >-->
<!--                                    <option value="">Select one</option>-->
<!--                                    <option v-for="uom in uoms" :value="uom.id" :data-uom="uom.uom_name" >{{uom.uom_name}}</option>-->
<!--                                    &lt;!&ndash;<option v-for="uom in uoms" :value="uom.id" :data-uom="uom.uom_name" v-if="selected_selling_uom.indexOf(uom.id) == -1" >{{uom.uom_name}}</option>&ndash;&gt;-->
<!--                                </select>-->
<!--                            </div>-->
<!--                        </div>-->
<!--                        <div class="form-group row">-->
<!--                            <label class="col-lg-3 col-form-label text-right form-control-label">Relation</label>-->
<!--                            <div class="col-lg-9 relation">-->
<!--                            </div>-->
<!--                        </div>-->
                        <div class="form-group row">
                            <label class="col-lg-3 col-form-label text-right form-control-label">Minimum QTY</label>
                            <div class="col-lg-6">
                                <input class="form-control num_txt" type="text"
                                    id="min_qty" name="min_qty"
                                    v-model="form.min_qty" required >
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-lg-3 col-form-label text-right form-control-label">Reorder Level</label>
                            <div class="col-lg-6">
                                <input class="form-control num_txt" type="text"
                                       id="reorder_level"
                                       v-model="form.reorder_level" required >
                            </div>
                        </div>
                        <!--<div class="form-group row">
                            <label class="col-lg-3 col-form-label text-right form-control-label">Image Upload</label>
                            <div class="col-lg-6">
                                <form action="/product/image/upload" class="dropzone" enctype="multipart/form-data" id="frmTarget">
                                  <input type="hidden" name="_token" :value="csrf">
                                  <input type="hidden" name="hid_product_id" id="hid_product_id">
                                  <div class="fallback">
                                    <input name="file" id="file" type="file" multiple>
                                  </div>
                              </form>
                            </div>
                        </div>-->

                        <div class="form-group row" v-if="isEdit && photos.length > 0">
                            <label class="col-lg-3 col-form-label text-right form-control-label">&nbsp; </label>
                            <div class="col-lg-9">
                                <span style="position:relative; float:left; display:table; margin-left:10px;" v-for="image,k in photos">
                                    <img :src="image.photo" width="130" class="img-thumbnail" />
                                        <br>
                                        <button
                                          type="button"
                                          rel="tooltip"
                                          class="btn btn-danger btn-sm"
                                          style="position: absolute;right: 0;top: 0;margin: 0px;padding:0px;"
                                          @click="remove_this(image.id)"
                                        >
                                          <small>Remove</small>
                                        </button>
                                </span>
                            </div>
                        </div>

                        <div class="form-group row text-right">
                            <label class="col-lg-3 col-form-label form-control-label"></label>
                            <div class="col-lg-6">
                                <input type="reset" class="btn btn-secondary btn-sm" value="Cancel" v-if="!isEdit">
                                <input type="button" onClick="location.reload()" class="btn btn-secondary btn-sm" value="Cancel" v-if="isEdit">
                                <input type="submit" class="btn btn-primary btn-sm" value="Save Changes">
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
    import warehouse from "./warehouse";

    export default {
        data() {
            return {
                form: new Form({
                    product_name: "",
                    product_code: "",
                    min_qty: '',
                    product_code_type:'',
                    selling_price:'',
                    purchase_price:'',
                    brand_id: "",
                    category_id: "",
                    uom_id: "",
                    reorder_level:'',
                    selling_uom_id: [],
                    selected_selling_uom: [],
                    product_price: "",
                    retail1_price: "",
                    retail2_price: "",
                    wholesale_price: "",
                    uom_relations: [],
                    uom_prices: [],
                    uom_retail1_prices: [],
                    uom_retail2_prices: [],
                    uom_wholesale_prices: [],
                    uom_purchase_prices: [],
                    uom_per_prices: [],
                    percentage_qty: '',
                }),
                brands: [],
                uoms: [],
                categories: [],
                isEdit: false,
                isRequired: false,
                product_id: '',
                selected_selling_uom: [],
                temp_selling_uom: [],
                user_role: '',
                site_path: '',
                storage_path: '',
                check_disable:true,
                csrf: '',
                dz: '',
                file_count: 0,
                mydz: '',
                photos: [],
            };
        },

        created() {
            this.user_role = document.querySelector("meta[name='user-role']").getAttribute('content');
            this.csrf = document.head.querySelector('meta[name="csrf-token"]').content;
            this.site_path = document.querySelector("meta[name='site-path']").getAttribute('content');
            //this.site_path = this.site_path.substring(this.site_path.lastIndexOf('/')+1);
            this.storage_path = document.querySelector("meta[name='storage-path']").getAttribute('content');
            if(this.user_role != "admin" && this.user_role != "system" && this.user_role != "office_user") {
                var url =  window.location.origin;
                window.location.replace(url);
            }
            if(this.$route.params.id) {
                this.isEdit = true;
                this.product_id = this.$route.params.id;
                this.getProduct(this.product_id);
            } else {
                this.initCategories();
            }

            //console.log(this.isEdit);
        },

        mounted() {
           // console.log(this.$route);
            $('#product_code_div').hide();

            $("#loading").hide();
            let app = this;

            $("#brand_id").select2();
            $("#brand_id").on("select2:select", function(e) {

                var data = e.params.data;
                app.form.brand_id = data.id;
                if(data.id != "") {
                    app.filterCategories(data.id);
                } else {
                    app.initCategories();
                }
            });

            $("#uom_id").on("select2:select", function(e) {
                if(app.isEdit) {
                    var data = e.params.data;
                    app.checkUOM(data,app.form.uom_id,app.product_id);
                    //app.form.uom_id = data.id;
                    //$(".wh_uom").text(data.text);
                } else {
                    var data = e.params.data;
                    app.form.uom_id = data.id;
                    $(".wh_uom").text(data.text);
                }
            });
            $("#category_id").select2();
            $("#category_id").on("select2:select", function(e) {
                var data = e.params.data;
                app.form.category_id = data.id;
            });
            $(".selling_uom_id").select2();

            app.initBrands();
            app.initUoms();

            // $(".selling_uom_id").on("select2:select", function(e) {
            //     var data = e.params.data;
            //
            //     const index = app.temp_selling_uom.indexOf(data.id);
            //     var add_rel = true;
            //     if (index > -1) {
            //         add_rel = false;
            //     }
            //     // console.log(index);
            //     //app.isRequired = true;
            //     app.temp_selling_uom.push(data.id);
            //     var unique_selling_uom = app.temp_selling_uom.filter((a, b) => app.temp_selling_uom.indexOf(a) === b);
            //     app.temp_selling_uom = unique_selling_uom;
            //     $('.selling_uom_id').val(app.temp_selling_uom).trigger('change');
            //
            //     var uom = e.target.options[e.target.options.selectedIndex].dataset.uom;
            //
            //     var wh_uom = document.getElementById('uom_id').options[document.getElementById('uom_id').options.selectedIndex].dataset.uom;
            //     if (typeof wh_uom === 'undefined') {
            //         wh_uom = '';
            //     }
            //
            //     var rel_div = $(".relation").html();
            //    /*** var rel_div_add = "<div id='rel_"+data.id+"'>1 "+ data.text +" = <input class='form-control decimal_no price_txtbox' type='text' style='display:inline-block;' id='rel_txt_"+data.id+"' required > <span class='wh_uom'>"+wh_uom+"</span>&nbsp; <br /> <br /> Selling Price <input class='form-control float_num price_txtbox' type='text' style='display:inline-block;' id='rel_price_txt_"+data.id+"' required >&nbsp;  Per Warehouse UOM Price <input class='form-control float_num price_txtbox' type='text' style='display:inline-block;' id='rel_per_price_txt_"+data.id+"' required > <br /><br/>"; ***/
            //
            //    var rel_div_add = "<div id='rel_"+data.id+"'>1 "+ data.text +" = <input class='form-control decimal_no price_txtbox' type='text' style='display:inline-block;' id='rel_txt_"+data.id+"' required > <span class='wh_uom'>"+wh_uom+"</span>&nbsp; <br /> <br /> <div class='float-left mt-2'>Retail 1 &nbsp;</div><div class='float-left'><input class='form-control float_num price_txtbox' type='text' style='width:90px;' id='rel_retail1_price_txt_"+data.id+"' required ></div><div class='float-left mt-2'>&nbsp; Retail 2 &nbsp;</div><div class='float-left'><input class='form-control float_num price_txtbox' type='text' style='width:90px;' id='rel_retail2_price_txt_"+data.id+"' required ></div><div class='float-left mt-2'>&nbsp; Wholesale &nbsp;</div><div class='float-left'><input class='form-control float_num price_txtbox' type='text' style='display:inline-block;width:90px;' id='rel_wholesale_price_txt_"+data.id+"' required ></div><div class='float-left mt-2'>&nbsp; Purchase &nbsp;</div><div class='float-left'><input class='form-control float_num price_txtbox' type='text' style='display:inline-block;width:120px;' id='rel_purchase_price_txt_"+data.id+"' required ></div><br /><br/><br />";
            //
            //     if(add_rel) {
            //         $(".relation").append(rel_div_add);
            //     }
            //
            // });
            //
            // $(".selling_uom_id").on("select2:unselect", function(e) {
            //
            //     var data = e.params.data;
            //     var is_remove = true;
            //
            //     if(app.isEdit) {
            //         //check selling uom is already used or not
            //         var uom_id = data.id;
            //         var product_id    = $("#rel_txt_"+uom_id).attr("data-product");
            //         var relation_val  = $("#rel_txt_"+uom_id).attr("data-relation");
            //         app.checkSellingUom(product_id, uom_id, relation_val, 'remove');
            //     }
            //     else {
            //         var unique_selling_uom = app.temp_selling_uom.filter((a, b) => app.temp_selling_uom.indexOf(a) === b);
            //         app.temp_selling_uom = unique_selling_uom;
            //         const index = app.temp_selling_uom.indexOf(data.id);
            //         if (index > -1) {
            //           app.temp_selling_uom.splice(index, 1);
            //         }
            //         if(!app.isEdit) {
            //             if(app.temp_selling_uom.length > 0) {
            //                 app.isRequired = true;
            //             } else {
            //                 app.isRequired = false;
            //             }
            //         }
            //         $('.selling_uom_id').val(app.temp_selling_uom).trigger('change');
            //         $("#rel_"+data.id).remove();
            //     }
            //
            // });

            $(document).on('blur','.rel_value',function(evt) {
                var product_id  = $(this).attr("data-product");
                var uom_id      = $(this).attr("data-uom");
                var relation_val = $(this).attr("data-relation");
                app.checkSellingUom(product_id, uom_id, relation_val, 'edit');
            });

            Dropzone.autoDiscover = false;

            //var dropzone = new Dropzone(".dropzone");
            var dropzone = new Dropzone(".dropzone", {
                paramName: "file", // The name that will be used to transfer the file
                maxFilesize: 5,
                uploadMultiple: false,
                acceptedFiles: 'image/*',
                autoProcessQueue: false,
                url: app.site_path+'/product/image/upload',
                maxFiles: 3,
                addRemoveLinks: true,
                maxfilesexceeded: function(file) {
                    swal({
                            title: "Warning!",
                            text: 'Only maximum 3 photos is allowed!',
                            icon: "warning",
                            button: true
                        });
                    this.removeFile(file);
                    return false;
                },
                error: function(file,errorMessage) {
                    swal(errorMessage, {
                        icon: "warning"
                      });
                    this.removeFile(file);
                },
                headers: {'X-CSRF-TOKEN' : $('meta[name="token"]').attr('value')},
                init: function() { 
                    var myDropzone = this;
                    app.dz = myDropzone;
                    // Update selector to match your button
                    /**$(".btn-submit").click(function (e) {
                        app.dz = myDropzone;
                       e.preventDefault();
                        myDropzone.processQueue();
                    });**/

                    /**this.on('sending', function(file, xhr, formData) {
                        var data = $('#frmTarget').serializeArray();
                        $.each(data, function(key, el) {
                            formData.append(el.name, el.value);
                        });
                       
                    });**/
                },
                success: function(file, response){
                    // success hook
                    this.removeFile(file);
                },
                complete: function(file, response){
                    console.log(response);
                    swal({
                            title: "Success!",
                            text: 'Customer Saved.',
                            icon: "success",
                            button: true
                        }).then(function() {
                            location.reload();
                           

                        });
                }

            });

            app.mydz = dropzone;

        },

        methods: {
            initBrands() {
              axios.get("/brands").then(({ data }) => (this.brands = data.data));
              $("#brand_id").select2();
            },

            initUoms() {
              axios.get("/uoms").then(({ data }) => (this.uoms = data.data));
              $("#uom_id").select2();
              $(".selling_uom_id").select2();

            },

            initCategories() {
              axios.get("/categories").then(({ data }) => (this.categories = data.data));
              $("#category_id").select2();
            },
            filterCategories(brand_id) {
              axios.get("/categories_bybrand/"+brand_id).then(({ data }) => (this.categories = data.data));
              $("#category_id").select2();
            },
             checkProductCode() {
                if($('#product_code').val() != '' && $('#product_code_type_id').val() != 'automatic') {
                    var pcode = $('#product_code').val();
                    if(/^[Pp][/\d]{5}?$/i.test(pcode)) {
                        swal({
                                title: "Warning!",
                                text: 'Product code is invalid!',
                                icon: "warning",
                                button: true
                            });
                        $('#product_code').val('');
                    }
                }                
            },
            checkUOM(data,uom_id,product_id) {
                let app = this;
                $("#loading").show();
                axios
                    .get("/check_warehouse_uom/" + product_id)
                    .then(function(response) {
                        $("#loading").hide();
                        if(response.data.message == "success") {
                            app.form.uom_id = data.id;
                            $(".wh_uom").text(data.text);
                        } else {
                            $('#uom_id').val(uom_id).trigger('change');
                            swal({
                                title: "Warning!",
                                text: 'Warehous UOM is already used in transition!.',
                                icon: "warning",
                                button: true
                            });

                        }
                    });
            },
            checkSellingUom(product_id,uom_id,rel_value,action) {
                let app = this;
                var msg = '';
                var edit_val = $("#rel_txt_"+uom_id).val();
                var chk = true;
                if(action == 'edit' && rel_value == edit_val) {
                    chk = false;
                }
                if(chk) {
                    $("#loading").show();
                    axios
                        .get("/check_selling_uom/" + product_id + "/" + uom_id)
                        .then(function(response) {
                            $("#loading").hide()
                            if(response.data.message == "used") {
                                $('#rel_txt_'+uom_id).val(rel_value);
                                swal({
                                    title: "Warning!",
                                    text: 'Selling UOM is already used in transition!.',
                                    icon: "warning",
                                    button: true
                                });
                                if(action == 'remove') {
                                    $('.selling_uom_id').val(app.temp_selling_uom).trigger('change');
                                }
                            } else {
                                if(action == 'remove') {
                                    var unique_selling_uom = app.temp_selling_uom.filter((a, b) => app.temp_selling_uom.indexOf(a) === b);
                                    app.temp_selling_uom = unique_selling_uom;
                                    const index = app.temp_selling_uom.indexOf(uom_id);
                                    if (index > -1) {
                                      app.temp_selling_uom.splice(index, 1);
                                    }
                                    if(!app.isEdit) {
                                        if(app.temp_selling_uom.length > 0) {
                                            app.isRequired = true;
                                        } else {
                                            app.isRequired = false;
                                        }
                                    }
                                    $('.selling_uom_id').val(app.temp_selling_uom).trigger('change');
                                    $("#rel_"+uom_id).remove();
                                }
                            }
                        });

                    }
            },
            getProduct(id) {
              let app = this;
              axios
                .get("/product/" + id)
                .then(function(response) {
                    console.log(response.data);
                    if(response.data.product.brand_id !="" || response.data.product.brand_id != null) {
                        app.filterCategories(response.data.product.brand_id);
                    } else {
                        app.initCategories();
                    }
                    if(response.data.product.product_code_type==="manual"){
                        $('#product_code_div').show();
                    }
                    if(response.data.product.photos != null) {
                        app.photos = response.data.product.photos;
                    }
                    app.form.product_name = response.data.product.product_name;
                    app.form.cost_price=parseInt(response.data.cost_price);
                    app.form.product_code = response.data.product.product_code;
                    app.form.brand_id = response.data.product.brand_id;
                    app.form.product_code_type = response.data.product.product_code_type;
                    app.form.min_qty = response.data.product.minimum_qty;
                    app.form.selling_price = response.data.product.selling_price;
                    app.form.purchase_price = response.data.product.purchase_price;
                    app.form.reorder_level = response.data.product.reorder_level;
                    // app.form.purchase_price = response.data.product.purchase_price;
                   //app.form.percentage_qty = response.data.product.percentage_qty;
                    $('#brand_id').val(app.form.brand_id).trigger('change');
                    app.form.category_id = response.data.product.category_id;
                    $('#product_code_type_id').val(app.form.product_code_type).trigger('change');
                    // app.form.prodcut_code_type = response.data.product.prodcut_code_type;
                    $('#category_id').val(app.form.category_id).trigger('change');
                    app.form.uom_id = response.data.product.uom_id;
                    $('#uom_id').val(app.form.uom_id).trigger('change');
                    // app.form.product_price = response.data.product.product_price;

                    // app.form.retail1_price = response.data.product.retail1_price;
                    // app.form.retail2_price = response.data.product.retail2_price;
                    // app.form.wholesale_price = response.data.product.wholesale_price;
                    // app.form.purchase_price = response.data.product.purchase_price;
                    // var rel_div_add = '';
                    // $.each(response.data.product.selling_uoms, function( key, obj ) {
                    //     app.selected_selling_uom.push(String(obj.pivot.uom_id));
                    //
                    //     app.form.selling_uom_id.push(String(obj.pivot.uom_id));
                    //
                    //     app.temp_selling_uom.push(String(obj.pivot.uom_id));
                    //
                    //     if(obj.pivot.product_price==null) {
                    //         var price = '';
                    //     } else {
                    //         var price = obj.pivot.product_price;
                    //     }
                    //     if(obj.pivot.per_warehouse_uom_price==null) {
                    //         var per_price = '';
                    //     } else {
                    //         var per_price = obj.pivot.per_warehouse_uom_price;
                    //     }
                    //
                    //     if(obj.pivot.retail1_price==null) {
                    //         var retail1_price = '';
                    //     } else {
                    //         var retail1_price = obj.pivot.retail1_price;
                    //     }
                    //     if(obj.pivot.retail2_price==null) {
                    //         var retail2_price = '';
                    //     } else {
                    //         var retail2_price = obj.pivot.retail2_price;
                    //     }
                    //     if(obj.pivot.wholesale_price==null) {
                    //         var wholesale_price = '';
                    //     } else {
                    //         var wholesale_price = obj.pivot.wholesale_price;
                    //     }
                    //     if(obj.pivot.warehouse_uom_purchase_price==null) {
                    //         var purchase_price = '';
                    //     } else {
                    //         var purchase_price = obj.pivot.warehouse_uom_purchase_price;
                    //     }
                    //
                    //    /* rel_div_add = "<div id='rel_"+obj.pivot.uom_id+"'>1 "+ obj.uom_name +" = <input class='form-control decimal_no price_txtbox rel_value' data-relation='"+obj.pivot.relation+"' data-uom ='"+obj.pivot.uom_id+"' data-product ='"+obj.pivot.product_id+"' type='text' style='display:inline-block;' id='rel_txt_"+obj.pivot.uom_id+"' value='"+obj.pivot.relation+"' required > <span class='wh_uom' >"+response.data.product.uom.uom_name+"</span>&nbsp; Selling Price <input class='form-control float_num price_txtbox' type='text' style='display:inline-block;' id='rel_price_txt_"+obj.pivot.uom_id+"' value='"+price+"' required >&nbsp; Per Warehouse UOM Price <input class='form-control float_num price_txtbox' type='text' style='display:inline-block;' id='rel_per_price_txt_"+obj.pivot.uom_id+"' value='"+per_price+"' required > <br /><br/>";
                    //     $(".relation").append(rel_div_add); */
                    //
                    //     rel_div_add = "<div id='rel_"+obj.pivot.uom_id+"'>1 "+ obj.uom_name +" = <input class='form-control decimal_no price_txtbox rel_value' data-relation='"+obj.pivot.relation+"' data-uom ='"+obj.pivot.uom_id+"' data-product ='"+obj.pivot.product_id+"' type='text' style='display:inline-block;' id='rel_txt_"+obj.pivot.uom_id+"' value='"+obj.pivot.relation+"' required > <span class='wh_uom' >"+response.data.product.uom.uom_name+"</span>&nbsp; <br /><br /><div class='float-left mt-2'>Retail 1 &nbsp;</div><div class='float-left'><input class='form-control float_num price_txtbox' type='text' style='display:inline-block;' id='rel_retail1_price_txt_"+obj.pivot.uom_id+"' value='"+retail1_price+"' required ></div><div class='float-left mt-2'>&nbsp; Retail 2 &nbsp;</div><div class='float-left'><input class='form-control float_num price_txtbox' type='text' style='display:inline-block;' id='rel_retail2_price_txt_"+obj.pivot.uom_id+"' value='"+retail2_price+"' required ></div><div class='float-left mt-2'>&nbsp; Wholesale &nbsp;</div><div class='float-left'><input class='form-control float_num price_txtbox' type='text' style='display:inline-block;' id='rel_wholesale_price_txt_"+obj.pivot.uom_id+"' value='"+wholesale_price+"' required ></div><div class='float-left mt-2'>&nbsp; Purchase &nbsp;</div><div class='float-left'><input class='form-control float_num price_txtbox' type='text' style='display:inline-block;' id='rel_purchase_price_txt_"+obj.pivot.uom_id+"' value='"+purchase_price+"' required ></div><br /><br/><br />";
                    //     $(".relation").append(rel_div_add);
                    //
                    //
                    // });
                    // $(".selling_uom_id").val(app.form.selected_selling_uom).trigger("change");
                    //console.log(response.data.product.selling_uoms);
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

                if (!this.isEdit) {
                    swal({
                    title: "Are you sure?",
                    text: "Once added, you will not be able to update UOM and RELATION!",
                    icon: "warning",
                    buttons: true,
                    dangerMode: true
                    }).then(willDelete => {
                        if (willDelete) {
                            console.log(app);
                        // app.form.selected_selling_uom = $(".selling_uom_id").val();
                        // console.log(app.form.selected_selling_uom);
                        //     app.form.selected_selling_uom=app.temp_selling_uom;
                        // var relation_arr = [];
                        // var price_arr = [];
                        // var per_price_arr = [];
                        //
                        // var retail1_price_arr = [];
                        // var retail2_price_arr = [];
                        // var wholesale_price_arr = [];
                        // var purchase_price_arr=[];

                        // for(var key in selling_uom_arr) {
                        //     var ukey = selling_uom_arr[key];
                        //
                        //     /*var obj = {};
                        //     obj[ukey] = $("#rel_txt_"+ukey).val();
                        //     app.form.uom_relations.push(obj);  */
                        //
                        //     relation_arr[ukey] = $("#rel_txt_"+ukey).val();
                        //
                        //     price_arr[ukey] = $("#rel_price_txt_"+ukey).val();
                        //
                        //     per_price_arr[ukey] = $("#rel_per_price_txt_"+ukey).val();
                        //
                        //     retail1_price_arr[ukey] = $("#rel_retail1_price_txt_"+ukey).val();
                        //     retail2_price_arr[ukey] = $("#rel_retail2_price_txt_"+ukey).val();
                        //     wholesale_price_arr[ukey] = $("#rel_wholesale_price_txt_"+ukey).val();
                        //     purchase_price_arr[ukey] = $("#rel_purchase_price_txt_"+ukey).val();
                        // }
                        //
                        // app.form.uom_relations = relation_arr;
                        // app.form.uom_prices = price_arr;
                        // app.form.uom_per_prices = per_price_arr;
                        // app.form.uom_retail1_prices = retail1_price_arr;
                        // app.form.uom_retail2_prices = retail2_price_arr;
                        // app.form.uom_wholesale_prices = wholesale_price_arr;
                        // app.form.uom_purchase_prices = purchase_price_arr;

                        this.form
                          .post("/product")
                          .then(function(data) {
                            console.log(data);
                            if(data.status == "success") {
                                $('#hid_product_id').val(data.product_id);
                                //app.dz.processQueue();
                                //reset form data
                                app.temp_selling_uom = [];
                                event.target.reset();
                                $("#brand_id").select2();
                                $("#category_id").select2();
                                $("#uom_id").select2();

                                app.form.selling_uom_id = [];
                                $(".selling_uom_id").select2();
                                $(".selling_uom_id").select2("val","");
                                $(".relation").html('');

                                swal({
                                    title: "Success!",
                                    text: 'Product Added.',
                                    icon: "success",
                                    button: true
                                }).then(function() {
                                    app.$router.push({name:'product'});
                                   // location.reload();


                                });



                                /* location.reload();
                                  $.notify("Success", {
                                    autoHideDelay: 3000,
                                    gap: 1,
                                    className: "success"
                                  });*/
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
                                var obj = $.parseJSON(response.errors);
                                var errmsg = "";
                                for (var key in obj) {
                                    if (obj.hasOwnProperty(key)) {
                                       errmsg += obj[key][0]+"\n";
                                    }
                                }
                               $.notify(errmsg, {
                                autoHideDelay: 3000,
                                gap: 1,
                                className: "error"
                              });

                            });

                        }
                    });


                  } else {
                    //Edit Product
                    swal({
                    title: "Are you sure?",
                    text: "",
                    icon: "warning",
                    buttons: true,
                    dangerMode: true
                    }).then(willDelete => {
                        if (willDelete) {
                            //app.form.selected_selling_uom = app.selected_selling_uom.concat($(".selling_uom_id").val());
                             app.form.selected_selling_uom = app.temp_selling_uom.filter((a, b) => app.temp_selling_uom.indexOf(a) === b);
                             // console.log(app.form.selected_selling_uom);
                        // var relation_arr = [];
                        // var price_arr = [];
                        // var per_price_arr = [];
                        // var retail1_price_arr = [];
                        // var retail2_price_arr = [];
                        //     var wholesale_price_arr = [];
                        //     var purchase_price_arr = [];
                        // for(var key in app.form.selected_selling_uom) {
                        //     var ukey = app.form.selected_selling_uom[key];
                        //
                        //     /*var obj = {};
                        //     obj[ukey] = $("#rel_txt_"+ukey).val();
                        //     app.form.uom_relations.push(obj);  */
                        //
                        //     relation_arr[ukey] = $("#rel_txt_"+ukey).val();
                        //     price_arr[ukey] = $("#rel_price_txt_"+ukey).val();
                        //     per_price_arr[ukey] = $("#rel_per_price_txt_"+ukey).val();
                        //     retail1_price_arr[ukey] = $("#rel_retail1_price_txt_"+ukey).val();
                        //     retail2_price_arr[ukey] = $("#rel_retail2_price_txt_"+ukey).val();
                        //     wholesale_price_arr[ukey] = $("#rel_wholesale_price_txt_"+ukey).val();
                        //     purchase_price_arr[ukey] = $("#rel_purchase_price_txt_"+ukey).val();
                        // }
                        // app.form.uom_relations = relation_arr;
                        // app.form.uom_prices = price_arr;
                        // app.form.uom_per_prices = per_price_arr;
                        // app.form.uom_retail1_prices = retail1_price_arr;
                        // app.form.uom_retail2_prices = retail2_price_arr;
                        // app.form.uom_wholesale_prices = wholesale_price_arr;
                        //     app.form.uom_purchase_prices = purchase_price_arr;
                            // var vm= this;
                        this.form
                          .patch("/product/" + app.product_id)
                          .then(function(data) {

                            $('#hid_product_id').val(app.product_id);
                           // app.dz.processQueue();
                            console.log(data);
                            //reset form data
                                app.temp_selling_uom = [];
                                event.target.reset();
                                $("#brand_id").select2();
                                $("#category_id").select2();
                                $("#uom_id").select2();
                                $(".selling_uom_id").select2("val","");
                                $(".relation").html('');
                            if(data.status == "success") {
                                // vm.$route.push('/product');
                                swal({
                                    title: "Success!",
                                    text: 'Product is updated.',
                                    icon: "success",
                                    button: true
                                }).then(function() {
                                    // this.$route
                                    app.$router.push({name:'product'});
                                    // vm.location.reload();
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
                                var obj = $.parseJSON(response.errors);
                                var errmsg = "";
                                for (var key in obj) {
                                    if (obj.hasOwnProperty(key)) {
                                       errmsg += obj[key][0]+"\n";
                                    }
                                }
                               $.notify(errmsg, {
                                autoHideDelay: 3000,
                                gap: 1,
                                className: "error"
                              });

                            });
                        }
                    });

                    //End Edit Product
                  }
            },
            codeType(){
                var check=this.form.product_code_type;
                if(check=='automatic'){
                    $('#product_code_div').hide();
                    // $( "#product_code" ).prop( "disabled", true );
                }else{
                    $('#product_code_div').show();
                    $( "#product_code" ).prop( "disabled", false );
                }
            },

            remove_this(p) {
           
              let app = this;
              swal({
                title: "Are you sure?",
                text: "Once deleted, you will not be able to recover!",
                icon: "warning",
                buttons: true,
                dangerMode: true
              }).then(willDelete => {
                if (willDelete) {
                  axios
                    .delete("/product_photo/" + p)
                    .then(function(response) {
                      //app.photos = [];
                    });
                  swal("Photo has been deleted!", {
                    icon: "success"
                  }).then(function() {
                    location.reload();

                });
                } else {
                  //
                }
              });
            },

        }
    }
</script>
