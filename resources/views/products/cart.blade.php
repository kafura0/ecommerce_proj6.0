@extends('layouts.frontLayout.front_design')
@section('content')
<?php use App\Product; ?>
<section id="cart_items">
		<div class="container">
			<div class="breadcrumbs">
				<ol class="breadcrumb">
				  <li><a href="#">Home</a></li>
				  <li class="active">Shopping Cart</li>
				</ol>
			</div>
			<div class="table-responsive cart_info">
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
				<table class="table table-condensed">
					<thead>
						<tr class="cart_menu">
							<td class="image">Item</td>
							<td class="description"></td>
							<td class="price">Price</td>
							<td class="quantity">Quantity</td>
							<td class="total">Total</td>
							<td></td>
						</tr>
					</thead>
					<tbody>
							<?php $total_amount = 0; ?>
							@foreach($userCart as $cart)
								<tr>
									<td class="cart_product">
										<a href=""><img style="width:100px;" src="{{ asset('/images/backend_images/products/small/'.$cart->image) }}" alt=""></a>
									</td>
									<td class="cart_description">
										<h4><a href="">{{ $cart->product_name }}</a></h4>
										<p>Product Code: {{ $cart->product_code }}</p>
									</td>
									<td class="cart_price">
										<?php $product_price = Product::getProductPrice( $cart->product_id,  $cart->size); ?>
										<p>Kshs. {{ $product_price }}</p>
									</td>
									<td class="cart_quantity">
										<div class="cart_quantity_button">
											<a class="cart_quantity_up" href="{{ url('/cart/update_quantity/'.$cart->id.'/1') }}"> + </a>
											<input class="cart_quantity_input" type="text" name="quantity" value="{{ $cart->quantity }}" autocomplete="off" size="2">
											@if($cart->quantity>1)
												<a class="cart_quantity_down" href="{{ url('/cart/update_quantity/'.$cart->id.'/-1') }}"> - </a>
											@endif
										</div>
									</td>
									<td class="cart_total">
										<p class="cart_total_price">kshs. {{ $product_price*$cart->quantity }}</p>
									</td>
									<td class="cart_delete">
										<a class="cart_quantity_delete" href="{{ url('/cart/delete_product/'.$cart->id) }}"><i class="fa fa-times"></i></a>
									</td>
								</tr>
								<?php $total_amount = $total_amount + ($product_price*$cart->quantity); ?>
							@endforeach
							
                       
					</tbody>
				</table>
			</div>
		</div>
</section> <!--/#cart_items-->

<section id="do_action">
		<div class="container">
			<div class="heading">
				<h3>What would you like to do next?</h3>
				<p>Choose if you have a coupon code </p>
			</div>
			<div class="row">
				<div class="col-sm-6">
					<div class="chose_area">
						<ul class="user_option">
							<li>
								<form action="{{ url('cart/apply_coupon') }}" method="post">{{ csrf_field() }}
									<label>Coupon Code</label>								
									<input type="text" name="coupon_code">
									<input type="submit" value="Apply" class="btn btn-default">
								</form>
							</li> 
							{{-- <li>
								<input type="checkbox">
								<label>Use Gift Voucher</label>
							</li>
							<li>
								<input type="checkbox">
								<label>Estimate Shipping & Taxes</label>
							</li>
						</ul>
						{{-- <ul class="user_info">
							<li class="single_field">
								<label>Country:</label>
								<select>
									<option>United States</option>
									<option>Bangladesh</option>
									<option>UK</option>
									<option>India</option>
									<option>Pakistan</option>
									<option>Ucrane</option>
									<option>Canada</option>
									<option>Dubai</option>
								</select>
								
							</li>
							<li class="single_field">
								<label>Region / State:</label>
								<select>
									<option>Select</option>
									<option>Dhaka</option>
									<option>London</option>
									<option>Dillih</option>
									<option>Lahore</option>
									<option>Alaska</option>
									<option>Canada</option>
									<option>Dubai</option>
								</select>
							
							</li>
							<li class="single_field zip-field">
								<label>Zip Code:</label>
								<input type="text">
							</li>
						</ul> --}}
						{{-- <a class="btn btn-default update" href="">Get Quotes</a>
						<a class="btn btn-default check_out" href="">Continue</a> --}}
					</div>
				</div>
				<div class="col-sm-6">
					<div class="total_area">
						<ul>
							@if(!empty(Session::get('CouponAmount')))
								<li>Sub Total <span>Kshs. <?php echo $total_amount; ?></span></li>
								<li>Coupon Discount <span>Kshs. <?php echo Session::get('CouponAmount'); ?></span></li>
								<li>Grand Total <span>Kshs. <?php echo $total_amount - Session::get('CouponAmount'); ?></span></li>
							@else
								<li>Grand Total <span>Kshs. <?php echo $total_amount; ?></span></li>
							@endif
						</ul>
							<a class="btn btn-default update" href="">Update</a>
							<a class="btn btn-default check_out" href="{{ url('/checkout') }}">Check Out</a>
					</div>
				</div>
			</div>
		</div>
</section><!--/#do_action-->


@endsection