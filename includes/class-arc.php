<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://wordpress.org/plugins/anonymous-restricted-content/
 * @since      1.0.0
 *
 * @package    ARC
 * @subpackage ARC/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    ARC
 * @subpackage ARC/includes
 * @author     Taras Sych <taras.sych@gmail.com>
 */
class ARC {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      ARC_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		if ( defined( 'ARC_VERSION' ) ) {
			$this->version = ARC_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = ARC_PACKAGE_NAME;

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - ARC_Loader. Orchestrates the hooks of the plugin.
	 * - ARC_i18n. Defines internationalization functionality.
	 * - ARC_Admin. Defines all hooks for the admin area.
	 * - ARC_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-arc-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-arc-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-arc-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-arc-public.php';

		$this->loader = new ARC_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the ARC_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new ARC_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new ARC_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_filter('plugin_action_links', $plugin_admin, 'add_action_links', 10, 5);

		$this->loader->add_filter('bulk_actions-edit-post', $plugin_admin, 'register_posts_bulk_actions', 10, 1);
		$this->loader->add_filter('bulk_actions-edit-page', $plugin_admin, 'register_posts_bulk_actions', 10, 1);
		$this->loader->add_filter('handle_bulk_actions-edit-post', $plugin_admin, 'handle_posts_bulk_actions', 10, 3);
		$this->loader->add_filter('handle_bulk_actions-edit-page', $plugin_admin, 'handle_posts_bulk_actions', 10, 3);

		$this->loader->add_filter('manage_posts_columns', $plugin_admin, 'posts_list_restricted_column');
		$this->loader->add_filter('manage_pages_columns', $plugin_admin, 'posts_list_restricted_column');
		$this->loader->add_filter('manage_posts_custom_column', $plugin_admin, 'fill_restricted_column', 10, 3);
		$this->loader->add_filter('manage_pages_custom_column', $plugin_admin, 'fill_restricted_column', 10, 3);

		$this->loader->add_filter('manage_edit-category_columns', $plugin_admin, 'posts_list_restricted_column');
		$this->loader->add_filter('manage_edit-post_tag_columns', $plugin_admin, 'posts_list_restricted_column');
		$this->loader->add_filter('manage_category_custom_column', $plugin_admin, 'fill_category_restricted_column', 10, 3);
		$this->loader->add_filter('manage_post_tag_custom_column', $plugin_admin, 'fill_category_restricted_column', 10, 3);

		$this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
		$this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );

		$this->loader->add_action('post_submitbox_misc_actions', $plugin_admin, 'add_restricted_checkbox_to_post_submitbox'); // display restricted checkbox on pages and posts edit screens in admin
		$this->loader->add_action('edit_post', $plugin_admin, 'save_restricted_option_on_post_edit'); // save restricted meta field, attach it to the edited page/post
		$this->loader->add_action('category_add_form_fields', $plugin_admin, 'add_restricted_checkbox_to_add_category_addtag');
		$this->loader->add_action('category_edit_form_fields', $plugin_admin, 'add_restricted_checkbox_to_edit_category_addtag');
		$this->loader->add_action('created_category', $plugin_admin, 'save_restricted_option_on_category'); // save restricted meta field when adding a new category
		$this->loader->add_action('edited_category', $plugin_admin, 'save_restricted_option_on_category');
		$this->loader->add_action('add_tag_form_fields', $plugin_admin, 'add_restricted_checkbox_to_add_category_addtag');
		$this->loader->add_action('post_tag_edit_form_fields', $plugin_admin, 'add_restricted_checkbox_to_edit_category_addtag');
		$this->loader->add_action('created_post_tag', $plugin_admin, 'save_restricted_option_on_category');
		$this->loader->add_action('edited_post_tag', $plugin_admin, 'save_restricted_option_on_category');
		$this->loader->add_action('admin_init', $plugin_admin, 'register_plugin_settings');
		$this->loader->add_action('admin_menu', $plugin_admin, 'register_plugin_admin_menu');
		$this->loader->add_action('init', $plugin_admin, 'register_gutenberg_meta');
		$this->loader->add_action('admin_notices', $plugin_admin, 'arc_admin_notices');

	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new ARC_Public( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );
		$this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );

		$this->loader->add_action('pre_get_posts', $plugin_public, 'hide_restricted_in_main_query');
		$this->loader->add_action('wp_body_open', $plugin_public, 'ajax_login_data');
		$this->loader->add_action('wp_ajax_nopriv_arcajaxlogin', $plugin_public, 'ajax_do_login');

		$this->loader->add_filter('login_message', $plugin_public, 'restricted_login_message');
		$this->loader->add_filter('pre_handle_404', $plugin_public, 'redirect_restricted_content_to_login', 10, 2 );
		$this->loader->add_filter('widget_comments_args', $plugin_public, 'hide_restricted_posts_in_query');
		$this->loader->add_filter('widget_posts_args', $plugin_public, 'hide_restricted_posts_in_query');
		$this->loader->add_filter('wp_list_pages_excludes', $plugin_public, 'hide_restricted_pages_in_list'); //hides in default primary menu
		$this->loader->add_filter('widget_categories_args', $plugin_public, 'hide_restricted_categories_in_list');

		$this->loader->add_filter('post_class', $plugin_public, 'add_post_class', 10, 3);

		$this->loader->add_filter( 'rest_pre_echo_response', $plugin_public, 'restricted_rest_api' );

	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    ARC_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

}
