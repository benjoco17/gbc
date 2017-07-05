<?php
/**
 * Class Name : GFHANNANSMS_Pro_{strtoupper( php file name) }
 */
class GFHANNANSMS_Pro_BULKSMS {

	/*
	* Gateway title	
	*/
	public static function name($gateways) {
	
		$name = __('BulkSMS', 'GF_SMS' );
		
		$gateway = array( strtolower( str_replace( 'GFHANNANSMS_Pro_', '', get_called_class())) => $name );
		return array_unique( array_merge( $gateways , $gateway ) );
	}
	
	/*
	* Gateway parameters
	*/
	public static function options(){
		return array(
			'username'  		=> __('Username','GF_SMS'),
			'password' 			=> __('Password','GF_SMS'),
		);
	}

	/*
	* Gateway credit
	*/
	public static function credit(){
		return true;
	}

	/*
	* Gateway action
	*/
	public static function process( $options, $action, $from, $to, $message ){
		
		if ( $action == 'credit' && !self::credit() ) {
			return false;
		}
		
		$reciever = urlencode($to);
		$username = urlencode($options['username']);
		$password = urlencode($options['password']);
		$message  = urlencode($message);
		$status   = '';
		
		if ($action == "send") {
			
			$url = "https://bulksms.vsms.net/eapi/submission/send_sms/2/2.0?username=$username&password=$password&msisdn=$reciever&message=$message&sender=$from";
		
			if ( extension_loaded('curl') ) {
				$ch = curl_init($url);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
				$response = curl_exec($ch);
				curl_close($ch);
			}
			else {
				$response = file_get_contents( $url );
			}
			
			$status = explode('|', $response);
			
			if ( !empty($status[0]) && $status[0] == '0' )
				return 'OK';
			
			return self::response( $status[0] );
		
		}
			

		if ($action == "credit") {

			$url = "https://bulksms.vsms.net/eapi/user/get_credits/1/1.1?username=$username&password=$password";
			
			if ( extension_loaded('curl') ) {
				$ch = curl_init($url);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
				$response = curl_exec($ch);
				curl_close($ch);
			}
			else {
				$response = file_get_contents( $url );
			}
			list( $status , $credit ) = explode('|', $response);
			
			if ( $status == '0' )
				return $credit;
			
			return self::response( $status );
		
		
		}
		
		if ($action == "range"){
			$min = 6;
			$max = 20;
			return array("min" => $min, "max" => $max);
		}

	}
	
	
			
	public static function response( $status ){
		
		$message = '';
		
		switch($status){
		
			case "1" :
				$message = __("Scheduled (see Scheduling below).", "GF_SMS" );
				break;
					
			case "22" :
				$message = __("Internal fatal error.", "GF_SMS" );
				break;
				
			case "23" :
				$message = __("Authentication failure.", "GF_SMS" );
				break;
				
			case "24" :
				$message = __("Data validation failed.", "GF_SMS" );
				break;
				
			case "25" :
				$message = __("You do not have sufficient credits.", "GF_SMS" );
				break;
				
			case "26" :
				$message = __("Upstream credits not available.", "GF_SMS" );
				break;
				
			case "27" :
				$message = __("You have exceeded your daily quota.", "GF_SMS" );
				break;
				
			case "28" :
				$message = __("Upstream quota exceeded.", "GF_SMS" );
				break;
				
			case "40" :
				$message = __("Temporarily unavailable.", "GF_SMS" );
				break;
				
			case "201" :
				$message = __('Maximum batch size exceeded.', "GF_SMS" );
				break;
				
		}
		
		return $message;
	}
}