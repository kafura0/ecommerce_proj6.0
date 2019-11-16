@extends('layouts.adminLayout.admin_design')
@section('content')

<div id="content">
    <div id="content-header">
        <div id="breadcrumb"> <a href="index.html" title="Go to Home" class="tip-bottom"><i class="icon-home"></i> Home</a> <a href="#">Coupons</a> <a href="#" class="current">View Coupons</a> </div>
        <h1>Coupons</h1>
        <!----- display error or success message of category update----->
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
        <!----------end of display error or success message of category update--------------->
    </div>
    <div class="container-fluid">
      <hr>
      <div class="row-fluid">
        <div class="span12">
          <div class="widget-box">
            <div class="widget-title"> <span class="icon"><i class="icon-th"></i></span>
              <h5>Coupons</h5>
            </div>
            <div class="widget-content nopadding">
              <table class="table table-bordered data-table">
                <thead>
                  <tr>
                    <th> Coupon ID </th>
                    <th> Coupon Code </th>
                    <th> Amount </th>                    
                    <th> Amount Type </th>
                    <th> Expiry Date </th>
                    <th> Created Date </th>
                    <th> Status </th>
                    <th> Actions </th>
              
                  </tr>
                </thead>
                <tbody>
                    @foreach($coupons as $coupon)
                    <tr class="gradeX">
                        <td> {{ $coupon->id }} </td>
                        <td> {{ $coupon->coupon_code }} </td>
                        <td class="center"> 
                          {{ $coupon->amount }} 
                          @if($coupon->amount_type == "Percentage") % @else Kshs. @endif
                             
                        </td>
                        <td> {{ $coupon->amount_type }} </td>
                        <td> {{ $coupon->expiry_date }} </td>
                        <td> {{ $coupon->created_at }} </td>
                        <td > 
                          @if($coupon->status==1) Active @else Inactive @endif
                        </td>
                           
                        <td class="center">
                          <a href="{{ url('/admin/edit_coupon/'.$coupon->id) }}" class="btn btn-primary btn-mini" title="Edit Product">Edit</a> 
                          <a rel="{{ $coupon->id }}" rel1="delete_coupon" href="javascript:" class="btn btn-danger btn-mini deleteRecord">Delete</a>
                        </td>
                      </tr>
                   

                    @endforeach
                  
                </tbody>
              </table> 
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="widget-content">  
    <div id="myModal" class="modal hide">
      <div class="modal-header">
        <button data-dismiss="modal" class="close" type="button">×</button>
        <h3>Pop up Header</h3>
      </div>
      <div class="modal-body">
        <p>Here is the text coming you can put also image if you want…</p>
      </div>
    </div>
    
  </div>

@endsection