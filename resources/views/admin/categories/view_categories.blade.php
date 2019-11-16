@extends('layouts.adminLayout.admin_design')
@section('content')

<div id="content">
    <div id="content-header">
        <div id="breadcrumb"> <a href="index.html" title="Go to Home" class="tip-bottom"><i class="icon-home"></i> Home</a> <a href="#">Categories</a> <a href="#" class="current">View Categories</a> </div>
        <h1>Categories</h1>
        <!----- display error or success message of category update----->
          @if(Session::has('flash_message_error'))
            <div class="alert alert-error alert-block">
              <button type="button" class="close" data-dismiss="alert">×</button> 
              <strong>{!! session('flash_message_error') !!}</strong>
            </div>
          @endif   
          @if(Session::has('flash_message_success'))
            <div class="alert alert-success alert-block">
              <button type="button" class="close" data-dismiss="alert">×</button> 
              <strong>{!! session('flash_message_success') !!}</strong>
            </div>
          @endif
        <!----------end of display error or success message of category update--------------->
    </div>
    <div class="container-fluid">
      <hr>
      <div class="row-fluid">
        <div class="span12">
          <div class="widget-box">
            <div class="widget-title"> <span class="icon"><i class="icon-th"></i></span>
              <h5>View Categories</h5>
            </div>
            <div class="widget-content nopadding">
              <table class="table table-bordered data-table">
                <thead>
                  <tr>
                    <th>Category ID</th>
                    <th>Category Name</th>
                    <th>Category Level</th>
                    <th>Category URL</th>
                    <th> Status </th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody>
                    @foreach($categories as $category)
                    <tr class="gradeX">
                        <td> {{ $category->id }} </td>
                        <td> {{ $category->name }} </td>
                        <td> {{ $category->parent_id }} </td>
                        <td> {{ $category->url }} </td>
                        <td class="center"> 
                          <input class="center" type="checkbox" name="enable" id="enable" @if($category->status=="1") checked @endif value="1" }} disabled>
                        </td>
                        
                        <td class="center">
                          @if(Session::get('adminDetails')['categories_edit_access']==1)
                            <a href="{{ url('/admin/edit_category/'.$category->id) }}" class="btn btn-primary btn-mini">Edit</a> 
                          @endif
                          @if(Session::get('adminDetails')['categories_full_access']==1)
                            <a rel="{{ $category->id }}" rel1="delete_category" <?php  ?>href="javascript:" class="btn btn-danger btn-mini deleteRecord">Delete</a>
                          @endif
                          </td>

                          {{-- <a id="delCat" href="{{ url('/admin/delete_category/'.$category->id) }}" class="btn btn-danger btn-mini">Delete</a> --}}
                    </tr>
                    @endforeach
                  
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

@endsection