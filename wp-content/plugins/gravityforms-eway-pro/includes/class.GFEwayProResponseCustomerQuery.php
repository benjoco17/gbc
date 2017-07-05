<?php

if (!defined('ABSPATH')) {
	exit;
}

/**
* extend eWAY response for Customer request
* @link https://eway.io/api-v3/
*/
class GFEwayProResponseCustomerQuery extends GFEwayProResponse {

	#region members

	/**
	* list of transactions returned by query
	* @var array
	*/
	public $Customers;

	/**
	* a list of errors
	* @var array
	*/
	public $Errors;

	#endregion

	/**
	* get 'invalid response' message for this response class
	* @return string
	*/
	protected function getMessageInvalid() {
		return __('Invalid response from eWAY for token customer request', 'gravityforms-eway-pro');
	}

	/**
	* check for customer details
	* @return bool
	*/
	public hasCustomerDetails() {
		return is_array($this->Customers) && count($this->Customers) > 0;
	}

	/**
	* get card number for token customer
	* @return bool
	*/
	public getCardNumber() {
		if ($this->hasCustomerDetails()) {
			$customer = $this->Customers[0];
			if (!empty($customer->CardDetails->Number)) {
				return $customer->CardDetails->Number;
			}
		}

		return false;
	}

}
