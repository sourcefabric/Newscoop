'use strict';

/**
* AngularJS controller for loading available articles by given filter.
*
* @class FiltersController
*/
angular.module('playlistsApp').controller('FiltersController', [
    '$scope',
    'Playlist',
    'Filter',
    function (
        $scope,
        Playlist,
        Filter
        ) {
        // assign tableParams (ng-table) from PlaylistsController
        // so we can load articles by given filters
        var tableParams = $scope.$parent.tableParams;
        $scope.publication = {}
        $scope.issue = {};
        $scope.section = {};
        // article statuses
        $scope.statuses = [
            {code: 'Y', name: Translator.trans("Published", {}, 'messages')},
            {code: 'S', name: Translator.trans("Submitted", {}, 'messages')},
            {code: 'N', name: Translator.trans("New", {}, 'messages')}
        ];

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
        tableParams.$params.filter.issue = undefined;
        tableParams.$params.filter.section = undefined;
        $scope.issues = Filter.getAllIssues(item.id);
        //load by publication id
        mergeFilters({publication: item.id});
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
        tableParams.$params.filter.section = undefined;
        $scope.sections = Filter.getAllSections(publicationId, item);
        // load by publication id and issue number
        mergeFilters({
            publication: publicationId,
            issue: item.number,
            language: item.language
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
        mergeFilters({
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

        mergeFilters(filters);
    }

    $scope.author = {};
    $scope.user = {};
    $scope.topic = {};


    $scope.loadAuthors = function (term) {
        $scope.authors = [];
        $scope.author.selected = undefined;
        if (term) {
            $scope.authors = Filter.getAuthors(term);
        } else {
            tableParams.$params.filter.author = undefined;
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

        mergeFilters(filters);
    }

    /**
     * Loads articles by publish date
     *
     * @param {Object} scope Current scope
     */
    $scope.loadByDateOnChange = function (scope) {
        var filters = {
            publish_date: scope.filterDate
        };

        mergeFilters(filters);
    }

    /**
     * Loads articles by published before date
     *
     * @param {Object} scope Current scope
     */
    $scope.loadByPublishedBeforeOnChange = function (scope) {
        if (scope.filterPublishedBefore) {
            var dateTime = scope.filterPublishedBefore + ' 00:00:00';
            var filters = {
                published_before: dateTime
            };

            mergeFilters(filters);
        } else {
            tableParams.$params.filter.published_before = undefined;
        }
    }

    /**
     * Loads articles by published after date
     *
     * @param {Object} scope Current scope
     */
    $scope.loadByPublishedAfterOnChange = function (scope) {
        if (scope.filterPublishedAfter) {
            var dateTime = scope.filterPublishedAfter + ' 00:00:00';
            var filters = {
                published_after: dateTime
            };

            mergeFilters(filters);
        } else {
            tableParams.$params.filter.published_after = undefined;
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
        } else {
            tableParams.$params.filter.creator = undefined;
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

        mergeFilters(filters);
    }

    /**
     * Loads articles by status
     *
     * @param {Object} item User object
     */
     $scope.loadByStatusOnSelect = function (item) {
        var filters = {
            status: item.code
        };

        mergeFilters(filters);
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

        mergeFilters(filters);
    }

    /**
     * It merges new filter into current filters, so we can
     * get articles by given filters
     *
     * @param  {Object} newFilter New filter object
     * @return {Array}           Array of filers' objects
     */
    var mergeFilters = function (newFilter) {
        return _.merge(
            tableParams.$params.filter,
            newFilter
        );
    }
}]);