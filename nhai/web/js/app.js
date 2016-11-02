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

var chart = c3.generate({
    bindto: '#chart',
    data: {
        columns: [
            ['Tollered vechicals', 4985],
            ['Tollered With Errors', 285],
            ['Wrong Entries', 123]
//           ['data1', 30, 200, 100, 400, 150, 250],
//           ['data2', 130, 100, 140, 200, 150, 50]
        ],
        type: 'bar',
        onclick: function (d, element) { console.log("onclick", d, element); },
        onmouseover: function (d) { console.log("onmouseover", d); },
        onmouseout: function (d) { console.log("onmouseout", d); }
    },
    axis: {
        x: {
            type: 'categorized'
        }
    },
    bar: {
        width: {
            ratio: 0.1,
//            max: 30
        },
    }
});

var vchart = c3.generate({
    bindto: '#vchart',
    data: {
        columns: [
            ['Bike', 10],
            ['Jeep/van', 32],
            ['LCV', 22],
            ['Bus/Truck', 6],
            ['Upto 3 Axle', 41],
            ['4to6 Axle', 15],
            ['HCM/EME', 25],
            ['7 or More Axle', 16]
//           ['data1', 30, 200, 100, 400, 150, 250],
//           ['data2', 130, 100, 140, 200, 150, 50]
        ],
        type: 'bar',
        onclick: function (d, element) { console.log("onclick", d, element); },
        onmouseover: function (d) { console.log("onmouseover", d); },
        onmouseout: function (d) { console.log("onmouseout", d); }
    },
    axis: {
        x: {
            type: 'categorized'
        }
    },
    bar: {
        width: {
            ratio: 0.1,
//            max: 30
        },
    }
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




