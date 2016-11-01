/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
/*function for button*/
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
}

function showPosition(position) {
    var latlng = new google.maps.LatLng(position.coords.latitude, position.coords.longitude);


    if ($('#tollform-toll_location').length > 0) {
        console.log($('#tolls-toll_lat').val());
        codeLatLng(position.coords.latitude, position.coords.longitude);
    } else if ($('.toll-view').length > 0) {
        latlng = new google.maps.LatLng($('table tr:eq(3) > td:eq(0)').text(), $('table tr:eq(4) > td:eq(0)').text());
        dragable_value = false;
    } else if ($('.toll-boothside-form').length > 0) {
        var toll_id = $('#tollboothside-toll_id').val();
        if ($('#tollboothside-lat').val() > 0) {
            var toll_lat = $('#tollboothside-lat').val();
            ;
            var toll_lng = $('#tollboothside-lng').val();
            latlng = new google.maps.LatLng(toll_lat, toll_lng);
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
    //}

    mapselect(latlng);
    /* var mapOptions = {
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
     });*/

}

function mapselect(latlng) {
    var dragable_value = true;
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
                if ($('.toll-boothside-form').length > 0) {
                    //$('#tolls-toll_location').val(results[1].formatted_address);
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

