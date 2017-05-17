'use strict';

// main page, will just display upload and list components

angular.module('myApp.archives', ['ngRoute'])

.config(['$routeProvider', function($routeProvider) {
  $routeProvider.when('/archives', {
    templateUrl: 'archives/archives.html',
    controller: 'ArchivesCtrl'
  });
}])

.controller('ArchivesCtrl', [function() {

}]);
