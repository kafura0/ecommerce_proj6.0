@extends('layouts.frontLayout.front_design')
@section('content')
<?php use App\Product; ?>
<section id="cart_items">
		<div class="container">
			<div class="breadcrumbs">
				<ol class="breadcrumb">
				  <li><a href="#">Home</a></li>
				  <li class="active">Wish List</li>
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
							@foreach($userWishList as $wishlist)
								<tr>
									<td class="cart_product">
										<a href=""><img style="width:100px;" src="{{ asset('/images/backend_images/products/small/'.$wishlist->image) }}" alt=""></a>
									</td>
									<td class="cart_description">
										<h4><a href="">{{ $wishlist->product_name }}</a></h4>
										<p>Product Code: {{ $wishlist->product_code }}</p>
									</td>
									<td class="cart_price">
										<?php $product_price = Product::getProductPrice( $wishlist->product_id,  $wishlist->size); ?>
										<p>Kshs. {{ $product_price }}</p>
									</td>
									<td class="cart_quantity">
										{{ $wishlist->quantity }}
									</td>
									<td class="cart_total">
										<p class="cart_total_price">kshs. {{ $product_price*$wishlist->quantity }}</p>
                                    </td>
                                    <form name="addToCartForm" id="addToCartForm" action="{{ url('add_cart') }}" method="POST"> {{ csrf_field() }}
                                            <input type="hidden" name="product_id" value="{{ $wishlist->product_id }}">
                                            <input type="hidden" name="product_name" value="{{ $wishlist->product_name }}">
                                            <input type="hidden" name="product_code" value="{{ $wishlist->product_code }}">
                                            <input type="hidden" name="product_color" value="{{ $wishlist->product_color }}">
                                            <input type="hidden" name="size" value="{{ $wishlist->id }}-{{ $wishlist->size }}">
                                            <input type="hidden" name="quantity" value="{{ $wishlist->quantity }}">
                                            <input type="hidden" name="price" id="price" value="{{ $wishlist->price }}">                
									    <td class="cart_delete">
                                            <button type="submit" class="btn btn-fefault cart" id="cartButton" name="cartButton" value="Add to Cart">
                                                <i class="fa fa-shopping-cart"></i>
                                                Add to cart
                                            </button> &nbsp;
										    <a class="cart_quantity_delete" href="{{ url('/wish_list/delete_product/'.$wishlist->id) }}"><i class="fa fa-times"></i></a>
                                        </td>
                                    </form>
								</tr>
							@endforeach
							
                       
					</tbody>
				</table>
			</div>
		</div>
</section> <!--/#cart_items-->



@endsection