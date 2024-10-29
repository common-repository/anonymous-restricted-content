<?php

/**
 * Fired during plugin deactivation
 *
 * @link       https://wordpress.org/plugins/anonymous-restricted-content/
 * @since      1.0.0
 *
 * @package    ARC
 * @subpackage ARC/includes
 */

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      1.0.0
 * @package    ARC
 * @subpackage ARC/includes
 * @author     Taras Sych <taras.sych@gmail.com>
 */
class ARC_Deactivator {

	/**
	 * Cleaning before turning off.
	 *
	 * Delete all meta fields created during this plugin lifetime.
	 *
	 * @since    1.0.0
	 */
	public static function deactivate() {

		delete_post_meta_by_key( 'arc_restricted_post' );
		delete_metadata( 'term', null, 'arc_restricted_category_value', '', true ); // must be deprecated in future

		return true;
	}

}
