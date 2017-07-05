<?php

if (!defined('ABSPATH')) {
	exit;
}

/**
* custom exception types
*/
class GFEwayProException extends Exception {}
class GFEwayProCurlException extends Exception {}

/**
* class for managing the plugin
*/
class GFEwayProPlugin {

	public $options;									// array of plugin options

	// minimum versions required
	const MIN_VERSION_GF		= '2.0';

	/**
	* static method for getting the instance of this singleton object
	* @return self
	*/
	public static function getInstance() {
		static $instance = null;

		if (is_null($instance)) {
			$instance = new self();
		}

		return $instance;
	}

	/**
	* initialise plugin
	*/
	private function __construct() {
		spl_autoload_register(array(__CLASS__, 'autoload'));

		add_action('gform_loaded', array($this, 'addonInit'));
		add_action('init', array($this, 'loadTextDomain'));

		if (is_admin()) {
			require GFEWAYPRO_PLUGIN_ROOT . 'includes/class.GFEwayProAdmin.php';
			new GFEwayProAdmin();
		}
	}

	/**
	* initialise the Gravity Forms add-on
	*/
	public function addonInit() {
		if (!method_exists('GFForms', 'include_feed_addon_framework')) {
			return;
		}

		if (self::hasMinimumGF()) {
			// load add-on framework and hook our add-on
			GFForms::include_feed_addon_framework();

			require GFEWAYPRO_PLUGIN_ROOT . 'includes/class.GFEwayProCustomerTokens.php';

			require GFEWAYPRO_PLUGIN_ROOT . 'includes/class.GFEwayProAddOn.php';
			GFAddOn::register('GFEwayProAddOn');

			// no need to load text domain now, Gravity Forms will do it for us
			remove_action('init', array($this, 'loadTextDomain'));
		}
	}

	/**
	* load text translations
	* Gravity Forms loads text domain for add-ons, so this won't be called if the add-on was registered
	*/
	public function loadTextDomain() {
		load_plugin_textdomain('gravityforms-eway-pro', false, plugin_basename(dirname(GFEWAYPRO_PLUGIN_FILE)) . '/languages/');
	}

	/**
	* compare Gravity Forms version against target
	* @param string $target
	* @param string $operator
	* @return bool
	*/
	public static function versionCompareGF($target, $operator) {
		if (class_exists('GFCommon', false)) {
			return version_compare(GFCommon::$version, $target, $operator);
		}

		return false;
	}

	/**
	* compare Gravity Forms version against minimum required version
	* @return bool
	*/
	public static function hasMinimumGF() {
		return self::versionCompareGF(self::MIN_VERSION_GF, '>=');
	}

	/**
	* autoload classes as/when needed
	* @param string $class_name name of class to attempt to load
	*/
	public static function autoload($class_name) {
		static $classMap = array (
			'GFEwayProEddUpdater'				=> 'includes/class.GFEwayProEddUpdater.php',
			'GFEwayProFormData'					=> 'includes/class.GFEwayProFormData.php',
			'GFEwayProNotification'				=> 'includes/class.GFEwayProNotification.php',
			'GFEwayProPayment'					=> 'includes/class.GFEwayProPayment.php',
			'GFEwayProResponse'					=> 'includes/class.GFEwayProResponse.php',
			'GFEwayProResponseCustomerQuery'	=> 'includes/class.GFEwayProResponseCustomerQuery.php',
			'GFEwayProResponseDirectPayment'	=> 'includes/class.GFEwayProResponseDirectPayment.php',
			'GFEwayProResponseQuery'			=> 'includes/class.GFEwayProResponseQuery.php',
			'GFEwayProResponseRecurringXML'		=> 'includes/class.GFEwayProResponseRecurringXML.php',
			'GFEwayProResponseSharedPage'		=> 'includes/class.GFEwayProResponseSharedPage.php',
		);

		if (isset($classMap[$class_name])) {
			require GFEWAYPRO_PLUGIN_ROOT . $classMap[$class_name];
		}
	}

}
