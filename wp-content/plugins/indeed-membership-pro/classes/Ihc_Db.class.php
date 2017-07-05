<?php
if (!class_exists('Ihc_Db')):
	
class Ihc_Db{
	
	public function __construct(){}
	
	public static function create_tables(){
		/*
		 * @param none
		 * @return none
		 */
		global $wpdb;
		$table_name = $wpdb->prefix . "ihc_user_levels";
		if ($wpdb->get_var( "show tables like '$table_name'" ) != $table_name){
			require_once (ABSPATH . 'wp-admin/includes/upgrade.php');
			$sql = "CREATE TABLE " . $table_name . " (
														id int(11) NOT NULL AUTO_INCREMENT,
														user_id int(11) NOT NULL,
														level_id int(11) NOT NULL,
														start_time datetime,
														update_time datetime,
														expire_time datetime,
														notification tinyint(1) DEFAULT 0,
														status int(3) NOT NULL,
														PRIMARY KEY (`id`)
			);
			";
			dbDelta ( $sql );
		}
		//ihc_debug_payments
		$table_name = $wpdb->prefix . "ihc_debug_payments";
		if ($wpdb->get_var( "show tables like '$table_name'" ) != $table_name){
			require_once (ABSPATH . 'wp-admin/includes/upgrade.php');
			$sql = "CREATE TABLE " . $table_name . " (
						id int(11) NOT NULL AUTO_INCREMENT,
						source VARCHAR(200),
						message TEXT,
						insert_time datetime,
						PRIMARY KEY (`id`)
			);";
			dbDelta ( $sql );
		}			
		////////// indeed_members_payments
		$table_name = $wpdb->prefix . 'indeed_members_payments';
		if ($wpdb->get_var( "show tables like '$table_name'" ) != $table_name){
			require_once ABSPATH . 'wp-admin/includes/upgrade.php';
			$sql = "CREATE TABLE " . $table_name . " (
						id int(9) NOT NULL AUTO_INCREMENT PRIMARY KEY,
						txn_id VARCHAR(100) DEFAULT NULL,
						u_id int(9) DEFAULT NULL,
						payment_data text DEFAULT NULL,
						history TEXT,
						orders TEXT DEFAULT NULL,
						paydate TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
				);";
			dbDelta($sql);
		}
		
		//ihc_notifications
		$table_name = $wpdb->prefix . "ihc_notifications";
		if ($wpdb->get_var( "show tables like '$table_name'" ) != $table_name){
			require_once ABSPATH . 'wp-admin/includes/upgrade.php';
			$sql = "CREATE TABLE " . $table_name . " (
						id int(11) NOT NULL AUTO_INCREMENT,
						notification_type VARCHAR(200),
						level_id VARCHAR(200),
						subject TEXT,
						message TEXT,
						pushover_message TEXT,
						pushover_status TINYINT(1) NOT NULL DEFAULT 0,
						status TINYINT(1),
						PRIMARY KEY (`id`)
					)
					COLLATE utf8_general_ci;
			";
			dbDelta($sql);
		}
	
		//ihc_coupons
		$table_name = $wpdb->prefix . "ihc_coupons";
		if ($wpdb->get_var( "show tables like '$table_name'" ) != $table_name){
			require_once (ABSPATH . 'wp-admin/includes/upgrade.php');
			$sql = "CREATE TABLE " . $table_name . " (
						id int(11) NOT NULL AUTO_INCREMENT,
						code varchar(200),
						settings text,
						submited_coupons_count int(11),
						status tinyint(1),
						PRIMARY KEY (`id`)
			);";
			dbDelta ( $sql );
		}
			
		//ihc_orders
		$table = $wpdb->prefix . 'ihc_orders';
		if ($wpdb->get_var( "show tables like '$table'" )!=$table){
			require_once ABSPATH . 'wp-admin/includes/upgrade.php';
			$sql = "CREATE TABLE $table(
										id INT(11) NOT NULL AUTO_INCREMENT,
										uid INT(11),
										lid INT(11),
										amount_type VARCHAR(200),
										amount_value DECIMAL(12, 2) DEFAULT 0,
										automated_payment TINYINT(1) DEFAULT NULL,
										status VARCHAR(100),
										create_date TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,									
										PRIMARY KEY (`id`)
			);";
			dbDelta($sql);		
		}

		///ihc_orders_meta
		$table = $wpdb->prefix . 'ihc_orders_meta';
		if ($wpdb->get_var("show tables like '$table'")!=$table){
			require_once ABSPATH . 'wp-admin/includes/upgrade.php';
			$sql = "CREATE TABLE $table(
										id INT(11) NOT NULL AUTO_INCREMENT,
										order_id INT(11),
										meta_key VARCHAR(200),
										meta_value TEXT,
										PRIMARY KEY (`id`)
			);";
			dbDelta($sql);
		}
		
		//ihc_taxes
		$table = $wpdb->prefix . 'ihc_taxes';
		if ($wpdb->get_var( "show tables like '$table'" )!=$table){
			require_once ABSPATH . 'wp-admin/includes/upgrade.php';
			$sql = "CREATE TABLE $table(
										id INT(11) NOT NULL AUTO_INCREMENT,
										country_code VARCHAR(20),
										state_code VARCHAR(50) DEFAULT '',
										amount_value DECIMAL(12, 2) DEFAULT 0,
										label VARCHAR(200),
										description TEXT,
										status TINYINT(1),					
										PRIMARY KEY (`id`)
			);";
			dbDelta($sql);		
		}	
		
		/// IHC_DASHBOARD_NOTIFICATIONS
		$table_name = $wpdb->prefix . 'ihc_dashboard_notifications';
		if ($wpdb->get_var("show tables like '$table_name'")!=$table_name){
			require_once ABSPATH . 'wp-admin/includes/upgrade.php';
			$sql = "CREATE TABLE $table_name (
						type VARCHAR(40) NOT NULL,
						value INT(11) DEFAULT 0
			);";
			dbDelta($sql);			
			
			/// THIS TABLE WILL CONTAIN ONLY THIS TWO ENTRIES	
			$wpdb->query("INSERT INTO $table_name VALUES('users', 0);");
			$wpdb->query("INSERT INTO $table_name VALUES('orders', 0);");		
		}	
		
		/// ihc_cheat_off
		$table_name = $wpdb->prefix . 'ihc_cheat_off';
		if ($wpdb->get_var("show tables like '$table_name'")!=$table_name){
			require_once ABSPATH . 'wp-admin/includes/upgrade.php';
			$sql = "CREATE TABLE $table_name (
						uid INT(11) NOT NULL,
						hash VARCHAR(40) NOT NULL
			);";
			dbDelta($sql);			
		}	

		//ihc_invitation_codes
		$table_name = $wpdb->prefix . "ihc_invitation_codes";
		if ($wpdb->get_var( "show tables like '$table_name'" ) != $table_name){
			require_once (ABSPATH . 'wp-admin/includes/upgrade.php');
			$sql = "CREATE TABLE " . $table_name . " (
						id int(11) NOT NULL AUTO_INCREMENT,
						code varchar(200),
						settings text,
						submited int(11),
						repeat_limit int(11),
						status tinyint(1),
						PRIMARY KEY (`id`)
			);";
			dbDelta ( $sql );
		}		
		
		$table_name = $wpdb->prefix . 'ihc_gift_templates';
		if ($wpdb->get_var( "show tables like '$table_name'" ) != $table_name){
			require_once (ABSPATH . 'wp-admin/includes/upgrade.php');
			$sql = "CREATE TABLE " . $table_name . " (
						id INT(11) NOT NULL AUTO_INCREMENT,
						lid INT(11),
						settings TEXT,
						status TINYINT(2),
						PRIMARY KEY (`id`)
			);";
			dbDelta ( $sql );
		}		
				
		$table = $wpdb->prefix . 'ihc_security_login';
		if ($wpdb->get_var("show tables like '$table' ")!=$table){
			require_once (ABSPATH . 'wp-admin/includes/upgrade.php');
			$sql = "CREATE TABLE $table (
											id INT(11) NOT NULL AUTO_INCREMENT,
											username VARCHAR(200),
											ip VARCHAR(30),
											log_time INT(11),
											attempts_count INT(3),
											locked TINYINT(1),
											PRIMARY KEY (`id`)					
			);";
			dbDelta($sql);			
		}				
				
		$table = $wpdb->prefix . 'ihc_user_logs';
		if ($wpdb->get_var("show tables like '$table' ")!=$table){
			require_once (ABSPATH . 'wp-admin/includes/upgrade.php');
			$sql = "CREATE TABLE $table (
											id INT(11) NOT NULL AUTO_INCREMENT,
											uid INT(9) NOT NULL DEFAULT 0,
											lid INT(3),
											log_type VARCHAR(100),
											log_content TEXT,
											create_date INT(11),
											PRIMARY KEY (`id`)					
			);";
			dbDelta($sql);			
		}
														 
	}
	
	public static function update_tables_structure(){
		/*
		 * @param none
		 * @return none
		 */
		global $wpdb;	
		$table = $wpdb->prefix . 'indeed_members_payments';
		$data = $wpdb->get_row("SHOW COLUMNS FROM " . $table . " LIKE 'txn_id';");
		if (!$data){
			$q = 'ALTER TABLE ' . $wpdb->prefix . 'indeed_members_payments ADD history TEXT AFTER payment_data';
			$wpdb->query($q);
			$q = 'ALTER TABLE ' . $wpdb->prefix . 'indeed_members_payments ADD txn_id VARCHAR(100) DEFAULT NULL AFTER id';
			$wpdb->query($q);
		}
		
		$data = $wpdb->get_row("SHOW COLUMNS FROM " . $table . " LIKE 'orders';");
		if (!$data){
			$q = "ALTER TABLE $table ADD orders TEXT AFTER history";
			$wpdb->query($q);
		}
	
		$table = $wpdb->prefix . 'ihc_user_levels';
		$data = $wpdb->get_row("SHOW COLUMNS FROM " . $table . " LIKE 'notification';");
		if (!$data){
			$q = 'ALTER TABLE ' . $wpdb->prefix . 'ihc_user_levels ADD notification tinyint(1) DEFAULT 0 AFTER expire_time;';
			$wpdb->query($q);
		}	
		
		/// alter ihc_taxes if its case
		$table = $wpdb->prefix . 'ihc_taxes';
		$data = $wpdb->get_row("SHOW COLUMNS FROM " . $table . " LIKE 'state_code';");
		if (!$data){
			$q = "ALTER TABLE $table ADD state_code VARCHAR(50) DEFAULT '' AFTER country_code;";
			$wpdb->query($q);
		}	
		
		/// alter ihc_notifications if its case
		$table = $wpdb->prefix . 'ihc_notifications';
		$data = $wpdb->get_row("SHOW COLUMNS FROM " . $table . " LIKE 'pushover_message';");
		if (!$data){
			$q = "ALTER TABLE $table ADD pushover_message TEXT AFTER message;";
			$wpdb->query($q);
			$q = "ALTER TABLE $table ADD pushover_status TINYINT(1) NOT NULL DEFAULT 0 AFTER pushover_message;";
			$wpdb->query($q);			
		}						 
	}

	public static function do_uninstall(){
		/*
		 * @param none
		 * @return none
		 */
		$values = self::default_settings_groups();
		foreach ($values as $value){
			$data = ihc_return_meta_arr($value, true);
			foreach ($data as $k=>$v){
				delete_option($k);
			}	
		}
		
		delete_option('ihc_levels');//delete the levels
		delete_option('ihc_lockers');//delete the lockers
		delete_option('ihc_dashboard_allowed_roles');
		delete_option('ihc_custom_redirect_links_array');
		
		//delete table indeed_members_payments
		global $wpdb;
		$tables = array( 
						 $wpdb->prefix . "indeed_members_payments", 
						 $wpdb->prefix . "ihc_user_levels", 
						 $wpdb->prefix . "ihc_debug_payments", 
						 $wpdb->prefix . "ihc_notifications",
						 $wpdb->prefix . "ihc_coupons",
						 $wpdb->prefix . 'ihc_orders',
						 $wpdb->prefix . 'ihc_orders_meta',
						 $wpdb->prefix . 'ihc_taxes',
						 $wpdb->prefix . 'ihc_dashboard_notifications',
						 $wpdb->prefix . 'ihc_cheat_off',
						 $wpdb->prefix . 'ihc_invitation_codes',
						 $wpdb->prefix . 'ihc_gift_templates',
		);
		foreach ($tables as $table){
			$wpdb->query("DROP TABLE IF EXISTS $table;");
		}
		
		//delete user levels
		$users_obj = new WP_User_Query(array(
				'meta_query' => array(
						'relation' => 'OR',
						array(
								'key' => $wpdb->get_blog_prefix() . 'capabilities',
								'value' => 'subscriber',
								'compare' => 'like'
						),
						array(
								'key' => $wpdb->get_blog_prefix() . 'capabilities',
								'value' => 'pending_user',
								'compare' => 'like'
						)
				)
		));
		$users = $users_obj->results;
		if (!empty($users)){
			foreach ($users as $user){
				delete_user_meta($user->data->ID, 'ihc_user_levels');
			}	
		}		 
	}

	public static function create_notifications(){
		/*
		 * @param none
		 * @return none
		 */
		global $wpdb;
		$keys = array(	
						'email_check', 
						'email_check_success', 
						'reset_password', 
						'admin_user_register', 
						'reset_password_process', 
						'change_password', 
						'register', 
						'review_request',
						'approve_account',
						'bank_transfer',
						'register_lite_send_pass_to_user',
		);
		$table = $wpdb->prefix . "ihc_notifications"; 
		if (!function_exists('ihc_save_notification_metas')){
			require_once IHC_PATH . 'admin/includes/functions.php';		
		}
		foreach ($keys as $key){
			$check = $wpdb->get_row("SELECT id FROM $table WHERE notification_type='$key';");
			if (empty($check)){
				$notf_data = ihc_return_notification_pattern($key);
				$notf_data['message'] = @$notf_data['content'];
				$notf_data['notification_type'] = $key;
				$notf_data['level_id'] = -1;		
				ihc_save_notification_metas($notf_data);
				unset($notf_data);
			}
		}		 
	}
	
	public static function create_default_pages(){
			/*
			 * @param none
			 * @return none
			 */
		$insert_array = array(
						'ihc_general_user_page' => array(
											'title' => __('IUMP - Account Page', 'ihc'),
											'content' => '[ihc-user-page]',
						),
						'ihc_general_login_default_page' => array(
											'title' => __('IUMP - Login', 'ihc'),
											'content' => '[ihc-login-form]',
						),
						'ihc_general_logout_page' => array(
											'title' => __('IUMP - LogOut', 'ihc'),
											'content' => '[ihc-logout-link]',
						),
						'ihc_general_register_default_page' => array(
											'title' => __('IUMP - Register', 'ihc'),
											'content' => '[ihc-register]',
						),	
						'ihc_general_lost_pass_page' => array(
											'title' => __('IUMP - Reset Password', 'ihc'),
											'content' => '[ihc-pass-reset]',
						),	
						'ihc_subscription_plan_page' => array(
											'title' => __('IUMP - Subscription Plan', 'ihc'),
											'content' => '[ihc-select-level]',
						),								
						'ihc_general_tos_page' => array(
											'title' => __('IUMP - TOS Page', 'ihc'),
											'content' => 'Terms of Services',
						),	
						'ihc_general_register_view_user' => array(
											'title' => __('IUMP - Visitor Inside User Page', 'ihc'),
											'content' => '[ihc-visitor-inside-user-page]',
						),																	
		);

		foreach ($insert_array as $key=>$inside_arr){
			$exists = get_option($key);
			if (!$exists){
				$arr = array(
							'post_content' => $inside_arr['content'],
							'post_title' => $inside_arr['title'],
							'post_type' => 'page',
							'post_status' => 'publish',
				);
				$post_id = wp_insert_post($arr);
				update_option($key, $post_id);				
			}				
		}
	}	

	public static function create_default_redirects(){
		/*
		 * @param none
		 * @return none
		 */
		///DEFAULT REDIRECT
		$exists = get_option('ihc_general_redirect_default_page');
		if (!$exists){
			$arr = array(
							'post_content' => 'Redirected',
							'post_title' => 'IUMP - Default Redirect Page',
							'post_type' => 'page',
							'post_status' => 'publish',
			);
			$post_id = wp_insert_post($arr);
			update_option('ihc_general_redirect_default_page', $post_id);				
		}		
		
		///AFTER LOGIN
		$exists = get_option('ihc_general_logout_redirect');
		if (!$exists){
			$login = get_option('ihc_general_login_default_page');
			update_option('ihc_general_logout_redirect', $login);					
		}
		
		///AFTER REGISTER
		$exists = get_option('ihc_general_register_redirect');
		if ($exists){
			$account_page = get_option('ihc_general_user_page');
			update_option('ihc_general_register_redirect', $account_page);								
		}
					
		///AFTER LOGIN
		$exists = get_option('ihc_general_login_redirect');
		if (!$exists){
			$account_page = get_option('ihc_general_user_page');
			update_option('ihc_general_login_redirect', $account_page);			
		}
	}

	public static function create_extra_redirects(){
		/*
		 * @param none
		 * @return none
		 */
		 $login = get_option('ihc_general_login_default_page');
		 $account_page = get_option('ihc_general_user_page');
		 $register = get_option('ihc_general_register_default_page');
		 $logout = get_option('ihc_general_logout_page');
		 $lost_password = get_option('ihc_general_lost_pass_page');
		 if ($login){
		 	/// LOGIN
		 	update_post_meta($login, 'ihc_mb_type', 'show');
		 	update_post_meta($login, 'ihc_mb_who', 'unreg');
			update_post_meta($login, 'ihc_mb_block_type', 'redirect');
			update_post_meta($login, 'ihc_mb_redirect_to', $account_page);
		 }
		 if ($account_page){
		 	/// ACCOUNT PAGE
		 	update_post_meta($account_page, 'ihc_mb_type', 'show');
		 	update_post_meta($account_page, 'ihc_mb_who', 'reg');
			update_post_meta($account_page, 'ihc_mb_block_type', 'redirect');
			update_post_meta($account_page, 'ihc_mb_redirect_to', $login);		 	
		 }
		 if ($register){
		 	/// REGISTER PAGE
		 	update_post_meta($register, 'ihc_mb_type', 'show');
		 	update_post_meta($register, 'ihc_mb_who', 'unreg');
			update_post_meta($register, 'ihc_mb_block_type', 'redirect');
			update_post_meta($register, 'ihc_mb_redirect_to', $account_page);		 	
		 }
		 if ($logout){
		 	///LOGOUT
		 	update_post_meta($logout, 'ihc_mb_type', 'show');
		 	update_post_meta($logout, 'ihc_mb_who', 'reg');
			update_post_meta($logout, 'ihc_mb_block_type', 'redirect');
			update_post_meta($logout, 'ihc_mb_redirect_to', $login);			 	
		 }
		 if ($lost_password){
		 	///LOGOUT
		 	update_post_meta($lost_password, 'ihc_mb_type', 'show');
		 	update_post_meta($lost_password, 'ihc_mb_who', 'unreg');
			update_post_meta($lost_password, 'ihc_mb_block_type', 'redirect');
			update_post_meta($lost_password, 'ihc_mb_redirect_to', $account_page);			 	
		 }		 
	}

	public static function create_default_lockers(){
		/*
		 * @param none
		 * @return none
		 */
		 $data = get_option('ihc_lockers');
		 if ($data){
		 	return;
		 }
		 $array = array(
		 				'ihc_locker_name' => 'Loker with Form',
		 				'ihc_locker_custom_content' => '<h2>This content is locked</h2>Login To Unlock The Content!',
		 				'ihc_locker_custom_css' => '',
		 				'ihc_locker_template' => 3,
		 				'ihc_locker_login_template' => 'ihc-login-template-7',
		 				'ihc_locker_login_form' => 1,
		 				'ihc_locker_additional_links' => 1,
		 				'ihc_locker_display_sm' => 0,
		 );
		 self::save_update_locker_template($array);
		 $array = array(
		 				'ihc_locker_name' => 'Empty Showcase (only hide)',
		 				'ihc_locker_custom_content' => '',
		 				'ihc_locker_custom_css' => '.ihc-locker-wrap{}',
		 				'ihc_locker_template' => 1,
		 				'ihc_locker_login_template' => '',
		 				'ihc_locker_login_form' => 0,
		 				'ihc_locker_additional_links' => 0,
		 				'ihc_locker_display_sm' => 0,
		 );
		 self::save_update_locker_template($array);		 
	}
	
	public static function create_demo_levels(){
		/*
		 * @param none
		 * @return none
		 */
		if (!function_exists('ihc_save_level')){
			include_once IHC_PATH . 'admin/includes/functions/levels.php';
		}
		$array = array(
							'name'=>'free_demo', 
							'payment_type'=>'free',
							'price'=>'',					
						    'label'=>'Free',
							'description'=>'<strong>Free</strong> level allowing limited access on most of our Content sides. ',
							'price_text' => 'Sign up Now!',
							'order' => '',
							'access_type' => 'unlimited',
							'access_limited_time_type' => 'D',
							'access_limited_time_value' => '',
							'access_interval_start' => '',
							'access_interval_end' => '',
							'access_regular_time_type' => 'D',
							'access_regular_time_value' => '',
							'billing_type' => '',
							'billing_limit_num' => '2',
							'show_on' => '1',
							'afterexpire_level' => -1,
							'custom_role_level' => '-1',
							'start_date_content' => '0',
							'special_weekdays' => '',
							//trial
							'access_trial_time_value' => '',
							'access_trial_time_type' => 'D',
							'access_trial_price' => '',
							'access_trial_couple_cycles' => '',			
							'access_trial_type' => 1,
		);
		ihc_save_level($array, TRUE);	 
		$array = array(
							'name'=>'onetime_demo', 
							'payment_type'=>'payment',
							'price'=>10,					
						    'label'=>'One Time Plan',
							'description'=>'<h4><strong>Preimum Content!</strong></h4>
Is an <strong>One Time</strong> payment with a small fee. Just have a test.',
							'price_text' => 'only $10',
							'order' => '',
							'access_type' => 'unlimited',
							'access_limited_time_type' => 'D',
							'access_limited_time_value' => '',
							'access_interval_start' => '',
							'access_interval_end' => '',
							'access_regular_time_type' => 'D',
							'access_regular_time_value' => '',
							'billing_type' => '',
							'billing_limit_num' => '2',
							'show_on' => '1',
							'afterexpire_level' => -1,
							'custom_role_level' => '-1',
							'start_date_content' => '0',
							'special_weekdays' => '',
							//trial
							'access_trial_time_value' => '',
							'access_trial_time_type' => 'D',
							'access_trial_price' => '',
							'access_trial_couple_cycles' => '',			
							'access_trial_type' => 1,
		);
		ihc_save_level($array, TRUE);
		$array = array(
							'name'=>'recurring_demo', 
							'payment_type'=>'payment',
							'price'=>1,					
						    'label'=>'Recurring Plan',
							'description'=>'Is a <strong>Recurring</strong> Payment (monthly) on a small fee for testing purpose.
<h4>New Updates will be available!</h4>',
							'price_text' => 'only $1',
							'order' => '',
							'access_type' => 'regular_period',
							'access_limited_time_type' => 'D',
							'access_limited_time_value' => '',
							'access_interval_start' => '',
							'access_interval_end' => '',
							'access_regular_time_type' => 'M',
							'access_regular_time_value' => 1,
							'billing_type' => 'bl_ongoing',
							'billing_limit_num' => '2',
							'show_on' => '1',
							'afterexpire_level' => -1,
							'custom_role_level' => '-1',
							'start_date_content' => '0',
							'special_weekdays' => '',
							//trial
							'access_trial_time_value' => '',
							'access_trial_time_type' => 'D',
							'access_trial_price' => '',
							'access_trial_couple_cycles' => '',			
							'access_trial_type' => 1,
		);
		ihc_save_level($array, TRUE);						
	}
		
	public static function add_new_role(){
		/*
		 * @param none
		 * @return none
		 */
			add_role( 'pending_user', 'Pending', array( 'read' => false, 'level_0' => true ) );
			if (is_multisite()){
				global $wpdb;
				$table = $wpdb->base_prefix . 'blogs';
				$data = $wpdb->get_results("SELECT blog_id FROM $table;");
				if ($data){
					foreach ($data as $object){
						if (!empty($object->blog_id) && $object->blog_id>1){
							$prefix = $wpdb->base_prefix . $object->blog_id . '_' ;
							$table = $prefix . 'options';
							$option = $prefix . 'user_roles';
							$temp_data = $wpdb->get_row("SELECT option_value FROM $table WHERE option_name='$option';");
							if ($temp_data && !empty($temp_data->option_value)){
								$array_unserialize = unserialize($temp_data->option_value);
								if (empty($array_unserialize['pending_user'])){
									$array_unserialize['pending_user'] = array(
																				'name' => 'Pending', 
																				'capabilities' => array(
																											'read' => FALSE,
																											'level_0' => 1,
																				)
									);
									$array_serialize = serialize($array_unserialize);
									$wpdb->query("UPDATE $table SET option_value='$array_serialize' WHERE option_name='$option'; ");					
								}
							}
						}
					}	
				}
			}		
			add_role( 'suspended', 'Suspended', array( 'read' => false, 'level_0' => false ) );
			if (is_multisite()){
				global $wpdb;
				$table = $wpdb->base_prefix . 'blogs';
				$data = $wpdb->get_results("SELECT blog_id FROM $table;");
				if ($data){
					foreach ($data as $object){
						if (!empty($object->blog_id) && $object->blog_id>1){
							$prefix = $wpdb->base_prefix . $object->blog_id . '_' ;
							$table = $prefix . 'options';
							$option = $prefix . 'user_roles';
							$temp_data = $wpdb->get_row("SELECT option_value FROM $table WHERE option_name='$option';");
							if ($temp_data && !empty($temp_data->option_value)){
								$array_unserialize = unserialize($temp_data->option_value);
								if (empty($array_unserialize['suspended'])){
									$array_unserialize['suspended'] = array(
																				'name' => 'Suspended', 
																				'capabilities' => array(
																											'read' => FALSE,
																											'level_0' => 0,
																				)
									);
									$array_serialize = serialize($array_unserialize);
									$wpdb->query("UPDATE $table SET option_value='$array_serialize' WHERE option_name='$option'; ");					
								}
							}
						}
					}	
				}
			}		 
	}
	
	public static function default_settings_groups(){
		/*
		 * @param none
		 * @return array
		 */
		return array(	
						'payment', 
		 				'payment_paypal', 
		 				'payment_stripe', 
		 				'payment_authorize',
						'payment_twocheckout', 
						'payment_bank_transfer', 
						'payment_braintree', 
						'payment_payza', 
						'login', 
						'login-messages', 
						'general-defaults',
						'general-captcha', 
						'general-subscription', 
						'general-msg', 
						'register', 
						'register-msg',
						'register-custom-fields', 
						'opt_in', 
						'notifications', 
						'extra_settings', 
						'account_page',
						'fb',
						'tw',
						'in',
						'tbr',
						'ig',
						'vk',
						'goo',
						'social_media', 
						'double_email_verification', 
						'licensing',
						'ihc_woo', 
						'ihc_bp',
						'admin_workflow',
						'public_workflow',
						'affiliate_options',
						'listing_users_inside_page',
						'listing_users',
						'ihc_taxes_settings',
		);
	}

	
	public static function save_settings_into_db(){
		/*
		 * @param none
		 * @return none
		 */
		//save the metas to db
		$values = self::default_settings_groups();
		foreach ($values as $value){
			ihc_return_meta_arr($value);
		}
	}	
	
	public static function save_udate_order_meta($order_id=0, $meta_key='', $meta_value=''){
		/*
		 * @param int, string, string
		 * @return boolean
		 */
		 if ($order_id && $meta_key){
		 	 global $wpdb;
			 $table = $wpdb->prefix . 'ihc_orders_meta';
			 $exists = $wpdb->get_row("SELECT id FROM $table WHERE order_id=$order_id AND meta_key='$meta_key';");
			 if ($exists && !empty($exists->id)){
			 	/// update
			 	$wpdb->query("UPDATE $table SET meta_value='$meta_value' WHERE order_id=$order_id AND meta_key='$meta_key';");
			 } else {
			 	/// insert
			 	$wpdb->query("INSERT INTO $table VALUES(null, $order_id, '$meta_key', '$meta_value');");
			 }
			 return TRUE;
		 }
		 return FALSE;
	}
	
	public static function delete_order($order_id=0){
		/*
		 * @param int
		 * @return none
		 */
		 if ($order_id){
		 	 global $wpdb;
			 $table = $wpdb->prefix . 'ihc_orders';
			 $wpdb->query("DELETE FROM $table WHERE id=$order_id;");
			 $table = $wpdb->prefix . 'ihc_orders_meta';
			 $wpdb->query("DELETE FROM $table WHERE order_id=$order_id;");
		 }
	}
	
	public static function delete_order_meta($order_id=0, $meta_key=''){
		/*
		 * @param int, string
		 * @return none
		 */
		 if ($order_id && $meta_key){
		 	 global $wpdb;
			 $table = $wpdb->prefix . 'ihc_orders_meta';
			 $wpdb->query("DELETE FROM $table WHERE order_id=$order_id AND meta_key='$meta_key';");
		 }		 
	}
		
	public static function get_order_meta($order_id=0, $meta_key=''){
		/*
		 * @param int, string
		 * @return string
		 */
		 if ($order_id && $meta_key){
		 	 global $wpdb;
			 $table = $wpdb->prefix . 'ihc_orders_meta';
			 $data = $wpdb->get_row("SELECT meta_value FROM $table WHERE order_id=$order_id AND meta_key='$meta_key';");
			 if ($data && isset($data->meta_value)){
			 	return $data->meta_value;
			 }
		 }
		 return '';
	}

	public static function get_order_id_by_meta_value_and_meta_type($meta_key='', $meta_value=''){
		/*
		 * @param string, string
		 * @return int
		 */
		 if ($meta_key && $meta_value){
		 	 global $wpdb;
			 $table = $wpdb->prefix . 'ihc_orders_meta';
			 $data = $wpdb->get_row("SELECT order_id FROM $table WHERE meta_key='$meta_key' AND meta_value='$meta_value' ;");
			 if ($data && isset($data->order_id)){
			 	return $data->order_id;
			 }
		 }
		 return 0;
	}
	
	public static function get_all_order_metas($order_id=0){
		/*
		 * @param int
		 * @return array
		 */
		 $array = array();
		 if ($order_id){
		 	 global $wpdb;
			 $table = $wpdb->prefix . 'ihc_orders_meta';
			 $data = $wpdb->get_results("SELECT meta_key, meta_value FROM $table WHERE order_id=$order_id;");
			 if ($data){
			 	foreach ($data as $object){
			 		$array[$object->meta_key] = $object->meta_value;	
				}
			 }		 	
		 }
		 return $array;
	}
	
	public static function get_all_order($limit=30, $offset=0, $uid=0){
		/*
		 * @param none
		 * @return array
		 */
		 global $wpdb;
		 $array = array();
		 $table = $wpdb->prefix . 'ihc_orders';
		 $q = "SELECT * FROM $table";
		 $q .= " WHERE 1=1";
		 if ($uid){
		 	$q .= " AND uid=$uid";
		 }
		 $q .= " ORDER BY create_date DESC LIMIT $limit OFFSET $offset;";
		 $data = $wpdb->get_results($q);
		 if ($data){
		 	foreach ($data as $object){
		 		$temp = (array)$object;
				$temp['metas'] = self::get_all_order_metas($temp['id']);
				$temp['user'] = self::get_username_by_wpuid($temp['uid']);
				$temp['transaction_id'] = (empty($temp['metas']) || empty($data['metas']['transaction_id'])) ? self::get_transaction_id_by_order_id($temp['id']) : $temp['metas']['transaction_id'];
				if (empty($temp['user'])){
					$temp['user'] = '-';
				}
				///payment type
				if (empty($temp['metas']['ihc_payment_type'])){
					$temp['metas']['ihc_payment_type'] = self::get_payment_type_by_transaction_id($temp['transaction_id']);
				}
				$temp['level'] = self::get_level_name_by_lid($temp['lid']);
		 		$array[] = $temp;
		 	}
		 }
		 return $array;
	}
	
	public static function get_payment_type_by_transaction_id($id=0){
		/*
		 * @param int
		 * @return string
		 */
		 if ($id){
		 	 global $wpdb;
		 	 $table = $wpdb->prefix . 'indeed_members_payments';
			 $q = $wpdb->prepare("SELECT * FROM $table WHERE id=%s", $id);
			 $data = $wpdb->get_row($q);
			 if ($data && !empty($data->payment_data)){
				$temp = json_decode($data->payment_data, TRUE);
				return (empty($temp['ihc_payment_type'])) ? '' : $temp['ihc_payment_type'];			 	
			 }			 
		 }
		 return '';
	}
	
	public static function get_count_orders($uid=0){
		/*
		 * @param none
		 * @return int
		 */
		 global $wpdb;
		 $table = $wpdb->prefix . 'ihc_orders';
		 $q = "SELECT COUNT(*) as num FROM $table";
		 $q .= " WHERE 1=1";
		 if ($uid){
		 	$q .= " AND uid=$uid ";
		 }
		 $data = $wpdb->get_row($q);
		 return (empty($data->num)) ? 0 : $data->num;	 
	}
	
	public static function get_username_by_wpuid($wpuid=0){
		/*
		 * @param int
		 * @return string
		 */
		if ($wpuid){
			global $wpdb;
			$table = $wpdb->base_prefix . 'users';
			$data = $wpdb->get_row("SELECT user_login FROM $table WHERE ID='$wpuid'");
			if (!empty($data->user_login)){
				return $data->user_login;
			}
		}
		return '';
	}	
	
	public static function get_level_name_by_lid($lid=0){
		/*
		 * @param int
		 * @return string
		 */
		if ($lid){
			$levels = get_option('ihc_levels');
			if (!empty($levels[$lid]) && !empty($levels[$lid]['label'])){
				return $levels[$lid]['label'];
			}
		}
		return '';
	}
	
	/*
	 * @param int
	 * @return boolean
	 */
	public static function does_level_exists($lid=-1){
		if ($lid>-1){
			$data = get_option('ihc_levels');
			if (isset($data[$lid])){
				return TRUE;
			}
		}
		return FALSE;
	}
	
	public static function get_transaction_id_by_order_id($order_id=0){
		/*
		 * @param int
		 * @return int
		 */
		if ($order_id){
			global $wpdb;
			$p = $wpdb->prefix . 'indeed_members_payments';
			$o = $wpdb->prefix . 'ihc_orders';
			$data = $wpdb->get_results("SELECT p.orders as orders, p.id as id FROM $p p INNER JOIN $o o ON p.u_id=o.uid WHERE o.id=$order_id");
			if ($data){
				foreach ($data as $object){
					if (isset($object->orders)){
						$temp_data = unserialize($object->orders);
						if ($temp_data && in_array($order_id, $temp_data)){
							return $object->id;
						}
					}	
				}
			}
		}
		return 0;		 
	}
	
	public static function get_order_data_by_id($order_id=0){
		/*
		 * @param none
		 * @return array
		 */
		 $array = array();
		 if ($order_id){
			 global $wpdb;			 
			 $table = $wpdb->prefix . 'ihc_orders';
			 $data = $wpdb->get_row("SELECT * FROM $table WHERE id=$order_id;");
			 if ($data){
			 	$array = (array)$data;
				$array['metas'] = self::get_all_order_metas($array['id']);
				$array['user'] = self::get_username_by_wpuid($array['uid']);
				$array['transaction_id'] = (empty($array['metas']) || empty($array['metas']['transaction_id'])) ? self::get_transaction_id_by_order_id($array['id']) : $array['metas']['transaction_id'];
				if (empty($array['user'])){
					$array['user'] = '-';
				}
				$array['level'] = self::get_level_name_by_lid($array['lid']);
			 }		 	
		 }
		 return $array;
	}	
	
	
	/// TAXES
	public static function save_tax($post_data=array()){
		/*
		 * @param array
		 * @return boolean
		 */
		 if ($post_data){
		 	 global $wpdb;
			 $table = $wpdb->prefix . 'ihc_taxes';
			 if (empty($post_data['id'])){
			 	//insert
			 	$data = $wpdb->get_row("SELECT * FROM $table WHERE country_code='" . $post_data['country_code'] . "' AND label='" . $post_data['label'] . "' AND state_code='" . $post_data['state_code'] . "' ");
			 	if (empty($data)){
				 	$wpdb->query("INSERT INTO $table 
				 						VALUES(null, 
				 								'" . $post_data['country_code'] . "', 
				 								'" . $post_data['state_code'] . "',
				 								'" . $post_data['amount_value'] . "',
				 								'" . $post_data['label'] . "',
				 								'" . $post_data['description'] . "',
				 								'" . $post_data['status'] . "' );"
					);
					return TRUE;			 		
			 	} else {
			 		return FALSE;
			 	}
			 } else {
			 	//update
			 	$data = $wpdb->get_row("SELECT id FROM $table WHERE country_code='" . $post_data['country_code'] . "' AND label='" . $post_data['label'] . "' ");
			 	if (isset($data) && isset($data->id) && $data->id!=$post_data['id']){
			 		return FALSE;
			 	}
				$wpdb->query("UPDATE $table SET 
				 								country_code='" . $post_data['country_code'] . "', 
				 								state_code='" . $post_data['state_code'] . "',
				 								amount_value='" . $post_data['amount_value'] . "',
				 								label='" . $post_data['label'] . "',
				 								description='" . $post_data['description'] . "',
				 								status='" . $post_data['status'] . "'	
				 						WHERE id='" . $post_data['id'] . "'		
				");
				return TRUE;
			 }
		 }
		 return FALSE;
	}
	
	public static function get_tax($id=0){
		/*
		 * @param int
		 * @return array
		 */
		 if (empty($id)){
		 	return array(
							'id' => 0,
							'country_code' => '',
							'state_code' => '',
							'amount_value' => '',
							'label' => '',
							'description' => '',
							'status' => 1,
			);
		 } else {
		 	global $wpdb;
			$table = $wpdb->prefix . 'ihc_taxes';
			$data = $wpdb->get_row("SELECT * FROM $table WHERE id=$id;");
			if ($data){
				return (array)$data;
			}
		 }
	}
	
	public static function get_all_taxes(){
		/*
		 * @param none
		 * @return array
		 */
		$array = array(); 
		global $wpdb;
		$table = $wpdb->prefix . 'ihc_taxes';
		$data = $wpdb->get_results("SELECT * FROM $table;");	
		if ($data){
			foreach ($data as $object){
				$array[] = (array)$object;
			}
		}
		return $array; 
	}
	
	public static function delete_tax($id=0){
		/*
		 * @param int
		 * @return none
		 */
		 if ($id){
			global $wpdb;
			$table = $wpdb->prefix . 'ihc_taxes';
			$wpdb->query("DELETE FROM $table WHERE id=$id;");			 	
		 }
	}
	
	public static function get_taxes_by_country($country='', $state=''){
		/*
		 * @param string, string
		 * @return array
		 */
		$array = array();  
		global $wpdb;
		$table = $wpdb->prefix . 'ihc_taxes';
		$q = "SELECT * FROM $table WHERE country_code='$country'";
		if ($state){
			$q .= " AND state_code='$state' ";
			$data = $wpdb->get_results($q);
			if (empty($data)){
				$q = "SELECT * FROM $table WHERE country_code='$country' AND state_code='' ";	
				$data = $wpdb->get_results($q);	
			}		
		} else {
			$q .= " AND state_code='' ";
			$data = $wpdb->get_results($q);
		}		

		if ($data){
			foreach ($data as $object){
				$array[] = (array)$object;
			}
		}	
		return $array; 	 
	}
	
	public static function get_taxes_rate_for_user($uid=0){
		/*
		 * @param int (user id)
		 * @return array
		 */
		 if (ihc_is_magic_feat_active('taxes') && $uid){
		 	 global $wpdb;
			 $country = get_user_meta($uid, 'ihc_country', TRUE);
 			 $state = get_user_meta($uid, 'ihc_state', TRUE);
			 $taxes = self::get_taxes_by_country($country, $state);
			 if ($taxes){
			 	/// taxes by country & state
			 	foreach ($taxes as $array){
			 		$return[$array['label']] = $array['amount_value'] . '%';
			 	}
			 } else {
			 	/// default taxes
			 	$taxes_settings = ihc_return_meta_arr('ihc_taxes_settings');
				if (!empty($taxes_settings['ihc_default_tax_label']) && !empty($taxes_settings['ihc_default_tax_value'])){
					$return[$taxes_settings['ihc_default_tax_label']] = $taxes_settings['ihc_default_tax_value'] . '%';
				}
			 }
			 return $return;
		 }
		 return array();
	}
	
	public static function increment_dashboard_notification($type=''){
		/*
		 * @param string ( affiliates || referrals )
		 * @return none
	 	 */
		global $wpdb;
		$table = $wpdb->prefix . 'ihc_dashboard_notifications';
		$wpdb->query("UPDATE $table SET value=value+1 WHERE type='$type';");		
		do_action('ihc_dashboard_notification_action', $type);	 	
	}
	
	public static function reset_dashboard_notification($type=''){
		/*
		 * @param string ( affiliates || referrals )
		 * @return none
		 */
		global $wpdb;
		$table = $wpdb->prefix . 'ihc_dashboard_notifications';
		$wpdb->query("UPDATE $table SET value=0 WHERE type='$type';");	
	}
		
	public static function get_dashboard_notification_value($type=''){
		/*
		 * @param string ( affiliates || referrals )
		 * @return none
		 */
		global $wpdb;
		$table = $wpdb->prefix . 'ihc_dashboard_notifications';
		$data = $wpdb->get_row("SELECT value FROM $table WHERE type='$type';");
		return (empty($data->value)) ? 0 : $data->value;			 	
	}	
	
	public static function save_update_locker_template($post_data=array()){
		/*
		 * @param array
		 * @return none
		 */
		$option_name = 'ihc_lockers';
		$meta_keys = ihc_locker_meta_keys();
		foreach ($meta_keys as $k=>$v){
			if (isset($post_data[$k])){
				$data[$k] = $post_data[$k];
			}
		}
		$data_db = get_option($option_name);
		if ($data_db!==FALSE){
			if (isset($post_data['template_id'])){
				$data_db[$post_data['template_id']] = $data;
			} else {
				end($data_db);
				$key = key($data_db);
				$key++;
				$data_db[$key] = $data;					
			}
			update_option($option_name, $data_db);
		} else {
			$data_db[1] = $data;
			add_option($option_name, $data_db);
		}	
	}
	
	public static function get_user_levels($uid=0, $check_expire=FALSE){
		/*
		 * @param int, bool
		 * @return array
		 */
		 $array = array();
		 if ($uid){
		 	 global $wpdb;
			 $levels = get_option('ihc_levels');
			 $table = $wpdb->prefix . "ihc_user_levels";
			 $data = $wpdb->get_results("SELECT * FROM $table WHERE user_id=$uid");
			 if ($data){
			 	foreach ($data as $object){
			 		$temp = (array)$object;
					$temp['label'] = $levels[$object->level_id]['label'];	
					if (!empty($levels[$object->level_id]['badge_image_url'])){
						$temp['badge_image_url'] = $levels[$object->level_id]['badge_image_url'];
					} else {
						$temp['badge_image_url'] = '';
					}
					if (self::is_user_level_active($uid, $object->level_id)){
						$temp['is_expired'] = FALSE;
					} else {
						$temp['is_expired'] = TRUE;
						if ($check_expire){
							continue;	
						}
					}
					$array[$object->level_id] = $temp;
			 	}
			 }
		 }
		 return $array;
	}
	
	public static function is_user_level_active($uid=0, $lid=0){
		/*
		 * @param int, int
		 * @return bool
		 */
		global $wpdb;
		$grace_period = get_option('ihc_grace_period');
		$data = $wpdb->get_row('SELECT expire_time, start_time FROM ' . $wpdb->prefix . 'ihc_user_levels WHERE user_id="' . $uid . '" AND level_id="' . $lid . '";');
		$current_time = time();
		if (!empty($data->start_time)){
			$start_time = strtotime($data->start_time);
			if ($current_time<$start_time){
				//it's not available yet
				return FALSE;
			}				
		}	
		if (!empty($data->expire_time)){
			$expire_time = strtotime($data->expire_time) + ((int)$grace_period * 24 * 60 *60);
			if ($current_time>$expire_time){
				//it's expired
				return FALSE;
			}
		}
		return TRUE;	 
	}
	
	public static function user_has_level($uid=0, $lid=0){
		/*
		 * @param int, int
		 * @return boolean
		 */
		 if ($uid && $lid!==FALSE){
		 	 global $wpdb;
			 $table = $wpdb->prefix . 'ihc_user_levels';
			 $data = $wpdb->get_row("SELECT * FROM $table WHERE user_id='$uid' AND level_id='$lid';");
			 if ($data && isset($data->start_time)){
			 	return TRUE;
			 }
		 }
		 return FALSE;
	}
	
	public static function cheat_off_get_hash($uid=0){
		/*
		 * @param int
		 * @return string
		 */
		 if ($uid){
			 global $wpdb;
			 $table = $wpdb->prefix . 'ihc_cheat_off';		 
		 	 $data = $wpdb->get_row("SELECT hash FROM $table WHERE uid=$uid;");
			 if (!empty($data) && !empty($data->hash)){
			 	return $data->hash;
			 }
		 }
		 return '';
	}
	
	public static function cheat_off_set_hash($uid=0, $hash=''){
		/*
		 * @param int, string
		 * @return boolean
		 */
		 if ($uid && $hash){
			 global $wpdb;
			 $table = $wpdb->prefix . 'ihc_cheat_off';		 
		 	 $data = $wpdb->get_row("SELECT hash FROM $table WHERE uid=$uid;");	
			 if (!empty($data) && !empty($data->hash)){
			 	/// update
			 	return $wpdb->query("UPDATE $table SET hash='$hash' WHERE uid=$uid;");
			 } else {
			 	/// insert
			 	return $wpdb->query("INSERT INTO $table VALUES($uid, '$hash');");			 	
			 }		 	 	
		 }
		 return FALSE;
	}
	
	public static function invitation_code_add_new($data=array()){
		/*
		 * @param array
		 * @return boolean
		 */
		 if ($data){
		 	global $wpdb;
			$table = $wpdb->prefix . 'ihc_invitation_codes';
			if (empty($data['repeat'])){
				$data['repeat'] = 1;
			}
			if (empty($data['how_many_codes'])){
				///single
				if (!empty($data['code'])){
					$data['code'] = ihc_make_string_simple($data['code']);
				 	$check = $wpdb->get_row("SELECT * FROM $table WHERE code='{$data['code']}';");
				 	if ($check && !empty($check->id)){
				 		return FALSE; ///already exists
				 	}					
					$wpdb->query("INSERT INTO $table VALUES(null, '{$data['code']}', '', 0, '{$data['repeat']}', 1);");
					return TRUE;
				}
			} else {
				/// multiple
				$prefix = $data['code_prefix'];
				$length = $data['code_length'] - strlen($data['code_prefix']);
				$limit = $data['how_many_codes'];
				while ($limit){
					$code = ihc_random_str($length);
					$code = $prefix . $code;	
					$code = str_replace(' ', '', $code);
					$code = ihc_make_string_simple($code);
					$check = $wpdb->get_row("SELECT * FROM $table WHERE code='$code';");
					if ($check){
						continue;
					}				
					$wpdb->query("INSERT INTO $table VALUES(null, '$code', '', 0, '{$data['repeat']}', 1);");
					$limit--;				
				}	
				return TRUE;					
			}
		 }
		 return FALSE;
	}
	
	public static function invitation_code_delete($id=0){
		/*
		 * @param int
		 * @return boolean
		 */
		 if (!empty($id)){
		 	global $wpdb;
			$table = $wpdb->prefix . 'ihc_invitation_codes';
			$wpdb->query("DELETE FROM $table WHERE id=$id;");
			return TRUE;		 	
		 }
		 return FALSE;
	}
	
	public static function invitation_code_check($code=''){
		/*
		 * @param string
		 * @return boolean
		 */
		 if (!empty($code)){
		 	global $wpdb;
			$table = $wpdb->prefix . 'ihc_invitation_codes';
			$check = $wpdb->get_row("SELECT * FROM $table WHERE code='$code';");
			if ($check && isset($check->submited) && isset($check->repeat_limit)){
				if ($check->submited<$check->repeat_limit){
					return TRUE;
				}
			}		 	
		 }
		 return FALSE;
	}
	
	public static function invitation_code_increment_submited_value($code=''){
		/*
		 * @param string
		 * @return boolean
		 */
		 if ($code){
		 	global $wpdb;
			$table = $wpdb->prefix . 'ihc_invitation_codes';
			$check = $wpdb->get_row("SELECT submited, repeat_limit FROM $table WHERE code='$code';");
			if ($check && isset($check->submited)){
				$increment_value = $check->submited + 1;
				if ($increment_value<=$check->repeat_limit){
					$wpdb->query("UPDATE $table SET submited=$increment_value WHERE code='$code';");
					return TRUE;					
				}
			}		 	
		 }
		 return FALSE;
	}
	
	public static function invitation_code_get_all(){
		/*
		 * @param none
		 * @return array
		 */
		$array = array();
		global $wpdb;
		$table = $wpdb->prefix . 'ihc_invitation_codes';
		$data = $wpdb->get_results("SELECT * FROM $table;");
		if ($data){
			foreach ($data as $object){
				$array[] = (array)$object;
			}
		} 
		return $array;
	}
	
	public static function invitation_code_does_exist_codes(){
		/*
		 * @param none
		 * @return boolean
		 */
		global $wpdb;
		$table = $wpdb->prefix . 'ihc_invitation_codes';
		$data = $wpdb->get_row("SELECT COUNT(*) as c FROM $table;");
		if ($data && isset($data->c) && $data->c>0){
			return TRUE;
		}
		return FALSE;
	}
	
	public static function download_monitor_get_count_for_user($uid=0, $type='files'){
		/*
		 * @param int, string. uid set as -1 means all registered users
		 * @return int
		 */
		 global $wpdb;
		 $table = $wpdb->base_prefix . 'download_log';

		 if ($type=='files'){
		 	$q = "SELECT COUNT(DISTINCT download_id) as c FROM $table WHERE";
		 } else {
		 	$q = "SELECT COUNT(*) as c FROM $table WHERE";
		 }
		 if ($uid==-1){
		 	/// all registered users
		 	$q .= " user_id<>0;";
		 } else {
		 	$q .= " user_id=$uid;";
		 }		 
		 $data = $wpdb->get_row($q);
		 if ($data && !empty($data->c)){
		 	return (int)$data->c;
		 }
		 return 0;
	}	
	
	public static function get_payment_tyoe_by_userId_levelId($uid=0, $lid=0){
		/*
		 * @param int, int
		 * @return string
		 */
		 $payment_type = '';
		 if ($uid && $lid){
		 	global $wpdb;
		 	$table = $wpdb->prefix . 'indeed_members_payments';
			$data = $wpdb->get_results("SELECT payment_data FROM $table WHERE u_id=$uid ORDER BY paydate DESC;");
			if ($data){
				foreach ($data as $object){
					$array = json_decode($object->payment_data, TRUE);
					
					if (empty($array['level']) && !empty($array['custom'])){
						$temp_paypal_data = json_decode(stripslashes($array['custom']), TRUE);
						$array['level'] = (isset($temp_paypal_data['level_id'])) ? $temp_paypal_data['level_id'] : '';
					}

					if (isset($array['level']) && $array['level']!='' && isset($array['ihc_payment_type'])){
						if ($lid==$array['level']){
							$payment_type = $array['ihc_payment_type'];
							break;			
						}
					} else if (isset($array['custom'])){
						$custom = json_decode($array['custom'], TRUE);
						if ($lid==$custom['level_id']){
							$payment_type = 'paypal';
							break;
						}
					}
				}
			}
		 }
		 return $payment_type;
	}
	
	public static function get_page_slug($post_id=0){
		/*
		 * @param int
		 * @return string
		 */
		 if ($post_id){
		 	 global $wpdb;
			 $table = $wpdb->prefix . 'posts';
			 $data = $wpdb->get_row("SELECT post_name FROM $table WHERE ID=$post_id;");
			 if ($data && !empty($data->post_name)){
			 	return $data->post_name;
			 }
		 }
		 return '';
	}
	
	public static function get_users_with_no_individual_page(){
	    /*
	     * @param none
	     * @return array
	     */
	     $array = array();
	     global $wpdb;
	     $table = $wpdb->base_prefix . 'usermeta';
	     $data = $wpdb->get_results("SELECT DISTINCT user_id, meta_value FROM $table WHERE meta_key='ihc_individual_page' GROUP BY user_id;");
	     $not_in_string = '';
	     if ($data){
	         foreach ($data as $object){
	         	 if (self::post_does_exists($object->meta_value)){
	             	$not_in[] = $object->user_id;	         	 	
	         	 }
	         }
	        if ($not_in){
	            $not_in_string = implode(',', $not_in);
	        }
	     }
	     $table = $wpdb->base_prefix . 'users';
	     $q = "SELECT ID FROM $table WHERE 1=1";
	     if (!empty($not_in_string)){
	         $q .= " AND ID NOT IN ($not_in_string) ";
	     }
	     $our_target = $wpdb->get_results($q);
	     if ($our_target){
	         foreach ($our_target as $u){
	             $array[] = $u->ID;
	         }
	     }
	     return $array;
	}
	
	public static function post_does_exists($post_id=0){
		/*
		 * @param int
		 * @return boolean
		 */
		 if ($post_id){
		 	 global $wpdb;
			 $table = $wpdb->base_prefix . 'posts';
			 $data = $wpdb->get_row("SELECT post_title FROM $table WHERE ID=$post_id LIMIT 1");
			 if ($data && isset($data->post_title)){
			 	return TRUE;
			 }
		 }
		 return FALSE;
	}	

	public static function get_excluded_payment_types_for_level_id($level_id=-1){
		/*
		 * @param int
		 * @return string
		 */
		 if ($level_id>-1){
		 	 $data = get_option('ihc_level_restrict_payment_values');
			 if ($data && !empty($data[$level_id])){
			 	return $data[$level_id];
			 }
		 }
		 return '';
	}
	
	public static function get_default_payment_gateway_for_level($lid=-1, $default_payment=''){
		/*
		 * @param int, string
		 * @return string
		 */
		 if ($lid>-1){
		 	 $data = get_option('ihc_levels_default_payments');
			 if ($data && !empty($data[$lid]) && $data[$lid]!=-1){
			 	if (!function_exists('ihc_check_payment_status')){
					require_once IHC_PATH . 'admin/includes/functions.php';
				}
				$check = ihc_check_payment_status($data[$lid]);
				if ($check['status'] && $check['settings']=='Completed'){
					return $data[$lid];
				}
			 }
		 }
		 return $default_payment;		 
	}
	
	public static function does_this_user_bought_something($uid=0){
		/*
		 * @param int
		 * @return boolean
		 */
		 $bool = FALSE;
		 if ($uid){
		 	 global $wpdb;
			 $table = $wpdb->prefix . 'indeed_members_payments';
			 $data = $wpdb->get_results("SELECT payment_data FROM $table WHERE u_id=$uid;");
			 if ($data){
			 	foreach ($data as $object){
			 		$temp = json_decode($object->payment_data, TRUE);
					if (!empty($temp['amount'])){
						$bool = TRUE;
						break;
					}
			 	}
			 }
		 }
		 return $bool;
	}
	
	public static function gift_templates_get_metas($id=0){
		/*
		 * @param int
		 * @return array
		 */
		 if (empty($id)){
		 	$array = array(
							'id' => 0,
							"discount_type" => "price",
							"discount_value" => '',
							'target_level' => -1,
							"reccuring" => '',
			);
		 } else {
		 	global $wpdb;
			$table = $wpdb->prefix . 'ihc_gift_templates';
			$data = $wpdb->get_row("SELECT lid, settings FROM $table WHERE id=$id;");
			if ($data && isset($data->lid) && isset($data->settings)){
				$array = unserialize($data->settings);
				$array['lid'] = $data->lid;
			}
		 }
		 return $array;
	}
	
	public static function gifts_do_save($data=array()){
		/*
		 * @param array
		 * @return boolean
		 */
		 if ($data){
		 	 global $wpdb;
			 $table = $wpdb->prefix . 'ihc_gift_templates';
			 if (empty($data['id'])){
			 	///insert
			 	$settings = array(
									'discount_type' => $data['discount_type'],
									"discount_value" => $data['discount_value'],
									'target_level' => $data['target_level'],
									"reccuring" => $data['reccuring'],
				);
				$settings = serialize($settings);
			 	$wpdb->query("INSERT INTO $table VALUES(null, '{$data['lid']}', '$settings', 1);");
			 } else {
			 	///update
			 	$settings = array(
									'discount_type' => $data['discount_type'],
									"discount_value" => $data['discount_value'],
									'target_level' => $data['target_level'],
									"reccuring" => $data['reccuring'],
				);
				$settings = serialize($settings);
			 	$wpdb->query("UPDATE $table SET lid='{$data['lid']}', settings='$settings' WHERE id='{$data['id']}';");			 	
			 }
			  	
		 }
		 return FALSE;
	}

	public static function gift_get_all_items($a_lid=''){
		/*
		 * @param int (aworded level id)
		 * @return array
		 */
		 global $wpdb;
		 $array = array();
		 $table = $wpdb->prefix . 'ihc_gift_templates';
		 $q = "SELECT * FROM $table";
		 if ($a_lid!=''){
		 	$q .= " WHERE lid=$a_lid OR lid=-1;";
		 }
		 $data = $wpdb->get_results($q);
		 if ($data){
		 	foreach ($data as $object){
		 		$temp = unserialize($object->settings);
				$item = $temp;
				$item['lid'] = $object->lid;
				$array[$object->id] = $item;
		 	}
		 }
		 return $array;
	}
	
	public static function gifts_do_delete($id=0){
		/*
		 * @param int
		 * @return none
		 */
		 if ($id){
			 global $wpdb;
			 $table = $wpdb->prefix . 'ihc_gift_templates';
			 $wpdb->query("DELETE FROM $table WHERE id=$id;");
		 }
	}

	public static function get_gifts_by_uid($uid=0){
		/*
		 * @param int (user id)
		 * @return array
		 */
		 $array = array();
		 if ($uid){
		 	 $gifts = get_user_meta($uid, 'ihc_gifts', TRUE);
			 if ($gifts){
			 	 foreach ($gifts as $arr){
					 $temp = ihc_get_coupon_by_code($arr['code']);					 
					 $temp['is_active'] = self::is_gift_stil_active($arr['code']);
					 $array[] = $temp;
			 	 }
			 }
		 }
		 return $array;
	}
	
	public static function is_gift_stil_active($code=''){
		/*
		 * @param string
		 * @return bool
		 */
		 if ($code){
			 $coupon_data = ihc_get_coupon_by_code($code);
			 if ($coupon_data){
			 	if ($coupon_data['submited_coupons_count']<1){
			 		return TRUE;
			 	}
			 }
		 }
		 return FALSE;
	}
	
	public static function get_all_gift_codes($limit=30, $offset=0){
		/*
		 * @param int
		 * @return array
		 */
		 $array = array();
		 global $wpdb;
		 $table = $wpdb->prefix . 'ihc_coupons';
		 $data = $wpdb->get_results("SELECT * FROM $table WHERE status=2 ORDER BY id DESC LIMIT $limit OFFSET $offset ");
		 if ($data){
		 	foreach ($data as $object){
		 		$temp = unserialize($object->settings);
		 		$temp['username'] = self::get_username_by_wpuid(@$temp['uid']);
				$temp['code'] = $object->code;
				$temp['is_active'] = self::is_gift_stil_active($object->code);
				$array[$object->id] = $temp;
		 	}
		 }
		 return $array;
	}
	
	public static function get_count_all_gift_codes(){
		/*
		 * @param none
		 * @return int
		 */
		 global $wpdb;
		 $table = $wpdb->prefix . 'ihc_coupons';
		 $data = $wpdb->get_row("SELECT COUNT(*) as c FROM $table WHERE status=2;");
		 if ($data && isset($data->c)){
		 	return $data->c;
		 }		 
		 return 0;
	}
		
	public static function do_delete_generated_gift_code($coupon_id=0){
		/*
		 * @param int
		 * @return none
		 */
		 if ($coupon_id){
		 	 $metas = ihc_get_coupon_by_id($coupon_id);
			 if (isset($metas['uid']) && isset($metas['code'])){
			 	 $code = $metas['code'];
			 	 $meta_user = get_user_meta($metas['uid'], 'ihc_gifts', TRUE);
				 $key = ihc_array_value_exists($meta_user, $code, 'code');  
				 if ($key!==FALSE){
				 	 unset($meta_user[$key]);
					 update_user_meta($metas['uid'], 'ihc_gifts', $meta_user);
				 }
			 }
		 	 ihc_delete_coupon($coupon_id);
		 }
	}

	public static function is_order_id_for_uid($uid=0, $order_id=0){
		/*
		 * check if a order belong to a user
		 * @param int, int
		 * @return boolean
		 */
		 if ($uid && $order_id){
		 	 global $wpdb;
			 $table = $wpdb->prefix . 'ihc_orders';
			 $check = $wpdb->get_row("SELECT * FROM $table WHERE uid=$uid AND id=$order_id;");
			 if ($check && !empty($check->id)){
			 	return TRUE;
			 }
		 }
		 return FALSE;
	}
	
	public static function get_uid_by_order_id($order_id=0){
		/*
		 * @param int
		 * @return int
		 */
		 if ($order_id){
		 	 global $wpdb;
			 $table = $wpdb->prefix . 'ihc_orders';
			 $check = $wpdb->get_row("SELECT uid FROM $table WHERE id=$order_id;");
			 if ($check && !empty($check->uid)){
			 	return $check->uid;
			 }
		 }
		 return 0;		 
	}
	
	public static function transactions_get_total_for_user($uid=0){
		/*
		 * @param int
		 * @return int
		 */
		 if ($uid){
		 	 global $wpdb;
			 $table = $wpdb->prefix . "indeed_members_payments";
			 $data = $wpdb->get_row("SELECT COUNT(*) as c FROM $table WHERE u_id=$uid;");
			 if ($data && !empty($data->c)){
			 	return $data->c;
			 }
		 }
		 return 0;
	}
	
	public static function transaction_get_items_for_user($limit=999, $offset=0, $uid=0){
		/*
		 * @param int, int, int
		 * @return array
		 */
		 if ($uid){
		 	 global $wpdb;
		 	 $table = $wpdb->prefix . "indeed_members_payments";
			 $q = "SELECT * FROM $table";
			 $q .= " WHERE 1=1";
			 $q .= " AND u_id=$uid";
			 $q .= " ORDER BY paydate DESC LIMIT $limit OFFSET $offset;";	
			 $data = $wpdb->get_results($q);
			 if (!empty($data)){
				 return $data;	 				 	
			 }
		 }
		 return array();
	}
	
	public static function user_get_register_date($uid=0){
		/*
		 * @param int
		 * @return string
		 */
		 if ($uid){
		 	 global $wpdb;
			 $table = $wpdb->base_prefix . 'users';
			 $data = $wpdb->get_row("SELECT user_registered FROM $table WHERE ID=$uid;");
			 if ($data && !empty($data->user_registered)){
			 	return $data->user_registered;
			 }
		 }
		 return '';
	}
	
	public static function user_get_email($uid=0){
		/*
		 * @param int
		 * @return string
		 */
		 if ($uid){
		 	 global $wpdb;
			 $table = $wpdb->base_prefix . 'users';
			 $data = $wpdb->get_row("SELECT user_email FROM $table WHERE ID=$uid;");
			 if ($data && !empty($data->user_email)){
			 	return $data->user_email;
			 }
		 }
		 return '';		
	}

	public static function update_order_status($order_id=0, $new_status=''){
		/*
		 * @param int, string
		 * @return boolean
		 */
		 if ($order_id){
		 	 global $wpdb;
			 $table = $wpdb->prefix . 'ihc_orders';
			 $check = $wpdb->get_row("SELECT * FROM $table WHERE id=$order_id;");
			 if ($check && !empty($check->id)){
			 	return $wpdb->query("UPDATE $table SET status='$new_status' WHERE id=$order_id;");
			 }
		 }
	}
	
	public static function update_transaction_status($txn_id='', $new_status=''){
		/*
		 * @param int, string
		 * @return boolean 
		 */
		 if ($txn_id){
		 	 global $wpdb;
			 $table = $wpdb->prefix . 'indeed_members_payments';
			 $check = $wpdb->get_row("SELECT payment_data FROM $table WHERE txn_id='$txn_id';");
			 if ($check && !empty($check->payment_data)){
			 	$data = json_decode($check->payment_data, TRUE);
				$data['message'] = $new_status;
				$json = json_encode($data); 
			 	return $wpdb->query("UPDATE $table SET payment_data='$json' WHERE txn_id='$txn_id';");
			 }		 	 
		 }
	}
	
	public static function get_woo_product_id_for_lid($lid=0){
		/*
		 * @param int
		 * @return int
		 */
		 if ($lid!==FALSE){
		 	 global $wpdb;
			 $table = $wpdb->prefix . 'postmeta';
			 $data = $wpdb->get_row("SELECT post_id FROM $table WHERE meta_key='iump_woo_product_level_relation' AND meta_value='$lid';");
			 if ($data && isset($data->post_id)){
			 	return $data->post_id;
			 }
		 }
		 return 0;
	}
	
	public static function get_woo_product_level_relations(){
		/*
		 * @param none
		 * @return array
		 */
		 $array = array();
		 global $wpdb;
		 $table = $wpdb->prefix . 'postmeta';
		 $data = $wpdb->get_results("SELECT meta_value, post_id FROM $table WHERE meta_key='iump_woo_product_level_relation' AND meta_value!='' AND meta_value!='-1';");
		 if ($data){
		 	foreach ($data as $object){
		 		$temp['level_label'] = self::get_level_name_by_lid($object->meta_value);
				$temp['product_label'] = get_the_title($object->post_id);
				$temp['level_id'] = $object->meta_value;
				$temp['product_id'] = $object->post_id;
				$array[] = $temp;
		 	}
		 }
		 return $array;
	}

	public static function search_woo_products($search=''){
		/*
		 * @param string
		 * @return array
		 */
		$arr = array();
		if ($search){
			global $wpdb;
			$table = $wpdb->prefix . 'posts';
			$data = $wpdb->get_results("SELECT post_title, ID
											FROM $table
											WHERE
											post_title LIKE '%$search%'
											AND post_type='product'
											AND post_status='publish'
			");
			if ($data){
				foreach ($data as $object){
					$arr[$object->ID] = $object->post_title;
				}
			}
		}
		return $arr;
	}
	
	public static function unsign_woo_product_level_relation($lid=-1){
		/*
		 * @param int
		 * @return none
		 */
		 if ($lid>-1){
		 	 $product_id = self::get_woo_product_id_for_lid($lid);
			 if ($product_id){
			 	 update_post_meta($product_id, 'iump_woo_product_level_relation', '');
			 }
		 }
	}

	public static function user_get_website($uid=0){
		/*
		 * @param int
		 * @return string
		 */
		 if ($uid){
		 	 global $wpdb;
			 $table = $wpdb->base_prefix . 'users';
			 $data = $wpdb->get_row("SELECT user_url FROM $table WHERE ID=$uid;");
			 if ($data && isset($data->user_url)){
			 	 return $data->user_url;
			 }
		 }
		 return '';
	}
	
	public static function user_get_inserted_posts_count($uid=0, $since=''){
		/*
		 * @param int, string (timestamp), 
		 * @return int
		 */
		 if ($uid){
		 	 global $wpdb;
		 	 $table = $wpdb->prefix . 'posts';
			 $q = "SELECT COUNT(ID) as c FROM $table WHERE post_author=$uid AND post_status!='auto-draft' AND post_status!='trash' ";
			 if ($since){
			 	$since = date('Y-m-d H:i:s', $since);
			 	$q .= " AND post_date>'$since' ";
			 }
		 	 $data = $wpdb->get_row($q);
			 if ($data && isset($data->c)){
			 	return $data->c;
			 }
		 }
		 return 0;
	}
	
	public static function user_get_inserted_comments_count($uid=0, $since=''){
		/*
		 * @param int, string (timestamp)
		 * @return int
		 */
		 if ($uid){
		 	 global $wpdb;
		 	 $table = $wpdb->prefix . 'comments';
			 $q = "SELECT COUNT(comment_ID) as c FROM $table WHERE user_id=$uid ";
			 if ($since){
			 	$since = date('Y-m-d H:i:s', $since);
			 	$q .= " AND comment_date>'$since' ";
			 }			 
		 	 $data = $wpdb->get_row($q);
			 if ($data && isset($data->c)){
			 	return $data->c;
			 }		 	
		 }
		 return 0;
	}
	
	public static function user_get_expire_time_for_level($uid=0, $lid=FALSE){
		/*
		 * @param int, int
		 * @return string || boolean
		 */
		 if ($uid && $lid!==FALSE){		
			 global $wpdb;
			 $table = $wpdb->prefix . 'ihc_user_levels';
			 $data = $wpdb->get_row("SELECT expire_time FROM $table WHERE user_id=$uid AND level_id=$lid;");
			 if ($data && !empty($data->expire_time)){
			 	 return $data->expire_time; 
			 }
		 }
		 return FALSE;
	}
		
	public static function do_delete_comment($comment_id=0){
		/*
		 * @param int
		 * @return none
		 */
		if ($comment_id){
			global $wpdb;
			$comments = $wpdb->prefix . 'comments'; 
			$commentmeta = $wpdb->prefix . 'commentmeta';
			$wpdb->query("DELETE FROM $comments WHERE comment_ID=$comment_id;");
			$wpdb->query("DELETE FROM $commentmeta WHERE comment_id=$comment_id;"); 		 			
		}
	}
	
	public static function do_delete_post($post_ID=0){
		/*
		 * @param int
		 * @return none
		 */
		 if ($post_ID){
			 global $wpdb;
			 $posts = $wpdb->prefix . 'posts'; 
			 $postmeta = $wpdb->prefix . 'postmeta';
			 $wpdb->query("DELETE FROM $posts WHERE ID=$post_ID;");
			 $wpdb->query("DELETE FROM $postmeta WHERE post_id=$post_ID;"); 			 	 
		 }
	}
	
	public static function level_get_delay_time($lid=-1){
		/*
		 * @param int
		 * @return int || boolean
		 */
		 if ($lid>-1){
		 	 $time_value = get_option('ihc_subscription_delay_time');
		 	 $time_type = get_option('ihc_subscription_delay_type');
			 if ($time_value && $time_type){
			 	 if (isset($time_value[$lid]) && isset($time_type[$lid])){
			 	 	 if ($time_type[$lid]=='h'){
			 	 	 	 ///hours
			 	 	 	 return $time_value[$lid] * 3600;
			 	 	 } else {
			 	 	 	 /// days
			 	 	 	 return $time_value[$lid] * 24 * 3600;
			 	 	 }
			 	 }
			 }
		 }
		 return FALSE;
	}

	
	/*
	 * @param array
	 * @return bool
	 */	
	public static function account_page_menu_save_custom_item($array=array()){
		 $data = get_option('ihc_account_page_custom_menu_items');
		 $slug = $array['ihc_account_page_menu_add_new-the_slug'];
		 if ($data && isset($data[$slug])){
		 	return FALSE; /// slug already exists
		 } else {
		 	$data[$slug] = array(
									'label' => $array['ihc_account_page_menu_add_new-the_label'],
			);
			$tempkey = 'ihc_ap_' . $slug . '_icon_code'; 
			update_option($tempkey, $array['ihc_account_page_menu_add_new-the_icon_code']);
			$tempkey = 'ihc_ap_' . $slug . '_icon_class'; 
			update_option($tempkey, $array['ihc_account_page_menu_add_new-the_icon_class']);			
		 }
		 update_option('ihc_account_page_custom_menu_items', $data);
		 return TRUE;
	}


	/*
	 * @param none
	 * @return array
	 */
	 public static function account_page_menu_get_custom_items(){
		$data = get_option('ihc_account_page_custom_menu_items');
		if ($data){
			foreach ($data as $slug => $array){
				$tempkey = 'ihc_ap_' . $slug . '_icon_code'; 
				$data[$slug]['icon'] = get_option($tempkey);	
				$tempkey = 'ihc_ap_' . $slug . '_icon_class'; 
				$data[$slug]['class'] = get_option($tempkey);
			}
		}
		return $data;
	 }
	 
	 
	 /*
	  * @param int
	  * @return noen
	  */
	  public static function account_page_menu_delete_custom_item($slug=''){
	  		if ($slug){
	  			$data = get_option('ihc_account_page_custom_menu_items');
				if (isset($data[$slug])){
					unset($data[$slug]);
				}
				update_option('ihc_account_page_custom_menu_items', $data);
	  		}
	  }
	  
	  
	  /*
	   * @param none
	   * @return array
	   */
	   public static function account_page_get_menu($only_standard=FALSE){
			$available_tabs = array(
									'overview'=> array('label' => __('Overview', 'ihc'), 'icon' => 'f015', 'icon_class' => ''),
									'profile'=> array('label' => __('Profile', 'ihc'), 'icon' => 'f007', 'icon_class' => ''),
									'subscription'=> array('label' => __('Subscription', 'ihc'), 'icon' => 'f0a1', 'icon_class' => ''),
									'social' => array('label' => __('Social Plus', 'ihc'), 'icon' => 'f067', 'icon_class' => ''),
									'orders' => array('label' => __('Orders', 'ihc'), 'icon' => 'f0d6', 'icon_class' => ''),
									'transactions'=> array('label' => __('Transactions', 'ihc'), 'icon' => 'f155', 'icon_class' => ''),	
									'membeship_gifts' => array('label' => __('Membership Gifts', 'ihc'), 'icon' => 'f06b', 'icon_class' => ''),
									'membership_cards' => array('label' => __('Membership Cards', 'ihc'), 'icon' => 'f022', 'icon_class' => ''),
									'pushover_notifications' => array('label' => __('Pushover Notifications', 'ihc'), 'icon' => 'f0f3', 'icon_class' => ''),
									'help' => array('label' => __('Help', 'ihc'), 'icon' => 'f059', 'icon_class' => ''),	
									'affiliate' => array('label' => __('Affiliate', 'ihc'), 'icon' => 'f0e8', 'icon_class' => ''),	
									'logout' => array('label' => 'LogOut', 'icon' => 'f08b', 'icon_class' => ''),						
			);
			foreach ($available_tabs as $slug=>$array_data){
				$tempkey = 'ihc_ap_' . $slug . '_icon_code'; 
				$temp_data = get_option($tempkey);
				if ($temp_data){
					$available_tabs[$slug]['icon'] = $temp_data;
				}
			}
			if ($only_standard){
				return $available_tabs;
			}
			$custom_available_tabs = Ihc_Db::account_page_menu_get_custom_items();
			if (!empty($custom_available_tabs)){
				$available_tabs = array_merge($available_tabs, $custom_available_tabs);				
			}
			$available_tabs = ihc_reorder_menu_items(get_option('ihc_account_page_menu_order'), $available_tabs); 
			return $available_tabs;
	   }


	/*
	 * used in account_page.php
	 * @param array
	 * @return none
	 */
	public static function account_page_save_tabs_details($array=array()){
		$keys = self::account_page_get_menu();
		foreach ($keys as $key => $extra){
			$tempkey = 'ihc_ap_' . $key . '_menu_label'; 
			if (isset($array[$tempkey])){
				update_option($tempkey, $array[$tempkey]);				
			}
			$tempkey = 'ihc_ap_' . $key . '_title'; 
			if (isset($array[$tempkey])){
				update_option($tempkey, $array[$tempkey]);				
			}
			$tempkey = 'ihc_ap_' . $key . '_msg'; 
			if (isset($array[$tempkey])){
				update_option($tempkey, $array[$tempkey]);				
			}
			$tempkey = 'ihc_ap_' . $key . '_icon_code'; 
			if (isset($array[$tempkey])){
				update_option($tempkey, $array[$tempkey]);				
			}	
			$tempkey = 'ihc_ap_' . $key . '_icon_class'; 
			if (isset($array[$tempkey])){
				update_option($tempkey, $array[$tempkey]);				
			}						
		}
	}	
	
	
	/*
	 * @param none
	 * @return array
	 */	
	 public static function account_page_get_tabs_details(){
	 	$keys = self::account_page_get_menu();
		$return = array();
		foreach ($keys as $key => $extra){
			$tempkey = 'ihc_ap_' . $key . '_menu_label'; 
			$return[$tempkey] = get_option($tempkey);
			$tempkey = 'ihc_ap_' . $key . '_title'; 
			$return[$tempkey] = get_option($tempkey);			
			$tempkey = 'ihc_ap_' . $key . '_msg'; 
			$return[$tempkey] = get_option($tempkey);			
			$tempkey = 'ihc_ap_' . $key . '_icon_code'; 
			$return[$tempkey] = get_option($tempkey);	
			$tempkey = 'ihc_ap_' . $key . '_icon_class'; 
			$return[$tempkey] = get_option($tempkey);			
		}
		return $return;
	 }
	 
	 
	 /*
	  * @param int
	  * @return array
	  */
	  public static function user_get_all_data($uid=0){
	  		global $wpdb;
			$array = array();
			if ($uid){
				$table_users = $wpdb->base_prefix . 'users';
				$q = "SELECT * FROM $table_users WHERE ID=$uid;";
				$data = $wpdb->get_row($q);
				if ($data){
					$array = (array)$data;
				}
				$table_user_meta = $wpdb->prefix . 'usermeta';
				$q = "SELECT meta_key, meta_value FROM $table_user_meta WHERE user_id=$uid;";
				$data = $wpdb->get_results($q);
				if ($data){
					foreach ($data as $object){
						$array[$object->meta_key] = $object->meta_value;
					}									
				}
			}
			return $array;
	  }
	 
	 
	 /*
	  * @param none
	  * @return array
	  */
	  public static function get_font_awesome_codes(){
			return array(
							 "fa-ihc-glass" => "f000",
							 "fa-ihc-music" => "f001",
							 "fa-ihc-search" => "f002",
							 "fa-ihc-envelope-o" => "f003",
							 "fa-ihc-heart" => "f004",
							 "fa-ihc-star" => "f005",
							 "fa-ihc-star-o" => "f006",
							 "fa-ihc-user" => "f007",
							 "fa-ihc-film" => "f008",
							 "fa-ihc-th-large" => "f009",
							 "fa-ihc-th" => "f00a",
							 "fa-ihc-th-list" => "f00b",
							 "fa-ihc-check" => "f00c",
							 "fa-ihc-times" => "f00d",
							 "fa-ihc-search-plus" => "f00e",
							 "fa-ihc-search-minus" => "f010",
							 "fa-ihc-power-off" => "f011",
							 "fa-ihc-signal" => "f012",
							 "fa-ihc-cog" => "f013",
							 "fa-ihc-trash-o" => "f014",
							 "fa-ihc-home" => "f015",
							 "fa-ihc-file-o" => "f016",
							 "fa-ihc-clock-o" => "f017",
							 "fa-ihc-road" => "f018",
							 "fa-ihc-download" => "f019",
							 "fa-ihc-arrow-circle-o-down" => "f01a",
							 "fa-ihc-arrow-circle-o-up" => "f01b",
							 "fa-ihc-inbox" => "f01c",
							 "fa-ihc-play-circle-o" => "f01d",
							 "fa-ihc-repeat" => "f01e",
							 "fa-ihc-refresh" => "f021",
							 "fa-ihc-list-alt" => "f022",
							 "fa-ihc-lock" => "f023",
							 "fa-ihc-flag" => "f024",
							 "fa-ihc-headphones" => "f025",
							 "fa-ihc-volume-off" => "f026",
							 "fa-ihc-volume-down" => "f027",
							 "fa-ihc-volume-up" => "f028",
							 "fa-ihc-qrcode" => "f029",
							 "fa-ihc-barcode" => "f02a",
							 "fa-ihc-tag" => "f02b",
							 "fa-ihc-tags" => "f02c",
							 "fa-ihc-book" => "f02d",
							 "fa-ihc-bookmark" => "f02e",
							 "fa-ihc-print" => "f02f",
							 "fa-ihc-camera" => "f030",
							 "fa-ihc-font" => "f031",
							 "fa-ihc-bold" => "f032",
							 "fa-ihc-italic" => "f033",
							 "fa-ihc-text-height" => "f034",
							 "fa-ihc-text-width" => "f035",
							 "fa-ihc-align-left" => "f036",
							 "fa-ihc-align-center" => "f037",
							 "fa-ihc-align-right" => "f038",
							 "fa-ihc-align-justify" => "f039",
							 "fa-ihc-list" => "f03a",
							 "fa-ihc-outdent" => "f03b",
							 "fa-ihc-indent" => "f03c",
							 "fa-ihc-video-camera" => "f03d",
							 "fa-ihc-picture-o" => "f03e",
							 "fa-ihc-pencil" => "f040",
							 "fa-ihc-map-marker" => "f041",
							 "fa-ihc-adjust" => "f042",
							 "fa-ihc-tint" => "f043",
							 "fa-ihc-pencil-square-o" => "f044",
							 "fa-ihc-share-square-o" => "f045",
							 "fa-ihc-check-square-o" => "f046",
							 "fa-ihc-arrows" => "f047",
							 "fa-ihc-step-backward" => "f048",
							 "fa-ihc-fast-backward" => "f049",
							 "fa-ihc-backward" => "f04a",
							 "fa-ihc-play" => "f04b",
							 "fa-ihc-pause" => "f04c",
							 "fa-ihc-stop" => "f04d",
							 "fa-ihc-forward" => "f04e",
							 "fa-ihc-fast-forward" => "f050",
							 "fa-ihc-step-forward" => "f051",
							 "fa-ihc-eject" => "f052",
							 "fa-ihc-chevron-left" => "f053",
							 "fa-ihc-chevron-right" => "f054",
							 "fa-ihc-plus-circle" => "f055",
							 "fa-ihc-minus-circle" => "f056",
							 "fa-ihc-times-circle" => "f057",
							 "fa-ihc-check-circle" => "f058",
							 "fa-ihc-question-circle" => "f059",
							 "fa-ihc-info-circle" => "f05a",
							 "fa-ihc-crosshairs" => "f05b",
							 "fa-ihc-times-circle-o" => "f05c",
							 "fa-ihc-check-circle-o" => "f05d",
							 "fa-ihc-ban" => "f05e",
							 "fa-ihc-arrow-left" => "f060",
							 "fa-ihc-arrow-right" => "f061",
							 "fa-ihc-arrow-up" => "f062",
							 "fa-ihc-arrow-down" => "f063",
							 "fa-ihc-share" => "f064",
							 "fa-ihc-expand" => "f065",
							 "fa-ihc-compress" => "f066",
							 "fa-ihc-plus" => "f067",
							 "fa-ihc-minus" => "f068",
							 "fa-ihc-asterisk" => "f069",
							 "fa-ihc-exclamation-circle" => "f06a",
							 "fa-ihc-gift" => "f06b",
							 "fa-ihc-leaf" => "f06c",
							 "fa-ihc-fire" => "f06d",
							 "fa-ihc-eye" => "f06e",
							 "fa-ihc-eye-slash" => "f070",
							 "fa-ihc-exclamation-triangle" => "f071",
							 "fa-ihc-plane" => "f072",
							 "fa-ihc-calendar" => "f073",
							 "fa-ihc-random" => "f074",
							 "fa-ihc-comment" => "f075",
							 "fa-ihc-magnet" => "f076",
							 "fa-ihc-chevron-up" => "f077",
							 "fa-ihc-chevron-down" => "f078",
							 "fa-ihc-retweet" => "f079",
							 "fa-ihc-shopping-cart" => "f07a",
							 "fa-ihc-folder" => "f07b",
							 "fa-ihc-folder-open" => "f07c",
							 "fa-ihc-arrows-v" => "f07d",
							 "fa-ihc-arrows-h" => "f07e",
							 "fa-ihc-bar-chart" => "f080",
							 "fa-ihc-twitter-square" => "f081",
							 "fa-ihc-facebook-square" => "f082",
							 "fa-ihc-camera-retro" => "f083",
							 "fa-ihc-key" => "f084",
							 "fa-ihc-cogs" => "f085",
							 "fa-ihc-comments" => "f086",
							 "fa-ihc-thumbs-o-up" => "f087",
							 "fa-ihc-thumbs-o-down" => "f088",
							 "fa-ihc-star-half" => "f089",
							 "fa-ihc-heart-o" => "f08a",
							 "fa-ihc-sign-out" => "f08b",
							 "fa-ihc-linkedin-square" => "f08c",
							 "fa-ihc-thumb-tack" => "f08d",
							 "fa-ihc-external-link" => "f08e",
							 "fa-ihc-sign-in" => "f090",
							 "fa-ihc-trophy" => "f091",
							 "fa-ihc-github-square" => "f092",
							 "fa-ihc-upload" => "f093",
							 "fa-ihc-lemon-o" => "f094",
							 "fa-ihc-phone" => "f095",
							 "fa-ihc-square-o" => "f096",
							 "fa-ihc-bookmark-o" => "f097",
							 "fa-ihc-phone-square" => "f098",
							 "fa-ihc-twitter" => "f099",
							 "fa-ihc-facebook" => "f09a",
							 "fa-ihc-github" => "f09b",
							 "fa-ihc-unlock" => "f09c",
							 "fa-ihc-credit-card" => "f09d",
							 "fa-ihc-rss" => "f09e",
							 "fa-ihc-hdd-o" => "f0a0",
							 "fa-ihc-bullhorn" => "f0a1",
							 "fa-ihc-bell" => "f0f3",
							 "fa-ihc-certificate" => "f0a3",
							 "fa-ihc-hand-o-right" => "f0a4",
							 "fa-ihc-hand-o-left" => "f0a5",
							 "fa-ihc-hand-o-up" => "f0a6",
							 "fa-ihc-hand-o-down" => "f0a7",
							 "fa-ihc-arrow-circle-left" => "f0a8",
							 "fa-ihc-arrow-circle-right" => "f0a9",
							 "fa-ihc-arrow-circle-up" => "f0aa",
							 "fa-ihc-arrow-circle-down" => "f0ab",
							 "fa-ihc-globe" => "f0ac",
							 "fa-ihc-wrench" => "f0ad",
							 "fa-ihc-tasks" => "f0ae",
							 "fa-ihc-filter" => "f0b0",
							 "fa-ihc-briefcase" => "f0b1",
							 "fa-ihc-arrows-alt" => "f0b2",
							 "fa-ihc-users" => "f0c0",
							 "fa-ihc-link" => "f0c1",
							 "fa-ihc-cloud" => "f0c2",
							 "fa-ihc-flask" => "f0c3",
							 "fa-ihc-scissors" => "f0c4",
							 "fa-ihc-files-o" => "f0c5",
							 "fa-ihc-paperclip" => "f0c6",
							 "fa-ihc-floppy-o" => "f0c7",
							 "fa-ihc-square" => "f0c8",
							 "fa-ihc-bars" => "f0c9",
							 "fa-ihc-list-ul" => "f0ca",
							 "fa-ihc-list-ol" => "f0cb",
							 "fa-ihc-strikethrough" => "f0cc",
							 "fa-ihc-underline" => "f0cd",
							 "fa-ihc-table" => "f0ce",
							 "fa-ihc-magic" => "f0d0",
							 "fa-ihc-truck" => "f0d1",
							 "fa-ihc-pinterest" => "f0d2",
							 "fa-ihc-pinterest-square" => "f0d3",
							 "fa-ihc-google-plus-square" => "f0d4",
							 "fa-ihc-google-plus" => "f0d5",
							 "fa-ihc-money" => "f0d6",
							 "fa-ihc-caret-down" => "f0d7",
							 "fa-ihc-caret-up" => "f0d8",
							 "fa-ihc-caret-left" => "f0d9",
							 "fa-ihc-caret-right" => "f0da",
							 "fa-ihc-columns" => "f0db",
							 "fa-ihc-sort" => "f0dc",
							 "fa-ihc-sort-desc" => "f0dd",
							 "fa-ihc-sort-asc" => "f0de",
							 "fa-ihc-envelope" => "f0e0",
							 "fa-ihc-linkedin" => "f0e1",
							 "fa-ihc-undo" => "f0e2",
							 "fa-ihc-gavel" => "f0e3",
							 "fa-ihc-tachometer" => "f0e4",
							 "fa-ihc-comment-o" => "f0e5",
							 "fa-ihc-comments-o" => "f0e6",
							 "fa-ihc-bolt" => "f0e7",
							 "fa-ihc-sitemap" => "f0e8",
							 "fa-ihc-umbrella" => "f0e9",
							 "fa-ihc-clipboard" => "f0ea",
							 "fa-ihc-lightbulb-o" => "f0eb",
							 "fa-ihc-exchange" => "f0ec",
							 "fa-ihc-cloud-download" => "f0ed",
							 "fa-ihc-cloud-upload" => "f0ee",
							 "fa-ihc-user-md" => "f0f0",
							 "fa-ihc-stethoscope" => "f0f1",
							 "fa-ihc-suitcase" => "f0f2",
							 "fa-ihc-bell-o" => "f0a2",
							 "fa-ihc-coffee" => "f0f4",
							 "fa-ihc-cutlery" => "f0f5",
							 "fa-ihc-file-text-o" => "f0f6",
							 "fa-ihc-building-o" => "f0f7",
							 "fa-ihc-hospital-o" => "f0f8",
							 "fa-ihc-ambulance" => "f0f9",
							 "fa-ihc-medkit" => "f0fa",
							 "fa-ihc-fighter-jet" => "f0fb",
							 "fa-ihc-beer" => "f0fc",
							 "fa-ihc-h-square" => "f0fd",
							 "fa-ihc-plus-square" => "f0fe",
							 "fa-ihc-angle-double-left" => "f100",
							 "fa-ihc-angle-double-right" => "f101",
							 "fa-ihc-angle-double-up" => "f102",
							 "fa-ihc-angle-double-down" => "f103",
							 "fa-ihc-angle-left" => "f104",
							 "fa-ihc-angle-right" => "f105",
							 "fa-ihc-angle-up" => "f106",
							 "fa-ihc-angle-down" => "f107",
							 "fa-ihc-desktop" => "f108",
							 "fa-ihc-laptop" => "f109",
							 "fa-ihc-tablet" => "f10a",
							 "fa-ihc-mobile" => "f10b",
							 "fa-ihc-circle-o" => "f10c",
							 "fa-ihc-quote-left" => "f10d",
							 "fa-ihc-quote-right" => "f10e",
							 "fa-ihc-spinner" => "f110",
							 "fa-ihc-circle" => "f111",
							 "fa-ihc-reply" => "f112",
							 "fa-ihc-github-alt" => "f113",
							 "fa-ihc-folder-o" => "f114",
							 "fa-ihc-folder-open-o" => "f115",
							 "fa-ihc-smile-o" => "f118",
							 "fa-ihc-frown-o" => "f119",
							 "fa-ihc-meh-o" => "f11a",
							 "fa-ihc-gamepad" => "f11b",
							 "fa-ihc-keyboard-o" => "f11c",
							 "fa-ihc-flag-o" => "f11d",
							 "fa-ihc-flag-checkered" => "f11e",
							 "fa-ihc-terminal" => "f120",
							 "fa-ihc-code" => "f121",
							 "fa-ihc-reply-all" => "f122",
							 "fa-ihc-star-half-o" => "f123",
							 "fa-ihc-location-arrow" => "f124",
							 "fa-ihc-crop" => "f125",
							 "fa-ihc-code-fork" => "f126",
							 "fa-ihc-chain-broken" => "f127",
							 "fa-ihc-question" => "f128",
							 "fa-ihc-info" => "f129",
							 "fa-ihc-exclamation" => "f12a",
							 "fa-ihc-superscript" => "f12b",
							 "fa-ihc-subscript" => "f12c",
							 "fa-ihc-eraser" => "f12d",
							 "fa-ihc-puzzle-piece" => "f12e",
							 "fa-ihc-microphone" => "f130",
							 "fa-ihc-microphone-slash" => "f131",
							 "fa-ihc-shield" => "f132",
							 "fa-ihc-calendar-o" => "f133",
							 "fa-ihc-fire-extinguisher" => "f134",
							 "fa-ihc-rocket" => "f135",
							 "fa-ihc-maxcdn" => "f136",
							 "fa-ihc-chevron-circle-left" => "f137",
							 "fa-ihc-chevron-circle-right" => "f138",
							 "fa-ihc-chevron-circle-up" => "f139",
							 "fa-ihc-chevron-circle-down" => "f13a",
							 "fa-ihc-html5" => "f13b",
							 "fa-ihc-css3" => "f13c",
							 "fa-ihc-anchor" => "f13d",
							 "fa-ihc-unlock-alt" => "f13e",
							 "fa-ihc-bullseye" => "f140",
							 "fa-ihc-ellipsis-h" => "f141",
							 "fa-ihc-ellipsis-v" => "f142",
							 "fa-ihc-rss-square" => "f143",
							 "fa-ihc-play-circle" => "f144",
							 "fa-ihc-ticket" => "f145",
							 "fa-ihc-minus-square" => "f146",
							 "fa-ihc-minus-square-o" => "f147",
							 "fa-ihc-level-up" => "f148",
							 "fa-ihc-level-down" => "f149",
							 "fa-ihc-check-square" => "f14a",
							 "fa-ihc-pencil-square" => "f14b",
							 "fa-ihc-external-link-square" => "f14c",
							 "fa-ihc-share-square" => "f14d",
							 "fa-ihc-compass" => "f14e",
							 "fa-ihc-caret-square-o-down" => "f150",
							 "fa-ihc-caret-square-o-up" => "f151",
							 "fa-ihc-caret-square-o-right" => "f152",
							 "fa-ihc-eur" => "f153",
							 "fa-ihc-gbp" => "f154",
							 "fa-ihc-usd" => "f155",
							 "fa-ihc-inr" => "f156",
							 "fa-ihc-jpy" => "f157",
							 "fa-ihc-rub" => "f158",
							 "fa-ihc-krw" => "f159",
							 "fa-ihc-btc" => "f15a",
							 "fa-ihc-file" => "f15b",
							 "fa-ihc-file-text" => "f15c",
							 "fa-ihc-sort-alpha-asc" => "f15d",
							 "fa-ihc-sort-alpha-desc" => "f15e",
							 "fa-ihc-sort-amount-asc" => "f160",
							 "fa-ihc-sort-amount-desc" => "f161",
							 "fa-ihc-sort-numeric-asc" => "f162",
							 "fa-ihc-sort-numeric-desc" => "f163",
							 "fa-ihc-thumbs-up" => "f164",
							 "fa-ihc-thumbs-down" => "f165",
							 "fa-ihc-youtube-square" => "f166",
							 "fa-ihc-youtube" => "f167",
							 "fa-ihc-xing" => "f168",
							 "fa-ihc-xing-square" => "f169",
							 "fa-ihc-youtube-play" => "f16a",
							 "fa-ihc-dropbox" => "f16b",
							 "fa-ihc-stack-overflow" => "f16c",
							 "fa-ihc-instagram" => "f16d",
							 "fa-ihc-flickr" => "f16e",
							 "fa-ihc-adn" => "f170",
							 "fa-ihc-bitbucket" => "f171",
							 "fa-ihc-bitbucket-square" => "f172",
							 "fa-ihc-tumblr" => "f173",
							 "fa-ihc-tumblr-square" => "f174",
							 "fa-ihc-long-arrow-down" => "f175",
							 "fa-ihc-long-arrow-up" => "f176",
							 "fa-ihc-long-arrow-left" => "f177",
							 "fa-ihc-long-arrow-right" => "f178",
							 "fa-ihc-apple" => "f179",
							 "fa-ihc-windows" => "f17a",
							 "fa-ihc-android" => "f17b",
							 "fa-ihc-linux" => "f17c",
							 "fa-ihc-dribbble" => "f17d",
							 "fa-ihc-skype" => "f17e",
							 "fa-ihc-foursquare" => "f180",
							 "fa-ihc-trello" => "f181",
							 "fa-ihc-female" => "f182",
							 "fa-ihc-male" => "f183",
							 "fa-ihc-gittip" => "f184",
							 "fa-ihc-sun-o" => "f185",
							 "fa-ihc-moon-o" => "f186",
							 "fa-ihc-archive" => "f187",
							 "fa-ihc-bug" => "f188",
							 "fa-ihc-vk" => "f189",
							 "fa-ihc-weibo" => "f18a",
							 "fa-ihc-renren" => "f18b",
							 "fa-ihc-pagelines" => "f18c",
							 "fa-ihc-stack-exchange" => "f18d",
							 "fa-ihc-arrow-circle-o-right" => "f18e",
							 "fa-ihc-arrow-circle-o-left" => "f190",
							 "fa-ihc-caret-square-o-left" => "f191",
							 "fa-ihc-dot-circle-o" => "f192",
							 "fa-ihc-wheelchair" => "f193",
							 "fa-ihc-vimeo-square" => "f194",
							 "fa-ihc-try" => "f195",
							 "fa-ihc-plus-square-o" => "f196",
							 "fa-ihc-space-shuttle" => "f197",
							 "fa-ihc-slack" => "f198",
							 "fa-ihc-envelope-square" => "f199",
							 "fa-ihc-wordpress" => "f19a",
							 "fa-ihc-openid" => "f19b",
							 "fa-ihc-university" => "f19c",
							 "fa-ihc-graduation-cap" => "f19d",
							 "fa-ihc-yahoo" => "f19e",
							 "fa-ihc-google" => "f1a0",
							 "fa-ihc-reddit" => "f1a1",
							 "fa-ihc-reddit-square" => "f1a2",
							 "fa-ihc-stumbleupon-circle" => "f1a3",
							 "fa-ihc-stumbleupon" => "f1a4",
							 "fa-ihc-delicious" => "f1a5",
							 "fa-ihc-digg" => "f1a6",
							 "fa-ihc-pied-piper" => "f1a7",
							 "fa-ihc-pied-piper-alt" => "f1a8",
							 "fa-ihc-drupal" => "f1a9",
							 "fa-ihc-joomla" => "f1aa",
							 "fa-ihc-language" => "f1ab",
							 "fa-ihc-fax" => "f1ac",
							 "fa-ihc-building" => "f1ad",
							 "fa-ihc-child" => "f1ae",
							 "fa-ihc-paw" => "f1b0",
							 "fa-ihc-spoon" => "f1b1",
							 "fa-ihc-cube" => "f1b2",
							 "fa-ihc-cubes" => "f1b3",
							 "fa-ihc-behance" => "f1b4",
							 "fa-ihc-behance-square" => "f1b5",
							 "fa-ihc-steam" => "f1b6",
							 "fa-ihc-steam-square" => "f1b7",
							 "fa-ihc-recycle" => "f1b8",
							 "fa-ihc-car" => "f1b9",
							 "fa-ihc-taxi" => "f1ba",
							 "fa-ihc-tree" => "f1bb",
							 "fa-ihc-spotify" => "f1bc",
							 "fa-ihc-deviantart" => "f1bd",
							 "fa-ihc-soundcloud" => "f1be",
							 "fa-ihc-database" => "f1c0",
							 "fa-ihc-file-pdf-o" => "f1c1",
							 "fa-ihc-file-word-o" => "f1c2",
							 "fa-ihc-file-excel-o" => "f1c3",
							 "fa-ihc-file-powerpoint-o" => "f1c4",
							 "fa-ihc-file-image-o" => "f1c5",
							 "fa-ihc-file-archive-o" => "f1c6",
							 "fa-ihc-file-audio-o" => "f1c7",
							 "fa-ihc-file-video-o" => "f1c8",
							 "fa-ihc-file-code-o" => "f1c9",
							 "fa-ihc-vine" => "f1ca",
							 "fa-ihc-codepen" => "f1cb",
							 "fa-ihc-jsfiddle" => "f1cc",
							 "fa-ihc-life-ring" => "f1cd",
							 "fa-ihc-circle-o-notch" => "f1ce",
							 "fa-ihc-rebel" => "f1d0",
							 "fa-ihc-empire" => "f1d1",
							 "fa-ihc-git-square" => "f1d2",
							 "fa-ihc-git" => "f1d3",
							 "fa-ihc-hacker-news" => "f1d4",
							 "fa-ihc-tencent-weibo" => "f1d5",
							 "fa-ihc-qq" => "f1d6",
							 "fa-ihc-weixin" => "f1d7",
							 "fa-ihc-paper-plane" => "f1d8",
							 "fa-ihc-paper-plane-o" => "f1d9",
							 "fa-ihc-history" => "f1da",
							 "fa-ihc-circle-thin" => "f1db",
							 "fa-ihc-header" => "f1dc",
							 "fa-ihc-paragraph" => "f1dd",
							 "fa-ihc-sliders" => "f1de",
							 "fa-ihc-share-alt" => "f1e0",
							 "fa-ihc-share-alt-square" => "f1e1",
							 "fa-ihc-bomb" => "f1e2",
							 "fa-ihc-futbol-o" => "f1e3",
							 "fa-ihc-tty" => "f1e4",
							 "fa-ihc-binoculars" => "f1e5",
							 "fa-ihc-plug" => "f1e6",
							 "fa-ihc-slideshare" => "f1e7",
							 "fa-ihc-twitch" => "f1e8",
							 "fa-ihc-yelp" => "f1e9",
							 "fa-ihc-newspaper-o" => "f1ea",
							 "fa-ihc-wifi" => "f1eb",
							 "fa-ihc-calculator" => "f1ec",
							 "fa-ihc-paypal" => "f1ed",
							 "fa-ihc-google-wallet" => "f1ee",
							 "fa-ihc-cc-visa" => "f1f0",
							 "fa-ihc-cc-mastercard" => "f1f1",
							 "fa-ihc-cc-discover" => "f1f2",
							 "fa-ihc-cc-amex" => "f1f3",
							 "fa-ihc-cc-paypal" => "f1f4",
							 "fa-ihc-cc-stripe" => "f1f5",
							 "fa-ihc-bell-slash" => "f1f6",
							 "fa-ihc-bell-slash-o" => "f1f7",
							 "fa-ihc-trash" => "f1f8",
							 "fa-ihc-copyright" => "f1f9",
							 "fa-ihc-at" => "f1fa",
							 "fa-ihc-eyedropper" => "f1fb",
							 "fa-ihc-paint-brush" => "f1fc",
							 "fa-ihc-birthday-cake" => "f1fd",
							 "fa-ihc-area-chart" => "f1fe",
							 "fa-ihc-pie-chart" => "f200",
							 "fa-ihc-line-chart" => "f201",
							 "fa-ihc-lastfm" => "f202",
							 "fa-ihc-lastfm-square" => "f203",
							 "fa-ihc-toggle-off" => "f204",
							 "fa-ihc-toggle-on" => "f205",
							 "fa-ihc-bicycle" => "f206",
							 "fa-ihc-bus" => "f207",
							 "fa-ihc-ioxhost" => "f208",
							 "fa-ihc-angellist" => "f209",
							 "fa-ihc-cc" => "f20a",
							 "fa-ihc-ils" => "f20b",
							 "fa-ihc-meanpath" => "f20c",
			);	  	
	  }
	 
	 
	 /*
	  * @param int, int
	  * @return array
	  */
	 public static function user_get_level_details($uid=0, $lid=-1){
	 	$data = array();
	 	global $wpdb;
		if ($uid && $lid>-1){
		 	$table = $wpdb->prefix . 'ihc_user_levels';
		 	$data = $wpdb->get_results("SELECT start_time, update_time, expire_time FROM $table WHERE user_id=$uid AND level_id=$lid");		
		 	return (array)$data;
		}
		return $data;
	 }
	 
	 
	 /*
	  * @param int, bool
	  * @return array
	  */
	 public static function get_level_users_list($lid=-1, $only_active=FALSE){
	 	global $wpdb;
	 	$data = array();
		if ($lid>-1){
		 	$table = $wpdb->prefix . 'ihc_user_levels';
		 	$data = $wpdb->get_results("SELECT user_id FROM $table WHERE level_id=$lid");		
			if ($data){
				foreach ($data as $object){
					$do_it = TRUE;
					if ($only_active){
						/// only active users
						if (!self::is_user_level_active($object->user_id, $lid)){
							$do_it = FALSE;
						}
					}
					if ($do_it){
						$array['username'] = self::get_username_by_wpuid($object->user_id);
						$array['user_id'] = $object->user_id;
						$data[] = array(
										'username' => self::get_username_by_wpuid($object->user_id),
										'user_id' => $object->user_id,
						);
					}
				}
			}
		}
		return $data;
	 }
	 	 
	 	 
	 /*
	  * @param string, string
	  * @return array
	  */ 
	 public static function search_user_by_term_name_term_value($term_name='', $term_value=''){
	 	 global $wpdb;
	 	 if ($term_name){
	 	 	 $search_into_users = array(
										'display_name',
										'user_registered',
										'user_nicename',
										'user_email',
										'user_login', 
			 );
			 if (in_array($term_name, $search_into_users)){
			 	 $table = $wpdb->base_prefix . 'users';
				 $q = "SELECT ID FROM $table WHERE $term_name LIKE '%$term_value%';";	
			 } else {
	 	 	 	 $table = $wpdb->base_prefix . 'usermeta';
		 	 	 $q = "SELECT user_id FROM $table WHERE meta_key LIKE '%$term_name%' AND meta_value LIKE '%$term_value%';";		 	
			 }
			 $data = $wpdb->get_results($q);
			 if ($data){
			 	return (array)$data;
			 }				 
	 	 }
	 	 return array();
	 }


	/*
	 * @param int ($post_id)
	 * @param string ($meta_key)
	 * @return bool
	 */
	 public static function does_post_meta_exists($post_id=0, $meta_key=''){
	 	 global $wpdb;
		 $table = $wpdb->prefix . 'postmeta';
		 $data = $wpdb->get_row("SELECT meta_id FROM $table WHERE post_id=$post_id AND meta_key='$meta_key';");
		 if (isset($data->meta_id)){
		 	return TRUE;
		 }
		 return FALSE;
	 }
	 
	 
	 /*
	  * Get all post meta values and post id for a post meta key
	  * @param string ($post_meta_key)
	  * @return array
	  */
	 public static function get_all_post_meta_data_for_meta_key($meta_key=''){
	 	 global $wpdb;
		 $array = array();
		 $table = $wpdb->prefix . 'postmeta';
		 $data = $wpdb->get_results("SELECT post_id, meta_value FROM $table WHERE meta_key='$meta_key';");
		 if ($data){
		 	foreach ($data as $object){
		 		$array[] = (array)$object;
		 	}
		 }
		 return $array;
	 }
	 
	 
	 /*
	  * @param none
	  * @return array
	  */
	 public static function get_post_meta_keys_used_in_ump(){
	 	return array(
						'ihc_mb_type',
						'ihc_mb_who',
						'ihc_mb_block_type',
						'ihc_mb_redirect_to',
						'ihc_replace_content',
						'ihc_drip_content',
						'ihc_drip_start_type',
						'ihc_drip_end_type',
						'ihc_drip_start_numeric_type',
						'ihc_drip_start_numeric_value',	
						'ihc_drip_end_numeric_type',
						'ihc_drip_end_numeric_value',	
						'ihc_drip_start_certain_date',	
						'ihc_drip_end_certain_date',
		);
	 }
	 
	 
	 /*
	  * Return all settings with values
	  * @param none
	  * @return array
	  */
	 public static function get_all_ump_wp_options($except=array('general-defaults')){
	 	 $search_groups = array(
		 							'payment',
		 							'payment_paypal',
		 							'payment_stripe',
		 							'payment_authorize',
		 							'payment_twocheckout',
		 							'payment_bank_transfer',
		 							'payment_braintree',
		 							'payment_payza',
		 							'login',
		 							'login-messages',
		 							'general-captcha',
		 							'general-subscription',
		 							'general-msg',
		 							'general-defaults',
		 							'register',
		 							'register-msg',
		 							'register-custom-fields',
		 							'opt_in',
		 							'notifications',
		 							'extra_settings',
		 							'account_page',
		 							'fb',
		 							'tw',
		 							'in',
		 							'tbr',
		 							'ig',
		 							'vk',
		 							'goo',
		 							'social_media',
		 							'double_email_verification',
		 							'licensing',
		 							'listing_users',
		 							'listing_users_inside_page',
		 							'affiliate_options',
		 							'ihc_taxes_settings',
		 							'admin_workflow',
		 							'public_workflow',
		 							'ihc_woo',
		 							'ihc_bp',
		 							'ihc_membership_card',
		 							'ihc_cheat_off',
		 							'ihc_invitation_code',
		 							'download_monitor_integration',
		 							'register_lite', 
		 							'individual_page',
		 							'level_restrict_payment',
		 							'level_subscription_plan_settings',
		 							'gifts',
		 							'login_level_redirect',
		 							'wp_social_login',
		 							'list_access_posts',
		 							'invoices',
		 							'woo_payment',
		 							'badges',
		 							'login_security',
		 							'workflow_restrictions',
		 							'subscription_delay',
		 							'level_dynamic_price',
		 							'user_reports',
		 							'pushover',
		 							'account_page_menu',
		 							'mycred',
		 							'api',
		 );
		 if ($except){
			 foreach ($except as $value){
			 	$key = array_search($value, $search_groups);
				if ($key!==FALSE){
				 	unset($search_groups[$key]);					
				}
			 }		 	
		 }
		 $array = array();
		 foreach ($search_groups as $key_group){
		 	 $temp = ihc_return_meta_arr($key_group);
			 $array = array_merge($array, $temp);
		 }
		 return $array;
	 }


	/*
	 * @param int (user id)
	 * @param string (meta name)
	 * @return bool
	 */
	public static function does_usermeta_exists($uid=0, $key_meta=''){
	 	 global $wpdb;
		 $table = $wpdb->prefix . 'usermeta';
		 $data = $wpdb->get_row("SELECT umeta_id FROM $table WHERE user_id=$uid AND meta_key='$key_meta';");
		 if (isset($data->umeta_id)){
		 	return TRUE;
		 }
		 return FALSE;		
	}


	/*
	 * @param array
	 * @return bool
	 */
	public static function custom_insert_user_with_ID($userdata=array()){
		global $wpdb;
		$table = $wpdb->prefix . 'users';
		foreach ($userdata as $key=>$check_data){
			if (empty($userdata[$key]) || is_object($userdata[$key])){
				$userdata[$key] = '';
			}
		}					
		return $wpdb->query("INSERT INTO $table VALUES(
														'{$userdata['ID']}', 
														'{$userdata['user_login']}', 
														'{$userdata['user_pass']}', 
														'{$userdata['user_nicename']}',
														'{$userdata['user_email']}',
														'{$userdata['user_url']}',	
														'{$userdata['user_registered']}',	
														'{$userdata['user_activation_key']}',	
														'{$userdata['user_status']}',			
														'{$userdata['display_name']}'							
		);");	
	}
	
	
	/*
	 * @param array
	 * @return bool
	 */
	public static function custom_insert_usermeta($uid=0, $key_meta='', $meta_value=''){
		global $wpdb;
		$table = $wpdb->prefix . 'usermeta';
		return $wpdb->query("INSERT INTO $table VALUES(
														null,
														$uid,
														'$key_meta',
														'$meta_value'																
		);");			
	}
	
	
	/*
	 * @param int (user id)
	 * @param string (selected row)
	 * @return string
	 */
	public static function get_user_col_value($uid=0, $col_name=''){
		if ($uid && $col_name){
			global $wpdb;
			$table = $wpdb->base_prefix . 'users';
			$data = $wpdb->get_row("SELECT $col_name FROM $table WHERE ID=$uid;");
			if (!empty($data->$col_name)){
				return $data->$col_name;
			}
		}
	}
	
}	
	
endif;


