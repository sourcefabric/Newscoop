'use strict';

/**
* AngularJS controller for managing various actions on the editorial comments, e.g.
* adding new comments, resolving comments etc.
*
* @class EditorialCommentsCtrl
*/
angular.module('editorialCommentsApp').controller('EditorialCommentsCtrl', [
	'$scope',
	'$activityIndicator',
	'$timeout',
	'Comments',
	'$interval',
	function (
		$scope,
		$activityIndicator,
		$timeout,
		Comments,
		$interval
	) {

	var comments = new Comments();
	$scope.comments = comments;
	$scope.stopRefreshing = false;

	$interval(function(){
		if (!$scope.stopRefreshing) {
			comments.refresh();
		}
    }.bind(this), 20000);


	/**
     * Updates comments array. It adds a new comment to the array
     * of the comments.
     *
     * @param  {array}   commentsArray Array of the comments data
     * @param  {integer} parentId      Parent comment id
     * @param  {object}  newComment    Newly inserted comment object
     */
    var addChildComment = function (commentsArray, parentId, newComment) {
    	var index = 0;
        for (var i = 0; i < commentsArray.length; i++) {
            if (commentsArray[i].parent && commentsArray[i].parent.id == parentId) {
            	index = commentsArray.indexOf(commentsArray[i]);
            }
        }

        index = index + 1;
        // add it as first child of the parent comment
        if (index == 1) {
        	for (var i = 0; i < commentsArray.length; i++) {
	            if (commentsArray[i].id == parentId) {
	            	index = commentsArray.indexOf(commentsArray[i]) + 1;
	            }
	        }
        }

        commentsArray.splice(index, 0, newComment);
    };

    /**
     * Removes comment and its children from the array of not solved comments
     *
     * @param  {array}   commentsArray Array of comments
     * @param  {integer} id       comment id
     */
    var removeCommentWithChildrenFromArray = function (commentsArray, id) {
    	var removeComments = [];
        for (var i = 0; i < commentsArray.length; i++) {
            if (commentsArray[i].id == id || (commentsArray[i].parent && commentsArray[i].parent.id == id)) {
            	removeComments.push(commentsArray[i]);
            }
        }

        for (var i = 0; i < removeComments.length; i++) {
            commentsArray.splice(commentsArray.indexOf(removeComments[i]), 1);
        }
    };

	/**
     * Hides/shows replying box
     *
     * @method showReplyBox
     * @param scope {object} currently selected element
     */
    $scope.showReplyBox = function(scope) {
      if (scope.isReplying) {
        scope.isReplying = false;
        $scope.stopRefreshing = false;
      } else {
        scope.isReplying = true;
        $scope.stopRefreshing = true;
      }
    };

    /**
     * Hides/shows edit box
     *
     * @method isEditing
     * @param scope {object} currently selected element
     */
    $scope.isEditing = function(scope) {
      if (scope.editing) {
        scope.editing = false;
        $scope.stopRefreshing = false;
      } else {
        scope.editing = true;
        $scope.stopRefreshing = true;
      }
    };

    /**
     * Hide button, hiding extra options like e.g. adding new substopic etc.
     *
     * @method hideExtraOptions
     * @parent scope {object} currently selected element in a tree
     */
    $scope.hide = function(scope) {
      scope.editing = false;
      scope.isReplying = false;
      $scope.stopRefreshing = false;
    };

    /**
     * Resolves editorial comment
     *
     * @method resolveComment
     * @param commentId {integer} comment's id
     */
    $scope.resolveComment = function(commentId) {
    	var postData = {
            editorial_comment: {
                resolved: true,
            },
            _csrf_token: token
        };

      	comments.update(postData, commentId).success(function (data) {
	        flashMessage(Translator.trans('editorial.alert.resolved', {}, 'comments'));
	        removeCommentWithChildrenFromArray($scope.comments.items, commentId);
	    }).error(function(data, status){
	        flashMessage(data.errors[0].message, 'error');
	    });
    };

    /**
     * Updates comment
     *
     * @method editComment
     * @param comment {object} comment object
     */
    $scope.editComment = function(comment) {
       var postData = {
          editorial_comment: {
              comment: comment.comment,
          },
          _csrf_token: token
      };

      comments.update(postData, comment.id).success(function (data) {
	        flashMessage(Translator.trans('editorial.alert.edited', {}, 'comments'));
			comment.editing = false;
	    }).error(function(data, status){
	        flashMessage(data.errors[0].message, 'error');
	    });

    };

    /**
     * Deletes comment
     *
     * @method deleteComment
     * @param comment {integer} comment's id
     */
    $scope.deleteComment = function(commentId) {
      comments.delete(commentId).success(function (data) {
	        flashMessage(Translator.trans('editorial.alert.deleted', {}, 'comments'));
	        removeCommentWithChildrenFromArray($scope.comments.items, commentId);
	    }).error(function(data, status){
	        flashMessage(data.errors[0].message, 'error');
	    });
    };

    $scope.textareaMessage = {};
    $scope.textareaReply = {};

    /**
     * Resolves editorial comment
     *
     * @method addComment
     * @param comment {integer} comment
     */
    $scope.addComment = function(comment) {
        var addFormData = {
            editorial_comment: {},
            _csrf_token: token
        }

        addFormData.editorial_comment["comment"] = $scope.textareaMessage.comment;

        if (comment.id && $scope.textareaReply.comment) {
        	addFormData.editorial_comment["comment"] = $scope.textareaReply.comment;
        	addFormData.editorial_comment["parent"] = comment.id;
        }

      	comments.create(addFormData).success(function (data, code, headers) {
	        comments.getOne(headers('X-Location')).success(function (data) {
	        	if (addFormData.editorial_comment.parent) {
	        		addChildComment(comments.items, addFormData.editorial_comment.parent, data);
	        	} else {
	        		comments.items.push(data);
	        	}
	        	flashMessage(Translator.trans('editorial.alert.added', {}, 'comments'));
	        	$scope.textareaMessage = {};
	        	$scope.textareaReply = {};
	        	comment.isReplying = false;
	        	$scope.enableTyping = false
	        }).error(function(data, status){
		        flashMessage(data.errors[0].message, 'error');
		    });
	    }).error(function(data, status){
	        flashMessage(data.errors[0].message, 'error');
	    });
    };
}]);