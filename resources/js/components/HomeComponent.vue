<template>
  <div class="row">

     	<div class="col-md-4 col-sm-6 offset-sm-3 offset-md-4 block mt-4">
            <div class="circle">
                <p><a href='/master/'><i class="fab fa-medium module_logo"></i> <br />Master</a></p>
            </div>
        </div> 

        <div class="col-md-6 col-sm-6 block">
            <div class="circle">
                <p><a href='/van/'><i class="fas fa-shipping-fast module_logo"></i> <br />Van Sale</a></p>
            </div>
        </div>

        <div class="col-md-6 col-sm-6 block">
            <div class="circle">
                <p><a href='/office/'><i class="fas fa-building module_logo"></i> <br />Office Sale</a></p>
            </div>
        </div>

        <div class="col-md-6 col-sm-6 block">
            <div class="circle">
                <p><a href='/inventory/'><i class="fas fa-boxes module_logo"></i> <br />Inventory</a></p>
            </div>
        </div>

        <div class="col-md-6 col-sm-6 block">
            <div class="circle">
                <p><a href='/report/'><i class="far fa-chart-bar module_logo"></i> <br />Report</a>@extends('layouts.master')

</p>
            </div>
        </div>

  </div>
</template>

<script>
    export default {
        mounted() {
            console.log('Component mounted.')
        }
    }
</script>