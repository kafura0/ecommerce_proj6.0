<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Category;
use App\Product;
use App\Banner;

class IndexController extends Controller
{
    //importing db data to the index page
    public function index(Request $request)
    {
        // //in ascending order by default
        // $productsAll = Product::get();

        // //in descending order by default
        // $productsAll = Product::orderBy('id', 'DESC')->get();

        //in randon order by default
        $productsAll = Product::inRandomOrder()->where('status',1)->where('feature_item',1)->paginate(3);

        //Get all categories and sub categories
        $categories = Category:: with('categories')->where(['parent_id'=> 0])->get();
        // $categories = json_decode(json_encode($categories));
        // echo "<pre>";print_r($categories); die;

        // $categories_menu = "";
        // //categories and subcategories accordian display
        // foreach($categories as $cat){
            
        //     $categories_menu .= "<div class='panel-heading'>
        //                             <h4 class='panel-title'>
        //                                 <a data-toggle='collapse' data-parent='#accordian' href='#".$cat->id."'>
        //                                     <span class='badge pull-right'><i class='fa fa-plus'></i></span>
        //                                     ".$cat->name."
        //                                 </a>
        //                             </h4>
        //                         </div>
        //                         <div id='".$cat->id."' class='panel-collapse collapse'>
        //                             <div class='panel-body'>
        //                                 <ul>
        //                         ";
        //                                 $sub_categories = Category::where(['parent_id'=>$cat->id])->get();
        //                                 foreach($sub_categories as $subcat){
                                        
        //                                     $categories_menu .= "<li><a href='".$subcat->url."'>".$subcat->name." </a></li>";
        //                                 }
        //                                 $categories_menu .= "
        //                                 </ul>                           
        //                             </div>
        //                         </div> 
        //                         ";   
        // }
        $banners = Banner::where('status', '1')->get();
        //echo $banners; die;

        // Meta tags
		$meta_title = "E-shop Website";
		$meta_description = "Online Shopping Site for Men, Women and Kids Clothing";
		$meta_keywords = "eshop website, online shopping, men clothing";
    
        return view('index')->with(compact('productsAll', 'categories', 'banners', 'meta_title','meta_description','meta_keywords'));
    }
}
