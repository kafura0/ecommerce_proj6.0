<?php $url = url()->current(); ?>
<!--sidebar-menu-->
<div id="sidebar"><a href="#" class="visible-phone"><i class="icon icon-home"></i> Dashboard</a>
  <ul>
    <li <?php if (preg_match("/dashboard/i", $url)){ ?> class="active" <?php } ?>><a href="{{ url('/admin/dashboard') }}"><i class="icon icon-home"></i> <span>Dashboard</span></a> </li>
    
    @if(Session::get('adminDetails')['categories_full_access']==1)
    <li class="submenu"> <a href="#"><i class="icon icon-th-list"></i> <span>Categories</span> <span class="label label-important">2</span></a>
      <ul <?php if (preg_match("/categor/i", $url)){ ?> style="display: block;" <?php } ?>>
        <li <?php if (preg_match("/add_category/i", $url)){ ?> class="active" <?php } ?>><a href="{{ url('/admin/add_category')}}">Add Category</a></li>
        <li <?php if (preg_match("/view_categories/i", $url)){ ?> class="active" <?php } ?>><a href="{{ url('/admin/view_categories')}}">View Categories</a></li>
      </ul>
    </li>
    @else
    <li class="submenu"> <a href="#"><i class="icon icon-th-list"></i> <span>Categories</span> <span class="label label-important">1</span></a>
      <ul <?php if (preg_match("/categor/i", $url)){ ?> style="display: block;" <?php } ?>>
        @if(Session::get('adminDetails')['categories_edit_access']==1)
        <li <?php if (preg_match("/add_category/i", $url)){ ?> class="active" <?php } ?>><a href="{{ url('/admin/add_category')}}">Add Category</a></li>
        @endif
        @if(Session::get('adminDetails')['categories_view_access']==1)
        <li <?php if (preg_match("/view_categories/i", $url)){ ?> class="active" <?php } ?>><a href="{{ url('/admin/view_categories')}}">View Categories</a></li>
        @endif
      </ul>
    </li>
    @endif

    @if(Session::get('adminDetails')['products_full_access']==1)
     <li class="submenu"> <a href="#"><i class="icon icon-th-list"></i> <span>Products</span> <span class="label label-important">2</span></a>
      <ul <?php if (preg_match("/product/i", $url)){ ?> style="display: block;" <?php } ?>>
        <li <?php if (preg_match("/add_product/i", $url)){ ?> class="active" <?php } ?>><a href="{{ url('/admin/add_product')}}">Add Product</a></li>
        <li <?php if (preg_match("/view_products/i", $url)){ ?> class="active" <?php } ?>><a href="{{ url('/admin/view_products')}}">View Products</a></li>
      </ul>
    </li>
    @else
    <li class="submenu"> <a href="#"><i class="icon icon-th-list"></i> <span>Products</span> <span class="label label-important">1</span></a>
      <ul <?php if (preg_match("/product/i", $url)){ ?> style="display: block;" <?php } ?>>
        @if(Session::get('adminDetails')['products_edit_access']==1)
        <li <?php if (preg_match("/add_product/i", $url)){ ?> class="active" <?php } ?>><a href="{{ url('/admin/add_product')}}">Add Product</a></li>
        @endif
        @if(Session::get('adminDetails')['products_view_access']==1)
        <li <?php if (preg_match("/view_products/i", $url)){ ?> class="active" <?php } ?>><a href="{{ url('/admin/view_products')}}">View Products</a></li>
        @endif
      </ul>
    </li>
    @endif 
    
    @if(Session::get('adminDetails')['type']=="Admin")   
    <li class="submenu"> <a href="#"><i class="icon icon-th-list"></i> <span>Coupons</span> <span class="label label-important">2</span></a>
      <ul <?php if (preg_match("/coupon/i", $url)){ ?> style="display: block;" <?php } ?>>
        <li <?php if (preg_match("/add_coupon/i", $url)){ ?> class="active" <?php } ?>><a href="{{ url('/admin/add_coupon')}}">Add Coupon</a></li>
        <li <?php if (preg_match("/view_coupons/i", $url)){ ?> class="active" <?php } ?>><a href="{{ url('/admin/view_coupons')}}">View Coupons</a></li>
      </ul>
    </li>
    @endif
    @if(Session::get('adminDetails')['orders_access']==1)
    <li class="submenu"> <a href="#"><i class="icon icon-th-list"></i> <span>Orders</span> <span class="label label-important">1</span></a>
      <ul <?php if (preg_match("/orders/i", $url)){ ?> style="display: block;" <?php } ?>>
        <li <?php if (preg_match("/view_orders/i", $url)){ ?> class="active" <?php } ?>><a href="{{ url('/admin/view_orders')}}">View Orders</a></li>
      </ul>
    </li>
    @endif
    @if(Session::get('adminDetails')['type']=="Admin")
    <li class="submenu"> <a href="#"><i class="icon icon-th-list"></i> <span>Banners</span> <span class="label label-important">2</span></a>
      <ul <?php if (preg_match("/banner/i", $url)){ ?> style="display: block;" <?php } ?>>
        <li <?php if (preg_match("/add_banner/i", $url)){ ?> class="active" <?php } ?>><a href="{{ url('/admin/add_banner')}}">Add Banner</a></li>
        <li <?php if (preg_match("/view_banners/i", $url)){ ?> class="active" <?php } ?>><a href="{{ url('/admin/view_banners')}}">View Banners</a></li>
      </ul>
    </li>
    @endif
    @if(Session::get('adminDetails')['users_access']==1)
    <li class="submenu"> <a href="#"><i class="icon icon-th-list"></i> <span>Users</span> <span class="label label-important">1</span></a>
      <ul <?php if (preg_match("/users/i", $url)){ ?> style="display: block;" <?php } ?>>
        <li <?php if (preg_match("/view_users/i", $url)){ ?> class="active" <?php } ?>><a href="{{ url('/admin/view_users')}}">View Users</a></li>
      </ul>
    </li>
    @endif
    @if(Session::get('adminDetails')['type']=="Admin")
    <li class="submenu"> <a href="#"><i class="icon icon-th-list"></i> <span>Admins / SubAdmins</span> <span class="label label-important">2</span></a>
      <ul <?php if (preg_match("/admins/i", $url)){ ?> style="display: block;" <?php } ?>>
        <li <?php if (preg_match("/add_admin/i", $url)){ ?> class="active" <?php } ?>><a href="{{ url('/admin/add_admin')}}">Add Admin / SubAdmin</a></li>
        <li <?php if (preg_match("/view_admins/i", $url)){ ?> class="active" <?php } ?>><a href="{{ url('/admin/view_admins')}}">View Admins / SubAdmins</a></li>  
      </ul>
    </li>
    
    <li class="submenu"> <a href="#"><i class="icon icon-th-list"></i> <span>CMS Pages</span> <span class="label label-important">2</span></a>
      <ul <?php if (preg_match("/cms-page/i", $url)){ ?> style="display: block;" <?php } ?>>
        <li <?php if (preg_match("/add_cms_page/i", $url)){ ?> class="active" <?php } ?>><a href="{{ url('/admin/add_cms_page')}}">Add CMS Page</a></li>
        <li <?php if (preg_match("/view_cms_pages/i", $url)){ ?> class="active" <?php } ?>><a href="{{ url('/admin/view_cms_pages')}}">View CMS Pages</a></li>
      </ul>
    </li>
    <li class="submenu"> <a href="#"><i class="icon icon-th-list"></i> <span>Enquiries</span> <span class="label label_important">1</span></a>
      <ul <?php if (preg_match("/enquiries/i", $url)){ ?> style="display: block;" <?php } ?>>
        <li <?php if (preg_match("/view_enquiries/i", $url)){ ?> class="active" <?php } ?>><a href="{{ url('/admin/view_enquiries')}}">View Enquiries</a></li>
      </ul>
    </li>
    <li class="submenu"> <a href="#"><i class="icon icon-th-list"></i> <span>Currencies</span> <span class="label label-important">2</span></a>
      <ul <?php if (preg_match("/currencies/i", $url)){ ?> style="display: block;" <?php } ?>>
        <li <?php if (preg_match("/add_currency/i", $url)){ ?> class="active" <?php } ?>><a href="{{ url('/admin/add-currency')}}">Add Currency</a></li>
        <li <?php if (preg_match("/view_currencies/i", $url)){ ?> class="active" <?php } ?>><a href="{{ url('/admin/view-currencies')}}">View Currencies</a></li>
      </ul>
    </li>
    <li class="submenu"> <a href="#"><i class="icon icon-th-list"></i> <span>Shipping</span> <span class="label label-important">1</span></a>
      <ul <?php if (preg_match("/shipping/i", $url)){ ?> style="display: block;" <?php } ?>>
        <li <?php if (preg_match("/view_shipping/i", $url)){ ?> class="active" <?php } ?>><a href="{{ url('/admin/view_shipping')}}">Shipping Charges</a></li>
      </ul>
    </li>
    <li class="submenu"> <a href="#"><i class="icon icon-th-list"></i> <span>Newsletter Subscribers</span> <span class="label label-important">1</span></a>
      <ul <?php if (preg_match("/newletter_subscribers/i", $url)){ ?> style="display: block;" <?php } ?>>
        <li <?php if (preg_match("/newletter_subscribers/i", $url)){ ?> class="active" <?php } ?>><a href="{{ url('/admin/view_newsletter_subscribers')}}">Newsletters</a></li>
      </ul>
    </li>
    @endif
  </ul>
</div>
<!--sidebar-menu-->