// Ionic Starter App

// angular.module is a global place for creating, registering and retrieving Angular modules
// 'starter' is the name of this angular module example (also set in a <body> attribute in index.html)
// the 2nd parameter is an array of 'requires'
angular.module('starter', ['ionic', 'awlert'])

.run(function($ionicPlatform) {
  $ionicPlatform.ready(function() {
    if(window.cordova && window.cordova.plugins.Keyboard) {
      // Hide the accessory bar by default (remove this to show the accessory bar above the keyboard
      // for form inputs)
      cordova.plugins.Keyboard.hideKeyboardAccessoryBar(true);

      // Don't remove this line unless you know what you are doing. It stops the viewport
      // from snapping when text inputs are focused. Ionic handles this internally for
      // a much nicer keyboard experience.
      cordova.plugins.Keyboard.disableScroll(true);
    }
    if(window.StatusBar) {
      StatusBar.styleDefault();
    }
  });
})


.controller('AppCtrl', function(awlert, $timeout){
  var vm = this;

  vm.duration = 2000;
  vm.durationValues = [];
  vm. message = 'Type your text';

  vm.openCustomAwlert = openCustomAwlert;
  vm.openNeutral = openNeutral;
  vm.openSuccess = openSuccess;
  vm.openError = openError;
  vm.openCustomAwlert = openCustomAwlert;

  init();

  function init(){
    for (var i = 0; i <= 15000; i += 500) {
      vm.durationValues.push(i);
    }
  }

  function openCustomAwlert(message, duration){
    awlert.neutral(message, duration)
  }

  function openNeutral(){
    var awlert = awlert.neutral('Click me... Click me... Click me... Click me... Click me...', -1);
    
    awlert.$on('awlert:click', function(ev, target){
      target.remove();
    })

  }

  function openSuccess(){
    console.log('hauishduad');
    awlert.success('This is a awesome success alert.', 3000);
  }

  function openError(){
    console.log('hauishduad');
    awlert.error('Mussum Ipsum, cacilds vidis litro abertis. Si u mundo tá muito paradis? '+
      'Toma um mé que o mundo vai girarzis! in elementis mé pra quem é amistosis quis leo. '+
      'Quem num gosta di mé, boa gente num é.', 5000);
  }

})