<?php
/**
** WPR Template to render autocomplete input
**/

    // not run if accessed directly
    if( ! defined("ABSPATH" ) )
        die("Not Allewed");
    
    $fm = new WPR_InputMeta($field_meta, 'autocomplete');

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
                data-type="autocomplete" 
            >
                <select 
                    multiple="multiple" 
                    name="<?php echo esc_attr($fm->input_name_multiple) ?>" 
                    id="<?php echo esc_attr($fm->data_name) ?>" 
                    class="<?php echo esc_attr($fm->classes_fm); ?>" 
                    data-req="<?php echo esc_attr($fm->required) ?>" 
                    data-message="<?php echo esc_attr($fm->error_msg) ?>" 
                >
                <?php 
                    foreach ($fm->options as $option) {

                        $key = $option['key'];
                        $label = $option['label'];
                        $opt_id = $option['option_id'];
                    
                        $selected = '';
                        if( !empty($fm->value) ) {
                            $selected = in_array($key, $fm->value) ? 'selected="selected"' : '';
                        }
                ?>
                        <option 
                            value="<?php echo esc_attr($key) ?>" 
                            <?php echo $selected; ?> 
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

<script>
    jQuery(document).ready(function($){

        $('#<?php echo $fm->data_name; ?>').select2({
            placeholder: '<?php echo $fm->placeholder; ?>',
            maximumSelectionLength: '<?php echo $fm->max_select; ?>',
            width:"100%",
        });
    });
</script>