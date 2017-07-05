<?php
/*
Plugin Name: SMSGlobal – SMS Plugin
Plugin URI: http://URI_Of_Page_Describing_Plugin_and_Updates
Description: Automated SMS messages can now be sent from Wordpress with the use of <a href="http://www.smsglobal.com/en-au/" > SMSGlobal </a>, an international SMS Gateway. This plugin allows <a href="http://www.smsglobal.com/en-au/solutions/signup_page.php">Sign up </a> for your free account at SMSGlobal to start sending messages today.
Version: 1.1
Author: SMSGlobal
Author URI: http://www.smsglobal.com/en-au/
License: 

	Copyright (C) 2011  SMSGlobal

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
*/

function my_init() {
    global $user_ID;
    wp_enqueue_script('jquery');
    wp_enqueue_script('my_script', WP_PLUGIN_URL .' /smsglobal-sms-plugin/checkLength.js', array('jquery'), '1.0', true);

    if(get_usermeta($user_ID, 'sendBlogAlerts') != ""){
        add_filter('manage_posts_columns', 'add_send_column');
        add_action('manage_posts_custom_column',  'add_send_column_content');
    }
}
add_action('init', 'my_init');
require_once ('dbfunctions.php');

add_action('admin_menu', 'add_menu');

//creates left-menu - SMS Alert
function add_menu() {
   add_menu_page('SMS Alert', 'Send SMS ', 8, __FILE__, 'sms_main_control', WP_PLUGIN_URL . '/smsglobal-sms-plugin/smsglobal.png');
   add_submenu_page(__FILE__, 'Settings', 'SMS Settings', 8, 'Settings', 'display_settings');
//   add_submenu_page(__FILE__, 'Add Contact', 'Add Contact', 8, 'Add_Contact', 'add_contact_');
}

// Add main control box
function sms_main_control(){
        add_meta_box("send_sms_box", "SMS Control Panel", "sms_meta_box", "sms"); ?>

        <div class="wrap">
                <h2><?php _e('SMS Control Panel') ?></h2>
                <div id="dashboard-widgets-wrap">
                        <div class="metabox-holder">
                                <div style="float:left; width:48%;" class="inner-sidebar1">
                                        <?php do_meta_boxes('sms','advanced','');  ?>
                                </div>
                        </div>
                </div>
        </div>
<?php }

// Add Send SMS meta box (wordpress css)
function sms_meta_box(){
    global $user_ID;
  ?>
    <div style="padding: 10px;">

<?php
    // if user has pressed Send Message - send message
    if(isset($_POST['user_type'])){
        if($_POST['user_type'] != "none"){
            $users_list = getAllUsersWithRole($_POST['user_type']);
            $phone_numbers = getPhoneNumbers($users_list);
        }
        else{
            $phone_numbers = $_POST['ph_numbers'];
        }
        $message = $_POST['message'];
        $r_message = send_SMS($phone_numbers, $message);
        echo $r_message;
    }
    //else display the form
    else { ?>
        <form name='send_sms_form' id='send_sms_form' method='POST' action='<?= "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'] . "?" . $_SERVER['QUERY_STRING'] ?>'>
            <font size="1px">Send SMS from Wordpress, type in the numbers separated by a comma, or select a group from the drop down.</font><br /><br /><br />
        	<table width="100%" cellpadding="2" cellspacing="2" border="0">
                <tr>
                	<td width="200">Sender ID</td>
                    <td><strong><?php echo getSender_ID($user_ID); ?></strong></td>
                </tr>
                <tr>
                	<td><font size="2"><br/>Mobile Numbers</font></td>
                    <td><input name="ph_numbers" id="ph_numbers" type="text" size="44" /></td>
                </tr>
                <tr>
                	<td><strong><em>or</em></strong> Select group</td>
                    <td>
                    	<select name="user_type" id="user_type">
                            <option value="none">None</option>
                            <option value="all">All Users</option>
                            <option value="administrator">Admins</option>
                            <option value="editor">Editors</option>
                            <option value="author">Authors</option>
                            <option value="contributor">Contributors</option>
                            <option value="subscriber">Subscriber</option>
                        </select>
                    </td>
                </tr>
                <tr>
                	<td valign="top"><br/>Messages</td>
                    <td>
                    	<textarea onKeyUp="updateTextBoxCounter(this.form);" name="message" rows="6" cols="30"></textarea>
                        <DIV ID="InfoCharCounter"></DIV>
                    </td>         
                </tr>
                <tr>
                	<td>&nbsp;</td>
                    <td><span class="submit"><input type="submit" value="Send Message" /></span></td>
                </tr>
            </table>
        </form>
    <?php } ?>
    </div>
<?php } 

// Mobile number fields and columns
add_filter('user_contactmethods','contact_mobile',10,1);
add_action('manage_users_custom_column', 'get_mobile_number', 15, 3);
add_filter('manage_users_columns', 'mobile_column', 15, 1);

//display Post Alert form and SMSGlobal Settings form
function display_settings(){
    add_meta_box("settings_box", "Post Alert Settings", "blog_settings", "blog_s");
    add_meta_box("settings_box", "User Settings", "user_settings", "user_s"); ?>
    <div class="wrap">
            <h2><?php _e('SMS Settings') ?></h2>
            <div id="dashboard-widgets-wrap">
                    <div class="metabox-holder">
                            <div style="float:left; width:48%;" class="inner-sidebar1">
                                    <?php do_meta_boxes('user_s','advanced','');  ?>
                                    <?php do_meta_boxes('blog_s','advanced','');  ?>
                            </div>
                    </div>
            </div>
    </div>
<?php  }

// User settings form and store settings
function user_settings(){
    global $user_ID;
    $username = getUsername_s($user_ID);
    $password = getPassword_s($user_ID);
    $sender_id = getSender_ID($user_ID);

    if(isset($_POST['username_s'])){
        $username = $_POST['username_s'];
        $password = $_POST['password_s'];
        $sender_id = $_POST['sender_id'];
        update($user_ID, $username, $password, $sender_id);
    }?>

    <form name='user_settings' id='blog_settings' method='POST' action='<?= "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'] . "?" . $_SERVER['QUERY_STRING'] ?>'>
   	<div style="padding: 5px; font-size:11px">SMSGlobal Wordpress plugin requires a free SMSGlobal account. If you don't have an account please <a href="http://www.smsglobal.com" target="_blank">sign up now</a>.<br /><br /></div>
    <table width="100%" cellpadding="5" cellspacing="5" border="0">
		<tr>
			<td colspan="2"><strong>SMSGlobal Settings</strong></td>
		</tr>
        <tr>
        	<td width="100">Username</td>
            <td><input type="text" id="username_s" name="username_s" value="<?php echo $username;?>"></td>
        </tr>
        <tr>
        	<td>Password</td>
            <td><input type="password" id="password_s" name="password_s" value="<?php echo $password;?>"></td>
        </tr>
        <tr>
        	<td>Sender ID</td>
            <td><input type="text" id="sender_id" name="sender_id" value="<?php echo $sender_id;?>"></td>
        </tr>
        <tr>
        	<td>&nbsp;</td>
            <td><span class="submit"><input type="submit" value="Save Changes" /></span></td>
        </tr>
	</table>
    </form>
<?php }

// Post alert settings and store data
function blog_settings(){
  //declaring variables
  global $user_ID;
  $send_blog_alerts = '';
  $send_alert = get_usermeta($user_ID, 'sendBlogAlerts');
  $roles_list = "";

  // to add meta data to the table
  if($send_alert == ""){
      if(add_user_meta($user_ID, "sendBlogAlerts", 0,true)){
          if(add_user_meta($user_ID, "roles_send_list", " ", true)){
                $send_blog_alerts = '0';
          }
      }
  }
  //if data is in the table
  else{
      if($send_alert == 1){
          $roles_list = get_usermeta($user_ID, 'roles_send_list');
          $send_blog_alerts = '1';
      }
      if($send_alert == 0){
          $send_blog_alerts = '0';
      }
  }

  //Changes are made
  if(isset($_POST['blog_alert']) ){
      $roles_list="";
      $send_blog_alerts = $_POST['blog_alert'];

      foreach($_POST as $i=>$value){
          if($i == "blog_alert")
              update_usermeta($user_ID, 'sendBlogAlerts', $_POST['blog_alert']);
          else {
              $roles_list .= $i." ";
          }
     }
     update_usermeta($user_ID, 'roles_send_list', $roles_list);
  }

  ?>
 <form name='blog_settings' id='blog_settings' method='POST' action='<?= "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'] . "?" . $_SERVER['QUERY_STRING'] ?>'>
     <table width="100%" cellpadding="5" cellspacing="5" border="0">
        <tr>
        	<td width="200">Send Post Alerts</td>
            <td>
            	<select name="blog_alert" id="blog_alert">
                    <option value="1" name="op1" <?php if($send_blog_alerts=='1') echo ' selected="selected"'; ?>>Yes</option>
                    <option value="0" name="op2" <?php if($send_blog_alerts=='0') echo ' selected="selected"'; ?>>No</option>
                 </select>
            </td>
        </tr>
        <tr>
        	<td valign="top"><br />Select Groups</td>
            <td>
                <br />
            	<label><input id="box1" type="checkbox" name="all"  <?php if((strlen(strstr($roles_list, "all")) > 0)) echo 'checked'; ?><?php if($send_blog_alerts=='0') echo 'disabled'; ?> > All Users </label>
				<br /><br /> <b>or</b>
                <br /><br /> <label><input class="box2" type="checkbox" name="administrator" value ="administrator" <?php if((strlen(strstr($roles_list, "administrator")) > 0)) echo 'checked'; ?> <?php if($send_blog_alerts=='0') echo 'disabled'; ?> > Admins</label>
                <br /><br /> <label><input class="box2" type="checkbox" name="editor" value="editor" <?php if((strlen(strstr($roles_list, "editor")) > 0)) echo 'checked'; ?> <?php if($send_blog_alerts=='0') echo 'disabled'; ?> > Editors</label>
                <br /><br /> <label><input class="box2" type="checkbox" name="author" value ="author" <?php if((strlen(strstr($roles_list, "author")) > 0)) echo 'checked'; ?> <?php if($send_blog_alerts=='0') echo 'disabled'; ?> > Authors</label>
                <br /><br /> <label><input class="box2" type="checkbox" name="contributor" value ="contributor" <?php if((strlen(strstr($roles_list, "contributor")) > 0)) echo 'checked'; ?> <?php if($send_blog_alerts=='0') echo 'disabled'; ?> > Contributors</label>
                <br /><br /> <label><input class="box2" type="checkbox" name="subscriber" value ="subscriber" <?php if((strlen(strstr($roles_list, "subscriber")) > 0)) echo 'checked'; ?> <?php if($send_blog_alerts=='0') echo 'disabled'; ?> > Subscriber</label>
            </td>
        </tr>
        <tr>
        	<td>&nbsp;</td>
            <td><br/><span class="submit"><input type="submit" value="Save Changes" /></span></td>
        </tr>
	</table>
    </form>
<?php }

// Add column name Send Post Alert
function add_send_column($columns) {
    $columns['send_p_alert'] = 'Send Post Alert';
    return $columns;
}

//Add "Send SMS" content and Send Messages
function add_send_column_content($name) {
    global $post;
    global $user_ID;
    $phoneNumbers = "";


    if(isset($_REQUEST['id']) && $_REQUEST['id'] != ""){
        $temp_post =  get_post($_REQUEST['id']);

        unset($_REQUEST['id']);

        $title = $temp_post->post_title;

        $roles_list = get_usermeta($user_ID, 'roles_send_list');
        $roles = explode(" ", $roles_list);
        $count = 0;
       // print_r ($roles);
        foreach($roles as $role){
            $users_list = getAllUsersWithRole($role);
            $tempNumbers = getPhoneNumbers($users_list);
            if($tempNumbers != ""){
                if($count != 0){
                    $phoneNumbers.= ", ".$tempNumbers;
                }
                else{
                    $phoneNumbers.= $tempNumbers;
                    $count = $count + 1;
                }
            }
        }
        
        $message = "New Post: \"".$title. "\"";
        $r_message = send_SMS($phoneNumbers, $message);
        $phoneNumbers = "";
    }

    switch ($name) {
        case 'send_p_alert':
            $click_sms = "<a href='http://" . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF']. "?"."id=".$post->ID."' id='click_sms'>Send SMS</a>";
            echo $click_sms;
    }
}
?>