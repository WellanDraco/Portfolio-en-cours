// création des contrôleurs
console.log("Ha les framework js... Ils génèrent parfois des erreurs 404 quand les url ne sont pas réécrites au moment de parser le document. Donc si vous en voyez, n'y prétez pas attention svp");

siteApp.controller('mainController', function($rootScope, $scope, $timeout, $http, $sce) {
	$rootScope.visit = 0;
	$scope.test = 'Main';
	// code pour l'affichage du texte principal
	$scope.message = "- Tu as déjà touché à cette techno ? - Non. C'est pas grave, on va apprendre !";
	const string = $scope.message; /* type your text here */
	const array = string.split("");
	$scope.message = "";
	$scope.presentation = "";

	(function frameLooper() {
		if (array.length > 0) {
			$scope.message += array.shift();
			$timeout(frameLooper, 100); /* change 70 for speed */
		}
	}());


	//code pour la persentation personnelle
	//https://back.arthur-moug.in/wp-json/wp/v2/pages/5
	$http({
			method: 'GET',
			url: "https://back.arthur-moug.in/wp-json/wp/v2/pages/"
		})
		.then(function successCallback(response) {
				$scope.presentation = $sce.trustAsHtml(response.data[0].content.rendered);

			},
			function errorCallback(response) {
				console.log('erreur ajax 4 :');
				console.log( response);
			});


	//code pour les differents travaux
	$http({
			method: 'GET',
			url: "https://back.arthur-moug.in/wp-json/wp/v2/travaux/"
		})
		.then(function successCallback(response) {
				//fonction pour tout trier
				var sort_by = function(field, reverse, primer) {

					var key = primer ?
						function(x) {
							return primer(x[field]);
						} :
						function(x) {
							return x[field];
						};

					reverse = !reverse ? 1 : -1;

					return function(a, b) {
						return a = key(a), b = key(b), reverse * ((a > b) - (b > a));
					};
				};

				$scope.data = response.data;
				$scope.list = [];

				$scope.data.sort(sort_by('date_gmt', true, function(a) {
					return a.toUpperCase();
				}));

				for (var i = 0; i < $scope.data.length; i++) {
					$scope.adress = 'https://back.arthur-moug.in/wp-json/wp/v2/media/' + $scope.data[i].featured_media;
					$http({
							method: 'GET',
							url: $scope.adress
						})
						.then(function successCallback(response1) {
								$scope.thumbnailimg = response1.data.media_details.sizes.thumbnail.source_url;
								for (var j = 0; j < $scope.data.length; j++) {
									if ($scope.data[j].featured_media === response1.data.id) {
										$scope.data[j]['thumbnail'] = response1.data.media_details.sizes.thumbnail.source_url;
									}
								}
							},
							function errorCallback(response1) {
												console.log('erreur ajax 3 :');
				console.log( response);
							});

					if (i < 3) {
						$scope.list.push($scope.data[i]);
					}
				}




			},
			function errorCallback(response) {
								console.log('erreur ajax 4 :');
				console.log( response);
			});



});

siteApp.controller('workController', function($scope, $routeParams, $location, $http) {
	$scope.message = 'Page About';
	$scope.data = [];
	$scope.contenu = [];

	$http({
			method: 'GET',
			url: "https://back.arthur-moug.in/wp-json/wp/v2/travaux/"
		})
		.then(function successCallback(response) {
				$scope.data = response.data;

				if ($location.path() === "/travaux") {
					for (var i = 0; i < $scope.data.length; i++) {

						$scope.adress = 'https://back.arthur-moug.in/wp-json/wp/v2/media/' + $scope.data[i].featured_media;
						$http({
							method: 'GET',
							url: $scope.adress
						}).then(function successCallback(response1) {
							$scope.thumbnailimg = response1.data.media_details.sizes.thumbnail.source_url;

							for (var j = 0; j < $scope.data.length; j++) {
								if ($scope.data[j].featured_media === response1.data.id) {
									$scope.data[j]['thumbnail'] = response1.data.media_details.sizes.thumbnail.source_url;

								}

							}


						}, function errorCallback(response1) {
											console.log('erreur ajax 5 :');
				console.log( response);
						});
					};
					$scope.list = $scope.data;
				}

				if ($location.path() === "/travaux/" + $routeParams.JobID) {
					$scope.JobID = $routeParams.JobID;
					for (var i = 0; i < $scope.data.length; i++) {
						if ($scope.data[i].id === Number($scope.JobID)) {
							$scope.thisdata = $scope.data[i];
						}
					}

					$scope.JobTitle = $scope.thisdata.title.rendered;
					$scope.exerpt = $scope.thisdata.excerpt.rendered;
					$scope.content = $scope.thisdata.content.rendered;

					$scope.thumbnail = 'https://back.arthur-moug.in/wp-json/wp/v2/media/' + $scope.thisdata.featured_media;

					$http({
						method: 'GET',
						url: $scope.thumbnail
					}).then(function successCallback(response1) {
						$scope.thumbnailimg = response1.data;
					}, function errorCallback(response1) {
										console.log('erreur ajax 1 :');
				console.log( response);
					});
				}

			},
			function errorCallback(response) {
								console.log('erreur ajax 2 :');
				console.log( response);
			});

});

siteApp.controller('contactController', function($scope) {
	$scope.message = 'Contactez nous !';
	$scope.hover = '';
	$scope.hover1 = '';
	$scope.hover2 = '';
	$scope.hover3 = '';
	$scope.show = '';

	$scope.telephone = '4185621240';
	$scope.mail = 'contact@arthur-moug.in';
	$scope.fb = 'www.facebook.com/arthur.mougin.1';
	$scope.in = 'www.linkedin.com/in/arthur-mougin';
});

siteApp.controller('LandingController', function($scope, $location, $http) {
	$scope.test = 'landing';
	$scope.next = $location.path().replace('/', '#');



	//code pour la notification
	var text = '';

	function envoisNotif(text) {
		notif = encodeURIComponent(text);
		var xhttp = new XMLHttpRequest();

		xhttp.open("GET", "location.php?notif=" + notif, true);
		xhttp.send();
	};


	$http({
			method: 'GET',
			url: "https://freegeoip.net/json/"
		})
		.then(function successCallback(response) {
				var data = response.data;
				text = 'Bonsoir tout seul, il y a un habitant du ' + data.country_name + ' qui visite le portfolio.';
				if (data.region_name !== '') {
					text = text + ' Il viens plus précisément du ' + data.region_name + '.';
				};
				envoisNotif(text);
			},
			function errorCallback() {
				text = 'quelqu\'un visite ton site de manière cachée...';
				envoisNotif(text);
			});
});
