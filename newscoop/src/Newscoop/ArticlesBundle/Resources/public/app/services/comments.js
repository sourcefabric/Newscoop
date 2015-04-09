'use strict';

/**
* A factory which is responsible to load comments using ngInfinite
*
* @class Comments
*/
angular.module('editorialCommentsApp').factory('Comments', function($http, $activityIndicator) {
  	var Comments = function() {
	    this.items = [];
	    this.busy = false;
	    this.after = 1;
	    this.itemsCount = 1;
	    this.articleNumber = articleNumber;
        this.articleLanguage = articleLanguage;
	};

	Comments.prototype.getOne = function(url) {
        return $http({
            method: "GET",
            url: url
        });
	}

	Comments.prototype.create = function(formData) {
        return $http({
            method: "POST",
            url: Routing.generate("newscoop_gimme_articles_create_editorial_comment", {
            	language: this.articleLanguage,
	            number: this.articleNumber
            }),
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            data: $.param(formData)
        });
	}

	Comments.prototype.delete = function(commentId) {
        return $http({
            method: "DELETE",
            url: Routing.generate("newscoop_gimme_articles_remove_editorial_comment", {
            	language: this.articleLanguage,
	            number: this.articleNumber,
	            commentId: commentId
            })
        });
	}

	Comments.prototype.update = function(formData, commentId) {
        return $http({
            method: "POST",
            url: Routing.generate("newscoop_gimme_articles_edit_editorial_comment", {
            	language: this.articleLanguage,
	            number: this.articleNumber,
	            commentId: commentId
            }),
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            data: $.param(formData)
        });
	}

	Comments.prototype.refresh = function() {

	    var url = Routing.generate("newscoop_gimme_articles_get_editorial_comments", {
            language: this.articleLanguage,
            number: this.articleNumber,
            order: 'nested',
        });

	    if (this.itemsCount <= 5) {
	    	if (this.busy) {
	    		return;
	    	}

	    	this.busy = true;
		    $http.get(url).success(function (data) {
		      	this.itemsCount = data.items.length;
		      	if (this.itemsCount > 0) {
		      		// TODO add items which dont exist in local array and exist in response
		      		// same for removing
		      		this.items = data.items;
		      	}

		      this.busy = false;
		      $activityIndicator.stopAnimating();
		    }.bind(this));
		}
  	}

  	Comments.prototype.nextPage = function() {
	    if (this.busy) return;
	    this.busy = true;

	    var url = Routing.generate("newscoop_gimme_articles_get_editorial_comments", {
		    language: this.articleLanguage,
            number: this.articleNumber,
		    order: 'nested'
		});

	    if (!$http.defaults.headers.common.Authorization) {
		    $http.get(Routing.generate("newscoop_gimme_users_getuseraccesstoken", {
	            clientId: clientId
		    })).success(function(data, status, headers, config) {
				$http.defaults.headers.common.Authorization = 'Bearer ' + data.access_token;
		  		$http.get(url).success(function (data) {
				    this.items = data.items;
				    this.busy = false;
				}.bind(this));
			}.bind(this));
		} else {
		  	$http.get(url).success(function (data) {
				this.items = data.items;
				this.busy = false;
			}.bind(this));
		}
  	};

  	return Comments;
});