@extends('layouts.adminLayout.admin_design')
@section('content')
<?php
$current_month = date('M'); 
$last_month = date('M', strtotime("-1 month")); 
$one_month_ago = date('M', strtotime("-2 month"));
$two_month_ago = date('M', strtotime("-3 month"));
$three_month_ago = date('M', strtotime("-4 month"));
$four_month_ago = date('M', strtotime("-5 month"));

?>
<script>
    window.onload = function () {
    
    var chart = new CanvasJS.Chart("chartContainer", {
        animationEnabled: true,
        theme: "light2", // "light1", "light2", "dark1", "dark2"
        title:{
            text: "Orders reporting"
        },
        axisY: {
            title: "Number of Orders"
        },
        data: [{        
            type: "column",  
            showInLegend: true, 
            legendMarkerColor: "grey",
            legendText: "Last 6 months",
            dataPoints: [
                { y: <?php echo $four_month_ago_orders ?>,  label: "<?php echo $four_month_ago; ?>" },
                { y: <?php echo $three_month_ago_orders ?>,  label: "<?php echo $three_month_ago; ?>" },
                { y: <?php echo $two_month_ago_orders ?>,  label: "<?php echo $two_month_ago; ?>" },
                { y: <?php echo $one_month_ago_orders ?>,  label: "<?php echo $one_month_ago; ?>" },      
                { y: <?php echo $last_month_orders; ?> ,  label: "<?php echo $last_month; ?>" },
                { y: <?php echo $current_month_orders; ?> , label: "<?php echo $current_month; ?>" },
            ]
        }]
    });
    chart.render();
    }
</script>

<div id="content">
    <div id="content-header">
        <div id="breadcrumb"> <a href="index.html" title="Go to Home" class="tip-bottom"><i class="icon-home"></i> Home</a> <a href="#">Orders</a> <a href="#" class="current">View Orders Charts</a> </div>
        <h1>Orders Charts</h1>
        @if(Session::has('flash_message_error'))
            <div class="alert alert-error alert-block">
                <button type="button" class="close" data-dismiss="alert">×</button> 
                <strong>{!! session('flash_message_error') !!}</strong>
            </div>
        @endif   
        @if(Session::has('flash_message_success'))
            <div class="alert alert-success alert-block">
                <button type="button" class="close" data-dismiss="alert">×</button> 
                <strong>{!! session('flash_message_success') !!}</strong>
            </div>
        @endif
    </div>
  
  <div class="container-fluid">
    <hr>
    <div class="row-fluid">
      <div class="span12">
        <div class="widget-box">
          <div class="widget-title"> <span class="icon"><i class="icon-th"></i></span>
            <h5>Orders Reporting</h5>
          </div>
          <div class="widget-content nopadding">
                <div id="chartContainer" style="height: 370px; width: 100%;"></div> 
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<script src="https://canvasjs.com/assets/script/canvasjs.min.js"></script>
@endsection