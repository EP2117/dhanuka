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
        <tr><th colspan="7" class="text-center font-bold mm-txt" style="text-align:center"><b>Sale Return Report</b></th></tr>
        <tr>
            <th class="text-center costing_th">No.</th>
            <th class="text-center costing_th">Invoice No.</th>
            <th class="text-center costing_th">Invoice Date</th>
            <th class="text-center costing_th">Return No.</th>
            <th class="text-center costing_th">Date</th>
            <th class="text-center costing_th">Customer</th>
            <th class="text-center costing_th">Return Amount</th>
        </tr>
        </thead>
        <tbody>
           <?php
              $html = ''; $i=0;$total=0;
            foreach($data as $r) {
                $i++;
                $html .= '<tr>';
                $html .= '<td>'.$i.'</td>';
                if(!empty($r->sale)) {
                    $html .= '<td class="text-center">'.$r->sale->invoice_no.'</td>';
                    $html .= '<td class="text-center">'.$r->sale->invoice_date.'</td>';
                } else {
                   $html .= '<td class="text-center"></td>';
                   $html .= '<td class="text-center"></td>';  
                }

                $html .= '<td class="text-center">'.$r->return_no.'</td>';
                $html .= '<td class="text-center">'.$r->return_date.'</td>';

                $html .= '<td class="text-center mm-txt">'.$r->customer->cus_name.'</td>';
                $html .= '<td style="text-align:right">'.number_format($r->total_amount).'</td>';
                $total += $r->total_amount;
                $html .= '</tr>';

            } 
            if(!empty($data)) {
                $html .= '<tr><td colspan="6" style="text-align:right">Return Total</td><td style="text-align:right">'.number_format($total).'</td></tr>';

            }

            echo $html;

           ?>
        </tbody>
    </table>
</body>
</html>