<?php

/*
 * Class Name : GFHANNANSMS_Pro_{strtoupper( php file name) }	
 */

class GFHANNANSMS_Pro_BULKSMSGLOBAL_IN
{

    /*
    * Gateway title	
    */
    public static function name($gateways)
    {

        $name = __('BulkSmsGlobal.in', 'GF_SMS');

        $gateway = array(strtolower(str_replace('GFHANNANSMS_Pro_', '', get_called_class())) => $name);
        return array_unique(array_merge($gateways, $gateway));
    }


    /*
    * Gateway parameters
    */
    public static function options()
    {
        return array(
            'authKey' => __('Authentication Key', 'GF_SMS'),
            'route' => __('Route', 'GF_SMS'),
        );
    }

    /*
    * Gateway credit
    */
    public static function credit()
    {
        return true;
    }

    /*
    * Gateway action
    */
    public static function process($options, $action, $from, $to, $messages)
    {

        if ($action == 'credit' && !self::credit()) {
            return false;
        }

		$authKey = $options['authKey'];
		$route = !empty($options['route']) ? $options['route'] : 'default';
			
        if ($action == "send") {
			
            $message = urlencode($messages);
			$senderId = $from;
			$mobiles = $to;
		//	$mobiles = str_replace('+','', $to );
		
            $content = 'authkey=' . $authKey .
					  '&mobiles=' . $mobiles .
					  '&message=' . $message .
					  '&sender=' . $senderId .
					  '&route=' . $route .
					  '&response=json'.
					  '&country=0';

			if ( extension_loaded('curl') ) {
				$ch = curl_init('http://login.bulksmsglobal.in/api/sendhttp.php?' . $content);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
				$output = curl_exec($ch);
				curl_close($ch);
			}
			else {
				$output = file_get_contents( 'http://login.bulksmsglobal.in/api/sendhttp.php?' . $content );
			}
			$output = json_decode($output);
		
		
			if ( isset($output->type) && $output->type == 'success' && isset($output->message) && strlen($output->message) == 24 )
				return 'OK';
			else if (isset($output->message))
				return is_numeric($output->message) ? self::response($output->message) : $output->message;
			else 
				return __( 'Unknown Error.' , 'GF_SMS');
        }


        if ($action == "credit") {

           $content = 'authkey=' . $authKey .
					  '&type=' . $route;
			
			if ( extension_loaded('curl') ) {
				$ch = curl_init('http://login.bulksmsglobal.in/api/balance.php?' . $content);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
				$output = curl_exec($ch);
				curl_close($ch);
			}
			else {
				$output = file_get_contents( 'http://login.bulksmsglobal.in/api/balance.php?' . $content );
			}
			
			if ( is_string($output) && is_array(json_decode($output, true)) ) {
				$output = json_decode($output);
				if ( isset($output->msg) )
					return is_numeric($output->msg) ? self::response($output->msg) : $output->msg;
				else 
					return __( 'Unknown Error.' , 'GF_SMS');
			}
			else {
				return is_numeric($output) ? intval($output) : __( 'Unknown Error.' , 'GF_SMS');
			}
				
        }

        if ($action == "range") {
            $min = 1000;
            $max = 2000;
            return array("min" => $min, "max" => $max);
        }

    }


    public static function response($status)
    {

        $message = '';

        switch ($status) {

            case "101" :
                $message = __("Missing mobile no.", "GF_SMS");
                break;

            case "102" :
                $message = __("Missing message", "GF_SMS");
                break;

            case "103" :
                $message = __("Missing sender ID", "GF_SMS");
                break;

            case "105" :
                $message = __("Missing password", "GF_SMS");
                break;

            case "106" :
                $message = __("Missing Authentication Key", "GF_SMS");
                break;

            case "107" :
                $message = __("Missing Route", "GF_SMS");
                break;

            case "202" :
                $message = __("Invalid mobile number. You must have entered either less than 10 digits or there is an alphabetic character in the mobile number field in API.", "GF_SMS");
                break;

            case "203" :
                $message = __("Invalid sender ID. Your sender ID must be 6 characters, alphabetic.", "GF_SMS");
                break;
				
            case "207" :
                $message = __("Invalid authentication key. Crosscheck your authentication key from your account’s API section.", "GF_SMS");
                break;
				
            case "208" :
                $message = __("IP is blacklisted. We are getting SMS submission requests other than your whitelisted IP list.", "GF_SMS");
                break;
				
            case "301" :
                $message = __("Insufficient balance to send SMS", "GF_SMS");
                break;
								
            case "302" :
                $message = __("Expired user account. You need to contact your account manager.", "GF_SMS");
                break;
								
            case "303" :
                $message = __("Banned user account", "GF_SMS");
                break;
								
            case "306" :
                $message = __("This route is currently unavailable. You can send SMS from this route only between 9 AM - 9 PM.", "GF_SMS");
                break;	
				
            case "307" :
                $message = __("Incorrect scheduled time", "GF_SMS");
                break;
								
            case "308" :
                $message = __("Campaign name cannot be greater than 32 characters", "GF_SMS");
                break;
								
            case "309" :
                $message = __("Selected group(s) does not belong to you", "GF_SMS");
                break;
								
            case "310" :
                $message = __("SMS is too long. System paused this request automatically.", "GF_SMS");
                break;
								
            case "311" :
                $message = __("Request discarded because same request was generated twice within 10 seconds", "GF_SMS");
                break;

        }

        return $message;
    }
}