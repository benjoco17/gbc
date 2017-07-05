<?php
if (!defined('ABSPATH')) {
	exit;
}
?>

<?php $this->settings_text($key_field); ?>

<?php /* translators: button for deactivating software license */ ?>
<button id="gfewaypro-license-deactivate" class="button button-secondary" style="display:none">
	<?php echo esc_html_x('Deactivate', 'license activation', 'gravityforms-eway-pro'); ?>
</button>

<?php /* translators: button for activating software license */ ?>
<button id="gfewaypro-license-activate" class="button button-secondary" style="display:none">
	<?php echo esc_html_x('Activate', 'license activation', 'gravityforms-eway-pro'); ?>
</button>

<span id="gfewaypro-license-status" data-license-status="<?php echo esc_attr($status); ?>"><?php echo esc_html($status); ?></span>

