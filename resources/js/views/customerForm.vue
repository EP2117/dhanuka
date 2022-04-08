<template>
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="#!"><i class="feather icon-home"></i></a></li>
                <li class="breadcrumb-item"><a :href="site_path+'/'">Home</a></li>
                <li class="breadcrumb-item"><a :href="site_path+'/master'">Master</a></li>
                <li class="breadcrumb-item"><router-link tag="span" to="/customer" class="font-weight-normal"><a href="#">Customer</a></router-link></li>
                <li class="breadcrumb-item active" aria-current="page">Customer Form</li>

            </ol>
        </nav>
        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h4 class="mb-0 text-gray-800" v-if="!isEdit">Create New Customer</h4>
            <h4 class="mb-0 text-gray-800" v-else>Edit Customer</h4>
        </div>

        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Customer Details</h6>
            </div>
            <div class="card-body">
                <div class="d-block">
                    <!-- form start -->
                    <form class="form" method="post" @submit.prevent="onSubmit" @keydown="form.errors.clear($event.target.name)">

                        <div class="row mt-3">
                            <div class="form-group col-md-4">
                                <label for="cus_code">Customer Code</label>
                                 <input type="text" class="form-control" id="cus_code" name="cus_code"
                                v-model="form.cus_code" readonly>
                            </div>

                            <div class="form-group col-md-4">
                                <label for="cus_name">Customer Name</label>
                                 <input type="text" class="form-control" id="cus_name" name="cus_name"
                                v-model="form.cus_name" required>
                            </div>                          
                        </div>

                        <div class="row mt-3">

                            <div class="form-group col-md-4">
                                <label for="cus_type">Customer Type</label>
                                <select id="cus_type" class="form-control"
                                    name="cus_type" v-model="form.cus_type" style="width:100%" required
                                >
                                    <option value="">Select One</option>
                                    <option v-for="ctype in cus_types" :value="ctype.id"  >{{ctype.customer_type_name}}</option>
                                </select>
                            </div>  
                            <div class="form-group col-md-8">
                                <label>Category</label>
                                <select class="form-control categories"
                                    name="categories[]" id="categories" style="width:100%" multiple>
                                    <option value="">Select One</option>
                                    <option v-for="cat in categories" v-if="!isEdit" :value="cat.id">{{cat.category_name}}</option>
                                </select>
                            </div>                          
                        </div>

                        <div class="row mt-3">
                            <div class="form-group col-md-4">
                                <label for="country_id">Country</label>
                                <select id="country_id" class="form-control"
                                    name="country_id" v-model="form.country_id" style="width:100%" required
                                >
                                    <option value="">Select One</option>
                                    <option v-for="country in countries" :value="country.id" >{{country.country_name}}</option>
                                </select>
                            </div> 

                            <div class="form-group col-md-4">
                                <label for="state_id">State</label>
                                <select id="state_id" class="form-control"
                                    name="state_id" v-model="form.state_id" style="width:100%" required
                                >
                                    <option value="">Select One</option>
                                    <option v-for="state in states" :value="state.id"  >{{state.state_name}}</option>
                                </select>
                            </div>  

                            <div class="form-group col-md-4">
                                <label for="township_id">Township</label>
                                <select id="township_id" class="form-control"
                                    name="township_id" v-model="form.township_id" style="width:100%" required
                                >
                                    <option value="">Select One</option>
                                    <option v-for="township in townships" :value="township.id"  >{{township.township_name}}</option>
                                </select>
                            </div>                           
                        </div>

                        <div class="row mt-3">
                            <div class="form-group col-md-4">
                                <label for="cus_phone">Phone</label>
                                 <input type="text" class="form-control" id="cus_phone" name="cus_phone"
                                v-model="form.cus_phone" required>
                            </div>

                            <div class="form-group col-md-4">
                                <label for="billing_address">Billing Address</label>
                                 <textarea class="form-control" id="billing_address" name="billing_address"
                                v-model="form.billing_address" rows='3' required></textarea>
                            </div>

                            <div class="form-group col-md-4">
                                <label for="shipping_address">Shipping Address</label>
                                 <textarea class="form-control" id="shipping_address" name="shipping_address"
                                v-model="form.shipping_address" rows='3' required></textarea>
                            </div>
                        </div>
                        <div class="row mt-2">
                        <div class="form-group col-12">
                          <label class="col-lg-2">Image Upload</label>
                          <div class="col-lg-9">
                              <form action="/customer/image/upload" class="dropzone" enctype="multipart/form-data" id="frmTarget">
                                  <input type="hidden" name="_token" :value="csrf">
                                  <input type="hidden" name="hid_cus_id" id="hid_cus_id">
                                  <div class="fallback">
                                    <input name="file" id="file" type="file" multiple>
                                  </div>
                              </form>
                          </div>
                        </div>
                        </div>

                        <div class="row" v-if="isEdit && photos.length > 0">
                            <div class="col-lg-12">
                            <span style="position:relative; float:left; display:table; margin-left:10px;" v-for="image,k in photos">
                                <img :src="storage_path+'/image/customer/'+photos[k]" width="130" class="img-thumbnail" />
                                    <br>
                                    <button
                                      type="button"
                                      rel="tooltip"
                                      class="btn btn-danger btn-sm"
                                      style="position: absolute;right: 0;top: 0;margin: 0px;padding:0px;"
                                      @click="remove_this(photos[k])"
                                    >
                                      <small>Remove</small>
                                    </button>
                            </span>
                            </div>
                        </div>
                        <div style="clear:both"></div>
                        <div class="row mt-2">
                            <div class="col-6">
                              <label class="">Lock  &nbsp;&nbsp;<input type="checkbox" name="lock" id="lock" class="lock_status mr-4" value="lock" @change="checkLockStatus($event.target)" /></label>
                              <label class="">Unlock  &nbsp;&nbsp;<input value="unlock" type="checkbox" name="unlock" id="unlock" class="lock_status" @change="checkLockStatus($event.target)" /></label>
                            </div>
                        </div>

                        <div class="row mt-3 lock_div" id="lock_div" style="display:none;">                        
                            <div class="form-group col-md-4">
                                <label for="lock_remark">remark</label>
                                <textarea class="form-control" id="lock_remark" name="lock_remark"
                                v-model="form.lock_remark"></textarea>
                            </div>

                            <div class="form-group col-md-4">
                                <label for="lock_approve">Approve By Name</label>
                                 <input type="text" class="form-control" id="lock_approve" name="lock_approve"
                                v-model="form.lock_approve" />
                            </div>
                        </div>

                        <div class="form-group row text-right">
                            <label class="col-lg-3 col-form-label form-control-label"></label>
                            <div class="col-lg-12">
                                <input type="reset" class="btn btn-secondary btn-sm" value="Cancel" v-if="!isEdit">
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
    export default {
        data() {
            return {
              form: new Form({
                cus_name: '',
                cus_code: '',
                cus_type: '',
                country_id: '',
                state_id: "",
                township_id : '',
                cus_phone: '',
                billing_address: '', 
                shipping_address: '',
                is_lock: '',
                lock_remark: '',
                lock_approve: '',
                categories: [],
              }),
              cus_types: [],
              countries: [],
              selected_categories: [],
              states: [],
              categories: [],
              townships: [],
              isEdit: false,
              customer_id: '',
              user_role: '',
              site_path: '',
              storage_path: '',
              csrf: '',
              dz: '',
              file_count: 0,
              mydz: '',
              photos: [],
            };
        },

        created() {
            this.csrf = document.head.querySelector('meta[name="csrf-token"]').content;
            this.user_role = document.querySelector("meta[name='user-role']").getAttribute('content');

            this.site_path = document.querySelector("meta[name='site-path']").getAttribute('content');
            //this.site_path = this.site_path.substring(this.site_path.lastIndexOf('/')+1);
            this.storage_path = document.querySelector("meta[name='storage-path']").getAttribute('content');
            
            if(this.user_role != "admin" && this.user_role != "system" && this.user_role != "office_user") {
                var url =  window.location.origin;
                window.location.replace(url);
            }

            if(this.$route.params.id) {
                this.isEdit = true;
                this.customer_id = this.$route.params.id;
                this.getCustomer(this.customer_id);
            } else {
                this.initCategories(); 
            }
            //console.log(this.isEdit);
        },
        mounted() {
            //console.log('Component mounted.')
            $("#loading").hide();
            let app = this;

            $("#cus_type").select2();
            $("#cus_type").on("select2:select", function(e) {

                var data = e.params.data;
                app.form.cus_type = data.id;
            });

            $("#country_id").select2();
            $("#country_id").on("select2:select", function(e) {  
                app.states = [];
                app.townships = [];      
                var data = e.params.data;
                app.form.country_id = data.id;
                axios.get("/state_by_country/"+ 1).then(({ data }) => (app.states = data.data));
            });
            $("#state_id").select2();
            $("#state_id").on("select2:select", function(e) { 
                app.townships = [];      
                var data = e.params.data;
                app.form.state_id = data.id;
                axios.get("/township_by_state/"+ data.id).then(({ data }) => (app.townships = data.data));
            });

            $("#township_id").select2();
            $("#township_id").on("select2:select", function(e) { 
                var data = e.params.data;
                app.form.township_id = data.id;
            });

            app.initTypes();
            app.initCountries();
            app.initStates();
            app.initTownships();
            // $('#country_id').val(1).trigger('change');

             

            $(".categories").select2();                      

            $(".categories").on("select2:select", function(e) {
                var data = e.params.data;
                app.selected_categories.push(data.id); 

                var unique_categories = app.selected_categories.filter((a, b) => app.selected_categories.indexOf(a) === b);
                // console.log(unique_invoices);
                app.selected_categories = unique_categories;

                $('.categories').val(app.selected_categories).trigger('change');
            });

            $(".categories").on("select2:unselect", function(e) {
                var data = e.params.data;
                var unique_categories = app.selected_categories.filter((a, b) => app.selected_categories.indexOf(a) === b);
                app.selected_categories = unique_categories;
                const index = app.selected_categories.indexOf(data.id);
                if (index > -1) {
                  app.selected_categories.splice(index, 1);
                }
                $('.categories').val(app.selected_categories).trigger('change');
            });

            Dropzone.autoDiscover = false;

            //var dropzone = new Dropzone(".dropzone");
            var dropzone = new Dropzone(".dropzone", {
                paramName: "file", // The name that will be used to transfer the file
                maxFilesize: 5,
                uploadMultiple: false,
                acceptedFiles: 'image/*',
                autoProcessQueue: false,
                url: app.site_path+'/customer/image/upload',
                //maxFiles: 1,
                addRemoveLinks: true,
                maxfilesexceeded: function(file) {
                    this.removeFile(file);
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

            initCategories() {
              axios.get("/categories").then(({ data }) => (this.categories = data.data));
              $("#categories").select2();
            },
            initTypes() {
              axios.get("/customer_type").then(({ data }) => (this.cus_types = data.data));
            //   console.log(this.cus_types);
              $("#cus_type").select2();
            },

            initCountries() {
              axios.get("/country").then(({ data }) => (this.countries = data.data));
              this.form.country_id=1;
              $("#country_id").val(1).trigger('change');
            //   $("#country_id").select2();
            },

            initStates() {
                if(this.form.country_id != "") {
                  axios.get("/state_by_country/" + this.form.country_id).then(({ data }) => (this.states = data.data));
                  console.log(this.states);
                  $("#state_id").select2();
                }
            },

            initTownships() {
                if(this.form.state_id != "") {
                  axios.get("/township_by_state/" + this.form.state_id).then(({ data }) => (this.townships = data.data));
                  $("#township_id").select2();
                }
            },

            checkLockStatus(obj) {
                let app = this;
                var is_lock = $(obj).prop("checked");                
                if(is_lock) {
                    $("#lock"). prop("checked", false);
                    $("#unlock"). prop("checked", false);
                    $("#lock_div").show();
                    $(obj). prop("checked", true);
                } else {
                    $("#lock_div").hide();
                }
            },

            getCustomer(id) {
              let app = this;
              axios
                .get("/customer/" + id)
                .then(function(response) { 
                    if(response.data.customer.photo != null) { 
                        var parr = response.data.customer.photo.split(","); 
                        for(var i=0; i<parr.length; i++)  {
                            app.photos.push(parr[i]);
                        } 
                    }
                    //console.log(app.photos);
                    app.form.cus_name = response.data.customer.cus_name;
                    app.form.cus_code = response.data.customer.cus_code;
                    app.form.cus_type = response.data.customer.customer_type_id;
                    $('#cus_type').val(app.form.cus_type).trigger('change');
                    app.form.country_id = response.data.customer.country_id;
                    $('#country_id').val(app.form.country_id).trigger('change');
                    app.form.state_id = response.data.customer.state_id;
                    $('#state_id').val(app.form.state_id).trigger('change');
                    app.form.township_id = response.data.customer.township_id;
                    $('#township_id').val(app.form.township_id).trigger('change');
                    app.form.cus_phone = response.data.customer.cus_phone;
                    app.form.shipping_address = response.data.customer.cus_shipping_address;
                    app.form.billing_address = response.data.customer.cus_billing_address;

                    var s2 = $(".categories").select2({
                        tags: true
                    });
                     var cat_arr = [];
                     $.each(response.data.customer.categories, function( key, value ) {
                        cat_arr.push(value.id);
                     });
                    var index = '';
                    console.log(response.data.categories);
                    $.each(response.data.categories, function( key, value ) {
                        index = cat_arr.indexOf(value.id);                        
                        if (index > -1) {
                          app.selected_categories.push(String(value.id));
                        }

                        if(!s2.find('option[value="'+value.id+'"]').length) {
                        // console.log(s2.find('option[value="'+value.id+'"]').length);
                            s2.append($('<option value="'+value.id+'">').text(value.category_name));
                        }
                    });
                    s2.val(app.selected_categories).trigger("change");

                    if(response.data.customer.is_lock !== null) {
                        $("#lock_div").show();
                        if(response.data.customer.is_lock == 1) {
                            $("#lock").prop("checked",true);
                        }
                        if(response.data.customer.is_lock === 0) {
                            $("#unlock").prop("checked",true);
                        
                        }  
                        app.form.lock_remark = response.data.customer.lock_remark;
                        app.form.lock_approve = response.data.customer.lock_approve;                      
                    }
                    axios.get("/state_by_country/" + app.form.country_id).then(({ data }) => (app.states = data.data));
                    $("#state_id").select2();

                    axios.get("/township_by_state/" + app.form.state_id).then(({ data }) => (app.townships = data.data));
                    $("#township_id").select2();
                    
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

                var is_lock = $("#lock").prop("checked");
                var is_unlock = $("#unlock").prop("checked"); 
                app.form.is_lock = "";
                if(is_lock) {
                    if(app.form.lock_remark == '' || app.form.lock_approve == '') {
                        swal("Warning!", "Remark and Approve By Name is required!", "warning");

                        return false;
                    } else {
                        app.form.is_lock = "lock";
                    }
                } 

                if(is_unlock) {
                    if(app.form.lock_remark == '' || app.form.lock_approve == '') {
                        swal("Warning!", "Remark and Approve By Name is required!", "warning");

                        return false;
                    } else {
                        app.form.is_lock = "unlock";
                    }
                }

                app.form.categories = [];
                $('#categories :selected').each(function() {                    
                    app.form.categories.push($(this).val());                   
                });

                $("#loading").show(); 

                if (!this.isEdit) {

                this.form
                  .post("/customer")
                  .then(function(data) {
                    console.log(data);
                    if(data.status == "success") {
                        $('#hid_cus_id').val(data.cus_id);
                        app.dz.processQueue();
                        //reset form data
                        event.target.reset();
                        $("#cus_type").select2();
                        $("#country_id").select2();
                        $("#state_id").select2();
                        $("#township_id").select2();
                        if(app.dz.files.length == 0) {
                            swal({
                                title: "Success!",
                                text: 'Customer is updated.',
                                icon: "success",
                                button: true
                            }).then(function() {
                                location.reload();

                            });
                        }

                       /** swal({
                            title: "Success!",
                            text: 'Customer Saved.',
                            icon: "success",
                            button: true
                        }).then(function() {
                           // location.reload();
                           

                        }); **/
                                                  


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
                    
                  } else {

                    //Edit Customer                    
                    this.form
                      .patch("/customer/" + app.customer_id)
                      .then(function(data) {
                        console.log(data);
                        //reset form data
                        event.target.reset();
                        $("#cus_type").select2();
                        $("#country_id").select2();
                        $("#state_id").select2();
                        $("#township_id").select2();  
                        if(data.status == "success") {
                            $('#hid_cus_id').val(data.cus_id);
                            //alert(document.getElementById("file").files.length);
                            console.log(app.dz.files.length);
                            app.dz.processQueue();
                            if(app.dz.files.length == 0) {
                                swal({
                                    title: "Success!",
                                    text: 'Customer is updated.',
                                    icon: "success",
                                    button: true
                                }).then(function() {
                                    location.reload();

                                });
                            }
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
                    //End Edit Customer
                  }
            },

            remove_this(name) {
           
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
                    .post("/customer/image/delete/" + name +"/"+app.customer_id)
                    .then(function(response) {
                      app.photos = [];
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