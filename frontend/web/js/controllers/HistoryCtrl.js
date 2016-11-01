/**
 * @ngdoc controller
 * @name History
 *
 * @description
 * _Please update the description and dependencies._
 *
 * @requires $scope
 * */
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
