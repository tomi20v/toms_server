'use strict';

describe('myApp.upload module', function() {

  beforeEach(module('myApp.repository'));
  beforeEach(module('myApp.upload'));

  var anyUrl = 'any url';

  describe('upload controller', function() {

    var $controller, $rootScope;
    var UploadCtrl;
    beforeEach(inject(function(_$controller_, _$rootScope_) {
      $controller = _$controller_;
      $rootScope = _$rootScope_;
      UploadCtrl = $controller('UploadCtrl');
    }));

    it('on init', function() {

      expect(UploadCtrl.uploading).toBeFalsy();
      expect(UploadCtrl.error).toBeFalsy();

    });

    it('.getUploadUrl should proxy', inject(function(archiveRepository) {

      spyOn(archiveRepository, 'getUploadUrl').and.returnValue(anyUrl);

      var result = UploadCtrl.getUploadUrl();
      expect(archiveRepository.getUploadUrl).toHaveBeenCalled();
      expect(result).toEqual(anyUrl);

    }));

    it('.onUpload should set flags', function() {

      UploadCtrl.onUpload();

      expect(UploadCtrl.uploading).toBeTruthy();
      expect(UploadCtrl.success).toBeFalsy();
      expect(UploadCtrl.error).toBeFalsy();

    });

    describe('.onSuccess', function() {

      it('should set flags', function() {
        UploadCtrl.onSuccess();
        expect(UploadCtrl.uploading).toBeFalsy();
        expect(UploadCtrl.success).toBeTruthy();
      });

      it('should emit reload', function()  {

        spyOn($rootScope, '$emit');

        UploadCtrl.onSuccess();

        expect($rootScope.$emit).toHaveBeenCalled();

      });

    });

    it('.onError should set flags', function() {

      UploadCtrl.onError();

      expect(UploadCtrl.uploading).toBeFalsy();
      expect(UploadCtrl.error).toBeTruthy();

    })

  })

});
