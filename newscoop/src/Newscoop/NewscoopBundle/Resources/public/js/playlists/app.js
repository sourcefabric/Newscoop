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

app.controller('FeaturedController', [
    '$scope',
    'Playlist',
    function (
        $scope,
        Playlist
    ) {

    $scope.sortableConfig = {
        group: 'articles',
        animation: 150,
        onEnd: function (evt/**Event*/){
            /*var item = evt.model; // the current dragged article
            var number = item.number;

            // TODO dont add article if its already in the playlist
            var result = _.some($scope.$parent.featuredArticles, {'number': number.toString()});*/
            console.log(evt);

        }
    };



    /**
     * Deletes single article from the currently loaded playlist
     *
     * @param  {Object} article Article object
     */
    $scope.deleteSingleArticle = function (article) {
        Playlist.removeSingleArticle(article.number, article.language)
        .then(function () {
            _.remove(
                $scope.$parent.featuredArticles,
                {number: article.number}
            );
            flashMessage(Translator.trans('List updated.'));
            Playlist.setCurrentPlaylistArticles($scope.$parent.featuredArticles);
        });
    };

    /**
     * Removes article from the playlist
     *
     * @param  {Object} article Article object
     */
    $scope.removeArticle = function (article) {
        _.remove(
            $scope.$parent.featuredArticles,
            {number: article.number}
        );

        Playlist.setCurrentPlaylistArticles($scope.$parent.featuredArticles);
    }
}]);

app.controller('PlaylistsController', [
    '$scope',
    'Playlist',
    'ngTableParams',
    function (
        $scope,
        Playlist,
        ngTableParams
    ) {

    $scope.isViewing = false;
    $scope.playlist = {};
    $scope.playlists = Playlist.getAll();
    $scope.playlistInfo = undefined;
    $scope.featuredArticles = [];
    $scope.formData = {};

    $scope.sortableConfig = {
        group: 'articles',
        animation: 150
    };

    $scope.tableParams = new ngTableParams({
        page: 1, // show first page
        count: 5 // count per page
    }, {
        total: 0,// length of data
        counts: [], // disable page sizes
        getData: function($defer, params) {
            var filters = params.filter();
            params.$params.query = filters.query;
            Playlist.getAllArticles($defer, params);
        }
    });

    /**
     * Opens selected article's preview
     *
     * @param  {Object} article Seected article
     */
    $scope.viewArticle = function (article) {
        $scope.isViewing = true;
        $scope.articlePreview = article;
    };

    /**
     * Closes article's preview window
     */
    $scope.closeViewArticle = function () {
        $scope.isViewing = false;
    };

    /**
     * Adds article to the playlist, which is currently in preview mode
     */
    $scope.addArticleToListFromPreview = function () {
        $scope.featuredArticles.unshift($scope.articlePreview);
        $scope.isViewing = false;
    };

    /**
     * Sets playlist details to the current scope.
     *
     * It loads playlist details on select2 change.
     *
     * @param  {Object} list  Playlist
     */
    $scope.setPlaylistInfoOnChange = function (list) {
        $scope.featuredArticles = Playlist.getArticlesByListId(list.id);
        Playlist.setListId(list.id);
        $scope.playlistInfo = list;
        $scope.formData = {title: list.title}
    };

    /**
     * Saves playlist with all articles in it.
     * It makes batch link or unlink of the articles. It also
     * saves a proper order of the articles. All list's changes are saved
     * by clicking Save button.
     *
     * @param  {Object} scope Current scope
     */
    $scope.savePlaylist = function (scope) {
        var listname = scope.formData.title;
        $scope.playlists.push({title: listname});
        $scope.playlist.selected = $scope.playlists[$scope.playlists.length - 1];

    }

    /**
     * Adds new playlist. Sets default list name to current date.
     */
    $scope.addNewPlaylist = function () {
        var defaultListName,
            currentDate = new Date();

        defaultListName = currentDate.toString();
        Playlist.setCurrentPlaylistArticles([]);
        $scope.featuredArticles = Playlist.getCurrentPlaylistArticles();
        $scope.formData.title = defaultListName;

        if ($scope.playlist.selected !== undefined) {
            $scope.playlistInfo.id = undefined;
            $scope.playlist.selected = undefined;
        } else {
            $scope.playlistInfo = {title: defaultListName, id: undefined};
        }
    }
}]);
})();