'use strict';

describe('myApp.repository module', function() {

  var anyApiRoot = 'any api root';
  var anyApiPaths = {
    archive: 'any archive path'
  };
  var anyObject = {any: 'object'};
  var anyId = 345;

  beforeEach(module('myApp.repository'));

  describe('archiveRepository service', function() {

    var $http, archiveRepository;

    beforeEach(module(function($provide) {
      $provide.value('apiRoot', anyApiRoot);
      $provide.value('apiPaths', anyApiPaths);
    }));
    beforeEach(inject(function(_$http_, _archiveRepository_) {
      $http = _$http_;
      archiveRepository = _archiveRepository_;
    }));

    it('.list should proxy and return promise', function() {

      spyOn($http, 'get').and.returnValue(anyObject);

      var result = archiveRepository.list();

      expect($http.get).toHaveBeenCalledWith(anyApiRoot + anyApiPaths.archive);
      expect(result).toEqual(anyObject);

    });

    it('.getUploadUrl should return url', function() {

      var result = archiveRepository.getUploadUrl();
      expect(result).toEqual(anyApiRoot + anyApiPaths.archive);

    });

    it('.getViewUrl should return url', function() {

      var result = archiveRepository.getViewUrl(anyId);

      expect(result).toEqual(anyApiRoot + anyApiPaths.archive + anyId);

    });

    it('.deleteById should proxy and return promise', function() {

      spyOn($http, 'delete').and.returnValue(anyObject);
      var expectedUrl = anyApiRoot + anyApiPaths.archive + anyId;

      var result = archiveRepository.deleteById(anyId);

      expect($http.delete).toHaveBeenCalledWith(expectedUrl);
      expect(result).toEqual(anyObject);

    });

  })

});
