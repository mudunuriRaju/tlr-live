/**
 * Created by admin on 5/18/2016.
 */

$(document).ready(function () {


    //for (var key in states) {
    // skip loop if the property is from prototype
    //    if (!states.hasOwnProperty(key)) continue;

    //    var obj = states[key];
    // $('.search-by').append('<option value="'+key+'">'+obj+'</option>');
    //}


    $(".but").click(function () {
        $(".google-map-overlay").toggleClass("moh");
        $(".grid").toggleClass("moh1");
        $(".but").toggleClass("but1");
    });
});


$(document).ready(function () {
    //initialize();
    $(".direction-but").click(function () {
        if ($('#state').css('display') == 'block') {
            $('#state').css('display', 'none');
            $('#direction').css('display', 'block');
        }
        else {
            $('#state').css('display', 'block');
            $('#direction').css('display', 'none');
        }
        $(".direction-but").toggleClass("direction-but1");
    });
});

var country;
var geolocation;
var directionsRenderer;
var markers = [];
var flightpaths = [];
var google_result_set = [];
var tolls_per_list = [];
var imageicon = {
    url: 'tollapp/images/logo-icon.png',
    size: new google.maps.Size(34, 32),
    //origin: new google.maps.Point(0, 0),
    //anchor: new google.maps.Point(0, 1)
};

//create the map
var map;

//instantiate the RouteBoxer library
var routeBoxer = new RouteBoxer();

var directionService = new google.maps.DirectionsService();


var autocompletedestination, autocompleteOrgin;
var index_path;

/*function createMarker(map, coords, title) {
 //console.log(coords);
 marker = new google.maps.Marker({
 position: coords,
 map: map,
 title: title,
 icon: imageicon,
 draggable: false
 });
 markers.push(marker);

 return marker;
 }*/

/*function DeleteMarkers() {
 //Loop through all the markers and remove
 for (var i = 0; i < markers.length; i++) {
 markers[i].setMap(null);
 }
 markers = [];
 };*/

/*function findTolls(){
 var places;
 $.get("http://115.124.125.42/Tolls/api/2119/t/tl/"+country, function (data) {

 places = data.Info;

 for (i in places) {
 var coords = new google.maps.LatLng(places[i].toll_lat, places[i].toll_lng);
 createMarker(map, coords, places[i].toll_location);
 content = places[i].toll_location+'</br><a href="'+places[i].toll_location+'">See report</a>';
 google.maps.event.addListener(marker,'click', (function(marker,content,infowindow){
 return function() {
 infowindow.setContent(content);
 infowindow.open(map,marker);
 };
 })(marker,content,infowindow));

 });
 }*/
var geocoder;
/*function initialize() {
 geocoder = new google.maps.Geocoder();
 if (navigator.geolocation) {
 navigator.geolocation.getCurrentPosition(function (position) {

 geolocation = new google.maps.LatLng(
 position.coords.latitude, position.coords.longitude);
 codeLatLng(position.coords.latitude, position.coords.longitude);
 map = new google.maps.Map(document.getElementById("google-map"), {
 center: new google.maps.LatLng(
 position.coords.latitude, position.coords.longitude),
 zoom: 5,
 mapTypeId: google.maps.MapTypeId.DRIVING
 });

 });

 }

 directionsRenderer = new google.maps.DirectionsRenderer({
 map: map,
 polylineOptions: {
 strokeColor: "#FFA500"
 }
 });


 }*/

/*function codeLatLng(lat, lng) {

 var latlng = new google.maps.LatLng(lat, lng);
 geocoder.geocode({'latLng': latlng}, function(results, status) {
 if (status == google.maps.GeocoderStatus.OK) {

 if (results[1]) {
 //formatted address

 for (var i=0; i<results[0].address_components.length; i++) {
 for (var b=0;b<results[0].address_components[i].types.length;b++) {

 //there are different types that might hold a city admin_area_lvl_1 usually does in come cases looking for sublocality type will be more appropriate
 if (results[0].address_components[i].types[b] == "country") {
 //this is the object you are looking for
 city= results[0].address_components[i];
 break;
 }
 }
 }
 //city data

 country = city.long_name;
 //findTolls();
 return findTolls();



 } else {
 alert("No results found");
 }
 } else {
 alert("Geocoder failed due to: " + status);
 }
 });
 }*/

