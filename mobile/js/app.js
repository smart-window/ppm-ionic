var show_ad = 0;
var max_ad;
var adMob;
var discoverChat = true;
var mobileUser = '';
var lag = [];
var callId;
var uLat = '';
var ag1 = 18;
var ag2 = 30;
var boostAction;
var boostPrice;
var current_user_id = 0;
var endedVideocall = false;
var lastTypedTime = new Date(0);
var min,minu,sec,secu,totalSeconds;
var called = false;
var video_user = 0;
var pad = 0;
var goToChatGlobalAction = '';
window.localStream = null;
angular.module('starter', [
  'ionic',
  'awlert',
  'flow',
  'starter.controllers',
  'starter.services',
  'starter.directives',
  'ionic.contrib.ui.tinderCards',
  'ionic.giphy',
  'ngResource',
  'ngCordovaOauth',
  'ngCordova',
  'ngAnimate', 
  'ngFx',
  'ui.router'
])

.run(function($ionicPlatform,$rootScope, $location, $state, $stateParams) {
  $rootScope.$location = $location;
  $rootScope.$state = $state;
  $rootScope.$stateParams = $stateParams;  
  $rootScope.theme = '';
  $ionicPlatform.ready(function() {
    if (window.cordova && window.cordova.plugins && window.cordova.plugins.Keyboard) {
  		cordova.plugins.Keyboard.hideKeyboardAccessoryBar(true);
  		cordova.plugins.Keyboard.disableScroll(false);
  		$ionicConfigProvider.scrolling.jsScrolling(false);
    }
    if(window.MobileAccessibility){
    	window.MobileAccessibility.usePreferredTextZoom(false);
    }
    if (window.StatusBar) {
      StatusBar.styleDefault();
    }

  });
})


.config(function($ionicConfigProvider) {
  $ionicConfigProvider.views.transition('none');
  $ionicConfigProvider.navBar.alignTitle('center');
  $ionicConfigProvider.views.swipeBackEnabled(true);
  $ionicConfigProvider.views.maxCache(0);
  $ionicConfigProvider.tabs.position('bottom');
})

.config(function($stateProvider, $urlRouterProvider) {
  $urlRouterProvider.otherwise('/loader');

  $stateProvider

    .state('intro', {
      url: '/intro',
      templateUrl: 'templates/'+mobileTheme+'/welcome/slide.html',
      controller: 'WelcomeCtrl'
    })    
    .state('home', {
      url: '/home',
      abstract: true,
      templateUrl: 'templates/'+mobileTheme+'/home/index.html'
    })

    .state('loader', {
      url: '/loader',
      templateUrl: 'templates/'+mobileTheme+'/welcome/loader.html',
      controller: 'LoaderCtrl'
    })
    .state('home.welcome', {
      url: '/welcome',
      templateUrl: 'templates/'+mobileTheme+'/welcome/welcome.html',
      controller: 'WelcomeCtrl'
    })


    .state('home.register', {
      url: '/register',
      templateUrl: 'templates/'+mobileTheme+'/home/register.html',
      controller: 'RegisterCtrl'
    })

    .state('home.register2', {
      url: '/register2',
      templateUrl: 'templates/'+mobileTheme+'/home/register2.html',
      controller: 'Register2Ctrl'
    })

    .state('home.register3', {
      url: '/register3',
      templateUrl: 'templates/'+mobileTheme+'/home/register3.html',
      controller: 'Register3Ctrl'
    })

    .state('home.explore', {
      url: '/explore',
      templateUrl: 'templates/'+mobileTheme+'/home/explore.html',
      controller: 'ExploreCtrl'
    })

    .state('home.stories', {
      url: '/stories',
      templateUrl: 'templates/'+mobileTheme+'/home/stories.html',
      controller: 'StoriesCtrl'
    })    

    .state('home.menu', {
      url: '/menu',
      templateUrl: 'templates/'+mobileTheme+'/home/menu.html',
      controller: 'menuCtrl'
    })  

    .state('home.location', {
      url: '/location',
      templateUrl: 'templates/'+mobileTheme+'/home/location.html',
      controller: 'locationCtrl'
    })        

    .state('home.settings', {
      url: '/settings',
      templateUrl: 'templates/'+mobileTheme+'/home/settings.html',
      controller: 'SettingsCtrl'
    })

    .state('home.matches', {
      url: '/matches',
      templateUrl: 'templates/'+mobileTheme+'/home/matches.html',
      controller: 'MatchesCtrl'
    })
    .state('home.profile', {
      url: '/profile',
      templateUrl: 'templates/'+mobileTheme+'/home/profile.html',
            controller: 'profileCtrl'
    })
    .state('home.match', {
      url: '/match',
      templateUrl: 'templates/'+mobileTheme+'/home/match.html',
      controller: 'MatchCtrl'
    })

    .state('home.mylikes', {
      url: '/mylikes',
      templateUrl: 'templates/'+mobileTheme+'/home/mylikes.html',
      controller: 'MatchCtrl'
    })  

    .state('home.likesme', {
      url: '/likesme',
      templateUrl: 'templates/'+mobileTheme+'/home/likesme.html',
      controller: 'MatchCtrl'
    })        

    .state('home.visitors', {
      url: '/visitors',
      templateUrl: 'templates/'+mobileTheme+'/home/visitors.html',
      controller: 'VisitorsCtrl'
    })

    .state('home.meet', {
      url: '/meet',
      templateUrl: 'templates/'+mobileTheme+'/home/meet.html',
      controller: 'MeetCtrl'
    })

    .state('home.messaging', {
      url: '/messaging',
      templateUrl: 'templates/'+mobileTheme+'/home/messaging.html',
      controller: 'MessagingCtrl'
    })
})

.run(function($rootScope, $state) {
  $rootScope.$state = $state;
})

var sape = 0;
function profilePhoto(o){var e=window.innerWidth;e/=2,e>200&&(e=200);angular.element(document.querySelector(".profilePhoto"));"explore"==url?(e=$(".profilePhoto").attr("data-w"),$(".profilePhoto").css("background-image","url("+o+")"),$(".profilePhoto").css("width",e+"px"),$(".profilePhoto").css("height",e+"px")):($(".profilePhoto").css("background-image","url("+o+")"),$(".profilePhoto").css("width",e+"px"),$(".profilePhoto").css("height",e+"px"))}function escapeRegExp(o){return o.replace(/[.*+?^${}()|[\]\\]/g,"\\$&")}
