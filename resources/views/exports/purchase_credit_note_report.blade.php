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
        <tr><th colspan="5" style="text-align: center;"><h3> Debit Note Report</h3></th>
        </tr>
            <tr>
                <th class="text-center">No.</th>
                <th class="text-center">Purcahse Invoice</th>
                <th class="text-center">Purcahse Invoice Date</th>
                <th class="text-center">Debit Note No</th>
                <th class="text-center">Date</th>
                <th class="text-center">supplier</th>
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
                    <td>{{$cn->purchase->invoice_no}}</td>
                    <td>{{$cn->purchase->invoice_date}}</td>
                    <td>{{$cn->credit_note_no}}</td>
                    <td>{{$cn->credit_note_date}}</td>
                    <td>{{$cn->supplier->name}}</td>
                    <td class="text-right">{{$cn->amount}}</td>
                </tr>

              <?php 
              $i++;
              $total+=$cn->amount;
               ?>

            @endforeach
            <tr>
                <td colspan ="6" style="text-align: right;">Total</td>
                <td  style="text-align: right;">{{ number_format($total) }}</td>
            </tr>
        </tbody>
    </table>
</body>
</html>