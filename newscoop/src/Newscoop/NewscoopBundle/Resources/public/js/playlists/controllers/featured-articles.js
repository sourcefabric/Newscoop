'use strict';

/**
* AngularJS controller for loading featured articles.
*
* @class FeaturedController
*/
angular.module('playlistsApp').controller('FeaturedController', [
    '$scope',
    'Playlist',
    function (
        $scope,
        Playlist
    ) {

    $scope.sortableConfig = {
        group: 'articles',
        animation: 150,
        onSort: function (evt/**Event*/){
            var article = evt.model;
            // only when sorting list of featured articles (playlist)
            var logList = Playlist.getLogList();

            var limit = $scope.$parent.playlist.selected.maxItems;
            console.log(article);
            // we need to call countDown function here again, because in PlaylistController
            // onEnd event doesnt work on Firefox, thus showing revert alert wont work.
            // see: https://github.com/RubaXa/Sortable/issues/313
            // we check if list length and limit equals because dropped item is not inserted into
            // the list in onSort event yet
            // show alert with revert button
            if (limit && limit != 0 && $scope.$parent.featuredArticles.length == limit) {
                // make sure that the counter is not counting
                // if using browser diffrent than FF, counter will be started in onEnd event
                // thats why we need to check if it't not running already
                if ($scope.$parent.isCounting === false) {
                    // setting article order and increasing by 1 because in
                    // onSort event item is not inserted into the list yet, which means
                    // that the list length equals the length of existing items in it
                    // (without the dragged-dropped one)
                    article._order = evt.newIndex + 1;
                    $scope.$parent.articleNotToRemove = article;
                    $scope.$parent.articleOverLimitIndex = evt.newIndex;
                    $scope.$parent.articleOverLimitNumber = article.number;
                    $scope.$parent.showLimitAlert = true;

                    $scope.$parent.countDown = 6;
                    $scope.$parent.startCountDown();

                    return true;
                }
            }

            article._order = evt.newIndex + 1;
            article._method = "link";
            Playlist.addItemToLogList(article);
        }
    };

    /**
     * It reverts new article insertion over the playlist's limit
     */
    $scope.revertAction = function () {
        if ($scope.$parent.articleOverLimitIndex !== undefined) {
            $scope.$parent.featuredArticles.splice($scope.$parent.articleOverLimitIndex, 1);
            $scope.$parent.showLimitAlert = false;
            $scope.$parent.isCounting = false;
            Playlist.removeItemFromLogList($scope.$parent.articleOverLimitNumber, 'link');
        }
    }

    /**
     * Deletes single article from the currently loaded playlist
     *
     * @param  {Object} article Article object
     */
    $scope.deleteSingleArticle = function (article) {
        Playlist.removeSingleArticle(article.number, article.language)
        .then(function () {
            _.remove(
                $scope.$parent.featuredArticles,
                {number: article.number}
            );
            flashMessage(Translator.trans('List updated.', {}, 'articles'));
            Playlist.setCurrentPlaylistArticles($scope.$parent.featuredArticles);
        });
    };

    /**
     * Removes article from the playlist.
     * Checks if article exist in the list of available articles (on the current page)
     * if it doesn't exist put it at the first position, else don't add it -
     * purpose of it is that this article can be reused again after it will be removed
     * from the list of Featured comments, so no need to search for it again etc.
     *
     * @param  {Object} article Article object
     */
    $scope.removeArticle = function (article) {
        var availablArticles = [],
            featuredArticles = [],
            exists = false,
            isInLogList = false;

        availablArticles = $scope.$parent.tableParams.data;
        featuredArticles = $scope.$parent.featuredArticles;
        exists = _.some(
            availablArticles,
            {number: article.number}
        );

        if (!exists) {
            availablArticles.unshift(article);
        }

        _.remove(
            featuredArticles,
            {number: article.number}
        );

        // check if the article exists in the logList,
        // if user will drag article to the playlist, it will add new field called "_method"
        // set to "link" value. Then if user will remove article from the featured articles' list
        // it should remove it from the logList to not pass fake data to the server, else it will add
        // article to the logList with field "_method": "unlink" so the article can be unlinked from
        // the playlist.
        isInLogList = _.some(
            Playlist.getLogList(),
            {number: article.number, _method: 'link'}
        );

        if (!isInLogList) {
            // set method for the removed object so we can pass it to
            // the API endpoint and remove item using batch remove feature
            article._method = "unlink";
            Playlist.addItemToLogList(article);
        } else {
            Playlist.removeItemFromLogList(article.number, 'link');
        }

        Playlist.setCurrentPlaylistArticles(featuredArticles);
    }

    /**
     * Updates parent controller's playlistLimit variable, so it can disable save button
     * when limit is incorrect, i.e. is string not number
     *
     * @param  {Object} scope Current scope
     */
    $scope.updateParentLimit = function (scope) {
        $scope.$parent.playlistLimit = scope.limitForm.$valid;
    }
}]);