<?php
class ClickSendBalance {
	var $country = NULL;
	/**
	 * @return the $country
	 */
	public function getCountry() {
		return $this->country;
	}

	/**
	 * @param NULL $country
	 */
	public function setCountry($country) {
		$this->country = $country;
	}
}


class ClickSendDlr {
	var $messageid = NULL;
	/**
	 * @return the $messageid
	 */
	public function getMessageid() {
		return $this->messageid;
	}

	/**
	 * @param NULL $messageid
	 */
	public function setMessageid($messageid) {
		$this->messageid = $messageid;
	}
}


class ClickSendSMS {
	var $to = NULL;
	var $message = NULL;
	var $senderid = NULL;
	var $schedule = NULL;
	var $customstring = NULL;
	var $return = NULL;

	/**
	 * @return the $to
	 */
	public function getTo() {
		return $this->to;
	}

	/**
	 * @return the $message
	 */
	public function getMessage() {
		return $this->message;
	}

	/**
	 * @return the $senderid
	 */
	public function getSenderid() {
		return $this->senderid;
	}

	/**
	 * @return the $schedule
	 */
	public function getSchedule() {
		return $this->schedule;
	}

	/**
	 * @return the $customstring
	 */
	public function getCustomstring() {
		return $this->customstring;
	}

	/**
	 * @return the $return
	 */
	public function getReturn() {
		return $this->return;
	}

	/**
	 * @param NULL $message
	 */
	public function setMessage($message) {
		$this->message = $message;
	}

	/**
	 * @param NULL $senderid
	 */
	public function setSenderid($senderid) {
		$this->senderid = $senderid;
	}

	/**
	 * @param NULL $schedule
	 */
	public function setSchedule($schedule) {
		$this->schedule = $schedule;
	}

	/**
	 * @param NULL $customstring
	 */
	public function setCustomstring($customstring) {
		$this->customstring = $customstring;
	}

	/**
	 * @param NULL $return
	 */
	public function setReturn($return) {
		$this->return = $return;
	}

	public function setTo($to){
		$this->to = $to;
	}

}

class ClickSend {
	// clicksend account credentials
	private $api_key = '';
	private $username = '';
	private $end_point = '';
	private $is_ssl = '';


	/**
	 * @param $clk_key Your ClickSend Api Key
	 * @param $clk_username Your ClickSend Username
	 */
	
	function ClickSend ($username,$api_key,$is_ssl=TRUE) {
		$this->username		= $username;
		$this->api_key		= $api_key;
		$this->is_ssl		= $is_ssl;
	}


	function send_sms($params){
		$params = (array) $params;
		$response = $this->send_request('send_sms', $params);
		return $response;
	}

	function get_dlr($params){
		$params = (array) $params;
		$response = $this->send_request('get_dlr', $params);
		return $response;
	}

	function get_balance($params){
		$params = (array) $params;
		$response = $this->send_request('get_balance', $params);
		return $response;
	}
	
	function get_reply(){
		$response = $this->send_request('get_reply');
		return $response;
	}
	
	function set_end_point($url){
		$this->end_point = $url;
	}

	function set_is_ssl($is_ssl=TRUE){
		$this->is_ssl = $is_ssl;
	}

	function check_request_params($request_type,$params){
		$request_params = $this->REQUEST_PARAMETERS[$request_type];
		foreach($request_params AS $k=>$v){
			if($v=='r' && !isset($params[$k])){
				return Array('status'=>FALSE, 'errordescription' => $k . ' is a required parameter for ' . $request_type . ' request');
			}
		}
		return Array('status'=>TRUE, 'errordescription' => '');
	}

	/**
	 * Prepare and send a new message.
	 */
	private function send_request ($request_type, $data=Array() ) {
		$check_request_params = $this->check_request_params($request_type, $data);	
		if(!$check_request_params['status']){
			return $check_request_params['errordescription'];
		}

		//setting API endpoint according to request type and ssl		
		if($this->is_ssl){
			$this->set_end_point($this->REQUEST_URLS[$request_type]['secure']);
		}else{
			$this->set_end_point($this->REQUEST_URLS[$request_type]['unsecure']);
		}

		// Build the post data
		$data['method'] = $this->API_TYPE;
		$post = '';
		foreach($data as $k => $v){ $post.= '&'.$k.'='.$v; }
		// If available, use CURL
		if (function_exists('curl_version')) {
			$ch = curl_init( $this->end_point );
			curl_setopt( $ch, CURLOPT_POST, TRUE );
			curl_setopt( $ch, CURLOPT_USERPWD,  $this->username.':'.$this->api_key);
			curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, FALSE);
			curl_setopt( $ch, CURLOPT_RETURNTRANSFER, TRUE );
			curl_setopt($ch,CURLOPT_USERAGENT,$this->SDK_USER_AGENT_STRING . ' '. $this->SDK_USER_AGENT_VERSION);
			curl_setopt( $ch, CURLOPT_POSTFIELDS, $post );
			$response = curl_exec( $ch );
			curl_close ( $ch );

		} elseif (ini_get('allow_url_fopen')) {
			// No CURL available so try file_get_contents
			$opts = array('http' =>
				array(
					'method'  =>	'POST',
					'header'  =>	"Content-type: application/x-www-form-urlencoded\r\n".
									"Authorization: Basic " . base64_encode($this->username.':'.$this->api_key)."\r\n".
									"User-Agent: ".$this->SDK_USER_AGENT_STRING . ' '. $this->SDK_USER_AGENT_VERSION."\r\n",
					'content' =>	$post
				)
			);
			$context = stream_context_create($opts);
			$response = file_get_contents($this->end_point, false, $context);

		} else {
			return false;
		}
		return $this->parse($request_type, $response );
	 
	}
	
	
	/**
	 * Recursively normalise any key names in an object, removing unwanted characters
	 */
	private function normalise_keys ($obj) {
		// Determine is working with a class or araay
		if ($obj instanceof stdClass) {
			$new_obj = new stdClass();
			$is_obj = true;
		} else {
			$new_obj = array();
			$is_obj = false;
		}
	
		foreach($obj as $key => $val){
			if ($val instanceof stdClass || is_array($val)) {
				$val = $this->normalise_keys($val);
			}

			if ($is_obj) {
				$new_obj->{str_replace('-', '', $key)} = $val;
			} else {
				$new_obj[str_replace('-', '', $key)] = $val;
			}
		}

		return $new_obj;
	}


	/**
	 * Parse server response.
	 */
	private function parse ($request_type, $data ) {
		$response = json_decode($data);
		$return_object = new stdClass();
		// Copy the response data into an object, removing any '-' characters senderid the key
		$response_obj = $this->normalise_keys($response);
		if($request_type=='send_sms'){
			for ($i = 0 ; $i<$response_obj->recipientcount; $i++ ){
				$response_obj->messages[$i]->errordescription	= $this->SEND_SMS_RESPONSE_CODES_ARRAY[$response_obj->messages[$i]->result];
			}
			$return_object = $response_obj;
		}else if($request_type=='get_dlr'){
			$response_obj->errordescription	= $this->GET_DLR_RESPONSE_CODES_ARRAY[$response_obj->result];
			$return_object = $response_obj;
		}else if($request_type=='get_balance'){
			$response_obj->errordescription	= $this->GET_BALANCE_RESPONSE_CODES_ARRAY[$response_obj->result];
			$return_object = $response_obj;
		}else if($request_type=='get_reply'){
			$response_obj->errordescription	= $this->GET_REPLY_RESPONSE_CODES_ARRAY[$response_obj->result];
			$return_object = $response_obj;
		}
		return $return_object;		
	}

	var $REQUEST_PARAMETERS = Array(
			'send_sms' => Array(
				'to'			=> 'r',
				'message'		=> 'r',
				'senderid'		=> 'o',
				'schedule'		=> 'o',
				'customstring'	=> 'o',
				'return'		=> 'o',
			),
			'get_dlr' => Array('messageid' => 'o'),
			'get_balance' => Array('country' => 'o'),
			'get_reply' => Array(),
	);



	 /**
     * SDK Useragent String
     */
	var $SDK_USER_AGENT_STRING = "ClickSend PHP SDK";
	
	 /**
     * SDK User agent version, actually SDK version
     */	
	var $SDK_USER_AGENT_VERSION = "1.0";
	
	 /**
     * Maximum recipient count for sms sending
     */
	var $MAXIMUM_RECIPIENT_COUNT = 1000;
		
	var $REQUEST_URLS = Array(
									'send_sms' => Array(
															'secure' => 'https://api.clicksend.com/rest/v2/send.json',
															'unsecure' => 'http://api.clicksend.com/rest/v2/send.json'
														),
									'get_balance' => Array(
															'secure' => 'https://api.clicksend.com/rest/v2/balance.json',
															'unsecure' => 'http://api.clicksend.com/rest/v2/balance.json'
														),
									'get_dlr' => Array(
															'secure' => 'https://api.clicksend.com/rest/v2/delivery.json',
															'unsecure' => 'http://api.clicksend.com/rest/v2/delivery.json'
														),
									'get_reply' => Array(
															'secure' => 'https://api.clicksend.com/rest/v2/reply.json',
															'unsecure' => 'http://api.clicksend.com/rest/v2/reply.json'
														)
		);
    
	/**
     * For "method" parameter<br>
     * Always rest
     */
    var $API_TYPE = "rest";
    
    
	/**
     * For successfully response status code<br>
     * 0000
     */ 
	var $STATUS_OK = "0000";
	
	/**
     * Send sms response status codes<br>
     */  
    var $SEND_SMS_RESPONSE_CODES_ARRAY = Array(
    	'0000' => 'Message added to queue OK.',
	    '2006' => 'Not enough information has been supplied for authentication. Please ensure that your Username and Unique Key are supplied in your request.',
	    '2007' => 'Your account has not been activated.',
	    '2015' => 'The destination mobile number is invalid.',
	    '2016' => 'Identical message already sent to this recipient. Please try again in a few seconds.',
	    '2017' => 'Invalid Sender ID. Please ensure Sender ID is no longer than 11 characters (if alphanumeric) => and contains no spaces.',
	    '2018' => 'You have reached the end of your message credits. You will need to purchase more message credits.',
	    '2022' => 'Your Username or Unique Key is incorrect.',
	    '2051' => 'Message is empty.',
	    '2052' => 'Too many recipients.',
	    '2100' => 'Internal error.',
	    '2101' => 'Internal error.',
	    '2102' => 'Internal error.',
	    '2103' => 'Internal error.',
	    '2104' => 'Internal error.',
	    '2105' => 'Internal error.',
	    '2106' => 'Internal error.',
	    '2107' => 'Internal error.',
	    '2108' => 'Internal error.',
	    '2109' => 'Internal error.',
	    '2110' => 'Internal error.',
	    '2111' => 'Internal error.',
	    '2112' => 'Internal error.',
	    '2113' => 'Internal error.',
	    '2114' => 'Internal error.',
	    '2115' => 'Internal error.',
	    '2116' => 'Internal error.',
	    '2117' => 'Internal error.',
	    '2118' => 'Internal error.',
	    '2119' => 'Internal error.',
	    '2120' => 'Internal error.',
	    '2121' => 'Internal error.',
	    '2122' => 'Internal error.',
	    '2123' => 'Internal error.',
	    '2124' => 'Internal error.',
	    '2125' => 'Internal error.',
	    '2126' => 'Internal error.',
	    '2127' => 'Internal error.',
	    '2128' => 'Internal error.',
	    '2129' => 'Internal error.',
	    '2130' => 'Internal error.',
	    '2131' => 'Internal error.',
	    '2132' => 'Internal error.',
	    '2133' => 'Internal error.',
	    '2134' => 'Internal error.',
	    '2135' => 'Internal error.',
	    '2136' => 'Internal error.',
	    '2137' => 'Internal error.',
	    '2138' => 'Internal error.',
	    '2139' => 'Internal error.',
	    '2140' => 'Internal error.',
	    '2141' => 'Internal error.',
	    '2142' => 'Internal error.',
	    '2143' => 'Internal error.',
	    '2144' => 'Internal error.',
	    '2145' => 'Internal error.',
	    '2146' => 'Internal error.',
	    '2147' => 'Internal error.',
	    '2148' => 'Internal error.',
	    '2149' => 'Internal error.',
	    '2150' => 'Internal error.',
	    '2151' => 'Internal error.',
	    '2152' => 'Internal error.',
	    '2153' => 'Internal error.',
	    '2154' => 'Internal error.',
	    '2155' => 'Internal error.',
	    '2156' => 'Internal error.',
	    '2157' => 'Internal error.',
	    '2158' => 'Internal error.',
	    '2159' => 'Internal error.',
	    '2160' => 'Internal error.',
	    '2161' => 'Internal error.',
	    '2162' => 'Internal error.',
	    '2163' => 'Internal error.',
	    '2164' => 'Internal error.',
	    '2165' => 'Internal error.',
	    '2166' => 'Internal error.',
	    '2167' => 'Internal error.',
	    '2168' => 'Internal error.',
	    '2169' => 'Internal error.',
	    '2170' => 'Internal error.',
	    '2171' => 'Internal error.',
	    '2172' => 'Internal error.',
	    '2173' => 'Internal error.',
	    '2174' => 'Internal error.',
	    '2175' => 'Internal error.',
	    '2176' => 'Internal error.',
	    '2177' => 'Internal error.',
	    '2178' => 'Internal error.',
	    '2179' => 'Internal error.',
	    '2180' => 'Internal error.',
	    '2181' => 'Internal error.',
	    '2182' => 'Internal error.',
	    '2183' => 'Internal error.',
	    '2184' => 'Internal error.',
	    '2185' => 'Internal error.',
	    '2186' => 'Internal error.',
	    '2187' => 'Internal error.',
	    '2188' => 'Internal error.',
	    '2189' => 'Internal error.',
	    '2190' => 'Internal error.',
	    '2191' => 'Internal error.',
	    '2192' => 'Internal error.',
	    '2193' => 'Internal error.',
	    '2194' => 'Internal error.',
	    '2195' => 'Internal error.',
	    '2196' => 'Internal error.',
	    '2197' => 'Internal error.',
	    '2198' => 'Internal error.',
	    '2199' => 'Internal error.',
    );
    
	/**
     * Gel balance response status codes<br>
     */  
    var $GET_BALANCE_RESPONSE_CODES_ARRAY = Array(
    	'0000' => 'Retrieved account balance OK.',
	    '2006' => 'Not enough information has been supplied for authentication. Please ensure that your Username and Unique Key are supplied in your request.',
	    '2007' => 'Your account has not been activated.',
	    '2022' => 'Your Username or Unique Key is incorrect.',
	    '2100' => 'Internal error.',
	    '2101' => 'Internal error.',
	    '2102' => 'Internal error.',
	    '2103' => 'Internal error.',
	    '2104' => 'Internal error.',
	    '2105' => 'Internal error.',
	    '2106' => 'Internal error.',
	    '2107' => 'Internal error.',
	    '2108' => 'Internal error.',
	    '2109' => 'Internal error.',
	    '2110' => 'Internal error.',
	    '2111' => 'Internal error.',
	    '2112' => 'Internal error.',
	    '2113' => 'Internal error.',
	    '2114' => 'Internal error.',
	    '2115' => 'Internal error.',
	    '2116' => 'Internal error.',
	    '2117' => 'Internal error.',
	    '2118' => 'Internal error.',
	    '2119' => 'Internal error.',
	    '2120' => 'Internal error.',
	    '2121' => 'Internal error.',
	    '2122' => 'Internal error.',
	    '2123' => 'Internal error.',
	    '2124' => 'Internal error.',
	    '2125' => 'Internal error.',
	    '2126' => 'Internal error.',
	    '2127' => 'Internal error.',
	    '2128' => 'Internal error.',
	    '2129' => 'Internal error.',
	    '2130' => 'Internal error.',
	    '2131' => 'Internal error.',
	    '2132' => 'Internal error.',
	    '2133' => 'Internal error.',
	    '2134' => 'Internal error.',
	    '2135' => 'Internal error.',
	    '2136' => 'Internal error.',
	    '2137' => 'Internal error.',
	    '2138' => 'Internal error.',
	    '2139' => 'Internal error.',
	    '2140' => 'Internal error.',
	    '2141' => 'Internal error.',
	    '2142' => 'Internal error.',
	    '2143' => 'Internal error.',
	    '2144' => 'Internal error.',
	    '2145' => 'Internal error.',
	    '2146' => 'Internal error.',
	    '2147' => 'Internal error.',
	    '2148' => 'Internal error.',
	    '2149' => 'Internal error.',
	    '2150' => 'Internal error.',
	    '2151' => 'Internal error.',
	    '2152' => 'Internal error.',
	    '2153' => 'Internal error.',
	    '2154' => 'Internal error.',
	    '2155' => 'Internal error.',
	    '2156' => 'Internal error.',
	    '2157' => 'Internal error.',
	    '2158' => 'Internal error.',
	    '2159' => 'Internal error.',
	    '2160' => 'Internal error.',
	    '2161' => 'Internal error.',
	    '2162' => 'Internal error.',
	    '2163' => 'Internal error.',
	    '2164' => 'Internal error.',
	    '2165' => 'Internal error.',
	    '2166' => 'Internal error.',
	    '2167' => 'Internal error.',
	    '2168' => 'Internal error.',
	    '2169' => 'Internal error.',
	    '2170' => 'Internal error.',
	    '2171' => 'Internal error.',
	    '2172' => 'Internal error.',
	    '2173' => 'Internal error.',
	    '2174' => 'Internal error.',
	    '2175' => 'Internal error.',
	    '2176' => 'Internal error.',
	    '2177' => 'Internal error.',
	    '2178' => 'Internal error.',
	    '2179' => 'Internal error.',
	    '2180' => 'Internal error.',
	    '2181' => 'Internal error.',
	    '2182' => 'Internal error.',
	    '2183' => 'Internal error.',
	    '2184' => 'Internal error.',
	    '2185' => 'Internal error.',
	    '2186' => 'Internal error.',
	    '2187' => 'Internal error.',
	    '2188' => 'Internal error.',
	    '2189' => 'Internal error.',
	    '2190' => 'Internal error.',
	    '2191' => 'Internal error.',
	    '2192' => 'Internal error.',
	    '2193' => 'Internal error.',
	    '2194' => 'Internal error.',
	    '2195' => 'Internal error.',
	    '2196' => 'Internal error.',
	    '2197' => 'Internal error.',
	    '2198' => 'Internal error.',
	    '2199' => 'Internal error.',
    );
    
    /**
     * Gel balance response status codes<br>
     */  
    var $GET_DLR_RESPONSE_CODES_ARRAY =Array(
    	'0000' => 'Checked the system for delivery reports OK. Note: This doesn’t mean the message was delivered successfully – it just means the API has checked for available reports successfully. Even if no delivery reports were available => it will still return 0000 Success. Check the dlrcount value to determine the number of delivery reports in the response.',
	    '2006' => 'Not enough information has been supplied for authentication. Please ensure that your Username and Unique Key are supplied in your request.',
	    '2007' => 'Your account has not been activated.',
	    '2022' => 'Your Username or Unique Key is incorrect.',
	    '2100' => 'Internal error.',
	    '2101' => 'Internal error.',
	    '2102' => 'Internal error.',
	    '2103' => 'Internal error.',
	    '2104' => 'Internal error.',
	    '2105' => 'Internal error.',
	    '2106' => 'Internal error.',
	    '2107' => 'Internal error.',
	    '2108' => 'Internal error.',
	    '2109' => 'Internal error.',
	    '2110' => 'Internal error.',
	    '2111' => 'Internal error.',
	    '2112' => 'Internal error.',
	    '2113' => 'Internal error.',
	    '2114' => 'Internal error.',
	    '2115' => 'Internal error.',
	    '2116' => 'Internal error.',
	    '2117' => 'Internal error.',
	    '2118' => 'Internal error.',
	    '2119' => 'Internal error.',
	    '2120' => 'Internal error.',
	    '2121' => 'Internal error.',
	    '2122' => 'Internal error.',
	    '2123' => 'Internal error.',
	    '2124' => 'Internal error.',
	    '2125' => 'Internal error.',
	    '2126' => 'Internal error.',
	    '2127' => 'Internal error.',
	    '2128' => 'Internal error.',
	    '2129' => 'Internal error.',
	    '2130' => 'Internal error.',
	    '2131' => 'Internal error.',
	    '2132' => 'Internal error.',
	    '2133' => 'Internal error.',
	    '2134' => 'Internal error.',
	    '2135' => 'Internal error.',
	    '2136' => 'Internal error.',
	    '2137' => 'Internal error.',
	    '2138' => 'Internal error.',
	    '2139' => 'Internal error.',
	    '2140' => 'Internal error.',
	    '2141' => 'Internal error.',
	    '2142' => 'Internal error.',
	    '2143' => 'Internal error.',
	    '2144' => 'Internal error.',
	    '2145' => 'Internal error.',
	    '2146' => 'Internal error.',
	    '2147' => 'Internal error.',
	    '2148' => 'Internal error.',
	    '2149' => 'Internal error.',
	    '2150' => 'Internal error.',
	    '2151' => 'Internal error.',
	    '2152' => 'Internal error.',
	    '2153' => 'Internal error.',
	    '2154' => 'Internal error.',
	    '2155' => 'Internal error.',
	    '2156' => 'Internal error.',
	    '2157' => 'Internal error.',
	    '2158' => 'Internal error.',
	    '2159' => 'Internal error.',
	    '2160' => 'Internal error.',
	    '2161' => 'Internal error.',
	    '2162' => 'Internal error.',
	    '2163' => 'Internal error.',
	    '2164' => 'Internal error.',
	    '2165' => 'Internal error.',
	    '2166' => 'Internal error.',
	    '2167' => 'Internal error.',
	    '2168' => 'Internal error.',
	    '2169' => 'Internal error.',
	    '2170' => 'Internal error.',
	    '2171' => 'Internal error.',
	    '2172' => 'Internal error.',
	    '2173' => 'Internal error.',
	    '2174' => 'Internal error.',
	    '2175' => 'Internal error.',
	    '2176' => 'Internal error.',
	    '2177' => 'Internal error.',
	    '2178' => 'Internal error.',
	    '2179' => 'Internal error.',
	    '2180' => 'Internal error.',
	    '2181' => 'Internal error.',
	    '2182' => 'Internal error.',
	    '2183' => 'Internal error.',
	    '2184' => 'Internal error.',
	    '2185' => 'Internal error.',
	    '2186' => 'Internal error.',
	    '2187' => 'Internal error.',
	    '2188' => 'Internal error.',
	    '2189' => 'Internal error.',
	    '2190' => 'Internal error.',
	    '2191' => 'Internal error.',
	    '2192' => 'Internal error.',
	    '2193' => 'Internal error.',
	    '2194' => 'Internal error.',
	    '2195' => 'Internal error.',
	    '2196' => 'Internal error.',
	    '2197' => 'Internal error.',
	    '2198' => 'Internal error.',
	    '2199' => 'Internal error.',
    );
    
    
    /**
     * Gel balance response status codes<br>
     */  
    var $GET_REPLY_RESPONSE_CODES_ARRAY = Array(
    	'0000' => 'Checked the system for replies OK. Note: This doesn’t mean the response contains replies – it just means the API has checked for available replies successfully. Even if no replies were available => it will still return 0000 Success. Check the replycount value to determine the number of replies in the response.',
	    '2006' => 'Not enough information has been supplied for authentication. Please ensure that your Username and Unique Key are supplied in your request.',
	    '2007' => 'Your account has not been activated.',
	    '2022' => 'Your Username or Unique Key is incorrect.',
	    '2100' => 'Internal error.',
	    '2101' => 'Internal error.',
	    '2102' => 'Internal error.',
	    '2103' => 'Internal error.',
	    '2104' => 'Internal error.',
	    '2105' => 'Internal error.',
	    '2106' => 'Internal error.',
	    '2107' => 'Internal error.',
	    '2108' => 'Internal error.',
	    '2109' => 'Internal error.',
	    '2110' => 'Internal error.',
	    '2111' => 'Internal error.',
	    '2112' => 'Internal error.',
	    '2113' => 'Internal error.',
	    '2114' => 'Internal error.',
	    '2115' => 'Internal error.',
	    '2116' => 'Internal error.',
	    '2117' => 'Internal error.',
	    '2118' => 'Internal error.',
	    '2119' => 'Internal error.',
	    '2120' => 'Internal error.',
	    '2121' => 'Internal error.',
	    '2122' => 'Internal error.',
	    '2123' => 'Internal error.',
	    '2124' => 'Internal error.',
	    '2125' => 'Internal error.',
	    '2126' => 'Internal error.',
	    '2127' => 'Internal error.',
	    '2128' => 'Internal error.',
	    '2129' => 'Internal error.',
	    '2130' => 'Internal error.',
	    '2131' => 'Internal error.',
	    '2132' => 'Internal error.',
	    '2133' => 'Internal error.',
	    '2134' => 'Internal error.',
	    '2135' => 'Internal error.',
	    '2136' => 'Internal error.',
	    '2137' => 'Internal error.',
	    '2138' => 'Internal error.',
	    '2139' => 'Internal error.',
	    '2140' => 'Internal error.',
	    '2141' => 'Internal error.',
	    '2142' => 'Internal error.',
	    '2143' => 'Internal error.',
	    '2144' => 'Internal error.',
	    '2145' => 'Internal error.',
	    '2146' => 'Internal error.',
	    '2147' => 'Internal error.',
	    '2148' => 'Internal error.',
	    '2149' => 'Internal error.',
	    '2150' => 'Internal error.',
	    '2151' => 'Internal error.',
	    '2152' => 'Internal error.',
	    '2153' => 'Internal error.',
	    '2154' => 'Internal error.',
	    '2155' => 'Internal error.',
	    '2156' => 'Internal error.',
	    '2157' => 'Internal error.',
	    '2158' => 'Internal error.',
	    '2159' => 'Internal error.',
	    '2160' => 'Internal error.',
	    '2161' => 'Internal error.',
	    '2162' => 'Internal error.',
	    '2163' => 'Internal error.',
	    '2164' => 'Internal error.',
	    '2165' => 'Internal error.',
	    '2166' => 'Internal error.',
	    '2167' => 'Internal error.',
	    '2168' => 'Internal error.',
	    '2169' => 'Internal error.',
	    '2170' => 'Internal error.',
	    '2171' => 'Internal error.',
	    '2172' => 'Internal error.',
	    '2173' => 'Internal error.',
	    '2174' => 'Internal error.',
	    '2175' => 'Internal error.',
	    '2176' => 'Internal error.',
	    '2177' => 'Internal error.',
	    '2178' => 'Internal error.',
	    '2179' => 'Internal error.',
	    '2180' => 'Internal error.',
	    '2181' => 'Internal error.',
	    '2182' => 'Internal error.',
	    '2183' => 'Internal error.',
	    '2184' => 'Internal error.',
	    '2185' => 'Internal error.',
	    '2186' => 'Internal error.',
	    '2187' => 'Internal error.',
	    '2188' => 'Internal error.',
	    '2189' => 'Internal error.',
	    '2190' => 'Internal error.',
	    '2191' => 'Internal error.',
	    '2192' => 'Internal error.',
	    '2193' => 'Internal error.',
	    '2194' => 'Internal error.',
	    '2195' => 'Internal error.',
	    '2196' => 'Internal error.',
	    '2197' => 'Internal error.',
	    '2198' => 'Internal error.',
	    '2199' => 'Internal error.',
    );
}