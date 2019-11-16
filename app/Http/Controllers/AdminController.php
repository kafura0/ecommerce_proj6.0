<?php
namespace App\Http\Controllers;

use Auth;
use Session;
use App\User;
use App\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    ///------------------LOGIN LOGOUT AND UPDATE WD FUNCTIONS------
    //login function.
    public function login(Request $request){
    	if($request->isMethod('post')){
    		$data = $request->input();
            $adminCount = Admin::where(['username' => $data['username'],'password'=>md5($data['password']),'status'=>1])->count(); 
            if($adminCount > 0){
                //echo "Success"; die;
                Session::put('adminSession', $data['username']);
                return redirect('/admin/dashboard');
        	}else{
                //echo "failed"; die;
                return redirect('/admin')->with('flash_message_error','Invalid Username or Password');
        	}
    	}
    	return view('admin.admin_login');
    }

    public function dashboard()
    {
        // if(Session::has('adminSession')){
        // //     // Perform all actions
        // }else{
        // //     //return redirect()->action('AdminController@login')->with('flash_message_error', 'Please Login');
        //    return redirect('/admin')->with('flash_message_error','Please Login to access admin panel');
        // }
        return view('admin.dashboard');
    }

    public function settings()
    {

        $adminDetails = Admin::where(['username'=>Session::get('adminSession')])->first();
        // $adminDetails = json_decode(json_encode($adminDetails));
        // echo "<pre>"; print_r($adminDetails); die;

        return view('admin.settings')->with(compact('adminDetails'));
    }

    public function chkPassword(Request $request)
    {
        $data = $request->all();
        //echo "<pre>"; print_r($data); die;
        $adminCount = Admin::where(['username' => Session::get('adminSession'),'password'=>md5($data['current_pwd'])])->count(); 
            if ($adminCount == 1) {
                //echo '{"valid":true}';die;
                echo "true"; die;
            } else {
                //echo '{"valid":false}';die;
                echo "false"; die;
            }
    }
    public function updatePassword(Request $request){
        if($request->isMethod('post')){
            $data = $request->all();
            //echo "<pre>"; print_r($data); die;
            $adminCount = Admin::where(['username' => Session::get('adminSession'),'password'=>md5($data['current_pwd'])])->count();

            if ($adminCount == 1) {
                // here you know data is valid
                $password = md5($data['new_pwd']);
                Admin::where('username',Session::get('adminSession'))->update(['password'=>$password]);
                return redirect('/admin/settings')->with('flash_message_success', 'Password updated successfully.');
            }else{
                return redirect('/admin/settings')->with('flash_message_error', 'Current Password entered is incorrect.');
            }

            
        }
    }

    public function logout()
    {
        Auth::logout();
        Session::forget('frontSession');
        Session::forget('session_id');
        Session::flush();
        return redirect('/admin') -> with('flash_message_success', 'Logged out Succesfully!');
    }
    //------------------end of login logout and update password-----------

    //admin roles functions
    public function viewAdmins()
    {
        $admins = Admin::get();
        // $admins = json_decode(json_encode($admins));
        // echo "<pre>"; print_r($admins); die;
        return view('admin.admins.view_admins')->with(compact('admins'));
    }

    public function addAdmin(Request $request)
    {
        if($request->isMethod('post'))
        {
            $data = $request->all();
            // echo "<pre>"; print_r($data); die;

            $adminCount = Admin::where('username', $data['username'])->count();
            if($adminCount>0)
            {
                return redirect()->back()->with('flash_message_error', 'Admin / SubAdmin UserName exists');
            }else{
                if(empty($data['status']))
                {
                    $data['status']=0;
                }
                if($data['type']=="Admin"){
                    $admin = new Admin;
                    $admin->type = $data['type'];
                    $admin->username = $data['username'];
                    $admin->password = md5($data['password']);
                    $admin->status = $data['status'];
                    $admin->save();
                    return redirect('/admin/view_admins')->with('flash_message_success', 'Admin added successfully' );
                }else if($data['type']=="Sub Admin"){
                    if(empty($data['categories_view_access']))
                    {
                        $data['categories_view_access'] = 0;
                    }
                    if(empty($data['categories_edit_access']))
                    {
                        $data['categories_edit_access'] = 0;
                    }
                    if(empty($data['categories_full_access']))
                    {
                        $data['categories_full_access'] = 0;
                    }else{
                        if($data['categories_full_access']==1)
                        {
                            $data['categories_view_access'] = 1;
                            $data['categories_edit_access'] = 1;
                        }
                    }
                    if(empty($data['products_view_access']))
                    {
                        $data['products_view_access'] = 0;
                    }
                    if(empty($data['products_edit_access']))
                    {
                        $data['products_edit_access'] = 0;
                    }
                    if(empty($data['products_full_access']))
                    {
                        $data['products_full_access'] = 0;
                    }else{
                        if($data['products_full_access']==1)
                        {
                            $data['products_view_access'] = 1;
                            $data['products_edit_access'] = 1;
                        }
                    }
                    if(empty($data['orders_access']))
                    {
                        $data['orders_access'] = 0;
                    }
                    if(empty($data['users_access']))
                    {
                        $data['users_access'] = 0;
                    }
                    $admin = new Admin;
                    $admin->type = $data['type'];
                    $admin->username = $data['username'];
                    $admin->password = md5($data['password']);
                    $admin->categories_view_access = $data['categories_view_access'];
                    $admin->categories_edit_access = $data['categories_edit_access'];
                    $admin->categories_full_access = $data['categories_full_access'];
                    $admin->products_view_access = $data['products_view_access'];
                    $admin->products_edit_access = $data['products_edit_access'];
                    $admin->products_full_access = $data['products_full_access'];
                    $admin->orders_access = $data['orders_access'];
                    $admin->users_access = $data['users_access'];
                    $admin->status = $data['status'];
                    $admin->save();
                    return redirect('/admin/view_admins')->with('flash_message_success', 'SubAdmin added successfully' );
                }
            }
        }
        return view('admin.admins.add_admin');
    }

    public function editAdmin(Request $request, $id)
    {
        $adminDetails = Admin::where('id',$id)->first();
        // $adminDetails = json_decode(json_encode($adminDetails));
        // echo "<pre>"; print_r($adminDetails); die;
        if($request->isMethod('post'))
        {
            $data = $request->all();
            // echo "<pre>"; print_r($data); die;
            if(empty($data['status']))
            {
                $data['status']=0;
            }
            if($data['type']=="Admin"){
                Admin::where('username', $data['username'])->update([
                    'password'=>md5($data['password']),
                    'status'=>$data['status'],                    
                ]);
                return redirect('/admin/view_admins')->with('flash_message_success', 'Admin updated successfully' );
            }else if($data['type']=="Sub Admin"){
                if(empty($data['categories_view_access']))
                {
                    $data['categories_view_access'] = 0;
                }
                if(empty($data['categories_edit_access']))
                {
                    $data['categories_edit_access'] = 0;
                }
                if(empty($data['categories_full_access']))
                {
                    $data['categories_full_access'] = 0;
                }else{
                    if($data['categories_full_access']==1)
                    {
                        $data['categories_view_access'] = 1;
                        $data['categories_edit_access'] = 1;
                    }
                }
                if(empty($data['products_view_access']))
                {
                    $data['products_view_access'] = 0;
                }
                if(empty($data['products_edit_access']))
                {
                    $data['products_edit_access'] = 0;
                }
                if(empty($data['products_full_access']))
                {
                    $data['products_full_access'] = 0;
                }else{
                    if($data['products_full_access']==1)
                    {
                        $data['products_view_access'] = 1;
                        $data['products_edit_access'] = 1;
                    }
                }
                if(empty($data['orders_access']))
                {
                    $data['orders_access'] = 0;
                }
                if(empty($data['users_access']))
                {
                    $data['users_access'] = 0;
                }
                Admin::where('username', $data['username'])->update([
                    'password'=>md5($data['password']),
                    'status'=>$data['status'], 
                    'categories_view_access'=>$data['categories_view_access'],
                    'categories_edit_access'=>$data['categories_edit_access'],
                    'categories_full_access'=>$data['categories_full_access'],
                    'products_view_access'=>$data['products_view_access'],
                    'products_edit_access'=>$data['products_edit_access'],
                    'products_full_access'=>$data['products_full_access'],
                    'orders_access'=>$data['orders_access'],
                    'users_access'=>$data['users_access'],

                ]);
                return redirect('/admin/view_admins')->with('flash_message_success', 'Sub Admin updated successfully' );
            }

        }
        return view('admin.admins.edit_admin')->with(compact('adminDetails'));

    }
}
