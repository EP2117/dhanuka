<template>
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="#!"><i class="feather icon-home"></i></a></li>
                <li class="breadcrumb-item"><a :href="site_path+'/'">Home</a></li>
                <li class="breadcrumb-item"><a :href="site_path+'/master'">Master</a></li>
                <li class="breadcrumb-item"><router-link tag="span" to="/township" class="font-weight-normal"><a href="#">Township</a></router-link></li>
                <li class="breadcrumb-item active" aria-current="page">Township Form</li>
            </ol>
        </nav>
        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h4 class="mb-0 text-gray-800" v-if="!isEdit">Create New Township</h4>
            <h4 class="mb-0 text-gray-800" v-else>Edit Supplier</h4>
        </div>

        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Township Details</h6>
            </div>
            <div class="card-body">
                <div class="d-block">
                    <form class="form" method="post" @submit.prevent="onSubmit" @keydown="form.errors.clear($event.target.name)">
                        <div class="row mt-3">
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
                                <label for="township_name">Township Name</label>
                                <input type="text" class="form-control" id="township_name" name="township_name"
                                       v-model="form.township_name" required>
                            </div>
                        </div>
                        <div class="form-group row text-center">
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
                township_name: '',
                state_id: "",
            }),
            states: [],
            isEdit: false,
            township_id: '',
            user_role: '',
            site_path: '',
            storage_path: '',
        };
    },

    created() {
        this.user_role = document.querySelector("meta[name='user-role']").getAttribute('content');

        this.site_path = document.querySelector("meta[name='site-path']").getAttribute('content');
        this.storage_path = document.querySelector("meta[name='storage-path']").getAttribute('content');

        if(this.user_role != "admin" && this.user_role != "system") {
            var url =  window.location.origin;
            window.location.replace(url);
        }

        if(this.$route.params.id) {
            // console.log('aaaa');
            this.isEdit = true;
            this.township_id = this.$route.params.id;
            this.getTownship(this.township_id);
        }
        console.log(this.isEdit);
    },
    mounted() {
        //console.log('Component mounted.')
        $("#loading").hide();
        let app = this;
        $("#state_id").select2();
        $("#state_id").on("select2:select", function(e) {
            app.townships = [];
            var data = e.params.data;
            app.form.state_id = data.id;
            axios.get("/township_by_state/"+ data.id).then(({ data }) => (app.townships = data.data));
        });
        app.initStates();
    },
    methods: {
        // initTypes() {
        //     axios.get("/customer_type").then(({ data }) => (this.cus_types = data.data));
        //     //console.log(this.cus_types);
        //     $("#cus_type").select2();
        // },

        initStates() {
            if(this.form.country_id != "") {
                axios.get("/state_by_country/1").then(({ data }) => (this.states = data.data));
                console.log(this.states);
                $("#state_id").select2();
            }
        },

        getTownship(id) {
            let app = this;
            axios
                .get("/township/" + id)
                .then(function(response) {
                    console.log(response);
                    app.form.township_name = response.data.township.township_name;
                    app.form.state_id = response.data.township.state_id;
                    $('#state_id').val(app.form.state_id).trigger('change');
                    $("#state_id").select2();

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
            console.log(this.form);
            if (!this.isEdit) {
                this.form
                    .post("/township")
                    .then(function(data) {
                        if(data.status == "success") {
                            $("#loading").hide();
                            swal({
                                title: "Success!",
                                text: 'Township is added.',
                                icon: "success",
                                button: true
                            }).then(function() {
                                 location.reload();

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
                        swal({
                                title: "Error!",
                                text: 'Township name is invalid.',
                                icon: "warning",
                                button: true
                            });
                        // console.log(response.errors);
                        /*var obj = $.parseJSON(response.errors);
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
                        });*/

                    });

            } else {
                // console.log(this);
                //Edit Customer
                var vm=this;
                this.form
                    .patch('/township/'+ app.township_id)
                    .then(function(data) {
                        // console.log(data);
                        if(data.status == "success") {
                            swal({
                                title: "Success!",
                                text: 'Township is updated.',
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
                        /**var obj = $.parseJSON(response.errors);
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
                        });***/
                        swal({
                                title: "Error!",
                                text: 'Township name is invalid.',
                                icon: "warning",
                                button: true
                            });

                    });
                //End Edit Customer
            }
        },
    }
}
</script>
<style scoped>

</style>
