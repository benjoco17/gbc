<?php
/**
 * Class Name : GFHANNANSMS_Pro_{strtoupper( php file name) }	
 */
class GFHANNANSMS_Pro_SMSGLOBAL {

	/*
	* Gateway title	
	*/
	public static function name($gateways) {
	
		$name = __('SMSGlobal', 'GF_SMS' );
		
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
		return false;
	}

	/*
	* Gateway action
	*/
	public static function process( $options, $action, $from, $to, $message ){
		
		if ( $action == 'credit' && !self::credit() ) {
			return false;
		}
		
		$reciever = str_replace('+','', $to );
	//	$recievers = explode( ',', $reciever );
	//	unset($reciever);
		
		$username = urlencode($options['username']);
		$password = urlencode($options['password']);
		$message  = urlencode($message);
		$response = '';
		
		if ($action == "send") {
			
			$result = '1';
			$output = '';
			
			$reciever = urlencode($reciever);
			$url = "http://www.smsglobal.com/http-api.php?action=sendsms&user=$username&password=$password&to=$reciever&from=$from&text=$message";
			if ( extension_loaded('curl') ) {
				$ch = curl_init($url);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
				$response = curl_exec($ch);
				curl_close($ch);
			}
			else {
				$response = file_get_contents( $url );
			}
			
			if ( strpos ( strtolower($response) , 'error' ) === false && strpos ( strtolower($response) , 'time' ) === false  && strpos ( strtolower($response) , 'no' ) === false  )
				return 'OK';
			
			return str_replace(
				array('12','13','14','69','88','102','400','401','402','10','11','1','2','3','4','5','8'),
				array(
					__('Message ID is invalid', 'GF_SMS' ),
					__('SMSGlobal was unable to contact the carrier', 'GF_SMS' ),
					__('Invalid Password', 'GF_SMS' ),
					__('Submit SM failed', 'GF_SMS' ),
					__('No credits (Can occasionally also be due to an internal error)', 'GF_SMS' ),
					__('Destination not covered or Unknown prefix', 'GF_SMS' ),
					__('Send message timed-out.', 'GF_SMS' ),
					__('Invalid credentials', 'GF_SMS' ),
					__('Invalid username/password', 'GF_SMS' ),
					__('Invalid Source Address', 'GF_SMS' ),
					__('Invalid Dest Addr', 'GF_SMS' ),
					__('Message Length is invalid', 'GF_SMS' ),
					__('Command Length is invalid', 'GF_SMS' ),
					__('Invalid Command ID', 'GF_SMS' ),
					__('Incorrect BIND', 'GF_SMS' ),
					__('Already in Bound State', 'GF_SMS' ),
					__('Source or destination is too short', 'GF_SMS' ),
				),
				$response
			);
			

		
		}

		if ($action == "range"){
			$min = 6;
			$max = 20;
			return array("min" => $min, "max" => $max);
		}

	}
	
}