/**
 * @ngdoc controller
 * @name Consess
 *
 * @description
 * _Please update the description and dependencies._
 *
 * @requires $scope
 * */

var Tollr = angular.module('Tollr', ['ngAnimate', 'ngCookies']);
(function (angular) {
    'use strict';
    var myApp = angular.module('Tollr', ['google.places', 'ngAnimate', 'ngCookies']);
    myApp.controller('Consess', Consess);
    function Consess($scope, $filter, $timeout, $element, $http, listService) {
        $scope.showState = true;
        $scope.fromPoint = null;
        $scope.tooint = null;
        $scope.directionBut = 'direction-but';
        $scope.SelectedState = "India";
        $scope.ListedTollsCount = 0;
        $scope.TollsIndiaCount = 0;
        $scope.tollList = [];
        $scope.filterCondition = {
            operator: ''
        };

        $scope.searchedList = [];
        $scope.states = [
            {short_code: "", state: "Selected All"},
            {short_code: "Andhra Pradesh", state: "Andhra Pradesh"},
            {short_code: "Arunachal Pradesh", state: "Arunachal Pradesh"},
            {short_code: "Assam", state: "Assam"},
            {short_code: "Bihar", state: "Bihar"},
            {short_code: "Chhattisgarh", state: "Chhattisgarh"},
            {short_code: "Chandigarh", state: "Chandigarh"},
            {short_code: "Dadra and Nagar Haveli", state: "Dadra and Nagar Haveli"},
            {short_code: "Daman and Diu", state: "Daman and Diu"},
            {short_code: "Delhi", state: "Delhi"},
            {short_code: "Goa", state: "Goa"},
            {short_code: "Gujarat", state: "Gujarat"},
            {short_code: "Haryana", state: "Haryana"},
            {short_code: "Himachal Pradesh", state: "Himachal Pradesh"},
            {short_code: "Jammu and Kashmir", state: "Jammu and Kashmir"},
            {short_code: "Jharkhand", state: "Jharkhand"},
            {short_code: "Karnataka", state: "Karnataka"},
            {short_code: "Kerala", state: "Kerala"},
            {short_code: "Madhya Pradesh", state: "Madhya Pradesh"},
            {short_code: "Maharashtra", state: "Maharashtra"},
            {short_code: "Manipur", state: "Manipur"},
            {short_code: "Meghalaya", state: "Meghalaya"},
            {short_code: "Mizoram", state: "Mizoram"},
            {short_code: "Nagaland", state: "Nagaland"},
            {short_code: "Orissa", state: "Orissa"},
            {short_code: "Punjab", state: "Punjab"},
            {short_code: "Pondicherry", state: "Pondicherry"},
            {short_code: "Rajasthan", state: "Rajasthan"},
            {short_code: "Sikkim", state: "Sikkim"},
            {short_code: "Tamil Nadu", state: "Tamil Nadu"},
            {short_code: "Tripura", state: "Tripura"},
            {short_code: "Uttar Pradesh", state: "Uttar Pradesh"},
            {short_code: "Uttarakhand", state: "Uttarakhand"},
            {short_code: "West Bengal", state: "West Bengal"}];
        $scope.ChangedState = function (names, query) {
            $scope.queryData = $filter('filter')(names, query);
            if ($scope.queryData[0].state == 'Selected All') {
                $scope.SelectedState = 'India';
            } else {
                $scope.SelectedState = $scope.queryData[0].state;
            }
            listService.getList($http, $scope, listService);
            //console.log($scope.queryData[0].state);
        };
        $scope.changeShowState = function () {
            if ($scope.showState) {
                $scope.showState = false;
                $scope.directionBut = 'direction-but1';
                listService.DeleteMarkers();
                listService.findByRoute($http, $scope);
            } else {
                $scope.showState = true;
                $scope.directionBut = 'direction-but';
                listService.getList($http, $scope, listService);
            }
        }
        $scope.findTolls = function () {
            listService.findByRoute($http, $scope);
        }

        $scope.searchToll = searchToll;

        function searchToll(names, query) {
            $scope.queryData = $filter('filter')(names, query);
            $scope.searchedList = $scope.queryData;
        };
        //
        listService.initialize($http, $scope, listService);
        //listService.getList($http, $scope,listService);


    };

})(window.angular);

//Fav Controller
angular.module('Tollr')
    .controller('Ctrl2', function ($scope, $http, $filter) {
        $scope.format = 'H:mm:ss';
        $scope.dateformat = 'EEEE, MMMM d, y';
    }).directive('myCurrentTime', function ($timeout, dateFilter) {

    return function (scope, element, attrs) {
        //console.log(attrs);
        var format,  // date format
            dateformat,
            timeoutId; // timeoutId, so that we can cancel the time updates

        // used to update the UI
        function updateTime() {
            element.text(dateFilter(new Date(), format, dateformat));
        }

        // watch the expression, and update the UI on change.
        scope.$watch(attrs.myCurrentTime, function (value) {
            format = value;
            dateformat = value;
            updateTime();
        });

        // schedule update in one second
        function updateLater() {
            // save the timeoutId for canceling
            timeoutId = $timeout(function () {
                updateTime(); // update DOM
                updateLater(); // schedule another update
            }, 1000);
        }

        // listen on DOM destroy (removal) event, and cancel the next UI update
        // to prevent updating time ofter the DOM element was removed.
        element.bind('$destroy', function () {
            $timeout.cancel(timeoutId);
        });

        updateLater(); // kick off the UI update process.
    }
});

angular.module('Tollr').service('listService', function () {

    return {
        getList: function ($http, $scope, listService) {
            listService.DeleteMarkers();
            $http.defaults.headers.post["Accept"] = "*/*";
            $http.defaults.headers.post["Content-Type"] = "application/x-www-form-urlencoded";
            $http({
                url: base_url + "/../api/2119/t/tl/" + $scope.SelectedState,
                method: "GET",
            })
                .then(function (response) {

                        if (response.data.Code == 200) {
                            var places;
                            if (response.data.Info.length > 0) {
                                $scope.searchedList = $scope.tollList = response.data.Info;
                                $scope.ListedTollsCount = response.data.Info.length;
                                if ($scope.SelectedState == "India") {
                                    $scope.TollsIndiaCount = response.data.Info.length;
                                }
                            } else {
                                $scope.ListedTollsCount = 0;
                                $scope.searchedList = $scope.tollList = [];
                            }
                            places = response.data.Info;
                            //console.log(places);
                            for (i in places) {
                                var coords = new google.maps.LatLng(places[i].toll_lat, places[i].toll_lng);
                                listService.createMarker(map, coords, places[i].toll_location);
                                content = places[i].toll_location + '</br><a href="' + places[i].toll_location + '">See report</a>';
                                google.maps.event.addListener(marker, 'click', (function (marker, content, infowindow) {
                                    return function () {
                                        infowindow.setContent(content);
                                        infowindow.open(map, marker);
                                    };
                                })(marker, content, infowindow));
                            }

                        } else {
                            $scope.ListedTollsCount = 0;
                            $scope.searchedList = $scope.tollList = [];
                        }

                        return response;
                        //console.log(response);
                    },
                    function (response) { // optional
                        //console.log(response);
                        // failed
                    });
        },
        codeLatLng: function (lat, lng, $http, $scope, listService) {

            var latlng = new google.maps.LatLng(lat, lng);
            geocoder.geocode({'latLng': latlng}, function (results, status) {
                if (status == google.maps.GeocoderStatus.OK) {

                    if (results[1]) {
                        //formatted address

                        for (var i = 0; i < results[0].address_components.length; i++) {
                            for (var b = 0; b < results[0].address_components[i].types.length; b++) {

                                //there are different types that might hold a city admin_area_lvl_1 usually does in come cases looking for sublocality type will be more appropriate
                                if (results[0].address_components[i].types[b] == "country") {
                                    //this is the object you are looking for
                                    city = results[0].address_components[i];
                                    break;
                                }
                            }
                        }
                        //city data

                        country = city.long_name;
                        listService.getList($http, $scope, listService)
                        //findTolls();
                        //return listService.getList($http,$scope,listService);


                    } else {
                        alert("No results found");
                    }
                } else {
                    alert("Geocoder failed due to: " + status);
                }
            });
        },
        createMarker: function (map, coords, title) {
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
        },
        initialize: function ($http, $scope, listService) {
            geocoder = new google.maps.Geocoder();
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(function (position) {

                    geolocation = new google.maps.LatLng(
                        position.coords.latitude, position.coords.longitude);
                    listService.codeLatLng(position.coords.latitude, position.coords.longitude, $http, $scope, listService);
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


        },
        DeleteMarkers: function () {
            //Loop through all the markers and remove
            for (var i = 0; i < markers.length; i++) {
                markers[i].setMap(null);
            }
            markers = [];
        },
        findByRoute: function ($http, $scope) {
            if ($scope.fromPoint.length > 0 && $scope.toPoint.length > 0) {
                console.log($scope.fromPoint);
            }
        }

    };
});
