(function () {
    'use strict';

    /**
    * Constructor function for the login modal controller
    *
    * @class ModalLoginCtrl
    */
    function ModalLoginCtrl($modalInstance) {
        var self = this,
            tokenRegex = new RegExp('access_token=(\\w+)');

        // On successful login, Newscoop login form redirects user to some
        // redirect URL and that URL contains the new authentication token.
        // Upon redirect, the iframe in modal body is reloaded and we catch
        // its onLoad event, giving us a chance to extract new token from URL.

        /**
        * Updates workflow status on the server.
        *
        * @method iframeLoadedHandler
        * @param location {Object} window.location object of the page
        *   loaded in the modal's iframe
        */
        self.iframeLoadedHandler = function (location) {
            var matches,
                token;

            if (typeof location.hash !== 'string') {
                return;
            }

            matches = tokenRegex.exec(location.hash);

            if (matches !== null) {
                token = matches[1];
                $modalInstance.close(token);
            }
            // if token is not found (perhaps due to the failed login),
            // nothing happens and the modal stays open
        };
    }

    ModalLoginCtrl.$inject = ['$modalInstance'];

    /**
    * A service for managing user authentication.
    *
    * @class userAuth
    */
    angular.module('playlistsApp').service('userAuth', [
        '$http',
        '$modal',
        '$q',
        '$window',
        function ($http, $modal, $q, $window) {
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
            * Opens a modal with Newscoop login form. On successful login it
            * stores the new authentication token into session storage and
            * resolves given promise with it.
            *
            * @method newTokenByLoginModal
            * @return {Object} promise object
            */
            self.newTokenByLoginModal = function () {
                var deferred = $q.defer(),
                    dialog;

                dialog = $modal.open({
                    templateUrl: '/bundles/newscoopnewscoop/views/modal-login.html',
                    controller: ModalLoginCtrl,
                    controllerAs: 'ctrl',
                    windowClass: 'modalLogin',
                    backdrop: 'static'
                });

                dialog.result.then(function (token) {
                    $window.sessionStorage.setItem('token', token);
                    flashMessage(Translator.trans('Successfully refreshed authentication token.', {}, 'messages'));
                    deferred.resolve(token);
                })
                .catch(function (reason) {
                    flashMessage(Translator.trans('Failed to refresh authentication token.', {}, 'messages'), 'error');
                    deferred.reject(reason);
                });

                return deferred.promise;
            };
        }
    ]);
}());
