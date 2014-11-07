(function() {
  'use strict';

var app = angular.module('treeApp', ['ui.tree'])
  .config(function($interpolateProvider, $sceProvider, $sceDelegateProvider, $locationProvider) {
      $locationProvider.html5Mode(true);
      $interpolateProvider.startSymbol('{[{').endSymbol('}]}');
  });

app.factory('TopicsFactory',  function($http) {
    return function(){
       return $http({
        method: 'GET',
        url: '/admin/topics/tree/'
    });
   };
});

app.controller('treeCtrl', function($scope, TopicsFactory) {

    $scope.remove = function(scope) {
      scope.remove();
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

    TopicsFactory().success(function (data) {
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


