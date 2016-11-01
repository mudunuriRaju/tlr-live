/**
 * @ngdoc controller
 * @name Favorite
 *
 * @description
 * _Please update the description and dependencies._
 *
 * @requires $scope
 * */
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
