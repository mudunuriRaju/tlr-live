/**
 * @ngdoc service
 * @name History
 * @description
 * _Please update the description and dependencies._
 *
 * */
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

