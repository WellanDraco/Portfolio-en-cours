// Déclaration du module principal avec ngRoute
var siteApp = angular.module('siteApp', ['ngRoute']);

// Configuration du provider
siteApp.config(['$locationProvider', function($locationProvider) {
  $locationProvider.hashPrefix('');
}]);

