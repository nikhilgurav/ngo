<?php
/**
 * WPR_Register class will handle Member Registration
 **/
 if( ! defined("ABSPATH" ) )
        die("Not Allewed");

class WPR_Register {
    
    private static $ins = null;
    
    function __construct( $formd_id, $singup_data ) {
        
        $this -> singup_data = $singup_data;
        $this -> userid = null;
        $this -> user   = null;
        $this -> form   = new WPR_Form($formd_id);
        $this -> errors = array();
     
     
        // Adding password to core field
        add_filter( 'wpr_core_fields', 'wpr_hook_set_password', 10, 2);

        // add_action( 'wpr_after_user_created', 'wpr_new_user_notification', 10, 2);

        // add_action( 'user_register', 'verification_key', 10, 2);
        add_action( 'wpr_after_user_created', array($this, 'wpr_insert_account_email_key'), 10);
    }
    

    function create_user() {
        
        // wpr_pa($this->singup_data); exit;
        
        if( ! isset($this->singup_data['wp_field']) ) {
            
            $response = array('status'=>'error', 'message'=>__("No WP Core fields were found.", "wpr") );
            wp_send_json($response);
        }
        
        $wp_fields      = apply_filters('wpr_core_fields', $this->singup_data['wp_field'], $this);
        
        // Creating a user, if success return new user ID
        $this->userid   = wp_insert_user( $wp_fields );
    
        if ( is_wp_error( $this->userid ) ) {
            
            return $this->userid;
        }
        
        $this -> user = get_user_by( 'id', $this->userid ); 
        
        // Adding extra fields
        $this -> setting_user_meta();
        
        // Applying settings like auto login, subscription etc
        $this -> apply_settings();
        
        // Applying Role to user
        $this -> set_roles();
        
        // Seding email to user and admin
        $this -> send_emails();

        // Now everything ok, User is created with id: $this->userid
        do_action('wpr_after_user_created', $this->userid);
        
        return $this->userid;
    }

    // Set the status key and email key
    function wpr_insert_account_email_key($user_id) { 

        
        if ( wpr_is_email_verification_required() ) {

            $email_verification_key = wp_generate_password( 40, false );
           
            update_user_meta($user_id ,'wpr_account_status' , 'inactive');
            update_user_meta($user_id ,'wpr_email_key' , $email_verification_key);
            
            // sending verification link
            $context = 'email_verify';
            $email = new WPR_Email( $this->userid, $context );
            $email -> send();
            
        } else {

            update_user_meta($user_id ,'wpr_account_status' , 'active');

        }
        

    }
    
    // Adding extra hooks via hook
    function setting_user_meta() {
        
        // wpr_pa($this);
        
        // Adding form id field in meta
        $this->set_meta( 'wpr_form_id', $this->form->form_id);
        
        // Adding extra fields in meta
        foreach( $this->singup_data as $type => $fields ) {
            
            // Skipp core fields
            if( $type == 'wp_field' ) continue;
            
            foreach( $fields as $key => $value ) {
                
                $value = apply_filters('wpr_meta_value', $value, $key, $this);
                $this->set_meta( $key, $value );
            }
        }
    }
    
    
    function apply_settings() {
        
        if( $this->form->auto_login() ) {
            
        	wp_set_current_user( $this->userid, $this->user->username );
			wp_set_auth_cookie( $this->userid );
			do_action( 'wp_login', $this->user->username );
        }
        
        // If mc_list key is set then subscribe
        $mc_lists = (!empty($this->singup_data['mc_list'])) ? $this->singup_data['mc_list'] : array();
        
        $mc = new WPR_MailChimp();
        $mc_response = $mc->handle_subscription($this->userid, $mc_lists);
        
        
        // If sb_list key is set then subscribe
        $sb_lists = (!empty($this->singup_data['sb_list'])) ? $this->singup_data['sb_list'] : array();
        
        $sb = new WPR_SendinBlue();
        $sb_response = $sb->handle_subscription($this->userid, $sb_lists);
        
    }
    
    function set_roles() {
        
        $roles_defined = $this->form->get_option('wpr_assign_role');
        if( $roles_defined == '' ) return;
        
        $roles_array = explode(",",$roles_defined);
        foreach($roles_array as $role) {
            
            $this->user->add_role( sanitize_text_field($role) );
        }
    }
    
    function send_emails() {
        
        $email = new WPR_Email( $this->userid );
        if( ! $email -> send() ) {

            $this->errors[] = __("Email not sent, please contact admin", 'wpr');
        }
    }
    
    
    // Setting User's meta
    function set_meta( $key, $value ) {
        
        update_user_meta( $this->userid, $key, $value );
    }   
}