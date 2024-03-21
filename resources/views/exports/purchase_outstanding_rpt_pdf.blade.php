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
                <th class="text-center">No.</th>
                <th class="text-center">V.No.</th>
                <th class="text-center">Date</th>
                <th class="text-center">Supplier</th>
                <th class="text-center">Supplier Code</th>
                <th class="text-center">Invoice Amount ({{$request->sign}})</th>
                <th class="text-center">Paid Amount ({{$request->sign}})</th>
                <th class="text-center">Currency Gain/Loss ({{$request->sign}})</th>
                <th class="text-center">Balance Amount ({{$request->sign}})</th>
            </tr>
        </thead>
        <tbody>

            <?php
            $i = 0;
            ?>
            @foreach($purchase_outstandings as $so)
            <?php
            $i++;
            ?>
            @foreach($so->out_list as $c)
            @if($c->type=="paid")
            <tr>
                <td class="text-center">{{$i}}</td>
                <td class="text-center">{{$c->invoice_no}}</td>
                <td class="text-center">{{$c->invoice_date}}</td>
                <td class="text-center">{{$c->supplier->name}}</td>
                <td class="text-center" style="right: 4px ">{{$c->supplier->supplier_code}}</td>
                @if($request->currency_id == 1 || $request->currency_id == '')
                <td class="text-center">{{$c->total_amount - $c->discount}} </td>
                @else
                <td class="text-center">{{$c->total_amount_fx - $c->discount_fx}} </td>
                @endif
                <td class="text-center">{{$c->t_paid_amount}} </td>
                <td class="text-center">{{$c->t_gain_loss_amount == 0 ? '' : $c->t_gain_loss_amount}} </td>
                <td class="text-center">{{$c->t_balance_amount}} </td>
            </tr>
            @endif
            @endforeach
            <tr class="">
                <td colspan="5" class="text-right mm-txt" style="text-align: right;"><b>Total</b></td>
                <td class="text-center">{{$so->total_inv_amt}}</td>
                <td class="text-center">{{$so->total_paid_amt}}</td>
                <td class="text-center">{{$so->total_gain_loss_amt == 0 ? '' : $so->total_gain_loss_amt}}</td>
                <td class="text-center">{{$so->total_bal_amt}}</td>
            </tr>
            @endforeach
            <tr class="">
                <td colspan="5" class="text-right mm-txt" style="text-align: right;"><strong>Total Net</strong></td>
                <td class="text-center">{{$net_inv_amt}}</td>
                <td class="text-center">{{$net_paid_amt}}</td>
                <td class="text-center">{{$net_gain_loss_amt == 0 ? '' : $net_gain_loss_amt}}</td>
                <td class="text-center">{{$net_balance_amt}}</td>
            </tr>
        </tbody>
    </table>
</body>

</html>