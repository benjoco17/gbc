<?php

defined( 'DOING_GRAVITYVIEW_TESTS' ) || exit;

/**
 * @group gvapi
 */
class GravityView_API_Test extends GV_UnitTestCase {

	/**
	 * @var int
	 */
	var $form_id = 0;

	/**
	 * @var array GF Form array
	 */
	var $form = array();

	/**
	 * @var int
	 */
	var $entry_id = 0;

	/**
	 * @var array GF Entry array
	 */
	var $entry = array();

	var $is_set_up = false;

	function setUp() {

		parent::setUp();

		$this->form = GV_Unit_Tests_Bootstrap::instance()->get_form();
		$this->form_id = GV_Unit_Tests_Bootstrap::instance()->get_form_id();

		$this->entry = GV_Unit_Tests_Bootstrap::instance()->get_entry();
		$this->entry_id = GV_Unit_Tests_Bootstrap::instance()->get_entry_id();

	}

	/**
	 * @covers GravityView_API::replace_variables()
	 * @covers GravityView_Merge_Tags::replace_variables()
	 */
	public function test_replace_variables() {

		$entry = GV_Unit_Tests_Bootstrap::instance()->get_entry();

		$form = GV_Unit_Tests_Bootstrap::instance()->get_form();

		// No match
		$this->assertEquals( 'no bracket', GravityView_API::replace_variables( 'no bracket', $form, $entry ) );

		// Include bracket with nomatch
		$this->assertEquals( $entry['id'] . ' {nomatch}', GravityView_API::replace_variables( '{entry_id} {nomatch}', $form, $entry ) );

		// Match tag, empty value
		$this->assertEquals( '', GravityView_API::replace_variables( '{user:example}', $form, $entry ) );

		// Open matching tag
		$this->assertEquals( '{entry_id', GravityView_API::replace_variables( '{entry_id', $form, $entry ) );

		// Form ID
		$this->assertEquals( $form['id'], GravityView_API::replace_variables( '{form_id}', $form, $entry ) );

		// Form title
		$this->assertEquals( 'Example '.$form['title'], GravityView_API::replace_variables( 'Example {form_title}', $form, $entry ) );

		$this->assertEquals( $entry['post_id'], GravityView_API::replace_variables( '{post_id}', $form, $entry ) );

		$this->assertEquals( date( 'm/d/Y' ), GravityView_API::replace_variables( '{date_mdy}', $form, $entry ) );

		$this->assertEquals( get_option( 'admin_email' ), GravityView_API::replace_variables( '{admin_email}', $form, $entry ) );

		$user = wp_set_current_user( $entry['created_by'] );

		// Test new Roles merge tag
		$this->assertEquals( implode( ', ', $user->roles ), GravityView_API::replace_variables( '{created_by:roles}', $form, $entry ) );

		$user->add_role( 'editor' );

		// Test new Roles merge tag again, with another role.
		$this->assertEquals( implode( ', ', $user->roles ), GravityView_API::replace_variables( '{created_by:roles}', $form, $entry ) );

		$var_content = '<p>I expect <strong>Entry #{entry_id}</strong> will be in Form #{form_id}</p>';
		$expected_content = '<p>I expect <strong>Entry #'.$entry['id'].'</strong> will be in Form #'.$form['id'].'</p>';
		$this->assertEquals( $expected_content, GravityView_API::replace_variables( $var_content, $form, $entry ) );

	}

	/**
	 * @covers GravityView_API::field_class()
	 */
	public function test_field_class() {

		$entry = $this->entry;

		$form = $this->form;

		$field_id = 2;

		$field = GFFormsModel::get_field( $form, $field_id);

		$this->assertEquals( 'gv-field-'.$form['id'].'-'.$field_id, GravityView_API::field_class( $field, $form, $entry ) );

		$field['custom_class'] = 'custom-class-{entry_id}';

		// Test the replace_variables functionality
		$this->assertEquals( 'custom-class-'.$entry['id'].' gv-field-'.$form['id'].'-'.$field_id, GravityView_API::field_class( $field, $form, $entry ) );

		$field['custom_class'] = 'testing,!@@($)*$ 12383';

		// Test the replace_variables functionality
		$this->assertEquals( 'testing 12383 gv-field-'.$form['id'].'-'.$field_id, GravityView_API::field_class( $field, $form, $entry ) );

	}

	/**
	 * @uses GravityView_API_Test::_override_no_entries_text_output()
	 * @covers GravityView_API::no_results()
	 */
	public function test_no_results() {

		global $gravityview_view;

		$gravityview_view = GravityView_View::getInstance();

		$gravityview_view->curr_start = false;
		$gravityview_view->curr_end = false;
		$gravityview_view->curr_search = false;

		// Not in search by default
			$this->assertEquals( 'No entries match your request.', GravityView_API::no_results( false ) );
			$this->assertEquals( '<p>No entries match your request.</p>'."\n", GravityView_API::no_results( true ) );
		// Pretend we're in search
		$gravityview_view->curr_search = true;

			$this->assertEquals( 'This search returned no results.', GravityView_API::no_results( false ) );
			$this->assertEquals( '<p>This search returned no results.</p>'."\n", GravityView_API::no_results( true ) );


		// Add the filter that modifies output
		add_filter( 'gravitview_no_entries_text', array( $this, '_override_no_entries_text_output' ), 10, 2 );

		// Test to make sure the $is_search parameter is passed correctly
		$this->assertEquals( 'SEARCH override the no entries text output', GravityView_API::no_results( false ) );

		$gravityview_view->curr_search = false;

		// Test to make sure the $is_search parameter is passed correctly
		$this->assertEquals( 'NO SEARCH override the no entries text output', GravityView_API::no_results( false ) );

		// Remove the filter for later
		remove_filter( 'gravitview_no_entries_text', array( $this, '_override_no_entries_text_output' ) );

	}

	public function _override_no_entries_text_output( $previous, $is_search = false ) {

		if ( $is_search ) {
			return 'SEARCH override the no entries text output';
		} else {
			return 'NO SEARCH override the no entries text output';
		}

	}

	public function _get_new_view_id() {

		$view_array = array(
			'post_content' => '',
			'post_type' => 'gravityview',
			'post_status' => 'publish',
		);

		// Add the View
		$view_post_type_id = wp_insert_post( $view_array );

		// Set the form ID
		update_post_meta( $view_post_type_id, '_gravityview_form_id', $this->form_id );

		// Set the View settigns
		update_post_meta( $view_post_type_id, '_gravityview_template_settings', GravityView_View_Data::get_default_args() );

		// Set the template to be table
		update_post_meta( $view_post_type_id, '_gravityview_directory_template', 'default_table' );

		return $view_post_type_id;

	}

	/**
	 * @internal Make sure this test is above the test_directory_link() test so that one doesn't pollute $post
	 */
	public function test_gravityview_get_current_views() {

		$fe = GravityView_frontend::getInstance();

		// Clear the data so that gravityview_get_current_views() runs parse_content()
		$fe->gv_output_data = null;

		$view_post_type_id = $this->_get_new_view_id();

		global $post;

		$post = get_post( $view_post_type_id );

		$this->assertEquals( $view_post_type_id, $post->ID );

		$current_views = gravityview_get_current_views();

		// Check if the view post is set
		$this->assertTrue( isset( $current_views[ $view_post_type_id ] ) );

		// When the view is added, the key is set to the View ID and the `id` is also set to that
		$this->assertEquals( $view_post_type_id, $current_views[ $view_post_type_id ]['id'] );

		// Just one View
		$this->assertEquals( 1, count( $current_views ) );

		$second_view_post_type_id = $this->_get_new_view_id();

		$fe->gv_output_data->add_view( $second_view_post_type_id );

		$second_current_views = gravityview_get_current_views();

		// Check to make sure add_view worked properly
		$this->assertEquals( $second_view_post_type_id, $second_current_views[ $second_view_post_type_id ]['id'] );

		// Now two Views
		$this->assertEquals( 2, count( $second_current_views ) );

	}

	/**
	 * @covers GravityView_API::directory_link()
	 */
	public function test_directory_link( ) {
		$post_array = array(
			'post_content' => 'asdasdsd',
			'post_type' => 'post',
			'post_status' => 'publish',
		);

		$post_id = wp_insert_post( $post_array );

		$view_post_type_id = $this->_get_new_view_id();

		$_GET['pagenum'] = 2;

		$add_pagination = false;
		$this->assertEquals( site_url( '?p=' . $post_id ), GravityView_API::directory_link( $post_id, $add_pagination ) );

		$add_pagination = true;
		$this->assertEquals( site_url( '?p=' . $post_id . '&pagenum=2' ), GravityView_API::directory_link( $post_id, $add_pagination ) );

		// Make sure the cache is working properly
		$this->assertEquals( site_url( '?p=' . $post_id ), wp_cache_get( 'gv_directory_link_' . $post_id ) );

		//
		// Use $gravityview_view data
		//
		global $gravityview_view;
		global $post;

		$post = get_post( $view_post_type_id );

		GravityView_frontend::getInstance()->parse_content();

		$gravityview_view->setViewId( $view_post_type_id );

		// Test post_id has been set
		$gravityview_view->setPostId( $post_id );

		/* TODO - fix this assertion */
		$this->assertEquals( site_url( '?p=' . $post_id . '&pagenum=2' ), GravityView_API::directory_link() );

		$gravityview_view->setPostId( $post_id );

		//
		// TESTING AJAX
		//
		define( 'DOING_AJAX', true );

		// No passed post_id; use $_POST when DOING_AJAX is set
		$this->assertNull( GravityView_API::directory_link() );

		$_POST['post_id'] = $post_id;
		// No passed post_id; use $_POST when DOING_AJAX is set
		$this->assertEquals( site_url( '?p=' . $post_id . '&pagenum=2' ), GravityView_API::directory_link() );

	}

}
