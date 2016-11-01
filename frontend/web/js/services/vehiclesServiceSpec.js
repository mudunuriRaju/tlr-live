describe('Service: UserApp.vehicles', function () {

    // load the service's module
    beforeEach(module('UserApp'));

    // instantiate service
    var service;

    //update the injection
    beforeEach(inject(function (vehicles) {
        service = vehicles;
    }));

    /**
     * @description
     * Sample test case to check if the service is injected properly
     * */
    it('should be injected and defined', function () {
        expect(service).toBeDefined();
    });
});
