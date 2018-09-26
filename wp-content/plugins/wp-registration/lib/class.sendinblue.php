<?php
/**
 * SendinBlue API Class
 * 
 * @since 2.4
 **/

if( ! class_exists('Mailin') ) {
	require( WPR_PATH . "/lib/sendinblue/vendor/mailin-api/mailin-api-php/V2.0/Mailin.php");
}

class WPR_SendinBlue {
    
    private static $ins = null;
    private static $apikey = null;
    
    private static $sb_ins = null;
    
    function __construct() {
        
        self::$apikey    = WPR_Settings()->get_option('sb_api_key');
	    $this -> save_lists = WPR_Settings()->get_option('sb_list');
        $this -> sb_heading = WPR_Settings()->get_option('sb_heading');
        $this -> enable  = WPR_Settings()->get_option('sb_enable') == 'on' ? true : false;
        $this -> sub_type   = WPR_Settings()->get_option('sb_subscription_type');

	   
        add_action('wpr_before_submit_button', array($this, 'show_lists'), 10, 1);

    }
    
    public static function get_instance() {
		// create a new object if it doesn't exist.
		is_null(self::$ins) && self::$ins = new self;
		return self::$ins;
	}
	
	public static function get_sb_instance() {
		// create a new object if it doesn't exist.
		if( ! is_null(self::$sb_ins) )
		    return self::$sb_ins;
		
		self::$sb_ins = new Mailin('https://api.sendinblue.com/v2.0', trim(self::$apikey), 5000);    //Optional parameter: Timeout in MS
		
		return self::$sb_ins;
	}

    // Render list front end
    function show_lists( $form ) {
        
        if( ! $this->is_show_list() ) return '';
        
        $sb_lists = $this -> get_lists();
        
        // wpr_pa($list_id['id']);
        echo '<div class="wpr-subscriber-wrapper form-group input-group">';
        echo '<p><strong class="wpr-subcriber-heading">'.$this -> sb_heading.'';
        echo '</strong></p>';
        foreach ($sb_lists as $list_ind => $list_id) {
            foreach ($this -> save_lists as $saved_id ) {
                if ($list_id['id'] == $saved_id) {
                echo '<label for="'.esc_attr($saved_id).'" class="wpr-subscriber-mr wpr-radio-check">';
                  echo '<input  type="checkbox" name="wpr[sb_list]['.esc_attr($saved_id).']" id="'.esc_attr($saved_id).'">';
                    echo $list_id['name'];
                echo '</label>';
                }
            }
        }
        echo "</div>";
    }
    
    function is_show_list() {
        
        $return = false;
        if( $this -> enable && $this -> sub_type == 'user_select' && $this->save_lists !== '' ) {
            
            $return = true;
        }
        
        return apply_filters('wpr_show_sb_list', $return, $this);
    }
	
    function get_lists() {
        
        $resp = array();
        
        try {
        
            $data = array('page'=>1, 'page_limit'=>3);
            $sb_lists = self::get_sb_instance()->get_lists( $data );
            // wpregistration_pa($sb_lists);
            if( $sb_lists['code'] != 'success' ) {
                $sb_lists = null;
            } else {
                
                $resp = $sb_lists['data']['lists'];
            }
            
        } catch (Exception $e) {
	
			$resp = new WP_Error( 'api_error', sprintf(__( "%s", "wpr" ), $e->getMessage()) );
	
	    }
        
        return $resp;
    }
    
    
    function handle_subscription( $user_id, $sb_lists) {
        
        // if list is not given then use from settings
        $sb_lists = $this->get_subscription_lists( $sb_lists );
        if( empty($sb_lists) ) return '';
        
        
        $user = new WPR_User( $user_id );
        
        if( empty($user->email) ) return '';
        
        
        $sb_lists = array_map('intval', $sb_lists);
        
       	$resp = '';
        
        $fullname = $user->first_name . ' ' .$user->last_name;
        $data = array( "email" => $user->email,
            "attributes" => array("NAME" => $fullname, "FIRSTNAME"=>$user->first_name, "SURNAME"=>$user->last_name),
            "listid" => $sb_lists,
        );
        
    
        $sub_resp = self::get_sb_instance()->create_update_user($data);
        return $sub_resp;
    }
    
    // Following function handle subscription type and return lists for both types
    function get_subscription_lists( $lists_frontend ) {
        
        $sb_lists = array();
        
        if( ! empty( $lists_frontend )) {
            
            foreach($lists_frontend as $list_id => $on) {
                
                $sb_lists[] = $list_id;
            }
        }
        if( $this->enable && $this->sub_type == 'auto_sub' ) {
            
            $sb_lists = $this->list_id;
        }
        
        return apply_filters('wpr_sb_subscription_lists', $sb_lists);
    }
}

WPRSB();
function WPRSB() {
	return WPR_SendinBlue::get_instance();
}