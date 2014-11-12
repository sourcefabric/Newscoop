(function() {
  'use strict';

var app = angular.module('treeApp', ['ui.tree'])
  .config(function($interpolateProvider, $sceProvider, $sceDelegateProvider, $locationProvider) {
      $locationProvider.html5Mode(true);
      $interpolateProvider.startSymbol('{[{').endSymbol('}]}');
  });

app.factory('TopicsFactory',  function($http) {
  return {
        getTopics: function() {
            return $http.get(Routing.generate("newscoop_newscoop_topics_tree"));
        },
        deleteTopic: function(id) {
            return $http.post(Routing.generate("newscoop_newscoop_topics_delete", {id: id}));
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
        updateTopic: function(formData, id) {
          return $http({
            method: "POST",
            url: Routing.generate("newscoop_newscoop_topics_edit", {id: id}),
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            data: $.param(formData)
          });
        },
    };
});

app.controller('treeCtrl', function($scope, TopicsFactory) {
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

    $scope.removeTopic = function(topicId) {
      TopicsFactory.deleteTopic(topicId).success(function (response) {
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

    $scope.toggle = function(scope) {
      scope.toggle();
    };

    $scope.moveLastToTheBegginig = function () {
      var a = $scope.data.pop();
      $scope.data.splice(0,0, a);
    };

    $scope.newSubItem = function(scope) {
      var nodeData = scope.$modelValue;
      nodeData.nodes.push({
        id: nodeData.id * 10 + nodeData.nodes.length,
        title: nodeData.title + '.' + (nodeData.nodes.length + 1),
        nodes: []
      });
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

    $scope.cancelEditing = function(scope) {
      if (scope.editing) {
        scope.editing = false;
      } else {
        scope.editing = true;
      }

      //todo restore topic label
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
          //$scope.data.push({ id: response.topicId, title: response.topicTitle });
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

      TopicsFactory.updateTopic(postData, node.id).success(function (response) {
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

    TopicsFactory.getTopics().success(function (data) {
       $scope.data = data.tree;
    }).error(function(data, status){
        if(status==401){
            $scope.article_url = global_notallowed_url;
        }else{
            $scope.article_url = global_error_url;
        }
        $rootScope.page_ready = true;
    });
  });

})();


