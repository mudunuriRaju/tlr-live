/**
 * @ngdoc controller
 * @name Vehicals
 *
 * @description
 * _Please update the description and dependencies._
 *
 * @requires $scope
 * */
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
