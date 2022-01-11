jQuery(document).ready( function() {

    //Add stores elements submitted in each posts
    jQuery('#aka-newmeta-submit').on( 'click', function(e) {
        e.preventDefault();

        var fields_count = jQuery('[name="aka_fields_count"]').val(),
        address_location = jQuery('[name="aka_location"]').val(),
        aka_name = jQuery('[name="aka_name"]').val(),
        aka_location = jQuery('[name="aka_location"]').val(),
        aka_url = jQuery('[name="aka_url"]').val(),
        aka_phone = jQuery('[name="aka_phone"]').val(),
        aka_description = jQuery('[name="aka_description"]').val();

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
                    html_return += '<span>'+aka_name+'</span><div class="aka-input-wrap"><input class="hidden" type="text" name="aka_store_meta['+fields_count+'][aka_name]" value="'+aka_name+'"></div>';
                    html_return += '</td>';
                    html_return += '<td>';
                    html_return += '<span>'+aka_location+'</span><div class="aka-input-wrap"><input class="hidden" type="text" name="aka_store_meta['+fields_count+'][aka_location]" value="'+aka_location+'"><input type="hidden" name="aka_store_meta['+fields_count+'][aka_location_latn]" value="'+response+'"></div>';
                    html_return += '</td>';
                    if ( slwp_stores.aka_settings.show_url_field ) {
                    html_return += '<td>';
                        html_return += '<span>'+aka_url+'</span><div class="aka-input-wrap"><input class="hidden" type="text" name="aka_store_meta['+fields_count+'][aka_url]" value="'+aka_url+'"></div>';
                        html_return += '</td>';
                    }
                    if ( slwp_stores.aka_settings.show_phone_field ) {
                        html_return += '<td>';
                        html_return += '<span>'+aka_phone+'</span><div class="aka-input-wrap"><input class="hidden" type="text" name="aka_store_meta['+fields_count+'][aka_phone]" value="'+aka_phone+'"></div>';
                        html_return += '</td>';
                    }
                    if ( slwp_stores.aka_settings.show_description_field ) {
                        html_return += '<td>';
                        html_return += '<span>'+aka_description+'</span><div class="aka-input-wrap"><textarea class="hidden" name="aka_store_meta['+fields_count+'][aka_description]">'+aka_description+'</textarea></div>';
                        html_return += '</td>';
                    }
                    html_return += '<td class="aka-del-edit">';
                    html_return += '<a href="#" data-list="'+fields_count+'" class="aka-button-delete"></a></td>';
                    html_return += '</tr>';

                    jQuery('#aka-newmeta tbody.list-meta-body tr:last').before(html_return);
                    fields_count++;
                    jQuery('[name="aka_fields_count"]').val(fields_count);
                    jQuery('.aka-fields').val('');
                }

            }
        });
    });


    //Trigger delete the stores row
    jQuery('a.aka-button-delete').on( 'click', function(e) {
        e.preventDefault();
        if ( confirm("Are you sure?") ) {
                jQuery(this).closest('tr').remove();
                var fields_count = jQuery('[name="aka_fields_count"]').val();
           }
    });


    // Show the tooltips.
    jQuery( ".aka-info" ).on( "mouseover", function() {
        jQuery( this ).find( ".aka-info-text" ).css( 'display', 'block');
    });

    jQuery( ".aka-info" ).on( "mouseout", function() {
        jQuery( this ).find( ".aka-info-text" ).css( 'display', 'none');
    });


    jQuery('a.remove-fields').on( 'click', function(e) {
        e.preventDefault();

        jQuery(this).closest('span').remove();
        var count = jQuery('[name="aka_store_setting[field_count]"]').val();
        jQuery('[name="aka_store_setting[field_count]"]').val(--count);

    });


    // If we have a city/country input field enable the autocomplete.
    if ( jQuery( "#map-start-point" ).length > 0 ) {
        slwp_activateAutoComplete("map-start-point");
    }
    if ( jQuery( "#aka-location" ).length > 0 ) {
        slwp_activateAutoComplete("aka-location");
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

    if ( 1 == slwp_stores.aka_settings.autocomplete ) {

            if ( typeof slwp_stores.aka_settings.region !== "undefined" && slwp_stores.aka_settings.region.length > 0 ) {
                var regionComponents = {};
                regionComponents.country = slwp_stores.aka_settings.region.toUpperCase();

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
        jQuery( "#aka-lat" ).val( lat );
        jQuery( "#aka-lng" ).val( lng );
    } else if ( target == "zoom" ) {
        jQuery( "#aka-latlng" ).val( lat + ',' + lng );
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