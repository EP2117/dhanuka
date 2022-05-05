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
            <td width="5%" style="border:0px">No.</td>
             @if($request->pshow == 1)
            <td width="45%" style="border:0px">
             <div style="float:left;padding:0;margin:0;width:150px;">Category</div>
            
             <div style="float:left;padding:0;margin:0;width:200px;">Product</div>
            </td>
            @else
            <td width="30%" style="border:0px">
             <div style="float:left;padding:0;margin:0;width:150px;">Category</div>
            </td>
            @endif
            <!--<th class="text-center">Product</th>-->
            <td style="border:0px">Customer Code</td>
            <td style="border:0px">Customer Name</td>
            <td style="border:0px">Phone Number</td>
        </tr>
        </thead>
        <tbody>
            @php
              $jj=0;
            @endphp
            @foreach($data as $k => $c)
              @if($k == 0 || ($c->state_id != $data[$k-1]->state_id))
              <tr>
                <td colspan="5" style="text-align: center;"><b>State - {{$c->state_name}}</b></td>
              </tr>
              @endif
              @if($k == 0 || ($c->township_id != $data[$k-1]->township_id))
              <tr>
                <td colspan="5" style="text-align: center;"><b>Township - {{$c->township_name}}</b></td>
              </tr>
              @endif
              <tr>
                  @if(!empty($c->id))
                  <td class="text-right" style="vertical-align: middle;">{{$jj=$jj+1}}</td>
                  @else                  
                  <td class="text-right" style="vertical-align: middle;"></td>
                  @endif

                  <!--<td >{{$c->category_name}}</td>-->
                  <td style="padding:0;margin:0;">
                    <table cellpadding='0' cellspacing='0' border='0'>
                      @if(!empty($c->category_id))

                      @foreach(explode(',',$c->category_id) as $i=>$cid)
                          @php
                            if(empty($request->categories)) {
                              $cateArr = array();
                            }
                            else {
                              $cateArr = explode(',',$request->categories);
                            }
                            $c_name = !empty($c->category_name) ? explode(',',$c->category_name) : array();
                          @endphp
                          <tr>
                          @if(empty($request->categories) || (count($cateArr) > 0 &&  in_array($cid,$cateArr)))
                            @if($request->pshow == 1)
                            <td style='vertical-align:middle;width:120px;' >{{$c_name[$i]}}
                            </td>
                            @else
                            <td style='vertical-align:middle;width:200px;' >{{$c_name[$i]}}
                            </td>
                            @endif
                          @endif
                            @if($request->pshow == 1)
                            @if(!empty($c->product_id))

                            <td style="padding:0;margin:0">
                            <table cellpadding='0' cellspacing='0' border='0' width="100%">
                             
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
                                  <tr>

                                  @if(empty($request->products) || (count($pArr) > 0 &&  in_array($pid,$pArr)))
                                  <td style='text-align:left; width:150px;'>{{$p_name[$j]}}</td>
                                  @else
                                  <td style='text-align:left; width:150px;height:0; border: 0'></td>
                                  @endif

                                  </tr>
                                  @endif
                                @endforeach
                              
                            </table>
                          </td>
                          @else
                          <td style="border:0;padding:0;margin:0;width: 120px"></td>
                          @endif
                          @endif
                          </tr>
                      @endforeach
                      @endif
                    </table>
                  </div>
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