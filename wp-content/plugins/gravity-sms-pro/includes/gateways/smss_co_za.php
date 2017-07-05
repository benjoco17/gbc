<?php
/*
 * Class Name : GFHANNANSMS_Pro_{strtoupper( php file name) }	
 */
class GFHANNANSMS_Pro_SMSS_CO_ZA {

	/*
	* Gateway title	
	*/
	public static function name($gateways) {
	
		$name = __('SMSS.co.za', 'GF_SMS' );
		
		$gateway = array( strtolower( str_replace( 'GFHANNANSMS_Pro_', '', get_called_class())) => $name );
		return array_unique( array_merge( $gateways , $gateway ) );
	}
	
	
	/*
	* Gateway parameters
	*/
	public static function options(){
		return array(
			'api_key' 	=> __('API Key','GF_SMS'),
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
		
		$api_key  = $options['api_key'];
		
		$to = str_replace('+','', $to );
		$to = str_replace(',','+', $to );
		
		if ($action == "send") {
			
			$xml_data = '<?xml version="1.0" encoding="utf-8" ?>' .
			'<messages>' .
				'<message>' .
					'<key>'.$api_key.'</key>' .
					'<to>'.$to.'</to>' .
					'<senderid>'.$from.'</senderid>' .
					'<text>'.$messages.'</text>' .
					'<type>text</type>' .
				'</message>' .
			'</messages>';
		
			$ch = curl_init();
			curl_setopt( $ch, CURLOPT_URL, 'http://148.251.196.36/app/smsXmlApi' );
			curl_setopt( $ch, CURLOPT_POST, true );
			curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-Type: text/xml'));
			curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
			curl_setopt( $ch, CURLOPT_POSTFIELDS, "$xml_data" );
			$response = curl_exec($ch);
			curl_close($ch);

			return var_dump($response);
		
		
			if ( stripos( $SMSS_CO_ZA_response , 'error') === false && count($succsess_response) > 1 ) {
				return 'OK';
			}
			else {
				return __( 'Unknown Error.' , 'GF_SMS' ); 
			}
			
		}
			

		if ($action == "credit") {
			$ch = curl_init('http://148.251.196.36/app/miscapi/' . $api_key . '/getBalance/true/');
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
			$response = curl_exec($ch);
			curl_close($ch);
			
			if ( stripos( $response , 'err') !== false ) {
				list($code , $error ) = explode( '-', $response );
				return trim( ucfirst($error) );
			}
			
			return $response;
		}
		
		if ($action == "range"){
			$min = 200;
			$max = 500;
			return array("min" => $min, "max" => $max);
		}

	}
	
}