<?php

if (!defined('ABSPATH')) {
	exit;
}

/**
* Class for dealing with an eWAY Rapid API payment
* @link https://eway.io/api-v3/
*/
class GFEwayProPayment {

	#region "constants"

	// API hosts
	const API_HOST_LIVE						= 'https://api.ewaypayments.com';
	const API_HOST_SANDBOX					= 'https://api.sandbox.ewaypayments.com';

	// API endpoints for REST/JSON
	const API_DIRECT_PAYMENT				= 'Transaction';
	const API_SHARED_ACCESS					= 'AccessCodesShared';
	const API_TRANSACTION_QUERY				= 'Transaction';
	const API_CUSTOMER_QUERY				= 'Customer';

	// XML API endpoints
	const XML_RECURRING_SANDBOX				= 'https://www.eway.com.au/gateway/rebill/test/upload_test.aspx';
	const XML_RECURRING_LIVE				= 'https://www.eway.com.au/gateway/rebill/upload.aspx';

	// connection methods (private definition)
	const CONNECT_DIRECT					= 'direct';
	const CONNECT_SHAREDPAGE				= 'shared';

	// valid actions
	const METHOD_PAYMENT					= 'ProcessPayment';
	const METHOD_AUTHORISE					= 'Authorise';
	const METHOD_TOKEN_CREATE				= 'CreateTokenCustomer';
	const METHOD_TOKEN_UPDATE				= 'UpdateTokenCustomer';
	const METHOD_TOKEN_PAYMENT				= 'TokenPayment';

	// valid transaction types
	const TRANS_PURCHASE					= 'Purchase';
	const TRANS_RECURRING					= 'Recurring';
	const TRANS_MOTO						= 'MOTO';

	// valid shipping methods
	const SHIP_METHOD_UNKNOWN				= 'Unknown';
	const SHIP_METHOD_LOWCOST				= 'LowCost';
	const SHIP_METHOD_CUSTOMER				= 'DesignatedByCustomer';
	const SHIP_METHOD_INTERNATIONAL			= 'International';
	const SHIP_METHOD_MILITARY				= 'Military';
	const SHIP_METHOD_NEXTDAY				= 'NextDay';
	const SHIP_METHOD_PICKUP				= 'StorePickup';
	const SHIP_METHOD_2DAY					= 'TwoDayService';
	const SHIP_METHOD_3DAY					= 'ThreeDayService';
	const SHIP_METHOD_OTHER					= 'Other';

	const PARTNER_ID						= '4577fd8eb9014c7188d7be672c0e0d88';

	#endregion // constants

	#region "members"

	#region "connection specific members"

	/**
	* use eWAY sandbox
	* @var boolean
	*/
	public $useSandbox;

	/**
	* capture payment (alternative is just authorise, no capture)
	* @var boolean
	*/
	public $capture;

	/**
	* default TRUE, whether to validate the remote SSL certificate
	* @var boolean
	*/
	public $sslVerifyPeer;

	/**
	* API key
	* @var string
	*/
	public $apiKey;

	/**
	* API password
	* @var string
	*/
	public $apiPassword;

	/**
	* eWAY customer ID, required for XML APIs
	* @var string
	*/
	public $customerID;

	/**
	* ID of device or application processing the transaction
	* @var string max. 50 characters
	*/
	public $deviceID;

	/**
	* HTTP user agent string identifying plugin, perhaps for debugging
	* @var string
	*/
	public $httpUserAgent;

	#endregion // "connection specific members"

	#region "payment specific members"

	/**
	* action to perform: one of the METHOD_* values
	* @var string
	*/
	public $method;

	/**
	* a unique transaction number from your site (NB: see transactionNumber which is intended for invoice number or similar)
	* @var string max. 50 characters
	*/
	public $transactionNumber;

	/**
	* an invoice reference to track by
	* @var string max. 12 characters
	*/
	public $invoiceReference;

	/**
	* description of what is being purchased / paid for
	* @var string max. 64 characters
	*/
	public $invoiceDescription;

	/**
	* total amount of payment, in dollars and cents as a floating-point number (will be converted to just cents for transmission)
	* @var float
	*/
	public $amount;

	/**
	* ISO 4217 currency code
	* @var string 3 characters in uppercase
	*/
	public $currencyCode;

	// customer and billing details

	/**
	* eWAY-generated token for customer, token payments only
	* @var string max. 16 characters
	*/
	public $customerToken;

	/**
	* customer's title
	* @var string max. 5 characters
	*/
	public $title;

	/**
	* customer's first name
	* @var string max. 50 characters
	*/
	public $firstName;

	/**
	* customer's last name
	* @var string max. 50 characters
	*/
	public $lastName;

	/**
	* customer's company name
	* @var string max. 50 characters
	*/
	public $companyName;

	/**
	* customer's job description (e.g. position)
	* @var string max. 50 characters
	*/
	public $jobDescription;

	/**
	* customer's address line 1
	* @var string max. 50 characters
	*/
	public $address1;

	/**
	* customer's address line 2
	* @var string max. 50 characters
	*/
	public $address2;

	/**
	* customer's suburb/city/town
	* @var string max. 50 characters
	*/
	public $suburb;

	/**
	* customer's state/province
	* @var string max. 50 characters
	*/
	public $state;

	/**
	* customer's postcode
	* @var string max. 30 characters
	*/
	public $postcode;

	/**
	* customer's country code
	* @var string 2 characters lowercase
	*/
	public $country;

	/**
	* customer's email address
	* @var string max. 50 characters
	*/
	public $emailAddress;

	/**
	* customer's phone number
	* @var string max. 32 characters
	*/
	public $phone;

	/**
	* customer's mobile phone number
	* @var string max. 32 characters
	*/
	public $mobile;

	/**
	* customer's fax number
	* @var string max. 32 characters
	*/
	public $fax;

	/**
	* customer's website URL
	* @var string max. 512 characters
	*/
	public $website;

	/**
	* comments about the customer
	* @var string max. 255 characters
	*/
	public $comments;

	// card details

	/**
	* name on credit card
	* @var string max. 50 characters
	*/
	public $cardHoldersName;

	/**
	* credit card number, with no spaces
	* @var string max. 50 characters
	*/
	public $cardNumber;

	/**
	* month of expiry, numbered from 1=January
	* @var integer max. 2 digits
	*/
	public $cardExpiryMonth;

	/**
	* year of expiry
	* @var integer will be truncated to 2 digits, can accept 4 digits
	*/
	public $cardExpiryYear;

	/**
	* start month, numbered from 1=January
	* @var integer max. 2 digits
	*/
	public $cardStartMonth;

	/**
	* start year
	* @var integer will be truncated to 2 digits, can accept 4 digits
	*/
	public $cardStartYear;

	/**
	* card issue number
	* @var string
	*/
	public $cardIssueNumber;

	/**
	* CVN (Creditcard Verification Number) for verifying physical card is held by buyer
	* @var string max. 3 or 4 characters (depends on type of card)
	*/
	public $cardVerificationNumber;

	/**
	* true when there is shipping information
	* @var bool
	*/
	public $hasShipping;

	/**
	* shipping method: one of the SHIP_METHOD_* values
	* @var string max. 30 characters
	*/
	public $shipMethod;

	/**
	* shipping first name
	* @var string max. 50 characters
	*/
	public $shipFirstName;

	/**
	* shipping last name
	* @var string max. 50 characters
	*/
	public $shipLastName;

	/**
	* shipping address line 1
	* @var string max. 50 characters
	*/
	public $shipAddress1;

	/**
	* shipping address line 2
	* @var string max. 50 characters
	*/
	public $shipAddress2;

	/**
	* shipping suburb/city/town
	* @var string max. 50 characters
	*/
	public $shipSuburb;

	/**
	* shipping state/province
	* @var string max. 50 characters
	*/
	public $shipState;

	/**
	* shipping postcode
	* @var string max. 30 characters
	*/
	public $shipPostcode;

	/**
	* shipping country code
	* @var string 2 characters lowercase
	*/
	public $shipCountry;

	/**
	* shipping email address
	* @var string max. 50 characters
	*/
	public $shipEmailAddress;

	/**
	* shipping phone number
	* @var string max. 32 characters
	*/
	public $shipPhone;

	/**
	* shipping fax number
	* @var string max. 32 characters
	*/
	public $shipFax;

	/**
	* optional additional information for use in shopping carts, etc.
	* @var array[string] max. 254 characters each
	*/
	public $options = array();

	#endregion "payment specific members"

	#region "shared pages options"

	/**
	* URL where shopper is directed on success
	* @var string max. 512 characters
	*/
	public $redirectURL;

	/**
	* URL where shopper is directed on failure
	* @var string max. 512 characters
	*/
	public $cancelUrl;

	/**
	* logo URL if available
	* @var string max. 512 characters
	*/
	public $logoURL;

	/**
	* payment page header text
	* @var string max. 255 characters
	*/
	public $headerText;

	/**
	* payment page language code
	* @var string max. 5 characters
	*/
	public $languageCode;

	/**
	* prevent shopper from modifying customer information (off by default)
	* @var bool
	*/
	public $customerReadOnly = false;

	/**
	* name of preset theme if selected
	* @var string
	*/
	public $customView;

	/**
	* verify customer phone number with Beagle
	* @var bool
	*/
	public $verifyCustomerPhone = null;

	/**
	* verify customer email by Beagle
	* @var bool
	*/
	public $verifyCustomerEmail = null;

	#endregion // "shared pages options"

	#region "recurring payments api"

	/**
	* total amount of intial payment, in dollars and cents as a floating-point number (will be converted to just cents for transmission)
	* may be 0 (i.e. nothing upfront, only on recurring billings)
	* @var float
	*/
	public $amountInit;

	/**
	* total amount of recurring payment, in dollars and cents as a floating-point number (will be converted to just cents for transmission)
	* @var float
	*/
	public $amountRecur;

	/**
	* the date of the initial payment (e.g. today, when the customer signed up)
	* @var DateTime
	*/
	public $dateInit;

	/**
	* the date of the first recurring payment
	* @var DateTime
	*/
	public $dateStart;

	/**
	* the date of the last recurring payment
	* @var DateTime
	*/
	public $dateEnd;

	/**
	* size of the interval between recurring payments (be it days, months, years, etc.) in range 1-31
	* @var integer
	*/
	public $intervalSize;

	/**
	* type of interval (see interval type constants below)
	* @var integer
	*/
	public $intervalType;

	/**
	* customer's country name
	* @var string 50 characters
	*/
	public $countryName;

	#endregion // "recurring payments api"

	/**
	* create a token customer when the transaction is complete (Responsive Page)
	* @var bool
	*/
	public $saveCustomer;

	#endregion // "members"

	/**
	* populate members with defaults, and set account and environment information
	* @param string $apiKey eWAY API key
	* @param string $apiPassword eWAY API password
	* @param string $customerID eWAY Customer ID
	* @param boolean $useSandbox use eWAY sandbox
	*/
	public function __construct($apiKey, $apiPassword, $customerID, $useSandbox = true) {
		$this->apiKey			= $apiKey;
		$this->apiPassword		= $apiPassword;
		$this->customerID		= $customerID;
		$this->useSandbox		= $useSandbox;
		$this->capture			= true;
		$this->httpUserAgent	= 'Gravity Forms eWAY Pro ' . GFEWAYPRO_PLUGIN_VERSION;
	}

	/**
	* process a customer creaton request against eWAY; throws exception on error with error described in exception message.
	* @throws GFEwayProException
	*/
	public function processCustomerCreate() {
		$errors = $this->validateCreditCard();

		if (!empty($errors)) {
			throw new GFEwayProException(implode("\n", $errors));
		}

		$request = $this->getCustomerCreate();
		$responseJSON = $this->apiPostRequest(self::API_DIRECT_PAYMENT, $request);

		$response = new GFEwayProResponseDirectPayment();
		$response->loadResponse($responseJSON);

		return $response;
	}

	/**
	* process a payment against eWAY; throws exception on error with error described in exception message.
	* @throws GFEwayProException
	*/
	public function processPayment() {
		$errors = $this->validateAmount();

		// if not a token payment, check for credit card details
		if (empty($this->customerToken)) {
			$errors = array_merge($errors, $this->validateCreditCard());
		}

		if (!empty($errors)) {
			throw new GFEwayProException(implode("\n", $errors));
		}

		$request = $this->getPaymentDirect();
		$responseJSON = $this->apiPostRequest(self::API_DIRECT_PAYMENT, $request);

		$response = new GFEwayProResponseDirectPayment();
		$response->loadResponse($responseJSON);

		return $response;
	}

	/**
	* process a recurring payment against eWAY; throws exception on error with error described in exception message.
	* @throws GFEwayProException
	*/
	public function processRecurringPayment() {
		$errors = $this->validateAmount();
		$errors = array_merge($errors, $this->validateCreditCard());

		if (!empty($errors)) {
			throw new GFEwayProException(implode("\n", $errors));
		}

		$request = $this->getRecurringPayment();

		$url = $this->useSandbox ? self::XML_RECURRING_SANDBOX : self::XML_RECURRING_LIVE;
		$responseXML = $this->xmlPostRequest($url, $request);

		$response = new GFEwayProResponseRecurringXML();
		$response->loadResponse($responseXML);

		return $response;
	}

	/**
	* request a Shared Page payment URL from eWAY; throws exception on error with error described in exception message.
	* @throws GFEwayProException
	*/
	public function requestSharedPage() {
		if (empty($this->amount) && $this->saveCustomer) {
			$errors = array();
		}
		else {
			$errors = $this->validateAmount();
		}

		if (!empty($errors)) {
			throw new GFEwayProException(implode("\n", $errors));
		}

		$request = $this->getPaymentSharedPage();
		$responseJSON = $this->apiPostRequest(self::API_SHARED_ACCESS, $request);

		$response = new GFEwayProResponseSharedPage();
		$response->loadResponse($responseJSON);

		return $response;
	}

	/**
	* request transaction details from eWAY by transaction ID or access code
	* @param string $idOrAccessCode
	* @throws GFEwayProException
	*/
	public function queryTransaction($idOrAccessCode) {
		$responseJSON = $this->apiGetRequest(self::API_TRANSACTION_QUERY, $idOrAccessCode);

		$response = new GFEwayProResponseQuery();
		$response->loadResponse($responseJSON);

		return $response;
	}

	/**
	* request customer token details from eWAY by token ID
	* @param string $customerToken
	* @throws GFEwayProException
	*/
	public function queryCustomer($customerToken) {
		$responseJSON = $this->apiGetRequest(self::API_CUSTOMER_QUERY, $customerToken);

		$response = new GFEwayProResponseCustomerQuery();
		$response->loadResponse($responseJSON);

		return $response;
	}

	/**
	* validate the amount for processing
	* @return array list of errors in validation
	*/
	protected function validateAmount() {
		$errors = array();

		if (!is_numeric($this->amount) || $this->amount <= 0) {
			$errors[] = __('Amount must be given as a number in dollars and cents', 'gravityforms-eway-pro');
		}
		else if (!is_float($this->amount)) {
			$this->amount = (float) $this->amount;
		}

		return $errors;
	}

	/**
	* validate the credit card members
	* @return array list of errors in validation
	*/
	protected function validateCreditCard() {
		$errors = array();

		if (strlen($this->cardHoldersName) === 0) {
			$errors[] = __('Cardholder name cannot be empty', 'gravityforms-eway-pro');
		}
		if (strlen($this->cardNumber) === 0) {
			$errors[] = __('Card number cannot be empty', 'gravityforms-eway-pro');
		}

		// make sure that card expiry month is a number from 1 to 12
		if (!is_int($this->cardExpiryMonth)) {
			if (strlen($this->cardExpiryMonth) === 0) {
				$errors[] = __('Card expiry month cannot be empty', 'gravityforms-eway-pro');
			}
			elseif (!ctype_digit($this->cardExpiryMonth)) {
				$errors[] = __('Card expiry month must be a number between 1 and 12', 'gravityforms-eway-pro');
			}
			else {
				$this->cardExpiryMonth = intval($this->cardExpiryMonth);
			}
		}
		if (is_int($this->cardExpiryMonth)) {
			if ($this->cardExpiryMonth < 1 || $this->cardExpiryMonth > 12) {
				$errors[] = __('Card expiry month must be a number between 1 and 12', 'gravityforms-eway-pro');
			}
		}

		// make sure that card expiry year is a 2-digit or 4-digit year >= this year
		if (!is_int($this->cardExpiryYear)) {
			if (strlen($this->cardExpiryYear) === 0) {
				$errors[] = __('Card expiry year cannot be empty', 'gravityforms-eway-pro');
			}
			elseif (!ctype_digit($this->cardExpiryYear)) {
				$errors[] = __('Card expiry year must be a two or four digit year', 'gravityforms-eway-pro');
			}
			else {
				$this->cardExpiryYear = intval($this->cardExpiryYear);
			}
		}
		if (is_int($this->cardExpiryYear)) {
			$thisYear = intval(date_create()->format('Y'));
			if ($this->cardExpiryYear < 0 || $this->cardExpiryYear >= 100 && $this->cardExpiryYear < 2000 || $this->cardExpiryYear > $thisYear + 20) {
				$errors[] = __('Card expiry year must be a two or four digit year', 'gravityforms-eway-pro');
			}
			else {
				if ($this->cardExpiryYear > 100 && $this->cardExpiryYear < $thisYear) {
					$errors[] = __("Card expiry can't be in the past", 'gravityforms-eway-pro');
				}
				else if ($this->cardExpiryYear < 100 && $this->cardExpiryYear < ($thisYear - 2000)) {
					$errors[] = __("Card expiry can't be in the past", 'gravityforms-eway-pro');
				}
			}
		}

		return $errors;
	}

	/**
	* create JSON request document for customer creation (token payments)
	* @return string
	*/
	public function getCustomerCreate() {
		$request = new stdClass();

		$request->Customer				= $this->getCustomerRecord(true);

		$request->Payment				= new stdClass();
		$request->Payment->TotalAmount	= 0;

		$request->Method				= self::METHOD_TOKEN_CREATE;
		$request->TransactionType		= self::TRANS_PURCHASE;
		$request->PartnerID				= self::PARTNER_ID;
		$request->CustomerIP			= self::getCustomerIP();

		return wp_json_encode($request);
	}

	/**
	* create JSON request document for direct payment
	* @return string
	*/
	public function getPaymentDirect() {
		$request = new stdClass();

		$request->Customer				= $this->getCustomerRecord(true);
		$request->Payment				= $this->getPaymentRecord();
		$request->TransactionType		= self::TRANS_PURCHASE;
		$request->PartnerID				= self::PARTNER_ID;
		$request->CustomerIP			= self::getCustomerIP();

		if (!$this->capture) {
			// just authorise the transaction;
			// if customer token is present, transaction will be a token customer PreAuth transaction
			$request->Method = self::METHOD_AUTHORISE;
		}
		elseif (!empty($this->customerToken)) {
			// capture transaction for token payment
			$request->Method = self::METHOD_TOKEN_PAYMENT;
		}
		else {
			// capture transaction for non-token payment
			$request->Method = self::METHOD_PAYMENT;
		}

		if ($this->hasShipping) {
			$request->ShippingAddress	= $this->getShippingAddressRecord();
		}

		if (!empty($this->options)) {
			$request->Options			= $this->getOptionsRecord();
		}

		if (!empty($this->deviceID)) {
			$request->DeviceID 			= substr($this->deviceID, 0, 50);
		}

		return wp_json_encode($request);
	}

	/**
	* create JSON request document for shared page payment
	* @return string
	*/
	public function getPaymentSharedPage() {
		$request = new stdClass();

		$request->Customer				= $this->getCustomerRecord(false);
		$request->Payment				= $this->getPaymentRecord();
		$request->TransactionType		= self::TRANS_PURCHASE;
		$request->PartnerID				= self::PARTNER_ID;
		$request->CustomerIP			= self::getCustomerIP();
		$request->RedirectUrl			= substr($this->redirectURL, 0, 512);
		$request->CancelUrl				= substr($this->cancelUrl, 0, 512);
		$request->Language				= $this->getLanguageCode();
		$request->CustomerReadOnly		= $this->customerReadOnly;

		if ($this->saveCustomer) {
			if (empty($this->amount)) {
				// just create the customer, no transaction
				$request->Method = self::METHOD_TOKEN_CREATE;
			}
			else {
				// create the customer and process a payment transaction
				// TODO: can we Authorise and Token Payments and create customer and the Responsive Shared Page? if not, need to change feed config conditions
				$request->Method = self::METHOD_TOKEN_PAYMENT;
			}
		}
		elseif (!$this->capture) {
			// just authorise the transaction;
			// if customer token is present, transaction will be a token customer PreAuth transaction
			$request->Method = self::METHOD_AUTHORISE;
		}
		elseif (!empty($this->customerToken)) {
			// capture transaction for token payment
			$request->Method = self::METHOD_TOKEN_PAYMENT;
		}
		else {
			// capture transaction for non-token payment
			$request->Method = self::METHOD_PAYMENT;
		}

		if ($this->hasShipping) {
			$request->ShippingAddress	= $this->getShippingAddressRecord();
		}

		if (!empty($this->options)) {
			$request->Options			= $this->getOptionsRecord();
		}

		if (!empty($this->deviceID)) {
			$request->DeviceID 			= substr($this->deviceID, 0, 50);
		}

		if (!empty($this->logoURL)) {
			$request->LogoURL 			= substr($this->logoURL, 0, 512);
		}

		if (!empty($this->headerText)) {
			$request->HeaderText		= substr($this->headerText, 0, 255);
		}

		if ($this->customView) {
			$request->CustomView		= $this->customView;
		}
		if (!is_null($this->verifyCustomerPhone)) {
			$request->VerifyCustomerPhone = (bool) $this->verifyCustomerPhone;
		}
		if (!is_null($this->verifyCustomerEmail)) {
			$request->VerifyCustomerEmail = (bool) $this->verifyCustomerEmail;
		}

		return wp_json_encode($request);
	}

	/**
	* create XML request document for recurring payment
	* @return string
	*/
	public function getRecurringPayment() {
		// aggregate street address1 & address2 into one string
		$parts = array($this->address1, $this->address2);
		$address = implode(', ', array_filter($parts, 'strlen'));

		$xml = new XMLWriter();
		$xml->openMemory();
		$xml->startDocument('1.0', 'UTF-8');
		$xml->startElement('RebillUpload');
		$xml->startElement('NewRebill');
		$xml->writeElement('eWayCustomerID', $this->customerID);

		// customer data
		$xml->startElement('Customer');
		$xml->writeElement('CustomerRef',			'');	// TODO: customer reference?
		$xml->writeElement('CustomerTitle',			$this->title ? substr($this->title, 0, 20) : '');
		$xml->writeElement('CustomerFirstName',		$this->firstName ? substr($this->firstName, 0, 50) : '');
		$xml->writeElement('CustomerLastName',		$this->lastName ? substr($this->lastName, 0, 50) : '');
		$xml->writeElement('CustomerCompany',		$this->companyName ? substr($this->companyName, 0, 100) : '');
		$xml->writeElement('CustomerJobDesc',		$this->jobDescription ? substr($this->jobDescription, 0, 50) : '');
		$xml->writeElement('CustomerEmail',			$this->emailAddress ? substr($this->emailAddress, 0, 50) : '');
		$xml->writeElement('CustomerAddress',		substr($address, 0, 255));
		$xml->writeElement('CustomerSuburb',		$this->suburb ? substr($this->suburb, 0, 50) : '');
		$xml->writeElement('CustomerState',			$this->state ? substr($this->state, 0, 50) : '');
		$xml->writeElement('CustomerPostCode',		$this->postcode ? substr($this->postcode, 0, 6) : '');
		$xml->writeElement('CustomerCountry',		$this->countryName ? substr($this->countryName, 0, 50) : '');
		$xml->writeElement('CustomerPhone1',		$this->phone ? substr(self::legacyCleanPhone($this->phone), 0, 20) : '');
		$xml->writeElement('CustomerPhone2',		$this->mobile ? substr(self::legacyCleanPhone($this->mobile), 0, 20) : '');
		$xml->writeElement('CustomerFax',			$this->fax ? substr(self::legacyCleanPhone($this->fax), 0, 20) : '');
		$xml->writeElement('CustomerURL',			$this->website ? substr($this->website, 0, 255) : '');
		$xml->writeElement('CustomerComments',		$this->comments ? substr($this->comments, 0, 255) : '');
		$xml->endElement();		// Customer

		// billing data
		$xml->startElement('RebillEvent');
		$xml->writeElement('RebillInvRef',			$this->invoiceReference);
		$xml->writeElement('RebillInvDesc',			$this->invoiceDescription);
		$xml->writeElement('RebillCCName',			$this->cardHoldersName);
		$xml->writeElement('RebillCCNumber',		$this->cardNumber);
		$xml->writeElement('RebillCCExpMonth',		sprintf('%02d', $this->cardExpiryMonth));
		$xml->writeElement('RebillCCExpYear',		sprintf('%02d', $this->cardExpiryYear % 100));
		$xml->writeElement('RebillInitAmt',			number_format($this->amountInit * 100, 0, '', ''));
		$xml->writeElement('RebillInitDate',		$this->dateInit->format('d/m/Y'));
		$xml->writeElement('RebillRecurAmt',		number_format($this->amountRecur * 100, 0, '', ''));
		$xml->writeElement('RebillStartDate',		$this->dateStart->format('d/m/Y'));
		$xml->writeElement('RebillInterval',		$this->intervalSize);
		$xml->writeElement('RebillIntervalType',	$this->intervalType);
		$xml->writeElement('RebillEndDate',			$this->dateEnd->format('d/m/Y'));
		$xml->endElement();		// RebillEvent

		$xml->endElement();		// NewRebill
		$xml->endElement();		// RebillUpload

		return $xml->outputMemory();
	}

	/**
	* clean phone number field value for legacy XML API
	* @param string $phone
	* @return string
	*/
	protected static function legacyCleanPhone($phone) {
		return preg_replace('#[^0-9 +-]#', '', $phone);
	}

	/**
	* build Customer record for request
	* @param bool $withCardDetails
	* @return stdClass
	*/
	protected function getCustomerRecord($withCardDetails) {
		$record = new stdClass;

		if (!empty($this->customerToken)) {
			$record->TokenCustomerID	= substr($this->customerToken, 0, 16);
		}

		//~ $record->Reference		= '';		// TODO: customer reference?
		$record->Title				= $this->title				? substr($this->title, 0, 5) : '';
		$record->FirstName			= $this->firstName			? substr($this->firstName, 0, 50) : '';
		$record->LastName			= $this->lastName			? substr($this->lastName, 0, 50) : '';
		$record->Street1			= $this->address1			? substr($this->address1, 0, 50) : '';
		$record->Street2			= $this->address2			? substr($this->address2, 0, 50) : '';
		$record->City				= $this->suburb				? substr($this->suburb, 0, 50) : '';
		$record->State				= $this->state				? substr($this->state, 0, 50) : '';
		$record->PostalCode			= $this->postcode			? substr($this->postcode, 0, 30) : '';
		$record->Country			= $this->country			? strtolower($this->country) : '';
		$record->Email				= $this->emailAddress		? substr($this->emailAddress, 0, 50) : '';

		if ($withCardDetails) {
			$record->CardDetails	= $this->getCardDetailsRecord();
		}

		if (!empty($this->companyName)) {
			$record->CompanyName	= substr($this->companyName, 0, 50);
		}

		if (!empty($this->jobDescription)) {
			$record->JobDescription	= substr($this->jobDescription, 0, 50);
		}

		if (!empty($this->phone)) {
			$record->Phone			= substr($this->phone, 0, 32);
		}

		if (!empty($this->mobile)) {
			$record->Mobile			= substr($this->mobile, 0, 32);
		}

		if (!empty($this->fax)) {
			$record->Fax			= substr($this->fax, 0, 32);
		}

		if (!empty($this->website)) {
			$record->Url			= substr($this->website, 0, 512);
		}

		if (!empty($this->comments)) {
			$record->Comments		= substr($this->comments, 0, 255);
		}

		return $record;
	}

	/**
	* build ShippindAddress record for request
	* @return stdClass
	*/
	protected function getShippingAddressRecord() {
		$record = new stdClass;

		if ($this->shipMethod) {
			$record->ShippingMethod	= $this->shipMethod;
		}
		if ($this->shipFirstName) {
			$record->FirstName		= substr($this->shipFirstName, 0, 50);
		}
		if ($this->shipLastName) {
			$record->LastName		= substr($this->shipLastName, 0, 50);
		}
		if ($this->shipEmailAddress) {
			$record->Email			= substr($this->shipEmailAddress, 0, 50);
		}
		if ($this->shipPhone) {
			$record->Phone			= substr($this->shipPhone, 0, 32);
		}
		if ($this->shipFax) {
			$record->Fax			= substr($this->shipFax, 0, 32);
		}

		$record->Street1			= $this->shipAddress1		? substr($this->shipAddress1, 0, 50) : '';
		$record->Street2			= $this->shipAddress2		? substr($this->shipAddress2, 0, 50) : '';
		$record->City				= $this->shipSuburb			? substr($this->shipSuburb, 0, 50) : '';
		$record->State				= $this->shipState			? substr($this->shipState, 0, 50) : '';
		$record->PostalCode			= $this->shipPostcode		? substr($this->shipPostcode, 0, 30) : '';
		$record->Country			= $this->shipCountry		? strtolower($this->shipCountry) : '';

		return $record;
	}

	/**
	* build CardDetails record for request
	* NB: TODO: does not currently handle StartMonth, StartYear, IssueNumber (used in UK)
	* NB: card number and CVN can be very lengthy encrypted values
	* @return stdClass
	*/
	protected function getCardDetailsRecord() {
		$record = new stdClass;

		if (!empty($this->cardHoldersName)) {
			$record->Name				= substr($this->cardHoldersName, 0, 50);
		}

		if (!empty($this->cardNumber)) {
			$record->Number				= $this->cardNumber;
		}

		if (!empty($this->cardExpiryMonth)) {
			$record->ExpiryMonth		= sprintf('%02d', $this->cardExpiryMonth);
		}

		if (!empty($this->cardExpiryYear)) {
			$record->ExpiryYear			= sprintf('%02d', $this->cardExpiryYear % 100);
		}

		if (!empty($this->cardVerificationNumber)) {
			$record->CVN				= $this->cardVerificationNumber;
		}

		return $record;
	}

	/**
	* build Payment record for request
	* @return stdClass
	*/
	protected function getPaymentRecord() {
		$record = new stdClass;

		if ($this->amount > 0) {
			$record->TotalAmount		= number_format($this->amount * 100, 0, '', '');
			$record->InvoiceReference	= $this->transactionNumber	? substr($this->transactionNumber, 0, 50) : '';
			$record->InvoiceDescription	= $this->invoiceDescription	? substr($this->invoiceDescription, 0, 64) : '';
			$record->InvoiceNumber		= $this->invoiceReference	? substr($this->invoiceReference, 0, 12) : '';
			$record->CurrencyCode		= $this->currencyCode		? substr($this->currencyCode, 0, 3) : '';
		}
		else {
			$record->TotalAmount		= 0;
		}

		return $record;
	}

	/**
	* build Options record for request
	* @return array
	*/
	protected function getOptionsRecord() {
		$options = array();

		foreach ($this->options as $option) {
			if (!empty($option)) {
				$options[] = array('Value' => substr($option, 0, 254));
			}
		}

		return $options;
	}

	/**
	* get gateway language code from WP language code
	* @return string
	*/
	protected function getLanguageCode() {
		if (empty($this->languageCode)) {
			return 'EN';
		}

		return strtoupper(substr($this->languageCode, 0, 2));
	}

	/**
	* generalise an API post request
	* @param string $endpoint
	* @param string $request
	* @return string JSON response
	* @throws GFEwayProException
	*/
	protected function apiPostRequest($endpoint, $request) {
		// select host and endpoint
		$host = $this->useSandbox ? self::API_HOST_SANDBOX : self::API_HOST_LIVE;
		$url = "$host/$endpoint";

		// execute the request, and retrieve the response
		$response = wp_remote_post($url, array(
			'user-agent'	=> $this->httpUserAgent,
			'sslverify'		=> $this->sslVerifyPeer,
			'timeout'		=> 60,
			'headers'		=> array(
									'Content-Type'		=> 'application/json',
									'Authorization'		=> $this->getBasicAuthentication(),
							   ),
			'body'			=> $request,
		));

		// check for http error
		$this->checkHttpResponse($response);

		return wp_remote_retrieve_body($response);
	}

	/**
	* generalise an API get request
	* @param string $endpoint
	* @param string $request
	* @return string JSON response
	* @throws GFEwayProException
	*/
	protected function apiGetRequest($endpoint, $request) {
		// select host and endpoint
		$host = $this->useSandbox ? self::API_HOST_SANDBOX : self::API_HOST_LIVE;
		$url = sprintf('%s/%s/%s', $host, urlencode($endpoint), urlencode($request));

		// execute the request, and retrieve the response
		$response = wp_remote_get($url, array(
			'user-agent'	=> $this->httpUserAgent,
			'sslverify'		=> $this->sslVerifyPeer,
			'timeout'		=> 60,
			'headers'		=> array(
									'Content-Type'		=> 'application/json',
									'Authorization'		=> $this->getBasicAuthentication(),
							   ),
		));

		// check for http error
		$this->checkHttpResponse($response);

		return wp_remote_retrieve_body($response);
	}

	/**
	* generalise an XML post request
	* @param string $url
	* @param string $request
	* @return string JSON response
	* @throws GFEwayProException
	*/
	protected function xmlPostRequest($url, $request) {

		// execute the request, and retrieve the response
		$response = wp_remote_post($url, array(
			'user-agent'	=> $this->httpUserAgent,
			'sslverify'		=> $this->sslVerifyPeer,
			'timeout'		=> 60,
			'headers'		=> array(
									'Content-Type'		=> 'text/xml; charset=utf-8',
							   ),
			'body'			=> $request,
		));

		// check for http error
		$this->checkHttpResponse($response);

		return wp_remote_retrieve_body($response);
	}

	/**
	* get encoded authorisation information for request
	* @return string
	*/
	protected function getBasicAuthentication() {
		return 'Basic ' . base64_encode("{$this->apiKey}:{$this->apiPassword}");
	}

	/**
	* check http get/post response, throw exception if an error occurred
	* @param array $response
	* @throws GFEwayProException
	*/
	protected function checkHttpResponse($response) {
		// failure to handle the http request
		if (is_wp_error($response)) {
			$msg = $response->get_error_message();
			throw new GFEwayProException(sprintf(__('Error posting eWAY request: %s', 'gravityforms-eway-pro'), $msg));
		}

		// error code returned by request
		$code = wp_remote_retrieve_response_code($response);
		if ($code !== 200) {
			$msg = wp_remote_retrieve_response_message($response);

			if (empty($msg)) {
				$msg = sprintf(__('Error posting eWAY request: %s', 'gravityforms-eway-pro'), $code);
			}
			else {
				/* translators: 1. the error code; 2. the error message */
				$msg = sprintf(__('Error posting eWAY request: %1$s, %2$s', 'gravityforms-eway-pro'), $code, $msg);
			}
			throw new GFEwayProException($msg);
		}
	}

	/**
	* get the customer's IP address dynamically from server variables
	* @return string
	*/
	protected function getCustomerIP() {
		// if test mode and running on localhost, then kludge to an Aussie IP address
		if (isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] === '127.0.0.1' && $this->useSandbox) {
			$ip = '210.1.199.10';
		}

		// check for remote address, ignore all other headers as they can be spoofed easily
		elseif (isset($_SERVER['REMOTE_ADDR']) && self::isIpAddress($_SERVER['REMOTE_ADDR'])) {
			$ip = $_SERVER['REMOTE_ADDR'];
		}

		// allow hookers to override for network-specific fixes
		$ip = apply_filters('gfeway_customer_ip', $ip);

		return $ip;
	}

	/**
	* check whether a given string is an IP address
	* @param string $maybeIP
	* @return bool
	*/
	protected static function isIpAddress($maybeIP) {
		if (function_exists('inet_pton')) {
			// check for IPv4 and IPv6 addresses
			return !!inet_pton($maybeIP);
		}

		// just check for IPv4 addresses
		return !!ip2long($maybeIP);
	}

}
