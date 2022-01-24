jQuery(document).ready( function() {

    //Add stores elements submitted in each posts
    jQuery('#slwp-newmeta-submit').on( 'click', function(e) {
        e.preventDefault();

        var fields_count = jQuery('[name="slwp_fields_count"]').val(),
        address_location = jQuery('[name="slwp_location"]').val(),
        slwp_name = jQuery('[name="slwp_name"]').val(),
        slwp_location = jQuery('[name="slwp_location"]').val(),
        slwp_url = jQuery('[name="slwp_url"]').val(),
        slwp_phone = jQuery('[name="slwp_phone"]').val(),
        slwp_description = jQuery('[name="slwp_description"]').val();

        jQuery.ajax({
            type: 'POST',
            url: slwp_stores.ajaxurl,
            data: {
                action: 'return_address_latlng',
                location: address_location
            },
            success: function( response ){

                if ( response.length > 0 ) {

                    var html_return = '<tr>';
                    html_return += '<td>';
                    html_return += '<span>'+slwp_name+'</span><div class="slwp-input-wrap"><input class="hidden" type="text" name="slwp_store_meta['+fields_count+'][slwp_name]" value="'+slwp_name+'"></div>';
                    html_return += '</td>';
                    html_return += '<td>';
                    html_return += '<span>'+slwp_location+'</span><div class="slwp-input-wrap"><input class="hidden" type="text" name="slwp_store_meta['+fields_count+'][slwp_location]" value="'+slwp_location+'"><input type="hidden" name="slwp_store_meta['+fields_count+'][slwp_location_latn]" value="'+response+'"></div>';
                    html_return += '</td>';
                    if ( slwp_stores.slwp_settings.show_url_field ) {
                    html_return += '<td>';
                        html_return += '<span>'+slwp_url+'</span><div class="slwp-input-wrap"><input class="hidden" type="text" name="slwp_store_meta['+fields_count+'][slwp_url]" value="'+slwp_url+'"></div>';
                        html_return += '</td>';
                    }
                    if ( slwp_stores.slwp_settings.show_phone_field ) {
                        html_return += '<td>';
                        html_return += '<span>'+slwp_phone+'</span><div class="slwp-input-wrap"><input class="hidden" type="text" name="slwp_store_meta['+fields_count+'][slwp_phone]" value="'+slwp_phone+'"></div>';
                        html_return += '</td>';
                    }
                    if ( slwp_stores.slwp_settings.show_description_field ) {
                        html_return += '<td>';
                        html_return += '<span>'+slwp_description+'</span><div class="slwp-input-wrap"><textarea class="hidden" name="slwp_store_meta['+fields_count+'][slwp_description]">'+slwp_description+'</textarea></div>';
                        html_return += '</td>';
                    }
                    html_return += '<td class="slwp-del-edit">';
                    html_return += '<a href="#" data-list="'+fields_count+'" class="slwp-button-delete"></a></td>';
                    html_return += '</tr>';

                    jQuery('#slwp-newmeta tbody.list-meta-body tr:last').before(html_return);
                    fields_count++;
                    jQuery('[name="slwp_fields_count"]').val(fields_count);
                    jQuery('.slwp-fields').val('');
                }

            }
        });
    });


    //Trigger delete the stores row
    jQuery('a.slwp-button-delete').on( 'click', function(e) {
        e.preventDefault();
        if ( confirm("Are you sure?") ) {
                jQuery(this).closest('tr').remove();
                var fields_count = jQuery('[name="slwp_fields_count"]').val();
           }
    });


    // Show the tooltips.
    jQuery( ".slwp-info" ).on( "mouseover", function() {
        jQuery( this ).find( ".slwp-info-text" ).css( 'display', 'block');
    });

    jQuery( ".slwp-info" ).on( "mouseout", function() {
        jQuery( this ).find( ".slwp-info-text" ).css( 'display', 'none');
    });


    jQuery('a.remove-fields').on( 'click', function(e) {
        e.preventDefault();

        jQuery(this).closest('span').remove();
        var count = jQuery('[name="slwp_store_setting[field_count]"]').val();
        jQuery('[name="slwp_store_setting[field_count]"]').val(--count);

    });


    // If we have a city/country input field enable the autocomplete.
    if ( jQuery( "#map-start-point" ).length > 0 ) {
        slwp_activateAutoComplete("map-start-point");
    }
    if ( jQuery( "#slwp-location" ).length > 0 ) {
        slwp_activateAutoComplete("slwp-location");
    }

    //initialize tab on backend
    jQuery('#tabs-wrap').tabs();
});


/**
 * Activate the autocomplete function for the city/country field.
 */
function slwp_activateAutoComplete(address) {
    var latlng,
        input = document.getElementById( address ),
        options = {},autocomplete;

    if ( 1 == slwp_stores.slwp_settings.autocomplete ) {

            if ( typeof slwp_stores.slwp_settings.region !== "undefined" && slwp_stores.slwp_settings.region.length > 0 ) {
                var regionComponents = {};
                regionComponents.country = slwp_stores.slwp_settings.region.toUpperCase();

                options.componentRestrictions = regionComponents;

            }

            autocomplete = new google.maps.places.Autocomplete( input, options );

        autocomplete.addListener( autocomplete, "place_changed", function() {
            latlng = autocomplete.getPlace().geometry.location;
            slwp_setLatlng( latlng, "zoom" );
        });
    }
}

/**
 * Update the hidden input field with the current latlng values.
 */
 function slwp_setLatlng( latLng, target ) {
    var coordinates = slwp_stripCoordinates( latLng ),
        lat         = slwp_roundCoordinate( coordinates[0] ),
        lng         = slwp_roundCoordinate( coordinates[1] );

    if ( target == "store" ) {
        jQuery( "#slwp-lat" ).val( lat );
        jQuery( "#slwp-lng" ).val( lng );
    } else if ( target == "zoom" ) {
        jQuery( "#slwp-latlng" ).val( lat + ',' + lng );
    }
}


/**
 * Strip the '(' and ')' from the captured coordinates and split them.
 */
function slwp_stripCoordinates( coordinates ) {
    var latLng    = [],
        selected  = coordinates.toString(),
        latLngStr = selected.split( ",", 2 );

    latLng[0] = latLngStr[0].replace( "(", "" );
    latLng[1] = latLngStr[1].replace( ")", "" );

    return latLng;
}

/**
 * Round the coordinate to 6 digits after the comma.
 * @returns {float} roundoff coordinates values
 */
function slwp_roundCoordinate( coordinate ) {
    var roundedCoord, decimals = 6;

    roundedCoord = Math.round( coordinate * Math.pow( 10, decimals ) ) / Math.pow( 10, decimals );

    return roundedCoord;
}