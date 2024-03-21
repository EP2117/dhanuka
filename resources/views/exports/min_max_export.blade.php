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
        <tr><th colspan="5" style="text-align: center;"><h3> Min-Max Report</h3></th>
        </tr>
        <tr>
            <th class="text-center">No</th>
            <th class="text-center">Product Code</th>
            <th class="text-center">Product Name</th>
            <th class="text-center">Brand</th>
            <th class="text-center">Category </th>
            <th class="text-center">Balance</th>
            {{-- <th class="text-center" v-if="search->type_id== 'min'">Min Qty</th> --}}
            {{-- <th class="text-center" v-else-if="search->type_id== 'max'">Max Qty</th> --}}
            @if($type=="min")
            <th class="text-center" >Minimum Qty</th>

            @elseif($type=="maximum")
            <th class="text-center" >Maximum Qty</th>
            @else 
            <th class="text-center" >Qty</th>

            @endif
            <!-- <th class="text-center" v-if="search->type_id== 'min'">Min Qty</th>
            <th class="text-center" v-else-if="search->type_id== 'max'">Max Qty</th>
            <th class="text-center" v-else>Qty</th> -->
            <th class="text-center">Type</th>
        </tr>
        </thead>
        <tbody>
            <?php
                $i = 1;
            ?>
            @foreach($min_max as $mm)
            @if($mm->balance != $mm->maximum_qty && $mm->balance != $mm->minimum_qty)
            @if($type=="min" && $mm->balance < $mm->minimum_qty)
                <tr>
                    <td class="text-right"></td>
                    <td class="text-center">{{$mm->product_code}}</td>
                    <td class="text-center">{{$mm->product_name}}</td>
                    <td class="text-center">{{$mm->brand_name}}</td>
                    <td class="text-center">{{$mm->category_name}}</td>
                    <td class="text-center" >{{$mm->balance}}</td>
                    <td class="text-center" > {{$mm->minimum_qty}}</td>
                    <!-- <td class="text-center" v-else-if="$mm->balance>$mm->maximum_qty"> {{$mm->maximum_qty}}</td> -->
                    <td class="text-center" >Short</td>
                    <!-- <td class="text-center" v-else-if="$mm->balance>$mm->maximum_qty">Excess</td> -->
                    <!-- <td class="text-center">{{$mm->uom_name}}</td> -->
                </tr>
            @endif
            @if($type=="max" && $mm->balance > $mm->maximum_qty)
                <tr>
                    <td class="text-right"></td>
                    <td class="text-center">{{$mm->product_code}}</td>
                    <td class="text-center">{{$mm->product_name}}</td>
                    <td class="text-center">{{$mm->brand_name}}</td>
                    <td class="text-center">{{$mm->category_name}}</td>
                    <td class="text-center" >{{$mm->balance}}</td>
                    <td class="text-center" > {{$mm->maximum_qty}}</td>
                    <!-- <td class="text-center" v-else-if="$mm->balance>$mm->maximum_qty"> {{$mm->maximum_qty}}</td> -->
                    <td class="text-center" >Excess</td>
                    <!-- <td class="text-center" v-else-if="$mm->balance>$mm->maximum_qty">Excess</td> -->
                    <!-- <td class="text-center">{{$mm->uom_name}}</td> -->
                </tr>
            @endif
            @if($type != 'max' && $type != 'min' && ($mm->balance<$mm->minimum_qty  || $mm->balance>$mm->maximum_qty))
                <tr >
                    <td class="text-right"></td>
                    <td class="text-center">{{$mm->product_code}}</td>
                    <td class="text-center">{{$mm->product_name}}</td>
                    <td class="text-center">{{$mm->brand_name}}</td>
                    <td class="text-center">{{$mm->category_name}}</td>
                    <td class="text-center" >{{$mm->balance}}</td>
                    @if($mm->balance<$mm->minimum_qty)
                    <td class="text-center" > {{$mm->minimum_qty}}</td>

                    @elseif($mm->balance>$mm->maximum_qty)
                    <td class="text-center" > {{$mm->maximum_qty}}</td>

                    @endif
                    @if($mm->balance<$mm->minimum_qty)
                    <td class="text-center" >Short</td>

                    @elseif($mm->balance>$mm->maximum_qty)
                    <td class="text-center" >Excess</td>

                    @endif
                    
                    <!-- <td class="text-center">{{$mm->uom_name}}</td> -->
                </tr>
            @endif
        @endif
              <?php 
              $i++;
               ?>

            @endforeach
            
        </tbody>
    </table>
</body>
</html>