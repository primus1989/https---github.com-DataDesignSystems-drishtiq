<?php
// CPO settings tabs
add_filter('woocommerce_product_data_tabs', 'uni_cpo_add_settings_tab');
function uni_cpo_add_settings_tab( $product_data_tabs ) {
    $product_data_tabs['cpo_settings'] = array(
							        'label'  => __( 'CPO Settings', 'uni-cpo' ),
							        'target' => 'uni_cpo_settings_data',
							        'class'  => array( 'hide_if_virtual', 'hide_if_grouped', 'hide_if_external' ),
                                );
    $product_data_tabs['cpo_options'] = array(
							        'label'  => __( 'CPO Options', 'uni-cpo' ),
							        'target' => 'uni_cpo_options_data',
							        'class'  => array( 'hide_if_virtual', 'hide_if_grouped', 'hide_if_external' ),
                                );
    $product_data_tabs['cpo_discounts'] = array(
							        'label'  => __( 'CPO Cart Discounts', 'uni-cpo' ),
							        'target' => 'uni_cpo_discounts_data',
							        'class'  => array( 'hide_if_virtual', 'hide_if_grouped', 'hide_if_external' ),
                                );

    return $product_data_tabs;
}

// CPO settings tab content
add_action('woocommerce_product_write_panels', 'uni_cpo_add_custom_settings_tab_content');
function uni_cpo_add_custom_settings_tab_content() {
        global $post;
        $currency_symbol = get_woocommerce_currency_symbol();
?>
        <div id="uni_cpo_settings_data" class="panel woocommerce_options_panel">
                <div class="options_group">
                            <?php woocommerce_wp_checkbox( array( 'id' => '_uni_cpo_enable_custom_options_calc', 'label' => __('Enable price calculation based on custom options?', 'uni-cpo'), 'description' => '' ) ); ?>
                </div>

                <div class="options_group">
                            <?php woocommerce_wp_checkbox( array( 'id' => '_uni_cpo_enable_qty_discounts_calc', 'label' => __('Enable discounts in the cart based on quantity?', 'uni-cpo'), 'description' => '' ) ); ?>
                </div>

                <div class="options_group uni_custom_tab_options">

                        <?php woocommerce_wp_text_input( array('id' => '_uni_cpo_min_price', 'label' => sprintf( __( 'Min. price (%s)', 'uni-cpo' ), $currency_symbol ), 'desc_tip' => 'false')); ?>

                    <div class="form-field">
                        <label for=""><?php _e('Available options tags (clickable)', 'uni-cpo') ?></label>
                        <?php $aCpoOptions = maybe_unserialize( get_post_meta( $post->ID, '_uni_cpo_product_attributes', true ) );
                        if ( $aCpoOptions ) { ?>
                        <ul id="cpo-options-elements-list">
                            <li class="uni_cpo_price tips" data-tip="<?php _e('Price of the product', 'uni-cpo'); ?>"><span>{uni_cpo_price}</span></li>
                        <?php
                        foreach ( $aCpoOptions as $aOption ) :
                            if ( $aOption['cpo_type'] == 'input' || $aOption['cpo_type'] == 'input_number' || $aOption['cpo_type'] == 'checkbox_multiple' ) {
                                if ( $aOption['value'] ) {
                                    foreach ( $aOption['value'] as $sInputName ) {
                                        echo '<li class="tips" data-tip="'.wc_attribute_label( $sInputName ).'"><span>{uni_cpo_'.$sInputName.'}</span></li>';
                                    }
                                }
                            } else {
                                echo '<li class="tips" data-tip="'.wc_attribute_label( $aOption['name'] ).'"><span>{uni_cpo_'.$aOption['name'].'}</span></li>';
                            }
                        ?>
                        <?php endforeach; ?>
                        </ul>
                        <div style="clear:both;"></div>
                        <span class="description">
                            <?php _e('Typical math operators: *, +, -, /. You can also use "(" and ")" in the formula.', 'uni-cpo'); ?>
                        </span>
                        <?php } else { _e('None of CPO options have been added yet. Maybe you need to reload the page?', 'uni-cpo'); } ?>
                    </div>

                        <?php woocommerce_wp_textarea_input( array('id' => '_uni_cpo_formula', 'label' => __( 'Formula', 'uni-cpo' ), 'placeholder' => '',
                            'desc_tip' => false,
                            'description' => __('The name of each field is a slug for this attribute prefixed with "uni_cpo_" and the brackets ("{}") from the both sides of the variable. For example: "{uni_cpo_width}" or "{uni_cpo_pa_thickness}". The formula example: "{uni_cpo_width}*{uni_cpo_height}+{uni_cpo_pa_glazing-thickness}". These special formatted names will be changed to the values of the price of the attribute during the price calculation. Also you should use "{uni_cpo_price}" if you want to add a price of the product.', 'uni-cpo' )
                        )); ?>

                </div>

                <div class="options_group">
                    <?php woocommerce_wp_text_input( array( 'id' => '_uni_cpo_min_qty', 'label' => __('Min. quantity value', 'uni-cpo'), 'description' => '', 'placeholder' => '1', 'type' => 'number', 	'custom_attributes' => array('step' => '1', 'min' => '1')  ) ); ?>
                    <?php woocommerce_wp_text_input( array( 'id' => '_uni_cpo_max_qty', 'label' => __('Max. quantity value', 'uni-cpo'), 'description' => '', 'placeholder' => '', 'type' => 'number', 	'custom_attributes' => array('step' => '1', 'min' => '')  ) ); ?>
                    <?php woocommerce_wp_text_input( array( 'id' => '_uni_cpo_start_qty', 'label' => __('Starting quantity value', 'uni-cpo'), 'description' => '', 'placeholder' => '1', 'type' => 'number', 	'custom_attributes' => array('step' => '1', 'min' => '1')  ) ); ?>
                    <?php woocommerce_wp_text_input( array( 'id' => '_uni_cpo_step_qty', 'label' => __('Increment step value', 'uni-cpo'), 'description' => '', 'placeholder' => '1', 'type' => 'number', 	'custom_attributes' => array('step' => '1', 'min' => '1')  ) ); ?>
                </div>

                <div class="options_group">
                            <?php woocommerce_wp_checkbox( array( 'id' => '_uni_cpo_conditions_enable', 'label' => __('Enable conditions?', 'uni-cpo'), 'description' => '' ) ); ?>
                </div>

                <div class="options_group">

                        <?php $aConditions = get_post_meta( $post->ID, '_uni_cpo_conditions_data', true); ?>

         <?php
            if ( !empty( $aConditions ) ) {
                foreach ( $aConditions as $sKey => $aValue ) {
         ?>
						<p id="conditions_field<?php echo $sKey; ?>" class="form-field uni-cloned-conditions">
							<label for="uni-condition-left-var<?php echo $sKey; ?>" class="label-uni-condition-var"><?php echo __( 'Rule', 'uni-cpo' ); ?></label>
							<span class="wrap" style="display:inline-block;background-color:#f1f1f1;width:100%;padding:2px;">
								<select id="uni_condition_left_var<?php echo $sKey; ?>" name="uni_condition_left_var<?php echo $sKey; ?>" class="select-uni-condition-left-var select short">
                                    <option value="uni_quantity"<?php selected('uni_quantity', $aValue['left_var']) ?>><?php _e( 'Quantity', 'uni-cpo'); ?></option>
                                <?php
                                if ( $aCpoOptions ) {
                                    foreach ( $aCpoOptions as $aOption ) :
                                        if ( $aOption['cpo_type'] == 'input' || $aOption['cpo_type'] == 'input_number' ) {
                                            if ( $aOption['value'] ) {
                                                foreach ( $aOption['value'] as $sInputName ) {
                                                    echo '<option value="uni_cpo_'.$sInputName.'"'.selected('uni_cpo_'.$sInputName, $aValue['left_var'], false).'>{uni_cpo_'.$sInputName.'}</option>';
                                                }
                                            }
                                        } else if ( $aOption['cpo_type'] == ( 'select' || 'checkbox' || 'radio' || 'color' || 'color_ext' ) ) {
                                            echo '<option value="uni_cpo_'.$aOption['name'].'"'.selected('uni_cpo_'.$aOption['name'], $aValue['left_var'], false).'>{uni_cpo_'.$aOption['name'].'}</option>';
                                        }
                                    endforeach;
                                } ?>
                                </select><br><br>
								<select id="uni_condition_left_operator<?php echo $sKey; ?>" name="uni_condition_left_operator<?php echo $sKey; ?>" class="select-uni-condition-left-operator select short">
                                    <option value="less"<?php selected('less', $aValue['left_operator']); ?>><?php _e( 'less then', 'uni-cpo'); ?></option>
                                    <option value="less_equal"<?php selected('less_equal', $aValue['left_operator']); ?>><?php _e( 'equal or less then', 'uni-cpo'); ?></option>
                                    <option value="equal"<?php selected('equal', $aValue['left_operator']); ?>><?php _e( 'equal', 'uni-cpo'); ?></option>
                                    <option value="greater_equal"<?php selected('greater_equal', $aValue['left_operator']); ?>><?php _e( 'equal or greater then', 'uni-cpo'); ?></option>
                                    <option value="greater"<?php selected('greater', $aValue['left_operator']); ?>><?php _e( 'greater then', 'uni-cpo'); ?></option>
                                    <option value="is"<?php selected('is', $aValue['left_operator']); ?>><?php _e( 'is', 'uni-cpo'); ?></option>
                                    <option value="isnot"<?php selected('isnot', $aValue['left_operator']); ?>><?php _e( 'is not', 'uni-cpo'); ?></option>
                                </select><br><br>
								<input placeholder="<?php _e( 'Value', 'uni-cpo' ); ?>" id="uni_condition_left_value<?php echo $sKey; ?>" class="input-uni-condition-left-value input-uni-condition-value input-text wc_input_decimal last" size="6" type="text" name="uni_condition_left_value<?php echo $sKey; ?>" value="<?php echo $aValue['left_value']; ?>" />
							</span>
                            <span class="wrap" style="display:inline-block;background-color:#CFE7B6;width:100%;padding:2px;margin-top:7px;">
                                <select id="uni_condition_conj_operator<?php echo $sKey; ?>" name="uni_condition_conj_operator<?php echo $sKey; ?>" class="select-uni-condition-conj-operator select short">
                                    <option value=""<?php selected('', $aValue['conj_operator']); ?>>- <?php _e( 'Disable the second part', 'uni-cpo'); ?> -</option>
                                    <option value="and"<?php selected('and', $aValue['conj_operator']); ?>><?php _e( 'AND', 'uni-cpo'); ?></option>
                                    <option value="or"<?php selected('or', $aValue['conj_operator']); ?>><?php _e( 'OR', 'uni-cpo'); ?></option>
                                </select>
                            </span>
                            <?php if ( empty($aValue['conj_operator']) ) { ?>
                            <span class="wrap" style="display:inline-block;background-color:#FFDDD1;width:100%;padding:2px;">
                            <?php } else { ?>
							<span class="wrap" style="display:inline-block;background-color:#f1f1f1;width:100%;padding:2px;">
                            <?php } ?>
								<select id="uni_condition_right_var<?php echo $sKey; ?>" name="uni_condition_right_var<?php echo $sKey; ?>" class="select-uni-condition-right-var select short">
                                    <option value="uni_quantity"<?php selected('uni_quantity', $aValue['right_var']) ?>><?php _e( 'Quantity', 'uni-cpo'); ?></option>
                                <?php
                                if ( $aCpoOptions ) {
                                    foreach ( $aCpoOptions as $aOption ) :
                                        if ( $aOption['cpo_type'] == 'input' || $aOption['cpo_type'] == 'input_number' ) {
                                            if ( $aOption['value'] ) {
                                                foreach ( $aOption['value'] as $sInputName ) {
                                                    echo '<option value="uni_cpo_'.$sInputName.'"'.selected('uni_cpo_'.$sInputName, $aValue['right_var'], false).'>{uni_cpo_'.$sInputName.'}</option>';
                                                }
                                            }
                                        } else if ( $aOption['cpo_type'] == ( 'select' || 'checkbox' || 'radio' || 'color' || 'color_ext' ) ) {
                                            echo '<option value="uni_cpo_'.$aOption['name'].'"'.selected('uni_cpo_'.$aOption['name'], $aValue['right_var'], false).'>{uni_cpo_'.$aOption['name'].'}</option>';
                                        }
                                    endforeach;
                                } ?>
                                </select><br><br>
								<select id="uni_condition_right_operator<?php echo $sKey; ?>" name="uni_condition_right_operator<?php echo $sKey; ?>" class="select-uni-condition-right-operator select short">
                                    <option value="less"<?php selected('less', $aValue['right_operator']); ?>><?php _e( 'less then', 'uni-cpo'); ?></option>
                                    <option value="less_equal"<?php selected('less_equal', $aValue['right_operator']); ?>><?php _e( 'equal or less then', 'uni-cpo'); ?></option>
                                    <option value="equal"<?php selected('equal', $aValue['right_operator']); ?>><?php _e( 'equal', 'uni-cpo'); ?></option>
                                    <option value="greater_equal"<?php selected('greater_equal', $aValue['right_operator']); ?>><?php _e( 'equal or greater then', 'uni-cpo'); ?></option>
                                    <option value="greater"<?php selected('greater', $aValue['right_operator']); ?>><?php _e( 'greater then', 'uni-cpo'); ?></option>
                                    <option value="is"<?php selected('is', $aValue['right_operator']); ?>><?php _e( 'is', 'uni-cpo'); ?></option>
                                    <option value="isnot"<?php selected('isnot', $aValue['right_operator']); ?>><?php _e( 'is not', 'uni-cpo'); ?></option>
                                </select><br><br>
								<input placeholder="<?php _e( 'Value', 'uni-cpo' ); ?>" id="uni_condition_right_value<?php echo $sKey; ?>" class="input-uni-condition-right-value input-uni-condition-value input-text wc_input_decimal last" size="6" type="text" name="uni_condition_right_value<?php echo $sKey; ?>" value="<?php echo $aValue['right_value']; ?>" />
							</span>
                            <br><br>
                            <span><?php _e( 'Formula', 'uni-cpo' ); ?></span>
                            <textarea name="uni_condition_formula<?php echo $sKey; ?>" id="uni_condition_formula<?php echo $sKey; ?>" class="textarea-uni-condition-formula" cols="30" rows="5"><?php echo $aValue['formula']; ?></textarea>
                        </p>
        <?php
                }
            } else {
        ?>
						<p id="conditions_field1" class="form-field uni-cloned-conditions">
							<label for="uni-condition-left-var1" class="label-uni-condition-var"><?php echo __( 'Rule', 'uni-cpo' ); ?></label>
							<span class="wrap" style="display:inline-block;background-color:#f1f1f1;width:100%;padding:2px;">
								<select id="uni_condition_left_var1" name="uni_condition_left_var1" class="select-uni-condition-left-var select short">
                                    <option value="uni_quantity"><?php _e( 'Quantity', 'uni-cpo'); ?></option>
                                <?php
                                if ( $aCpoOptions ) {
                                    foreach ( $aCpoOptions as $aOption ) :
                                        if ( $aOption['cpo_type'] == 'input' || $aOption['cpo_type'] == 'input_number' ) {
                                            if ( $aOption['value'] ) {
                                                foreach ( $aOption['value'] as $sInputName ) {
                                                    echo '<option value="uni_cpo_'.$sInputName.'"'.selected('uni_cpo_'.$sInputName, $aValue['left_var'], false).'>{uni_cpo_'.$sInputName.'}</option>';
                                                }
                                            }
                                        } else if ( $aOption['cpo_type'] == ( 'select' || 'checkbox' || 'radio' || 'color' || 'color_ext' ) ) {
                                            echo '<option value="uni_cpo_'.$aOption['name'].'"'.selected('uni_cpo_'.$aOption['name'], $aValue['left_var'], false).'>{uni_cpo_'.$aOption['name'].'}</option>';
                                        }
                                    endforeach;
                                } ?>
                                </select><br><br>
								<select id="uni_condition_left_operator1" name="uni_condition_left_operator1" class="select-uni-condition-left-operator select short">
                                    <option value="less"><?php _e( 'less then', 'uni-cpo'); ?></option>
                                    <option value="less_equal"><?php _e( 'equal or less then', 'uni-cpo'); ?></option>
                                    <option value="equal"><?php _e( 'equal', 'uni-cpo'); ?></option>
                                    <option value="greater_equal"><?php _e( 'equal or greater then', 'uni-cpo'); ?></option>
                                    <option value="greater"><?php _e( 'greater then', 'uni-cpo'); ?></option>
                                    <option value="is"><?php _e( 'is', 'uni-cpo'); ?></option>
                                    <option value="isnot"><?php _e( 'is not', 'uni-cpo'); ?></option>
                                </select><br><br>
								<input placeholder="<?php _e( 'Value', 'uni-cpo' ); ?>" id="uni_condition_left_value1" class="input-uni-condition-left-value input-uni-condition-value input-text wc_input_decimal last" size="6" type="text" name="uni_condition_left_value1" value="" />
							</span>
                            <span class="wrap" style="display:inline-block;background-color:#CFE7B6;width:100%;padding:2px;margin-top:7px;">
                                <select id="uni_condition_conj_operator1" name="uni_condition_conj_operator1" class="select-uni-condition-conj-operator select short">
                                    <option value="">- <?php _e( 'Disable the second part', 'uni-cpo'); ?> -</option>
                                    <option value="and"><?php _e( 'AND', 'uni-cpo'); ?></option>
                                    <option value="or"><?php _e( 'OR', 'uni-cpo'); ?></option>
                                </select>
                            </span>
							<span class="wrap" style="display:inline-block;background-color:#f1f1f1;width:100%;padding:2px;">
								<select id="uni_condition_right_var1" name="uni_condition_right_var1" class="select-uni-condition-right-var select short">
                                    <option value="uni_quantity"><?php _e( 'Quantity', 'uni-cpo'); ?></option>
                                <?php
                                if ( $aCpoOptions ) {
                                    foreach ( $aCpoOptions as $aOption ) :
                                        if ( $aOption['cpo_type'] == 'input' || $aOption['cpo_type'] == 'input_number' ) {
                                            if ( $aOption['value'] ) {
                                                foreach ( $aOption['value'] as $sInputName ) {
                                                    echo '<option value="uni_cpo_'.$sInputName.'"'.selected('uni_cpo_'.$sInputName, $aValue['right_var'], false).'>{uni_cpo_'.$sInputName.'}</option>';
                                                }
                                            }
                                        } else if ( $aOption['cpo_type'] == ( 'select' || 'checkbox' || 'radio' || 'color' || 'color_ext' ) ) {
                                            echo '<option value="uni_cpo_'.$aOption['name'].'"'.selected('uni_cpo_'.$aOption['name'], $aValue['right_var'], false).'>{uni_cpo_'.$aOption['name'].'}</option>';
                                        }
                                    endforeach;
                                } ?>
                                </select><br><br>
								<select id="uni_condition_right_operator1" name="uni_condition_right_operator1" class="select-uni-condition-right-operator select short">
                                    <option value="less"><?php _e( 'less then', 'uni-cpo'); ?></option>
                                    <option value="less_equal"><?php _e( 'equal or less then', 'uni-cpo'); ?></option>
                                    <option value="equal"><?php _e( 'equal', 'uni-cpo'); ?></option>
                                    <option value="greater_equal"><?php _e( 'equal or greater then', 'uni-cpo'); ?></option>
                                    <option value="greater"><?php _e( 'greater then', 'uni-cpo'); ?></option>
                                    <option value="is"><?php _e( 'is', 'uni-cpo'); ?></option>
                                    <option value="isnot"><?php _e( 'is not', 'uni-cpo'); ?></option>
                                </select><br><br>
								<input placeholder="<?php _e( 'Value', 'uni-cpo' ); ?>" id="uni_condition_right_value1" class="input-uni-condition-right-value input-uni-condition-value input-text wc_input_decimal last" size="6" type="text" name="uni_condition_right_value1" value="" />
							</span>
                            <br><br>
                            <span><?php _e( 'Formula', 'uni-cpo' ); ?></span>
                            <textarea name="uni_condition_formula1" id="uni_condition_formula1" class="textarea-uni-condition-formula" cols="30" rows="5"></textarea>
                        </p>
        <?php } ?>

                    <div id="addDelButtons">
                        <span id="uni-clone-condition" class="uni-clone-add dashicons dashicons-yes"><b><?php _e( 'Add new', 'uni-cpo'); ?></b></span> <span id="uni-delete-condition" class="uni-clone-delete dashicons dashicons-no"><b><?php _e( 'Delete', 'uni-cpo'); ?></b></span>
                    </div>

                </div>

        </div>
<?php
}

// Save custom fields
function uni_cpo_save_settings( $post_id ) {
        update_post_meta( $post_id, '_uni_cpo_enable_custom_options_calc', ( isset($_POST['_uni_cpo_enable_custom_options_calc']) && !empty($_POST['_uni_cpo_enable_custom_options_calc']) ) ? 'yes' : 'no' );
        update_post_meta( $post_id, '_uni_cpo_enable_qty_discounts_calc', ( isset($_POST['_uni_cpo_enable_qty_discounts_calc']) && !empty($_POST['_uni_cpo_enable_qty_discounts_calc']) ) ? 'yes' : 'no' );
        $fMinPrice = (float)$_POST['_uni_cpo_min_price'];
        update_post_meta( $post_id, '_uni_cpo_min_price', $fMinPrice);
        update_post_meta( $post_id, '_uni_cpo_formula', $_POST['_uni_cpo_formula']);

        // qty
        update_post_meta( $post_id, '_uni_cpo_min_qty', $_POST['_uni_cpo_min_qty']);
        update_post_meta( $post_id, '_uni_cpo_max_qty', $_POST['_uni_cpo_max_qty']);
        update_post_meta( $post_id, '_uni_cpo_start_qty', $_POST['_uni_cpo_start_qty']);
        update_post_meta( $post_id, '_uni_cpo_step_qty', $_POST['_uni_cpo_step_qty']);

        //conditions
        update_post_meta( $post_id, '_uni_cpo_conditions_enable', $_POST['_uni_cpo_conditions_enable']);
        $aConditions = array();
        for ( $i = 1; $i < 36; $i++ ){
            if ( isset($_POST['uni_condition_left_var'.$i]) ) {
                $aConditions[$i]['left_var'] = $_POST['uni_condition_left_var'.$i];
            }
            if ( isset($_POST['uni_condition_left_operator'.$i]) ) {
                $aConditions[$i]['left_operator'] = $_POST['uni_condition_left_operator'.$i];
            }
            if ( isset($_POST['uni_condition_left_value'.$i]) ) {
                $aConditions[$i]['left_value'] = $_POST['uni_condition_left_value'.$i];
            }
            if ( isset($_POST['uni_condition_conj_operator'.$i]) ) {
                $aConditions[$i]['conj_operator'] = $_POST['uni_condition_conj_operator'.$i];
            }
            if ( isset($_POST['uni_condition_right_var'.$i]) ) {
                $aConditions[$i]['right_var'] = $_POST['uni_condition_right_var'.$i];
            }
            if ( isset($_POST['uni_condition_right_operator'.$i]) ) {
                $aConditions[$i]['right_operator'] = $_POST['uni_condition_right_operator'.$i];
            }
            if ( isset($_POST['uni_condition_right_value'.$i]) ) {
                $aConditions[$i]['right_value'] = $_POST['uni_condition_right_value'.$i];
            }
            if ( isset($_POST['uni_condition_formula'.$i]) ) {
                $aConditions[$i]['formula'] = $_POST['uni_condition_formula'.$i];
            }
        }
        if ( !empty( $aConditions ) ) {
            update_post_meta( $post_id, '_uni_cpo_conditions_data', $aConditions);
        }

        //discounts
        update_post_meta( $post_id, '_uni_cpo_discount_type', $_POST['_uni_cpo_discount_type']);
        $aDiscounts = array();
        for ( $i = 1; $i < 36; $i++ ){
            if ( isset($_POST['uni_cpo_min_qty'.$i]) ) {
                $aDiscounts[$i]['min'] = $_POST['uni_cpo_min_qty'.$i];
            }
            if ( isset($_POST['uni_cpo_max_qty'.$i]) ) {
                $aDiscounts[$i]['max'] = $_POST['uni_cpo_max_qty'.$i];
            }
            if ( isset($_POST['uni_cpo_discount'.$i]) ) {
                $aDiscounts[$i]['value'] = $_POST['uni_cpo_discount'.$i];
            }
        }
        if ( !empty( $aDiscounts ) ) {
            update_post_meta( $post_id, '_uni_cpo_discount_data', $aDiscounts);
        }
}
add_action('woocommerce_process_product_meta', 'uni_cpo_save_settings');

// CPO options tab content
add_action('woocommerce_product_write_panels', 'uni_cpo_add_custom_options_tab_content');
function uni_cpo_add_custom_options_tab_content() {
    global $post, $wpdb, $thepostid;
?>

			<div id="uni_cpo_options_data" class="panel wc-metaboxes-wrapper">

				<p class="toolbar">
					<a href="#" class="close_all"><?php _e( 'Close all', 'woocommerce' ); ?></a><a href="#" class="expand_all"><?php _e( 'Expand all', 'woocommerce' ); ?></a>
				</p>

				<div class="uni_cpo_product_attributes wc-metaboxes">

					<?php
						global $wc_product_attributes;

						// Array of defined attribute taxonomies
						$attribute_taxonomies = wc_get_attribute_taxonomies();

						// Product attributes - taxonomies and custom, ordered, with visibility and variation attributes set
						$attributes           = maybe_unserialize( get_post_meta( $thepostid, '_uni_cpo_product_attributes', true ) );

						// Output All Set Attributes
						if ( ! empty( $attributes ) ) {
							$attribute_keys = array_keys( $attributes );

							for ( $i = 0; $i < sizeof( $attribute_keys ); $i ++ ) {
								$attribute     = $attributes[ $attribute_keys[ $i ] ];
								$position      = empty( $attribute['position'] ) ? 0 : absint( $attribute['position'] );
								$taxonomy      = '';
								$metabox_class = array();

								if ( $attribute['is_taxonomy'] ) {
									$taxonomy = $attribute['name'];

									if ( ! taxonomy_exists( $taxonomy ) ) {
										continue;
									}

									$attribute_taxonomy = $wc_product_attributes[ $taxonomy ];
									$metabox_class[]    = 'taxonomy';
									$metabox_class[]    = $taxonomy;
									$attribute_label    = wc_attribute_label( $taxonomy );
								} else {
									$attribute_label    = apply_filters( 'woocommerce_attribute_label', $attribute['name'], $attribute['name'] );
								}

								include( 'includes/html-product-attribute.php' );
							}
						}
					?>
				</div>

				<p class="toolbar">
					<button type="button" class="button button-primary uni_cpo_add_attribute uni-cpo-button-primary"><?php _e( 'Add', 'woocommerce' ); ?></button>
					<select name="attribute_taxonomy" class="uni_cpo_attribute_taxonomy">
						<option value="" disabled><?php _e( 'Choose an option...', 'uni-cpo' ); ?></option>
						<?php
							if ( $attribute_taxonomies ) {
								foreach ( $attribute_taxonomies as $tax ) {
									$attribute_taxonomy_name = wc_attribute_taxonomy_name( $tax->attribute_name );
									$label = $tax->attribute_label ? $tax->attribute_label : $tax->attribute_name;
									echo '<option value="' . esc_attr( $attribute_taxonomy_name ) . '">' . esc_html( $label ) . '</option>';
								}
							}
						?>
					</select>

					<button type="button" class="button uni_cpo_save_attributes"><?php _e( 'Save CPO options', 'uni-cpo' ); ?></button>
				</p>
				<?php do_action( 'woocommerce_product_options_attributes' ); ?>
			</div>
            <?php /*
			<div id="uni_cpo_options_data" class="panel wc-metaboxes-wrapper">

				<p class="toolbar">
					<a href="#" class="close_all"><?php _e( 'Close all', 'woocommerce' ); ?></a><a href="#" class="expand_all"><?php _e( 'Expand all', 'woocommerce' ); ?></a>
				</p>

				<div class="uni_cpo_product_attributes wc-metaboxes">

					<?php
						// Array of defined attribute taxonomies
						$attribute_taxonomies = wc_get_attribute_taxonomies();

						// Product attributes - taxonomies and custom, ordered, with visibility and variation attributes set
						$attributes = maybe_unserialize( get_post_meta( $thepostid, '_uni_cpo_product_attributes', true ) );
                        //print_r($attributes);
						$i = -1;

						// Taxonomies
						if ( $attribute_taxonomies ) {
					    	foreach ( $attribute_taxonomies as $tax ) {


					    		// Get name of taxonomy we're now outputting (pa_xxx)
					    		$attribute_taxonomy_name = wc_attribute_taxonomy_name( $tax->attribute_name );

					    		// Ensure it exists
					    		if ( ! taxonomy_exists( $attribute_taxonomy_name ) )
					    			continue;

					    		$i++;

                                $attribute = array();
					    		// Get product data values for current taxonomy - this contains ordering and visibility data
					    		if ( isset( $attributes[ sanitize_title( $attribute_taxonomy_name ) ] ) )
					    			$attribute = $attributes[ sanitize_title( $attribute_taxonomy_name ) ];

					    		$position = empty( $attribute['position'] ) ? 0 : absint( $attribute['position'] );

					    		// Get terms of this taxonomy associated with current product
					    		$post_terms = wp_get_post_terms( $thepostid, $attribute_taxonomy_name );

					    		?>
					    		<div class="woocommerce_attribute wc-metabox closed taxonomy <?php echo $attribute_taxonomy_name; ?>" rel="<?php echo $position; ?>" <?php if ( !$attribute ) echo 'style="display:none"'; ?>>
									<h3>
										<button type="button" class="remove_row button"><?php _e( 'Remove', 'woocommerce' ); ?></button>
										<div class="handlediv" title="<?php _e( 'Click to toggle', 'woocommerce' ); ?>"></div>
										<strong class="attribute_name"><?php echo apply_filters( 'woocommerce_attribute_label', $tax->attribute_label ? $tax->attribute_label : $tax->attribute_name, $tax->attribute_name ); ?></strong>
									</h3>
									<table cellpadding="0" cellspacing="0" class="woocommerce_attribute_data wc-metabox-content">
										<tbody>
											<tr>
												<td class="attribute_name">
													<label><?php _e( 'Name', 'woocommerce' ); ?>:</label>
													<strong><?php echo $tax->attribute_label ? $tax->attribute_label : $tax->attribute_name; ?></strong>

													<input type="hidden" name="attribute_names[<?php echo $i; ?>]" value="<?php echo esc_attr( $attribute_taxonomy_name ); ?>" />
													<input type="hidden" name="attribute_position[<?php echo $i; ?>]" class="attribute_position" value="<?php echo esc_attr( $position ); ?>" />
													<input type="hidden" name="attribute_is_taxonomy[<?php echo $i; ?>]" value="1" />
												</td>
												<td rowspan="3">
													<label><?php _e( 'Value(s)', 'woocommerce' ); ?>:</label>
													<?php if ( $tax->attribute_type == "select" ) : ?>
														<select multiple="multiple" data-placeholder="<?php _e( 'Select terms', 'woocommerce' ); ?>" class="multiselect attribute_values" name="attribute_values[<?php echo $i; ?>][]">
															<?php
								        					$all_terms = get_terms( $attribute_taxonomy_name, 'orderby=name&hide_empty=0' );
							        						if ( $all_terms ) {
								        						foreach ( $all_terms as $term ) {
                                                                    $bAttrIs = false;
                                                                    if ( isset($attribute['value']) ) {
                                                                        if ( is_array($attribute['value']) ) {
                                                                            if ( in_array( $term->slug, $attribute['value']) ) {
                                                                                $bAttrIs = true;
                                                                            }
                                                                        }
                                                                    }
                                                                    echo '<option value="' . esc_attr( $term->slug ) . '" ' . selected( $bAttrIs, 1, false ) . '>' . $term->name . '</option>';
																}
															}
															?>
														</select>

														<button class="button plus select_all_attributes"><?php _e( 'Select all', 'woocommerce' ); ?></button> <button class="button minus select_no_attributes"><?php _e( 'Select none', 'woocommerce' ); ?></button>



													<?php elseif ( $tax->attribute_type == "text" ) : ?>
														<input type="text" name="attribute_values[<?php echo $i; ?>]" value="<?php

															// Text attributes should list terms pipe separated
															if ( $post_terms ) {
																$values = array();
																foreach ( $post_terms as $term )
																	$values[] = $term->name;
																echo esc_attr( implode( ' ' . WC_DELIMITER . ' ', $values ) );
															}

														?>" placeholder="<?php _e( 'Pipe (|) separate terms', 'woocommerce' ); ?>" />
													<?php endif; ?>
													<?php do_action( 'woocommerce_product_option_terms', $tax, $i ); ?>
												</td>
											</tr>
											<tr>
												<td>
													<label><input type="checkbox" class="checkbox" <?php

														if ( isset( $attribute['is_visible'] ) )
															checked( $attribute['is_visible'], 1 );
														else
															checked( apply_filters( 'default_attribute_visibility', false, $tax ), true );

													?> name="attribute_visibility[<?php echo $i; ?>]" value="1" /> <?php _e( 'Visible on the product page', 'woocommerce' ); ?></label>
												</td>
											</tr>
											<tr>
												<td>
													<label><input type="checkbox" class="checkbox" <?php

                                                        if ( isset( $attribute['is_cpo_required'] ) )
															checked( $attribute['is_cpo_required'], 1 );

													?> name="attribute_cpo_require[<?php echo $i; ?>]" value="1" /> <?php _e( 'Must be filled/selected by user', 'uni-cpo' ); ?></label>
												</td>
											</tr>
											<tr>
												<td>
													<label>
					                                    <select name="attribute_cpo_type[<?php echo $i; ?>]">
						                                    <option value="select"<?php if ( isset( $attribute['cpo_type'] ) ) selected($attribute['cpo_type'], 'select') ?>><?php _e( 'Dropdown (default)', 'uni-cpo' ); ?></option>
                                                            <option value="input"<?php if ( isset( $attribute['cpo_type'] ) ) selected($attribute['cpo_type'], 'input') ?>><?php _e( 'Text input', 'uni-cpo' ); ?></option>
                                                            <option value="radio"<?php if ( isset( $attribute['cpo_type'] ) ) selected($attribute['cpo_type'], 'radio') ?>><?php _e( 'Radio input', 'uni-cpo' ); ?></option>
                                                            <option value="checkbox"<?php if ( isset( $attribute['cpo_type'] ) ) selected($attribute['cpo_type'], 'checkbox') ?>><?php _e( 'Checkbox input', 'uni-cpo' ); ?></option>
                                                            <option value="checkbox_multiple"<?php if ( isset( $attribute['cpo_type'] ) ) selected($attribute['cpo_type'], 'checkbox_multiple') ?>><?php _e( 'Checkboxes', 'uni-cpo' ); ?></option>
                                                            <option value="color"<?php if ( isset( $attribute['cpo_type'] ) ) selected($attribute['cpo_type'], 'color') ?>><?php _e( 'Color (palette only)', 'uni-cpo' ); ?></option>
                                                            <option value="color_ext"<?php if ( isset( $attribute['cpo_type'] ) ) selected($attribute['cpo_type'], 'color_ext') ?>><?php _e( 'Color (picker only)', 'uni-cpo' ); ?></option>
                                                            <option value="textarea"<?php if ( isset( $attribute['cpo_type'] ) ) selected($attribute['cpo_type'], 'textarea') ?>><?php _e( 'Textarea', 'uni-cpo' ); ?></option>
					                                    </select>
                                                        <?php _e( 'Type of field', 'uni-cpo' ); ?>
                                                    </label>
												</td>
											</tr>
											<tr>
												<td>
													<label>
                                                        <?php $aRegisteredImagesSizes = uni_get_registered_image_sizes(); ?>
                                                        <select name="attribute_cpo_image_size[<?php echo $i; ?>]">
                                                            <option value="full"<?php if ( isset( $attribute['cpo_image_size'] ) ) selected($attribute['cpo_image_size'], 'full') ?>>full</option>
                                                            <?php foreach ($aRegisteredImagesSizes as $sName => $aValue): ?>
                                                            <option value="<?php echo $sName ?>"<?php if ( isset( $attribute['cpo_image_size'] ) ) selected($attribute['cpo_image_size'], $sName) ?>><?php echo $sName ?> (<?php echo $aValue['width'] ?>x<?php echo $aValue['height'] ?>)</option>
                                                            <?php endforeach; ?>
                                                        </select>
                                                        <?php _e( 'Image size', 'uni-cpo' ); ?>
                                                    </label>
												</td>
											</tr>
										</tbody>
									</table>
								</div>
					    		<?php
            		    	}
					    }
					?>
				</div>

			</div>
            */ ?>
<?php
}

// save attributes-options via ajax
add_action( 'wp_ajax_uni_cpo_save_attributes', 'uni_cpo_save_attributes' );
function uni_cpo_save_attributes() {

		$attributes = $data = array();

        parse_str( $_POST['data'], $data );
        $post_id = absint( $_POST['post_id'] );

		if ( isset( $data['attribute_names'] ) ) {


            $attribute_names  = array_map( 'stripslashes', $data['attribute_names'] );
			$attribute_values = $data['attribute_values'];

			if ( isset( $data['attribute_cpo_description'] ) ) {
				$attribute_cpo_description = $data['attribute_cpo_description'];
			}

			if ( isset( $data['attribute_visibility'] ) ) {
				$attribute_visibility = $data['attribute_visibility'];
			}

			if ( isset( $data['attribute_cpo_require'] ) ) {
				$attribute_cpo_required = $data['attribute_cpo_require'];
			}

            $attribute_cpo_type         = $data['attribute_cpo_type'];
            $attribute_cpo_image_size   = $data['attribute_cpo_image_size'];
			$attribute_is_taxonomy      = $data['attribute_is_taxonomy'];
			$attribute_position         = $data['attribute_position'];
			$attribute_names_count      = sizeof( $attribute_names );

			for ( $i = 0; $i < $attribute_names_count; $i++ ) {

				if ( ! $attribute_names[ $i ] ) {
					continue;
				}

                $cpo_description    = isset( $attribute_cpo_description[ $i ] ) ? esc_attr( $attribute_cpo_description[ $i ] ) : '';
				$is_visible         = isset( $attribute_visibility[ $i ] ) ? 1 : 0;
				$is_cpo_required    = isset( $attribute_cpo_required[ $i ] ) ? 1 : 0;
				$is_taxonomy        = $attribute_is_taxonomy[ $i ] ? 1 : 0;

				if ( $is_taxonomy ) {

					if ( isset( $attribute_values[ $i ] ) ) {

						// Select based attributes - Format values (posted values are slugs)
						if ( is_array( $attribute_values[ $i ] ) ) {
							$values = array_map( 'sanitize_title', $attribute_values[ $i ] );

						// Text based attributes - Posted values are term names - don't change to slugs
						} else {
							$values = array_map( 'stripslashes', array_map( 'strip_tags', explode( WC_DELIMITER, $attribute_values[ $i ] ) ) );
						}

						// Remove empty items in the array
						$values = array_filter( $values, 'strlen' );

					} else {
						$values = array();
					}

                    /*
                    // don't do that!)
					// Update post terms
					if ( taxonomy_exists( $attribute_names[ $i ] ) ) {
						wp_set_object_terms( $post_id, $values, $attribute_names[ $i ] );
					}
                    */

					if ( $values ) {
						// Add attribute to array, but don't set values
						$attributes[ sanitize_title( $attribute_names[ $i ] ) ] = array(
							'name'              => wc_clean( $attribute_names[ $i ] ),
							'value'             => $values,
							'position'          => $attribute_position[ $i ],
                            'cpo_description'   => $cpo_description,
							'is_visible'        => $is_visible,
							'is_cpo_required' 	=> $is_cpo_required,
                            'cpo_type' 	        => $attribute_cpo_type[ $i ],
                            'cpo_image_size'    => $attribute_cpo_image_size[ $i ],
							'is_taxonomy'       => $is_taxonomy
						);
					}

				}
                /*elseif ( isset( $attribute_values[ $i ] ) ) {

					// Text based, separate by pipe
					$values = implode( ' ' . WC_DELIMITER . ' ', array_map( 'trim', array_map( 'wp_kses_post', array_map( 'stripslashes', explode( WC_DELIMITER, $attribute_values[ $i ] ) ) ) ) );

					// Custom attribute - Add attribute to array and set the values
					$attributes[ sanitize_title( $attribute_names[ $i ] ) ] = array(
						'name'         => wc_clean( $attribute_names[ $i ] ),
						'value'        => $values,
						'position'     => $attribute_position[ $i ],
						'is_visible'   => $is_visible,
						'is_variation' => $is_variation,
						'is_taxonomy'  => $is_taxonomy
					);
				} */

			 }
		}

		if ( ! function_exists( 'attributes_cmp' ) ) {
			function attributes_cmp( $a, $b ) {
				if ( $a['position'] == $b['position'] ) {
					return 0;
				}

				return ( $a['position'] < $b['position'] ) ? -1 : 1;
			}
		}
		uasort( $attributes, 'attributes_cmp' );

		update_post_meta( $post_id, '_uni_cpo_product_attributes', $attributes );

/*
		check_ajax_referer( 'save-attributes', 'security' );

		// Get post data
		parse_str( $_POST['data'], $data );
		$post_id = absint( $_POST['post_id'] );

		// Save Attributes
		$attributes = array();

		if ( isset( $data['attribute_names'] ) ) {

			$attribute_names  = array_map( 'stripslashes', $data['attribute_names'] );
			$attribute_values = isset( $data['attribute_values'] ) ? $data['attribute_values'] : array();

			if ( isset( $data['attribute_visibility'] ) ) {
				$attribute_visibility = $data['attribute_visibility'];
			}

			if ( isset( $data['attribute_cpo_require'] ) ) {
				$attribute_cpo_required = $data['attribute_cpo_require'];
			}

            $attribute_cpo_type    = $data['attribute_cpo_type'];
            $attribute_cpo_image_size = $data['attribute_cpo_image_size'];
			$attribute_is_taxonomy = $data['attribute_is_taxonomy'];
			$attribute_position    = $data['attribute_position'];
			$attribute_names_count = sizeof( $attribute_names );

			for ( $i = 0; $i < $attribute_names_count; $i++ ) {
				if ( ! $attribute_names[ $i ] ) {
					continue;
				}

				$is_visible   = isset( $attribute_visibility[ $i ] ) ? 1 : 0;
				$is_cpo_required = isset( $attribute_cpo_required[ $i ] ) ? 1 : 0;
				$is_taxonomy  = $attribute_is_taxonomy[ $i ] ? 1 : 0;

				if ( $is_taxonomy ) {

					if ( isset( $attribute_values[ $i ] ) ) {

						// Select based attributes - Format values (posted values are slugs)
						if ( is_array( $attribute_values[ $i ] ) ) {
							$values = array_map( 'sanitize_title', $attribute_values[ $i ] );

						// Text based attributes - Posted values are term names - don't change to slugs
						} else {
							$values = array_map( 'stripslashes', array_map( 'strip_tags', explode( WC_DELIMITER, $attribute_values[ $i ] ) ) );
						}

						// Remove empty items in the array
						$values = array_filter( $values, 'strlen' );

					} else {
						$values = array();
					}

					if ( $values ) {
						// Add attribute to array, but don't set values
						$attributes[ sanitize_title( $attribute_names[ $i ] ) ] = array(
							'name' 			    => wc_clean( $attribute_names[ $i ] ),
							'value' 		    => $values,
							'position' 		    => $attribute_position[ $i ],
							'is_visible' 	    => $is_visible,
							'is_cpo_required' 	=> $is_cpo_required,
                            'cpo_type' 	        => $attribute_cpo_type[ $i ],
                            'cpo_image_size'    => $attribute_cpo_image_size[ $i ],
							'is_taxonomy' 	    => $is_taxonomy
						);
					}

				}
                //************************************
                elseif ( isset( $attribute_values[ $i ] ) ) {
                    // TODO
					// Text based, separate by pipe
					$values = implode( ' ' . WC_DELIMITER . ' ', array_map( 'wc_clean', array_map( 'stripslashes', explode( WC_DELIMITER, $attribute_values[ $i ] ) ) ) );

					// Custom attribute - Add attribute to array and set the values
					$attributes[ sanitize_title( $attribute_names[ $i ] ) ] = array(
						'name' 			    => wc_clean( $attribute_names[ $i ] ),
						'value' 		    => $values,
						'position' 		    => $attribute_position[ $i ],
						'is_visible' 	    => $is_visible,
						'is_cpo_required' 	=> $is_cpo_required,
                        'cpo_type' 	        => $attribute_cpo_type,
						'is_taxonomy' 	    => $is_taxonomy
					);

				}
                //*********************

			 }
		}

		if ( ! function_exists( 'attributes_cmp' ) ) {
			function attributes_cmp( $a, $b ) {
				if ( $a['position'] == $b['position'] ) {
					return 0;
				}

				return ( $a['position'] < $b['position'] ) ? -1 : 1;
			}
		}
		uasort( $attributes, 'attributes_cmp' );

		update_post_meta( $post_id, '_uni_cpo_product_attributes', $attributes );
        */
		die();
}

// add new row via ajax
add_action( 'wp_ajax_uni_add_row_attribute', 'uni_add_row_attribute' );
function uni_add_row_attribute() {
		ob_start();

		check_ajax_referer( 'add-attribute', 'security' );

		global $wc_product_attributes;

		$thepostid     = 0;
		$taxonomy      = sanitize_text_field( $_POST['taxonomy'] );
		$i             = absint( $_POST['i'] );
		$position      = 0;
		$metabox_class = array();
		$attribute     = array(
			'name'         => $taxonomy,
			'value'        => '',
			'is_visible'   => 1,
			'is_variation' => 0,
			'is_taxonomy'  => $taxonomy ? 1 : 0
		);

		if ( $taxonomy ) {
			$attribute_taxonomy = $wc_product_attributes[ $taxonomy ];
			$metabox_class[]    = 'taxonomy';
			$metabox_class[]    = $taxonomy;
			$attribute_label    = wc_attribute_label( $taxonomy );
		} else {
			$attribute_label = '';
		}

		include( 'includes/html-product-attribute.php' );
		die();
}

// CPO discounts tab content
add_action('woocommerce_product_write_panels', 'uni_cpo_add_custom_discounts_tab_content');
function uni_cpo_add_custom_discounts_tab_content() {
        global $post;
        $currency_symbol = get_woocommerce_currency_symbol();
?>
        <div id="uni_cpo_discounts_data" class="panel woocommerce_options_panel">
                <div class="options_group">
                            <?php woocommerce_wp_select( array( 'id' => '_uni_cpo_discount_type', 'label' => __( 'Type of discount', 'uni-cpo' ),
                                        'options' => array(
                                            'fixed' => __( 'Fixed', 'uni-cpo' ),
                                            'percent' => __( 'Percent', 'uni-cpo' ),
                                        )
                                    )
                                ); ?>
                                <?php $aDiscounts = get_post_meta( $post->ID, '_uni_cpo_discount_data', true); ?>
                </div>
         <?php
            if ( !empty( $aDiscounts ) ) {
                foreach ( $aDiscounts as $sKey => $aValue ) {
         ?>
                <div id="uni_discount_rule<?php echo $sKey; ?>" class="options_group uni_custom_tab_options uniClonedInput">

                    <p class="form-field">
                        <label for="uni-cpo-min-qty-input<?php echo $sKey; ?>" class="label-uni-min-qty"><?php _e('Min. qty', 'uni-cpo') ?></label>
                        <input type="number" name="uni_cpo_min_qty<?php echo $sKey; ?>" value="<?php echo $aValue['min']; ?>" id="uni-cpo-min-qty-input<?php echo $sKey; ?>" class="input-uni-min-qty long wc_input_price" />
                    </p>

                    <p class="form-field">
                        <label for="uni-cpo-max-qty-input<?php echo $sKey; ?>" class="label-uni-max-qty"><?php _e('Max. qty', 'uni-cpo') ?></label>
                        <input type="number" name="uni_cpo_max_qty<?php echo $sKey; ?>" value="<?php echo $aValue['max']; ?>" id="uni-cpo-max-qty-input<?php echo $sKey; ?>" class="input-uni-max-qty long wc_input_price" />
                    </p>

                    <p class="form-field">
                        <label for="uni-cpo-discount-input<?php echo $sKey; ?>" class="label-uni-discount"><?php _e('Discount value', 'uni-cpo') ?></label>
                        <input type="text" name="uni_cpo_discount<?php echo $sKey; ?>" value="<?php echo $aValue['value']; ?>" id="uni-cpo-discount-input<?php echo $sKey; ?>" class="input-uni-discount long wc_input_price" />
                    </p>
                    <div style="clear:both;"></div>
                </div>
        <?php
                }
            } else {
        ?>
                <div id="uni_discount_rule1" class="options_group uni_custom_tab_options uniClonedInput">

                    <p class="form-field">
                        <label for="uni-cpo-min-qty-input1" class="label-uni-min-qty"><?php _e('Min. qty', 'uni-cpo') ?></label>
                        <input type="number" name="uni_cpo_min_qty1" value="" id="uni-cpo-min-qty-input1" class="input-uni-min-qty long wc_input_price" />
                    </p>

                    <p class="form-field">
                        <label for="uni-cpo-max-qty-input1" class="label-uni-max-qty"><?php _e('Max. qty', 'uni-cpo') ?></label>
                        <input type="number" name="uni_cpo_max_qty1" value="" id="uni-cpo-max-qty-input1" class="input-uni-max-qty long wc_input_price" />
                    </p>

                    <p class="form-field">
                        <label for="uni-cpo-discount-input1" class="label-uni-discount"><?php _e('Discount value', 'uni-cpo') ?></label>
                        <input type="text" name="uni_cpo_discount1" value="" id="uni-cpo-discount-input1" class="input-uni-discount long wc_input_price" />
                    </p>
                    <div style="clear:both;"></div>
                </div>
        <?php } ?>
                <div id="addDelButtons">
                    <span id="uniBtnAdd" class="uni-clone-add dashicons dashicons-yes"><b><?php _e( 'Add new', 'uni-cpo') ?></b></span> <span id="uniBtnDel" class="uni-clone-delete dashicons dashicons-no"><b><?php _e( 'Delete', 'uni-cpo') ?></b></span>
                </div>

        </div>
<?php
}
?>