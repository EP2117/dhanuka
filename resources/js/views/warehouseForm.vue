<template>
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="#!"><i class="feather icon-home"></i></a></li>
                <li class="breadcrumb-item"><a :href="site_path+'/'">Home</a></li>
                <li class="breadcrumb-item"><a :href="site_path+'/master'">Master</a></li>
                <li class="breadcrumb-item"><router-link tag="span" to="/warehouse" class="font-weight-normal"><a href="#">Warehouse</a></router-link></li>
                <li class="breadcrumb-item active" aria-current="page">Warehouse Form</li>

            </ol>
        </nav>
        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h4 class="mb-0 text-gray-800" v-if="!isEdit">Create New Warehouse</h4>
            <h4 class="mb-0 text-gray-800" v-else>Edit Warehouse</h4>
        </div>

        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Warehouse Details</h6>
            </div>
            <div class="card-body">
                <div class="d-block">
                    <!-- form start -->
                    <form class="form" method="post" @submit.prevent="onSubmit" @keydown="form.errors.clear($event.target.name)">

                        <div class="row mt-3">

                            <div class="form-group col-md-4">
                                <label for="wh_type">Warehouse Type</label>
                                <select id="wh_type" class="form-control"
                                    name="wh_type" v-model="form.wh_type" style="width:100%" required 
                                >
                                    <option value="">Select One</option>
                                    <option value="1">Office Warehouse</option>
                                    <option value="2">Vehicle Warehouse</option>
                                </select>
                            </div>
                        </div>

                        <div class="row mt-3">

                            <div class="form-group col-md-4">
                                <label for="warehouse_name">Warehouse Name</label>
                                 <input type="text" class="form-control" id="warehouse_name" name="warehouse_name"
                                v-model="form.warehouse_name" required>
                            </div> 
                            <div class="form-group col-md-4">
                                <label for="branch_id">Branch</label>
                                <select id="branch_id" class="form-control"
                                    name="branch_id" v-model="form.branch_id" style="width:100%" required 
                                >
                                    <option value="">Select One</option>
                                    <option v-for="branch in branches" :value="branch.id"  >{{branch.branch_name}}</option>
                                </select>
                            </div>
                            
                        </div>

                        <div class="row">
                            <div class="form-group col-md-8 text-right">
                                <label class="">&nbsp;</label>                                
                                <input type="submit" class="btn btn-primary btn-sm" value="Save">&nbsp;
                                <input type="reset" class="btn btn-secondary btn-sm" value="Cancel" v-if="!isEdit">
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
                warehouse_name: '',
                branch_id: '',
                wh_type: '',
              }),
              isEdit: false,
              warehouse_id: '',
              user_role: '',
              branches: [],
              site_path: '',
              storage_path: '',
            };
        },

        created() {
            this.user_role = document.querySelector("meta[name='user-role']").getAttribute('content');

            this.site_path = document.querySelector("meta[name='site-path']").getAttribute('content');
            //this.site_path = this.site_path.substring(this.site_path.lastIndexOf('/')+1);
            this.storage_path = document.querySelector("meta[name='storage-path']").getAttribute('content');
            
            if(this.user_role != "system") {
                var url =  window.location.origin;
                window.location.replace(url);
            }

            if(this.$route.params.id) {
                this.isEdit = true;
                this.warehouse_id = this.$route.params.id;
                this.getWarehouse(this.warehouse_id);
            }
            //console.log(this.isEdit);
        },
        mounted() {
            //console.log('Component mounted.')
            $("#loading").hide();
            let app = this;
            app.initBranches();

            $("#branch_id").on("select2:select", function(e) {

                var data = e.params.data;
                app.form.branch_id = data.id;
            });
        },

        methods: {

            initBranches() {
                axios.get("/branches").then(({ data }) => (this.branches = data.data));
                $("#branch_id").select2();
            },

            getWarehouse(id) {
              let app = this;
              axios
                .get("/warehouse/" + id)
                .then(function(response) {
                    app.form.warehouse_name = response.data.warehouse.warehouse_name;
                    app.form.branch_id = response.data.warehouse.branch_id;
                    $("#branch_id").attr('disabled',true);
                    app.form.wh_type = response.data.warehouse.warehouse_type;
                    
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

                if (!this.isEdit) {

                this.form
                  .post("/warehouse")
                  .then(function(data) {
                    console.log(data);
                    if(data.status == "success") {
                        //reset form data
                        event.target.reset();
                        $("#branch_id").select2();
                        swal({
                            title: "Success!",
                            text: 'Warehouse Added.',
                            icon: "success",
                            button: true
                        }).then(function() {
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
                    
                  } else {

                    //Edit Customer                    
                    this.form
                      .patch("/warehouse/" + app.warehouse_id)
                      .then(function(data) {
                        console.log(data);
                        //reset form data
                        event.target.reset(); 
                        if(data.status == "success") {
                            swal({
                                title: "Success!",
                                text: 'Warehouse is updated.',
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
        }
    }
</script>