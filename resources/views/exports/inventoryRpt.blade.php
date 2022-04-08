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
        function getOpening($op_data,$product_id) {
            $in_count = 0;
            $out_count = 0;
            $count = 0;
            foreach($op_data as $op_product) {
                if($op_product->product_id == $product_id) {
                    $in_count  = $in_count + $op_product->in_qty;
                    $out_count = $out_count + $op_product->out_qty;
                }
           }

           $count = $in_count - $out_count;
           return $count;
        }

        function getSO($order_data,$product_id) {

                $qty = 0;

                $key = -1;
                foreach ( $order_data as $k => $v ) {
                  if ( $v->product_id == $product_id ) {
                    $key = $k;
                    break;
                  }
                }

                if($key != -1) {
                    $qty = $order_data[$key]->order_qty;
                }

                return $qty;
            }
    ?>
    <table id="t01" class="table_no" width="100%" style="table-layout: fixed">
        <thead>
        <tr><th colspan="13" style="text-align: center;"><h3>Inventory Report</h3></th></tr>
        <tr>
            <th class="text-center">No.</th>
            <th class="text-center">Brand</th>
            <th class="text-center">Internal Category</th>
            <th class="text-center">Product Code</th>
            <th class="text-center">Product Name</th>
            <th class="text-center">Warehouse UOM</th>
            <th class="text-center">Opening</th>
            <th class="text-center">In</th>
            <th class="text-center">Stock <br />Receive</th>
            <th class="text-center">Stock <br />Transfer</th>            
            <th class="text-center">Sale Order</th>
            <th class="text-center">Approval <br />QTY</th>
            <th class="text-center">Revise <br />QTY</th>
            <th class="text-center">Revise Sale</th>
            <th class="text-center">No Revise Sale</th>
            <th class="text-center">Direct Sale</th>
            <th class="text-center">Reserve <br />Balance</th>
            <th class="text-center">Balance</th>
        </tr>
        </thead>
        <tbody>
            <?php
                $total = 0;
                $i = 1;
            ?>
        @foreach($data as $product)
          <!-- check for Country Head -->
            @if($user_role == 6)
                @if(in_array($product->brand_id, $brands))
                <tr>
                    <td class="text-right">{{$i}}</td>
                    <td>{{$product->brand_name}}</td>
                    <td>{{$product->category_name}}</td>
                    <td>{{$product->product_code}}</td>
                    <td>{{$product->product_name}}</td>
                    <td>{{$product->uom_name}}</td>

                    <td>{{$opening = getOpening($op_data,$product->product_id)}}</td>
                    <td>{{$inQty = $product->in_qty == null ? '0' : $product->in_qty}}</td>
                    <td>{{$receiveQty = $product->receive_qty == null ? '0' : $product->receive_qty}}</td>
                    <td>{{$transferQty = $product->transfer_qty == null ? '0' : $product->transfer_qty}}</td>
                    <td>{{$saleOrder = getSO($order_data,$product->product_id)}}</td>
                    <td>{{$approvalQty = $product->approval_qty == null ? '0' : $product->approval_qty}}</td>
                    <td>{{$reviseQty = $product->revise_qty == null ? '0' : $product->revise_qty}}</td>
                    <td>{{$reviseSaleQty = $product->revise_sale_qty == null ? '0' : $product->revise_sale_qty}}</td>
                    <td>{{$approvalSaleQty = $product->approval_sale_qty == null ? '0' : $product->approval_sale_qty}}</td>
                    <td>{{$saleQty = $product->sale_qty == null ? '0' : $product->sale_qty}}</td>

                    <td>{{($opening + $inQty + $receiveQty + $reviseQty) - ($saleQty + $saleOrder + $reviseSaleQty + $transferQty)}}</td>
                    
                    <!--<td>
                        {{($opening + $inQty + $receiveQty + $reviseQty) - ($saleQty + $approvalQty + $reviseSaleQty + $transferQty)}}
                    </td>-->

                    <td>
                        <!--{{($opening + $inQty + $receiveQty + $reviseQty) - ($saleQty + $approvalQty + $reviseSaleQty + $transferQty)}}-->

                         {{($opening + $inQty + $receiveQty)-$product->out_qty}}
                    </td>

                    <!--<td>
                    <?php
                        $opening = getOpening($op_data,$product->product_id);
                        echo $opening;
                    ?>

                    </td>
                    <td>{{$product->in_qty}}</td>
                    <td>{{$product->transfer_qty}}</td>
                    <td>{{$product->sale_qty}}</td>
                    <td>
                        {{(($opening+$product->in_qty)-$product->transfer_qty)-$product->sale_qty}}
                    </td>-->
                </tr>
                @endif
            @else
                <tr>
                    <td class="text-right">{{$i}}</td>
                    <td>{{$product->brand_name}}</td>
                    <td>{{$product->category_name}}</td>
                    <td>{{$product->product_code}}</td>
                    <td>{{$product->product_name}}</td>
                    <td>{{$product->uom_name}}</td>

                    <td>{{$opening = getOpening($op_data,$product->product_id)}}</td>
                    <td>{{$inQty = $product->in_qty == null ? '0' : $product->in_qty}}</td>
                    <td>{{$receiveQty = $product->receive_qty == null ? '0' : $product->receive_qty}}</td>
                    <td>{{$transferQty = $product->transfer_qty == null ? '0' : $product->transfer_qty}}</td>
                    <td>{{$saleOrder = getSO($order_data,$product->product_id)}}</td>
                    <td>{{$approvalQty = $product->approval_qty == null ? '0' : $product->approval_qty}}</td>
                    <td>{{$reviseQty = $product->revise_qty == null ? '0' : $product->revise_qty}}</td>
                    <td>{{$reviseSaleQty = $product->revise_sale_qty == null ? '0' : $product->revise_sale_qty}}</td>
                    <td>{{$approvalSaleQty = $product->approval_sale_qty == null ? '0' : $product->approval_sale_qty}}</td>
                    <td>{{$saleQty = $product->sale_qty == null ? '0' : $product->sale_qty}}</td>

                    <td>{{($opening + $inQty + $receiveQty + $reviseQty) - ($saleQty + $saleOrder + $reviseSaleQty + $transferQty)}}</td>
                    
                    <td>
                        <!--{{($opening + $inQty + $receiveQty + $reviseQty) - ($saleQty + $approvalQty + $reviseSaleQty + $transferQty)}}-->

                         {{($opening + $inQty + $receiveQty)-$product->out_qty}}
                    </td>                
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