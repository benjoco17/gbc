<?php
/**
 * Class Name : GFHANNANSMS_Pro_{strtoupper( php file name) }	
 */
class GFHANNANSMS_Pro_INFOBIP {

	/*
	* Gateway title	
	*/
	public static function name($gateways) {
	
		$name = __('Infobip', 'GF_SMS' );
		
		$gateway = array( strtolower( str_replace( 'GFHANNANSMS_Pro_', '', get_called_class())) => $name );
		return array_unique( array_merge( $gateways , $gateway ) );
	}
	
	
	/*
	* Gateway parameters
	*/
	public static function options(){
		return array(
			'username'  => __('Username','GF_SMS'),
			'password' 	=> __('Password','GF_SMS'),
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
		
		
		if ( $action == "send" ) {
		
			$username 	= $options['username'];
			$password   = $options['password'];
			
			$destinations = array();
			$recievers = explode( ',' , $to );
			foreach ( $recievers as $reciever ) {
				$destination = array(
					'messageId' => rand(1000,9999),
					'to' 		=> $reciever
				);
				$destinations[] = $destination;
			}
			
            $message = array(
				"from" 				=> $from,
                "destinations" 		=> $destinations,
                "text" 				=> $messages,
                "notifyUrl" 		=> '',
                "notifyContentType" => '',
                "callbackData" 		=> ''
			);

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, "https://api.infobip.com/sms/1/text/advanced" );
            curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type:application/json", "Accept:application/json") );
            curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
            curl_setopt($ch, CURLOPT_USERPWD, $username . ":" . $password);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 2);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        //  curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
            curl_setopt($ch, CURLOPT_MAXREDIRS, 2);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(array("messages" => array($message))));
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $responseBody = json_decode($response);
            curl_close($ch);

			$response = 'true';	
			$result_message = array();
			if ( isset($httpCode) && $httpCode >= 200 && $httpCode < 300) {
		
				$results = $responseBody->messages;
				foreach ($results as $result) {
					/*
					echo "<tr>";
					echo "<td>" . $result->resultId . "</td>";
					echo "<td>" . $result->to . "</td>";
					echo "<td>" . $result->status->groupId . "</td>";
					echo "<td>" . $result->status->groupName . "</td>";
					echo "<td>" . $result->status->id . "</td>";
					echo "<td>" . $result->status->name . "</td>";
					echo "<td>" . $result->status->description . "</td>";
					echo "<td>" . $result->smsCount . "</td>";
					echo "</tr>";
					*/
					if ( stripos($result->status->description, 'sent' ) === false || stripos($result->status->groupName, 'REJECTED' ) !== false ) {
						$response = 'false';
						$result_message[] = $result->to . '=>' . $result->status->description;
					}
				}
			}
			else {
				return $responseBody->requestError->serviceException->text;
			}
			
			if ( $response == 'true' &&  empty($result_message) ) {
				return 'OK';
			}
			else {
				return __( 'Failed for : ' , 'GF_SMS' ) . implode( ' , ', $result_message);
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