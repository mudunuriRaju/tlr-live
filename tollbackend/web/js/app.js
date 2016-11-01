/**
 * Created by admin on 5/18/2016.
 */
var country;
var geolocation;
var directionsRenderer;
var markers = [];
var flightpaths = [];
var google_result_set = [];
var tolls_per_list = [];
var imageicon = {
    url: base_url + '/images/tool-point-icon.png',
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


var geocoder;

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


/*$(document).ready(function(){
 //initialize();
 $(".direction-but").click(function(){
 if($('#state').css('display') == 'block') {
 $('#state').css('display', 'none');
 $('#direction').css('display', 'block');
 }
 else {
 $('#state').css('display', 'block');
 $('#direction').css('display', 'none');
 }
 $(".direction-but").toggleClass("direction-but1");
 });
 });*/




