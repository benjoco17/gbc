<?php

if (!defined('ABSPATH')) {
	exit;
}

/**
* extend eWAY response for Shared Page request
* @link https://eway.io/api-v3/
*/
class GFEwayProResponseSharedPage extends GFEwayProResponse {

	#region members

	/**
	* redirect URL for the shared payment page, including query parameters (AccessCode)
	* @var string
	*/
	public $SharedPaymentUrl;

	/**
	* post URL for redirecting client from a form post
	* @var string
	*/
	public $FormActionURL;

	/**
	* unique access code for transaction (already included in SharedPaymentURL)
	* @var string
	*/
	public $AccessCode;

	/**
	* customer details object (includes card details object)
	* @var object
	*/
	public $Customer;

	/**
	* payment details object
	* @var object
	*/
	public $Payment;

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
		return __('Invalid response from eWAY for Shared Page request', 'gravityforms-eway-pro');
	}

}
