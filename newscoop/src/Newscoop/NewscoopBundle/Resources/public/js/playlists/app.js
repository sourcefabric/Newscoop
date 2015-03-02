(function() {
'use strict';
var app = angular.module('playlistsApp', [
    'ngSanitize',
    'ui.select',
    'ngTable',
    'ng-sortable',
    'ui.bootstrap',
    'infinite-scroll',
    'angularMoment'
]).config(function($interpolateProvider, $httpProvider) {
    $interpolateProvider.startSymbol('{[{').endSymbol('}]}');
    $httpProvider.interceptors.push('authInterceptor');
});
})();