<!DOCTYPE html>
<html>
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <style type="text/css">
  table#t01 {
    /* width:100%; */
    /* border:solid 1px #000; */
    /* border-collapse: collapse; */
  } 
  th,td {
    /* border: 1px solid black; */
    /* padding: 5px; */
    /* border-right:1; */
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
    {{-- <div> --}}
        @if($profit_and_loss!=''  || $expense !='' || $income!='')
        <table id="t01" width="100%" style="" class="">
            <thead>
                <tr><th  colspan="4" style="text-align: center;"><h3>Profit & Loss Report</h3></th></tr>
                <tr>
                    <!-- <th class="text-center">No.</th> -->
                    {{-- <th class="text-center"></th> --}}
                    <th  colspan="4" class="text-right" style="padding-left:500px">Amount</th>
                </tr>
            </thead>
            <tbody>
                @if($profit_and_loss!='')
                @foreach($profit_and_loss as $k=>$pl)
                   @if($k=='Revenue')
                   <tr>
                    <td colspan="4"> 
                        <!--<h3 style="" ><u>{{$k}}</u>(Account Head)</h3>-->
                        <h3 style="" ><u>{{$k}}</u></h3>
                       </td>
                   </tr>
                     @foreach($pl as $r)
                     <tr>
                        <td class="text-right" colspan="3" style="padding-left:100px">
                            <!--{{$r->name}}(Sub-Account)-->
                            {{$r->name}}
                          </td>
                          <td class="text-center"  style="padding-left:35px">
                            {{number_format($r->amount)}}
                          </td>
                     </tr>
                     @endforeach
                   @endif
                @endforeach
                @if($k=='Cost of Revenue')
                  <tr>
                      <td colspan="4">
                        <!--<h3 style="" ><u>{{$k}}</u>(Account Head)</h3>-->
                        <h3 style="" ><u>{{$k}}</u></h3>
                      </td>
                  </tr>
                  @foreach($pl as $r)
                    <tr>
                        <td class="text-right" colspan="3" style="padding-left:100px">
                            <!--{{$r->name}}(Sub-Account)-->
                            {{$r->name}}
                        </td>
                        <td class="text-center"  style="padding-left:35px">
                            {{number_format($r->amount)}}
                        </td>
                    </tr>
                    @endforeach
                @endif
                <tr>
                    <td colspan="3" style="padding-left:400px;padding-top:30px">
                        @if($gross_profit>=0)
                        <strong><h3>Gross Profit </h3></strong> 
                        @elseif($gross_profit<0)
                        <strong><h3>Loss Profit </h3></strong> 
                        @endif
                    </td>

                    <td style="">
                        <hr style="width:10px;padding-right:80px" >
                        <hr style="width:10px;padding-right:80px" >
                        <span style="padding-left:35px;">{{number_format($gross_profit)}}</span>
                    </td>
                </tr>
                @endif
                @if($income!= "")
                <tr>
                    <td colspan="4">
                        <!--<h3 style=""><u>Income</u>(Financial Type-2)</h3>-->
                        <h3 style=""><u>Income</u></h3>
                    </td>
                </tr>
                @foreach($income as $k=>$in)
                <tr class="">
                    <td colspan="4">
                        <!--<h5  style="margin-left:100px">{{$in->account_head_name}}(Account-Head)</h5>-->
                        <h5  style="margin-left:100px">{{$in->account_head_name}}</h5>
                    </td>
                </tr>
                  @foreach($in->income as $index=>$i)
                  <tr>
                    <td class="text-right" style="padding-left:200px" colspan="3">
                        <!--{{$i->sub_account_name}}(Sub-Account)-->
                        {{$i->sub_account_name}}
                    </td>
                    <td class="text-right" style="padding-left:35px" >
                        {{number_format($i->amount)}}
                    </td>
                 </tr>
                  @endforeach
                  <tr>
                      <td colspan="3" style="padding-left:360px;padding-top:30px">
                          <strong>{{$in->account_head_name}} Total</strong>
                      </td>
                      <td style="">
                        <hr style="width:10px;padding-right:80px" >
                        <hr style="width:10px;padding-right:80px" >
                          <span style="padding-left:35px;">  {{number_format($in->total)}}
                        </span>

                      </td>
                  </tr>
                @endforeach
                @endif

                @if($expense!= "")
                <tr>
                    <td colspan="4">
                        <!--<h3 style=""><u>Expense</u>(Financial Type-2)</h3>-->
                        <h3 style=""><u>Expense</u></h3>
                    </td>
                </tr>
                @foreach($expense as $k=>$exp)
                <tr class="">
                    <td colspan="4">
                        <!--<h5  style="margin-left:100px">{{$exp->account_head_name}}(Account-Head)</h5>-->
                        <h5  style="margin-left:100px">{{$exp->account_head_name}}</h5>
                    </td>
                </tr>
                  @foreach($exp->expense as $index=>$e)
                  <tr>
                    <td class="text-right" style="padding-left:200px" colspan="3">
                        <!--{{$e->sub_account_name}}(Sub-Account)-->
                        {{$e->sub_account_name}}
                    </td>
                    <td class="text-right" style="padding-left:35px" >
                        {{number_format($e->amount)}}
                    </td>
                 </tr>
                  @endforeach
                  <tr>
                    <td colspan="3" style="padding-left:360px;padding-top:30px">
                        <strong>{{$exp->account_head_name}} Total</strong>
                    </td>
                    <td>
                        <hr style="width:10px;padding-right:80px" >
                        <hr style="width:10px;padding-right:80px" >
                        <span style="padding-left:35px">
                        {{number_format($exp->total)}}
                        </span>
                    </td>
                </tr>
                @endforeach
                @endif
                @if($net_profit!='')
                <tr>
                    <td colspan="3" style="padding-left:400px;padding-top:30px">
                        @if($net_profit>=0)
                        <strong><h3>Net Profit </h3></strong> 
                        @elseif($net_profit<0)
                        <strong><h3>Net Loss </h3></strong> 
                        @endif
                    </td>
                    <td >
                        <hr style="width:10px;padding-right:80px" >
                        <hr style="width:10px;padding-right:80px" >
                        <span style="padding-left:35px;">
                        {{number_format($net_profit)}}
                        </span>
                    </td>
                </tr>
                @endif

                <tr>
                  <td class="text-right" style="padding-left:200px" colspan="3">
                      <!--{{$e->sub_account_name}}(Sub-Account)-->
                      Currency Gain
                  </td>
                  <td class="text-right" style="padding-left:35px" >
                      {{!empty($c_gain->amount) ? number_format($c_gain->amount) : 0}}
                  </td>
               </tr>

               <tr>
                  <td class="text-right" style="padding-left:200px" colspan="3">
                      <!--{{$e->sub_account_name}}(Sub-Account)-->
                      Currency Loss
                  </td>
                  <td class="text-right" style="padding-left:35px" >
                      {{!empty($c_loss->amount) ? number_format($c_loss->amount) : 0}}
                  </td>
               </tr>
               <?php
                  $gain = !empty($c_gain->amount) ? $c_gain->amount : 0;
                  $loss = !empty($c_loss->amount) ? $c_loss->amount : 0;
                  $netProfit = $net_profit!='' ? $net_profit : 0;
                  $gain_loss_total = $gain + $loss;
                  $gain_loss_profit = ($netProfit + $gain) - $loss;
               ?>

               <tr>
                  <td colspan="3" style="padding-left:400px;padding-top:30px">
                      <strong><h5>Currency Gain/Loss Total</h5></strong>
                  </td>
                  <td >
                      <hr style="width:10px;padding-right:80px" >
                      <hr style="width:10px;padding-right:80px" >
                      <span style="padding-left:35px;">
                      {{number_format($gain_loss_total)}}
                      </span>
                  </td>
              </tr>

              <tr>
                  <td colspan="3" style="padding-left:400px;padding-top:30px">
                      <strong><h3>After Gain/Loss Profit</h3></strong>
                  </td>
                  <td >
                      <hr style="width:10px;padding-right:80px" >
                      <hr style="width:10px;padding-right:80px" >
                      <span style="padding-left:35px;">
                      {{number_format($gain_loss_profit)}}
                      </span>
                  </td>
              </tr>
            </tbody>
        </table>
        @endif
    {{-- </div> --}}
</body>
</html>