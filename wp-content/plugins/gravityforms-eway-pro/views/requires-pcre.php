<?php
if (!defined('ABSPATH')) {
	exit;
}
?>

<div class="error">
	<p><?php printf(__('Gravity Forms eWAY Pro requires <a target="_blank" href="%1$s">PCRE</a> version %2$s or higher; your website has PCRE version %3$s', 'gravityforms-eway-pro'),
		'http://php.net/manual/en/book.pcre.php', esc_html($pcre_min), esc_html(PCRE_VERSION)); ?></p>
</div>
