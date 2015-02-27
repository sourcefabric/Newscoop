'use strict';

/**
* AngularJS controller for managing playlists
*
* @class PalylistsController
*/
angular.module('playlistsApp').controller('PlaylistsController', [
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
    $scope.processing = false;
    $scope.playlist.selected = undefined;

    // array of the articles,
    // that will be removed or added to the list
    $scope.logList = [];

    $scope.sortableConfig = {
        group: {
            name: 'articles',
            pull: 'clone',
            put: false
        },
        sort: false,
        animation: 150,
        handle: ".move-elements",
        filter: ".ignore-elements",
        onEnd: function (evt/**Event*/){
            var item,
                number,
                occurences,
                isInLogList = false,
                limit;
                console.log(evt);
            item = evt.model; // the current dragged article
            number = item.number;
            occurences = 0;
            angular.forEach($scope.featuredArticles, function(value, key) {
                if (value.number == number) {
                    occurences++;
                }
            });

            if (occurences > 1) {
                $scope.featuredArticles.splice(evt.newIndex, 1);
                flashMessage(Translator.trans('Item already exists in the list'), 'error');

                return true;
            }

            limit = $scope.playlist.selected.maxItems;
            if (limit && limit != 0 && $scope.featuredArticles.length > limit) {
                $scope.featuredArticles.splice(evt.newIndex, 1);
                flashMessage(Translator.trans(
                    'List limit reached! Remove some articles from the list before adding new ones.'
                ), 'error');

                return true;
            }

            isInLogList = _.some(
                Playlist.getLogList(),
                {number: number, _method: 'unlink'}
            );

            if (!isInLogList) {
                // this check prevents inserting duplicate article
                // when it's drag from the available articles list.
                // onSort event is executed before onEnd so in both of them it will
                // insert the same value to the log list
                isInLogList = _.some(
                    Playlist.getLogList(),
                    {number: number}
                );

                if (!isInLogList) {
                    // add article to log list, so we can save it later using batch save
                    item._method = "link";
                    item._order = evt.newIndex + 1;
                    Playlist.addItemToLogList(item);
                }
            } else {
                Playlist.removeItemFromLogList(number, 'unlink');
            }
        }
    };

    $scope.tableParams = new ngTableParams({
        //page: 1, // show first page
        count: 10 // count per page
    }, {
        total: 0,// length of data
        counts: [], // disable page sizes
        getData: function($defer, params) {
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
            var isInLogList = _.some(
                Playlist.getLogList(),
                {number: $scope.articlePreview.number}
            );

            $scope.articlePreview._method = "link";
            $scope.articlePreview._order = 1;
            Playlist.addItemToLogList($scope.articlePreview);
            $scope.featuredArticles.unshift($scope.articlePreview);
            $scope.isViewing = false;
        } else {
            flashMessage(Translator.trans('Item already exists in the list'), 'error');
        }
    };

    /**
     * Adds article to the playlist from the editor mode
     */
    $scope.addArticleToListFromEditor = function (number, language) {
        var exists = _.some(
            $scope.featuredArticles,
            {number: number}
        );

        if (!exists) {
            var article = undefined,
                isInLogList;
            $scope.processing = true;
            isInLogList = _.some(
                Playlist.getLogList(),
                {number: number}
            );

            if (!isInLogList) {
                Playlist.getArticle(number, language).then(function (article) {
                    article._method = "link";
                    article._order = 1;
                    Playlist.addItemToLogList(article);
                    $scope.featuredArticles.unshift(article);
                    $scope.processing = false;
                }, function() {
                    flashMessage(Translator.trans('Error List'), 'error');
                });

                return true;
            }
        }

        flashMessage(Translator.trans('Item already exists in the list'), 'error');
    };

    $scope.savePlaylistInEditorMode = function () {
        var logList = [];
        $scope.processing = true;
        logList = Playlist.getLogList();
        if (logList.length == 0) {
            flashMessage(Translator.trans('List saved'));
            $scope.processing = false;

            return true;
        }

        Playlist.batchUpdate(logList)
        .then(function () {
            flashMessage(Translator.trans('List saved'));
            Playlist.clearLogList();
            $scope.processing = false;
        }, function() {
            flashMessage(Translator.trans('Could not save the list'), 'error');
        });
    }

    var page = 2,
        isRunning = false,
        isEmpty = false;

    /**
     * Sets playlist details to the current scope.
     *
     * It loads playlist details on select2 change.
     *
     * @param  {Object} list  Playlist
     */
    $scope.setPlaylistInfoOnChange = function (list) {
        $scope.featuredArticles = Playlist.getArticlesByListId(list);
        Playlist.setListId(list.id);
        $scope.playlistInfo = list;
        $scope.playlist.selected.oldLimit = list.maxItems;
        $scope.formData = {title: list.title}
        page = 2;
        isRunning = false;
    };

    /**
     * Loads more playlist's articles on scroll
     */
    $scope.loadArticlesOnScrollDown = function () {
        if ($scope.playlist.selected) {
            if (!isEmpty && !isRunning) {
                isRunning = true;
                Playlist.getArticlesByListId($scope.playlist.selected, page).$promise
                .then(function (response) {
                    if (response.length == 0) {
                        isEmpty = true;
                    } else {
                        page++;
                        isEmpty = false;
                        angular.forEach(response, function(value, key) {
                            if (value.number !== undefined) {
                                $scope.featuredArticles.push(value);
                            }
                        });
                    }

                    isRunning = false;
                });
            }
        }
    }

    /**
     * Adds new playlist. Sets default list name to current date.
     */
    $scope.addNewPlaylist = function () {
        var defaultListName,
            currentDate = new Date();

        $scope.playlist.selected = {};
        defaultListName = currentDate.toString();
        Playlist.setCurrentPlaylistArticles([]);
        $scope.featuredArticles = Playlist.getCurrentPlaylistArticles();
        Playlist.clearLogList();
        $scope.formData.title = defaultListName;
        $scope.playlist.selected = {title: defaultListName, id: undefined};
    }
}]);