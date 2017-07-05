<?php
/*
 * Class Name : GFHANNANSMS_Pro_{strtoupper( php file name) }	
 */
class GFHANNANSMS_Pro_TEXTLOCAL {

	/*
	* Gateway title	
	*/
	public static function name($gateways) {
	
		$name = __('TextLocal', 'GF_SMS' );
		
		$gateway = array( strtolower( str_replace( 'GFHANNANSMS_Pro_', '', get_called_class())) => $name );
		return array_unique( array_merge( $gateways , $gateway ) );
	}
	
	
	/*
	* Gateway parameters
	*/
	public static function options(){
		return array(
			'username' 	=> __('Username','GF_SMS'),
			'hash'  	=> __('Hash','GF_SMS'),
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
		
		if ( ! extension_loaded('curl') )
			return __('Please enable cURL extension in PHP','GF_SMS');
		
		$username  = $options['username'];
		$hash  	   = $options['hash'];
		
		if ($action == "send") {
			
			$numbers = str_ireplace( '+' , '' , $to );
			$sender  = urlencode($from);
			$message = rawurlencode($messages);
			
			$data = array('username' => $username, 'hash' => $hash, 'numbers' => $numbers, "sender" => $sender, "message" => $message);
			$ch = curl_init('http://api.textlocal.in/send/');
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			$response = curl_exec($ch);
			$response = (array) json_decode($response);
			curl_close($ch);
		
			if ( isset($response['status']) && $response['status'] == 'success' ) {
				return 'OK';
			}
			else {
				
				if ( isset($response['errors'][0]) || isset($response['warnings'][0]) ) {
					
					$error = $warning = '';
					
					if ( isset($response['errors'][0]) ) {
						$errors = (array) $response['errors'][0];
						if ( isset($errors['message']) )
							$error = $errors['message'];
					}
					
					if ( isset($response['warnings'][0]) ) {
						$warnings = (array) $response['warnings'][0];
						if ( isset($warnings['message']) )
							$warning = $warnings['message'] . ' - ';
					}
					
					return $warning . $error;
				}
				else
					return __( 'Unknown Error.' , 'GF_SMS' ); 
				
			}
			
		}
			

		if ($action == "credit") {
		
			$data = 'username=' . $username . '&hash=' . $hash;
			$ch = curl_init('http://api.textlocal.in/balance/?' . $data);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			$response = curl_exec($ch);
			$response = (array) json_decode($response);
			curl_close($ch);
	
			if ( isset($response['status']) && $response['status'] == 'success' ) {
				if ( isset($response['balance']) ) {
					$balance = (array) $response['balance'];
					if ( isset($balance['sms']) )
						return $balance['sms'];
					else 
						return __( 'Unknown Error.' , 'GF_SMS' ); 
				}
				else 
					return __( 'Unknown Error.' , 'GF_SMS' ); 
			}
			else {
				
				if ( isset($response['errors'][0]) || isset($response['warnings'][0]) ) {
					
					$error = $warning = '';
					
					if ( isset($response['errors'][0]) ) {
						$errors = (array) $response['errors'][0];
						if ( isset($errors['message']) )
							$error = $errors['message'];
					}
					
					if ( isset($response['warnings'][0]) ) {
						$warnings = (array) $response['warnings'][0];
						if ( isset($warnings['message']) )
							$warning = $warnings['message'] . ' - ';
					}
					
					return $warning . $error;
				}
				else
					return __( 'Unknown Error.' , 'GF_SMS' ); 
				
			}
		
		}
		
		if ($action == "range"){
			$min = 5000;
			$max = 10000;
			return array("min" => $min, "max" => $max);
		}

	}
	
}