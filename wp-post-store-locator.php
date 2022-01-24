<?php
/**
 * Plugin Name: Store Locator for WordPress Posts
 * Plugin URI:
 * Description:A full-featured map maker & location management interface for creating store locator for any posts of any post type.
 * Version:1.1
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
            $this->slwp_includes();
            $this->slwp_plugin_setting();

            add_action( 'admin_init', array( $this, 'slwp_register_settings' ) );

            add_action( 'admin_menu', array( $this, 'slwp_register_menu_page' ) );

            add_action( 'wp_enqueue_scripts', array( $this, 'slwp_stores_add_scripts_style' ) );
            add_action( 'admin_enqueue_scripts', array( $this, 'slwp_stores_admin_scripts_style' ) );
            add_action( 'add_meta_boxes', array( $this, 'slwp_stores_meta_boxes' ) );
            add_action( 'wp_ajax_return_address_latlng', array( $this, 'slwp_stores_return_address_latlng' ) );
            // add_action( 'wp_ajax_nopriv_return_address_latlng', array( $this, 'slwp_stores_return_address_latlng' ) );
            add_action( 'save_post', array( $this, 'slwp_stores_save_posts' ) );
            add_shortcode( 'slwp-stores', array( $this, 'slwp_list_stores') );
            add_action( 'wp_ajax_slwp_store_search', array( $this, 'slwp_store_search' ) );
            add_action( 'wp_ajax_nopriv_slwp_store_search', array( $this, 'slwp_store_search' ) );
        }

        /**
        * Set admin form default value.
        */
        public function slwp_plugin_setting() {
            global $aka_store_default_setting;
            $aka_store_default_setting = slwp_stores_default_settings();
        }

        /**
        * Enqueue required admin styles and scripts.
        */
        public function slwp_stores_admin_scripts_style() {
            $slwp_store_setting = get_option('slwp_store_options');


            wp_enqueue_style( 'admin-style', plugin_dir_url( __FILE__ ).'admin/css/admin-style.css' );

            wp_enqueue_script('jquery-ui-tabs');

            //deregister other google map scripts if enqueued
            slwp_stores_deregister_other_gmaps();

            wp_enqueue_script( 'slwp-gmap', '//maps.google.com/maps/api/js' . slwp_stores_gmap_api_params('browser_key'), false, '1.0.0', true );
            wp_enqueue_script( 'admin-script', plugin_dir_url( __FILE__ ).'admin/js/admin-script.js', array('jquery'), '1.0.0', true );
            wp_localize_script( 'admin-script', 'slwp_stores', array(
                'slwp_settings' =>   stripslashes_deep( $slwp_store_setting ),
                'ajaxurl' => admin_url( 'admin-ajax.php' )
                )

            );
        }

        /**
        *  Enqueue scripts for front-end
        */
        public function slwp_stores_add_scripts_style() {
            $slwp_store_setting = get_option('slwp_store_options');

            //deregister other google map scripts if enqueued
            slwp_stores_deregister_other_gmaps();

            //deregister other font awesome scripts if enqueued
            slwp_stores_deregister_other_font_awesome();

            wp_enqueue_style( 'slwp-front-style', plugin_dir_url( __FILE__ ).'public/assets/css/aka-front-style.css');

            wp_enqueue_style( 'slwp-load-fa', 'https://maxcdn.bootstrapcdn.com/font-awesome/4.6.3/css/font-awesome.min.css' );

            wp_enqueue_script( 'slwp-gmap', '//maps.google.com/maps/api/js' . slwp_stores_gmap_api_params('browser_key'), false, '1.0.0', true );

            wp_enqueue_script( 'slwp-front-js', plugin_dir_url( __FILE__ ).'public/assets/js/slwp-maps.js', array('jquery'), '1.0.0', true );

            $store_location_marker = apply_filters( 'slwp_store_marker', plugin_dir_url( __FILE__ ).'markers/blue.png');
            $initial_location_marker = apply_filters( 'slwp_initial_marker', plugin_dir_url( __FILE__ ).'markers/red.png');
            wp_localize_script( 'slwp-front-js', 'slwp_stores', array(
                'slwp_settings' =>   stripslashes_deep( $slwp_store_setting ),
                'ajaxurl' => admin_url( 'admin-ajax.php' ),
                'store_location_marker' => $store_location_marker,
                'initial_location_marker' => $initial_location_marker,
                )

            );

        }

        /**
        * Add menu page.
        */
        public function slwp_register_menu_page() {
            add_menu_page( __( 'Store Settings', 'slwp-stores'), 'Store Settings', 'manage_options', 'slwp_stores.php', array($this, 'slwp_add_setting_page' ), '', 20 );
        }

        /**
        * Callback function of add_menu_page. Displays the page's content.
        */
        public function slwp_add_setting_page() {
            require plugin_dir_path( __FILE__ ).'admin/view/store-settings-form.php';

        }

        /**
        * Register settings options and save to wp_options table.
        */
        public function slwp_register_settings() {
            register_setting( 'slwp_store_options', 'slwp_store_options', array( $this, 'slwp_sanitize_settings' ) );

        }

        /**
        * Save admin form settings value to aka_store_option option.
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
                
                // echo "<pre>";
                // print_r($input_options);
                // echo "</pre>";
                // die();

                return $input_options;

            }

        /**
        * Includes certain functions from aka-functions.php.
        */
        public function slwp_includes() {
            require_once( plugin_dir_path( __FILE__ ). 'inc/aka-functions.php' );
        }

        /**
        * Add meta box to selected post types.
        */
        public function slwp_stores_meta_boxes() {
            $slwp_store_setting = get_option('slwp_store_options');
            if ( !empty( $slwp_store_setting['post_type'] ) && isset( $slwp_store_setting['post_type'] ) ) {
                $option_postTypes = $slwp_store_setting['post_type'];

                foreach ($option_postTypes as $type_value) {

                    add_meta_box( 'meta-box-id', __( 'AKA Stores Box', 'slwp-stores' ), array( $this, 'slwp_display_metabox' ), $type_value );
                }
            }

        }

        /**
        * Callback function displaying form elements to add_meta_box.
        */
        public function slwp_display_metabox() {
            require plugin_dir_path( __FILE__ ).'admin/view/metabox-form.php';
        }

        /**
        * Ajax response returns concatinated lat-long
        * @return lat,lng(121.230045,234.000034573)
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
        */
        public function slwp_stores_save_posts( $post_id ) {

            if ( isset( $_POST['slwp_store_meta'] ) && !empty( $_POST['slwp_store_meta'] ) ) {
                $sanitized_post_array = array();

                foreach ($_POST['slwp_store_meta'] as $post_value) {
                    $post_array['aka_name'] = sanitize_text_field( $post_value['slwp_name']);
                    $post_array['aka_location'] = sanitize_text_field( $post_value['slwp_location']);
                    $post_array['aka_location_latn'] = sanitize_text_field( $post_value['slwp_location_latn']);
                    $post_array['aka_url'] = esc_url( $post_value['slwp_url']);
                    $post_array['aka_phone'] = sanitize_text_field( $post_value['slwp_phone']);
                    $post_array['aka_description'] = sanitize_text_field( $post_value['slwp_description']);
                    array_push( $sanitized_post_array, $post_array);
                }

                update_post_meta( $post_id, 'aka_saved_locators', array_values( $sanitized_post_array ) );
            } else {
                update_post_meta( $post_id, 'aka_saved_locators', '' );
            }
        }

        /**
        * Returns shortcode output
        */
        public function slwp_list_stores( $atts ) {
            $slwp_store_setting = get_option('slwp_store_options');

            $values = shortcode_atts( array(
                'id'    => '',
                ), $atts );

            $post_id = get_the_ID();

            if ( isset( $values['id'] ) && !empty( $values['id'] ) ) {
                $post_id = $values['id'];
            }

            $post_type = get_post_type( $post_id );

            if ( in_array( $post_type, $slwp_store_setting['post_type'] ) ){

                $slwp_saved_locators = get_post_meta( $post_id, 'aka_saved_locators', true );

                
                ob_start();
                echo "<pre>";
                print_r($slwp_saved_locators);
                echo "</pre>";
                ?>
                <div class="slwp-store-wrap">
                    <div id="slwp-search-wrap">
                        <form class="slwp-search-form">
                            <div class="slwp-input">
                                <label for="slwp-search-input"><?php _e('Location', 'slwp-stores' ); ?></label>
                                <input id="slwp-search-input" value="" name="slwp-search-input" placeholder="" aria-required="true" autocomplete="off" type="text">
                            </div>
                            <div id="slwp-radius">
                                <label for="slwp-radius-dropdown"><?php _e('Search Radius', 'slwp-stores' ); ?></label>
                                <div class="slwp-dropdown">
                                    <select id="slwp-radius-dropdown" class="" name="slwp-radius">
                                        <?php echo slwp_stores_get_dropdown_list('radius_options'); ?>
                                    </select>
                                </div>
                            </div>
                            <div id="slwp-results">
                                <label for="slwp-results-dropdown"><?php _e('Results', 'slwp-stores'); ?></label>
                                <div class="slwp-dropdown">
                                    <select id="slwp-results-dropdown" class="" name="slwp-results" >
                                        <?php echo slwp_stores_get_dropdown_list('max_results'); ?>
                                    </select>
                                </div>
                            </div>
                            <div class="slwp-search-btn-wrap">
                                <input type="hidden" id="slwp-post-id" name="slwp_post_id" value="<?php echo $post_id; ?>">
                                <input id="slwp-search-btn" value="Search" type="submit">
                            </div>
                        </form>
                    </div>

                    <aside class="slwp-right-wrap">
                        <div class="slwp-map-wrap">
                            <div id="slwp-map" style="height: 418px;"></div>
                            <!-- Map renders in slwp-map id -->
                        </div>
                    </aside>
                    <aside class="slwp-left-wrap">

                        <?php
                        if ( !empty( $slwp_saved_locators ) ) { ?>
                        <ul class="store-ul-lists" id="slwp-store-lists">

                            <?php
                            foreach ( $slwp_saved_locators as $aka_key => $store_value ) {

                                $sn = $aka_key;

                                ?>
                                <li class="store-items" id="store-item-id-<?php echo $aka_key; ?>" data-storeid="<?php echo $aka_key; ?>" data-storename="<?php echo esc_attr( $store_value['aka_name'] ); ?>" data-storeurl="<?php echo esc_url( $store_value['aka_url'] ); ?>" data-latlng="<?php echo esc_attr( $store_value['aka_location_latn'] ); ?>" data-phone="<?php echo esc_attr( $store_value['aka_phone'] ); ?>" data-address="<?php echo esc_attr( $store_value['aka_location'] ); ?>" data-desc="<?php echo $store_value['aka_description']; ?>">
                                    <div class="map-content">
                                        <h3 class="store-title">
                                        <span class="store-key"><?php echo ++$sn; ?></span>
                                            <?php
                                            $store_name = esc_attr( $store_value['aka_name'] );
                                            $store_url = esc_url( $store_value['aka_url'] );
                                            $show_url = intval( $slwp_store_setting['show_url_field'] );
                                            $return_output = slwp_stores_get_link_title( $store_name, $store_url, $show_url );

                                            if ( !empty( $return_output ) ) {

                                                if ( !empty( $return_output['before_wrap'] ) ) {
                                                    echo $return_output['before_wrap'];
                                                }
                                                if ( !empty( $return_output['title'] ) ) {
                                                    echo $return_output['title'];
                                                }
                                                if ( !empty( $return_output['after_wrap'] ) ) {
                                                    echo $return_output['after_wrap'];
                                                }
                                            }
                                            ?>
                                        </h3>
                                        <?php

                                        echo sprintf( __( '%s%s%s', 'slwp-stores' ), '<span class="store-items store-address">', esc_attr( $store_value['aka_location'] ), '</span>' );

                                        if ( $slwp_store_setting['show_phone_field'] ) {

                                            echo sprintf( __( '%s%s%s', 'slwp-stores' ),  '<span class="store-items store-phone">', esc_attr( $store_value['aka_phone'] ), '</span>');

                                        }

                                        if ( $slwp_store_setting['show_description_field'] ) {

                                            echo sprintf( __( '%s%s%s', 'slwp-stores' ), '<p>', esc_attr( $store_value['aka_description'] ), '</p>' );

                                        }

                                        if ( $slwp_store_setting['direction_view_control'] ) {
                                            echo sprintf( __( '%s<a class="slwp-get-direction" href="#" id="%s">%s</a>%s', 'slwp-stores' ), '<span class="store-items get-direction">', 'get-direction-'.$aka_key, 'Get Direction', '</span>');
                                        }

                                        ?>
                                    </div>
                                </li>
                                <?php
                            }
                            ?>

                        </ul>
                        <?php
                    }

                //Render direction routes
                    if ( $slwp_store_setting['direction_view_control'] ) {
                        ?>
                        <div class="slwp-ren-dir" id="slwp-direction-detail" style="display: none;">
                            <ul></ul>
                        </div>
                        <?php
                    }
                    ?>
                </aside>
            </div>
            <?php

            $return_content = ob_get_clean();
            ob_flush();
            return $return_content;
        }
    }


    /**
     * Handle the Ajax search on the frontend.
     * @return json A list of store locations that are located within the selected search radius
     */
    function slwp_store_search() {
        $slwp_store_setting = get_option('slwp_store_options');

        $exploded_start_latlng = explode( ',', $slwp_store_setting['start_latlng'] );
        $post_id = intval( $_POST['post_id'] );

        $search_radius = intval( $_POST['search_radius'] );
        $stores_count = intval( $_POST['stores_count'] );


        $myformlat = ( isset( $_POST['lat'] ) && !empty( $_POST['lat'] ) ) ? sanitize_text_field( $_POST['lat'] ) : sanitize_text_field( $exploded_start_latlng[0] );
        $myformlng = ( isset( $_POST['lng'] ) && !empty( $_POST['lng'] ) ) ? sanitize_text_field( $_POST['lng'] ) : sanitize_text_field( $exploded_start_latlng[1] );

        $radius = ( $slwp_store_setting['distance_unit'] == 'km' ) ? 6371 : 3959;
        $store_data = array();

        $slwp_saved_locators = get_post_meta( $post_id, 'aka_saved_locators', true );
        if ( !empty( $slwp_saved_locators ) ) {
            foreach ($slwp_saved_locators as $store_key => $store_value) {
                if ( $store_key < $stores_count) {

                    $exploded_store_latlng = explode( ',', $store_value['aka_location_latn'] );
                    $store_lat = $exploded_store_latlng[0];
                    $store_lng = $exploded_store_latlng[1];

                    $distance = $radius * acos( cos( deg2rad( $myformlat ) ) * cos( deg2rad( $store_lat ) ) * cos( deg2rad( $store_lng ) - deg2rad( $myformlng ) ) + sin( deg2rad( $myformlat ) ) * sin( deg2rad( $store_lat ) ) );

                    if ( $distance <= $search_radius ) {
                        $store_data[] = $slwp_saved_locators[$store_key];
                    }
                }

            }
            wp_send_json( $store_data );
        }
        die();

    }


        /**
         * Handle the different validation errors for the plugin settings.
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

    $slwp_stores = new Slwp_Stores();
}