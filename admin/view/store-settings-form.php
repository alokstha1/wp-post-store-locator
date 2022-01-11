<?php
if ( !defined( 'ABSPATH' ) ) exit;

$slwp_store_setting = get_option('slwp_store_options');

?>

<div class="wptg-backend-wrap">
    <div class="header-wrap">
        <div class="row">
            <div class="logo-wrap col-md-4">
                <div class="title">
                    <h1><?php _e( 'Store Locator Settings', 'slwp-stores' ); ?></h1>
                </div>
            </div>

            <div class="addbanner-wrap col-md-4">
                <img src="<?php echo plugin_dir_url( __FILE__ ).'assets/images/addbanner.jpg'; ?>" alt="addbanner" class="addbanner">
            </div>

            <div class="btn-wrap col-md-4">
                <a href="#" class="wptg-btn dashicons-before dashicons-heart">support</a>
                <a href="#" class="wptg-btn btn2 dashicons-before dashicons-star-filled">Rate us</a>
            </div>
        </div>
    </div>

    <div class="body-wrap">
        <div id="tabs-wrap" class="tabs-wrap">

            <ul class="tab-menu">
                <li class="nav-tab"><a href="#general" class="dashicons-before dashicons-editor-alignleft"><?php _e( 'General', 'slwp-stores' ); ?></a></li>
                <li class="nav-tab"><a href="#map-api" class="dashicons-before dashicons-location"><?php _e( 'Map API', 'slwp-stores' ); ?></a></li>
                <li class="nav-tab"><a href="#options" class="dashicons-before dashicons-screenoptions"><?php _e( 'Options', 'slwp-stores' ); ?></a></li>
                <li class="nav-tab"><a href="#roles" class="dashicons-before dashicons-admin-tools"><?php _e( 'Roles', 'slwp-stores' ); ?></a></li>
            </ul>

            <form method="POST" action="options.php" id="slwp-store-setting-form">

	            <div class="tab-content">

	            	<p class="submit">
	            	    <?php submit_button( __( 'Save Changes', 'slwp-stores' ), 'primary', 'submit_store', false ); ?>
	            	</p>

	            	<div id="general">

	            		<h2 class="tab-title"><?php _e( 'General Map Setting', 'slwp-stores' ); ?></h2>

	            		<div class="form-group">
	            		    <label for="map-start-point">
	            		    	<?php _e( 'Start Point:', 'slwp-stores' ); ?>
	            		    	
	            		    	<span class="slwp-info">
	            		    	    <span class="slwp-info-text slwp-hide">
	            		    	        <?php _e( 'Required Field', 'slwp-stores' ); ?>
	            		    	    </span>
	            		    	</span>		
	            		    </label>

	            		    <div class="form-control-wrap">
	            		        <input type="text" name="slwp_store_setting[start_point]" id="map-start-point" value="<?php echo ( !empty( $slwp_store_setting['start_point']) ) ? esc_attr( $slwp_store_setting['start_point'] ) : ''; ?>" />
	            		        <input value="<?php echo ( !empty( $slwp_store_setting['start_latlng']) ) ? esc_attr( $slwp_store_setting['start_latlng'] ) : ''; ?>" name="slwp_store_setting[start_latlng]" id="slwp-latlng" type="hidden">
	            		    </div>
	            		</div>

	            		<div class="form-group">
	            		    <label for="zoom-level">
	            		    	<?php _e( 'Initial zoom level:', 'slwp-stores' ); ?>	
	            		    </label>

	            		    <div class="form-control-wrap">
	            		        <?php echo slwp_stores_map_zoom_levels( intval( $slwp_store_setting['zoom_level'] ) ); ?>
	            		    </div>
	            		</div>

	            		<div class="form-group">
	            		    <label for="max-zoom-level">
	            		    	<?php _e( 'Max auto zoom level:', 'slwp-stores' ); ?>
	            		    </label>

	            		    <div class="form-control-wrap">
	            		        <?php echo slwp_stores_max_map_zoom_levels( intval( $slwp_store_setting['max_zoom_level'] ) ); ?>
	            		    </div>
	            		</div>

	            		<div class="form-group">
	            		    <label for="direction-view-control">
                                    <?php _e( 'Get direction view control?', 'slwp-stores' ) ; ?>
	            		    </label>

	            		    <div class="form-control-wrap">
	            		        <input type="checkbox" name="slwp_store_setting[direction_view_control]" id="direction-view-control" value="" <?php checked( $slwp_store_setting['direction_view_control'], true ); ?> />
	            		    </div>
	            		</div>

	            		<div class="form-group">
	            		    <label for="map-type-control">
                                    <?php _e( 'Show the map type control?', 'slwp-stores' ) ; ?>
	            		    </label>

	            		    <div class="form-control-wrap">
	            		        <input type="checkbox" name="slwp_store_setting[map_type_control]" id="map-type-control" value="" <?php checked( $slwp_store_setting['map_type_control'], true ); ?> />
	            		    </div>
	            		</div>

	            		<div class="form-group">
	            		    <label for="scrollwheel-zoom">
                                    <?php _e( 'Enable scroll wheel zooming?', 'slwp-stores' ) ; ?>
	            		    </label>

	            		    <div class="form-control-wrap">
	            		        <input type="checkbox" name="slwp_store_setting[scrollwheel_zoom]" id="scrollwheel-zoom" value="" <?php checked( $slwp_store_setting['scrollwheel_zoom'], true ); ?> />
	            		    </div>
	            		</div>

	            		<div class="form-group">
	            		    <label for="map-type">
                                    <?php _e( 'Map Type', 'slwp-stores' ); ?>
	            		    </label>

	            		    <div class="form-control-wrap">
	            		        <?php echo slwp_stores_map_type_options( esc_attr( $slwp_store_setting['map_type'] ) ); ?>
	            		    </div>
	            		</div>

	            	</div>

	            	<div id="map-api">
	            	    
	            	    <h2 class="tab-title"><?php _e( 'Google Map Api', 'slwp-stores' ); ?></h2>

	            	    <div class="form-group">
	            	        <label for="slwp-server-key"><?php _e('Server Key:', 'slwp-stores'); ?></label>
	            	        <div class="form-control-wrap">
	            	            <input type="text" name="slwp_store_setting[server_key]" id="slwp-server-key" value="<?php echo ( !empty( $slwp_store_setting['server_key']) ) ? esc_attr( $slwp_store_setting['server_key'] ) : ''; ?>" />
	            	            <span class="slwp-info">
	            	                <span class="slwp-info-text slwp-hide"><?php echo sprintf( __( 'Get your server key %shere%s.', 'slwp-stores' ), '<a href="https://developers.google.com/maps/documentation/geocoding/get-api-key#get-an-api-key" target="_blank">', '</a>' ); ?>
	            	                </span>
	            	            </span>
	            	        </div>
	            	    </div>

	            	    <div class="form-group">
	            	        <label for="slwp-browser-key"><?php _e( 'Browser Key:', 'slwp-stores' ); ?></label>
	            	        <div class="form-control-wrap">
	            	            <input type="text" name="slwp_store_setting[browser_key]" id="slwp-browser-key" value="<?php echo ( !empty( $slwp_store_setting['browser_key']) ) ? esc_attr( $slwp_store_setting['browser_key'] ) : ''; ?>" />
	            	            <span class="slwp-info">
                                    <span class="slwp-info-text slwp-hide"><?php echo sprintf( __( 'Get your browser key %shere%s', 'slwp-stores' ), '<a href="https://developers.google.com/maps/documentation/javascript/get-api-key#get-an-api-key" target="_blank">', '</a>' ); ?>
                                    </span>
                                </span>
	            	        </div>
	            	    </div>

	            	    <div class="form-group">
	            	        <label for="slwp-api-language"><?php _e( 'Map Language:', 'slwp-stores' ); ?></label>
	            	        <div class="form-control-wrap">
	            	            <select id="slwp-api-language" name="slwp_store_setting[language]">
	            	                <?php echo slwp_stores_api_option_lists('language', esc_attr( $slwp_store_setting['language'] ) ); ?>
	            	            </select>
	            	        </div>
	            	    </div>

	            	    <div class="form-group">
	            	        <label for="slwp-api-region"><?php _e( 'Map Region:', 'slwp-stores' ); ?></label>
	            	        <div class="form-control-wrap">
	            	            <select id="slwp-api-region" name="slwp_store_setting[region]">
                                    <?php echo slwp_stores_api_option_lists('region', esc_attr( $slwp_store_setting['region'] ) ); ?>
                                </select>
	            	        </div>
	            	    </div>

	            	</div>

	            	<div id="options">

	            		<h2 class="tab-title"><?php _e( 'Map Options', 'slwp-stores' ); ?></h2>

	            	    <div class="form-group">
	            	        <label for="enable-autocomplete">
	                            <?php _e( 'Enable autocomplete?', 'slwp-stores' ); ?>
	                        </label>
	            	        <div class="form-control-wrap">
	            	            <input type="checkbox" name="slwp_store_setting[autocomplete]" id="enable-autocomplete" value="" <?php checked( $slwp_store_setting['autocomplete'], true ); ?> />
	            	        </div>
	            	    </div>

	            	    <div class="form-group">
	            	        <label for="distance-unit">
                                <?php _e( 'Distance Unit:', 'slwp-stores' ); ?>
	                        </label>
	            	        <div class="form-control-wrap">
	            	            <input type="radio" name="slwp_store_setting[distance_unit]" id="unit-km" value="km" <?php checked( 'km', $slwp_store_setting['distance_unit'] ); ?> />
                                <label for="unit-km">Km</label>

                                <input type="radio" name="slwp_store_setting[distance_unit]" id="unit-mi" value="mi" <?php checked( 'mi', $slwp_store_setting['distance_unit'] ); ?> />
                                <label for="unit-mi">Mi</label>
	            	        </div>
	            	    </div>

        	    	    <div class="form-group">
        	    	        <label for="max-search-results">
                                <?php _e( 'Max search results:', 'slwp-stores' ); ?>
                                <span class="slwp-info">
                                    <span class="slwp-info-text slwp-hide">
                                        <?php _e( 'The default value is set between the [].', 'slwp-stores'); ?>
                                    </span>
                                </span>
        	                </label>
        	    	        <div class="form-control-wrap">
        	    	            <input type="text" name="slwp_store_setting[max_results]" id="max-search-results" value="<?php echo ( !empty( $slwp_store_setting['max_results'] ) ) ? esc_attr( $slwp_store_setting['max_results'] ) : ''; ?>">
        	    	        </div>
        	    	    </div>

        	    	    <div class="form-group">
        	    	        <label for="search-radius-options">
                                <?php _e( 'Search radius options:', 'slwp-stores' ); ?>
                                <span class="slwp-info">
                                    <span class="slwp-info-text slwp-hide">
                                        <?php _e( 'The default value is set between the [].', 'slwp-stores'); ?>
                                    </span>
                                </span>
        	                </label>
        	    	        <div class="form-control-wrap">
        	    	            <input type="text" name="slwp_store_setting[radius_options]" id="search-radius-options" value="<?php echo ( !empty( $slwp_store_setting['radius_options'] ) ) ? esc_attr( $slwp_store_setting['radius_options'] ) : ''; ?>">
        	    	        </div>
        	    	    </div>
	            		
	            	</div>

	            	<div id="roles">

	            		<h2 class="tab-title"><?php _e('Role Manager', 'slwp-stores' ); ?></h2>

	            		<div class="form-group">
	            		    <label for="post-type-select">
                               <?php _e(' Select Post Type', 'slwp-stores' ); ?>
                            </label>
	            		    <div class="form-control-wrap">
	            		        
	            		        <?php
	            		        $post_types = get_post_types(array(
	            		            'public'    => true,
	            		            'show_ui' => true,
	            		            'show_in_menu' => true,
	            		            ), 'objects');

	            		        foreach ($post_types as $post_type) {
	            		            if ( 'attachment' == $post_type->name )
	            		                continue;
	            		            ?>
	            		            <div class="post-types-lists">
		            		            <input type="checkbox" name="slwp_store_setting[post_type][]" value="<?php echo $post_type->name; ?>" id="select-<?php echo $post_type->name; ?>" <?php if (isset($slwp_store_setting['post_type']) && is_array($slwp_store_setting['post_type'])) {
		            		                if (in_array($post_type->name, $slwp_store_setting['post_type'])) {
		            		                    echo 'checked="checked"';
		            		                }
		            		            }
		            		            ?>>
		            		            <label for="select-<?php echo $post_type->name; ?>">
		            		                <?php echo esc_attr( $post_type->label ); ?>
		            		            </label>
	            		            </div>

	            		            <?php
	            		        }
	            		        ?>

	            		    </div>
	            		</div>

			    	    <div class="form-group">
			    	        <label for="show-description-field">
		                        <?php _e( 'Show description field?', 'slwp-stores' ); ?>
			                </label>
			    	        <div class="form-control-wrap">
			    	            <input type="checkbox" name="slwp_store_setting[show_description_field]" id="show-description-field" value="1" <?php checked( $slwp_store_setting['show_description_field'], true ); ?> />
			    	        </div>
			    	    </div>

			    	    <div class="form-group">
			    	        <label for="show-phone-field">
		                        <?php _e( 'Show phone field?', 'slwp-stores' ); ?>
			                </label>
			    	        <div class="form-control-wrap">
			    	            <input type="checkbox" name="slwp_store_setting[show_phone_field]" id="show-phone-field" value="1" <?php checked( $slwp_store_setting['show_phone_field'], true ); ?> />
			    	        </div>
			    	    </div>

			    	    <div class="form-group">
			    	        <label for="show-url-field">
		                        <?php _e( 'Show url field?', 'slwp-stores' ); ?>
			                </label>
			    	        <div class="form-control-wrap">
			    	            <input type="checkbox" name="slwp_store_setting[show_url_field]" id="show-url-field" value="1" <?php checked( $slwp_store_setting['show_url_field'], true ); ?> />
			    	        </div>
			    	    </div>

	            	</div>

		            <?php settings_fields( 'slwp_store_options' ); ?>

		            <p class="submit">
		                <?php submit_button( __( 'Save Changes', 'slwp-stores' ), 'primary', 'submit_store', false ); ?>
		            </p>

	            </div>

	        </form>

        </div>
    </div>

    <div class="footer-wrap">
        <div class="row">
            <div class="creator col-md-3">
                <span>Proudly Created by</span>
                <a href="codepixelzmedia.com">WPTG</a>
            </div>

            <div class="col-md-6">
                <ul class="footer-nav">
                    <li><a href="#">Free Plugins</a></li>
                    <li><a href="#">Membership</a></li>
                    <li><a href="#">Support</a></li>
                    <li><a href="#">Docs</a></li>
                    <li><a href="#">Terms of Service</a></li>
                    <li><a href="#">Privacy Policy</a></li>
                </ul>
            </div>

            <div class="copyright col-md-3">
                <span>All rights reserved</span>
                &copy; <?php echo date("Y"); ?>
            </div>
        </div>
    </div>

</div>
<div class="clear"></div>