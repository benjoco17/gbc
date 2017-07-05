<?php
if (!class_exists('Ihc_User_Logs')):
	
class Ihc_User_Logs{
	private static $user_id = '';
	private static $lid = '';
	private static $module_enabled = array('payments'=>FALSE, 'general'=>FALSE);
	private static $update_uid_later_arr = array();
	private static $update_lid_later_arr = array();
	
	/*
	 * @param none
	 * @return none
	 */
	public function __construct(){
		self::$module_enabled['payments'] = get_option('ihc_payment_logs_on');
		self::$module_enabled['user_logs'] = get_option('ihc_user_reports_enabled');
	}
	
	
	/*
	 * @param int($uid)
	 * @return none
	 */
	public static function set_user_id($uid=0){
		if ($uid){
			self::$user_id = $uid;
			
			/// do update previous entries
			if (!empty(self::$update_uid_later_arr)){
				foreach (self::$update_uid_later_arr as $id){
					self::do_update_row($id, 'uid', $uid);
				}
			}
		}
	}
	
	
	/*
	 * @param int($lid)
	 * @return none
	 */
	public static function set_level_id($lid=FALSE){
		if ($lid!==FALSE){
			self::$lid = $lid;
			
			/// do update previous entries
			if (!empty(self::$update_lid_later_arr)){
				foreach (self::$update_uid_later_arr as $id){
					self::do_update_row($id, 'lid', $lid);
				}
			}			
		}
	}
	
		
	/*
	 * @param string ($message)
	 * @param string ($type can be 'payments' 'user_logs' )
	 * @return none
	 */
	public static function write_log($message='', $type=''){
		global $wpdb;
		if (!self::$module_enabled[$type]){
			return;
		}
		if (empty(self::$user_id)){
			self::$user_id = get_current_user_id();	
		}
		
		$table = $wpdb->prefix . 'ihc_user_logs';
		if ($message){
			$uid = self::$user_id;
			$lid = self::$lid;
			$now = time();
			$wpdb->query("INSERT INTO $table VALUES(null, '$uid', '$lid', '$type', '$message', '$now');");
			$inserted = $wpdb->insert_id;
			
			if (empty($uid)){
				self::$update_uid_later_arr[] = $inserted;
			}
			if (empty($lid)){
				self::$update_lid_later_arr[] = $inserted;
			}
		}
	}
	
	
	/*
	 * @param string ($type can be 'payments' 'general' )
	 * @param int ($older_then timestamp)
	 * @return none
	 */
	public static function delete_logs($type='', $older_then=''){
		global $wpdb;
		if ($type && $older_then){
			$table = $wpdb->prefix . 'ihc_user_logs';
			$wpdb->query("DELETE FROM $table WHERE log_type='$type' AND create_date<$older_then;");
		}
	}
	
	
	/*
	 * @param string ($type can be 'payments' 'general')
	 * @param int ($uid, 0 means all)
	 * @param int ($offset)
	 * @param int ($limit)
	 * @return array
	 */
	 public static function get_logs($type='', $uid=0, $offset=0, $limit=0){
	 	 global $wpdb;
		 $table = $wpdb->prefix . 'ihc_user_logs';
		 $array = array();
		 if ($type){
		 	$q = "SELECT id,uid, lid, log_content, create_date FROM $table ";
			$q .= " WHERE log_type='$type' ";
			if ($uid){
				$q .= " AND uid=$uid ";
			}
			$q .= "ORDER BY create_date DESC"; // id DESC
			if ($limit){
				$q .= " LIMIT $limit ";
			}
			if ($offset){
				$q .= " OFFSET $offset ";
			}
		 	$data = $wpdb->get_results($q);
			if ($data){
				foreach ($data as $object){
					$array[] = (array)$object;
				}
			}
		 }
		 return $array;
	 }
	 
	 
	/*
	 * @param string ($type can be 'payments' 'general')
	 * @param int ($uid, 0 means all)
	 * @return int
	 */	 
	 public static function get_count_logs($type='', $uid=0){
	 	 global $wpdb;
		 $table = $wpdb->prefix . 'ihc_user_logs';
		 $q = "SELECT COUNT(id) as c FROM $table ";
		 $q .= " WHERE log_type='$type' ";
		 if ($uid){
		 	$q .= " AND uid=$uid ";
		 }
		 $data = $wpdb->get_row($q);
		 if ($data && isset($data->c)){
		 	return $data->c;
		 }
	 	 return 0;
	 }
	 
	 
	 /*
	  * Update log with lid or uid
	  * @param int, string, int
	  * @return none
	  */
	  private static function do_update_row($id=0, $column='', $new_value=0){
	  	 if ($id){
	  	 	global $wpdb;
	  	 	$table = $wpdb->prefix . 'ihc_user_logs';
	  	 	$wpdb->query("UPDATE $table SET $column='$new_value' WHERE id=$id;");
	  	 }
	  }
}
	
endif;
