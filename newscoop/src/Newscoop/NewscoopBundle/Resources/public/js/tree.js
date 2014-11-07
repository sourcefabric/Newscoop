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

    TopicsFactory().success(function (data) {
       console.log(data.tree);
       $scope.data =  data.tree;



    //$rootScope.page_ready = true;


    }).error(function(data, status){
        if(status==401){
            $scope.article_url = global_notallowed_url;
        }else{
            $scope.article_url = global_error_url;
        }
        $rootScope.page_ready = true;
    });
  });

   /* $scope.data = [{
      "id": 1,
      "title": "node1",
      "nodes": [
        {
          "id": 11,
          "title": "node1.1",
          "nodes": [
            {
              "id": 111,
              "title": "node1.1.1",
              "nodes": []
            }
          ]
        },
        {
          "id": 12,
          "title": "node1.2",
          "nodes": []
        }
      ],
    }, {
      "id": 2,
      "title": "node2",
      "nodes": [
        {
          "id": 21,
          "title": "node2.1",
          "nodes": []
        },
        {
          "id": 22,
          "title": "node2.2",
          "nodes": []
        }
      ],
    }, {
      "id": 3,
      "title": "node3",
      "nodes": [
        {
          "id": 31,
          "title": "node3.1",
          "nodes": []
        }
      ],
    }, {
      "id": 4,
      "title": "node4",
      "nodes": [
        {
          "id": 41,
          "title": "node4.1",
          "nodes": []
        }
      ],
    }];*/

})();


