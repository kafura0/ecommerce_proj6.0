<?php

namespace App\Http\Controllers;
use App\Coupon; 

use Illuminate\Http\Request;

class CouponsController extends Controller
{
    //
    public function addCoupon(Request $request){
		if($request->isMethod('post')){
			$data = $request->all();
			//echo "<pre>"; print_r($data); die;
			$coupon = new Coupon;
			$coupon->coupon_code = $data['coupon_code'];	
			$coupon->amount_type = $data['amount_type'];	
			$coupon->amount = $data['amount'];
			$coupon->expiry_date = $data['expiry_date'];
			$coupon->status = $data['status'];
			$coupon->save();	
			return redirect()->action('CouponsController@viewCoupons')->with('flash_message_success', 'Coupon has been added successfully');
		}
		return view('admin.coupons.add_coupon');
    }  
    
    public function viewCoupons(Request $request)
    {
		$coupons = Coupon::get();
		//$coupons= json_decode(json_encode($coupons));
		//echo "<pre>"; print_r($coupons); die;
        return view('admin.coupons.view_coupons')->with(compact('coupons'));
	}
	
	public function editCoupon(Request $request,$id=null){
		if($request->isMethod('post')){
			$data = $request->all();
			/*echo "<pre>"; print_r($data); die;*/
			$coupon = Coupon::find($id);
			$coupon->coupon_code = $data['coupon_code'];	
			$coupon->amount_type = $data['amount_type'];	
			$coupon->amount = $data['amount'];
			$coupon->expiry_date = $data['expiry_date'];
			if(empty($data['status'])){
				$data['status'] = 0;
			}
			$coupon->status = $data['status'];
			$coupon->save();	
			return redirect()->action('CouponsController@viewCoupons')->with('flash_message_success', 'Coupon has been updated successfully');
		}
		$couponDetails = Coupon::find($id);
		/*$couponDetails = json_decode(json_encode($couponDetails));
		echo "<pre>"; print_r($couponDetails); die;*/
		return view('admin.coupons.edit_coupon')->with(compact('couponDetails'));
	} 

	public function deleteCoupon($id = null){
        Coupon::where(['id'=>$id])->delete();
        return redirect()->back()->with('flash_message_success', 'Coupon has been deleted successfully');
	}
	

	
}
