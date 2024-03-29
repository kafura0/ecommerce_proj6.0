<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\NewsletterSubscriber;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\subscribersExport;

class NewsletterController extends Controller
{
    public function checkSubscriber(Request $request)
    {
        if($request->ajax())
        {
            $data = $request->all();
            // echo "<pre>"; print_r($data); die;
            $subscriberCount = NewsletterSubscriber::where('email', $data['subscriber_email'])->count();
            if($subscriberCount>0)
            {
                echo "exists"; 
            }
        }
    }

    public function addSubscriber(Request $request)
    {
        if($request->ajax())
        {
            $data = $request->all();
            // echo "<pre>"; print_r($data); die;
            $subscriberCount = NewsletterSubscriber::where('email', $data['subscriber_email'])->count();
            if($subscriberCount>0)
            {
                echo "exists"; 
            }else{
                //add newsletter in newsleter_subscribers table
                $newsletter = new NewsletterSubscriber;
                $newsletter->email = $data['subscriber_email'];
                $newsletter->status = 1;
                $newsletter->save();
                echo "saved";
            }
        }
    }

    public function viewNewsletterSubscribers()
    {
        $newsletters = NewsletterSubscriber::get();
        return view('admin.newsletters.view_newsletters_subscribers')->with(compact('newsletters'));
    }

    public function updateNewsletterStatus($id, $status)
    {
        NewsletterSubscriber::where('id', $id)->update(['status'=>$status]);
        return redirect()->back()->with('flash_message_success', 'Subscriber status has been updated');
    }

    public function deleteNewsletterEmail($id)
    {
        NewsletterSubscriber::where('id', $id)->delete();
        return redirect()->back()->with('flash_message_success', 'Newsletter Subscriber deleted');
    }

    // public function exportNewsletterEmails()
    // {
    //     $subscribersData = NewsletterSubscriber::select('id', 'email', 'created_at')->where('status', 1)->orderBy('id', 'Desc')->get();
    //     $subscribersData = json_decode(json_encode($subscribersData), true);
    //     // echo "<pre>"; print_r($subscriberData); die;

    //     return Excel::create('subscribers'.rand(), function($excel) use($subscribersData){
    //         $excel->sheet('mySheet', function($sheet) use($subscribersData){
    //             $sheet->fromArray($subscribersData);
    //         });
    //     })->download('xlsx');
    // }

    public function exportNewsletterEmails()
    {
        return Excel::download(new subscribersExport, 'subscribers.xlsx');
    }
}