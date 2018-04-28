// Définition des routes
siteApp.config(function($routeProvider) {
    //cette variable sert à détecter si c'est le premier chargement ou non.
    var a = true;
    $routeProvider
	.when('/', {
            templateUrl : function(){
                if(a===true){
                    a=false;
                    return 'pages/landing.html';
                } else {
                    return 'pages/home.html';
                }
            },
            controller  : 'mainController'
	})
	.when('/accueil', {
            templateUrl : function(){if(a===true){a=false;return 'pages/landing.html';} else {return 'pages/home.html'}},
            controller  : 'mainController'
                })
	.when('/travaux', {
            templateUrl : function(){
                if(a===true){
                    a=false;
                    return 'pages/landing.html';
                } else {
                    return 'pages/travaux.html'}},
            controller  : 'workController'
	})
  .when('/travaux/:JobID', {
        templateUrl : function(){
            if(a===true){
                a=false;
                return 'pages/landing.html';
            } else {
                return 'pages/untravail.html'}},
        controller  : 'workController'
    })
	.when('/contact', {
            templateUrl : function(){
                if(a===true){
                    a=false;
                    return 'pages/landing.html';
                } else {
                    return 'pages/contact.html'}},
            controller : 'contactController'
	})
		.when('/vr', {
            templateUrl : function(){
                if(a===true){
                    a=false;
                    return 'pages/landing.html';
                } else {
                    return 'pages/vr.html'}},
            controller : 'vrcontroller'
	})
        .otherwise({
            redirectTo: "/"
        });
});


