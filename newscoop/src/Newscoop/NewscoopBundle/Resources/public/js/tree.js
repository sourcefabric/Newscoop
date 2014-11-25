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
        moveTopic: function(id, before) {
            return $http.post(Routing.generate("newscoop_newscoop_topics_move", {id: id, before: before}));
        },
        deleteTopicTranslation: function(id) {
            return $http.post(Routing.generate("newscoop_newscoop_topics_deletetranslation", {id: id}));
        },
        addTopic: function(formData, languageCode) {
          return $http({
            method: "POST",
            url: Routing.generate("newscoop_newscoop_topics_add"),
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            data: $.param(formData),
            params: {_code: languageCode}
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
    $scope.treeOptions = {
      dropped: function(event) {
        var array = [];
        /*if (event.dest.nodesScope.$$childHead.$$prevSibling) {//
          console.log('$$childHead.$$prevSibling');
          console.log(event.dest.nodesScope.$$childHead.$$prevSibling.node);
        }
        if (event.dest.nodesScope.$$childHead.$$nextSibling) {
          console.log('$$childHead.$$nextSibling');
          console.log(event.dest.nodesScope.$$childHead.$$nextSibling.node);
          array.push(event.dest.nodesScope.$$childHead.$$nextSibling.node);
        }
        if (event.dest.nodesScope.$$childTail.$$prevSibling) {
          console.log('$$childTail.$$prevSibling');
          console.log(event.dest.nodesScope.$$childTail.$$prevSibling.node);
           array.push(event.dest.nodesScope.$$childTail.$$prevSibling.node);
        }
        if (event.dest.nodesScope.$$childTail.$$nextSibling) {//
          console.log('$$childTail.$$nextSibling');
          console.log(event.dest.nodesScope.$$childTail.$$nextSibling.node);
         
        }*/
//console.log();
        var result = event.dest.nodesScope.$modelValue;
        var next = null;
        //var index = array.indexOf(event.dest.nodesScope.$modelValue);
        var rr = $.each(result, function(i){
            if(result[i].id === event.source.nodeScope.$modelValue.id) {
                var l = result.length;

              var current = result[i];
              var previous = result[(i+l-1)%l];
              next = result[(i+1)%l];
              //console.log(previous, next);
              if (previous == next) {
                //result = previous;
                return previous;
              }
                return next;
            }
        });
        
        //console.log(event.source.nodeScope.$modelValue.id, next.id);
        //console.log(event.source.nodeScope.$$childHead.$nodeScope.$modelValue);
        console.log(event.source.nodeScope);

        /*TopicsFactory.moveTopic(event.source.nodeScope.$modelValue.id, next.id).success(function (response) {
          if (response.status) {
            flashMessage(response.message);
          } else {
            flashMessage(response.message, 'error');
          }
        }).error(function(response, status){
            flashMessage(response.message, 'error');
        });*/
        
      },
      beforeDrop: function(event) {
        console.log(event);

      }
    };
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

    var updateAfterAddSubtopic = function (children, id, response) {
        if (children) {
            for (var i = 0; i < children.length; i++) {
                if (children[i].id == id) {
                  if (children[i].__children == undefined) {
                    console.log(children[i]);
                    children[i]['__children'] = [{id: response.topicId, locale: response.locale, title: response.topicTitle }];
                    children[i]['__children'][0]['translations'] = [{locale: response.locale, field: "title", content: response.topicTitle }];
                  } else {
                    console.log(children[i].__children);
                    children[i].__children.push({
                      id: response.topicId,
                      locale: response.locale,
                      title: response.topicTitle,
                      translations: [{locale: response.locale, field: "title", content: response.topicTitle }]
                    });
                    //children[i].__children.translations.push([{locale: response.locale, field: "title", content: response.topicTitle }]);
                  }
                  return children[i];
                }

                var found = updateAfterAddSubtopic(children[i].__children, id, response);
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
          $('#removeAlert').modal('hide');
          flashMessage(response.message);
          updateList($scope.data, removeTopicId);
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

    $scope.dropped = function(e) {
        console.log (e.source);     
      }
    $scope.moveTopic = function() {
      
      /*TopicsFactory.moveTopic(112, 109).success(function (response) {
        if (response.status) {
          flashMessage(response.message);
        } else {
          flashMessage(response.message, 'error');
        }
      }).error(function(response, status){
          flashMessage(response.message, 'error');
      });*/
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
            topic: {},
            _csrf_token: token
        }

      if (topicId !== undefined) {
          addFormData.topic["title"] = $scope.subtopicForm.title;
          addFormData.topic["parent"] = topicId;
      } else {
        addFormData.topic["title"] = $scope.formData.title;
      }

      TopicsFactory.addTopic(addFormData, languageCode).success(function (response) {
        if (response.status) {
          flashMessage(response.message);
          if (topicId == undefined) {
              $scope.data.push({
                id: response.topicId,
                title: response.topicTitle,
                root: response.topicId,
                translations: [{ content: response.topicTitle, locale: response.locale}]
              });

          } else {
            updateAfterAddSubtopic($scope.data, topicId, response)
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
