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
    '$timeout',
    '$activityIndicator',
    function (
        $scope,
        Playlist,
        ngTableParams,
        modalFactory,
        $q,
        $timeout,
        $activityIndicator
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
    $scope.showLimitAlert = false;
    var countDownTimeInSeconds = 11;

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
        filter: ".ignore-elements"
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

    // stops, starts counter
    $scope.isCounting = false;

    $scope.startCountDown = function () {
        countDown();
    }

    /**
     * This function count seconds after which revert popup will be closed
     * and removes last article from the playlist if the limit is reached,
     * inserts new article
     */
    var countDown = function(){
       if($scope.isCounting) {
            return;
       }

       $scope.isCounting = true;
       (function countEvery() {
            if ($scope.isCounting) {
                $scope.countDown--;
                $timeout(countEvery, 1000);
                if ($scope.countDown === 0) {
                    removeLastArticle();
                }
            }
        }());
    }

    /**
     * It removes last article from the playlist if the limit is reached,
     * inserts new article. It is called from FeaturedController.
     */
    $scope.removeLastInsertNew = function () {
        removeLastArticle();
    }

    /**
     * It removes last article from the playlist if the limit is reached,
     * inserts new article
     */
    var removeLastArticle =  function () {
        // remove one before last article from the featured articles list
        // when we drag-drop to the list as a last element, thats why we need to remove
        // one before last article else remove last one (-1)
        var articleToRemove = $scope.featuredArticles[$scope.featuredArticles.length - 1];
        if ($scope.articleNotToRemove._order == $scope.featuredArticles.length) {
            articleToRemove = $scope.featuredArticles[$scope.featuredArticles.length - 2];
        }

        articleToRemove._method = "unlink";
        _.remove(
            $scope.featuredArticles,
            {number: articleToRemove.number}
        );

        Playlist.addItemToLogList(articleToRemove);
        var logList = Playlist.getLogList();

        // we have to now replace last element with one before last in log list
        // so it can be save in API in a proper order, actually we first add a
        // new article to the featured articles list and then we unlink the last one.
        // We need to do it in a reverse way, so we first unlink, and then add a new one.
        var lastElement = logList[logList.length - 1];
        var beforeLast = logList[logList.length - 2];

        logList[logList.length - 1] = beforeLast;
        logList[logList.length - 2] = lastElement;

        Playlist.setLogList(logList);
        $scope.showLimitAlert = false;
        $scope.isCounting = false;
    }

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

        if (!exists) {
            var isInLogList = _.some(
                Playlist.getLogList(),
                {number: $scope.articlePreview.number}
            );

            if (isLimitReached()) {
                $scope.articleNotToRemove = $scope.articlePreview;
                $scope.articleOverLimitIndex = 0;
                $scope.articleOverLimitNumber = $scope.articlePreview.number;
                $scope.showLimitAlert = true;
                $scope.countDown = countDownTimeInSeconds;
                $scope.startCountDown();
            }

            $scope.articlePreview._method = "link";
            $scope.articlePreview._order = 1;
            Playlist.addItemToLogList($scope.articlePreview);
            $scope.featuredArticles.unshift($scope.articlePreview);
            $scope.isViewing = false;
        } else {
            flashMessage(Translator.trans('Item already exists in the list', {}, 'articles'), 'error');
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
                    if (isLimitReached()) {
                        $scope.articleNotToRemove = article;
                        $scope.articleOverLimitIndex = 0;
                        $scope.articleOverLimitNumber = article.number;
                        $scope.showLimitAlert = true;
                        $scope.countDown = countDownTimeInSeconds;
                        $scope.startCountDown();
                    }

                    article._method = "link";
                    article._order = 1;
                    Playlist.addItemToLogList(article);
                    $scope.featuredArticles.unshift(article);
                    $scope.processing = false;
                }, function() {
                    flashMessage(Translator.trans('Error List', {}, 'articles'), 'error');
                });

                return true;
            }
        }

        flashMessage(Translator.trans('Item already exists in the list', {}, 'articles'), 'error');
    };

    /**
     * Checks if playlist's limit is reached.
     *
     * @return {Boolean}
     */
    var isLimitReached = function () {
        var limit = 0;
        if ($scope.playlist.selected !== undefined) {
            limit = $scope.playlist.selected.maxItems;
        }

        return (limit && limit != 0 && $scope.featuredArticles.length >= limit);
    }

    /**
     * Saves playlist from the article edit screen view
     */
    $scope.savePlaylistInEditorMode = function () {
        saveList();
    }

    // page variable is needed for fetching articles on scroll in playlist box
    // (right box), by default it's set to 2 because we start fetching new
    // articles from page 2, since articles on page 1 are loaded
    // by default when selecting playlist.
    // set in scope so we can reset it when playlist will be saved
    // and it can load more articles on scroll, without a need
    // to refresh the page
    $scope.page = 2;
    $scope.isRunning = false;
    $scope.isEmpty = false;

    /**
     * Sets playlist details to the current scope.
     *
     * It loads playlist details on select2 change.
     *
     * @param  {Object} list  Playlist
     */
     $scope.setPlaylistInfoOnChange = function (list) {
        $scope.loadingSpinner = true;
        Playlist.getArticlesByListId(list).then(function (data) {
            $scope.featuredArticles = data.items;
            $scope.loadingSpinner = false;
            $activityIndicator.stopAnimating();
        }, function(response) {
            flashMessage(Translator.trans('Could not refresh the list', {}, 'articles'), 'error');
            $scope.loadingSpinner = false;
            $activityIndicator.stopAnimating();
        });

        Playlist.clearLogList();
        Playlist.setListId(list.id);
        $scope.playlistInfo = list;
        $scope.playlist.selected.oldLimit = list.maxItems;
        $scope.formData = {title: list.title}
        $scope.page = 2;
        $scope.isRunning = false;
    };

    /**
     * Loads more playlist's articles on scroll
     */
     $scope.loadArticlesOnScrollDown = function () {
        if ($scope.playlist.selected) {
            if ($scope.playlist.selected.maxItems === undefined ||
                $scope.playlist.selected.maxItems === 0) {
                if (!$scope.isEmpty && !$scope.isRunning) {
                    $scope.isRunning = true;
                    Playlist.getArticlesByListId($scope.playlist.selected, $scope.page)
                    .then(function (response) {
                        if (response.items.length == 0) {
                            $scope.isEmpty = true;
                        } else {
                            $scope.page++;
                            $scope.isEmpty = false;
                            angular.forEach(response.items, function(value, key) {
                                if (value.number !== undefined) {
                                    $scope.featuredArticles.push(value);
                                }
                            });
                        }
                        $scope.isRunning = false;
                    }, function(response) {
                        flashMessage(Translator.trans('Could not refresh the list', {}, 'articles'), 'error');
                    });
                }
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

        title = Translator.trans('Remove list', {}, 'articles');
        text = Translator.trans('Are you sure you want to delete this list?', {}, 'articles');
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

        okText = Translator.trans('OK', {}, 'messages');
        cancelText = Translator.trans('Cancel', {}, 'messages');
        if ($scope.playlist.selected.title !== $scope.formData.title && $scope.playlist.selected.id !== undefined) {
            title = Translator.trans('Info', {}, 'articles');
            text = Translator.trans('articles.playlists.namechanged', {}, 'articles');
            modal = modalFactory.confirmLight(title, text, okText, cancelText);

            modal.result.then(function () {
                showLimitPopupAndSave(newLimit, oldLimit, modal, okText, cancelText);

                return true;
            });
        } else {
            showLimitPopupAndSave(newLimit, oldLimit, modal, okText, cancelText);
        }
    };

    var showLimitPopupAndSave = function (newLimit, oldLimit, modal, okText, cancelText) {
        if (newLimit && newLimit != 0 && newLimit != oldLimit) {
            var title = Translator.trans('Info', {}, 'articles');
            var text = Translator.trans('articles.playlists.alert', {}, 'articles');
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
    }

    /**
     * Saves, updates playlist with all articles on server side.
     * It makes batch link or unlink of the articles. It also
     * saves a proper order of the articles. All list's changes are saved
     * by clicking Save button.
     */
    var saveList = function () {
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

        $scope.playlist.selected.title = listname;
        var flash = flashMessage(Translator.trans('Processing', {}, 'messages'), null, true);
        $scope.processing = true;

        if (!playlistExists && !update && $scope.playlist.selected.id === undefined) {
            Playlist.createPlaylist($scope.playlist.selected).then(function (response) {
                $scope.playlist.selected.id = response.id;
                logList = Playlist.getLogList();
                if (logList.length == 0) {
                    afterSave(response);
                    flash.fadeOut();
                    return;
                }

                Playlist.batchUpdate(logList, $scope.playlist.selected).then(function (data) {
                    afterSave(data);
                    flash.fadeOut();
                }, function(response) {
                    flash.fadeOut();
                    afterSaveError(response);
                });
            }, function(response) {
                flash.fadeOut();
                afterSaveError(response);
            });

            return;
        }

        if ($scope.playlist.selected !== undefined) {
            Playlist.updatePlaylist($scope.playlist.selected).then(function (response) {
                logList = Playlist.getLogList();
                if (logList.length == 0) {
                    afterSave(response);
                    flash.fadeOut();
                    return;
                }

                Playlist.batchUpdate(logList, $scope.playlist.selected).then(function (data) {
                    afterSave(data);
                    flash.fadeOut();
                }, function(response) {
                    flash.fadeOut();
                    afterSaveError(response);
                });
            }, function(response) {
                flash.fadeOut();
                afterSaveError(response);
            });
        }
    }

    var afterSave = function (response) {
        $scope.processing = false;
        Playlist.clearLogList();
        flashMessage(Translator.trans('List saved', {}, 'articles'));
        $scope.loadingSpinner = true;
        Playlist.getArticlesByListId({id: Playlist.getListId(), maxItems: $scope.playlist.selected.maxItems}).then(function (data) {
            $scope.featuredArticles = data.items;
            $scope.loadingSpinner = false;
            $scope.isEmpty = false;
            $scope.page = 2;
            $activityIndicator.stopAnimating();
        }, function(response) {
            flashMessage(Translator.trans('Could not refresh the list', {}, 'articles'), 'error');
            $scope.loadingSpinner = false;
            $activityIndicator.stopAnimating();
        });

        $scope.playlist.selected.id = Playlist.getListId();

        if (response && response[0] !== undefined && response[0].object.articlesModificationTime !== undefined) {
            $scope.playlist.selected.articlesModificationTime = response[0].object.articlesModificationTime;
        }
    }

    var afterSaveError = function (response) {
        if (response.errors[0].code === 409) {
            flashMessage(Translator.trans(
                        'This list is already in a different state than the one in which it was loaded.', {}, 'articles'
            ), 'error');
            // automatically refresh playlist
            $scope.loadingSpinner = true;
            Playlist.getArticlesByListId({id: Playlist.getListId()}).then(function (data) {
                $scope.featuredArticles = data.items;
                $scope.playlist.selected.articlesModificationTime = data.articlesModificationTime;
                $scope.loadingSpinner = false;
                $activityIndicator.stopAnimating();
            }, function(response) {
               flashMessage(Translator.trans('Could not refresh the list', {}, 'articles'), 'error');
               $scope.loadingSpinner = false;
               $activityIndicator.stopAnimating();
            });
        } else {
            flashMessage(Translator.trans('Could not save the list', {}, 'articles'), 'error');
        }

        $scope.processing = false;
    }
}]);