<!DOCTYPE html>
<html>
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

  <style type="text/css">
    @font-face {
    font-family: 'ZawgyiOne2008';
      src: url({{ storage_path('fonts/ZawgyiOne2008.ttf') }}) format("truetype");
  }
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
    font-size:12px;
  }
  .box {
    float:left;
    width: 100px;
    height: 25px;
    border: 1px #000 solid;
  }

  table#t01 {
    width:100%;
    margin-top:20px;
  } 
  table#t01 tr.tr_heigh td{
    height: 20px;
  }
  td {
    border: 1px solid black
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
  </style>
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
    <table id="t01" cellpadding="0" cellspacing="0" style="width:100%">
      <thead>
        <!--<tr style="border:none;">
          <td colspan="5" style="border:none;">
              <div style="margin-top:212px;">&nbsp;</div>
          </td>
        </tr>-->
        <tr>
          <td colspan="5">
            <table style="border:none; width:100%">
                <tr>
                    <td class="mm-txt" style="border:none;">ဝယ်သူ</td>
                    <td class="mm-txt" style="border:none;">{{$order->customer->cus_name}}</td>
                    <td class="mm-txt" style="text-align:right;border:none;">ရက်စွဲ</td>
                    <td class="mm-txt" style="text-align:right;border:none;">{{$order->order_date}}</td>
                </tr>
                <tr>
                    <td class="mm-txt" style="border:none;">လိပ်စာ</td>
                    <td class="mm-txt" style="border:none;">{{$order->customer->cus_shipping_address}}</td>
                    <td style="text-align:right;border:none;" class="mm-txt">Order No.</td>
                    <td style="text-align:right; border:none;" class="mm-txt">{{$order->order_no}}</td>
                </tr>
            </table>
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
        <tr>
          <td class='mm-txt' style="text-align: center; width:50px;">စဉ်</td>
          <td class='mm-txt' style="text-align: center;width:300px;">အမျိုးအမည်</td>
          <td class='mm-txt' style="text-align: center; min-width:50px;">အရေအတွက်</td>
          <td class='mm-txt' style="text-align: center; min-width:30px;">ဈေးနှုန်း</td>
          <td class='mm-txt' style="text-align: center;">သင့်ငွေ</td>
        </tr>
      </thead>
      <?php
        $count = count($order->products);
        $extra_count = $count < 15 ? 15-$count : 0;
        $k = 0;
        foreach($order->products as $product) {
          $k++;
      ?>
        <tr>
          <td style="text-align: center;">{{$k}}</td>
          <td class="mm-txt" style="text-align: left;">{{trim($product->product_name)}}</td>

          @if($product->pivot->uom_id == $product->uom_id)
          <td style="text-align: right;">
              {{$product->pivot->product_quantity}} {{getUomName($product,$product->pivot->uom_id)}}
          </td>
          @else
          <td style="text-align: right;">
              {{$product->pivot->product_quantity}} {{getUomName($product,$product->pivot->uom_id)}} x {{getUomRelation($product,$product->pivot->uom_id)}} {{getUomName($product,$product->uom_id)}}
          </td>
          @endif

          @if($product->pivot->is_foc == 0)
            <td style="text-align: right;">{{$product->pivot->rate}}</td>
            <td style="text-align: right;">{{number_format($product->pivot->total_amount)}}</td>
          @else
            <td style="text-align: right">FOC</td>
            <td></td>
          @endif

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
      </tr>      
      <?php
        }
      ?>

      <tr>
        <td colspan="4" class="mm-txt" style="text-align: right;">စုစုပေါင်း</td>
        <td style="text-align: right;">{{number_format($order->total_amount)}}</td>
      </tr>
      <!--<tr>
        <td colspan="2"><br /><br />Check By</td>
        <td></td>
        <td colspan="2" style="text-align: right;"><br /><br />Receive By</td>
      </tr>-->
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