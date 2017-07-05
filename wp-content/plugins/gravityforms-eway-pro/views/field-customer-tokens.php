<?php
if (!defined('ABSPATH')) {
	exit;
}

/*
inputs:

.1 = token/card to use for transaction (empty for new card)
.2 = security code for token/card
.3 = checkbox for customer asking to remember card

*/
?>

<div class="ginput_complex ginput_container ginput_container_gfewaypro_cust_tokens <?php echo $css; ?>" id="<?php echo esc_attr($field_id); ?>" data-gfeway_conditional_fields="<?php echo esc_attr(implode(',', $conditional_fields)); ?>">

	<?php if (count($tokenlist) > 0): ?>

		<span class="gfewaypro_cust_tokens_list">

			<span class="gfewaypro_cust_tokens_cardlist ginput_container_radio ginput_left">
				<ul class="gfield_radio">
					<?php $i = 1; foreach ($tokenlist as $token => $card) { ?>
					<li>
						<input type="radio" name="input_<?php echo esc_attr($id); ?>.1" id="<?php echo esc_attr($field_id); ?>_1_<?php echo $i; ?>" value="<?php echo esc_attr($token); ?>" <?php checked($token, $current_token); ?> <?php echo $this->get_tabindex(), $disabled_text; ?> />
						<label for="<?php echo esc_attr($field_id); ?>_1_<?php echo $i; ?>"><?php echo esc_html($card); ?></label>
					</li>
					<?php $i++; } ?>

					<li>
						<input type="radio" name="input_<?php echo esc_attr($id); ?>.1" id="<?php echo esc_attr($field_id); ?>_1_0" value="" <?php checked('', $current_token); ?> <?php echo $this->get_tabindex(), $disabled_text; ?> />
						<?php /* translators: list of previous customer cards, this option lets customer enter a new card */ ?>
						<label for="<?php echo esc_attr($field_id); ?>_1_0"><?php echo esc_html_x('Use a new card', 'customer token field label', 'gravityforms-eway-pro'); ?></label>
					</li>
				</ul>
			</span>

			<span class="gfewaypro_cust_tokens_inputs ginput_right">

				<span class="gfewaypro_cust_tokens_remember">

					<?php if ($cust_can_ask_remember): ?>

					<input type="checkbox" name="input_<?php echo esc_attr($id); ?>.3" id="<?php echo esc_attr($field_id); ?>_3" value="1" <?php echo $this->get_tabindex(), $disabled_text; ?> />
					<label for="<?php echo esc_attr($field_id); ?>_3"><?php echo esc_html_x('Remember card securely with eWAY', 'customer token field label', 'gravityforms-eway-pro'); ?></label>

					<?php else: ?>

					<input type="hidden" name="input_<?php echo esc_attr($id); ?>.3" id="<?php echo esc_attr($field_id); ?>_3" value="" />
					<?php echo esc_html_x('Your card will be remembered securely with eWAY.', 'customer token field label', 'gravityforms-eway-pro'); ?>

					<?php endif; ?>

				</span>

				<span class="gfewaypro_cust_tokens_security_code">

					<?php if ($is_sub_label_above): ?>
					<label for="<?php echo esc_attr($field_id); ?>_2"><?php echo esc_html_x('Security Code', 'customer token field label', 'gravityforms-eway-pro'); ?></label>
					<?php endif; ?>

					<input type="text" maxlength="4" name="input_<?php echo esc_attr($id); ?>.2" id="<?php echo esc_attr($field_id); ?>_2" <?php echo $this->get_tabindex(), $disabled_text, $cvv_validation; ?> data-gfeway-encrypt-name="EWAY_CARDCVN" />

					<?php if (!$is_sub_label_above): ?>
					<label for="<?php echo esc_attr($field_id); ?>_2"><?php echo esc_html_x('Security Code', 'customer token field label', 'gravityforms-eway-pro'); ?></label>
					<?php endif; ?>

				</span>

			</span>

		</span>

	<?php else: ?>

		<input type="hidden" name="input_<?php echo esc_attr($id); ?>.1" id="<?php echo esc_attr($field_id); ?>_1" value="" />
		<input type="hidden" name="input_<?php echo esc_attr($id); ?>.2" id="<?php echo esc_attr($field_id); ?>_2" value="" />

		<span class="gfewaypro_cust_tokens_remember">

			<?php if ($cust_can_ask_remember): ?>

			<input type="checkbox" name="input_<?php echo esc_attr($id); ?>.3" id="<?php echo esc_attr($field_id); ?>_3" value="1" <?php echo $this->get_tabindex(), $disabled_text; ?> />
			<label for="<?php echo esc_attr($field_id); ?>_3"><?php echo esc_html_x('remember card securely with eWAY', 'customer token field label', 'gravityforms-eway-pro'); ?></label>

			<?php else: ?>

			<input type="hidden" name="input_<?php echo esc_attr($id); ?>.3" id="<?php echo esc_attr($field_id); ?>_3" value="" />
			<?php echo esc_html_x('Your card will be remembered securely with eWAY.', 'customer token field label', 'gravityforms-eway-pro'); ?>

			<?php endif; ?>

		</span>

	<?php endif; ?>

</div>
