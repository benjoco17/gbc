<?php
/**
 * Class Name : GFHANNANSMS_Pro_{strtoupper( php file name) }	
 */
class GFHANNANSMS_Pro_CLICKATELL {

	/*
	* Gateway title	
	*/
	public static function name($gateways) {
	
		$name = __('Clickatell', 'GF_SMS' );
		
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
			'api_id' 			=> __('API ID','GF_SMS'),
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
		
		$reciever = urlencode(str_replace('+','', $to ));
		
		$username = urlencode($options['username']);
		$password = urlencode($options['password']);
		$api_id   = urlencode($options['api_id']);
		$message  = urlencode($message);
		$response = '';
		
		if ($action == "send") {
			
			$url = "https://api.clickatell.com/http/sendmsg?user=$username&password=$password&api_id=$api_id&to=$reciever&text=$message&from=$from";
		
			if ( extension_loaded('curl') ) {
				$ch = curl_init($url);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
				$response = curl_exec($ch);
				curl_close($ch);
			}
			else {
				$response = file_get_contents( $url );
			}
			
			if ( strpos ( strtolower($response) , 'err' ) === false )
				return 'OK';
			
			return $response;
		
		}
			

		if ($action == "credit") {

			$url = "https://api.clickatell.com/http/getbalance?user=$username&password=$password&api_id=$api_id";
			
			if ( extension_loaded('curl') ) {
				$ch = curl_init($url);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
				$response = curl_exec($ch);
				curl_close($ch);
			}
			else {
				$response = file_get_contents( $url );
			}
			
			return $response;
		
		}
		
		if ($action == "range"){
			$min = 5;
			$max = 20;
			return array("min" => $min, "max" => $max);
		}

	}
}