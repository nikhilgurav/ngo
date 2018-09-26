"use strict";
jQuery(function($){
	
	$( '.wpr-pr-change-photo').hide();
	$( '.wpr-pr-coverupload').hide();
	$( ".wpr-pr-userphotp" ).mouseenter(function() {
    	$( '.wpr-pr-change-photo').show();
  	});	
	$( ".wpr-pr-userphotp" ).mouseleave(function() {
		$( '.wpr-pr-change-photo').hide();
  	});
  	
	$( ".wpr-pr-coverphoto" ).mouseenter(function() {
    	$( '.wpr-pr-coverupload').show();
  	});	
	$( ".wpr-pr-coverphoto" ).mouseleave(function() {
		$( '.wpr-pr-coverupload').hide();
  	});

  	// user delete account 

  	$(document).on('click', '.wpr_user_delete_account', function(e) {
  		e.preventDefault();
  		var user_id = jQuery('#wpr_delete_account').val();
  		var data = {	
						'action': 'delete_user_account', 
						// 'wpr_nonce': wpr_nonce, 
						'user_id': user_id
					};
  		$.post(wpr_vars.ajax_url, data, function(resp) {
  		
				WPR.alert(resp.message, resp.status, function(){ 
	                   location.reload();
        		});

			}, 'json');
  	});
	// change password setting
	$('.wpr-pr-pass-wrapper').find('.wpr-pass-alert').hide();
    $(document).on('click', '.wpr-change-pass', function(e) {

    	e.preventDefault();
		jQuery(".wpr-pass-alert").html('<img src="' + wpr_vars.loading + '">').css('border-left','none').show();

		var has_error 		= false;		
		var oldpassword 	= jQuery('#old_password').val();
		var newpassword 	= jQuery('#new_password').val();
		var renewpassword 	= jQuery('#re_new_password').val();
		var wpr_nonce 		= jQuery('#wpr_change_pass_nonce').val();

		if (oldpassword == ''){
			jQuery(".wpr-pass-alert").html( wpr_vars.strings.old_password_empty ).css('border-left','7px solid red').show();
			has_error = true;
		}
		else if(newpassword == ''){
			jQuery(".wpr-pass-alert").html( wpr_vars.strings.new_password_empty ).css('border-left','7px solid red').show();
			has_error = true;
		}
		else if(newpassword != renewpassword){
			jQuery(".wpr-pass-alert").html( wpr_vars.strings.new_password_not_match ).css('border-left','7px solid red').show();
			has_error = true;
		}
		
		if (!has_error){
			var data = {	
						'action': 'profile_change_password', 
						'wpr_nonce': wpr_nonce, 
						'old_password': oldpassword,
						'new_password': newpassword
					   };
					   
			$.post(wpr_vars.ajax_url, data, function(data) {
				

				WPR.alert(data.message, data.status, function(){ 
	                if (data.status == 'error') {
	                    jQuery('.wpr-pr-pass-wrapper').find('.wpr-pass-alert').hide();
	                }else{
	                   location.reload();
	                }
        		});

			}, 'json');
		}
  	});
});