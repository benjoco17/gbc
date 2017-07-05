<?php
/**
 * Class Name : GFHANNANSMS_Pro_{strtoupper( php file name) }	
 */
class GFHANNANSMS_Pro_CM {

	/*
	* Gateway title	
	*/
	public static function name($gateways) {
	
		$name = __('CM SMS', 'GF_SMS' );
		
		$gateway = array( strtolower( str_replace( 'GFHANNANSMS_Pro_', '', get_called_class())) => $name );
		return array_unique( array_merge( $gateways , $gateway ) );
	}
	
	/*
	* Gateway parameters
	*/
	public static function options(){
		return array(
			'token'  => __('Token','GF_SMS'),
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
		
		$token = $options['token'];
		
		if ($action == "send") {
		
			if ( empty($from) )
				return __( '"From" is required.' , 'GF_SMS' );
		
			if ( empty($messages) )
				return __( '"Message Body" is required.' , 'GF_SMS' );
		
			if ( empty($to) )
				return __( '"To" is required.' , 'GF_SMS' );
		
			if ( empty($token) )
				return __( '"Token" is required.' , 'GF_SMS' );
		
		
			$recievers = explode(',',$to);
			$response = 'true';	
			$result_message = array(); 
			
			foreach ( (array) $recievers as $reciever ) {
				$to = str_replace('+', '00', $reciever);
				$status = self::sendMessage( $to , $messages , $from , $token );
				if ( !empty($status) ) {
					$response = 'failed';
					$result_message[] = $to . '=>'.$status;
				}
				unset($status);
			}
			
			if ( $response == 'true' && empty($result_message) ) {
				return 'OK';
			}
			else {
				return __( 'Failed for : ' , 'GF_SMS' ) . implode( ' , ', $result_message);
			}

		}

		
		if ($action == "range"){
			$min = 10;
			$max = 100;
			return array("min" => $min, "max" => $max);
		}

	}
	
	
	public static function sendMessage( $to, $message , $from , $token ) {
		
		if ( extension_loaded('curl') ) {
			$xml = new SimpleXMLElement('<MESSAGES/>');
			$authentication = $xml->addChild('AUTHENTICATION');
			$authentication->addChild('PRODUCTTOKEN', $token);
			$msg = $xml->addChild('MSG');
			$msg->addChild('FROM', $from);
			$msg->addChild('TO',   $to);
			$msg->addChild('BODY', $message);
			$field = $xml->asXML();
			$ch = curl_init();
			curl_setopt_array($ch, array(
				CURLOPT_URL            => 'https://sgw01.cm.nl/gateway.ashx',
				CURLOPT_HTTPHEADER     => array('Content-Type: application/xml'),
				CURLOPT_POST           => true,
				CURLOPT_POSTFIELDS     => $field,
				CURLOPT_RETURNTRANSFER => true
			));
			$response = curl_exec($ch);
			curl_close($ch);
		}
		else {
			
			$message = rawurlencode($message);
			$token   = rawurlencode($token);
			$to 	 = rawurlencode($to);
			$from    = rawurlencode($from);
			
			$url = "https://sgw01.cm.nl/gateway.ashx?producttoken=$token&body=$message&to=$to&from=$from";
			$response = file_get_contents( $url );
		}
		
		return $response;
	}
	
}