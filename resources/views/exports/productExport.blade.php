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
        <tr><th colspan="13" style="text-align: center;"><h3>Product Export</h3></th></tr>
        <tr>
            <th class="text-center">No.</th>
            <th class="text-center">Product Name</th>
            <th class="text-center">Product Code</th>
            <th class="text-center">Brand</th>
            <th class="text-center">Category</th>
            <th class="text-center">Warehouse UOM</th>
            <th class="text-center">Warehouse UOM Selling Price</th>
            <th class="text-center">Selling UOM</th>
            <th class="text-center">Relation</th>
            <th class="text-center">Selling Price/th>
            <th class="text-center">Per Warehouse UOM Price</th>
            <th class="text-center">Minimum QTY</th>
            <th class="text-center">Percentage QTY 100%</th>
            <th class="text-center">Status</th>
        </tr>
        </thead>
        <tbody>
            <?php
                $i = 1;               
            ?>
        @foreach($data as $product)
        <?php
             $j = 1;
            $suom_count = count($product->selling_uoms);
            if($suom_count > 0) {

        ?>
        @foreach($product->selling_uoms as $suom)
            @if($j == 1)
            <tr>
                <td class="text-right">{{$i}}</td>
                <td class="mm-txt">{{$product->product_name}}</td>
                <td>{{$product->product_code}}</td>
                <td  class="mm-txt">{{$product->brand_name}}</td>
                <td class="mm-txt">{{$product->category_name}}</td>
                <td>{{$product->uom_name}}</td>
                <td>{{$product->product_price}}</td>
                <td>{{$suom->uom_name}}</td>
                <td>{{'1'.$suom->uom_name. "=" .$suom->pivot->relation. $product->uom_name}}</td>
                <td>{{$suom->pivot->product_price}}</td>
                <td>{{$suom->pivot->per_warehouse_uom_price}}</td>
                <td>{{$product->minimum_qty}}</td>
                <td>{{$product->percentage_qty}}</td>
                <td>{{empty($product->is_active) ? 'Inactive' : 'Active'}}</td>
            </tr>
            @else
            <tr>
                <td class="text-right"></td>
                <td class="mm-txt"></td>
                <td></td>
                <td class="mm-txt"></td>
                <td class="mm-txt"></td>
                <td></td>
                <td></td>
                <td>{{$suom->uom_name}}</td>
                <td>{{'1'.$suom->uom_name. "=" .$suom->pivot->relation. $product->uom_name}}</td>
                <td>{{$suom->pivot->product_price}}</td>
                <td>{{$suom->pivot->per_warehouse_uom_price}}</td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            @endif
        <?php
            $j++;
        ?>
        @endforeach
        <?php
          }
          else {
        ?>
        <tr>
            <td class="text-right">{{$i}}</td>
            <td class="mm-txt">{{$product->product_name}}</td>
            <td>{{$product->product_code}}</td>
            <td  class="mm-txt">{{$product->brand_name}}</td>
            <td class="mm-txt">{{$product->category_name}}</td>
            <td>{{$product->uom_name}}</td>
            <td>{{$product->product_price}}</td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td>{{$product->minimum_qty}}</td>
            <td>{{$product->percentage_qty}}</td>
            <td>{{empty($product->is_active) ? 'Ínactive' : 'Active'}}</td>
        </tr>
        <?php
            }
            $i++;
        ?>
        @endforeach
        </tbody>
    </table>
</body>
</html>