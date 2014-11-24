(function() {
  'use strict';

var app = angular.module('treeApp', ['ui.tree', 'ui.tree-filter', 'ui.highlight'])
  .config(function($interpolateProvider, $sceProvider, $sceDelegateProvider, $locationProvider, uiTreeFilterSettingsProvider) {
      $locationProvider.html5Mode(true);
      $interpolateProvider.startSymbol('{[{').endSymbol('}]}');
      uiTreeFilterSettingsProvider.descendantCollection = "__children";
  });

app.factory('TopicsFactory',  function($http) {
  return {
        getTopics: function(languageCode) {
            return $http({
              method: "GET",
              url: Routing.generate("newscoop_newscoop_topics_tree"),
              params: {_code: languageCode}
            });
        },
        getLanguages: function() {
            return $http.get(Routing.generate("newscoop_newscoop_topics_getlanguages"));
        },
        deleteTopic: function(id) {
            return $http.post(Routing.generate("newscoop_newscoop_topics_delete", {id: id}));
        },
        deleteTopicTranslation: function(id) {
            return $http.post(Routing.generate("newscoop_newscoop_topics_deletetranslation", {id: id}));
        },
        addTopic: function(formData) {
          return $http({
            method: "POST",
            url: Routing.generate("newscoop_newscoop_topics_add"),
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            data: $.param(formData)
          });
        },
        updateTopic: function(formData, id, languageCode) {
          return $http({
            method: "POST",
            url: Routing.generate("newscoop_newscoop_topics_edit", {id: id}),
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            data: $.param(formData),
            params: {_code: languageCode}
          });
        },
        addTranslation: function(formData, id) {
          return $http({
            method: "POST",
            url: Routing.generate("newscoop_newscoop_topics_addtranslation", {id: id}),
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            data: $.param(formData)
          });
        },
    };
});

app.controller('treeCtrl', function($scope, TopicsFactory, $filter) {
    $scope.treeFilter = $filter('uiTreeFilter');
    $scope.availableFields = ['content', 'title'];
    $scope.supportedFields = ['content', 'title'];
    var languageCode = null;

    TopicsFactory.getTopics().success(function (data) {
       $scope.data = data.tree;
    }).error(function(data, status){
        flashMessage(response.message, 'error');
    });

    TopicsFactory.getLanguages().success(function (data) {
       $scope.languageList = data.languages;
    }).error(function(data, status){
        flashMessage(response.message, 'error');
    });

    var updateList = function (children, id) {
        if (children) {
            for (var i = 0; i < children.length; i++) {
                if (children[i].id == id) {
                  children.splice(children.indexOf(children[i]), 1);

                  return true;
                }

                var found = updateList(children[i].__children, id);
                if (found) {
                  return true;
                }
            }
        }
    };

    var updateTranslations = function (children, id) {
        if (children) {
            for (var i = 0; i < children.length; i++) {
                angular.forEach(children[i].translations, function(value, key) {
                  if (value.id == id) {
                    children[i].translations.splice(children[i].translations.indexOf(value), 1);

                    return true;
                  }
                });

                var found = updateTranslations(children[i].__children, id);
                if (found) {
                  return true;
                }
            }
        }
    };

    var updateAfterAddTranslation = function (children, id, response) {
        if (children) {
            for (var i = 0; i < children.length; i++) {
                if (children[i].id == id) {
                  children[i].translations.push({id: response.topicTranslationId, locale: response.topicTranslationLocale, field: "title", content: response.topicTranslationTitle });
                  return children[i];
                }

                var found = updateAfterAddTranslation(children[i].__children, id, response);
                if (found) {
                  return found;
                }
            }
        }
    };

    var removeTopicId = null;
    $scope.removeTopicAlert = function(topicId) {
      removeTopicId = topicId;
    }

    $scope.removeTopic = function() {
      TopicsFactory.deleteTopic(removeTopicId).success(function (response) {
        if (response.status) {
          flashMessage(response.message);
          updateList($scope.data, topicId);
        } else {
          flashMessage(response.message, 'error');
        }
      }).error(function(response, status){
          flashMessage(response.message, 'error');
      });
    };

    $scope.removeTranslation = function(translationId)
    {
      TopicsFactory.deleteTopicTranslation(translationId).success(function (response) {
        if (response.status) {
          flashMessage(response.message);
          updateTranslations($scope.data, translationId);
        } else {
          flashMessage(response.message, 'error');
        }
      }).error(function(response, status){
          flashMessage(response.message, 'error');
      });
    }

    $scope.toggle = function(scope) {
      scope.toggle();
    };

    var getRootNodesScope = function() {
      return angular.element(document.getElementById("tree-root")).scope();
    };

    $scope.collapseAll = function() {
      var scope = getRootNodesScope();
      scope.collapseAll();
    };

    $scope.expandAll = function() {
      var scope = getRootNodesScope();
      scope.expandAll();
    };

    $scope.startEditing = function(scope) {
      if (scope.editing) {
        scope.editing = false;
      } else {
        scope.editing = true;
      }
    };

    $scope.formData = {};
    $scope.subtopicForm = {};
    $scope.addNewTopic = function(topicId, event) {
      var addFormData = {
            topic: {
                title: $scope.formData.title,
            },
            _csrf_token: token
        }

      if (topicId !== undefined) {
          addFormData.topic["title"] = $scope.subtopicForm.title;
          addFormData.topic["parent"] = topicId;
      }

      TopicsFactory.addTopic(addFormData).success(function (response) {
        if (response.status) {
          flashMessage(response.message);
          if (topicId == undefined) {
              $scope.data.push({ id: response.topicId, title: response.topicTitle });
          }
          $scope.formData = null;
        } else {
          flashMessage(response.message, 'error');
        }
      }).error(function(response, status){
          flashMessage(response.message, 'error');
      });
    };

    $scope.editFormData = {};
    $scope.updateTopic = function(node, event) {
       var postData = {
          topic: {
              title: node.title,
          },
          _csrf_token: token
      };

      TopicsFactory.updateTopic(postData, node.id, languageCode).success(function (response) {
        if (response.status) {
          flashMessage(response.message);
          $scope.editFormData = null;
        } else {
          flashMessage(response.message, 'error');
        }
      }).error(function(response, status){
          flashMessage(response.message, 'error');
      });
    };

    $scope.onLanguageChange = function(language) {
        languageCode = language.code;
    }

    $scope.onFilterLanguageChange = function(langCode) {
        languageCode = langCode;
        $scope.languageCode = langCode;
        TopicsFactory.getTopics(langCode).success(function (data) {
           $scope.data = data.tree;
        }).error(function(data, status){
            flashMessage(response.message, 'error');
        });
    }

    $scope.translationForm = {};
    $scope.addTranslation = function(topicId) {
      var postData = {
          topicTranslation: {
              title: $scope.translationForm.title,
              locale: languageCode
          },
          _csrf_token: token
      };

      TopicsFactory.addTranslation(postData, topicId).success(function (response) {
        if (response.status) {
          flashMessage(response.message);
          updateAfterAddTranslation($scope.data, topicId, response);
        } else {
          flashMessage(response.message, 'error');
        }
      }).error(function(response, status){
          flashMessage(response.message, 'error');
      });
    };
  })
    /**
     * Ad-hoc $sce trusting to be used with ng-bind-html
     */
        .filter('trust', function ($sce) {
            return function (val) {
                return $sce.trustAsHtml(val);
            };
        });
})();
