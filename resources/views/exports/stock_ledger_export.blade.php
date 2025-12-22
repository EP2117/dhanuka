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

        function getOpeningByDate($op_data,$data,$id,$t_date) {
                $op = getOpening($op_data,$id);
                $products = array();
                // foreach($data as $d) {
                //     if($d->product_id == $id) {
                //         array_push($products, $d);
                //     }
                // }

                $closing = 0;
                $entry = 0; $sale_return =0;  $purchase = 0;  $receive = 0;
                $sale = 0; $adjustment =0; $transfer=0;

                foreach($data as $p) {
                   if($p->product_id == $id) {
                   $p_date = $p->transition_date;
                   $t_date = $t_date;
                   if(!empty($p->transition_entry_id) && $p_date < $t_date) {
                        $entry += $p->product_quantity;
                   }
                   else if(!empty($p->transition_return_id) && $p_date < $t_date) {
                        $sale_return += $p->product_quantity;
                   }
                   else if(!empty($p->transition_purchase_id) && $p_date < $t_date) {
                        $purchase += $p->product_quantity;
                   }
                   else if(!empty($p->transition_transfer_id) && $p->transition_type == 'out' && $p_date < $t_date) {
                        $transfer += $p->product_quantity;
                   }
                   else if(!empty($p->transition_transfer_id) && $p->transition_type == 'in' && $p_date < $t_date) {
                        $receive += $p->product_quantity;
                   }
                   else if(!empty($p->transition_sale_id) && empty($p->transition_return_id) && $p_date < $t_date) {
                        $sale += $p->product_quantity;
                   }
                   else if(!empty($p->transition_adjustment_id) && $p_date < $t_date) {
                        $adjustment += $p->product_quantity;
                   } else {}
                }
                }

                //console.log (op + ', E ' + entry + ', P ' + purchase+ ', R ' + sale_return+ ', Rcv ' + receive+ ', Adj ' + adjustment+ ', S ' + sale+ ', T ' + transfer)

                $closing = ($op + $entry + $purchase + $sale_return + $receive + $adjustment)-($sale + $transfer);

                return $closing;
            }

            function getClosing($op_data,$data,$id,$t_date) {

                $op = getOpening($op_data,$id);
                $products = array();
                // foreach($data as $d) {
                //     array_push($products, $d);
                // }

                $closing = 0;
                $entry = 0; $sale_return =0;  $purchase = 0;  $receive = 0;
                $sale = 0; $adjustment =0; $transfer=0;

                foreach($data as $p) {
                if($p->product_id == $id) {
                   $p_date = $p->transition_date;
                   $t_date = $t_date;
                   if(!empty($p->transition_entry_id) && $p_date <= $t_date) {
                        $entry += $p->product_quantity;
                   }
                   else if(!empty($p->transition_return_id) && $p_date <= $t_date) {
                        $sale_return += $p->product_quantity;
                   }
                   else if(!empty($p->transition_purchase_id) && $p_date <= $t_date) {
                        $purchase += $p->product_quantity;
                   }
                   else if(!empty($p->transition_transfer_id) && $p->transition_type == 'out' && $p_date <= $t_date) {
                        $transfer += $p->product_quantity;
                   }
                   else if(!empty($p->transition_transfer_id) && $p->transition_type == 'in' && $p_date <= $t_date) {
                        $receive += $p->product_quantity;
                   }
                   else if(!empty($p->transition_sale_id) && empty($p->transition_return_id) && $p_date <= $t_date) {
                        $sale += $p->product_quantity;
                   }
                   else if(!empty($p->transition_adjustment_id ) && $p_date <= $t_date) {
                        $adjustment += $p->product_quantity;
                   } else {}
                   }
                }

                //console.log (op + ', E ' + entry + ', P ' + purchase+ ', R ' + sale_return+ ', Rcv ' + receive+ ', Adj ' + adjustment+ ', S ' + sale+ ', T ' + transfer)

                $closing = ($op + $entry + $purchase + $sale_return + $receive + $adjustment)-($sale + $transfer);

                return $closing;
            }

            
    ?>
    <table id="t01" class="table_no" width="100%" style="table-layout: fixed">
        <thead>
        <tr><th colspan="13" style="text-align: center;"><h3>Inventory Report</h3></th></tr>
        <tr>
            <th >Date</th>
            <th>Brand</th>
            <th>Product Code</th>
            <th>Product Name</th>
            <th>Invoice No.</th>
            <th>Description</th>
            <th>Opening</th>
            <th>In</th>
            <th>Stock <br />Receive</th>
            <th>Stock <br />Transfer</th>
            <th>Direct Sale</th>
            <th>Sale Return</th>
            <th>Adjustment</th>
        </tr>
        </thead>
        <tbody>
            <?php
                $total = 0;
                $i = 1;
            ?>

            @foreach($data as $k=>$p)
                @if($k== 0 || (count($data) > 1 && ($data[$k-1]->product_id !=$p->product_id && $data[$k-1]->transition_date != $p->transition_date) || ($data[$k-1]->product_id == $p->product_id && $data[$k-1]->transition_date != $p->transition_date) ||  ($data[$k-1]->product_id != $p->product_id && $data[$k-1]->transition_date == $p->transition_date)))

                                <tr >
                                    <td>{{$p->transition_date}}</td>
                                    <td>{{$p->brand_name}}</td>
                                    <td>{{$p->product_code}}</td>
                                    <td>{{$p->product_name}}</td>
                                    <td></td>

                                    <td></td>

                                    @if($k==0)
                                    <td v-if="index==0">{{getOpening($op_data,$p->product_id)}}</td>
                                    @else
                                    <td v-if="index==0">{{getOpeningByDate($op_data,$data,$p->product_id,$p->transition_date)}}</td>
                                    @endif
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    @if(empty($p->transition_return_id))
                                    <td>{{$p->invoice_no}}</td>
                                    @else
                                    <td>{{$p->return_invoice_no}}</td>
                                    @endif                                  
                                    

                                    @if(!empty($p->transition_sale_id))
                                    <td>{{ $p->sale_customer_name}} -  {{$p->invoice_no}}
                                    </td>
                                    @elseif(!empty($p->transition_purchase_id))
                                    <td>
                                        <!-- Purchase Invoice --> {{$p->purchase_reference_no}} - {{$p->invoice_no}}
                                     </td>
                                     @elseif(!empty($p->transition_entry_id))
                                    <td> 
                                        <!--Main Warehouse Entry --> {{$p->entry_reference_no}} - {{$p->invoice_no}}
                                    </td>
                                     @elseif(!empty($p->transition_adjustment_id))
                                    <td> 
                                        <!--Inventory Adjustment--> {{$p->adjustment_reference_no}} - {{$p->invoice_no}}
                                    </td>
                                    @elseif(!empty($p->transition_transfer_id) && $p->transition_type == 'out')
                                    <td> Transfer</td>
                                    @elseif(!empty($p->transition_transfer_id) && $p->transition_type == 'in')
                                    <td> Receive from Tranfer</td>
                                    @elseif(!empty($p->transition_return_id))
                                    <td> 
                                        <!--Sale Return-->{{$p->return_customer_name}} - {{$p->return_invoice_no}}
                                    </td>
                                    @else
                                    @endif

                                    <td></td>

                                    <td>{{!empty($p->transition_entry_id) || !empty($p->transition_purchase_id) ? $p->product_quantity : ''}}</td>
                                    <td>{{!empty($p->transition_transfer_id) && $p->transition_type == 'in' ? $p->product_quantity : ''}}</td>
                                    <td>{{!empty($p->transition_transfer_id) && $p->transition_type == 'out' ? $p->product_quantity : ''}}</td>
                                    <td>{{!empty($p->transition_sale_id) && empty($p->transition_return_id) ? $p->product_quantity : ''}}</td>
                                    <td>{{!empty($p->transition_return_id) ? $p->product_quantity : ''}}</td>
                                    <td>{{!empty($p->transition_adjustment_id) ? $p->product_quantity : ''}}</td>
                                </tr>
                        @else
                               <tr>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    @if(empty($p->transition_return_id))
                                    <td>{{$p->invoice_no}}</td>
                                    @else
                                    <td>{{$p->return_invoice_no}}</td>
                                    @endif                                  
                                    

                                    @if(!empty($p->transition_sale_id))
                                    <td>{{ $p->sale_customer_name}} -  {{$p->invoice_no}}
                                    </td>
                                    @elseif(!empty($p->transition_purchase_id))
                                    <td>
                                        <!-- Purchase Invoice --> {{$p->purchase_reference_no}} - {{$p->invoice_no}}
                                     </td>
                                     @elseif(!empty($p->transition_entry_id))
                                    <td> 
                                        <!--Main Warehouse Entry --> {{$p->entry_reference_no}} - {{$p->invoice_no}}
                                    </td>
                                     @elseif(!empty($p->transition_adjustment_id))
                                    <td> 
                                        <!--Inventory Adjustment--> {{$p->adjustment_reference_no}} - {{$p->invoice_no}}
                                    </td>
                                    @elseif(!empty($p->transition_transfer_id) && $p->transition_type == 'out')
                                    <td> Transfer</td>
                                    @elseif(!empty($p->transition_transfer_id) && $p->transition_type == 'in')
                                    <td> Receive from Tranfer</td>
                                    @elseif(!empty($p->transition_return_id))
                                    <td> 
                                        <!--Sale Return-->{{$p->return_customer_name}} - {{$p->return_invoice_no}}
                                    </td>
                                    @else
                                    @endif

                                    <td></td>

                                    <td>{{!empty($p->transition_entry_id) || !empty($p->transition_purchase_id) ? $p->product_quantity : ''}}</td>
                                    <td>{{!empty($p->transition_transfer_id) && $p->transition_type == 'in' ? $p->product_quantity : ''}}</td>
                                    <td>{{!empty($p->transition_transfer_id) && $p->transition_type == 'out' ? $p->product_quantity : ''}}</td>
                                    <td>{{!empty($p->transition_sale_id) && empty($p->transition_return_id) ? $p->product_quantity : ''}}</td>
                                    <td>{{!empty($p->transition_return_id) ? $p->product_quantity : ''}}</td>
                                    <td>{{!empty($p->transition_adjustment_id) ? $p->product_quantity : ''}}</td>
                                </tr>
                                @endif
                                @if(count($data) == 1 || count($data) - 1 == $k)
                                    <tr>
                                        <td style="text-align:right;" colspan="6"><b>Closing Balances</b></td>
                                        <td>{{getClosing($op_data,$data,$p->product_id,$p->transition_date) }} </td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                    </tr>

                                @elseif(($data[$k+1]->product_id != $p->product_id && $data[$k+1]->transition_date != $p->transition_date) || ($data[$k+1]->product_id == $p->product_id && $data[$k+1]->transition_date != $p->transition_date) || ($data[$k+1]->product_id != $p->product_id && $data[$k+1]->transition_date == $p->transition_date))
                                    <tr>
                                        <td style="text-align:right" colspan="6"><b>Closing Balances</b></td>
                                        <td>{{getClosing($op_data,$data,$p->product_id,$p->transition_date) }} </td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                @else
                                @endif
                            @endforeach
        </tbody>
    </table>
</body>
</html>