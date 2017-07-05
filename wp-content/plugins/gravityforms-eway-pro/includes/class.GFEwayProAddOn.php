<?php

if (!defined('ABSPATH')) {
	exit;
}

/**
* implement a Gravity Forms Add-on instance
*/
class GFEwayProAddOn extends GFFeedAddOn {

	protected $validationMessages;						// any validation messages picked up for the form as a whole
	protected $formData = array();						// data for each feed mapped to form
	protected $urlPaymentForm;							// URL for payment form where purchaser will enter credit card details
	protected $feed = null;								// current feed mapping form fields to payment fields
	protected $currency = null;							// current currency as detected in validation step, via feed settings
	protected $txResult = null;							// results from Direction Connection credit card payment transaction
	protected $error_msg;								// temporary store for eWAY error message during payment request processing
	protected $customerToken;							// new customer token from transaction
	protected $customerTokenCard;						// new customer token card number from transaction
	protected $feedAdminArgs;							// array -- script arguments for feed admin page
	protected $feedDefaultFieldMap;						// map of default fields for feed
	protected $customFields;							// array -- the custom fields that are registered for this add-on
	protected $ecryptManager;							// manage Client Side Encryption
	protected $eddUpdater = false;

	// Gravity Forms payment_gateway meta value
	const PAYMENT_GATEWAY		= 'gfewaypro';

	// end point for return to website
	const GFEWAY_RETURN			= '__gfewayproreturn';
	const GFEWAY_CONFIRM		= '__gfewayproconfirm';
	const GFEWAY_HASH			= '__gfewayprohash';

	/**
	* static method for getting the instance of this singleton object
	* @return self
	*/
	public static function get_instance() {
		static $instance = null;

		if (is_null($instance)) {
			$instance = new self();
		}

		return $instance;
	}

	/**
	* declare detail to GF Add-On framework
	*/
	public function __construct() {
		$this->_version						= GFEWAYPRO_PLUGIN_VERSION;
		$this->_min_gravityforms_version	= GFEwayProPlugin::MIN_VERSION_GF;
		$this->_slug						= 'gravityforms-eway-pro';
		$this->_path						= GFEWAYPRO_PLUGIN_NAME;
		$this->_full_path					= GFEWAYPRO_PLUGIN_FILE;
		$this->_title						= 'eWAY Payments Pro';			// NB: no localisation yet
		$this->_short_title					= 'eWAY Payments';				// NB: no localisation yet

		// define capabilities in case role/permissions have been customised (e.g. Members plugin)
		$this->_capabilities_settings_page	= 'gravityforms_edit_settings';
		$this->_capabilities_form_settings	= 'gravityforms_edit_forms';
		$this->_capabilities_uninstall		= 'gravityforms_uninstall';

		parent::__construct();

		add_action('init', array($this, 'lateLocalise'), 50);
		add_action('gform_enable_credit_card_field', '__return_true');
		add_filter('gform_pre_render', array($this, 'detectFeedCurrency'));
		add_filter('gform_validation', array($this, 'gformValidation'), 100);
		add_filter('gform_validation_message', array($this, 'gformValidationMessage'), 10, 2);
		add_filter('gform_custom_merge_tags', array($this, 'gformCustomMergeTags'), 10, 4);
		add_filter('gform_replace_merge_tags', array($this, 'gformReplaceMergeTags'), 10, 7);
		add_filter('gform_entry_post_save', array( $this, 'maybeProcessFeed' ), 9, 2);

		// handle the new Payment Details box
		add_action('gform_payment_details', array($this, 'gformPaymentDetails'), 10, 2);

		// return URL from eWAY
		add_filter('do_parse_request',  array($this, 'processReturn'));
		add_action('wp', array($this, 'processFormConfirmation'), 5);		// process redirect to GF confirmation

		// handle deferrals
		add_filter('gform_is_delayed_pre_process_feed', array($this, 'gformIsDelayed'), 10, 4);
		add_filter('gform_disable_post_creation', array($this, 'gformDelayPost'), 10, 3);
		add_filter('gform_disable_notification', array($this, 'gformDelayNotification'), 10, 4);
		add_action('gform_after_submission', array($this, 'gformDelayOther'), 5, 2);

		// catch user registration so that we can attach customer token to it
		add_action('gform_user_registered', array($this, 'userAttachCustomerToken'), 10, 3);

		// maybe suppress free Gravity Forms eWAY add-on
		add_filter('gfeway_form_is_eway', array($this, 'maybeSuppressFreeAddon'), 10, 2);

		if (is_admin()) {
			$this->feedAdminArgs = array();
			add_action('admin_print_footer_scripts', array($this, 'adminScriptLocalise'), 5);
		}

		$this->registerCustomFields();

		require GFEWAYPRO_PLUGIN_ROOT . 'includes/class.GFEwayProClientSideEncryption.php';
		$this->ecryptManager = new GFEwayProClientSideEncryption($this);

		add_filter('gform_add_field_buttons', array($this, 'gformAddFieldButtons'));
		add_filter('gform_tooltips', array($this, 'gformFieldTooltips'));
	}

	/**
	* register custom fields
	*/
	protected function registerCustomFields() {
		$this->customFields = array('GFEwayProFieldCustomerTokens');

		foreach ($this->customFields as $fieldname) {
			require GFEWAYPRO_PLUGIN_ROOT . "includes/class.$fieldname.php";
			call_user_func(array($fieldname, 'register'));
		}
	}

	/**
	* register form editor buttons for custom fields
	* @param array $button_groups
	* @return array
	*/
	public function gformAddFieldButtons($button_groups) {
		$buttons = array();

		foreach ($this->customFields as $fieldname) {
			$buttons = call_user_func(array($fieldname, 'addFieldButtons'), $buttons);
		}

		$button_groups[] = array(
			'name'		=> 'gfewaypro_fields',
			'label'		=> esc_html_x('Gravity Forms eWAY Pro', 'form editor buttons', 'gravityforms-eway-pro'),
			'fields'	=> $buttons,
		);

		return $button_groups;
	}

	/**
	* tooltip for form editor
	* @param array $tooltips
	* @return array
	*/
	public function gformFieldTooltips($tooltips) {
		$tooltips['form_gfewaypro_fields'] = __('Custom fields available to Gravity Forms eWAY Pro feeds.', 'gravityforms-eway-pro');

		return $tooltips;
	}

	/**
	* late localisation of strings, after load_plugin_textdomain() has been called
	*/
	public function lateLocalise() {
		$this->_title			= esc_html_x('eWAY Payments Pro', 'add-on full title', 'gravityforms-eway-pro');
		$this->_short_title		= esc_html_x('eWAY Payments', 'add-on short title', 'gravityforms-eway-pro');
	}

	/**
	* add our admin initialisation
	*/
	public function init_admin() {
		parent::init_admin();

		$this->loadEddUpdater();

		add_action('gform_payment_status', array($this, 'gformPaymentStatus' ), 10, 3);
		add_action('gform_after_update_entry', array($this, 'gformAfterUpdateEntry' ), 10, 2);
	}

	/**
	* add our AJAX handling
	*/
	public function init_ajax() {
		parent::init_ajax();

		$this->loadEddUpdater();
	}

	/**
	* load EDD Software Licensing update handler
	*/
	protected function loadEddUpdater() {
		// need to access settings directly, because they are not loaded into add-on yet
		$settings = $this->get_plugin_settings();
		$licenseKey = rgar($settings, 'eddLicense_key', '');

		$api_data = array(
			'version' 		=> GFEWAYPRO_PLUGIN_VERSION,
			'license'		=> $licenseKey,
			'item_name' 	=> 'Gravity Forms eWAY Pro',
			'author'	 	=> 'WebAware',
			'base_dir'		=> GFEWAYPRO_PLUGIN_ROOT,
			'status_key'	=> 'gfewaypro_license_status',
		);
		$this->eddUpdater = new GFEwayProEddUpdater('https://shop.webaware.com.au/', GFEWAYPRO_PLUGIN_FILE, $api_data);
	}

	/**
	* if form has a feed for this add-on, then suppress free eWAY add-on
	* @param bool $allow_free_addon
	* @param int $form_id
	* @return bool
	*/
	public function maybeSuppressFreeAddon($allow_free_addon, $form_id) {
		if ($this->has_feed($form_id)) {
			$allow_free_addon = false;
		}

		return $allow_free_addon;
	}

	/**
	* enqueue required styles
	*/
	public function styles() {
		$ver = SCRIPT_DEBUG ? time() : GFEWAYPRO_PLUGIN_VERSION;

		$styles = array(

			array(
				'handle'		=> 'gfewaypro_admin',
				'src'			=> plugins_url('css/admin.css', GFEWAYPRO_PLUGIN_FILE),
				'version'		=> $ver,
				'enqueue'		=> array(
										array(
											'admin_page'	=> array('plugin_settings', 'form_settings'),
											'tab'			=> array($this->_slug),
										),
										array(
											'admin_page'	=> array('form_editor'),
										),
									),
			),

			array(
				'handle'		=> 'gfewaypro_front',
				'src'			=> plugins_url('css/front.css', GFEWAYPRO_PLUGIN_FILE),
				'version'		=> $ver,
				'enqueue'		=> array(
										array('field_types' => array('gfewaypro_cust_tokens')),
									),
			),

		);

		return array_merge(parent::styles(), $styles);
	}

	/**
	* enqueue required scripts
	*/
	public function scripts() {
		$min = SCRIPT_DEBUG ? '' : '.min';
		$ver = SCRIPT_DEBUG ? time() : GFEWAYPRO_PLUGIN_VERSION;

		$scripts = array(

			array(
				'handle'		=> 'gfewaypro_settings_admin',
				'src'			=> plugins_url("js/settings-admin$min.js", GFEWAYPRO_PLUGIN_FILE),
				'version'		=> $ver,
				'deps'			=> array('jquery'),
				'in_footer'		=> true,
				'enqueue'		=> array( array(
										'admin_page'	=> array('plugin_settings'),
										'tab'			=> array($this->_slug),
									)),
			),

			array(
				'handle'		=> 'gfewaypro_editor_admin',
				'src'			=> plugins_url("js/editor-admin$min.js", GFEWAYPRO_PLUGIN_FILE),
				'version'		=> $ver,
				'deps'			=> array('jquery'),
				'in_footer'		=> true,
				'enqueue'		=> array( array(
										'admin_page'	=> array('form_editor'),
									)),
				'strings'		=> $this->getFormEditorStrings(),
			),

			array(
				'handle'		=> 'gfewaypro_feed_admin',
				'src'			=> plugins_url("js/feed-admin$min.js", GFEWAYPRO_PLUGIN_FILE),
				'version'		=> $ver,
				'deps'			=> array('jquery'),
				'in_footer'		=> true,
				'enqueue'		=> array( array(
										'admin_page'	=> array('form_settings'),
										'tab'			=> array($this->_slug),
									)),
			),

			array(
				'handle'		=> 'eway-ecrypt',
				'src'			=> "https://secure.ewaypayments.com/scripts/eCrypt$min.js",
				'version'		=> null,
				'deps'			=> array('jquery'),
				'in_footer'		=> true,
				'enqueue'		=> array( array($this->ecryptManager, 'canEnqueueEncryptScripts') ),
			),

		);

		return array_merge(parent::scripts(), $scripts);
	}

	/**
	* get localised strings for the form editor script
	* @return array
	*/
	protected function getFormEditorStrings() {
		$strings = array();

		foreach ($this->customFields as $fieldname) {
			$strings = call_user_func(array($fieldname, 'addFormEditorStrings'), $strings);
		}

		return $strings;
	}

	/**
	* localise admin scripts if anything to localise
	*/
	public function adminScriptLocalise() {
		if (wp_script_is('gfewaypro_feed_admin', 'enqueued')) {
			wp_localize_script('gfewaypro_feed_admin', 'gfewaypro_feed', $this->feedAdminArgs);
		}
	}

	/**
	* set full title of add-on as settings page title
	* @return string
	*/
	public function plugin_settings_title() {
		return esc_html__('eWAY Payments Pro Settings', 'gravityforms-eway-pro');
	}

	/**
	* set icon for settings page
	* @return string
	*/
	public function plugin_settings_icon() {
		return '<i class="fa fa-credit-card" aria-hidden="true"></i>';
	}

	/**
	* specify the settings fields to be rendered on the plugin settings page
	* @return array
	*/
	public function plugin_settings_fields() {
		$settings = array (
			array (
				'title'					=> esc_html__('Live settings', 'gravityforms-eway-pro'),
				'description'			=> esc_html__('These are default settings. Feeds can specify different settings to override these settings.', 'gravityforms-eway-pro'),
				'fields'				=> array (

					array (
						'name'			=> 'apiKey',
						'label'			=> esc_html_x('API Key', 'feed field name', 'gravityforms-eway-pro'),
						'type'			=> 'text',
						'class'			=> 'large',
						'tooltip'		=> esc_html__('eWAY Rapid API key, from your MYeWAY console.', 'gravityforms-eway-pro'),
					),

					array (
						'name'			=> 'password',
						'label'			=> esc_html_x('API Password', 'feed field name', 'gravityforms-eway-pro'),
						'type'			=> 'text',
						'class'			=> 'regular',
						'tooltip'		=> esc_html__('eWAY Rapid API password, from your MYeWAY console.', 'gravityforms-eway-pro'),
					),

					array (
						'name'			=> 'ecryptKey',
						'label'			=> esc_html_x('Client Side Encryption Key', 'feed field name', 'gravityforms-eway-pro'),
						'type'			=> 'textarea',
						'class'			=> 'large',
						'style'			=> 'height: 9em',
						'tooltip'		=> esc_html__("Securely encrypts sensitive credit card information in the customer's browser, so that you can accept credit cards on your website without full PCI certification. Not required for Responsive Shared Page forms.", 'gravityforms-eway-pro'),
					),

					array (
						'name'			=> 'customerID',
						'label'			=> esc_html_x('Customer ID', 'feed field name', 'gravityforms-eway-pro'),
						'type'			=> 'text',
						'class'			=> 'regular',
						'tooltip'		=> esc_html__('eWAY customer ID, required for Recurring Payments XML API; from your MYeWAY console.', 'gravityforms-eway-pro'),
					),

				),
			),

			array(
				'title'					=> esc_html__('Sandbox settings', 'gravityforms-eway-pro'),
				'description'			=> esc_html__('These are default settings. Feeds can specify different settings to override these settings.', 'gravityforms-eway-pro'),
				'fields'				=> array (

					array (
						'name'			=> 'sandboxApiKey',
						'label'			=> esc_html_x('API Key', 'feed field name', 'gravityforms-eway-pro'),
						'type'			=> 'text',
						'class'			=> 'large',
						'tooltip'		=> esc_html__('eWAY Rapid API key, from your MYeWAY console.', 'gravityforms-eway-pro'),
					),

					array (
						'name'			=> 'sandboxPassword',
						'label'			=> esc_html_x('API Password', 'feed field name', 'gravityforms-eway-pro'),
						'type'			=> 'text',
						'class'			=> 'regular',
						'tooltip'		=> esc_html__('eWAY Rapid API password, from your MYeWAY console.', 'gravityforms-eway-pro'),
					),

					array (
						'name'			=> 'sandboxEcryptKey',
						'label'			=> esc_html_x('Client Side Encryption Key', 'feed field name', 'gravityforms-eway-pro'),
						'type'			=> 'textarea',
						'class'			=> 'large',
						'style'			=> 'height: 9em',
						'tooltip'		=> esc_html__("Securely encrypts sensitive credit card information in the customer's browser, so that you can accept credit cards on your website without full PCI certification. Not required for Responsive Shared Page forms.", 'gravityforms-eway-pro'),
					),

				),
			),

			array(
				'title'					=> esc_html__('Licensing', 'gravityforms-eway-pro'),
				'description'			=> esc_html__('The software license key for this add-on, for automatic updates.', 'gravityforms-eway-pro'),
				'fields'				=> array (

					array(
						'name'			=> 'eddLicense',
						'label'			=> _x('License key', 'feed field name', 'gravityforms-eway-pro'),
						'type'			=> 'edd_license',
					),

                    array(
                        'type'			=> 'save',
                        'messages'		=> array('success' => esc_html__('Settings updated', 'gravityforms-eway-pro')),
                    )

				),
			),
		);

		return $settings;
	}

	/**
	* show license key settings field
	* @param array $field
	* @param bool $echo
	* @return string
	*/
	public function settings_edd_license($field, $echo = true) {
		$key_field = array (
			'name'			=> $field['name'] . '_key',
			'type'			=> 'text',
			'class'			=> 'medium',
		);

		$licenseKey = $this->get_setting($key_field['name'], '');
		$status = $this->eddUpdater->licenseCheck();

		ob_start();
		require GFEWAYPRO_PLUGIN_ROOT . 'views/admin-settings-edd-license.php';
		$html = ob_get_clean();

		if ($echo) {
			echo $html;
		}

		return $html;
	}

	/**
	* watch for license key changes and act upon them
	* @param array $settings
	*/
	public function update_plugin_settings($settings) {
		parent::update_plugin_settings($settings);

		// if license key has changed, get new license status
		$oldSettings = $this->get_previous_settings();
		if (rgar($settings, 'eddLicense_key') !== rgar($oldSettings, 'eddLicense_key')) {
			$this->eddUpdater->setLicense(rgar($settings, 'eddLicense_key'));
		}
	}

	/**
	* check form for conditions that require a warning message
	* @param array $form
	*/
	public function form_settings($form) {
		if ($this->is_form_settings($this->_slug)) {
			$feed_id = $this->get_current_feed_id();
			if ($feed_id) {
				// it's a feed page, check for credit card field and see if it should be there
				$feed = $this->get_feed($feed_id);
				$feedMethod = rgar($feed['meta'], 'feedMethod', '');
				if (!empty($_POST['_gaddon_setting_feedMethod'])) {
					// saving, so use new setting instead of old setting
					$feedMethod = $_POST['_gaddon_setting_feedMethod'];
				}

				$hasCCfield = GFCommon::has_credit_card_field($form);

				if (!empty($feedMethod)) {
					// Direct Connection / Recurring Payments require a credit card field, others should not have credit card field
					if (!$hasCCfield && $feedMethod === 'direct') {
						GFCommon::add_message(__('NB: Direct Connection requires a Credit Card field on the form', 'gravityforms-eway-pro'));
					}
					elseif (!$hasCCfield && $feedMethod === 'recurxml') {
						GFCommon::add_message(__('NB: Recurring Payments requires a Credit Card field on the form', 'gravityforms-eway-pro'));
					}
					elseif ($hasCCfield && $feedMethod !== 'direct' && $feedMethod !== 'recurxml') {
						GFCommon::add_message(__('NB: Your form has a Credit Card field, which is not required for the selected Integration Method', 'gravityforms-eway-pro'));
					}
				}

			}
		}

		parent::form_settings($form);
	}

	/**
	* title of feed settings
	* @return string
	*/
	public function feed_settings_title() {
		return esc_html__('eWAY Payments Transaction Settings', 'gravityforms-eway-pro');
	}

	/**
	* configure the fields in a feed
	* @return array
	*/
	public function feed_settings_fields() {
		$this->setFeedDefaultFieldMap();

		$fields = array(

			#region "core settings"

			array(
				'fields' => array(

					array(
						'name'   		=> 'feedName',
						'label'  		=> esc_html_x('Feed name', 'feed field name', 'gravityforms-eway-pro'),
						'type'   		=> 'text',
						'class'			=> 'medium',
						'tooltip'		=> esc_html__('Give this feed a name, to differentiate it from other feeds.', 'gravityforms-eway-pro'),
						'required'		=> '1',
					),

					array(
						'name'   		=> 'useTest',
						'label'  		=> esc_html_x('Mode', 'payment transaction mode', 'gravityforms-eway-pro'),
						'type'   		=> 'radio',
						'tooltip'		=> esc_html__('Credit cards will not be processed in Test mode. Special card numbers must be used.', 'gravityforms-eway-pro'),
						'choices'		=> array(
							array('value' => '0', 'label' => esc_html_x('Live', 'payment transaction mode', 'gravityforms-eway-pro')),
							array('value' => '1', 'label' => esc_html_x('Test', 'payment transaction mode', 'gravityforms-eway-pro')),
						),
						'default_value'	=> '1',
					),

					array(
						'name'   		=> 'feedMethod',
						'label'  		=> esc_html_x('Integration Method', 'feed field name', 'gravityforms-eway-pro'),
						'type'   		=> 'radio',
						'tooltip'		=> esc_html__('Responsive Shared Page takes payers offsite to enter credit card details. Direct Connection and Recurring Payments require an SSL/TLS certificate and PCI compliance accredited by eWAY.', 'gravityforms-eway-pro'),
						'choices'		=> array(
							array('value' => 'shared',		'label' => esc_html_x('Responsive Shared Page', 'integration method', 'gravityforms-eway-pro')),
							array('value' => 'direct',		'label' => esc_html_x('Direct Connection', 'integration method', 'gravityforms-eway-pro')),
							array('value' => 'recurxml',	'label' => esc_html_x('Recurring Payments', 'integration method', 'gravityforms-eway-pro')),
						),
						'default_value'	=> 'shared',
					),

					array(
						'name'   		=> 'paymentMethod',
						'label'  		=> esc_html_x('Payment Method', 'feed field name', 'gravityforms-eway-pro'),
						'type'   		=> 'radio',
						'tooltip'		=> esc_html__("Capture processes the payment immediately. Authorize holds the amount on the customer's card for processing later.", 'gravityforms-eway-pro'),
						'choices'		=> array(
							array('value' => 'capture',		'label' => esc_html_x('Capture', 'payment method', 'gravityforms-eway-pro')),
							array('value' => 'preauth',		'label' => esc_html_x('Authorize', 'payment method', 'gravityforms-eway-pro')),
						),
						'default_value'	=> 'capture',
					),

					array(
						'name'			=> 'customConnection',
						'label'			=> esc_html_x('Customize Connection', 'feed field name', 'gravityforms-eway-pro'),
						'type'			=> 'checkbox',
						'tooltip'		=> esc_html__('You can use different connection settings and currency for each feed if you need to.', 'gravityforms-eway-pro'),
						'choices'		=> array(
							array('value' => '1', 'name' => 'custom_connection', 'label' => esc_html__('Override the default connection settings, just for this feed', 'gravityforms-eway-pro')),
						),
					),

				),
			),

			#endregion "core settings"

			#region "connection settings"

			array(
				'title'					=> esc_html__('Connection Settings', 'gravityforms-eway-pro'),
				'id'					=> 'gfewaypro-settings-connection',
				'fields' => array(

					array(
						'name'			=> 'apiKey',
						'label'			=> esc_html_x('API Key', 'feed field name', 'gravityforms-eway-pro'),
						'type'			=> 'text',
						'class'			=> 'large',
						'placeholder'	=> esc_html_x('Leave empty to use add-on settings', 'field placeholder', 'gravityforms-eway-pro'),
						'tooltip'		=> esc_html__('You can use a different API key for this feed, or leave it blank to use the add-on settings.', 'gravityforms-eway-pro'),
					),

					array(
						'name'			=> 'password',
						'label'			=> esc_html_x('API Password', 'feed field name', 'gravityforms-eway-pro'),
						'type'			=> 'text',
						'class'			=> 'medium',
						'placeholder'	=> esc_html_x('Leave empty to use add-on settings', 'field placeholder', 'gravityforms-eway-pro'),
						'tooltip'		=> esc_html__('You can use a different API password for this feed, or leave it blank to use the add-on settings.', 'gravityforms-eway-pro'),
					),

					array (
						'name'			=> 'ecryptKey',
						'label'			=> esc_html_x('Client Side Encryption Key', 'feed field name', 'gravityforms-eway-pro'),
						'type'			=> 'textarea',
						'class'			=> 'large',
						'style'			=> 'height: 9em',
						'placeholder'	=> esc_html_x('Leave empty to use add-on settings', 'field placeholder', 'gravityforms-eway-pro'),
						'tooltip'		=> esc_html__('You can use a different Client Side Encryption Key for this feed, or leave it blank to use the add-on settings.', 'gravityforms-eway-pro'),
					),

					array (
						'name'			=> 'customerID',
						'label'			=> esc_html_x('Customer ID', 'feed field name', 'gravityforms-eway-pro'),
						'type'			=> 'text',
						'class'			=> 'medium',
						'placeholder'	=> esc_html_x('Leave empty to use add-on settings', 'field placeholder', 'gravityforms-eway-pro'),
						'tooltip'		=> esc_html__('You can use a different Customer ID for this feed, or leave it blank to use the add-on settings.', 'gravityforms-eway-pro'),
					),

					array (
						'name'			=> 'currency',
						'label'			=> esc_html_x('Currency', 'feed field name', 'gravityforms-eway-pro'),
						'type'			=> 'select',
						'tooltip'		=> esc_html__('You can use a different currency for this feed, or use the Gravity Forms settings.', 'gravityforms-eway-pro'),
						'choices'		=> self::getCurrencies(__('Use default currency', 'gravityforms-eway-pro')),
					),

				),
			),

			#endregion "connection settings"

			#region "customer token settings"

			array(
				'title'					=> esc_html__('Token Payments Settings', 'gravityforms-eway-pro'),
				'id'					=> 'gfewaypro-settings-token-payments',
				'description'			=> sprintf('<p>%s</p><p>%s</p>',
												sprintf(__("With <a href='%s' target='_blank'>eWAY Token Payments</a>, you can have eWAY safely remember your customers' credit card numbers without storing them on your website. You can then manually process new token payments through MYeWAY.", 'gravityforms-eway-pro'), esc_url('https://www.eway.com.au/features/payments-token-payments')),

												__("If your customer is logged in (or you're creating a new WordPress user for them), your website can remember their customer token so that they can pay more quickly next time.", 'gravityforms-eway-pro')
											),
				'fields'				=> array(

					array(
						'name'   		=> 'rememberCustomer',
						'label'  		=> esc_html_x('Remember Customer', 'feed field name', 'gravityforms-eway-pro'),
						'type'   		=> 'gfeway_remember_customer',
						'tooltip'		=> esc_html__('Remember customers with eWAY token payments, so that they can be rebilled from MYeWAY and can make payments without entering their card details again.', 'gravityforms-eway-pro'),
					),

				),
			),

			#endregion "customer token settings"

			#region "recurring settings"

			array(
				'title'					=> esc_html__('Recurring Payments Settings', 'gravityforms-eway-pro'),
				'id'					=> 'gfewaypro-settings-recurring',
				'fields'				=> array(

					array(
						'name'			=> 'recurringAmount',
						'label'			=> esc_html_x('Recurring Amount', 'feed field name', 'gravityforms-eway-pro'),
						'type'			=> 'select',
						'choices'		=> $this->recurring_amount_choices(false),
						'tooltip'		=> esc_html__('Select which field determines the recurring payment amount, or select "Form Total" to use the total of all pricing fields as the recurring amount.', 'gravityforms-eway-pro'),
					),

					array(
						'name'			=> 'billingCycle',
						'label'			=> esc_html_x('Billing Cycle', 'feed field name', 'gravityforms-eway-pro'),
						'type'			=> 'gfeway_billing_cycle',
						'tooltip'		=> esc_html__('Select the interval between recurring payments.', 'gravityforms-eway-pro'),
					),

					array(
						'name'			=> 'recurringTimes',
						'label'			=> esc_html_x('Recurring Times', 'feed field name', 'gravityforms-eway-pro'),
						'type'			=> 'gfeway_recurring_times',
						'tooltip'		=> esc_html__('Select how many times the recurring payment should be made. The default is to bill the customer until the subscription is canceled.', 'gravityforms-eway-pro'),
					),

					array(
						'name'			=> 'initialAmount',
						'label'			=> esc_html_x('Initial Amount', 'feed field name', 'gravityforms-eway-pro'),
						'type'			=> 'select',
						'choices'		=> $this->recurring_amount_choices(true),
						'tooltip'		=> esc_html__('Select which field determines the initial payment amount, e.g. for an establishment charge or setup fee.', 'gravityforms-eway-pro'),
					),

					array(
						'name'			=> 'recurringStart',
						'label'			=> esc_html_x('Recurring Start', 'feed field name', 'gravityforms-eway-pro'),
						'type'			=> 'gfeway_recurring_start',
						'tooltip'		=> esc_html__('Select when recurring payments should start. The default is to start recurring payments immediately.', 'gravityforms-eway-pro'),
					),

				),
			),

			#endregion "recurring settings"

			#region "mapped fields"

			array(
				'title'					=> esc_html__('Mapped Field Settings', 'gravityforms-eway-pro'),
				'fields'				=> array(

					array(
						'name'			=> 'shippingAddress',
						'label'			=> esc_html_x('Shipping Address', 'feed field name', 'gravityforms-eway-pro'),
						'type'			=> 'radio',
						'choices'		=> array(
							array('value' => 'empty',   'label' => esc_html__('Shipping address is left empty', 'gravityforms-eway-pro')),
							array('value' => 'billing', 'label' => esc_html__('Shipping address is the same as Billing address', 'gravityforms-eway-pro')),
							array('value' => 'mapped',  'label' => esc_html__('Shipping address is from mapped fields, below', 'gravityforms-eway-pro')),
						),
						'default_value'	=> 'empty',
					),

					array(
						'name'			=> 'mappedFields',
						'type'			=> 'field_map',
						'field_map'		=> $this->billing_info_fields(),
					),

				),
			),

			#endregion "mapped fields"

			#region "shared page settings"

			array(
				'title'					=> esc_html__('Responsive Shared Page Settings', 'gravityforms-eway-pro'),
				'id'					=> 'gfewaypro-settings-shared',
				'fields'				=> array(

					array(
						'name'			=> 'sharedPageTheme',
						'label'			=> esc_html_x('Shared Page Theme', 'feed field name', 'gravityforms-eway-pro'),
						'type'			=> 'select',
						'choices'		=> array(
							array('value' => '',                  'label' => esc_html_x('Use default theme', 'shared page theme', 'gravityforms-eway-pro')),
							array('value' => 'Bootstrap',         'label' => 'Bootstrap'),
							array('value' => 'BootstrapAmelia',   'label' => 'Amelia'),
							array('value' => 'BootstrapCosmo',    'label' => 'Cosmo'),
							array('value' => 'BootstrapCyborg',   'label' => 'Cyborg'),
							array('value' => 'BootstrapFlatly',   'label' => 'Flatly'),
							array('value' => 'BootstrapJournal',  'label' => 'Journal'),
							array('value' => 'BootstrapReadable', 'label' => 'Readable'),
							array('value' => 'BootstrapSimplex',  'label' => 'Simplex'),
							array('value' => 'BootstrapSlate',    'label' => 'Slate'),
							array('value' => 'BootstrapSpacelab', 'label' => 'Spacelab'),
							array('value' => 'BootstrapUnited',   'label' => 'United'),
						),
						'default_value'	=> '',
					),

					array(
						'name'			=> 'cancelURL',
						'label'			=> esc_html_x('Cancel URL', 'feed field name', 'gravityforms-eway-pro'),
						'type'			=> 'text',
						'class'  		=> 'large',
						'placeholder'	=> esc_html_x('Leave empty to use default Gravity Forms confirmation handler', 'field placeholder', 'gravityforms-eway-pro'),
						'tooltip'		=> __('Redirect to this URL if the transaction is canceled.', 'gravityforms-eway-pro')
										.  '<br/><br/>'
										.  __('Please note: standard Gravity Forms submission logic applies if the transaction is successful.', 'gravityforms-eway-pro'),
					),

					array(
						'name'			=> 'allowEditCustomer',
						'label'			=> esc_html_x('Allow Customer Edit', 'feed field name', 'gravityforms-eway-pro'),
						'type'			=> 'checkbox',
						'choices'		=> array(
							array('name' => 'allowEditCustomer', 'label' => esc_html__('Allow customers to edit their details on the eWAY hosted page', 'gravityforms-eway-pro')),
						),
						'default_value'	=> 'no',
					),

					array(
						'name'			=> 'delayPost',
						'label'			=> esc_html_x('Create Post', 'feed field name', 'gravityforms-eway-pro'),
						'type'			=> 'checkbox',
						'choices'		=> array(
							array('name' => 'delayPost', 'label' => esc_html__('Create post only when transaction completes', 'gravityforms-eway-pro')),
						),
					),

					array(
						'name'			=> 'delayMailchimp',
						'label'			=> esc_html_x('MailChimp Subscription', 'feed field name', 'gravityforms-eway-pro'),
						'type'			=> 'checkbox',
						'choices'		=> array(
							array('name' => 'delayMailchimp', 'label' => esc_html__('Subscribe user to MailChimp only when transaction completes', 'gravityforms-eway-pro')),
						),
					),

					array(
						'name'			=> 'delayUserrego',
						'label'			=> esc_html_x('User Registration', 'feed field name', 'gravityforms-eway-pro'),
						'type'			=> 'checkbox',
						'choices'		=> array(
							array('name' => 'delayUserrego', 'label' => esc_html__('Register user only when transaction completes', 'gravityforms-eway-pro')),
						),
					),

					array(
						'name'			=> 'delayZapier',
						'label'			=> esc_html_x('Zapier', 'feed field name', 'gravityforms-eway-pro'),
						'type'			=> 'checkbox',
						'choices'		=> array(
							array('name' => 'delayZapier', 'label' => esc_html__('Send feed to Zapier only when transaction completes', 'gravityforms-eway-pro')),
						),
					),

					array(
						'name'			=> 'delaySalesforce',
						'label'			=> esc_html_x('Salesforce', 'feed field name', 'gravityforms-eway-pro'),
						'type'			=> 'checkbox',
						'choices'		=> array(
							array('name' => 'delaySalesforce', 'label' => esc_html__('Send feed to Salesforce only when transaction completes', 'gravityforms-eway-pro')),
						),
						'tooltip'		=> esc_html__('Supports the free Gravity Forms Salesforce add-on.', 'gravityforms-eway-pro'),
					),

					array(
						'name'			=> 'execDelayedAlways',
						'label'			=> esc_html_x('Always Execute', 'feed field name', 'gravityforms-eway-pro'),
						'type'			=> 'checkbox',
						'choices'		=> array(
							array('name' => 'execDelayedAlways', 'label' => esc_html__('Always execute delayed actions, regardless of payment status', 'gravityforms-eway-pro')),
						),
						'default_value'	=> '1',
						'tooltip'		=> __('The delayed actions above will only be processed for successful transactions, unless this option is enabled.', 'gravityforms-eway-pro'),
					),

					array(
						'name'			=> 'verifyCustomerPhone',
						'label'			=> esc_html_x('Verify Customer Phone', 'feed field name', 'gravityforms-eway-pro'),
						'type'			=> 'checkbox',
						'choices'		=> array(
							array('name' => 'verifyCustomerPhone', 'label' => esc_html__('Confirm the phone number using Beagle Verify', 'gravityforms-eway-pro')),
						),
					),

					array(
						'name'			=> 'verifyCustomerEmail',
						'label'			=> esc_html_x('Verify Customer Email', 'feed field name', 'gravityforms-eway-pro'),
						'type'			=> 'checkbox',
						'choices'		=> array(
							array('name' => 'verifyCustomerEmail', 'label' => esc_html__('Confirm the email address using Beagle Verify', 'gravityforms-eway-pro')),
						),
					),

				),
			),

			#endregion "shared page settings"

			#region "deprecated notification settings"

			array(
				'title'					=> esc_html__('Deprecated Notification Settings', 'gravityforms-eway-pro'),
				'id'					=> 'gfewaypro-settings-deprecated-notification',
				'description'			=> sprintf(__('Delayed notification settings will be removed in a future version of the add-on, but are preserved here for compatibility. Please update your notifications to use <a href="%s" target="_blank">form action events</a> instead, then disable these settings.', 'gravityforms-eway-pro'), esc_url('https://gfeway.webaware.net.au/faq/notifications-and-responsive-shared-page/')),
				'fields'				=> array(

					array(
						'name'			=> 'delayNotify',
						'label'			=> esc_html_x('Notifications', 'feed field name', 'gravityforms-eway-pro'),
						'type'			=> 'checkbox',
						'choices'		=> array(
							array('name' => 'delayNotify', 'label' => esc_html__('Send notifications only when transaction completes', 'gravityforms-eway-pro')),
						),
						'tooltip'		=> __('Delayed notifications will be processed regardless of transaction status. You can make notifications conditional on AuthCode "is not" an empty field for successful transactions, or "is" an empty field for failed or canceled transactions.', 'gravityforms-eway-pro'),
					),

					array(
						'name'			=> 'delayNotifications',
						'label'			=> '',
						'type'			=> 'gfeway_notifications',
						'class'			=> 'gfewaypro-feed-notifications',
					),

				),
			),

			#endregion "deprecated notification settings"

			#region "conditional processing settings"

			array(
				'title'					=> esc_html__('Feed Conditions', 'gravityforms-eway-pro'),
				'fields'				=> array(

					array(
						'name'			=> 'condition',
						'label'			=> esc_html_x('eWAY Condition', 'feed field name', 'gravityforms-eway-pro'),
						'type'			=> 'feed_condition',
						'checkbox_label' => 'Enable',
						'instructions'	=> esc_html_x('Send to eWAY if', 'eWAY condition instructions', 'gravityforms-eway-pro'),
						'tooltip'		=> esc_html__('When the eWAY condition is enabled, form submissions will only be sent to eWAY when the condition is met. When disabled, all form submissions will be sent to eWAY.', 'gravityforms-eway-pro'),
					),

				),
			),

			#endregion "conditional processing settings"

		);

		return $fields;
	}

	/**
	* build list of fields mapped to the payment gateway
	* @return array
	*/
	public function billing_info_fields() {
		$fields = array(
			array(
				'name'			=> 'description',
				'label'			=> esc_html_x('Description', 'mapped field name', 'gravityforms-eway-pro'),
				'required'		=> 0,
			),
			array(
				'name'			=> 'title',
				'label'			=> esc_html_x('Customer Title', 'mapped field name', 'gravityforms-eway-pro'),
				'required'		=> 0,
			),
			array(
				'name'			=> 'firstName',
				'label'			=> esc_html_x('Customer First Name', 'mapped field name', 'gravityforms-eway-pro'),
				'required'		=> 0,
			),
			array(
				'name'			=> 'lastName',
				'label'			=> esc_html_x('Customer Last Name', 'mapped field name', 'gravityforms-eway-pro'),
				'required'		=> 0,
			),
			array(
				'name'			=> 'companyName',
				'label'			=> esc_html_x('Company Name', 'mapped field name', 'gravityforms-eway-pro'),
				'required'		=> 0,
			),
			array(
				'name'			=> 'jobDesc',
				'label'			=> esc_html_x('Job Description', 'mapped field name', 'gravityforms-eway-pro'),
				'required'		=> 0,
			),
			array(
				'name'			=> 'email',
				'label'			=> esc_html_x('Email', 'mapped field name', 'gravityforms-eway-pro'),
				'required'		=> 0,
			),
			array(
				'name'			=> 'billAddress1',
				'label'			=> esc_html_x('Billing Address 1', 'mapped field name', 'gravityforms-eway-pro'),
				'required'		=> 0,
			),
			array(
				'name'			=> 'billAddress2',
				'label'			=> esc_html_x('Billing Address 2', 'mapped field name', 'gravityforms-eway-pro'),
				'required'		=> 0,
			),
			array(
				'name'			=> 'billCity',
				'label'			=> esc_html_x('Billing City', 'mapped field name', 'gravityforms-eway-pro'),
				'required'		=> 0,
			),
			array(
				'name'			=> 'billState',
				'label'			=> esc_html_x('Billing State', 'mapped field name', 'gravityforms-eway-pro'),
				'required'		=> 0,
			),
			array(
				'name'			=> 'billPostcode',
				'label'			=> esc_html_x('Billing Postcode', 'mapped field name', 'gravityforms-eway-pro'),
				'required'		=> 0,
			),
			array(
				'name'			=> 'billCountry',
				'label'			=> esc_html_x('Billing Country', 'mapped field name', 'gravityforms-eway-pro'),
				'required'		=> 0,
			),
			array(
				'name'			=> 'billPhone',
				'label'			=> esc_html_x('Phone', 'mapped field name', 'gravityforms-eway-pro'),
				'required'		=> 0,
			),
			array(
				'name'			=> 'billMobile',
				'label'			=> esc_html_x('Mobile', 'mapped field name', 'gravityforms-eway-pro'),
				'required'		=> 0,
			),
			array(
				'name'			=> 'fax',
				'label'			=> esc_html_x('Fax', 'mapped field name', 'gravityforms-eway-pro'),
				'required'		=> 0,
			),
			array(
				'name'			=> 'website',
				'label'			=> esc_html_x('Website', 'mapped field name', 'gravityforms-eway-pro'),
				'required'		=> 0,
			),
			array(
				'name'			=> 'comments',
				'label'			=> esc_html_x('Customer Comments', 'mapped field name', 'gravityforms-eway-pro'),
				'required'		=> 0,
			),
			array(
				'name'			=> 'shipFirstName',
				'label'			=> esc_html_x('Shipping First Name', 'mapped field name', 'gravityforms-eway-pro'),
				'required'		=> 0,
			),
			array(
				'name'			=> 'shipLastName',
				'label'			=> esc_html_x('Shipping Last Name', 'mapped field name', 'gravityforms-eway-pro'),
				'required'		=> 0,
			),
			array(
				'name'			=> 'shipEmail',
				'label'			=> esc_html_x('Shipping Email', 'mapped field name', 'gravityforms-eway-pro'),
				'required'		=> 0,
			),
			array(
				'name'			=> 'shipAddress1',
				'label'			=> esc_html_x('Shipping Address 1', 'mapped field name', 'gravityforms-eway-pro'),
				'required'		=> 0,
			),
			array(
				'name'			=> 'shipAddress2',
				'label'			=> esc_html_x('Shipping Address 2', 'mapped field name', 'gravityforms-eway-pro'),
				'required'		=> 0,
			),
			array(
				'name'			=> 'shipCity',
				'label'			=> esc_html_x('Shipping City', 'mapped field name', 'gravityforms-eway-pro'),
				'required'		=> 0,
			),
			array(
				'name'			=> 'shipState',
				'label'			=> esc_html_x('Shipping State', 'mapped field name', 'gravityforms-eway-pro'),
				'required'		=> 0,
			),
			array(
				'name'			=> 'shipPostcode',
				'label'			=> esc_html_x('Shipping Postcode', 'mapped field name', 'gravityforms-eway-pro'),
				'required'		=> 0,
			),
			array(
				'name'			=> 'shipCountry',
				'label'			=> esc_html_x('Shipping Country', 'mapped field name', 'gravityforms-eway-pro'),
				'required'		=> 0,
			),
			array(
				'name'			=> 'shipPhone',
				'label'			=> esc_html_x('Shipping Phone', 'mapped field name', 'gravityforms-eway-pro'),
				'required'		=> 0,
			),
			array(
				'name'			=> 'shipFax',
				'label'			=> esc_html_x('Shipping Fax', 'mapped field name', 'gravityforms-eway-pro'),
				'required'		=> 0,
			),
			array(
				'name'			=> 'option1',
				'label'			=> esc_html_x('Option 1', 'mapped field name', 'gravityforms-eway-pro'),
				'required'		=> 0,
			),
			array(
				'name'			=> 'option2',
				'label'			=> esc_html_x('Option 2', 'mapped field name', 'gravityforms-eway-pro'),
				'required'		=> 0,
			),
			array(
				'name'			=> 'option3',
				'label'			=> esc_html_x('Option 3', 'mapped field name', 'gravityforms-eway-pro'),
				'required'		=> 0,
			),
		);

		return $fields;
	}

	/**
	* build map of field types to fields, for default field mappings
	*/
	protected function setFeedDefaultFieldMap() {
		$this->feedDefaultFieldMap = array();

		$form_id = rgget( 'id' );
		$form = RGFormsModel::get_form_meta( $form_id );

		if (!isset($this->feedDefaultFieldMap['mappedFields_description'])) {
			$this->feedDefaultFieldMap['mappedFields_description']			= 'form_title';
		}

		if (is_array($form['fields'])) {
			foreach ($form['fields'] as $field) {
				switch ($field->type) {

					case 'name':
						if (!isset($this->feedDefaultFieldMap['mappedFields_title'])) {
							$this->feedDefaultFieldMap['mappedFields_title']			= $field->id . '.2';
							$this->feedDefaultFieldMap['mappedFields_firstName']		= $field->id . '.3';
							$this->feedDefaultFieldMap['mappedFields_lastName']			= $field->id . '.6';

							$this->feedDefaultFieldMap['mappedFields_shipFirstName']	= $field->id . '.3';
							$this->feedDefaultFieldMap['mappedFields_shipLastName']		= $field->id . '.6';
						}
						break;

					case 'address':
						if (!isset($this->feedDefaultFieldMap['mappedFields_billAddress1'])) {
							// assign first address field to billing address
							$this->feedDefaultFieldMap['mappedFields_billAddress1']		= $field->id . '.1';
							$this->feedDefaultFieldMap['mappedFields_billAddress2']		= $field->id . '.2';
							$this->feedDefaultFieldMap['mappedFields_billCity']			= $field->id . '.3';
							$this->feedDefaultFieldMap['mappedFields_billState']		= $field->id . '.4';
							$this->feedDefaultFieldMap['mappedFields_billPostcode']		= $field->id . '.5';
							$this->feedDefaultFieldMap['mappedFields_billCountry']		= $field->id . '.6';
						}
						elseif (!isset($this->feedDefaultFieldMap['mappedFields_shipAddress1'])) {
							// assign second address field to shipping address
							$this->feedDefaultFieldMap['mappedFields_shipAddress1']		= $field->id . '.1';
							$this->feedDefaultFieldMap['mappedFields_shipAddress2']		= $field->id . '.2';
							$this->feedDefaultFieldMap['mappedFields_shipCity']			= $field->id . '.3';
							$this->feedDefaultFieldMap['mappedFields_shipState']		= $field->id . '.4';
							$this->feedDefaultFieldMap['mappedFields_shipPostcode']		= $field->id . '.5';
							$this->feedDefaultFieldMap['mappedFields_shipCountry']		= $field->id . '.6';
						}
						break;

					case 'email':
						if (!isset($this->feedDefaultFieldMap['mappedFields_email'])) {
							$this->feedDefaultFieldMap['mappedFields_email']			= $field->id;
							$this->feedDefaultFieldMap['mappedFields_shipEmail']		= $field->id;
						}
						break;

					case 'website':
						if (!isset($this->feedDefaultFieldMap['mappedFields_website'])) {
							$this->feedDefaultFieldMap['mappedFields_website']			= $field->id;
						}
						break;

					case 'phone':
						if (!isset($this->feedDefaultFieldMap['mappedFields_billPhone'])) {
							// assign first phone field to billing and shipping phone number
							$this->feedDefaultFieldMap['mappedFields_billPhone']		= $field->id;
							$this->feedDefaultFieldMap['mappedFields_shipPhone']		= $field->id;
						}
						elseif (!isset($this->feedDefaultFieldMap['mappedFields_billMobile'])) {
							// assign second phone field to mobile number
							$this->feedDefaultFieldMap['mappedFields_billMobile']		= $field->id;
						}
						break;

				}
			}
		}
	}

	/**
	* override to set default mapped field selections from first occurring field of type
	* @param  array $field
	* @return string|null
	*/
	public function get_default_field_select_field( $field ) {
		if (!empty($this->feedDefaultFieldMap[$field['name']])) {
			return $this->feedDefaultFieldMap[$field['name']];
		}

		return parent::get_default_field_select_field($field);
	}

	/**
	* title of fields column for mapped fields
	* @return string
	*/
	public function field_map_title() {
		return esc_html_x('eWAY Field', 'mapped fields title', 'gravityforms-eway-pro');
	}

	/**
	* columns to display in list of feeds
	* @return array
	*/
	public function feed_list_columns() {
		$columns = array(
			'feedName'				=> esc_html_x('Feed name', 'feed field name', 'gravityforms-eway-pro'),
			'feedItem_useTest'		=> esc_html_x('Mode', 'payment transaction mode', 'gravityforms-eway-pro'),
			'feedItem_feedMethod'	=> esc_html_x('Integration Method', 'feed field name', 'gravityforms-eway-pro'),
		);

		return $columns;
	}

	/**
	* feed list value for payment mode
	* @param array $item
	* @return string
	*/
	protected function get_column_value_feedItem_useTest($item) {
		switch (rgars($item, 'meta/useTest')) {

			case '0':
				$value = esc_html_x('Live', 'payment transaction mode', 'gravityforms-eway-pro');
				break;

			case '1':
				$value = esc_html_x('Test', 'payment transaction mode', 'gravityforms-eway-pro');
				break;

			default:
				$value = '';
				break;

		}

		return $value;
	}

	/**
	* feed list value for payment integration method
	* @param array $item
	* @return string
	*/
	protected function get_column_value_feedItem_feedMethod($item) {
		switch (rgars($item, 'meta/feedMethod')) {

			case 'shared':
				$value = esc_html_x('Responsive Shared Page', 'integration method', 'gravityforms-eway-pro');
				break;

			case 'direct':
				$value = esc_html_x('Direct Connection', 'integration method', 'gravityforms-eway-pro');
				break;

			case 'recurxml':
				$value = esc_html_x('Recurring Payments', 'integration method', 'gravityforms-eway-pro');
				break;

			default:
				$value = '';
				break;

		}

		return $value;
	}

	/***
	* add custom bulk actions to feed list
	*/
	public function get_bulk_actions() {
		$actions = array(
			'gfeway_live'    => esc_html_x('Set Mode Live', 'feed list bulk action', 'gravityforms-eway-pro'),
			'gfeway_test'    => esc_html_x('Set Mode Test', 'feed list bulk action', 'gravityforms-eway-pro'),
		);

		$actions = array_merge(parent::get_bulk_actions(), $actions);

		return $actions;
	}

	/***
	* process custom bulk actions for feed list
	* @param string $action
	*/
	public function process_bulk_action($action) {
		switch ($action) {

			case 'gfeway_live':
			case 'gfeway_test':
				$feeds = rgpost('feed_ids');
				if (is_array($feeds)) {
					$mode = substr($action, 7);
					foreach ($feeds as $feed_id) {
						$this->setFeedMode($feed_id, $mode);
					}
				}
				break;

			default:
				parent::process_bulk_action($action);
				break;

		}
	}

	/**
	* change the live/test mode of a feed
	* @param int $feed_id
	* @param string $mode "live|test"
	*/
	protected function setFeedMode($feed_id, $mode) {
		$feed_id = absint($feed_id);
		$feed = $this->get_feed($feed_id);
		if ($feed) {
			$feed['meta']['useTest'] = ($mode !== 'live');
			$this->update_feed_meta($feed_id, $feed['meta']);
		}
	}

	/**
	* get list of fields for the recurring payment amount
	* @param bool $isInitialAmount
	* @return array
	*/
	protected function recurring_amount_choices($isInitialAmount = false) {
		$form = $this->get_current_form();

		if ($isInitialAmount) {
			$label = esc_html_x('No initial amount', 'mapped field choice', 'gravityforms-eway-pro');
		}
		else {
			$label = esc_html__('Select a Product Field', 'gravityforms-eway-pro');
		}

		$choices = array(
			array('value' => '', 'label' => $label),
		);

		$fields  = GFAPI::get_fields_by_type($form, array('product', 'number'));
		foreach ($fields as $field) {
			$choices[] = array('value' => $field->id, 'label' => GFFormsModel::get_label($field));
		}

		if (!$isInitialAmount) {
			$choices[] = array('value' => 'form_total', 'label' => esc_html_x('Form total', 'mapped field choice', 'gravityforms-eway-pro'));
		}

		return $choices;
	}

	/**
	* get settings for customer tokens / remembered cards
	* @param array $field
	* @param bool $echo
	*/
	public function settings_gfeway_remember_customer($field, $echo = true) {
		$remember_field = array(
			'name'			=> $field['name'],
			'type'			=> 'radio',
			'choices'		=> array(
				array('value' => 'off',			'label' => esc_html_x("Off; don't remember customer", 'remember customer', 'gravityforms-eway-pro')),
				array('value' => 'create',		'label' => esc_html_x("Create token customer, but don't remember in WordPress", 'remember customer', 'gravityforms-eway-pro')),
				array('value' => 'remember',	'label' => esc_html_x('Create token customer, and remember in WordPress', 'remember customer', 'gravityforms-eway-pro')),
				array('value' => 'ask',			'label' => esc_html_x('Create token customer only if customer permits, and remember in WordPress', 'remember customer', 'gravityforms-eway-pro')),
			),
			'default_value'	=> 'off',
		);

		$no_tx_field = array(
			'name'			=> $field['name'] . '_no_tx',
			'type'			=> 'checkbox',
			'choices'		=> array(
				array('value' => '1', 'name' => 'rememberCustomer_no_tx', 'label' => esc_html__('Create customer even if there is nothing to charge', 'gravityforms-eway-pro')),
			),
		);

		ob_start();

		$this->settings_radio($remember_field);
		$this->settings_checkbox($no_tx_field);

		$html = ob_get_clean();

		if ($echo) {
			echo $html;
		}

		return $html;
	}

	/**
	* get list of options for the number of recurrences
	* @param array $field
	* @param bool $echo
	* @return string
	*/
	public function settings_gfeway_recurring_times($field, $echo = true) {
		$form = $this->get_current_form();

		$times = array(
			array('value' =>  '0', 'label' => esc_html_x('Infinite', 'recurring times choice', 'gravityforms-eway-pro')),
			array('value' => '-1', 'label' => esc_html_x('Mapped field', 'field choice', 'gravityforms-eway-pro')),
		);

		for ($i = 1; $i <= 100; $i++) {
			$times[] = array('label' => $i, 'value' => $i);
		}

		$times_field = array(
			'name'			=> $field['name'] . '_times',
			'type'			=> 'select',
			'choices'		=> $times,
			'default_value'	=> 0,
		);

		$field_types = array('checkbox', 'hidden', 'list', 'number', 'option', 'quantity', 'radio', 'select');
		$fields = $this->get_field_map_choices($form['id'], $field_types);

		$mapped_field = array(
			'name'			=> $field['name'] . '_mapped',
			'type'			=> 'select',
			'choices'		=> $fields,
			'default_value'	=> 0,
		);

		ob_start();

		$this->settings_select($times_field);
		echo '&nbsp;';
		$this->settings_select($mapped_field);

		$html = ob_get_clean();

		if ($echo) {
			echo $html;
		}

		return $html;
	}

	/**
	* show a Billings Cycle field, similar to Gravity Forms' payment add-on field
	* @param array $field
	* @param bool $echo
	* @return string
	*/
	public function settings_gfeway_billing_cycle($field, $echo = true) {
		$form   = $this->get_current_form();

		$unit   = $this->get_setting($field['name'] . '_unit', 'month');
		$length = $this->get_setting($field['name'] . '_length', '1');

		// available cycles

		$cycles = array(
			// eWAY RebillInterval max is 31; @link https://eway.io/api-v3/#recurring-payments
			'day'   => array('label' => esc_html_x('Days',   'billing cycle label', 'gravityforms-eway-pro'), 'min' => 1, 'max' => 31),
			'week'  => array('label' => esc_html_x('Weeks',  'billing cycle label', 'gravityforms-eway-pro'), 'min' => 1, 'max' => 31),
			'month' => array('label' => esc_html_x('Months', 'billing cycle label', 'gravityforms-eway-pro'), 'min' => 1, 'max' => 24),
			'year'  => array('label' => esc_html_x('Years',  'billing cycle label', 'gravityforms-eway-pro'), 'min' => 1, 'max' =>  5),
		);

		$this->feedAdminArgs['billing'] = array (
			'cycles'	=> $cycles,
			'msg'		=> array (
								'mapped' => esc_html_x('Mapped field', 'field choice', 'gravityforms-eway-pro'),
							),
		);

		$unitChoices = array();
		foreach ($cycles as $value => $cycle) {
			$unitChoices[] = array('value' => $value, 'label' => $cycle['label']);
		}

		$unit_field = array(
			'name'			=> $field['name'] . '_unit',
			'type'			=> 'select',
			'choices'		=> $unitChoices,
			'default_value'	=> 'month',
		);

		// list of available lengths for unit

		if ($unit && isset($cycles[$unit])) {
			$selectedUnit = $cycles[$unit];
		}
		else {
			$selectedUnit = $cycles['month'];
		}

		$lengthChoices = array(
			array('value' => '-1', 'label' => esc_html_x('Mapped field', 'field choice', 'gravityforms-eway-pro'))
		);
		for ($i = $selectedUnit['min']; $i <= $selectedUnit['max']; $i++) {
			$lengthChoices[] = array('label' => $i, 'value' => $i);
		}

		$length_field = array(
			'name'			=> $field['name'] . '_length',
			'type'			=> 'select',
			'choices'		=> $lengthChoices,
			'default_value'	=> '1',
		);

		// mapped fields that can be used to specify lengths for unit

		$field_types = array('checkbox', 'hidden', 'list', 'number', 'option', 'quantity', 'radio', 'select');
		$fields = $this->get_field_map_choices($form['id'], $field_types);

		$mapped_field = array(
			'name'			=> $field['name'] . '_mapped',
			'type'			=> 'select',
			'choices'		=> $fields,
			'default_value'	=> '0',
		);

		if ($length != -1) {
			$mapped_field['style'] = 'display:none';
		}

		// build the HTML

		ob_start();

		$this->settings_select($length_field);
		echo '&nbsp;';
		$this->settings_select($unit_field);
		echo '&nbsp;';
		$this->settings_select($mapped_field);

		$html = ob_get_clean();

		if ($echo) {
			echo $html;
		}

		return $html;
	}

	/**
	* show fields for selecting when recurring payments will start
	* @param array $field
	* @param bool $echo
	* @return string
	*/
	public function settings_gfeway_recurring_start($field, $echo = true) {
		$form   = $this->get_current_form();

		$unit   = $this->get_setting($field['name'] . '_unit', 'now');
		$length = $this->get_setting($field['name'] . '_length');

		// available cycles

		$cycles = array(
			// eWAY RebillInterval max is 31; @link https://eway.io/api-v3/#recurring-payments
			'now'    => array('label' => esc_html_x('Immediately',  'recurring payments start', 'gravityforms-eway-pro')),
			'mapped' => array('label' => esc_html_x('Mapped field', 'field choice', 'gravityforms-eway-pro')),
			'day'    => array('label' => esc_html_x('Days',   'recurring payments start', 'gravityforms-eway-pro'), 'min' => 1, 'max' => 31),
			'week'   => array('label' => esc_html_x('Weeks',  'recurring payments start', 'gravityforms-eway-pro'), 'min' => 1, 'max' => 26),
			'month'  => array('label' => esc_html_x('Months', 'recurring payments start', 'gravityforms-eway-pro'), 'min' => 1, 'max' =>  6),
		);

		$this->feedAdminArgs['startbilling'] = array (
			'cycles'	=> $cycles,
			'msg'		=> array (
								'mapped' => esc_html_x('Mapped field', 'field choice', 'gravityforms-eway-pro'),
							),
		);

		$unitChoices = array();
		foreach ($cycles as $value => $cycle) {
			$unitChoices[] = array('value' => $value, 'label' => $cycle['label']);
		}

		$unit_field = array(
			'name'			=> $field['name'] . '_unit',
			'type'			=> 'select',
			'choices'		=> $unitChoices,
			'default_value'	=> 'now',
		);

		// list of available lengths for unit

		if (isset($cycles[$unit])) {
			$selectedUnit = $cycles[$unit];
		}
		else {
			$selectedUnit = $cycles['month'];
		}

		$lengthChoices = array(
			array('value' => '-1', 'label' => esc_html_x('Mapped field', 'field choice', 'gravityforms-eway-pro'))
		);
		if (isset($selectedUnit['min'])) {
			for ($i = $selectedUnit['min']; $i <= $selectedUnit['max']; $i++) {
				$lengthChoices[] = array('label' => $i, 'value' => $i);
			}
		}

		$length_field = array(
			'name'			=> $field['name'] . '_length',
			'type'			=> 'select',
			'choices'		=> $lengthChoices,
			'default_value'	=> 1,
		);

		if ($unit === 'now' || $unit === 'mapped') {
			$length_field['style'] = 'display:none';
		}

		// mapped fields that can be used to specify lengths for unit

		$field_types = array('checkbox', 'hidden', 'list', 'number', 'option', 'quantity', 'radio', 'select');
		$fields = $this->get_field_map_choices($form['id'], $field_types);

		$mapped_lengths = array(
			'name'			=> $field['name'] . '_mapped',
			'type'			=> 'select',
			'choices'		=> $fields,
			'default_value'	=> 0,
		);

		if ($unit === 'now' || $unit === 'mapped' || $length != -1) {
			$mapped_lengths['style'] = 'display:none';
		}

		// mapped fields that can be used to select a start date

		$fields = $this->get_field_map_choices($form['id'], array('date'));

		$mapped_date = array(
			'name'			=> $field['name'] . '_date',
			'type'			=> 'select',
			'choices'		=> $fields,
			'default_value'	=> 0,
		);

		if ($unit !== 'mapped') {
			$mapped_date['style'] = 'display:none';
		}

		// build the HTML

		ob_start();

		$this->settings_select($unit_field);
		echo '&nbsp;';
		$this->settings_select($length_field);
		echo '&nbsp;';
		$this->settings_select($mapped_lengths);
		echo '&nbsp;';
		$this->settings_select($mapped_date);

		$html = ob_get_clean();

		if ($echo) {
			echo $html;
		}

		return $html;
	}

	/**
	* get list of currencies for select lists
	* @param string $default_label
	* @return array
	*/
	protected function getCurrencies($default_label) {
		if (!class_exists('RGCurrency', false)) {
			require_once GFCommon::get_base_path() . '/currency.php';
		}
		$currencies = RGCurrency::get_currencies();

		$options = array();

		if ($default_label) {
			$options[] = array('value' => '', 'label' => $default_label);
		}

		// translators: 1: currency code; 2: currency name
		$optionFormat = __('%1$s &mdash; %2$s', 'currency list', 'gravityforms-eway-pro');

		foreach ($currencies as $ccode => $currency) {
			$options[] = array('value' => $ccode, 'label' => sprintf($optionFormat, esc_html($ccode), $currency['name']));
		}

		return $options;
	}

	/**
	* detect that active feed is modifying currency, set currency hooks
	* @param array $form
	* @return array
	*/
	public function detectFeedCurrency($form) {
		$this->currency = null;

		if (GFEwayProFormData::hasProductFields($form)) {
			$feeds = $this->get_active_feeds($form['id']);
			$defaultCurrency = GFCommon::get_currency();

			foreach ($feeds as $feed) {
				// must meet feed conditions, if any
				if (!$this->is_feed_condition_met($feed, $form, array())) {
					continue;
				}

				// pick up the currency of this feed, if different to global setting
				$feedCurrency = $this->getActiveCurrency($feed);
				if ($defaultCurrency !== $feedCurrency) {
					$this->currency = $feedCurrency;
					add_filter('gform_currency', array($this, 'gformCurrency'));
					break;
				}

			}
		}

		return $form;
	}

	/**
	* process a form validation filter hook; if can find a total, attempt to bill it if method is Direct Connection
	* @param array $data an array with elements is_valid (boolean) and form (array of form elements)
	* @return array
	*/
	public function gformValidation($data) {
		$form = $data['form'];

		// ignore if it's a Partial Save heartbeat, or not the last page
		if (rgpost('action') === 'heartbeat' || !GFFormDisplay::is_last_page($form)) {
			return $data;
		}

		// ignore if the form has failed a honeypot test
		if (GFFormDisplay::is_last_page($form) && rgar($form, 'enableHoneypot')) {
			$honeypot_id = GFFormDisplay::get_max_field_id($form) + 1;
			if (!rgempty("input_{$honeypot_id}")) {
				return $data;
			}
		}

		$this->currency = null;

		// make sure all other validations passed
		if ($data['is_valid']) {
			$feeds = $this->get_active_feeds($form['id']);
			$defaultCurrency = GFCommon::get_currency();

//~ error_log(__METHOD__ . ": feeds =\n" . print_r($feeds,1));

			// if we have feeds for this form, otherwise don't validate
			if (count($feeds) > 0) {
				$this->validationMessages = array();

				$directMethods = array('direct' => 1, 'recurxml' => 1);

				// load feeds and check for minimum data
				// will stop processing feeds on first Direct Connection feed matched
				foreach ($feeds as $feed) {
					// must meet feed conditions, if any
					if (!$this->is_feed_condition_met($feed, $form, array())) {
						continue;
					}

					// parse the form and store a mapping
					$formData = new GFEwayProFormData($form, $feed);
					$this->formData[$feed['id']] = $formData;

					// make sure there is an API key and password set for each feed, or globally
					$creds = $this->getEwayCredentials($feed);
					if (empty($creds['apiKey']) || empty($creds['password'])) {
						$data['is_valid'] = false;
						$this->validationMessages[] = esc_html__('No eWAY API key or password for payment; please tell the web master.', 'gravityforms-eway-pro');
						break;
					}

					// feed requires a transaction UNLESS it allows creating a token customer without a transaction
					$requireTransaction = self::testFeedRequiresTransaction($feed);

					// make sure that we have something to bill
					if ($requireTransaction && !$formData->hasPurchaseFields()) {
						$data['is_valid'] = false;
						$this->validationMessages[] = esc_html__('This form has no products or totals; unable to process transaction.', 'gravityforms-eway-pro');
						break;
					}

					// run away if nothing to charge, unless creating token customers without transactions
					if ($requireTransaction && empty($formData->total)) {
						continue;
					}

					// pick up the currency of this feed, if different to global setting and not already defined by a feed
					$feedCurrency = $this->getActiveCurrency($feed);
					if (is_null($this->currency) && $defaultCurrency !== $feedCurrency) {
						$this->currency = $feedCurrency;
						add_filter('gform_currency_pre_save_entry', array($this, 'gformCurrency'));
						add_action('gform_entry_created', array($this, 'gformCurrencyEndSave'));
					}

					// skip to next feed if not a direct payment method
					if (!isset($directMethods[$feed['meta']['feedMethod']])) {
						continue;
					}

					// direct methods must have a visible Credit Card field or customer token
					if ($formData->ccField === false && (empty($formData->customerToken) || $feed['meta']['feedMethod'] === 'recurxml')) {
						continue;
					}

					// run direct payment transactions now, or skip to next feed if not a direct payment method
					switch ($feed['meta']['feedMethod']) {

						case 'direct':
							list($is_valid, $messages) = $this->processPaymentDirect($formData, $feed, $form);
							break;

						case 'recurxml':
							list($is_valid, $messages) = $this->processPaymentRecurring($formData, $feed, $form);
							break;

					}

					if (!$is_valid) {
						$data['is_valid'] = false;

						if ($formData->ccField) {
							$formData->ccField['failed_validation']		= true;
							$formData->ccField['validation_message']	= nl2br($messages);
							$this->validationMessages = false;
							GFFormDisplay::set_current_page($form['id'], $formData->ccField->pageNumber);
						}
						else {
							foreach (explode("\n", $messages) as $message) {
								$this->validationMessages[] = $message;
							}
						}
					}

					// if we got here, then we've attempted a transaction so don't "validate" any more feeds
					break;
				}

				// make sure form hasn't already been submitted / processed
				if ($this->hasFormBeenProcessed($form)) {
					$data['is_valid'] = false;
					$this->validationMessages[] = esc_html__('Payment already submitted and processed - please close your browser window.', 'gravityforms-eway-pro');
				}

			}
		}

		return $data;
	}

	/**
	* alter the validation message
	* @param string $msg
	* @param array $form
	* @return string
	*/
	public function gformValidationMessage($msg, $form) {
		if (!empty($this->validationMessages)) {
			$msg = sprintf('<div class="validation_error">%s</div>', implode('<br />', $this->validationMessages));
		}

		return $msg;
	}

	/**
	* change the entry currency just before saving
	* @param string $currency
	* @return string
	*/
	public function gformCurrency($currency) {
		if (!is_null($this->currency)) {
			$currency = $this->currency;
		}

		return $currency;
	}

	/**
	* if we hooked in to modify the entry currency, unhook now
	*/
	public function gformCurrencyEndSave() {
		remove_filter('gform_currency_pre_save_entry', array($this, 'gformCurrency'));
	}

	/**
	* maybe process current feed, send user's browser to eWAY with required data
	* @param array $entry the form entry
	* @param array $form the form submission data
	* @return array
	*/
    public function maybeProcessFeed($entry, $form) {
		$feed = false;
		$feeds = $this->get_active_feeds($form['id']);
		foreach ($feeds as $f) {
			if ($this->is_feed_condition_met($f, $form, $entry)) {
				$feed = $f;
				break;
			}
		}

		// get form data for feed
		if (empty($feed) || empty($this->formData[$feed['id']])) {
			return $entry;
		}
		$formData = $this->formData[$feed['id']];

//~ error_log(__METHOD__ . ": feed =\n" . print_r($feed,1));
//~ error_log(__METHOD__ . ": formData =\n" . print_r($formData,1));

		// feed requires a transaction UNLESS it allows creating a token customer without a transaction
		$requireTransaction = self::testFeedRequiresTransaction($feed);

		// run away if nothing to charge
		if ($requireTransaction && empty($formData->total)) {
			return $entry;
		}

		switch ($feed['meta']['feedMethod']) {

			case 'direct':
				$entry = $this->recordPaymentDirect($feed, $entry);
				$this->sendNotificationsSingle($entry, $form);
				break;

			case 'recurxml':
				$entry = $this->recordPaymentDirect($feed, $entry);
				$this->sendNotificationsRecurring($entry, $form);
				break;

			case 'shared':
				$entry = $this->processPaymentShared($formData, $feed, $form, $entry);
				break;

			default:
				$this->error_msg = esc_html__('Invalid eWAY Pro payment method, unable to process transaction', 'gravityforms-eway-pro');
				add_filter('gform_confirmation', array($this, 'displayPaymentFailure'), 1000, 4);
				break;

		}

		return $entry;
	}

	/**
	* create and populate a Payment Request object
	* @param GFEwayProFormData $formData
	* @param array $feed
	* @param array $form
	* @param array|false $entry
	* @return GFEwayProPayment
	*/
	protected function getPaymentRequest($formData, $feed, $form, $entry = false) {
		// build a payment request and execute on API
		$requestor = $this->getPaymentRequestor($feed);

		// generate a unique transaction ID to avoid collisions, e.g. between different installations using the same eWAY account
		$transactionID = uniqid();

		// allow plugins/themes to modify transaction ID; NB: must remain unique for eWAY account!
		$transactionID = apply_filters('gfeway_invoice_trans_number', $transactionID, $form);

		// support pre-auth transactions (store but don't capture)
		$requestor->capture					= (rgar($feed['meta'], 'paymentMethod', 'capture') === 'capture');

		// maybe allow customer to edit their details on eWAY hosted page
		$requestor->customerReadOnly		= !rgar($feed['meta'], 'allowEditCustomer');

		$requestor->amount					= $formData->total;
		$requestor->currencyCode			= $this->getActiveCurrency($feed);
		$requestor->transactionNumber		= $transactionID;
		$requestor->invoiceDescription		= $formData->description;

		if ($entry) {
			$requestor->invoiceReference	= sprintf('F%d:E%d', $form['id'], $entry['id']);
		}
		else {
			$requestor->invoiceReference	= 'F' . $form['id'];
		}

		// customer token for token payments
		$requestor->customerToken			= $formData->customerToken;

		// credit card details
		$requestor->cardHoldersName			= $formData->ccName;
		$requestor->cardNumber				= $formData->ccNumber;
		$requestor->cardExpiryMonth			= $formData->ccExpMonth;
		$requestor->cardExpiryYear			= $formData->ccExpYear;
		$requestor->cardVerificationNumber	= $formData->ccCVN;

		// billing details
		$requestor->title					= $formData->title;
		$requestor->lastName				= $formData->lastName;
		$requestor->firstName				= $formData->firstName;
		$requestor->companyName				= $formData->companyName;
		$requestor->jobDesc					= $formData->jobDesc;
		$requestor->address1				= $formData->billAddress1;
		$requestor->address2				= $formData->billAddress2;
		$requestor->suburb					= $formData->billCity;
		$requestor->state					= $formData->billState;
		$requestor->postcode				= $formData->billPostcode;
		$requestor->countryName				= $formData->billCountry;
		$requestor->country					= GFCommon::get_country_code($formData->billCountry);
		$requestor->emailAddress			= $formData->email;
		$requestor->phone					= $formData->billPhone;
		$requestor->mobile					= $formData->billMobile;
		$requestor->fax						= $formData->fax;
		$requestor->website					= $formData->website;
		$requestor->comments				= $formData->comments;

		// delivery details
		switch (rgar($feed['meta'], 'shippingAddress', 'empty')) {

			// left empty
			case 'empty':
				// NOP
				break;

			// same a billing address
			case 'billing':
				$requestor->hasShipping				= true;
				$requestor->shipFirstName			= $requestor->firstName;
				$requestor->shipLastName			= $requestor->lastName;
				$requestor->shipAddress1			= $requestor->address1;
				$requestor->shipAddress2			= $requestor->address2;
				$requestor->shipSuburb				= $requestor->suburb;
				$requestor->shipState				= $requestor->state;
				$requestor->shipPostcode			= $requestor->postcode;
				$requestor->shipCountry				= $requestor->country;
				$requestor->shipPhone				= $requestor->phone;
				$requestor->shipEmailAddress		= $requestor->emailAddress;
				break;

			// from mapped shipping fields
			case 'mapped':
				$requestor->hasShipping				= true;
				$requestor->shipFirstName			= $formData->shipFirstName;
				$requestor->shipLastName			= $formData->shipLastName;
				$requestor->shipAddress1			= $formData->shipAddress1;
				$requestor->shipAddress2			= $formData->shipAddress2;
				$requestor->shipSuburb				= $formData->shipCity;
				$requestor->shipState				= $formData->shipState;
				$requestor->shipPostcode			= $formData->shipPostcode;
				$requestor->shipCountry				= GFCommon::get_country_code($formData->shipCountry);
				$requestor->shipEmailAddress		= $formData->shipEmail;
				$requestor->shipPhone				= $formData->shipPhone;
				$requestor->shipFax					= $formData->shipFax;
				break;

		}

		// pick up any options from mapped form fields
		$requestor->options							= array_filter(array(
														apply_filters('gfeway_invoice_option1', $formData->option1, $form),
														apply_filters('gfeway_invoice_option2', $formData->option2, $form),
														apply_filters('gfeway_invoice_option3', $formData->option3, $form),
													), 'strlen');

		// allow plugins/themes to modify invoice description and reference, and set option fields
		$requestor->invoiceDescription				= apply_filters('gfeway_invoice_desc', $requestor->invoiceDescription, $form);
		$requestor->invoiceReference				= apply_filters('gfeway_invoice_ref', $requestor->invoiceReference, $form);
		$requestor->options							= apply_filters('gfeway_invoice_options', $requestor->options, $form);
		$requestor->options							= array_filter($requestor->options, 'strlen');

//~ error_log(__METHOD__ . "\n" . print_r($requestor,1));

		return $requestor;
	}

    /**
    * get payment note based on payment method, with details, and eWAY response messages
    * @param bool $capture
    * @param array $results
    * @param array $ewayMessages
    * @return string
    */
    protected function getPaymentNote($capture, $results, $ewayMessages) {
		if ($capture) {
			$message = esc_html__('Payment has been captured successfully. Amount: %1$s. Transaction ID: %2$s.', 'gravityforms-eway-pro');
		}
		else {
			$message = esc_html__('PreAuth payment has been saved successfully. Amount: %1$s. Transaction ID: %2$s.', 'gravityforms-eway-pro');
		}

		$amount = GFCommon::to_money($results['payment_amount'], $results['currency']);

		$note = sprintf($message, $amount, $results['transaction_id']);
		if (!empty($ewayMessages)) {
			$note .= "\n" . esc_html(implode("\n", $ewayMessages));
		}

		return $note;
	}

    /**
    * get failure note based on payment method, with eWAY response messages
    * @param bool $capture
    * @param array $ewayMessages
    * @return string
    */
    protected function getFailureNote($capture, $ewayMessages) {
		if ($capture) {
			$note = esc_html__('Failed to capture payment.', 'gravityforms-eway-pro');
		}
		else {
			$note = esc_html__('PreAuth payment failed.', 'gravityforms-eway-pro');
		}

		if (!empty($ewayMessages)) {
			$note .= "\n" . esc_html(implode("\n", $ewayMessages));
		}

		return $note;
	}

	/**
	* get formatted error message for front end, with eWAY errors or response codes appended
	* @param string $error_msg
	* @param array $errors
	* @param array $messages
	* @return string
	*/
	protected function getErrorMessage($error_msg, $errors, $messages = false) {
		if (!empty($errors)) {
			// add detailed error messages
			$error_msg .= '<br/>' . nl2br(esc_html(implode("\n", $errors)));
		}
		elseif (!empty($messages)) {
			// just add response codes for messages
			$error_msg .= ' (' . esc_html(implode(',', array_keys($messages))) . ')';
		}

		return $error_msg;
	}

	/**
	* get errors and response messages as a string, for logging
	* @param array $errors
	* @param array $messages
	* @return string
	*/
	protected function getErrorsForLog($errors, $messages) {
		return implode('; ', array_merge((array) $errors, (array) $messages));
	}

	/**
	* process payment via Direct Connection
	* @param GFEwayProFormData $formData
	* @param array $feed
	* @param array $form
	* @return array
	*/
	protected function processPaymentDirect($formData, $feed, $form) {
		$return = array(false, '');

		$this->txResult = array('notes' => array());

		$this->log_debug('========= initiating transaction request');
		$this->log_debug(sprintf('%s: feed #%d - %s', __FUNCTION__, $feed['id'], $feed['meta']['feedName']));

		try {
			$requestor = $this->getPaymentRequest($formData, $feed, $form);

			$this->txResult['gfewaypro_txn_id']		= $requestor->transactionNumber;
			$this->txResult['gfewaypro_feed_id']	= $feed['id'];
			$this->txResult['payment_gateway']		= self::PAYMENT_GATEWAY;

			$this->ecryptManager->setCardNumber('');

			// maybe create a token customer first
			if (empty($requestor->customerToken)) {
				$requestor->customerToken = $this->maybeCreateCustomer($feed, $formData, $requestor);

				if (empty($formData->total)) {
					// nothing to bill, creating the token customer was the only task, and it was successful (i.e. no Exception)
					$return = array(true, '');

					$this->txResult['payment_status']	= 'Active';
					$this->txResult['payment_date']		= date('Y-m-d H:i:s');
					$this->txResult['eway_token']		= $requestor->customerToken;

					return $return;
				}
			}

			if (!empty($requestor->customerToken)) {
				$this->log_debug(sprintf('%s: using customer token %s', __FUNCTION__, $requestor->customerToken));
			}

			$response = $requestor->processPayment();

//~ error_log(__METHOD__ . "\n" . print_r($response,1));

			if ($response->TransactionStatus) {
				// transaction was successful, so record details and continue
				$this->txResult['payment_status']	= $requestor->capture ? 'Paid' : 'Pending';
				$this->txResult['payment_date']		= date('Y-m-d H:i:s');
				$this->txResult['payment_amount']	= $response->Payment->TotalAmount;
				$this->txResult['currency']			= $this->getActiveCurrency($feed);
				$this->txResult['transaction_id']	= $response->TransactionID;
				$this->txResult['gateway_txn_id']	= $response->TransactionID;
				$this->txResult['transaction_type']	= 1;
				$this->txResult['authcode']			= $response->AuthorisationCode;
				$this->txResult['beagle_score']		= $response->BeagleScore >= 0 ? $response->BeagleScore : '';
				$this->txResult['eway_token']		= $requestor->customerToken;

				// record obfuscated customer card number so that it can be injected back into credit card field
				if (!empty($response->Customer->CardDetails->Number)) {
					$this->ecryptManager->setCardNumber($response->Customer->CardDetails->Number);
				}

				$return = array(true, '');

				$note = $this->getPaymentNote($requestor->capture, $this->txResult, $response->ResponseMessage);
				$this->txResult['notes'][] = array('note' => $note, 'type' => 'success');

				$this->log_debug(sprintf('%s: success, date = %s, id = %s, status = %s, amount = %s, authcode = %s, Beagle = %s',
					__FUNCTION__, $this->txResult['payment_date'], $this->txResult['transaction_id'], $this->txResult['payment_status'],
					$this->txResult['payment_amount'], $this->txResult['authcode'], $this->txResult['beagle_score']));
				if (!empty($response->ResponseMessage)) {
					$this->log_debug(sprintf('%s: %s', __FUNCTION__, $this->getErrorsForLog(array(), $response->ResponseMessage)));
				}
			}
			else {
				$error_msg = esc_html__('Transaction failed', 'gravityforms-eway-pro');

				$this->txResult['payment_status']	= 'Failed';
				$this->txResult['payment_date']		= date('Y-m-d H:i:s');
				$this->txResult['currency']			= $this->getActiveCurrency($feed);
				$this->txResult['authcode']			= '';			// empty bank authcode, for conditional logic
				$this->txResult['beagle_code']		= 0;			// empty Beagle code, for conditional logic

				$error_msg = $this->getErrorMessage($error_msg, $response->Errors, $response->ResponseMessage);
				$return = array(false, $error_msg);

				$this->log_debug(sprintf('%s: failed; %s', __FUNCTION__, $this->getErrorsForLog($response->Errors, $response->ResponseMessage)));
				if ($response->BeagleScore > 0) {
					$this->log_debug(sprintf('%s: BeagleScore = %s', __FUNCTION__, $response->BeagleScore));
				}
			}
		}
		catch (GFEwayProException $e) {

//~ error_log(__METHOD__ . ": exception =\n" . $e->getMessage());
			$this->log_error(__FUNCTION__ . ': exception = ' . $e->getMessage());

			// record payment failure, and set error message
			$this->txResult['payment_status'] = 'Failed';
			$return = array(false, $e->getMessage());
		}

		return $return;
    }

    /**
    * maybe create a customer in MYeWAY and return token
    * @param array $feed
    * @param GFEwayProFormData $formData
    * @param GFEwayProPayment $requestor
    * @return string|false
    */
    protected function maybeCreateCustomer($feed, $formData, $requestor) {
		$customerToken = false;

		switch (rgar($feed['meta'], 'rememberCustomer', 'off')) {

			case 'create':
				$create   = true;
				$remember = false;
				break;

			case 'remember':
				$create   = true;
				$remember = true;
				break;

			case 'ask':
				$create   = $formData->rememberCard;
				$remember = $formData->rememberCard;
				break;

			default:
				$create   = false;
				$remember = false;
				break;

		}

		if ($create) {
			$this->log_debug(sprintf('%s: create a new customer in MYeWAY', __FUNCTION__));
			$response = $requestor->processCustomerCreate();

			if (empty($response->Customer->TokenCustomerID)) {
				$error_msg = esc_html__('Customer creation failed', 'gravityforms-eway-pro');
				$error_msg = $this->getErrorMessage($error_msg, $response->Errors, $response->ResponseMessage);

				$this->log_debug(sprintf('%s: customer create failed; %s', __FUNCTION__, $this->getErrorsForLog($response->Errors, $response->ResponseMessage)));

				throw new GFEwayProException($error_msg);
			}

			$customerToken     = $response->Customer->TokenCustomerID;
			$customerTokenCard = empty($response->Customer->CardDetails->Number) ? false : $response->Customer->CardDetails->Number;

			$this->log_debug(sprintf('%s: customer created with token %s for card "%s"', __FUNCTION__, $customerToken, $customerTokenCard));

			// record customer token details for adding to newly registered user
			$this->customerToken     = $customerToken;
			$this->customerTokenCard = $customerTokenCard;

			// record obfuscated customer card number so that it can be injected back into credit card field
			if ($customerTokenCard) {
				$this->ecryptManager->setCardNumber($customerTokenCard);
			}

			$note = sprintf(__('eWAY customer created with token: %s', 'gravityforms-eway-pro'), $customerToken);
			$this->txResult['notes'][] = array('note' => $note, 'type' => 'success');

			if ($remember && $customerToken && !$this->hasUserRegistration($feed['form_id'])) {
				$tokens = new GFEwayProCustomerTokens();
				$tokens->addToken($customerToken, $customerTokenCard);
			}
		}

		return $customerToken;
	}

	/**
	* determine whether form has active User Rego feed that meets its conditions
	* @param int $form_id
	* @return bool
	*/
	protected function hasUserRegistration($form_id) {
		if (!class_exists('GF_User_Registration', false)) {
			return false;
		}

		$userrego = GF_User_Registration::get_instance();
		return $userrego->has_feed($form_id, true);
	}

	/**
	* record payment via Direct Connection or Recurring Payments
	* @param array $feed
	* @param array $entry
	*/
	protected function recordPaymentDirect($feed, $entry) {
		if (!empty($this->txResult['payment_status'])) {

			foreach ($this->txResult as $key => $value) {
				switch ($key) {
					case 'payment_status':
					case 'payment_date':
					case 'payment_amount':
					case 'currency':
					case 'transaction_id':
					case 'transaction_type':
					case 'gateway_txn_id':				// custom entry meta must be saved with entry
					case 'payment_gateway':				// custom entry meta must be saved with entry
					case 'authcode':					// custom entry meta must be saved with entry
					case 'beagle_score':				// custom entry meta must be saved with entry
					case 'eway_token':					// custom entry meta must be saved with entry
						// update entry
						$entry[$key] = $value;
						break;

					case 'notes':
						foreach ($value as $note) {
							$this->add_note($entry['id'], $note['note'], $note['type']);
						}
						break;

					default:
						// update entry meta
						gform_update_meta($entry['id'], $key, $value);
						break;
				}
			}

			GFAPI::update_entry($entry);
		}

		return $entry;
    }

    /**
    * process payment via Recurring Payments XML
	* @param GFEwayProFormData $formData
	* @param array $feed
	* @param array $form
	* @return array
    */
    protected function processPaymentRecurring($formData, $feed, $form) {
		$return = array(false, '');

		$this->txResult = array('notes' => array());

		$this->log_debug('========= initiating transaction request');
		$this->log_debug(sprintf('%s: feed #%d - %s', __FUNCTION__, $feed['id'], $feed['meta']['feedName']));

		try {
			$requestor = $this->getPaymentRequest($formData, $feed, $form);

//~ error_log(__METHOD__ . ": paymentReq =\n" . print_r($requestor,1));
//~ error_log(__METHOD__ . ": formData =\n" . print_r($formData,1));

			$intervalUnit		=       $formData->billingCycle_unit;
			$intervalSize		= (int) $formData->billingCycle_length;
			$recurringTimes		= (int) $formData->recurringTimes;
			$startUnit			=       $formData->recurringStart_unit;
			$startLength		= (int) $formData->recurringStart_length;
			$startDate			=       $formData->recurringStart_date;

			// recurring payment details
			$requestor->amountInit		= GFCommon::to_number($formData->initialAmount);
			$requestor->amountRecur		= GFCommon::to_number($formData->recurringAmount);
			$requestor->intervalSize	= $intervalSize;
			$requestor->intervalType	= $this->getRecurringIntervalValue($intervalUnit);
			$requestor->dateInit		= date_create('now', new DateTimeZone('Australia/Sydney'));
			$requestor->dateStart		= $this->calcRecurringDateStart($startUnit, $startLength, $startDate);
			$requestor->dateEnd			= $this->calcRecurringDateEnd($requestor->dateStart, $intervalSize, $intervalUnit, $recurringTimes);

			$this->txResult['gfewaypro_txn_id']		= $requestor->transactionNumber;
			$this->txResult['gfewaypro_feed_id']	= $feed['id'];
			$this->txResult['payment_gateway']		= self::PAYMENT_GATEWAY;

			// eWAY recurring payments sandbox can't handle real customer ID, must use test customer ID
			if ($requestor->useSandbox) {
				$requestor->customerID = '87654321';
			}

			$response = $requestor->processRecurringPayment();

//~ error_log(__METHOD__ . ": response =\n" . print_r($response,1));

			if ($response->status) {
				// transaction was successful, so record details and continue
				$this->txResult['payment_status']	= 'Active';
				$this->txResult['payment_date']		= date('Y-m-d H:i:s');
				$this->txResult['transaction_type']	= 1;

				$return = array(true, '');

				$amountRecur = GFCommon::to_money($requestor->amountRecur, 'AUD');	// FIXME: if support other currencies, change here!
				$amountInit  = $requestor->amountInit ? GFCommon::to_money($requestor->amountInit, 'AUD') : false;
				$note = $this->getNoteRecurringSuccess($requestor, $intervalSize, $intervalUnit, $recurringTimes, $amountRecur, $amountInit);
				$this->txResult['notes'][] = array('note' => $note, 'type' => 'success');

				self::log_debug(sprintf('%s: success, date = %s, status = %s',
					__FUNCTION__, $this->txResult['payment_date'], $this->txResult['payment_status']));
			}
			else {
				$error_msg = esc_html__('Recurring payment failed', 'gravityforms-eway-pro');

				$this->txResult['payment_status']	= 'Failed';
				$this->txResult['payment_date']		= date('Y-m-d H:i:s');

				if (!empty($response->error)) {
					$error_msg .= '<br/>' . nl2br(esc_html($response->error));
				}
				$return = array(false, $error_msg);

				$this->log_debug(sprintf('%s: failed; %s', __FUNCTION__, $response->error));
			}
		}
		catch (GFEwayProException $e) {

//~ error_log(__METHOD__ . ": exception =\n" . $e->getMessage());
			$this->log_error(__FUNCTION__ . ': exception = ' . $e->getMessage());

			// record payment failure, and set error message
			$this->txResult['payment_status'] = 'Failed';
			$return = array(false, $e->getMessage());
		}

		return $return;
	}

	/**
	* get eWAY recurring payments interval value
	* @param string $intervalUnit
	* @return int
	* @throws GFEwayProException
	*/
	protected function getRecurringIntervalValue($intervalUnit) {
		// map intervals to eWAY interval values
		$intervals = array (
			'day'	=> 1,
			'week'	=> 2,
			'month'	=> 3,
			'year'	=> 4,
		);

		if (!isset($intervals[$intervalUnit])) {
			$this->log_debug(sprintf('%s: invalid recurring payments interval unit "%s"', __FUNCTION__, $intervalUnit));
			throw new GFEwayProException(__('Invalid recurring payments interval unit', 'gravityforms-eway-pro'));
		}

		return $intervals[$intervalUnit];
	}

	/**
	* calculate start date for recurring payment
	* @param string $startUnit now, mapped, day, week, month
	* @param int $startLength
	* @param string $startDate
	* @return DateTime
	* @throws GFEwayProException
	*/
	protected function calcRecurringDateStart($startUnit, $startLength, $startDate) {
		$dateStart = null;
		$timezone = new DateTimeZone('Australia/Sydney');

		if (!empty($startDate)) {
			$dateStart = date_create($startDate, $timezone);
			if ($dateStart === false) {
				throw new GFEwayProException(__('Invalid start date given for eWAY Recurring Payment', 'gravityforms-eway-pro'));
			}
		}
		else {
			$dateStart = date_create('now', $timezone);

			if ($startUnit !== 'now') {
				// need to add an interval; must be a valid one!
				if ($startLength < 1 || $startLength > 31) {
					throw new GFEwayProException(__('Invalid start date given for eWAY Recurring Payment', 'gravityforms-eway-pro'));
				}

				switch ($startUnit) {

					case 'day':
						$dateStart->add(new DateInterval("P{$startLength}D"));
						break;

					case 'week':
						$dateStart->add(new DateInterval("P{$startLength}W"));
						break;

					case 'month':
						$dateStart->add(new DateInterval("P{$startLength}M"));
						break;

				}
			}
		}

		return $dateStart;
	}

	/**
	* calculate end date for recurring payment, from start date and recurrance spec
	* @param DateTime $dateStart
	* @param int $length
	* @param string $intervalUnit day, week, month, year
	* @param int $recurrences
	* @return DateTime
	* @throws GFEwayProException
	*/
	protected function calcRecurringDateEnd($dateStart, $length, $intervalUnit, $recurrences) {
		if ($recurrences < 0) {
			throw new GFEwayProException(__('Invalid recurring payments recurrences, less than 0', 'gravityforms-eway-pro'));
		}

		if ($recurrences === 0) {
			$dateEnd = date_create('2099-12-31 12:00:00', new DateTimeZone('Australia/Sydney'));
		}
		else {
			$dateEnd = clone $dateStart;

			// ref eWAY case 00368117, "end date should be at least one day after the last payment"
			// date start is first "recurrence", so reduce recurrences by 1; add a day in the next step
			$interval = $length * ($recurrences - 1);

			switch ($intervalUnit) {

				case 'day':
					$interval++;
					$dateEnd->add(new DateInterval("P{$interval}D"));
					break;

				case 'week':
					// W is converted to D, so can't combine W and D in one interval spec; do the calcs then pass as D
					$interval = $interval * 7 + 1;
					$dateEnd->add(new DateInterval("P{$interval}D"));
					break;

				case 'month':
					$dateEnd->add(new DateInterval("P{$interval}M1D"));
					break;

				case 'year':
					$dateEnd->add(new DateInterval("P{$interval}Y1D"));
					break;

			}
		}

		return $dateEnd;
	}

	/**
	* get note for successful recurring payment
	* @param GFEwayProPayment $requestor
	* @param int $intervalSize
	* @param string $intervalUnit
	* @param int $recurringTimes
	* @param string $amount formatted amount
	* @param string $amountInit formatted amount for initial fee
	* @return string
	*/
	protected function getNoteRecurringSuccess($requestor, $intervalSize, $intervalUnit, $recurringTimes, $amount, $amountInit) {
		$intervalFormat	= $this->getIntervalUnitText($intervalUnit, $intervalSize);
		$interval		= sprintf($intervalFormat, number_format_i18n($intervalSize));

		$dateFormat		= get_option('date_format');
		$dateStart		= $requestor->dateStart->format($dateFormat);
		$dateEnd		= $requestor->dateEnd->format($dateFormat);

		if ($recurringTimes === 0) {
			// recurring indefinitely
			// translators: 1: interval; 2: date start
			return sprintf(esc_html__('Recurring payment created. Payment %1$s every %2$s, from %3$s', 'gravityforms-eway-pro'),
						$amount, $interval, $dateStart);
		}

		$period			= sprintf(_nx('%s period', '%s periods', $recurringTimes, 'recurring period', 'gravityforms-eway-pro'), number_format_i18n($recurringTimes));

		// translators: 1: interval; 2: period; 3: date start; 4: date end
		$note = sprintf(esc_html__('Recurring payment created. Payment %1$s every %2$s for %3$s, from %4$s until %5$s', 'gravityforms-eway-pro'),
					$amount, $interval, $period, $dateStart, $dateEnd);

		if ($amountInit) {
			// translators: %s is initial payment amount
			$note .= "\n" . sprintf(esc_html__('Initial payment %s', 'gravityforms-eway-pro'), $amountInit);
		}

		return $note;
	}

	/**
	* get interval unit text
	* @param string $intervalUnit
	* @param int $intervalSize
	* @return string
	*/
	protected function getIntervalUnitText($intervalUnit, $intervalSize) {
		// cover unhandled values of $intervalUnit
		$intervalFormat = '?';

		if ($intervalSize === 1) {
			switch ($intervalUnit) {

				case 'day':
					$intervalFormat = _x('day', 'recurring interval', 'gravityforms-eway-pro');
					break;

				case 'week':
					$intervalFormat = _x('week', 'recurring interval', 'gravityforms-eway-pro');
					break;

				case 'month':
					$intervalFormat = _x('month', 'recurring interval', 'gravityforms-eway-pro');
					break;

				case 'year':
					$intervalFormat = _x('year', 'recurring interval', 'gravityforms-eway-pro');
					break;

			}
		}
		else {
			switch ($intervalUnit) {

				case 'day':
					$intervalFormat = _nx('%s day', '%s days', $intervalSize, 'recurring interval', 'gravityforms-eway-pro');
					break;

				case 'week':
					$intervalFormat = _nx('%s week', '%s weeks', $intervalSize, 'recurring interval', 'gravityforms-eway-pro');
					break;

				case 'month':
					$intervalFormat = _nx('%s month', '%s months', $intervalSize, 'recurring interval', 'gravityforms-eway-pro');
					break;

				case 'year':
					$intervalFormat = _nx('%s year', '%s years', $intervalSize, 'recurring interval', 'gravityforms-eway-pro');
					break;

			}
		}

		return $intervalFormat;
	}

	/**
	* process payment via Direct Connection
	* @param GFEwayProFormData $formData
	* @param array $feed
	* @param array $form
	* @param array $entry
	*/
	protected function processPaymentShared($formData, $feed, $form, $entry) {
		$this->log_debug('========= initiating transaction request');
		$this->log_debug(sprintf('%s: feed #%d - %s', __FUNCTION__, $feed['id'], $feed['meta']['feedName']));

		try {
			$requestor = $this->getPaymentRequest($formData, $feed, $form, $entry);

			$returnURL						= $this->getReturnURL($entry);
			$requestor->redirectURL			= $returnURL;
			$requestor->cancelUrl			= $returnURL;
			$requestor->customView			= rgar($feed['meta'], 'sharedPageTheme');
			$requestor->languageCode		= get_locale();
			$requestor->verifyCustomerPhone	= rgar($feed['meta'], 'verifyCustomerPhone', null);
			$requestor->verifyCustomerEmail	= rgar($feed['meta'], 'verifyCustomerEmail', null);

			//~ $requestor->logoURL			= '';
			//~ $requestor->headerText		= '';

			// maybe create a token customer with transaction
			if (empty($requestor->customerToken) && (rgar($feed['meta'], 'rememberCustomer', 'off') !== 'off')) {
				$requestor->saveCustomer = true;
			}

			// record some payment meta
			gform_update_meta($entry['id'], 'gfewaypro_txn_id', $requestor->transactionNumber);
			gform_update_meta($entry['id'], 'gfewaypro_feed_id', $feed['id']);

			$response = $requestor->requestSharedPage();

//~ error_log(__METHOD__ . "\n" . print_r($response,1));

			if (empty($response->Errors)) {
				// set lead payment status to Processing
				$entry['payment_gateway']	= self::PAYMENT_GATEWAY;
				$entry['payment_status']	= 'Processing';

				GFAPI::update_entry($entry);

				// record URL for payment page, and set hook for redirecting to it
				$this->urlPaymentForm = $response->SharedPaymentUrl;
				add_filter('gform_confirmation', array($this, 'redirectToPaymentForm'), 1000, 4);
			}
			else {
				$capture = (rgar($feed['meta'], 'paymentMethod', 'capture') === 'capture');

				$entry['payment_gateway']	= self::PAYMENT_GATEWAY;
				$entry['payment_status']	= 'Failed';
				$entry['payment_date']		= date('Y-m-d H:i:s');
				$entry['currency']			= $this->getActiveCurrency($feed);
				$entry['authcode']			= '';			// empty bank authcode, for conditional logic
				$entry['beagle_code']		= 0;			// empty Beagle code, for conditional logic

				GFAPI::update_entry($entry);

				$this->log_debug(sprintf('%s: failed; %s', __FUNCTION__, $this->getErrorsForLog($response->Errors, array())));

				$error_msg = esc_html__('Transaction failed', 'gravityforms-eway-pro');
				$error_msg = $this->getErrorMessage($error_msg, $response->Errors);

				$note = $this->getFailureNote($capture, $response->Errors);
				$this->add_note($entry['id'], $note, 'error');

				// record payment failure, and set hook for displaying error message
				$this->error_msg = $error_msg;
				add_filter('gform_confirmation', array($this, 'displayPaymentFailure'), 1000, 4);
			}
		}
		catch (GFEwayProException $e) {

//~ error_log(__METHOD__ . ": exception =\n" . $e->getMessage());
			$this->log_error(__FUNCTION__ . ': exception = ' . $e->getMessage());

			// record payment failure, and set hook for displaying error message
			GFFormsModel::update_lead_property($entry['id'], 'payment_status', 'Failed');
			$this->error_msg = $e->getMessage();
			add_filter('gform_confirmation', array($this, 'displayPaymentFailure'), 1000, 4);
		}

		return $entry;
    }

	/**
	* redirect purchaser to the eWAY payment form, only called when a payment request has been successfully processed
	* @param mixed $confirmation text or redirect for form submission
	* @param array $form the form submission data
	* @param array $entry the form entry
	* @param bool $ajax form submission via AJAX
	* @return mixed
	*/
    public function redirectToPaymentForm($confirmation, $form, $entry, $ajax) {
		$confirmation = array('redirect' => $this->urlPaymentForm);

		// record entry's unique ID in database, to signify that it has been processed so don't attempt another payment!
		gform_update_meta($entry['id'], 'gfewaypro_unique_id', GFFormsModel::get_form_unique_id($form['id']));

		return $confirmation;
	}

	/**
	* display a payment request failure message
	* @param mixed $confirmation text or redirect for form submission
	* @param array $form the form submission data
	* @param array $entry the form entry
	* @param bool $ajax form submission via AJAX
	* @return mixed
	*/
    public function displayPaymentFailure($confirmation, $form, $entry, $ajax) {
		// record entry's unique ID in database, to signify that it has been processed so don't attempt another payment!
		gform_update_meta($entry['id'], 'gfewaypro_unique_id', GFFormsModel::get_form_unique_id($form['id']));

		// create a "confirmation message" in which to display the error
		$default_anchor = count(GFCommon::get_fields_by_type($form, array('page'))) > 0 ? 1 : 0;
		$default_anchor = apply_filters('gform_confirmation_anchor_'.$form['id'], apply_filters('gform_confirmation_anchor', $default_anchor));
		$anchor = $default_anchor ? "<a id='gf_{$form["id"]}' name='gf_{$form["id"]}' class='gform_anchor' ></a>" : '';
		$cssClass = rgar($form, 'cssClass');
		$error_msg = wpautop($this->error_msg);

		ob_start();
		include GFEWAYPRO_PLUGIN_ROOT . 'views/error-payment-failure.php';
		return ob_get_clean();
	}

    /**
    * generate an entry-based return URL for passing information back from eWAY
	* @param array $entry
	* @return string
	*/
	protected function getReturnURL($entry) {
		$param = base64_encode($entry['id'] . '|' . wp_hash(self::GFEWAY_HASH . $entry['id']));

		return add_query_arg(self::GFEWAY_RETURN, $param, home_url('/'));
	}

	/**
	* decode the parameter passed in the return URL above
	* @param string $param
	* @return int the entry ID
	* @throws GFEwayProException
	*/
	protected function decodeReturnUrlParam($param) {
		$decoded = base64_decode($param);
		if ($decoded) {
			$parts = explode('|', $decoded);
			if (count($parts) === 2 && $parts[1] === wp_hash(self::GFEWAY_HASH . $parts[0])) {
				return (int) $parts[0];
			}
		}

		throw new GFEwayProException();
	}

	/**
	* payment processed and recorded, show confirmation message / page
	* @param bool $do_parse
	* @return bool
	*/
	public function processReturn($do_parse) {
		if (empty($_GET[self::GFEWAY_RETURN])) {
			return $do_parse;
		}

		try {
			$lead_id = $this->decodeReturnUrlParam(wp_unslash($_GET[self::GFEWAY_RETURN]));
		}
		catch (GFEwayProException $e) {
			return $do_parse;
		}

		// retrieve the eWAY access code
		$accessCode = isset($_GET['AccessCode']) ? wp_unslash($_GET['AccessCode']) : false;
		if (empty($accessCode)) {
			return $do_parse;
		}

		$this->log_debug(__FUNCTION__);

		$entry = GFFormsModel::get_lead($lead_id);
		$form = GFFormsModel::get_form_meta($entry['form_id']);
		$feed = $this->getFeed($lead_id);

		$requestor = $this->getPaymentRequestor($feed);

		try {
			self::log_debug('========= requesting transaction result');
			$response = $requestor->queryTransaction($accessCode);

			if (count($response->Transactions) < 1) {
				throw new GFEwayProException(__('Transaction could not be retrieved from eWAY', 'gravityforms-eway-pro'));
			}
			$transaction = $response->Transactions[0];

			// if there was a payment transaction, verify that we have the correct transaction ID
			if (!empty($transaction->InvoiceReference)) {
				global $wpdb;
				$sql = "select lead_id from {$wpdb->prefix}rg_lead_meta where meta_key='gfewaypro_txn_id' and meta_value = %s";
				$lead_id = $wpdb->get_var($wpdb->prepare($sql, $transaction->InvoiceReference));
			}

			// must have a lead ID, or nothing to do
			if (empty($lead_id)) {
				throw new GFEwayProException(sprintf(__('Invalid entry ID: %s', 'gravityforms-eway-pro'), $lead_id));
			}

			// attempt to lock entry
			$lock_id = 'gfeway_elock_' . $lead_id;
			$entry_was_locked = get_transient($lock_id);
			if (!$entry_was_locked) {
				set_transient($lock_id, time(), 90);
			}
			else {
				self::log_debug("entry $lead_id was locked");
			}

			// capture current state of lead
			$initial_status = $entry['payment_status'];

			do_action('gfeway_process_return_parsed', $entry, $form, $feed);

			$capture = (rgar($feed['meta'], 'paymentMethod', 'capture') === 'capture');

			// record customer token if we have one
			if (!empty($transaction->TokenCustomerID)) {
				$entry['eway_token']		= $transaction->TokenCustomerID;

				// if was just created, note customer creation
				if (empty($transaction->Customer->TokenCustomerID)) {
					$entry['payment_status'] = 'Active';
					$entry['payment_date']	= date('Y-m-d H:i:s');
					$note = sprintf(__('eWAY customer created with token: %s', 'gravityforms-eway-pro'), $transaction->TokenCustomerID);
					$this->add_note($entry['id'], $note, 'success');
				}
			}

			// update lead entry, with success/fail details
			if ($transaction->TransactionStatus) {
				$entry['payment_status']	= $capture ? 'Paid' : 'Pending';
				$entry['payment_date']		= date('Y-m-d H:i:s');
				$entry['payment_amount']	= $transaction->TotalAmount;
				$entry['currency']			= $this->getActiveCurrency($feed);
				$entry['transaction_id']	= $transaction->TransactionID;
				$entry['gateway_txn_id']	= $transaction->TransactionID;
				$entry['transaction_type']	= 1;
				$entry['authcode']			= $transaction->AuthorisationCode;
				$entry['beagle_score']		= $transaction->BeagleScore >= 0 ? $transaction->BeagleScore : '';

				$note = $this->getPaymentNote($capture, $entry, $transaction->ResponseMessage);
				$this->add_note($entry['id'], $note, 'success');

				$this->log_debug(sprintf('%s: success, date = %s, id = %s, status = %s, amount = %s, authcode = %s, Beagle = %s',
					__FUNCTION__, $entry['payment_date'], $entry['transaction_id'], $entry['payment_status'],
					$entry['payment_amount'], $entry['authcode'], $entry['beagle_score']));
				if (!empty($transaction->ResponseMessage)) {
					$this->log_debug(sprintf('%s: %s', __FUNCTION__, $this->getErrorsForLog(array(), $transaction->ResponseMessage)));
				}
			}
			elseif (!empty($transaction->InvoiceReference)) {
				$entry['payment_status']	= 'Failed';
				$entry['payment_date']		= date('Y-m-d H:i:s');
				$entry['currency']			= $this->getActiveCurrency($feed);
				$entry['authcode']			= '';			// empty bank authcode, for conditional logic
				$entry['beagle_code']		= 0;			// empty Beagle code, for conditional logic

				$note = $this->getFailureNote($capture, array_merge($response->Errors, $transaction->ResponseMessage));
				$this->add_note($entry['id'], $note, 'error');

				$this->log_debug(sprintf('%s: failed; %s', __FUNCTION__, $this->getErrorsForLog($response->Errors, $transaction->ResponseMessage)));
			}

			if (!$entry_was_locked) {

				// update the entry
				self::log_debug(sprintf('updating entry %d', $lead_id));
				GFAPI::update_entry($entry);

				// if order hasn't been fulfilled, process any deferred actions
				if ($initial_status === 'Processing') {
					self::log_debug('processing deferred actions');

					$this->sendNotificationsSingle($entry, $form);

					$this->processDelayed($feed, $entry, $form);

					// allow hookers to trigger their own actions
					$hook_status = $transaction->TransactionStatus ? 'approved' : 'failed';
					do_action("gfeway_process_{$hook_status}", $entry, $form, $feed);
				}

			}

			// clear lock if we set one
			if (!$entry_was_locked) {
				delete_transient($lock_id);
			}

			// on failure, redirect to failure page if set, otherwise fall through to redirect back to confirmation page
			if ($entry['payment_status']	=== 'Failed') {
				if ($feed['meta']['cancelURL']) {
					wp_redirect(esc_url_raw($feed['meta']['cancelURL']));
					exit;
				}
			}

			// redirect to Gravity Forms page, passing form and lead IDs, encoded to deter simple attacks
			$query = array(
				'form_id'	=> $entry['form_id'],
				'lead_id'	=> $entry['id'],
			);
			$hash = wp_hash(http_build_query($query));
			$query['hash']	=  $hash;
			$query = base64_encode(http_build_query($query));
			$redirect_url = esc_url_raw(add_query_arg(array(self::GFEWAY_CONFIRM => $query), $entry['source_url']));
			wp_safe_redirect($redirect_url);
			exit;
		}
		catch (GFEwayProException $e) {
			// TODO: what now?
			echo nl2br(esc_html($e->getMessage()));
			self::log_error(__FUNCTION__ . ': ' . $e->getMessage());
			exit;
		}

		return $do_parse;
	}

	/**
	* payment processed and recorded, show confirmation message / page
	*/
	public function processFormConfirmation() {
		// check for redirect to Gravity Forms page with our encoded parameters
		if (isset($_GET[self::GFEWAY_CONFIRM])) {
			do_action('gfeway_process_confirmation');

			// decode the encoded form and lead parameters
			parse_str(base64_decode($_GET[self::GFEWAY_CONFIRM]), $query);

			$check = array(
				'form_id'	=> rgar($query, 'form_id'),
				'lead_id'	=> rgar($query, 'lead_id'),
			);

			// make sure we have a match
			if ($query && wp_hash(http_build_query($check)) === rgar($query, 'hash')) {

				// stop WordPress SEO from stripping off our query parameters and redirecting the page
				global $wpseo_front;
				if (isset($wpseo_front)) {
					remove_action('template_redirect', array($wpseo_front, 'clean_permalink'), 1);
				}

				// load form and lead data
				$form = GFFormsModel::get_form_meta($query['form_id']);
				$lead = GFFormsModel::get_lead($query['lead_id']);

				do_action('gfeway_process_confirmation_parsed', $lead, $form);

				// get confirmation page
				if (!class_exists('GFFormDisplay', false)) {
					require_once(GFCommon::get_base_path() . '/form_display.php');
				}
				$confirmation = GFFormDisplay::handle_confirmation($form, $lead, false);

				// preload the GF submission, ready for processing the confirmation message
				GFFormDisplay::$submission[$form['id']] = array(
					'is_confirmation'		=> true,
					'confirmation_message'	=> $confirmation,
					'form'					=> $form,
					'lead'					=> $lead,
				);

				// if it's a redirection (page or other URL) then do the redirect now
				if (is_array($confirmation) && isset($confirmation['redirect'])) {
					wp_safe_redirect($confirmation['redirect']);
					exit;
				}
			}
		}
	}

	/**
	* supported notification events
	* @param array $form
	* @return array
	*/
	public function supported_notification_events($form) {
		if (!$this->has_feed($form['id'])) {
			return false;
		}

		return array(
			'complete_payment'			=> esc_html_x('Payment Completed', 'notification event', 'gravityforms-eway-pro'),
			'fail_payment'				=> esc_html_x('Payment Failed', 'notification event', 'gravityforms-eway-pro'),
			'add_pending_payment'		=> esc_html_x('Payment Pending', 'notification event', 'gravityforms-eway-pro'),
			'create_subscription'		=> esc_html_x('Subscription Created', 'notification event', 'gravityforms-eway-pro'),
			//~ 'fail_subscription_create'	=> esc_html_x('Subscription Creation Failed', 'notification event', 'gravityforms-eway-pro'),
		);
	}

	/**
	* send notifications for single payment (Direct Connection, Responsive Shared Page)
	* @param array $entry
	* @param array $form
	*/
	protected function sendNotificationsSingle($entry, $form) {
		switch ($entry['payment_status']) {

			case 'Paid':
				GFAPI::send_notifications($form, $entry, 'complete_payment');
				break;

			case 'Pending':
				GFAPI::send_notifications($form, $entry, 'add_pending_payment');
				break;

			case 'Active':
				// no transaction, but created a new token customer
				GFAPI::send_notifications($form, $entry, 'create_subscription');
				break;

			default:
				GFAPI::send_notifications($form, $entry, 'fail_payment');
				break;

		}
	}

	/**
	* send notifications for recurring payments
	* @param array $entry
	* @param array $form
	*/
	protected function sendNotificationsRecurring($entry, $form) {
		switch ($entry['payment_status']) {

			case 'Active':
				GFAPI::send_notifications($form, $entry, 'create_subscription');
				break;

			default:
				//~ GFAPI::send_notifications($form, $entry, 'fail_subscription_payment');
				break;

		}
	}

	/**
	* disable "feeds" that don't subclass the feed add-on, like Zapier
	* @param array $entry
	* @param array $form
	*/
	public function gformDelayOther($entry, $form) {
		$feed = $this->getFeed($entry['id']);

		if (!empty($feed['meta']['delayZapier']) && has_action('gform_after_submission', array('GFZapier', 'send_form_data_to_zapier'))) {
			$this->log_debug(sprintf('delay Zapier feed: form id %s, lead id %s', $form['id'], $entry['id']));
			remove_action('gform_after_submission', array('GFZapier', 'send_form_data_to_zapier'), 10, 2);
		}

		if (!empty($feed['meta']['delaySalesforce']) && has_action('gform_after_submission', array('GFSalesforce', 'export'))) {
			$this->log_debug(sprintf('delay Salesforce feed: form id %s, lead id %s', $form['id'], $entry['id']));
			remove_action('gform_after_submission', array('GFSalesforce', 'export'), 10, 2);
		}
	}

	/**
	* filter whether form delays some actions (e.g. MailChimp)
	* @param bool $is_delayed
	* @param array $form
	* @param array $entry
	* @param string $addon_slug
	* @return bool
	*/
	public function gformIsDelayed($is_delayed, $form, $entry, $addon_slug) {
		if ($entry['payment_status'] === 'Processing') {
			$feed = $this->getFeed($entry['id']);

			if ($feed) {

				switch ($addon_slug) {

					case 'gravityformsmailchimp':
						if (!empty($feed['meta']['delayMailchimp'])) {
							$is_delayed = true;
							$this->log_debug(sprintf('delay MailChimp registration: form id %s, lead id %s', $form['id'], $entry['id']));
						}
						break;

					case 'gravityformsuserregistration':
						if (!empty($feed['meta']['delayUserrego'])) {
							$is_delayed = true;
							$this->log_debug(sprintf('delay user registration: form id %s, lead id %s', $form['id'], $entry['id']));
						}
						break;

				}

			}

		}

		return $is_delayed;
	}

	/**
	* filter whether post creation from form is enabled (yet)
	* @param bool $is_delayed
	* @param array $form
	* @param array $entry
	* @return bool
	*/
	public function gformDelayPost($is_delayed, $form, $entry) {
		$feed = $this->getFeed($entry['id']);

		if ($entry['payment_status'] === 'Processing' && !empty($feed['meta']['delayPost'])) {
			$is_delayed = true;
			$this->log_debug(sprintf('delay post creation: form id %s, lead id %s', $form['id'], $entry['id']));
		}

		return $is_delayed;
	}

	/**
	* filter whether form triggers admin notification (yet)
	* @param bool $is_delayed
	* @param array $notification
	* @param array $form
	* @param array $entry
	* @return bool
	*/
	public function gformDelayNotification($is_delayed, $notification, $form, $entry) {
		// only for default form submission event
		if (rgar($notification, 'event') !== 'form_submission') {
			return $is_delayed;
		}

		$feed = $this->getFeed($entry['id']);

		if ($entry['payment_status'] === 'Processing' && !empty($feed['meta']['delayNotify']) && !empty($feed['meta']['delayNotifications'][$notification['id']])) {
			$is_delayed = true;
			$this->log_debug(sprintf('delay notification: form id %s, lead id %s, notification "%s"', $form['id'], $entry['id'], $notification['name']));
		}

		return $is_delayed;
	}

	/**
	* get feed for lead/entry
	* @param int $lead_id the submitted entry's ID
	* @return array
	*/
	protected function getFeed($lead_id) {
		if ($this->feed !== false && (empty($this->feed['lead_id']) || $this->feed['lead_id'] != $lead_id)) {
			$this->feed = $this->get_feed(gform_get_meta($lead_id, 'gfewaypro_feed_id'));
			if ($this->feed) {
				$this->feed['lead_id'] = $lead_id;
			}
		}

		return $this->feed;
	}

	/**
	* process any delayed actions
	* @param array $feed
	* @param array $entry
	* @param array $form
	*/
	protected function processDelayed($feed, $entry, $form) {
		// go no further if we've already done this
		if ($entry['is_fulfilled']) {
			return;
		}

		// default to only performing delayed actions if payment was successful, unless feed opts to always execute
		// can filter each delayed action to permit / deny execution
		$execute_delayed = in_array($entry['payment_status'], array('Paid', 'Pending', 'Active')) || $feed['meta']['execDelayedAlways'];

		if ($feed['meta']['delayPost']) {
			if (apply_filters('gfeway_delayed_post_create', $execute_delayed, $entry, $form, $feed)) {
				$this->log_debug(sprintf('executing delayed post creation; form id %s, lead id %s', $form['id'], $entry['id']));
				GFFormsModel::create_post($form, $entry);
			}
		}

		if ($feed['meta']['delayNotify'] && count($feed['meta']['delayNotifications']) > 0) {
			$this->sendDeferredNotifications($feed, $form, $entry);
		}

		// record that basic delayed actions have been fulfilled, before attempting things that might fail
		GFFormsModel::update_lead_property($entry['id'], 'is_fulfilled', true);
		$entry['is_fulfilled'] = true;

		if ($execute_delayed) {
			if (!empty($feed['meta']['delaySalesforce'])) {
				add_action('gform_paypal_fulfillment', array($this, 'maybeExecuteSalesforce'), 10, 4);
			}

			$this->log_debug(sprintf('calling gform_paypal_fulfillment action; form id %s, lead id %s', $form['id'], $entry['id']));
			do_action('gform_paypal_fulfillment', $entry, $feed, $entry['transaction_id'], $entry['payment_amount']);
		}
	}

	/**
	* maybe execute delayed Salesforce feed, if there is one
	* @param array $entry
	* @param array $form
	* @param string $transaction_id
	* @param float $payment_amount
	*/
	public function maybeExecuteSalesforce($entry, $feed, $transaction_id, $payment_amount) {
		if (class_exists('GFSalesforce', false) && method_exists('GFSalesforce', 'export')) {
			$form = GFFormsModel::get_form_meta($entry['form_id']);
			$this->log_debug(sprintf('executing delayed Salesforce feed: form id %s, lead id %s', $form['id'], $entry['id']));
			GFSalesforce::export($entry, $form);
		}
	}

	/**
	* when User Registration add-on creates a user, check for new customer token on entry and attach to user
	* @param int $user_id
	* @param array $userrego_feed
	* @param array $entry
	*/
	public function userAttachCustomerToken($user_id, $userrego_feed, $entry) {
		$feed = $this->getFeed($entry['id']);

		$customerToken     = $this->customerToken ? $this->customerToken : gform_get_meta($entry['id'], 'eway_token');
		$customerTokenCard = $this->customerTokenCard;

		switch (rgar($feed['meta'], 'rememberCustomer', 'off')) {

			case 'remember':
				$remember = true;
				break;

			case 'ask':
				$remember = !empty($customerToken);
				break;

			default:
				$remember = false;
				break;

		}

		if ($remember && $customerToken) {
			$tokens = new GFEwayProCustomerTokens();

			// if we don't have the card number, get it from eWAY; can happen e.g. for user created after activation process
			if (empty($customerTokenCard)) {
				$requestor         = $this->getPaymentRequestor($feed);
				$response          = $requestor->queryCustomer($customerToken);
				$customerTokenCard = $response->getCardNumber();
			}

			$tokens->addToken($customerToken, $customerTokenCard, $user_id);
		}
	}

	/**
	* send deferred notifications
	* @param array $feed
	* @param array $form the form submission data
	* @param array $lead the form entry
	*/
	protected function sendDeferredNotifications($feed, $form, $lead) {
		$notifications = GFCommon::get_notifications_to_send('form_submission', $form, $lead);
		foreach ($notifications as $notification) {
			if (!empty($feed['meta']['delayNotifications'][$notification['id']])) {
				if (apply_filters('gfeway_delayed_notification_send', true, $notification, $lead, $form, $feed)) {
					$this->log_debug(sprintf('sending delayed notification; form id %s, lead id %s, notification "%s"',
						$form['id'], $lead['id'], $notification['name']));
					GFCommon::send_notification($notification, $form, $lead);
				}
			}
		}
	}

	/**
	* show a Delay Notifications field
	* @param array $field
	* @param bool $echo
	* @return string
	*/
	public function settings_gfeway_notifications($field, $echo = true) {
		$form = $this->get_current_form();

        $notifications = GFCommon::get_notifications('form_submission', $form);
        $selections = $this->get_setting($field['name']);

		ob_start();

		// hidden field to hold the recorded value
		$this->settings_hidden(array('name'	=> $field['name'], 'id'	=> $field['name']));

		// list of checkboxes, recorded in hidden field as JSON via JavaScript event monitoring
		require GFEWAYPRO_PLUGIN_ROOT . 'views/admin-feed-settings-notifications.php';

		$html = ob_get_clean();

		if ($echo) {
			echo $html;
		}

		return $html;
	}

	/**
	* add custom merge tags
	* @param array $merge_tags
	* @param int $form_id
	* @param array $fields
	* @param int $element_id
	* @return array
	*/
	public function gformCustomMergeTags($merge_tags, $form_id, $fields, $element_id) {
		if ($form_id) {
			$feeds = $this->get_feeds($form_id);
			if (!empty($feeds)) {
				// at least one feed for this add-on, so add our merge tags
				$merge_tags[] = array('label' => esc_html_x('Transaction ID', 'merge tag label', 'gravityforms-eway-pro'), 'tag' => '{transaction_id}');
				$merge_tags[] = array('label' => esc_html_x('AuthCode',       'merge tag label', 'gravityforms-eway-pro'), 'tag' => '{authcode}');
				$merge_tags[] = array('label' => esc_html_x('Beagle Score',   'merge tag label', 'gravityforms-eway-pro'), 'tag' => '{beagle_score}');
				$merge_tags[] = array('label' => esc_html_x('Payment Amount', 'merge tag label', 'gravityforms-eway-pro'), 'tag' => '{payment_amount}');
				$merge_tags[] = array('label' => esc_html_x('Payment Status', 'merge tag label', 'gravityforms-eway-pro'), 'tag' => '{payment_status}');
				$merge_tags[] = array('label' => esc_html_x('Customer Token', 'merge tag label', 'gravityforms-eway-pro'), 'tag' => '{eway_token}');
			}
		}

		return $merge_tags;
	}

	/**
	* replace custom merge tags
	* @param string $text
	* @param array $form
	* @param array $entry
	* @param bool $url_encode
	* @param bool $esc_html
	* @param bool $nl2br
	* @param string $format
	* @return string
	*/
	public function gformReplaceMergeTags($text, $form, $entry, $url_encode, $esc_html, $nl2br, $format) {
		// check for invalid calls, e.g. Gravity Forms User Registration login form widget
		if (empty($form) || empty($entry)) {
			return $text;
		}

		$entry_id = rgar($entry, 'id');

		if ($entry_id) {
			$gateway = gform_get_meta($entry_id, 'payment_gateway');
			if ($gateway === self::PAYMENT_GATEWAY) {
				$authCode  = gform_get_meta($entry_id, 'authcode');
				$ewayToken = gform_get_meta($entry_id, 'eway_token');

				// format payment amount as currency
				if (isset($entry['payment_amount'])) {
					$payment_amount = GFCommon::format_number($entry['payment_amount'], 'currency', rgar($entry, 'currency', ''));
				}
				else {
					$payment_amount = '';
				}

				$tags = array (
					'{transaction_id}',
					'{payment_status}',
					'{payment_amount}',
					'{authcode}',
					'{eway_token}',
				);
				$values = array (
					rgar($entry, 'transaction_id', ''),
					rgar($entry, 'payment_status', ''),
					$payment_amount,
					!empty($authCode)  ? $authCode  : '',
					!empty($ewayToken) ? $ewayToken : '',
				);

				$text = str_replace($tags, $values, $text);
			}
		}

		return $text;
	}

	/**
	* action hook for building the entry details view
	* @param int $form_id
	* @param array $lead
	*/
	public function gformPaymentDetails($form_id, $lead) {
		$payment_gateway = gform_get_meta($lead['id'], 'payment_gateway');
		if ($payment_gateway === self::PAYMENT_GATEWAY) {
			$authcode		= gform_get_meta($lead['id'], 'authcode');
			$beagle_score	= gform_get_meta($lead['id'], 'beagle_score');
			$eway_token		= gform_get_meta($lead['id'], 'eway_token');

			require GFEWAYPRO_PLUGIN_ROOT . 'views/admin-entry-payment-details.php';
		}
	}

	/**
	* test whether we can edit payment details
	* @param array $entry
	* @param string $action
	* @return bool
	*/
	protected function canEditPaymentDetails($entry, $action) {
		// make sure payment is not Approved already (can't go backwards!)
		// no Paid (or Approved), and no Active recurring payments
		$payment_status = rgar($entry, 'payment_status');
		if ($payment_status === 'Approved' || $payment_status === 'Paid' || $payment_status === 'Active') {
			return false;
		}

		// check that we're editing the lead
		if (strcasecmp(rgpost('save'), $action) !== 0) {
			return false;
		}

		// make sure payment is one of ours
		if (gform_get_meta($entry['id'], 'payment_gateway') !== self::PAYMENT_GATEWAY) {
			return false;
		}

		return true;
	}

	/**
	* allow edits to payment status
	* @param string $content
	* @param array $form
	* @param array $entry
	* @return string
	*/
    public function gformPaymentStatus($content, $form, $entry) {
		// make sure that we're editing the entry and are allowed to change it
		if ($this->canEditPaymentDetails($entry, 'edit')) {
			// create drop down for payment status
			ob_start();
			include GFEWAYPRO_PLUGIN_ROOT . 'views/admin-entry-payment-status.php';
			$content = ob_get_clean();
		}

		return $content;
    }

	/**
	* update payment status if it has changed
	* @param array $form
	* @param int $entry_id
	*/
	public function gformAfterUpdateEntry($form, $entry_id) {
		// make sure we have permission
		check_admin_referer('gforms_save_entry', 'gforms_save_entry');

		$entry = GFFormsModel::get_lead($entry_id);

		// make sure that we're editing the entry and are allowed to change it
		if (!$this->canEditPaymentDetails($entry, 'update')) {
			return;
		}

		// make sure we have new values
		$payment_status = rgpost('payment_status');

		if (empty($payment_status)) {
			return;
		}

		$note = __('Payment information was manually updated.', 'gravityforms-eway-pro');

		if ($entry['payment_status'] !== $payment_status) {
			// translators: 1: old payment status; 2: new payment status
			$note .= "\n" . sprintf(__('Payment status changed from %1$s to %2$s.', 'gravityforms-eway-pro'), $entry['payment_status'], $payment_status);
			$entry['payment_status'] = $payment_status;
		}


		GFAPI::update_entry($entry);

		$user = wp_get_current_user();
		GFFormsModel::add_note($entry['id'], $user->ID, $user->display_name, esc_html($note));
	}

	/**
	* check whether this form entry's unique ID has already been used; if so, we've already done/doing a payment attempt.
	* @param array $form
	* @return boolean
	*/
	protected function hasFormBeenProcessed($form) {
		global $wpdb;

		$unique_id = GFFormsModel::get_form_unique_id($form['id']);

		$sql = "select lead_id from {$wpdb->prefix}rg_lead_meta where meta_key='gfewaypro_unique_id' and meta_value = %s";
		$lead_id = $wpdb->get_var($wpdb->prepare($sql, $unique_id));

		return !empty($lead_id);
	}

	/**
	* get eWAY credentials for selected feed
	* @param array $feed
	* @return string
	*/
	public function getEwayCredentials($feed) {
		// get defaults from add-on settings
		$creds = array(
			'apiKey'		=> $this->get_plugin_setting('apiKey'),
			'password'		=> $this->get_plugin_setting('password'),
			'ecryptKey'		=> $this->get_plugin_setting('ecryptKey'),
			'customerID'	=> $this->get_plugin_setting('customerID'),
		);

		// override with Sandbox settings if set for Sandbox
		if (!empty($feed['meta']['useTest'])) {
			$credsSandbox = array_filter(array(
				'apiKey'		=> $this->get_plugin_setting('sandboxApiKey'),
				'password'		=> $this->get_plugin_setting('sandboxPassword'),
				'ecryptKey'		=> $this->get_plugin_setting('sandboxEcryptKey'),
			));
			$creds = array_merge($creds, $credsSandbox);
		}

		// maybe override from feed settings
		if (!empty($feed['meta']['custom_connection'])) {
			if (!empty($feed['meta']['apiKey'])) {
				$creds['apiKey'] = $feed['meta']['apiKey'];
			}

			if (!empty($feed['meta']['password'])) {
				$creds['password'] = $feed['meta']['password'];
			}

			if (!empty($feed['meta']['ecryptKey'])) {
				$creds['ecryptKey'] = $feed['meta']['ecryptKey'];
			}

			if (!empty($feed['meta']['customerID'])) {
				$creds['customerID'] = $feed['meta']['customerID'];
			}
		}

		return $creds;
	}

	/**
	* get a payment API request object
	* @param array $feed
	* @return GFEwayProPayment
	*/
	protected function getPaymentRequestor($feed) {
		$creds   = $this->getEwayCredentials($feed);
		$useTest = (bool) $feed['meta']['useTest'];

		return new GFEwayProPayment($creds['apiKey'], $creds['password'], $creds['customerID'], $useTest);
	}

	/**
	* get currency for feed
	* @param array $feed
	* @return string
	*/
	protected function getActiveCurrency($feed) {
		// if feed has specified a currency, use that
		if (!empty($feed['meta']['custom_connection']) && !empty($feed['meta']['currency'])) {
			return $feed['meta']['currency'];
		}

		// get the Gravity Forms currency setting as the default
		return GFCommon::get_currency();
	}

	/**
	* test whether form for feed has a MailChimp feed
	* @param int $form_id
	* @return boolean
	*/
	protected static function formHasMailChimp($form_id) {
		if ($form_id && class_exists('GFMailChimp', false)) {
			return !!GFMailChimp::has_mailchimp($form_id);
		}

		return false;
	}

	/**
	* test whether form for feed has a User Registration feed
	* @param int $form_id
	* @return boolean
	*/
	protected static function formHasUserRego($form_id) {
		if ($form_id && class_exists('GFUser', false)) {
			return !!GFUser::get_config($form_id);
		}

		return false;
	}

	/**
	* test if feed requires a transaction (something to charge the customer)
	* @param array $feed
	* @return bool
	*/
	protected static function testFeedRequiresTransaction($feed) {
		if ($feed['meta']['feedMethod'] === 'recurxml') {
			// always required for Recurring Payments
			return true;
		}

		return rgar($feed['meta'], 'rememberCustomer', 'off') === 'off' || empty($feed['meta']['rememberCustomer_no_tx']);
	}

    /**
    * activate and configure custom entry meta
    * @param array $entry_meta
    * @param int $form_id
    * @return array
    */
	public function get_entry_meta($entry_meta, $form_id) {

		// not on feed admin screen
		if (is_admin()) {
			global $hook_suffix;
			$subview = isset($_GET['subview']) ? $_GET['subview'] : '';

			if ($hook_suffix === 'toplevel_page_gf_edit_forms' && $subview === $this->_slug) {
				return $entry_meta;
			}
		}

		$entry_meta['gateway_txn_id'] = array(
			'label'					=> esc_html_x('Transaction ID', 'entry meta label', 'gravityforms-eway-pro'),
			'is_numeric'			=> false,
			'is_default_column'		=> false,
			'filter'				=> array(
											'operators' => array('is', 'isnot')
										),
		);

		$entry_meta['payment_gateway'] = array(
			'label'					=> esc_html_x('Payment Gateway', 'entry meta label', 'gravityforms-eway-pro'),
			'is_numeric'			=> false,
			'is_default_column'		=> false,
			'filter'				=> array(
											'operators' => array('is', 'isnot')
										),
		);

		$entry_meta['authcode'] = array(
			'label'					=> esc_html_x('AuthCode', 'entry meta label', 'gravityforms-eway-pro'),
			'is_numeric'			=> false,
			'is_default_column'		=> false,
			'filter'				=> array(
											'operators' => array('is', 'isnot')
										),
		);

		$entry_meta['beagle_score'] = array(
			'label'					=> esc_html_x('Beagle Score', 'entry meta label', 'gravityforms-eway-pro'),
			'is_numeric'			=> false,	// TODO: maybe numeric for Beagle score?
			'is_default_column'		=> false,
			'filter'				=> array(
											'operators' => array('is', 'isnot', '<', '>')
										),
		);

		$entry_meta['eway_token'] = array(
			'label'					=> esc_html_x('Customer Token', 'entry meta label', 'gravityforms-eway-pro'),
			'is_numeric'			=> false,
			'is_default_column'		=> false,
			'filter'				=> array(
											'operators' => array('is', 'isnot')
										),
		);

		return $entry_meta;
	}

}
