<?php
/**
 * @package   Gravity_Forms_Styler
 * @author    WordpressGurus <support@wordpressgurus.net>
 * @license   GPL-2.0+
 * @link      http://www.wordpressgurus.net
 * @copyright 2014 WordpressGurus
 *
 * @wordpress-plugin
 * Plugin Name: Gravity Forms Styler
 * Plugin URI:  http://codecanyon.net/item/gravity-forms-styler/6817645
 * Description: Style your gravity forms beautifully.
 * Version:     2.0
 * Author:      WordpressGurus
 * Author URI:  http://www.wordpressgurus.net
 * Text Domain: gravity-forms-styler
 * License:     GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * Domain Path: /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

require_once( plugin_dir_path( __FILE__ ) . 'class-gravity-forms-styler.php' );

require_once( plugin_dir_path( __FILE__ ) . '/includes/widget-gravity_forms_styler.php' );

// Register hooks that are fired when the plugin is activated or deactivated.
// When the plugin is deleted, the uninstall.php file is loaded.
register_activation_hook( __FILE__, array( 'Gravity_Forms_Styler', 'activate' ) );
register_deactivation_hook( __FILE__, array( 'Gravity_Forms_Styler', 'deactivate' ) );

// Load instance
add_action( 'plugins_loaded', array( 'Gravity_Forms_Styler', 'get_instance' ) );
//Gravity_Forms_Styler::get_instance();
?>