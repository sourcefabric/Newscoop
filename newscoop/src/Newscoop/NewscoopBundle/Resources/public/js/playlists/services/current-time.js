'use strict';

angular.module('playlistsApp').service('currentTime', function () {
    var preset = false;
    this.set = function (newPreset) {
        preset = newPreset;
    };
    this.unset = function () {
        preset = false;
    };
    this.get = function () {
        if (preset === false) {
            return new Date();
        } else {
            return preset;
        }
    };
    this.isToday = function (date) {
        var now = this.get();
        return date.toDateString() === now.toDateString();
    };
});
