(function() {
'use strict';
var app = angular.module('playlistsApp', ['ngSanitize', 'ui.select', 'ngTable', 'ng-sortable', 'ui.bootstrap', 'infinite-scroll'])
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
        onSort: function (evt/**Event*/){
            var article = evt.model;
            var isInLogList = false;
            isInLogList = _.some(
                Playlist.getLogList(),
                {number: article.number}
            );

            // only when sorting list of featured articles (playlist)
            var logList = Playlist.getLogList();
            article._order = evt.newIndex + 1;
            article._method = "link";
            if (!isInLogList) {

                Playlist.addItemToLogList(article);
            }
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
            article._method = "unlink";
            Playlist.addItemToLogList(article);
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
    'Filter',
    function (
        $scope,
        Playlist,
        ngTableParams,
        Filter
    ) {

    $scope.isViewing = false;
    $scope.playlist = {};
    $scope.playlists = Playlist.getAll();
    $scope.playlistInfo = undefined;
    $scope.featuredArticles = [];
    $scope.formData = {};
    $scope.processing = false;

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

    $scope.playlist.selected = undefined;

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

    $scope.publication = {}
    $scope.issue = {};
    $scope.section = {};

    /**
     * Loads all publications to filter articles by
     */
    $scope.loadPublications = function () {
        if (_.isEmpty($scope.publications)) {
            $scope.publications = Filter.getAllPublications();
        }
    }

    /**
     * Loads all issues and articles by publication id
     *
     * @param  {Object} item Publication object
     */
    $scope.loadIssues = function (item) {
        $scope.issues = [];
        $scope.issue.selected = undefined;
        $scope.sections = [];
        $scope.section.selected = undefined;
        $scope.tableParams.$params.filter.issue = undefined;
        $scope.tableParams.$params.filter.section = undefined;
        $scope.issues = Filter.getAllIssues(item.id);
        //load by publication id
        $scope.tableParams.$params.filter = _.merge($scope.tableParams.$params.filter, {publication: item.id});
    };

    /**
     * Loads all sections and articles by publication id and issue number
     *
     * @param  {Object} item Issue object
     */
    $scope.loadSections = function (item) {
        var publicationId = $scope.publication.selected.id;
        $scope.sections = [];
        $scope.section.selected = undefined;
        $scope.tableParams.$params.filter.section = undefined;
        $scope.sections = Filter.getAllSections(publicationId, item.number);
        // load by publication id and issue number
        $scope.tableParams.$params.filter = _.merge($scope.tableParams.$params.filter, {
            publication: publicationId,
            issue: item.number
        });
    };

    /**
     * Loads all articles by publication id, issue number
     * and section number
     *
     * @param  {Object} item Section object
     */
    $scope.loadByMainFilters = function (item) {
        var publicationId = $scope.publication.selected.id;
        var issue = $scope.issue.selected.number;

        // load by publication id and issue number
        $scope.tableParams.$params.filter = _.merge($scope.tableParams.$params.filter, {
            publication: publicationId,
            issue: issue,
            section: item.number
        });
    };

    /**
     * Loads all articles' types
     */
    $scope.loadArticleTypes = function () {
        if (_.isEmpty($scope.articleTypes)) {
            $scope.articleTypes = Filter.getArticleTypes();
        }
    }

    /**
     * Loads all articles' types
     *
     * @param {Object} type Article type
     */
    $scope.loadByArticleTypesOnSelect = function (type) {
        var filters = {
            article_type: type.name
        };

        $scope.tableParams.$params.filter = _.merge($scope.tableParams.$params.filter, filters);
    }

    $scope.author = {};
    $scope.user = {};
    $scope.topic = {};


    $scope.loadAuthors = function (term) {
        $scope.authors = [];
        $scope.author.selected = undefined;
        if (term) {
            $scope.authors = Filter.getAuthors(term);
        }
    }

    /**
     * Loads all authors
     *
     * @param {Object} type Author object
     */
    $scope.loadByAuthorsOnSelect = function (item) {
        var filters = {
            author: item.id
        };

        $scope.tableParams.$params.filter = _.merge($scope.tableParams.$params.filter, filters);
    }

    $scope.loadByDateOnChange = function (scope) {
        var filters = {
            publish_date: scope.filterDate
        };

        $scope.tableParams.$params.filter = _.merge($scope.tableParams.$params.filter, filters);
    }

    $scope.loadByPublishedBeforeOnChange = function (scope) {
        if (scope.filterPublishedBefore) {
            var dateTime = scope.filterPublishedBefore + ' 00:00:00';
            var filters = {
                published_before: dateTime
            };

            $scope.tableParams.$params.filter = _.merge($scope.tableParams.$params.filter, filters);
        } else {
            $scope.tableParams.$params.filter.published_before = undefined;
        }
    }

    $scope.loadByPublishedAfterOnChange = function (scope) {
        if (scope.filterPublishedAfter) {
            var dateTime = scope.filterPublishedAfter + ' 00:00:00';
            var filters = {
                published_after: dateTime
            };

            $scope.tableParams.$params.filter = _.merge($scope.tableParams.$params.filter, filters);
        } else {
            $scope.tableParams.$params.filter.published_after = undefined;
        }
    }

    /**
     * Loads all users
     *
     * @param {Object} item User object
     */
    $scope.loadUsers = function (term) {
        $scope.users = [];
        $scope.user.selected = undefined;
        if (term) {
            $scope.users = Filter.getUsers(term);
        }
    }

    /**
     * Loads all articles by user
     *
     * @param {Object} item User object
     */
    $scope.loadByUsersOnSelect = function (item) {
        var filters = {
            creator: item.id
        };

        $scope.tableParams.$params.filter = _.merge($scope.tableParams.$params.filter, filters);
    }

    // article statuses
    $scope.statuses = [
        {code: 'Y', name: Translator.trans("Published", {}, 'messages')},
        {code: 'S', name: Translator.trans("Submitted", {}, 'messages')},
        {code: 'N', name: Translator.trans("New", {}, 'messages')}
    ];

    /**
     * Loads articles by status
     *
     * @param {Object} item User object
     */
    $scope.loadByStatusOnSelect = function (item) {
        var filters = {
            status: item.code
        };

        $scope.tableParams.$params.filter = _.merge($scope.tableParams.$params.filter, filters);
    }

    /**
     * Loads all topics
     *
     * @param {Object} item Topic object
     */
    $scope.loadTopics = function (term) {
        $scope.topics = [];
        $scope.topic.selected = undefined;
        if (term) {
            $scope.topics = Filter.getTopics(term);
        }
    }

    /**
     * Loads all articles by topic
     *
     * @param {Object} item Topic object
     */
    $scope.loadByTopicOnSelect = function (item) {
        var filters = {
            topic: item.id
        };

        $scope.tableParams.$params.filter = _.merge($scope.tableParams.$params.filter, filters);
    }
}]);
})();