
$(document).ready(function(){
	$("#access").hide();
	$("#type").change(function(){
		var type = $("#type").val();
		// alert(type);
		if(type == "Admin")
		{
			$("#access").hide();
		}else{
			$("#access").show();
		}
	});

	//update password javascript
	//alert('test');
	$("#current_pwd").keyup(function(){
		var current_pwd = $("#current_pwd").val();
		// alert(current_pwd);
		$.ajax({
			type: 'get',
			url: '/admin/check-pwd',
			data: {current_pwd:current_pwd},
			success:function(resp){
				//alert(resp);
				if(resp=="false"){
					$("#chkPwd").html("<font color='red'>Current Password is incorrect</font>");
				}else if(resp=="true"){
					$("#chkPwd").html("<font color='green'>Current Password is correct</font>");
				}
			},error:function(){
				alert("Error");
			}
		});
	});

	//password validation
	$("#password_validate").validate({
		rules:{
			current_pwd:{
				required: true,
				minlength: 6,				
				maxlength:20
			},
			new_pwd:{
				required: true,
				minlength: 6,
				maxlength:20
			},
			confirm_pwd:{
				required:true,
				minlength: 6,
				maxlength:20,
				equalTo:"#new_pwd"
			}
		},
		errorClass: "help-inline",
		errorElement: "span",
		highlight:function(element, errorClass, validClass) {
			$(element).parents('.control-group').addClass('error');
		},
		unhighlight: function(element, errorClass, validClass) {
			$(element).parents('.control-group').removeClass('error');
			$(element).parents('.control-group').addClass('success');
		}
	});
	
	$('input[type=checkbox],input[type=radio],input[type=file]').uniform();
	
	$('select').select2();
	
	// Form Validation
    $("#basic_validate").validate({
		rules:{
			required:{
				required:true
			},
			email:{
				required:true,
				email: true
			},
			date:{
				required:true,
				date: true
			},
			url:{
				required:true,
				url: true
			}
		},
		errorClass: "help-inline",
		errorElement: "span",
		highlight:function(element, errorClass, validClass) {
			$(element).parents('.control-group').addClass('error');
		},
		unhighlight: function(element, errorClass, validClass) {
			$(element).parents('.control-group').removeClass('error');
			$(element).parents('.control-group').addClass('success');
		}
	});

	//--------------category validations----------------------
	//Add Category Validation
	$("#add_category").validate({
		rules:{
			category_name:{
				required:true
			},
			descrption:{
				required:true,
			},
			url:{
				required:true,
			}
		},
		errorClass: "help-inline",
		errorElement: "span",
		highlight:function(element, errorClass, validClass) {
			$(element).parents('.control-group').addClass('error');
		},
		unhighlight: function(element, errorClass, validClass) {
			$(element).parents('.control-group').removeClass('error');
			$(element).parents('.control-group').addClass('success');
		}
	});
	//delete category confirmation
	// $("#delCat").click(function(){
	// 	if(confirm('Are you sure you want to delte this category?')){
	// 		return true;
	// 	}
	// 	return false;
	// });
	//--------------end of category validations--------------------

	//----------------product validations-------------------------
	//add product validations
    $("#add_product").validate({
		rules:{
			category_id:{
				required:true,
			},
			product_name:{
				required:true,
			},
			product_code:{
				required:true,	
			},
			product_color:{
				required:true,
			},
			description:{
				required:true,
			},
			price:{
				required:true,
				number:true
			},
			image:{
				required: true,
			}	
		},
		errorClass: "help-inline",
		errorElement: "span",
		highlight:function(element, errorClass, validClass) {
			$(element).parents('.control-group').addClass('error');
		},
		unhighlight: function(element, errorClass, validClass) {
			$(element).parents('.control-group').removeClass('error');
			$(element).parents('.control-group').addClass('success');
		}
	});
	//edit product validations
    $("#edit_product").validate({
		rules:{
			category_id:{
				required:true,
			},
			product_name:{
				required:true,
			},
			product_code:{
				required:true,	
			},
			product_color:{
				required:true,
			},
			description:{
				required:true,
			},
			price:{
				required:true,
				number:true
			}
			// image:{
			// 	required: true,
			// }	
		},
		errorClass: "help-inline",
		errorElement: "span",
		highlight:function(element, errorClass, validClass) {
			$(element).parents('.control-group').addClass('error');
		},
		unhighlight: function(element, errorClass, validClass) {
			$(element).parents('.control-group').removeClass('error');
			$(element).parents('.control-group').addClass('success');
		}
	});
	// //simple delete product confirmation alert
	// $("#delProduct").click(function(){
	// 	if(confirm('Are you sure you want to delete this product?')){
	// 		return true;
	// 	}
	// 	return false;
	// });

	//sweet alert delete product confirmation - better
	$(document).on('click', '.deleteRecord', function(e){
		var id = $(this).attr('rel');
		var deleteFunction = $(this).attr('rel1');
        swal({
          title: "Are you sure?",
          text: "Your will not be able to recover this Record Again!",
          type: "warning",
          showCancelButton: true,
          confirmButtonClass: "btn-danger",
          confirmButtonText: "Yes, delete it!",
          closeOnConfirm: false
        },
        function(){
            window.location.href="/admin/"+deleteFunction+"/"+id;
        });
	});

	//add product attribute jquery
	//source: https://www.codexworld.com/add-remove-input-fields-dynamically-using-jquery/
	$(document).ready(function(){
	    var maxField = 10; //Input fields increment limitation
	    var addButton = $('.add_button'); //Add button selector
	    var wrapper = $('.field_wrapper'); //Input field wrapper
		var fieldHTML ='<div class="controls field_wrapper" style="margin-left:-2px;"><input type="text" name="sku[]" style="width:120px"/>&nbsp;<input type="text" name="size[]" style="width:120px"/>&nbsp;<input type="text" name="price[]" style="width:120px"/>&nbsp;<input type="text" name="stock[]" style="width:120px"/><a href="javascript:void(0);" class="remove_button" title="Remove field">Remove</a></div>'; //New input field html 
	    var x = 1; //Initial field counter is 1
	    $(addButton).click(function(){ //Once add button is clicked
	        if(x < maxField){ //Check maximum number of input fields
	            x++; //Increment field counter
	            $(wrapper).append(fieldHTML); // Add field html
	        }
	    });
	    $(wrapper).on('click', '.remove_button', function(e){ //Once remove button is clicked
	        e.preventDefault();
	        $(this).parent('div').remove(); //Remove field html
	        x--; //Decrement field counter
	    });
	});


	
	$("#number_validate").validate({
		rules:{
			min:{
				required: true,
				min:10
			},
			max:{
				required:true,
				max:24
			},
			number:{
				required:true,
				number:true
			}
		},
		errorClass: "help-inline",
		errorElement: "span",
		highlight:function(element, errorClass, validClass) {
			$(element).parents('.control-group').addClass('error');
		},
		unhighlight: function(element, errorClass, validClass) {
			$(element).parents('.control-group').removeClass('error');
			$(element).parents('.control-group').addClass('success');
		}
	});

	
});
