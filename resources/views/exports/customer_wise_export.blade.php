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
        <tr><th colspan="6" style="text-align: center;"><h3> Category Wise Contact Report</h3></th>
        </tr>
        <tr>
            <td class="text-center">No.</td>
            <td>Category</td>
            <td>Product</td>
            <td class="text-center">Customer Code</td>
            <td class="text-center">Customer Name</td>
            <td class="text-center">Phone Number</td>
        </tr>
        </thead>
        <tbody>
            @php
              $jj=0;
            @endphp
            @foreach($data as $k => $c)
              @if($k == 0 || ($c->state_id != $data[$k-1]->state_id))
              <tr>
                <td colspan="6" style="text-align: center;"><b>State - {{$c->state_name}}</b></td>
              </tr>
              @endif
              @if($k == 0 || ($c->township_id != $data[$k-1]->township_id))
              <tr>
                <td colspan="6" style="text-align: center;"><b>Township - {{$c->township_name}}</b></td>
              </tr>
              @endif
              <tr>
                  @if(!empty($c->id))
                  <td class="text-right" style="vertical-align: middle;">{{$jj=$jj+1}}</td>
                  @else                  
                  <td class="text-right" style="vertical-align: middle;"></td>
                  @endif

                  <!--<td >{{$c->category_name}}</td>-->
                  <td style="padding:0;margin:0;width: 30px;">
                      @if(!empty($c->category_id))

                      @foreach(explode(',',$c->category_id) as $i=>$cid)
                        <?php
                           $pCount = 0;
                           $cat_p_arr = explode('_',$c->cat_product_id);
                           foreach(explode(',',$c->product_id) as $j=>$pid) {
                              if(in_array($pid, explode(',',$cat_p_arr[$i]))) {
                                  $pCount++;
                              }
                           }
                        ?>
                          @php
                            if(empty($request->categories)) {
                              $cateArr = array();
                            }
                            else {
                              $cateArr = explode(',',$request->categories);
                            }
                            $c_name = !empty($c->category_name) ? explode(',',$c->category_name) : array();
                          @endphp
                          @if(empty($request->categories) || (count($cateArr) > 0 &&  in_array($cid,$cateArr)) || !empty(array_intersect(explode(',',$cat_p_arr[$i]), explode(',',$request->products))))
                           <br />
                           {{$c_name[$i]}} 
                           @for($n=0; $n<=$pCount; $n++)
                            <br />
                           @endfor
                          @endif
                    @endforeach
                    @endif
                  </td>

                  <td style="padding:0;margin:0;width: 40px;">
                      @if(!empty($c->product_id))
                      @foreach(explode(',',$c->category_id) as $i=>$cid)
                       @foreach(explode(',',$c->product_id) as $j=>$pid)
                        @php
                           if(empty($request->products)) {
                              $pArr = array();
                           }
                          else {
                              $pArr = explode(',',$request->products);
                          }
                          $cp_arr = explode('_',$c->cat_product_id);
                          $p_name = explode(',',$c->product_name);
                        @endphp

                        @if(in_array($pid, explode(',',$cp_arr[$i])))

                       @if((empty($request->products) && (in_array($cid,explode(',',$request->categories)) || empty($request->categories))) || (count($pArr) > 0 &&  in_array($pid,$pArr)))
                          {{$p_name[$j]}}<br />
                        @endif

                        @endif
                      @endforeach
                     @endforeach
                    @endif
                  </td>

                  <td class="text-center" style="vertical-align: middle;">{{$c->cus_code}}</td>
                  <td class="text-center mm-txt" style="vertical-align: middle;">{{$c->cus_name}}</td>
                  <td class="text-center mm-txt" style="vertical-align: middle;">{{$c->cus_phone}}</td>
              </tr>
            @endforeach
             
        </tbody>
    </table>
</body>
</html>