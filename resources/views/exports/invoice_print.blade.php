<!DOCTYPE html>
<html>
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <style type="text/css">
    @font-face {
    font-family: 'ZawgyiOne2008';
      src: url({{ storage_path('fonts/ZawgyiOne2008.ttf') }}) format("truetype");
   }
 /* @page { margin:20px 60px 10px 25px; } */
 @font-face {
  font-family: "Pyidaungsu";
  src: local("Pyidaungsu"), url(/fonts/Pyidaungsu-2.1_Regular.woff) format("woff"), url(/fonts/Pyidaungsu-2.1_Regular.ttf) format("ttf");
}

@font-face {
  font-family: "Pyidaungsu";
  src: local("Pyidaungsu"), url(/fonts/Pyidaungsu-2.1_Bold.woff) format("woff"), url(/fonts/Pyidaungsu-2.1_Bold.ttf) format("ttf");
  font-weight: bold;
}
  .body {
    font-family: 'Pyidaungsu' !important;
  }
  .title {
    font-size: 35px;
    text-align:center;  
  }
  .mm-title {
    text-align:center;
    font-size: 25px;
  }
  .mm-txt{
    font-family: 'Pyidaungsu' !important;  
    font-size:16px;
  }
  .box {
    float:left;
    width: 100px;
    height: 25px;
    border: 1px #000 solid;
  }

  table#t01 {
    width:100%;
   font-size:16px;
   /* margin-top:20px; */
  } 
  table#t01 tr.tr_heigh td{
    /*height: 20px;*/
    height: 16px;
  }
  td {
    border: 1px solid black;
  }
  th, td {
    padding: 5px;
    text-align: left;
  }
  /*table#t01 tr:nth-child(even) {
    background-color: #eee;
  }
  table#t01 tr:nth-child(odd) {
   background-color: #fff;
  }*/
  table#t01 tr:first-child{
    color: #000;
  }

  header {
  }

  table#t01 tr:first-child td{
    text-align: center;
  }

  table#t02 {
    width:100%;
    font-size:16px;
  }

  table#t02 td{
    border:0;
    margin-top:30px;
  }

  div.absolute {
    position: absolute;
    top: 70px;
    right: 0;
    width: 200px;
  }
  .serial_no {
    width:5%;
  }
  .pt_header{
    border:none;height:30px; 
    background-color: #4472c4 !important;
    color:#fffff !important;
    text-align: center !important;
    font-weight: bold;
   },
  @media print
     {
       thead {display: table-header-group;},
      
      @page {
          margin: 0 0 10px 0px;
      }
     }
  </style>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
</head>
<body>
  <?php
    function getUomName($product,$uom_id) {
      $key = -1;
      foreach ( $product->selling_uoms as $k => $v ) {
        if ( $v->pivot->uom_id == $uom_id ) {
          $key = $k;
          break;
        }
      }

      if($key == -1) {
          return $product->uom->uom_name;
      } else {
          return $product->selling_uoms[$key]->uom_name;   
      }
    }

    function getUomRelation($product,$uom_id) {
      $key = -1;
      foreach ( $product->selling_uoms as $k => $v ) {
        if ( $v->pivot->uom_id == $uom_id ) {
          $key = $k;
          break;
        }
      }
      return $product->selling_uoms[$key]->pivot->relation;
    }

  ?>
  <!--<div>    
    <div class = "mm-title mm-txt" style="display: inline-block; margin-left:270px;margin-right:50px; font-size: 25px; vertical-align: top;">အေရာင္းေျပစာ</div>
    <div style="display: inline-block; padding-top:20px; line-height: 0">
      <div style="line-height: 0"><span>aa</label><input type="text" style="border:solid 1px #000; height:25px;line-height: 0" value"aa" /></div>
      <div style="line-height: 0"><label>aa</label><input type="text" style="border:solid 1px #000; height:25px;line-height: 0" value"aa" /></div>
    </div>
  </div>-->
  <!--<div>-->
    <table id="t01" cellpadding="0" cellspacing="0" style="border:none;width:100%;">
      <thead>
        <tr style="border:none;">
          {{-- <td colspan="9" style="border:none;height:20px;">
              &nbsp;
          </td> --}}
        <td colspan="9" style="border:none;">
            <div style="width:100%; text-align: center;">
              <div style="text-align: center;height: auto;float:left;margin-right: 10px;"><img src="{{url('storage/image/print_logo.jpg')}}" width="100" /></div>
              <div style="vertical-align: middle; text-align: center;float:left">
                  <b><u><font style="font-size: 20px">DHANUKA INTERNATIONAL CO.,LTD</font></u></b>
                  <br />No(60),29th Street, Pabedan Township, Yangon, Myanmar.
                  <br />Office phone :01-250067, 09-954990290, 09-954990291,
                  <br />09-954990292, 09-954990293, 09-954990294
              </div>
              <div style="clear:both"></div>
            </div>
        </td>
        </tr>
        <!--<tr style="border:none;">
          {{-- style="border:none;height:30px; background-color: #4472c4;color:#fffff; text-align: center;font-weight: bold" --}}
          <td colspan="9"  class="pt_header">
              SALES INVOICE
          </td>
        </tr>-->
        <tr>
          <td colspan="9" style="border:none;">
            <div>
              <div style="float:left;" class="mm-txt"><b>ဝယ်သူအမည်</b>&nbsp; &nbsp; &nbsp;&nbsp; {{$sale->customer->cus_name}}</div>
              <div style="float:right" class="mm-txt"><b>ရက်စွဲ</b>&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php
                        $date_arr = explode('-',$sale->invoice_date);
                      ?>
                      {{$date_arr[2].'-'.$date_arr[1].'-'.$date_arr[0]}}
              </div>
            </div>
            <div style="clear: both;">
              <div style="float:left;" class="mm-txt"><b>လိပ်စာ/Ph. No.</b>&nbsp;&nbsp;&nbsp;{{$sale->customer->cus_shipping_address}} / {{$sale->customer->cus_phone}}</div>
              <div style="float:right" class="mm-txt"><b>ဘောက်ချာ နံပါတ်</b>&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;{{$sale->invoice_no}}</div>
            </div>
            <!--<table cellpadding="0" cellspacing="0" style="border:none; width:100%;">
                <tr>
                    <td class="mm-txt" style="border:none;text-align: left;">ဝယ်သူအမည်</td>
                    <td class="mm-txt" style="border:none;">{{$sale->customer->cus_name}}</td>
                    <td style="text-align:right;border:none;" class="mm-txt">ဘောက်ချာ နံပါတ်</td>
                    <td style="text-align:right; border:none;" class="mm-txt">{{$sale->invoice_no}}</td>
                    
                </tr>
                <tr>
                    <td class="mm-txt" style="border:none;text-align: left;">လိပ်စာ</td>
                    <td class="mm-txt" style="border:none;">{{$sale->customer->cus_shipping_address}}</td>
                    <td class="mm-txt" style="text-align:right;border:none;">ရက်စွဲ</td>
                    <td class="mm-txt" style="text-align:right;border:none;">
                      <?php
                        $date_arr = explode('-',$sale->invoice_date);
                      ?>
                      {{$date_arr[2].'-'.$date_arr[1].'-'.$date_arr[0]}}
                    </td>
                </tr>
            </table>-->
            <!--<div style="float:left;">
              <div class="mm-txt" style="float:left;">
                ၀ယ္သူ <br />
                လိပ္စာ 
              </div>
              <div class="mm-txt" style="margin-left:100px;">
                Aung Aung <br />
                Yangon 
              </div>
            </div>
            <div style="text-align: right;">
              <div class="mm-txt">
                ၀ယ္သူ s   Aung Aung<br />
                လိပ္စာ s   Yangon
              </div>
            </div>-->
          </td>
        </tr>
        <tr class="tr_heigh">
          <td class='mm-txt' style="text-align: center;width:10px;font-weight: bold;">နံပါတ်</td>
          <td class='mm-txt' style="text-align: center;width:400px;font-weight: bold;">အမျိုးအမည်</td>
          <td class='mm-txt' style="text-align: center;font-weight: bold;">CTN</td>
          <td class='mm-txt' style="text-align: center;font-weight: bold;">PCS</td>
          <td class='mm-txt' style="text-align: center;font-weight: bold;width:150px;">ဈေးနှုန်း<br />(MMK)</td>
          <td class='mm-txt' style="text-align: center;font-weight: bold;width:100px;">လျှော့ငွေ<br />(MMK)</td>
          <td class='mm-txt' style="text-align: center;font-weight: bold;width:100px;">သင့်ငွေ<br />(MMK)</td>
        </tr>
      </thead>
      <?php
        $count = count($sale->products);
        $extra_count = $count < 10 ? 10-$count : 0;
        $k = 0;
        foreach($sale->products as $product) {
          $k++;
      ?>
        <tr class="tr_heigh">
          <td style="text-align: center;margin:0;padding:0;line-height: 0px;">{{$k}}</td>
          <td class="mm-txt" style="text-align: left;margin:0;padding:0;">{{$product->product_name}}</td>
          <td style="text-align: right;line-height: 1px;margin:0;padding:0;">{{!empty($product->pivot->ctn) ? $product->pivot->ctn : ''}}</td>
          @if($product->pivot->uom_id == $product->uom_id)
          <td style="text-align: right;line-height: 1px;margin:0;padding:0;">
              {{$product->pivot->product_quantity}}
          </td>          
          @else
          <td style="text-align: right;line-height: 1px;margin:0;padding:0;">
              {{$product->pivot->product_quantity}} {{getUomName($product,$product->pivot->uom_id)}} x {{getUomRelation($product,$product->pivot->uom_id)}} {{getUomName($product,$product->uom_id)}}
          </td>
          @endif
          <!--<td class="mm-txt" style="margin:0;padding:0;">
            {{getUomName($product,$product->pivot->uom_id)}}
          </td>-->
          @if($product->pivot->is_foc == 0)
            <td style="text-align: right;margin:0;padding:0;">{{number_format($product->pivot->rate)}}</td>
            <td style="text-align: right;margin:0;padding:0;">{{!empty($product->pivot->discount) ? number_format($product->pivot->discount).'%' : '0'}}</td>
            <td style="text-align: right;margin:0;padding:0;">{{number_format($product->pivot->total_amount)}}</td>
            <!--<td style="text-align: right;">{{!empty($product->pivot->other_discount) ? number_format($product->pivot->other_discount).'%' : '0'}}</td>
            <td style="text-align: right;">{{number_format($product->pivot->total_amount)}}</td>-->
          @else
            <td style="text-align: right;margin:0;padding:0;">FOC</td>
            <td style="text-align: right;margin:0;padding:0;">0</td>
            <td style="text-align: right;margin:0;padding:0;">0</td>
            <!--<td style="text-align: right;">0</td>
            <td style="text-align: right;">0</td>-->
          @endif
          <!--@if($product->pivot->uom_id == $product->uom_id)
            <td></td>
          @else
            <td>1 x {{getUomRelation($product,$product->pivot->uom_id)}}</td>
          @endif-->
        </tr>
      <?php
        }
        for($i=0; $i<$extra_count; $i++) {
          $k++;
      ?>

      <tr class="tr_heigh">
        <td style="text-align: right;"></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <!--<td></td>
        <td></td>-->
        <td></td>
      </tr>      
      <?php
        }
      ?>

      <tr class="tr_heigh">
        <td colspan="4" rowspan="6" style="vertical-align: top;" class="mm-txt">
          Sales Man: {{$sale->sale_man->sale_man}} <br />
          @if($sale->payment_type == 'credit')
          Due Date: <?php
            $date=date_create($sale->due_date);
          ?>{{date_format($date,'dMY')}} <br />
          @endif
          ယခင်လက်ကျန်ငွေ(MMK): {{number_format($previous_balance)}}<br />
          Notes:<br /><br />
          <div>
              <div style="float:left" class="mm-txt">User ID: {{ Auth::user()->name }}</div>
              <div style="float:right" class="mm-txt">Signature: .............</div>
          </div>           
        </td>
        <td colspan="2" class="mm-txt" style="padding:0px;padding-left: 5px">Total Amount(MMK)</td>
        <td style="text-align: right;padding: 0px;padding-right: 5px">{{number_format($sale->total_amount)}}</td>
      </tr>
      <tr>
        <td colspan="2" class="mm-txt" style="padding:0px;padding-left: 5px">Cash Discount(MMK)</td>
        <td style="text-align: right;padding: 0px;padding-right: 5px">{{number_format($sale->cash_discount)}}</td>
      </tr>
      <tr>
        <td colspan="2" class="mm-txt" style="padding:0px;padding-left: 5px">Net Total(MMK)</td>
        <td style="text-align: right;padding: 0px;padding-right: 5px">{{number_format($sale->net_total)}}</td>
      </tr>
      <tr>
        <td colspan="2" class="mm-txt" style="padding:0px;padding-left: 5px">Tax(MMK)</td>
        <!--<td>{{!empty($sale->tax) ? number_format($sale->tax).'%':''}}</td>-->
        <td style="text-align: right;padding: 0px;padding-right: 5px">{{number_format($sale->tax_amount)}}</td>
      </tr>
      <tr>
        <td colspan="2" class="mm-txt" style="padding:0px;padding-left: 5px">Paid Amount(MMK)</td>
        <td style="text-align: right;padding: 0px;padding-right: 5px">{{!empty($sale->paid_amount) ? number_format($sale->paid_amount) : '0'}}</td>
      </tr>
      <tr>
        <td colspan="2" class="mm-txt" style="padding:0px;padding-left: 5px">Balance Amount(MMK)</td>
        <td style="text-align: right;padding: 0px;padding-right: 5px">{{number_format($sale->balance_amount)}}</td>
      </tr>
      <tr>
        <td colspan="9" style="height:70px;vertical-align: top" class="mm-txt">Bank Details:</td>
      </tr>
      <!--<tfoot>
        <tr style="border:none;">
          <td colspan="5" style="border:none;">
              <div style="height:30px;">&nbsp;</div>
          </td>
        </tr>
      </tfoot>-->
    </table>
    <script >
      $(document).ready(function(){
       setTimeout(function(){
        window.onload=window.print();
       }, 300);
     });
       
     </script>
</body>
</html>