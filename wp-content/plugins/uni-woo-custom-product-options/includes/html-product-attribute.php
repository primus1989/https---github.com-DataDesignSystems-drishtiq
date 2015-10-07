<div data-taxonomy="<?php echo esc_attr( $taxonomy ); ?>" class="woocommerce_attribute wc-metabox closed <?php echo esc_attr( implode( ' ', $metabox_class ) ); ?>" rel="<?php echo $position; ?>">
	<h3>
		<button type="button" class="remove_row button"><?php _e( 'Remove', 'woocommerce' ); ?></button>
		<div class="handlediv" title="<?php _e( 'Click to toggle', 'woocommerce' ); ?>"></div>
		<strong class="attribute_name"><?php echo esc_html( $attribute_label ); ?></strong>
	</h3>
	<div class="woocommerce_attribute_data wc-metabox-content">
		<table cellpadding="0" cellspacing="0">
			<tbody>
				<tr>
					<td class="attribute_name">
						<label><?php _e( 'Name', 'woocommerce' ); ?>:</label>

						<?php if ( $attribute['is_taxonomy'] ) : ?>
							<strong><?php echo esc_html( $attribute_label ); ?></strong>
							<input type="hidden" name="attribute_names[<?php echo $i; ?>]" value="<?php echo esc_attr( $taxonomy ); ?>" />
						<?php else : ?>
							<input type="text" class="attribute_name" name="attribute_names[<?php echo $i; ?>]" value="<?php echo esc_attr( $attribute['name'] ); ?>" />
						<?php endif; ?>

						<input type="hidden" name="attribute_position[<?php echo $i; ?>]" class="attribute_position" value="<?php echo esc_attr( $position ); ?>" />
						<input type="hidden" name="attribute_is_taxonomy[<?php echo $i; ?>]" value="<?php echo $attribute['is_taxonomy'] ? 1 : 0; ?>" />
					</td>
					<td rowspan="3">
						<label><?php _e( 'Value(s)', 'woocommerce' ); ?>:</label>

						<?php if ( $attribute['is_taxonomy'] ) : ?>
							<?php if ( 'select' === $attribute_taxonomy->attribute_type ) : ?>

								<select multiple="multiple" data-placeholder="<?php _e( 'Select terms', 'woocommerce' ); ?>" class="multiselect attribute_values wc-enhanced-select" name="attribute_values[<?php echo $i; ?>][]">
									<?php
									$all_terms = get_terms( $taxonomy, 'orderby=name&hide_empty=0' );
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
											echo '<option value="' . esc_attr( $term->slug ) . '" ' . selected( $bAttrIs, true, false ) . '>' . $term->name . '</option>';
										}
									}
									?>
								</select>
								<button class="button plus select_all_attributes"><?php _e( 'Select all', 'woocommerce' ); ?></button>
								<button class="button minus select_no_attributes"><?php _e( 'Select none', 'woocommerce' ); ?></button>
								<button class="button fr plus add_new_attribute"><?php _e( 'Add new', 'woocommerce' ); ?></button>

							<?php // Uni CPO doesn't use this feature at the moment
                                /*elseif ( 'text' == $attribute_taxonomy->attribute_type ) : ?>

								<input type="text" name="attribute_values[<?php echo $i; ?>]" value="<?php

									// Text attributes should list terms pipe separated
									echo esc_attr( implode( ' ' . WC_DELIMITER . ' ', wp_get_post_terms( $thepostid, $taxonomy, array( 'fields' => 'names' ) ) ) );

								?>" placeholder="<?php echo esc_attr( sprintf( __( '"%s" separate terms', 'woocommerce' ), WC_DELIMITER ) ); ?>" />

							<?php */ endif; ?>

							<?php do_action( 'woocommerce_product_option_terms', $attribute_taxonomy, $i ); ?>

						<?php /*else : ?>

							<textarea name="attribute_values[<?php echo $i; ?>]" cols="5" rows="5" placeholder="<?php echo esc_attr( sprintf( __( 'Enter some text, or some attributes by "%s" separating values.', 'woocommerce' ), WC_DELIMITER ) ); ?>"><?php echo esc_textarea( $attribute['value'] ); ?></textarea>

						<?php */ endif; ?>
					</td>
				</tr>
				<tr>
					<td>
						<label><input type="checkbox" class="checkbox" <?php checked( $attribute['is_visible'], 1 ); ?> name="attribute_visibility[<?php echo $i; ?>]" value="1" /> <?php _e( 'Visible on the product page', 'woocommerce' ); ?></label>
					</td>
				</tr>
				<tr>
					<td>
					    <label><input type="checkbox" class="checkbox" <?php if ( isset( $attribute['is_cpo_required'] ) ) checked( $attribute['is_cpo_required'], 1 ); ?> name="attribute_cpo_require[<?php echo $i; ?>]" value="1" /> <?php _e( 'Must be filled/selected by user', 'uni-cpo' ); ?></label>
					</td>
				</tr>
											<tr>
												<td>
													<label>
					                                    <select name="attribute_cpo_type[<?php echo $i; ?>]">
						                                    <option value="select"<?php if ( isset( $attribute['cpo_type'] ) ) selected($attribute['cpo_type'], 'select') ?>><?php _e( 'Dropdown (default)', 'uni-cpo' ); ?></option>
                                                            <option value="input"<?php if ( isset( $attribute['cpo_type'] ) ) selected($attribute['cpo_type'], 'input') ?>><?php _e( 'Text input', 'uni-cpo' ); ?></option>
                                                            <option value="input_number"<?php if ( isset( $attribute['cpo_type'] ) ) selected($attribute['cpo_type'], 'input_number') ?>><?php _e( 'Text input number', 'uni-cpo' ); ?></option>
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
</div>