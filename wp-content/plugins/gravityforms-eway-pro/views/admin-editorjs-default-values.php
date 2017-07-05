<?php
if (!defined('ABSPATH')) {
	exit;
}
?>

case "<?php echo self::GFIELD_TYPE; ?>":
	if (!field.label) {
		field.label = <?php echo json_encode(esc_html_x('Remembered Cards', 'default field label', 'gravityforms-eway-pro')); ?>;
	}
	field.inputs = [
		new Input(field.id + ".1", <?php echo json_encode(esc_html_x('Remembered Card', 'customer token field label', 'gravityforms-eway-pro')); ?>),
		new Input(field.id + ".2", <?php echo json_encode(esc_html_x('Security Code', 'customer token field label', 'gravityforms-eway-pro')); ?>),
		new Input(field.id + ".3", <?php echo json_encode(esc_html_x('Remember card securely with eWAY', 'customer token field label', 'gravityforms-eway-pro')); ?>)
	];
	break;

