<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://wordpress.org/plugins/anonymous-restricted-content/
 * @since      1.0.0
 *
 * @package    ARC
 * @subpackage ARC/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * @package    ARC
 * @subpackage ARC/public
 * @author     Taras Sych <taras.sych@gmail.com>
 */
class ARC_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.5.0
	 */
	public function enqueue_styles() {

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/arc-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.5.0
	 */
	public function enqueue_scripts() {

		wp_enqueue_script( 'arc-public.js', plugin_dir_url( __FILE__ ) . 'js/arc-public.js', array( 'jquery' ), $this->version, false );
		wp_localize_script( 'arc-public.js', 'ArcPubLStrings', $this->get_language_strings() );
	}

	/**
  * translations strings for JS scripts
  *
  * @since    1.5.2
  */
	private function get_language_strings() {
		$strings = array(
			'SendingUserInfo' => __( 'Sending user info, please wait...', 'anonymous-restricted-content' ),
			'LogInFailed' => __( 'Log In failed. Try again later.', 'anonymous-restricted-content' ),
			'RestrictedContent' => __( 'Restricted Content', 'anonymous-restricted-content' ),
			'PleaseLogIn' => __( 'Please log in to get access:', 'anonymous-restricted-content' ),
			'Username' => __( 'Username:', 'anonymous-restricted-content' ),
			'Password' => __( 'Password:', 'anonymous-restricted-content' ),
			'LogIn' => __( 'Log In', 'anonymous-restricted-content' ),
			'GoBack' => __( 'Go Back', 'anonymous-restricted-content' ),
    );

    return $strings;
	}

	/**
	 * check the WP_Post object to be restricted
	 *
	 * @since    1.0.0
	 */
	private function is_post_restricted($wp_post)
	{
		$is_restricted = ( get_post_meta($wp_post->ID, 'arc_restricted_post', true) && (bool) get_post_meta($wp_post->ID, 'arc_restricted_post', true) === true ) ? true : false;

		$post_terms = $this->get_post_taxonomies( $wp_post->ID );

		if ( $post_terms )
		{
			foreach ( $post_terms as $post_term )
			{
				$is_category_restricted = $this->is_category_restricted( $post_term );

				if ( $is_category_restricted === true ) {
					$is_restricted = true;
					break;
				}
			}
		}

		return $is_restricted;
	}

	/**
	 * Check the WP_Term object to be restricted
	 *
	 * @since    1.5.5
	 */
	private function is_category_restricted($wp_term)
	{
		$is_restricted = get_term_meta($wp_term->term_id, 'arc_restricted_category', true) && (bool) get_term_meta($wp_term->term_id, 'arc_restricted_category', true) === true;

		// Check if parent category restricted
		if ( ! $is_restricted )
		{
			$is_restricted = get_term_meta($wp_term->parent, 'arc_restricted_category', true) && (bool) get_term_meta($wp_term->parent, 'arc_restricted_category', true) === true;
		}

		return $is_restricted;
	}

	/**
	 * redirect to login with option to back to requested page after logging in
	 *
	 * @since    1.0.0
	 */
	private function redirect_login($wp_obj)
	{
		wp_redirect(add_query_arg(['arc_restricred' => '1', 'timecode' => time()], wp_login_url(get_permalink($wp_obj->ID))));
	}

	/**
	 * check for content restrictions and redirect to login page
	 *
	 * @since    1.0.0
	 */
	public function redirect_restricted_content_to_login($false, $query )
	{

		// print_r($query);
		$queried_object = $query->get_queried_object();

		$is_content_restricted = false;

		if ( $queried_object instanceof WP_Post )
		{
			$is_content_restricted = $this->is_post_restricted($queried_object);
		}
		elseif ( $queried_object instanceof WP_Term )
		{
			$is_content_restricted = $this->is_category_restricted($queried_object);
		}


		if ( $is_content_restricted && ! is_admin() && !is_user_logged_in() )
		{
			$arc_options = get_option( 'arc_options' );

			$ajax_mode = ( isset($arc_options['arc-ajax-login-on']) && $arc_options['arc-ajax-login-on'] == 'on' ) ? true : false;

			if ( $ajax_mode ) {
				// ajax mode

				// add blur with css to whole content
				add_filter( 'body_class', function( $classes ) {
				    return array_merge( $classes, array( 'arc-content-blur' ) );
				} );

				// add blur with css to whole content
				add_filter( 'wp_body_open', function( ) {
				    return 'aaaaaaaa';
				} );


			} else {
				// redirect mode

				if ( isset($arc_options['arc-redirect-to-url']) && !empty($arc_options['arc-redirect-to-url']) )
				{
					wp_redirect($arc_options['arc-redirect-to-url']);
				}
				else
				{
					$this->redirect_login($queried_object);
				}
			}

			return true;
		}

		return false;
	}


	/**
	 * return list of post and pages ids which are restricted, for internal use only
	 *
	 * @since    1.2.0
	*/
	private function get_restricted_ids()
	{
		$query_args = array(
			'post_type'      => array( 'any' ),
			'post_status'    => array( 'publish' ),
			'posts_per_page' => - 1,
			'offset'         => 0,
			'fields'         => 'ids',
			'meta_query'     => array(
				array(
					'key'     => 'arc_restricted_post',
					'value'   => 1,
					'compare' => '=',
					'type'    => 'NUMERIC'
				)
			)
		);

		$list = get_posts( $query_args );

		return $list;
	}

	private function get_restricted_category_ids()
	{
		$taxonomies = get_taxonomies( array(
			'public' => true
		) );

		$list = get_terms( array(
			'taxonomy' => $taxonomies,
			'fields'   => 'ids',
			'meta_query' => array(
				array(
					'key' => 'arc_restricted_category',
					'value' => 1,
					'type' => 'NUMBER'
				)
			)
		) );

		return $list;
	}

	/**
	 * Get all taxonomies related to the post
	 *
	 * @since    1.5.5
	 */
	private function get_post_taxonomies( $post_id )
	{
		// Get the names of all taxonomies related to the post
		$post_taxonomies = get_post_taxonomies( $post_id );

		if ( $post_taxonomies )
		{
			// Obtain objects of terms by the names of taxonomies
			$post_terms = wp_get_post_terms( $post_id, $post_taxonomies );

			if ( ! is_wp_error( $post_terms ) )
			{
				return $post_terms;
			}
		}

		return false;
	}

	/**
	 * prevent from displaying restricted posts in recent comments widget
	 *
	 * @since    1.2.0
	 */
	public function hide_restricted_posts_in_query($query_args)
	{
		if ( ! is_admin() && !is_user_logged_in() )
		{
			$restricted_ids = $this->get_restricted_ids();
			$restricted_category_ids = $this->get_restricted_category_ids();

			if ( is_array($restricted_ids) && sizeof($restricted_ids) > 0 )
			{
				$query_args['post__not_in'] = $restricted_ids;
			}

			if ( is_array($restricted_category_ids) && sizeof($restricted_category_ids) > 0 )
			{
				$query_args['category__not_in'] = $restricted_category_ids;
				$query_args['tag__not_in'] = $restricted_category_ids;
			}
		}

		return $query_args;
	}


	/**
	 * prevent from displaying restricted pages in primary default menu, also other places where wp_list_pages() used
	 *
	 * @since    1.3
	 */
	public function hide_restricted_pages_in_list()
	{
		$restricted_ids = array();
		if ( ! is_admin() && !is_user_logged_in() )
		{
			$restricted_ids = $this->get_restricted_ids();
		}

		return $restricted_ids;
	}


	/**
	 * updates meta query to skip restricted content from main WP query loop
	 *
	 * @since    1.3
	 */
	public function hide_restricted_in_main_query( $query )
	{
		if ( $query->is_main_query()
					&& !$query->is_singular()
					&& ! is_admin()
				 	&& !is_user_logged_in() )
		{
			$arc_options    = get_option( 'arc_options' );
			$hidden_content = ! ( isset( $arc_options['arc-restricted-content'] ) && $arc_options['arc-restricted-content'] === 'css-blurred' );

			if ( $hidden_content )
			{
				$meta_query = array(
					'relation' => 'OR',
					array(
						'key'     => 'arc_restricted_post',
						'value'   => 1,
						'compare' => '!=',
						'type'    => 'NUMERIC'
					),
					array(
						'key'     => 'arc_restricted_post',
						'compare' => 'NOT EXISTS',
					)
				);

				$query->set( 'meta_query', $meta_query );
			}

			$restricted_category_ids = $this->get_restricted_category_ids();

			if ( is_array($restricted_category_ids) && sizeof($restricted_category_ids) > 0 )
			{
				$query->set( 'category__not_in', $restricted_category_ids );
				$query->set( 'tag__not_in', $restricted_category_ids );
			}
		}
	}

	/**
	 * @since    1.5.5
	 */
	public function hide_restricted_categories_in_list( $cat_args )
	{
		if ( ! is_admin() && !is_user_logged_in() )
		{
			$restricted_ids = $this->get_restricted_category_ids();

			$cat_args['exclude'] = $restricted_ids;
		}

		return $cat_args;
	}

	/**
	 * generates data needed for arc ajax login form
	 *
	 * @since    1.5.0
	 */
	public function ajax_login_data()
	{
		wp_nonce_field( 'arc-ajax-login-nonce', 'arc-ajax-security' );
		?><input type="hidden" id="arc-ajax-login-url" value="<?php echo admin_url( 'admin-ajax.php' )?>"><?php
	}

	/**
	 * check request and make a user login with credentials provided
	 *
	 * @since    1.5.0
	 */
	public function ajax_do_login()
	{
		// First check the nonce, if it fails the function will break
    check_ajax_referer( 'arc-ajax-login-nonce', 'arc-ajax-security' );

		// Nonce is checked, get the POST data and sign user on
    $user = array();
    $user['user_login'] = $_POST['username'];
    $user['user_password'] = $_POST['password'];
    //$user['remember'] = true;

    $user_signon = wp_signon( $user, false );
    if ( !is_wp_error($user_signon) ){
        wp_set_current_user($user_signon->ID);
        wp_set_auth_cookie($user_signon->ID);
        echo json_encode(array('loggedin'=>true, 'message'=>__('Login successful.', 'anonymous-restricted-content')));
    } else {
				echo json_encode(array('error'=>true, 'message'=>__('Login failed.', 'anonymous-restricted-content')));
		}

		exit();
	}


	/**
	 * updates meta query to skip restricted content from main WP query loop
	 *
	 * @since    1.2.6
	 */
	public function restricted_login_message($message)
	{
		$is_restricted = ( isset($_REQUEST['arc_restricred']) ) ? intval($_REQUEST['arc_restricred']) : 0;

		if ( $is_restricted )
		{
			$arc_options = get_option( 'arc_options' );
			if ( isset($arc_options['arc-login-screen-message']) && !empty($arc_options['arc-login-screen-message']) )
			{
				$message = trim($arc_options['arc-login-screen-message']);
			}
			else
			{
				$message = __('This content was restricted from anonymous access. Please, login first:', 'anonymous-restricted-content');
			}
		}

		return $message;
	}

	public function add_post_class( $classes, $css_class, $post_id )
	{
		if ( ! is_admin() && ! is_user_logged_in() )
		{
			$arc_options    = get_option( 'arc_options' );
			$hidden_content = ! ( isset( $arc_options['arc-restricted-content'] ) && $arc_options['arc-restricted-content'] === 'css-blurred' );
			$restricted_ids = $this->get_restricted_ids();

			if ( ! $hidden_content && in_array( $post_id, $restricted_ids ) ) {
				$classes[] = 'arc-post-css-blurred';
			}

		}

		return $classes;
	}

	public function restricted_rest_api( $result )
	{
		if ( is_array( $result ) && ! is_user_logged_in() ) {
			if ( isset( $result['id'], $result['meta']['arc_restricted_post'] ) && $result['meta']['arc_restricted_post'] === true ) {
				return new WP_Error(
					'rest_restricted_content',
					__( 'This content was restricted from anonymous access.', 'anonymous-restricted-content' ),
					array( 'status' => 401 )
				);
			}

			if ( isset( $result[0]['id'] ) ) {
				foreach ( $result as $k => $item ) {
					if ( isset( $item['meta']['arc_restricted_post'] ) && $item['meta']['arc_restricted_post'] === true ) {
						$result[ $k ] = __( 'This content was restricted from anonymous access.', 'anonymous-restricted-content' );
					}
				}
			}
		}

		return $result;
	}


}
