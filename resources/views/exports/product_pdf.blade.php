<!DOCTYPE html>
<html>
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <style type="text/css">
    @font-face {
    font-family: 'ZawgyiOne2008';
      src: url({{ storage_path('fonts/ZawgyiOne2008.ttf') }}) format("truetype");
   }
 /* @page { margin:20px 60px 10px 25px; } */
 @font-face {
  font-family: "Pyidaungsu";
  src: local("Pyidaungsu"), url(/fonts/Pyidaungsu-2.1_Regular.woff) format("woff"), url(/fonts/Pyidaungsu-2.1_Regular.ttf) format("ttf");
}

@font-face {
  font-family: "Pyidaungsu";
  src: local("Pyidaungsu"), url(/fonts/Pyidaungsu-2.1_Bold.woff) format("woff"), url(/fonts/Pyidaungsu-2.1_Bold.ttf) format("ttf");
  font-weight: bold;
}
  .body {
    font-family: 'Pyidaungsu' !important;
  }
  .title {
    font-size: 35px;
    text-align:center;  
  }
  .mm-title {
    text-align:center;
    font-size: 25px;
  }
  .mm-txt{
    font-family: 'Pyidaungsu' !important;  
    font-size:16px;
  }
  @media print
     {
       
      
      @page {
          margin: 0 0 10px 0px;
      }
     }
  </style>
</head>
<body>
    <table border="1" style="border-collapse: collapse;">
        <thead>
        <tr><th colspan="8" style="text-align: center;"><h3>Product List(PDF) View</h3></th></tr>
        <tr>
            <th class="text-center" style="text-align: center;">No.</th>
            <th class="text-center" style="text-align: center;">Name</th>
            <th class="text-center" style="text-align: center;">Product Code</th>
            <th class="text-center" style="text-align: center;">Brand</th>
            <th class="text-center" style="text-align: center;">Category</th>
            <th class="text-center" style="text-align: center;">Warehouse UOM</th>
            <th class="text-center" style="text-align: center;">Photo View</th>
            <th class="text-center" style="text-align: center;">Status</th>
        </tr>
        </thead>
        <tbody>
            <?php
                $i = 1;
            ?>
            @foreach($data as $product)
              <tr>
                <td class="text-right">{{$i}}</td>
                <td class="mm-txt">{{$product->product_name}}</td>
                <td>{{$product->product_code}}</td>
                <td  class="mm-txt">{{$product->brand_name}}</td>
                <td class="mm-txt">{{$product->category_name}}</td>
                <td>{{$product->uom_name}}</td>
                <td>
                  @foreach($product->photos as $k=>$p)
                  <?php
                    echo "<div style='text-align:center'><img style='max-width:100px' src='".$p->photo."' /></div>";
                  ?>
                  @endforeach
                </td>
                <td>{{empty($product->is_active) ? 'Inactive' : 'Active'}}</td>
            </tr>
            @php
              $i++;
            @endphp
            @endforeach
        </tbody>
    </table>
</body>
</html>