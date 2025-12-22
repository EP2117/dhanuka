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
        <tr><th colspan="10" class="text-center font-bold mm-txt" style="text-align:center"><b>Purchase Currency Gain/Loss Report</b></th></tr>
        <tr>
            <th class="text-center costing_th">No.</th>
            <th class="text-center costing_th">Advance No.</th>
            <th class="text-center costing_th">Advance Date</th>
            <th class="text-center costing_th">Advance <br />Currency Rate</th>
            <th class="text-center costing_th">Invoice No.</th>
            <th class="text-center costing_th">Invoice Date</th>
            <th class="text-center costing_th">Invoice <br />Currency Rate</th>
            <th class="text-center costing_th">Payment No.</th>
            <th class="text-center costing_th">Payment Date</th>
            <th class="text-center costing_th">Payment <br />Currency Rate</th>
            <th class="text-center costing_th">Currency</th>
            <th class="text-center costing_th">Gain</th>
            <th class="text-center costing_th">Loss</th>
        </tr>
        </thead>
        <tbody>
           <?php
              $html = ''; $i=0;$gain =$loss=0;
            foreach($data as $p) {
                $i++;
                $html .= '<tr>';
                $html .= '<td>'.$i.'</td>';
                $html .= '<td class="text-center">'.$p->advance_no.'</td>';
                $html .= '<td class="text-center">'.$p->advance_date.'</td>';
                if(!empty($p->advance_no)) {
                    $html .= '<td class="text-center">1'.$p->adv_currency_sign.' = '.floatval($p->adv_currency_rate).'MMK</td>'; 
                }else {
                    $html .= '<td class="text-center"></td>'; 
                }           
                if(empty($p->advance_no)) {
                    $html .= '<td class="text-center">'.$p->purchase_invoice_no.'</td>';
                    $html .= '<td class="text-center">'.$p->purchase_invoice_date.'</td>';
                    $html .= '<td class="text-center">1'.$p->purchase_currency_sign.' = '.floatval($p->purchase_currency_rate).'MMK</td>';
                }
                else {
                    $html .= '<td class="text-center">'.$p->invoice_no.'</td>';
                    $html .= '<td class="text-center">'.$p->invoice_date.'</td>';
                    $html .= '<td class="text-center">1'.$p->inv_currency_sign.' = '.floatval($p->inv_currency_rate).'MMK</td>';
                }

                $html .= '<td style="text-align:center;">'.$p->collection_no.'</td>';
                $html .= '<td style="text-align:center; ">'.$p->collection_date.'</td>';
                if(empty($p->advance_no)) {
                    $html .= '<td style="text-align:center;">1'.$p->col_currency_sign.' = '.floatval($p->col_currency_rate).'MMK</td>';
                } else {
                    $html .= '<td></td>';
                }
                $html .= '<td style="text-align:center;">'.$p->col_currency_name.'</td>';

                $gain_amount = empty($p->credit) ? '' : floatval($p->credit);
                $loss_amount = empty($p->debit) ? '' : floatval(abs($p->debit));
                $html .= '<td class="text-right">'.$gain_amount .'</td>';
                $html .= '<td class="text-right">'.$loss_amount .'</td>';

                $html .= '</tr>';
                $credit = !empty($p->credit) ? $p->credit : 0;
                $debit = !empty($p->debit) ? $p->debit : 0;
                $gain += abs($credit);
                $loss += abs($debit);
            } 
            $net_gain = $net_loss = 0;
            if(!empty($data)) {
                $html .= '<tr><td colspan="11" class="text-right" style="text-align:right">Total</td><td class="text-right">'.floatval($gain) .'</td><td class="text-right" style="text-align:right">'.floatval($loss) .'</td></tr>';

                if($gain > $loss) {
                    $net_gain = $gain - $loss;
                } else {
                    $net_loss = $loss - $gain;
                }
                $net_gain = $net_gain == 0 ? '' : floatval($net_gain);
                $net_loss = $net_loss == 0 ? '' : floatval($net_loss);
                $html .= '<tr><td colspan="11" style="text-align:right">Net Total</td><td class="text-right">'.$net_gain.'</td><td class="text-right" style="text-align:right">'.$net_loss.'</td></tr>';

            }

            echo $html;

           ?>
        </tbody>
    </table>
</body>
</html>