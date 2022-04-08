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
        <tr><th colspan="7" style="text-align: center;"><h3> Black List Report</h3></th>
        </tr>
        <tr>
            <th class="text-center">No</th>
            <th class="text-center">Date</th>
            <th class="text-center">User</th>
            <th class="text-center">Status</th>
            <th class="text-center">Remark</th>
            <th class="text-center">Approve By Name</th>
        </tr>
        </thead>
        <tbody>
            @foreach($data as $k => $bl)
              @if($k == 0 || ($bl->customer_id != $data[$k-1]->customer_id))
               @php
                $j=0;
              @endphp
              <tr>
                <td colspan="6" style="text-align: center;"><b>Customer Name - {{$bl->customer->cus_name}}</b></td>
              </tr>
              @endif
              <tr>
                  <td class="text-right">{{$j= $j+1}}</td>
                  <td class="text-center">{{date("Y-m-d h:i", strtotime($bl->added_time))}}</td>
                  <td class="text-center">{{$bl->user->name}}</td>
                  <td class="text-center">{{$bl->is_lock == 1 ? 'Lock' : 'Unlock'}}</td>
                  <td class="text-center mm-txt">{{$bl->rark}}</td>
                  <td class="text-center">{{$bl->approved_by}}</td>
              </tr>
            @endforeach
             
        </tbody>
    </table>
</body>
</html>