<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Allows plugins to use their own update API.
 *
 * @author Easy Digital Downloads
 * @version 1.6.8
 */
class GFEwayProEddUpdater {

	private $api_url     = '';
	private $api_data    = array();
	private $plugin_file = '';
	private $name        = '';
	private $slug        = '';
	private $version     = '';
	private $wp_override = false;
	private $cache_key   = '';

	/**
	 * Class constructor.
	 *
	 * @uses plugin_basename()
	 * @uses hook()
	 *
	 * @param string  $_api_url     The URL pointing to the custom API endpoint.
	 * @param string  $_plugin_file Path to the plugin file.
	 * @param array   $_api_data    Optional data to send with API calls.
	 */
	public function __construct( $_api_url, $_plugin_file, $_api_data = null ) {

		$this->api_url     = trailingslashit( $_api_url );
		$this->api_data    = $_api_data;
		$this->plugin_file = $_plugin_file;
		$this->name        = plugin_basename( $_plugin_file );
		$this->slug        = basename( $_plugin_file, '.php' );
		$this->version     = $_api_data['version'];
		$this->wp_override = isset( $_api_data['wp_override'] ) ? (bool) $_api_data['wp_override'] : false;

		$this->cache_key   = 'edd_sl_' . md5( $this->slug . '_version_info' );

		// Set up hooks.
		$this->init();

	}

	/**
	 * Set up WordPress filters to hook into WP's update process.
	 *
	 * @uses add_filter()
	 *
	 * @return void
	 */
	public function init() {

		add_filter( 'pre_set_site_transient_update_plugins', array( $this, 'check_update' ), 10 );
		add_filter( 'plugins_api', array( $this, 'plugins_api_filter' ), 10, 3 );
		remove_action( 'after_plugin_row_' . $this->name, 'wp_plugin_update_row', 10 );
		add_action( 'after_plugin_row_' . $this->name, array( $this, 'show_update_notification' ), 10, 2 );
		add_action( 'admin_init', array( $this, 'show_changelog' ) );

		add_filter( 'plugin_row_meta', array($this, 'plugin_details_link'), 5, 2 );

		add_action("wp_ajax_{$this->slug}-license-activate", array($this, 'ajaxLicenseAction'));
		add_action("wp_ajax_{$this->slug}-license-deactivate", array($this, 'ajaxLicenseAction'));
	}

	/**
	 * Check for Updates at the defined API endpoint and modify the update array.
	 *
	 * This function dives into the update API just when WordPress creates its update array,
	 * then adds a custom API call and injects the custom plugin data retrieved from the API.
	 * It is reassembled from parts of the native WordPress plugin update code.
	 * See wp-includes/update.php line 121 for the original wp_update_plugins() function.
	 *
	 * @uses api_request()
	 *
	 * @param array   $_transient_data Update array build by WordPress.
	 * @return array Modified update array with custom plugin data.
	 */
	public function check_update( $_transient_data ) {

		global $pagenow;

		if ( ! is_object( $_transient_data ) ) {
			$_transient_data = new stdClass;
		}

		if ( 'plugins.php' == $pagenow && is_multisite() ) {
			return $_transient_data;
		}

		if ( ! empty( $_transient_data->response ) && ! empty( $_transient_data->response[ $this->name ] ) && false === $this->wp_override ) {
			return $_transient_data;
		}

		$version_info = $this->getVersionInfo();

		if ( false !== $version_info && is_object( $version_info ) && isset( $version_info->new_version ) ) {

			if ( version_compare( $this->version, $version_info->new_version, '<' ) ) {

				$_transient_data->response[ $this->name ] = $version_info;

			}

			$_transient_data->last_checked           = current_time( 'timestamp' );
			$_transient_data->checked[ $this->name ] = $this->version;

		}

		return $_transient_data;
	}

	/**
	 * maybe change the View Details link on the plugin page
	 *
	 * @param array   $links
	 * @param string  $file
	 */
	public function plugin_details_link( $links, $file ) {
		if ( $this->name === $file && is_multisite() && current_user_can( 'install_plugins' ) ) {

			// look for View Details link
			foreach ($links as $key => $link) {
				if (strpos($link, 'plugin-install.php?tab=plugin-information') !== false) {
					$links[$key] = preg_replace_callback('#href="\K[^"]+#', array($this, 'get_plugin_details_link'), $link);
					break;
				}
			}

		}

		return $links;
	}

	/**
	* get custom link to view plugin details
	* @return string
	*/
	public function get_plugin_details_link() {
		return self_admin_url( "index.php?edd_sl_action=view_plugin_changelog&plugin={$this->slug}&slug={$this->slug}&TB_iframe=true" );
	}

	/**
	 * show update nofication row -- needed for multisite subsites, because WP won't tell you otherwise!
	 *
	 * @param string  $file
	 * @param array   $plugin
	 */
	public function show_update_notification( $file, $plugin ) {

		if ( is_network_admin() ) {
			return;
		}

		if( ! current_user_can( 'update_plugins' ) ) {
			return;
		}

		if( ! is_multisite() ) {
			return;
		}

		if ( $this->name != $file ) {
			return;
		}

		// Remove our filter on the site transient
		remove_filter( 'pre_set_site_transient_update_plugins', array( $this, 'check_update' ), 10 );

		$update_cache = get_site_transient( 'update_plugins' );

		$update_cache = is_object( $update_cache ) ? $update_cache : new stdClass();

		if ( empty( $update_cache->response ) || empty( $update_cache->response[ $this->name ] ) ) {

			$version_info = $this->getVersionInfo();

			if ( ! is_object( $version_info ) ) {
				return;
			}

			if ( version_compare( $this->version, $version_info->new_version, '<' ) ) {

				$update_cache->response[ $this->name ] = $version_info;

			}

			$update_cache->last_checked = current_time( 'timestamp' );
			$update_cache->checked[ $this->name ] = $this->version;

			set_site_transient( 'update_plugins', $update_cache );

		} else {

			$version_info = $update_cache->response[ $this->name ];

		}

		// Restore our filter
		add_filter( 'pre_set_site_transient_update_plugins', array( $this, 'check_update' ) );

		if ( ! empty( $update_cache->response[ $this->name ] ) && version_compare( $this->version, $version_info->new_version, '<' ) ) {

			// build a plugin list row, with update notification
			$wp_list_table = _get_list_table( 'WP_Plugins_List_Table' );
			$plugin_name   = esc_html( $version_info->name );
			$plugin_slug   = esc_html( $version_info->slug );
			$new_version   = esc_html( $version_info->new_version );

			global $wp_version;
			$legacy = version_compare($wp_version, '4.6-dev', '>=') ? '' : '-legacy';
			$view   = empty($version_info->download_link) ? 'details' : 'upgrade';

			include $this->api_data['base_dir'] . "views/admin-plugin-update-{$view}{$legacy}.php";
		}
	}


	/**
	 * Updates information on the "View version x.x details" page with custom data.
	 *
	 * @uses api_request()
	 *
	 * @param mixed   $_data
	 * @param string  $_action
	 * @param object  $_args
	 * @return object $_data
	 */
	public function plugins_api_filter( $_data, $_action = '', $_args = null ) {

		if ( $_action != 'plugin_information' ) {

			return $_data;

		}

		if ( ! isset( $_args->slug ) || ( $_args->slug != $this->slug ) ) {

			return $_data;

		}

		$api_response = $this->getVersionInfo();

		if ( !empty( $api_response ) ){
			$_data = $api_response;
		}

		return $_data;
	}

	/**
	* get version info, cached
	* @param bool $cached
	* @return false|object
	*/
	private function getVersionInfo($cached = true) {
		if (!empty($_GET['force-check'])) {
			$cached = false;
		}

		$version_info = $cached ? $this->get_cached_version_info() : false;

		if( false === $version_info ) {

			$version_info = $this->api_request( 'get_version', array( 'slug' => $this->slug ) );

			// duplicate version member for plugin info pop-up
			$version_info->version = $version_info->new_version;

			// add Author link
			$plugin_data = get_plugin_data($this->plugin_file, false, false);
			$version_info->author = sprintf('<a href="%s">%s</a>', esc_url($plugin_data['AuthorURI']), esc_html($plugin_data['Author']));

			// convert array of contributors' names into keyed array of contributors' profile links
			if (!empty($version_info->contributors) && is_array($version_info->contributors)) {
				reset($version_info->contributors);
				if (is_int(key($version_info->contributors))) {
					$contributors = array();
					foreach ($version_info->contributors as $contributor) {
						$contributor = sanitize_user($contributor);
						$contributors[$contributor] = "https://profiles.wordpress.org/$contributor";
					}
					$version_info->contributors = $contributors;
				}
			}

			$this->set_version_info_cache($version_info);
		}

		return $version_info;
	}

	/**
	 * Calls the API and, if successfull, returns the object delivered by the API.
	 *
	 * @uses get_bloginfo()
	 * @uses wp_remote_post()
	 * @uses is_wp_error()
	 *
	 * @param string  $_action The requested action.
	 * @param array   $_data   Parameters for the API action.
	 * @return false|object
	 */
	private function api_request( $_action, $_data ) {

		global $wp_version;

		$data = array_merge( $this->api_data, $_data );

		if ( $data['slug'] != $this->slug ) {
			return;
		}

		if( $this->api_url == trailingslashit (home_url() ) ) {
			return false; // Don't allow a plugin to ping itself
		}

		$api_params = array(
			'edd_action' => $_action,
			'license'    => ! empty( $data['license'] ) ? $data['license'] : '',
			'item_name'  => isset( $data['item_name'] ) ? $data['item_name'] : false,
			'item_id'    => isset( $data['item_id'] ) ? $data['item_id'] : false,
			'slug'       => $data['slug'],
			'author'     => $data['author'],
			'url'        => home_url(),
			'beta'       => ! empty( $data['beta'] ),
		);

		$request = wp_remote_post( $this->api_url, array( 'timeout' => 15, 'sslverify' => false, 'body' => $api_params ) );

		if ( ! is_wp_error( $request ) ) {
			$request = json_decode( wp_remote_retrieve_body( $request ) );
		}
		else {
			$request = false;
		}

		if ( $request && isset( $request->sections ) ) {
			$request->sections = maybe_unserialize( $request->sections );
		}
		elseif ( $_action === 'get_version' ) {
			// version request must return sections, otherwise it is invalid
			$request = false;
		}

		if ( $request && isset( $request->banners ) ) {
			$request->banners = maybe_unserialize( $request->banners );
		}

		return $request;
	}

	public function show_changelog() {

		if( empty( $_REQUEST['edd_sl_action'] ) || 'view_plugin_changelog' != $_REQUEST['edd_sl_action'] ) {
			return;
		}

		if( empty( $_REQUEST['plugin'] ) ) {
			return;
		}

		if( empty( $_REQUEST['slug'] ) ) {
			return;
		}

		if( ! current_user_can( 'update_plugins' ) ) {
			wp_die( translate( 'Sorry, you are not allowed to update plugins for this site.' ), translate( 'Error' ), array( 'response' => 403 ) );
		}

		global $tab, $body_id;
		$body_id = $tab = 'plugin-information';
		$_REQUEST['section'] = 'changelog';

		require_once( ABSPATH . 'wp-admin/includes/plugin-install.php' );

		wp_enqueue_style( 'plugin-install' );
		wp_enqueue_script( 'plugin-install' );
		set_current_screen();
		install_plugin_information();

		exit;
	}

	/**
	* update the license key and check license status again
	* @param string $licenseKey
	*/
	public function setLicense($licenseKey) {
		// if license key has changed, deactivate old license and record new
		if ($licenseKey !== $this->api_data['license']) {
			// deactivate the old license
			$this->api_request('deactivate_license', array('slug' => $this->slug));

			// record the new license and reset license status
			$this->api_data['license'] = $licenseKey;
			update_option($this->api_data['status_key'], '');

			if ($licenseKey) {
				// we have a new license, check its status
				$license_info = $this->api_request('activate_license', array('slug' => $this->slug));
				$status = empty($license_info->license) ? false : $license_info->license;
				update_option($this->api_data['status_key'], $status);

				// refresh plugin update info, to ensure that version info is refreshed
				delete_site_transient('update_plugins');
				$this->delete_version_info_cache();
			}
		}
	}

	/**
	* check the current license status
	* @param bool $cached
	* @return string returns 'valid' or a reason why it isn't (e.g. invalid, deactivated, expired, site_inactive)
	*/
	public function licenseCheck($cached = true) {
		$status = $cached ? get_option($this->api_data['status_key']) : false;

		if ($status !== false) {
			if (empty($this->api_data['license'])) {
				$status = '';
			}
			else {
				$license_info = $this->api_request('check_license', array('slug' => $this->slug));
				$status = empty($license_info->license) ? false : $license_info->license;
			}

			update_option($this->api_data['status_key'], $status);
		}

		return $status;
	}

	/**
	* perform a software license action
	* @return string|false returns current license status
	*/
	public function ajaxLicenseAction() {
		$action = empty($_REQUEST['action']) ? '' : $_REQUEST['action'];

		switch ($action) {

			case "{$this->slug}-license-activate":
				$action = 'activate_license';
				break;

			case "{$this->slug}-license-deactivate":
				$action = 'deactivate_license';
				break;

			default:
				// invalid action, bugger off
				wp_send_json(array('status' => 'invalid', 'error' => 'invalid action requested: ' . $action));
				break;

		}

		// make sure we actually have a license key
		if (empty($this->api_data['license'])) {
			wp_send_json(array('status' => 'invalid', 'error' => 'no license key'));
		}

		$license_info = $this->api_request($action, array('slug' => $this->slug));

		// $license_info->license will be either "valid" or something indicating why it isn't valid/activated
		switch ($license_info->license) {

			case 'deactivated':
				$status = 'inactive';
				break;

			default:
				$status = $license_info->license;
				break;

		}
		update_option($this->api_data['status_key'], $status);

		// refresh plugin update info, to ensure that version info is refreshed
		delete_site_transient('update_plugins');
		$this->delete_version_info_cache();

		wp_send_json(array('status' => $license_info->license));
	}

	public function get_cached_version_info( $cache_key = '' ) {
		if( empty( $cache_key ) ) {
			$cache_key = $this->cache_key;
		}

		$cache = get_option( $cache_key );

		if( empty( $cache['timeout'] ) || current_time( 'timestamp' ) > $cache['timeout'] ) {
			return false; // Cache is expired
		}

		return maybe_unserialize( $cache['value'] );

	}

	public function set_version_info_cache( $value = '', $cache_key = '' ) {

		if( empty( $cache_key ) ) {
			$cache_key = $this->cache_key;
		}

		$data = array(
			'timeout' => strtotime( '+3 hours', current_time( 'timestamp' ) ),
			'value'   => $value,
		);

		update_option( $cache_key, $data );

	}

	protected function delete_version_info_cache( $cache_key = '' ) {
		if( empty( $cache_key ) ) {
			$cache_key = $this->cache_key;
		}

		delete_option( $cache_key );

	}

}
