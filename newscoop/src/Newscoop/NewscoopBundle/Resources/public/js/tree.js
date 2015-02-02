(function() {
  'use strict';

var app = angular.module('treeApp', ['ui.tree', 'ui.tree-filter', 'ui.highlight', 'checklist-model'])
  .config(function($interpolateProvider, $sceProvider, $sceDelegateProvider, uiTreeFilterSettingsProvider) {
      $interpolateProvider.startSymbol('{[{').endSymbol('}]}');
      uiTreeFilterSettingsProvider.descendantCollection = "__children";
  });

/**
* A factory which keeps routes to manage topics.
*
* @class Topic
*/
app.factory('TopicsFactory',  function($http) {
  return {
        getTopics: function(language, articleNumber) {
            return $http({
              method: "GET",
              url: Routing.generate("newscoop_newscoop_topics_tree"),
              params: {
                _code: language,
                _articleNumber: articleNumber
              }
            });
        },
        getLanguages: function() {
            return $http.get(Routing.generate("newscoop_newscoop_topics_getlanguages"));
        },
        isAttached: function(id) {
            return $http.get(Routing.generate("newscoop_newscoop_topics_isattached", {id: id}));
        },
        deleteTopic: function(id) {
            return $http.post(Routing.generate("newscoop_newscoop_topics_delete", {id: id}));
        },
        moveTopic: function(id, params) {
            return $http({
              method: "POST",
              url: Routing.generate("newscoop_newscoop_topics_move", {id: id}),
              data: params
            });
        },
        deleteTopicTranslation: function(id) {
            return $http.post(Routing.generate("newscoop_newscoop_topics_deletetranslation", {id: id}));
        },
        addTopic: function(formData, language) {
          return $http({
            method: "POST",
            url: Routing.generate("newscoop_newscoop_topics_add"),
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            data: $.param(formData),
            params: {_code: language}
          });
        },
        attachTopics: function(formData, articleNumber, language) {
          return $http({
            method: "POST",
            url: Routing.generate("newscoop_newscoop_topics_attachtopic"),
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            data: $.param(formData),
            params: {
              _articleNumber: articleNumber,
              _languageCode: language
            }
          });
        },
        updateTopic: function(formData, id, language) {
          return $http({
            method: "POST",
            url: Routing.generate("newscoop_newscoop_topics_edit", {id: id}),
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            data: $.param(formData),
            params: {_code: language}
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

/**
* AngularJS controller for managing various actions on the topics, e.g.
* changing topic's position in tree, adding new topics, adding new translations etc.
*
* @class TreeCtrl
*/
app.controller('treeCtrl', function($scope, TopicsFactory, $filter) {
    $scope.treeOptions = {
      dropped: function(event) {
        var closestNode = event.dest.nodesScope.$$childHead;
        var draggedNode = event.dest.nodesScope.$$childTail;
        var params = {};

        if (draggedNode.$first) {
            params['first'] = true;
            if (event.dest.nodesScope.$parent.$modelValue) {
              params['parent'] = event.dest.nodesScope.$parent.$modelValue.id;
            } else {
              params['order'] = [];
              angular.forEach(event.dest.nodesScope.$modelValue, function(value, key) {
                  params['order'].push(parseInt(value.id));
              });

              params['order'] = params['order'].join();
              if (draggedNode.node.id != draggedNode.node.root) {
                  params['asRoot'] = true;
              }
            }
        }

        if (draggedNode.$last) {
            params['last'] = true;
            if (event.dest.nodesScope.$parent.$modelValue) {
              params['parent'] = event.dest.nodesScope.$parent.$modelValue.id;
            } else {
              params['order'] = [];
              angular.forEach(event.dest.nodesScope.$modelValue, function(value, key) {
                  params['order'].push(parseInt(value.id));
              });

              params['order'] = params['order'].join();
              if (draggedNode.node.id != draggedNode.node.root) {
                  params['asRoot'] = true;
              }
            }
        }

        if (draggedNode.$middle) {
            params['middle'] = true;
            if (event.dest.nodesScope.$parent.$modelValue) {
              var closestIndex = closestNode.$index;
              var draggedIndex = draggedNode.$index;
              if (draggedIndex > closestIndex) {
                 params['parent'] = closestNode.node.id;
              }
            } else {

              params['order'] = [];
              angular.forEach(event.dest.nodesScope.$modelValue, function(value, key) {
                  params['order'].push(parseInt(value.id));
              });

              params['order'] = params['order'].join();
              if (draggedNode.node.id != draggedNode.node.root) {
                  params['asRoot'] = true;
              }
            }

        }

        moveTopic(event.source.nodeScope.$modelValue.id, params);
      }
    };
    $scope.treeFilter = $filter('uiTreeFilter');
    $scope.availableFields = ['content', 'title'];
    $scope.supportedFields = ['content', 'title'];
    var languageCode = null;
    var languageSelected = null;

    /**
     * Loads all topics. It loads all the topics in tree structure
     *
     * @method loadTopicsTree
     */
    $scope.loadTopicsTree = function() {
      TopicsFactory.getTopics().success(function (data) {
         $scope.data = data.tree;
      }).error(function(data, status){
          flashMessage(response.message, 'error');
      });
    }

    TopicsFactory.getLanguages().success(function (data) {
       $scope.languageList = data.languages;
    }).error(function(data, status){
        flashMessage(response.message, 'error');
    });

    /**
     * Moves topic. It moves topic to given position in a tree.
     *
     * @method moveTopic
     * @param draggedNode {string} topic's id which will be moved
     * @param params {array} parameters e.g. order, parent etc.
     */
    var moveTopic = function(draggedNode, params) {
       TopicsFactory.moveTopic(draggedNode, params).success(function (response) {
          if (response.status) {
            flashMessage(response.message);
          } else {
            flashMessage(response.message, 'error');
          }
        }).error(function(response, status){
            flashMessage(response.message, 'error');
        });
    }

    $scope.selectedTopics = {
      ids: []
    };

    /**
     * Get topics assigned to the article
     *
     * @param  {Array} array array of all the topics
     */
    var getSelectedTopics = function (array) {
        if (array) {
            for (var i = 0; i < array.length; i++) {
                if (array[i].attached) {
                  $scope.selectedTopics.ids.push(array[i].id);
                }

                getSelectedTopics(array[i].__children);
            }
        }
    };

    /**
     * Removes topic from the array of the topics
     *
     * @param  {array} children Array of children
     * @param  {integer} id     Topic id
     * @return {boolean}        true or false
     */
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

    /**
     * Removes topic from the array of the topics
     *
     * @param  {array} children Array of children
     * @param  {integer} id     Topic id
     * @return {boolean}        True when topic removed from array
     */
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

    /**
     * Removes topic's translation from the array of the topics' translations
     *
     * @param  {array} children   Array of children
     * @param  {integer} id       Topic id
     * @param  {object}  response Object with data returned from the server
     * @return {boolean}          True when topic removed from array
     */
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

    /**
     * Updates topics' array. It adds a new topic to the array
     * of the topics when it is added.
     *
     * @param  {array} children   Array of children
     * @param  {integer} id       Topic id
     * @param  {object}  response Object with data returned from the server
     * @return {object}           Returns added topic object
     */
    var updateAfterAddSubtopic = function (children, id, response) {
        if (children) {
            for (var i = 0; i < children.length; i++) {
                if (children[i].id == id) {
                  if (children[i].__children == undefined) {
                    children[i]['__children'] = [{id: response.topicId, locale: response.locale, title: response.topicTitle }];
                    children[i]['__children'][0]['translations'] = [{locale: response.locale, field: "title", content: response.topicTitle }];
                  } else {
                    children[i].__children.push({
                      id: response.topicId,
                      locale: response.locale,
                      title: response.topicTitle,
                      translations: [{locale: response.locale, field: "title", content: response.topicTitle }]
                    });
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

    /**
     * Displays alert to inform user if he/she is sure to remove this topic.
     * It also checks if topic is assigned to any article and returns number
     * of articles its assigned to.
     *
     * @method removeTopicAlert
     * @param topicId {integer} topic's id which will be removed
     */
    $scope.removeTopicAlert = function(topicId) {
      removeTopicId = topicId;
      var attachedInfo = $('#removeAlert').find('.attached-info');
      attachedInfo.hide();
      attachedInfo.html('');
      TopicsFactory.isAttached(removeTopicId).success(function (response) {
        if (response.status) {
          attachedInfo.show();
          attachedInfo.append(response.message);
        }
      }).error(function(response, status){
          flashMessage(response.message, 'error');
          $('#removeAlert').modal('hide');
      });
    }

    /**
     * Removes topic by given id and closes the alert popup
     *
     * @method removeTopic
     */
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

    /**
     * Removes topic's translation by given translation id.
     *
     * @method removeTranslation
     */
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

    /**
     * Toggles the tree.
     *
     * @method toggleTopic
     *
     * @param scope {object} current scope
     */
    $scope.toggleTopic = function(scope) {
      if (scope.$nodeScope.$modelValue.hasAttachedSubtopic !== undefined) {
        if (scope.$nodeScope.$modelValue.hasAttachedSubtopic) {
          scope.$nodeScope.$modelValue.hasAttachedSubtopic = false;
          scope.$nodeScope.collapse();
        }
      }

      scope.toggle();
    };

    /**
     * Get root nodes.
     *
     * @method getRootNodesScope
     */
    var getRootNodesScope = function() {
      return angular.element(document.getElementById("tree-root")).scope();
    };

    /**
     * Expand or collapse all elements in the tree
     *
     * @method expandCollapseAll
     */
    $scope.expandCollapseAll = function() {
      var scope = getRootNodesScope();
      if (!scope.ex) {
        scope.ex = true;
        scope.showExpanded = true;
      } else {
        if (scope.showExpanded) {
          scope.showExpanded = false;
        } else {
          scope.showExpanded = true;
        }
      }
    };

    /**
     * Hides/shows more options of the tree node
     *
     * @method startEditing
     * @parent scope {object} currently selected element in a tree
     */
    $scope.startEditing = function(scope) {
      if (scope.editing) {
        scope.editing = false;
      } else {
        scope.editing = true;
        scope.addingSubtopic = false;
      }
    };

    /**
     * Hides/shows options to add new subtopic
     *
     * @method showNewSubtopicBox
     * @parent scope {object} currently selected element in a tree
     */
    $scope.showNewSubtopicBox = function(scope) {
      if (scope.addingSubtopic) {
        scope.addingSubtopic = false;
      } else {
        scope.addingSubtopic = true;
        scope.editing = false;
      }
    };

    /**
     * Hide button, hiding extra options like e.g. adding new substopic etc.
     *
     * @method hideExtraOptions
     * @parent scope {object} currently selected element in a tree
     */
    $scope.hideExtraOptions = function(scope) {
      scope.editing = false;
      scope.addingSubtopic = false;
    }

    $scope.formData = {};
    $scope.subtopicForm = {};

    /**
     * Adds a new topic
     *
     * @method addNewTopic
     * @param scope {Object} Current topic scope
     */
    $scope.addNewTopic = function(scope) {
      var addFormData = {
            topic: {},
            _csrf_token: token
        }

      var topicId;
      if (scope !== undefined) {
        topicId = scope.$parent.$nodeScope.$modelValue.id;
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
              $scope.data.unshift({
                id: response.topicId,
                title: response.topicTitle,
                root: response.topicId,
                translations: [{ content: response.topicTitle, locale: response.locale}]
              });

          } else {
            updateAfterAddSubtopic($scope.data, topicId, response);
            scope.$parent.$nodeScope.collapsed = true;
          }
          $scope.subtopicForm.title = undefined;
          $scope.formData = null;
        } else {
          flashMessage(response.message, 'error');
        }
      }).error(function(response, status){
          flashMessage(response.message, 'error');
      });
    };

    /**
     * Attach topics to the articles. If attaching topics from AES, it also closes the iframe
     * when the topic has been assigned successfully.
     *
     * @method attachTopics
     * @param articleNumber {integer} article's number to which topic will be assigned
     * @param languageCode {integer} article's language to which topic will be assigned
     */
    $scope.attachTopics = function(articleNumber, languageCode) {
      var topicsIds = $scope.selectedTopics;
      TopicsFactory.attachTopics(topicsIds, articleNumber, languageCode).success(function (response) {
        if (response.status) {
          flashMessage(response.message);
          setTimeout(function() {
            window.parent.$.fancybox.close();
          }, 1500);
        } else {
          flashMessage(response.message, 'error');
        }
      }).error(function(response, status){
          flashMessage(response.message, 'error');
      });
    }

    $scope.editFormData = {};

    /**
     * Updates topic's name
     *
     * @method updateTopic
     * @param node {object} topic object
     */
    $scope.updateTopic = function(node) {
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

    /**
     * On language change in box when adding a new translation, it assigns selected
     * language code to the variable so it can be used later elsewhere.
     *
     * @method onLanguageChange
     * @param language {object} language object
     */
    $scope.onLanguageChange = function(language) {
        languageCode = language.code;
        languageSelected = languageCode;
    }

    /**
     * It loads the topics' tree by given language. If selected language is english,
     * it will load tree with topics by english language.
     *
     * @method onFilterLanguageChange
     * @param langCode {string} language's code
     * @param articleNumber {integer} article's number
     */
    $scope.onFilterLanguageChange = function(langCode, articleNumber) {
        languageCode = langCode;
        $scope.languageCode = langCode;
        TopicsFactory.getTopics(langCode, articleNumber).success(function (data) {
           $scope.data = data.tree;
           $scope.pattern = undefined;
          getSelectedTopics(data.tree);
        }).error(function(data, status){
            flashMessage(data.message, 'error');
        });
    }

    $scope.translationForm = {};

    /**
     * It adds a new translation for given topic id
     *
     * @method addTranslation
     * @param topicId {integer} topic's id
     */
    $scope.addTranslation = function(topicId) {
      var postData = {
          topicTranslation: {
              title: $scope.translationForm.title,
              locale: languageSelected
          },
          _csrf_token: token
      };

      TopicsFactory.addTranslation(postData, topicId).success(function (response) {
        if (response.status) {
          flashMessage(response.message);
          $scope.languageCode = null;
          languageCode = null;
          updateAfterAddTranslation($scope.data, topicId, response);
        } else {
          flashMessage(response.message, 'error');
        }
      }).error(function(response, status){
          flashMessage(response.message, 'error');
      });
    };

    /**
     * It sets a proper key: "activeLabel" or "fallback" in translation
     * object, based on the current locale and selected filter language
     *
     * @method setLanguageLabel
     * @param node {Object} topic
     * @param langCode {String} Current locale
     */
    $scope.setLanguageLabel = function(node, langCode) {
      angular.forEach(node.translations, function(value, key) {
          if (languageCode === value.locale) {
            value.activeLabel = true;
          }

          if (!languageCode) {
            if (value.locale === langCode) {
               value.activeLabel = true;
            }
          }

      });

      // find element with activeLabel set to true
      // if set to true, set fallback to false
      // else to true
      var setfallback = true;
      var i;
      angular.forEach(node.translations, function(value, key) {
          if (value.activeLabel) {
              setfallback = false;
          }
      });

      for (i = 0; i < node.translations.length; i++) {
        if (node.translations[i].activeLabel == undefined && setfallback) {
              node.translations[i].fallback = true;
        }
      }

      // if languageCode not in array
      // choose first locale and set fallback
      var inArray = false;
      for (i = 0; i < node.translations.length; i++) {
        if (angular.equals(node.translations[i].locale, languageCode)) {
            inArray = true;
        }
      }

      if (!inArray) {
        // restore fallback fields
        // first translation is always default, so  we unset fallback
        // for all translations diffrent than default
        for (i = 0; i < node.translations.length; i++) {
          if (!angular.equals(node.translations[i].locale, languageCode) && i !== 0) {
              node.translations[i].fallback = false;
          }
        }
      }
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
