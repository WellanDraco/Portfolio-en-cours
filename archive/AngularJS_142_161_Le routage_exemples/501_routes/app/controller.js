
	// création des contrôleurs 
	siteApp.controller('mainController', function($scope) {
		// Message de la vue
		$scope.message = 'Bienvenue sur note site!';
	});

	siteApp.controller('aboutController', function($scope) {
		$scope.message = 'Page About';
	});

	siteApp.controller('contactController', function($scope) {
		$scope.message = 'Contactez nous !';
	});
