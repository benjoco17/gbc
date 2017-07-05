<?php

if (!defined('ABSPATH')) {
	exit;
}

/**
* manage eWAY Client Side Encryption
*/
class GFEwayProClientSideEncryption {

	protected $addon;									// the add-on object
	protected $ecryptKeys;								// array -- ecrypt keys for active forms
	protected $ecryptRecurring;							// array -- nested array of conditional logic rules for Recurring Payment feeds
	protected $txCardNumber = null;						// obfuscated credit card number from last transaction

	public function __construct($addon) {
		$this->addon = $addon;

		add_filter('gform_pre_render', array($this, 'ecryptModifyForm'));
		add_filter('gform_pre_validation', array($this, 'ecryptPreValidation'));
		add_filter('gform_has_conditional_logic', array($this, 'ecryptMaybeConditionalLogic'), 10, 2);
	}

	/**
	* record the obfuscated credit card number from the last transaction
	* @param string $cardNumber
	*/
	public function setCardNumber($cardNumber) {
		$this->txCardNumber = $cardNumber;
	}

	/**
	* test if can enqueue client-side encryption scripts
	* @param array $form
	* @param boolean $ajax
	* @return bool
	*/
	public function canEnqueueEncryptScripts($form, $is_ajax) {
		$is_admin = is_admin() && strpos(rgget('page'), 'gf_') === 0;

		if (!$is_admin && !empty($form) && $this->canEncryptCardDetails($form)) {
			add_action('wp_print_footer_scripts', array($this, 'ecryptInitScript'));
			add_action('gform_preview_footer', array($this, 'ecryptInitScript'));

			return true;
		}

		return false;
	}

	/**
	* maybe flag form as requiring conditional logic, if needed for working around CSE vs Recurring Payments
	* this tells Gravity Forms to load JavaScript support for conditional logic in the browser
	* @param bool $has_conditions
	* @param array $form
	* @return bool
	*/
	public function ecryptMaybeConditionalLogic($has_conditions, $form) {
		if (!empty($this->ecryptRecurring)) {
			$has_conditions = true;
		}

		return $has_conditions;
	}

	/**
	* register inline scripts for client-side encryption if form posts with AJAX
	*/
	public function ecryptInitScript() {
		$min = SCRIPT_DEBUG ? '' : '.min';

		echo '<script>';
		printf('var gfewaypro_recurring_rules = %s;', empty($this->ecryptRecurring) ? 'false' : json_encode($this->ecryptRecurring));
		readfile(GFEWAYPRO_PLUGIN_ROOT . "js/gfewaypro_ecrypt$min.js");
		echo '</script>';
	}

	/**
	* set form modifiers for eWAY client side encryption
	* @param array $form
	* @return array
	*/
	public function ecryptModifyForm($form) {
		if ($this->canEncryptCardDetails($form)) {
			// inject eWAY Client Side Encryption
			add_filter('gform_form_tag', array($this, 'ecryptFormTag'), 10, 2);
			add_filter('gform_field_content', array($this, 'ecryptCcField'), 10, 5);
			add_filter('gform_get_form_filter_' . $form['id'], array($this, 'ecryptEndRender'), 10, 2);

			// clear any previously set credit card data set for fooling GF validation
			foreach ($form['fields'] as $field) {
				if (GFFormsModel::get_input_type($field) === 'creditcard') {
					$field_name    = 'input_' . $field->id;
					$ccnumber_name = $field_name . '_1';
					$cvn_name      = $field_name . '_3';

					// clear dummy credit card details used for Gravity Forms validation
					if (!empty($_POST[$ccnumber_name]) || !empty($_POST[$cvn_name])) {
						$_POST[$ccnumber_name] = '';
						$_POST[$cvn_name]      = '';
					}

					// exit loop
					break;
				}
			}
		}

		return $form;
	}

	/**
	* stop injecting eWAY Client Side Encryption
	* @param string $html form html
	* @param array $form
	* @return string
	*/
	public function ecryptEndRender($html, $form) {
		remove_filter('gform_form_tag', array($this, 'ecryptFormTag'), 10, 2);
		remove_filter('gform_field_content', array($this, 'ecryptCcField'), 10, 5);

		return $html;
	}

	/**
	* inject eWAY Client Side Encryption into form tag
	* @param string $tag
	* @param array $form
	* @return string
	*/
	public function ecryptFormTag($tag, $form) {
		$attr = sprintf('data-gfeway-encrypt-key="%s"', esc_attr($this->ecryptKeys[$form['id']]));
		$tag = str_replace('<form ', "<form $attr ", $tag);

		return $tag;
	}

	/**
	* inject eWAY Client Side Encryption into credit card field
	* @param string $field_content
	* @param GF_Field $field
	* @param string $value
	* @param int $zero
	* @param int $form_id
	* @return string
	*/
	public function ecryptCcField($field_content, $field, $value, $zero, $form_id) {
		if (RGFormsModel::get_input_type($field) === 'creditcard') {
			$field_id    = "input_{$form_id}_{$field->id}";
			$ccnumber_id = $field_id . '_1';
			$cvn_id      = $field_id . '_3';

			$field_content = preg_replace("#<input[^>]+id='$ccnumber_id'\K#", ' data-gfeway-encrypt-name="EWAY_CARDNUMBER"', $field_content);
			$field_content = preg_replace("#<input[^>]+id='$cvn_id'\K#",      ' data-gfeway-encrypt-name="EWAY_CARDCVN"', $field_content);
		}

		return $field_content;
	}

	/**
	* put something back into Credit Card field inputs, to enable validation when using eWAY Client Side Encryption
	* @param array $form
	* @return array
	*/
	public function ecryptPreValidation($form) {
		if ($this->canEncryptCardDetails($form)) {

			if (!empty($_POST['EWAY_CARDNUMBER']) && !empty($_POST['EWAY_CARDCVN'])) {
				foreach ($form['fields'] as $field) {
					if (GFFormsModel::get_input_type($field) === 'creditcard') {
						$field_name    = 'input_' . $field->id;
						$ccnumber_name = $field_name . '_1';
						$cvn_name      = $field_name . '_3';

						// fake some credit card details for Gravity Forms to validate
						$_POST[$ccnumber_name] = self::getTestCardNumber($field->creditCards);
						$_POST[$cvn_name]      = '***';

						add_action("gform_save_field_value_{$form['id']}_{$field->id}", array($this, 'ecryptSaveCreditCard'), 10, 5);

						// exit loop
						break;
					}
				}
			}

		}

		return $form;
	}

	/**
	* change the credit card field value so that it doesn't imply an incorrect card type when using Client Side Encryption
	* @param string $value
	* @param array $lead
	* @param GF_Field $field
	* @param array $form
	* @param string $input_id
	* @return string
	*/
	public function ecryptSaveCreditCard($value, $lead, $field, $form, $input_id) {
		switch (substr($input_id, -2, 2)) {

			case '.1':
				// card number
				$value = empty($this->txCardNumber) ? 'XXXXXXXXXXXXXXXX' : $this->txCardNumber;
				break;

			case '.4':
				// card type
				$value = empty($this->txCardNumber) ? false : self::maybeGetCardType($this->txCardNumber);
				if (empty($value)) {
					// translators: credit card type reported when card type is unknown due to client-side encryption
					$value = _x('Card', 'credit card type', 'gravityforms-eway');
				}
				break;

		}

		return $value;
	}

	/**
	* attempt to match a partial card number to a card type
	* @param string $number
	* @return string
	*/
	protected static function maybeGetCardType($number) {
		$cards = GFCommon::get_card_types();

		$number_length = strlen($number);

		foreach ($cards as $card) {
			// check for matching prefix
			foreach (explode(',', $card['prefixes']) as $prefix) {
				if (strpos($number, $prefix) === 0) {
					// check for matching length
					foreach (explode(',', $card['lengths']) as $length) {
						if ($number_length == absint($length)) {
							return $card['name'];
						}
					}
				}
			}
		}

		return false;
	}

	/**
	* find a test card number for a supported credit card, for faking card number validation when encrypting card details
	* @param array $supportedCards
	* @return string
	*/
	protected static function getTestCardNumber($supportedCards) {
		if (empty($supportedCards)) {
			$cardType = 'visa';
		}
		else {
			$cardType = $supportedCards[0];
		}

		$testNumbers = array(
			'amex'			=> '378282246310005',
			'discover'		=> '6011111111111117',
			'mastercard'	=> '5105105105105100',
			'visa'			=> '4444333322221111',
		);

		return isset($testNumbers[$cardType]) ? $testNumbers[$cardType] : $testNumbers['visa'];
	}

	/**
	* look at config to see whether client-side encryption is possible
	* @param array $form
	* @return bool
	*/
	protected function canEncryptCardDetails($form) {
		if (!is_array($this->ecryptKeys)) {
			$this->ecryptKeys = array();
			$this->ecryptRecurring = array();
		}
		elseif (isset($this->ecryptKeys[$form['id']])) {
			return !empty($this->ecryptKeys[$form['id']]);
		}

		$feeds = $this->addon->get_active_feeds($form['id']);
		$this->ecryptKeys[$form['id']] = false;

		// scan for Recurring Payments feeds and collect the conditional logic rules
		$recurringConditions = array();
		foreach ($feeds as $feed) {
			if ($feed['meta']['feedMethod'] === 'recurxml') {
				if (!empty($feed['meta']['feed_condition_conditional_logic'])) {
					$recurringConditions[] = rgars($feed['meta'], 'feed_condition_conditional_logic_object/conditionalLogic');
				}
			}
		}

		foreach ($feeds as $feed) {
			// must be a Direct Connection feed
			if ($feed['meta']['feedMethod'] !== 'direct') {
				continue;
			}

			$creds = $this->addon->getEwayCredentials($feed);

			// must have Rapid API key/password and Client Side Encryption key
			if (empty($creds['ecryptKey']) || empty($creds['apiKey']) || empty($creds['password'])) {
				return false;
			}

			// must have a credit card field
			if (!GFCommon::has_credit_card_field($form)) {
				return false;
			}

			// record ecrypt key and any Recurring Payments conditional logic rules
			$this->ecryptKeys[$form['id']] = $creds['ecryptKey'];
			if (!empty($recurringConditions)) {
				$this->ecryptRecurring[$form['id']] = $recurringConditions;
			}
			return true;
		}

		return false;
	}

}
