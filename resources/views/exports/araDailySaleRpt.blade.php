<!DOCTYPE html>
<html>
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <style type="text/css">
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

  /*table#t01 tr:nth-child(even) {
    background-color: #eee;
  }
  table#t01 tr:nth-child(odd) {
   background-color: #fff;
  }*/

  </style>
</head>
<body>
    <table id="t01" class="table_no" width="100%" style="table-layout: fixed">
        <thead>
        <tr><th colspan="5" style="text-align: center;"><h3>ERP Daily Sale Report</h3></th></tr>
        <tr>
            <th class="text-center">No.</th>
            <th class="text-center">Invoice No.</th>
            <th class="text-center">Date</th>
            <th class="text-center">Customer</th>
            <th class="text-center">Sale Man</th>
            <th class="text-center">Total</th>
        </tr>
        </thead>
        <tbody>
            <?php
                $total = 0;
                $i = 1;
            ?>
            @foreach($data as $sale)
                <tr>
                    <td class="text-right">{{$i}}</td>
                    <td>{{$sale->ara_invoice_no}}</td>
                    <td>{{$sale->ara_invoice_date}}</td>
                    <td class="mm-txt">{{$sale->customer->cus_name}}</td>
                    <td>{{$sale->sale_man}}</td>
                    <td class="text-right">{{$sale->total_amount}}</td>
                </tr>

                <?php
                    if($sale->total_amount != NULL && $sale->total_amount != ''){
                        $total = $total + $sale->total_amount;
                    }
                    $i = $i+1;
                ?>

            @endforeach
            <tr>
                <td colspan ="5" style="text-align: right;">Total</td>
                <td  style="text-align: right;">{{ number_format($total) }}</td>
            </tr>
        </tbody>
    </table>
</body>
</html>