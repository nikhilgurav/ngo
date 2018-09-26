<?php
// require ReCaptcha class
require( WPR_PATH.'/lib/recaptcha/vendor/google/recaptcha/src/autoload.php');



// add ReCaptcha hook
function wpr_hooks_render_recaptcha(){

    $site_key = WPR_Settings()->get_option("site_key");

    $recaptcha_enable = wpr_recaptcha_enable_setting();

    // wpr_pa($recaptcha_enable);
    if ($recaptcha_enable == true) {
        
        wp_enqueue_script('WPR-recaptcha-js', "https://www.google.com/recaptcha/api.js", array('jquery'), WPR_VERSION, true);
        echo '<div class="col-md-12 col-sm-12">';
        echo '<div class="form-group wpr-recaptcha">';
            echo '<div class="g-recaptcha" data-sitekey="'.esc_attr($site_key) . '" data-callback="verifyRecaptchaCallback" data-expired-callback="expiredRecaptchaCallback"></div>';
        echo '</div>';
        echo '</div>';
    }
}


function wpr_verify_recaptcha() {

// ReCaptch Secret
$recaptchaSecret = WPR_Settings()->get_option("recapcta_secret_key");


// if you are not debugging and don't need error reporting, turn this off by error_reporting(0);
// error_reporting(E_ALL & ~E_NOTICE);

$responseArray = array();
try {
    if (!empty($_POST)) {

        // validate the ReCaptcha, if something is wrong, we throw an Exception,
        // i.e. code stops executing and goes to catch() block
        
        if (!isset($_POST['g-recaptcha-response'])) {
            throw new \Exception('ReCaptcha is not set.');
        }

        // do not forget to enter your secret key from https://www.google.com/recaptcha/admin
        
        $recaptcha = new \ReCaptcha\ReCaptcha($recaptchaSecret, new \ReCaptcha\RequestMethod\CurlPost());
        
        // we validate the ReCaptcha field together with the user's IP address
        
        $response = $recaptcha->verify($_POST['g-recaptcha-response'], $_SERVER['REMOTE_ADDR']);

        if (!$response->isSuccess()) {
            throw new \Exception('ReCaptcha was not validated.');
        }
        
        $responseArray = array('status' => 'success', 'message' => '');
    }
} catch (\Exception $e) {
    $responseArray = array('status' => 'error', 'message' => $e->getMessage());
}

return $responseArray;
}