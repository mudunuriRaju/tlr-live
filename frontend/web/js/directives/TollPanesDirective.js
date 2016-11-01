/**
 * @ngdoc directive
 * @name TollPanes
 *
 * @description
 * _Please update the description and restriction._
 *
 * @restrict A
 * */
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
