<?php
//Check Send Status API using CURL method 
if ( !function_exists('sendStatus') ) {
function sendStatus($viewResult=1)
{
	global $arraySendStatus;	
	$url = "www.mobily.ws/api/sendStatus.php";
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_TIMEOUT, 5);
	$result = curl_exec($ch);

	if($viewResult)
		$result = printStringResult(trim($result) , $arraySendStatus);
	return $result;
}}

//change password function using CURL
if ( !function_exists('changePassword') ) {
function changePassword($userAccount, $passAccount, $newPassAccount, $viewResult=1)
{
	global $arrayAddAlphaSender;
	$url = "www.mobily.ws/api/changePassword.php";
	$stringToPost = "mobile=".$userAccount."&password=".$passAccount."&newPassword=".$newPassAccount;

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_TIMEOUT, 5);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $stringToPost);
	$result = curl_exec($ch);
	
	if($viewResult)
		$result = printStringResult(trim($result) , $arrayChangePassword);
	return $result;
}}

//Retrieve your password API using CURL method
if ( !function_exists('forgetPassword') ) {
function forgetPassword($userAccount, $sendType, $viewResult=1)
{
	global $arrayForgetPassword;
	$url = "http://www.mobily.ws/api/forgetPassword.php";
	$stringToPost = "mobile=".$userAccount."&type=".$sendType;
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_TIMEOUT, 5);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $stringToPost);
	$result = curl_exec($ch);
	
	if($viewResult)
		$result = printStringResult(trim($result) , $arrayForgetPassword);
	return $result;
}}

//Check your Balance API using CURL method
if ( !function_exists('balanceSmsMobily') ) {
function balanceSmsMobily($userAccount, $passAccount, $viewResult=1)
{
	global $arrayBalance;
	$url = "http://www.mobily.ws/api/balance.php";
	$stringToPost = "mobile=".$userAccount."&password=".$passAccount;
	
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_TIMEOUT, 5);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $stringToPost);
	$result = curl_exec($ch);

	if($viewResult)
		$result = printStringResult(trim($result), $arrayBalance, 'Balance');
	return $result;
}}

//Send SMS API using CURL method
if ( !function_exists('sendSmsMobily') ) {
function sendSmsMobily($userAccount, $passAccount, $numbers, $sender, $msg, $MsgID, $timeSend=0, $dateSend=0, $deleteKey=0, $viewResult=1)
{
	global $arraySendMsg;
	$url = "www.mobily.ws/api/msgSend.php";
	$applicationType = "24";  
    $msg = convertToUnicode($msg);
	$sender = urlencode($sender);
	$domainName = $_SERVER['SERVER_NAME'];
	$stringToPost = "mobile=".$userAccount."&password=".$passAccount."&numbers=".$numbers."&sender=".$sender."&msg=".$msg."&timeSend=".$timeSend."&dateSend=".$dateSend."&applicationType=".$applicationType."&domainName=".$domainName."&msgId=".$MsgID."&deleteKey=".$deleteKey;

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_TIMEOUT, 5);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $stringToPost);
	$result = curl_exec($ch);

	if($viewResult)
		$result = printStringResult(trim($result) , $arraySendMsg);
	return $result;
}}

//Send Formatted SMS API using CURL method
if ( !function_exists('sendSmsMobilyWK') ) {
function sendSmsMobilyWK($userAccount, $passAccount, $numbers, $sender, $msg, $msgKey, $MsgID, $timeSend=0, $dateSend=0, $deleteKey=0, $viewResult=1)
{
	global $arraySendMsgWK;
	$url = "www.mobily.ws/api/msgSendWK.php";
	$applicationType = "24";  
    $msg = convertToUnicode($msg);
	$msgKey = convertToUnicode($msgKey);
	$sender = urlencode($sender);
	$domainName = $_SERVER['SERVER_NAME'];
	$stringToPost = "mobile=".$userAccount."&password=".$passAccount."&numbers=".$numbers."&sender=".$sender."&msg=".$msg."&msgKey=".$msgKey."&timeSend=".$timeSend."&dateSend=".$dateSend."&applicationType=".$applicationType."&domainName=".$domainName."&msgId=".$MsgID."&deleteKey=".$deleteKey;

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_TIMEOUT, 5);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $stringToPost);
	$result = curl_exec($ch);

	if($viewResult)
		$result = printStringResult(trim($result) , $arraySendMsgWK);
	return $result;
}}

//Delete messages using CURL
if ( !function_exists('deleteSMS') ) {
function deleteSMS($userAccount, $passAccount, $deleteKey=0, $viewResult=1)
{
	global $arrayDeleteSMS;
	$url = "www.mobily.ws/api/deleteMsg.php";
	$stringToPost = "mobile=".$userAccount."&password=".$passAccount."&deleteKey=".$deleteKey;

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_TIMEOUT, 5);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $stringToPost);
	$result = curl_exec($ch);

	if($viewResult)
		$result = printStringResult(trim($result) , $arrayDeleteSMS);
	return $result;
}}

//Sender name request(mobile number) function using CURL
if ( !function_exists('addSender') ) {
function addSender($userAccount, $passAccount, $sender, $viewResult=1)
{	
	global $arrayAddSender;
	$url = "www.mobily.ws/api/addSender.php";
	$stringToPost = "mobile=".$userAccount."&password=".$passAccount."&sender=".$sender;

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_TIMEOUT, 5);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $stringToPost);
	$result = curl_exec($ch);

	if($viewResult)
		$result = printStringResult(trim($result), $arrayAddSender, 'Normal');
	return $result;
}}

//Activate sender name(mobile number) using CURL
if ( !function_exists('activeSender') ) {
function activeSender($userAccount, $passAccount, $senderId, $activeKey, $viewResult=1)
{
	global $arrayActiveSender;
	$url = "www.mobily.ws/api/activeSender.php";
	$stringToPost = "mobile=".$userAccount."&password=".$passAccount."&senderId=".$senderId."&activeKey=".$activeKey;

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_TIMEOUT, 5);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $stringToPost);
	$result = curl_exec($ch);

	if($viewResult)
		$result = printStringResult(trim($result) , $arrayActiveSender);
	return $result;
}}

//Validate sender name request(mobile number) using CURL
if ( !function_exists('checkSender') ) {
function checkSender($userAccount, $passAccount, $senderId, $viewResult=1)
{	
	global $arrayCheckSender;
	$url = "www.mobily.ws/api/checkSender.php";
	$stringToPost = "mobile=".$userAccount."&password=".$passAccount."&senderId=".$senderId;
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_TIMEOUT, 5);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $stringToPost);
	$result = curl_exec($ch);
	if($viewResult)
		$result = printStringResult(trim($result) , $arrayCheckSender);
	return $result;
}}

//Sender name request(As Characters) function using CURL
if ( !function_exists('addAlphaSender') ) {
function addAlphaSender($userAccount, $passAccount, $sender, $viewResult=1)
{
	global $arrayAddAlphaSender;
	$url = "www.mobily.ws/api/addAlphaSender.php";
	$stringToPost = "mobile=".$userAccount."&password=".$passAccount."&sender=".$sender;

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_TIMEOUT, 5);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $stringToPost);
	$result = curl_exec($ch);

	if($viewResult)
		$result = printStringResult(trim($result) , $arrayAddAlphaSender);
	return $result;
}}

//Validate sender name request(As Characters) using CURL
if ( !function_exists('checkAlphasSender') ) {
function checkAlphasSender($userAccount, $passAccount, $viewResult=1)
{
	global $arrayCheckAlphasSender;
	$url = "www.mobily.ws/api/checkAlphasSender.php";
	$stringToPost = "mobile=".$userAccount."&password=".$passAccount;

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_TIMEOUT, 5);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $stringToPost);
	$result = curl_exec($ch);
	
	if($viewResult)
		$result = printStringResult(trim($result) , $arrayCheckAlphasSender, 'Senders');
	return $result;
}}
?>