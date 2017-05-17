'use strict';

angular
  .module('myApp', [
    'ngRoute',
    'myApp.repository',
    'myApp.archives',
    'myApp.list',
    'myApp.upload',
    'lr.upload'
  ])
  .config(['$locationProvider', '$routeProvider', function($locationProvider, $routeProvider) {
    $locationProvider.hashPrefix('!');
    $routeProvider.otherwise({redirectTo: '/archives'});
  }])
;
