/**
* A directive which creates an iframe containing the Newscoop login form.
*
* @class sfIframeLogin
*/
angular.module('playlistsApp').directive('sfIframeLogin', [
    function () {
        return {
            template: '<iframe></iframe>',
            replace: true,
            restrict: 'E',
            link: function(scope, $element, attrs) {
                var url;

                url = [
                    Routing.generate('fos_oauth_server_authorize'),
                    '?client_id=', clientId,
                    '&redirect_uri=', redirectUris,
                    '&response_type=token'
                ].join('');

                $element.attr('src', url);
            }
        };
    }
]);