/**
 * @ngdoc controller
 * @name Home
 *
 * @description
 * _Please update the description and dependencies._
 *
 * @requires $scope
 * */
(function (angular) {
    'use strict';
    var myApp = angular.module('UserApp', ['ngAnimate', 'ui.bootstrap']);
    myApp.controller('Home', function ($scope, $http, $filter, historylist) {
        var dt = new Date();
        $scope.tripdetails = [];
        $scope.history = [];
        $scope.oneAtATime = true;
        $scope.inClass = "";

        $scope.addinClass = function () {
            $scope.inClass = 'in';
        }

        // GET THE MONTH AND YEAR OF THE SELECTED DATE.
        var month = dt.getMonth(),
            year = dt.getFullYear();
        $scope.from_date = new Date(year, month, 1);
        $scope.to_date = new Date();
        $scope.open1 = function () {
            $scope.popup1.opened = true;
        };

        $scope.open2 = function () {
            $scope.popup2.opened = true;
        };

        $scope.popup1 = {
            opened: false
        };
        $scope.maxDate = new Date();
        $scope.popup2 = {
            opened: false
        };
        $scope.historystatus = function (type) {
            if (type = 'Previous') {
                return 'Completed';
            } else {
                return 'Pending';
            }
        }


    });
})(window.angular);

//History controller
angular.module('UserApp')
    .controller('History', function ($scope, $http, $filter, historylist) {
        $scope.history = history_c;
        var from_date = $filter('date')(new Date($scope.from_date), 'yyyy-MM-dd');
        var to_date = $filter('date')(new Date($scope.to_date), 'yyyy-MM-dd');
        //historylist.GetList($http,$scope,from_date,to_date);

        $scope.findHistory = function (from_date, to_date) {
            var from_date = $filter('date')(new Date(from_date), 'yyyy-MM-dd');
            var to_date = $filter('date')(new Date(to_date), 'yyyy-MM-dd');
            //$scope.history = [];
            historylist.GetList($http, $scope, from_date, to_date);
        }

    });

//Fav Controller
angular.module('UserApp')
    .controller('Favorite', function ($scope, $http, $filter, historylist) {
        $scope.favorites = favs_c;
        var from_date = $filter('date')(new Date($scope.from_date), 'yyyy-MM-dd');
        var to_date = $filter('date')(new Date($scope.to_date), 'yyyy-MM-dd');
        //historylist.FavList($http,$scope,from_date,to_date);
        $scope.findFavs = function (from_date, to_date) {
            var from_date = $filter('date')(new Date(from_date), 'yyyy-MM-dd');
            var to_date = $filter('date')(new Date(to_date), 'yyyy-MM-dd');
            historylist.FavList($http, $scope, from_date, to_date);
        }
    });

//Vehical Controllers
angular.module('UserApp')
    .controller('Vehicals', function ($scope, $http, vehicleslist) {
        $scope.vehical_sele_type = "";
        $scope.vehicles = vechicals_c;
        //vehicleslist.GetList($http, $scope);
        $scope.vehicles_types = vechicaal_types_c;
        //vehicleslist.GetVehicalsList($http, $scope);
        $scope.changetype = function (type) {
            $scope.vehical_sele_type = type;
        }
        $scope.findVehicles = function () {
            vehicleslist.GetList($http, $scope);
        }


    });

//Toll panes Directive
angular.module('UserApp')
    .directive('tollPanes', function () {
        return {
            require: '^tollTabs',
            restrict: 'E',
            transclude: true,
            scope: {
                title: '@'
            },
            link: function (scope, element, attrs, tabsCtrl) {
                tabsCtrl.addPane(scope);
            },
            templateUrl: 'toll-panes.tpl.html'
        };
    });

//Toll tabs Directive
angular.module('UserApp')
    .directive('tollTabs', function () {
        return {
            restrict: 'E',
            transclude: true,
            scope: {},
            controller: ['$scope', function ($scope) {
                var panes = $scope.panes = [];

                $scope.select = function (pane) {
                    angular.forEach(panes, function (pane) {
                        pane.selected = false;
                    });
                    pane.selected = true;
                };

                this.addPane = function (pane) {
                    if (panes.length === 0) {
                        $scope.select(pane);
                    }
                    panes.push(pane);
                };
            }],
            templateUrl: 'my-tabs.tpl.html',
        };
    });

//History Service
angular.module('UserApp')
    .service('historylist', function () {
        var that = this;
        return {
            GetList: function ($http, $scope, from_date, to_date) {
                var self = this;
                $http.defaults.headers.post["Accept"] = "*/*";
                $http.defaults.headers.post["Content-Type"] = "application/x-www-form-urlencoded";
                $http({
                    url: base_url + 'wua/th/' + user_id,
                    method: "POST",
                    data: $.param({'from_date': from_date, 'to_date': to_date})
                }).then(
                    function (response) {
                        if (response.data.Code == 200) {
                            $scope.history = response.data.Info;
                            console.log($scope.history);
                            var history = response.data.Info;
                            angular.forEach(history, function (value, key) {
                                if (typeof $scope.tripdetails[value.trip_id] == 'undefined') {
                                    self.TripDetailsList($scope, $http, value.trip_id);
                                }

                            });
                        } else {
                            console.log('asd')
                        }


                    },
                    function (response) {

                    }
                );
            },
            FavList: function ($http, $scope, from_date, to_date) {
                var self = this;
                $http.defaults.headers.post["Accept"] = "*/*";
                $http.defaults.headers.post["Content-Type"] = "application/x-www-form-urlencoded";
                $http({
                    url: base_url + 'wua/tf/' + user_id,
                    method: "POST",
                    data: $.param({'from_date': from_date, 'to_date': to_date})
                }).then(
                    function (response) {
                        if (response.data.Code == 200) {
                            $scope.favorites = response.data.Info;
                            var favs = response.data.Info;
                            angular.forEach(favs, function (value, key) {
                                if (typeof $scope.tripdetails[value.trip_id] == 'undefined') {
                                    self.TripDetailsList($scope, $http, value.trip_id);
                                }
                            });
                        }
                    },
                    function (response) {

                    }
                );
            },
            TripDetailsList: function ($scope, $http, trip_id) {
                $http.defaults.headers.post["Accept"] = "*/*";
                $http.defaults.headers.post["Content-Type"] = "application/x-www-form-urlencoded";
                $http({
                    url: base_url + 'wua/tripdetails',
                    method: "POST",
                    data: $.param({'user_id': user_id, 'trip_id': trip_id})
                }).then(
                    function (response) {
                        $scope.tripdetails[trip_id] = response.data.Info.trip_details;
                        //console.log($scope.tripdetails)
                        //return response.data.Info;
                    },
                    function (response) {

                    }
                );
            }

        }

    });

//Vehicle Service
angular.module('UserApp')
    .service('vehicleslist', function () {

        return {
            GetList: function ($http, $scope) {
                $http.defaults.headers.post["Accept"] = "*/*";
                $http.defaults.headers.post["Content-Type"] = "application/x-www-form-urlencoded";
                $http({
                    url: base_url + 'wua/vll/' + user_id,
                    method: "POST",
                    data: $.param({'type': $scope.vehical_sele_type})
                })
                    .then(function (response) {
                            $scope.vehicles = response.data.Info;

                        },
                        function (response) { // optional
                            //console.log(response);
                            // failed
                        });
            },
            GetVehicalsList: function ($http, $scope) {

                $http.defaults.headers.post["Accept"] = "*/*";
                $http.defaults.headers.post["Content-Type"] = "application/x-www-form-urlencoded";
                $http({
                    url: base_url + 'wua/vl',
                    method: "GET"
                })
                    .then(function (response) {
                            $scope.vehicles_types = response.data.items;

                        },
                        function (response) { // optional
                            //console.log(response);
                            // failed
                        });
            }
        }


    });


