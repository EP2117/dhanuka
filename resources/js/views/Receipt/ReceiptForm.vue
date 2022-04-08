<template>
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="#!"><i class="feather icon-home"></i></a></li>
                <li class="breadcrumb-item"><a :href="site_path+'/'">Home</a></li>
                <li class="breadcrumb-item"><a :href="site_path+'/master'">Master</a></li>
                <li class="breadcrumb-item"><router-link tag="span" to="/receipt" class="font-weight-normal"><a href="#">Category</a></router-link></li>
                <li class="breadcrumb-item active" aria-current="page">Receipt Form</li>

            </ol>
        </nav>
        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h4 class="mb-0 text-gray-800" v-if="!isEdit">Create New Receipt</h4>
            <h4 class="mb-0 text-gray-800" v-else>Edit Receipt</h4>
            <a onClick="history.go(-1);" class="btn btn-primary btn text-white text-right" value="Back"><i class="fas fa-angle-double-left"></i> Back</a>
        </div>
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Receipt Details</h6>
            </div>
            <div class="card-body">
                <div class="d-block">
                    <!-- form start -->
                    <form class="form" method="post" @submit.prevent="onSubmit" @keydown="form.errors.clear($event.target.name)">

                        <div class="form-group  row">
                            <label class="col-lg-3 text-right col-form-label form-control-label">Cash Receipt No:</label>
                            <div class="col-lg-6 col-md-offset-2">
                                <input class="form-control" type="text"
                                      v-model="form.cash_receipt_no" readonly required >
                            </div>
                        </div>
                        <div class="form-group  row">
                            <label class="col-lg-3 text-right col-form-label form-control-label">Date</label>
                            <div class="col-lg-6 col-md-offset-2">
                                <input class="form-control datetimepicker" type="text"
                                       id="date" autocomplete="off" v-model="form.date" required >
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-lg-3 text-right col-form-label form-control-label">Debit</label>
                            <div class="col-lg-6">
                                <select id="debit_id" class="form-control form-control-alternative"
                                        name="debit" v-model="form.debit" style="width:100%" required>
                                    <option value="">Select One</option>
                                    <option v-for="d in debit" :value="d.id"  >{{d.sub_account_name}}</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-lg-3 text-right col-form-label form-control-label">Credit</label>
                            <div class="col-lg-6">
                                <select  class="form-control form-control-alternative" id="credit_id" required v-model="form.credit" style="width:100%">
                                    <option value="">Select One</option>
                                    <option v-for="c in credit" :value="c.id"  >{{c.sub_account_name}}</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group  row">
                            <label class="col-lg-3 text-right col-form-label form-control-label">Amount</label>
                            <div class="col-lg-6 col-md-offset-2">
                                <input class="form-control" type="text"

                                       v-model="form.amount" required >
                            </div>
                        </div>
                        <div class="form-group  row">
                            <label class="col-lg-3 text-right col-form-label form-control-label">Remark</label>
                            <div class="col-lg-6 col-md-offset-2">
                                <textarea class="form-control" type="text"
                                       id="remark"
                                          v-model="form.remark"  ></textarea>
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
<!--        <div id="loading" class="text-center"><img :src="storage_path+'/image/loader_2.gif'" /></div>-->
    </div>
</template>
<script>
export default {
    data(){
        return {
            form:new Form({
                cash_receipt_no:'',
                debit:'',
                date:"",
                credit:'',
                amount:'',
                remark:'',

            }),
            isEdit:false,
            sub_account:[],
            debit:[],
            credit:[],
            site_path:'',
            user_role:'',
            storage_path:'',
            receipt_id:'',
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
            this.receipt_id = this.$route.params.id;
            this.getReceipt(this.receipt_id);
        } else {
            // this.initCategories();
        }
        // this.form.date = moment().format("YYYY-MM-DD");

    },
    mounted() {

        let app=this;
        $("#debit_id").select2();
        $("#debit_id").on("select2:select", function(e) {
            var data = e.params.data;
            app.form.debit = data.id;
        });
        $("#credit_id").select2();
        $("#credit_id").on("select2:select", function(e) {
            var data = e.params.data;
            app.form.credit = data.id;
        });
        this.initDebit();
        this.initCredit();
        $(".datetimepicker")
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
                if(app.user_year < y) {
                    if(app.form.date == app.user_year+"-12-31" ||  app.form.date == '') {
                        app.form.date = app.user_year+"-12-31";
                    }
                }
            }).on("dp.change", function(e) {
                var formatedValue = e.date.format("YYYY-MM-DD");
                app.form.date = formatedValue;
            });
    },
    methods:{
        initDebit(){
            axios.get('/sub_account/get_sub_account/'+"debit").then(({data})=>(this.debit=data.sub_account));
            $("#debit").select2();
        },
        initCredit(){
            axios.get('/sub_account/get_sub_account/'+"credit").then(({data})=>(this.credit=data.sub_account));
            $("#credit_id").select2();
        },
        getReceipt(id){
            let app=this;
            axios.get('/receipt/edit/'+id).then(function (response){
                var r=response.data.receipt;
                app.form.cash_receipt_no=r.cash_receipt_no;
                app.form.amount=r.amount;
                app.form.remark=r.remark;
                app.form.debit=r.debit_id;
                app.form.date = moment(r.date).format('YYYY-MM-DD');
                $('#debit_id').val(app.form.debit).trigger('change');
                app.form.credit=r.credit_id;
                $('#credit_id').val(app.form.credit).trigger('change');
            });
        },
        onSubmit:function (event){
            var app = this;
            $("#loading").show();
            if (!this.isEdit) {
                this.form.post('/receipt/store').then(function (data){
                    if(data.status=='success'){
                        console.log(data);
                        event.target.reset();
                        $("#debit_id").select2();
                        $("#credit_id").select2();
                        $("#loading").hide();
                        swal({
                            title: "Success!",
                            text: 'Receipt Added.',
                            icon: "success",
                            button: true
                        }).then(function() {
                            app.$router.push({name:'receipt'});

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
                this.form.patch('/receipt/update/'+app.receipt_id).then(function (data){
                    if(data.status=='success'){
                        event.target.reset();
                        $("#debit_id").select2();
                        $("#credit_id").select2();
                        $("#loading").hide();
                        swal({
                            title: "Success!",
                            text: 'Receipt  Updated.',
                            icon: "success",
                            button: true
                        }).then(function() {
                            app.$router.push({name:'receipt'});
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


    }
}
</script>

<style scoped>

</style>
