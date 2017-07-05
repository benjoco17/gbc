<?php
/**
 * Class Name : GFHANNANSMS_Pro_{strtoupper( php file name) }	
 */
class GFHANNANSMS_Pro_MOBILY_WS {

	/*
	* Gateway title	
	*/
	public static function name($gateways) {
	
		$name = __('Mobily.ws', 'GF_SMS' );
		
		$gateway = array( strtolower( str_replace( 'GFHANNANSMS_Pro_', '', get_called_class())) => $name );
		return array_unique( array_merge( $gateways , $gateway ) );
	}
	
	
	/*
	* Gateway parameters
	*/
	public static function options(){
		return array(
			'mobile'    => __('UserName/Mobile','GF_SMS'),
			'password' 	=> __('Password','GF_SMS'),
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
		
		$mobile   = str_ireplace( '+', '', $options['mobile']);
		$password   = $options['password'];
		
		if ( function_exists('is_rtl') && is_rtl() )
			require( 'lib/Mobily.ws/includeSettings_AR.php'); 
		else 
			require( 'lib/Mobily.ws/includeSettings_EN.php');
		
		
		if ($action == "send") {
		
			$numbers = str_replace( '+', '', $to);
			
			$MsgID = rand(1,99999);					
			$timeSend = 0;						
			$dateSend = 0;	
			$deleteKey = 0;					
			$resultType = 0;
			
			$result = sendSmsMobily($mobile, $password, $numbers, $from, $messages, $MsgID, $timeSend, $dateSend, $deleteKey, $resultType);	
			
			if ( $result == 1 || $result == '1' )
				return 'OK';
			else
				return printStringResult(trim($result), $arraySendMsg);
		}
		

		if ($action == "credit") {
			
			$result = balanceSmsMobily($mobile, $password, 0 );
			
			if ( in_array( $result , array( '0' , '1' , '2' , '3' ) ))
				return printStringResult(trim($result), $arrayBalance, 'Balance');
			else {
				list($originalAccount, $currentAccount) = explode("/", $result);
				return $currentAccount;
			}
		}
		
		if ($action == "range"){
			$min = 5;
			$max = 30;
			return array("min" => $min, "max" => $max);
		}

	}
}