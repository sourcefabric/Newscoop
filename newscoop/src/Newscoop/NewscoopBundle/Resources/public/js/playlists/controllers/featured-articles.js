'use strict';

/**
* AngularJS controller for loading featured articles.
*
* @class FeaturedController
*/
angular.module('playlistsApp').controller('FeaturedController', [
    '$scope',
    'Playlist',
    '$timeout',
    function (
        $scope,
        Playlist,
        $timeout
    ) {

    $scope.sortableConfig = {
        group: 'articles',
        animation: 150,
        onAdd: function (evt/**Event*/){
         var item,
            number,
            occurences,
            isInLogList = false,
            limit;
            item = evt.model; // the current dragged article
            number = item.number;
            occurences = 0;
            angular.forEach($scope.$parent.featuredArticles, function(value, key) {
                if (value.number == number) {
                    occurences++;
                }
            });

            if (occurences > 1) {
                $scope.$parent.featuredArticles.splice(evt.newIndex, 1);
                Playlist.removeItemFromLogList(number, 'link');
                flashMessage(Translator.trans('Item already exists in the list', {}, 'articles'), 'error');

                return true;
            }

            limit = $scope.$parent.playlist.selected.maxItems;
            // show alert with revert button
            if (limit && limit != 0 && $scope.$parent.featuredArticles.length > limit) {
                // article that shouldn't be removed, its needed to determine on
                // which position it's placed so we can remove the last one elment
                // from the list or the one before last - see removeLastArticle function
                $scope.articleNotToRemove = item;
                $scope.articleOverLimitIndex = evt.newIndex + 1;
                $scope.articleOverLimitNumber = number;
                $scope.showLimitAlert = true;
                $scope.countDown = 6;
                $scope.startCountDown();

                return true;
            }

            isInLogList = _.some(
                Playlist.getLogList(),
                {number: number, _method: 'unlink'}
            );

            if (!isInLogList) {
                // this check prevents inserting duplicate article
                // when it's drag from the available articles list.
                // onSort event is executed before onEnd so in both of them it will
                // insert the same value to the log list
                isInLogList = _.some(
                    Playlist.getLogList(),
                    {number: number}
                    );

                if (!isInLogList) {
                    // add article to log list, so we can save it later using batch save
                    item._method = "link";
                    item._order = evt.newIndex + 1;
                    Playlist.addItemToLogList(item);
                }
            } else {
                Playlist.removeItemFromLogList(number, 'unlink');
            }

        },
        onSort: function (evt/**Event*/){
            var article = evt.model;
            article._order = evt.newIndex + 1;
            article._method = "link";
            Playlist.addItemToLogList(article);

        }
    };

    // stops, starts counter
    $scope.isCounting = false;
    $scope.showLimitAlert = false;

    $scope.startCountDown = function () {
        countDown();
    }

    /**
     * This function count seconds after which revert popup will be closed
     * and removes last article from the playlist if the limit is reached,
     * inserts new article
     */
    var countDown = function(){
       if($scope.isCounting) {
            return;
       }

       $scope.isCounting = true;
       (function countEvery() {
            if ($scope.isCounting) {
                $scope.countDown--;
                $timeout(countEvery, 1000);
                if ($scope.countDown === 0) {
                    removeLastArticle();
                }
            }
        }());
    }

    /**
     * It removes last article from the playlist if the limit is reached,
     * inserts new article. It is called from FeaturedController.
     */
    $scope.removeLastInsertNew = function () {
        removeLastArticle();
    }

    /**
     * It removes last article from the playlist if the limit is reached,
     * inserts new article
     */
    var removeLastArticle =  function () {
        // remove one before last article from the featured articles list
        // when we drag-drop to the list as a last element, thats why we need to remove
        // one before last article else remove last one (-1)
        var articleToRemove = $scope.featuredArticles[$scope.featuredArticles.length - 1];
        if ($scope.articleNotToRemove._order == $scope.featuredArticles.length) {
            articleToRemove = $scope.featuredArticles[$scope.featuredArticles.length - 2];
        }

        articleToRemove._method = "unlink";
        _.remove(
            $scope.featuredArticles,
            {number: articleToRemove.number}
        );

        Playlist.addItemToLogList(articleToRemove);
        var logList = Playlist.getLogList();

        // we have to now replace last element with one before last in log list
        // so it can be save in API in a proper order, actually we first add a
        // new article to the featured articles list and then we unlink the last one.
        // We need to do it in a reverse way, so we first unlink, and then add a new one.
        var lastElement = logList[logList.length - 1];
        var beforeLast = logList[logList.length - 2];

        logList[logList.length - 1] = beforeLast;
        logList[logList.length - 2] = lastElement;

        Playlist.setLogList(logList);
        $scope.showLimitAlert = false;
        $scope.isCounting = false;
    }

    /**
     * It reverts new article insertion over the playlist's limit
     */
    $scope.revertAction = function () {
        if ($scope.articleOverLimitIndex !== undefined) {
            $scope.featuredArticles.splice($scope.$parent.articleOverLimitIndex, 1);
            $scope.showLimitAlert = false;
            $scope.isCounting = false;
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