<?php
/**
 * Plugin Name: Store Locator for WordPress Posts
 * Plugin URI:
 * Description: A full-featured map maker & location management interface for creating store locator for any posts of any post type.
 * Version: 2.0.0
 * Author: WP Tiro
 * Author URI: http://wptiro.com
 * Text Domain: slwp-stores
 * License: GPLv3 or later
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}


/**
* Initialize Slwp_Stores class
*/
if ( !class_exists('Slwp_Stores') ) {

    class Slwp_Stores {

        /**
        * Class constructor
        */
        function __construct() {
            $this->define_constants();
            $this->slwp_includes();
            $this->slwp_plugin_setting();

            $this->frontend     = new SLWP_Frontend();
            $this->backend      = new SLWP_Admin();
        }

        /**
         * Setup plugin constants.
         * @since 2.0.0
         * @return void
         */
        public function define_constants() {
            if ( !defined( 'SLWP_URL' ) )
                define( 'SLWP_URL', plugin_dir_url( __FILE__ ) );

            if ( !defined( 'SLWP_BASENAME' ) )
                define( 'SLWP_BASENAME', plugin_basename( __FILE__ ) );

            if ( !defined( 'SLWP_PLUGIN_DIR' ) )
                define( 'SLWP_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
        }

        /**
        * Set admin form default value.
        */
        public function slwp_plugin_setting() {
            global $slwp_store_default_setting;
            $slwp_store_default_setting = slwp_stores_default_settings();
        }

        /**
        * Includes required files.
        * @since 2.0.0
        * @return void
        */
        public function slwp_includes() {
            require_once( SLWP_PLUGIN_DIR. 'inc/slwp-extras.php' );
            require_once( SLWP_PLUGIN_DIR. 'admin/class-slwp-admin.php' );
            require_once( SLWP_PLUGIN_DIR. 'public/class-slwp-frontend.php' );
        }

    }

    $slwp_stores = new Slwp_Stores();
}