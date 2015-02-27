'use strict';

/**
* Constructor function for modal confirmations controllers.
*
* @class ModalCtrlConstructor
* @param $scope {Object} AngularJS $scope object
* @param $modalInstance {Object} AngularJS UI instance of the modal
*     window the coontroller controls.
* @param title {String} title of the modal window
* @param text {String} text in the modal window's body
* @param okText {String} confirm button text
* @param cancelText {String} cancel button text
*/
// XXX: this is defined as a variable, because there were *big* problems with
// injecting controller as a dependency (and we need it to pass it as a
// parameter to $modal.open())
var ModalCtrlConstructor = function ($scope, $modalInstance, title, text, okText, cancelText) {

    $scope.title = title;
    $scope.text = text;
    $scope.okText = okText;
    $scope.cancelText = cancelText;

    /**
    * Closes the modal with a resolution of OK.
    * @method ok
    */
    $scope.ok = function () {
        $modalInstance.close(true);
    };

    /**
    * Closes the modal with a resolution of CANCEL.
    * @method ok
    */
    $scope.cancel = function () {
        $modalInstance.dismiss(false);
    };
};

// needed so that it works even when the code gets minified
ModalCtrlConstructor.$inject = ['$scope', '$modalInstance', 'title', 'text', 'okText', 'cancelText'];


/**
* AngularJS Service for creating modal dialog instances.
*
* @class modalFactory
*/
angular.module('playlistsApp').factory('modalFactory', [
    '$modal',
    function ($modal) {

        return {
            /**
            * @class modalFactory
            */

            /**
            * Creates a new confirmation dialog instance.
            *
            * @class createConfirmInstance
            * @param title {String} title of the modal window
            * @param text {String} text in the modal window's body
            * @param okText {String} confirm button text
            * @param cancelText {String} cancel button text
            * @param isDanger {Boolean} whether to create a "danger" version
            *   of the confirmation dialog
            * @return {Object} AngularJS UI modal dialog instance
            */
            _createConfirmInstance: function (title, text, okText, cancelText, isDanger) {
                // this method, although "private", is publicly exposed in the
                // factory object for easier testability
                var templateUrl = isDanger ?
                    '../../bundles/newscoopnewscoop/views/modal-danger.html' :
                    '../../bundles/newscoopnewscoop/views/modal-confirm.html';

                return $modal.open({
                    templateUrl: templateUrl,
                    controller: ModalCtrlConstructor,
                    backdrop: 'static',
                    keyboard: false,
                    resolve: {
                        title: function () {
                            return title;
                        },
                        text: function () {
                            return text;
                        },
                        okText: function () {
                            return okText;
                        },
                        cancelText: function () {
                            return cancelText;
                        }
                    }
                });
            },

            /**
            * Opens a "light" confirmation dialog (generally used for
            * confirming non-critical actions).
            * @method confirmLight
            * @param title {String} title of the modal window
            * @param text {String} text in the modal window's body
            * @param okText {String} confirm button text
            * @param cancelText {String} cancel button text
            * @return {Object} modal dialog instance
            */
            confirmLight: function (title, text, okText, cancelText) {
                return this._createConfirmInstance(title, text, okText, cancelText, false);
            },

            /**
            * Opens a "danger" confirmation dialog (generally used for
            * confirming critical actions with major and/or irreversible
            * effects).
            * @method confirmDanger
            * @param title {String} title of the modal window
            * @param text {String} text in the modal window's body
            * @param okText {String} confirm button text
            * @param cancelText {String} cancel button text
            * @return {Object} modal dialog instance
            */
            confirmDanger:  function (title, text, okText, cancelText) {
                return this._createConfirmInstance(title, text, okText, cancelText, true);
            },
        };

    }
]);
