(function() {
'use strict';
var app = angular.module('playlistsApp', ['ngSanitize', 'ui.select', 'ngTable', 'ng-sortable'])
  .config(function($interpolateProvider, $httpProvider) {
      $interpolateProvider.startSymbol('{[{').endSymbol('}]}');
      $httpProvider.interceptors.push('authInterceptor');
});

app.directive('loadingContainer', function () {
    return {
        restrict: 'A',
        scope: false,
        link: function(scope, element, attrs) {
            var loadingLayer = angular.element('<div class="loading"></div>');
            element.append(loadingLayer);
            element.addClass('loading-container');
            scope.$watch(attrs.loadingContainer, function(value) {
                loadingLayer.toggleClass('ng-hide', !value);
            });
        }
    };
});

/**
 * AngularJS default filter with the following expression:
 * "playlist in playlists | filter: {name: $select.search, age: $select.search}"
 * performs a AND between 'name: $select.search' and 'age: $select.search'.
 * We want to perform a OR.
 */
app.filter('listsFilter', function() {
  return function(items, props) {
    var out = [];

    if (angular.isArray(items)) {
      items.forEach(function(item) {
        var itemMatches = false;

        var keys = Object.keys(props);
        for (var i = 0; i < keys.length; i++) {
          var prop = keys[i];
          var text = props[prop].toLowerCase();
          if (item[prop].toString().toLowerCase().indexOf(text) !== -1) {
            itemMatches = true;
            break;
          }
        }

        if (itemMatches) {
          out.push(item);
        }
      });
    } else {
      // Let the output be the input untouched
      out = items;
    }

    return out;
  }
});

/**
 * @ngdoc function
 * @name playlistsApp.controller:PlaylistCtrl
 * @description
 * # PlaylistCtrl
 * Controller of the playlistsApp
 */
app.controller('PlaylistCtrl', function ($scope, Playlist, ngTableParams, $timeout, $http) {
    $scope.playlist = {};
    $scope.playlists = []; //Playlist.getAll();

    $scope.sortableConfig = {
        group: 'articles',
        animation: 150
    };

    $scope.tableParams = new ngTableParams({
        page: 1, // show first page
        count: 5, // count per page
        itemsPerPage: 5
    }, {
        total: 0,// length of data
        counts: [], // disable page sizes
        getData: function($defer, params) {
            var filters = params.filter();
            params.$params.query = filters.query;
            Playlist.getAllArticles($defer, params);
        }
    });
}).controller('FeaturedController', ['$scope', function ($scope) {
    // TODO - this array should be filled with data taken from the Playlists API
    $scope.articles = [
        {title: 'learn angular', status: "Y", type: "news", created: "2014-02-19T15:48:13+0100"},
        {title: 'build an angular app', status: "Y", type: "news", created: "2010-12-23T15:48:13+0100"}
    ];
    $scope.sortableConfig = { group: 'articles', animation: 150 };

    }]);
})();