@extends('layouts.adminLayout.admin_design')
@section('content')

<div id="content">
    <div id="content-header">
      <div id="breadcrumb"> <a href="index.html" title="Go to Home" class="tip-bottom"><i class="icon-home"></i> Home</a> <a href="#">Products</a> <a href="#" class="current">Add Product Attributes</a> </div>
      <h1>Products Attributes</h1>
      <!----- display error or success message of product update----->
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
  <!----------end of display error or success message of product update--------------->
    </div>
    <div class="container-fluid"><hr>
      <div class="row-fluid">
        <div class="span12">
          <div class="widget-box">
            <div class="widget-title"> <span class="icon"> <i class="icon-info-sign"></i> </span>
              <h5>Add Product Attribute</h5>
            </div>
            <div class="widget-content nopadding">
                <form enctype="multipart/form-data" class="form-horizontal" method="post" action="{{ url('admin/add_attributes/'.$productDetails->id) }}" name="add_attribute" id="add_attribute">{{ csrf_field() }}
                    <input type="hidden" name="product_id" value="{{ $productDetails->id }}">
                    <div class="control-group">
                      {{-- <label class="control-label">Category Name</label>
                      <label class="control-label"><strong>{{ $category_name }}</strong></label> --}}
                    </div>
                    <div class="control-group">
                      <label class="control-label">Product Name</label>
                      <label class="control-label"><strong> {{ $productDetails->product_name }}</strong></label>
                    </div>
                    <div class="control-group">
                      <label class="control-label">Product Code</label>
                      <label class="control-label"><strong>{{ $productDetails->product_code }}</strong></label>
                    </div>
                    <div class="control-group">
                        <label class="control-label">Product Colour</label>
                        <label class="control-label"><strong>{{ $productDetails->product_color }}</strong></label>
                    </div>
    
                    <div class="control-group">
                        <label class="control-label" ></label>
                            <div class="controls field_wrapper">
                              <input required type="text" name="sku[]" id="sku" placeholder="SKU" style="width:120px;"/>
                              <input required type="text" name="size[]" id="size" placeholder="Size" style="width:120px;"/>
                              <input required type="text" name="price[]" id="price" placeholder="Price" style="width:120px;"/> 
                              <input required type="text" name="stock[]" id="stock" placeholder="Stock" style="width:120px;"/>
                              <a href="javascript:void(0);" class="add_button" title="Add field">Add</a>
                            </div>
                    </div>
                    <div class="form-actions">
                        <input type="submit" value="Add Attributes" class="btn btn-success">
                    </div>
              </form>
            </div>
          </div>
        </div>
      </div>
      <div class="row-fluid">
        <div class="span12">
          <div class="widget-box">
            <div class="widget-title"> <span class="icon"><i class="icon-th"></i></span>
              <h5>View Attributes</h5>
            </div>
            <div class="widget-content nopadding">
                <form action="{{ url('/admin/edit_attributes/'.$productDetails->id) }}" method="post">
                  {{ csrf_field() }}
                  <table class="table table-bordered data-table">
                    <thead>
                      <tr>
                        <th>Attribute ID</th>
                        <th>SKU</th>
                        <th>Size</th>
                        <th>Price</th>
                        <th>Stock</th>
                        <th>Actions</th>
                      </tr>
                    </thead>
                    <tbody>
                      
                      @foreach($productDetails['attributes'] as $attribute)
                      <tr class="gradeX">
                        <td class="center"><input type="hidden" name="idAttr[]" value="{{ $attribute->id }}">{{ $attribute->id }}</td>
                        <td class="center">{{  $attribute->sku }}</td>
                        <td class="center">{{  $attribute->size }}</td>
                        <td class="center"><input name="price[]" type="text" value="{{  $attribute->price }}" /></td>
                        <td class="center"><input name="stock[]" type="text" value="{{  $attribute->stock }}" required /></td> 
                        <td class="center">
                          <input type="submit" value="Update" class="btn btn-primary btn-mini" />
                          <a rel="{{ $attribute->id }}" rel1="delete_attribute" href="javascript:" class="btn btn-danger btn-mini deleteRecord">Delete</a> 
                         
                          {{-- <a href="{{ url('admin/delete_attribute/'.$attribute->id) }}" href="javascript:" class="btn btn-danger btn-mini">Delete</a> --}}
                        </td>
    
                      </tr>
                      @endforeach
                    </tbody>
                  </table>
                </form>
            </div>
          </div>
        </div>
      </div>
    </div>
</div>


@endsection