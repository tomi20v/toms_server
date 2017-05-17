'use strict';

describe('myApp.list module', function() {

  beforeEach(module('myApp.repository'));
  beforeEach(module('myApp.list'));

  var timeoutMock = function() {};
  var anyUrl = 'any url';
  var anyId = 234;
  var anyData = {data: {any: 'data'}};
  var anyArchive = { id: anyId };

  describe('list controller', function(){

    describe('on init', function() {

      var $controller, $scope;
      beforeEach(inject(function(_$controller_, $rootScope) {
        $controller = _$controller_;
        $scope = $rootScope.$new();
      }));

      it('should initialize', function() {

        var ListCtrl = $controller('ListCtrl');

        expect(ListCtrl).toBeDefined();
        expect(ListCtrl.loading).toBeTruthy();
        expect(ListCtrl.error).toBeFalsy();
        expect(ListCtrl.archives).toEqual([]);

      });

      it('should refresh on load', function() {

        var timeoutCallback = function() {};
        var t = {
          timeout: function(callback) {
            timeoutCallback = callback;
          }
        };
        spyOn(t, 'timeout').and.callThrough();

        var ListCtrl = $controller('ListCtrl', {
          $timeout: t.timeout,
          $scope: $scope
        });

        expect(t.timeout).toHaveBeenCalled();

        spyOn(ListCtrl, 'reload');

        expect(ListCtrl.reload).not.toHaveBeenCalled();

        timeoutCallback();

        expect(ListCtrl.reload).toHaveBeenCalled();

      });

    });

    describe('on list.reload event', function() {

      it('should reload on event', inject(function($controller, $rootScope) {

        var $scope = $rootScope.$new();

        var ListCtrl = $controller('ListCtrl', {
          $timeout: timeoutMock,
          $scope: $scope
        });

        spyOn(ListCtrl, 'reload');

        expect(ListCtrl.reload).not.toHaveBeenCalled();

        $rootScope.$emit('list.reload');

        expect(ListCtrl.reload).toHaveBeenCalled();

      }));

    });

    describe('.isEmpty', function() {

      var ListCtrl;
      beforeEach(inject(function($controller) {
        ListCtrl = $controller('ListCtrl');
      }));

      it('should return true when items empty', function() {
        ListCtrl.archives = [];
        expect(ListCtrl.isEmpty()).toBeTruthy();
      });

      it('should return false when items not empty', function() {
        ListCtrl.archives = [1,2,3];
        expect(ListCtrl.isEmpty()).toBeFalsy();
      });

    });

    describe('.reload', function() {

      var $scope, archiveRepository, ListCtrl, deferred;

      beforeEach(inject(function($q, $controller, $rootScope, _archiveRepository_) {

        $scope = $rootScope.$new();
        archiveRepository = _archiveRepository_;

        deferred = $q.defer();
        spyOn(archiveRepository, 'list').and.returnValue(deferred.promise);

        ListCtrl = $controller('ListCtrl', {
          archiveRepository: archiveRepository,
          $scope: $scope
        });
      }));

      it('should set flag when loading', function() {

        ListCtrl.loading = false;
        ListCtrl.reload();

        expect(ListCtrl.loading).toBeTruthy();

      });

      it('should set flags after loading, on success', function() {

        ListCtrl.reload();
        deferred.resolve(anyData);
        $scope.$apply();

        expect(ListCtrl.archives).toEqual(anyData.data);
        expect(ListCtrl.loading).toBeFalsy();
        expect(ListCtrl.error).toBeFalsy();

      });

      it('should set flags after loading, on error', function() {

        ListCtrl.reload();
        deferred.reject();
        $scope.$apply();

        expect(ListCtrl.archives).toEqual([]);
        expect(ListCtrl.loading).toBeFalsy();
        expect(ListCtrl.error).toBeTruthy();

      });

    });

    describe('.viewUrl', function() {

      var archiveRepository, ListCtrl;
      beforeEach(inject(function($controller, _archiveRepository_) {
        archiveRepository = _archiveRepository_;
        ListCtrl = $controller('ListCtrl', {
          $timeout: timeoutMock,
          archiveRepository: archiveRepository
        });

      }));

      it('should proxy', function() {

        spyOn(archiveRepository, 'getViewUrl').and.returnValue(anyUrl);

        var result = ListCtrl.viewUrl(anyId);
        expect(result).toEqual(anyUrl);
        expect(archiveRepository.getViewUrl).toHaveBeenCalled();

      });

    });

    describe('.delete', function() {

      var $scope, archiveRepository, ListCtrl, deferred;

      beforeEach(inject(function(_$q_, $rootScope, $controller, _archiveRepository_) {

        deferred = _$q_.defer();

        archiveRepository = _archiveRepository_;
        spyOn(archiveRepository, 'deleteById').and.returnValue(deferred.promise);

        $scope = $rootScope.$new();

        ListCtrl = $controller('ListCtrl', {
          archiveRepository: archiveRepository,
          $scope: $scope
        });
        spyOn(ListCtrl, 'reload');

      }));

      it('should call on repository', function() {

        ListCtrl.delete(anyArchive);

        expect(archiveRepository.deleteById).toHaveBeenCalledWith(anyId);

      });

      it('should refresh on success', function() {

        ListCtrl.delete(anyArchive);

        deferred.$$resolve();
        $scope.$apply();

        expect(ListCtrl.reload).toHaveBeenCalled();

      });

      it('should not refresh on reject', function() {

        ListCtrl.delete(anyArchive);

        deferred.$$reject();
        $scope.$apply();

        expect(ListCtrl.reload).not.toHaveBeenCalled();

      });

    });

  });

});
