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
            foreach($data as $collection) {
                $rowspan = count($collection->purchases);
            foreach($collection->purchases as $k=>$p) {
                $i++;
                $html .= '<tr>';
                $html .= '<td>'.$i.'</td>';
                $html .= '<td class="text-center">'.$p->invoice_no.'</td>';
                $html .= '<td class="text-center">'.$p->invoice_date.'</td>';
                $html .= '<td class="text-center">1'.$collection->currency->sign.' = '.floatval($p->currency_rate).'MMK</td>';
                if($k==0) {
                    $html .= '<td rowspan="'.$rowspan.'" style="text-align:center; vertical-align:middle;">'.$collection->collection_no.'</td>';
                    $html .= '<td rowspan="'.$rowspan.'" style="text-align:center; vertical-align:middle;">'.$collection->collection_date.'</td>';
                    $html .= '<td rowspan="'.$rowspan.'" style="text-align:center; vertical-align:middle;">1'.$collection->currency->sign.' = '.floatval($collection->currency_rate).'MMK</td>';
                    $html .= '<td rowspan="'.$rowspan.'" style="text-align:center; vertical-align:middle;">'.$collection->currency->name.'</td>';
                }

                $gain_amount = $p->pivot->gain_amount == 0 ? '' : floatval($p->pivot->gain_amount);
                $loss_amount = abs($p->pivot->loss_amount) == 0 ? '' : floatval(abs($p->pivot->loss_amount));
                $html .= '<td class="text-right">'.$gain_amount .'</td>';
                $html .= '<td class="text-right">'.$loss_amount .'</td>';

                $html .= '</tr>';

                $gain += abs($p->pivot->gain_amount);
                $loss += abs($p->pivot->loss_amount);

            } 
            } 
            $net_gain = $net_loss = 0;
            if(!empty($data)) {
                $html .= '<tr><td colspan="8" class="text-right" style="text-align:right;">Total</td><td class="text-right">'.floatval($gain) .'</td><td class="text-right">'.floatval($loss) .'</td></tr>';

                if($gain > $loss) {
                    $net_gain = $gain - $loss;
                } else {
                    $net_loss = $loss - $gain;
                }
                $net_gain = $net_gain == 0 ? '' : floatval($net_gain);
                $net_loss = $net_loss == 0 ? '' : floatval($net_loss);
                $html .= '<tr><td colspan="8" class="text-right" style="text-align:right;">Net Total</td><td class="text-right">'.$net_gain.'</td><td class="text-right">'.$net_loss.'</td></tr>';

            }

            echo $html;

           ?>
        </tbody>
    </table>
</body>
</html>