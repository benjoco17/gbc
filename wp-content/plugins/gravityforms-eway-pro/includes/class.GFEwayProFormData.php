<?php

if (!defined('ABSPATH')) {
	exit;
}

/**
* class for managing form data
*/
class GFEwayProFormData {

	public $total					= 0;
	public $formID					= 0;

	// field mappings to GF form
	public $description;
	public $title;
	public $firstName;
	public $lastName;
	public $companyName;
	public $jobDesc;
	public $billAddress1;
	public $billAddress2;
	public $billCity;
	public $billState;
	public $billPostcode;
	public $billCountry;
	public $billPhone;
	public $billMobile;
	public $email;
	public $fax;
	public $website;
	public $comments;
	public $shipFirstName;
	public $shipLastName;
	public $shipAddress1;
	public $shipAddress2;
	public $shipCity;
	public $shipState;
	public $shipPostcode;
	public $shipCountry;
	public $shipEmail;
	public $shipPhone;
	public $shipFax;
	public $option1;
	public $option2;
	public $option3;

	// credit card fields (Direct Connection feeds)
	public $ccName					= '';
	public $ccNumber				= '';
	public $ccExpMonth				= '';
	public $ccExpYear				= '';
	public $ccCVN					= '';
	public $ccField					= false;					// handle to meta-"field" for credit card in form

	// token payments
	public $customerToken			= '';
	public $rememberCard			= false;

	// recurring payment fields
	public $recurringAmount;
	public $billingCycle_unit;
	public $billingCycle_length;
	public $recurringTimes;
	public $initialAmount;
	public $recurringStart_unit;
	public $recurringStart_length;
	public $recurringStart_date;

	private $hasPurchaseFieldsFlag	= false;
	private $inverseMap;

	/**
	* initialise instance
	* @param array $form
	* @param array $feed
	*/
	public function __construct(&$form, $feed) {
		// load the form data
		$this->formID = $form['id'];
		$this->loadGfFieldMap($feed['meta']);
		$this->loadForm($form, $feed);
	}

	/**
	* load the form data we care about from the form array
	* @param array $form
	* @param array $feed
	*/
	private function loadForm(&$form, $feed) {
		$this->setMappedFieldValue('form_title',		$form['title']);
		$this->setMappedFieldValue('date_created',		date('Y-m-d H:i:s'));
		$this->setMappedFieldValue('source_url',		get_permalink());
		$this->setMappedFieldValue('payment_gateway',	GFEwayProAddOn::PAYMENT_GATEWAY);

		// TODO: id, ip, authcode, beagle_score

		// iterate over fields to collect data
		foreach ($form['fields'] as &$field) {
			$id = (string) $field->id;

			switch(GFFormsModel::get_input_type($field)){

				case 'total':
					$this->hasPurchaseFieldsFlag = true;
					break;

				case 'creditcard':
					if (!GFFormsModel::is_field_hidden($form, $field, array())) {
						$this->ccField					=& $field;
						$this->ccName					= trim(rgpost("input_{$id}_5"));
						$this->ccNumber					= self::cleanCcNumber(trim(rgpost("input_{$id}_1")));
						$ccExp							= rgpost("input_{$id}_2");
						if (is_array($ccExp) && count($ccExp) === 2) {
							list($this->ccExpMonth, $this->ccExpYear) = $ccExp;
						}
						$this->ccCVN					= trim(rgpost("input_{$id}_3"));

						// handle optional eWAY Client Side Encryption
						if (!empty($_POST['EWAY_CARDNUMBER']) && !empty($_POST['EWAY_CARDCVN'])) {
							$this->ccNumber				= trim(rgpost('EWAY_CARDNUMBER'));
							$this->ccCVN				= trim(rgpost('EWAY_CARDCVN'));
						}
					}
					break;

				case 'gfewaypro_cust_tokens':
					if (!GFFormsModel::is_field_hidden($form, $field, array())) {
						$this->customerToken		= rgpost("input_{$id}_1");
						$this->rememberCard			= $this->customerToken ? false : (bool) rgpost("input_{$id}_3");

						if (!empty($this->customerToken)) {
							// handle optional eWAY Client Side Encryption
							$input_cvn              = isset($_POST['EWAY_CARDCVN']) ? 'EWAY_CARDCVN' : "input_{$id}_2";
							$this->ccCVN			= trim(rgpost($input_cvn));
						}
					}
					break;

				default:
					if ($field->type === 'shipping' || $field->type === 'product') {
						$this->hasPurchaseFieldsFlag = true;
					}
					break;

			}

			// check for feed mapping
			$inputs = $field->get_entry_inputs();
			if ($field->type === 'date') {
				// parse data value according to the field format
				$value = $field->get_value_save_entry(rgpost('input_' . $id), $form, 'input_' . $id, 0, false);
				$this->setMappedFieldValue($id, $value);
			}
			elseif (is_array($inputs)) {
				// compound field
				$values = array();

				foreach($inputs as $input) {
					$sub_id = strtr($input['id'], '.', '_');

					// collect sub-field values in case want a compound field as one field value
					$values[] = trim(rgpost('input_' . $sub_id));

					// pass to any fields that want a sub-field
					$this->setMappedFieldValue((string) $input['id'], rgpost('input_' . $sub_id));
				}

				// see if want the whole field as one field value
				if (isset($this->inverseMap[$id])) {
					$this->setMappedFieldValue($id, implode(' ', array_filter($values, 'strlen')));
				}
			}
			else {
				// simple field, just take value
				$this->setMappedFieldValue($id, rgpost('input_' . $id));
			}
		}

		$entry = GFFormsModel::get_current_lead();
		$this->total = GFCommon::get_order_total($form, $entry);

		$this->setMappedFieldValue('form_total', $this->total);
	}

	/**
	* check whether form has any product fields (because CC needs something to bill against)
	* @return boolean
	*/
	public function hasPurchaseFields() {
		return $this->hasPurchaseFieldsFlag;
	}

	/**
	* get inverse map of GF fields to feed fields
	* @param array $feed_meta
	* @return array
	*/
	private function loadGfFieldMap($feed_meta) {
		$this->inverseMap = array();

		$fields = array(
			'description', 'title', 'firstName', 'lastName', 'companyName', 'jobDesc',
			'billAddress1', 'billAddress2', 'billCity', 'billState', 'billPostcode', 'billCountry',
			'billPhone', 'billMobile', 'email', 'fax', 'website', 'comments',
			'shipFirstName', 'shipLastName', 'shipAddress1', 'shipAddress2', 'shipCity', 'shipState', 'shipPostcode', 'shipCountry',
			'shipPhone', 'shipEmail', 'shipFax', 'option1', 'option2', 'option3',
		);

		foreach ($fields as $name) {
			$mapped_name = 'mappedFields_' . $name;
			if (!empty($feed_meta[$mapped_name])) {
				$this->addInverseMapping($feed_meta[$mapped_name], $name);
			}
		}

		// pick up Recurring Payments XML fields
		if ($feed_meta['feedMethod'] === 'recurxml') {

			if (!empty($feed_meta['initialAmount'])) {
				$this->addInverseMapping($feed_meta['initialAmount'], 'initialAmount');
			}

			if (!empty($feed_meta['recurringAmount'])) {
				$this->addInverseMapping($feed_meta['recurringAmount'], 'recurringAmount');
			}

			$this->billingCycle_unit = $feed_meta['billingCycle_unit'];
			if ($feed_meta['billingCycle_length'] == -1 && !empty($feed_meta['billingCycle_mapped'])) {
				$this->addInverseMapping($feed_meta['billingCycle_mapped'], 'billingCycle_length');
			}
			else {
				$this->billingCycle_length = $feed_meta['billingCycle_length'];
			}

			if ($feed_meta['recurringTimes_times'] == -1 && !empty($feed_meta['recurringTimes_mapped'])) {
				$this->addInverseMapping($feed_meta['recurringTimes_mapped'], 'recurringTimes');
			}
			else {
				$this->recurringTimes = $feed_meta['recurringTimes_times'];
			}

			$this->recurringStart_unit = $feed_meta['recurringStart_unit'];
			if ($this->recurringStart_unit === 'mapped') {
				if (!empty($feed_meta['recurringStart_date'])) {
					$this->addInverseMapping($feed_meta['recurringStart_date'], 'recurringStart_date');
				}
			}
			else {
				if ($feed_meta['recurringStart_length'] == -1 && !empty($feed_meta['recurringStart_mapped'])) {
					$this->addInverseMapping($feed_meta['recurringStart_mapped'], 'recurringStart_length');
				}
				else {
					$this->recurringStart_length = $feed_meta['recurringStart_length'];
				}
			}

		}
	}

	/**
	* add inverse mapping from GF field to eWAY field
	* @param string $gfField name of field mapped from Gravity Forms
	* @param string $ewayField name of field to in eWAY feed
	*/
	private function addInverseMapping($gfField, $ewayField) {
		if (empty($this->inverseMap[$gfField])) {
			$this->inverseMap[$gfField] = array($ewayField);
		}
		else {
			$this->inverseMap[$gfField][] = $ewayField;
		}
	}

	/**
	* set eWAY field values
	* @param string $gfField name of field mapped from Gravity Forms
	* @param string $value
	*/
	private function setMappedFieldValue($gfField, $value) {
		if (!empty($this->inverseMap[$gfField])) {
			foreach ($this->inverseMap[$gfField] as $ewayField) {
				$this->$ewayField = $value;
			}
		}
	}

	/**
	* clean up credit card number, removing spaces and dashes, so that it should only be digits if correctly submitted
	* @param string $ccNumber
	* @return string
	*/
	private static function cleanCcNumber($ccNumber) {
		return strtr($ccNumber, array(' ' => '', '-' => ''));
	}

	/**
	* test form for product fields
	* @param array $form
	* @return bool
	*/
	public static function hasProductFields($form) {
		foreach ($form['fields'] as $field) {
			if ($field->type === 'shipping' || $field->type === 'product') {
				return true;
			}
		}

		return false;
	}

}
