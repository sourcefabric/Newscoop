'use strict';

/**
* AngularJS controller for managing playlists
*
* @class PlaylistsController
*/
angular.module('playlistsApp').controller('PlaylistsController', [
    '$scope',
    'Playlist',
    'ngTableParams',
    'modalFactory',
    '$q',
    function (
        $scope,
        Playlist,
        ngTableParams,
        modalFactory,
        $q
        ) {

        $scope.isViewing = false;
        $scope.playlist = {};
        $scope.playlists = [];
        $scope.playlistInfo = undefined;
        $scope.featuredArticles = [];
        $scope.formData = {};
        $scope.processing = false;
        $scope.playlist.selected = undefined;
        // limit var, which is set to false by FeaturedController
        // when provided limit is invalid
        $scope.playlistLimit = true;

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
     * Checks if list name max length is not exceeded,
     * if it is, then it will display flash message with error.
     *
     * @param  {Object} scope Current scope
     */
    $scope.validateListName = function (scope) {
        if (scope.listNameForm.playlistName.$error.maxlength) {
            flashMessage(Translator.trans('List name should not be longer than 40 chars', {}, 'articles'), 'error');
        }
    }

    /**
     * Loads available playlists on select box click (lazy load)
     */
    $scope.loadAllPlaylists = function () {
        if (_.isEmpty($scope.playlists)) {
            $scope.playlists = Playlist.getAll();
        }
    }

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

        if (isLimitReached()) {
            flashMessage(Translator.trans(
                    'List limit reached! Remove some articles from the list before adding new ones.'
            ), 'error');

            return true;
        }

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

        if (isLimitReached()) {
            flashMessage(Translator.trans(
                    'List limit reached! Remove some articles from the list before adding new ones.'
            ), 'error');

            return true;
        }

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

    /**
     * Checks if playlist's limit is reached.
     *
     * @return {Boolean}
     */
    var isLimitReached = function () {
        var limit = $scope.playlist.selected.maxItems;

        return (limit && limit != 0 && $scope.featuredArticles.length >= limit);
    }

    /**
     * Saves playlist from the article edit screen view
     */
    $scope.savePlaylistInEditorMode = function () {
        var logList = [];
        $scope.processing = true;
        logList = Playlist.getLogList();
        if (logList.length == 0) {
            flashMessage(Translator.trans('List saved'));
            $scope.processing = false;

            return true;
        }

        Playlist.batchUpdate(logList, $scope.playlist.selected)
        .then(function () {
            flashMessage(Translator.trans('List saved'));
            Playlist.clearLogList();
            $scope.processing = false;
        }, function() {
            flashMessage(Translator.trans('Could not save the list'), 'error');
        });
    }

    // page variable is needed for fetching articles on scroll in playlist box
    // (right box), by default it's set to 2 because we start fetching new
    // articles from page 2, since articles on page 1 are loaded
    // by default when selecting playlist.
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

    /**
     * Removes playlist
     */
    $scope.removePlaylist = function () {
        var modal,
        title,
        text,
        okText,
        cancelText;

        title = Translator.trans('Remove list');
        text = Translator.trans('Are you sure you want to delete this list?');
        okText = Translator.trans('OK', {}, 'messages');
        cancelText = Translator.trans('Cancel', {}, 'messages');

        modal = modalFactory.confirmDanger(title, text, okText, cancelText);
        modal.result.then(function (data) {
            if ($scope.playlist.selected.id !== undefined) {
                Playlist.deletePlaylist().then(function () {
                    flashMessage(Translator.trans('Entry deleted.', {}, 'messsages'));
                    _.remove(
                      $scope.playlists,
                      {id: $scope.playlist.selected.id}
                      );

                    $scope.playlist.selected = undefined;
                }, function () {
                  flashMessage(Translator.trans('Error List', {}, 'articles'), 'error');
              });
            } else {
                $scope.playlist.selected = undefined;
            }
        }, function (reason) {
        });
    }

    /**
     * It makes a proper API calls (create, update, link, unlink) based on
     * performed actions. If the list's limit will be change, popup will be displayed.
     */
    $scope.savePlaylist = function () {
        var newLimit = $scope.playlist.selected.maxItems,
            oldLimit = $scope.playlist.selected.oldLimit,
            modal,
            title,
            text,
            okText,
            cancelText;

        title = Translator.trans('Info');
        text = Translator.trans('articles.playlists.alert', {}, 'articles');
        okText = Translator.trans('OK', {}, 'messages');
        cancelText = Translator.trans('Cancel', {}, 'messages');

        if (newLimit && newLimit != 0 && newLimit != oldLimit) {
            modal = modalFactory.confirmLight(title, text, okText, cancelText);

            modal.result.then(function () {
                saveList();
                $scope.playlist.selected.oldLimit = $scope.playlist.selected.maxItems;
            }, function () {
              return false;
          });
        } else {
            saveList();
        }
    };

    /**
     * Saves, updates the list and make post actions on promise resolve, such
     * clearing log list, showing/hiding flash messages etc.
     */
    var saveList = function () {
        var flash = flashMessage(Translator.trans('Processing', {}, 'messages'), null, true);
        $scope.processing = true;

        doSaveAPICalls().then(function (response) {
            flash.fadeOut();
            $scope.processing = false;
            Playlist.clearLogList();
            flashMessage(Translator.trans('List saved'));
            $scope.featuredArticles = Playlist.getArticlesByListId({id: Playlist.getListId()});
            $scope.playlist.selected.id = Playlist.getListId();
            if (response !== undefined && response[0].object.articlesModificationTime !== undefined) {
                $scope.playlist.selected.articlesModificationTime = response[0].object.articlesModificationTime;
            }
        }, function(response) {
            if (response.errors[0].code === 409) {
                flashMessage(Translator.trans(
                    'This list is already in a different state than the one in which it was loaded.'
                ), 'error');
                // automatically refresh playlist
                $scope.featuredArticles = Playlist.getArticlesByListId({id: Playlist.getListId()});
            } else {
                flashMessage(Translator.trans('Could not save the list'), 'error');
            }
            flash.fadeOut();
            $scope.processing = false;
        });
    }

    /**
     * Saves, updates playlist with all articles on server side.
     * It makes batch link or unlink of the articles. It also
     * saves a proper order of the articles. All list's changes are saved
     * by clicking Save button.
     */
    var doSaveAPICalls = function () {
        var deferred,
            listname,
            logList = [],
            playlistExists,
            list;

        listname = $scope.formData.title
        playlistExists = _.some(
            $scope.playlists,
            {title: listname}
        );

        var update = false;
        // if we rename the current playlist, update it only
        if (listname != $scope.playlist.selected.title && $scope.playlist.selected.id !== undefined) {
            update = true;
        }

        deferred = $q.defer();
        list = deferred;
        $scope.playlist.selected.title = listname;

        if (!playlistExists && !update && $scope.playlist.selected.id === undefined) {
            return Playlist.createPlaylist($scope.playlist.selected);
        }

        if ($scope.playlist.selected !== undefined) {
            list = Playlist.updatePlaylist($scope.playlist.selected);
            deferred.resolve();
        }

        logList = Playlist.getLogList();
        if (logList.length == 0) {
            return list;
        }

        return Playlist.batchUpdate(logList, $scope.playlist.selected);
    }
}]);