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
        <tr><th colspan="7" style="text-align: center;"><h3>Credit Payment Report</h3></th>
        </tr>
        <tr>
          <th class="text-center">Payment No.</th>
          <th class="text-center">Payment Date</th>
          <th class="text-center">Invoice Date</th>
          <th class="text-center">Invoice No</th>
          <th class="text-center">Supplier</th>
          <th class="text-center">Payment Amount({{$request->sign}})</th>
          <th class="text-center"> Discount({{$request->sign}})</th>
        </tr>
        </thead>
        <tbody>
            @php 
            $total_paid=0;
            $total_discount=0;
            @endphp
            @foreach($payments as $cc)
               
                        <tr>
                            <td class="text-center">{{$cc->collection_no}}</td>
                            <td class="text-center">{{$cc->collection_date}}</td>
                            <td class="text-center">{{$cc->invoice_date}}</td>
                            <td class="text-center">{{$cc->invoice_no}}</td>
                            <td class="text-center">{{$cc->supplier_name}}</td>
                            @if($request->currency_id!=1 && $request->currency_id!="")
                              @php
                                 $total_paid+=(float)$cc->paid_amount_fx;
                                 $total_discount+=(float)$cc->discount_fx;
                              @endphp
                            <td class="text-center">{{(float)$cc->paid_amount_fx}} </td>
                            <td class="text-center">{{(float)$cc->discount_fx}} </td>
                            @else
                              @php
                                 $total_paid+=$cc->paid_amount;
                                 $total_discount+=$cc->discount;
                              @endphp
                            <td class="text-center">{{$cc->paid_amount}} </td>
                            <td class="text-center">{{$cc->discount == null ? 0 : $cc->discount}} </td>
                            @endif
                        </tr>
            @endforeach
            <tr>
              <td colspan ="5" style="text-align: right;"><strong>Total</strong></td>
              <td class="text-center">
                {{$total_paid}}
              </td>
               <td class="text-center">
                 {{$total_discount}}
               </td>
            </tr>
             
        </tbody>
    </table>
</body>
</html>