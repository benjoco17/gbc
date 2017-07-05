<?php
//Select the proper function according to your site settings	
include ( 'function/checkSendPorts.php');
if(fsockopenTest() == 5)
{
	//Test fsockopen function
	include ( 'function/fsockopen.php');
}
elseif(curlTest() == 5)
{
	//Test curl function
	include ( 'function/curl.php');
}
elseif(fopenTest() == 3)
{
	//Test fopen function
	include ( 'function/fopen.php');
}
elseif(fileTest() == 3)
{
	//Test curl function
	include ( 'function/file.php');
}
elseif(filegetcontentsTest() == 3)
{
	//Test fopen function
	include ( 'function/filegetcontents.php');
}
else
{
	//end process, in case there is no method available
	echo "There is no method available<br>Please contact your server supput to activate one of those methods: fsockopen or curlSMS or fopen or file or fopenSMS";
}

//This variable is used in case the result of API was undefined
global $undefinedResult;
$undefinedResult = "The result of the operation is undefined, please try again";

//Results of check sending status API, in text format
global $arraySendStatus;
$arraySendStatus = array();
$arraySendStatus[0] = "We are sory, you can't send now";
$arraySendStatus[1] = "You can send the message now";

//Change password notifications 
global $arrayChangePassword;
$arrayChangePassword = array();
$arrayChangePassword[0] = "Connection failed to Mobily.ws server";
$arrayChangePassword[1] = "Your mobile number (userName) is Invalid";
$arrayChangePassword[2] = "Invalid password";
$arrayChangePassword[3] = "Your password has been changed successfully";

//Results of forgot Password API, in text format
global $arrayForgetPassword;
$arrayForgetPassword = array();
$arrayForgetPassword[0] = "Connection failed to Mobily.ws server";
$arrayForgetPassword[1] = "Your mobile number (userName) is Invalid";
$arrayForgetPassword[2] = "Your email is unavailable";
$arrayForgetPassword[3] = "The password has been sent to your mobile number successfully"; 
$arrayForgetPassword[4] = "Your balance is not enough to send the new password as SMS";
$arrayForgetPassword[5] = "The password has been sent to your email successfully";
$arrayForgetPassword[6] = "Your mobile number (userName) is Invalid";
$arrayForgetPassword[7] = "Your email is incorrect";

//Results of Send SMS API, in text format
global $arraySendMsg;
$arraySendMsg = array();
$arraySendMsg[0] = "Connection failed to Mobily.ws server";
$arraySendMsg[1] = "SMS message sent successfully";
$arraySendMsg[2] = "Your balance is 0";
$arraySendMsg[3] = "Your balance is not enough";
$arraySendMsg[4] = "Your mobile number (userName) is Invalid";
$arraySendMsg[5] = "Your Password is incorrect";
$arraySendMsg[6] = "Sms send operation failed, try again later";
$arraySendMsg[7] = "The schools system is unavailable";
$arraySendMsg[8] = "Repetition of the school code for the same user";
$arraySendMsg[9] = "Trial version is expired ";
$arraySendMsg[10] = "The count of mobile number does not match the count of messages";
$arraySendMsg[11] = "Your subscription does not allow you to send messages to this school";
$arraySendMsg[12] = "Incorrect portal version";
$arraySendMsg[13] = "Your number does not active or the (BS) symbol is missing in the end of the message";
$arraySendMsg[14] = "Sender Name not accepted, or you not authorized to perform this action";
$arraySendMsg[15] = "Number(s) is empty or incorrect";
$arraySendMsg[16] = "Sender Name is empty or invalid";
$arraySendMsg[17] = "Incorrect message encode";
$arraySendMsg[18] = "Sending stoped from the provider";
$arraySendMsg[19] = "No applicationType";

global $arrayDeleteSMS;
$arrayDeleteSMS = array();
$arrayDeleteSMS[1] = "Deleted successfully";
$arrayDeleteSMS[2] = "Your mobile number (userName) is Invalid";
$arrayDeleteSMS[3] = "Invalid Password";
$arrayDeleteSMS[4] = "Invalid deleteKey";

//Balance request notifications
global $arrayBalance;
$arrayBalance = array();
$arrayBalance[0] = "Connection failed to Mobily.ws server";
$arrayBalance[1] = "Your mobile number (userName) is Invalid";
$arrayBalance[2] = "Invalid Password";
$arrayBalance[3] = "Your balance is %s point(s) from %s point(s)";

//validate sender name request notifications-Alphabet
global $arrayCheckAlphasSender;
$arrayCheckAlphasSender = array();
$arrayCheckAlphasSender[0] = "Connection failed to Mobily.ws server";
$arrayCheckAlphasSender[1] = "Your mobile number (userName) is Invalid";
$arrayCheckAlphasSender[2] = "Invalid Password";

//request the sender name notifications-Alphabet
global $arrayAddAlphaSender;
$arrayAddAlphaSender = array();
$arrayAddAlphaSender[0] = "Connection failed to Mobily.ws server";
$arrayAddAlphaSender[1] = "Your mobile number (userName) is Invalid";
$arrayAddAlphaSender[2] = "Invalid Password";
$arrayAddAlphaSender[3] = "The length of the sender name is greater than 11 character";
$arrayAddAlphaSender[4] = "Your request has been added successfully";

//request the sender name notifications-mobile number
global $arrayAddSender;
$arrayAddSender = array();
$arrayAddSender[0] = "Connection failed to Mobily.ws server";
$arrayAddSender[1] = "Your mobile number (userName) is Invalid";
$arrayAddSender[2] = "Invalid Password";
$arrayAddSender[3] = "Sender name 'international number' is incorrect";
$arrayAddSender[4] = "Your name does not need to activate! ";
$arrayAddSender[5] = "Your balance is not enough to send the activation code";
$arrayAddSender[6] = "Invalid Password";

//validate the activation request for the sender name notifications-mobile number
global $arrayCheckSender;
$arrayCheckSender = array();
$arrayCheckSender[0] = "Sender name is not activated";
$arrayCheckSender[1] = "Sender name is activated";
$arrayCheckSender[2] = "Sender name rejected";
$arrayCheckSender[3] = "Your mobile number (userName) is Invalid";
$arrayCheckSender[4] = "Invalid Password";
$arrayCheckSender[5] = "Invalid senderId";

//activation sender name request notifications-mobile number
global $arrayActiveSender;
$arrayActiveSender = array();
$arrayActiveSender[0] = "Connection failed to Mobily.ws server";
$arrayActiveSender[1] = "Your mobile number (userName) is Invalid";
$arrayActiveSender[2] = "Invalid Password";
$arrayActiveSender[3] = "Sender name is activated";
$arrayActiveSender[4] = "Invalid activation code";
$arrayActiveSender[5] = "Invalid senderId";

//The Send template notifications
global $arraySendMsgWK;
$arraySendMsgWK = array();
$arraySendMsgWK[0] = "Connection failed to Mobily.ws server";
$arraySendMsgWK[1] = "Successful send operation";
$arraySendMsgWK[2] = "Your balance is 0";
$arraySendMsgWK[3] = "Your balance is not enough";
$arraySendMsgWK[4] = "Your mobile number (userName) is Invalid";
$arraySendMsgWK[5] = "Your Password is incorrect";
$arraySendMsgWK[6] = "Sms send operation failed, try again later";
$arraySendMsgWK[7] = "The schools system is unactivated";
$arraySendMsgWK[8] = "Repetition of the school code for the same user";
$arraySendMsgWK[9] = "Trial version is expired";
$arraySendMsgWK[10] = "The count of mobile number does not match the count of messages";
$arraySendMsgWK[11] = "Your subscription does not allow you to send messages to this school";
$arraySendMsgWK[12] = "Incorrect portal version";
$arraySendMsgWK[13] = "Your number does not active or the (BS) symbol is missing in the end of the message";
$arraySendMsgWK[14] = "Sender Name not accepted, or you not authorized to perform this action";
$arraySendMsgWK[15] = "Number(s) is empty or incorrect";
$arraySendMsgWK[16] = "Sender Name is empty or invalid";
$arraySendMsgWK[17] = "Incorrect message encode";
$arraySendMsgWK[18] = "Sending stoped from the provider";
$arraySendMsgWK[19] = "No applicationType";

//Include Function: printStringResult, to print the result of API in meaningful way.
include ( 'function/functionPrintResult.php');

//Include Function: convertToUnicode, to convert messages to our special UNICODE. 
include ( 'function/functionUnicode.php');
?>