'use strict';

// list component will load and display list of archives, with options
//  to view and delete
// will show messages for loading, load error, and if list is empty
// can be refreshed by 'list.reload' event on $rootScope

angular
  .module('myApp.list', [])
  .controller('ListCtrl', [
    '$rootScope', '$timeout', 'archiveRepository',
    function($rootScope, $timeout, archiveRepository) {

      var vm = this;

      angular.extend(vm, {
        loading: true,
        error: false,
        archives: [],
        isEmpty: function() {
          return this.archives.length == 0;
        },
        reload: function() {
          vm.loading = true;
          archiveRepository.list()
            .then(
              function(response) {
                vm.archives = response.data || [];
                vm.loading = false;
                vm.error = false;
              },
              function() {
                vm.archives = [];
                vm.loading = false;
                vm.error = true;
              }
            );

        },
        viewUrl: function(archive) {
          return archiveRepository.getViewUrl(archive.id);
        },
        delete: function(archive) {
          archiveRepository
            .deleteById(archive.id)
            .then(angular.bind(vm, vm.reload));
        }
      });

      // I wrap so it's more testable
      var boundReload = function() {
        vm.reload();
      };

      $timeout(boundReload);
      $rootScope.$on('list.reload', boundReload);

    }
  ])
;
