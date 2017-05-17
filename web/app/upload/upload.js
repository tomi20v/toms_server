'use strict';

angular
  .module('myApp.upload', [])
  .controller('UploadCtrl', ['$rootScope', 'archiveRepository', function($rootScope, archiveRepository) {
    var vm = this;
    angular.extend(vm, {
      uploading: false,
      error: false,
      getUploadUrl: function() {
        return archiveRepository.getUploadUrl();
      },
      onUpload: function() {
        vm.uploading = true;
        vm.success = false;
        vm.error = false;
      },
      onSuccess: function() {
        vm.uploading = false;
        vm.success = true;
        $rootScope.$emit('list.reload');
      },
      onError: function() {
        vm.uploading = false;
        vm.error = true;
      }
    })
  }]);
