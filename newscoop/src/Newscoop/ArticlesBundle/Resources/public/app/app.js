(function() {
'use strict';
var app = angular.module('editorialCommentsApp', ['ngActivityIndicator', 'angularMoment', 'infinite-scroll', 'ngRoute'])
  .config(function($interpolateProvider, $routeProvider) {
    $routeProvider
        .otherwise({
            templateUrl: '../../bundles/newscooparticles/views/main.html',
            controller: 'EditorialCommentsCtrl'
        });

      $interpolateProvider.startSymbol('{[{').endSymbol('}]}');
});
})();