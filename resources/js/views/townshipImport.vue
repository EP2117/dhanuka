<template>
	<div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="#!"><i class="feather icon-home"></i></a></li>
                <li class="breadcrumb-item"><a :href="site_path+'/'">Home</a></li>
                <li class="breadcrumb-item"><a :href="site_path+'/master'">Master</a></li>
                <li class="breadcrumb-item active" aria-current="page">Import</li>
                <li class="breadcrumb-item active" aria-current="page">Township Import</li>

            </ol>
        </nav>
        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h4 class="mb-0 text-gray-800">Township Import</h4>
        </div>

        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Import Excel File</h6>
            </div>
            <div class="card-body">

            	<form id='import_form' name='import_form' method="POST" enctype="multipart/form-data" @submit.prevent="onSubmit">
      				<input type="hidden" name="_token" :value="csrf">
                <div class="row">
                	
  				  	<div class='col-md-4'>
                    	<input name='file' type='file' ref="myFiles" class="form-control" required>
                  	</div>
                  	<div class='col-md-4'>
                    	<button class="btn btn-primary">Import Data</button>
                  	</div>
                </div>
                </form>

            </div>
        </div>

        <div id="loading" class="text-center"><img :src="storage_path+'/image/loader_2.gif'" /></div>
    </div>
</template>
<script>
	export default {
	  	data() {
	    	return {
	      		csrf: "",
                site_path: '',
                storage_path: '',
	    	};
	 	 },
	 	mounted() {
	 		$("#loading").hide();
	 	},
        created() {
            this.site_path = document.querySelector("meta[name='site-path']").getAttribute('content');
            //this.site_path = this.site_path.substring(this.site_path.lastIndexOf('/')+1);
            this.storage_path = document.querySelector("meta[name='storage-path']").getAttribute('content');
        },
		methods: {

		    onSubmit() {
		      var data = new FormData();
		      var file = this.$refs.myFiles.files[0];

		      $("#loading").show();
		      data.append('file', file);
		      axios.post('import/township', data)
		        .then(response => {        
		        $("#loading").hide();

		        swal({
                    title: "Success!",
                    text: 'Imported Successfully!.',
                    icon: "success",
                    button: true
                });

		       });
		  	},
	  	}
	}
</script>