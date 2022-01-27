jQuery(document).ready( function() {

    //not needed: var resetMap, draggable, 
    var map, geocoder, displayDirection, serviceDirections, startMarkerData, infoWindow, autoCompleteLatLng,
    openInfoWindow = [],
    markersArray = [],
    markerSettings = {},
    directionMarkerPosition = {};

    if ( jQuery('#slwp-map').length ) {

        slwp_initMap();
    }

    //Return initialized map
    function slwp_initMap() {

        var mapOptions, infoWindow, latLng, bounds, startLatLng,
        maxZoom = Number( slwp_stores.slwp_settings.max_zoom_level );

        //Create an infowindow
        infoWindow = new google.maps.InfoWindow();
        startLatLng = slwp_getStartLatlng();

        geocoder          = new google.maps.Geocoder();
        displayDirection = new google.maps.DirectionsRenderer();
        serviceDirections = new google.maps.DirectionsService();

        // Set map options.
        mapOptions = {
            zoom: Number( slwp_stores.slwp_settings.zoom_level ),
            center: startLatLng,
            mapTypeId: google.maps.MapTypeId[ slwp_stores.slwp_settings.map_type.toUpperCase() ],
            mapTypeControl: Number( slwp_stores.slwp_settings.map_type_control ) ? true : false,
            scrollwheel: Number( slwp_stores.slwp_settings.scrollwheel_zoom ) ? true : false,
        };

        map = new google.maps.Map(document.getElementById('slwp-map'), mapOptions );

        // Only run this part if the store locator exist and we don't just have a basic map.
        if ( jQuery( "#slwp-map" ).length ) {

            if ( slwp_stores.slwp_settings.autocomplete == 1 ) {
                slwp_initAutocomplete();
            }
        }

        bounds        = new google.maps.LatLngBounds();
        slwp_addMarker( startLatLng, null, infoWindow );
        bounds.extend( startLatLng );


        //loop over the store items and add map marker
        if ( jQuery('ul.store-ul-lists li').length > 0 ) {

            jQuery('ul.store-ul-lists li').each( function(index){

                var item_latlng = jQuery(this).data('latlng').split( "," );
                latLng = new google.maps.LatLng( item_latlng[0], item_latlng[1] );
                slwp_addMarker( latLng, index, infoWindow );
                bounds.extend( latLng );
            });


            // Make sure we don't zoom to far.
            google.maps.event.addListenerOnce( map, "bounds_changed", ( function( currentMap ) {
                return function() {
                    if ( currentMap.getZoom() > maxZoom ) {
                        currentMap.setZoom( maxZoom );
                    }
                };
            }( map ) ) );
            // Make all the markers fit on the map.
            slwp_fitBounds();
        }

        //initialize form store search button
        slwp_initialize_store_search( infoWindow );

        //initialize render direction event
        slwp_render_direction();

    }


    function slwp_initAutocomplete() {

        var input, autocomplete, place,
        options = {};

        if ( 1 == slwp_stores.slwp_settings.autocomplete ) {

            // Check if we need to set the geocode component restrictions.
            if ( typeof slwp_stores.slwp_settings.region !== "undefined" && slwp_stores.slwp_settings.region.length > 0 ) {
                var regionComponents = {};
                regionComponents.country = slwp_stores.slwp_settings.region.toUpperCase();

                options.componentRestrictions = regionComponents;

            }

            input        = document.getElementById( "slwp-search-input" );
            autocomplete = new google.maps.places.Autocomplete( input, options );

            autocomplete.addListener( "place_changed", function() {
                place = autocomplete.getPlace();

                /*
                 * Assign the returned latlng to the autoCompleteLatLng var.
                 * This var is used when the users submits the search.
                 */
                 if ( place.geometry ) {
                    autoCompleteLatLng = place.geometry.location;

                }
            });

        }
    }


        /**
         * Return the latlng coordinates that are used to init the map.
         * @return {void}
         */
         function slwp_getStartLatlng() {
            var startLatLng, latLng;

            /*
             * Use coordinates from the default start point defined or we set it to 0,0
             */
             if ( slwp_stores.slwp_settings.start_latlng !== "" ) {
                latLng      = slwp_stores.slwp_settings.start_latlng.split( "," );
                startLatLng = new google.maps.LatLng( latLng[0], latLng[1] );
            } else {
                startLatLng = new google.maps.LatLng( 0,0 );
            }
            return startLatLng;
        }


        /**
         * Add a new marker to the map based on the provided location (latLng).
         * @param  {object}  latLng         The coordinates
         * @param  {number}  storeId        The store id
         * @param  {object}  infoWindow     The infoWindow object
         * @return {void}
         */
         function slwp_addMarker( latLng, storeId, infoWindow ) {
            var url, mapIcon, marker,
            keepStartMarker = true;

            if ( storeId == null ) {
                url = slwp_stores.initial_location_marker;
                mapIcon = {
                    url: url,
                    scaledSize: new google.maps.Size( 24, 36 ), //retina format
                    origin: new google.maps.Point( 0, 0 ),
                    anchor: new google.maps.Point( 0, 32 )
                };
            } else {
                url = slwp_stores.store_location_marker;
                mapIcon = {
                    url: url,
                    scaledSize: new google.maps.Size( 20, 32 ), //retina format
                    origin: new google.maps.Point( 0, 0 ),
                    anchor: new google.maps.Point( 0, 32 )
                };
            }

            marker = new google.maps.Marker({
                position: latLng,
                map: map,
                optimized: false, //fixes markers flashing while bouncing
                title: 'Test Title',//decodeHtmlEntity( infoWindowData.store ),
                storeId: storeId,
                icon: mapIcon
            });

            if ( storeId == null) {
                startMarkerData = marker;
            }

            // Store the marker for later use.
            markersArray.push( marker );
            if ( storeId != null) {

                google.maps.event.addListener( marker, "click",( function( currentMap ) {
                    return function() {
                        if ( storeId !== '') {
                            slwp_setInfoWindowContent( storeId, marker, infoWindow, currentMap );
                            openInfoWindow.push( infoWindow );
                        }

                        google.maps.event.clearListeners( infoWindow, "domready" );
                    };
                }( map ) ) );
            }

            //marker bounce animation
            slwp_toggleMarkerAnimation();

        }

        /**
         * Set the correct info window content for the marker.
         * @param   {integer} storeId          Store Id
         * @param   {object} marker            Marker data
         * @param   {object} infoWindow        The infoWindow object
         * @param   {object} currentMap        The map object
         * @returns {void}
         */
         function slwp_setInfoWindowContent( storeId, marker, infoWindow, currentMap ) {
            var infoWindowContent = '', storeName, storeUrl, storeLatLng, storePhone, storeDescription, storeAddress, url = '';
            storeName = jQuery('#store-item-id-'+storeId).data('storename');
            storeUrl = jQuery('#store-item-id-'+storeId).data('storeurl');
            storePhone = jQuery('#store-item-id-'+storeId).data('phone');
            storeAddress = jQuery('#store-item-id-'+storeId).data('address');
            storeDescription = jQuery('#store-item-id-'+storeId).data('desc');

            if ( typeof storeUrl !== 'undefined') {
                url = storeUrl;
            }


            var title_url_wrap = slwp_setTitleUrl(storeName, url, slwp_stores.slwp_settings.show_url_field);
            infoWindowContent += '<div class="slwp-info-wrap">';
            infoWindowContent += '<span class="slwp-title"><label>Title:</label> ';
            infoWindowContent += title_url_wrap.before_wrap;
            infoWindowContent += title_url_wrap.title;
            infoWindowContent += title_url_wrap.after_wrap;
            infoWindowContent += '</span>';
            infoWindowContent += '<span class="slwp-address"><label>Address:</label> ';
            infoWindowContent += storeAddress;
            infoWindowContent += '</span>';
            if ( slwp_stores.slwp_settings.show_phone_field ) {

                infoWindowContent += '<span class="slwp-phone"><label>Phone No:</label> ' ;
                infoWindowContent += storePhone;
                infoWindowContent += '</span>';

            }
            if ( slwp_stores.slwp_settings.show_description_field ) {

                infoWindowContent += '<span class="slwp-desc"><label>Description:</label> ';
                infoWindowContent += storeDescription;
                infoWindowContent += '</span>';

            }

            infoWindowContent += '</div>';
            openInfoWindow.length = 0;

            infoWindow.setContent( infoWindowContent );
            infoWindow.open( currentMap, marker );

            openInfoWindow.push( infoWindow );
        }


        /**
        * Set the url for the title in infowindow
        * @param   {string} title            TItle
        * @param   {string} url              Url
        * @param   {boolean} show_url        Show or hide url
        * @returns {string}                  Formatted url
        */
        function slwp_setTitleUrl(title, url, show_url) {

            var return_output = {};
            return_output.before_wrap = '';
            return_output.after_wrap = '';

            if ( typeof url != 'undefined' && url != '' && show_url ) {

                return_output.before_wrap = '<a href="'+url+'" class="title-link" target="_blank">';
                return_output.title = title;
                return_output.after_wrap = '</a>';
            } else {
                return_output.before_wrap = '';
                return_output.title = title;
                return_output.after_wrap = '';
            }
            return return_output;
        }

        /**
        * Set form element to search stores
        * @param   {object} infoWindow        The infoWindow object
        * @returns {void}
        */
        function slwp_initialize_store_search( infoWindow ) {

            jQuery( "#slwp-search-btn" ).unbind( "click" ).bind( "click", function( e ) {
                e.preventDefault();

                var keepStartMarker = false;

                // Force the open InfoBox info window to close.
                slwp_closeInfoBoxWindow();

                slwp_deleteOverlays( keepStartMarker );
                slwp_deleteStartMarker();

                /*
                 * Check if we need to geocode the user input,
                 * or if autocomplete is enabled and we already
                 * have the latlng values.
                 */
                 if ( slwp_stores.slwp_settings.autocomplete == 1 && typeof autoCompleteLatLng !== "undefined" ) {

                    slwp_prepareStoreSearch( autoCompleteLatLng, infoWindow );
                } else {
                    slwp_codeAddress( infoWindow );

                }

                return false;
            });

        }

        /**
         * Force the open InfoBox info window to close.
         * @returns {void}
         */
         function slwp_closeInfoBoxWindow() {
            if ( typeof openInfoWindow[0] !== "undefined" ) {
                openInfoWindow[0].close();
            }
        }

        /**
         * Remove all existing markers from the map.
         * @param   {boolean} keepStartMarker Whether or not to keep the start marker while removing all the other markers from the map
         * @returns {void}
         */
         function slwp_deleteOverlays( keepStartMarker ) {
            var markerLen, i;

            displayDirection.setMap( null );

            // Remove all the markers from the map, and empty the array.
            if ( markersArray ) {
                for ( i = 0, markerLen = markersArray.length; i < markerLen; i++ ) {

                    // Check if we need to keep the start marker, or remove everything.
                    if ( keepStartMarker ) {
                        if ( markersArray[i].draggable != true ) {
                            markersArray[i].setMap( null );
                        } else {
                            startMarkerData = markersArray[i];
                        }
                    } else {
                        markersArray[i].setMap( null );
                    }
                }

                markersArray.length = 0;
            }

        }


        /**
         * Remove the start marker from the map.
         * @returns {void}
         */
         function slwp_deleteStartMarker() {
            if ( ( typeof( startMarkerData ) !== "undefined" ) && ( startMarkerData !== "" ) ) {
                startMarkerData.setMap( null );
                startMarkerData = "";
            }
        }

        /**
         * Geocode the user input.
         * @param   {object} infoWindow The infoWindow object
         * @returns {void}
         */
         function slwp_codeAddress( infoWindow ) {

            var request = {
                'address': jQuery( "#slwp-search-input" ).val()
            };
            var latLng;

            // Check if we need to set the geocode component restrictions.
            if ( typeof slwp_stores.slwp_settings.region !== "undefined" && slwp_stores.slwp_settings.region.length > 0 ) {
                var regionComponents = {};
                regionComponents.country = slwp_stores.slwp_settings.region.toUpperCase();

                request.componentRestrictions = regionComponents;

            }

            geocoder.geocode( request, function( response, status ) {
                if ( status == google.maps.GeocoderStatus.OK ) {
                    latLng = response[0].geometry.location;

                    slwp_prepareStoreSearch( latLng, infoWindow );
                } else {
                    slwp_geocodeErrors( status );
                }
            });
        }


        /**
         * Prepare a new location search.
         * @param   {object} prepare_latLng
         * @param   {object} infoWindow The infoWindow object.
         * @returns {void}
         */
         function slwp_prepareStoreSearch( prepare_latLng, infoWindow ) {

            // Add a new start marker.
            slwp_addMarker( prepare_latLng, null, infoWindow );

            // Try to find stores that match the radius, location criteria.
            slwp_makeAjaxRequest( prepare_latLng, infoWindow );
        }


        /**
         * Make the AJAX request to load the store data.
         *
         * @param   {object}  startLatLng The latlng used as the starting point
         * @param   {object}  infoWindow  The infoWindow object
         * @returns {void}
         */
         function slwp_makeAjaxRequest( startLatLng, infoWindow ) {

            var post_id = jQuery('#slwp-post-id').val();
            var storeList = jQuery("#slwp-store-lists");
            var search_radius = jQuery('#slwp-radius-dropdown').val();
            var stores_count = jQuery('#slwp-results-dropdown').val();

            var ajaxData = {
                action: "slwp_store_search",
                lat: startLatLng.lat(),
                lng: startLatLng.lng(),
                post_id: post_id,
                search_radius: search_radius,
                stores_count: stores_count
            };

            var maxZoom = Number( slwp_stores.slwp_settings.max_zoom_level );

            jQuery(storeList).empty();

            var result_html = '';

            jQuery.ajax({
                data: ajaxData,
                type: 'POST',
                url: slwp_stores.ajaxurl,
                success: function( response ){
                    jQuery.each( response, function( index, value ){

                        var serial_no = index;
                        serial_no = ++serial_no;

                        var title_url_wrap = slwp_setTitleUrl(value.slwp_name, value.slwp_url, slwp_stores.slwp_settings.show_url_field);

                        result_html += '<li class="store-items" id="store-item-id-'+index+'" data-storeid="'+index+'" data-storename="'+value.slwp_name+'" data-storeurl="'+value.slwp_url+'" data-latlng="'+value.slwp_location_latn+'" data-phone="'+value.slwp_phone+'" data-address="'+value.slwp_location+'" data-desc="'+value.slwp_description+'">';
                        result_html += '<div class="map-content">';
                        result_html += '<h3 class="store-title">';
                        result_html += '<span class="store-key">'+serial_no+'</span>';
                        result_html += title_url_wrap.before_wrap;
                        result_html += title_url_wrap.title;
                        result_html += title_url_wrap.after_wrap;
                        result_html += '</h3>';
                        result_html += '<span class="store-items store-address">'+value.slwp_location;
                        result_html += '</span>';
                        if ( slwp_stores.slwp_settings.show_phone_field ) {

                            result_html += '<span class="store-items store-phone">'+value.slwp_phone+'</span>';
                        }
                        if ( slwp_stores.slwp_settings.show_description_field ) {

                            result_html += '<p>'+value.slwp_description+'</p>';
                        }
                        if ( slwp_stores.slwp_settings.direction_view_control ) {
                            result_html += '<span class="store-items get-direction"><a class="slwp-get-direction" href="#" id="get-direction-'+index+'">Get Direction</a></span>';
                        }
                        result_html += '</div>';
                        result_html += '</li>';


                        var item_latlng = value.slwp_location_latn.split( "," );
                        response_latLng = new google.maps.LatLng( item_latlng[0], item_latlng[1] );

                        slwp_addMarker( response_latLng, index, infoWindow );

                    });
                    //Append items to lists.
                    storeList.html(result_html);

                    // Make sure we don't zoom to far.
                    slwp_fitBounds();
                    slwp_render_direction();
                }
            });
        }


         /**
          * Handle the geocode errors.
          * @param   {string} status Contains the error code
          * @returns {void}
          */
          function slwp_geocodeErrors( status ) {
             var msg;
             switch ( status ) {
                case "ZERO_RESULTS":
                msg = 'No results found';
                break;
                case "OVER_QUERY_LIMIT":
                msg = 'API usage limit reached';
                break;
                default:
                msg = 'Something went wrong, please try again!';
                break;
            }

            alert( msg );
        }


         /**
          * Zoom the map so that all markers fit in the window.
          * @returns {void}
          */
          function slwp_fitBounds() {
             var i, markerLen,
             maxZoom = Number( slwp_stores.slwp_settings.max_zoom_level ),
             bounds  = new google.maps.LatLngBounds();

             // Make sure we don't zoom to far.
             google.maps.event.addListenerOnce( map, "bounds_changed", function( event ) {
                if ( this.getZoom() > maxZoom ) {
                    this.setZoom( maxZoom );
                }
            });

             for ( i = 0, markerLen = markersArray.length; i < markerLen; i++ ) {
                bounds.extend ( markersArray[i].position );
            }

            map.fitBounds( bounds );
        }


         /**
         * Trigger to render driving directions.
         * @returns {void}
         */
         function slwp_render_direction() {
            jQuery( "#slwp-store-lists" ).on( "click", ".slwp-get-direction", function() {

                // Check if we need to render the direction on the map.
                if ( slwp_stores.slwp_settings.direction_view_control == 1 ) {
                    slwp_renderDirections( jQuery( this ) );
                    return false;
                }
            });
        }

         /**
          * Show the driving directions.
          * @param  {object} e The clicked elemennt
          * @returns {void}
          */
          function slwp_renderDirections( e ) {
             var i, start, end, len, store_Id;

            // Force the open InfoBox info window to close.
            slwp_closeInfoBoxWindow();

             /*
              * The storeId is placed on the li in the results list,
              * but in the marker it will be on the wrapper div. So we check which one we need to target.
              */
              if ( e.parents( "li" ).length > 0 ) {
                store_Id = e.parent().parent().closest( "li" ).data( "storeid" );
             }

            // Check if we need to get the start point from a dragged marker.
            if ( ( typeof( startMarkerData ) !== "undefined" )  && ( startMarkerData !== "" ) ) {
                start = startMarkerData.getPosition();
            }

            // Used to restore the map back to the state it was in before the user clicked on 'directions'.
            directionMarkerPosition = {
                centerLatlng: map.getCenter(),
                zoomLevel: map.getZoom()
            };

             // Find the latlng that belongs to the start and end point.
             for ( i = 0, len = markersArray.length; i < len; i++ ) {
              if ( markersArray[i].storeId === store_Id ) {
                end = markersArray[i].getPosition();
            }
        }

        if ( start && end ) {
            jQuery( "#slwp-direction-details ul" ).empty();
                slwp_calcRoute( start, end );
                //Trigger click on back button to locations lists when directions are shown.
                slwp_triggerLocationLists();
            } else {
                alert( 'Something went wrong, please try again!' );
            }
        }

         /**
          * Calculate the route from the start to the end.
          * @param  {object} start The latlng from the start point
          * @param  {object} end   The latlng from the end point
          * @returns {void}
          */
          function slwp_calcRoute( start, end ) {
             var legs, len, step, index, direction, i, j, distanceUnit, directionOffset,
             directionStops = "",
             request = {};

             if ( slwp_stores.slwp_settings.distance_unit == "km" ) {
                distanceUnit = 'METRIC';
            } else {
                distanceUnit = 'IMPERIAL';
            }

            request = {
                origin: start,
                destination: end,
                travelMode: 'DRIVING',
                unitSystem: google.maps.UnitSystem[ distanceUnit ]
            };

            serviceDirections.route( request, function( response, status ) {
                if ( status == google.maps.DirectionsStatus.OK ) {
                    displayDirection.setMap( map );
                    displayDirection.setDirections( response );

                    if ( response.routes.length > 0 ) {
                        direction = response.routes[0];

                        directionStops += "<li><div class='slwp-direction-before'><a class='slwp-back' id='slwp-direction-start' href='#'>Back</a><div class='slwp-distance-time'><span class='slwp-total-distance'>" + direction.legs[0].distance.text + "</span> - <span class='slwp-total-durations'>" + direction.legs[0].duration.text + "</span></div></div></li>";

                        // Loop over the legs and steps of the directions.
                        for ( i = 0; i < direction.legs.length; i++ ) {
                            legs = direction.legs[i];

                            for ( j = 0, len = legs.steps.length; j < len; j++ ) {
                                step = legs.steps[j];
                                index = j+1;
                                directionStops = directionStops + "<li><div class='slwp-direction-index'>" + index + "</div><div class='slwp-direction-txt'>" + step.instructions + "</div><div class='slwp-direction-distance'>" + step.distance.text + "</div></li>";
                            }
                        }
                        directionStops += "<p class='slwp-direction-after'>" + response.routes[0].copyrights + "</p>";

                        jQuery( "#slwp-direction-detail ul" ).html( directionStops );
                        jQuery( "#slwp-store-lists" ).hide();
                        jQuery( "#slwp-direction-detail" ).show();

                        // Remove all single markers from the map.
                        for ( i = 0, len = markersArray.length; i < len; i++ ) {
                            markersArray[i].setMap( null );
                        }

                        // Remove the start marker from the map.
                        if ( ( typeof( startMarkerData ) !== "undefined" ) && ( startMarkerData !== "" ) ) {
                            startMarkerData.setMap( null );
                        }
                    }
                } else {
                    slwp_geocodeErrors( status );
                }
            });
        }


        /**
        * Triggers when clicked on back button to locations lists when directions are shown.
        * @returns {void}
        */
        function slwp_triggerLocationLists() {
             // Handle the click on the back button when the route directions are displayed.
             jQuery( "#slwp-direction-detail" ).on( "click", ".slwp-back", function() {
                var i, len;

                 // Remove the directions from the map.
                 displayDirection.setMap( null );

                 // Restore the store markers on the map.
                 for ( i = 0, len = markersArray.length; i < len; i++ ) {
                    markersArray[i].setMap( map );
                }

                // Restore the start marker on the map.
                if ( ( typeof( startMarkerData ) !== "undefined" )  && ( startMarkerData !== "" ) ) {
                    startMarkerData.setMap( map );
                }

                map.setCenter( directionMarkerPosition.centerLatlng );
                map.setZoom( directionMarkerPosition.zoomLevel );

                jQuery( ".slwp-direction-before, .slwp-direction-after" ).remove();
                jQuery( "#slwp-store-lists" ).show();
                jQuery( "#slwp-direction-detail" ).hide();

                return false;
            });

         }


         /**
         * Animate bounce when mouse enters or leavs the list items. 
         * @returns {void}
         */
         function slwp_toggleMarkerAnimation() {

            jQuery('ul#slwp-store-lists').on('mouseenter', 'li', function(){
                slwp_letsAnimate( jQuery(this).data('storeid'), 'start' );
            });

            jQuery('ul#slwp-store-lists').on('mouseleave', 'li', function(){
                slwp_letsAnimate( jQuery(this).data('storeid'), 'stop' );
            });

        }

        /**
        * Animation bounce triggered.
        * @returns {void}
        */
        function slwp_letsAnimate( storeId, status ) {
           var i, len, marker;

             // Find the correct marker to bounce based on the storeId.
             for ( i = 0, len = markersArray.length; i < len; i++ ) {
                if ( markersArray[i].storeId == storeId ) {

                    if ( status == "start" ) {
                        markersArray[i].setAnimation( google.maps.Animation.BOUNCE );
                    } else {
                        markersArray[i].setAnimation( null );
                    }
                }
            }
        }
    });