<template>
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="#!"><i class="feather icon-home"></i></a></li>
                <li class="breadcrumb-item"><a href="/">Home</a></li>
                <li class="breadcrumb-item"><a href="/master">Master</a></li>
                <li class="breadcrumb-item"><router-link tag="span" to="/currency" class="font-weight-normal"><a href="#">Currency</a></router-link></li>
                <li class="breadcrumb-item active" aria-current="page">Currency Form</li>

            </ol>
        </nav>
        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h4 class="mb-0 text-gray-800" v-if="!isEdit">Create New Currency</h4>
            <h4 class="mb-0 text-gray-800" v-else>Edit Currency</h4>
        </div>

        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Currency Details</h6>
            </div>
            <div class="card-body">
                <div class="d-block">
                    <!-- form start -->
                    <form class="form" method="post" @submit.prevent="onSubmit" @keydown="form.errors.clear($event.target.name)">

                        <div class="row mt-3">
                            <div class="form-group col-md-4">
                                <label for="name">Currency Name</label>
                                 <input type="text" class="form-control" id="name" name="name"
                                v-model="form.name" required>
                            </div>
                            <div class="form-group col-md-2">
                                <label for="sign">Currency Sign</label>
                                 <input type="text" class="form-control" id="sign" name="sign"
                                v-model="form.sign" required>
                            </div>
                            <div class="form-group col-md-4 mt-2">
                                <label class="col-lg-3 col-form-label form-control-label"></label>
                                <div class="col-lg-12">
                                    <input type="reset" class="btn btn-secondary btn-sm" value="Cancel" v-if="!isEdit">
                                    <input type="submit" class="btn btn-primary btn-sm" value="Save">
                                </div>
                            </div>
                        </div>

                    </form>                    
                <!-- form end -->  
                </div>

            </div>
        </div>
        <div id="loading" class="text-center"><img src="/storage/image/loader_2.gif" /></div>
    </div>

</template>

<script>
    export default {
        data() {
            return {
              form: new Form({
                name: '',
                sign: '',
              }),
              isEdit: false,
              currency_id: '',
              user_role: '',
            };
        },

        created() {
            this.user_role = document.querySelector("meta[name='user-role']").getAttribute('content');
            if(this.user_role != "system") {
                var url =  window.location.origin;
                window.location.replace(url);
            }

            if(this.$route.params.id) {
                this.isEdit = true;
                this.currency_id = this.$route.params.id;
                this.getCurrency(this.currency_id);
            }
            //console.log(this.isEdit);
        },
        mounted() {
            //console.log('Component mounted.')
            $("#loading").hide();
            let app = this;
        },

        methods: {

           /*** getBrand(id) {
              let app = this;
              axios
                .get("/brand/" + id)
                .then(function(response) {
                    app.form.brand_name = response.data.brand.brand_name;
                    
                })
                .catch(function(error) {
                  // handle error
                  console.log(error);
                })
                .then(function() {
                  // always executed
                });
            },**/

            onSubmit: function(event){
                let app = this; 
                $("#loading").show();               

                if (!this.isEdit) {

                this.form
                  .post("/currency")
                  .then(function(data) {
                    console.log(data);
                    if(data.status == "success") {
                        //reset form data
                        event.target.reset();
                        $("#loading").hide();

                        swal({
                            title: "Success!",
                            text: 'Currency is created.',
                            icon: "success",
                            button: true
                        }).then(function() {
                           // location.reload();
                           

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
                    /***this.form
                      .patch("/brand/" + app.brand_id)
                      .then(function(data) {
                        console.log(data);
                        //reset form data
                        event.target.reset();  
                        if(data.status == "success") {
                            swal({
                                title: "Success!",
                                text: 'Brand is updated.',
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

                        });****/
                    //End Edit Brand
                  }
            },
        }
    }
</script>