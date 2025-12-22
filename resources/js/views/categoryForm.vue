<template>
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="#!"><i class="feather icon-home"></i></a></li>
                <li class="breadcrumb-item"><a :href="site_path+'/'">Home</a></li>
                <li class="breadcrumb-item"><a :href="site_path+'/master'">Master</a></li>
                <li class="breadcrumb-item"><router-link tag="span" to="/category" class="font-weight-normal"><a href="#">Category</a></router-link></li>
                <li class="breadcrumb-item active" aria-current="page">Category Form</li>

            </ol>
        </nav>
        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h4 class="mb-0 text-gray-800" v-if="!isEdit">Create New Category</h4>
            <h4 class="mb-0 text-gray-800" v-else>Edit Category</h4>
        </div>

        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Category Details</h6>
            </div>
            <div class="card-body">
                <div class="d-block">
                    <!-- form start -->
                    <form class="form" method="post" @submit.prevent="onSubmit" @keydown="form.errors.clear($event.target.name)">

                        <div class="row mt-3">
                            <div class="form-group col-md-4">
                                <label for="category_name">Brand</label>
                                <select id="brand_id" class="form-control form-control-alternative"
                                    name="brand_id" v-model="form.brand_id" style="width:100%" required
                                >
                                    <option value="">Select One</option>
                                    <option v-for="brand in brands" :value="brand.id">{{brand.brand_name}}</option>
                                </select>
                            </div>
                            <div class="form-group col-md-4">
                                <label for="category_name">Category Name</label>
                                 <input type="text" class="form-control" id="category_name" name="category_name"
                                v-model="form.category_name" required>
                            </div>
                            <div class="form-group col-md-4 mt-2">
                                <label class="col-lg-3 col-form-label form-control-label"></label>
                                <div class="col-lg-12">
                                    <input type="reset" class="btn btn-secondary btn-sm" value="Cancel" v-if="!isEdit">
                                    <input type="submit" class="btn btn-primary btn-sm" value="Save" v-if="!isEdit || (isEdit && user_role != 'Account')">
                                </div>
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
                brand_id: '',
                category_name: '',
              }),
              brands: [],
              isEdit: false,
              category_id: '',
              user_role: '',
              site_path: '',
              storage_path: '',
            };
        },

        created() {
            this.user_role = document.querySelector("meta[name='user-role']").getAttribute('content');

            this.site_path = document.querySelector("meta[name='site-path']").getAttribute('content');
            //this.site_path = this.site_path.substring(this.site_path.lastIndexOf('/')+1);
            this.storage_path = document.querySelector("meta[name='storage-path']").getAttribute('content');

            /*if(this.user_role != "admin" && this.user_role != "system" && this.user_role != "office_user") {
                var url =  window.location.origin;
                window.location.replace(url);
            }*/

            if(this.$route.params.id) {
                this.isEdit = true;
                this.category_id = this.$route.params.id;
                this.getCategory(this.category_id);
            }
            //console.log(this.isEdit);
        },
        mounted() {
            //console.log('Component mounted.')
            $("#loading").hide();
            let app = this;

            $("#brand_id").select2();
            $("#brand_id").on("select2:select", function(e) {

                var data = e.params.data;
                app.form.brand_id = data.id;
            });

            app.initBrands();
        },

        methods: {

            initBrands() {
              axios.get("/brands").then(({ data }) => (this.brands = data.data));
              $("#brand_id").select2();
            },

            getCategory(id) {
              let app = this;
              axios
                .get("/category/" + id)
                .then(function(response) {
                    app.form.brand_id = response.data.category.brand_id;
                    app.form.category_name = response.data.category.category_name;
                    
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
                $("#loading").show();               

                if (!this.isEdit) {

                this.form
                  .post("/category")
                  .then(function(data) {
                    console.log(data);
                    if(data.status == "success") {
                        //reset form data
                        event.target.reset();
                        $("#loading").hide();

                        swal({
                            title: "Success!",
                            text: 'Category is added.',
                            icon: "success",
                            button: true
                        }).then(function() {
                           // location.reload();
                           

                        });
                    } else {
                      $.notify("Already Exist!", {
                        autoHideDelay: 3000,
                        gap: 1,
                        className: "error"
                      });
                      $("#loading").hide();
                    }
                  })
                    .catch(function (response)  {
                      // console.log(response.errors);
                        $("#loading").hide();
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

                    //Edit Brand                  
                    this.form
                      .patch("/category/" + app.category_id)
                      .then(function(data) {
                        console.log(data);
                        //reset form data
                        event.target.reset();  
                        if(data.status == "success") {
                            swal({
                                title: "Success!",
                                text: 'Category is updated.',
                                icon: "success",
                                button: true
                            }).then(function() {
                                location.reload();

                            });
                        } else {
                          $.notify("Already Exist!", {
                            autoHideDelay: 3000,
                            gap: 1,
                            className: "error"
                          });
                          $("#loading").hide();
                        }
                      })
                        .catch(function (response)  {
                          // console.log(response.errors);
                            $("#loading").hide();
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
                    //End Edit Brand
                  }
            },
        }
    }
</script>