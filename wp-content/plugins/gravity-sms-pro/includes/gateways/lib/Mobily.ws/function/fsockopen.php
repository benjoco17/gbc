<?php
//Check Send Status API using fsockopen method 
if ( !function_exists('sendStatus') ) {
function sendStatus($viewResult=1)
{
	global $arraySendStatus;	
	$fsockParameter = "POST /api/sendStatus.php HTTP/1.0 \r\n";
	$fsockParameter.= "Host: www.mobily.ws \r\n";
	$fsockParameter.= "Content-type: application/x-www-form-urlencoded \r\n";
	$fsockParameter.= "Content-length: 0 \r\n\r\n";

	$fsockConn = fsockopen("www.mobily.ws", 80, $errno, $errstr, 5);
	fputs($fsockConn,$fsockParameter);
	
	$result = ""; 
	$clearResult = false; 
	
	while(!feof($fsockConn))
	{
		$line = fgets($fsockConn, 10240);
		if($line == "\r\n" && !$clearResult)
		$clearResult = true;
		
		if($clearResult)
			$result .= trim($line); 
	}

	if($viewResult)
		$result = printStringResult(trim($result) , $arraySendStatus);
	return $result;
}}

//Change password function in mobily site using fsockopen
if ( !function_exists('changePassword') ) {
function changePassword($userAccount, $passAccount, $newPassAccount, $viewResult=1)
{
	global $arrayChangePassword;
	$stringToPost = "mobile=".$userAccount."&password=".$passAccount."&newPassword=".$newPassAccount;
	$stringToPostLength = strlen($stringToPost);
	$fsockParameter = "POST /api/changePassword.php HTTP/1.0 \r\n";
	$fsockParameter.= "Host: www.mobily.ws \r\n";
	$fsockParameter.= "Content-type: application/x-www-form-urlencoded \r\n";
	$fsockParameter.= "Content-length: $stringToPostLength \r\n\r\n";
	$fsockParameter.= "$stringToPost";

	$fsockConn = fsockopen("www.mobily.ws", 80, $errno, $errstr, 5);
	fputs($fsockConn,$fsockParameter);
	
	$result = ""; 
	$clearResult = false; 
	while(!feof($fsockConn))
	{
		$line = fgets($fsockConn, 10240);
		if($line == "\r\n" && !$clearResult)
		$clearResult = true;
		
		if($clearResult)
			$result .= trim($line); 
	}
	
	if($viewResult)
		$result = printStringResult(trim($result) , $arrayChangePassword);
	return $result;
}}

//Retrieve your password API using fsockopen
if ( !function_exists('forgetPassword') ) {
function forgetPassword($userAccount, $sendType, $viewResult=1)
{
	global $arrayForgetPassword;
	$stringToPost = "mobile=".$userAccount."&type=".$sendType;
	$stringToPostLength = strlen($stringToPost);
	$fsockParameter = "POST /api/forgetPassword.php HTTP/1.0 \r\n";
	$fsockParameter.= "Host: www.mobily.ws \r\n";
	$fsockParameter.= "Content-type: application/x-www-form-urlencoded \r\n";
	$fsockParameter.= "Content-length: $stringToPostLength \r\n\r\n";
	$fsockParameter.= "$stringToPost";

	$fsockConn = fsockopen("www.mobily.ws", 80, $errno, $errstr, 5);
	fputs($fsockConn,$fsockParameter);
	
	$result = ""; 
	$clearResult = false; 
	while(!feof($fsockConn))
	{
		$line = fgets($fsockConn, 10240);
		if($line == "\r\n" && !$clearResult)
		$clearResult = true;
		
		if($clearResult)
			$result .= trim($line); 
	}
	
	if($viewResult)
		$result = printStringResult(trim($result) , $arrayForgetPassword);
	return $result;
}}

//Check your Balance API using fsockopen
if ( !function_exists('balanceSmsMobily') ) {
function balanceSmsMobily($userAccount, $passAccount, $viewResult=1)
{
	global $arrayBalance;
	$stringToPost = "mobile=".$userAccount."&password=".$passAccount;
	$stringToPostLength = strlen($stringToPost);
	$fsockParameter = "POST /api/balance.php HTTP/1.0 \r\n";
	$fsockParameter.= "Host: www.mobily.ws \r\n";
	$fsockParameter.= "Content-type: application/x-www-form-urlencoded \r\n";
	$fsockParameter.= "Content-length: $stringToPostLength \r\n\r\n";
	$fsockParameter.= "$stringToPost";

	$fsockConn = fsockopen("www.mobily.ws", 80, $errno, $errstr, 5);
	fputs($fsockConn,$fsockParameter);
	
	$result = ""; 
	$clearResult = false; 
	while(!feof($fsockConn))
	{
		$line = fgets($fsockConn, 10240);
		if($line == "\r\n" && !$clearResult)
		$clearResult = true;
		
		if($clearResult)
			$result .= trim($line); 
	}

	if($viewResult)
		$result = printStringResult(trim($result), $arrayBalance, 'Balance');
	return $result;
}}

//Send SMS API using fsockopen
if ( !function_exists('sendSmsMobily') ) {
function sendSmsMobily($userAccount, $passAccount, $numbers, $sender, $msg, $MsgID, $timeSend=0, $dateSend=0, $deleteKey=0, $viewResult=1)
{
	global $arraySendMsg;
	$applicationType = "24";  
    $msg = convertToUnicode($msg);
	$sender = urlencode($sender);
	$domainName = $_SERVER['SERVER_NAME'];
	$stringToPost = "mobile=".$userAccount."&password=".$passAccount."&numbers=".$numbers."&sender=".$sender."&msg=".$msg."&timeSend=".$timeSend."&dateSend=".$dateSend."&applicationType=".$applicationType."&domainName=".$domainName."&msgId=".$MsgID."&deleteKey=".$deleteKey;
	$stringToPostLength = strlen($stringToPost);
	$fsockParameter = "POST /api/msgSend.php HTTP/1.0 \r\n";
	$fsockParameter.= "Host: www.mobily.ws \r\n";
	$fsockParameter.= "Content-type: application/x-www-form-urlencoded \r\n";
	$fsockParameter.= "Content-length: $stringToPostLength \r\n\r\n";
	$fsockParameter.= "$stringToPost";

	$fsockConn = fsockopen("www.mobily.ws", 80, $errno, $errstr, 30);
	fputs($fsockConn, $fsockParameter);
		
	$result = ""; 
	$clearResult = false; 
	
	while(!feof($fsockConn))
	{
		$line = fgets($fsockConn, 10240);
		if($line == "\r\n" && !$clearResult)
		$clearResult = true;
		
		if($clearResult)
			$result .= trim($line); 
	}

	if($viewResult)
		$result = printStringResult(trim($result) , $arraySendMsg);
	return $result;
}}

//Send Formatted SMS API using fsockopen
if ( !function_exists('sendSmsMobilyWK') ) {
function sendSmsMobilyWK($userAccount, $passAccount, $numbers, $sender, $msg, $msgKey, $MsgID, $timeSend=0, $dateSend=0, $deleteKey=0, $viewResult=1)
{
	global $arraySendMsgWK;
	$applicationType = "24";  
    $msg = convertToUnicode($msg);
	$msgKey = convertToUnicode($msgKey);
	$sender = urlencode($sender);
	$domainName = $_SERVER['SERVER_NAME'];
	$stringToPost = "mobile=".$userAccount."&password=".$passAccount."&numbers=".$numbers."&sender=".$sender."&msg=".$msg."&msgKey=".$msgKey."&timeSend=".$timeSend."&dateSend=".$dateSend."&applicationType=".$applicationType."&domainName=".$domainName."&msgId=".$MsgID."&deleteKey=".$deleteKey;
	$stringToPostLength = strlen($stringToPost);
	$fsockParameter = "POST /api/msgSendWK.php HTTP/1.0 \r\n";
	$fsockParameter.= "Host: www.mobily.ws \r\n";
	$fsockParameter.= "Content-type: application/x-www-form-urlencoded \r\n";
	$fsockParameter.= "Content-length: $stringToPostLength \r\n\r\n";
	$fsockParameter.= "$stringToPost";

	$fsockConn = fsockopen("www.mobily.ws", 80, $errno, $errstr, 30);
	fputs($fsockConn, $fsockParameter);
		
	$result = ""; 
	$clearResult = false; 
	
	while(!feof($fsockConn))
	{
		$line = fgets($fsockConn, 10240);
		if($line == "\r\n" && !$clearResult)
		$clearResult = true;
		
		if($clearResult)
			$result .= trim($line); 
	}

	if($viewResult)
		$result = printStringResult(trim($result) , $arraySendMsgWK);
	return $result;
}}

//Delete messages using fsockopen
if ( !function_exists('deleteSMS') ) {
function deleteSMS($userAccount, $passAccount, $deleteKey=0, $viewResult=1)
{
	global $arrayDeleteSMS;
	$stringToPost = "mobile=".$userAccount."&password=".$passAccount."&deleteKey=".$deleteKey;
	$stringToPostLength = strlen($stringToPost);
	$fsockParameter = "POST /api/deleteMsg.php HTTP/1.0 \r\n";
	$fsockParameter.= "Host: www.mobily.ws \r\n";
	$fsockParameter.= "Content-type: application/x-www-form-urlencoded \r\n";
	$fsockParameter.= "Content-length: $stringToPostLength \r\n\r\n";
	$fsockParameter.= "$stringToPost";

	$fsockConn = fsockopen("www.mobily.ws", 80, $errno, $errstr, 30);
	fputs($fsockConn,$fsockParameter);
	
	$result = ""; 
	$clearResult = false; 
	
	while(!feof($fsockConn))
	{
		$line = fgets($fsockConn, 10240);
		if($line == "\r\n" && !$clearResult)
		$clearResult = true;
		
		if($clearResult)
			$result .= trim($line); 
	}

	if($viewResult)
		$result = printStringResult(trim($result) , $arrayDeleteSMS);
	return $result;
}}

//Sender name request(mobile number) function using fsockopen
if ( !function_exists('addSender') ) {
function addSender($userAccount, $passAccount, $sender, $viewResult=1)
{	
	global $arrayAddSender;
	$stringToPost = "mobile=".$userAccount."&password=".$passAccount."&sender=".$sender;
	$stringToPostLength = strlen($stringToPost);
	$fsockParameter = "POST /api/addSender.php HTTP/1.0 \r\n";
	$fsockParameter.= "Host: www.mobily.ws \r\n";
	$fsockParameter.= "Content-type: application/x-www-form-urlencoded \r\n";
	$fsockParameter.= "Content-length: $stringToPostLength \r\n\r\n";
	$fsockParameter.= "$stringToPost"; 

	$fsockConn = fsockopen("www.mobily.ws", 80, $errno, $errstr, 30);
	fputs($fsockConn,$fsockParameter);
	
	$result = ""; 
	$clearResult = false; 
	
	while(!feof($fsockConn))
	{
		$line = fgets($fsockConn, 10240);
		if($line == "\r\n" && !$clearResult)
		$clearResult = true;
		
		if($clearResult)
			$result .= trim($line); 
	}

	if($viewResult)
		$result = printStringResult(trim($result), $arrayAddSender, 'Normal');
	return $result;
}}

//Activate sender name(mobile number) using fsockopen
if ( !function_exists('activeSender') ) {
function activeSender($userAccount, $passAccount, $senderId, $activeKey, $viewResult=1)
{
	global $arrayActiveSender;
	$stringToPost = "mobile=".$userAccount."&password=".$passAccount."&senderId=".$senderId."&activeKey=".$activeKey;
	$stringToPostLength = strlen($stringToPost);
	$fsockParameter = "POST /api/activeSender.php HTTP/1.0 \r\n";
	$fsockParameter.= "Host: www.mobily.ws \r\n";
	$fsockParameter.= "Content-type: application/x-www-form-urlencoded \r\n";
	$fsockParameter.= "Content-length: $stringToPostLength \r\n";
	$fsockParameter.= "$stringToPost \r\n";

	$fsockConn = fsockopen("www.mobily.ws", 80, $errno, $errstr, 30);
	fputs($fsockConn,$fsockParameter);
	
	$result = ""; 
	$clearResult = false; 
	
	while(!feof($fsockConn))
	{
		$line = fgets($fsockConn, 10240);
		if($line == "\r\n" && !$clearResult)
		$clearResult = true;
		
		if($clearResult)
			$result .= trim($line); 
	}

	if($viewResult)
		$result = printStringResult(trim($result) , $arrayActiveSender);
	return $result;
}}

//Validate sender name request(mobile number) using fsockopen
if ( !function_exists('checkSender') ) {
function checkSender($userAccount, $passAccount, $senderId, $viewResult=1)
{	
	global $arrayCheckSender;
	$stringToPost = "mobile=".$userAccount."&password=".$passAccount."&senderId=".$senderId;
	$stringToPostLength = strlen($stringToPost);
	$fsockParameter = "POST /api/checkSender.php HTTP/1.0 \r\n";
	$fsockParameter.= "Host: www.mobily.ws \r\n";
	$fsockParameter.= "Content-type: application/x-www-form-urlencoded \r\n";
	$fsockParameter.= "Content-length: $stringToPostLength \r\n\r\n";
	$fsockParameter.= "$stringToPost";

	$fsockConn = fsockopen("www.mobily.ws", 80, $errno, $errstr, 30);
	fputs($fsockConn,$fsockParameter);
	
	$result = ""; 
	$clearResult = false; 
	
	while(!feof($fsockConn))
	{
		$line = fgets($fsockConn, 10240);
		if($line == "\r\n" && !$clearResult)
		$clearResult = true;
		
		if($clearResult)
			$result .= trim($line); 
	}

	if($viewResult)
		$result = printStringResult(trim($result) , $arrayCheckSender);
	return $result;
}}

//Sender name request(As Characters) function using fsockopen
if ( !function_exists('addAlphaSender') ) {
function addAlphaSender($userAccount, $passAccount, $sender, $viewResult=1)
{
	global $arrayAddAlphaSender;
	$stringToPost = "mobile=".$userAccount."&password=".$passAccount."&sender=".$sender;
	$stringToPostLength = strlen($stringToPost);
	$fsockParameter = "POST /api/addAlphaSender.php HTTP/1.0 \r\n";
	$fsockParameter.= "Host: www.mobily.ws \r\n";
	$fsockParameter.= "Content-type: application/x-www-form-urlencoded \r\n";
	$fsockParameter.= "Content-length: $stringToPostLength \r\n";
	$fsockParameter.= "$stringToPost \r\n";
		
	$fsockConn = fsockopen("www.mobily.ws", 80, $errno, $errstr, 30);
	fputs($fsockConn,$fsockParameter);
	
	$result = ""; 
	$clearResult = false; 
	
	while(!feof($fsockConn))
	{
		$line = fgets($fsockConn, 10240);
		if($line == "\r\n" && !$clearResult)
		$clearResult = true;
		
		if($clearResult)
			$result .= trim($line); 
	}

	if($viewResult)
		$result = printStringResult(trim($result) , $arrayAddAlphaSender);
	return $result;
}}

//Validate sender name request(As Characters) using fsockopen
if ( !function_exists('checkAlphasSender') ) {
function checkAlphasSender($userAccount, $passAccount, $viewResult=1)
{
	global $arrayCheckAlphasSender;
	$stringToPost = "mobile=".$userAccount."&password=".$passAccount;
	$stringToPostLength = strlen($stringToPost);
	$fsockParameter = "POST /api/checkAlphasSender.php HTTP/1.0 \r\n";
	$fsockParameter.= "Host: www.mobily.ws \r\n";
	$fsockParameter.= "Content-type: application/x-www-form-urlencoded \r\n";
	$fsockParameter.= "Content-length: $stringToPostLength \r\n";
	$fsockParameter.= "$stringToPost \r\n";
		
	$fsockConn = fsockopen("www.mobily.ws", 80, $errno, $errstr, 30);
	fputs($fsockConn,$fsockParameter);
	
	$result = ""; 
	$clearResult = false; 
	
	while(!feof($fsockConn))
	{
		$line = fgets($fsockConn, 10240);
		if($line == "\r\n" && !$clearResult)
		$clearResult = true;
		
		if($clearResult)
			$result .= trim($line); 
	}

	if($viewResult)
		$result = printStringResult(trim($result) , $arrayCheckAlphasSender, 'Senders');
	return $result;
}}
?>