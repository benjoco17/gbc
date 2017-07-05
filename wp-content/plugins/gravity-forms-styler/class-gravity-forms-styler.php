<?php
/**
 * Gravity Forms Styler.
 *
 * @package   Gravity_Forms_Styler
 * @author    WordpressGurus <support@wordpressgurus.net>
 * @license   GPL-2.0+
 * @link      http://www.wordpressgurus.net
 * @copyright 2014 WordpressGurus
 */

/**
 * Plugin class.
 * @package Gravity_Forms_Styler
 * @author  WordpressGurus <support@wordpressgurus.net>
 */
class Gravity_Forms_Styler {

	/**
	 * @var     string
	 */
	const VERSION = '2.0';
	/**
	 * @var      string
	 */
	protected $plugin_slug = 'gravity-forms-styler';
	/**
	 * @var      object
	 */
	protected static $instance = null;
	/**
	 * @var      array
	 */
	protected $element_instances = array();
	/**
	 * @var      array
	 */
	protected $element_css_once = array();
	/**
	 * @var      array
	 */
	protected $elements = array();
	/**
	 * @var      string
	 */
	protected $plugin_screen_hook_suffix = null;
	/**
	 * Initialize the plugin by setting localization, filters, and administration functions.
	 *
	 */
	private function __construct() {

		// Load plugin text domain
		add_action( 'init', array( $this, 'load_plugin_textdomain' ) );

		// Activate plugin when new blog is added
		add_action( 'wpmu_new_blog', array( $this, 'activate_new_site' ) );

		// Load admin style sheet and JavaScript.
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_stylescripts' ) );
		
		add_action('wp_footer', array( $this, 'footer_scripts' ) );

		// Detect element before rendering the page so that we can enque scripts and styles needed
		if(!is_admin()){
			add_action( 'wp', array( $this, 'detect_elements' ) );
		}

		add_action( 'init', array( $this, 'activate_post_types' ) );
if(is_admin()){
			add_action( 'media_buttons', array($this, 'shortcode_insert_button' ), 11 );
			add_action( 'admin_footer', array( $this, 'shortcode_modal_template' ) );
		}
// Add shortcodes
		add_shortcode('gravity_forms_styler', array($this, 'render_element'));
		$this->elements = array_merge($this->elements, array(
			'shortcodes'			=>	array(
				'gravity_forms_styler' 			=> '1',
			)
		));

	}

	/**
	 * Return an instance of this class.
	 *
	 *
	 * @return    object    A single instance of this class.
	 */
	public static function get_instance() {

		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	/**
	 * Fired when the plugin is activated.
	 *
	 *
	 * @param    boolean    $network_wide    True if WPMU superadmin uses "Network Activate" action, false if WPMU is disabled or plugin is activated on an individual blog.
	 */
	public static function activate( $network_wide ) {
		if ( function_exists( 'is_multisite' ) && is_multisite() ) {
			if ( $network_wide  ) {
				// Get all blog ids
				$blog_ids = self::get_blog_ids();

				foreach ( $blog_ids as $blog_id ) {
					switch_to_blog( $blog_id );
					self::single_activate();
				}
				restore_current_blog();
			} else {
				self::single_activate();
			}
		} else {
			self::single_activate();
		}
	}

	/**
	 * Fired when the plugin is deactivated.
	 *
	 *
	 * @param    boolean    $network_wide    True if WPMU superadmin uses "Network Deactivate" action, false if WPMU is disabled or plugin is deactivated on an individual blog.
	 */
	public static function deactivate( $network_wide ) {
		if ( function_exists( 'is_multisite' ) && is_multisite() ) {
			if ( $network_wide ) {
				// Get all blog ids
				$blog_ids = self::get_blog_ids();

				foreach ( $blog_ids as $blog_id ) {
					switch_to_blog( $blog_id );
					self::single_deactivate();
				}
				restore_current_blog();
			} else {
				self::single_deactivate();
			}
		} else {
			self::single_deactivate();
		}
	}

	/**
	 * Fired when a new site is activated with a WPMU environment.
	 *
	 *
	 * @param	int	$blog_id ID of the new blog.
	 */
	public function activate_new_site( $blog_id ) {
		if ( 1 !== did_action( 'wpmu_new_blog' ) )
			return;

		switch_to_blog( $blog_id );
		self::single_activate();
		restore_current_blog();
	}

	/**
	 * Get all blog ids of blogs in the current network that are:
	 * - not archived
	 * - not spam
	 * - not deleted
	 *
	 *
	 * @return	array|false	The blog ids, false if no matches.
	 */
	private static function get_blog_ids() {
		global $wpdb;

		// get an array of blog ids
		$sql = "SELECT blog_id FROM $wpdb->blogs
			WHERE archived = '0' AND spam = '0'
			AND deleted = '0'";
		return $wpdb->get_col( $sql );
	}

	/**
	 * Fired for each blog when the plugin is activated.
	 *
	 */
	private static function single_activate() {
		// TODO: Define activation functionality here if needed
	}

	/**
	 * Fired for each blog when the plugin is deactivated.
	 *
	 */
	private static function single_deactivate() {
		// TODO: Define deactivation functionality here needed
	}

	/**
	 * Load the plugin text domain for translation.
	 *
	 */
	public function load_plugin_textdomain() {
		// TODO: Add translations as need in /languages
		$domain = $this->plugin_slug;
		$locale = apply_filters( 'plugin_locale', get_locale(), $domain );

		load_textdomain( $domain, trailingslashit( WP_LANG_DIR ) . $domain . '/' . $domain . '-' . $locale . '.mo' );
		load_plugin_textdomain( $domain, FALSE, basename( dirname( __FILE__ ) ) . '/languages' );
	}
	
	
	/**
	 * Register post types.
	 *
	 *
	 * @return    null
	 */
	public function activate_post_types() {
		
		$args = array(
			'labels' 				=> array(
				'name' 				=> __('Gravity Forms Styler', 'gravity-forms-styler'),
				'singular_name' 	=> __('Form Style', 'gravity-forms-styler'),
				'add_new' 			=> __('Add New', 'gravity-forms-styler'),
				'add_new_item' 		=> __('Add New Form Style', 'gravity-forms-styler'),
				'edit_item' 		=> __('Edit Form Style', 'gravity-forms-styler'),
				'all_items' 		=> __('Styler', 'gravity-forms-styler'),
				'view_item' 		=> __('View Form Style', 'gravity-forms-styler'),
				'search_items' 		=> __('Search Forms Styles', 'gravity-forms-styler'),
				'not_found' 		=> __('No Forms Styles defined', 'gravity-forms-styler'),
				'not_found_in_trash'=> __('No Forms Styles in trash', 'gravity-forms-styler'),
				'parent_item_colon' => '',
				'menu_name' 		=> __('Forms Styles', 'gravity-forms-styler')
			),
			'public' 				=>	false,
			'publicly_queryable'	=>	false,
			'show_ui' 				=>	true,
			'show_in_menu' 			=>	"gf_edit_forms",
			'query_var' 			=>	true,
			'rewrite' 				=>	false,
			'exclude_from_search' 	=>	false,
			'capability_type' 		=>	'post',
			'has_archive' 			=>	true,
			'hierarchical' 			=>	false,
			'menu_position' 		=>	null,
			'menu_icon'				=>	plugin_dir_url( __FILE__ ) . "assets/images/white.png",
			'supports' 				=> array(
				'title',

			),
		);
		register_post_type('gravity_forms_styler', $args);		$this->elements = array_merge($this->elements, array(
			'posttypes'			=>	array(
				'gravity_forms_styler' 			=> 'element',
			)
		));

		add_action('add_meta_boxes', array($this, 'add_metabox'), 5, 4);
		add_action('save_post', array($this, 'save_post_metaboxes'), 1, 2);
		add_filter('manage_gravity_forms_styler_posts_columns', array($this, 'posts_column'),10,2);
		add_action('manage_gravity_forms_styler_posts_custom_column', array($this, 'custom_postcolumn'), 10, 2);
		add_filter( 'post_updated_messages', array($this, 'updated_messages') );
	}

	/**
	 * setup post type messages.
	 *
	 *
	 * @return    array
	 */
	function updated_messages( $messages ) {
	  global $post, $post_ID;

	  
	
	  $messages['gravity_forms_styler'] = array(
	    0 => '', // Unused. Messages start at index 1.
	    1 => sprintf( __('Form Style updated.', 'gravity-forms-styler'), esc_url( get_permalink($post_ID) ) ),
	    2 => __('Custom field updated.', 'gravity-forms-styler'),
	    3 => __('Custom field deleted.', 'gravity-forms-styler'),
	    4 => __('Form Style updated.', 'gravity-forms-styler'),
	    /* translators: %s: date and time of the revision */
	    5 => isset($_GET['revision']) ? sprintf( __('Form Style restored to revision from %s', 'gravity-forms-styler'), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
	    6 => sprintf( __('Form Style published.', 'gravity-forms-styler'), esc_url( get_permalink($post_ID) ) ),
	    7 => __('Form Style saved.', 'gravity-forms-styler'),
	    8 => sprintf( __('Form Style submitted.', 'gravity-forms-styler'), esc_url( add_query_arg( 'preview', 'true', get_permalink($post_ID) ) ) ),
	    9 => sprintf( __('Form Style scheduled for: <strong>%1$s</strong>.', 'gravity-forms-styler'),
	      // translators: Publish box date format, see http://php.net/date
	      date_i18n( __( 'M j, Y @ G:i' ), strtotime( $post->post_date ) ), esc_url( get_permalink($post_ID) ) ),
	    10 => sprintf( __('Form Style draft updated.', 'gravity-forms-styler'), esc_url( add_query_arg( 'preview', 'true', get_permalink($post_ID) ) ) ),
	  );


	  return $messages;
	}

	
	/**
	 * setup meta boxes.
	 *
	 *
	 * @return    null
	 */
	function add_metabox($slug, $post){
		// Always good to have.
		wp_enqueue_media();
		wp_enqueue_script('media-upload');
		
		wp_enqueue_style( $this->plugin_slug . '-panel-styles', self::get_url( 'assets/css/panel.css', __FILE__ ), array(), self::VERSION );
		wp_enqueue_script( $this->plugin_slug . '-panel-script', self::get_url( 'assets/js/panel.js', __FILE__ ), array( 'jquery' ), self::VERSION );
		add_meta_box('6101571215', __('Form Settings', 'gravity-forms-styler' ), array($this, 'render_metabox'), 'gravity_forms_styler', 'normal'/* or side */, 'core', array( 'slug' => 'gravity_forms_styler', 'file' => 'form-settings') );
		add_meta_box('1215713511', __('Form Wrapper', 'gravity-forms-styler' ), array($this, 'render_metabox'), 'gravity_forms_styler', 'normal'/* or side */, 'core', array( 'slug' => 'gravity_forms_styler', 'file' => 'form-wrapper') );
		add_meta_box('141031113', __('Form Header', 'gravity-forms-styler' ), array($this, 'render_metabox'), 'gravity_forms_styler', 'normal'/* or side */, 'core', array( 'slug' => 'gravity_forms_styler', 'file' => 'form-header') );
		add_meta_box('88751415', __('Form Title', 'gravity-forms-styler' ), array($this, 'render_metabox'), 'gravity_forms_styler', 'normal'/* or side */, 'core', array( 'slug' => 'gravity_forms_styler', 'file' => 'form-title') );
		add_meta_box('91091510', __('Form Description', 'gravity-forms-styler' ), array($this, 'render_metabox'), 'gravity_forms_styler', 'normal'/* or side */, 'core', array( 'slug' => 'gravity_forms_styler', 'file' => 'form-description') );
		add_meta_box('1431071', __('Fields Labels', 'gravity-forms-styler' ), array($this, 'render_metabox'), 'gravity_forms_styler', 'normal'/* or side */, 'core', array( 'slug' => 'gravity_forms_styler', 'file' => 'fields-labels') );
		add_meta_box('2891213', __('Field Icons', 'gravity-forms-styler' ), array($this, 'render_metabox'), 'gravity_forms_styler', 'normal'/* or side */, 'core', array( 'slug' => 'gravity_forms_styler', 'file' => 'field-icons') );
		add_meta_box('1151161411', __('Outer Box Shadow', 'gravity-forms-styler' ), array($this, 'render_metabox'), 'gravity_forms_styler', 'normal'/* or side */, 'core', array( 'slug' => 'gravity_forms_styler', 'file' => 'outer-box-shadow') );
		add_meta_box('851510713', __('Inner Box Shadow', 'gravity-forms-styler' ), array($this, 'render_metabox'), 'gravity_forms_styler', 'normal'/* or side */, 'core', array( 'slug' => 'gravity_forms_styler', 'file' => 'inner-box-shadow') );
		add_meta_box('141111302', __('Text Field', 'gravity-forms-styler' ), array($this, 'render_metabox'), 'gravity_forms_styler', 'normal'/* or side */, 'core', array( 'slug' => 'gravity_forms_styler', 'file' => 'text-field') );
		add_meta_box('141115545', __(' Paragraph (Textarea) Field', 'gravity-forms-styler' ), array($this, 'render_metabox'), 'gravity_forms_styler', 'normal'/* or side */, 'core', array( 'slug' => 'gravity_forms_styler', 'file' => 'paragraph-textarea-field') );
		add_meta_box('0510004', __(' Drop-Down (Select) Field ', 'gravity-forms-styler' ), array($this, 'render_metabox'), 'gravity_forms_styler', 'normal'/* or side */, 'core', array( 'slug' => 'gravity_forms_styler', 'file' => 'drop-down-select-field') );
		add_meta_box('412713100', __(' Multiple-Choice (Radio) Inputs', 'gravity-forms-styler' ), array($this, 'render_metabox'), 'gravity_forms_styler', 'normal'/* or side */, 'core', array( 'slug' => 'gravity_forms_styler', 'file' => 'multiple-choice-radio-inputs') );
		add_meta_box('153966', __(' Checkbox Inputs', 'gravity-forms-styler' ), array($this, 'render_metabox'), 'gravity_forms_styler', 'normal'/* or side */, 'core', array( 'slug' => 'gravity_forms_styler', 'file' => 'checkbox-inputs') );
		add_meta_box('5471343', __(' Submit Button', 'gravity-forms-styler' ), array($this, 'render_metabox'), 'gravity_forms_styler', 'normal'/* or side */, 'core', array( 'slug' => 'gravity_forms_styler', 'file' => 'submit-button') );
		add_meta_box('116291215', __(' Section Break Title', 'gravity-forms-styler' ), array($this, 'render_metabox'), 'gravity_forms_styler', 'normal'/* or side */, 'core', array( 'slug' => 'gravity_forms_styler', 'file' => 'section-break-title') );
		add_meta_box('0411111511', __(' Section Break Description', 'gravity-forms-styler' ), array($this, 'render_metabox'), 'gravity_forms_styler', 'normal'/* or side */, 'core', array( 'slug' => 'gravity_forms_styler', 'file' => 'section-break-description') );
		add_meta_box('510310212', __('Confirmation Message ', 'gravity-forms-styler' ), array($this, 'render_metabox'), 'gravity_forms_styler', 'normal'/* or side */, 'core', array( 'slug' => 'gravity_forms_styler', 'file' => 'confirmation-message') );
		add_meta_box('076686', __(' Validation Error Message', 'gravity-forms-styler' ), array($this, 'render_metabox'), 'gravity_forms_styler', 'normal'/* or side */, 'core', array( 'slug' => 'gravity_forms_styler', 'file' => 'validation-error-message') );
		add_meta_box('10100911', __('Custom CSS', 'gravity-forms-styler' ), array($this, 'render_metabox'), 'gravity_forms_styler', 'normal'/* or side */, 'core', array( 'slug' => 'gravity_forms_styler', 'file' => 'custom-css') );
		
	}

	/**
	 * render meta boxes.
	 *
	 *
	 * @return    null
	 */
	function render_metabox($post, $args){
		// include the metabox view
		echo '<input type="hidden" name="gravity_forms_styler_metabox" id="gravity_forms_styler_metabox" value="'.wp_create_nonce(plugin_basename(__FILE__)).'" />';
		echo '<input type="hidden" name="gravity_forms_styler_metabox_prefix[]" value="'.$args['args']['slug'].'" />';

		include self::get_path( __FILE__ ) . 'configs/' . $post->post_type . '-' . $args['args']['file'] . '.php';
		
		echo "<div class=\"group gravity-forms-styler-row-sorter\" id=\"rowitems\">\r\n";
		// build instance
		$depth = 1;
		$instance = get_post_meta($post->ID, $args['args']['slug'], true);
		if(!empty($group['multiple'])){
			foreach($group['fields'] as $field=>$settings){
				if(isset($instance[$field])){
					if(count($instance[$field]) > $depth){
						$depth = count($instance[$field]);
					}
				}
			}
		}
		for( $i=0; $i<$depth;$i++ ){
			
			if($i > 0){
				echo '  <div class="button button-primary right gravity-forms-styler-removeRow" style="margin-top:5px;">'.__('Remove', 'gravity-forms-styler').'</div>';
			}

			echo "<table class=\"form-table rowGroup groupitems " . ( !empty($group['multiple']) ? 'group-multiple row-sorter' : '' ) . "\" id=\"groupitems_".$i."\" ref=\"items\">\r\n";
			echo "	<tbody>\r\n";
				foreach($group['fields'] as $field=>$settings){
					//dump($settings);
					$id = 'field_'.$field;
					$groupid = $args['id'];
					$name = $args['args']['slug'].'['.$field.']';
					$single = true;
					$value = $settings['default'];
					if(!empty($group['multiple'])){
						$name = $args['args']['slug'].'['.$field.'][]';
						if(isset($instance[$field][$i])){
							$value = $instance[$field][$i];
						}						
					}else{
						if(isset($instance[$field])){
							$value = $instance[$field];
						}						
					}
					$label = $settings['label'];
					$caption = $settings['caption'];
					echo "<tr valign=\"top\">\r\n";
						echo "<th scope=\"row\">\r\n";
							echo "<label for=\"".$id."\">".$label."</label>\r\n";
						echo "</th>\r\n";
						echo "<td>\r\n";					
							include self::get_path( __FILE__ ) . 'includes/field-'.$settings['type'].'.php';
							if(!empty($caption)){
								echo '<p class="description">'.$caption.'</p>';
							}
						echo "</td>\r\n";
					echo "</tr>\r\n";

				}
			echo "	</tbody>\r\n";
			echo "</table>\r\n";
		}
		if(!empty($group['multiple'])){
			echo "<div class=\"toolrow\"><button class=\"button gravity-forms-styler-add-group-row\" type=\"button\" data-rowtemplate=\"group-".$args['id']."-tmpl\">".__('Add Another', 'gravity-forms-styler')."</button></div>\r\n";
		}
		echo "</div>\r\n";
		// Place html template for repeated fields.
		if(!empty($group['multiple'])){
			echo "<script type=\"text/html\" id=\"group-".$args['id']."-tmpl\">\r\n";
			echo '  <div class="button button-primary right gravity-forms-styler-removeRow" style="margin-top:5px;">'.__('Remove', 'gravity-forms-styler').'</div>';
			echo "	<table class=\"form-table rowGroup groupitems " . ( !empty($group['multiple']) ? 'group-multiple row-sorter' : '' ) . "\" id=\"groupitems\" ref=\"items\">\r\n";
			echo "		<tbody>\r\n";
				foreach($group['fields'] as $field=>$settings){
					//dump($settings);
					$id = 'field_{{id}}';
					$groupid = $args['id'];
					$name = $args['args']['slug'].'['.$field.']';
					$single = true;
					if(!empty($group['multiple'])){
						$name = $args['args']['slug'].'['.$field.'][]';
					}
					$label = $settings['label'];
					$caption = $settings['caption'];
					$value = $settings['default'];
					echo "<tr valign=\"top\">\r\n";
						echo "<th scope=\"row\">\r\n";
							echo "<label for=\"".$id."\">".$label."</label>\r\n";
						echo "</th>\r\n";
						echo "<td>\r\n";
							include self::get_path( __FILE__ ) . 'includes/field-'.$settings['type'].'.php';
							if(!empty($caption)){
								echo '<p class="description">'.$caption.'</p>';
							}

						echo "</td>\r\n";
					echo "</tr>\r\n";

				}
			echo "		</tbody>\r\n";
			echo "	</table>\r\n";	
			echo "</script>";
		}
	}


	/**
	 * setup meta boxes.
	 *
	 *
	 * @return    null
	 */

	
	
	/**
	 * Custom Post type columns.
	 *
	 *
	 * @return    columns
	 */
	function posts_column($hd){
		unset($hd['date']);
		$hd['shortcode_slug'] = __('Shortcode', 'gravity-forms-styler');
		$hd['date'] = __('Date', 'gravity-forms-styler');
		return $hd;
	}
	
	/**
	 * Custom Post type column render.
	 *
	 *
	 */
	function custom_postcolumn($col, $id){
		echo '['.$_GET['post_type'].' id="'.$id.'"]';
	}

	/**
	 * Register and enqueue admin-specific style sheet.
	 *
	 *
	 * @return    null
	 */
	public function enqueue_admin_stylescripts() {

		$screen = get_current_screen();

		
		if($screen->post_type == 'gravity_forms_styler' && $screen->base == 'post'){
			wp_enqueue_style( $this->plugin_slug . '-onoff-styles-toggles-css', self::get_url( 'assets/css/toggles.css', __FILE__ ), array(), self::VERSION );
			wp_enqueue_script( $this->plugin_slug . '-onoff-script-toggles-min-js', self::get_url( 'assets/js/toggles.min.js', __FILE__ ), array( 'jquery' ), self::VERSION );
		}
		
		if($screen->post_type == 'gravity_forms_styler' && $screen->base == 'post'){
			wp_enqueue_style( $this->plugin_slug . '-colorpicker-styles-minicolors-css', self::get_url( 'assets/css/minicolors.css', __FILE__ ), array(), self::VERSION );
			wp_enqueue_script( $this->plugin_slug . '-colorpicker-script-minicolors-js', self::get_url( 'assets/js/minicolors.js', __FILE__ ), array( 'jquery' ), self::VERSION );
		}
		
		if($screen->post_type == 'gravity_forms_styler' && $screen->base == 'post'){
			wp_enqueue_script( $this->plugin_slug . '-image-script-image-modal-js', self::get_url( 'assets/js/image-modal.js', __FILE__ ), array( 'jquery' ), self::VERSION );
		}
		
		if($screen->post_type == 'gravity_forms_styler' && $screen->base == 'post'){
			wp_enqueue_style( $this->plugin_slug . '-codeeditor-styles-editor-css', self::get_url( 'assets/css/editor.css', __FILE__ ), array(), self::VERSION );
			wp_enqueue_script( $this->plugin_slug . '-codeeditor-script-codemirror-compressed-js', self::get_url( 'assets/js/codemirror-compressed.js', __FILE__ ), array( 'jquery' ), self::VERSION );
		}
		
		if($screen->base == 'post'){
			wp_enqueue_script( $this->plugin_slug . '-shortcode-modal-script', self::get_url( 'assets/js/shortcode-modal.js', __FILE__ ), array( 'jquery' ), self::VERSION );
			wp_enqueue_script( $this->plugin_slug . '-panel-script', self::get_url( 'assets/js/panel.js', __FILE__ ), array( 'jquery' ), self::VERSION );
			wp_enqueue_style( $this->plugin_slug . '-panel-styles', self::get_url( 'assets/css/panel.css', __FILE__ ), array(), self::VERSION );
			wp_enqueue_style( $this->plugin_slug . '-codeeditor-styles-editor-css', self::get_url( 'assets/css/editor.css', __FILE__ ), array(), self::VERSION );
			wp_enqueue_style( $this->plugin_slug . '-onoff-styles-toggles-css', self::get_url( 'assets/css/toggles.css', __FILE__ ), array(), self::VERSION );
			wp_enqueue_script( $this->plugin_slug . '-onoff-script-toggles-min-js', self::get_url( 'assets/js/toggles.min.js', __FILE__ ), array( 'jquery' ), self::VERSION );
			wp_enqueue_style( $this->plugin_slug . '-colorpicker-styles-minicolors-css', self::get_url( 'assets/css/minicolors.css', __FILE__ ), array(), self::VERSION );
			wp_enqueue_script( $this->plugin_slug . '-colorpicker-script-minicolors-js', self::get_url( 'assets/js/minicolors.js', __FILE__ ), array( 'jquery' ), self::VERSION );
			wp_enqueue_script( $this->plugin_slug . '-image-script-image-modal-js', self::get_url( 'assets/js/image-modal.js', __FILE__ ), array( 'jquery' ), self::VERSION );
			wp_enqueue_style( $this->plugin_slug . '-codeeditor-styles-editor-css', self::get_url( 'assets/css/editor.css', __FILE__ ), array(), self::VERSION );
			wp_enqueue_script( $this->plugin_slug . '-codeeditor-script-codemirror-compressed-js', self::get_url( 'assets/js/codemirror-compressed.js', __FILE__ ), array( 'jquery' ), self::VERSION );
		}
		

		if ( ! isset( $this->plugin_screen_hook_suffix ) ) {
			return;
		}

		if ( in_array( $screen->id, $this->plugin_screen_hook_suffix ) ) {
			$slug = array_search( $screen->id, $this->plugin_screen_hook_suffix );
			//$configfiles = glob( self::get_path( __FILE__ ) .'configs/'.$slug.'-*.php' );
			if(file_exists(self::get_path( __FILE__ ) .'configs/fieldgroups-'.$slug.'.php')){
				include self::get_path( __FILE__ ) .'configs/fieldgroups-'.$slug.'.php';
			}else{
				return;
			}

			if( !empty( $configfiles ) ) {
				// Always good to have.
				wp_enqueue_media();
				wp_enqueue_script('media-upload');

				foreach ($configfiles as $key=>$fieldfile) {
					include $fieldfile;
					if(!empty($group['scripts'])){
						foreach($group['scripts'] as $script){
							if( is_array( $script ) ){
								foreach($script as $remote=>$location){
									$infoot = false;
									if($location == 'footer'){
										$infoot = true;
									}
									if( false !== strpos($remote, '.')){
										wp_enqueue_script( $this->plugin_slug . '-' . strtok(basename($remote), '.'), $remote , array('jquery'), false, $infoot );
									}else{
										wp_enqueue_script( $remote, false , array(), false, $infoot );
									}
								}
							}else{
								if( false !== strpos($script, '.')){
									wp_enqueue_script( $this->plugin_slug . '-' . strtok($script, '.'), self::get_url( 'assets/js/'.$script , __FILE__ ) , array('jquery'), false, true );					
								}else{
									wp_enqueue_script( $script );
								}
							}
						}
					}
					if(!empty($group['styles'])){
						foreach($group['styles'] as $style){
							if( is_array( $style ) ){
								foreach($style as $remote){
									wp_enqueue_style( $this->plugin_slug . '-' . strtok(basename($remote), '.'), $remote );
								}
							}else{
								wp_enqueue_style( $this->plugin_slug . '-' . strtok($style, '.'), self::get_url( 'assets/css/'.$style , __FILE__ ) );
							}
						}
					}
				}
			}	
			wp_enqueue_style( $this->plugin_slug .'-admin-styles', self::get_url( 'assets/css/panel.css', __FILE__ ), array(), self::VERSION );
			wp_enqueue_script( $this->plugin_slug .'-admin-scripts', self::get_url( 'assets/js/panel.js', __FILE__ ), array(), self::VERSION );
		}

	}

	
	
	
	/**
	 * Process a field value
	 *
	 */
	public function process_value($type, $value){

		switch ($type){
			default:
				return $value;
				break;
			case "image":
				$attachment = explode(',',$value);
				if(floatval($attachment[0]) > 0){
					$image = wp_get_attachment_image_src($attachment[0], $attachment[1]);
					$value = $image[0];
				}
			break;
			
		}

		return $value;	

	}

	
	/**
	 * Insert shortcode media button
	 *
	 *
	 */
	function shortcode_insert_button(){
		global $post;
		if(!empty($post)){
			echo "<a id=\"gravity-forms-styler-shortcodeinsert\" title=\"".__('Gravity Forms Styler Shortcode Builder','gravity-forms-styler')."\" style=\"padding-left: 0.4em;\" class=\"button gravity-forms-styler-editor-button\" href=\"#inst\">\n";
			echo "	<img src=\"". self::get_url( __FILE__ ) . "assets/images/icon.png\" alt=\"".__("Insert Shortcode","gravity-forms-styler")."\" style=\"padding:0 2px 1px;\" /> ".__('Gravity Forms Styler', 'gravity-forms-styler')."\n";
			echo "</a>\n";
		}
	}

	/**
	 * render shortcode config panel.
	 *
	 *
	 * @return    null
	 */
	function render_shortcode_panel($shortcode, $type = 1, $template = false){


		if(!empty($template)){
			echo "<script type=\"text/html\" id=\"gravity-forms-styler-".$shortcode."-config-tmpl\">\r\n";
		}
		echo "<input id=\"gravity-forms-styler-shortcodekey\" class=\"configexclude\" type=\"hidden\" value=\"".$shortcode."\">\r\n";
		echo "<input id=\"gravity-forms-styler-shortcodetype\" class=\"configexclude\" type=\"hidden\" value=\"".$type."\">\r\n";
		echo "<input id=\"gravity-forms-styler-default-content\" class=\"configexclude\" type=\"hidden\" value=\" ".__('Your content goes here','gravity-forms-styler')." \">\r\n";

		if(!empty($this->elements['posttypes'][$shortcode])){
			$posts = get_posts(array('post_type' => $shortcode));

			if(empty($posts)){
				echo 'No items available';
			}else{
				foreach($posts as $post){
					echo '<div class="posttype-item"><label><input type="radio" value="'.$post->ID.'" name="id"> '.$post->post_title.'</label></div>';
				}
			}
			if(!empty($template)){
				echo "</script>\r\n";
			}
			return;
		}
	
		if(file_exists(self::get_path( __FILE__ ) .'configs/fieldgroups-'.$shortcode.'.php')){
			include self::get_path( __FILE__ ) .'configs/fieldgroups-'.$shortcode.'.php';		
		}else{
			if(!empty($template)){
				echo "</script>\r\n";
			}
			return;
		}

		$groups = array();
		echo "<div class=\"gravity-forms-styler-shortcode-config-nav\">\r\n";
		echo "	<ul>\r\n";
		foreach ($configfiles as $key=>$fieldfile) {
			include $fieldfile;
			$groups[] = $group;
				echo "		<li class=\"" . ( $key === 0 ? "current" : "" ) . "\">\r\n";
				echo "			<a title=\"".$group['label']."\" href=\"#row".$group['master']."\"><strong>".$group['label']."</strong></a>\r\n";
				echo "		</li>\r\n";
		}
		echo "	</ul>\r\n";
		echo "</div>\r\n";

		echo "<div class=\"gravity-forms-styler-shortcode-config-content " . ( count($configfiles) > 1 ? "" : "full" ) . "\">\r\n";
			foreach($groups as $key=>$group){
				echo "<div class=\"group\" " . ( $key === 0 ? "" : "style=\"display:none;\"" ) . " id=\"row".$group['master']."\">\r\n";
				echo "<h3 class=\"gravity-forms-styler-config-header\">".$group['label']."</h3>\r\n";
				echo "<table class=\"form-table rowGroup groupitems\" id=\"groupitems\" ref=\"items\">\r\n";
				echo "	<tbody>\r\n";
					foreach($group['fields'] as $field=>$settings){
						//dump($settings);
						$id = 'field_'.$field;
						$groupid = $group['id'];
						$name = $field;
						$single = true;
						if(!empty($group['multiple'])){
							$name = $field.'[]';
						}
						$label = $settings['label'];
						$caption = $settings['caption'];
						$value = $settings['default'];
						echo "<tr valign=\"top\">\r\n";
							echo "<th scope=\"row\">\r\n";
								echo "<label for=\"".$id."\">".$label."</label>\r\n";
							echo "</th>\r\n";
							echo "<td>\r\n";
							include self::get_path( __FILE__ ) . 'includes/field-'.$settings['type'].'.php';
							if(!empty($caption)){
								echo "<p class=\"description\">".$caption."</p>\r\n";
							}
							echo "</td>\r\n";
						echo "</tr>\r\n";
					}
				echo "	</tbody>\r\n";
				echo "</table>\r\n";

				if(!empty($group['multiple'])){
					echo "<div class=\"toolrow\"><button class=\"button gravity-forms-styler-add-group-row\" type=\"button\" data-rowtemplate=\"group-".$group['id']."-tmpl\">".__('Add Another','gravity-forms-styler')."</button></div>\r\n";
				}
				echo "</div>\r\n";
			}
		echo "</div>\r\n";

		if(!empty($template)){
			echo "</script>\r\n";
		}
		// go get the loop templates
		foreach($groups as $group){
			// Place html template for repeated fields.
			if(!empty($group['multiple'])){
				echo "<script type=\"text/html\" id=\"group-".$group['id']."-tmpl\">\r\n";
				echo '  <div class="button button-primary right gravity-forms-styler-removeRow" style="margin:5px 5px 0;">'.__('Remove','gravity-forms-styler').'</div>';
				echo "	<table class=\"form-table rowGroup groupitems\" id=\"groupitems\" ref=\"items\">\r\n";
				echo "		<tbody>\r\n";
					foreach($group['fields'] as $field=>$settings){
						//dump($settings);
						$id = 'field_{{id}}';
						$groupid = $group['id'];
						$name = $field.'[__count__]';
						$single = true;
						$label = $settings['label'];
						$caption = $settings['caption'];
						$value = $settings['default'];
						echo "<tr valign=\"top\">\r\n";
							echo "<th scope=\"row\">\r\n";
								echo "<label for=\"".$id."\">".$label."</label>\r\n";
							echo "</th>\r\n";
							echo "<td>\r\n";
							include self::get_path( __FILE__ ) . 'includes/field-'.$settings['type'].'.php';
							if(!empty($caption)){
								echo "<p class=\"description\">".$caption."</p>\r\n";
							}
							echo "</td>\r\n";
						echo "</tr>\r\n";

					}
				echo "		</tbody>\r\n";
				echo "	</table>\r\n";
				echo "</script>";
			}
		}
	}

	/**
	 * Insert shortcode modal template
	 *
	 *
	 */
	function shortcode_modal_template(){
		$screen = get_current_screen();

		if($screen->base != 'post'){return;}

		echo "<script type=\"text/html\" id=\"gravity-forms-styler-shortcode-panel-tmpl\">\r\n";
		echo "	<div tabindex=\"0\" id=\"gravity-forms-styler-shortcode-panel\" class=\"hidden\">\r\n";
		echo "		<div class=\"media-modal-backdrop\"></div>\r\n";
		echo "		<div class=\"gravity-forms-styler-modal-modal\">\r\n";
		echo "			<div class=\"gravity-forms-styler-modal-content\">\r\n";
		echo "				<div class=\"gravity-forms-styler-modal-header\">\r\n";
		echo "					<a title=\"Close\" href=\"#\" class=\"media-modal-close\">\r\n";
		echo "						<span class=\"media-modal-icon\"></span>\r\n";
		echo "					</a>\r\n";
		echo "					<h2 style=\"background: url(".self::get_url( '/assets/images/icon.png', __FILE__ ) . ") no-repeat scroll 0px 2px transparent; padding-left: 20px;\">".__('Gravity Forms Styler','gravity-forms-styler')." <small>".__("Shortcode Builder","gravity-forms-styler")."</small></h2>\r\n";
		echo "				</div>\r\n";
		echo "				<div class=\"gravity-forms-styler-modal-body\">\r\n";
		echo "					<span id=\"gravity-forms-styler-categories\">\r\n";
		echo "						<div class=\"gravity-forms-styler-shortcode-name\">".__('Gravity Forms Styler','gravity-forms-styler')."</div><span class=\"gravity-forms-styler-autoload\" data-shortcode=\"gravity_forms_styler\"></span>\r\n";
		echo "					</span>\r\n";
		echo "					<div id=\"gravity-forms-styler-shortcode-config\" class=\"gravity-forms-styler-shortcode-config\">\r\n";
		echo "					</div>\r\n";
		echo "				</div>\r\n";
		echo "				<div class=\"gravity-forms-styler-modal-footer\">\r\n";
		echo "					<button class=\"button button-primary button-large\" id=\"gravity-forms-styler-insert-shortcode\">".__("Insert Shortcode","gravity-forms-styler")."</button>\r\n";
		echo "				</div>\r\n";
		echo "			</div>\r\n";
		echo "		</div>\r\n";
		echo "	</div>\r\n";
		echo "</script>\r\n";

		foreach($this->elements['shortcodes'] as $shortcode=>$type){
			$this->render_shortcode_panel($shortcode, $type, true);
		}
		
	}

	/**
	 * Gets a list of shorcodes used within the content provided
	 *
	 * @return 	array
	 */
	function get_regex(){

	// this makes it easier to cycle through and get the used codes for inclusion
	$validcodes = join( '|', array_map('preg_quote', array_keys($this->elements['shortcodes'])) );
	return
			  '\\['                              // Opening bracket
			. '(\\[?)'                           // 1: Optional second opening bracket for escaping shortcodes: [[tag]]
			. "($validcodes)"                    // 2: selected codes only
			. '\\b'                              // Word boundary
			. '('                                // 3: Unroll the loop: Inside the opening shortcode tag
			.     '[^\\]\\/]*'                   // Not a closing bracket or forward slash
			.     '(?:'
			.         '\\/(?!\\])'               // A forward slash not followed by a closing bracket
			.         '[^\\]\\/]*'               // Not a closing bracket or forward slash
			.     ')*?'
			. ')'
			. '(?:'
			.     '(\\/)'                        // 4: Self closing tag ...
			.     '\\]'                          // ... and closing bracket
			. '|'
			.     '\\]'                          // Closing bracket
			.     '(?:'
			.         '('                        // 5: Unroll the loop: Optionally, anything between the opening and closing shortcode tags
			.             '[^\\[]*+'             // Not an opening bracket
			.             '(?:'
			.                 '\\[(?!\\/\\2\\])' // An opening bracket not followed by the closing shortcode tag
			.                 '[^\\[]*+'         // Not an opening bracket
			.             ')*+'
			.         ')'
			.         '\\[\\/\\2\\]'             // Closing shortcode tag
			.     ')?'
			. ')'
			. '(\\]?)';                          // 6: Optional second closing brocket for escaping shortcodes: [[tag]]

	}

	function get_used_shortcodes($content, $return = array(), $internal = true, $preview = false){

		$regex = self::get_regex();

		preg_match_all('/' . $regex . '/s', $content, $found);

		foreach($found[5] as $innerContent){
			if(!empty($innerContent)){
			   $new = self::get_used_shortcodes($innerContent, $found, $internal);
				if(!empty($new)){
					foreach($new as $key=>$val){
						$found[$key] = array_merge($found[$key], $val);
					}
				}
			}
		}

		return $found;
	}


	/**
	 * setup meta boxes.
	 *
	 *
	 * @return    null
	 */
	public function get_post_meta($id, $key = null, $single = false){
		
		if(!empty($key)){

			//$configfiles = glob(self::get_path( __FILE__ ) .'configs/*.php');
			if(file_exists(self::get_path( __FILE__ ) .'configs/fieldgroups-gravity_forms_styler.php')){
				include self::get_path( __FILE__ ) .'configs/fieldgroups-gravity_forms_styler.php';		
			}else{
				return;
			}

			$field_type = 'text';
			foreach( $configfiles as $config=>$file ){
				include $file;
				if(isset($group['fields'][$key]['type'])){
					$field_type = $group['fields'][$key]['type'];
					break;
				}
			}
			$key = 'gravity_forms_styler_' . $key;
		}
		if( false === $single){
			$metas = get_post_meta( $id, $key );
			foreach ($metas as $key => &$value) {
				$value = $this->process_value( $field_type, $value );
			}
			return $metas;
		}
		return $this->process_value( $field_type, get_post_meta( $id, $key, $single ) );

	}


	/**
	 * save metabox data
	 *
	 *
	 */
	function save_post_metaboxes($pid, $post){

		if(!isset($_POST['gravity_forms_styler_metabox']) || !isset($_POST['gravity_forms_styler_metabox_prefix'])){return;}


		if(!wp_verify_nonce($_POST['gravity_forms_styler_metabox'], plugin_basename(__FILE__))){
			return $post->ID;
		}
		if(!current_user_can( 'edit_post', $post->ID)){
			return $post->ID;
		}
		if($post->post_type == 'revision' ){return;}
		
		foreach( $_POST['gravity_forms_styler_metabox_prefix'] as $prefix ){
			if(!isset($_POST[$prefix])){continue;}

			if($_POST['gravity_forms_styler_storage'] == 'single'){
				foreach($_POST[$prefix] as $field=>$data){
					update_post_meta($post->ID, $field, $data);
				}
			}else{
				delete_post_meta($post->ID, $prefix);
				add_post_meta($post->ID, $prefix, $_POST[$prefix]);
			}
			//foreach($_POST['gravity_forms_styler'] as $field=>$data){
			/*
			*/
			//}
		}
	}	
	/**
	 * create and register an instance ID
	 *
	 */
	public function element_instance_id($id, $process){

		$this->element_instances[$id][$process][] = true;
		$count = count($this->element_instances[$id][$process]);
		if($count > 1){
			return $id.($count-1);
		}
		return $id;
	}

	/**
	 * Render the element
	 *
	 */
	public function render_element($atts, $content, $slug, $head = false) {
		
		$raw_atts = $atts;
		
	
				// is this a post type ID?
				if(!empty($atts['id'])){
					$content = get_post_field('post_content', $atts['id']);
					$atts = get_post_meta($atts['id'], $slug, true);
					//if(is_array($value)){
					//	foreach($value as &$varval){
					//		$varval = $this->process_value($conf['type'],$varval);
					//	}
					//}
				}

		if(!empty($head)){
			$instanceID = $this->element_instance_id('gravity_forms_styler'.$slug, 'header');
		}else{
			$instanceID = $this->element_instance_id('gravity_forms_styler'.$slug, 'footer');
		}

		//$configfiles = glob(self::get_path( __FILE__ ) .'configs/'.$slug.'-*.php');
		if(file_exists(self::get_path( __FILE__ ) .'configs/fieldgroups-'.$slug.'.php')){
			include self::get_path( __FILE__ ) .'configs/fieldgroups-'.$slug.'.php';		
		
			$defaults = array();
			foreach($configfiles as $file){

				include $file;
				foreach($group['fields'] as $variable=>$conf){
					if(!empty($group['multiple'])){
						$value = array($this->process_value($conf['type'],$conf['default']));
					}else{
						$value = $this->process_value($conf['type'],$conf['default']);
					}
					if(!empty($group['multiple'])){
						if(isset($atts[$variable.'_1'])){
							$index = 1;
							$value=array();
							while(isset($atts[$variable.'_'.$index])){
								$value[] = $this->process_value($conf['type'],$atts[$variable.'_'.$index]);
								$index++;
							}
						}elseif (isset($atts[$variable])) {
							if(is_array($atts[$variable])){
								foreach($atts[$variable] as &$varval){
									$varval = $this->process_value($conf['type'],$varval);
								}
								$value = $atts[$variable];
							}else{
								$value[] = $this->process_value($conf['type'],$atts[$variable]);
							}
						}
					}else{
						if(isset($atts[$variable])){
							$value = $this->process_value($conf['type'],$atts[$variable]);
						}
					}
					
					if(!empty($group['multiple']) && !empty($value)){
						foreach($value as $key=>$val){
							//if(is_array($val)){
								$groups[$group['master']][$key][$variable] = $val;
							//}elseif(strlen($val) > 0){
							//	$groups[$group['master']][$key][$variable] = $val;
							//}
						}
					}
					$defaults[$variable] = $value;
					/*if(is_array($value)){
						foreach($value as $varkey=>&$varval){
							if(is_array($val)){
								if(!empty($val)){
									unset($value[$varkey]);
								}
							}elseif(strlen($varval) === 0){
								unset($value[$varkey]);
							}
						}
						if(!empty($value)){
							$defaults[$variable] = implode(', ', $value);
						}
					}else{
						if(strlen($value) > 0){
							$defaults[$variable] = $value;
						}
					}*/
				}
			}
			//dump($atts,0);
			//dump($defaults,0);
			$atts = $defaults;
		}
		// pull in the assets
		$assets = array();
		if(file_exists(self::get_path( __FILE__ ) . 'assets/assets-'.$slug.'.php')){
			include self::get_path( __FILE__ ) . 'assets/assets-'.$slug.'.php';
		}

		ob_start();
		if(file_exists(self::get_path( __FILE__ ) . 'includes/element-'.$slug.'.php')){
			include self::get_path( __FILE__ ) . 'includes/element-'.$slug.'.php';
		}else if( file_exists(self::get_path( __FILE__ ) . 'includes/element-'.$slug.'.html')){
			include self::get_path( __FILE__ ) . 'includes/element-'.$slug.'.html';
		}
		$out = ob_get_clean();


		if(!empty($head)){

			// process headers - CSS
			if(file_exists(self::get_path( __FILE__ ) . 'assets/css/styles-'.$slug.'.php')){
				ob_start();
				include self::get_path( __FILE__ ) . 'assets/css/styles-'.$slug.'.php';
				$this->element_header_styles[] = ob_get_clean();
				add_action('wp_head', array( $this, 'header_styles' ) );
			}else if( file_exists(self::get_path( __FILE__ ) . 'assets/css/styles-'.$slug.'.css')){
				wp_enqueue_style( $this->plugin_slug . '-'.$slug.'-styles', self::get_url( 'assets/css/styles-'.$slug.'.css', __FILE__ ), array(), self::VERSION );
			}
			// process headers - JS
			if(file_exists(self::get_path( __FILE__ ) . 'assets/js/scripts-'.$slug.'.php')){
				ob_start();
				include self::get_path( __FILE__ ) . 'assets/js/scripts-'.$slug.'.php';				
				$this->element_footer_scripts[] = ob_get_clean();
			}else if( file_exists(self::get_path( __FILE__ ) . 'assets/js/scripts-'.$slug.'.js')){
				wp_enqueue_script( $this->plugin_slug . '-'.$slug.'-script', self::get_url( 'assets/js/scripts-'.$slug.'.js', __FILE__ ), array( 'jquery' ), self::VERSION , true );
			}
			// get clean do shortcode for header checking
			ob_start();
			do_shortcode( $out );
			ob_get_clean();			
			return;
		}
		
		// CHECK FOR EMBEDED ELEMENTS
		/*foreach($elements as $subelement){
			if(empty($subelement['state']) || $subelement['shortcode'] == $element['_shortcode']){continue;}
			if(false !== strpos($out, '{{:'.$subelement['shortcode'].':}}')){
				$out = str_replace('{{:'.$subelement['shortcode'].':}}', caldera_doShortcode(array(), $out, $subelement['shortcode']), $out);
			}
		}*/


		/*if(!empty($element['_removelinebreaks'])){
			add_filter( 'the_content', 'wpautop' );
		}*/

		return do_shortcode($out);
	}

	/**
	 * Detect elements used on the page to allow us to enqueue needed styles and scripts
	 *
	 */
	public function detect_elements() {
		global $wp_query;

		
	
		// is this a post type?
		if(isset($this->elements['posttypes'])){
			$this_post_type = get_post_type();
			if(isset($this->elements['posttypes'][$this_post_type])){
				if($this->elements['posttypes'][$this_post_type] === 'browsable'){
					// Browseable - render element over content.
					foreach ($wp_query->posts as $key => &$post) {
						// process header portion
						$this->render_element(array('id'=>$post->ID), $post->post_content, $this_post_type, true);
						// render content portion and replace content
						$post->post_content = $this->render_element(array('id'=>$post->ID), $post->post_content, $this_post_type);
					}
				}
			}
		}

	
		// find used shortcodes within posts
		foreach ($wp_query->posts as $key => &$post) {
			$shortcodes = self::get_used_shortcodes($post->post_content);
			if(!empty($shortcodes[2])){
				foreach($shortcodes[2] as $foundkey=>$shortcode){
					$atts = array();
					if(!empty($shortcodes[3][$foundkey])){
						$atts = shortcode_parse_atts($shortcodes[3][$foundkey]);
					}
					
					// process header portion
					$this->render_element($atts, $post->post_content, $shortcode, true);
				}
			}
		}


	}

	/**
	 * Render any header styles
	 *
	 */
	public function header_styles() {
		if(!empty($this->element_header_styles)){
			echo "<style type=\"text/css\">\r\n";
			foreach($this->element_header_styles as $styles){
				echo $styles."\r\n";
			}			
			echo "</style>\r\n";
		}
	}
	
	/**
	 * Render any footer scripts
	 *
	 */
	public function footer_scripts() {

		if(!empty($this->element_footer_scripts)){
			echo "<script type=\"text/javascript\">\r\n";
				foreach($this->element_footer_scripts as $script){
					echo $script."\r\n";
				}
			echo "</script>\r\n";
		}
	}

	

	/***
	 * Get the current URL
	 *
	 */
	static function get_url($src = null, $path = null) {
		if(!empty($path)){
			return plugins_url( $src, $path);
		}
		return trailingslashit( plugins_url( $path , __FILE__ ) );
	}

	/***
	 * Get the current URL
	 *
	 */
	static function get_path($src = null) {
		return plugin_dir_path( $src );

	}
	
}
