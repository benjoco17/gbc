<?php
/**
 * Class Name : GFHANNANSMS_Pro_{strtoupper( php file name) }	
 */
class GFHANNANSMS_Pro_CLICKSEND {

	/*
	* Gateway title	
	*/
	public static function name($gateways) {
	
		$name = __('ClickSend', 'GF_SMS' );
		
		$gateway = array( strtolower( str_replace( 'GFHANNANSMS_Pro_', '', get_called_class())) => $name );
		return array_unique( array_merge( $gateways , $gateway ) );
	}
	
	
	/*
	* Gateway parameters
	*/
	public static function options(){
		return array(
			'username'  		=> __('Username','GF_SMS'),
			'api_key' 		=> __('API Key','GF_SMS'),
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
		
		$username 	= $options['username'];
		$api_key  = $options['api_key'];
		
		if ( ! class_exists('ClickSendSMS') || ! class_exists('ClickSend')  )
			require( 'lib/ClickSend/ClickSend.php'); 
		
		$clickSend = new ClickSend( $username , $api_key );
			
		if ($action == "send") {
			
			$response = 'true';	
			$result_message = array();
			$smsObject = new ClickSendSMS();
			
			/* Required Parameters */
			$smsObject->setTo( $to );
			$smsObject->setMessage( $messages );

			
			/* Optional Parameters */
			$smsObject->setSenderid($from);
			$smsObject->setSchedule("");
			$smsObject->setCustomstring("");
		
			$smsResponse = $clickSend->send_sms($smsObject);

			for($i = 0; $i < $smsResponse->recipientcount; $i++ ){

				$recepient = $smsResponse->messages[$i];
	
				/*Result Text and Description*/
				/*
				print '<br>Recipient: '.$recepient->to;
				print '<br>Message ID : '.$recepient->messageid;
				print '<br>Result : '.$recepient->result;
				print '<br>Error Text : '.$recepient->errortext;
				print '<br>Error Description : '.$recepient->errordescription;	
				print '<hr>';
				*/
				
				if ( empty($recepient->result) || $recepient->result != '0000') {
					$response = 'failed';
					$result_message[] = $to . '=>'.$recepient->errordescription;
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

			$balanceObject = new ClickSendBalance();

			/* Optional Parameters */
			$balanceObject->setCountry('AU');
			$balanceResponse = $clickSend->get_balance($balanceObject);
			/*Result Text and Description*/
			if ($balanceResponse->result == "0000"){
				/*	
				print '<br>Account type : '.$balanceResponse->type;
				print '<br>Currency Symbol : '.$balanceResponse->currency_symbol;
				print '<br>Credit : '.$balanceResponse->credit;
				print '<br>Balance : '.$balanceResponse->balance;
				*/
				return __('Balance : ' , 'GF_SMS') . $balanceResponse->currency_symbol . $balanceResponse->balance;
			}
			else {
				/*
				print '<br>Result : '.$balanceResponse->result;
				print '<br>Error Text : '.$balanceResponse->errortext;
				print '<br>Error Description : '.$balanceResponse->errordescription;
				*/
				return $balanceResponse->errordescription;
			}

		}
		
		if ($action == "range"){
			$min = 5;
			$max = 30;
			return array("min" => $min, "max" => $max);
		}

	}
}