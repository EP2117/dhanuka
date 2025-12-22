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
        <tr><th colspan="5" style="text-align: center;"><h3> Reorder Level Report</h3></th>
        </tr>
        <tr>
            <th class="text-center">No.</th>
            <th class="text-center">Brand</th>
            <th class="text-center">Category</th>
            <th class="text-center">Product Code</th>
            <th class="text-center">Product Name</th>
            <th class="text-center">Warehouse UOM</th>
            <th class="text-center">Reorder Level</th>
            <th class="text-center">Balance</th>
        </tr>
        </thead>
        <tbody>
            <?php
                $total = 0;
                $i = 1;
            ?>
            @foreach($reorder_level as $cn)
              @if($cn->reorder_level > $cn->balance)
                <tr>
                    <td class="text-right">{{$i}}</td>
                    <td>{{$cn->brand_name}}</td>
                    <td>{{$cn->category_name}}</td>
                    <td>{{$cn->product_code}}</td>
                    <td>{{$cn->product_name}}</td>
                    <td>{{$cn->uom_name}}</td>
                    <td class="text-right">{{$cn->reorder_level}}</td>
                    <td class="text-right">{{$cn->balance}}</td>
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