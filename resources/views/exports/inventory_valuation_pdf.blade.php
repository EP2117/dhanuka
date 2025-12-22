<!DOCTYPE html>
<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <style type="text/css">
        .body {
            font-family: 'ZawgyiOne2008' !important;
            font-size: 12px;
        }

        .mm-txt {
            font-family: 'ZawgyiOne2008' !important;
            font-size: 12px;
        }

        table#t01 {
            width: 100%;
            border: solid 1px #000;
            border-collapse: collapse;
            font-size: 12px;
        }

        th,
        td {
            border: 1px solid black;
            padding: 5px;
            font-size: 12px;
        }

        th {
            text-align: center;
            font-size: 12px;
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
                <th class="text-center">No</th>
                <th class="text-center">Brand</th>
                <th class="text-center">Internal Category</th>
                <th class="text-center">Product Code</th>
                <th class="text-center">Product Name</th>
                <th class="text-center">Warehouse UOM</th>
                <th class="text-center">Balance Qty </th>
                <th class="text-center">Valuation Amount</th>
                <th class="text-center">Adjustment In <br />Valuation Amount</th>
                <th class="text-center">Adjustment Out <br />Valuation Amount</th>
                <th class="text-center">Before Adjustment <br />Valuation Amount</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $i = 0;
            ?>
            @foreach($data as $product)
            <?php
            $i++;
            ?>
            <tr>
                <td class="text-center">{{$i}}</td>
                <td class="text-center">{{$product->brand_name}}</td>
                <td class="text-center">{{$product->category_name}}</td>
                <td class="text-center">{{$product->product_code}}</td>
                <td class="text-center">{{$product->product_name}}</td>
                <td class="text-center">{{$product->uom_name}}</td>
                <td class="text-center">{{$product->balance}}</td>
                <td class="text-center">{{$product->t_valuation_amount}}</td>
                <td class="text-center">{{$product->adj_in_cost_price}}</td>
                <td class="text-center">{{$product->adj_out_cost_price}}</td>
                <td class="text-center">{{($product->t_valuation_amount + $product->adj_out_cost_price) - $product->adj_in_cost_price}}</td>
            </tr>
            @endforeach
            <tr>
                <td colspan="7" style="text-align: right;"><strong>Total Amt</strong></td>
                <td class="text-center">
                    {{$total_valuation}}
                </td>
                <td class="text-center">
                   {{$total_adj_in}}
               </td>
               <td class="text-center">
                   {{$total_adj_out}}
               </td>
               <td class="text-center">
                   {{$total_after_valuation}}
               </td>
            </tr>
        </tbody>
    </table>
</body>

</html>