<?php
/**
** WPR Template to render Wordpress input
**/

    // not run if accessed directly
    if( ! defined("ABSPATH" ) )
        die("Not Allewed");

    $fm = new WPR_InputMeta($field_meta, 'wp_field');
    $field_type = $fm->data_name;
    $confirm_password_setting = $fm->confirm_password;

    $accept_weak_pass = $fm->accept_weak_password;

    if ($field_type == 'password') {
        $input_type = 'password';
        $html = '';
        $html .= '<span id="password_result"></span>';

    }else {
        $input_type = 'text';
        $html = '';
    }
 

 ?>
    <div class="col-md-<?php echo esc_attr($fm->width) ?> wpr_field_wrapper">
        <label class="wpr-field-title"><?php echo $fm->title; ?>
            <span class="wpr_field_icon" data-desc="<?php echo esc_attr($fm->desc) ?>"> *
            </span>
            <span class="wpr-field-desc"><?php echo esc_attr($fm->desc) ?></span>
        </label>
        <div class="form-group input-group">
            <span  class="input-group-addon">
                <i class="<?php echo esc_attr($fm->icon) ?>" aria-hidden="true"></i>
            </span>
            <p 
                class="wpr_field_selector" 
                data-key="<?php echo esc_attr($fm->data_name) ?>" 
                data-type=<?php echo esc_attr($input_type) ?> 
            >
                <input 
                    type=<?php echo esc_attr($input_type) ?> 
                    name="<?php echo esc_attr($fm->input_name) ?>" 
                    id="<?php echo esc_attr($fm->data_name) ?>" 
                    class="<?php echo esc_attr($fm->classes_fm);?>" 
                    placeholder="<?php echo esc_attr($fm->placeholder) ?>" 
                    data-req="<?php echo esc_attr($fm->required) ?>" 
                    data-message="<?php echo esc_attr($fm->error_msg) ?>" 
                    data-pass="<?php echo esc_attr($fm->accept_weak_password) ?>"
                    value="<?php echo esc_attr($fm->value);?>" 
                > 
            </p>
        </div>
            <?php echo $html; ?>
            <?php  if ($confirm_password_setting == 'on') { ?>
            <label class="wpr-field-title"> <?php _e('Confirm Password' , 'wp-registration'); ?></label>
                <div class="form-group input-group">
                    <span  class="input-group-addon">
                        <i class="<?php echo esc_attr($fm->icon) ?>" aria-hidden="true"></i>
                    </span>
                    <p 
                        class="wpr_field_selector" 
                        data-key="<?php echo esc_attr($fm->data_name) ?>" 
                        data-type="text" 
                    >
                    <?php
                        echo '<input type="password"  class="'.esc_attr($fm->classes_fm) .'" name="'.esc_attr($fm->confirm_pass_name).'" id="wpr_confirm_password" data-req="on" data-message="Confirm password must be required"/>';
                    ?>
                    </p>
                </div>
            <?php } ?>
        <span class="wpr_field_error"></span>
    </div>