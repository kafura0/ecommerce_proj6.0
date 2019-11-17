<?php

//Route::get('/', function () {return view('welcome');});

use App\Http\Controllers\ProductsController;

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

//----------front_end routes--------------------------------------
//index Page
Route::get('/','IndexController@index');

//category listing page
Route::get('/products/{url}','ProductsController@products');

Route::match(['get', 'post'],'/products-filter', 'ProductsController@filter');

//product detail page
Route::get('product/{id}','ProductsController@product');

// Search Products
Route::post('/search_products','ProductsController@searchProducts');

//get product ATTRIBUTE: PRICE for size dropdown varied pricing
Route::get('/get_product_price', 'ProductsController@getProductPrice');

// Add to Cart Route
Route::match(['get', 'post'], '/add_cart', 'ProductsController@addToCart');

//update cart
Route::get('/cart/update_quantity/{id}/{quantity}','ProductsController@updateCartQuantity');

//cart page
Route::match(['get', 'post'], '/cart', 'ProductsController@cart');

//delete cart product
Route::get('/cart/delete_product/{id}','ProductsController@deleteCartProduct');

//apply coupon route 
Route::post('/cart/apply_coupon', 'ProductsController@applyCoupon');
//-----------------------------------------------------------------
//USER LOGIN and LOGOUT ROUTES
//user login/register page
Route::get('/login_register','UsersController@userLoginRegister');

//forgot password
Route::match(['get','post'],'forgot_password','UsersController@forgotPassword');

//users register form submit
Route::post('/user_register','UsersController@register');

// Confirm Account
Route::get('confirm/{code}','UsersController@confirmAccount');

//check email exists
Route::match(['GET','POST'],'/check_email','UsersController@checkEmail');

//user login form
Route::post('user_login','UsersController@login');

//user logout
Route::get('/user_logout','UsersController@logout'); 

// Check Pincode
Route::post('/check-pincode','ProductsController@checkPincode');

//check subcriber email route
Route::post('/check-subscriber-email', 'NewsletterController@checkSubscriber');
// Add Subscriber Email
Route::post('/add-subscriber-email','NewsletterController@addSubscriber');


Route::group(['middleware'=>['frontLogin']],function(){
	// Users Account Page
	Route::match(['get','post'],'account','UsersController@account');
	// Check User Current Password
	Route::get('/check_user_pwd','UsersController@chkUserPassword');
	// Update User Password
	Route::post('/update_user_pwd','UsersController@updatePassword');
	// Checkout Page
       Route::match(['get','post'],'checkout','ProductsController@checkout');
       
       // Wish List Page
       Route::match(['get', 'post'],'/wish_list','ProductsController@wishList');
       // Delete Product from Wish List Route
       Route::get('/wish_list/delete_product/{id}','ProductsController@deleteWishlistProduct');

	// Order Review Page
	Route::match(['get','post'],'/order_review','ProductsController@orderReview');
	// Place Order
       Route::match(['get','post'],'/place_order','ProductsController@placeOrder');     
       // //send email of order details
       // // Confirm Account
       // Route::get('order/{id}','UsersController@placeOrder');

	// Thanks Page
	Route::get('/thanks','ProductsController@thanks');
	// Paypal Page
	Route::get('/paypal','ProductsController@paypal');
	// Users Orders Page
	Route::get('/orders','ProductsController@userOrders');
	// User Ordered Products Page
	Route::get('/orders/{id}','ProductsController@userOrderDetails');
	// Paypal Thanks Page
	Route::get('/paypal/thanks','ProductsController@thanksPaypal');
	// Paypal Cancel Page
	Route::get('/paypal/cancel','ProductsController@cancelPaypal');
});

//-------Admin routes---------------------
Route::match(['get', 'post'], '/admin', 'AdminController@login');

Route::group(['middleware' => ['adminLogin']], function () { 
       //admin dashboard view
       Route::get('/admin/dashboard', 'AdminController@dashboard'); 
       //SETTINGS VIEW: update password settings view
       Route::get('/admin/settings', 'AdminController@settings');
       //to check if the Current Password entry is correct
       Route::get('/admin/check-pwd','AdminController@chkPassword');
       //update the password route.
       Route::match(['get', 'post'],'/admin/update-pwd','AdminController@updatePassword');
       
       //categories Routes (Admin)
       Route::match(['get', 'post'], '/admin/add_category', 'CategoryController@addCategory');
       Route::match(['get','post'],'/admin/edit_category/{id}','CategoryController@editCategory');
       Route::match(['get','post'],'/admin/delete_category/{id}','CategoryController@deleteCategory');
       Route::get('/admin/view_categories','CategoryController@viewCategories');

       //product Routes(Admin)
       Route::match(['get', 'post'], '/admin/add_product', 'ProductsController@addProduct');
       Route::match(['get','post'],'/admin/edit_product/{id}','ProductsController@editProduct');
       Route::get('/admin/view_products','ProductsController@viewProducts');
       Route::get('/admin/delete_product/{id}','ProductsController@deleteProduct');
       Route::get('/admin/delete_product_image/{id}','ProductsController@deleteProductImage');
       // Export Products
       Route::get('/admin/export_products','ProductsController@exportProducts');


       //attributes routes
       Route::match(['get','post'],'admin/add_attributes/{id}','ProductsController@addAttributes');
       Route::match(['get','post'],'admin/edit_attributes/{id}','ProductsController@editAttributes');
       Route::get('/admin/delete_attribute/{id}','ProductsController@deleteAttribute');
       Route::match(['get','post'],'admin/add_images/{id}','ProductsController@addImages');
       Route::get('/admin/delete_alt_image/{id}','ProductsController@deleteAltImage');

       //coupons route
       Route::match(['get','post'],'/admin/add_coupon','CouponsController@addCoupon');
       Route::match(['get','post'],'/admin/edit_coupon/{id}','CouponsController@editCoupon');
       Route::get('admin/view_coupons','CouponsController@viewCoupons');
       Route::get('/admin/delete_coupon/{id}','CouponsController@deleteCoupon');

       //admin banner routes
       Route::match(['get','post'],'/admin/add_banner','BannersController@addBanner');
	Route::match(['get','post'],'/admin/edit_banner/{id}','BannersController@editBanner');
	Route::get('admin/view_banners','BannersController@viewBanners');
       Route::get('/admin/delete_banner/{id}','BannersController@deleteBanner'); 
       
       //admin view orders
       Route::get('/admin/view_orders','ProductsController@viewOrders');
       Route::get('/admin/view_order_details/{id}','ProductsController@viewOrderDetails');
       //order invoice
       Route::get('/admin/view_order_invoice/{id}','ProductsController@viewOrderInvoice');
       // Print PDF Invoice
       Route::get('/admin/print_pdf_invoice/{id}','ProductsController@printPDFInvoice');

       Route::post('/admin/update_order_status','ProductsController@updateOrderStatus'); 

       //admin manage users
       // Admin Users Route
       Route::get('/admin/view_users','UsersController@viewUsers');
       // Export Users
       Route::get('/admin/export_users','UsersController@exportUsers');


       //admin/ sub admin roles routes
       Route::get('/admin/view_admins','AdminController@viewAdmins');
       // Add Admins/Sub-Admins Route
       Route::match(['get','post'],'/admin/add_admin','AdminController@addAdmin');
        // Edit Admins/Sub-Admins Route
        Route::match(['get','post'],'/admin/edit_admin/{id}','AdminController@editAdmin');


       // Add CMS Route 
	Route::match(['get','post'],'/admin/add_cms_page','CmsController@addCmsPage');
	// Edit CMS Route
	Route::match(['get','post'],'/admin/edit_cms_page/{id}','CmsController@editCmsPage');
	// View CMS Pages Route
	Route::get('/admin/view_cms_pages','CmsController@viewCmsPages');
	// Delete CMS Route 
       Route::get('/admin/delete_cms_page/{id}','CmsController@deleteCmsPage');
       
       //user enquiries routes
       // Get Enquiries
	Route::get('/admin/get_enquiries','CmsController@getEnquiries');
	// View Enquiries
       Route::get('/admin/view_enquiries','CmsController@viewEnquiries');
       
       // View Shipping Charges
	Route::get('/admin/view_shipping','ShippingController@viewShipping');
	// Update Shipping Charges
       Route::match(['get','post'],'/admin/edit_shipping/{id}','ShippingController@editShipping');
       
       // View Newsletter Subscribers
       Route::get('/admin/view_newsletter_subscribers','NewsletterController@viewNewsletterSubscribers');
       // Update Newsletter Status
       Route::get('/admin/update_newsletter_status/{id}/{status}','NewsletterController@updateNewsletterStatus');
       //delete newsletter subscriber
       Route::get('/admin/delete_newsletter_email/{id}','NewsletterController@deleteNewsletterEmail');
       // Export Newsletter Emails
       Route::get('/admin/export_newsletter_emails','NewsletterController@exportNewsletterEmails');



      
});

Route::get('/logout','AdminController@logout');

// Display Contact Page
Route::match(['get','post'],'/page/contact','CmsController@contact');

// Display Post Page (for Vue.js)
Route::match(['get','post'],'/page/post','CmsController@addPost');

// Display CMS Page
Route::match(['get','post'],'/page/{url}','CmsController@cmsPage');
