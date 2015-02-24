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
        * Saves playlist items and order.
        *
        * @method batchUpdate
        * @param number {Array} array which keeps modified articles for given playlist
        * @return {Object} promise object that is resolved on successful server
        *   response and rejected on server error response
        */
        Playlist.batchUpdate = function(logList) {
            var deferred = $q.defer(),
                postParams = [];

            angular.forEach(logList, function(article, key) {
            	var link = [
            		'<',
	                Routing.generate(
	                    'newscoop_gimme_articles_getarticle',
	                    {number: article.number, language: article.language},
	                    false
	                ),
	                '; rel="article">'].join('');;

	            if (article._method == 'link') {
	            	var position = [',<',
	                article._order,
	                '; rel="article-position">'].join('');
	                link = link.concat(position);
	            }

            	postParams.push({link: link, method: article._method});
            });

            $http({
                url: Routing.generate(
                    'newscoop_gimme_articleslist_getarticleslist',
                    {id: listId},
                    true
                ),
                method: 'POST',
                headers: {
	                'Content-Type': 'application/x-www-form-urlencoded'
	            },
	            transformRequest: function (data) {
	                var str = [];
			        angular.forEach(data, function(param, key){
			        	str.push("actions[]["+param.method+"]=" + encodeURIComponent(param.link));
			    	});

			        return str.join("&");
	            },
	            data: postParams
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
         * Gets playlist's articles array
         *
         * @return {Array} Playlist's articles
         */
        Playlist.getCurrentPlaylistArticles = function () {
            return playlistArticles;
        };

        /**
         * Sets current playlist articles
         *
         * @param {Array} list Playlist's articles
         */
        Playlist.setCurrentPlaylistArticles = function (list) {
            playlistArticles = list;
        };

        /**
         * Gets log list
         *
         * @return {Array} Log list
         */
        Playlist.getLogList = function () {
            return logList;
        };

        /**
         * Clears log list
         *
         * @return {Array} Log list
         */
        Playlist.clearLogList = function () {
        	logList = [];

            return logList;
        };

        /**
         * Adds new item to the logList
         * @param {Object} article Article object
         */
        Playlist.addItemToLogList = function (article) {
            logList.push(article);
        };

        /**
         * Removes article from logList by number and method
         *
         * @param  {Integer} articleNumber Article's number
         * @param  {String} method         method type (e.g. "unlink" or "link")
         */
        Playlist.removeItemFromLogList = function (articleNumber, method) {
        	_.remove(
                logList,
                {number: articleNumber, _method: method}
            );
        }

        return Playlist;
    }
]);
