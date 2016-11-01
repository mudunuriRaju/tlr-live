/**
 * @ngdoc service
 * @name vehicles
 * @description
 * _Please update the description and dependencies._
 *
 * */
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

