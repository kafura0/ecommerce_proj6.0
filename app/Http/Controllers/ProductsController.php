<?php

namespace App\Http\Controllers;

use DB;
use Auth;


use Session;
use App\User;
use App\Order;
use App\Coupon;
use App\Country;
use App\Product;
use App\Category;
use Dompdf\Dompdf;
use App\OrdersProduct;
use App\ProductsImage;
use App\DeliveryAddress;
use App\ProductsAttribute;
use Illuminate\Http\Request;
use App\Exports\productsExport;
use Illuminate\Support\Facades\Mail;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Input;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Redirect;

class ProductsController extends Controller
{
    //
    public function addProduct(Request $request)
    {
        if(Session::get('adminDetails')['products_edit_access']==0)
        {
            return redirect('/admin/dashboard')->with('flash_message_error','You have no access to this module');
        }
        //save data to DB-------------------------
        if($request->isMethod('post'))
        {
            $data = $request->all();

            $product = new Product;
            //echo "<pre>"; print_r($data); die;
            $product->category_id = $data['category_id'];
            $product->product_name = $data['product_name'];
            $product->brand = $data['brand'];
            $product->product_code = $data['product_code'];
            $product->product_color = $data['product_color'];
            $product->price = $data['price'];
            // $product = json_decode(json_encode($product));
            // echo "<pre>"; print_r($product); die;

            if(!empty($data['weight']))
            {
                $product->weight = $data['weight'];
            }else{
                $product->weight = 0;
            }
            if(!empty($data['description']))
            {
                $product->description = $data['description'];
            }else{
                $product->description = '';
            }
            if(!empty($data['care']))
            {
                $product->care = $data['care'];
            }else{
                $product->description = '';
            }
            if(!empty($data['sleeve'])){
                $product->sleeve = $data['sleeve'];
            }else{
                $product->sleeve = ''; 
            }
            if(!empty($data['pattern'])){
                $product->pattern = $data['pattern'];
            }else{
                $product->pattern = ''; 
            }
            
            if(empty($data['status']))
            {
                $status = 0;
            }else{
                $status = 1;
            }
            if(empty($data['feature_item'])){
                $feature_item='0';
            }else{
                $feature_item='1';
            }
            
            //upload image
            if($request->hasFile('image'))
            {
                //echo $image_tmp = Input::file('image'); die;
                $image_tmp = $request->file('image');
                if($image_tmp->isValid())
                {
                    //echo "test";die;
                    //Resize Image code
                    $extension = $image_tmp->getClientOriginalExtension();
                    $filename = rand(111,99999999999).'.'.$extension;
                    $large_image_path = 'images/backend_images/products/large/'.$filename;
                    $medium_image_path = 'images/backend_images/products/medium/'.$filename;
                    $small_image_path = 'images/backend_images/products/small/'.$filename;
                    // //Resize images
                    Image::make($image_tmp)->save($large_image_path);
                    Image::make($image_tmp)->resize(600,600)->save($medium_image_path);
                    Image::make($image_tmp)->resize(300,300)->save($small_image_path);
                    //store image name in products table
                    $product->image = $filename;
                }
            }
            //$product->brand = $brand;
            $product->feature_item = $feature_item;
            $product->status = $status;

            $product->save();
            return redirect('/admin/view_products')->with('flash_message_success', 'Product added successfully!');
        }
        //-----------end of save data to DB------------------- 

        //display categories and sub categories drop downs
        $categories = Category::where(['parent_id'=>0])->get();
        $categories_dropdown = "<option selected diabled>Select</option>";
        foreach($categories as $cat)
        {
            $categories_dropdown .= "<option value='".$cat->id."'>".$cat->name."</option>";
            $sub_categories = Category::where(['parent_id' => $cat->id]) ->get();
            foreach ($sub_categories as $sub_cat)
            {
                $categories_dropdown .= "<option value = '". $sub_cat-> id."'> &nbsp;--&nbsp;".$sub_cat->name."</option>";
            }
        }
        ///------end of display categories and sub categories drop downs----------

        $sleeveArray = array('Full Sleeve','Half Sleeve','Short Sleeve','Sleeveless');

        $patternArray = array('Checked','Plain','Printed','Self','Solid', 'Animal Print');

        return view('admin.products.add_product')->with(compact('categories_dropdown', 'sleeveArray', 'patternArray')); 
    }
    
    public function editProduct(Request $request, $id = null)
    {
        if(Session::get('adminDetails')['products_edit_access']==0)
        {
            return redirect('/admin/dashboard')->with('flash_message_error','You have no access to this module');
        }
        if($request-> isMethod('post'))
        {
            $data = $request->all();
            //echo"<pre>"; print_r($data); die;

            if(empty($data['description'])){
                $data['description'] = "";
            }            
            if(empty($data['care'])){
                $data['care'] = "";
            }
            if(!empty($data['sleeve'])){
                $sleeve = $data['sleeve'];
            }else{
                $sleeve = ''; 
            }
            if(!empty($data['pattern'])){
                $pattern = $data['pattern'];
            }else{
                $pattern = ''; 
            }
            if(empty($data['status']))
            {
                $status = 0;
            }else{
                $status = 1;
            }
            if(empty($data['feature_item'])){
                $feature_item='0';
            }else{
                $feature_item='1';
            }
            if(empty($data['brand'])){
                $data['brand'] = "";
            }

            //upload image
            if($request->hasFile('image'))
            {
                //echo $image_tmp = Input::file('image'); die;
                $image_tmp = $request->file('image');
                if($image_tmp->isValid())
                {
                    //echo "test";die;
                    //Resize Image code
                    $extension = $image_tmp->getClientOriginalExtension();
                    $filename = rand(111,99999999999).'.'.$extension;
                    $large_image_path = 'images/backend_images/products/large/'.$filename;
                    $medium_image_path = 'images/backend_images/products/medium/'.$filename;
                    $small_image_path = 'images/backend_images/products/small/'.$filename;
                    // //Resize images
                    Image::make($image_tmp)->save($large_image_path);
                    Image::make($image_tmp)->resize(600,600)->save($medium_image_path);
                    Image::make($image_tmp)->resize(300,300)->save($small_image_path);
                    //store image name in products table
                    
                }
            }elseif(!empty($data['current_image'])){
                $filename = $data['current_image'];
            }else{
                $filename = "";
            }
            
            ////update Product details from product id
            Product::where(['id'=>$id])->update(
                    [
                        'feature_item'=>$feature_item,
                        'category_id'=>$data['category_id'],
                        'product_code'=>$data['product_code'],
                        'product_color'=>$data['product_color'],
                        'description'=>$data['description'],
                        'care'=>$data['care'],
                        'sleeve'=>$sleeve,
                        'pattern'=>$pattern,
                        'brand' => $data['brand'],                   
                        'price'=>$data['price'],
                        'weight'=>$data['weight'],
                        'image'=>$filename,
                        'status'=>$status,
                    ]);
                return redirect('/admin/view_products')->with('flash_message_success', 'Product updated successfully!');
        }
        //echo "test"; die;
        //get product details
        $productDetails = Product::where(['id'=>$id])->first();
        // $productDetails = json_decode(json_encode($productDetails));
        // echo "<pre>"; print_r($productDetails); die;

        //display categories and sub categories drop downs
        $categories = Category::where(['parent_id'=>0])->get();
        $categories_dropdown = "<option required selected diabled>Select</option>";
        foreach($categories as $cat)
        {
            if($cat-> id == $productDetails->category_id){
                echo $productDetails; die;
                $selected = "selected";
            }else{
                $selected = "";
            }
            $categories_dropdown .= "<option value='".$cat->id."'".$selected.">".$cat->name."</option>";
            $sub_categories = Category::where(['parent_id' => $cat->id]) ->get();
            foreach ($sub_categories as $sub_cat)
            {
                if($sub_cat-> id == $productDetails->category_id){
                    $selected = "selected";
                }else{
                    $selected = "";
                }
                $categories_dropdown .= "<option value = '". $sub_cat-> id."'".$selected."> &nbsp;--&nbsp;".$sub_cat->name."</option>";
            }
        }
        $sleeveArray = array('Full Sleeve','Half Sleeve','Short Sleeve','Sleeveless');

        $patternArray = array('Checked','Plain','Printed','Self','Solid', 'Animal Print');

        return view('admin.products.edit_product')->with(compact('productDetails', 'categories_dropdown', 'sleeveArray', 'patternArray'));
    }

    public function viewProducts(Request $request)
    {
        if(Session::get('adminDetails')['products_view_access']==0)
        {
            return redirect('/admin/dashboard')->with('flash_message_error','You have no access to this module');
        }
        $products = Product::get();
        $products =json_decode(json_encode($products));
        // echo "<pre>"; print_r($products); die;
        foreach($products as $key=>$val){
            $category_name = Category::where(['id'=>$val->category_id])->first();
            // $category_name =json_decode(json_encode($category_name));
            // echo"<pre>"; print_r($category_name); die;
            $products[$key]->category_name = $category_name->name;
            // echo"<pre>"; print_r($products); die;

        }
        // $products =json_decode(json_encode($products));
        // echo "<pre>"; print_r($products); die;
        return view('admin.products.view_products')->with(compact('products'));
    }

    public function deleteProduct( $id = null)
    {
        if(Session::get('adminDetails')['products_full_access']==0)
        {
            return redirect('/admin/dashboard')->with('flash_message_error','You have no access to this module');
        }
        if(!empty($id)){
            Product::where(['id'=>$id])->delete();
            return redirect()->back()->with('flash_message_success', 'Product deleted successfully');
        }
    }

    public function deleteProductImage( $id = null)
    {
        if(Session::get('adminDetails')['products_full_access']==0)
        {
            return redirect('/admin/dashboard')->with('flash_message_error','You have no access to this module');
        }
        //get product image name
        $productImage = Product::where(['id'=>$id])->first();
        //echo $productImage->image; die;
        //get product image path
        $small_image_path ='images/backend_images/products/small/';
        $medium_image_path ='images/backend_images/products/medium/';
        $large_image_path = 'images/backend_images/products/large/';

        //delete small image if it does not exist in folder
        if(file_exists($small_image_path.$productImage->image))
        {
            unlink($small_image_path.$productImage->image);
        }
        //delete medium image if it does not exist in folder
        if(file_exists($medium_image_path.$productImage->image))
        {
            unlink($medium_image_path.$productImage->image);
        }
        //delete large image if it does not exist in folder
        if(file_exists($large_image_path.$productImage->image))
        {
            unlink($large_image_path.$productImage->image);
        }

        // Delete Image from Products table
        Product::where(['id'=>$id])->update(['image'=>'']);

        return redirect()->back()->with('flash_message_success', 'Product image deleted successfully');
    }

    public function addAttributes(Request $request, $id = null)
    {
        if(Session::get('adminDetails')['products_edit_access']==0)
        {
            return redirect('/admin/dashboard')->with('flash_message_error','You have no access to this module');
        }
        //Fetch Product details from product id
        $productDetails = Product::with('attributes')->where(['id'=> $id])->first();
        //$productDetails = json_decode(json_encode($productDetails));
        //echo "<pre>"; print_r($productDetails); die;

        if($request -> isMethod('post'))
        {
            $data = $request->all();
            //echo "<pre>"; print_r($data); die;
            
            foreach($data['sku'] as $key => $val)
            {
                if(!empty($val)){
                    //SKU Check: Prevent duplicates
                    $attrCountSKU = ProductsAttribute::where('sku', $val)->count();
                    if($attrCountSKU>0)
                    {
                        return redirect('admin/add_attributes/'.$id)->with('flash_message_error', 
                        'SKU already exists! Please add another SKU');
                    }
                    //Size check: prevent duplicates
                    $attrCountSizes = ProductsAttribute::where([
                        'product_id'=>$id, 
                        'size'=>$data['size'][$key]])->count();
                    if($attrCountSizes>0)
                    {
                        return redirect('admin/add_attributes/'.$id)->with('flash_message_error', 
                        ''.$data['size'][$key].'Size already exists for this product! Please add another SKU');
                    }

                    $attr = new ProductsAttribute();
                    $attr->product_id = $id;
                    $attr->sku = $val;
                    $attr->size = $data['size'][$key];
                    $attr->price = $data['price'][$key];
                    $attr->stock = $data['stock'][$key];
                    $attr->save();
                }
            }
            return redirect('admin/add_attributes/' .$id)->with('flash_message_success', 'Product atrribute added successfully');

        }
        return view('admin.products.add_attributes')->with(compact('productDetails'));
    }

    public function addImages(Request $request, $id = null)
    {
        if(Session::get('adminDetails')['products_edit_access']==0)
        {
            return redirect('/admin/dashboard')->with('flash_message_error','You have no access to this module');
        }
        //Fetch Product details from product id
        $productDetails = Product::with('attributes')->where(['id'=> $id])->first();
        

        if($request -> isMethod('post'))
        {
            //add image
            $data = $request->all();
            //echo "<pre>"; print_r($data); die;

            if($request->hasFile('image')){
                $files = $request->file('image');
                //echo "<pre>"; print_r($files); die;

                foreach($files as $file)
                {
                    //upload images after resize
                    $image = new ProductsImage;
                    $extension = $file->getClientOriginalExtension();
                    $fileName = rand(111,99999999999999).'.'.$extension;
                    $large_image_path = 'images/backend_images/products/large/'.$fileName;
                    $medium_image_path = 'images/backend_images/products/medium/'.$fileName;
                    $small_image_path = 'images/backend_images/products/small/'.$fileName;
                    // //Resize images
                    Image::make($file)->save($large_image_path);
                    Image::make($file)->resize(600,600)->save($medium_image_path);
                    Image::make($file)->resize(300,300)->save($small_image_path);
                    //store image name in products table
                    $image->image = $fileName;
                    $image->product_id = $data['product_id'];
                    $image->save();
                }        
        
            }
            return redirect('admin/add_images/'.$id)->with('flash_message_success','Product Images added successfully');
        }

        $productsImages = ProductsImage::where(['product_id'=>$id])->get();
        //$productsImages = json_decode(json_encode($productsImages));
        // echo "<pre>"; print_r($productsImages); die;


        return view('admin.products.add_images')->with(compact('productDetails','productsImages'));
    }

    public function editAttributes(Request $request, $id = null)
    {
        if(Session::get('adminDetails')['products_edit_access']==0)
        {
            return redirect('/admin/dashboard')->with('flash_message_error','You have no access to this module');
        }
        if($request->isMethod('post')){
            $data = $request->all();
            //$data = json_decode(json_encode($data));
            //echo "<pre>"; print_r($data); die;
            foreach($data['idAttr'] as $key=> $attribute){

                ProductsAttribute::where(['id' => $data['idAttr'][$key]])->update(
                        [
                            'price' => $data['price'][$key],
                            'stock' => $data['stock'][$key],
                         ]);
                
            }
            return redirect('/admin/view_products')->with('flash_message_success', 'Product Attributes has been updated successfully');
        }
    }

    public function deleteAttribute( $id = null)
    {
        if(Session::get('adminDetails')['products_edit_access']==0)
        {
            return redirect('/admin/dashboard')->with('flash_message_error','You have no access to this module');
        }
        if(!empty($id)){
            ProductsAttribute::where(['id'=>$id])->delete();
            return redirect()->back()->with('flash_message_success', 'Product Attribute deleted successfully');
        }
    }

    public function deleteAltImage( $id = null)
    {
        if(Session::get('adminDetails')['products_edit_access']==0)
        {
            return redirect('/admin/dashboard')->with('flash_message_error','You have no access to this module');
        }
        //get product image name
        $productImage = ProductsImage::where(['id'=>$id])->first();
        //echo $productImage->image; die;
        //get product image path
        $small_image_path ='images/backend_images/products/small/';
        $medium_image_path ='images/backend_images/products/medium/';
        $large_image_path = 'images/backend_images/products/large/';

        //delete small image if it does not exist in folder
        if(file_exists($small_image_path.$productImage->image))
        {
            unlink($small_image_path.$productImage->image);
        }
        //delete medium image if it does not exist in folder
        if(file_exists($medium_image_path.$productImage->image))
        {
            unlink($medium_image_path.$productImage->image);
        }
        //delete large image if it does not exist in folder
        if(file_exists($large_image_path.$productImage->image))
        {
            unlink($large_image_path.$productImage->image);
        }

        // Delete Image from Products table
        ProductsImage::where(['id'=>$id])->delete();

        return redirect()->back()->with('flash_message_success', 'Product Alternate image deleted successfully');
    }

    public function exportProducts()
    {
        return Excel::download(new productsExport, 'products.xlsx');
    }

    public function viewOrders()
    {
        if(Session::get('adminDetails')['orders_access']==0)
        {
            return redirect('/admin/dashboard')->with('flash_message_error','You have no access to this module');
        }
        $orders = Order::with('orders')->orderBy('id','desc')->get();
        $orders = json_decode(json_encode($orders));
        /*echo "<pre>"; print_r($orders); die;*/
        return view('admin.orders.view_orders')->with(compact('orders'));
    }

    public function viewOrderDetails($order_id)
    {
        if(Session::get('adminDetails')['orders_access']==0)
        {
            return redirect('/admin/dashboard')->with('flash_message_error','You have no access to this module');
        }
        $orderDetails = Order::with('orders')->where('id',$order_id)->first();
        $orderDetails = json_decode(json_encode($orderDetails));
        /*echo "<pre>"; print_r($orderDetails); die;*/
        $user_id = $orderDetails->user_id;
        $userDetails = User::where('id',$user_id)->first();
        /*$userDetails = json_decode(json_encode($userDetails));
        echo "<pre>"; print_r($userDetails);*/
        return view('admin.orders.view_order_details')->with(compact('orderDetails','userDetails'));
    }

    //order invoice
    public function viewOrderInvoice($order_id)
    {
        if(Session::get('adminDetails')['orders_access']==0)
        {
            return redirect('/admin/dashboard')->with('flash_message_error','You have no access to this module');
        }
        $orderDetails = Order::with('orders')->where('id',$order_id)->first();
        $orderDetails = json_decode(json_encode($orderDetails));
        /*echo "<pre>"; print_r($orderDetails); die;*/
        $user_id = $orderDetails->user_id;
        $userDetails = User::where('id',$user_id)->first();
        /*$userDetails = json_decode(json_encode($userDetails));
        echo "<pre>"; print_r($userDetails);*/
        return view('admin.orders.view_order_invoice')->with(compact('orderDetails','userDetails'));
    }

    public function printPDFInvoice($order_id)
    {
        if(Session::get('adminDetails')['orders_access']==0)
        {
            return redirect('/admin/dashboard')->with('flash_message_error','You have no access to this module');
        }
        $orderDetails = Order::with('orders')->where('id',$order_id)->first();
        $orderDetails = json_decode(json_encode($orderDetails));
        /*echo "<pre>"; print_r($orderDetails); die;*/
        $user_id = $orderDetails->user_id;
        $userDetails = User::where('id',$user_id)->first();
        /*$userDetails = json_decode(json_encode($userDetails));
        echo "<pre>"; print_r($userDetails);*/
        
        $output = '
        <!DOCTYPE html>
        <html lang="en">
          <head>
            <meta charset="utf-8">
            <title>Example 1</title>
            <style>
            .clearfix:after {
                content: "";
                display: table;
                clear: both;
              }
              
              a {
                color: #5D6975;
                text-decoration: underline;
              }
              
              body {
                position: relative;
                width: 21cm;  
                height: 29.7cm; 
                margin: 0 auto; 
                color: #001028;
                background: #FFFFFF; 
                font-family: Arial, sans-serif; 
                font-size: 12px; 
                font-family: Arial;
              }
              
              header {
                padding: 10px 0;
                margin-bottom: 30px;
              }
              
              #logo {
                text-align: center;
                margin-bottom: 10px;
              }
              
              #logo img {
                width: 90px;
              }
              
              h1 {
                border-top: 1px solid  #5D6975;
                border-bottom: 1px solid  #5D6975;
                color: #5D6975;
                font-size: 2.4em;
                line-height: 1.4em;
                font-weight: normal;
                text-align: center;
                margin: 0 0 20px 0;
                background: url(dimension.png);
              }
              
              #project {
                float: left;
              }
              
              #project span {
                color: #5D6975;
                text-align: right;
                width: 52px;
                margin-right: 10px;
                display: inline-block;
                font-size: 0.8em;
              }
              
              #company {
                float: right;
                text-align: right;
              }
              
              #project div,
              #company div {
                white-space: nowrap;        
              }
              
              table {
                width: 100%;
                border-collapse: collapse;
                border-spacing: 0;
                margin-bottom: 20px;
              }
              
              table tr:nth-child(2n-1) td {
                background: #F5F5F5;
              }
              
              table th,
              table td {
                text-align: center;
              }
              
              table th {
                padding: 5px 20px;
                color: #5D6975;
                border-bottom: 1px solid #C1CED9;
                white-space: nowrap;        
                font-weight: normal;
              }
              
              table .service,
              table .desc {
                text-align: left;
              }
              
              table td {
                padding: 20px;
                text-align: right;
              }
              
              table td.service,
              table td.desc {
                vertical-align: top;
              }
              
              table td.unit,
              table td.qty,
              table td.total {
                font-size: 1.2em;
              }
              
              table td.grand {
                border-top: 1px solid #5D6975;;
              }
              
              #notices .notice {
                color: #5D6975;
                font-size: 1.2em;
              }
              
              footer {
                color: #5D6975;
                width: 100%;
                height: 30px;
                position: absolute;
                bottom: 0;
                border-top: 1px solid #C1CED9;
                padding: 8px 0;
                text-align: center;
              }
            </style>
          </head>
          <body>
            <header class="clearfix">
              <div id="logo">
                <img src="images/backend_images/logo.png">
              </div>
              <h1>INVOICE '.$orderDetails->id.'</h1>
              <div id="project" class="clearfix">
              <div><span>Order ID</span> '.$orderDetails->id.'</div>
              <div><span>Order Date</span> '.$orderDetails->created_at.'</div>
              <div><span>Order Amount</span> '.$orderDetails->grand_total.'</div>
              <div><span>Order status</span> '.$orderDetails->order_status.'</div>
              <div><span>Payment Method</span> '.$orderDetails->payment_method.'</div>
              </div>
              <div id="project" style="float:right;">
                <div><strong>Shipping Address </strong></div>
                <div><span>Name: </span> '.$orderDetails->name.'</div>
                <div><span>Address</span> '.$orderDetails->address.','.$orderDetails->city.'</div>
                <div><span>State</span> '.$orderDetails->state.'</div>
                <div><span>Pincode</span> '.$orderDetails->pincode.'</div>
                <div><span>Country</span> '.$orderDetails->country.'</div>
                <div><span>Mobile</span> '.$orderDetails->mobile.'</div>
              </div>
            </header>
            <main>
              <table>
                <thead>
                  <tr>
                  <td style="width:18%"><strong>Product Code</strong></td>
                  <td style="width:18%" class="text-center"><strong>Size</strong></td>
                  <td style="width:18%" class="text-center"><strong>Color</strong></td>
                  <td style="width:18%" class="text-center"><strong>Price</strong></td>
                  <td style="width:18%" class="text-center"><strong>Qty</strong></td>
                  <td style="width:18%" class="text-right"><strong>Totals</strong></td>
                  </tr>
                </thead>
                <tbody>';
        $Subtotal = 0; 
        foreach($orderDetails->orders as $pro){
        $output .= '<tr>
                    <td class="text-left"> '.$pro->product_code.'</td>
                    <td class="text-center"> '.$pro->product_size.' </td>
                    <td class="text-center">'.$pro->product_color.'</td>
                    <td class="text-center">KES '.$pro->product_price.'</td>
                    <td class="text-center">'.$pro->product_qty.'</td>
                    <td class="text-right">KES'.$pro->product_price * $pro->product_qty.'</td>
                </tr>';
        $Subtotal = $Subtotal + ($pro->product_price * $pro->product_qty); }                
        $output .= '<tr>
                    <td class="thick-line"></td>
                    <td class="thick-line"></td>
                    <td class="thick-line"></td>
                    <td class="thick-line"></td>
                    <td class="thick-line text-center"><strong>Subtotal</strong></td>
                    <td class="thick-line text-right">KES'.$Subtotal.'</td>
                </tr>
                <tr>
                    <td class="no-line"></td>
                    <td class="no-line"></td>
                    <td class="no-line"></td>
                    <td class="no-line"></td>
                    <td class="no-line text-center"><strong>Shipping Charges (+)</strong></td>
                    <td class="no-line text-right">KES '.$orderDetails->shipping_charges.'</td>
                </tr>
                <tr>
                    <td class="no-line"></td>
                    <td class="no-line"></td>
                    <td class="no-line"></td>
                    <td class="no-line"></td>
                    <td class="no-line text-center"><strong>Coupon Discount (-)</strong></td>
                    <td class="no-line text-right">KES'.$orderDetails->coupon_amount.'</td>
                </tr>
                <tr>
                    <td class="no-line"></td>
                    <td class="no-line"></td>
                    <td class="no-line"></td>
                    <td class="no-line"></td>
                    <td class="no-line text-center"><strong>Grand Total</strong></td>
                    <td class="no-line text-right">KES'.$orderDetails->grand_total.'</td>
                </tr>
              </table>
            </main>
            <footer>
              Invoice was created on a computer and is valid without the signature and seal.
            </footer>
          </body>
        </html>';

        $dompdf = new Dompdf();
        $dompdf->loadHtml($output);

        $dompdf->setPaper('A4','landscape');
        $dompdf->render();
        $dompdf->stream();
    }

    public function updateOrderStatus(Request $request)
    {
        if(Session::get('adminDetails')['orders_access']==0)
        {
            return redirect('/admin/dashboard')->with('flash_message_error','You have no access to this module');
        }
        if($request->isMethod('post')){
            $data = $request->all();
            Order::where('id',$data['order_id'])->update(['order_status'=>$data['order_status']]);
            return redirect()->back()->with('flash_message_success','Order Status has been updated successfully!');
        }
    }

    //front_end functions-----------------------------------------------
    //display products in category for header
    public function products($url=null){
    	// Show 404 Page if Category does not exists
    	$categoryCount = Category::where(['url'=>$url,'status'=>1])->count();
    	if($categoryCount==0){
    		abort(404);
    	}
    	$categories = Category::with('categories')->where(['parent_id' => 0])->get();
    	$categoryDetails = Category::where(['url'=>$url])->first();
    	if($categoryDetails->parent_id==0){
    		$subCategories = Category::where(['parent_id'=>$categoryDetails->id])->get();
    		$subCategories = json_decode(json_encode($subCategories));
    		foreach($subCategories as $subcat){
    			$cat_ids[] = $subcat->id;
    		}
    		$productsAll = Product::whereIn('products.category_id', $cat_ids)->where('products.status','1')->orderBy('products.id','Desc');
            $breadcrumb = "<a href='/'>Home</a> / <a href='".$categoryDetails->url."'>".$categoryDetails->name."</a>";
    	}else{
    		$productsAll = Product::where(['products.category_id'=>$categoryDetails->id])->where('products.status','1')->orderBy('products.id','Desc');
            $mainCategory = Category::where('id',$categoryDetails->parent_id)->first();
            $breadcrumb = "<a href='/'>Home</a> / <a href='".$mainCategory->url."'>".$mainCategory->name."</a> / <a href='".$categoryDetails->url."'>".$categoryDetails->name."</a>";	
    	}
        if(!empty($_GET['color'])){
            $colorArray = explode('-',$_GET['color']);
            $productsAll = $productsAll->whereIn('products.product_color',$colorArray);
        }
        if(!empty($_GET['sleeve'])){
            $sleeveArray = explode('-',$_GET['sleeve']);
            $productsAll = $productsAll->whereIn('products.sleeve',$sleeveArray);
        }
        if(!empty($_GET['pattern'])){
            $patternArray = explode('-',$_GET['pattern']);
            $productsAll = $productsAll->whereIn('products.pattern',$patternArray);
        }
        if(!empty($_GET['size'])){
            $sizeArray = explode('-',$_GET['size']);
            $productsAll = $productsAll->join('products_attributes','products_attributes.product_id','=','products.id')
            ->select('products.*','products_attributes.product_id','products_attributes.size')
            ->groupBy('products_attributes.product_id')
            ->whereIn('products_attributes.size',$sizeArray);
        }

        $productsAll = $productsAll->paginate(6);
        // $productsAll = json_decode(json_encode($productsAll));
        // echo "<pre>"; print_r($productsAll); die;
        /*$colorArray = array('Black','Blue','Brown','Gold','Green','Orange','Pink','Purple','Red','Silver','White','Yellow');*/
        $colorArray = Product::select('product_color')->groupBy('product_color')->get();
        $colorArray = array_flatten(json_decode(json_encode($colorArray),true));
        $sleeveArray = Product::select('sleeve')->where('sleeve','!=','')->groupBy('sleeve')->get();
        $sleeveArray = array_flatten(json_decode(json_encode($sleeveArray),true));
        $patternArray = Product::select('pattern')->where('pattern','!=','')->groupBy('pattern')->get();
        $patternArray = array_flatten(json_decode(json_encode($patternArray),true));
        $sizesArray = ProductsAttribute::select('size')->groupBy('size')->get();
        $sizesArray = array_flatten(json_decode(json_encode($sizesArray),true));
        /*echo "<pre>"; print_r($sizesArray); die;*/
        $meta_title = $categoryDetails->meta_title;
        $meta_description = $categoryDetails->meta_description;
    	$meta_keywords = $categoryDetails->meta_keywords;
    	return view('products.listing')->with(compact('categories','productsAll','categoryDetails','meta_title','meta_description','meta_keywords','url','colorArray','sleeveArray','patternArray','sizesArray','breadcrumb'));
    }

    public function filter(Request $request){
        $data = $request->all();
        //echo "<pre>"; print_r($data); die;

        $colorUrl="";
        if(!empty($data['colorFilter'])){
            foreach($data['colorFilter'] as $color){
                if(empty($colorUrl)){
                    $colorUrl = "&color=".$color;
                }else{
                    $colorUrl .= "-".$color;
                }
            }
        }
        $sleeveUrl="";
        if(!empty($data['sleeveFilter'])){
            foreach($data['sleeveFilter'] as $sleeve){
                if(empty($sleeveUrl)){
                    $sleeveUrl = "&sleeve=".$sleeve;
                }else{
                    $sleeveUrl .= "-".$sleeve;
                }
            }
        }
        $patternUrl="";
        if(!empty($data['patternFilter'])){
            foreach($data['patternFilter'] as $pattern){
                if(empty($patternUrl)){
                    $patternUrl = "&pattern=".$pattern;
                }else{
                    $patternUrl .= "-".$pattern;
                }
            }
        }
        $sizeUrl="";
        if(!empty($data['sizeFilter'])){
            foreach($data['sizeFilter'] as $size){
                if(empty($sizeUrl)){
                    $sizeUrl = "&size=".$size;
                }else{
                    $sizeUrl .= "-".$size;
                }
            }
        }
        $finalUrl = "products/".$data['url']."?".$colorUrl.$sleeveUrl.$patternUrl.$sizeUrl;
        return redirect::to($finalUrl);
    }

    //search products
    public function searchProducts(Request $request)
    {
        if($request->isMethod('post'))
        {
            $data = $request->all();
            $categories = Category::with('categories')->where(['parent_id' => 0])->get();
            $search_product = $data['product'];
            /*$productsAll = Product::where('product_name','like','%'.$search_product.'%')->orwhere('product_code',$search_product)->where('status',1)->paginate();*/

            $productsAll = Product::where(function($query) use($search_product)
            {
                //search depending on...
                $query->where('product_name','like','%'.$search_product.'%')
                    ->orWhere('product_code','like','%'.$search_product.'%')
                    ->orWhere('description','like','%'.$search_product.'%')
                    ->orWhere('product_color','like','%'.$search_product.'%')
                    ->orWhere('brand','like','%'.$search_product.'%');
            })->where('status',1)->get();

            $breadcrumb = "<a href='/'>Home</a> / ".$search_product;

            return view('products.listing')->with(compact('categories','productsAll','search_product','breadcrumb')); 
        }
    }

    //display product detail apge
    public function product($id = null)
    {
        //404 page if product status is 0
        $productsCount = Product::where(['id'=>$id, 'status'=>1])-> count();
        if($productsCount==0)
        {
            abort(404);
        }

        //get product details
        $productDetails = Product::with('attributes')->where('id',$id)->first();
        $relatedProducts = Product::where('id','!=',$id)->where(['category_id' => $productDetails->category_id])->get();
        //$relatedProducts = json_decode(json_encode($relatedProducts));
        //echo "<pre>"; print_r($relatedProducts); die;
        // foreach($relatedProducts->chunk(3) as $chunk)
        // {
        //     foreach($chunk as $item)
        //     {
        //         echo $item; echo "<br>";
        //     }
        //     echo "<br><br><br>";
        // }
        // die;
        
        //get Product Alternate Image
        $productAltImages = ProductsImage::where('product_id', $id)->get();

        //get all categories and sub categories in detail.blade.php
        $categories = Category::with('categories')->where(['parent_id' => 0])->get();
        //breadcrumb
    	$categoryDetails = Category::where('id',$productDetails->category_id)->first();
    	if($categoryDetails->parent_id==0){
            $breadcrumb = "<a href='/'>Home</a> / <a href='".$categoryDetails->url."'>".$categoryDetails->name."</a> / ".$productDetails->product_name;
    	}else{
            $mainCategory = Category::where('id',$categoryDetails->parent_id)->first();
            $breadcrumb = "<a href='/'>Home</a> / <a href='/products/".$mainCategory->url."'>".$mainCategory->name."</a> / <a href='/products/".$categoryDetails->url."'>".$categoryDetails->name."</a> / ".$productDetails->product_name;	
    	}        

        // check stock attribute to display on frontend
        $total_stock = ProductsAttribute::where('product_id',$id)->sum('stock');
        $meta_title = $productDetails->product_name;
        $meta_description = $productDetails->description;
        $meta_keywords = $productDetails->product_name;
        return view('products.detail')->with(compact('productDetails','categories','productAltImages','total_stock','relatedProducts','meta_title','meta_description','meta_keywords', 'breadcrumb'));
    }

    public function getProductPrice(Request $request)
    {
        $data = $request->all();
        //echo"<pre>"; print_r($data); die;
        $proArr = explode("-", $data['idSize']);
        //echo $proArr[0]; echo $proArr[1]; die;
        $proAttr = ProductsAttribute::where(['product_id'=> $proArr[0],'size'=> $proArr[1]])->first();
        //$getCurrencyRates = Product::getCurrencyRates($proAttr->price);
        echo $proAttr->price;
        //."-".$getCurrencyRates['USD_Rate']."-".$getCurrencyRates['GBP_Rate']."-".$getCurrencyRates['EUR_Rate'];
        echo "#";
        echo $proAttr->stock; 


    }

    //--------------cart functions------------
    public function addtocart(Request $request)
    {
        Session::forget('CouponAmount');
        Session::forget('CouponCode');

        $data = $request->all();
        // echo "<pre>"; print_r($data); die;

        // Check Product Stock is available or not
        $product_size = explode("-",$data['size']);
        $getProductStock = ProductsAttribute::where(['product_id'=>$data['product_id'],'size'=>$product_size[1]])->first();
        if($getProductStock->stock<$data['quantity']){
            return redirect()->back()->with('flash_message_error','Required Quantity is not available!');
        }
        //-----end of check product stock

        //if user email is empty set to empty
        if(empty(Auth::user()->email)){
            $data['user_email'] = '';    
        }else{
            //else authenticate the email
            $data['user_email'] = Auth::user()->email;
        }

        $session_id = Session::get('session_id');
        if(!isset($session_id)){
            $session_id = str_random(40);
            Session::put('session_id',$session_id);
        }

        //get the string of the size
        $sizeIDArr = explode('-',$data['size']);
        $product_size = $sizeIDArr[1];

        //check for logged in users and guests if product already exists in cart
        if(empty(Auth::check())){
            //if guest check stock availability from email
            //able to add different products of different size.//101
            $countProducts = DB::table('cart')->where(['product_id' => $data['product_id'],'product_color' => $data['product_color'],'size' => $product_size,'session_id' => $session_id])->count();
            if($countProducts>0){
                return redirect()->back()->with('flash_message_error','Product already exist in Cart!');
            }
        }else{
            //iflogged in check stock availability from session id
            $countProducts = DB::table('cart')->where(['product_id' => $data['product_id'],'product_color' => $data['product_color'],'size' => $product_size,'user_email' => $data['user_email']])->count();
            if($countProducts>0){
                return redirect()->back()->with('flash_message_error','Product already exist in Cart!');
            }    
        }        

        // check in cart if product exixts
        $getSKU = ProductsAttribute::select('sku')->where(['product_id' => $data['product_id'], 'size' => $product_size])->first();                
        DB::table('cart')
        ->insert(['product_id' => $data['product_id'],'product_name' => $data['product_name'],
            'product_code' => $getSKU['sku'],'product_color' => $data['product_color'],
            'price' => $data['price'],'size' => $product_size,'quantity' => $data['quantity'],'user_email' => $data['user_email'],'session_id' => $session_id]);
        //check if cart product exists

        return redirect('cart')->with('flash_message_success','Product has been added in Cart!');
    } 

    public function cart()
    {
        if(Auth::check()){
            $user_email = Auth::user()->email;
            $userCart = DB::table('cart')->where(['user_email' => $user_email])->get();     
        }else{
            $session_id = Session::get('session_id');
            $userCart = DB::table('cart')->where(['session_id' => $session_id])->get();    
        }
    
        foreach($userCart as $key => $product){
            $productDetails = Product::where('id',$product->product_id)->first();
            $userCart[$key]->image = $productDetails->image;
        }
        //echo "<pre>"; print_r($userCart); die;
        $meta_title = "Shopping Cart - E-com Website";
        $meta_description = "View Shopping Cart of E-com Website";
        $meta_keywords = "shopping cart, e-com Website";
        return view('products.cart')->with(compact('userCart','meta_title','meta_description','meta_keywords'));
    }

    public function updateCartQuantity($id = null, $quantity = null)
    {
        Session::forget('CouponAmount');
        Session::forget('CouponCode');

        $getCartDetails = DB::table('cart')->where('id',$id)->first();
        $getAttributeStock = ProductsAttribute::where('sku', $getCartDetails->product_code)->first();
        echo $getAttributeStock->stock; echo "---";
        echo $updated_quantity = $getCartDetails->quantity+$quantity; 
        if($getAttributeStock->stock >= $updated_quantity)
        {
            DB::table('cart')->where('id', $id)->increment('quantity', $quantity);
            return redirect('cart')->with('flash_message_success', 'Added successfully');
        }else{
            return redirect('cart')->with('flash_message_error', 'Product Quantity unavailable');
        }   
    }

    public function deleteCartProduct($id=null){
        Session::forget('CouponAmount');
        Session::forget('CouponCode');
        
        //echo $id;  die;
        DB::table('cart')->where('id',$id)->delete();
        return redirect('cart')->with('flash_message_success','Product has been deleted in Cart!');
    }


    public function applyCoupon(Request $request, $id = null)
	{
        Session::forget('CouponAmount');
        Session::forget('CouponCode');

		$data = $request->all();
        //echo "<pre>"; print_r($data); die;
        $couponCount = Coupon::where('coupon_code', $data['coupon_code'])->count();
        //if coupon doesnt exist
        if($couponCount == 0)
        {
            return redirect()->back()->with('flash_message_error', 'Coupon doesnt exist');
        }else{
            //echo "Success"; die;
            // with perform other checks like Active/Inactive, Expiry date..

            // Get Coupon Details
            $couponDetails = Coupon::where('coupon_code',$data['coupon_code'])->first();
            //echo "<pre>"; print_r($couponDetails); die;

            // If coupon is Inactive
            if($couponDetails->status==0){
                
                return redirect()->back()->with('flash_message_error','Coupon Inactive!');
            }

            // If coupon is Expired
            $expiry_date = $couponDetails->expiry_date;
            $current_date = date('Y-m-d');  
            if($expiry_date < $current_date){
                return redirect()->back()->with('flash_message_error','Coupon Expired!');
            }
            //echo "Success"; die;
            //if the coupon is valid
            // Get Cart Total Amount
            if(Auth::check()){
                $user_email = Auth::user()->email;
                $userCart = DB::table('cart')->where(['user_email' => $user_email])->get();     
            }else{
                $session_id = Session::get('session_id');
                $userCart = DB::table('cart')->where(['session_id' => $session_id])->get();    
             }

            $total_amount = 0;
            foreach($userCart as $item){
               $total_amount = $total_amount + ($item->price * $item->quantity);
            }

            // Check if amount type is Fixed or Percentage
            if($couponDetails->amount_type=="Fixed"){
                $couponAmount = $couponDetails->amount;
            }else{
                $couponAmount = $total_amount * ($couponDetails->amount/100);
            }
            //echo $couponAmount; die;

            // Add Coupon Code & Amount in Session
            Session::put('CouponAmount',$couponAmount);
            Session::put('CouponCode',$data['coupon_code']);

            return redirect()->back()->with('flash_message_success','Coupon code successfully
                applied. You are availing discount!');
        }
    }
    
    public function checkout(Request $request)
    {
        //get user id details
        $user_id = Auth::user()->id;
        $user_email = Auth::user()->email;
        $userDetails = User::find($user_id);
        $countries = Country::get();

        //Check if Shipping Address exists
        $shippingCount = DeliveryAddress::where('user_id',$user_id)->count();
        $shippingDetails = array();
        if($shippingCount>0){
            $shippingDetails = DeliveryAddress::where('user_id',$user_id)->first();
        }

        // Update cart table with user email
        $session_id = Session::get('session_id');
        DB::table('cart')->where(['session_id'=>$session_id])->update(['user_email'=>$user_email]);

        if($request->isMethod('post'))
        {
            $data = $request->all();
            //echo "<pre>"; print_r($data); die;

            //Return to check out page if any of the fields is empty
            if(empty($data['billing_name']) || empty($data['billing_address']) || empty($data['billing_city']) || empty($data['billing_state']) || empty($data['billing_country']) || empty($data['billing_pincode']) || empty($data['billing_mobile']) || empty($data['shipping_name']) || empty($data['shipping_address']) || empty($data['shipping_city']) || empty($data['shipping_state']) || empty($data['shipping_country']) || empty($data['shipping_pincode']) || empty($data['shipping_mobile']))
            {
                return redirect()->back()->with('flash_message_error','Please fill all fields to Checkout!');
            }

             // Update User details
            User::where('id',$user_id)->update(['name'=>$data['billing_name'],'address'=>$data['billing_address'],'city'=>$data['billing_city'],'state'=>$data['billing_state'],'pincode'=>$data['billing_pincode'],'country'=>$data['billing_country'],'mobile'=>$data['billing_mobile']]);
            
            //check if shipping address already exists
            if($shippingCount>0){
                // Update Shipping Address
                DeliveryAddress::where('user_id',$user_id)->update(['name'=>$data['shipping_name'],'address'=>$data['shipping_address'],'city'=>$data['shipping_city'],'state'=>$data['shipping_state'],'pincode'=>$data['shipping_pincode'],'country'=>$data['shipping_country'],'mobile'=>$data['shipping_mobile']]);
            }else{
                // Add New Shipping Address
                $shipping = new DeliveryAddress;
                $shipping->user_id = $user_id;
                $shipping->user_email = $user_email;
                $shipping->name = $data['shipping_name'];
                $shipping->address = $data['shipping_address'];
                $shipping->city = $data['shipping_city'];
                $shipping->state = $data['shipping_state'];
                $shipping->pincode = $data['shipping_pincode'];
                $shipping->country = $data['shipping_country'];
                $shipping->mobile = $data['shipping_mobile'];
                $shipping->save();
            }
            //echo "Redirect to Order Review page"; die;
            return redirect()->action('ProductsController@orderReview');
        }
        $meta_title = "Checkout - E-com Website";
        return view('products.checkout')->with(compact('userDetails','countries','shippingDetails','meta_title'));
    }

    public function orderReview()
    {
        $user_id = Auth::user()->id;
        $user_email = Auth::user()->email;
        $userDetails = User::where('id',$user_id)->first();
        
        $shippingDetails = DeliveryAddress::where('user_id',$user_id)->first();
        //$shippingDetails = array();

        //get user cart details
        $userCart = DB::table('cart')->where(['user_email' => $user_email])->get();
        $total_weight = 0;
        foreach($userCart as $key => $product){
            $productDetails = Product::where('id',$product->product_id)->first();
            $userCart[$key]->image = $productDetails->image;
            $total_weight = $total_weight + $productDetails->weight;
        }
        //echo "<pre>"; print_r($userCart); die;
        // $codpincodeCount = DB::table('cod_pincodes')->where('pincode',$shippingDetails->pincode)->count();
        // $prepaidpincodeCount = DB::table('prepaid_pincodes')->where('pincode',$shippingDetails->pincode)->count();

        // Fetch Shipping Charges
        $shippingCharges = Product::getShippingCharges($total_weight,$shippingDetails->country); 
        Session::put('ShippingCharges',$shippingCharges);

        $meta_title = "Order Review - E-com Website";
        return view('products.order_review')->with(compact('userDetails','shippingDetails','shippingCharges', 'userCart', 'meta_title'));
    }

    public function placeOrder(Request $request)
    {
        if($request->isMethod('post')){
            $data = $request->all();
            $user_id = Auth::user()->id;
            $user_email = Auth::user()->email;
            //echo "<pre>"; print_r($data); print_r($user_id); print_r($user_email); die;

            //prevent out of stock product from ordering
            $userCart = DB::table('cart')->where('user_email', $user_email)->get();
            // $userCart = json_decode(json_encode($userCart));
            // echo "<pre>"; print_r($userCart); die;
            foreach($userCart as $cart)
            {
                $getAttributeCount = Product::getAttributeCount($cart->product_id,$cart->size);
                if($getAttributeCount==0){
                    Product::deleteCartProduct($cart->product_id,$user_email);
                    return redirect('/cart')->with('flash_message_error','One of the product is not available. Try again!');
                } 
                
                $product_stock = Product::getProductStock($cart->product_id, $cart->size);
                if($product_stock==0){
                    Product::deleteCartProduct($cart->product_id,$user_email);
                    return redirect('/cart')->with('flash_message_error','Sold Out product removed from Cart. Try again!');
                }
                /*echo "Original Stock: ".$product_stock;
                echo "Demanded Stock: ".$cart->quantity; die;*/
                if($cart->quantity>$product_stock){
                    return redirect('/cart')->with('flash_message_error','Reduce Product Stock and try again.');    
                }

                $product_status = Product::getProductStatus($cart->product_id);
                if($product_status==0){
                    Product::deleteCartProduct($cart->product_id,$user_email);
                    return redirect('/cart')->with('flash_message_error','Disabled product removed from Cart. Please try again!');
                }

                $getCategoryId = Product::select('category_id')->where('id',$cart->product_id)->first();
                $category_status = Product::getCategoryStatus($getCategoryId->category_id);
                if($category_status==0){
                    Product::deleteCartProduct($cart->product_id,$user_email);
                    return redirect('/cart')->with('flash_message_error','One of the product category is disabled. Please try again!');    
                }
            }
            //end of prevent out of stock product from ordering

            // Get Shipping Address of User
            $shippingDetails = DeliveryAddress::where(['user_email' => $user_email])->first();
            //echo "<pre>"; print_r($shippingDetails); die;

            if(empty(Session::get('CouponCode')))
            {
                $coupon_code = ''; 
            }else{
                $coupon_code = Session::get('CouponCode'); 
            }
 
            if(empty(Session::get('CouponAmount')))
            {
                $coupon_amount = ''; 
            }else{
                $coupon_amount = Session::get('CouponAmount'); 
            } 

            //Fetch shipping charges
            //$shippingCharges = Product::getShippingCharges($shippingDetails->country);
            $data['grandTotal'] = 0;

            $grandTotal = Product::getGrandTotal(); 
            Session::put('grandTotal', $grandTotal);

            $order = new Order;
            $order->user_id = $user_id;
            $order->user_email = $user_email;
            $order->name = $shippingDetails->name;
            $order->address = $shippingDetails->address;
            $order->city = $shippingDetails->city;
            $order->state = $shippingDetails->state;
            $order->pincode = $shippingDetails->pincode;
            $order->country = $shippingDetails->country;
            $order->mobile = $shippingDetails->mobile;
            $order->coupon_code = $coupon_code;
            $order->coupon_amount = $coupon_amount;
            $order->order_status = "New";
            $order->payment_method = $data['payment_method'];
            $order->shipping_charges = Session::get('ShippingCharges');
            $order->grand_total = $grandTotal;
            $order->save();

            //get id of the order placed by user
            $order_id = DB::getPdo()->lastInsertId();

            //fetch the products from the cart order using users email
            $cartProducts = DB::table('cart')->where(['user_email'=>$user_email])->get();
            foreach($cartProducts as $pro){
                $cartPro = new OrdersProduct;
                $cartPro->order_id = $order_id;
                $cartPro->user_id = $user_id;
                $cartPro->product_id = $pro->product_id;
                $cartPro->product_code = $pro->product_code;
                $cartPro->product_name = $pro->product_name;
                $cartPro->product_color = $pro->product_color;
                $cartPro->product_size = $pro->size;
                $product_price = Product::getProductPrice( $pro->product_id,  $pro->size);
                $cartPro->product_price = $product_price;
                $cartPro->product_qty = $pro->quantity;
                $cartPro->save();

                // Reduce Stock Script Starts
                $getProductStock = ProductsAttribute::where('sku',$pro->product_code)->first();
                /*echo "Original Stock: ".$getProductStock->stock;
                echo "Stock to reduce: ".$pro->quantity;*/
                $newStock = $getProductStock->stock - $pro->quantity;
                if($newStock<0){
                    $newStock = 0;
                }
                ProductsAttribute::where('sku',$pro->product_code)->update(['stock'=>$newStock]);
                // End of Reduce Stock Script 
            }

            //get order id and total amount from session 
            //get grand total
            Session::put('order_id',$order_id);
            Session::put('grand_total',$data['grand_total']);

            if($data['payment_method'] == "COD"){

                /* Code for Order Email Start */                 
                $productDetails = Order::with('orders')->where('id',$order_id)->first();
                $productDetails = json_decode(json_encode($productDetails),true);
                // echo "<pre>"; print_r($productDetails); die;
                $userDetails = User::where('id',$user_id)->first();
                $userDetails = json_decode(json_encode($userDetails),true);
                // echo "<pre>"; print_r($userDetails); die;
                $email = $user_email;
                $messageData = [
                    'email' => $email,
                    'name' => $shippingDetails->name,
                    'order_id' => $order_id,
                    'productDetails' => $productDetails,
                    'userDetails' => $userDetails,
                ];
                Mail::send('emails.order',$messageData,function($message) use($email){
                    $message->to($email)->subject('Order Placed - E-com Website');    
                });
                 /* Code for Order Email Ends */
                
                // COD - Redirect user to thanks page after saving order
                return redirect('/thanks');
            }else{
                return redirect('/paypal');
            }
        }
    }

    public function thanks(Request $request)
    {
        $user_email = Auth::user()->email;
        DB::table('cart')->where('user_email',$user_email)->delete();
        return view('orders.thanks');
    }

    public function paypal(Request $request)
    {
        $user_email = Auth::user()->email;
        DB::table('cart')->where('user_email',$user_email)->delete();
        return view('orders.paypal');
    }

    public function thanksPaypal()
    {
        return view('orders.thanks_paypal');
    }

    public function cancelPaypal()
    {
        return view('orders.cancel_paypal');
    }

    public function userOrders()
    {
        $user_id = Auth::user()->id;
        $orders = Order::with('orders')->where('user_id',$user_id)->orderBy('id','DESC')->get();
        // $orders = json_decode(json_encode($orders));
        // echo "<pre>"; print_r($orders); die;
        return view('orders.user_orders')->with(compact('orders'));
    }

    public function userOrderDetails($order_id)
    {
        $user_id = Auth::user()->id;
        $orderDetails = Order::with('orders')->where('id',$order_id)->first();
        $orderDetails = json_decode(json_encode($orderDetails));
        // echo "<pre>"; print_r($orderDetails); die;
        return view('orders.user_order_details')->with(compact('orderDetails'));
    }

    public function checkPincode(Request $request)
    {
        if($request->isMethod('post')){
            $data = $request->all();
            echo $pincodeCount = DB::table('pincodes')->where('pincode',$data['pincode'])->count();
        }
    }

}