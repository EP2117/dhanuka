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
        <tr><th colspan="5" style="text-align: center;"><h3> Category Wise Contact Report</h3></th>
        </tr>
        <tr>
            <th class="text-center">No.</th>
            <th class="text-center">Category</th>
            <th class="text-center">Customer Code</th>
            <th class="text-center">Customer Name</th>
            <th class="text-center">Phone Number</th>
        </tr>
        </thead>
        <tbody>
            @php
              $j=0;
            @endphp
            @foreach($data as $k => $c)
              @if($k == 0 || ($c->township_id != $data[$k-1]->township_id))
              <tr>
                <td colspan="5" style="text-align: center;"><b>Township - {{$c->township_name}}</b></td>
              </tr>
              @endif
              <tr>
                  @if(!empty($c->id))
                  <td class="text-right">{{$j=$j+1}}</td>
                  @else                  
                  <td class="text-right"></td>
                  @endif
                  <td >{{$c->category_name}}</td>
                  <td class="text-center">{{$c->cus_code}}</td>
                  <td class="text-center mm-txt">{{$c->cus_name}}</td>
                  <td class="text-center mm-txt">{{$c->cus_phone}}</td>
              </tr>
            @endforeach
             
        </tbody>
    </table>
</body>
</html>