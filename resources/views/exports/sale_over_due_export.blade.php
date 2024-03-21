<?php
    function getDueDay($dd) {
      $now = time(); // or your date as well
      $dd = strtotime($dd);
      $datediff =$dd - $now;

      return round($datediff / (60 * 60 * 24));
    }
?>

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
  .over_due{
      color:red;
  },
  th {
    text-align: center;
  }
table#t01 tr:nth-child(even) {
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
        <tr><th colspan="5" style="text-align: center;"><h3>Sale Over Due Report</h3></th>
        </tr>
        <tr>
            {{-- <th class="text-center">No-></th>oo --}}
            <th class="text-center">Invoice No</th>
            <th class="text-center">Invoice Date</th>
            <th class="text-center">Due Date</th>
            <th class="text-center">Due Day</th>
            <th class="text-center">Customer Name</th>
            <th class="text-center">Reference No</th>
            <th class="text-center">Invoice Amount  ({{$request->sign}})</th>
            <th class="text-center">Paid Amount ({{$request->sign}})</th>
            <th class="text-center">Balance Amount ({{$request->sign}})</th>
        </tr>
        </thead>
        <tbody>
            {{-- <template v-for="($so,k) in sale_over_due"> --}}
            @foreach($sale_over_due as $so)
              @foreach($so->out_list as $c)
                {{-- <template v-for="(c,key) in $so->out_list"> --}}
                <!-- <td class="text-center"></td> -->
                   @if($c->type=="paid")
                        <!-- originalif($c->diff_day>$c->credit_day) -->
                        <!--ep -->
                            @if(getDueDay($c->due_date) > 0)

                                <tr>
                                    <td class="text-center over_due" >{{$c->invoice_no}}</td>
                                    <td class="text-center">{{$c->invoice_date}}</td>
                                    <td class="text-center">{{empty($c->due_date) ? '' : $c->due_date}}</td>
                                    <td class="text-center" >{{getDueDay($c->due_date)}}</td>
                                    <td class="text-center">{{$c->customer->cus_name}}</td>
                                    <td class="text-center">{{$c->reference_no}}</td>
                                    <td class="text-center">{{$c->total_amount}} </td>
                                    <td class="text-center">{{$c->t_paid_amount}} </td>
                                    <td class="text-center">{{$c->t_balance_amount}} </td>
                                </tr>
                           @else
                                <tr style="color: red">
                                    <td class="text-center" style="color:red"><font color="red">{{$c->invoice_no}}</font></td>
                                    <td class="text-center"  style="color:red">{{$c->invoice_date}}</td>
                                    <td class="text-center" style="color:red">{{empty($c->due_date) ? '' : $c->due_date}}</td>
                                    <td class="text-center" style="color:red">{{getDueDay($c->due_date)}}</td>
                                    <td class="text-center"  style="color:red">{{$c->customer->cus_name}}</td>
                                    <td class="text-center" style="right: 4px; color:red">{{$c->reference_no}}</td>
                                    <td class="text-center"  style="color:red">{{$c->total_amount}} </td>
                                    <td class="text-center"  style="color:red">{{$c->t_paid_amount}} </td>
                                    <td class="text-center"  style="color:red">{{$c->t_balance_amount}} </td>
                                </tr>
                                @endif
                    @endif
             @endforeach
                    <tr class="">
                        <td colspan="6" class="text-right mm-txt" style="text-align: right;"><b>Total</b></td>
                        <td class="text-center">{{$so->total_inv_amt}}</td>
                        <td class="text-center">{{$so->total_paid_amt}}</td>
                        <td class="text-center">{{$so->total_bal_amt}}</td>
                    </tr>
            @endforeach
             <tr class="">
                    <td colspan="6" class="text-right mm-txt" style="text-align: right;"><strong>Total Net</strong></td>
                    <td class="text-center">{{$net_inv_amt}}</td>
                    <td class="text-center">{{$net_paid_amt}}</td>
                    <td class="text-center">{{$net_bal_amt}}</td>
                </tr>
        </tbody>
    </table>
</body>
</html>