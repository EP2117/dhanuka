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
        <tr><th colspan="16" class="text-center font-bold mm-txt" style="text-align:center"><b>Product Costing Report</b></th></tr>
        <tr>
          <th class="text-center costing_th">Landed Cost No.</th>
          <th class="text-center costing_th">Supplier</th>
          <th class="text-center costing_th">Bill Date</th>
          <th class="text-center costing_th">Bill No.</th>
          <th class="text-center costing_th">Container No.</th>
          <th class="text-center costing_th">CTN QTY</th>
          <th class="text-center costing_th">1CTN=PCS</th>
          <th class="text-center costing_th">Total PCS</th>
          <th class="text-center costing_th">RMB Rate</th>
          <th class="text-center costing_th">Total RMB Amount</th>
          <th class="text-center costing_th">1RMB=Kyat</th>
          <th class="text-center costing_th">MMK Rate</th>
          <th class="text-center costing_th">Duty Charges</th>
          <th class="text-center costing_th">Landed Cost Per Product</th>
          <th class="text-center costing_th">Cost</th>
          <th class="text-center costing_th">Total Cost</th>
      </tr>
        </thead>
        <tbody>
           <?php
              $html = '';

            foreach($data as $k=>$product) {  
                if($k==0 || $product->product_id != $data[$k-1]->product_id)
                {
                  $total_ctn = 0; $total_pcs = 0; $total_mmk = 0;
                  $total_rmb = 0; $total_cost = 0; $total_total_cost = 0;
                  $html .= '<tr><td colspan="16" class="text-center font-bold mm-txt" style="text-align:center"><b>'.$product->product_name.'</b></td></tr>';
                }
                $html .= '<tr>';
                $html .= '<td>'.$product->landed_costing_no.'</td>';
                $html .= '<td class="text-center mm-txt">'.$product->supplier_name.'</td>';
                $html .= '<td class="text-center">'.$product->bill_date.'</td>';
                $html .= '<td class="text-center">'.$product->bill_no.'</td>';
                $html .= '<td class="text-center">'.$product->container_no.'</td>';
                $html .= '<td class="text-right">'.floatval($product->total_ctn) .'</td>';
                $html .= '<td class="text-right">'.floatval($product->pcs_per_ctn) .'</td>';
                $html .= '<td class="text-right">'.floatval($product->total_pcs).'</td>';
                $html .= '<td class="text-right">'.floatval($product->rmb_rate) .'</td>';
                $html .= '<td class="text-right">'.floatval($product->total_rmb) .'</td>';
                $html .= '<td class="text-right">'.floatval($product->mmk_per_rmb) .'</td>';
                $html .= '<td class="text-right">'.floatval($product->mmk_rate) .'</td>';
                $html .= '<td class="text-right">'.floatval($product->duty_charges) .'</td>';
                $html .= '<td class="text-right">'.floatval($product->landed_cost_per_product).'</td>';
                $html .= '<td class="text-right">'.floatval($product->cost).'</td>';
                $html .= '<td class="text-right">'.floatval($product->total_cost) .'</td>';

                $html .= '</tr>';

                $total_ctn = $total_ctn + $product->total_ctn;
                $total_pcs = $total_pcs + $product->total_pcs;
                $total_rmb = $total_rmb + $product->total_rmb;
                $total_mmk = $total_mmk + $product->mmk_rate;
                $total_cost = $total_cost + $product->cost;
                $total_total_cost = $total_total_cost + $product->total_cost;

              if(count($data) == 1 || $k == count($data) - 1 || (isset($data[$k+1]) && $product->product_id != $data[$k+1]->product_id))
                {
                    $html .= '<tr>';
                    $html .= '<td colspan ="5" style="text-align: right;">Total</td>';
                    $html .= '<td style="text-align: right;">'.floatval($total_ctn) .'</td>';
                    $html .= '<td></td>';
                    $html .= '<td style="text-align: right;">'.floatval($total_pcs) .'</td>';
                    $html .= '<td></td>';
                    $html .= '<td style="text-align: right;">'.floatval($total_rmb) .'</td>';
                    $html .= '<td></td>';
                    $html .= '<td style="text-align: right;">'.floatval($total_mmk) .'</td>';
                    $html .= '<td></td>';
                    $html .= '<td></td>';
                    $html .= '<td style="text-align: right;">'.floatval($total_cost) .'</td>';
                    $html .= '<td style="text-align: right;">'.floatval($total_total_cost) .'</td>';
                    $html .= '</tr>';
                } 

            }

            echo $html;

           ?>
        </tbody>
    </table>
</body>
</html>