describe('Controller: Tollr.Consess', function () {

    // load the controller's module
    beforeEach(module('Tollr'));

    var ctrl,
        scope;

    // Initialize the controller and a mock scope
    beforeEach(inject(function ($controller, $rootScope) {
        scope = $rootScope.$new();
        ctrl = $controller('Consess', {
            $scope: scope
        });
    }));

    it('should be defined', function () {
        expect(ctrl).toBeDefined();
    });
});
