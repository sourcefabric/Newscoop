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
            scope: {
                onLoadHandler: '&onLoad'
            },
            link: function(scope, $element, attrs) {
                var url;

                if (!attrs.onLoad) {
                    throw 'sfIframeLogin: missing onLoad handler';
                }

                url = [
                    Routing.generate('fos_oauth_server_authorize'),
                    '?client_id=', clientId,
                    '&redirect_uri=', redirectUris,
                    '&response_type=token'
                ].join('');

                $element.attr('src', url);
                $element.attr('width', attrs.width || 535);
                $element.attr('height', attrs.height || 510);

                $element.on('load', function () {
                    try {
                        scope.onLoadHandler({
                            location: $element[0].contentWindow.location
                        });
                    } catch (e) {
                        // A security exception occurs when trying to access
                        // iframe's contents when login comes from a different
                        // origin. We simply silence such exceptions, because
                        // the only load event we are interested in is when
                        // the login form redirects us back to our own
                        // domain - that redirection URL contains auth. token.
                    }
                });
            }
        };
    }
]);