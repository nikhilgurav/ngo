<?php
/**
** WPR Template to render select input
**/

    // not run if accessed directly
    if( ! defined("ABSPATH" ) )
        die("Not Allewed");

    $fm = new WPR_InputMeta($field_meta, 'select');
    // var_dump($fm->value);
    $value = sanitize_key($fm->value);
 ?>
    <div class="col-md-<?php echo esc_attr($fm->width) ?> wpr_field_wrapper">
        <label class="wpr-field-title"><?php echo $fm->title; ?>
            <span class="wpr_field_icon" data-desc="<?php echo esc_attr($fm->desc) ?>"> *</span>
            <span class="wpr-field-desc"><?php echo $fm->desc; ?></span>
        </label>
        <div class="form-group input-group">
            <span  class="input-group-addon">
                <i class="<?php echo esc_attr($fm->icon) ?>" aria-hidden="true"></i>
            </span>  
            <p 
                class="wpr_field_selector" 
                data-key="<?php echo esc_attr($fm->data_name) ?>" 
                data-type="select" 
            >
                <select
                style="border-left: white;" 
                    name="<?php echo esc_attr($fm->input_name) ?>" 
                    id="<?php echo esc_attr($fm->data_name) ?>" 
                    class="<?php echo esc_attr($fm->classes_fm); ?>" 
                    data-req="<?php echo esc_attr($fm->required) ?>" 
                    data-message="<?php echo esc_attr($fm->error_msg) ?>"
                >
                <option value=""></option>
                <?php 
                    foreach ($fm->options as $option) {

                        $key = $option['key'];
                        $label = $option['label'];
                        $opt_id = $option['option_id'];
                ?>
                        <option 
                            value="<?php echo esc_attr($key) ?>" 
                            <?php selected( $key, $value); ?> 
                        >
                            <?php echo $label; ?>
                        </option>
                <?php
                    }
                ?>
                </select>
            </p>
        </div>
        <span class="wpr_field_error"></span>
    </div>