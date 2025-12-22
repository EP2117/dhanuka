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
        <tr><th colspan="5" style="text-align: center;"><h3>Debit Note Product Wise Report</h3></th>
        </tr>
            <tr>
                <th class="text-center">No.</th>
                <th class="text-center">Purchase Invoice</th>
                <th class="text-center">Purchase Invoice Date</th>
                <th class="text-center">Debit Note No</th>
                <th class="text-center">Date</th>
                <th class="text-center">Product Code</th>
                <th class="text-center">Product Name</th>
                <th class="text-center">Supplier</th>
                <th class="text-center">Qty</th>
                <th class="text-center">Rate</th>
                <th class="text-center">Amount</th>
                <!-- <th class="text-center"> Total </th>  -->
            </tr>
        </thead>
        <tbody>
            <?php
                $total = 0;
                $i = 1;
            ?>
            @foreach($credit_note as $cn)
                <tr>
                    <td class="text-right">{{$i}}</td>
                    <td>{{$cn->invoice_no}}</td>
                    <td>{{$cn->invoice_date}}</td>
                    <td>{{$cn->credit_note_no}}</td>
                    <td>{{$cn->credit_note_date}}</td>
                    <td >{{$cn->product_code}}</td>
                    <td >{{$cn->product_name}}</td>
                    <td>{{$cn->name}}</td>
                    <td>{{$cn->qty}}</td>
                    <td class="mm-txt">{{$cn->rate}}</td>
                    <td class="text-right">{{$cn->total_amount}}</td>
                </tr>

              <?php 
              $i++;
              $total+=$cn->total_amount;
               ?>

            @endforeach
            <tr>
                <td colspan ="10" style="text-align: right;">Total</td>
                <td  style="text-align: right;">{{ number_format($total) }}</td>
            </tr>
        </tbody>
    </table>
</body>
</html>