<?php
/**
 * Frontend class
 *
 * @author Alok Shrestha
 * @since  2.0.0
 */

if ( !defined( 'ABSPATH' ) ) exit;

if ( !class_exists( 'SLWP_Frontend' ) ) {

    /**
     * Handle the frontend of the store locator
     *
     * @since 2.0.0
     */
    class SLWP_Frontend {

        /**
         * Class constructor
         */
        public function __construct() {
            add_action( 'wp_enqueue_scripts', array( $this, 'slwp_stores_add_scripts_style' ) );
            add_shortcode( 'slwp-stores', array( $this, 'slwp_list_stores') );
            add_action( 'wp_ajax_slwp_store_search', array( $this, 'slwp_store_search' ) );
            add_action( 'wp_ajax_nopriv_slwp_store_search', array( $this, 'slwp_store_search' ) );
        }

        /**
        * Enqueue scripts for front-end
        * @since 1.0.0
        * @return void
        */
        public function slwp_stores_add_scripts_style() {
            $slwp_store_setting = get_option('slwp_store_options');

            //deregister other google map scripts if enqueued
            slwp_stores_deregister_other_gmaps();

            //deregister other font awesome scripts if enqueued
            slwp_stores_deregister_other_font_awesome();

            wp_enqueue_style( 'slwp-front-style', SLWP_URL .'public/assets/css/slwp-front-style.css');

            wp_enqueue_style( 'slwp-load-fa', 'https://maxcdn.bootstrapcdn.com/font-awesome/4.6.3/css/font-awesome.min.css' );

            wp_enqueue_script( 'slwp-gmap', '//maps.google.com/maps/api/js' . slwp_stores_gmap_api_params('browser_key'), false, '1.0.0', true );

            wp_enqueue_script( 'slwp-front-js', SLWP_URL .'public/assets/js/slwp-maps.js', array('jquery'), '1.0.0', true );

            $store_location_marker = apply_filters( 'slwp_store_marker', SLWP_URL .'markers/blue.png');
            $initial_location_marker = apply_filters( 'slwp_initial_marker', SLWP_URL .'markers/red.png');
            wp_localize_script( 'slwp-front-js', 'slwp_stores', array(
                'slwp_settings' =>   stripslashes_deep( $slwp_store_setting ),
                'ajaxurl' => admin_url( 'admin-ajax.php' ),
                'store_location_marker' => $store_location_marker,
                'initial_location_marker' => $initial_location_marker,
                )

            );
        }

        /**
        * Returns shortcode output
        * @since 1.0
        * @return void
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

            if ( in_array( $post_type, $slwp_store_setting['post_type'] ) ) {

                $slwp_saved_locators = get_post_meta( $post_id, 'slwp_saved_locators', true );

                
                ob_start();
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
                                foreach ( $slwp_saved_locators as $slwp_key => $store_value ) {

                                    $sn = $slwp_key;

                                    ?>
                                    <li class="store-items" id="store-item-id-<?php echo $slwp_key; ?>" data-storeid="<?php echo $slwp_key; ?>" data-storename="<?php echo esc_attr( $store_value['slwp_name'] ); ?>" data-storeurl="<?php echo esc_url( $store_value['slwp_url'] ); ?>" data-latlng="<?php echo esc_attr( $store_value['slwp_location_latn'] ); ?>" data-phone="<?php echo esc_attr( $store_value['slwp_phone'] ); ?>" data-address="<?php echo esc_attr( $store_value['slwp_location'] ); ?>" data-desc="<?php echo $store_value['slwp_description']; ?>">
                                        <div class="map-content">
                                            <h3 class="store-title">
                                            <span class="store-key"><?php echo ++$sn; ?></span>
                                                <?php
                                                $store_name = esc_attr( $store_value['slwp_name'] );
                                                $store_url = esc_url( $store_value['slwp_url'] );
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

                                            echo sprintf( __( '%s%s%s', 'slwp-stores' ), '<span class="store-items store-address">', esc_attr( $store_value['slwp_location'] ), '</span>' );

                                            if ( $slwp_store_setting['show_phone_field'] ) {

                                                echo sprintf( __( '%s%s%s', 'slwp-stores' ),  '<span class="store-items store-phone">', esc_attr( $store_value['slwp_phone'] ), '</span>');

                                            }

                                            if ( $slwp_store_setting['show_description_field'] ) {

                                                echo sprintf( __( '%s%s%s', 'slwp-stores' ), '<p>', esc_attr( $store_value['slwp_description'] ), '</p>' );

                                            }

                                            if ( $slwp_store_setting['direction_view_control'] ) {
                                                echo sprintf( __( '%s<a class="slwp-get-direction" href="#" id="%s">%s</a>%s', 'slwp-stores' ), '<span class="store-items get-direction">', 'get-direction-'.$slwp_key, 'Get Direction', '</span>');
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
         * @since 1.0.0
         * @return json A list of store locations that are located within the selected search radius
         */
        public function slwp_store_search() {
            $slwp_store_setting = get_option('slwp_store_options');

            $exploded_start_latlng = explode( ',', $slwp_store_setting['start_latlng'] );
            $post_id = intval( $_POST['post_id'] );

            $search_radius = intval( $_POST['search_radius'] );
            $stores_count = intval( $_POST['stores_count'] );


            $myformlat = ( isset( $_POST['lat'] ) && !empty( $_POST['lat'] ) ) ? sanitize_text_field( $_POST['lat'] ) : sanitize_text_field( $exploded_start_latlng[0] );
            $myformlng = ( isset( $_POST['lng'] ) && !empty( $_POST['lng'] ) ) ? sanitize_text_field( $_POST['lng'] ) : sanitize_text_field( $exploded_start_latlng[1] );

            $radius = ( $slwp_store_setting['distance_unit'] == 'km' ) ? 6371 : 3959;
            $store_data = array();

            $slwp_saved_locators = get_post_meta( $post_id, 'slwp_saved_locators', true );
            if ( !empty( $slwp_saved_locators ) ) {
                foreach ($slwp_saved_locators as $store_key => $store_value) {
                    if ( $store_key < $stores_count) {

                        $exploded_store_latlng = explode( ',', $store_value['slwp_location_latn'] );
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
    }

}