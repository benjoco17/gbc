<?php

if (!defined('ABSPATH')) {
	exit;
}

class GFEwayProFieldCustomerTokens extends GF_Field {

	const GFIELD_TYPE		= 'gfewaypro_cust_tokens';

	public $type			= self::GFIELD_TYPE;

	/**
	* register field with Gravity Forms
	*/
	public static function register() {
		GF_Fields::register(new GFEwayProFieldCustomerTokens());

		add_action('gform_editor_js_set_default_values', array(__CLASS__, 'gformJsSetDefaultValues'));
		add_action('gform_enqueue_scripts', array(__CLASS__, 'gformEnqueueScripts'), 20, 2);
		add_filter('gform_field_css_class', array(__CLASS__, 'maybeHideField'), 10, 3);
		add_filter('gform_is_value_match', array(__CLASS__, 'gformIsValueMatch'), 10, 6);
	}

	/**
	* register form editor buttons for custom fields
	* @param array $button_groups
	* @return array
	*/
	public static function addFieldButtons($buttons) {
		$buttons[] = array(
			'class'			=> 'button',
			'data-type'		=> self::GFIELD_TYPE,
			'value'			=> esc_html_x('Customer Tokens', 'form editor buttons', 'gravityforms-eway-pro'),
		);

		return $buttons;
	}

	/**
	* register form editor strings for custom field
	* @param array $strings nested array, group_name => array(key => string)
	* @return array
	*/
	public static function addFormEditorStrings($strings) {
		$strings['customer_tokens'] = array(
			'only_one'			=> __('Only one Customer Tokens field can be added to the form', 'gravityforms-eway-pro'),
			'gfeway_new_card'	=> _x('New card', 'conditional logic operator label', 'gravityforms-eway-pro'),
		);

		return $strings;
	}

	/**
	* add JS code for setting field default values
	*/
	public static function gformJsSetDefaultValues() {
		require GFEWAYPRO_PLUGIN_ROOT . 'views/admin-editorjs-default-values.php';
	}

	/**
	* enqueue additional scripts if required by form
	* @param array $form
	* @param boolean $ajax
	*/
	public static function gformEnqueueScripts($form, $ajax) {
		if (is_array($form['fields'])) {
			foreach ($form['fields'] as $field) {
				if (GFFormsModel::get_input_type($field) === self::GFIELD_TYPE) {
					add_action('wp_print_footer_scripts', array(__CLASS__, 'addFooterScript'));
					add_action('gform_preview_footer', array(__CLASS__, 'addFooterScript'));
					break;
				}
			}
		}
	}

	/**
	* add the footer script for this field type
	*/
	public static function addFooterScript() {
		$min = SCRIPT_DEBUG ? '' : '.min';

		echo '<script>';
		readfile(GFEWAYPRO_PLUGIN_ROOT . "js/customer-tokens$min.js");
		echo '</script>';
	}

	/**
	* if visitor is anonymous (not logged in) then don't show field
	* @param string $classes
	* @param GF_Field $field
	* @param array $form
	* @return string
	*/
	public static function maybeHideField($classes, $field, $form) {
		if ($field->type === self::GFIELD_TYPE && self::testHideField($field, $form)) {
			if (!preg_match('/\bgform_hidden\b/', $classes)) {
				$classes .= ' gform_hidden';
			}
		}

		return $classes;
	}

	/**
	* test whether field should be hidden, e.g. anonymous user (not logged in)
	* @param GF_Field $field
	* @param array $form
	* @return bool
	*/
	protected static function testHideField($field, $form) {
		if (is_user_logged_in()) {
			// always show logged-in customers the field
			$hide = false;
		}
		else {
			// only show anonymous customers if configured to rememberCustomer
			$hide = !self::custCanAskRemember($field, $form);
		}

		return apply_filters('gfeway_hide_field_customer_tokens', $hide, $field, $form);
	}

	/**
	* test whether customer can ask to be remembered
	* @param GF_Field $field
	* @param array $form
	* @return bool
	*/
	protected static function custCanAskRemember($field, $form) {
		$can_ask = false;
		$addon = GFEwayProAddOn::get_instance();
		$feeds = $addon->get_active_feeds($form['id']);
		foreach ($feeds as $feed) {
			$rememberCustomer = rgar($feed['meta'], 'rememberCustomer', 'off');
			if ($rememberCustomer === 'ask') {
				$can_ask = true;
				break;
			}
		}

		return apply_filters('gfeway_customer_can_ask_remember_card', $can_ask, $field, $form);
	}

	/**
	* field title
	* @return string
	*/
	public function get_form_editor_field_title() {
		return esc_attr_x('Customer Tokens', 'form editor buttons', 'gravityforms-eway-pro');
	}

	/**
	* the class names of the settings which should be available on the field in the form editor
	* @return array
	*/
	public function get_form_editor_field_settings() {
		// TODO: review for required settings
		return array(
			'conditional_logic_field_setting',
			'error_message_setting',
			'label_setting',
			'label_placement_setting',
			//~ 'sub_labels_setting',	// TODO: support customisable sub-labels
			'sub_label_placement_setting',
			'admin_label_setting',
			'rules_setting',
			'description_setting',
			'css_class_setting',
			//~ 'input_placeholders_setting',
		);
	}

	/**
	* allow field to be used in conditional logic
	* @return bool
	*/
	public function is_conditional_logic_supported() {
		return true;
	}

	/**
	* don't add editor button to a standard group; we're adding it to a custom button group
	* @return array
	*/
	public function get_form_editor_button() {
		return array();
	}

	//~ public function validate( $value, $form ) {
		// TODO: validate()
	//~ }

	/**
	* get the field value on form submit
	* @param array $field_values
	* @param bool $get_from_post
	* @return array|string
	*/
	public function get_value_submission($field_values, $get_from_post = true) {
		$id = $this->id;

		if ($get_from_post) {
			$value[$id . '.1'] = $this->get_input_value_submission("input_{$id}_1", '', $field_values, true);
			$value[$id . '.2'] = $this->get_input_value_submission(isset($_POST['EWAY_CARDCVN']) ? 'EWAY_CARDCVN' : "input_{$id}_2", '', $field_values, true);
			$value[$id . '.3'] = $this->get_input_value_submission("input_{$id}_3", '', $field_values, true);
		} else {
			$value = $this->get_input_value_submission('input_' . $id, $this->inputName, $field_values, $get_from_post);
		}

		return $value;
	}

	/**
	* get html for field inputs
	* @param array $form
	* @param string|array $value
	* @param array $entry
	* @return string
	*/
	public function get_field_input( $form, $value = '', $entry = null ) {
		if (self::testHideField($this, $form)) {
			return '';
		}

		$is_entry_detail = $this->is_entry_detail();
		$is_form_editor  = $this->is_form_editor();
		$is_postback     = isset(GFFormDisplay::$submission[$form['id']]);

		$form_id  = $form['id'];
		$id       = intval( $this->id );
		$field_id = $is_entry_detail || $is_form_editor || $form_id == 0 ? "input_$id" : "input_{$form_id}_{$id}";
		$form_id  = ( $is_entry_detail || $is_form_editor ) && empty( $form_id ) ? rgget( 'id' ) : $form_id;

		$disabled_text = $is_form_editor ? 'disabled="disabled"' : '';
		$class_suffix  = $is_entry_detail ? '_admin' : '';


		$form_sub_label_placement  = rgar( $form, 'subLabelPlacement' );
		$field_sub_label_placement = $this->subLabelPlacement;
		$is_sub_label_above        = $field_sub_label_placement === 'above' || ( empty( $field_sub_label_placement ) && $form_sub_label_placement == 'above' );
		$sub_label_class_attribute = $field_sub_label_placement === 'hidden_label' ? 'class="hidden_sub_label screen-reader-text"' : '';

		$css = isset($field->cssClass) ? esc_attr($field->cssClass) : '';

		if (GFFormsModel::is_html5_enabled()) {
			$cvv_validation = sprintf(' pattern="[0-9]*" inputmode="numeric" title="%s"', esc_attr_x('Only digits 0-9 are allowed', 'security code validation message', 'gravityforms-eway-pro'));
		}
		else {
			$cvv_validation = '';
		}

		if ($is_form_editor) {
			$tokenlist = array(
				'example1' => '44443XXXXXXXX111',
				'example2' => '54545XXXXXXXX454',
			);
		}
		else {
			$tokens = new GFEwayProCustomerTokens();
			$tokenlist = $tokens->getTokenList();
		}
		$conditional_fields = $this->conditionalLogicFields ? $this->conditionalLogicFields : array();

		if (is_array($value) && $is_postback) {
			// pick up selected token posted from form
			$current_token = rgget("$id.1", $value);
		}
		else {
			// pick up first token in list, if any
			if (count($tokenlist) > 0) {
				$keys = array_keys($tokenlist);
				$current_token = reset($keys);
			}
			else {
				$current_token = '';
			}
		}

		$cust_can_ask_remember = self::custCanAskRemember($this, $form);

		ob_start();
		require GFEWAYPRO_PLUGIN_ROOT . 'views/field-customer-tokens.php';
		$out = ob_get_clean();

		// trim leading whitespace
		$out = preg_replace('#^\s+#m', '', $out);

		return $out;
	}

	/**
	* get entry detail for selected token
	* @param string|array $value
	* @param string $currency
	* @param bool|false $use_text
	* @param string $format
	* @param string $media
	* @return string
	*/
	public function get_value_entry_detail($value, $currency = '', $use_text = false, $format = 'html', $media = 'screen') {
		if ( is_array( $value ) ) {
			$tokens = new GFEwayProCustomerTokens();
			$token = trim(rgget($this->id . '.1', $value));

			$remember = rgget($this->id . '.3', $value) ? '1' : '';

			if ($token) {
				// can't replace token with cardnumber here, because we need the user ID from the entry; replaced by filter
				$value = $token;
				add_filter('gform_entry_field_value', array(__CLASS__, 'entryDetailCardNumber'), 10, 3);
			}
			else if ($remember) {
				$value = _x('remember card', 'card token entry', 'gravityforms-eway-pro');
			}
			else {
				$value = '';
			}
		}

		return $value;
	}

	/**
	* replace token with card number on entry details page
	* @param string|array $value
	* @param GF_Field $field
	* @param array $entry
	* @return string
	*/
	public static function entryDetailCardNumber($value, $field, $entry) {
		if ($field->type === self::GFIELD_TYPE) {
			if (!empty($value) && !empty($entry['created_by'])) {
				// get partial card number
				$tokens = new GFEwayProCustomerTokens();
				$value = $tokens->getCardnumber($value, $entry['created_by']);
			}

			remove_filter('gform_entry_field_value', array(__CLASS__, __FUNCTION__), 10, 3);
		}

		return $value;
	}

	/**
	* Format the entry value for display on the entries list page.
	* @param string|array $value
	* @param array $entry
	* @param string $field_id
	* @param array $columns
	* @param array $form
	* @return string
	*/
	public function get_value_entry_list($value, $entry, $field_id, $columns, $form) {
		$subfield = explode('.', $field_id);
		if (count($subfield) === 2) {
			switch ($subfield[1]) {

				case '1':
					if (!empty($value) && !empty($entry['created_by'])) {
						// get partial card number
						$tokens = new GFEwayProCustomerTokens();
						$value = $tokens->getCardnumber($value, $entry['created_by']);
					}
					break;

				case '2':
					// never show the CVN
					$value = '';
					break;

				case '3':
					if (!empty($value)) {
						// customer ticked "remember card"
						$value = _x('remember card', 'card token entry', 'gravityforms-eway-pro');
					}
					break;

			}
		}

		return $value;
	}

	/**
	* only save the token from the field, not the CVN
	* TODO: record Remember Card tick as a string (the sub-label value?)
	* @return string
	*/
	public function get_value_save_entry( $value, $form, $input_name, $lead_id, $lead ) {

		list($input_token, $field_id_token, $input_id) = rgexplode('_', $input_name, 3);

		if ($input_id === '2') {
			$value = '';
		}

		return $value;
	}

	/**
	* custom conditional logic test for custom rule
	* @param bool $is_match
	* @param mixed $field_value
	* @param $target_value
	* @param string $operation
	* @param GF_Field $source_field
	* @param array $rule
	* @return bool
	*/
	public static function gformIsValueMatch($is_match, $field_value, $target_value, $operation, $source_field, $rule) {
		// NB: sometimes $source_field is skipped and $rule contains a Form object
		if (isset($rule['operator']) && $rule['operator'] === 'gfeway_new_card' && $source_field->type === self::GFIELD_TYPE) {
			$is_match = is_array($field_value) ? empty($field_value[0]) : empty($field_value);
		}

		return $is_match;
	}

}
