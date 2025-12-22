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
        <tr><th colspan="13" style="text-align: center;"><h3>Sale Order Product Wise Report</h3></th></tr>
        <tr>
            <th class="text-center">No.</th>
            <th class="text-center">SO No.</th>
            <th class="text-center">Sale Man</th>
            <th class="text-center">Date</th>
            <th class="text-center">Branch</th>
            <th class="text-center">Customer</th>
            <th class="text-center">Brand</th>
            <th class="text-center">Product Code</th>
            <th class="text-center">Product Name</th>
            <th class="text-center">QTY</th>
            <th class="text-center">Selling UOM</th>
            <th class="text-center">Unit Price</th>
            <th class="text-center">Total Amount</th>
        </tr>
        </thead>
        <tbody>
            <?php
                $total = 0;
                $i = 1;
            ?>
        @foreach($data as $order)
        <tr>
            <td class="text-right">{{$i}}</td>
            <td>{{$order->order_no}}</td>
            <td>{{$order->sale_man}}</td>
            <td>{{$order->order_date}}</td>
            <td>{{$order->branch_name}}</td>
            <td class="mm-txt">{{$order->cus_name}}</td>
            <td>{{$order->brand_name}}</td>
            <td>{{$order->product_code}}</td>
            <td class="mm-txt">{{$order->product_name}}</td>
            <td>{{$order->product_quantity}}</td>
            <td>{{$order->uom_name}}</td>
            <td>{{$order->is_foc == 1 ? "FOC" : $order->price}}</td>
            <td>{{$order->total_amount == null || $order->total_amount == '' ? 0 : $order->total_amount}}</td>
        </tr>
        <?php
            $i++;
            $t_amt = $order->total_amount == null || $order->total_amount == '' ? 0 : $order->total_amount;
            $total = $total + $t_amt;
        ?>
        @endforeach
            <tr>
                <td colspan ="12" style="text-align: right;">Total</td>
                <td  style="text-align: right;">{{ number_format($total) }}</td>
            </tr>
        </tbody>
    </table>
</body>
</html>