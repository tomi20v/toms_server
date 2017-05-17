'use strict';

angular
  .module('myApp.repository', [])
  .value('apiRoot', 'http://localhost:8000')
  .value('apiPaths', {
    archive: '/api/archive/'
  })
  .service('archiveRepository', ['$http', 'apiRoot', 'apiPaths', function($http, apiRoot, apiPaths) {
    return {
      list: function() {
        return $http.get(apiRoot + apiPaths.archive);
      },
      getUploadUrl: function() {
        return apiRoot + apiPaths.archive;
      },
      getViewUrl: function(id) {
        return apiRoot + apiPaths.archive + id;
      },
      deleteById: function(id) {
        return $http.delete(apiRoot + apiPaths.archive + id);
      }
    }
  }])
;
