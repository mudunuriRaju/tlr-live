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
    var myApp = angular.module('Tollr', ['google.places', 'ngAnimate', 'ngCookies', 'ngRoute']);
    myApp.controller('Consess', Consess);
    function Consess($scope, $filter, $timeout, $element, $http, listService, $location, $rootScope, reportService) {
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
        $scope.reqs_type = 'Daily';
        $scope.require_types = [
            {name: 'Daily', value: 1},
            {name: 'Month', value: 2}
        ];

        $scope.dataRT = {selectedOption: $scope.require_types[0].value};
        //console.log($scope.require_types[0].value);
        //$scope.req_type =  $scope.require_types[0].name;
        $scope.id_month_select = true;
        $scope.req_type_change = function () {
            $scope.id_month_select = true;
            if ($scope.dataRT.selectedOption == 2) {
                $scope.id_month_select = false;
            }
            reportService.report_list($http, $scope);
        };

        $scope.exelDownload = function () {
            var blob = new Blob([document.getElementById('exportable').innerHTM], {
                type: "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;charset=utf-8"
            });
            saveAs(blob, "Report.xls");

        };

        $scope.requestDateFormat = function () {
            if ($scope.dataRT.selectedOption == 2) {
                return 'MMM/y';
            } else {
                return 'shortDate';

            }
        }
        $scope.change_month = function () {
            reportService.report_list($http, $scope);
        }

        $scope.ChangedStateReport = function (names, query) {
            $scope.queryData = $filter('filter')(names, query);
            if ($scope.queryData[0].state == 'Selected All') {
                $scope.SelectedState = 'India';
            } else {
                $scope.SelectedState = $scope.queryData[0].state;
            }
            reportService.report_list($http, $scope);
        }

        $scope.getAmount = function (vehical_type, report) {
            if (isNull(report['amount_' + vehical_type.vechical_types_id]))
                return 0;
            else
                return report['amount_' + vehical_type.vechical_types_id];
        }

        if ($('#report_screen').length > 0) {

            $scope.vehical_types = vehical_types;
            $scope.H_reports = reports;
            $scope.month_options = month_options;
            $scope.dataM = {selectedOption: $scope.month_options[0].name};
            $scope.counter = counters;
            $scope.average = counters.sum_amount / total_count;

        }


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
            {short_code: "Telangana", state: "Telangana"},
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
        if (pathInfo == '') {
            listService.initialize($http, $scope, listService);
        }


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
                                content = places[i].toll_location + '</br><a href="site/report?id=' + places[i].toll_id + '">See report</a>';
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
                        alert("Not able locate present Location");
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

                console.log(navigator.geolocation);
                navigator.geolocation.getCurrentPosition(function (position) {

                    geolocation = new google.maps.LatLng(position.coords.latitude, position.coords.longitude);
                    listService.codeLatLng(position.coords.latitude, position.coords.longitude, $http, $scope, listService);
                    map = new google.maps.Map(document.getElementById("google-map"), {
                        center: new google.maps.LatLng(
                            position.coords.latitude, position.coords.longitude),
                        zoom: 5,
                        mapTypeId: google.maps.MapTypeId.DRIVING
                    });

                }, function (failure) {
                    if (failure.message.indexOf("Only secure origins are allowed") == 0) {
                        // Secure Origin issue.'
                        listService.codeLatLng(21.324196, 78.013832, $http, $scope, listService);
                        map = new google.maps.Map(document.getElementById("google-map"), {
                            center: new google.maps.LatLng(
                                21.324196, 78.013832),
                            zoom: 5,
                            mapTypeId: google.maps.MapTypeId.DRIVING
                        });
                    }
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

angular.module('Tollr').service('reportService', function () {
    var report_list = report_list;

    var service = {
        report_list: report_list,
    };

    function report_list($http, $scope) {
        var filter_data = {};
        if ($scope.SelectedState.length > 0) {
            filter_data.state = $scope.SelectedState
        }
        $scope.reqs_type = "Monthly";
        filter_data.type_report = $scope.dataRT.selectedOption
        if ($scope.dataRT.selectedOption == 1) {
            filter_data.selected_month = $scope.dataM.selectedOption;
            $scope.reqs_type = "Daily";
        }

        if (toll_id.length > 0 || toll_id != 'undefined') {
            filter_data.toll_id = toll_id;
        }

        $http.defaults.headers.post["Accept"] = "*/*";
        $http.defaults.headers.post["Content-Type"] = "application/x-www-form-urlencoded";
        $http({
            url: base_url + "/../api/2419/hra/r",
            method: "POST",
            data: $.param(filter_data)
        })
            .then(function (response) {

                    if (response.data.Code == 200) {
                        $scope.H_reports = response.data.Info;
                        $scope.counter = response.data.Counters;
                        $scope.average = response.data.Counters.sum_amount / response.data.Count;


                    } else {
                        $scope.ListedTollsCount = 0;
                        $scope.searchedList = $scope.tollList = [];
                        $scope.average = 0;
                    }

                    return response;
                    //console.log(response);
                },
                function (response) { // optional
                    //console.log(response);
                    // failed
                });
    }

    return service;
});
