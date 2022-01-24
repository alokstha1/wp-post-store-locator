<?php
/**
* Returns map api url based on languare and regions set.
*/
function slwp_stores_gmap_api_params( $api_key_type, $geocode_params = false ) {
    $slwp_store_setting = get_option('slwp_store_options');
    $api_params = '';
    $param_keys = array( 'language', 'region', 'key' );

    /**
     * The geocode params are included after the address so we need to
     * use a '&' as the first char, but when the maps script is included on
     * the front-end it does need to start with a '?'.
     */
    $first_sep = ( $geocode_params ) ? '&' : '?';

    foreach ( $param_keys as $param_key ) {
        if ( 'key' == $param_key ) {
            $option_key = $api_key_type;
        } else {
            $option_key = $param_key;
        }

        $param_val = $slwp_store_setting[$option_key];

        if ( !empty( $param_val ) ) {
            $api_params .= $param_key . '=' . $param_val . '&';
        }
    }

    if ( $api_params ) {
        $api_params = $first_sep . rtrim( $api_params, '&' );
    }

    if ( 'browser_key' == $api_key_type && $slwp_store_setting['autocomplete']  ) {
        $api_params .= '&libraries=places';
    }

    return apply_filters( 'slwp_gmap_api_params', $api_params );
}

/**
* Return Map zoom level dropdown setting for admin.
*/
function slwp_stores_map_zoom_levels( $saved_zoom_level = '' ) {
    $select_dropdown = '<select id="zoom-level" name="slwp_store_setting[zoom_level]" autocomplete="off">';

    for ( $i = 1; $i < 13; $i++ ) {
        $selected = '';

        if ( isset( $saved_zoom_level ) && !empty( $saved_zoom_level ) ) {
            $selected = ( $saved_zoom_level == $i ) ? 'selected="selected"' : '';
        }

        switch ( $i ) {
            case 1:
            $zoom_desc = ' - ' . __( 'World view', 'slwp-stores' );
            break;
            case 3:
            $zoom_desc = ' - ' . __( 'Default', 'slwp-stores' );
            if ( !isset( $saved_zoom_level ) && empty( $saved_zoom_level ) ) {
                $selected = 'selected="selected"';
            }
            break;
            case 12:
            $zoom_desc = ' - ' . __( 'Roadmap', 'slwp-stores' );
            break;
            default:
            $zoom_desc = '';
        }
        $select_dropdown .= "<option value='$i' $selected>". $i . esc_html( $zoom_desc ) . "</option>";
    }
    $select_dropdown .= "</select>";
    return $select_dropdown;
}

/**
* Return google map type dropdown.
*/
function slwp_stores_map_type_options( $saved_map_type = '' ) {
    $map_types = array(
        'roadmap'   => __( 'Roadmap', 'slwp-stores' ),
        'satellite' => __( 'Satellite', 'slwp-stores' ),
        'hybrid'    => __( 'Hybrid', 'slwp-stores' ),
        'terrain'   => __( 'Terrain', 'slwp-stores' )
        );

    $select_dropdown = '<select name="slwp_store_setting[map_type]" id="map-type">';
    foreach ( $map_types as $key => $map_type_value ) {
        $selected = ( $key == $saved_map_type ) ? 'selected="selected"' : '';
        $select_dropdown .= '<option value="' . esc_attr( $key ) . '" '.$selected.'>' . esc_html( $map_type_value ) . '</option>';
    }
    $select_dropdown .= '</select>';
    return $select_dropdown;
}


/**
* Return language and region setting dropdown for admin setting.
*/
function slwp_stores_api_option_lists( $list, $list_option_value = '' ) {
    switch ( $list ) {
        case 'language':
        $api_option_list = array (
            __('Select your language', 'slwp-stores')    => '',
            __('English', 'slwp-stores')                 => 'en',
            __('Arabic', 'slwp-stores')                  => 'ar',
            __('Basque', 'slwp-stores')                  => 'eu',
            __('Bulgarian', 'slwp-stores')               => 'bg',
            __('Bengali', 'slwp-stores')                 => 'bn',
            __('Catalan', 'slwp-stores')                 => 'ca',
            __('Czech', 'slwp-stores')                   => 'cs',
            __('Danish', 'slwp-stores')                  => 'da',
            __('German', 'slwp-stores')                  => 'de',
            __('Greek', 'slwp-stores')                   => 'el',
            __('English (Australian)', 'slwp-stores')    => 'en-AU',
            __('English (Great Britain)', 'slwp-stores') => 'en-GB',
            __('Spanish', 'slwp-stores')                 => 'es',
            __('Farsi', 'slwp-stores')                   => 'fa',
            __('Finnish', 'slwp-stores')                 => 'fi',
            __('Filipino', 'slwp-stores')                => 'fil',
            __('French', 'slwp-stores')                  => 'fr',
            __('Galician', 'slwp-stores')                => 'gl',
            __('Gujarati', 'slwp-stores')                => 'gu',
            __('Hindi', 'slwp-stores')                   => 'hi',
            __('Croatian', 'slwp-stores')                => 'hr',
            __('Hungarian', 'slwp-stores')               => 'hu',
            __('Indonesian', 'slwp-stores')              => 'id',
            __('Italian', 'slwp-stores')                 => 'it',
            __('Hebrew', 'slwp-stores')                  => 'iw',
            __('Japanese', 'slwp-stores')                => 'ja',
            __('Kannada', 'slwp-stores')                 => 'kn',
            __('Korean', 'slwp-stores')                  => 'ko',
            __('Lithuanian', 'slwp-stores')              => 'lt',
            __('Latvian', 'slwp-stores')                 => 'lv',
            __('Malayalam', 'slwp-stores')               => 'ml',
            __('Marathi', 'slwp-stores')                 => 'mr',
            __('Dutch', 'slwp-stores')                   => 'nl',
            __('Norwegian', 'slwp-stores')               => 'no',
            __('Norwegian Nynorsk', 'slwp-stores')       => 'nn',
            __('Polish', 'slwp-stores')                  => 'pl',
            __('Portuguese', 'slwp-stores')              => 'pt',
            __('Portuguese (Brazil)', 'slwp-stores')     => 'pt-BR',
            __('Portuguese (Portugal)', 'slwp-stores')   => 'pt-PT',
            __('Romanian', 'slwp-stores')                => 'ro',
            __('Russian', 'slwp-stores')                 => 'ru',
            __('Slovak', 'slwp-stores')                  => 'sk',
            __('Slovenian', 'slwp-stores')               => 'sl',
            __('Serbian', 'slwp-stores')                 => 'sr',
            __('Swedish', 'slwp-stores')                 => 'sv',
            __('Tagalog', 'slwp-stores')                 => 'tl',
            __('Tamil', 'slwp-stores')                   => 'ta',
            __('Telugu', 'slwp-stores')                  => 'te',
            __('Thai', 'slwp-stores')                    => 'th',
            __('Turkish', 'slwp-stores')                 => 'tr',
            __('Ukrainian', 'slwp-stores')               => 'uk',
            __('Vietnamese', 'slwp-stores')              => 'vi',
            __('Chinese (Simplified)', 'slwp-stores')    => 'zh-CN',
            __('Chinese (Traditional)' ,'slwp-stores')   => 'zh-TW'
            );
        break;
        case 'region':
        $api_option_list = array (
            __('Select your region', 'slwp-stores')               => '',
            __('Afghanistan', 'slwp-stores')                      => 'af',
            __('Albania', 'slwp-stores')                          => 'al',
            __('Algeria', 'slwp-stores')                          => 'dz',
            __('American Samoa', 'slwp-stores')                   => 'as',
            __('Andorra', 'slwp-stores')                          => 'ad',
            __('Anguilla', 'slwp-stores')                         => 'ai',
            __('Angola', 'slwp-stores')                           => 'ao',
            __('Antigua and Barbuda', 'slwp-stores')              => 'ag',
            __('Argentina', 'slwp-stores')                        => 'ar',
            __('Armenia', 'slwp-stores')                          => 'am',
            __('Aruba', 'slwp-stores')                            => 'aw',
            __('Australia', 'slwp-stores')                        => 'au',
            __('Austria', 'slwp-stores')                          => 'at',
            __('Azerbaijan', 'slwp-stores')                       => 'az',
            __('Bahamas', 'slwp-stores')                          => 'bs',
            __('Bahrain', 'slwp-stores')                          => 'bh',
            __('Bangladesh', 'slwp-stores')                       => 'bd',
            __('Barbados', 'slwp-stores')                         => 'bb',
            __('Belarus', 'slwp-stores')                          => 'by',
            __('Belgium', 'slwp-stores')                          => 'be',
            __('Belize', 'slwp-stores')                           => 'bz',
            __('Benin', 'slwp-stores')                            => 'bj',
            __('Bermuda', 'slwp-stores')                          => 'bm',
            __('Bhutan', 'slwp-stores')                           => 'bt',
            __('Bolivia', 'slwp-stores')                          => 'bo',
            __('Bosnia and Herzegovina', 'slwp-stores')           => 'ba',
            __('Botswana', 'slwp-stores')                         => 'bw',
            __('Brazil', 'slwp-stores')                           => 'br',
            __('British Indian Ocean Territory', 'slwp-stores')   => 'io',
            __('Brunei', 'slwp-stores')                           => 'bn',
            __('Bulgaria', 'slwp-stores')                         => 'bg',
            __('Burkina Faso', 'slwp-stores')                     => 'bf',
            __('Burundi', 'slwp-stores')                          => 'bi',
            __('Cambodia', 'slwp-stores')                         => 'kh',
            __('Cameroon', 'slwp-stores')                         => 'cm',
            __('Canada', 'slwp-stores')                           => 'ca',
            __('Cape Verde', 'slwp-stores')                       => 'cv',
            __('Cayman Islands', 'slwp-stores')                   => 'ky',
            __('Central African Republic', 'slwp-stores')         => 'cf',
            __('Chad', 'slwp-stores')                             => 'td',
            __('Chile', 'slwp-stores')                            => 'cl',
            __('China', 'slwp-stores')                            => 'cn',
            __('Christmas Island', 'slwp-stores')                 => 'cx',
            __('Cocos Islands', 'slwp-stores')                    => 'cc',
            __('Colombia', 'slwp-stores')                         => 'co',
            __('Comoros', 'slwp-stores')                          => 'km',
            __('Congo', 'slwp-stores')                            => 'cg',
            __('Costa Rica', 'slwp-stores')                       => 'cr',
            __('Côte d\'Ivoire', 'slwp-stores')                   => 'ci',
            __('Croatia', 'slwp-stores')                          => 'hr',
            __('Cuba', 'slwp-stores')                             => 'cu',
            __('Czech Republic', 'slwp-stores')                   => 'cz',
            __('Denmark', 'slwp-stores')                          => 'dk',
            __('Djibouti', 'slwp-stores')                         => 'dj',
            __('Democratic Republic of the Congo', 'slwp-stores') => 'cd',
            __('Dominica', 'slwp-stores')                         => 'dm',
            __('Dominican Republic', 'slwp-stores')               => 'do',
            __('Ecuador', 'slwp-stores')                          => 'ec',
            __('Egypt', 'slwp-stores')                            => 'eg',
            __('El Salvador', 'slwp-stores')                      => 'sv',
            __('Equatorial Guinea', 'slwp-stores')                => 'gq',
            __('Eritrea', 'slwp-stores')                          => 'er',
            __('Estonia', 'slwp-stores')                          => 'ee',
            __('Ethiopia', 'slwp-stores')                         => 'et',
            __('Fiji', 'slwp-stores')                             => 'fj',
            __('Finland', 'slwp-stores')                          => 'fi',
            __('France', 'slwp-stores')                           => 'fr',
            __('French Guiana', 'slwp-stores')                    => 'gf',
            __('Gabon', 'slwp-stores')                            => 'ga',
            __('Gambia', 'slwp-stores')                           => 'gm',
            __('Germany', 'slwp-stores')                          => 'de',
            __('Ghana', 'slwp-stores')                            => 'gh',
            __('Greenland', 'slwp-stores')                        => 'gl',
            __('Greece', 'slwp-stores')                           => 'gr',
            __('Grenada', 'slwp-stores')                          => 'gd',
            __('Guam', 'slwp-stores')                             => 'gu',
            __('Guadeloupe', 'slwp-stores')                       => 'gp',
            __('Guatemala', 'slwp-stores')                        => 'gt',
            __('Guinea', 'slwp-stores')                           => 'gn',
            __('Guinea-Bissau', 'slwp-stores')                    => 'gw',
            __('Haiti', 'slwp-stores')                            => 'ht',
            __('Honduras', 'slwp-stores')                         => 'hn',
            __('Hong Kong', 'slwp-stores')                        => 'hk',
            __('Hungary', 'slwp-stores')                          => 'hu',
            __('Iceland', 'slwp-stores')                          => 'is',
            __('India', 'slwp-stores')                            => 'in',
            __('Indonesia', 'slwp-stores')                        => 'id',
            __('Iran', 'slwp-stores')                             => 'ir',
            __('Iraq', 'slwp-stores')                             => 'iq',
            __('Ireland', 'slwp-stores')                          => 'ie',
            __('Israel', 'slwp-stores')                           => 'il',
            __('Italy', 'slwp-stores')                            => 'it',
            __('Jamaica', 'slwp-stores')                          => 'jm',
            __('Japan', 'slwp-stores')                            => 'jp',
            __('Jordan', 'slwp-stores')                           => 'jo',
            __('Kazakhstan', 'slwp-stores')                       => 'kz',
            __('Kenya', 'slwp-stores')                            => 'ke',
            __('Kuwait', 'slwp-stores')                           => 'kw',
            __('Kyrgyzstan', 'slwp-stores')                       => 'kg',
            __('Laos', 'slwp-stores')                             => 'la',
            __('Latvia', 'slwp-stores')                           => 'lv',
            __('Lebanon', 'slwp-stores')                          => 'lb',
            __('Lesotho', 'slwp-stores')                          => 'ls',
            __('Liberia', 'slwp-stores')                          => 'lr',
            __('Libya', 'slwp-stores')                            => 'ly',
            __('Liechtenstein', 'slwp-stores')                    => 'li',
            __('Lithuania', 'slwp-stores')                        => 'lt',
            __('Luxembourg', 'slwp-stores')                       => 'lu',
            __('Macau', 'slwp-stores')                            => 'mo',
            __('Macedonia', 'slwp-stores')                        => 'mk',
            __('Madagascar', 'slwp-stores')                       => 'mg',
            __('Malawi', 'slwp-stores')                           => 'mw',
            __('Malaysia ', 'slwp-stores')                        => 'my',
            __('Mali', 'slwp-stores')                             => 'ml',
            __('Marshall Islands', 'slwp-stores')                 => 'mh',
            __('Martinique', 'slwp-stores')                       => 'il',
            __('Mauritania', 'slwp-stores')                       => 'mr',
            __('Mauritius', 'slwp-stores')                        => 'mu',
            __('Mexico', 'slwp-stores')                           => 'mx',
            __('Micronesia', 'slwp-stores')                       => 'fm',
            __('Moldova', 'slwp-stores')                          => 'md',
            __('Monaco' ,'slwp-stores')                           => 'mc',
            __('Mongolia', 'slwp-stores')                         => 'mn',
            __('Montenegro', 'slwp-stores')                       => 'me',
            __('Montserrat', 'slwp-stores')                       => 'ms',
            __('Morocco', 'slwp-stores')                          => 'ma',
            __('Mozambique', 'slwp-stores')                       => 'mz',
            __('Myanmar', 'slwp-stores')                          => 'mm',
            __('Namibia', 'slwp-stores')                          => 'na',
            __('Nauru', 'slwp-stores')                            => 'nr',
            __('Nepal', 'slwp-stores')                            => 'np',
            __('Netherlands', 'slwp-stores')                      => 'nl',
            __('Netherlands Antilles', 'slwp-stores')             => 'an',
            __('New Zealand', 'slwp-stores')                      => 'nz',
            __('Nicaragua', 'slwp-stores')                        => 'ni',
            __('Niger', 'slwp-stores')                            => 'ne',
            __('Nigeria', 'slwp-stores')                          => 'ng',
            __('Niue', 'slwp-stores')                             => 'nu',
            __('Northern Mariana Islands', 'slwp-stores')         => 'mp',
            __('Norway', 'slwp-stores')                           => 'no',
            __('Oman', 'slwp-stores')                             => 'om',
            __('Pakistan', 'slwp-stores')                         => 'pk',
            __('Panama' ,'slwp-stores')                           => 'pa',
            __('Papua New Guinea', 'slwp-stores')                 => 'pg',
            __('Paraguay' ,'slwp-stores')                         => 'py',
            __('Peru', 'slwp-stores')                             => 'pe',
            __('Philippines', 'slwp-stores')                      => 'ph',
            __('Pitcairn Islands', 'slwp-stores')                 => 'pn',
            __('Poland', 'slwp-stores')                           => 'pl',
            __('Portugal', 'slwp-stores')                         => 'pt',
            __('Qatar', 'slwp-stores')                            => 'qa',
            __('Reunion', 'slwp-stores')                          => 're',
            __('Romania', 'slwp-stores')                          => 'ro',
            __('Russia', 'slwp-stores')                           => 'ru',
            __('Rwanda', 'slwp-stores')                           => 'rw',
            __('Saint Helena', 'slwp-stores')                     => 'sh',
            __('Saint Kitts and Nevis', 'slwp-stores')            => 'kn',
            __('Saint Vincent and the Grenadines', 'slwp-stores') => 'vc',
            __('Saint Lucia', 'slwp-stores')                      => 'lc',
            __('Samoa', 'slwp-stores')                            => 'ws',
            __('San Marino', 'slwp-stores')                       => 'sm',
            __('São Tomé and Príncipe', 'slwp-stores')            => 'st',
            __('Saudi Arabia', 'slwp-stores')                     => 'sa',
            __('Senegal', 'slwp-stores')                          => 'sn',
            __('Serbia', 'slwp-stores')                           => 'rs',
            __('Seychelles', 'slwp-stores')                       => 'sc',
            __('Sierra Leone', 'slwp-stores')                     => 'sl',
            __('Singapore', 'slwp-stores')                        => 'sg',
            __('Slovakia', 'slwp-stores')                         => 'si',
            __('Solomon Islands', 'slwp-stores')                  => 'sb',
            __('Somalia', 'slwp-stores')                          => 'so',
            __('South Africa', 'slwp-stores')                     => 'za',
            __('South Korea', 'slwp-stores')                      => 'kr',
            __('Spain', 'slwp-stores')                            => 'es',
            __('Sri Lanka', 'slwp-stores')                        => 'lk',
            __('Sudan', 'slwp-stores')                            => 'sd',
            __('Swaziland', 'slwp-stores')                        => 'sz',
            __('Sweden', 'slwp-stores')                           => 'se',
            __('Switzerland', 'slwp-stores')                      => 'ch',
            __('Syria', 'slwp-stores')                            => 'sy',
            __('Taiwan', 'slwp-stores')                           => 'tw',
            __('Tajikistan', 'slwp-stores')                       => 'tj',
            __('Tanzania', 'slwp-stores')                         => 'tz',
            __('Thailand', 'slwp-stores')                         => 'th',
            __('Timor-Leste', 'slwp-stores')                      => 'tl',
            __('Tokelau' ,'slwp-stores')                          => 'tk',
            __('Togo', 'slwp-stores')                             => 'tg',
            __('Tonga', 'slwp-stores')                            => 'to',
            __('Trinidad and Tobago', 'slwp-stores')              => 'tt',
            __('Tunisia', 'slwp-stores')                          => 'tn',
            __('Turkey', 'slwp-stores')                           => 'tr',
            __('Turkmenistan', 'slwp-stores')                     => 'tm',
            __('Tuvalu', 'slwp-stores')                           => 'tv',
            __('Uganda', 'slwp-stores')                           => 'ug',
            __('Ukraine', 'slwp-stores')                          => 'ua',
            __('United Arab Emirates', 'slwp-stores')             => 'ae',
            __('United Kingdom', 'slwp-stores')                   => 'gb',
            __('United States', 'slwp-stores')                    => 'us',
            __('Uruguay', 'slwp-stores')                          => 'uy',
            __('Uzbekistan', 'slwp-stores')                       => 'uz',
            __('Wallis Futuna', 'slwp-stores')                    => 'wf',
            __('Venezuela', 'slwp-stores')                        => 've',
            __('Vietnam', 'slwp-stores')                          => 'vn',
            __('Yemen', 'slwp-stores')                            => 'ye',
            __('Zambia' ,'slwp-stores')                           => 'zm',
            __('Zimbabwe', 'slwp-stores')                         => 'zw'
            );
}

if ( !empty( $api_option_list ) && is_array( $api_option_list ) ) {
    $option_lists = '';

    foreach ($api_option_list as $option_api_key => $api_list_value) {
        $selected = ( $list_option_value == $api_list_value ) ? 'selected="selected"' : '';
        $option_lists .= '<option value="'.esc_attr( $api_list_value ).'" '.$selected.'>'.esc_html( $option_api_key ).'</option>';
    }
}
return $option_lists;
}


/**
* Return default admin form setting.
*/
function slwp_stores_default_settings() {
    $default_setting = array(
        'server_key'                    => '',
        'browser_key'                   => '',
        'language'                      => 'en',
        'region'                        => '',
        'start_point'                   => '',
        'start_latlng'                  => '',
        'zoom_level'                    => 3,
        'max_zoom_level'                => 15,
        'direction_view_control'        => 0,
        'map_type_control'              => 0,
        'scrollwheel_zoom'              => 1,
        'map_type'                      => 'roadmap',
        'autocomplete'                  => 1,
        'distance_unit'                 => 'km',
        'max_results'                   => '[25],50,75,100',
        'radius_options'                => '10,25,[50],100,200,500',
        'post_type'                     => array(),
        'show_url_field'                => 0,
        'show_phone_field'              => 0,
        'show_description_field'        => 0,
        );

    $settings = get_option('slwp_store_options');

    if ( empty( $settings ) ) {
        update_option( 'slwp_store_options', $default_setting );
    }

    return $default_setting;
}


/**
* Deregister other Google Map
*/
function slwp_stores_deregister_other_gmaps() {
    global $wp_scripts;
    if ( !empty( $wp_scripts->registered ) ) {
        foreach ( $wp_scripts->registered as $index => $script ) {
            if ( ( strpos( $script->src, 'font-awesome.min.css' ) !== false ) || ( strpos( $script->src, 'font-awesome.css' ) !== false ) && ( $script->handle !== 'slwp-load-fa' ) ) {
                wp_deregister_script( $script->handle );
            }
        }
    }
}

/**
* Deregister other Font Awesome
*/
function slwp_stores_deregister_other_font_awesome() {
    global $wp_scripts;
    if ( !empty( $wp_scripts->registered ) ) {
        foreach ( $wp_scripts->registered as $index => $script ) {
            if ( ( strpos( $script->src, 'maps.google.com' ) !== false ) || ( strpos( $script->src, 'maps.googleapis.com' ) !== false ) && ( $script->handle !== 'slwp-gmap' ) ) {
                wp_deregister_script( $script->handle );
            }
        }
    }
}

/**
* Return default saved value
*/
function slwp_stores_get_default_setting( $setting ) {
    global $aka_store_default_setting;
    return $aka_store_default_setting[$setting];
}

/**
 * Get the latlng for the provided address.
 */
function slwp_stores_get_address_latlng( $address ) {
    $latlng   = '';
    $response = slwp_stores_call_geocode_api( $address );
    if ( !is_wp_error( $response ) ) {
        $response = json_decode( $response['body'], true );
        if ( $response['status'] == 'OK' ) {
            $latlng = $response['results'][0]['geometry']['location']['lat'] . ',' . $response['results'][0]['geometry']['location']['lng'];
        }
    }
    return $latlng;
}


/**
 * @param string $address  The address to geocode.
 * @return array $response Either a WP_Error or the response from the Geocode API.
 */
function slwp_stores_call_geocode_api( $address ) {
    // $url      = 'https://maps.googleapis.com/maps/api/geocode/json?address=' . urlencode( $address ) . slwp_stores_gmap_api_params( 'server_key', true );
    $url      = 'https://maps.googleapis.com/maps/api/geocode/json?address=' . urlencode( $address ) . slwp_stores_gmap_api_params( 'browser_key', true );
    $response = wp_remote_get( $url );
    return $response;
}

/**
* Max auto zoom level
* @param string $max_value from database
*/
function slwp_stores_max_map_zoom_levels( $max_value ) {
    $max_zoom_levels = array();
    $zoom_level = array(
        'min' => 10,
        'max' => 21
        );

    $i = $zoom_level['min'];

    while ( $i <= $zoom_level['max'] ) {
        $max_zoom_levels[$i] = $i;
        $i++;
    }

    $dropdown = '<select id="max-zoom-level" name="slwp_store_setting[max_zoom_level]" autocomplete="off">';

    foreach ( $max_zoom_levels as $key => $value ) {
        $selected = ( $max_value == $value ) ? 'selected="selected"' : '';
        $dropdown .= "<option value='" . esc_attr( $value ) . "' $selected>" . esc_html( $value ) . "</option>";
    }
    $dropdown .= '</select>';
    return $dropdown;
}


/**
 * Create a dropdown list holding the search radius or
 * max search results options.
 * @param $list_type either Search Results or Maximum no of result values
 */
function slwp_stores_get_dropdown_list( $list_type ) {

    $slwp_store_setting = get_option('slwp_store_options');
    $dropdown_list = '';
    $settings      = explode( ',', $slwp_store_setting[$list_type] );

    // Only show the distance unit if we are dealing with the search radius.
    if ( 'radius_options' == $list_type  ) {
        $distance_unit = ' '. esc_attr( $slwp_store_setting['distance_unit'] );
    } else {
        $distance_unit = '';
    }

    foreach ( $settings as $index => $setting_value ) {

        // The default radius has a [] wrapped around it, so we check for that and filter out the [].
        if ( strpos( $setting_value, '[' ) !== false ) {
            $setting_value = filter_var( $setting_value, FILTER_SANITIZE_NUMBER_INT );
            $selected = 'selected="selected" ';
        } else {
            $selected = '';
        }

        $dropdown_list .= '<option ' . $selected . 'value="'. absint( $setting_value ) .'">'. absint( $setting_value ) . $distance_unit .'</option>';
    }

    return $dropdown_list;
}


/**
* @param $title String Title of locator
* @param $url String Url Added to the locator
* @param $show_url boolean
*/
function slwp_stores_get_link_title( $title, $url, $show_url ) {
    $return_output = array();
    $return_output['before_wrap'] = '';
    $return_output['after_wrap'] = '';

    if ( !empty( $url ) && $show_url ) {
        $return_output['before_wrap'] = '<a href="'.$url.'" class="title-link" target="_blank">';
        $return_output['title'] = $title;
        $return_output['after_wrap'] = '</a>';
    } else {
        $return_output['before_wrap'] = '';
        $return_output['title'] = $title;
        $return_output['after_wrap'] = '';
    }
    return $return_output;
}
