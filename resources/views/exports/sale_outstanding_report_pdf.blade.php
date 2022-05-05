<!DOCTYPE html>
<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <style type="text/css">
        /* Kamlesh */
        /* @font-face {
    font-family: 'ZawgyiOne2008';
      src: url({{ storage_path('fonts/ZawgyiOne2008.ttf') }}) format("truetype");
  } */
        .body {
            font-family: 'ZawgyiOne2008' !important;
        }

        .mm-txt {
            font-family: 'ZawgyiOne2008' !important;
            font-size: 13px;
        }

        table#t01 {
            width: 100%;
            border: solid 1px #000;
            border-collapse: collapse;
        }

        th,
        td {
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
                <th class="text-center">Invoice Type</th>
                <th class="text-center">Invoice No</th>
                <th class="text-center">Invoice Date</th>
                <th class="text-center">Due Date</th>
                <th class="text-center">Customer Name</th>
                <th class="text-center">Customer Code</th>
                <th class="text-center">Township</th>
                <th class="text-center">Sale Man</th>
                <th class="text-center">Contact Multiple</th>
                <th class="text-center" style="min-width:150px;">Product Name</th>
                <th class="text-center">Invoice Amount ({{$request->sign}})</th>
                <th class="text-center">Paid Amount ({{$request->sign}})</th>
                <th class="text-center">Currency Gain/Loss ({{$request->sign}})</th>
                <th class="text-center">Balance Amount ({{$request->sign}})</th>
            </tr>
        </thead>
        <tbody>
            {{-- <template v-for="($so,k) in sale_outstandings"> --}}
            @foreach($sale_outstandings as $k=>$so)
            @if($k == 0 || ($so->state_id != $sale_outstandings[$k-1]->state_id))
            <tr>
                <td colspan="14" style="text-align: center;"><b>{{$so->state_name}}</b></td>
            </tr>
            @endif
            @foreach($so->out_list as $c)
            {{-- <template v-for="(c,key) in $so->out_list"> --}}
            <!-- <td class="text-center"></td> -->
            @php $bg = ''; @endphp
            @if($c->type=="paid" && $c->due_date != NULL && $c->due_date < $c->current_date)
                @php
                $bg = "style='background-color:#F3C1C2;'";
                @endphp
                @endif
                @if($c->type=="paid")
                <tr style={{$bg}}>
                    <td class="text-center">{{$c->invoice_type}}</td>
                    <td class="text-center">{{$c->invoice_no}}</td>
                    <td class="text-center">{{$c->invoice_date}}</td>
                    <td class="text-center">{{$c->due_date}}</td>
                    <td class="text-center">{{$c->customer->cus_name}}</td>
                    <td class="text-center">{{$c->customer->cus_code}}</td>
                    <td class="text-center">{{$c->customer->township->township_name}}</td>
                    @if(!empty($c->sale_man))
                    <td class="text-center">{{$c->sale_man->sale_man}}</td>
                    @else
                    <td></td>
                    @endif
                    <td class="text-center">{{$c->customer->cus_phone}}</td>
                    <td>
                        @foreach($c->products as $j=>$prod)
                        {{$prod->product_name}}
                        @if($j!=count($c->products) - 1)
                        ,
                        @endif
                        @endforeach
                    </td>
                    @if($c->is_opening == 1)
                    <td class="text-center">{{$c->total_amount}}</td>
                    @elseif($c->is_opening != 1 && $request->currency_id != 1)
                    <td class="text-center">{{$c->net_total_fx + $c->tax_amount_fx}}</td>
                    @else
                    @php
                    $taxAmt = $c->tax_amount == NULL ? 0 : $c->tax_amount;
                    @endphp
                    <td class="text-center">{{$c->net_total + $taxAmt}}</td>
                    @endif
                    <!--<td class="text-center">{{$c->total_amount}} </td>-->
                    <td class="text-center">{{$c->t_paid_amount}} </td>
                    <td class="text-center">{{$c->t_gain_loss_amount == 0 ? '' : $c->t_gain_loss_amount}} </td>
                    <td class="text-center">{{$c->t_balance_amount}} </td>
                </tr>
                @endif
                @endforeach
                <tr class="">
                    <td colspan="10" class="text-right mm-txt" style="text-align: right;"><b>Total</b></td>
                    <td class="text-center">{{$so->total_inv_amt}}</td>
                    <td class="text-center">{{$so->total_paid_amt}}</td>
                    <td class="text-center">{{$so->total_gain_loss_amt == 0 ? '' : $so->total_gain_loss_amt}}</td>
                    <td class="text-center">{{$so->total_bal_amt}}</td>
                </tr>
                @endforeach
                <tr class="">
                    <td colspan="10" class="text-right mm-txt" style="text-align: right;"><strong>Total Net</strong></td>
                    <td class="text-center">{{$net_inv_amt}}</td>
                    <td class="text-center">{{$net_paid_amt}}</td>
                    <td class="text-center">{{$net_gain_loss_amt == 0 ? '' : $net_gain_loss_amt}}</td>
                    <td class="text-center">{{$net_balance_amt}}</td>
                </tr>
        </tbody>
    </table>
</body>

</html>