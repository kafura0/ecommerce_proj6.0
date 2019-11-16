<?php

namespace App\Http\Middleware;

use Closure;
use Session;
use App\Admin;
use Illuminate\Support\Facades\Route;


class Adminlogin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        //echo "test"; die;
        if(empty(Session::has('adminSession'))){
            return redirect('/admin');
        } else{
            // echo "test"; die;
            //Get Admin / Sub Admin details
            $adminDetails = Admin::where('username', Session::get('adminSession'))->first();
            $adminDetails = json_decode(json_encode($adminDetails), true);
            if($adminDetails['type']=="Admin")
            {
                $adminDetails['categories_view_access']=1;
                $adminDetails['categories_edit_access']=1;
                $adminDetails['categories_full_access']=1;
                $adminDetails['products_view_access']=1;
                $adminDetails['products_edit_access']=1;
                $adminDetails['products_full_access']=1;
                $adminDetails['orders_access']=1;
                $adminDetails['users_access']=1;
            }
            Session::put('adminDetails', $adminDetails);
            
            // echo "<pre>"; print_r(Session::get('adminDetails')); die;

            //get current path
            $currentPath = Route::getFacadeRoot()->current()->uri(); 
            if($currentPath == "admin/view_categories" && Session::get('adminDetails')['categories_view_access']==0)
            {
                return redirect('/admin/dashboard')->with('flash_message_error','You have no access to this module');
            }
            if($currentPath == "admin/view_products" && Session::get('adminDetails')['products_view_access']==0)
            {
                return redirect('/admin/dashboard')->with('flash_message_error','You have no access to this module');
            }
            if($currentPath == "admin/add_product" && Session::get('adminDetails')['products_edit_access']==0)
            {
                return redirect('/admin/dashboard')->with('flash_message_error','You have no access to this module');
            }
          
        }      
        return $next($request);
    }
}
