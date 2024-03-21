<template>
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="#!"><i class="feather icon-home"></i></a></li>
                <li class="breadcrumb-item"><a :href="site_path+'/'">Home</a></li>
                <li class="breadcrumb-item"><a :href="site_path+'/master'">Master</a></li>
                <li class="breadcrumb-item"><router-link tag="span" to="/users" class="font-weight-normal"><a href="#">User</a></router-link></li>
                <li class="breadcrumb-item active" aria-current="page">User Form</li>

            </ol>
        </nav>
        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h4 class="mb-0 text-gray-800">User Form</h4>
        </div>

        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">User Details</h6>
            </div>
            <div class="card-body">
                <div class="d-block">
                    <!-- form start -->
                    <form class="form" method="post" @submit.prevent="onSubmit" >
                        <div class="row">
                            <div class="form-group col-md-4">
                                <label for="role_id">User Role</label>
                                <select id="role_id" class="form-control mm-txt"
                                    name="role_id" v-model="form.role_id" style="width:100%" 
                                >
                                    <option value="">Select One</option>
                                    <option v-for="role in roles" :value="role.id"  >{{role.role_name}}</option>
                                </select>
                            </div>
                        </div>

                        <div class="row branch_div" style="display:none;">
                            <div class="form-group col-md-4">
                                <label for="branch_id">Branch</label>
                                <select id="branch_id" class="form-control mm-txt"
                                    name="branch_id" v-model="form.branch_id" style="width:100%" 
                                >
                                    <option value="">Select One</option>
                                    <option v-for="branch in branches" :value="branch.id"  >{{branch.branch_name}}</option>
                                </select>
                            </div>
                        </div>

                        <div class="row branches_div" style="display:none;">
                            <div class="form-group col-md-8">
                                <label for="branches">Branch</label>
                                <select multiple class="form-control branches"
                                    name="branches[]" v-model="form.branches" style="width:100%"
                                >
                                    <option v-for="branch in branches" :value="branch.id">{{branch.branch_name}}</option>
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            <div class="form-group col-md-4">
                                <label for="name">Name</label>
                                <input type="text" class="form-control" id="name" name="name" v-model="form.name" required>
                            </div>
                            <div class="form-group col-md-4">
                                <label for="email">Email</label>
                                <input type="text" class="form-control" id="email" name="email" v-model="form.email">
                            </div>
                        </div>

                        <div class="row">
                            <div class="form-group col-md-4">
                                <label for="warehouse_id">Warehouse</label>
                                <select id="warehouse_id" class="form-control"
                                    name="warehouse_id" v-model="form.warehouse_id" style="width:100%" required
                                >
                                    <option value="">Select One</option>
                                    <option v-for="warehouse in warehouses" :value="warehouse.id"  >{{warehouse.warehouse_name}}</option>
                                </select>
                            </div>
                            
                            <div class="form-group col-md-4">
                                <label for="phone">Phone</label>
                                <input type="text" class="form-control" id="phone" name="phone" v-model="form.phone">
                            </div>
                        </div>

                        <div class="row country_head_div" style="display:none;">
                            <div class="form-group col-md-8">
                                <label for="local_supervisors">Local Supervisors</label>
                                <select multiple class="form-control local_supervisors"
                                    name="local_supervisors[]" v-model="form.local_supervisors" style="width:100%"
                                >
                                    <option v-for="ls in local_supervisors" :value="ls.id">{{ls.email}}</option>
                                </select>
                            </div>

                            <div class="form-group col-md-8">
                                <label for="office_sale_man">Office Sale Man</label>
                                <select multiple class="form-control office_sale_man"
                                    name="office_sale_man[]" v-model="form.office_sale_man" style="width:100%"
                                >
                                    <option v-for="osm in office_sale_man" :value="osm.id">{{osm.email}}</option>
                                </select>
                            </div>

                            <div class="form-group col-md-8">
                                <label for="brands">Brands</label>
                                <select multiple class="form-control brands"
                                    name="brands[]" v-model="form.brands" style="width:100%"
                                >
                                    <option v-for="brand in brands" :value="brand.id">{{brand.brand_name}}</option>
                                </select>
                            </div>
                            
                        </div> 

                        <div class="row local_supervisor_div" style="display:none;">
                            <div class="form-group col-md-8">
                                <label for="so_man">Sale Order Man</label>
                                <select multiple class="form-control so_man"
                                    name="so_man[]" v-model="form.so_man"style="width:100%"
                                >
                                    <option v-for="so_man in so_mans" :value="so_man.id">{{so_man.email}}</option>
                                </select>
                            </div>
                            
                        </div>  

                        <div class="row">
                            <div class="form-group col-md-4">
                                <label for="password">Password</label>
                                <input type="password" class="form-control" id="password" name="password" v-model="form.password" :required="!isEdit">
                            </div>
                            <div class="form-group col-md-4">
                                <label for="password_confirmation">Confirm Password</label>
                                <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" v-model="form.password_confirmation" :required="!isEdit">
                            </div>
                        </div>                    

                        <div class="row">
                            <div class="col-md-12">
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
                name: "",
                email: "",
                role_id: "",
                warehouse_id: "",
                phone: "",
                local_supervisors: [],
                office_sale_man: [],
                brands: [],
                so_man: [], 
                password: '', 
                password_confirmation: '',  
                branches: [],
                branch_id: '',         
              }),
              branches: [],
              isEdit: false,
              roles: [],
              warehouses:[],
              brands: [],
              local_supervisors: [],
              office_sale_man: [],
              so_mans: [],
              temp_local_supervisors: [],
              temp_office_sale_man: [],
              temp_brands: [],
              temp_so_man: [],
              temp_branches: [],
              user_role: '',
              user_year: '',
              user_id: '',
              site_path: '',
              storage_path: '',
            };
        },

        created() {

            this.user_role = document.querySelector("meta[name='user-role']").getAttribute('content');

            this.site_path = document.querySelector("meta[name='site-path']").getAttribute('content');
            //this.site_path = this.site_path.substring(this.site_path.lastIndexOf('/')+1);
            this.storage_path = document.querySelector("meta[name='storage-path']").getAttribute('content');
            
            this.user_year = document.querySelector("meta[name='user-year-likelink']").getAttribute('content');

            if(this.user_role != "system") {
                var url =  window.location.origin;
                window.location.replace(url);
            }

            if(this.$route.params.id) {
                this.isEdit = true;
                this.user_id = this.$route.params.id;
                this.getUser(this.user_id);
            }
        },
        mounted() {

            $("#loading").hide();
            let app = this;

            app.initRoles();
            app.initLocalSupervisors();
            app.initOfficeSaleMan();
            app.initSOMan();
            app.initBrands();
            //app.initWarehouses()

            app.initBranches();

            $("#branch_id").on("select2:select", function(e) {

                var data = e.params.data;
                app.form.branches = [];
                app.temp_branches = [];
                app.form.branch_id = data.id;
                app.getWarehouseByBranch(data.id,app.form.role_id);
            });

            $(".branches").select2();
            $(".branches").on("select2:select", function(e) {
                app.form.branch_id = '';
                var data = e.params.data;
                app.temp_branches.push(data.id); 

                var unique_arr = app.temp_branches.filter((a, b) => app.temp_branches.indexOf(a) === b);
                app.temp_branches = unique_arr;

                $('.branches').val(app.temp_branches).trigger('change');
                app.getWarehouseByBranch(app.temp_branches,app.form.role_id);
            });

            $(".branches").on("select2:unselect", function(e) {            
                var data = e.params.data;
                var unique_arr = app.temp_branches.filter((a, b) => app.temp_branches.indexOf(a) === b);
                app.temp_branches = unique_arr;                
                const index = app.temp_branches.indexOf(data.id);
                if (index > -1) {
                  app.temp_branches.splice(index, 1);
                }
                console.log(app.temp_branches);
                $('.branches').val(app.temp_branches).trigger('change');
                if(app.temp_branches == '') {
                    app.warehouses = [];
                } else {
                    app.getWarehouseByBranch(app.temp_branches,app.form.role_id);
                }
            });



            $("#role_id").select2();
            $("#role_id").on("select2:select", function(e) {
                var data = e.params.data;
                app.form.role_id = data.id;
                if(data.id == 6) {
                    $(".country_head_div").show();
                    $('.local_supervisors').val('').trigger('change');
                    $('.brands').val('').trigger('change');
                    $(".local_supervisor_div").hide();
                }
                else if(data.id == 7){
                    $(".country_head_div").hide();
                    $('.so_man').val('').trigger('change');
                    $(".local_supervisor_div").show();
                } else {
                    $(".country_head_div").hide();
                    $(".local_supervisor_div").hide();
                }

                if(data.id == 6 || data.id == 2) {
                    //for country head and admin roles
                    $(".branches_div").show();
                    //$('.branches').val('').trigger('change');
                    $('#branch_id').val('').trigger('change');
                    $(".branch_div").hide();
                } else {
                    //$('.branches').val('').trigger('change');
                    $('#branch_id').val('').trigger('change');
                    $(".branches_div").hide();
                    $(".branch_div").show(); 
                }
            });

            $(".local_supervisors").select2();
            $(".local_supervisors").on("select2:select", function(e) {
                var data = e.params.data;
                app.temp_local_supervisors.push(data.id); 

                var unique_arr = app.temp_local_supervisors.filter((a, b) => app.temp_local_supervisors.indexOf(a) === b);
                app.temp_local_supervisors = unique_arr;

                $('.local_supervisors').val(app.temp_local_supervisors).trigger('change');
            });

            $(".local_supervisors").on("select2:unselect", function(e) {
                var data = e.params.data;
                var unique_arr = app.temp_local_supervisors.filter((a, b) => app.temp_local_supervisors.indexOf(a) === b);
                app.temp_local_supervisors = unique_arr;
                const index = app.temp_local_supervisors.indexOf(data.id);
                if (index > -1) {
                  app.temp_local_supervisors.splice(index, 1);
                }
                $('.local_supervisors').val(app.temp_local_supervisors).trigger('change');

            });

            $(".office_sale_man").select2();
            $(".office_sale_man").on("select2:select", function(e) {
                var data = e.params.data;
                app.temp_office_sale_man.push(data.id); 

                var unique_arr = app.temp_office_sale_man.filter((a, b) => app.temp_office_sale_man.indexOf(a) === b);
                app.temp_office_sale_man = unique_arr;

                $('.office_sale_man').val(app.temp_office_sale_man).trigger('change');
            });

            $(".office_sale_man").on("select2:unselect", function(e) {
                var data = e.params.data;
                var unique_arr = app.temp_office_sale_man.filter((a, b) => app.temp_office_sale_man.indexOf(a) === b);
                app.temp_office_sale_man = unique_arr;
                const index = app.temp_office_sale_man.indexOf(data.id);
                if (index > -1) {
                  app.temp_office_sale_man.splice(index, 1);
                }
                $('.office_sale_man').val(app.temp_office_sale_man).trigger('change');

            });

            $(".brands").select2();
            $(".brands").on("select2:select", function(e) {
                var data = e.params.data;
                app.temp_brands.push(data.id); 

                var unique_arr = app.temp_brands.filter((a, b) => app.temp_brands.indexOf(a) === b);
                app.temp_brands = unique_arr;

                $('.brands').val(app.temp_brands).trigger('change');
            });

            $(".brands").on("select2:unselect", function(e) {
                var data = e.params.data;
                var unique_arr = app.temp_brands.filter((a, b) => app.temp_brands.indexOf(a) === b);
                app.temp_brands = unique_arr;
                const index = app.temp_brands.indexOf(data.id);
                if (index > -1) {
                  app.temp_brands.splice(index, 1);
                }
                $('.brands').val(app.temp_brands).trigger('change');

            });

            $(".so_man").select2();
            $(".so_man").on("select2:select", function(e) {
                var data = e.params.data;
                app.temp_so_man.push(data.id); 

                var unique_arr = app.temp_so_man.filter((a, b) => app.temp_so_man.indexOf(a) === b);
                app.temp_so_man = unique_arr;

                $('.so_man').val(app.temp_so_man).trigger('change');
            });

            $(".so_man").on("select2:unselect", function(e) {
                var data = e.params.data;
                var unique_arr = app.temp_so_man.filter((a, b) => app.temp_so_man.indexOf(a) === b);
                app.temp_so_man = unique_arr;
                const index = app.temp_so_man.indexOf(data.id);
                if (index > -1) {
                  app.temp_so_man.splice(index, 1);
                }
                $('.so_man').val(app.temp_so_man).trigger('change');

            });

            $("#warehouse_id").select2();
            $("#warehouse_id").on("select2:select", function(e) {

                var data = e.params.data;
                app.form.warehouse_id = data.id;
            });
           
        },

        methods: {

            initBranches() {
                axios.get("/branches").then(({ data }) => (this.branches = data.data));
                $("#branch_id").select2();
                $(".branches").select2();
            },

            initRoles() {
              axios.get("/role").then(({ data }) => (this.roles = data.data));
              $("#role_id").select2();
            },

            initWarehouses() {
              axios.get("/warehouses").then(({ data }) => (this.warehouses = data.data));
              $("#warehouse_id").select2();
            },

            getWarehouseByBranch(branch_id,role_id) {
                this.warehouses = [];
                var search = "&role_id=" + role_id + "&branch=" + branch_id;
                axios.get("/warehouses?" + search).then(({ data }) => (this.warehouses = data.data));
                $("#warehouse_id").select2();
            },

            initLocalSupervisors() {
              axios.get("/user/role/7").then(({ data }) => (this.local_supervisors = data.data));
              $(".local_supervisors").select2();
            },

            initOfficeSaleMan() {
              axios.get("/user/role/3").then(({ data }) => (this.office_sale_man = data.data));
              $(".office_sale_man").select2();
            },

            initSOMan() {
              axios.get("/user/role/4").then(({ data }) => (this.so_mans = data.data));
              $(".so_man").select2();
            },

            initBrands() {
              axios.get("/users/brands").then(({ data }) => (this.brands = data.data));
              $(".brands").select2();
            },

            getUser(id) {
              let app = this;
              $("#loading").show();
              axios
                .get("/user/" + id)
                .then(function(response) {
                    $("#loading").hide();
                    app.form.role_id      = response.data.user.role_id;
                    app.form.warehouse_id = response.data.user.warehouse_id;
                    app.form.name         = response.data.user.name;
                    app.form.email        = response.data.user.email;
                    app.form.phone        = response.data.user.phone;

                    if(response.data.user.role_id == 6 || response.data.user.role_id == 2) {
                        $(".branches_div").show();

                        /*var b2 = $(".branches").select2({
                            tags: true
                        });*/
                        var branch = '';
                        $.each(response.data.user.branches, function( key, value ) {  
                            app.temp_branches.push(String(value.id));   
                            app.form.branches.push(String(value.id)); 
                            if(branch == '') {
                                branch = value.id;
                            } else {
                                branch += ','+value.id;
                            }           
                            /*if(!b2.find('option[value="'+value.id+'"]').length) {
                                b2.append($('<option value="'+value.id+'">').text(value.branch_name));
                            }*/
                        });
                       // $('.branches').val(app.temp_branches).trigger("change");

                    } else {
                        $(".branch_div").show();
                        /*if(response.data.user.branch_id != null) {
                            app.form.branch_id = response.data.user.branch_id;
                            branch = app.form.branch_id;
                        }*/

                        $.each(response.data.user.branches, function( key, value ) { 
                             app.form.branch_id = String(value.id);
                             branch = app.form.branch_id;
                        });
                    }


                    if(response.data.user.role_id == 6) {
                        $(".country_head_div").show();
                    }
                    else if(response.data.user.role_id == 7){
                        $(".local_supervisor_div").show();
                    } else {}

                    var ls2 = $(".local_supervisors").select2({
                        tags: true
                    });
                     
                    var os2 = $(".so_man").select2({
                        tags: true
                    });

                    var bs2 = $(".brands").select2({
                        tags: true
                    });

                    var osm2 = $(".office_sale_man").select2({
                        tags: true
                    });


                    $.each(response.data.user.country_head_children, function( key, value ) {  
                        app.temp_local_supervisors.push(String(value.id)); 
                        app.form.local_supervisors.push(String(value.id));               
                        if(!ls2.find('option[value="'+value.id+'"]').length) {
                            ls2.append($('<option value="'+value.id+'">').text(value.email));
                        }
                    });

                    ls2.val(app.temp_local_supervisors).trigger("change");

                    $.each(response.data.user.brands, function( key, value ) {  
                        app.temp_brands.push(String(value.id));
                        app.form.brands.push(String(value.id));                
                        if(!bs2.find('option[value="'+value.id+'"]').length) {                        
                            bs2.append($('<option value="'+value.id+'">').text(value.brand_name));
                        }
                    });

                    bs2.val(app.temp_brands).trigger("change");

                    $.each(response.data.user.local_supervisor_children, function( key, value ) {  
                        app.temp_so_man.push(String(value.id));
                        app.form.so_man.push(String(value.id));                
                        if(!os2.find('option[value="'+value.id+'"]').length) {
                            os2.append($('<option value="'+value.id+'">').text(value.email));
                        }
                    });

                    os2.val(app.temp_so_man).trigger("change");

                    $.each(response.data.user.office_sale_mans, function( key, value ) {  
                        app.temp_office_sale_man.push(String(value.id));  
                        app.form.office_sale_man.push(String(value.id));              
                        /*if(!osm2.find('option[value="'+value.id+'"]').length) {
                            osm2.append($('<option value="'+value.id+'">').text(value.email));
                        }*/
                    });

                    osm2.val(app.temp_office_sale_man).trigger("change");

                    app.getWarehouseByBranch(branch,app.form.role_id);

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
                $("#loading").show();
                if (!this.isEdit) {
                    app.form.local_supervisors = app.temp_local_supervisors.filter((a, b) => app.temp_local_supervisors.indexOf(a) === b);

                    app.form.branches = app.temp_branches.filter((a, b) => app.temp_branches.indexOf(a) === b);

                    app.form.office_sale_man = app.temp_office_sale_man.filter((a, b) => app.temp_office_sale_man.indexOf(a) === b);
                    app.form.brands = app.temp_brands.filter((a, b) => app.temp_brands.indexOf(a) === b);
                    app.form.so_man = app.temp_so_man.filter((a, b) => app.temp_so_man.indexOf(a) === b);
                    //console.log(app.form.local_supervisors); console.log(app.form.brands);
                    this.form
                      .post("/user")
                      .then(function(data) {
                        console.log(data.data);
                        $("#loading").hide();
                        if(data.status == "success") {
                            swal({
                                title: "Success!",
                                text: 'User is created.',
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
                      if(response.errors.email){
                        error += response.errors.email[0];
                        error += '\n';
                      }
                      if(response.errors.password){
                        error += response.errors.password[0];
                        error += '\n';
                      }

                      swal("Warning!", error, "warning");

                    });
                } else {
                    //Edit entry details

                    app.form.local_supervisors = app.temp_local_supervisors.filter((a, b) => app.temp_local_supervisors.indexOf(a) === b);

                    app.form.branches = app.temp_branches.filter((a, b) => app.temp_branches.indexOf(a) === b);

                    app.form.office_sale_man = app.temp_office_sale_man.filter((a, b) => app.temp_office_sale_man.indexOf(a) === b);
                    app.form.brands = app.temp_brands.filter((a, b) => app.temp_brands.indexOf(a) === b);
                    app.form.so_man = app.temp_so_man.filter((a, b) => app.temp_so_man.indexOf(a) === b);

                    this.form
                      .patch("/user/" + app.user_id)
                      .then(function(data) {
                        $("#loading").hide();
                        if(data.status == "success") {
                            //reset form data
                            swal({
                                title: "Success!",
                                text: 'User is updated.',
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
                        if(response.errors.email){
                        error += response.errors.email[0];
                        error += '\n';
                        }
                        if(response.errors.password){
                        error += response.errors.password[0];
                        error += '\n';
                        }

                        swal("Warning!", error, "warning");

                    });
                }
            },
        }
    }
</script>