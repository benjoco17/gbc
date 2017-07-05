<?php
/**
 * Class Name : GFHANNANSMS_Pro_{strtoupper( php file name) }	
 */
class GFHANNANSMS_Pro_TWILIO {

	/*
	* Gateway title	
	*/
	public static function name($gateways) {
	
		$name = __('Twilio', 'GF_SMS' );
		
		$gateway = array( strtolower( str_replace( 'GFHANNANSMS_Pro_', '', get_called_class())) => $name );
		return array_unique( array_merge( $gateways , $gateway ) );
	}
	
	/*
	* Gateway parameters
	*/
	public static function options(){
		return array(
			'account_sid'  => __('Account SID','GF_SMS'),
			'auth_token'  => __('Auth Token','GF_SMS')
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
		
		
		$account_sid 	= $options['account_sid'];
		$auth_token  	= $options['auth_token'];
		
		if ( ! class_exists('Services_Twilio') )
			require( 'lib/Twilio/Twilio.php'); 
		
		if ( $action == 'send' ) {

			$account = '';
			$client = new Services_Twilio($account_sid, $auth_token);	
			try {
				$account = $client->account->status;
			}
			catch ( Exception $ex ) {
				return $ex->getMessage();
			}
			if ( $account != 'active' ) {
				return __( 'Your Twilio Account isn\'t active or is wrong .' , 'GF_SMS');
			}
			else {
				
				$recievers = explode(',',$to);
				$response = 'true';	
				$status = '';
				$result_message = array();
			
				foreach ( (array) $recievers as $reciever ) {
					try {
						$result = $client->account->messages->create(array( 
							'To' => $reciever, 
							'From' => $from, 
							'Body' => $messages
						));
						$status = $result->status;
					}
					catch ( Exception $ex ) {
						$error = $ex->getMessage();
					}
					
					if ( $status == 'failed' || !empty($error) ) {
						$response = 'failed';
						$result_message[] = $reciever . '=>Error:'.$error;
						unset($error);
					}
					
				}
			}
			
			if ( $response == 'true' &&  empty($result_message) ) {
				return 'OK';
			}
			else {
				return __( 'Failed for : ' , 'GF_SMS' ) . implode( ' , ', $result_message);
			}
			
			
		}
		
		
		if ($action == "range"){
			$min = 10;
			$max = 10000;
			return array("min" => $min, "max" => $max);
		}

	}
}