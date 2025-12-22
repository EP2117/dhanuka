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
    <?php
        function getSellingUom($product,$uom_id) {
           // $key = array_search(array('ADADCD' => 'HOSP1'), $product->selling_uoms);
            $key = -1;
            foreach ( $product->selling_uoms as $k => $v ) {
              if ( $v->pivot->uom_id == $uom_id ) {
                $key = $k;
                break;
              }
            }

            if($key == -1) {
                return $product->uom->uom_name;
            } else {
                return $product->selling_uoms[$key]->uom_name;   
            }
        }
    ?>
    <table id="t01" class="table_no" width="100%" style="table-layout: fixed">
        <thead>
        <tr><th colspan="13" style="text-align: center;"><h3>Daily Sale Report</h3></th></tr>
        <tr>
            <th class="text-center">No.</th>
            <th class="text-center">Invoice No.</th>
            <th class="text-center">Reference No.</th>
            <th class="text-center">Invoice Date</th>
            <th class="text-center">Branch</th>
            <th class="text-center">Customer</th>
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
        @foreach($data as $sale)

            @if($request->product_name != '')

                @foreach($sale->products as $product) 
                    @if(strpos($product->product_name, $request->product_name) !== false)
                    <tr>
                        <td class="text-right">{{$i}}</td>
                        <td>{{$sale->invoice_no}}</td>
                        <td>{{$sale->reference_no}}</td>
                        <td>{{$sale->invoice_date}}</td>
                        @if($sale->branch != NULL)
                        <td>{{$sale->branch->branch_name}}</td>
                        @else
                        <td></td>
                        @endif
                        <td class="mm-txt">{{$sale->customer->cus_name}}</td>
                        <td>{{$product->product_code}}</td>
                        <td>{{$product->product_name}}</td>
                        <td>{{$product->pivot->product_quantity}}</td>
                        <td>{{getSellingUom($product,$product->pivot->uom_id)}}</td>
                        @if($product->pivot->is_foc == 0)                        
                            <td class="text-right">{{$product->pivot->price}}</td>
                        @else
                            <td v-else>FOC</td>
                        @endif
                        <td class="text-right">{{$product->pivot->total_amount}}</td>
                    </tr>

                    <?php
                        if($product->pivot->is_foc == 0){
                            $total = $total + $product->pivot->total_amount;
                        }
                        $i = $i+1;
                    ?>

                    @endif

                @endforeach

            @else

                @foreach($sale->products as $product) 

                    <tr>
                        <td class="text-right">{{$i}}</td>
                        <td>{{$sale->invoice_no}}</td>
                        <td>{{$sale->reference_no}}</td>
                        <td>{{$sale->invoice_date}}</td>
                        @if($sale->branch != NULL)
                        <td>{{$sale->branch->branch_name}}</td>
                        @else
                        <td></td>
                        @endif
                        <td class="mm-txt">{{$sale->customer->cus_name}}</td>
                        <td>{{$product->product_code}}</td>
                        <td>{{$product->product_name}}</td>
                        <td>{{$product->pivot->product_quantity}}</td>
                        <td>{{getSellingUom($product,$product->pivot->uom_id)}}</td>
                        @if($product->pivot->is_foc == 0)
                            <td class="text-right">{{$product->pivot->price}}</td>
                        @else
                            <td v-else>FOC</td>
                        @endif
                        <td class="text-right">{{$product->pivot->total_amount}}</td>
                    </tr>

                    <?php
                        if($product->pivot->is_foc == 0){
                            $total = $total + $product->pivot->total_amount;
                        }
                        $i = $i+1;
                    ?>

                @endforeach

            @endif
        @endforeach
            <tr>
                <td colspan ="10" style="text-align: right;">Total</td>
                <td  style="text-align: right;">{{ number_format($total) }}</td>
            </tr>
        </tbody>
    </table>
</body>
</html>