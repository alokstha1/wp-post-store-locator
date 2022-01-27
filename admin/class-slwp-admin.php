<?php
/**
 * Backend Admin's class
 *
 * @author Alok Shrestha
 * @since  2.0.0
 */

if ( !defined( 'ABSPATH' ) ) exit;

if ( !class_exists( 'SLWP_Admin' ) ) {
    class SLWP_Admin {
        /**
        * Class constructor
        */
        function __construct() {
            add_action( 'admin_menu', array( $this, 'slwp_register_menu_page' ) );
            add_action( 'admin_init', array( $this, 'slwp_register_settings' ) );
            
            add_action( 'admin_enqueue_scripts', array( $this, 'slwp_stores_admin_scripts' ) );
            add_action( 'add_meta_boxes', array( $this, 'slwp_stores_meta_boxes' ) );
            add_action( 'wp_ajax_return_address_latlng', array( $this, 'slwp_stores_return_address_latlng' ) );
            add_action( 'save_post', array( $this, 'slwp_stores_save_posts' ) );
        }

        /**
        * Add menu page.
        * @since 1.0.0
        * @return void
        */
        public function slwp_register_menu_page() {
            add_menu_page( __( 'Store Settings', 'slwp-stores'), __( 'Store Settings', 'slwp-stores'), 'manage_options', 'slwp_stores.php', array( $this, 'slwp_add_setting_page' ), '', 20 );
        }

        /**
        * Enqueue Google Map API into the admin.
        * @since 2.0.0
        * @return void
        */
        public function slwp_stores_admin_scripts() {

            $slwp_store_setting = get_option('slwp_store_options');

            wp_enqueue_style( 'admin-style', SLWP_URL .'admin/css/admin-style.css' );

            wp_enqueue_script('jquery-ui-tabs');

            //deregister other google map scripts if enqueued
            slwp_stores_deregister_other_gmaps();

            wp_enqueue_script( 'slwp-gmap', '//maps.google.com/maps/api/js' . slwp_stores_gmap_api_params('browser_key'), false, '1.0.0', true );

            wp_enqueue_script( 'admin-script', SLWP_URL .'admin/js/admin-script.js', array('jquery'), '1.0.0', true );
            wp_localize_script( 'admin-script', 'slwp_stores', array(
                'slwp_settings' =>   stripslashes_deep( $slwp_store_setting ),
                'ajaxurl' => admin_url( 'admin-ajax.php' )
                )
            );
        }

        /**
        * Callback function of add_menu_page. Displays the page's content.
        * @since 1.0.0
        * @return void
        */
        public function slwp_add_setting_page() {

            require SLWP_PLUGIN_DIR .'admin/view/store-settings-form.php';
        }

        /**
        * Register settings options and save to wp_options table.
        * @since 1.0.0
        * @return void
        */
        public function slwp_register_settings() {
            register_setting( 'slwp_store_options', 'slwp_store_options', array( $this, 'slwp_sanitize_settings' ) );

        }

        /**
        * Save admin form settings value to slwp_store_option option.
        * @since 1.0.0
        * @return array
        */
        public function slwp_sanitize_settings() {

            if ( !isset( $_POST['_wpnonce'] ) || !wp_verify_nonce( $_POST['_wpnonce'], 'slwp_store_options-options' ) )
                return false;
            
            $input_options = array();
            $slwp_store_setting = get_option('slwp_store_options');


            //Map Api Section
            // $input_options['server_key'] = sanitize_text_field( $_POST['slwp_store_setting']['server_key']);

            $input_options['browser_key'] = sanitize_text_field( $_POST['slwp_store_setting']['browser_key']);

            $input_options['language'] = wp_filter_nohtml_kses( $_POST['slwp_store_setting']['language'] );
            $input_options['region'] = wp_filter_nohtml_kses( $_POST['slwp_store_setting']['region'] );
            //End of Map Api Section

            //General Map Setting

            $input_options['start_point'] = sanitize_text_field( $_POST['slwp_store_setting']['start_point'] );

            // If no location name is then we also empty the latlng values from the hidden input field.
            if ( empty( $input_options['start_point'] ) ) {
                $this->settings_error( 'start_point' );
                $input_options['start_latlng'] = '';
            } else {

                    /*
                     * If the start latlng is empty, but a start location name is provided,
                     * then make a request to the Geocode API to get it.
                     *
                     * This can only happen if there is a JS error in the admin area that breaks the
                     * Google Maps Autocomplete. So this code is only used as fallback to make sure
                     * the provided start location is always geocoded.
                     */
                    if ( $slwp_store_setting['start_point'] != $_POST['slwp_store_setting']['start_point'] && $slwp_store_setting['start_latlng'] == $_POST['slwp_store_setting']['start_latlng'] || empty( $_POST['slwp_store_setting']['start_latlng'] ) ) {
                        $start_latlng = slwp_stores_get_address_latlng( $_POST['slwp_store_setting']['start_point'] );
                    } else {
                        $start_latlng = sanitize_text_field( $_POST['slwp_store_setting']['start_latlng'] );
                    }

                    $input_options['start_latlng'] = sanitize_text_field( $start_latlng );
                }

                $input_options['zoom_level'] = intval( $_POST['slwp_store_setting']['zoom_level'] );

                $input_options['max_zoom_level'] = intval( $_POST['slwp_store_setting']['max_zoom_level'] );

                $input_options['direction_view_control'] = isset ($_POST['slwp_store_setting']['direction_view_control'] ) ? 1 : 0;

                $input_options['map_type_control'] = isset( $_POST['slwp_store_setting']['map_type_control'] ) ? 1 : 0;

                $input_options['scrollwheel_zoom'] = isset( $_POST['slwp_store_setting']['scrollwheel_zoom'] ) ? 1 : 0;

                $input_options['map_type'] = sanitize_text_field( $_POST['slwp_store_setting']['map_type'] );
            //End of General Map Setting

            //Start of Search
                $input_options['autocomplete'] = isset( $_POST['slwp_store_setting']['autocomplete'] ) ? 1 : 0;

                $input_options['distance_unit'] = ( $_POST['slwp_store_setting']['distance_unit'] == 'km' ) ? 'km' : 'mi';


            // Check for a valid max results value, otherwise we use the default.
                if ( !empty( $_POST['slwp_store_setting']['max_results'] ) ) {
                    $input_options['max_results'] = sanitize_text_field( $_POST['slwp_store_setting']['max_results'] );
                } else {
                    $this->settings_error( 'max_results' );
                    $input_options['max_results'] = slwp_stores_get_default_setting( 'max_results' );
                }


            // See if a search radius value exist, otherwise we use the default.
                if ( !empty( $_POST['slwp_store_setting']['radius_options'] ) ) {
                    $input_options['radius_options'] = sanitize_text_field( $_POST['slwp_store_setting']['radius_options'] );
                } else {
                    $this->settings_error( 'radius_options' );
                    $input_options['radius_options'] = slwp_stores_get_default_setting( 'radius_options' );
                }
            //End of Search Section

            //Role Manager Section
                if ( isset( $_POST['slwp_store_setting']['post_type'] ) && !empty( $_POST['slwp_store_setting']['post_type'] ) ) {

                    $input_options['post_type'] = $_POST['slwp_store_setting']['post_type'];

                }

                $input_options['show_url_field'] = isset( $_POST['slwp_store_setting']['show_url_field'] ) ? 1 : 0;
                $input_options['show_phone_field'] = isset( $_POST['slwp_store_setting']['show_phone_field'] ) ? 1 : 0;
                $input_options['show_description_field'] = isset( $_POST['slwp_store_setting']['show_description_field'] ) ? 1 : 0;

            return $input_options;
        }

        /**
        * Add meta box to selected post types.
        * @since 1.0.0
        * @return void
        */
        public function slwp_stores_meta_boxes() {
            $slwp_store_setting = get_option('slwp_store_options');
            if ( !empty( $slwp_store_setting['post_type'] ) && isset( $slwp_store_setting['post_type'] ) ) {
                $option_postTypes = $slwp_store_setting['post_type'];

                foreach ($option_postTypes as $type_value) {

                    add_meta_box( 'meta-box-id', __( 'SLWP Stores Box', 'slwp-stores' ), array( $this, 'slwp_display_metabox' ), $type_value );
                }
            }
        }

        /**
        * Callback function displaying form elements to add_meta_box.
        * @since 1.0.0
        * @return void
        */
        public function slwp_display_metabox() {
            require SLWP_PLUGIN_DIR .'admin/view/metabox-form.php';
        }

        /**
        * Ajax response returns concatinated lat-long
        * @return string lat,lng(121.230045,234.000034573)
        * @since 1.0.0
        */
        public function slwp_stores_return_address_latlng() {
            
            if ( isset( $_POST['location'] ) && !empty( $_POST['location'] ) ) {
                
                $location = slwp_stores_get_address_latlng($_POST['location']);
                echo $location;
            }
            die();
        }

        /**
        * Save stores- items meta
        * @since 1.0.0
        * @return void
        */
        public function slwp_stores_save_posts( $post_id ) {

            if ( isset( $_POST['slwp_store_meta'] ) && !empty( $_POST['slwp_store_meta'] ) ) {
                $sanitized_post_array = array();

                foreach ($_POST['slwp_store_meta'] as $post_value) {
                    $post_array['slwp_name'] = sanitize_text_field( $post_value['slwp_name']);
                    $post_array['slwp_location'] = sanitize_text_field( $post_value['slwp_location']);
                    $post_array['slwp_location_latn'] = sanitize_text_field( $post_value['slwp_location_latn']);
                    $post_array['slwp_url'] = esc_url( $post_value['slwp_url']);
                    $post_array['slwp_phone'] = sanitize_text_field( $post_value['slwp_phone']);
                    $post_array['slwp_description'] = sanitize_text_field( $post_value['slwp_description']);
                    array_push( $sanitized_post_array, $post_array);
                }

                update_post_meta( $post_id, 'slwp_saved_locators', array_values( $sanitized_post_array ) );
            } else {
                update_post_meta( $post_id, 'slwp_saved_locators', '' );
            }
        }

        /**
         * Handle the different validation errors for the plugin settings.
         * @since 1.0.0
         * @return string
         */
        private function settings_error( $error_type ) {

            switch ( $error_type ) {
                case 'max_results':
                $error_msg = __( 'The max results field cannot be empty, the default value has been restored.', 'slwp-stores' );
                break;
                case 'radius_options':
                $error_msg = __( 'The search radius field cannot be empty, the default value has been restored.', 'slwp-stores' );
                break;
                case 'start_point':
                $error_msg = sprintf( __( 'Please provide the name of a city or country that can be used as a starting point under "Map Settings". %s This will only be used if auto-locating the user fails, or the option itself is disabled.', 'slwp-stores' ), '<br><br>' );
                break;
            }

            add_settings_error( 'setting-errors', esc_attr( 'settings_fail' ), $error_msg, 'error' );
        }
    }
}