'use strict';

angular.module('playlistsApp').controller('ModalCtrl', [
  '$scope',
  '$modal',
  'Playlist',
  function ($scope, $modal, Playlist) {

    /**
     * Saves playlist with all articles in it.
     * It makes batch link or unlink of the articles. It also
     * saves a proper order of the articles. All list's changes are saved
     * by clicking Save button.
     */
    var saveList = function () {
            var listname = $scope.$parent.formData.title,
            logList = [];
        var playlistExists = _.some(
            $scope.$parent.playlists,
            {title: listname}
        );

        var flash = flashMessage(Translator.trans('Processing', {}, 'messages'), null, true);
        $scope.$parent.processing = true;
        if (!playlistExists) {
            $scope.$parent.playlist.selected.title = listname;
            Playlist.createPlaylist($scope.$parent.playlist.selected).then(function () {
                flash.fadeOut();
                flashMessage(Translator.trans('List saved'));
                $scope.$parent.playlist.selected.id = Playlist.getListId();
                $scope.$parent.processing = false;
            }, function() {
                flashMessage(Translator.trans('Could not save the list'), 'error');
                flash.fadeOut();
            });

            return true;
        }

        if ($scope.$parent.playlist.selected !== undefined) {
            Playlist.updatePlaylist($scope.playlist.selected).then(function () {
                //$scope.$parent.featuredArticles = Playlist.getArticlesByListId($scope.$parent.playlist.selected);
                flash.fadeOut();
                $scope.$parent.processing = false;
            }, function() {
                flashMessage(Translator.trans('Could not save the list'), 'error');
                flash.fadeOut();

                return false;
            });
        }

        logList = Playlist.getLogList();
        if (logList.length == 0) {
            $scope.$parent.processing = false;
            flash.fadeOut();
            flashMessage(Translator.trans('List saved'));

            return true;
        }

        Playlist.batchUpdate(logList)
        .then(function () {
            flashMessage(Translator.trans('List saved'));
            Playlist.clearLogList();
            flash.fadeOut();
            $scope.$parent.processing = false;
        }, function() {
            flashMessage(Translator.trans('Could not save the list'), 'error');
        });
    }

  var reloadArticles = function () {
    $scope.$parent.featuredArticles = Playlist.getArticlesByListId($scope.$parent.playlist.selected);
  }

  $scope.openRemoveListModal = function () {
    var modalInstance = $modal.open({
      templateUrl: 'removeListModal.html',
      controller: 'ModalInstanceCtrl'
    });

    modalInstance.result.then(function () {
      if ($scope.$parent.playlist.selected.id !== undefined) {
        Playlist.deletePlaylist().then(function () {
          flashMessage(Translator.trans('Entry deleted.', {}, 'messsages'));
            _.remove(
              $scope.playlists,
              {id: $scope.$parent.playlist.selected.id}
            );

            $scope.$parent.playlist.selected = undefined;
        }, function () {
          flashMessage(Translator.trans('Error List', {}, 'articles'), 'error');
        });
      } else {
        $scope.$parent.playlist.selected = undefined;
      }
    });
  };

  $scope.openLimitModal = function () {
    var newLimit = $scope.$parent.playlist.selected.maxItems,
        oldLimit = $scope.$parent.playlist.selected.oldLimit;

    if (newLimit && newLimit != 0 && newLimit != oldLimit) {
      var modalInstance = $modal.open({
        templateUrl: 'limitModal.html',
        controller: 'ModalInstanceCtrl'
      });

       modalInstance.result.then(function () {
        saveList();
        $scope.$parent.playlist.selected.oldLimit = $scope.$parent.playlist.selected.maxItems;
        reloadArticles();
      }, function () {
          return false;
      });
    } else {
       saveList();
       reloadArticles();
    }
  };
}]);

angular.module('playlistsApp').controller('ModalInstanceCtrl', function ($scope, $modalInstance) {

  $scope.ok = function () {
    $modalInstance.close();
  };

  $scope.cancel = function () {
    $modalInstance.dismiss('cancel');
  };
});