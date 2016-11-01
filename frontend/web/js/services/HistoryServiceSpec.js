describe('Service: UserApp.History', function () {

    // load the service's module
    beforeEach(module('UserApp'));

    // instantiate service
    var service;

    //update the injection
    beforeEach(inject(function (History) {
        service = History;
    }));

    /**
     * @description
     * Sample test case to check if the service is injected properly
     * */
    it('should be injected and defined', function () {
        expect(service).toBeDefined();
    });
});
