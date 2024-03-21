<!DOCTYPE html>
<html>
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <style type="text/css">
  @font-face {
    font-family: 'ZawgyiOne2008';
      src: url({{ storage_path('fonts/ZawgyiOne2008.ttf') }}) format("truetype");
  }
  .body {
    font-family: 'ZawgyiOne2008' !important;
  }
  .mm-txt{
    font-family: 'ZawgyiOne2008' !important;  
    font-size:13px;
  }
  table#t01 {
    width:100%;
    border:solid 1px #000;
    border-collapse: collapse;
  } 
  th,td {
    border: 1px solid black;
    padding: 5px;
  }

  th {
    text-align: center;
  }
  .text-center {
    text-align: center;
  }
  .text-right {
    text-align: right;
  }

  /*table#t01 tr:nth-child(even) {
    background-color: #eee;
  }
  table#t01 tr:nth-child(odd) {
   background-color: #fff;
  }*/

  </style>
</head>
<body>
    <table id="t01" class="table_no" style="table-layout: fixed">
        <thead>
        <tr>
            <th class="text-center">Date</th>
            <th class="text-center">V.No.</th> 
            <!--<th class="text-center">Reference No.</th> -->
            <th class="text-center">Branch</th>           
            <th class="text-center">Customer Name</th>
            <th class="text-center">Sale Man</th>
            <!--<th class="text-center">Office Sale Man</th>-->
            <th class="text-center">Total Amount</th>
            <th class="text-center">Paid Date</th>
        </tr>
        </thead>
        <tbody>
            <?php
                $total = 0;
                $i = 1;
            ?>
            @foreach($data as $sale)
                <tr>
                    <td style="width:110px;">{{$sale->invoice_date}}</td>
                    <td>{{$sale->invoice_no}}</td>
                    <!--<td>{{$sale->reference_no}}</td>  -->
                    @if($sale->branch != NULL)
                    <td>{{$sale->branch->branch_name}}</td>
                    @else
                    <td></td>
                    @endif                  
                    <td class="mm-txt" style="width:25%;">{{$sale->customer->cus_name}}</td>
                    
                    @if(!empty($sale->office_sale_man_id))
                    <td class="mm-txt">{{$sale->sale_man->sale_man}}</td>
                    @else
                    <td></td>
                    @endif
                    <td class="text-right">{{number_format($sale->net_total)}}</td>
                    <td></td>
                </tr>
                <?php
                    if($sale->net_total != NULL && $sale->net_total != ''){
                        $total = $total + $sale->net_total;
                    }
                    $i = $i+1;
                ?>

            @endforeach
            <tr>
                <td colspan ="5" style="text-align: right;">Total</td>
                <td  style="text-align: right;">{{ number_format($total) }}</td>
                <td></td>
            </tr>
        </tbody>
    </table>
</body>
</html>