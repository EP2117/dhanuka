<!DOCTYPE html>
<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <style type="text/css">
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
    <?php
    function getOpening($op_data, $product_id)
    {
        $in_count = 0;
        $out_count = 0;
        $count = 0;
        foreach ($op_data as $op_product) {
            if ($op_product->product_id == $product_id) {
                $in_count  = $in_count + $op_product->in_qty;
                $out_count = $out_count + $op_product->out_qty;
            }
        }

        $count = $in_count - $out_count;
        return $count;
    }
    ?>
    <table id="t01" class="table_no" style="table-layout: fixed">
        <thead>
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
                <th class="text-center">Direct Sale</th>
                <th class="text-center">Balance</th>

            </tr>
        </thead>
        <tbody>
            <?php
            $i = 0;
            ?>
            @foreach($data as $product)
            <?php
            $i++;
            $opening = getOpening($op_data, $product->product_id);
            ?>
            <tr>
                <td>{{$i}}</td>
                <td>{{$product->brand_name}}</td>
                <td>{{$product->category_name}}</td>
                <td>{{$product->product_code}}</td>
                <td>{{$product->product_name}}</td>
                <td>{{$product->uom_name}}</td>
                <td>{{$opening}}</td>
                <td>{{$product->inQty=$product->in_qty==null? 0 :intval($product->in_qty)}}</td>
                <td>{{$product->receiveQty = $product->receive_qty == null ? '0' : $product->receive_qty}}</td>
                <td>{{$product->transferQty = $product->transfer_qty == null ? '0' : $product->transfer_qty}}</td>
                <td>{{$product->saleQty = $product->sale_qty == null ? '0' : $product->sale_qty}}</td>
                <td>
                    {{(floatval($opening) + floatval($product->inQty)+ floatval($product->receiveQty) )- floatval($product->out_qty)}}
                </td>
            </tr>
            @endforeach

        </tbody>
    </table>
</body>

</html>