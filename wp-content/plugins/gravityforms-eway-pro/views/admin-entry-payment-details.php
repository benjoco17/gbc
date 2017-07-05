<?php
if (!defined('ABSPATH')) {
	exit;
}
?>

<?php if ($authcode): ?>
<div class="gf_payment_detail">
	<?php echo esc_html_x('AuthCode:', 'entry details', 'gravityforms-eway-pro') ?>
	<span id="gfewaypro_payment_authcode"><?php echo esc_html($authcode); ?></span>
</div>
<?php endif; ?>

<?php if ($beagle_score): ?>
<div class="gf_payment_detail">
	<?php echo esc_html_x('Beagle Score:', 'entry details', 'gravityforms-eway-pro') ?>
	<span id="gfewaypro_payment_beagle_score"><?php echo esc_html($beagle_score); ?></span>
</div>
<?php endif; ?>

<?php if ($eway_token): ?>
<div class="gf_payment_detail">
	<?php echo esc_html_x('Customer Token:', 'entry details', 'gravityforms-eway-pro') ?>
	<span id="gfewaypro_payment_eway_token"><?php echo esc_html($eway_token); ?></span>
</div>
<?php endif; ?>
