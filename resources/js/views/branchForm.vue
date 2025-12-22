<template>
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="#!"><i class="feather icon-home"></i></a></li>
                <li class="breadcrumb-item"><a :href="site_path+'/'">Home</a></li>
                <li class="breadcrumb-item"><a :href="site_path+'/master'">Master</a></li>
                <li class="breadcrumb-item"><router-link tag="span" to="/branch" class="font-weight-normal"><a href="#">Branch</a></router-link></li>
                <li class="breadcrumb-item active" aria-current="page">Branch Form</li>
            </ol>
        </nav>
        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h4 class="mb-0 text-gray-800" v-if="!isEdit">Create New Branch</h4>
            <h4 class="mb-0 text-gray-800" v-else>Edit Branch</h4>
        </div>

        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Branch Details</h6>
            </div>
            <div class="card-body">
                <div class="d-block">
                    <!-- form start -->
                    <form class="form" method="post" @submit.prevent="onSubmit" @keydown="form.errors.clear($event.target.name)">

                        <div class="row mt-3">

                            <div class="form-group col-md-4">
                                <label for="branch_name">Branch Name</label>
                                 <input type="text" class="form-control" id="branch_name" name="branch_name"
                                v-model="form.branch_name" required>
                            </div>

                            <div class="form-group col-md-8 mt-4 pt-2">
                                <label class="">&nbsp;</label>
                                <input type="submit" class="btn btn-primary btn-sm" value="Save Changes">
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
                branch_name: '',
              }),
              isEdit: false,
              branch_id: '',
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
            if(this.user_role != "system") {
                var url =  window.location.origin;
                window.location.replace(url);
            }

            if(this.$route.params.id) {
                this.isEdit = true;
                this.branch_id = this.$route.params.id;
                this.getBranch(this.branch_id);
            }
            //console.log(this.isEdit);
        },
        mounted() {
            //console.log('Component mounted.')
            $("#loading").hide();
            let app = this;
        },

        methods: {

            getBranch(id) {
              let app = this;
              axios
                .get("/branch/" + id)
                .then(function(response) {
                    app.form.branch_name = response.data.branch.branch_name;

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
                  .post("/branch")
                  .then(function(data) {
                    console.log(data);
                    if(data.status == "success") {
                        //reset form data
                        event.target.reset();

                        swal({
                            title: "Success!",
                            text: 'Branch Added.',
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
                      .patch("/branch/" + app.branch_id)
                      .then(function(data) {
                        console.log(data);
                        //reset form data
                        event.target.reset();
                        if(data.status == "success") {
                            swal({
                                title: "Success!",
                                text: 'Branch is updated.',
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
