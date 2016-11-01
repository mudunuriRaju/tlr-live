/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
/*function for button*/
var autocompletedestination, autocompleteOrgin, autocompletewaypoint;
if ($('#tollform-toll_location').length > 0) {
    var searchBox = new google.maps.places.Autocomplete(
        /** @type {HTMLInputElement} */(document.getElementById('tollform-toll_location'))
    );
}


$(document).ready(function () {
    if ($('#map-canvas').length > 0) {
        getLocation();
    }

    $('#tollboothside-toll_id').on('change', function () {
        var toll_id = $(this).val();
        var toll_lat = Object.keys(tolls[toll_id])[0];
        var toll_lng = tolls[toll_id][toll_lat];
        $('#tollboothside-lat').val(toll_lat);
        $('#tollboothside-lng').val(toll_lng);
        latlng = new google.maps.LatLng(toll_lat, toll_lng);
        mapselect(latlng);
    });

});


// Listen for the event fired when the user selects an item from the
// pick list. Retrieve the matching places for that item.


/*autocompleteplace =  new google.maps.places.Autocomplete(
 *//** @type {HTMLInputElement} *//*(document.getElementById('tollform-toll_location')),
 {types: ['geocode']});*/
if ($('#commonroutes-destination2').length > 0) {
    autocompletedestination = new google.maps.places.Autocomplete(
        /** @type {HTMLInputElement} */(document.getElementById('commonroutes-destination2')),
        {types: ['geocode']});
}

if ($('#commonroutes-destination1').length > 0) {
    autocompleteOrgin = new google.maps.places.Autocomplete(
        /** @type {HTMLInputElement} */(document.getElementById('commonroutes-destination1')),
        {types: ['geocode']});
}

if ($('#commonroutes-waypoints').length > 0) {
    autocompletewaypoint = new google.maps.places.Autocomplete(
        /** @type {HTMLInputElement} */(document.getElementById('commonroutes-waypoints')),
        {types: ['geocode']});
}


var geocoder = new google.maps.Geocoder();
var infowindow = new google.maps.InfoWindow();
var map = null;
var marker = null;
var Lat;
var Lng;
function getLocation() {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(showPosition);
    } else {
        x.innerHTML = "Geolocation is not supported by this browser.";
    }

    google.maps.event.addListener(searchBox, 'place_changed', function () {
        infowindow.close();
        marker.setVisible(false);
        var place = searchBox.getPlace();
        if (!place.geometry) {
            window.alert("Autocomplete's returned place contains no geometry");
            return;
        }

        // If the place has a geometry, then present it on a map.
        if (place.geometry.viewport) {
            map.fitBounds(place.geometry.viewport);
        } else {
            map.setCenter(place.geometry.location);
            //map.setZoom(17);  // Why 17? Because it looks good.
        }

        marker.setPosition(place.geometry.location);
        marker.setVisible(true);
        var address = '';
        /* if (place.address_components) {
         address = [
         (place.address_components[0] && place.address_components[0].short_name || ''),
         (place.address_components[1] && place.address_components[1].short_name || ''),
         (place.address_components[2] && place.address_components[2].short_name || '')
         ].join(' ');
         }*/

        //infowindow.setContent('<div><strong>' + place.name + '</strong><br>' + address);
        //infowindow.open(map, marker);
    });

}
$(document).ready(function () {


    google.maps.event.addListener(searchBox, 'places_changed', function () {
        infowindow.close();
        var place = searchBox.getPlace();
        s

    });


    if ($('#map-canvas').length > 0) {
        getLocation();
    }

});

function showPosition(position) {

    var circle = new google.maps.Circle({
        center: new google.maps.LatLng(
            position.coords.latitude, position.coords.longitude),
        radius: position.coords.accuracy
    });
    if ($('#tollform-toll_location').length > 0) {
        searchBox.setBounds(circle.getBounds());
    }

    if ($('#commonroutes-destination2').length > 0) {
        autocompleteOrgin.setBounds(circle.getBounds());
    }
    if ($('#commonroutes-destination1').length > 0) {
        autocompletedestination.setBounds(circle.getBounds());
    }
    if ($('#commonroutes-waypoints').length > 0) {
        autocompletewaypoint.setBounds(circle.getBounds());
    }

    var latlng = new google.maps.LatLng(position.coords.latitude, position.coords.longitude);
    if ($('#tollboothside-toll_id').length > 0) {
        // alert(toll.toll_lat);
        //latlng = new google.maps.LatLng(toll.toll_lat, toll.toll_lng);

    }

    var dragable_value = true;
    if ($('#tollform-toll_location').length > 0 || $('.common-route-location').length > 0) {
        //console.log($('#tolls-toll_lat').val());
        codeLatLng(position.coords.latitude, position.coords.longitude);
    } else if ($('.toll-view').length > 0) {
        latlng = new google.maps.LatLng($('table tr:eq(3) > td:eq(0)').text(), $('table tr:eq(4) > td:eq(0)').text());
        dragable_value = false;
    } else if ($('.toll-boothside-form').length > 0) {
        var toll_id = $('#tollboothside-toll_id').val();
        if ($('#tollboothside-lat').val() > 0) {
            latlng = new google.maps.LatLng(toll.toll_lat, toll.toll_lng);
            dragable_value = false;
            //latlng = new google.maps.LatLng(toll_lat, toll_lng);
        } else if ($('#tollboothside-toll_id').length > 0) {
            latlng = new google.maps.LatLng(toll.toll_lat, toll.toll_lng);
            dragable_value = false;
            //mapselect(latlng);
        } else {
            //var toll_lat = Object.keys(tolls[toll_id])[0];
            // var toll_lng = tolls[toll_id][toll_lat];
            // $('#tollboothside-lat').val(toll_lat);
            // $('#tollboothside-lng').val(toll_lng);
            latlng = new google.maps.LatLng(position.coords.latitude, position.coords.longitude);

        }
        // latlng = new google.maps.LatLng(toll_lat, toll_lng);
    } else {

        latlng = new google.maps.LatLng($('#tolls-toll_lat').val(), $('#tolls-toll_lng').val());
        //codeLatLng($('#tolls-toll_lat').val(), $('#tolls-toll_lng').val());
    }
    var mapOptions = {
        zoom: 17,
        center: latlng
    };
    console.log(mapOptions);
    map = new google.maps.Map(document.getElementById("map-canvas"),
        mapOptions);
    //alert(toll.toll_lat);
    marker = new google.maps.Marker({
        position: map.getCenter(),
        draggable: dragable_value,
        map: map
    });
    if ($('.CreateTollSidesForm').length > 0) {
        marker1 = new google.maps.Marker({
            position: map.getCenter(),
            draggable: true,
            animation: google.maps.Animation.DROP,
            map: map,
            icon: 'http://maps.google.com/mapfiles/ms/icons/purple-dot.png'
        });
        marker2 = new google.maps.Marker({
            position: map.getCenter(),
            draggable: true,
            map: map,
            animation: google.maps.Animation.DROP,
            icon: 'http://maps.google.com/mapfiles/ms/icons/blue-dot.png'
        });
        google.maps.event.addListener(marker1, 'dragend', function (event) {
            $('.purple_lat').val(event.latLng.lat());
            $('.purple_lng').val(event.latLng.lng());
        });
        google.maps.event.addListener(marker2, 'dragend', function (event) {
            $('.blue_lat').val(event.latLng.lat());
            $('.blue_lng').val(event.latLng.lng());
        });
    }

    if ($('.UpdateTollSidesForm').length > 0) {
        var toll_lat = $('#tollboothside-lat').val();
        var toll_lng = $('#tollboothside-lng').val();

        marker1 = new google.maps.Marker({
            position: {lat: parseFloat(toll_lat), lng: parseFloat(toll_lng)},
            draggable: true,
            animation: google.maps.Animation.DROP,
            map: map,
            icon: 'http://maps.google.com/mapfiles/ms/icons/purple-dot.png'
        });
        google.maps.event.addListener(marker1, 'dragend', function (event) {
            $('.purple_lat').val(event.latLng.lat());
            $('.purple_lng').val(event.latLng.lng());
        });
    }


    google.maps.event.addListener(marker, 'dragend', function (event) {
        if (map.getZoom() < 10) {
            map.setZoom(10);
        }
        map.setCenter(event.latLng);
        codeLatLng(event.latLng.lat(), event.latLng.lng());
        drag = true;
        setTimeout(function () {
            drag = false;
        }, 250);
    });

    if ($('#tollform-toll_location').length > 0) {
        searchBox.bindTo('bounds', map);
    }


}

function codeLatLng(lat, Lng) {
    var latlng = new google.maps.LatLng(lat, Lng);
    geocoder.geocode({'latLng': latlng}, function (results, status) {
        if (status == google.maps.GeocoderStatus.OK) {
            if (results[1]) {
                map.setZoom(17);
                if ($('#tollform-toll_location').length > 0) {
                    console.log(results);
                    $('#tollform-toll_location').val(results[1].formatted_address);
                    $('#tollform-toll_lat').val(lat);
                    $('#tollform-toll_lng').val(Lng);
                }
                if ($('#tolls-toll_location').length > 0) {
                    $('#tolls-toll_location').val(results[1].formatted_address);
                    $('#tolls-toll_lat').val(lat);
                    $('#tolls-toll_lng').val(Lng);
                }
                if ($('#tollboothside-boothside_from').length > 0) {
                    //$('#tollboothside-boothside_from').val(results[1].formatted_address);
                    $('#tollboothside-lat').val(lat);
                    $('#tollboothside-lng').val(Lng);
                }
                //console.log(results[1].formatted_address);
                //infowindow.setContent(results[1].formatted_address);
                //infowindow.open(map, marker);
            } else {
                alert('No results found');
            }
        } else {
            alert('Geocoder failed due to: ' + status);
        }
    });
}

