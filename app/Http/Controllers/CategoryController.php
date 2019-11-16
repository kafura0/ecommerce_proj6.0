<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use Session;
use App\Category;

class CategoryController extends Controller
{
    //add new category 
    public function addCategory(Request $request)
    {
        if(Session::get('adminDetails')['categories_edit_access']==0)
        {
            return redirect('/admin/dashboard')->with('flash_message_error','You have no access to this module');
        }
        if($request->isMethod('post')){
            $data = $request -> all();
            //echo "<pre>"; print_r($data); die;

            if(empty($data['status']))
            {
                $status = 0;
            }else{
                $status = 1;
            }
            if(empty($data['meta_title'])){
                $data['meta_title'] = "";    
            }
            if(empty($data['meta_description'])){
                $data['meta_description'] = "";    
            }
            if(empty($data['meta_keywords'])){
                $data['meta_keywords'] = "";    
            }

            $category = new Category;
            $category->name = $data['category_name'];
            $category->description = $data['description'];
            $category->parent_id = $data['parent_id'];
            $category->url = $data['url'];
            $category->meta_title = $data['meta_title'];
            $category->meta_description = $data['meta_description'];
            $category->meta_keywords = $data['meta_keywords'];
            $category->status = $status;
            $category-> save();
            return redirect('/admin/view_categories')-> with('flash_message_success','Category created successfully');
        }

        $levels = Category::where(['parent_id'=>0])->get();

        return view('admin.categories.add_category')-> with(compact('levels'));
    }

    public function editCategory(Request $request, $id = null)
    {
        
        if(Session::get('adminDetails')['categories_edit_access']==0)
        {
            return redirect('/admin/dashboard')->with('flash_message_error','You have no access to this module');
        }
        //echo "test"; die;
        if($request->isMethod('post')){
            $data = $request-> all();
            //echo "<pre>"; print_r($data); die;

            if(empty($data['status']))
            {
                $status = 0;
            }else{
                $status = 1;
            }
            if(empty($data['meta_title'])){
                $data['meta_title'] = "";    
            }
            if(empty($data['meta_description'])){
                $data['meta_description'] = "";    
            }
            if(empty($data['meta_keywords'])){
                $data['meta_keywords'] = "";    
            }

            Category::where(['id' => $id])-> update(
                ['name'=>$data['category_name'],
                'description'=>$data['description'],
                'url'=>$data['url'],
                'status'=>$data['status'],
                'meta_title'=>$data['meta_title'],
                'meta_description'=>$data['meta_description'],
                'meta_keywords'=>$data['meta_keywords']
            ]);           
            return redirect('/admin/view_categories')->with('flash_message_success', 'Category updated successfully!');
        }
        $categoryDetails = Category::where(['id'=> $id])->first();
        $levels = Category::where(['parent_id'=>0])->get();
        return view('admin.categories.edit_category')->with(compact('categoryDetails', 'levels'));
        
    }

    public function deleteCategory($id = null)
    {
        if(!empty($id)){
            Category::where(['id'=> $id])->delete();
            return redirect()->back()->with('flash_message_success', 'Category deleted successfully!');
        }
    }

    public function viewCategories()
    {
        if(Session::get('adminDetails')['categories_view_access']==0)
        {
            return redirect('/admin/dashboard')->with('flash_message_error','You have no access to this module');
        }
        $categories = Category::get();
        // $categories =json_decode(json_encode($categories));
        // echo "<pre>"; print_r($categories); die;
        return view('admin.categories.view_categories')->with(compact('categories'));
    }    
}
