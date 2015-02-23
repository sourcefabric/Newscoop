'use strict';

angular.module('playlistsApp').factory('Playlist', [
    '$http',
    '$q',
    '$timeout',
    function ($http, $q, $timeout) {
        var Playlist = function () {};  // topic constructor

        var listId = undefined,
			playlistArticles = [],
			logList = [];

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
                'newscoop_gimme_articleslist_getarticleslists',
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
        * Retrieves a list of all articles by given list id.
        *
        * Initially, an empty array is returned, which is later filled with
        * data on successful server response. At that point the given promise
        * is resolved (exposed as a $promise property of the returned array).
        *
        * @method getArticlesByListId
        * @return {Object} array of playlists
        */
        Playlist.getArticlesByListId = function (playlistId) {
            var articles = [],
                deferredGet = $q.defer(),
                url;

            articles.$promise = deferredGet.promise;

            url = Routing.generate(
                'newscoop_gimme_articleslist_getplaylistsarticles',
                {id: playlistId, items_per_page: 10},
                true
            );

            $http.get(url)
            .success(function (response) {
                response.items.forEach(function (item) {
                    articles.push(item);
                });
                playlistArticles = articles;
                deferredGet.resolve();
            }).error(function (responseBody) {
                deferredGet.reject(responseBody);
            });

            return articles;
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

           	requestParams = {items_per_page: 10}

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

        /**
        * Unassignes article from playlist.
        *
        * @method unlinkSingleArticle
        * @param number {Number} article ID
        * @param language {String} article language code (e.g. 'de')
        * @return {Object} promise object that is resolved on successful server
        *   response and rejected on server error response
        */
        Playlist.unlinkSingleArticle = function(number, language) {
            var deferred = $q.defer(),
                linkHeader;

            linkHeader = [
                '<',
                Routing.generate(
                    'newscoop_gimme_articles_getarticle',
                    {number: number, language:language},
                    false
                ),
                '; rel="article">'
            ].join('');

            $http({
                url: Routing.generate(
                    'unlink article from playlist url', //TODO
                    {id: listId},
                    true
                ),
                method: 'UNLINK',
                headers: {link: linkHeader}
            })
            .success(function () {
                deferred.resolve();
            })
            .error(function (responseBody) {
                deferred.reject(responseBody);
            });

            return deferred.promise;
        };

        /**
        * Unassignes article from playlist.
        *
        * @method unlinkSingleArticle
        * @param number {Number} article ID
        * @param language {String} article language code (e.g. 'de')
        * @return {Object} promise object that is resolved on successful server
        *   response and rejected on server error response
        */
        Playlist.batchUpdate = function(logList) {
            var deferred = $q.defer(),
                postParams;

            angular.forEach(logList, function(article, key) {
            	var link = [
            		'<',
	                Routing.generate(
	                    'newscoop_gimme_articles_getarticle',
	                    {number: article.number, language: article.language},
	                    false
	                ),
	                '; rel="article">'].join('');
            	postParams = [link, article._method];
            });

            console.log(postParams);
            /*linkHeader = [
                
            ].join('');*/

            /*$http({
                url: Routing.generate(
                    'unlink article from playlist url', //TODO
                    {id: listId},
                    true
                ),
                method: 'UNLINK',
                headers: {link: linkHeader}
            })
            .success(function () {
                deferred.resolve();
            })
            .error(function (responseBody) {
                deferred.reject(responseBody);
            });

            return deferred.promise;*/
        };

        /**
         * Gets list identifier
         *
         * @return {Integer|null} List id
         */
        Playlist.getListId = function () {
            return listId;
        };

        /**
         * Sets list identifier
         *
         * @param {Integer} value List id
         */
        Playlist.setListId = function(value) {
            listId = value;
        };

        /**
         * Gets list identifier
         *
         * @return {Integer|null} List id
         */
        Playlist.getCurrentPlaylistArticles = function () {
            return playlistArticles;
        };

        /**
         * Gets list identifier
         *
         * @return {Integer|null} List id
         */
        Playlist.setCurrentPlaylistArticles = function (list) {
            playlistArticles = list;
        };

        /**
         * Gets list identifier
         *
         * @return {Integer|null} List id
         */
        Playlist.getLogList = function () {
            return logList;
        };

        /**
         * Gets list identifier
         *
         * @return {Integer|null} List id
         */
        Playlist.addItemToLogList = function (article) {
            logList.push(article);
        };

        return Playlist;
    }
]);
