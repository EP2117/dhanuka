<template>
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="#!"><i class="feather icon-home"></i></a></li>
                <li class="breadcrumb-item"><a :href="site_path+'/'">Home</a></li>
                <li class="breadcrumb-item"><a :href="site_path+'/office'">Office</a></li>
                <li class="breadcrumb-item"><router-link tag="span" to="/sale_advance" class="font-weight-normal"><a href="#">Sale Advance</a></router-link></li>
                <li class="breadcrumb-item active" aria-current="page">Sale Advance Form</li>
            </ol>
        </nav>
        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h4 class="mb-0 text-gray-800" v-if="!isEdit">Add Sale Advance</h4>
            <h4 class="mb-0 text-gray-800" v-else>Edit Sale Advance</h4>

            <a onClick="history.go(-1);" class="btn btn-primary btn text-white text-right" value="Back"><i class="fas fa-angle-double-left"></i> Back</a>
        </div>

        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Sale Advance Details</h6>
            </div>
            <div class="card-body">
                <div class="d-block">
                    <!-- form start -->
                    <form class="form" method="post" @submit.prevent="onSubmit" @keydown="form.errors.clear($event.target.name)">

                        <div class="form-group row">
                            <label class="col-lg-3 text-right col-form-label form-control-label">Sale Advance No</label>
                            <div class="col-lg-6 col-md-offset-2">
                                <input class="form-control" type="text"
                                       id="advance_no" name="advance_no"
                                       v-model="form.advance_no" readonly >
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
                            <label class="col-lg-3 text-right col-form-label form-control-label">Customer</label>
                            <div class="col-lg-6">
                                <select id="customer_id" class="form-control form-control-alternative"
                                        name="customer" v-model="form.customer_id" style="width:100%" required>
                                    <option value="">Select One</option>
                                    <option v-for="c in customers" :value="c.id"  >{{c.cus_name}}</option>
                                </select>
                            </div>
                        </div>

                        <div class="row form-group ">
                            <label class="col-lg-3 text-right col-form-label form-control-label">Currency</label>
                            <div class="col-md-4">
                                <select class="form-control"
                                        name="currency_id" id="currency_id" style="min-width:100px;" v-model="form.currency_id"
                                >
                                    <option v-for="c in currency" :value="c.id" :data-sign="c.sign">{{c.name}}</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <div id="currency_div" v-if="!isMMK"> <label class="sign">{{sign}}</label> 1 = ( <input type="text" style="width:100px;display:inline-block;" class="form-control decimal_no" id="currency_rate" name="currency_rate" v-model="form.currency_rate" :readonly="rate_readonly"> ) MMK</span></div>
                            </div>
                        </div>

                        <div class="form-group row"  v-if="!isMMK">
                            <label class="col-lg-3 col-form-label text-right form-control-label">Amount ({{sign}})</label>
                            <div class="col-lg-6">
                                <input class="form-control" type="number"
                                       id="amount_fx" name="amount_fx"
                                       v-model="form.amount_fx" autocomplete="off">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-lg-3 col-form-label text-right form-control-label">Amount (MMK)</label>
                            <div class="col-lg-6">
                                <input class="form-control" type="number"
                                       id="amount" name="amount" :readonly="!isMMK"
                                       v-model="form.amount" autocomplete="off">
                            </div>
                        </div>

                       <div class="form-group row">
                            <label class="col-lg-3 col-form-label text-right form-control-label">Payment Method</label>
                            <div class="col-md-4">
                                <select class="form-control" required
                                        v-model="form.account_group" style="width:100%" @change="changeAccountGroup()">
                                    <option value="">Select One</option>
                                    <option v-for="at in account_group" :value="at.id"  >{{at.name}}</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <select class="form-control" required
                                        v-model="form.cash_bank_account" style="width:100%">
                                    <option value="">Select One</option>
                                    <option v-for="at in cash_bank_accounts" :value="at.id"  >{{at.sub_account_name}}</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group row text-center" v-if="">
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
                date:'',
                advance_no:'',
                customer_id:'',
                amount:'',
                currency_id: 1,
                currency_rate: '',
                amount_fx: '',
                account_group: "",
                cash_bank_account: '',
            }),
            isEdit:false,
            customers:[],
            advance_id:'',
            site_path:'',
            check_collection:0,
            user_role:'',
            storage_path:'',
            user_year:'',
            isMMK: true,
            currency: [],
            account_group: [],
            cash_bank_accounts: [],
            sign: '',
            rate_readonly: false,
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
            this.advance_id = this.$route.params.id;
            this.getAdvance(this.advance_id);

        } else {
        }
    },
    mounted() {
        let app=this;
        app.initCustomers();

        app.initCurrency();

        app.initAccountGroup();

        $("#currency_id").on("select2:select", function(e) {            
            var data = e.params.data;
            app.form.currency_id = data.id;
            var sign = e.target.options[e.target.options.selectedIndex].dataset.sign;
            if(data.id != 1) {
                app.isMMK = false;
                app.form.amount_fx = '';
            } else{
                app.isMMK = true;
            }

            app.sign = sign;
            
        });

        $("#customer_id").select2();
        $("#customer_id").on("select2:select", function(e) {
            var data = e.params.data;
            app.form.customer_id = data.id;
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
               // minDate: app.user_year+"-01-01",
               // maxDate: app.user_year+"-12-31",
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
            app.form.date = formatedValue;
        });

        $(document).on('blur','#currency_rate, #amount_fx',function(e) {
            var currency_rate = app.form.currency_rate == '' ? 0 : app.form.currency_rate;
            var amount_fx = app.form.amount_fx == '' ? 0 : app.form.amount_fx;
            $('#amount').val(Math.round(parseFloat(parseFloat(amount_fx) * parseFloat(currency_rate))));
            app.form.amount = $('#amount').val();
        });

        $(document).on('keyup','#amount_fx',function(e) {
            var currency_rate = app.form.currency_rate == '' ? 0 : app.form.currency_rate;
            var amount_fx = app.form.amount_fx == '' ? 0 : app.form.amount_fx;
            $('#amount').val(Math.round(parseFloat(parseFloat(amount_fx) * parseFloat(currency_rate))));
            app.form.amount = $('#amount').val();
        });

    },
    methods:{

        initCurrency() {
            axios.get("/all_currency").then(({ data }) => (this.currency = data.data));
            $("#currency_id").select2();

        },

        initAccountGroup(){
            axios.get('/sub_account/get_account_group').then(({data})=>(this.account_group=data.account_group));
            // $("#financial_type2_id").select2();
        },

        changeAccountGroup(id) {
            var ag_id = this.form.account_group;
            axios.get('/sub_account/get_account_group/'+ag_id).then(({data})=>(this.cash_bank_accounts=data.sub_accounts));
        },

        initCustomers() {
            axios.get("/customers").then(({ data }) => (this.customers = data.data));
            $("#customer_id").select2();
        },
        getAdvance(id){
            let app=this;
            axios.get("/sale_advance/" + id).then(function (response){
                console.log(response.data);
               
                app.form.advance_no=response.data.data.advance_no;

                app.form.currency_id = response.data.data.currency_id;
                $("#currency_id").val(app.form.currency_id).trigger('change'); 
                app.sign = response.data.data.currency.sign;               
                if(response.data.data.currency_id != 1) {
                    app.isMMK = false;
                    app.form.amount_fx = response.data.data.amount_fx;
                    $("#currency_id").attr('disabled',true);                    
                    if(response.data.data.used_count > 0) {
                        app.rate_readonly = true;
                    }
                    app.form.currency_rate = response.data.data.currency_rate;
                } else{
                    app.isMMK = true;
                }

                app.form.amount=response.data.data.amount;
               // app.check_collection=r.collection_amount;
                app.form.customer_id=response.data.data.customer_id;

                app.form.account_group = response.data.data.account_group_id;        
                if(response.data.data.account_group_id != '' && response.data.data.account_group_id != null) {
                    axios.get('/sub_account/get_account_group/'+response.data.data.account_group_id).then(({data})=>(app.cash_bank_accounts=data.sub_accounts));
                }
                app.form.cash_bank_account = response.data.data.sub_account_id;

                app.form.date = moment(response.data.data.advance_date).format('YYYY-MM-DD');
                $('#customer_id').val(app.form.customer_id).trigger('change');
            });
        },
        onSubmit:function (event){
            var app = this;
            app.form.amount = $("#amount").val();
            //alert(app.form.amount);
            $("#loading").show();
            if (!this.isEdit) {
                this.form.post('/sale_advance').then(function (data){
                    if(data.status=='success'){
                        console.log(data);
                        event.target.reset();
                        $("#customer_id").select2();
                        $("#loading").hide();
                        swal({
                            title: "Success!",
                            text: 'Sale advance is added.',
                            icon: "success",
                            button: true
                        }).then(function() {
                            app.$router.push({name:'sale_advance'});

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
                this.form.patch('/sale_advance/'+app.advance_id).then(function (data){
                    if(data.status=='success'){
                        //event.target.reset();
                        $("#customer_id").select2();
                        $("#loading").hide();
                        swal({
                            title: "Success!",
                            text: 'Sale advance is updated.',
                            icon: "success",
                            button: true
                        }).then(function() {
                            app.$router.push({name:'sale_advance'});
                        });
                    }else {
                        swal("Invalid! Sale Advance  has been used!", {
                                icon: "warning"
                            }).then(function() {
                            location.reload();
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
