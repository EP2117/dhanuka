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
        <tr><th colspan="5" style="text-align: center;"><h3>Purchase Outstanding Report</h3></th>
        </tr>
        <tr>
            {{-- <th class="text-center">No-></th>oo --}}
            <th class="text-center">Invoice No</th>
            <th class="text-center">Date</th>
            <th class="text-center">Supplier Name</th>
            <th class="text-center">Reference No</th>
            <th class="text-center">Invoice Amount	</th>
            <th class="text-center">Paid Amount	</th>
            <th class="text-center">Balance Amount </th>
        </tr>
        </thead>
        <tbody>
            {{-- <template v-for="($so,k) in purchase_over_due"> --}}
            @foreach($purchase_over_due as $so)
              @foreach($so->out_list as $c)
                {{-- <template v-for="(c,key) in $so->out_list"> --}}
                <!-- <td class="text-center"></td> -->
                   @if($c->type=="paid")
                        @if($c->diff_day>$c->credit_day)
                                <tr>
                                    <td class="text-center over_due" style="background-color:black;color:red">{{$c->invoice_no}}</td>
                                    <td class="text-center" style="background-color:black;color:red">{{$c->invoice_date}}</td>
                                    <td class="text-center" style="color:red">{{$c->supplier->name}}</td>
                                    <td class="text-center" style="color:red">{{$c->reference_no}}</td>
                                    <td class="text-center" style="color:red">{{$c->total_amount}} </td>
                                    <td class="text-center" style="color:red">{{$c->t_paid_amount}} </td>
                                    <td class="text-center" style="color:red">{{$c->t_balance_amount}} </td>
                                </tr>
                           @else
                                <tr>
                                    <td class="text-center" style="color:red"><span class="over_due">{{$c->invoice_no}}</span></td>
                                    <td class="text-center">{{$c->invoice_date}}</td>
                                    <td class="text-center">{{$c->supplier->name}}</td>
                                    <td class="text-center" style="right: 4px ">{{$c->reference_no}}</td>
                                    <td class="text-center">{{$c->total_amount}} </td>
                                    <td class="text-center">{{$c->t_paid_amount}} </td>
                                    <td class="text-center">{{$c->t_balance_amount}} </td>
                                </tr>
                                @endif
                    @endif
             @endforeach
                    <tr class="">
                        <td colspan="4" class="text-right mm-txt" style="text-align: right;"><b>Total</b></td>
                        <td class="text-center">{{$so->total_inv_amt}}</td>
                        <td class="text-center">{{$so->total_paid_amt}}</td>
                        <td class="text-center">{{$so->total_bal_amt}}</td>
                    </tr>
            @endforeach
             <tr class="">
                    <td colspan="4" class="text-right mm-txt" style="text-align: right;"><strong>Total Net</strong></td>
                    <td class="text-center">{{$net_inv_amt}}</td>
                    <td class="text-center">{{$net_paid_amt}}</td>
                    <td class="text-center">{{$net_bal_amt}}</td>
                </tr>
        </tbody>
    </table>
</body>
</html>