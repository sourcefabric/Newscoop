(function () {
    'use strict';

    /**
    * A service for managing user authentication.
    *
    * @class userAuth
    */
    angular.module('playlistsApp').service('userAuth', [
        '$http',
        '$q',
        '$window',
        function ($http, $q, $window) {
            var self = this;

            /**
            * Returns the current oAuth token in sessionStorage.
            *
            * @method token
            * @return {String} the token itself or null if does not exist
            */
            self.token = function () {
                return $window.sessionStorage.getItem('token');
            };

            /**
            * Returns true if the current user is authenticated (=has a token),
            * otherwise false.
            *
            * @method isAuthenticated
            * @return {Boolean}
            */
            self.isAuthenticated = function () {
                return !!$window.sessionStorage.getItem('token');
            };

            /**
            * It makes a request and on success it
            * stores the new authentication token into session storage and
            * resolves given promise with it.
            *
            * @method newToken
            * @return {Object} promise object
            */
            self.newToken = function () {
                var deferred = $q.defer();

                $http.get(Routing.generate("newscoop_gimme_users_getuseraccesstoken", {
                    clientId: clientId
                }))
                .success(function (response) {
                    $window.sessionStorage.setItem('token', response.access_token);
                    deferred.resolve(response.access_token);
                })
                .catch(function (reason) {
                    deferred.reject(reason);
                });

                return deferred.promise;
            };
        }
    ]);
}());
