angular.module('starter.services', [])
	.factory('$localstorage', ['$window', function($window) {
	  return {
		set: function(key, value) {
		  $window.localStorage[key] = value;
		},
		get: function(key, defaultValue) {
		  return $window.localStorage[key] || defaultValue;
		},
		setObject: function(key, value) {
		  $window.localStorage[key] = JSON.stringify(value);
		},
		getObject: function(key) {
		  return JSON.parse($window.localStorage[key] || '{}');
		}
	  }
	}])
	
	.service('currentUser', function() {
		this.currentUser;
	})	
	
	.service('Navigation', function($state) {
	  //directly binding events to this context
	  this.goNative = function(view, direction,data) {
		$state.go(view,data);
		window.plugins.nativepagetransitions.slide({
			"direction": direction
		  },
		  function(msg) {
			console.log("success: " + msg)
		  }, // called when the animation has finished
		  function(msg) {
			alert("error: " + msg)
		  } // called in case you pass in weird values
		);
	  };
	  this.goNativeFade = function(view, data) {
		$state.go(view, data);
		window.plugins.nativepagetransitions.fade({
			"duration": 500
		  },
		  function(msg) {
			console.log("success: " + msg)
		  }, // called when the animation has finished
		  function(msg) {
			alert("error: " + msg)
		  } // called in case you pass in weird values
		);
	  };	  
	})
	.factory('A', ['$resource',
		function($resource){;
			return {
				Query: $resource(site_url+'requests/api.php?action=:action&query=:query', {action:'@action',query: '@query'}),
				RT: $resource(site_url+'requests/rt.php?action=:action&query=:query', {action:'@action',query: '@query'}),				
				Game: $resource(site_url+'requests/api.php?action=:action&id=:id', {action:'@action',id: '@id'}),					
				User: $resource(site_url+'requests/api.php?action=:action&login_email=:login_email&login_pass=:login_pass&dID=:dID', {action:'@action',login_email: '@login_email',login_pass: '@login_pass',dID: '@dID'}),
				Reg: $resource(site_url+'requests/api.php?action=:action&reg_name=:reg_name&reg_email=:reg_email&reg_pass=:reg_pass&reg_birthday=:reg_birthday&reg_gender=:reg_gender&reg_looking=:reg_looking&reg_photo=:reg_photo&dID=:dID', 
					{action:'@action',reg_email: '@reg_email',reg_pass: '@reg_pass',reg_name: '@reg_name',reg_photo: '@reg_photo',reg_thumb: '@reg_thumb',
					reg_username: '@reg_username',reg_lat: '@reg_lat',reg_lng: '@reg_lng',reg_city: '@reg_city',reg_country: '@reg_country',dID: '@dID'}),
				PDS: $resource('https://www.premiumdatingscript.com/php/func.php?action=:action&id=:id', {action:'@action',id: '@id'}),
				Chat: $resource(site_url+'requests/api.php?action=:action&uid1=:uid1&uid2=:uid2', {action:'@action',uid1: '@uid1',uid2: '@uid2'}),
				Meet: $resource(site_url+'requests/api.php?action=:action&uid1=:uid1&uid2=:uid2&uid3=:uid3', {action:'@action',uid1: '@uid1',uid2: '@uid2',uid3: '@uid3'}),				
				Device: $resource(site_url+'requests/api.php?action=:action&dID=:dID', {action:'@action', dID: '@dID'}),
				Cuser: $resource(site_url+'requests/api.php?action=:action&dID=:dID', {action:'@action', dID: '@dID'}),				
				Config: $resource(site_url+'requests/api.php?action=:action', {action:'@action'}),			
				ChatDetails: $resource(site_url+'requests/chat.php?profileID=:profileID', {profileID: '@profileID'}),
			};
	}])	
    .factory('preloader', function( $q, $rootScope ) {
		function Preloader( imageLocations ) {
			this.imageLocations = imageLocations;
			this.imageCount = this.imageLocations.length;
			this.loadCount = 0;
			this.errorCount = 0;
			this.states = {
				PENDING: 1,
				LOADING: 2,
				RESOLVED: 3,
				REJECTED: 4
			};
			this.state = this.states.PENDING;
			this.deferred = $q.defer();
			this.promise = this.deferred.promise;
		}
		Preloader.preloadImages = function( imageLocations ) {
			var preloader = new Preloader( imageLocations );
			return( preloader.load() );
		}
		Preloader.prototype = {
			constructor: Preloader,
			isInitiated: function isInitiated() {
				return( this.state !== this.states.PENDING );
			},
			isRejected: function isRejected() {
				return( this.state === this.states.REJECTED );
			},
			isResolved: function isResolved() {
				return( this.state === this.states.RESOLVED );
			},
			load: function load() {
				if ( this.isInitiated() ) {
					return( this.promise );
				}
				this.state = this.states.LOADING;
				for ( var i = 0 ; i < this.imageCount ; i++ ) {
					this.loadImageLocation( this.imageLocations[ i ] );
				}
				return( this.promise );
			},
			handleImageError: function handleImageError( imageLocation ) {
				this.errorCount++;
				if ( this.isRejected() ) {
					return;
				}
				this.state = this.states.REJECTED;
				this.deferred.reject( imageLocation );
			},
			handleImageLoad: function handleImageLoad( imageLocation ) {
				this.loadCount++;
				if ( this.isRejected() ) {
					return;
				}
				this.deferred.notify({
					percent: Math.ceil( this.loadCount / this.imageCount * 100 ),
					imageLocation: imageLocation
				});
				if ( this.loadCount === this.imageCount ) {
					this.state = this.states.RESOLVED;
					this.deferred.resolve( this.imageLocations );
				}
			},
			loadImageLocation: function loadImageLocation( imageLocation ) {
				var preloader = this;
				var image = angular.element( new Image() )
					.bind('load', function( event ) {
						$rootScope.$apply(
							function() {
								preloader.handleImageLoad( event.target.src );
								preloader = image = event = null;
							}
						);
					})
					.bind('error', function( event ) {
						// Since the load event is asynchronous, we have to
						// tell AngularJS that something changed.
						$rootScope.$apply(
							function() {
								preloader.handleImageError( event.target.src );
								// Clean up object reference to help with the
								// garbage collection in the closure.
								preloader = image = event = null;
							}
						);
					})
					.attr( 'src', imageLocation )
				;
			}
		};
        return( Preloader );
	})


	.service('Giphy', function($http) {
	var API_KEY = 'dc6zaTOxFJmzC';
	var ENDPOINT = 'http://api.giphy.com/v1/gifs/';
	
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
	});
