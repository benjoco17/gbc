<?php
//Check Send Status API using File method
if ( !function_exists('sendStatus') ) {
function sendStatus($viewResult=1)
{
	global $arraySendStatus;
	$contextOptions['http'] = array('method' => 'GET', 'max_redirects'=>0, 'protocol_version'=> 1.0, 'timeout'=>10, 'ignore_errors'=>TRUE);
	$contextResouce  = stream_context_create($contextOptions);
	$url = "http://www.mobily.ws/api/sendStatus.php";
	$arrayResult = file($url, FILE_IGNORE_NEW_LINES, $contextResouce);
	$result = $arrayResult[0];
	
	if($viewResult)
		$result = printStringResult(trim($result), $arraySendStatus);
	return $result;
}}

//Change password function in mobily site using file
if ( !function_exists('changePassword') ) {
function changePassword($userAccount, $passAccount, $newPassAccount, $viewResult=1)
{
	global $arrayChangePassword;
	$contextPostValues = http_build_query(array('mobile'=>$userAccount, 'password'=>$passAccount, 'newPassword'=>$newPassAccount));
	$contextOptions['http'] = array('method' => 'POST', 'header'=>'Content-type: application/x-www-form-urlencoded', 'content'=> $contextPostValues, 'max_redirects'=>0, 'protocol_version'=> 1.0, 'timeout'=>10, 'ignore_errors'=>TRUE);
	$contextResouce  = stream_context_create($contextOptions);
	$url = "http://www.mobily.ws/api/changePassword.php";
	$arrayResult = file($url, FILE_IGNORE_NEW_LINES, $contextResouce);
	$result = $arrayResult[0];
	
	if($viewResult)
		$result = printStringResult(trim($result), $arrayChangePassword);
	return $result;
}}

//Retrieve your password API using File method
if ( !function_exists('forgetPassword') ) {
function forgetPassword($userAccount, $sendType, $viewResult=1)
{
	global $arrayForgetPassword;
	$contextPostValues = http_build_query(array('mobile'=>$userAccount, 'type'=>$sendType));
	$contextOptions['http'] = array('method' => 'POST', 'header'=>'Content-type: application/x-www-form-urlencoded', 'content'=> $contextPostValues, 'max_redirects'=>0, 'protocol_version'=> 1.0, 'timeout'=>10, 'ignore_errors'=>TRUE);
	$contextResouce  = stream_context_create($contextOptions);
	$url = "http://www.mobily.ws/api/forgetPassword.php";
	$arrayResult = file($url, FILE_IGNORE_NEW_LINES, $contextResouce);
	$result = $arrayResult[0];
	
	if($viewResult)
		$result = printStringResult(trim($result), $arrayForgetPassword);
	return $result;
}}

//Check your Balance API using File method
if ( !function_exists('balanceSmsMobily') ) {
function balanceSmsMobily($userAccount, $passAccount, $viewResult=1)
{
	global $arrayBalance;
	$contextPostValues = http_build_query(array('mobile'=>$userAccount, 'password'=>$passAccount));
	$contextOptions['http'] = array('method' => 'POST', 'header'=>'Content-type: application/x-www-form-urlencoded', 'content'=> $contextPostValues, 'max_redirects'=>0, 'protocol_version'=> 1.0, 'timeout'=>10, 'ignore_errors'=>TRUE);
	$contextResouce  = stream_context_create($contextOptions);
	$url = "http://www.mobily.ws/api/balance.php";
	$arrayResult = file($url, FILE_IGNORE_NEW_LINES, $contextResouce);
	$result = $arrayResult[0];

	if($viewResult)
		$result = printStringResult(trim($result), $arrayBalance, 'Balance');
	return $result;
}}

//Send SMS API using File method
if ( !function_exists('sendSmsMobily') ) {
function sendSmsMobily($userAccount, $passAccount, $numbers, $sender, $msg, $MsgID, $timeSend=0, $dateSend=0, $deleteKey=0, $viewResult=1)
{
	global $arraySendMsg;
	$applicationType = "24";  
    $msg = convertToUnicode($msg);
	$sender = urlencode($sender);
	$domainName = $_SERVER['SERVER_NAME'];
	$contextPostValues = http_build_query(array('mobile'=>$userAccount, 'password'=>$passAccount, 'numbers'=>$numbers, 'sender'=>$sender, 'msg'=>$msg, 'timeSend'=>$timeSend, 'dateSend'=>$dateSend, 'applicationType'=>$applicationType, 'domainName'=>$domainName, 'msgId'=>$MsgID, 'deleteKey'=>$deleteKey));
	$contextOptions['http'] = array('method' => 'POST', 'header'=>'Content-type: application/x-www-form-urlencoded', 'content'=> $contextPostValues, 'max_redirects'=>0, 'protocol_version'=> 1.0, 'timeout'=>10, 'ignore_errors'=>TRUE);
	$contextResouce  = stream_context_create($contextOptions);
	$url = "http://www.mobily.ws/api/msgSend.php";
	$arrayResult = file($url, FILE_IGNORE_NEW_LINES, $contextResouce);
	$result = $arrayResult[0];

	if($viewResult)
		$result = printStringResult(trim($result), $arraySendMsg);
	return $result;
}}

//Send Formatted SMS API using File method
if ( !function_exists('sendSmsMobilyWK') ) {
function sendSmsMobilyWK($userAccount, $passAccount, $numbers, $sender, $msg, $msgKey, $MsgID, $timeSend=0, $dateSend=0, $deleteKey=0, $viewResult=1)
{
	global $arraySendMsgWK;
	$applicationType = "24";  
    $msg = convertToUnicode($msg);
	$msgKey = convertToUnicode($msgKey);
	$sender = urlencode($sender);
	$domainName = $_SERVER['SERVER_NAME'];
	$contextPostValues = http_build_query(array('mobile'=>$userAccount, 'password'=>$passAccount, 'numbers'=>$numbers, 'sender'=>$sender, 'msg'=>$msg, 'msgKey'=>$msgKey, 'timeSend'=>$timeSend, 'dateSend'=>$dateSend, 'applicationType'=>$applicationType, 'domainName'=>$domainName, 'msgId'=>$MsgID, 'deleteKey'=>$deleteKey));
	$contextOptions['http'] = array('method' => 'POST', 'header'=>'Content-type: application/x-www-form-urlencoded', 'content'=> $contextPostValues, 'max_redirects'=>0, 'protocol_version'=> 1.0, 'timeout'=>10, 'ignore_errors'=>TRUE);
	$contextResouce  = stream_context_create($contextOptions);
	$url = "http://www.mobily.ws/api/msgSendWK.php";
	$arrayResult = file($url, FILE_IGNORE_NEW_LINES, $contextResouce);
	$result = $arrayResult[0];

	if($viewResult)
		$result = printStringResult(trim($result) , $arraySendMsgWK);
	return $result;
}}

//Delete messages using File method
if ( !function_exists('deleteSMS') ) {
function deleteSMS($userAccount, $passAccount, $deleteKey=0, $viewResult=1)
{
	global $arrayDeleteSMS;
	$contextPostValues = http_build_query(array('mobile'=>$userAccount, 'password'=>$passAccount, 'deleteKey'=>$deleteKey));
	$contextOptions['http'] = array('method' => 'POST', 'header'=>'Content-type: application/x-www-form-urlencoded', 'content'=> $contextPostValues, 'max_redirects'=>0, 'protocol_version'=> 1.0, 'timeout'=>10, 'ignore_errors'=>TRUE);
	$contextResouce  = stream_context_create($contextOptions);
	$url = "http://www.mobily.ws/api/deleteMsg.php";
	$arrayResult = file($url, FILE_IGNORE_NEW_LINES, $contextResouce);
	$result = $arrayResult[0];

	if($viewResult)
		$result = printStringResult(trim($result) , $arrayDeleteSMS);
	return $result;
}}

//Sender name request(mobile number) function using file
if ( !function_exists('addSender') ) {
function addSender($userAccount, $passAccount, $sender, $viewResult=1)
{	
	global $arrayAddSender;
	$contextPostValues = http_build_query(array('mobile'=>$userAccount, 'password'=>$passAccount, 'sender'=>$sender));
	$contextOptions['http'] = array('method' => 'POST', 'header'=>'Content-type: application/x-www-form-urlencoded', 'content'=> $contextPostValues, 'max_redirects'=>0, 'protocol_version'=> 1.0, 'timeout'=>10, 'ignore_errors'=>TRUE);
	$contextResouce  = stream_context_create($contextOptions);
	$url = "http://www.mobily.ws/api/addSender.php";
	$arrayResult = file($url, FILE_IGNORE_NEW_LINES, $contextResouce);
	$result = $arrayResult[0];

	if($viewResult)
		$result = printStringResult(trim($result), $arrayAddSender, 'Normal');
	return $result;
}}

//Activate sender name(mobile number) using file
if ( !function_exists('activeSender') ) {
function activeSender($userAccount, $passAccount, $senderId, $activeKey, $viewResult=1)
{
	global $arrayActiveSender;
	$contextPostValues = http_build_query(array('mobile'=>$userAccount, 'password'=>$passAccount, 'senderId'=>$senderId, 'activeKey'=>$activeKey));
	$contextOptions['http'] = array('method' => 'POST', 'header'=>'Content-type: application/x-www-form-urlencoded', 'content'=> $contextPostValues, 'max_redirects'=>0, 'protocol_version'=> 1.0, 'timeout'=>10, 'ignore_errors'=>TRUE);
	$contextResouce  = stream_context_create($contextOptions);
	$url = "http://www.mobily.ws/api/activeSender.php";
	$arrayResult = file($url, FILE_IGNORE_NEW_LINES, $contextResouce);
	$result = $arrayResult[0];

	if($viewResult)
		$result = printStringResult(trim($result) , $arrayActiveSender);
	return $result;
}}

//Validate sender name request(mobile number) using file
if ( !function_exists('checkSender') ) {
function checkSender($userAccount, $passAccount, $senderId, $viewResult=1)
{	
	global $arrayCheckSender;
	$contextPostValues = http_build_query(array('mobile'=>$userAccount, 'password'=>$passAccount, 'senderId'=>$senderId));
	$contextOptions['http'] = array('method' => 'POST', 'header'=>'Content-type: application/x-www-form-urlencoded', 'content'=> $contextPostValues, 'max_redirects'=>0, 'protocol_version'=> 1.0, 'timeout'=>10, 'ignore_errors'=>TRUE);
	$contextResouce  = stream_context_create($contextOptions);
	$url = "http://www.mobily.ws/api/checkSender.php";
	$arrayResult = file($url, FILE_IGNORE_NEW_LINES, $contextResouce);
	$result = $arrayResult[0];

	if($viewResult)
		$result = printStringResult(trim($result) , $arrayCheckSender);
	return $result;
}}

//Sender name request(As Characters) function using file
if ( !function_exists('addAlphaSender') ) {
function addAlphaSender($userAccount, $passAccount, $sender, $viewResult=1)
{
	global $arrayAddAlphaSender;
	$contextPostValues = http_build_query(array('mobile'=>$userAccount, 'password'=>$passAccount, 'sender'=>$sender));
	$contextOptions['http'] = array('method' => 'POST', 'header'=>'Content-type: application/x-www-form-urlencoded', 'content'=> $contextPostValues, 'max_redirects'=>0, 'protocol_version'=> 1.0, 'timeout'=>10, 'ignore_errors'=>TRUE);
	$contextResouce  = stream_context_create($contextOptions);
	$url = "http://www.mobily.ws/api/addAlphaSender.php";
	$arrayResult = file($url, FILE_IGNORE_NEW_LINES, $contextResouce);
	$result = $arrayResult[0];

	if($viewResult)
		$result = printStringResult(trim($result) , $arrayAddAlphaSender);
	return $result;
}}

//Validate sender name request(As Characters) using file
if ( !function_exists('checkAlphasSender') ) {
function checkAlphasSender($userAccount, $passAccount, $viewResult=1)
{
	global $arrayCheckAlphasSender;
	$contextPostValues = http_build_query(array('mobile'=>$userAccount, 'password'=>$passAccount));
	$contextOptions['http'] = array('method' => 'POST', 'header'=>'Content-type: application/x-www-form-urlencoded', 'content'=> $contextPostValues, 'max_redirects'=>0, 'protocol_version'=> 1.0, 'timeout'=>10, 'ignore_errors'=>TRUE);
	$contextResouce  = stream_context_create($contextOptions);
	$url = "http://www.mobily.ws/api/checkAlphasSender.php";
	$arrayResult = file($url, FILE_IGNORE_NEW_LINES, $contextResouce);
	$result = implode('',$arrayResult);

	if($viewResult)
		$result = printStringResult(trim($result) , $arrayCheckAlphasSender, 'Senders');
	return $result;
}}
?>