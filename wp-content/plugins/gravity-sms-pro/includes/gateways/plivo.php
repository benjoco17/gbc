<?php
/**
 * Class Name : GFHANNANSMS_Pro_{strtoupper( php file name) }	
 */
class GFHANNANSMS_Pro_PLIVO {

	/*
	* Gateway title	
	*/
	public static function name($gateways) {
	
		$name = __('Plivo', 'GF_SMS' );
		
		$gateway = array( strtolower( str_replace( 'GFHANNANSMS_Pro_', '', get_called_class())) => $name );
		return array_unique( array_merge( $gateways , $gateway ) );
	}
	
	
	/*
	* Gateway parameters
	*/
	public static function options(){
		return array(
			'auth_id'  		=> __('Auth ID','GF_SMS'),
			'auth_token' 	=> __('Auth Token','GF_SMS'),
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
		
		if ( $action == "send" ) {
		
			$auth_id 	= $options['auth_id'];
			$auth_token  = $options['auth_token'];
			
			
			$from = str_ireplace( '+' , '' , trim($from));
			$to = str_ireplace( array('+' , ',') , array( '', '<' ) , $to);
			
			$parameters = array (
				'headers' => array(
					'Authorization'	=> 'Basic ' . base64_encode( $auth_id . ":" . $auth_token ),
					'Connection'	=> 'close',
					'Content-Type'	=> 'application/json',
				),
				'body' => json_encode( array(
					'src'		=> $from,
					'dst'		=> $to,
					'text'		=> $messages,
					'type'		=> 'sms',
				) )
			);
			
			$response = wp_remote_post( "https://api.plivo.com/v1/Account/" . $auth_id . "/Message/", $parameters );
			
			if ( is_wp_error( $response ) ) {
				return $response->get_error_message();
			}
			else {
				
				if ( isset($response['response']['code']) && ($response['response']['code'] == 202 || $response['response']['code'] == '202') )
					return 'OK';
				
				if ( isset($response['response']['message']) && $response['response']['message'] == 'message(s) queued' )
					return 'OK';
				
				$result = json_decode($response['body']);
				if ( !empty($result->error) )
					return ucfirst($result->error);
			
				if (isset($response['body']))
					return ucfirst($response['body']);
				
				if ( isset($response['response']['message']) )
					return ucfirst($response['response']['message']);
				
			}
		
		}
			

		if ($action == "credit") {

		}
		
		if ($action == "range"){
			$min = 5;
			$max = 30;
			return array("min" => $min, "max" => $max);
		}

	}
}