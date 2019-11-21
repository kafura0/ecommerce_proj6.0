@extends('layouts.adminLayout.admin_design')
@section('content')
<?php
$current_month = date('M'); 
$last_month = date('M', strtotime("-1 month")); 
$one_month_ago = date('M', strtotime("-2 month"));
$two_month_ago = date('M', strtotime("-3 month"));
$three_month_ago = date('M', strtotime("-4 month"));
$four_month_ago = date('M', strtotime("-5 month"));
 
$dataPoints = array(
  array("y" => $four_month_ago_users, "label" => $four_month_ago),
  array("y" => $three_month_ago_users, "label" => $three_month_ago),
  array("y" => $two_month_ago_users, "label" => $two_month_ago),
  array("y" => $one_month_ago_users, "label" => $one_month_ago),
  array("y" => $last_month_users, "label" => $last_month),
  array("y" => $current_month_users, "label" => $current_month),
);
 
?>
<script>
window.onload = function () {
         
var chart = new CanvasJS.Chart("chartContainer", {
    title: {
        text: "Users Reporting"
    },
    axisY: {
        title: "Number of Users"
    },
    data: [{
        type: "line",
        dataPoints: <?php echo json_encode($dataPoints, JSON_NUMERIC_CHECK); ?>
    }]
});
chart.render();
}        
</script>

<div id="content">
    <div id="content-header">
        <div id="breadcrumb"> <a href="index.html" title="Go to Home" class="tip-bottom"><i class="icon-home"></i> Home</a> <a href="#">Users</a> <a href="#" class="current">View Users</a> </div>
        <h1>Users</h1>
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
<div style="margin-left: 20px;">
    <a href="{{ url('/admin/export_users') }}" class="btn btn-primary"> Export</a>
</div>   
  <div class="container-fluid">
    <hr>
    <div class="row-fluid">
      <div class="span12">
        <div class="widget-box">
          <div class="widget-title"> <span class="icon"><i class="icon-th"></i></span>
            <h5>Users Reporting</h5>
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