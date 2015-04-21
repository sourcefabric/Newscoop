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
        var result = data.items;
        if (this.items.length > result.length) {
          this.items = _.difference(result, this.items);
        } else {
          for (var i = 0; i < result.length; i++) {
            var found = false;
            for (var j = 0; j < this.items.length; j++) {
              if (this.items[j].id == result[i].id) {
                found = true;
                break;
              }
            }

            if (found && (this.items[i].comment !== result[i].comment)) {
              this.items[i] = result[i];
            }

            if (!found) {
              this.items.splice(i, 0, result[i]);
            }
          }
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