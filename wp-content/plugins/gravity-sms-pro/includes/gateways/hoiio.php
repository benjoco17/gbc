<?php
/*
 * Class Name : GFHANNANSMS_Pro_{strtoupper( php file name) }	
 */
class GFHANNANSMS_Pro_HOIIO {

	/*
	* Gateway title	
	*/
	public static function name($gateways) {
	
		$name = __('Hoiio', 'GF_SMS' );
		
		$gateway = array( strtolower( str_replace( 'GFHANNANSMS_Pro_', '', get_called_class())) => $name );
		return array_unique( array_merge( $gateways , $gateway ) );
	}
	
	
	/*
	* Gateway parameters
	*/
	public static function options(){
		return array(
			'app_id'  		=> __('APP ID','GF_SMS'),
			'access_token' 	=> __('Access Token','GF_SMS'),
		);
	}

	/*
	* Gateway credit
	*/
	public static function credit(){
		return false;
	}

	/*
	* Gateway action
	*/
	public static function process( $options, $action, $from, $to, $messages ){
		
		if ( $action == 'credit' && !self::credit() ) {
			return false;
		}
		
		$access_token  = $options['access_token'];
		$app_id  = $options['app_id'];
		
		if ($action == "send") {
			
			$content = 'dest=' . rawurlencode( $to ) .
					'&msg=' . rawurlencode( $messages ) .
					'&sender_name=' . $from .
					'&access_token=' . $access_token .
					'&app_id=' . $app_id;
			
			if ( extension_loaded('curl') ) {
				$ch = curl_init('https://secure.hoiio.com/open/sms/bulk_send?' . $content);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
				$hoiio_response = curl_exec($ch);
				curl_close($ch);
			}
			else {
				$hoiio_response = file_get_contents( 'https://secure.hoiio.com/open/sms/bulk_send?' . $content );
			}
			$response = (array) json_decode( $hoiio_response );

			if ( !empty($response['status']) && $response['status'] == 'success_ok' ) {
				return 'OK';
			}
			else {
				return self::response($response['status']);
			}
			
		}
			

		if ($action == "credit") {
		
		}
		
		if ($action == "range"){
			$min = 5000;
			$max = 10000;
			return array("min" => $min, "max" => $max);
		}

	}
	
		
	public static function response( $status ){
		
		$message = '';
		
		switch($status){
		
			case "error_invalid_http_method" :
				$message = __("Invalid HTTP method. Only GET or POST are allowed.", "GF_SMS" );
				break;
					
			case "error_malformed_params" :
				$message = __("HTTP POST request parameters contains non-readable bytes.", "GF_SMS" );
				break;
				
			case "error_X_param_missing" :
				$message = __("A required parameter is missing. X is the name of the parameter that is missing.", "GF_SMS" );
				break;
				
			case "error_invalid_access_token" :
				$message = __("Your Access Token is invalid, expired or has been revoked.", "GF_SMS" );
				break;
				
			case "error_invalid_app_id" :
				$message = __("Your Application ID is invalid or has been revoked.", "GF_SMS" );
				break;
				
			case "error_tag_invalid_length" :
				$message = __("tag parameter is too long, must be 256 characters or less.", "GF_SMS" );
				break;
				
			case "error_not_allowed_for_trial" :
				$message = __("Only your registered mobile number can be the destination number(s) for trial accounts. To remove this restriction, please make a credit top-up. See Free Trial for more details.", "GF_SMS" );
				break;
				
			case "error_msg_empty" :
				$message = __("message parameter is empty.", "GF_SMS" );
				break;
				
			case "error_dest_too_many" :
				$message = __("dest parameter contains too many destinations. We support a maximum of 1000 destinations.", "GF_SMS" );
				break;
				
			case "error_no_valid_dests" :
				$message = __('The dest parameter does not contain any valid destinations conforming to the E.164 format. This error can occur if the wrong destination delimiter (any character that is not the comma ",") is used.', "GF_SMS" );
				break;
				
			case "error_sms_rebrand_not_enabled" :
				$message = __("SMS Rebranding is not enabled for your application. Please enable it in your Developer Portal.", "GF_SMS" );
				break;
				
			case "error_invalid_sender_name" :
				$message = __("sender_name parameter is invalid.", "GF_SMS" );
				break;
				
			case "error_invalid_notify_url" :
				$message = __("Invalid URL in notify_url parameter.", "GF_SMS" );
				break;
				
			case "error_unable_to_resolve_notify_url" :
				$message = __("Cannot resolve URL in notify_url parameter.", "GF_SMS" );
				break;
				
			case "error_unable_to_complete_ssl_handshake_notify_url" :
				$message = __("Cannot complete SSL Handshake with the provided URL in notify_url parameter. Check if your cert chain is complete using an SSL checker.", "GF_SMS" );
				break;
				
			case "error_insufficient_credit" :
				$message = __("You have insufficient credit in your developer account to send this SMS.", "GF_SMS" );
				break;
				
			case "error_rate_limit_exceeded" :
				$message = __("You have exceeded your request limit for this API. Refer to API Limits for details.", "GF_SMS" );
				break;
				
			case "error_internal_server_error" :
				$message = __("There is an unexpected error. Please contact Hoiio support for assistance.", "GF_SMS" );
				break;
				
		}
		
		return $message;
	}
}