<?php

// Returns comma separated phone numbers of list
function getPhoneNumbers($user_list){
    $phone_numbers = "";
    $comma = ", ";
    $size = sizeof($user_list);
    $count = $size;

    foreach($user_list as $s_user){
        $single = true;
        $number = get_usermeta($s_user->ID, 'mobilenumber', $single);

        if($number != ""){
            $phone_numbers .= $number.$comma;
        }
        $count =  $count - 1;
    }
    $phone_numbers = substr($phone_numbers, 0, -2);
    return $phone_numbers;
}

// Removes duplicate numbers and sends message
function send_SMS ($destination, $text){
    //removes duplicate numbers
    $destination = implode(',',array_unique(explode(',', $destination)));
    global $user_ID;
    $username = getUsername_s($user_ID);
    $password = getPassword_s($user_ID);
    $source = getSender_ID($user_ID);

    $content =  'action=sendsms'.
                '&user='.rawurlencode($username).
                '&password='.rawurlencode($password).
                '&to='.rawurlencode($destination).
                '&from='.rawurlencode($source).
                '&text='.rawurlencode($text);

    $smsglobal_response = file_get_contents('http://www.smsglobal.com.au/http-api.php?'.$content);

    //Sample Response
    //OK: 0; Sent queued message ID: 04b4a8d4a5a02176 SMSGlobalMsgID:6613115713715266

    $explode_response = explode('SMSGlobalMsgID:', $smsglobal_response);

    if(count($explode_response) >= 2) { //Message Success
                return "Message Sent"; ;
    } else { //Message Failed
        return "Message Failed";
    }
}

// adds mobile number field to the contact list displayed
function contact_mobile( $contactmethods ) {
  // Add Mobile
  $contactmethods['mobilenumber'] = 'Mobile Number';
  return $contactmethods;
}

//adds the mobile number column to the user table
function mobile_column( $defaults ) {
  $defaults['mobile_column'] = __('Mobile Number', 'user-column');
  return $defaults;
}

// returns the mobile number
function get_mobile_number($value, $column_name, $id) {
  if( $column_name == 'mobile_column' ) {
    if ( current_user_can('edit_users') )
      return get_usermeta($id, 'mobilenumber');
  }
}

//returns list of user roles
function getAllUsersWithRole($role) {
    $args = "";
    if($role != "all"){
        $args = array('role' => $role);
    }
        $wp_user_search = get_users($args);
        return $wp_user_search;
}

//returns SMSGlobal username
function getUsername_s($user_id){
    return get_usermeta($user_id, 'username_s');
}

//returns SMSGlobal password
function getPassword_s($user_id){
    return get_usermeta($user_id, 'password_s');
}

//returns SMSGlobal Sender ID
function getSender_ID($user_id){
    return get_usermeta($user_id, 'sender_id');
}

//Updates SMSGlobal settings
function update($user_id, $username, $password, $sender_id){
    update_user_meta($user_id, "username_s", $username);
    update_user_meta($user_id, "password_s", $password);
    update_user_meta($user_id, "sender_id", $sender_id);
}
?>