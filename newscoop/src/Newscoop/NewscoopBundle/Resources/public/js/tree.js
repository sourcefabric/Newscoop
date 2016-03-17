(function() {
  'use strict';

  var app = angular.module('treeApp', ['ui.tree', 'ui.tree-filter', 'ui.highlight', 'checklist-model'])
    .config(function($interpolateProvider, $httpProvider, uiTreeFilterSettingsProvider) {
      $interpolateProvider.startSymbol('{[{').endSymbol('}]}');
      uiTreeFilterSettingsProvider.descendantCollection = "__children";
      $httpProvider.interceptors.push(function($q, $injector) {
        return {
          'responseError': function(response) {
            var configToRepeat,
              failedRequestConfig,
              retryDeferred,
              $http;

            if (response.config.IS_RETRY) {
              return $q.reject(response);
            }

            if (response.status === 401) {
              failedRequestConfig = response.config;
              retryDeferred = $q.defer();
              $http = $injector.get('$http');
              configToRepeat = angular.copy(failedRequestConfig);
              configToRepeat.IS_RETRY = true;

              callServer('ping', [], function(json) {
                $http(configToRepeat)
                  .then(function(newResponse) {
                    delete newResponse.config.IS_RETRY;
                    retryDeferred.resolve(newResponse);
                  })
                  .catch(function() {
                    retryDeferred.reject(response);
                  });
              });

              return retryDeferred.promise;
            } else {
              return $q.reject(response);
            }
          }
        };
      });
    });

  /**
   * A factory which keeps routes to manage topics.
   *
   * @class Topic
   */
  app.factory('TopicsFactory', function($http) {
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
        return $http.get(Routing.generate("newscoop_newscoop_language_getlanguages"));
      },
      isAttached: function(id) {
        return $http.get(Routing.generate("newscoop_newscoop_topics_isattached", {
          id: id
        }));
      },
      deleteTopic: function(id) {
        return $http.post(Routing.generate("newscoop_newscoop_topics_delete", {
          id: id
        }));
      },
      moveTopic: function(id, params) {
        return $http({
          method: "POST",
          url: Routing.generate("newscoop_newscoop_topics_move", {
            id: id
          }),
          data: params
        });
      },
      deleteTopicTranslation: function(id) {
        return $http.post(Routing.generate("newscoop_newscoop_topics_deletetranslation", {
          id: id
        }));
      },
      addTopic: function(formData, language) {
        return $http({
          method: "POST",
          url: Routing.generate("newscoop_newscoop_topics_add"),
          headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
          },
          data: $.param(formData),
          params: {
            _code: language
          }
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
          url: Routing.generate("newscoop_newscoop_topics_edit", {
            id: id
          }),
          headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
          },
          data: $.param(formData),
          params: {
            _code: language
          }
        });
      },
      addTranslation: function(formData, id) {
        return $http({
          method: "POST",
          url: Routing.generate("newscoop_newscoop_topics_addtranslation", {
            id: id
          }),
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
   * @class treeController
   */
  app.controller('treeController', function(TopicsFactory, $filter, $location, $anchorScroll) {
      var self = this;

      self.treeOptions = {
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
              var draggedIndex = draggedNode.$index;
              if (closestNode.$$nextSibling.node !== undefined) {
                params['parent'] = closestNode.$$nextSibling.node.id;
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
      self.treeFilter = $filter('uiTreeFilter');
      self.availableFields = ['content', 'title'];
      self.supportedFields = ['content', 'title'];
      self.deleteDisabled = false;
      var languageCode = null;
      var languageSelected = null;

      /**
       * Go to topic function.
       * This function sets pattern to the current
       * topic title. Thus, the whole subtree will be found
       * using the search field, not needed topics won't be shown.
       * It also scrolls down to the found topic.
       *
       * @param {String}  title topic title
       * @param {Integer} id    topic id
       */
      self.goToTopic = function(title, id) {
        $anchorScroll.yOffset = 300;
        var newHash = 'node' + id;
        self.pattern = title;
        if ($location.hash() !== newHash) {
          $location.path('scroll')
          $location.hash(newHash);
        } else {
          $anchorScroll();
        }
      }

      /**
       * Loads all topics. It loads all the topics in tree structure
       *
       * @method loadTopicsTree
       * @param {String} current locale
       */
      self.loadTopicsTree = function(currentLocale) {
        TopicsFactory.getTopics().success(function(data) {
          self.data = data.tree;
          setLanguageLabels(self.data, currentLocale);
        }).error(function(data, status) {
          flashMessage(data.message, 'error');
        });
      }

      TopicsFactory.getLanguages().success(function(data) {
        self.languageList = data.languages;
      }).error(function(data, status) {
        flashMessage(data.message, 'error');
      });

      /**
       * Moves topic. It moves topic to given position in a tree.
       *
       * @method moveTopic
       * @param draggedNode {string} topic's id which will be moved
       * @param params {array} parameters e.g. order, parent etc.
       */
      var moveTopic = function(draggedNode, params) {
        TopicsFactory.moveTopic(draggedNode, params).success(function(response) {
          if (response.status) {
            flashMessage(response.message);
            updateTopicAfterMove(self.data, draggedNode, response.topic);
          } else {
            flashMessage(response.message, 'error');
          }
        }).error(function(response, status) {
          flashMessage(response.message, 'error');
        });
      }

      self.selectedTopics = {
        ids: []
      };

      /**
       * Get topics assigned to the article
       *
       * @param  {Array} array array of all the topics
       */
      var getSelectedTopics = function(array) {
        if (array) {
          for (var i = 0; i < array.length; i++) {
            if (array[i].attached) {
              self.selectedTopics.ids.push(array[i].id);
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
      var updateList = function(children, id) {
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
       * Updates topic after it has been moved.
       *
       * @param  {array} children   Array of children
       * @param  {integer} id       Topic id
       * @param  {object}  response Object with data returned from the server
       * @return {boolean}          True when topic found
       */
      var updateTopicAfterMove = function(children, id, response) {
        if (children) {
          for (var i = 0; i < children.length; i++) {
            if (children[i].id == id) {
              children[i].root = response.root;
              children[i].parentId = response.parentId;

              return children[i];
            }

            var found = updateTopicAfterMove(children[i].__children, id, response);
            if (found) {
              return found;
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
      var updateTranslations = function(children, id) {
        if (children) {
          for (var i = 0; i < children.length; i++) {
            angular.forEach(children[i].translations, function(value, key) {
              if (value.id == id) {
                children[i].translations.splice(children[i].translations.indexOf(value), 1);
                self.setLanguageLabel(children[i]);

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

      var setLanguageLabels = function (children, currentLocale) {
        if (children) {
          for (var i = 0; i < children.length; i++) {
            self.setLanguageLabel(children[i], currentLocale);

            setLanguageLabels(children[i].__children, currentLocale);
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
      var updateAfterAddTranslation = function(children, id, response) {
        if (children) {
          for (var i = 0; i < children.length; i++) {
            if (children[i].id == id) {
              children[i].translations.push({
                id: response.topicTranslationId,
                locale: response.topicTranslationLocale,
                field: "title",
                content: response.topicTranslationTitle
              });
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
      var updateAfterAddSubtopic = function(children, id, response) {
        if (children) {
          for (var i = 0; i < children.length; i++) {
            if (children[i].id == id) {
              if (children[i]['__children'].length == 0) {
                children[i]['__children'].push({
                  id: response.topicId,
                  locale: response.locale,
                  title: response.topicTitle,
                  __children: []
                });
                children[i]['__children'][0]['translations'] = [{
                  locale: response.locale,
                  field: "title",
                  content: response.topicTitle
                }];

                self.setLanguageLabel(children[i]['__children'][0], response.locale);
              } else {
                var topic = {
                  id: response.topicId,
                  locale: response.locale,
                  title: response.topicTitle,
                  __children: [],
                  translations: [{
                    locale: response.locale,
                    field: "title",
                    content: response.topicTitle
                  }]
                };

                self.setLanguageLabel(topic, response.locale);
                children[i]['__children'].push(topic);
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
      self.removeTopicAlert = function(topicId) {
        removeTopicId = topicId;
        var attachedInfo = $('#removeAlert').find('.attached-info');
        attachedInfo.hide();
        attachedInfo.html('');
        TopicsFactory.isAttached(removeTopicId).success(function(response) {
          if (response.status) {
            attachedInfo.show();
            attachedInfo.append(response.message);
          }
        }).error(function(response, status) {
          flashMessage(response.message, 'error');
          $('#removeAlert').modal('hide');
        });
      }

      /**
       * Removes topic by given id and closes the alert popup
       *
       * @method removeTopic
       */
      self.removeTopic = function() {
        self.deleteDisabled = true;
        TopicsFactory.deleteTopic(removeTopicId).success(function(response) {
          if (response.status) {
            $('#removeAlert').modal('hide');
            flashMessage(response.message);
            updateList(self.data, removeTopicId);
          } else {
            flashMessage(response.message, 'error');
          }
          self.deleteDisabled = false;
        }).error(function(response, status) {
          self.deleteDisabled = false;
          flashMessage(response.message, 'error');
        });
      };

      /**
       * Removes topic's translation by given translation id.
       *
       * @method removeTranslation
       */
      self.removeTranslation = function(translationId) {
        TopicsFactory.deleteTopicTranslation(translationId).success(function(response) {
          if (response.status) {
            flashMessage(response.message);
            updateTranslations(self.data, translationId);
          } else {
            flashMessage(response.message, 'error');
          }
        }).error(function(response, status) {
          flashMessage(response.message, 'error');
        });
      }

      /**
       * Expand or collapse all elements in the tree
       *
       * @method expandCollapseAll
       */
      self.expandCollapseAll = function() {
          if (self.showExpanded) {
            self.showExpanded = false;
          } else {
            self.showExpanded = true;
          }
      };

      /**
       * Hides/shows more options of the tree node
       *
       * @method startEditing
       * @parent scope {object} currently selected element in a tree
       */
      self.startEditing = function(scope) {
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
      self.showNewSubtopicBox = function(scope) {
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
      self.hideExtraOptions = function(scope) {
        scope.editing = false;
        scope.addingSubtopic = false;
      }

      self.formData = {};

      /**
       * Adds a new topic
       *
       * @method addNewTopic
       * @param scope {Object} Current topic scope
       */
      self.addNewTopic = function(scope) {
        var addFormData = {
          topic: {},
          _csrf_token: token
        }

        var topic;
        if (scope !== undefined) {
          topic = scope.$parent.$nodeScope.$modelValue;
        }

        if (topic !== undefined) {
          addFormData.topic["title"] = topic.newChild[topic.id];
          addFormData.topic["parent"] = topic.id;
          topic.newChild = {};
        } else {
          addFormData.topic["title"] = self.formData.title;
        }

        TopicsFactory.addTopic(addFormData, languageCode).success(function(response) {
          if (response.status) {
            flashMessage(response.message);
            if (topic == undefined) {
              topic = {
                id: response.topicId,
                title: response.topicTitle,
                root: response.topicId,
                __children: [],
                translations: [{
                  content: response.topicTitle,
                  locale: response.locale
                }]
              };

              self.setLanguageLabel(topic, response.locale);
              self.data.unshift(topic);

            } else {
              updateAfterAddSubtopic(self.data, topic.id, response);
              scope.$parent.collapse();
              self.hideExtraOptions(scope.$parent);
            }
            self.formData = null;
          } else {
            flashMessage(response.message, 'error');
          }
        }).error(function(response, status) {
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
      self.attachTopics = function(articleNumber, languageCode) {
        var topicsIds = self.selectedTopics;
        TopicsFactory.attachTopics(topicsIds, articleNumber, languageCode).success(function(response) {
          if (response.status) {
            flashMessage(response.message);
            setTimeout(function() {
              window.parent.$.fancybox.close();
            }, 1500);
          } else {
            flashMessage(response.message, 'error');
          }
        }).error(function(response, status) {
          flashMessage(response.message, 'error');
        });
      }

      self.editFormData = {};

      /**
       * Updates topic's name
       *
       * @method updateTopic
       * @param node {object} topic object
       */
      self.updateTopic = function(node) {
        var postData = {
          topic: {
            title: node.title,
          },
          _csrf_token: token
        };

        TopicsFactory.updateTopic(postData, node.id, languageCode).success(function(response) {
          if (response.status) {
            flashMessage(response.message);
            self.editFormData = null;
          } else {
            flashMessage(response.message, 'error');
          }
        }).error(function(response, status) {
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
      self.onLanguageChange = function(language) {
        if (language !== undefined) {
          languageCode = language.code;
          languageSelected = languageCode;
        }
      }

      /**
       * It loads the topics' tree by given language. If selected language is english,
       * it will load tree with topics by english language.
       *
       * @method onFilterLanguageChange
       * @param langCode {string} language's code
       * @param currentLocale {string} current locale
       * @param articleNumber {integer} article's number
       */
      self.onFilterLanguageChange = function(langCode, currentLocale, articleNumber) {
        if (langCode === undefined) {
          languageCode = currentLocale;
        } else {
          languageCode = langCode;
        }
        self.languageCode = languageCode;
        TopicsFactory.getTopics(langCode, articleNumber).success(function(data) {
          self.data = data.tree;
          self.pattern = undefined;
          getSelectedTopics(data.tree);
          setLanguageLabels(self.data, currentLocale);
        }).error(function(data, status) {
          flashMessage(data.message, 'error');
        });
      }

      /**
       * It adds a new translation for given topic object.
       *
       * @method addTranslation
       * @param topicId {integer} topic object
       */
      self.addTranslation = function(topic) {
        var postData = {
          topicTranslation: {
            title: topic.newTranslation[topic.id],
            locale: languageSelected
          },
          _csrf_token: token
        };

        topic.newTranslation = {};
        TopicsFactory.addTranslation(postData, topic.id).success(function(response) {
          if (response.status) {
            flashMessage(response.message);
            self.languageCode = null;
            languageCode = null;
            updateAfterAddTranslation(self.data, topic.id, response);
            self.setLanguageLabel(topic, languageCode);
          } else {
            flashMessage(response.message, 'error');
          }
        }).error(function(response, status) {
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
      self.setLanguageLabel = function(node, langCode) {
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
    .filter('trust', function($sce) {
      return function(val) {
        return $sce.trustAsHtml(val);
      };
    });
})();
