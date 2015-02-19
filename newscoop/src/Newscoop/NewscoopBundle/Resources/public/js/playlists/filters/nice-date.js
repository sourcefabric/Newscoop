'use strict';

/**
* AngularJS filter for converting datetime to a nice format
*/
angular.module('playlistsApp').filter('niceDate', [
    'currentTime',
    '$filter',
    function (currentTime, $filter) {
        return function (input) {
            var date;
            if (typeof input === 'string') {
                date = new Date(Date.parse(input));
            } else {
                date = input;
            }
            if (currentTime.isToday(date)) {
                return 'today @ ' + $filter('date')(date, 'H:mm');
            } else {
                return $filter('date')(date, 'dd.MM.yyyy @ H:mm');
            }
        };
    }
]);
