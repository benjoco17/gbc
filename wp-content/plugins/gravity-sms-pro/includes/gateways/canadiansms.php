<?php
/*
 * Class Name : GFHANNANSMS_Pro_{strtoupper( php file name) }	
 */
class GFHANNANSMS_Pro_CANADIANSMS {

	/*
	* Gateway title	
	*/
	public static function name($gateways) {
	
		$name = __('CanadianSMS', 'GF_SMS' );
		
		$gateway = array( strtolower( str_replace( 'GFHANNANSMS_Pro_', '', get_called_class())) => $name );
		return array_unique( array_merge( $gateways , $gateway ) );
	}
	
	
	/*
	* Gateway parameters
	*/
	public static function options(){
		return array(
			'username' 	=> __('Username','GF_SMS'),
			'password'  => __('Password','GF_SMS'),
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
		
		if ( ! extension_loaded('curl') )
			return __('Please enable cURL extension in PHP','GF_SMS');
		
		$username  = $options['username'];
		$password  = $options['password'];
		
		$to = str_replace('+','', $to );
		
		if ($action == "send") {
		
			$Parameters = array(
				'username' => $username, 
				'password' => $password,
				'route'	   => 'primary',
				'message'  => $messages,
				'mobiles'  => $to
			);
			
			$curl = curl_init();
			curl_setopt($curl, CURLOPT_URL, 'http://canadiansms.com/api/sendsms.php');
			curl_setopt($curl, CURLOPT_POSTFIELDS, $Parameters);
			curl_setopt($curl, CURLOPT_TIMEOUT, 400);
			curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			$canadiansms_response = curl_exec($curl);
			curl_close($curl);
		
			$canadiansms_response = strtolower($canadiansms_response);
			$succsess_response = explode( '~' , $canadiansms_response );
			
			if ( stripos( $canadiansms_response , 'error') === false && count($succsess_response) > 1 ) {
				return 'OK';
			}
			else {
				
				if ( stripos( $canadiansms_response , 'error1') )
					return __( 'Error your username or password are invalid.' , 'GF_SMS' );
				
				else if ( stripos( $canadiansms_response , 'error2') )
					return __( 'Duplicate in username and password (contact with customer service department).' , 'GF_SMS' );
				
				else if ( stripos( $canadiansms_response , 'error5') )
					return __( 'Missing the telephone field.' , 'GF_SMS' );
				
				else if ( stripos( $canadiansms_response , 'error7') )
					return __( 'Not enough remaining credits to your account.' , 'GF_SMS' );
				
				else if ( stripos( $canadiansms_response , 'error8') )
					return __( 'Your message are too small means not excedded the 10 characters.' , 'GF_SMS' );
				
				else if ( stripos( $canadiansms_response , 'error9') )
					return __( 'Your message is more than 400 characters' , 'GF_SMS' );
				
				else
					return __( 'Unknown Error.' , 'GF_SMS' ); 
				
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
	
}