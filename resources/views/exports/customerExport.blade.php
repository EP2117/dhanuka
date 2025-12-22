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
        <tr><th colspan="13" style="text-align: center;"><h3>Customer Lists</h3></th></tr>
        <tr>
            <th class="text-center">No.</th>
            <th class="text-center">Customer Name</th>
            <th class="text-center">Customer Code</th>
            <th class="text-center">Customer Type</th>
            <th class="text-center">Phone</th>
            <th class="text-center">Township</th>
            <th class="text-center">State</th>
            <th class="text-center">Country</th>
            <th class="text-center">Billing Address</th>
            <th class="text-center">Shipping Address</th>
            <th class="text-center">Status</th>
        </tr>
        </thead>
        <tbody>
            <?php
                $i = 1;
            ?>
        @foreach($data as $customer)
        <tr>
            <td class="text-right">{{$i}}</td>
            <td class="mm-txt">{{$customer->cus_name}}</td>
            <td>{{$customer->cus_code}}</td>
            <td>{{$customer->customer_type_name}}</td>
            <td >{{$customer->cus_phone}}</td>
            <td>{{$customer->township_name}}</td>
            <td>{{$customer->state_name}}</td>
            <td>{{$customer->country_name}}</td>
            <td class="mm-txt">{{$customer->cus_billing_address}}</td>
            <td class="mm-txt">{{$customer->cus_shipping_address}}</td>
            <td>{{empty($customer->is_active) ? 'Inactive' : 'Active'}}</td>
        </tr>
        <?php
            $i++;
        ?>
        @endforeach
        </tbody>
    </table>
</body>
</html>