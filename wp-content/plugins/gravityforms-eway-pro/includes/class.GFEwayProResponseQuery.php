<?php

if (!defined('ABSPATH')) {
	exit;
}

/**
* extend eWAY response for Shared Page request
* @link https://eway.io/api-v3/
*/
class GFEwayProResponseQuery extends GFEwayProResponse {

	#region members

	/**
	* list of transactions returned by query
	* @var array
	*/
	public $Transactions;

	/**
	* a list of errors
	* @var array
	*/
	public $Errors;

	#endregion

	/**
	* load eWAY response data as JSON string
	* @param string $json eWAY response as a string (hopefully of JSON data)
	* @throws GFEwayProException
	*/
	public function loadResponse($json) {
		parent::loadResponse($json);

		foreach ($this->Transactions as $transaction) {
			// convert amounts back into dollars.cents from just cents
			if (!empty($transaction->TotalAmount)) {
				$transaction->TotalAmount = floatval($transaction->TotalAmount) / 100.0;
			}

			$transaction->ResponseMessage = $this->getResponseDetails($transaction->ResponseMessage);
		}
	}

	/**
	* get 'invalid response' message for this response class
	* @return string
	*/
	protected function getMessageInvalid() {
		return __('Invalid response from eWAY for Shared Page request', 'gravityforms-eway-pro');
	}

}
