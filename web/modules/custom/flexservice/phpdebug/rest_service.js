@@ -1,150 +0,0 @@
var jsonFileUrl = drupalSettings.path.baseUrl + 'manageinfo/term-form-json/province/12';

var pageInfoBaseControllers = angular.module('pageInfoBase', ['ngResource', 'ngMaterial']);
pageInfoBaseControllers.controller('MildderPreFormController', ['$scope', '$http', '$timeout', '$q', '$log', '$filter','$mdDialog', '$element',
  function($scope, $http, $timeout, $q, $log, $filter, $mdDialog, $element) {

    angular.element(document).ready(function() {
      $http.get(jsonFileUrl).then(function(response) {
        $scope.formJson = response.data;
      }).catch(function(err) {
        // Log error somehow.
      }).finally(function() {
        // Hide loading spinner whether our call succeeded or failed.
      });
    });
    // working diagnosis sub questions
    $scope.superSelectOptions = function(answerTid) {
      $scope.clearSubModels();
      angular.forEach($scope.formJson.formElementsSection, function(field) {
        if(field.fieldTid == answerTid) {
          field.fieldShow = true;
        }
      });
    }

    //clearing subModels on select
    $scope.clearSubModels = function() {
      angular.forEach($scope.formJson.formElementsSection, function(field) {
        if(field.filter == true) {
          field.defaultValue = '';
          field.fieldShow = false;
          field.updateStatus = 0;
        }
      });
    }

    // show Ct chest question
    $scope.updateChildfield = function(answerTid) {
      if(answerTid.indexOf(1125) >= 0) {
        $scope.showChildField = true;
      }
      else {
        $scope.showChildField = false;
      }
    }

    $scope.convertDate  = function(referralDate) {
      angular.forEach($scope.formJson.formElementsSection, function(field) {
        if(field.fieldType == 'datetime') {
          var time = field.defaultValue;
          var formatDate = $filter('date')(referralDate, 'EEEE, MMMM d, y');
          var timeStamp = formatDate + ' ' + time;
          timeStamp = Date.parse(timeStamp) / 1000;
          field.defaultValue = timeStamp;
        }
      });
    }

    $scope.updateChildOptions = function(answerTid) {
      $scope.filteredLabels = [];
      angular.forEach($scope.formJson.formElementsSection, function(field) {
        if(field.fieldType == 'selectFilterChild') {
          field.fieldShow = true;
          angular.forEach(field.fieldLabel, function(value) {
            if(value.parentTid == answerTid) {
              $scope.filteredLabels.push(value);
            }
          });
        }
      });
    }

    /*
     * post form function
     */
    $scope.submit  = function() {
      $scope.submitAnswers = [];
      angular.forEach($scope.formJson.formElementsSection, function(field) {
        if(field.updateStatus == 1) {
          $scope.submitAnswers[field.fieldName] = field.defaultValue;
        }
      });

      var createNodeJson = angular.toJson($scope.submitAnswers);

      // post argument
      var postUrl = $scope.formJson.formInfo.postUrl;
      var redirectUrl = $scope.formJson.formInfo.redirectUrl;
      var csrfToken = 'lriNqN49wB6jvd0eEZjUAlgvv1YYfO-mKG3NFeRxbjg';

      // endpoint
      postUrl = drupalSettings.path.baseUrl + 'entity/node';
      // postUrl = drupalSettings.path.baseUrl + '/node/13?_format=json';    // GET or PATCH
      // postUrl = drupalSettings.path.baseUrl + '/node/13';    // DELETE

      var postTermJson = {
        "title": [{ "value": "Created title for Article custom rest" }],
        "type": [{ "target_id": "article" }],
        "body": [{ "value": "article test custom" }]
      };

      $http({
        method  : 'POST',   // GET, POST, PATCH, DELETE
        url     : postUrl,
        data    : postTermJson,
        headers : {'Content-Type': 'application/json', 'X-CSRF-Token': csrfToken},
      })
      .then(
        function(response) {
          console.log('Success');
          // console.log(response.data);
        },
        function(error) {
          $scope.isLoading = false;
          $scope.submitUnsuccessfulAlert();

          console.log('Form not submit successfully');
        }
      );


      // post
      // $http.post(postUrl, postTermJson).then(function(response) {

      //   // this callback will be called asynchronously when the response is available
      //   $scope.status = response.status;
      //   $scope.data = response.data;

      //   // window.location.replace(redirectUrl);
      // }, function(response) {
      //   // called asynchronously if an error occurs or server returns response with an error status.
      //   $scope.data = response.data || "Request failed";
      //   $scope.status = response.status;
      //   console.log('response failed');
      // });
    }

    /*
     * delete form function
     */
    $scope.delete = function() {

    }

  }
]);
