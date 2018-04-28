// Déclaration du module principal avec ngRoute
var siteApp = angular.module('siteApp', [
    'ngRoute',
    'ui.mask',
    'ngSanitize'
]).run(function($rootScope) {
    $rootScope.data = [];
});

// Configuration du provider
siteApp.config(['$locationProvider', function($locationProvider) {
  $locationProvider.hashPrefix('');
}]);

