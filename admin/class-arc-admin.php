<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://wordpress.org/plugins/anonymous-restricted-content/
 * @since      1.0.0
 *
 * @package    ARC
 * @subpackage ARC/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * @package    ARC
 * @subpackage ARC/admin
 * @author     Taras Sych <taras.sych@gmail.com>
 */
class ARC_Admin {

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
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {
		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/arc-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		if ( !is_plugin_active('classic-editor/classic-editor.php') ) {
			wp_enqueue_script(
	        'arc-admin.js',
	        plugin_dir_url( __FILE__ ) . 'js/arc-admin.js',
	        array( 'wp-element', 'wp-components', 'wp-i18n', 'wp-plugins', 'wp-edit-post' ), // Dependencies, defined above.
	        $this->version,
	        true
	    );

			wp_localize_script( 'arc-admin.js', 'ArcLStrings', $this->get_language_strings() );

	    // if(get_current_screen()->base == 'post'){
			//
	    //   global $post;
			//
	    //   $is_post_restricted = ( get_post_meta($post->ID, 'arc_restricted_post', true) && (bool) get_post_meta($post->ID, 'arc_restricted_post', true) === true ) ? true : false;
			//
	    //   $js_arc_vars = array(
	    //     'arc_checked' => $is_post_restricted,
	    //     'post_id' => $post->ID
	    //   );
			// 	wp_add_inline_script( 'arc-admin.js', 'const arc_vars = ' . json_encode( $js_arc_vars ), 'before' );
	    // }
		}
	}

	/**
  * translations strings for JS scripts
  *
  * @since    1.5.2
  */
	private function get_language_strings() {
		$strings = array(
        'RestrictedForAnonymousUsers' => __( 'Restricted for anonymous users', 'anonymous-restricted-content' ),
    );

    return $strings;
	}

	/**
	 * display restricted option checkbox on page/post editing screens
	 *
	 * @since    1.0.0
	 */
	public function add_restricted_checkbox_to_post_submitbox($post){
		$is_post_restricted = ( get_post_meta($post->ID, 'arc_restricted_post', true) && (bool) get_post_meta($post->ID, 'arc_restricted_post', true) === true ) ? true : false;

		?>
		<div class="misc-pub-section misc-pub-restricted-post">
			<input type="checkbox" name="restricted_post_value" id="restricted_post_value" <?php if( $is_post_restricted ) echo 'checked'; ?> value="yes">
			<label for="restricted_post_value"><?php _e('Restricted for anonymouse users', 'anonymous-restricted-content')?></label>
      <input type="hidden" name="arc_classic_editor" value="1">
		</div>
		<?php
	}

	/**
	 * save restricted option during page/post saving
	 *
	 * @since    1.0.0
	 */
	public function save_restricted_option_on_post_edit($post_id)
	{
    if( isset($_REQUEST['arc_classic_editor'])){

      if ( isset($_REQUEST['restricted_post_value']) && sanitize_text_field($_REQUEST['restricted_post_value']) == 'yes' )
      {
        add_post_meta($post_id, 'arc_restricted_post', true); // add meta field when check box is checked ON
      }
      else
      {
        delete_post_meta($post_id, 'arc_restricted_post'); // clear meta field when check box is checked OFF
      }
    }
	}

	/**
	 * Display restricted option checkbox on category adding screens
	 *
	 * @since    1.5.5
	 */
	public function add_restricted_checkbox_to_add_category_addtag( $taxonomy )
	{
		?>
		<div class="form-field term-restricted-wrap">
			<label>
				<input type="checkbox" name="restricted_category_value" value="yes">
				<?php _e('Restricted for anonymouse users', 'anonymous-restricted-content')?>
			</label>
		</div>
		<?php
	}

	/**
	 * Display restricted option checkbox on category editing screens
	 *
	 * @since    1.5.5
	 */
	public function add_restricted_checkbox_to_edit_category_addtag( $tag )
	{
		$is_category_restricted = get_term_meta( $tag->term_id, 'arc_restricted_category', true ) && (bool) get_term_meta( $tag->term_id, 'arc_restricted_category', true ) === true;

		?>
		<tr class='form-field'>
			<th scope='row'><label for="restricted_category_value"><?php _e('Restricted for anonymouse users', 'anonymous-restricted-content')?></label></th>
			<td>
				<input type="checkbox" name="restricted_category_value" id="restricted_category_value" <?php if ( $is_category_restricted ) echo 'checked'; ?> value="yes">
			</td>
		</tr>
		<?php
	}

	/**
	 * Save restricted option during category saving
	 *
	 * @since    1.5.5
	 */
	public function save_restricted_option_on_category( $term_id ) {
		if ( isset($_REQUEST['restricted_category_value']) && sanitize_text_field($_REQUEST['restricted_category_value']) == 'yes' )
		{
			add_term_meta( $term_id , 'arc_restricted_category', true );
		}
		else
		{
			delete_term_meta( $term_id, 'arc_restricted_category' );
		}
	}

	/**
    * @since    1.2.3
	*/
	public function register_plugin_settings()
    {
	    register_setting( 'arc', 'arc_options' );

	    add_settings_section(
		    'arc_section_options',
		    __( 'Redirect Options', 'anonymous-restricted-content' ),
		    array($this, 'plugin_options_cb'),
		    'arc'
	    );

	    add_settings_field(
		    'arc-redirect-to-url', // as of WP 4.6 this value is used only internally
		    __( 'Redirect URL:', 'anonymous-restricted-content' ),
		    array($this, 'plugin_options_redirect_field_cb'),
		    'arc',
		    'arc_section_options',
		    [
			    'label_for' => 'arc-redirect-to-url',
			    'class' => 'arc-row'
		    ]
	    );

			add_settings_section(
		    'arc_section_login_screen',
		    __( 'Login Screen:', 'anonymous-restricted-content' ),
		    array($this, 'plugin_login_screen_cb'),
		    'arc'
	    );

			add_settings_field(
		    'arc-login-screen-message',
		    __( 'Login screen message:', 'anonymous-restricted-content' ),
		    array($this, 'plugin_options_login_screen_message'),
		    'arc',
		    'arc_section_login_screen',
		    [
			    'label_for' => 'arc-login-screen-message',
			    'class' => 'arc-row'
		    ]
	    );

			add_settings_section(
		    'arc_section_ajax_login',
		    __( 'AJAX Alternative:', 'anonymous-restricted-content' ),
		    array($this, 'plugin_ajax_login_screen_cb'),
		    'arc'
	    );

			add_settings_field(
		    'arc-ajax-login-on',
		    __( 'AJAX Login:', 'anonymous-restricted-content' ),
		    array($this, 'plugin_options_ajax_login_on'),
		    'arc',
		    'arc_section_ajax_login',
		    [
			    'label_for' => 'arc-ajax-login-on',
			    'class' => 'arc-row'
		    ]
	    );

		add_settings_section(
		'arc_section_restricted',
			__( 'Restricted:', 'anonymous-restricted-content' ),
			array( $this, 'plugin_restricted_screen_cb' ),
			'arc'
		);

		add_settings_field(
			'arc-restricted-content',
			__( 'Restricted Content:', 'anonymous-restricted-content' ),
			array( $this, 'plugin_options_restricted_content' ),
			'arc',
			'arc_section_restricted',
			[
				'label_for' => 'arc-restricted-content',
				'class'     => 'arc-row'
			]
		);


    }

	/**
	* @since    1.2.3
	*/
    public function plugin_options_redirect_field_cb($args)
    {
	    $options = get_option( 'arc_options' );
	    $value = isset( $options[$args['label_for']] ) ? $options[$args['label_for']] : '';

        ?>
            <input type="url" class="regular-text code" name="arc_options[<?php echo esc_attr( $args['label_for'] ); ?>]" id="<?php echo esc_attr( $args['label_for'] ); ?>" value="<?php echo $value; ?>">
        <?php
    }

		/**
		* @since    1.3
		*/
	    public function plugin_options_login_screen_message($args)
	    {
		    $options = get_option( 'arc_options' );
		    $value = isset( $options[$args['label_for']] ) ? $options[$args['label_for']] : '';

	        ?>
	            <input type="text" class="regular-text code" name="arc_options[<?php echo esc_attr( $args['label_for'] ); ?>]" id="<?php echo esc_attr( $args['label_for'] ); ?>" value="<?php echo $value; ?>">
	        <?php
	    }

		/**
		* @since    1.5.0
		*/
	    public function plugin_options_ajax_login_on($args)
	    {
		    $options = get_option( 'arc_options' );
		    $value = isset( $options[$args['label_for']] ) ? $options[$args['label_for']] : '';

	        ?>
						<label for="<?php echo esc_attr( $args['label_for'] ); ?>-off">
							<input type="radio" value="off" name="arc_options[<?php echo esc_attr( $args['label_for'] ); ?>]"
								<?php if ( $value == 'off' || ! $value ) echo "checked"; ?>
								id = "<?php echo esc_attr( $args['label_for'] ); ?>-off"
							>
							OFF
						</label>
						&nbsp;
						<label for="<?php echo esc_attr( $args['label_for'] ); ?>-off">
							<input type="radio" value="on" name="arc_options[<?php echo esc_attr( $args['label_for'] ); ?>]"
								<?php if ( $value == 'on' ) echo "checked"; ?>
								id = "<?php echo esc_attr( $args['label_for'] ); ?>-on"
							>
							ON
						</label>
						<br>
						<br>
						<i>Important: your website theme should match WP requirements and has "wp_body_open" and "body_class" actions properly used in theme code header.</i><br>
						<i>Security Notice: As this method uses CSS and JS to hide your restricted content - it can NOT be used with any sensitive and personal data, as data privacy is not guaranteed by the plugin in case JS is turned Off in client browser.</i>
	        <?php
	    }

	/**
	 * @since     1.6.1
	 */
	public function plugin_options_restricted_content( $args )
	{
		$options = get_option( 'arc_options' );
		$value = isset( $options[$args['label_for']] ) ? $options[$args['label_for']] : '';
		?>
		<select name="arc_options[<?php echo esc_attr( $args['label_for'] ); ?>]" id="<?php echo esc_attr( $args['label_for'] ); ?>">
			<option value="hidden" <?php selected( 'hidden', $value ); ?>>Hidden</option>
			<option value="css-blurred" <?php selected( 'css-blurred', $value ); ?>>CSS Blurred</option>
		</select>
		<?php
	}

	/**
	* @since    1.2.3
	*/
    public function plugin_options_cb( $args )
    {
        ?>
        <p><?php _e( 'Anonymous users, who are restricted to access content, can be redirected to the URL you provide below.', 'anonymous-restricted-content' ); ?></p>
        <p><?php _e( 'Leave Redirect URL empty to force anonymous users to login (redirect to Wordpress Login page).', 'anonymous-restricted-content' ); ?></p>
        <?php
    }

		/**
		* @since    1.2.6
		*/
	    public function plugin_login_screen_cb( $args )
	    {
	        ?>
	        <p><?php _e( 'Message to be shown on the login screen, for users who trying to access restricted content and forced to login.', 'anonymous-restricted-content' ); ?></p>
	        <p><?php _e( 'Default message: "This content was restricted from anonymous access. Please, login first:"', 'anonymous-restricted-content' ); ?></p>
	        <?php
	    }

		/**
		* @since    1.5.0
		*/
	    public function plugin_ajax_login_screen_cb( $args )
	    {
	        ?>
	        <p><?php _e( 'Turn ON this option to prevent users redirects to the WP login page.', 'anonymous-restricted-content' ); ?></p>
	        <p><?php _e( 'And use AJAX login form in floating popup instead.', 'anonymous-restricted-content' ); ?></p>
	        <p><?php _e( 'This feature should prevent issues with "loop login screen redirects" in case of other plugin integration conflicts.', 'anonymous-restricted-content' ); ?></p>
	        <?php
	    }

	/**
	 * @since     1.6.1
	 */
	public function plugin_restricted_screen_cb()
	{

	}

	/**
	* @since    1.2.3
	*/
    public function register_plugin_admin_menu()
    {
	    add_submenu_page(
		    'options-general.php',
		    __( 'Anonymous Restricted Content', 'anonymous-restricted-content' ),
		    __( 'Restricted Content', 'anonymous-restricted-content' ),
		    'manage_options',
		    'anonymous-restricted-content',
		    array($this, 'plugin_options_page_html_cb')
	    );
    }

	/**
	* @since    1.2.3
	*/
    public function plugin_options_page_html_cb()
    {
        // check user capabilities
	    if ( ! current_user_can( 'manage_options' ) ) {
		    return;
	    }

	    // show error/update messages
	    settings_errors( 'arc_messages' );
	    ?>
        <div class="wrap">
            <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
            <form action="options.php" method="post">
			    <?php
			    // output security fields for the registered setting "wporg"
			    settings_fields( 'arc' );
			    // output setting sections and their fields
			    // (sections are registered for "wporg", each field is registered to a specific section)
			    do_settings_sections( 'arc' );
			    // output save settings button
			    submit_button( 'Save Settings' );
			    ?>
            </form>
        </div>
	    <?php
    }

	/**
	* @since    1.4.0
	*/
	public function arc_admin_notices() {

		if ( ! empty( $_REQUEST['arc_posts_restricted'] ) ) {
	    $restricted_count = intval( $_REQUEST['arc_posts_restricted'] );
	    printf( '<div id="message" class="updated fade"><br>' .
	      _n( 'Restricted %s posts (pages) from Anonymous Access.',
	        'Restricted %s posts (pages) from Anonymous Access.',
	        $restricted_count,
	        'arc_posts_restricted'
	      ) . '<br><br></div>', $restricted_count );
	  }
	}

	/**
	* @since    1.4.0
	*/
	public function posts_list_restricted_column($columns) {

		$new_columns = array();

	 	if ( is_array($columns) && sizeof($columns) > 0 ) {
			foreach ($columns as $key => $column) {
				$new_columns[$key] = $column;
				if ($key == 'cb') {
					$new_columns['is_arc'] = __( 'Restr', 'anonymous-restricted-content');
				}
			}
		}

		return $new_columns;
	}

	/**
	* @since    1.4.0
	*/
	public function fill_restricted_column($column, $post_id) {
		switch ( $column ) {
			case 'is_arc' : {
				$is_restricted = intval(get_post_meta( $post_id , 'arc_restricted_post' , true ));
				if ( $is_restricted == 1 ) {
					echo "+";
				} else {
					echo "—";
				}
				break;
			}
		}
	}

	/**
	* @since    1.5.5
	*/
	public function fill_category_restricted_column( $string, $column_name, $term_id ) {
		switch ( $column_name ) {
			case 'is_arc' : {
				$is_restricted = intval(get_term_meta( $term_id , 'arc_restricted_category' , true ));
				if ( $is_restricted == 1 ) {
					echo "+";
				} else {
					echo "—";
				}
				break;
			}
		}
	}

	/**
	* @since    1.4.0
	*/
	public function register_posts_bulk_actions($bulk_actions) {
	  $bulk_actions['arc_do_restrict'] = __( 'Restrict for Anonymous', 'anonymous-restricted-content');
	  return $bulk_actions;
	}

	/**
	* @since    1.4.0
	*/
	public function handle_posts_bulk_actions( $redirect_to, $doaction, $post_ids ) {
		if ( $doaction !== 'arc_do_restrict' ) {
	    return $redirect_to;
	  }

		foreach ( $post_ids as $post_id ) {
    	add_post_meta($post_id, 'arc_restricted_post', true);
	  }

	  $redirect_to = add_query_arg( 'arc_posts_restricted', count( $post_ids ), $redirect_to );
	  return $redirect_to;
	}

	/**
	* @since    1.2.3
    */
    public function add_action_links($links, $plugin_file)
    {
        if ( $plugin_file == 'anonymous-restricted-content/anonymous-restricted-content.php' )
        {
	        $plugin_links = array('<a href="' . admin_url( 'options-general.php?page=anonymous-restricted-content' ) . '">'.__('Settings', 'anonymous-restricted-content').'</a>', );
	        return array_merge( $links, $plugin_links );
        }

        return $links;
    }

	/**
	* @since    1.2.0
	*/
    public function register_gutenberg_meta(){
        register_meta( 'post', 'arc_restricted_post', array(
          'show_in_rest' => true,
          'single' => true,
          'type' => 'boolean',
        ) );
    }
}
