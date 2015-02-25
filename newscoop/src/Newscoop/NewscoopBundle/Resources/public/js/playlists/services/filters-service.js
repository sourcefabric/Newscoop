'use strict';

angular.module('playlistsApp').factory('Filter', [
    '$http',
    '$q',
    '$timeout',
    function ($http, $q, $timeout) {
        var Filter = function () {};  // Filter constructor

        /**
        * Retrieves a list of all existing playlists.
        *
        * Initially, an empty array is returned, which is later filled with
        * data on successful server response. At that point the given promise
        * is resolved (exposed as a $promise property of the returned array).
        *
        * @method getAll
        * @return {Object} array of playlists
        */
        Filter.getAllPublications = function () {
            var items = [],
                deferredGet = $q.defer(),
                url;

            items.$promise = deferredGet.promise;

            url = Routing.generate(
                'newscoop_gimme_publications_getpublications',
                {items_per_page: 9999},
                true
            );

            $http.get(url)
            .success(function (response) {
                response.items.forEach(function (item) {
                    items.push(item);
                });
                deferredGet.resolve();
            }).error(function (responseBody) {
                deferredGet.reject(responseBody);
            });

            return items;
        };

        Filter.getAllIssues = function (publicationId) {
            var items = [],
                deferredGet = $q.defer(),
                url;

            items.$promise = deferredGet.promise;

            url = Routing.generate(
                'newscoop_gimme_issues_getissues',
                {items_per_page: 9999, publication: publicationId},
                true
            );

            $http.get(url)
            .success(function (response) {
                response.items.forEach(function (item) {
                    items.push(item);
                });
                deferredGet.resolve();
            }).error(function (responseBody) {
                deferredGet.reject(responseBody);
            });

            return items;
        };

        Filter.getAllSections = function (publicationId, issueNumber) {
            var items = [],
                deferredGet = $q.defer(),
                url;

            items.$promise = deferredGet.promise;

            url = Routing.generate(
                'newscoop_gimme_sections_getsections',
                {
                    items_per_page: 9999,
                    publication: publicationId,
                    issue: issueNumber
                },
                true
            );

            $http.get(url)
            .success(function (response) {
                response.items.forEach(function (item) {
                    items.push(item);
                });
                deferredGet.resolve();
            }).error(function (responseBody) {
                deferredGet.reject(responseBody);
            });

            return items;
        };

        Filter.getArticleTypes = function () {
            var items = [],
                deferredGet = $q.defer(),
                url;

            items.$promise = deferredGet.promise;

            url = Routing.generate(
                'newscoop_gimme_articletypes_getarticletypes',
                {
                    items_per_page: 9999
                },
                true
            );

            $http.get(url)
            .success(function (response) {
                response.items.forEach(function (item) {
                    items.push(item);
                });
                deferredGet.resolve();
            }).error(function (responseBody) {
                deferredGet.reject(responseBody);
            });

            return items;
        };

        Filter.getAuthors = function (term) {
            var items = [],
                deferredGet = $q.defer(),
                url;

            items.$promise = deferredGet.promise;

            url = Routing.generate(
                'newscoop_gimme_authors_searchauthors',
                {
                    items_per_page: 25,
                    query: term
                },
                true
            );

            $http.get(url)
            .success(function (response) {
                response.items.forEach(function (item) {
                    items.push(item);
                });
                deferredGet.resolve();
            }).error(function (responseBody) {
                deferredGet.reject(responseBody);
            });

            return items;
        };

        Filter.getUsers = function (term) {
            var items = [],
                deferredGet = $q.defer(),
                url;

            items.$promise = deferredGet.promise;

            url = Routing.generate(
                'newscoop_gimme_users_searchusers',
                {
                    query: term,
                    items_per_page: 25
                },
                true
            );

            $http.get(url)
            .success(function (response) {
                response.items.forEach(function (item) {
                    items.push(item);
                });
                deferredGet.resolve();
            }).error(function (responseBody) {
                deferredGet.reject(responseBody);
            });

            return items;
        };

        Filter.getTopics = function (term) {
            var items = [],
                deferredGet = $q.defer(),
                url;

            items.$promise = deferredGet.promise;

            url = Routing.generate(
                'newscoop_gimme_topics_gettopics',
                {
                    items_per_page: 25,
                    query: term
                },
                true
            );

            $http.get(url)
            .success(function (response) {
                response.items.forEach(function (item) {
                    items.push(item);
                });
                deferredGet.resolve();
            }).error(function (responseBody) {
                deferredGet.reject(responseBody);
            });

            return items;
        };

        return Filter;
    }
]);
