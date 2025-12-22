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
        <tr><th colspan="11" style="text-align: center;"><h3>Daily Purchase Product Report</h3></th>
        </tr>
        <tr>
          <th class="text-center">No.</th>
          <th class="text-center">Invoice No.</th>
          <th class="text-center">Invoice Date</th>
          <th class="text-center">Branch</th>
          <th class="text-center">Supplier</th>
          <th class="text-center">Product Code</th>
          <th class="text-center">Product Name</th>
          <th class="text-center">QTY</th>
          <th class="text-center"> UOM</th>
          <th class="text-center">Unit Price</th>
          <th class="text-center">Total Amount</th>
        </tr>
        </thead>
        <tbody>
          <?php
            $total = 0;
            $i = 1;
            $html = '';
            foreach($data as $purchase) {
                $html .= '<tr><td class="text-right"></td><td>'.$purchase->invoice_no.'</td><td>'.$purchase->invoice_date.'</td>';
                $html .= '<td class="mm-txt">'.$purchase->branch_name.'</td>';
                $html .= '<td class="mm-txt">'.$purchase->name.'</td>';
                $html .= '<td>'.$purchase->product_code.'</td>';
                $html .= '<td>'.$purchase->product_name.'</td>';
                $html .= '<td>'.$purchase->product_quantity.'</td>';
                $html .= '<td>'.$purchase->uom_name.'</td>';
                if($purchase->is_foc == 0) {
                    $html .= '<td class="text-right">'.$purchase->price.'</td>';
                }
                else {
                    $html .= '<td>FOC</td>';
                }

                $html .='<td class="text-right">'.$purchase->total_amount.'</td>';
                $html .= '</tr>';

                if($purchase->is_foc == 0){
                    $total = $total + $purchase->total_amount;
                }

                $i++;

            }

            $html .= '<tr><td colspan ="10" style="text-align: right;"><strong>Total</strong></td><td class="text-right">'.number_format($total).'</td></tr>';

            echo $html;
        ?>
             
        </tbody>
    </table>
</body>
</html>