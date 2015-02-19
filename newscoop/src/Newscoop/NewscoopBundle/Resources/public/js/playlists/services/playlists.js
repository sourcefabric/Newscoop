'use strict';

angular.module('playlistsApp').factory('Playlist', [
    '$http',
    '$q',
    '$timeout',
    function ($http, $q, $timeout) {
        var Playlist = function () {};  // topic constructor

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
        Playlist.getAll = function () {
            var playlists = [],
                deferredGet = $q.defer(),
                url;

            playlists.$promise = deferredGet.promise;

            url = Routing.generate(
                'playlists url', // TODO
                {items_per_page: 9999},
                true
            );

            $http.get(url)
            .success(function (response) {
                response.items.forEach(function (item) {
                    playlists.push(item);
                });
                deferredGet.resolve();
            }).error(function (responseBody) {
                deferredGet.reject(responseBody);
            });

            return playlists;
        };

        /**
        * Retrieves a list of all available articles.
        *
        * At the begining we check for valid ngTable parameters
        * and pass them to the API endpoint, next we make a GET call
        * and fill ngTable with data returned from the API.
        *
        * @method getAllArticles
        * @return {Object} array of playlists
        */
        Playlist.getAllArticles = function ($defer, params) {
            var url,
            	requestParams;

           	requestParams = {items_per_page: 5}

           	if(params.$params.query !== undefined) {
           		requestParams.query = params.$params.query;
           	}

           	if(params.$params.page !== undefined) {
           		requestParams.page = params.$params.page;
           	}

            url = Routing.generate(
                'newscoop_gimme_articles_searcharticles',
                requestParams,
                true
            );

            $http.get(url)
            .success(function (response) {
                if (response.pagination !== undefined) {
                    params.total(response.pagination.itemsCount);
                } else {
                    params.total(response.items.length);
                }
                $defer.resolve(response.items);
            }).error(function (responseBody) {
                $defer.reject(responseBody);
            });
        };

        return Playlist;
    }
]);
