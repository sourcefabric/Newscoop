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
        animation: 150
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
     * Removes article from the playlist.
     * Checks if article exist in the list of available articles (on the current page)
     * if it doesn't exist put it at the first position, else don't add it -
     * purpose of it is that this article can be reused again after it will be removed
     * from the list of Featured comments, so no need to search for it again etc.
     *
     * @param  {Object} article Article object
     */
    $scope.removeArticle = function (article) {
        var availablArticles = [],
            featuredArticles = [],
            exists = false,
            isInLogList = false;

        availablArticles = $scope.$parent.tableParams.data;
        featuredArticles = $scope.$parent.featuredArticles;
        exists = _.some(
            availablArticles,
            {number: article.number}
        );

        if (!exists) {
            availablArticles.unshift(article);
        }

        _.remove(
            featuredArticles,
            {number: article.number}
        );

        // check if the article exists in the logList,
        // if user will drag article to the playlist, it will add new field called "_method"
        // set to "link" value. Then if user will remove article from the featured articles' list
        // it should remove it from the logList to not pass fake data to the server, else it will add
        // article to the logList with field "_method": "unlink" so the article can be unlinked from
        // the playlist.
        isInLogList = _.some(
            Playlist.getLogList(),
            {number: article.number, _method: 'link'}
        );

        if (!isInLogList) {
            // set method for the removed object so we can pass it to
            // the API endpoint and remove item using batch remove feature
            item._method = "unlink";
            Playlist.addItemToLogList(item);
        } else {
            Playlist.removeItemFromLogList(article.number, 'link');
        }

        Playlist.setCurrentPlaylistArticles(featuredArticles);
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

    // array of the articles,
    // that will be removed or added to the list
    $scope.logList = [];

    $scope.sortableConfig = {
        group: 'articles',
        animation: 150,
        onEnd: function (evt/**Event*/){
            var item,
                number,
                occurences,
                isInLogList = false;

            item = evt.model; // the current dragged article
            number = item.number;
            occurences = 0;
            angular.forEach($scope.featuredArticles, function(value, key) {
                if (value.number == number) {
                    occurences++;
                }
            });

            if (occurences != 1) {
                $scope.featuredArticles.splice(evt.newIndex, 1);
                flashMessage(Translator.trans('Item already exists in the list'), 'error');

                return true;
            }

            isInLogList = _.some(
                Playlist.getLogList(),
                {number: number, _method: 'unlink'}
            );

            if (!isInLogList) {
                // add article to log list, so we can save it later using batch save
                item._method = "link";
                Playlist.addItemToLogList(item);
            } else {
                Playlist.removeItemFromLogList(number, 'unlink');
            }
        }
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
        var exists = _.some(
            $scope.featuredArticles,
            {number: $scope.articlePreview.number}
        );

        if (!exists) {
            $scope.featuredArticles.unshift($scope.articlePreview);
            $scope.isViewing = false;
        } else {
            flashMessage(Translator.trans('Item already exists in the list'), 'error');
        }
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
        var listname = scope.formData.title,
            logList = [];
        var playlistExists = _.some(
            $scope.playlists,
            {title: listname}
        );

        if (!playlistExists) {
            $scope.playlists.push({title: listname});
            $scope.playlist.selected = $scope.playlists[$scope.playlists.length - 1];
        }

        logList = Playlist.getLogList();
        if (logList.length == 0) {
            flashMessage(Translator.trans('Nothing to save'));

            return true;
        }

        Playlist.batchUpdate(logList)
        .then(function () {
            flashMessage(Translator.trans('List saved'));
            logList = [];
        }, function() {
            flashMessage(Translator.trans('Could not save the list'), 'error');
        });
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