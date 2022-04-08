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
        <tr><th colspan="13" style="text-align: center;"><h3>Daily Sale Product Wise Report</h3></th></tr>
        <tr>
            <th class="text-center">No.</th>
            <th class="text-center">Invoice No.</th>
            <th class="text-center">Invoice Date</th>
            <th class="text-center">Branch</th>
            <th class="text-center">Customer</th>
            <th class="text-center">Sale Man</th>
            <!--<th class="text-center">Office Sale Man</th>-->
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
                $html = '';
               /** foreach($data as $sale) {
                        $html .= '<tr><td class="text-right">'.$i.'</td><td>'.$sale->invoice_no.'</td><td>'.$sale->invoice_date.'</td>';
                        $html .= '<td class="mm-txt">'.$sale->branch_name.'</td>';
                        $html .= '<td class="mm-txt">'.$sale->cus_name.'</td>';
                        $html .= '<td class="mm-txt">'.$sale->sale_man.'</td>';
                        //$html .= '<td class="mm-txt">'.$sale->office_sale_man.'</td>';              
                        $html .= '<td>'.$sale->product_code.'</td>';
                        $html .= '<td>'.$sale->product_name.'</td>';
                        $html .= '<td>'.$sale->product_quantity.'</td>';
                        $html .= '<td>'.$sale->uom_name.'</td>';
                        if($sale->is_foc == 0) {
                            if(!empty($sale->other_discount)) {
                                $other_discount = ($sale->other_discount/100) * $sale->actual_rate;
                            } else {
                                $other_discount = 0;
                            }
                            $price = $sale->actual_rate - $other_discount;
                            $html .= '<td class="text-right">'.$price.'</td>';
                        }
                        else {
                            $html .= '<td>FOC</td>';
                        }
                        
                        $html .='<td class="text-right">'.$sale->total_amount.'</td>';
                        $html .= '</tr>';
                        
                        if($sale->is_foc == 0){
                            $total = $total + $sale->total_amount;
                        }
                        
                        $i++;
                    
                }**/
                $n=0;
                $sp_total = 0;
                foreach($data as $k=>$sale) {                
                        $html .= '<tr>';
                        if($k==0 || $sale->sale_id != $data[$k-1]->sale_id)
                        {
                            $sp_total = 0;
                            $n=$n+1;
                            $r_count = count(array_keys($sale_arr, (int)$sale->sale_id));
                            $html .= '<td class="text-right" rowspan="'.$r_count.'" style="vertical-align:middle">'.$n.'</td><td rowspan="'.$r_count.'" style="vertical-align:middle">'.$sale->invoice_no.'</td><td rowspan="'.$r_count.'" style="vertical-align:middle">'.$sale->invoice_date.'</td>';
                            $html .= '<td class="mm-txt" rowspan="'.$r_count.'" style="vertical-align:middle;min-width:150px;">'.$sale->branch_name.'</td>';
                            $html .= '<td class="mm-txt" rowspan="'.$r_count.'" style="vertical-align:middle;text-align:center;min-width:150px;">'.str_replace('&', ' &amp; ', $sale->cus_name).'</td>';
                            $html .= '<td class="mm-txt" rowspan="'.$r_count.'" style="min-width:150px;border-right:solid 1px #ccc !important;vertical-align:middle">'.$sale->sale_man.'</td>';
                        } 
                        //$html .= '<td class="mm-txt">'.$sale->office_sale_man.'</td>';
                        $html .= '<td>'.$sale->product_code.'</td>';
                        $html .= '<td class="mm-txt">'.str_replace('&', ' &amp; ', $sale->product_name).'</td>';
                        $html .= '<td>'.$sale->product_quantity.'</td>';
                        $html .= '<td>'.$sale->uom_name.'</td>';
                        if($sale->is_foc == 0) {
                            if(!empty($sale->other_discount)) {
                                $other_discount = ($sale->other_discount/100) * $sale->actual_rate;
                            } else {
                                $other_discount = 0;
                            }
                            $price = $sale->actual_rate - $other_discount;
                            $html .= '<td class="text-right">'.$price.'</td>';
                        }
                        else {
                            $html .= '<td class="text-right">FOC</td>';
                        }

                        $html .='<td class="text-right" style="text-align: right;">'.$sale->total_amount.'</td>';
                        $html .= '</tr>';

                        if($sale->is_foc == 0){
                            $total = $total + $sale->total_amount;
                            $sp_total = $sp_total + $sale->total_amount;
                        }

                        $i++;

                        if(count($data) == 1 || $k == count($data) - 1 || (isset($data[$k+1]) && $sale->sale_id != $data[$k+1]->sale_id))
                        {
                            $html .= '<tr><td colspan ="11" style="text-align: right;">Total</td><td class="text-right" style="text-align: right;">'.number_format($sp_total).'</td></tr>';
                        } 
                } 
                
                $html .= '<tr><td colspan ="11" style="text-align: right;">Total</td><td class="text-right" style="text-align: right;">'.number_format($total).'</td></tr>';
                
                echo $html;
            ?>
        </tbody>
    </table>
</body>
</html>