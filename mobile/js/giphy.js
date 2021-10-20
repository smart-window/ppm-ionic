angular.module('ionic.giphy', ['monospaced.elastic'])
  /*
    Giphy Service
    Docs: https://github.com/Giphy/GiphyAPI#search-endpoint
  */
  .service('Giphy', function($http) {
    var API_KEY = 'dc6zaTOxFJmzC'; // Public Beta Key
    var ENDPOINT = 'https://api.giphy.com/v1/gifs/';

    this.search = function(query) {
      return $http.get(ENDPOINT + 'search', {params: {
        q: query,
        api_key: API_KEY
      }}).then(function(response) {
        return response.data.data;
      })
    }

    this.trending = function() {
      return $http.get(ENDPOINT + 'trending', {params: {
        api_key: API_KEY
      }}).then(function(response) {
        return response.data.data;
      })
    }
  })

  .directive('elastic', function() {
    return {
      restric: 'A',
      link: function(scope, el, attr) {
        scope.$on('elastic:resize', function(event, element, oldHeight, newHeight) {
          newHeight = Math.max(44, newHeight);
          el[0].style.height = newHeight + 'px';
        });
      }
    }
  })

  .filter('nl2br', ['$filter',
    function($filter) {
      return function(data) {
        if (!data) return data;
        return data.replace(/\n\r?/g, '<br />');
      };
    }
  ])
