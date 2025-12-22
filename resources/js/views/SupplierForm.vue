<template>
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="#!"><i class="feather icon-home"></i></a></li>
                <li class="breadcrumb-item"><a :href="site_path+'/'">Home</a></li>
                <li class="breadcrumb-item"><a :href="site_path+'/master'">Master</a></li>
                <li class="breadcrumb-item"><router-link tag="span" to="/supplier" class="font-weight-normal"><a href="#">Supplier</a></router-link></li>
                <li class="breadcrumb-item active" aria-current="page">Supplier Form</li>
            </ol>
        </nav>
        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h4 class="mb-0 text-gray-800" v-if="!isEdit">Create New Supplier</h4>
            <h4 class="mb-0 text-gray-800" v-else>Edit Supplier</h4>
        </div>

        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Supplier Details</h6>
            </div>
            <div class="card-body">
                <div class="d-block">
                    <form class="form" method="post" @submit.prevent="onSubmit" @keydown="form.errors.clear($event.target.name)">
                        <div class="row mt-3">
                            <div class="form-group col-md-4">
                                <label for="cus_code">Supplier Code</label>
                                <input type="text" class="form-control" id="cus_code" name="supplier_code"
                                       v-model="form.supplier_code" readonly>
                            </div>

                            <div class="form-group col-md-4">
                                <label for="cus_name">Supplier Name</label>
                                <input type="text" class="form-control" id="cus_name" name="cus_name"
                                       v-model="form.supplier_name" required>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="form-group col-md-4">
                                <label for="country_id">Country</label>
                                <select id="country_id" class="form-control"
                                        name="country_id" v-model="form.country_id" style="width:100%" required
                                >
                                    <option value="">Select One</option>
                                    <option v-for="country in countries" :value="country.id"  >{{country.country_name}}</option>
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
                                <label>Phone</label>
                                <input type="text" class="form-control" id="supplier_phone_number"
                                       v-model="form.supplier_phone_number" required>
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

                        <div class="form-group row text-right">
                            <label class="col-lg-3 col-form-label form-control-label"></label>
                            <div class="col-lg-12">
                                <input type="reset" class="btn btn-secondary btn-sm" value="Cancel" v-if="!isEdit">
                                <input type="submit" class="btn btn-primary btn-sm" value="Save Changes" v-if="!isEdit || (isEdit && user_role != 'Account')">
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
                supplier_name: '',
                supplier_code: '',
                country_id: '',
                state_id: "",
                township_id : '',
                supplier_phone_number: '',
                billing_address: '',
                shipping_address: '',
            }),
            countries: [],
            states: [],
            townships: [],
            isEdit: false,
            supplier_id: '',
            user_role: '',
            site_path: '',
            storage_path: '',
        };
    },

    created() {
        this.user_role = document.querySelector("meta[name='user-role']").getAttribute('content');

        this.site_path = document.querySelector("meta[name='site-path']").getAttribute('content');
        this.storage_path = document.querySelector("meta[name='storage-path']").getAttribute('content');

        /*if(this.user_role != "admin" && this.user_role != "system" && this.user_role != "office_user") {
            var url =  window.location.origin;
            window.location.replace(url);
        }*/

        if(this.$route.params.id) {
            // console.log('aaaa');
            this.isEdit = true;
            this.supplier_id = this.$route.params.id;
            this.getSupplier(this.supplier_id);
        }
        console.log(this.isEdit);
    },
    mounted() {
        //console.log('Component mounted.')
        $("#loading").hide();
        let app = this;

        $("#country_id").select2();
        // console.log($('#country_id').select2());
        $("#country_id").on("select2:select", function(e) {
            app.states = [];
            app.townships = [];
            var data = e.params.data;
            app.form.country_id = data.id;
            axios.get("/state_by_country/"+ data.id).then(({ data }) => (app.states = data.data));
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
        // app.initTypes();
        app.initCountries();
        app.initStates();
        app.initTownships();
    },
    methods: {
        // initTypes() {
        //     axios.get("/customer_type").then(({ data }) => (this.cus_types = data.data));
        //     //console.log(this.cus_types);
        //     $("#cus_type").select2();
        // },
        initCountries() {
            axios.get("/country").then(({ data }) => (this.countries = data.data));
            this.form.country_id=1;
              $("#country_id").val(1).trigger('change');
            // $("#country_id").select2();
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

        getSupplier(id) {
            let app = this;
            axios
                .get("/supplier/edit/" + id)
                .then(function(response) {
                    console.log(response);
                    app.form.supplier_name = response.data.supplier.name;
                    app.form.supplier_code = response.data.supplier.supplier_code;
                    app.form.country_id = response.data.supplier.country_id;
                    $('#country_id').val(app.form.country_id).trigger('change');
                    app.form.state_id = response.data.supplier.state_id;
                    $('#state_id').val(app.form.state_id).trigger('change');
                    app.form.township_id = response.data.supplier.township_id;
                    $('#township_id').val(app.form.township_id).trigger('change');
                    app.form.supplier_phone_number = response.data.supplier.phone_number;
                    app.form.shipping_address = response.data.supplier.supplier_shipping_address;
                    app.form.billing_address = response.data.supplier.supplier_billing_address;
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
            $("#loading").show();
            console.log(this.form);
            if (!this.isEdit) {
                this.form
                    .post("/supplier/create")
                    .then(function(data) {
                        if(data.status == "success") {
                            event.target.reset();
                            $("#cus_type").select2();
                            $("#country_id").select2();
                            $("#state_id").select2();
                            $("#township_id").select2();
                            $("#loading").hide();
                            swal({
                                title: "Success!",
                                text: 'Supplier Added.',
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
                // console.log(this);
                //Edit Customer
                var vm=this;
                this.form
                    .patch('/supplier/'+ app.supplier_id+'/update')
                    .then(function(data) {
                        // console.log(data);
                        //reset form data
                        event.target.reset();
                        $("#cus_type").select2();
                        $("#country_id").select2();
                        $("#state_id").select2();
                        $("#township_id").select2();
                        if(data.status == "success") {
                            swal({
                                title: "Success!",
                                text: 'Supplier is updated.',
                                icon: "success",
                                button: true
                            }).then(function() {
                                // this.$router.push('/supplier');
                                // location.replace('/supplier');
                                // location.reload();
                                vm.$router.push({name:'supplier'})
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
<style scoped>

</style>
