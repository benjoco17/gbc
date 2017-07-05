<?php
/**
 * Class Name : GFHANNANSMS_Pro_{strtoupper( php file name) }	
 */
class GFHANNANSMS_Pro_NEXMO {

	/*
	* Gateway title	
	*/
	public static function name($gateways) {
	
		$name = __('Nexmo', 'GF_SMS' );
		
		$gateway = array( strtolower( str_replace( 'GFHANNANSMS_Pro_', '', get_called_class())) => $name );
		return array_unique( array_merge( $gateways , $gateway ) );
	}
	
	/*
	* Gateway parameters
	*/
	public static function options(){
		return array(
			'api_key'  => __('API Key','GF_SMS'),
			'api_secret'  => __('API Secret','GF_SMS')
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
	public static function process( $options, $action, $from, $to, $messages ){
		
		if ( $action == 'credit' && !self::credit() ) {
			return false;
		}
		
		$api_key = $options['api_key'];
		$api_secret = $options['api_secret'];
		
		
		if ($action == "send") {
		
			if ( !class_exists('NexmoMessage') )
				require( 'lib/Nexmo/NexmoMessage.php'); 		
			
			$recievers = explode(',',$to);
			$response = 'true';	
			$status = '';
			$result_message = array();
			
			foreach ( (array) $recievers as $reciever ) {
				
				$nexmo_sms = new NexmoMessage( $api_key, $api_secret );
				$info = $nexmo_sms->sendText( $reciever, $from, $messages );
				$status = $info->messages[0]->status;
				if ( $status != '0' ) {
					$response = 'failed';
					$result_message[] = $to . '=>Error:'.self::response($status);
				}
				
			}
			
			if ( $response == 'true' &&  empty($result_message) ) {
				return 'OK';
			}
			else {
				return __( 'Failed for : ' , 'GF_SMS' ) . implode( ' , ', $result_message);
			}

		}

		
		if ($action == "credit") {
			if ( !class_exists('NexmoAccount') )
				require( 'lib/Nexmo/NexmoAccount.php'); 
			$nexmo_sms = new NexmoAccount( $api_key, $api_secret );
			return $nexmo_sms->balance();
		}

		if ($action == "range"){
			$min = 1000;
			$max = 5000;
			return array("min" => $min, "max" => $max);
		}

	}
	
	
	public static function response( $status ){
		
		$message = '';
		
		switch($status){
		
			case "0" :
				$message = __("The message was successfully accepted for delivery by Nexmo.", "GF_SMS" );
				break;
					
			case "1" :
				$message = __("You have exceeded the submission capacity allowed on this account. Please wait and retry.", "GF_SMS" );
				break;
				
			case "2" :
				$message = __("Your request is incomplete and missing some mandatory parameters.", "GF_SMS" );
				break;
				
			case "3" :
				$message = __("The value of one or more parameters is invalid.", "GF_SMS" );
				break;
				
			case "4" :
				$message = __("The api_key / api_secret you supplied is either invalid or disabled.", "GF_SMS" );
				break;
				
			case "5" :
				$message = __("There was an error processing your Request in the Nexmo Cloud Communications Platform.", "GF_SMS" );
				break;
				
			case "6" :
				$message = __("The Nexmo Cloud Communications Platform was unable to process your Request. For example, due to an unrecognized prefix for the phone number.", "GF_SMS" );
				break;
				
			case "7" :
				$message = __("The number you are trying to submit to is blacklisted and may not receive messages.", "GF_SMS" );
				break;
				
			case "8" :
				$message = __("The api_key you supplied is for an account that has been barred from submitting messages.", "GF_SMS" );
				break;
				
			case "9" :
				$message = __("Your pre-paid account does not have sufficient credit to process this message.", "GF_SMS" );
				break;
				
			case "11" :
				$message = __("This account is not provisioned for REST submission, you should use SMPP instead.", "GF_SMS" );
				break;
				
			case "12" :
				$message = __("The length of udh and body was greater than 140 octets for a binary type SMS Request.", "GF_SMS" );
				break;
				
			case "13" :
				$message = __("Message was not submitted because there was a communication failure.", "GF_SMS" );
				break;
				
			case "14" :
				$message = __("Message was not submitted due to a verification failure in the submitted signature.", "GF_SMS" );
				break;
				
			case "15" :
				$message = __("Due to local regulations, the SenderID you set in from in the Request was not accepted. Please check the country specific features and restrictions.", "GF_SMS" );
				break;
				
			case "16" :
				$message = __("The value of ttl in your Request was invalid.", "GF_SMS" );
				break;
				
			case "19" :
				$message = __("Your request makes use of a facility that is not enabled on your account.", "GF_SMS" );
				break;
				
			case "20" :
				$message = __("The value of message-class in your Request was out of range. Possible values are from 0 to 3 inclusive.", "GF_SMS" );
				break;
				
			case "29" :
				$message = __("The phone number you set in to is not in your pre-approved destination list. To send messages to this phone number, add it using Nexmo Dashboard.", "GF_SMS" );
				break;
					
			case "101" :
				$message = __("The following response codes are for 2FA and Campaign Subscription Management only.", "GF_SMS" );
				break;
			
		}
		
		return $message;
	}
	
}