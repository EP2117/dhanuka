<!DOCTYPE html>
<html>
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <style type="text/css">
    @page { margin: 5px; }
   body { margin: 0px; }
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
          @if($request->pshow == 1)
          @php
            $cspan = 6;
          @endphp
          @else
            @php
              $cspan = 5;
            @endphp
          @endif
        <tr><th colspan="{{$cspan}}" style="text-align: center;"><h3> Category Wise Contact Report</h3></th>
        </tr>
        <tr>
            <td style="text-align: center" width="4%">No.</td>
            @if($request->pshow == 1)
            <td style="text-align: center" width="20%">Category</td>
            <td style="text-align: center" width="30%">Product</td>
            @else
            <td style="text-align: center" width="30%">Category</td>
            @endif
            <td style="text-align: center"class="text-center">Customer Code</td>
            <td style="text-align: center" class="text-center">Customer Name</td>
            <td style="text-align: center" class="text-center">Phone Number</td>
        </tr>
        </thead>
        <tbody>
            @php
              $jj=0;
            @endphp
            @foreach($data as $k => $c)
              @if($k == 0 || ($c->state_id != $data[$k-1]->state_id))
              <tr>
                <td colspan="{{$cspan}}" style="text-align: center;"><b>State - {{$c->state_name}}</b></td>
              </tr>
              @endif
              @if($k == 0 || ($c->township_id != $data[$k-1]->township_id))
              <tr>
                <td colspan="{{$cspan}}" style="text-align: center;"><b>Township - {{$c->township_name}}</b></td>
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
                            
                           @if($request->pshow == 1)
                          <div style="height: 5px"></div>
                           {{$c_name[$i]}}
                            @for($n=0; $n<=$pCount; $n++)
                            <br />
                           @endfor
                           @else
                           <br />
                           @endif
                          @endif
                    @endforeach
                    @endif
                  </td>
                  @if($request->pshow == 1)
                  <td style="padding:0;margin:0;width: 40px;vertical-align: bottom;">
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
                        @else
                        <div style="height: 6px;"></div>
                        @endif
                      @endforeach
                     @endforeach
                    @endif
                  </td>
                  @endif
                  <td class="text-center" style="vertical-align: middle;">{{$c->cus_code}}</td>
                  <td class="text-center mm-txt" style="vertical-align: middle;">{{$c->cus_name}}</td>
                  <td class="text-center mm-txt" style="vertical-align: middle;">{{$c->cus_phone}}</td>
              </tr>
            @endforeach
             
        </tbody>
    </table>
</body>
</html>