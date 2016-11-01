/**
 * @ngdoc directive
 * @name TollTabs
 *
 * @description
 * _Please update the description and restriction._
 *
 * @restrict A
 * */
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
