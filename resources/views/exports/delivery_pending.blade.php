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
        <tr><th colspan="5" style="text-align: center;"><h3>Delivery Pending</h3></th>
        </tr>
        <tr>
            <th class="text-center">No.</th>
            <th class="text-center">Sale Order Invoice </th>
            <th class="text-center">Sale Order Date</th>
            <th class="text-center">Customer</th>
            <th class="text-center">Product Code</th>
            <th class="text-center">Product Name</th>
            <th class="text-center">Invoice Qty</th>
            <th class="text-center">Del Qty</th>
            <th class="text-center">Balance</th>
            <!-- <th class="text-center"> Total </th>  -->
        </tr>
        </thead>
        <tbody>
            <?php
                $total = 0;
                $i = 1;
            ?>
            @foreach($delivery_pending as $cn)
                <tr>
                    <td class="textalign">{{$i}}</td>
                    <td class="textalign">{{$cn->invoice_no}}</td>
                    <td class="textalign">{{$cn->invoice_date}}</td>
                    <td class="textalign">{{$cn->cus_name}}</td>
                    <td class="textalign">{{$cn->product_code}}</td>
                    <td class="textalign">{{$cn->product_name}}</td>
                    <td class="textalign">{{$cn->si_qty}}</td>
                    <td class="textalign">{{(int)($cn->del_qty)}}</td>
                    <td class="textalign">{{$cn->si_qty-(int)($cn->del_qty)}}</td>
                </tr>

              <?php 
              $i++;
               ?>

            @endforeach
           
        </tbody>
    </table>
</body>
</html>