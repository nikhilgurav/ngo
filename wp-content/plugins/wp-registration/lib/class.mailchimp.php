<?php
/**
 * MailChimp API Class
 * 
 * @since 2.4
 **/

if( ! class_exists('MailChimp') ) {
	include_once WPR_PATH . "/lib/mailchimp/vendor/autoload.php";
}

use \DrewM\MailChimp\MailChimp;
use \DrewM\MailChimp\Batch;

class WPR_MailChimp {
    
    private static $ins = null;
    private static $apikey = null;
    private static $ins_mc = null;
    
    function __construct() {
        
        self::$apikey       = WPR_Settings()->get_option('mc_api_key');
	    $this -> list_id    = WPR_Settings()->get_option('mc_list');
	    $this -> mc_heading = WPR_Settings()->get_option('mc_heading');
	    $this -> enable     = WPR_Settings()->get_option('mc_enable') == 'on' ? true : false;
	    $this -> sub_type   = WPR_Settings()->get_option('mc_subscription_type');
	    
	    
	    add_action('wpr_before_submit_button', array($this, 'show_lists'), 10, 1);
    }
    
    public static function get_instance() {
	    // create a new object if it doesn't exist.
		is_null(self::$ins) && self::$ins = new self;
		return self::$ins;
	}
	
	public static function get_mc_instance() {
		// create a new object if it doesn't exist.
		if( ! is_null(self::$ins_mc) )
		    return self::$ins_mc;
		
		try {	
				
        		self::$ins_mc = new MailChimp(self::$apikey);
	            
		    } catch (Exception $e) {
		
				add_action( 'admin_notices', 'wpr_error_mailchimp_apikey' );
		
		    }
		return self::$ins_mc;
	}
	
    // Render list front end
    function show_lists( $form ) {

        // wpr_pa($list_id['id']);
        if( ! $this->is_show_list() ) return '';
        
        $mc_lists = $this -> get_lists();
        
        echo '<div class="wpr-subscriber-wrapper form-group input-group">';
        echo '<p><strong class="wpr-subcriber-heading">'.$this -> mc_heading.'';
        echo '</strong></p>';
       foreach ($mc_lists as $list_ind => $list_id) {
            foreach ($this -> list_id as $saved_id ) {
                if ($list_id['id'] == $saved_id) {
                echo '<label for="'.esc_attr($saved_id).'" class="wpr-subscriber-mr wpr-radio-check">';
                  echo '<input type="checkbox" name="wpr[mc_list]['.esc_attr($saved_id).']" id="'.esc_attr($saved_id).'">';
                    echo $list_id['name'];
                echo '</label>';
                }
            }   
        }
        echo "</div>";
    }
    
    function is_show_list() {
        
        $return = false;
        if( $this -> enable && $this -> sub_type == 'user_select' && $this->list_id !== '' ) {
            
            $return = true;
        }
        
        return apply_filters('wpr_show_mc_list', $return, $this);
    }
    
    
    function get_lists() {
        
        if( empty(self::$apikey) ) return null;
        
        $mc_lists = array();
	 	// fetchin lists
	 	$lists = self::get_mc_instance()->get('lists');
	 	$lists = $lists['lists'];
	 	
	 	if( !empty($saved_lists) ) {
	 		foreach($lists as $list){
	 			
	 			if( in_array($list['id'], $saved_lists) ) {
	 				$mc_lists[] = $list;
	 			}
	 		}
	 	} else {
	 		
	 		$mc_lists = $lists;
	 	}
	 	

		return $mc_lists;
    }
    
    
    function handle_subscription($user_id, $lists) {
        
        // if list is not given then use from settings
        $mc_lists = $this->get_subscription_lists( $lists );
        if( empty($mc_lists) ) return '';
        
        
        $user = new WPR_User( $user_id );
        
        if( empty($user->email) ) return '';
        
       	$resp = '';
		
		try {
			
			$Batch     = self::get_mc_instance()->new_batch();
			
			$opr_id = '';
			$total_list_subscribed = 0;
	 		foreach($mc_lists as $key => $listid) {
	 			
	 			$opr_id = "opr_".$key;
	 			$sub_data = array(	'email_address' => $user->email, 
	 								'status' => 'subscribed', 
	 								'email_type' => 'html',
	 								'merge_fields' => array('FNAME' => $user->first_name, 'LNAME' => $user->last_name)
	 							);
	 			
	 			$Batch -> post($opr_id, "lists/$listid/members", $sub_data);
	 			$total_list_subscribed++;
	
	 		}
		 	
		 	$response = $Batch->execute();
		 	if ($total_list_subscribed > 0) {
		 		$message = sprintf(__('Subscribed to %d lists', 'wpr'), $total_list_subscribed);
			    $resp = array('status' => 'success', 'message' => $message);
			}

            
	    } catch (Exception $e) {
	
			$resp = new WP_Error( 'api_error', sprintf(__( "%s", "wpr" ), $e->getMessage()) );
	
	    }
	    
	    return $resp;
    }
    
    // Following function handle subscription type and return lists for both types
    function get_subscription_lists( $lists_frontend ) {
        
        $mc_lists = array();
        
        if( ! empty( $lists_frontend )) {
            
            foreach($lists_frontend as $list_id => $on) {
                
                $mc_lists[] = $list_id;
            }
        }
        if( $this->enable && $this->sub_type == 'auto_sub' ) {
            
            $mc_lists = $this->list_id;
        }
        
        return apply_filters('wpr_mc_subscription_lists', $mc_lists);
    }
    
}

WPRMC();
function WPRMC() {
	return WPR_MailChimp::get_instance();
}