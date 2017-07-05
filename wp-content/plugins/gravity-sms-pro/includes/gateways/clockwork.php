<?php
/*
 * Class Name : GFHANNANSMS_Pro_{strtoupper( php file name) }	
 */
class GFHANNANSMS_Pro_CLOCKWORK {

	/*
	* Gateway title	
	*/
	public static function name($gateways) {
	
		$name = __('ClockworkSMS', 'GF_SMS' );
		
		$gateway = array( strtolower( str_replace( 'GFHANNANSMS_Pro_', '', get_called_class())) => $name );
		return array_unique( array_merge( $gateways , $gateway ) );
	}
	
	/*
	* Gateway parameters
	*/
	public static function options(){
		return array(
			'api_key' 		=> __('API key','GF_SMS'),
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
		
		$API_KEY  = $options['api_key'];
		
		if ( ! class_exists('Clockwork') )
			require( 'lib/Clockwork/class-Clockwork.php'); 
				
		if ($action == "send") {
			
			$response = 'true';	
			$result_message = array();		
			$recievers = explode(',',$to);
			$recieve = array();
			foreach( (array) $recievers as $reciever ) {
				$recieve[] = array( 'to' => str_replace( '+', '', $reciever) , 'message' => $message, 'from' => $from );
			}
	
			try {
				$clockwork = new Clockwork( $API_KEY, array( 'from' => $from ) );		
				$results = $clockwork->send( $recieve );
			}
			catch( ClockworkException $e ){
				return $e->getMessage();
			}
			
			foreach ( (array) $results as $result) {
				if ( empty($result['success']) || ( $result['success'] != 1 && $result['success'] != '1' ) ) {
					$response = 'failed';
					$result_message[] = $to . '=>'.$result['error_message'];
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
			try {
				$clockwork = new Clockwork( $API_KEY );
				$balance = $clockwork->checkBalance();
			}
			catch( ClockworkException $e ){
				return $e->getMessage();
			}
			return $balance['symbol'].$balance['balance'].' ('.$balance['code'].')';
		}
		
		if ($action == "range"){
			$min = 5;
			$max = 10;
			return array("min" => $min, "max" => $max);
		}

	}
}