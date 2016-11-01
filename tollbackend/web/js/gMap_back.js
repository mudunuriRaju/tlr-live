/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
/*function for button*/
var autocompletedestination, autocompleteOrgin, autocompletewaypoint;
var searchBox = new google.maps.places.Autocomplete(
    /** @type {HTMLInputElement} */(document.getElementById('tollform-toll_location'))
);


// Listen for the event fired when the user selects an item from the
// pick list. Retrieve the matching places for that item.


/*autocompleteplace =  new google.maps.places.Autocomplete(
 *//** @type {HTMLInputElement} *//*(document.getElementById('tollform-toll_location')),
 {types: ['geocode']});*/


autocompletedestination = new google.maps.places.Autocomplete(
    /** @type {HTMLInputElement} */(document.getElementById('commonroutes-destination2')),
    {types: ['geocode']});
autocompleteOrgin = new google.maps.places.Autocomplete(
    /** @type {HTMLInputElement} */(document.getElementById('commonroutes-destination1')),
    {types: ['geocode']});
autocompletewaypoint = new google.maps.places.Autocomplete(
    /** @type {HTMLInputElement} */(document.getElementById('commonroutes-waypoints')),
    {types: ['geocode']});

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
        console.log(place);

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
    searchBox.setBounds(circle.getBounds());

    autocompleteOrgin.setBounds(circle.getBounds());
    autocompletedestination.setBounds(circle.getBounds());
    autocompletewaypoint.setBounds(circle.getBounds());
    var latlng = new google.maps.LatLng(position.coords.latitude, position.coords.longitude);

    var dragable_value = true;
    if ($('#tollform-toll_location').length > 0 || $('.common-route-location').length > 0) {
        //console.log($('#tolls-toll_lat').val());
        codeLatLng(position.coords.latitude, position.coords.longitude);
    } else if ($('.toll-view').length > 0) {
        latlng = new google.maps.LatLng($('table tr:eq(3) > td:eq(0)').text(), $('table tr:eq(4) > td:eq(0)').text());
        dragable_value = false;
    } else {
        latlng = new google.maps.LatLng($('#tolls-toll_lat').val(), $('#tolls-toll_lng').val());
        //codeLatLng($('#tolls-toll_lat').val(), $('#tolls-toll_lng').val());
    }
    var mapOptions = {
        zoom: 17,
        center: latlng
    };
    map = new google.maps.Map(document.getElementById("map-canvas"),
        mapOptions);
    marker = new google.maps.Marker({
        position: map.getCenter(),
        draggable: dragable_value,
        map: map
    });
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

    searchBox.bindTo('bounds', map);


}

function codeLatLng(lat, Lng) {
    var latlng = new google.maps.LatLng(lat, Lng);
    geocoder.geocode({'latLng': latlng}, function (results, status) {
        if (status == google.maps.GeocoderStatus.OK) {
            if (results[1]) {
                map.setZoom(17);
                if ($('#tollform-toll_location').length > 0) {
                    $('#tollform-toll_location').val(results[1].formatted_address);
                    $('#tollform-toll_lat').val(lat);
                    $('#tollform-toll_lng').val(Lng);
                }
                if ($('#tolls-toll_location').length > 0) {
                    $('#tolls-toll_location').val(results[1].formatted_address);
                    $('#tolls-toll_lat').val(lat);
                    $('#tolls-toll_lng').val(Lng);
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

