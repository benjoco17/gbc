<?php

if (!defined('ABSPATH')) {
	exit;
}

/**
* Class for dealing with an eWAY recurring payment response
*/
class GFEwayProResponseRecurringXML {

	#region members

	/**
	* For a successful transaction "True" is passed and for a failed transaction "False" is passed.
	* @var boolean
	*/
	public $status;

	/**
	* the error severity, either Error or Warning
	* @var string max. 16 characters
	*/
	public $errorType;

	/**
	* the error response returned by the bank
	* @var string max. 255 characters
	*/
	public $error;

	#endregion

	/**
	* load eWAY response data as XML string
	* @param string $response eWAY response as a string (hopefully of XML data)
	*/
	public function loadResponse($response) {
		// make sure we actually got something from eWAY
		if (strlen(trim($response)) === 0) {
			throw new GFEwayProException(__('eWAY payment request returned nothing; please check your card details', 'gravityforms-eway-pro'));
		}

		// prevent XML injection attacks, and handle errors without warnings
		$oldDisableEntityLoader = libxml_disable_entity_loader(true);
		$oldUseInternalErrors = libxml_use_internal_errors(true);

		try {
			$xml = simplexml_load_string($response);
			if ($xml === false) {
				$errors = array();
				foreach (libxml_get_errors() as $error) {
					$errors[] = $error->message;
				}
				throw new GFEwayProException(implode("\n", $errors));
			}

			$this->status = (strcasecmp((string) $xml->Result, 'success') === 0);
			$this->errorType = (string) $xml->ErrorSeverity;
			$this->error = (string) $xml->ErrorDetails;

			// restore old libxml settings
			libxml_disable_entity_loader($oldDisableEntityLoader);
			libxml_use_internal_errors($oldUseInternalErrors);
		}
		catch (Exception $e) {
			// restore old libxml settings
			libxml_disable_entity_loader($oldDisableEntityLoader);
			libxml_use_internal_errors($oldUseInternalErrors);

			throw new GFEwayProException(sprintf(__('Invalid response from eWAY for recurring payment: %s', 'gravityforms-eway-pro'), $e->getMessage()));
		}
	}

}
