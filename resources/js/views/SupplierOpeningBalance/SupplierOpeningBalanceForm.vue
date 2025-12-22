<template>
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="#!"><i class="feather icon-home"></i></a></li>
                <li class="breadcrumb-item"><a :href="site_path+'/'">Home</a></li>
<!--                <li class="breadcrumb-item"><a :href="site_path+'/master'">Master</a></li>-->
                <li class="breadcrumb-item"><router-link tag="span" to="/supplier_ob" class="font-weight-normal"><a href="#">Supplier Opening Balance</a></router-link></li>
                <li class="breadcrumb-item active" aria-current="page">Supplier Opening Balance Form</li>
            </ol>
        </nav>
        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h4 class="mb-0 text-gray-800" v-if="!isEdit">Create New Supplier Opening Balance</h4>
            <h4 class="mb-0 text-gray-800" v-else>Edit Supplier Opening Balance</h4>

            <a onClick="history.go(-1);" class="btn btn-primary btn text-white text-right" value="Back"><i class="fas fa-angle-double-left"></i> Back</a>
        </div>

        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Supplier Opening Balance Details</h6>
            </div>
            <div class="card-body">
                <div class="d-block">
                    <!-- form start -->
                    <form class="form" method="post" @submit.prevent="onSubmit" @keydown="form.errors.clear($event.target.name)">

                        <div class="form-group row">
                            <label class="col-lg-3 text-right col-form-label form-control-label">Invoice No</label>
                            <div class="col-lg-6 col-md-offset-2">
                                <input class="form-control" type="text"
                                       id="invoice_no" name="invoice_no"
                                       v-model="form.invoice_no" readonly >
                            </div>
                        </div>
                        <div class="form-group  row">
                            <label class="col-lg-3 text-right col-form-label form-control-label">Date</label>
                            <div class="col-lg-6 col-md-offset-2">
                                <input class="form-control datetimepicker" type="text"
                                       id="date" autocomplete="off" v-model="form.opening_date" required >
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-lg-3 text-right col-form-label form-control-label">Supplier</label>
                            <div class="col-lg-6">
                                <select id="supplier_id" class="form-control form-control-alternative"
                                        name="supplier" v-model="form.supplier_id" style="width:100%" required>
                                    <option value="">Select One</option>
                                    <option v-for="sup in suppliers" :value="sup.id"  >{{sup.name}}</option>
                                </select>
                            </div>
                        </div>


                        <div class="form-group row">
                            <label class="col-lg-3 col-form-label text-right form-control-label">Amount</label>
                            <div class="col-lg-6">
                                <input class="form-control" type="text"
                                       id="amount" name="amount"
                                       v-model="form.amount" autocomplete="off">
                            </div>
                        </div>
                        <div class="form-group row text-right" v-if="check_collection<=0">
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
<!--        <div id="loading" class="text-center"><img :src="storage_path+'/image/loader_2.gif'" /></div>-->
    </div>

</template>

<script>
export default {
    data(){
        return{
            form:new Form({
                opening_date:'',
                invoice_no:'',
                supplier_id:'',
                amount:'',
            }),
            isEdit:false,
            suppliers:[],
            supplier_ob_id:'',
            site_path:'',
            check_collection:0,
            user_role:'',
            storage_path:'',
            user_year:'',
        }
    },
    created() {
        this.user_role = document.querySelector("meta[name='user-role']").getAttribute('content');

        this.site_path = document.querySelector("meta[name='site-path']").getAttribute('content');
        //this.site_path = this.site_path.substring(this.site_path.lastIndexOf('/')+1);
        this.storage_path = document.querySelector("meta[name='storage-path']").getAttribute('content');
        this.user_year = document.querySelector("meta[name='user-year-likelink']").getAttribute('content');

        if(this.user_role != "admin" && this.user_role != "system" && this.user_role != "office_user") {
            var url =  window.location.origin;
            window.location.replace(url);
        }
        if(this.$route.params.id) {
            this.isEdit = true;
            this.supplier_ob_id = this.$route.params.id;
            this.getSupplierOB(this.supplier_ob_id);

        } else {
        }
    },
    mounted() {
        let app=this;
        app.initSuppliers();

        $("#supplier_id").select2();
        $("#supplier_id").on("select2:select", function(e) {
            var data = e.params.data;
            app.form.supplier_id = data.id;
        });
        $("#date")
            .datetimepicker({
                icons: {
                    time: "fa fa-clock-o",
                    date: "fa fa-calendar",
                    up: "fa fa-chevron-up",
                    down: "fa fa-chevron-down",
                    previous: "fa fa-chevron-left",
                    next: "fa fa-chevron-right",
                    today: "fa fa-screenshot",
                    clear: "fa fa-trash",
                    close: "fa fa-remove"
                },
                format:"YYYY-MM-DD",
                minDate: app.user_year+"-01-01",
                maxDate: app.user_year+"-12-31",
            }).on("dp.show", function(e) {
            var y = new Date().getFullYear();
            // if(app.user_year < y) {
            //     if(app.form.date == app.user_year+"-12-31" ||  app.form.date == '') {
            //         // app.form.date = app.user_year+"-12-31";
            //     }
            // }
        }).on("dp.change", function(e) {
            // alert('a');
            // console.log(e);
            var formatedValue = e.date.format("YYYY-MM-DD");
            app.form.opening_date = formatedValue;
        });



    },
    methods:{
        initSuppliers() {
            axios.get("/supplier").then(({ data }) => (this.suppliers = data.data));
            $("#supplier_id").select2();
        },
        getSupplierOB(id){
            let app=this;
            axios.get("/supplier_ob/edit/" + id).then(function (response){
                console.log(response.data);
                var r=response.data.supplier_ob;
                app.form.invoice_no=r.invoice_no;
                app.form.amount=r.total_amount;
                app.check_collection=r.collection_amount;
                // app.form.opening_date=r.opening_date;
                app.form.supplier_id=r.supplier_id;
                app.form.opening_date = moment(r.opening_date).format('YYYY-MM-DD');
                $('#supplier_id').val(app.form.supplier_id).trigger('change');
            });
        },
        onSubmit:function (event){
            var app = this;
            $("#loading").show();
            if (!this.isEdit) {
                this.form.post('/supplier_ob/store').then(function (data){
                    if(data.status=='success'){
                        console.log(data);
                        event.target.reset();
                        $("#supplier_id").select2();
                        $("#loading").hide();
                        swal({
                            title: "Success!",
                            text: 'Supplier Opening Balance Added.',
                            icon: "success",
                            button: true
                        }).then(function() {
                            app.$router.push({name:'supplier_ob'});

                        });
                    }else {
                        $.notify("Error", {
                            autoHideDelay: 3000,
                            gap: 1,
                            className: "error"
                        });
                    }
                }) .catch(function (response)  {
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
            }else{
                // console.log(this.form);
                this.form.patch('/supplier_ob/update/'+app.supplier_ob_id).then(function (data){
                    if(data.status=='success'){
                        event.target.reset();
                        $("#supplier_id").select2();
                        $("#loading").hide();
                        swal({
                            title: "Success!",
                            text: 'Supplier Opening Balance  Updated.',
                            icon: "success",
                            button: true
                        }).then(function() {
                            app.$router.push({name:'supplier_ob'});
                        });
                    }else {
                        $.notify("Error", {
                            autoHideDelay: 3000,
                            gap: 1,
                            className: "error"
                        });
                    }
                }) .catch(function (response)  {
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
        },
        // removeSupplierOB(id) {
        //     let app = this;
        //     swal({
        //         title: "Are you sure?",
        //         text: "Once deleted, you will not be able to recover!",
        //         icon: "warning",
        //         buttons: true,
        //         dangerMode: true
        //     }).then(willDelete => {
        //         if (willDelete) {
        //             axios.get("/supplier_ob/" + id+"/destroy").then(function() {
        //                 app.getSupplierOB();
        //             });
        //             swal("Success! Supplier Opening Balance has been deleted!", {
        //                 icon: "success"
        //             });
        //         }
        //     });
        // },


    }
}
</script>

<style scoped>

</style>
