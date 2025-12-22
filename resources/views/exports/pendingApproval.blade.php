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
        <tr><th colspan="7" style="text-align: center;"><h3>Pending Approval Report</h3></th></tr>
        <tr>
            <th class="text-center">No.</th>
            <th class="text-center">Approval No.</th>
            <th class="text-center">Approval Date</th>
            <th class="text-center">Branch</th>
            <th class="text-center">Order No.</th>
            <th class="text-center">Sale Man</th>                                
            <th class="text-center">Customer</th>
            <th class="text-center">Total Amount</th>
        </tr>
        </thead>
        <tbody>
            <?php
                $i = 1;
            ?>
        @foreach($data as $approval)
        @if($approval->order != NULL)
        <tr>
            <td class="text-right">{{$i}}</td>
            <td>{{$approval->approval_no}}</td>
             <?php
                $date=date_create($approval->created_at);
                $app_date = date_format($date,"Y-m-d");
            ?> 
            <td>{{$app_date}}</td>
             @if($approval->order != NULL && $approval->order->branch != NULL)
            <td>{{$approval->order->branch->branch_name}}</td>
            @endif
            <td>{{$approval->order->order_no}}</td>
            @if($approval->order != NULL && $approval->order->sale_man != NULL)
            <td>{{$approval->order->sale_man->name}}</td>
            @endif
            <td>{{$approval->order->customer->cus_name}}</td>
            <td>{{$approval->total_amount}}</td>
        </tr>
        @endif
        <?php
            $i++;
        ?>
        @endforeach
        </tbody>
    </table>
</body>
</html>