<?php

namespace App\Http\Controllers;

use Auth;
use Session;
use App\User;
use App\Country;
use Carbon\Carbon;
use App\Exports\usersExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Maatwebsite\Excel\Facades\Excel;


class UsersController extends Controller
{
    //
    public function userLoginRegister()
    {
        //echo "test"; die;
        //user register form route
        $meta_title = "User Login/Register - Ecom Website";
        return view('users.login_register')->with(compact('meta_title'));
    }

    //
    public function register(Request $request)
    {
        if($request->isMethod('post'))
        {
            $data = $request->all();
            //echo "<pre>"; print_r($data);die;
            // Check if User already exists
    		$usersCount = User::where('email',$data['email'])->count();
    		if($usersCount>0){
                return redirect()->back()->with('flash_message_error','Email already exists!');
                //echo "<pre>"; print_r($usersCount);die;
    		}else{
                //echo "success"; die;
                $user = new User;
                $user->name = $data['name'];
                $user->email = $data['email'];
                $user->password = bcrypt($data['password']);
                date_default_timezone_set('Africa/Nairobi');
                $user->created_at = date("Y-m-d H:i:s");
                $user->updated_at = date("Y-m-d H:i:s");
                $user->save();

                // //verification email
                // $email = $data['email'];
                // $messageData = ['email'=>$data['email'], 'name'=> $data['name']];
                // Mail::send('emails.register', $messageData, function($message)use($email){
                //     $message->to($email)->subject('Registration with Ecom website');
                // });

                //send confirmation email
                $email = $data['email'];
                $messageData = ['email'=>$data['email'], 'name'=> $data['name'], 'code'=>base64_encode($data['email'])];
                Mail::send('emails.confirmation', $messageData, function($message)use($email){
                    $message->to($email)->subject('Registration with Ecom website');
                });

                return redirect()->back()->with('flash_message_success','Please confirm your email to activate your account!');

                if(Auth::attempt(['email'=>$data['email'],'password'=>$data['password']])){
                    Session::put('frontSession',$data['email']);

                    if(!empty(Session::get('session_id'))){
                        $session_id = Session::get('session_id');
                        DB::table('cart')->where('session_id',$session_id)->update(['user_email' => $data['email']]);
                    }

                    return redirect('/cart');
                }
            }
        }       
    }

    public function login(Request $request)
    {
        if($request->isMethod('post')){
            $data = $request->all();
            //echo "<pre>"; print_r($data); die;
            if(Auth::attempt(['email'=>$data['email'],'password'=>$data['password']])){
                $userStatus = User::where('email',$data['email'])->first();
                if($userStatus->status == 0){
                    return redirect()->back()->with('flash_message_error','Your account is not activated! Please confirm your email to activate.');    
                }
                Session::put('frontSession',$data['email']);

                if(!empty(Session::get('session_id'))){
                    $session_id = Session::get('session_id');
                    DB::table('cart')->where('session_id',$session_id)->update(['user_email' => $data['email']]);
                }
                return redirect('/cart');
            }else{
                return redirect()->back()->with('flash_message_error','Invalid Username or Password!');
            }
        }
    }

    public function confirmAccount($email)
    {
        //decode email from the link
        $email = base64_decode($email);
        //check in users table if the email exists
        $userCount = User::where('email',$email)->count();
        //if email exists
        if($userCount > 0){
            $userDetails = User::where('email',$email)->first();
            //if the user status is already activated
            if($userDetails->status == 1){
                return redirect('login_register')->with('flash_message_success','Your Email account is already activated. You can login now.');
            }else{
                //else update the status
                User::where('email',$email)->update(['status'=>1]);

                // Send Welcome Email
                $messageData = ['email'=>$email,'name'=>$userDetails->name];
                Mail::send('emails.welcome',$messageData,function($message) use($email){
                    $message->to($email)->subject('Welcome to E-com Website');
                });

                return redirect('login_register')->with('flash_message_success','Your Email account is activated. You can login now.');
            }
        }else{
            abort(404);
        }
    }

    public function account(Request $request)
    {
        $user_id = Auth::user()->id;
        $userDetails = User::find($user_id);
        $countries = Country::get();

        if($request->isMethod('post')){
            $data = $request->all();
            //echo "<pre>"; print_r($data); die;

            if(empty($data['name'])){
                return redirect()->back()->with('flash_message_error','Please enter your Name to update your account details!');    
            }

            if(empty($data['address'])){
                $data['address'] = '';    
            }

            if(empty($data['city'])){
                $data['city'] = '';    
            }

            if(empty($data['state'])){
                $data['state'] = '';    
            }

            if(empty($data['country'])){
                $data['country'] = '';    
            }

            if(empty($data['pincode'])){
                $data['pincode'] = '';    
            }

            if(empty($data['mobile'])){
                $data['mobile'] = '';    
            }

            $user = User::find($user_id);
            $user->name = $data['name'];
            $user->address = $data['address'];
            $user->city = $data['city'];
            $user->state = $data['state'];
            $user->country = $data['country'];
            $user->pincode = $data['pincode'];
            $user->mobile = $data['mobile'];
            $user->save();
            return redirect()->back()->with('flash_message_success','Your account details has been successfully updated!');
        }           
        return view('users.account')->with(compact('countries','userDetails'));
    }

    public function chkUserPassword(Request $request)
    {
        $data = $request->all();
        //echo "<pre>"; print_r($data); die;
        $current_password = $data['current_pwd'];
        $user_id = Auth::User()->id;
        $check_password = User::where('id',$user_id)->first();
        if(Hash::check($current_password,$check_password->password)){
            echo "true"; die;
        }else{
            echo "false"; die;
        }
    }

    public function forgotPassword(Request $request)
    {
        if($request->isMethod('post')){
            $data = $request->all();
            /*echo "<pre>"; print_r($data); die;*/
            //check if email exists
            $userCount = User::where('email',$data['email'])->count();
            if($userCount == 0){
                return redirect()->back()->with('flash_message_error','Email does not exists!');
            }

            //Get User Details
            $userDetails = User::where('email',$data['email'])->first();

            //Generate Random Password
            $random_password = str_random(8);

            //Encode/Secure Password
            $new_password = bcrypt($random_password);

            //Update Password
            User::where('email',$data['email'])->update(['password'=>$new_password]);

            //Send Forgot Password Email Code
            $email = $data['email'];
            $name = $userDetails->name;
            $messageData = [
                'email'=>$email,
                'name'=>$name,
                'password'=>$random_password
            ];
            Mail::send('emails.forgotpassword',$messageData,function($message)use($email){
                $message->to($email)->subject('New Password - E-com Website');
            });

            return redirect('login_register')->with('flash_message_success','Please check your email for new password!');

        }
        return view('users.forgot_password');
    }

    public function updatePassword(Request $request)
    {
        if($request->isMethod('post')){
            $data = $request->all();
            /*echo "<pre>"; print_r($data); die;*/
            $old_pwd = User::where('id',Auth::User()->id)->first();
            $current_pwd = $data['current_pwd'];
            if(Hash::check($current_pwd,$old_pwd->password)){
                // Update password
                $new_pwd = bcrypt($data['new_pwd']);
                User::where('id',Auth::User()->id)->update(['password'=>$new_pwd]);
                return redirect()->back()->with('flash_message_success',' Password updated successfully!');
            }else{
                return redirect()->back()->with('flash_message_error','Current Password is incorrect!');
            }
        }
    }

    public function logout()
    {
        Auth::logout();
        Session::forget('frontSession');
        Session::forget('session_id');
        return redirect('/');
    }

    public function checkEmail(Request $request)
    {
        // Check if User already exists
    	$data = $request->all();
        $usersCount = User::where('email',$data['email'])->count();
    		if($usersCount>0){
                echo "false";
    		}else{
                echo "true";
            }
    }

    public function viewUsers()
    {
        if(Session::get('adminDetails')['users_access']==0)
        {
            return redirect('/admin/dashboard')->with('flash_message_error','You have no access to this module');
        }
        $users = User::get();
        return view('admin.users.view_users')->with(compact('users'));
    }

    public function viewUsersCharts()
    {
        $current_month_users = User::whereYear('created_at', Carbon::now()->year)
                                    ->whereMonth('created_at', Carbon::now()->month)->count(); 
                                
        $last_month_users = User::whereYear('created_at', Carbon::now()->year)
                                    ->whereMonth('created_at', Carbon::now()->subMonth(1))->count(); 
        
        $one_month_ago_users = User::whereYear('created_at', Carbon::now()->year)
                                    ->whereMonth('created_at', Carbon::now()->subMonth(2))->count(); 
        
        $two_month_ago_users = User::whereYear('created_at', Carbon::now()->year)
                                    ->whereMonth('created_at', Carbon::now()->subMonth(3))->count(); 
        
        $three_month_ago_users = User::whereYear('created_at', Carbon::now()->year)
                                    ->whereMonth('created_at', Carbon::now()->subMonth(4))->count(); 
                                    
        $four_month_ago_users = User::whereYear('created_at', Carbon::now()->year)
                                    ->whereMonth('created_at', Carbon::now()->subMonth(5))->count(); 

        return view('admin.users.chart_users')->with(compact('current_month_users', 'last_month_users', 
        'one_month_ago_users', 'two_month_ago_users', 'three_month_ago_users','four_month_ago_users' ));
    }

    public function exportUsers()
    {
        return Excel::download(new usersExport, 'users.xlsx');
    }
    
}
