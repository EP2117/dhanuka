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
        <tr><th colspan="8" class="text-center font-bold mm-txt" style="text-align:center"><b>Sale Analyst Report</b></th></tr>
        <tr>
            <th class="text-center">No.</th>
            <th class="text-center">Brand</th>
            <th class="text-center">Category</th>
            <th class="text-center">Product Code</th>
            <th class="text-center">Product Name</th>
            <th class="text-center">QTY</th>
            <th class="text-center">Selling UOM</th>
            <th class="text-center">Total Amount</th>
        </tr>
        </thead>
        <tbody>
           <?php
              $html = ''; $i=0;$total=0;$total_qty=0;
            foreach($data as $r) {
                $i++;
                $html .= '<tr>';
                $html .= '<td>'.$i.'</td>';
                $html .= '<td class="text-center">'.$r->brand_name.'</td>';
                $html .= '<td class="text-center">'.$r->category_name.'</td>';
                $html .= '<td class="text-center">'.$r->product_code.'</td>';
                $html .= '<td class="text-center">'.$r->product_name.'</td>';

                $html .= '<td class="text-center mm-txt">'.(int)$r->total_quantity.'</td>';
                $html .= '<td class="text-center mm-txt">'.$r->uom_name.'</td>';
                $html .= '<td class="text-right">'.number_format($r->total_amount).'</td>';
                $total += $r->total_amount;
                $total_qty += $r->total_quantity;
                $html .= '</tr>';

            } 
            if(!empty($data)) {
                $html .= '<tr><td colspan="5" style="text-align:right">Total</td><td class="text-right">'.number_format($total_qty).'</td><td></td><td class="text-right">'.number_format($total).'</td></tr>';

            }

            echo $html;

           ?>
        </tbody>
    </table>
</body>
</html>