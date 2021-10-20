angular.module('starter.controllers', [])
  .controller('AppCtrl', function($scope,$compile,awlert,$state,$rootScope,$ionicHistory,$window,$ionicViewSwitcher,$ionicSideMenuDelegate,$ionicPlatform, $cordovaNativeAudio,$ionicModal,$ionicPopup,A,$localstorage,Navigation,$ionicPlatform,$ionicSlideBoxDelegate,$ionicScrollDelegate,$timeout,currentUser,$interval,$ionicActionSheet,$state,$sce,$cordovaCamera,$ionicLoading) {		
	
	$(window).on('load resize',function(){
	    if($(window).width() > 768){
	    	$('#goToMobile').show();
	        window.location = site_url;
	    }
	}); 

  	var multipleUpload = false;
	var ph = 0;
	var photoUploadSlot = 0;
	var upphotos = [];

	var extFilter = ["jpg", "jpeg", "png", "mp4", "ogg", "webm"];
	var storyAlbumFilter = ["video/3gpp", "video/mpeg", "video/mp4","video/webm","video/ogg"];
	$("#upload-area").dmUploader({
		url: site_url+'/assets/sources/upload.php',
		extFilter: extFilter,
		multiple: multipleUpload,
		onFileExtError: function(file){
			awlert.neutral(lang[596]['text']);
		},
	    onNewFile: function(id, file){
	    	var fileUrl = URL.createObjectURL(file);

	    	var fileType = file.type;
			var storyAlbumFilterResponse = (storyAlbumFilter.indexOf(fileType) > -1); 
			
	        if(file.size > config.max_upload){
	            var maxAllowed = config.max_upload / 1024 / 1024;
	            awlert.neutral(lang[809]['text']+' ('+maxAllowed+') MB',1500);
	            return false;
	        }

	    	if(upType == 1){
				var div = angular.element(document.getElementById('photo'+photoUploadSlot)); 
				div.css('background-image','url('+fileUrl+')');
				div.addClass('grayScale');
				awlert.neutral(lang[594].text+'...',1500);	 	
			}

			if(upType == 6){
			}		
			
	    },	
		onUploadProgress: function(id,percent){
			//$('#upload'+id).css('width',percent+'%');
		},
		onComplete: function(){
		},
		onUploadSuccess: function(id, file){
	  	
		if(upType == 6){

		}
		console.log(user);
	    upphotos[0] = file;
	    var photoPath = file.path;

	    console.log(file);

	    if(upType == 1){  	
			$.ajax({
			  type: "POST",
			  url: site_url+'requests/belloo.php',
			  data: {
			    action: 'uploadMedia',
			    media: upphotos
			  },
			  dataType: 'JSON',
			  success: function(response) {
				if(file.video == 0){

				}
				var slot = photoUploadSlot -1;
				$rootScope.uphotos[slot] = response['data'];
				usPhotos = $rootScope.uphotos;

				var div = angular.element(document.getElementById('photo'+photoUploadSlot));
				if(plugins['settings']['photoReview'] == 'No'){
					div.removeClass('grayScale');
				} else {
					$timeout(function() {
						awlert.neutral(lang[697].text,3000);	
					}, 1500);
				}
				
				awlert.neutral(lang[609].text,1000);					  	
			  }
			});

			if(plugins['story']['uploadToStory'] == 'Yes'){
				$.ajax({
				  type: "POST",
				  url: request_source()+'/belloo.php',
				  data: {
				    action: 'uploadStory',
				    media: upphotos
				  },
				  success: function(r) {

					
				  }
				});			
			}			 	    	
	    }

	    //force profile photo
	    if(upType == 5){  
	    	var bio = $('[data-force-profile-photo=5]').text();
			$.ajax({
			  type: "POST",
			  url: request_source()+'/belloo.php',
			  data: {
			    action: 'uploadMedia',
			    media: upphotos,
			    bio: bio
			  },
			  success: function(t) {
			  	$('[data-force-profile-photo="10"]').hide();
			  	$('.add-yourself').find('.profile-photo').attr('data-src',upphotos[0].path);
			  	goToProfile(user_info.id);
			  }
			});    	
	    }

	    //photo verification
	    if(upType == 3){
		   $.ajax({
		      type: "POST",
			  url: site_url+'requests/belloo.php',
		      data: {
		        action: 'verifyAccount',
		        media: upphotos
		      },
		      success: function(t) {
		      	  $rootScope.closeVerificationModal();
		      	  awlert.neutral(lang[601].text,5000);
		      }
		  });
	    }

	    //stories
	    if(upType == 4){
	    	if(url == 'discover'){
	    		$('[data-discover-upload-story="1"]').show();
	    	}
			$.ajax({
			  type: "POST",
			  url: request_source()+'/belloo.php',
			  data: {
			    action: 'uploadStory',
			    media: upphotos
			  },
			  success: function(r) {
			  	var story =  JSON.parse(r);
				storyLoader(story['story'],story['stories'],1,1);
				$('[data-upload-story]').show();
				
			  }
			}); 	   	
	    }
	  }
	  
	}); 
	  
	function createPreview(file, fileContents,id) {
		var $previewElement = '';
		switch (file.type) {
		  case 'image/png':
		  case 'image/jpeg':
		  case 'image/gif':
		    $previewElement = $('<img src="' + fileContents + '" />');
		    break;
		  case 'video/mp4':
		  case 'video/webm':
		  case 'video/ogg':
		    $previewElement = $('<video autoplay muted loop width="100%" height="100%"><source src="' + fileContents + '" type="' + file.type + '"></video>');
		    break;
		  default:
		    break;
		}
		var $displayElement = $('<div class="preview" style="cursor:pointer" data-manage-media="'+id+'" data-manage-media-path>\
								<div class="contentSwitch">\
		                            <div class="progress" data-upload-progress="upload'+id+'"><div class="determinate" id="upload'+id+'" style="width: 0%"></div></div>\
		                            </div>\
		                           <div class="preview__thumb uploadingGray" id="gray'+id+'"></div>\
			    				<label class="switch">\
	        <input type="checkbox" id="checkbox'+id+'">\
	        <span>\
	            <em></em>\
	            <strong></strong>\
	        </span>\
	    </label>\
		                         </div>');
		$displayElement.find('.preview__thumb').append($previewElement);
		$('[data-user-media]').append($displayElement);

	}


	$rootScope.logged = false;
	$rootScope.hideNav = false;
	$rootScope.siteUrl = site_url;
	$rootScope.trustPhoto = function(url){
		return $sce.trustAsResourceUrl(url);		
	}

	$rootScope.updateRegLocation = function(){
		$scope.currentLocation = reg_city + ' ' + reg_country;
	}

	$rootScope.storiesEnabled = true;
	if(plugins['story']['enabled'] == 'No'){
		$rootScope.storiesEnabled = false;
	}
	$rootScope.unreadMessage = false;

	$scope.changePage = function(url,slide,val) {
		currentUser.selectedUser=val;
		console.log(currentUser.selectedUser);
		if(window.cordova){
			$state.go(url, val); 
		} else {
			$state.go(url,slide, val); 		
		} 		 
	};

	$rootScope.openStory = function(uid,story){
		if(story > 0){
	        $.ajax({
	            url: request_source()+'/api.php', 
	            data: {
	                action:"viewStory",
	                uid: uid
	            },  
	            type: "GET",
	            dataType: 'JSON',
	            success: function(response) {
	                currentStoryUserData =  response['stories'];
	                socialStory = new Story({
	                    playlist: currentStoryUserData
	                });
	                $('.spinner').hide();
	                preloadStories(currentStoryUserData);
	                socialStory.launch();   
	            }
	        }); 
        } else {
        	if(url == 'messages'){
				try {		  
					var query = uid+',';
					$scope.ajaxRequest = A.Query.get({action: 'getUserData', query: query});
					$scope.ajaxRequest.$promise.then(function(){
					currentUser.selectedUser = $scope.ajaxRequest.user[0];
					$scope.changePage('home.messaging','left',currentUser.selectedUser);	
				  },
				  function(){}
				  )		 
				}
				catch (err) {
					console.log("Error " + err);
				}	        		
        	} else {
				try {		  
					var query = uid+',';
					$scope.ajaxRequest = A.Query.get({action: 'getUserData', query: query});
					$scope.ajaxRequest.$promise.then(function(){
					currentUser.selectedUser = $scope.ajaxRequest.user[0];
					$rootScope.openProfileStory(currentUser.selectedUser);	
				  },
				  function(){}
				  )		 
				}
				catch (err) {
					console.log("Error " + err);
				}	        		
        	}
        }
	}


	$rootScope.openPrivacy = function(){
		if (window.cordova) {
			cordova.InAppBrowser.open(site_url+'index.php?page=pp', '_blank', 'location=yes');
		} else {
			window.open(site_url+'index.php?page=pp', '_blank', 'location=yes');
		}		
	}
	$rootScope.openTerms = function(){
		if (window.cordova) {
			cordova.InAppBrowser.open(site_url+'index.php?page=tac', '_blank', 'location=yes');
		} else {
			window.open(site_url+'index.php?page=tac', '_blank', 'location=yes');
		};			
	}

	$rootScope.openStoryDiscover = function(element,uid,openAdmin=''){
	    viewingStory = element;
	    if(!isStoryLoading){
	        isStoryLoading = true;
	        $('[data-story-loader='+uid+']').show();
	        $.ajax({
	            url: request_source()+'/api.php', 
	            data: {
	                action:"viewStory",
	                uid: uid
	            },  
	            type: "GET",
	            dataType: 'JSON',
	            success: function(response) {
	                currentStoryUserData =  response['stories'];
	                socialStory = new Story({
	                    playlist: currentStoryUserData
	                });
	                preloadStories(currentStoryUserData);
	                storyAdmin = false;
	                socialStory.launch();
	                isStoryLoading = false;
	                $('[data-story-loader='+uid+']').hide();    
	            }
	        });
	    }   
	}

	$rootScope.updateCredits = function(uid,amount,type,reason,reward='') {
		if(type == 1){
			 user.credits = user.credits - amount;
		} else {
			user.credits = user.credits + amount;
		}
		user.credits = parseInt(user.credits);

		$localstorage.setObject('user',user);
		var query = uid+','+amount+','+type+','+reason+','+reward;
		A.Query.get({action: 'updateCredits', query: query});
		if(user.credits < 0){
			user.credits = 0;
			$localstorage.setObject('user',user);
			fixUserCredits();
		}
	}

	$rootScope.fixUserCredits = function(user) {
		$.ajax({
			data: {
				action: "fixUserCredits",
				id: user
			},
			url:   request_source()+'/api.php',
			type:  'GET',
			dataType: 'JSON',
			success:  function (response) {}
		});	
	}


	$rootScope.openChangeLocModal = function(page='') {
		$rootScope.changeLocation.show();
		if(page == 'register'){
			updateLocationRegister = 'Yes';
			$timeout(function() {
				document.getElementById("loc").focus();
			}, 1000);
		} else {
			updateLocationRegister = 'No';
		}
		$scope.locCity = user.city;
		$scope.focusInput = true;

		new TeleportAutocomplete({ el: '#loc', maxItems: 1,geoLocate:false});

	};

	$rootScope.closeChangeLocModal = function() {
		$rootScope.changeLocation.hide();	
		if(updateLocationRegister == 'Yes'){
			$state.go($state.current, {}, {reload: true});
		}	
	};	



	$rootScope.pushNotif = function(data,type=0,time=1000){

		if(type == 1){//credits
			$rootScope.playAudio('coin.wav');
		}
		if(!$('.chatNotification').hasClass('is-visible')){		
			
			$('.chatNotification').attr('ng-click',"hideNotification()");
			$compile($('.chatNotification'))($scope);								
			$('.chatNotification').removeClass('is-visible');
			$('.chatNotificationPhoto').removeClass('sblur');	
			$('.chatNotificationPhoto').css('background-image', 'url('+ data.icon +')');
			$('.chatNotificationContent').text(data.message);
			$timeout(function(){
				if(!$('.chatNotification').hasClass('is-visible')){
					$('.chatNotification').addClass('is-visible');
				}
			},100);				
			$timeout(function(){
				if($('.chatNotification').hasClass('is-visible')){
					$('.chatNotification').removeClass('is-visible');
				}
			},time);					
		}
	}

	$rootScope.playAudio = function(sound) {
        var audio = new Audio(site_url+'assets/sounds/'+sound);
        audio.play();
    };

    $rootScope.unEntity = function(str=""){
   		var string = str.replace(/&amp;/g, "&").replace(/&lt;/g, "<").replace(/&gt;/g, ">").replace(/&quot;/g, '');
   		return string;
	}

	$rootScope.currency = plugins['settings']['currency'];
    $rootScope.days = plugins['withdrawal']['days'];
    $rootScope.payoutMin = plugins['withdrawal']['minRequired'];

	$rootScope.unlockPhotos = function(cu){
		site_prices = $localstorage.getObject('prices');
		user =$localstorage.getObject('user');
		if(user.credits < site_prices.private){
			awlert.neutral(lang[817]['text']);
			$scope.openCreditsModal("'"+user.profile_photo+"'");
			return false;
		} else {
        	user.credits = user.credits - site_prices.private;
        	user.credits = parseInt(user.credits);
			$ionicLoading.show({
				content: 'Loading',
				animation: 'fade-in',
				showBackdrop: true,
				maxWidth: 200,
				showDelay: 0
			}); 													
			$.ajax({
				type: "POST",
				url: config.ajax_path +'/api.php', 
				data: {
					action:"p_access",
					id : cu.id,
					uid : user.id,
					credits: site_prices.private
				},			
				success: function(response) {
		        	$ionicLoading.hide();
		        	$rootScope.closePrivateImageModal();
		        	$rootScope.closeProfileModal();

				}
			});
		}
	}
	$rootScope.sendCreditsNow = function(c,id,cu){
		if(c == 0){
			awlert.neutral(lang[584]['text']);
			return false;		
		}
		if(c < 1){
			awlert.neutral(lang[584]['text']);
			return false;		
		}		
		if (user.credits < c) {
			awlert.neutral(lang[817]['text']);
			$scope.openCreditsModal("'"+user.profile_photo+"'");
			return false;			
		}  else {
        	user.credits = user.credits - c;
        	user.credits = parseInt(user.credits);
			$ionicLoading.show({
				content: 'Loading',
				animation: 'fade-in',
				showBackdrop: true,
				maxWidth: 200,
				showDelay: 0
			}); 

			var messageVal = 'Credits';
			var message = user.id+'[message]'+id+'[message]'+messageVal+'[message]credits'+'[message]'+c;
			var send = user.id+'[rt]'+id+'[rt]'+user.profile_photo+'[rt]'+user.first_name+'[rt]'+messageVal+'[rt]credits[rt]'+c;      

			$localstorage.setObject('user',user);

			A.Query.get({action: 'message', query: send});
			A.Query.get({action: 'sendMessage', query: message});

	        var data = [];
	        data.name = '';
	        data.icon = site_theme['notification_inapp_credits_icon']['val'];
	        data.message = c + " "+ lang[128]['text']  + ' ' + lang[587]['text']  + " " + cu.name;

			$rootScope.pushNotif(data,1);
	
			$timeout(function() {
	        	$ionicLoading.hide();
	        	$rootScope.closeSendCreditsModal();
	        	$rootScope.closeProfileModal();
	        	$rootScope.goToChatGlobal('home.messaging','left',cu);				
			}, 1500);
		}
	}
	$rootScope.hideNotification = function (){
		$('.inapp-notification-wrapper').removeClass('is-visible');
	}

	$rootScope.userStoriesMenu = function() {
		var hideSheet = $ionicActionSheet.show({
	        buttons: [
	          { text:  alang[262].text },
	          { text:  alang[271].text },
	        ],
			cancelText: alang[2].text,
			cancel: function() {
			},
			buttonClicked: function(index,val) {
				if(index == 1){
					$('#uploadStoryMobile').click();
				}
				if(index == 0){
					if(user.story == 0){
						user.story = $rootScope.checkHasStory(user.id);
						$localstorage.setObject('user',user);
						if(user.story > 0){
							$rootScope.openStory(user.id,1);
						} else {
							awlert.neutral(alang[272].text, 3000);						
						}
					} else {
						$rootScope.openStory(user.id,1);
					}
				}			
			  return true;
			}
		});
	}

	$rootScope.checkHasStory = function(id){
		try {	
			var query = id;
			$scope.ajax = A.Query.get({action: 'checkHasStory', query: query});
			$scope.ajax.$promise.then(function(){	
				return $scope.ajax['story'];
			},
			function(){}
			)		 
		}
		catch (err) {
			console.log("Error " + err);
		}		
	}

	$rootScope.openMenu = function() {
		$ionicSideMenuDelegate.toggleLeft();
	}  	
	$rootScope.toogleMenu = function(){
		alang = $localstorage.getObject('alang');
		$scope.alang = [];
		angular.forEach(alang,function(entry) {						  
		  $scope.alang.push({
			id: entry,
			text: entry.text
		  });
		})

		lang = $localstorage.getObject('lang');
		$scope.lang = [];
		angular.forEach(alang,function(entry) {						  
		  $scope.lang.push({
			id: entry,
			text: entry.text
		  });
		})				
		$ionicSideMenuDelegate.toggleLeft();
	}
	$rootScope.gameGlobal = function(id,val,current_user){	
		if(plugins['discover']['creditForLike'] > 0){
			if(user.credits < plugins['discover']['creditForLike']){ 
				$scope.openCreditsModal();
				return false;
			}
			var data = [];
			data.name = '';
			data.icon = site_theme['notification_inapp_credits_icon']['val'];
			data.message = lang[610].text+' '+plugins['discover']['creditForLike']+' ' + lang[73].text;
			$rootScope.pushNotif(data,1);
			$rootScope.updateCredits(user.id,plugins['discover']['creditForLike'],1,'Credits for like');
		}		
		angular.element(document.querySelector('#meetLike'+id)).css('display','block');		
		A.Meet.get({action: 'game_like',uid1: user.id, uid2: id, uid3: val});		
	};
	$rootScope.goToChatGlobal = function(url,slide,val,action='') {
		currentUser.selectedUser = val;
		goToChatGlobalAction = action;
		console.log(currentUser.selectedUser);
		if(window.cordova){
			$ionicSideMenuDelegate.toggleLeft();
			$state.go(url, val);
		} else {
			$ionicSideMenuDelegate.toggleLeft();
			$state.go(url, val); 		
		} 
	};

	$rootScope.openProfileExplore = function(user){
		$rootScope.openProfileModal(user.full.id,user.name,user.discoverPhoto,user.full.age,user.full.city,user.status);		
	}

	$rootScope.openProfileStory = function(user){
		$rootScope.openProfileModal(user.id,user.name,user.profile_photo_big,user.age,user.city,user.status);		
	}	


	$rootScope.openModalBoost = function(val){
		user = $localstorage.getObject('user');
		lang = $localstorage.getObject('lang');
		site_prices = $localstorage.getObject('prices');
		user.credits = parseInt(user.credits);

		var a = '';
		var action = '';
		if(val == 1){
			//RiseUp
			a = site_prices.first;
			a = parseInt(a);
			boostAction = 'riseUp';
			boostPrice = a;
			$rootScope.boostBtnText = lang[416].text;
			$rootScope.boostTitle = lang[414].text;
			$rootScope.boostDesc = lang[415].text;
			$rootScope.boostPrice = a;
		}
		if(val == 2){
			//100times
			a = site_prices.discover;
			a = parseInt(a);
			boostAction = 'discover100';
			boostPrice = a;
			$rootScope.boostBtnText = lang[526].text;
			$rootScope.boostTitle = lang[526].text;
			$rootScope.boostDesc = lang[527].text;
			$rootScope.boostPrice = a;	
		}		
		$rootScope.modalBoost = true;
	}
	$rootScope.closeModalBoost = function(){
		$rootScope.modalBoost = false;	
	}

	//INCREASE VISIBILITY
	$rootScope.boostBtn = function(){
		user = $localstorage.getObject('user');
		user.credits = parseInt(user.credits);
		if(user.credits < boostPrice){
			$scope.openCreditsModal("'"+user.profile_photo+"'");
		} else {
			boostFn();
		}
	}		
	var boostFn = function () {
		var val = user.id+','+boostPrice;
		if(boostAction == 'discover100'){
			$rootScope.showBoostDiscover = false;
		}
        var data = [];
        data.name = '';
        data.icon = site_theme['notification_inapp_credits_icon']['val'];
        data.message = lang[610]['text'] + " "+ a + ' ' + lang[73]['text'];
		$rootScope.pushNotif(data,1);			
		try {	
		  $scope.ajaxRequest = A.Query.get({action: boostAction, query: val});
		  $scope.ajaxRequest.$promise.then(function(){
			  $localstorage.setObject('user', $scope.ajaxRequest.user);
			  user = $localstorage.getObject('user');
		  },
		  function(){}
		  )		 
		}
		catch (err) {
			console.log("Error " + err);
		}	
	}


	$rootScope.goTo = function(url,slide) {
		if(window.cordova){
			$state.go(url);
		} else {		
			$state.go(url); 		
		}  
	};	

	$rootScope.goBack = function(){
		$ionicHistory.goBack();	
	}		

	$rootScope.proceedToWithdraw = false;

	$rootScope.showWithdraw = function(){
		$rootScope.proceedToWithdraw = true;
		$ionicLoading.show({
			content: 'Loading',
			animation: 'fade-in',
			showBackdrop: true,
			maxWidth: 200,
			showDelay: 0
		});	
		$timeout(function() {
			$ionicLoading.hide();
		}, 1200);	
	}

	$rootScope.hideWithdraw = function(){
		$('#proceedToWithdraw').hide();
		$rootScope.proceedToWithdraw = false;	
	}

	function validateEmail(email) {
		var re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
		return re.test(email);
	}

	$rootScope.withdrawNow = function(p){


		if (user.payout < plugins['withdrawal']['minRequired']) {
			awlert.neutral(lang[585].text, 5000);
			return false;	    
		} 	
		var t = user.payout,
		e = user.credits;

		if (!validateEmail(p)) {		
			awlert.neutral(alang[5].text,2000);
			return false;		
		}

		if(p == ''){
			awlert.neutral(lang[589].text, 5000);
			return false;				
		}
		if (user.payout < plugins['withdrawal']['minRequired']) {
			awlert.neutral(lang[585].text, 5000);
			return false;	    
		}  else {
		     $.ajax({
		        type: "POST",
		        url: request_source()+"/api.php",
		        data: {
		            action: "withdraw",
		            paypal: p,
		            uid: user.id,
		            credits: e,
		            money: t
		        },
		        success: function(t) {
	            	awlert.neutral(alang[270].text, 5000);
	            	$rootScope.hideWithdraw();
					$rootScope.me.pendingPayout = 1;
					$rootScope.me.payout = 0;
					$rootScope.me.credits = 0;
					user.credits = 0;
					user.payout = 0;
					user.pendingPayout = 1;
					$localstorage.setObject('user',user);
					$timeout(function() {
						$rootScope.closeWithdrwal();
					}, 5000);
		        }
		    })
		}			
	}

	$rootScope.goBackSettings = function(){
		$state.go('home.'+goBackGlobal);
	}
	$rootScope.aImages = '';
	$rootScope.cards = [];

	//MENU WIDTH
	var a = $window.innerWidth;
	a = a - 25;
	$rootScope.menuWidth = a;
	var b = $window.innerHeight;
	$rootScope.menuHeight = b;		
	$rootScope.appGifts = [];
	$rootScope.logout = function(){
		var message = user.id;
		oneSignalID = 0;
		A.Query.get({action: 'logout', query: message});
		$localstorage.setObject('user','');
		$localstorage.set('userHistory','');
		chats = [];
		matche = [];
		mylikes = [];
		myfans = [];
		cards = [];
		visitors = [];
		$ionicSideMenuDelegate.toggleLeft();
		$scope.closeEditProfileModal();
		window.location.href = siteUrl+'logout';	
	}


	$rootScope.loader = function(){
	  user = $localstorage.getObject('user');
	  if(user == ''){
	  	oneSignalID = -1;
	  } else {
	  	oneSignalID = user.id;
	  	userId = user.id;
	  }

	  try {	
		  $scope.ajaxRequest = A.Device.get({action: 'config', dID: oneSignalID, siteLang: siteLang});
		  $scope.ajaxRequest.$promise.then(function(){											
				$localstorage.setObject('config', $scope.ajaxRequest.config);
				$localstorage.setObject('app', $scope.ajaxRequest.app);
				app = $scope.ajaxRequest.app;
				$localstorage.setObject('prices', $scope.ajaxRequest.prices);
				max_ad = $scope.ajaxRequest.ad;
				var isAndroid = ionic.Platform.isAndroid();
				console.log(isAndroid);
				if(isAndroid){
					adMobI = $scope.ajaxRequest.adMobA;
				} else {
					adMobI = $scope.ajaxRequest.adMobI;
				}
				var l1 = $scope.ajaxRequest.lang;
				var l2 = $scope.ajaxRequest.alang;
				
				angular.forEach(l1,function(entry) {	
					l1[entry.id].text = entry.text;					  
					//l1[entry.id].text = entrytext.replace("&#039;", "'");	
				});	
				angular.forEach(l2,function(entry) {					  
				  l2[entry.id].text = entry.text.replace("&#039;", "'");		
				});	
				site_lang = $scope.ajaxRequest.lang;
				site_config = $scope.ajaxRequest.config;											
				$localstorage.setObject('lang', l1);
				$localstorage.setObject('alang', l2);
				$rootScope.alang = l2;
				$rootScope.appConfig = $scope.ajaxRequest.config;
					
				$localstorage.setObject('user', $scope.ajaxRequest.user);

				$localstorage.setObject('premium_package', $scope.ajaxRequest.premium_package);
				$localstorage.setObject('credits_package', $scope.ajaxRequest.credits_package);					
				$localstorage.setObject('account_basic', $scope.ajaxRequest.account_basic);
				$localstorage.setObject('account_premium', $scope.ajaxRequest.account_premium);
				$localstorage.setObject('gifts', $scope.ajaxRequest.gifts);
				$rootScope.appGifts = $scope.ajaxRequest.gifts;
				$rootScope.languages = $scope.ajaxRequest.config.languages;
			
				if($scope.ajaxRequest.user != ''){
					$localstorage.setObject('usPhotos', $scope.ajaxRequest.user.photos);
					usPhotos = $scope.ajaxRequest.user.photos;
					sape = $scope.ajaxRequest.user.slike;
					userId = $scope.ajaxRequest.user.id;
					var rtnotification = 'notification'+$scope.ajaxRequest.user.id;
					channel.bind(rtnotification, function(data) {
						if(data.id != current_user_id ){
							$rootScope.unreadMessage = true;
							if(!$('.chatNotification').hasClass('is-visible')){		

								$rootScope.playAudio('Notification.wav');
								$('.chatNotification').attr('ng-click',"goTo('home.matches');hideNotification()");
								$compile($('.chatNotification'))($scope);								
								$('.chatNotification').removeClass('is-visible');
								$('.chatNotificationPhoto').removeClass('sblur');	
								$('.chatNotificationPhoto').css('background-image', 'url('+ data.icon +')');
								$('.chatNotificationContent').text(data.message);
								$timeout(function(){
									if(!$('.chatNotification').hasClass('is-visible')){
										$('.chatNotification').addClass('is-visible');
									}
								},100);				
								$timeout(function(){
									if($('.chatNotification').hasClass('is-visible')){
										$('.chatNotification').removeClass('is-visible');
									}
								},3000);					
							}
						}
					});
					var rtvisit = 'visit'+$scope.ajaxRequest.user.id;
					channel.bind(rtvisit, function(data) {	
						if(!$('.chatNotification').hasClass('is-visible')){	

							$rootScope.playAudio('Notification.wav');
							$('.chatNotification').attr('ng-click',"goTo('home.visitors');hideNotification()");
							$compile($('.chatNotification'))($scope);		

							$('.chatNotification').removeClass('is-visible');
							$('.chatNotificationPhoto').removeClass('sblur');	
							$('.chatNotificationPhoto').css('background-image', 'url('+ data.icon +')');
							$('.chatNotificationContent').text(data.message);
							$timeout(function(){
								if(!$('.chatNotification').hasClass('is-visible')){
									$('.chatNotification').addClass('is-visible');
								}
							},100);				
							$timeout(function(){
								if($('.chatNotification').hasClass('is-visible')){
									$('.chatNotification').removeClass('is-visible');
								}
							},3000);
						}					
					});	
					var rtlike = 'like'+$scope.ajaxRequest.user.id;
					channel.bind(rtlike, function(data) {
						if(!$('.chatNotification').hasClass('is-visible')){		
							$('.chatNotification').removeClass('is-visible');

							$rootScope.playAudio('Notification.wav');
							$('.chatNotification').attr('ng-click',"goTo('home.match');hideNotification()");
							$compile($('.chatNotification'))($scope);

							if(user.premium == 1){
								$('.chatNotificationPhoto').removeClass('sblur');	
							} else {
								$('.chatNotificationPhoto').addClass('sblur');
							}							
							$('.chatNotificationPhoto').css('background-image', 'url('+ data.icon +')');
							$('.chatNotificationContent').text(data.message);
							$timeout(function(){
								if(!$('.chatNotification').hasClass('is-visible')){
									$('.chatNotification').addClass('is-visible');
								}
							},100);				
							$timeout(function(){
								if($('.chatNotification').hasClass('is-visible')){
									$('.chatNotification').removeClass('is-visible');
								}
							},3000);						
						}
					});																				
					$state.go('home.explore');					
				} else {
					$state.go('intro');
				}
				$rootScope.logo = app.logo;
				var style = document.createElement('style');
				style.type = 'text/css';
				style.innerHTML = '.bg-tinder {background:'+app.first_color+'; background: -moz-linear-gradient(left,  '+app.first_color+' 0%, '+app.second_color+' 100%);background: -webkit-linear-gradient(left,  '+app.first_color+' 0%,'+app.second_color+' 100%); background: linear-gradient(to right,  '+app.first_color+' 0%,'+app.second_color+' 100%); color:#fff }';
				document.getElementsByTagName('head')[0].appendChild(style);		
		  },
		  function(){}
		  )		 
	  }
	  catch (err) {
		console.log("Error " + err);
	  }		
	}

	$rootScope.showGoToChat = false;
	if(plugins['chat']['firstMessage'] == allG || plugins['chat']['firstMessage'] == user.gender){
		$rootScope.showGoToChat = true;
	}

	$rootScope.showMobileAd = false;


    $rootScope.playSound = function(sound) {
        //$cordovaNativeAudio.play(sound);
    };

    $ionicModal.fromTemplateUrl('templates/'+mobileTheme+'/modals/profile_edit.html', {
      scope: $scope,
      animation: 'slide-in-up'
    }).then(function(modal) {
      $scope.editProfileModal = modal;
    });

    $ionicModal.fromTemplateUrl('templates/'+mobileTheme+'/modals/changeLocation.html', {
      scope: $rootScope,
      animation: 'slide-in-up'
    }).then(function(modal) {
      $rootScope.changeLocation = modal;
    });    

    $ionicModal.fromTemplateUrl('templates/'+mobileTheme+'/modals/profile_languages.html', {
      scope: $rootScope,
      animation: 'slide-in-up'
    }).then(function(modal) {
      $rootScope.editLanguageModal = modal;
    }); 
    $ionicModal.fromTemplateUrl('templates/'+mobileTheme+'/modals/ad.html', {
      scope: $rootScope,
      animation: 'slide-in-up'
    }).then(function(modal) {
      $rootScope.adModal = modal;
    });        


    $scope.openEditProfileModal = function() {
		$scope.editProfileModal.show();
		user = $localstorage.getObject('user');
		lang = $localstorage.getObject('lang');
		$('[data-lid]').each(function(){
		  var id = $(this).attr('data-lid');
		  $(this).text(lang[id].text);
		});

		lang = $localstorage.getObject('lang');
		$scope.lang = [];
		angular.forEach(lang,function(entry) {						  
		  $scope.lang.push({
			id: entry,
			text: entry.text
		  });
		})	

		$scope.noData = lang[279].text;
		$scope.lang = lang[134].text;
		$scope.loading = false;
		$scope.bio = user.bio;
		$scope.name = user.name;
		$scope.age = user.age;		
		$rootScope.uphotos = usPhotos;
		console.log(usPhotos);
		$rootScope.questions = user.question;		
		var answers = user.question;
		var la = user.lang;
		console.log('lang:'+la);
		console.log(config.languages);
		angular.forEach(config.languages,function(lang) {
			if(lang['id'] == user.lang){
				$scope.userLang = lang['name'];
			}
		});
		
		$rootScope.updateUserLanguage = function() {
		  $rootScope.openLanguagesModal();
		  $rootScope.languages = config.languages;
		  /*
		  var hideSheet = $ionicActionSheet.show({
			buttons: config.languages,
			cancelText: alang[2].text,
			cancel: function() {
			  },
			buttonClicked: function(index,val) {
				var message = user.id+','+val.id;
				$scope.loading = true;
				$scope.ajaxRequest34 = A.Query.get({action: 'updateUserLanguage', query: message});
				$scope.ajaxRequest34.$promise.then(function(){											
					$state.go('home.loader');
				});				
			  return true;
			}
		  });*/
		}

		$scope.updateNotification = function(e,a) {
			if(a === true){
				a = 1;
			} else {
				a = 0;
			}
			var message = user.id+','+e+','+a;
			$scope.ajaxRequest = A.Query.get({action: 'updateNotification', query: message});
			$scope.ajaxRequest.$promise.then(function(){											
			});			
		};

		if(user.notification.fan.inapp == 1){
			$scope.fans = true;
		} else {
			$scope.fans = false;
		}
		if(user.notification.near_me.inapp == 1){
			$scope.near_me = true;
		} else {
			$scope.near_me = false;
		}

		if(user.notification.match_me.inapp == 1){
			$scope.matches = true;
		} else {
			$scope.matches = false;
		}
		if(user.notification.message.inapp == 1){
			$scope.messages = true;
		} else {
			$scope.messages = false;
		}		



		$scope.pick = function(id=0) {
			photoUploadSlot = id;
			upType = 1;
			$('#uploadContent').click();		
		};

		$rootScope.selectLanguage = function(id){
			console.log(updateVisitorLanguage);

			if(updateVisitorLanguage){
				window.location.href = siteUrl+'mobile/index.php?lang='+id;
			} else {
				$scope.loading = true;
				var message = user.id+','+id;
				$scope.ajaxRequest34 = A.Query.get({action: 'updateUserLanguage', query: message});
				$scope.ajaxRequest34.$promise.then(function(){											
					$rootScope.closeLanguagesModal();
					$scope.closeEditProfileModal();
					siteLang = id;
					$state.go('loader');
				});	
			}		
		}

		$rootScope.updateUserQuestion = function(q,a) {
		  if(q.method == 'select'){
			  var hideSheet = $ionicActionSheet.show({
				buttons: a,
				cancelText: alang[2].text,
				cancel: function() {
				  },
				buttonClicked: function(index,val) {
					var message = user.id+','+q.id+','+val.text;
					$scope.loading = true;
					var e = angular.element(document.getElementsByClassName('userAnswer'+q.id));
					e.text(val.text);
					e.css('color', '#111');
					$scope.ajaxRequest34 = A.Query.get({action: 'updateUserExtended', query: message});
					$scope.ajaxRequest34.$promise.then(function(){											
						$localstorage.setObject('user', $scope.ajaxRequest34.user);
						$scope.loading = false;
					});				
				  return true;
				}
			  });
		  } else {

		  }

		}

	   $rootScope.showPhotoOptions = function(val,pid,blocked,profile,approved) {
	   		if(approved == 0){
	   			awlert.neutral(lang[697].text,2000);
	   			return false;
	   		}
	      var hideSheet = $ionicActionSheet.show({
	        buttons: [
	          { text:  lang[289].text },
	          { text:  lang[292].text },
	        ],
	        cancelText: lang[617].text,
	        cancel: function() {
	          },
	        buttonClicked: function(index) {
			  if(index ==0){
				  var p = $rootScope.uphotos[0];
				  if(val == 2){
					var n = $rootScope.uphotos[1];
					$rootScope.uphotos[0] = n;
					$rootScope.uphotos[1] = p;
					var m = user.id +','+pid;
					$scope.ajaxRequest = A.Query.get({action: 'updateUserProfilePhoto', query: m});
					$scope.ajaxRequest.$promise.then(function(){							
						$localstorage.setObject('user', $scope.ajaxRequest.user);
						usPhotos = $scope.ajaxRequest.user.photos;	
						$rootScope.me = $scope.ajaxRequest.user;
					}); 
				  }
				  if(val == 3){
					var n = $rootScope.uphotos[2]
					$rootScope.uphotos[0] = n;
					$rootScope.uphotos[2] = p;
					var m = user.id +','+pid;
					$scope.ajaxRequest = A.Query.get({action: 'updateUserProfilePhoto', query: m});
					$scope.ajaxRequest.$promise.then(function(){							
						$localstorage.setObject('user', $scope.ajaxRequest.user);
						usPhotos = $scope.ajaxRequest.user.photos;	
						$rootScope.me = $scope.ajaxRequest.user;
					}); 
				  }
				  if(val == 4){
					var n = $rootScope.uphotos[3]
					$rootScope.uphotos[0] = n;
					$rootScope.uphotos[3] = p;
					var m = user.id +','+pid;
					$scope.ajaxRequest = A.Query.get({action: 'updateUserProfilePhoto', query: m});
					$scope.ajaxRequest.$promise.then(function(){							
						$localstorage.setObject('user', $scope.ajaxRequest.user);
						usPhotos = $scope.ajaxRequest.user.photos;	
						$rootScope.me = $scope.ajaxRequest.user;
					}); 
				  }
				  if(val == 5){
					var n = $rootScope.uphotos[4]
					$rootScope.uphotos[0] = n;
					$rootScope.uphotos[4] = p;
					var m = user.id +','+pid;
					$scope.ajaxRequest = A.Query.get({action: 'updateUserProfilePhoto', query: m});
					$scope.ajaxRequest.$promise.then(function(){							
						$localstorage.setObject('user', $scope.ajaxRequest.user);
						usPhotos = $scope.ajaxRequest.user.photos;	
						$rootScope.me = $scope.ajaxRequest.user;
					}); 
				  }
				  if(val == 6){
					var n = $rootScope.uphotos[5]
					$rootScope.uphotos[0] = n;
					$rootScope.uphotos[5] = p;
					var m = user.id +','+pid;
					$scope.ajaxRequest = A.Query.get({action: 'updateUserProfilePhoto', query: m});
					$scope.ajaxRequest.$promise.then(function(){							
						$localstorage.setObject('user', $scope.ajaxRequest.user);
						usPhotos = $scope.ajaxRequest.user.photos;
						$rootScope.me = $scope.ajaxRequest.user;
					}); 
				  }
			  }
			  if(index == 1){
				  if(val == 2){
					$rootScope.uphotos[1] = null;
					var m = user.id +','+pid;
					$scope.ajaxRequest = A.Query.get({action: 'deletePhoto', query: m});
					$scope.ajaxRequest.$promise.then(function(){							
						usPhotos = $scope.ajaxRequest.user.photos;	
						$rootScope.me = $scope.ajaxRequest.user;
					}); 				
				  }
				  if(val == 3){
					$rootScope.uphotos[2] = null;
					var m = user.id +','+pid;
					$scope.ajaxRequest = A.Query.get({action: 'deletePhoto', query: m});
					$scope.ajaxRequest.$promise.then(function(){							
						usPhotos = $scope.ajaxRequest.user.photos;	
						$rootScope.me = $scope.ajaxRequest.user;
					});
				  }
				  if(val == 4){
					$rootScope.uphotos[3] = null;
					var m = user.id +','+pid;
					$scope.ajaxRequest = A.Query.get({action: 'deletePhoto', query: m});
					$scope.ajaxRequest.$promise.then(function(){							
						usPhotos = $scope.ajaxRequest.user.photos;
						$rootScope.me = $scope.ajaxRequest.user;	
					});
				  }
				  if(val == 5){
					$rootScope.uphotos[4] = null;
					var m = user.id +','+pid;
					$scope.ajaxRequest = A.Query.get({action: 'deletePhoto', query: m});
					$scope.ajaxRequest.$promise.then(function(){							
						usPhotos = $scope.ajaxRequest.user.photos;	
						$rootScope.me = $scope.ajaxRequest.user;
					});
				  }
				  if(val == 6){
					$rootScope.uphotos[5] = null;
					var m = user.id +','+pid;
					$scope.ajaxRequest = A.Query.get({action: 'deletePhoto', query: m});
					$scope.ajaxRequest.$promise.then(function(){							
						usPhotos = $scope.ajaxRequest.user.photos;	
						$rootScope.me = $scope.ajaxRequest.user;
					});
				  }
				  
			  }
	          return true;
	        }
	      });
	    }	

		$('#userName').change(function(){
			var val = $(this).val();
			var col = 'name';
			$scope.loading = true;
			var message = user.id+','+val+','+col;
			$scope.ajaxRequest14 = A.Query.get({action: 'updateUser', query: message});
			$scope.ajaxRequest14.$promise.then(function(){											
				$localstorage.setObject('user', $scope.ajaxRequest14.user);
				$scope.loading = false;				
			});				
		});
		$('#userAge').change(function(){
			var val = $(this).val();
			var col = 'age';
			$scope.loading = true;
			var message = user.id+','+val+','+col;
			$scope.ajaxRequest14 = A.Query.get({action: 'updateUser', query: message});
			$scope.ajaxRequest14.$promise.then(function(){											
				$localstorage.setObject('user', $scope.ajaxRequest14.user);
				$scope.loading = false;				
			});				
		});		
		$('#userBio').change(function(){
			var val = $(this).val();
			var col = 'bio';
			var message = user.id+'[divider]'+val+'[divider]'+col;
			$scope.ajaxRequest14 = A.Query.get({action: 'updateUser', query: message});
			$scope.ajaxRequest14.$promise.then(function(){											
				$localstorage.setObject('user', $scope.ajaxRequest14.user);
			});				
		});		
		var l = user.gender - 1;
		$scope.gender = config.genders[l].text;		

		$scope.updateUserGender = function() {
		  var hideSheet = $ionicActionSheet.show({
			buttons: config.genders,
			cancelText: alang[2].text,
			cancel: function() {
			  },
			buttonClicked: function(index,val) {
				var gender;
				$scope.gender = val.text;		
				gender = val.id;	
				var message = user.id+','+gender;
				$scope.ajaxRequest34 = A.Query.get({action: 'updateUserGender', query: message});
				$scope.ajaxRequest34.$promise.then(function(){											
					$localstorage.setObject('user', $scope.ajaxRequest34.user);
				});				
			  return true;
			}
		  });
		}		
    }
    $scope.closeEditProfileModal = function() {
      $scope.editProfileModal.hide();
	  $state.go('home.profile');
    };


	$rootScope.deleteProfile = function(){
		  var hideSheet = $ionicActionSheet.show({
			buttons: [
			  { text: lang[150].text }
			],
			cancelText: alang[2].text,
			cancel: function() {
			  },
			buttonClicked: function(index) {	
				var message = user.id;
				oneSignalID = null;
				A.Query.get({action: 'delete_profile', query: message});
				$localstorage.setObject('user','');
				$localstorage.set('userHistory','');
				chats = [];
				matche = [];
				mylikes = [];
				myfans = [];
				cards = [];
				visitors = [];
				$ionicSideMenuDelegate.toggleLeft();
				$scope.closeEditProfileModal();
				$state.go('loader');	
			}
		  });

	}

	//VIDEOCALL SYSTEM	
	  $ionicModal.fromTemplateUrl('templates/'+mobileTheme+'/modals/video.html', {
		scope: $scope,
		animation: 'slide-in-up'
	  }).then(function(modal) {
		$scope.videoModal = modal;
	  });
	  $scope.closeVideoModal = function() {
		$scope.videoModal.hide(); 
		//check anumation
		var check = $('body').hasClass( "anim-start" );
		if(check === true){
			$('body').toggleClass('anim-start');							
		} 
		finishCall(true);
		//window.location.reload();
	}

	function saveCall(c_id,r_id){
	    $.ajax({
	      url: site_url+'requests/videocall.php',
	      data:{
	        action: 'saveCall',
	        c_id: c_id,
	        r_id: r_id,
	        callId: callId
	      },        
	      type:"post",
	      success:function(){}
	    });	
	}
	function endCall(callId,min,sec,totalSeconds){
	    $.ajax({
	      url: site_url+'requests/videocall.php',
	      data:{
	        action: 'log',
	        callId: callId,
	        min: min,
	        sec: sec,
	        totalSeconds: totalSeconds
	      },        
	      type:"post",
	      success:function(){}
	    }); 
	}
	function updateCallStatus(callId) {
	  $.ajax({
		url: site_url+'requests/videocall.php',
		data: {
			action:"callStatus",
			callId: callId
		 },			
		type: "post",
		success: function(data) {	}
	  });
	}

	function finishCall(val) {
		user = $localstorage.getObject('user');
		var c_id;
		var r_id;
		console.log('call id:'+callId);
		if(called){
			c_id = video_user;
			r_id = user.id;
		} else {
			c_id = user.id;
			r_id = video_user;
		}
		endCall(callId,minu,secu,sec);
		console.log('caller id:'+c_id);
		sec = 0;
		clearInterval(window.timer);


		$('.videocall-notify').fadeOut();
		$('.videocall-container').fadeOut();
		in_videocall = false;
		endedVideocall = val;

		if(plugins['recordVideocall']['enabled'] == 'Yes'){
			if(plugins['recordVideocall']['filterGender'] == user.gender){
				//stopRecording(user.id,callId,called);
			}
			if(plugins['recordVideocall']['filterGender'] == allG){
				//stopRecording(user.id,callId,called);
			}			
		}

		var MediaStream = window.localStream || window.MediaStream;

		if (typeof MediaStream === 'undefined' && typeof webkitMediaStream !== 'undefined') {
		    MediaStream = webkitMediaStream;
		}

		if (typeof MediaStream !== 'undefined' && !('stop' in MediaStream.prototype)) {
		    MediaStream.prototype.stop = function() {
		        this.getTracks().forEach(function(track) {
		            track.stop();
		        });
		    };
		}		

		//check anumation
		var check = $('body').hasClass( "anim-start" );
		if(check === true){
			$('body').toggleClass('anim-start');							
		}   

		//window.location.reload();
		//$(".chat-container").scrollTop(100000);

		//$('#callSound')[0].pause();
		peerConnect(1);
		
	}

	function pad(val) {
		return val > 9 ? val : "0" + val;
	} 

	function peerConnect(con) {
		
		user = $localstorage.getObject('user');
		config = $localstorage.getObject('config');
		if(con == 1){
			if(peer != undefined){
				peer.destroy();
			}
		}

		peer = new Peer({secure:true});		
		peer.on('open', function(){
			var query = user.id+','+peer.id; 
			A.Query.get({action: 'updatePeer' ,query: query});					 
			endedVideocall = false;
			console.log('videocall active '+ peer.id);			
		});
		
		peer.on('error', function(err){
			console.log(err);
		});	
		peer.on('call', onReceiveCall);			
	}
	
	
	if(user.premium == 0 && account_basic.videocall == 0 && plugins['videocall']['freeUserCall'] == 'No') {	
		$interval(function(){
			config = $localstorage.getObject('config');						   
			if(in_videocall === false && user != '' && config != '' && config.videocall != '' && plugins['videocall']['enabled'] == 'Yes'){
				peerConnect(1);
			} 
		}, 50000);
	
		$timeout(function(){
			config = $localstorage.getObject('config');					  
			if(in_videocall === false && user != '' && config != ''  && plugins['videocall']['enabled'] == 'Yes'){
				peerConnect(0);
			} 
		}, 6000);
	
	}
	function getVideo(successCallback, errorCallback){
		var constraints = window.constraints = {
		  audio: true,
		  video: true
		};	
		navigator.mediaDevices.getUserMedia(constraints).then(successCallback).catch(errorCallback);		
	}
				
	function onReceiveCall(call){
		window.existingCall = call;
		try {		  
			$scope.getCaller = A.Query.get({action: 'income' ,query: call.peer});
			$scope.getCaller.$promise.then(function(){
				var caller = $scope.getCaller;
				$scope.called = true;
				called = true;
				callId = caller.peer+peer.id;

				$scope.videoModal.show();	
				$scope.name = 'Incoming call';
				$scope.text = caller.name+" wants to start a videocall with you";	
				$('.ball').css("background-image",'url(' + caller.photo + ')');
				$('.videopb').css("background-image",'url(' + caller.photo + ')');				
				$timeout(function(){
					$('body').toggleClass('anim-start');
				}, 300);
				$scope.acceptCall = function(){
					$scope.called = false;
					in_videocall = true;
					video_user = caller.id;
					//record
						
					getVideo(
						function(MediaStream){
							window.stream = MediaStream;	
							if(plugins['recordVideocall']['enabled'] == 'Yes'){
								if(plugins['recordVideocall']['filterGender'] == user.gender){
									startRecording();
								}
								if(plugins['recordVideocall']['filterGender'] == allG){
									startRecording();
								}			
							}							
							call.answer(MediaStream);
							window.localStream = MediaStream;
							updateCallStatus(callId);						
						},
						function(err){
							$ionicPopup.alert({
								title: 'Error',
								template: 'An error occured while try to connect to the device mic and camera'
							});
						}
					);					
				}
		},
			function(){})		 
		}
		catch (err) {
			console.log("Error " + err);
		}
		call.on('stream', function(stream){
			$scope.videoModal.show();
			in_videocall = true;
			$rootScope.inCall = true;
			var video = document.getElementById('theirCam');
			video.srcObject = stream;
			$('#theirCam').fadeIn();	
			$('#myCam').addClass('myCamOnCall');					
			//$('.videocall-container').fadeOut();
		});
	}
	

	function onReceiveStream(stream){	
		$scope.videoModal.show();
		in_videocall = true;
		$rootScope.inCall = true;
		var video = document.getElementById('theirCam');
		video.srcObject = stream;
		$('#theirCam').fadeIn();	
		$('#myCam').addClass('myCamOnCall');
		window.timer = setInterval(function () {
			secu = pad(++sec % 60);
			minu = pad(parseInt(sec / 60, 10));
		}, 1000);			
		//$('.videocall-container').fadeOut();
	}
	$scope.startVideocall = function(val){

		if(user.premium == 0 && account_basic.videocall == 0 && plugins['videocall']['freeUserCall'] == 'No') {
			var dataNotif = [];
			dataNotif.name = lang['449']['text'];
			dataNotif.icon = user.profile_photo;
			dataNotif.message = lang['211']['text'];
			$rootScope.pushNotif(dataNotif,2);
			return false;
		}

		$scope.called = false;
		called = false;
		$scope.videoModal.show();
		$scope.name = chatUser.name;
		$scope.text = 'calling..';
		$('.ball').css("background-image",'url(' + chatUser.photo + ')');
		$('.videopb').css("background-image",'url(' + chatUser.photo + ')');
		$timeout(function(){
			$('body').toggleClass('anim-start');
			$('.videocall-container').fadeIn();
		}, 300);			
		getVideo(
			function(MediaStream){	
				window.localStream = MediaStream;
				var video = document.getElementById('myCam');
				video.srcObject = MediaStream;
				//record
				window.stream = MediaStream;
				if(plugins['recordVideocall']['enabled'] == 'Yes'){
					if(plugins['recordVideocall']['filterGender'] == user.gender){
						//startRecording();
					}
					if(plugins['recordVideocall']['filterGender'] == allG){
						//startRecording();
					}			
				}				
				try {		  
					$scope.getUserPeer = A.Query.get({action: 'getpeerid' ,query: chatUser.id});
					$scope.getUserPeer.$promise.then(function(){
						var userPeer = $scope.getUserPeer.peer;
						var call = peer.call(userPeer, MediaStream);
						saveCall(user.id,chatUser.id);
						video_user = chatUser.id;	
						callId = peer.id+chatUser.id;		
						call.on('stream', onReceiveStream);
						call.on('close', function(){
							if(endedVideocall === false) {						
								finishCall(false);
							} 
						});							
				},
					function(){})		 
				}
				catch (err) {
					console.log("Error " + err);
				}				
			},
			function(err){
				$ionicPopup.alert({
					title: 'Error',
					template: 'An error occured while try to connect to the device mic and camera'
				});
			}
		);

	};

	$scope.videoModa = function(){
		$scope.videoModal.show();
		$scope.oncall = true;			
	}
	
								  
	$scope.firstOpen = true;							  

	
    $ionicModal.fromTemplateUrl('templates/'+mobileTheme+'/modals/chat-image.html', {
      scope: $scope,
      animation: 'slide-in-up'
    }).then(function(modal) {
      $scope.modalChatImage = modal;
    });

    $rootScope.openChatImageModal = function(image) {
	  $scope.chatImage = image;
      $scope.modalChatImage.show();
    };

    $rootScope.closeChatImageModal = function() {
      $scope.modalChatImage.hide();
    };	

    $ionicModal.fromTemplateUrl('templates/'+mobileTheme+'/modals/share.html', {
      scope: $rootScope,
      animation: 'slide-in-up'
    }).then(function(modal) {
      $rootScope.modalShare = modal;
    });

    $ionicModal.fromTemplateUrl('templates/'+mobileTheme+'/modals/verification.html', {
      scope: $rootScope,
      animation: 'slide-in-up'
    }).then(function(modal) {
      $rootScope.modalVerification = modal;
    });    

    $ionicModal.fromTemplateUrl('templates/'+mobileTheme+'/modals/sendCredits.html', {
      scope: $rootScope,
      animation: 'slide-in-up'
    }).then(function(modal) {
      $rootScope.modalSendCredits = modal;
    });    
    $rootScope.openSendCreditsModal = function() {
      $rootScope.modalSendCredits.show();
    };
    $rootScope.closeSendCreditsModal = function() {
      $rootScope.modalSendCredits.hide();
    };        

    $rootScope.openPrivateImage = function(img) {
      $rootScope.modalPrivateImage.show();
      site_prices = $localstorage.getObject('prices');
      console.log(site_prices);
      $rootScope.price = site_prices.private;
      $rootScope.img = img;
    };
    $rootScope.closePrivateImageModal = function() {
      $scope.modalPrivateImage.hide();
    };	    
    $ionicModal.fromTemplateUrl('templates/'+mobileTheme+'/modals/privateImage.html', {
      scope: $rootScope,
      animation: 'slide-in-up'
    }).then(function(modal) {
      $rootScope.modalPrivateImage = modal;
    });

    $rootScope.openWithdrwal = function() {
	$('#topPhoto').removeClass('sblack');
	$rootScope.pLoad = true;
	if(url == 'explore'){
		ticky = false;	
	} else {
		ticky = true;
	}
	config = $localstorage.getObject('config');									  
	alang = $localstorage.getObject('alang');
	lang = $localstorage.getObject('lang');
	site_prices = $localstorage.getObject('prices');
	$rootScope.alang = [];
	$rootScope.lang = [];
	$rootScope.site_name = config.name;
	angular.forEach(alang,function(entry) {						  
	  $rootScope.alang.push({
		id: entry,
		text: entry.text
	  });
	});
	angular.forEach(lang,function(entry) {				  
	  $rootScope.lang.push({
		id: entry,
		text: entry.text
	  });
	});	    	
    $rootScope.modalWithdrwal.show();
    user = $localstorage.getObject('user');



    $rootScope.credits = user.credits;
    $rootScope.photo = user.profile_photo;
    $rootScope.paypal = user.paypal;
    };
    $rootScope.closeWithdrwal = function() {
      $scope.modalWithdrwal.hide();
    };	    
    $ionicModal.fromTemplateUrl('templates/'+mobileTheme+'/modals/withdrwal.html', {
      scope: $rootScope,
      animation: 'slide-in-up'
    }).then(function(modal) {
      $rootScope.modalWithdrwal = modal;
    });    

    $rootScope.openShareModal = function() {
      $rootScope.modalShare.show();
    };
    $rootScope.openVerificationModal = function() {
		$rootScope.modalVerification.show();
		$rootScope.verificationImage = plugins['verification']['gesture'];								  
		alang = $localstorage.getObject('alang');
		lang = $localstorage.getObject('lang');
		$rootScope.alang = [];
		$rootScope.lang = [];
		angular.forEach(alang,function(entry) {						  
		  $rootScope.alang.push({
			id: entry,
			text: entry.text
		  });
		});
		angular.forEach(lang,function(entry) {				  
		  $rootScope.lang.push({
			id: entry,
			text: entry.text
		  });
		});
		$rootScope.uploadVerificationImage = function() {
			upType = 3;
			$('#uploadContent').click();		
		};

    };        

    $rootScope.openLanguagesModal = function(visitor='') {
      if(visitor == 'Visitor'){
      	updateVisitorLanguage = true;
      } else {
      	updateVisitorLanguage = false;
      }
      $rootScope.editLanguageModal.show();
    };
    $rootScope.closeLanguagesModal = function() {
      $rootScope.editLanguageModal.hide();
    }; 
    $rootScope.openAdModal = function() {
      $rootScope.adModal.show();
    };
    $rootScope.closeAdModal = function() {
      $rootScope.adModal.hide();
    };            

    $rootScope.closeShareModal = function() {
      $rootScope.modalShare.hide();
    };
    $rootScope.closeVerificationModal = function() {
      $rootScope.modalVerification.hide();
    };    	



    $ionicModal.fromTemplateUrl('templates/'+mobileTheme+'/modals/profile.html', {
      scope: $scope,
      animation: 'slide-in-up'
    }).then(function(modal) {
      $rootScope.modalProfileUser = modal;
    });

    $rootScope.openProfileModal = function(id,name,photo,age,city,status=0) {
	$('#topPhoto').removeClass('sblack');
	$rootScope.pLoad = true;
	if(url == 'explore'){
		ticky = false;	
	} else {
		ticky = true;
	}
	config = $localstorage.getObject('config');									  
	alang = $localstorage.getObject('alang');
	lang = $localstorage.getObject('lang');
	site_prices = $localstorage.getObject('prices');
	$rootScope.alang = [];
	$rootScope.lang = [];
	$rootScope.site_name = config.name;
	angular.forEach(alang,function(entry) {						  
	  $rootScope.alang.push({
		id: entry,
		text: entry.text
	  });
	});
	angular.forEach(lang,function(entry) {				  
	  $rootScope.lang.push({
		id: entry,
		text: entry.text
	  });
	});	

	if(config.plugins.meet.viewOnlyPremiumOnline == 'Yes' && user.premium == 0 && status == 1){
		awlert.neutral(lang[695].text, 3000);
		return false;
	} 

	$rootScope.profileModal.show();
	$rootScope.bio = '';	
	$rootScope.photo = photo;
	$rootScope.name = name;
	$rootScope.age = age;
	$rootScope.question = '';
	if(city == 'undefined'){
		city = '';
	}
	$rootScope.city = city;	
	$('#user-name').addClass('fadeIn');
	$('#user-country').addClass('fadeIn');
	$rootScope.myProfile = false;
	$rootScope.wtf = true;	
	$rootScope.photos = '';
	$rootScope.aImages = '';
	$rootScope.extendedd = false;
	$rootScope.status = false;
	$rootScope.shareImage = photo;
	$rootScope.shareId = id;

	$scope.hasStory = false;

	user = $localstorage.getObject('user');

    $rootScope.blockUser = function() {
      var hideSheet = $ionicActionSheet.show({
		titleText: alang[14].text,									 
        buttons: [
          { text: alang[17].text +' '+name }
        ],
        cancelText: alang[2].text,
        cancel: function() {
            // add cancel code..
          },
        buttonClicked: function(index) {
			if(index == 0){
			   var confirmPopup = $ionicPopup.confirm({
				 title: alang[17].text+' '+ name,
				 template: alang[18].text +' '+ name +'?'
			   });
			
			   confirmPopup.then(function(res) {
				 if(res) {
					var query = user.id+','+id;
					A.Query.get({action: 'block' ,query: query});
					$timeout(function(){
						$rootScope.closeProfileModal();
					},550);
				 } else {
				   
				 }
			   });
			 };	
          return true;
        }
      });
    }	

    $('#hasStory').hide();
	$('.profile').addClass('desenfocame'); 	
	var addvisit = user.id+','+id;
	if(user.id != id){
		A.Query.get({action: 'addVisit', query: addvisit});		
	} else {
	}
			
	var cuser = function () {
	  try {		  
		  $rootScope.ajaxRequest = A.Chat.get({action: 'cuser',uid1: id,uid2: user.id});
		  $rootScope.ajaxRequest.$promise.then(function(){
				$localstorage.setObject('cuser', $rootScope.ajaxRequest.user);
				current_user = $localstorage.getObject('cuser');
				$rootScope.country = current_user.country;
				$rootScope.interest = current_user.interest;
				$rootScope.photos = current_user.photos;
				$rootScope.videos = current_user.videos;
				$rootScope.aImages = current_user.photos;
				$rootScope.photo = current_user.profile_photo_big;
				$rootScope.pLoad = false;
				if(current_user.status == "y"){
					$rootScope.status = true;
				} else {
					$rootScope.status = false;	
				}

				if(current_user.story > 0){
					$('#hasStory').show();
				}
				
				console.log(current_user.question);
				$rootScope.question = current_user.question;
				$rootScope.id = current_user.id;	
				$rootScope.cu = current_user;
				$rootScope.unlocked = current_user.unlocked;
				console.log(current_user.unlocked);
				if(current_user.fake == 0){
					$rootScope.extended = current_user.extended;
				}
				if(current_user.photos.length > 1){
					$('#topPhoto').addClass('sblack');
					$ionicSlideBoxDelegate.update();
				}
				if(current_user.isFan == 0){
					if(ticky == false){
						$rootScope.wtf = true;		
					} else {
						$rootScope.wtf = false;
					}
				}
				if(user.id == current_user.id){
					$rootScope.myProfile = true;	
				}		
				$rootScope.bio = decodeHTMLEntities(current_user.bio);

				var check = Math.floor(Math.random()*(100-1+1)+1);
				var tempVisitUsersArr = new Array();
				var tempLikeUsersArr = new Array();
				if(plugins['fakeUsersInteractions']['enabled'] == 'Yes'){
					if(plugins['fakeUsersInteractions']['visitBackChance'] > check && current_user.status == "y" && current_user.fake == 1 && current_user.id != user.id){
						var time = Math.floor(Math.random()*(45000-5000+1)+5000);
						var randId = Math.random();
						var thisUserId = current_user.id,thisUserName = current_user.first_name,thisUserPhoto=current_user.profile_photo;
						tempVisitUsersArr[randId] = current_user.id; 
						$timeout(function(){
							var addvisit = tempVisitUsersArr[randId]+','+user.id;
							var randomNumber = Math.random();
							tempLikeUsersArr[randomNumber] = tempVisitUsersArr[randId]; 
							//interaction(thisUserId,thisUserName,thisUserPhoto,site_lang[657]['text'],'visit');
							A.Query.get({action: 'addVisit', query: addvisit});
							if(plugins['fakeUsersInteractions']['likeVisitorChance'] > check && current_user.status == "y" && current_user.fake == 1){
								var time2 = Math.floor(Math.random()*(15000-3000+1)+3000);
								$timeout(function(){
									A.Meet.get({action: 'game_like',uid1: tempLikeUsersArr[randomNumber], uid2: user.id, uid3: 1});		
								},time2);
							}										
						},time);
					}
				}	

		  },
		  function(){}
		  )		 
	  }
	  catch (err) {
		console.log("Error " + err);
	  }
	};
	cuser();
    }
    $rootScope.closeProfileModal = function() {
    	$rootScope.profileModal.hide();
		$ionicSlideBoxDelegate.slide(0);
		$ionicScrollDelegate.$getByHandle('modalContent').scrollTop(true);		
		
    };    
	 
	/* 
    $ionicModal.fromTemplateUrl('templates/modals/profile-photos.html', {
      scope: $scope,
      animation: 'slide-in-up'
    }).then(function(modal) {
      $scope.modal = modal;
    });

    $scope.openModal = function() {
      $ionicSlideBoxDelegate.slide(0);
      $scope.modal.show();
    };

    $scope.closeModal = function() {
      $scope.modal.hide();
    };
	*/
    // Cleanup the modal when we're done with it!
    $scope.$on('$destroy', function() {
      $scope.modal.remove();
    });
    // Execute action on hide modal
    $scope.$on('modal.hide', function() {
      // Execute action
    });
    // Execute action on remove modal
    $scope.$on('modal.removed', function() {
      // Execute action
    });
    $scope.$on('modal.shown', function() {
    });

    // Call this functions if you need to manually control the slides
    $scope.next = function() {
      $ionicSlideBoxDelegate.next();
    };
  
    $scope.previous = function() {
      $ionicSlideBoxDelegate.previous();
    };
  
  	$rootScope.goToSlide = function(index) {
      $scope.modal.show();

      $ionicSlideBoxDelegate.slide(index);
    }
  
    // Called each time the slide changes
    $scope.slideChanged = function(index) {
      $scope.slideIndex = index;
    };

	function onHardwareBackButton() {
		if($('.modal-backdrop.active').length){		
			$scope.profileModal.hide();
			return false;
		}else{
			window.history.back();
			return false;
		}
	}
	

	
	$ionicPlatform.onHardwareBackButton(onHardwareBackButton);
	
    $ionicModal.fromTemplateUrl('templates/'+mobileTheme+'/modals/profile.html', {
      scope: $rootScope,
      animation: 'slide-in-up'
    }).then(function(modal) {
      $rootScope.profileModal = modal;
    });
	
	
    $ionicModal.fromTemplateUrl('templates/'+mobileTheme+'/modals/premium.html', {
      scope: $scope,
      animation: 'slide-in-up'
    }).then(function(modal) {
      $scope.premiumModal = modal;
    });

    $scope.openPremiumModal = function() {
		config = $localstorage.getObject('config');
		lang = $localstorage.getObject('lang');

		alang = $localstorage.getObject('alang');
		site_prices = $localstorage.getObject('prices');
		account_premium = $localstorage.getObject('account_premium');
		$scope.pchat = account_premium.chat;
		$scope.dchatprice = site_prices.chat;
		$scope.alang = [];

		angular.forEach(alang,function(entry) {						  
		  $scope.alang.push({
			id: entry,
			text: entry.text
		  });
		});
		$scope.config_email = config.paypal;
		$scope.premium_days = p_quantity;
		$scope.currency = config.currency;
		$scope.cp = $localstorage.getObject('premium_package');		
		$scope.premiumModal.show();
		$scope.videocallEnabled = true;
		$scope.premiumSlide = [1,2,3,4,5,6];
		if(plugins['videocall']['enabled'] == 'No'){
			$scope.videocallEnabled = false;
			$scope.premiumSlide = [1,2,3,4,5];
		}
		$scope.buyPremium = function(c,p,i){

			p_quantity = c;
			p_price = p;
			$scope.premium_days = c;
			$scope.premium_price = p;
			$scope.premium_custom = user.id+','+c;			
			$scope.premiumModal.hide();
			var paypalU = site_url +'app/paypal.php?type=2&amount='+p_price+'&custom='+$scope.premium_custom+'&days='+$scope.premium_days;
			if (window.cordova) {
				cordova.InAppBrowser.open(config.site_url+'pay/index.php?package='+i+'&type=premium', '_blank', 'location=yes');
			} else {
				window.location.href = config.site_url+'pay/index.php?package='+i+'&type=premium&fromMobile=true';
			}
		}
	}

    $scope.closePremiumModal = function() {
		$scope.premiumModal.hide();
	}	

    $ionicModal.fromTemplateUrl('templates/'+mobileTheme+'/modals/credits.html', {
      scope: $scope,
      animation: 'slide-in-up'
    }).then(function(modal) {
      $scope.creditsModal = modal;
    });

    $scope.openCreditsModal = function(photo) {
		config = $localstorage.getObject('config');
		lang = $localstorage.getObject('lang');

		alang = $localstorage.getObject('alang');
		site_prices = $localstorage.getObject('prices');
		account_basic = $localstorage.getObject('account_basic');
		$scope.site_name = config.name;
		$scope.dchat = account_basic.chat;
		$scope.dchatprice = site_prices.chat;
		$scope.alang = [];

		angular.forEach(alang,function(entry) {						  
		  $scope.alang.push({
			id: entry,
			text: entry.text
		  });
		});

		
		if(config.paypal != '' ){ 
			$scope.PAYPAL = true;
		}
		if(config.stripe != '' ){ 
			$scope.STRIPE = true;
		}
		if(config.fortumo != '' ){ 
			$scope.SMS = true;
		}		
		$scope.photo = photo;
		$scope.config_email = config.paypal;
		$scope.credits_amount = c_quantity;
		$scope.currency = config.currency;
		$scope.cp = $localstorage.getObject('credits_package');	
		console.log($scope.cp);	
		$scope.creditsModal.show();
		$scope.buyCredit = function(val){
			if(c_quantity == 0){
				alert(lang[79].text);
				return false;
			}
			if(val == 1){
				var c = $scope.credits_custom;
				var paypalU = site_url +'app/paypal.php?type=1&amount='+c_price+'&custom='+c;
					if (window.cordova) {
					cordova.InAppBrowser.open(paypalU, '_blank', 'location=yes');
				} else {
					window.open(paypalU, '_blank', 'location=yes');
				}
			}
			if(val == 2){
				$scope.creditsModal.hide();
				var price = c_price*100;
				var app = 1;
				var handler = StripeCheckout.configure({
					key: config.stripe,
					image: config.logo,
					locale: 'auto',
					token: function(token) {
						$.ajax({
							url: config.ajax_path+'/stripe.php', 
							data: {
								token:token.id,
								price: price,
								app: app,
								quantity: c_quantity,
								uid: user.id,
								de: config.name + ' ' + c_quantity + ' credits'
							},	
							type: "post",
							success: function(response) {
							},
							complete: function(){
								if(app == 1){
									$state.go('loader');
								}
							}
						});
					}
				});
				handler.open({
					name: config.name,
					description: config.name + ' ' + c_quantity + ' credits',
					amount: price
				});
			
				$(window).on('popstate', function() {
					handler.close();
				});				
			}
			if(val == 3){
				var name = config.name + ' ' + c_quantity + ' credits';
				var encode = 'amount='+c_quantity+'callback_url='+config.site_url+'credit_name='+name+'cuid='+user.id+'currency='+config.currency+'display_type=userprice='+c_price+'v=web';			
				$.ajax({ 
					type: "POST", 
					url: config.ajax_path + "/user.php",
					data: {
						action: 'fortumo',
						encode: encode
					},
					success: function(response){
						var md5 = response;
						var callback = encodeURI(config.site_url);
						name = encodeURI(name);
						var href= 'http://pay.fortumo.com/mobile_payments/'+config.fortumo+'?amount='+c_quantity+'&callback_url='+callback+'&credit_name='+name+'&cuid='+user.id+'&currency='+config.currency+'&display_type=user&price='+c_price+'&v=web&sig='+md5;
						if (window.cordova) {
							cordova.InAppBrowser.open(href, '_blank', 'location=yes');
						} else {
							window.open(href, '_blank', 'location=yes');
						}			
					}
				});				
			}	
		}
		$scope.selectCredit = function(q,p,i){
			if (window.cordova) {
				cordova.InAppBrowser.open(config.site_url+'pay/index.php?package='+i+'&type=credits', '_blank', 'location=yes');
			} else {
				window.location.href = config.site_url+'pay/index.php?package='+i+'&type=credits&fromMobile=true';
			}
		}		
	}
    $scope.closeCreditsModal = function() {
		$scope.creditsModal.hide();
	}


    $rootScope.blockUser = function() {
      var hideSheet = $ionicActionSheet.show({
		titleText: alang[14].text,									 
        buttons: [
          { text: alang[17].text +' '+name }
        ],
        cancelText: alang[2].text,
        cancel: function() {
            // add cancel code..
          },
        buttonClicked: function(index) {
			if(index == 0){
			   var confirmPopup = $ionicPopup.confirm({
				 title: alang[17].text+' '+ name,
				 template: alang[18].text +' '+ name +'?'
			   });
			
			   confirmPopup.then(function(res) {
				 if(res) {
					var query = user.id+','+id;
					A.Query.get({action: 'block' ,query: query});
					$timeout(function(){
						$rootScope.closeProfileModal();
					},550);
				 } else {
				   
				 }
			   });
			 };	
          return true;
        }
      });
    }


 		
  })

	.controller('menuCtrl',function($scope,$rootScope,$state,$ionicViewSwitcher, $cordovaDevice,A,$localstorage,$ionicLoading) {
		if(window.cordova){
			$ionicViewSwitcher.nextDirection("bddk");
		} else {
			$ionicViewSwitcher.nextDirection("back");
		}
		url = 'menu';
		$('.navigation-bar').hide();
		user = $localstorage.getObject('user');
		config = $localstorage.getObject('config');	 
		lang = $localstorage.getObject('lang');
		alang = $localstorage.getObject('alang');
		app = $localstorage.getObject('app');
		$('#ready').removeClass('hidden');
		$rootScope.logged = true;
		$rootScope.me = user;	
		$scope.credits = user.credits;
		if(user.premium == 1){
			$scope.premium = 'Activated';
		} else {
			$scope.premium	= 'No'
		}

		$scope.alang = [];
		angular.forEach(alang,function(entry) {						  
		  $scope.alang.push({
			id: entry,
			text: entry.text
		  });
		});	

		app = $localstorage.getObject('app');
		$scope.logo = app.logo;

	})  
	.controller('LoaderCtrl',function($scope,$rootScope,$state,$ionicViewSwitcher, $cordovaDevice,A,$localstorage,$ionicLoading) {   
		 //mobileUser = $localstorage.get('userHistory');
		 $ionicViewSwitcher.nextDirection("exit");
		if (window.cordova) {
			document.addEventListener('deviceready', function () {
				var notificationOpenedCallback = function(jsonData) { 
				};
				window.plugins.OneSignal
					.startInit("92b5304a-f8bb-4c97-ac22-f4ba773cf573")
					.handleNotificationOpened(notificationOpenedCallback)
					.endInit();
				window.plugins.OneSignal.getIds(function(ids) {
					oneSignalID = ids.userId;	
					$rootScope.loader();  
				});	
			}, false);
		} else {

			if(mobileUser == null){
				//oneSignalID = Math.floor((Math.random() * 9999999) + 1);
				//$localstorage.set('userHistory', oneSignalID);			
			} else {
				//oneSignalID = mobileUser;
			}
			
			/*
			var getUserData = $localstorage.getObject('user');
			console.log(getUserData);
			if(getUserData.id != undefined){
				oneSignalID = getUserData.id;
				$('#ready').removeClass('hidden');
				$rootScope.loader();				
			} else {
				$('#ready').removeClass('hidden');
				$rootScope.loader();				
			} */
			$('#ready').removeClass('hidden');
			$rootScope.loader();
		}
	})
  

  .controller('WelcomeCtrl', function($scope,$rootScope, $state,$ionicViewSwitcher,$http,awlert, $ionicLoading,$ionicActionSheet, $timeout,A,$cordovaOauth,$localstorage,Navigation) {
	if(config == ''){
		$state.go('loader');
	}
	config = $localstorage.getObject('config');
	if(window.cordova){
		$ionicViewSwitcher.nextDirection("forward");
	} else {
		$ionicViewSwitcher.nextDirection("back");
	}

	lang = $localstorage.getObject('lang');
	alang = $localstorage.getObject('alang');
	$('#ready').removeClass('hidden');	
	url = 'welcome';


	$scope.showChangeLanguage = false;
	if(config.languages.length > 1){
		$scope.showChangeLanguage = true;
	}

	$('[data-alid]').each(function(){
	  var id = $(this).attr('data-alid');
	  $(this).text(alang[id].text);
	});
	$scope.openPrivacy = function(){
		if (window.cordova) {
			cordova.InAppBrowser.open(site_url+'index.php?page=pp', '_blank', 'location=yes');
		} else {
			window.open(site_url+'index.php?page=pp', '_blank', 'location=yes');
		}		
	}
	$scope.openTerms = function(){
		if (window.cordova) {
			cordova.InAppBrowser.open(site_url+'index.php?page=tac', '_blank', 'location=yes');
		} else {
			window.open(site_url+'index.php?page=tac', '_blank', 'location=yes');
		};			
	}
	$scope.site_url = site_url;
	$scope.alang = [];
	angular.forEach(alang,function(entry) {				  
	  $scope.alang.push({
		id: entry,
		text: entry.text
	  });
	});	

	$rootScope.selectLanguage = function(id){
		console.log(updateVisitorLanguage);

		if(updateVisitorLanguage){
			window.location.href = siteUrl+'mobile/index.php?lang='+id;
		} else {
			$scope.loading = true;
			var message = user.id+','+id;
			$scope.ajaxRequest34 = A.Query.get({action: 'updateUserLanguage', query: message});
			$scope.ajaxRequest34.$promise.then(function(){											
				$rootScope.closeLanguagesModal();
				$scope.closeEditProfileModal();
				siteLang = id;
				$state.go('loader');
			});	
		}		
	}

	var regButtons = [];
	$scope.fbEnabled = false;
	if(plugins['facebook']['enabled'] == 'Yes'){
		$scope.fbEnabled = true;
		regButtons=[
			{'text': alang[246].text},
			{'text': alang[159].text}
		];
	} else {
		regButtons=[
			{'text': alang[246].text}
		];	
	}
	$scope.register = function() {
	  var hideSheet = $ionicActionSheet.show({
		titleText: alang[25].text,									 
	    buttons: regButtons,
	    cancelText: alang[2].text,
	    cancel: function() {
	        // add cancel code..
	      },
	    buttonClicked: function(index) {
			if(index == 0){
				$state.go('home.register');
			 };	
			if(index == 1){
				$scope.fb();
			 };				 
	      return true;
	    }
	  });
	}	
	app = $localstorage.getObject('app');
	$scope.slideTitleLang1 = alang[240].text;
	$scope.slideLang1 = alang[241].text;
	$scope.slideTitleLang2 = alang[242].text;
	$scope.slideLang2 = alang[243].text;
	$scope.slideTitleLang3 = alang[244].text;
	$scope.slideLang3 = alang[245].text;		
	$scope.logo = config.logo;

	var val = 0;
	$scope.forgetBtn = false;
	$scope.recoverPass = function(){
		$scope.forgetBtn = true;
		$scope.loginBtn = true;
	}

	$scope.backLogin = function(){
		$scope.forgetBtn = false;
		$scope.loginBtn = false;
	}
	$scope.isActive = true;
	$scope.keyup = function(key){
		val = key;
		if(val > 3){
			$scope.isActive = false;
		} else {
			$scope.isActive = true;
		}
    }

	$scope.loginBtn = false;

	$scope.send = function(user) {
		if(val < 4){
			return false;
		}		
		$scope.master = angular.copy(user);
		$scope.loginBtn = true;
		var dID = -1;
		$scope.ajaxRequest = A.User.get({action : 'login',login_email: $scope.master.login_email, login_pass:$scope.master.login_pass , dID : dID });
		$scope.ajaxRequest.$promise.then(function(){						
			if($scope.ajaxRequest.error == 1){
				awlert.neutral($scope.ajaxRequest.error_m, 3000);
				$scope.loginBtn = false;
				$scope.isActive = true;		
			} else {		
				console.log($scope.ajaxRequest.user);
				$localstorage.setObject('user', $scope.ajaxRequest.user);
				$localstorage.setObject('usPhotos', $scope.ajaxRequest.user.photos);
				usPhotos = $scope.ajaxRequest.user.photos;
				oneSignalID = $scope.ajaxRequest.user.id;
				userId = $scope.ajaxRequest.user.id;
				sape = $scope.ajaxRequest.user.slike;
					var rtnotification = 'notification'+$scope.ajaxRequest.user.id;
					channel.bind(rtnotification, function(data) {
						if(data.id != current_user_id ){
							$rootScope.unreadMessage = true;
							if(!$('.chatNotification').hasClass('is-visible')){
								$rootScope.playAudio('Notification.wav');		
								$('.chatNotification').removeClass('is-visible');
								$('.chatNotificationPhoto').removeClass('sblur');	
								$('.chatNotificationPhoto').css('background-image', 'url('+ data.icon +')');
								$('.chatNotificationContent').text(data.message);
								$timeout(function(){
									if(!$('.chatNotification').hasClass('is-visible')){
										$('.chatNotification').addClass('is-visible');
									}
								},100);				
								$timeout(function(){
									if($('.chatNotification').hasClass('is-visible')){
										$('.chatNotification').removeClass('is-visible');
									}
								},3000);					
							}
						}
					});
					var rtvisit = 'visit'+$scope.ajaxRequest.user.id;
					channel.bind(rtvisit, function(data) {	
						if(!$('.chatNotification').hasClass('is-visible')){	
							$rootScope.playAudio('Notification.wav');
							$('.chatNotification').removeClass('is-visible');
							$('.chatNotificationPhoto').removeClass('sblur');	
							$('.chatNotificationPhoto').css('background-image', 'url('+ data.icon +')');
							$('.chatNotificationContent').text(data.message);
							$timeout(function(){
								if(!$('.chatNotification').hasClass('is-visible')){
									$('.chatNotification').addClass('is-visible');
								}
							},100);				
							$timeout(function(){
								if($('.chatNotification').hasClass('is-visible')){
									$('.chatNotification').removeClass('is-visible');
								}
							},3000);
						}					
					});	
					var rtlike = 'like'+$scope.ajaxRequest.user.id;
					channel.bind(rtlike, function(data) {
						if(!$('.chatNotification').hasClass('is-visible')){		
							$('.chatNotification').removeClass('is-visible');
							$rootScope.playAudio('Notification.wav');
							if(user.premium == 1){
								$('.chatNotificationPhoto').removeClass('sblur');	
							} else {
								$('.chatNotificationPhoto').addClass('sblur');
							}							
							$('.chatNotificationPhoto').css('background-image', 'url('+ data.icon +')');
							$('.chatNotificationContent').text(data.message);
							$timeout(function(){
								if(!$('.chatNotification').hasClass('is-visible')){
									$('.chatNotification').addClass('is-visible');
								}
							},100);				
							$timeout(function(){
								if($('.chatNotification').hasClass('is-visible')){
									$('.chatNotification').removeClass('is-visible');
								}
							},3000);						
						}
					});					
				if(window.cordova){
					$state.go('home.explore'); 	
				} else {
					$state.go('home.explore'); 		
				} 				
				
				$localstorage.set('mobileUser',$scope.ajaxRequest.user.app_id);
			}
		},
		function(){
			awlert.neutral('Something went wrong. Please try again later',3000);
		}
	)};
		
	$scope.forget = function(user) {	
		$scope.master = angular.copy(user);
		$scope.ajaxRequest = A.Query.get({action : 'recover',query: $scope.master.login_email });
		$scope.ajaxRequest.$promise.then(function(){						
			if($scope.ajaxRequest.error == 1){
				awlert.neutral($scope.ajaxRequest.error_m, 3000);		
			} else {		
				awlert.neutral(lang[341].text);
			}
		},
		function(){
			awlert.neutral('Something went wrong. Please try again later',3000);
		}
	)};

	if(plugins['facebook']['enabled'] == 'Yes'){
	    FB.init({
	      appId: plugins['facebook']['id'],
	      status: true,
	      cookie: true,
	      xfbml: true,
	      version: 'v2.2'
	    });	
	}
	$scope.fb = function() {
		if (window.cordova) {
			 $cordovaOauth.facebook('844622042382060', ["email"]).then(function(result) {
				$http.get("https://graph.facebook.com/v2.2/me", { params: { access_token: result.access_token, fields: "id,name,email,gender", format: "json" }}).then(function(result) {
					var dID = oneSignalID;
					var query = result.data.id+','+result.data.email+','+result.data.name+','+result.data.gender+','+dID;
				$scope.ajaxRequest = A.Query.get({action : 'fbconnect',query: query });
				$scope.ajaxRequest.$promise.then(function(){							
					$localstorage.setObject('user', $scope.ajaxRequest.user);
					usPhotos = $scope.ajaxRequest.user.photos;
					$state.go('home.explore');	
				},
				function(){
					awlert.neutral('Something went wrong. Please try again later',3000);
				});
				
				}, function(error) {
				alert("There was a problem getting your profile.  Check the logs for details.");
					console.log(error);
				});
			 }, function(error) {
				 alert("Auth Failed..!!"+error);
			 });	
			} else {
				FB.getLoginStatus(function(response) {
				    if (response.status === 'connected') {
			            FB.api('/me', {
			              	 fields: 'id,name,email,gender'
			            }, function(response) {
							var dID = oneSignalID;
							var query = response.id+','+response.email+','+response.name+','+response.gender+','+dID;
							$scope.ajaxRequest = A.Query.get({action : 'fbconnect',query: query });
							$scope.ajaxRequest.$promise.then(function(){							
								$localstorage.setObject('user', $scope.ajaxRequest.user);
								usPhotos = $scope.ajaxRequest.user.photos;
								$state.go('home.explore');	
							},
							function(){
								awlert.neutral('Something went wrong. Please try again later',3000);
							});		
						});
				    } else {
						FB.login(function(response){
							if(response.authResponse){
					            FB.api('/me', {
					                fields: 'id,name,email,gender'
					            }, function(response) {
									var dID = oneSignalID;
									var query = response.id+','+response.email+','+response.name+','+response.gender+','+dID;
									$scope.ajaxRequest = A.Query.get({action : 'fbconnect',query: query });
									$scope.ajaxRequest.$promise.then(function(){							
										$localstorage.setObject('user', $scope.ajaxRequest.user);
										usPhotos = $scope.ajaxRequest.user.photos;
										$state.go('home.explore');	
									},
									function(){
										awlert.neutral('Something went wrong. Please try again later',3000);
									});		
								});
							}
						})	
				    } 
				});				
			}		 
		};	
	//$scope.site_name = lang[0].text;

  })
  .controller('MeetCtrl', function($scope,$rootScope,$sce,$ionicPlatform,$ionicScrollDelegate,$ionicViewSwitcher, $state,$ionicModal, $ionicLoading,A, $timeout,$localstorage,Navigation,$window,preloader) {
	//$sce.trustAsResourceUrl(url);
	var cc = 0;
	current_user_id = 0;
	url = 'meet';
	goBackGlobal = 'meet';

	
	$ionicViewSwitcher.nextDirection("forward");
	
	if(config == '' || user == ''){
		$state.go('loader');
	}

	$('#ready').removeClass('hidden');	
	config = $localstorage.getObject('config');								  
	lang = $localstorage.getObject('lang');

	alang = $localstorage.getObject('alang');
	site_prices = $localstorage.getObject('prices');
	$scope.alang = [];
	$scope.lang = [];
	user = $localstorage.getObject('user');
	prices = $localstorage.getObject('prices');
	$rootScope.logged = true;
	$rootScope.me = user;

	$scope.noResultImage = site_theme['meet_no_result']['val'];
	var viewScroll = $ionicScrollDelegate.$getByHandle('meetScroll');

	if(rt == ''){
		rt = new Pusher(plugins['pusher']['key'], {
		  encrypted: true,
		  cluster: plugins['pusher']['cluster']

		});
		channel = rt.subscribe(plugins['pusher']['key']);		
	}

	if(window.cordova){
		$ionicViewSwitcher.nextDirection("forward");
	} else {
		$ionicViewSwitcher.nextDirection("back");
	}

	angular.forEach(alang,function(entry) {						  
	  $scope.alang.push({
		id: entry,
		text: entry.text
	  });
	});

	angular.forEach(lang,function(entry) {						  
	  $scope.lang.push({
		id: entry,
		text: entry.text
	  });
	});	

	$scope.meetAll = true;
	$scope.meetSpotlight = false;
	$scope.meetPopular = false;
	$scope.tab1 = 'is-active';
   	$scope.onTabShow = function(val,title){
		$scope.tab1 = '';   		
		$scope.tab2 = '';
		$scope.tab3 = '';   		
		if(val == 1){
		 $scope.meetAll = true;
		 $scope.meetSpotlight = false;
		 $scope.meetPopular = false;	
		 $scope.tab1 = 'is-active';
		 $scope.$broadcast('scroll.infiniteScrollComplete');
		}
		if(val == 2){
		 $scope.meetAll = false;
		 $scope.meetSpotlight = true;
		 $scope.meetPopular = false;	
		 $scope.tab2 = 'is-active';
		 $scope.$broadcast('scroll.infiniteScrollComplete');
		}
		if(val == 3){
		 $scope.meetAll = false;
		 $scope.meetSpotlight = false;
		 $scope.meetPopular = true;	
		 $scope.tab3 = 'is-active';
		 $scope.$broadcast('scroll.infiniteScrollComplete');
		}								
	    viewScroll.scrollTop(true);
	}

	if(user.s_radius >= 1000){
		$scope.check = 'All the world'	
	}
	if(user.s_radius < 550 && user.s_radius >= 500 ){
		$scope.check = user.city;	
	}
	if(user.s_radius < 550 && user.s_radius >= 500 ){
		$scope.check = user.country;	
	}
	if(user.s_radius < 50 ){
		$scope.check = user.city;	
	}
	if(user.s_radius > 30 && user.s_radius < 500 || user.s_radius > 550 && user.s_radius < 1000){
		$scope.check = user.s_radius+' KM';	
	}

    var n = new Date().getTime() / 1000;
    console.log(user.last_access + ' - ' + n);
    if(user.last_access > n && user.meet == 1){
    	$scope.showRiseUp = false;
    } else {
    	$scope.showRiseUp = true;
    }

	$scope.photo = user.profile_photo;

	$scope.showMeet = function(action){
		if(action == 'all'){
			$scope.meet = [];
			$scope.populars = [];
			meet();
			$('#showAll').css('color','#7000e3');
			$('#showPopular').css('color','#222');			
		} else {
			$('#showAll').css('color','#222');
			$('#showPopular').css('color','#7000e3');

			if($scope.popularsFound == true && $scope.noResult == true){
				$scope.noResult = false;
			}
			$scope.meet = $scope.populars;
		}
	}

	$scope.goToChat = function(){
		$ionicViewSwitcher.nextDirection('back'); // 'forward', 'back', etc.
		$state.go('home.matches');		
	}
	
	$('[data-lid]').each(function(){
	  var id = $(this).attr('data-lid');
	  $(this).text(lang[id].text);
	});
	
	
	var result = [];
	var populars = [];
	var loadMore = [];
	$scope.imageLocations = [];
	$scope.loading = true;
	$scope.loadingHeader = true;
    $scope.meet = [];
    $scope.noResult = false;
    $scope.populars = [];
    $scope.popularsFound = false;
  
	var meet = function () {
		meet_limit = 0;
		try {		  
		  $scope.ajaxRequest = A.Meet.get({action: 'meet',uid1: user.id, uid2: meet_limit, uid3 : onlineMeet});
		  $scope.ajaxRequest.$promise.then(function(){											
				result = $scope.ajaxRequest.result;
				populars = $scope.ajaxRequest.popular;
				var i = 0;
				if(result){
					result.forEach(function(entry) {
						i++;
						entry.show = i;		
						$scope.meet.push(entry);	
						$scope.imageLocations.push(entry.photo);
					});						
					cc++;
					$scope.loading = false;
					$scope.loadingHeader = false;
					preloader.preloadImages( $scope.imageLocations )
					.then(function() {
						
					},
					function() {
						// Loading failed on at least one image.
					});					
				} else {
					$scope.noResult = true;
					$scope.loading = false;
					$scope.loadingHeader = false;
				}

				if(populars){
					$scope.popularsFound = true;
					populars.forEach(function(entry) {	
						$scope.populars.push(entry);	
						$scope.imageLocations.push(entry.photo);
					});						
					preloader.preloadImages( $scope.imageLocations )
					.then(function() {
						
					},
					function() {
						// Loading failed on at least one image.
					});					
				}

		  },
		  function(){}
		  )		 
		}
		catch (err) {
			console.log("Error " + err);
		}	
	}
	
	var loadMore = function () {
		meet_limit = meet_limit+1;
		$scope.imageLocations = [];
		$scope.loadingHeader = true;
		try {		  
		  $scope.ajaxRequest = A.Meet.get({action: 'meet',uid1: user.id, uid2: meet_limit, uid3 : onlineMeet});
		  $scope.ajaxRequest.$promise.then(function(){											
				result = $scope.ajaxRequest.result;
				$scope.loadMores = $scope.ajaxRequest.result;
				var i = 0;
				result.forEach(function(entry) {
					i++;
					entry.show = i;					  
					$scope.meet.push(entry);
					$scope.imageLocations.push(entry.photo);
				});
				$scope.loadingHeader = false;
				$scope.$broadcast('scroll.infiniteScrollComplete');
				preloader.preloadImages( $scope.imageLocations )
				.then(function() {
					show = meet_limit * 9;
					var maxShow = show + 10;
					var show_search = setInterval(function(){
						show++;	
						if(show == maxShow){
							clearInterval(show_search);	
							$scope.$broadcast('scroll.infiniteScrollComplete');
						}
					},150);
				},
				function() {
					// Loading failed on at least one image.
				});				
		  },
		  function(){}
		  )		 
		}
		catch (err) {
			console.log("Error " + err);
		}	
	}

	meet();	
	
	$scope.spot_price = prices.spotlight;
	$scope.openSpot = function(){
		$scope.showSpot = true;
	}
	$scope.cancelSpot = function(){
		$scope.showSpot = false;	
	}
	$scope.addToSpotBtn = function(){
		user.credits = parseInt(user.credits);
		if(user.credits < site_prices.spotlight){
			$scope.openCreditsModal("'"+user.profile_photo+"'");
		} else {
			$scope.showMe = false;
			addToSpotlight();
		}
	}

	var addToSpotlight = function () {
		try {	
		  $scope.ajaxRequest2 = A.Query.get({action: 'addToSpotlight', query: user.id});
		  $scope.ajaxRequest2.$promise.then(function(){	
		  	$rootScope.spotlight = [];
			spot();
            var data = [];
            data.name = '';
            data.icon = site_theme['notification_inapp_credits_icon']['val'];
            data.message = lang[610].text+' '+site_prices.spotlight+' ' + lang[73].text;
			$rootScope.pushNotif(data,1);

		  },
		  function(){}
		  )		 
		}
		catch (err) {
			console.log("Error " + err);
		}	
	}

	$rootScope.spotlight = [];
	//SPOTLIGHT
	var spot = function () {
		try {		  
		  $scope.ajaxRequest5 = A.Game.get({action: 'spotlight', id: user.id});
		  $scope.ajaxRequest5.$promise.then(function(){											
				spotlight = $scope.ajaxRequest5.spotlight;
				console.log(spotlight);
				
				$rootScope.spotlight = spotlight;
				
		  },
		  function(){}
		  )		 
		}
		catch (err) {
			console.log("Error " + err);
		}	
	}
	if(spotlight == ''){
		spot();
	}	else {
		$rootScope.spotlight = spotlight;	
		spot();
	}



	//ADMOB
	/*
	if(show_ad == max_ad && user.premium == 0){
		if(window.AdMob) window.AdMob.prepareInterstitial( {adId:adMobI, autoShow:true} );
		show_ad = 0;	
	}
	show_ad++;	
	*/
 
  $scope.loadMore = function() {
	if($scope.tab1 != ''){
		loadMore();
	}
  };
 


  })  
  
  .controller('LoginCtrl', function($scope,$ionicPlatform,$http, $state,$ionicViewSwitcher,$ionicModal,A,awlert,$cordovaOauth,Navigation) {
	var app = $localstorage.getObject('app');
	var val = 0;
	$('#ready').removeClass('hidden');	

	if(window.cordova){
		$ionicViewSwitcher.nextDirection("forward");
	} else {
		$ionicViewSwitcher.nextDirection("back");
	}

	lang = $localstorage.getObject('lang');
	alang = $localstorage.getObject('alang');

	$('[data-alid]').each(function(){
	  var id = $(this).attr('data-alid');
	  $(this).text(alang[id].text);
	});
	$('[data-lid]').each(function(){
	  var id = $(this).attr('data-lid');
	  $(this).text(lang[id].text);
	});

	$scope.alang = [];
	$scope.lang = [];
	angular.forEach(alang,function(entry) {						  
	  $scope.alang.push({
		id: entry,
		text: entry.text
	  });
	});
	angular.forEach(lang,function(entry) {						  
	  $scope.lang.push({
		id: entry,
		text: entry.text
	  });
	});
  })  

  .controller('RegisterCtrl', function($scope,$rootScope, $state,$ionicViewSwitcher,$ionicModal,A,awlert, $ionicLoading, $timeout,$localstorage,$cordovaCamera, $cordovaFile, $cordovaFileTransfer, $cordovaDevice) {
	var reg = '';								   
	var app = $localstorage.getObject('app'); 
	var w;
	if(window.cordova){
		$ionicViewSwitcher.nextDirection("forward");
	} else {
		$ionicViewSwitcher.nextDirection("back");
	}	
	if(config == ''){
		$state.go('loader');
	}
	config = $localstorage.getObject('config');	
	lang = $localstorage.getObject('lang');
	alang = $localstorage.getObject('alang');
	$('#ready').removeClass('hidden');	
	$('[data-alid]').each(function(){
	  var id = $(this).attr('data-alid');
	  $(this).text(alang[id].text);
	});
	$('[data-lid]').each(function(){
	  var id = $(this).attr('data-lid');
	  $(this).text(lang[id].text);
	});

	$scope.alang = [];
	$scope.lang = [];
	angular.forEach(alang,function(entry) {						  
	  $scope.alang.push({
		id: entry,
		text: entry.text
	  });
	});
	
	angular.forEach(lang,function(entry) {						  
	  $scope.lang.push({
		id: entry,
		text: entry.text
	  });
	});	

	var options = {
	  enableHighAccuracy: true,
	  timeout: 5000,
	  maximumAge: 1000
	};

	var geoAllowed = false;
	function geoError(err) {
	  console.warn('ERROR(' + err.code + '): ' + err.message);
	  if(err.code == 1){
		awlert.neutral(lang[818].text,2000);		
		return false;	  	
	  }
	};

	if(plugins['ipstack']['enabled'] == 'Yes' || !navigator.geolocation){
		geoAllowed = true;
		console.log('no geolocation - ip location');
		var access_key = plugins['ipstack']['key'];
		console.log(userIp);
		$timeout(function(){
			$.ajax({
			    url: 'http://api.ipstack.com/' + userIp + '?access_key=' + access_key,   
			    dataType: 'jsonp',
			    success: function(json) {
			       reg_city = json.city;
			       reg_country = json.country_name;
			       reg_lat = json.latitude;
			       reg_lng = json.longitude;
			       console.log(json);
			    }
			});	
		},1500);	
	} else {
		$timeout(function(){
			navigator.geolocation.watchPosition(logPosition,geoError,options);
		},1200);
	}

	var posCollected = false;
	function logPosition(position) {
		if(!posCollected){
			posCollected = true;
			geoAllowed = true;
			reg_lat = position.coords.longitude;
			reg_lng = position.coords.latitude;	

			var apikey = '5735f128554f4f4db408b1e4c7f861cc';
			var latitude = reg_lat;
			var longitude = reg_lng;

			var api_url = 'https://api.opencagedata.com/geocode/v1/json'

			var request_url = api_url
			+ '?'
			+ 'key=' + apikey
			+ '&q=' + encodeURIComponent(latitude + ',' + longitude)
			+ '&pretty=1'
			+ '&no_annotations=1';

			var request = new XMLHttpRequest();
			request.open('GET', request_url, true);
			request.onload = function() {
			if (request.status == 200){ 
			  var data = JSON.parse(request.responseText);
			  reg_city = data.results[0].components['city'];
			  reg_country= data.results[0].components['country'];

			  if(reg_city === undefined){
			  	reg_city = 'Los Angeles';
			  }

			  if(reg_country === undefined){
			  	reg_country = 'USA';
			  }

			} else if (request.status <= 500){               
			  console.log("unable to geocode! Response code: " + request.status);
			  var data = JSON.parse(request.responseText);
			  console.log(data.status.message);
			} else {
			  console.log("server error");
			}
			};
			request.onerror = function() {
				console.log("unable to connect to server");        
			};
			request.send();  // make the request
		}			
	}

	$scope.config = config;

	var cuteUrl = config["site_url"].replace("http://www.", "");
	cuteUrl = cuteUrl.replace("https://www.", "");
	cuteUrl = cuteUrl.replace("https://", "");
	cuteUrl = cuteUrl.replace("http://", "");		
	$scope.profileUrl = cuteUrl;

	$scope.profileUsername = '';

    $scope.checkUsername = function(value){
        $scope.profileUsername = value;
        console.log(value);
    }	
	$('#regusername').keyup(function(){
		$scope.profileUsername = $(this).val();
	});

	$scope.lname = lang[26].text;
	$scope.lemail = lang[28].text;
	$scope.lpass = lang[29].text;
	$scope.nexttext = alang[26].text;
	$scope.regPhoto = '';
	alang = $localstorage.getObject('alang');
	lang = $localstorage.getObject('lang');

	var div = angular.element(document.getElementById('photo-upload'));
	w = angular.element(document.getElementById('photo-upload')).prop('offsetWidth'); 
	div.css('height',w+'px');
	window.addEventListener('native.keyboardshow', keyboardHandler);
	window.addEventListener('native.keyboardhide', keyboardHandler);
	function keyboardHandler(e){
		var div = angular.element(document.getElementById('photo-upload')); 
		w = angular.element(document.getElementById('photo-upload')).prop('offsetWidth'); 
		div.css('height',w+'px');
	}
	
	var val = 0;
	$scope.isActive = false;
	$('#regpass').keyup(function(){
		val = $('#regpass').val().length;
		if(val > 4){
			$scope.isActive = true;
		} else {
			$scope.isActive = false;
		}
    });	
	$scope.regBtn = false;
	var regPhoto = '';
	var con = false;

	$scope.validUsername = 'Yes';
	$scope.validEmail = 'Yes';
	$scope.next = function(user) {
		if(val < 4){
			return false;
		}

		if(con == false && plugins['settings']['forcePhotoUpload'] == 'Yes'){
			awlert.neutral(alang[3].text,2000);
			return false;
		}		
		if(user.reg_username == '' || user.reg_username == undefined){
			awlert.neutral(alang[4].text,2000);		
			return false;
		}
		if(user.reg_name == '' || user.reg_name == undefined){
			awlert.neutral(alang[4].text,2000);		
			return false;
		}		
		if(user.reg_email == ''){
			awlert.neutral(alang[4].text,2000);
			return false;
		}

		if(!geoAllowed){
			awlert.neutral(lang[818].text,2000);
			navigator.geolocation.watchPosition(logPosition,geoError,options);		
			return false;				
		}
	
		if (!validateEmail(user.reg_email)) {		
			awlert.neutral(alang[5].text,2000);
			return false;		
		}

		if(user.reg_pass == ''){
			awlert.neutral(alang[4].text,2000);	
			return false;
		}


		checkUsernameAjax(user.reg_username,user.reg_email);

		regName = user.reg_name;
		reg = user.reg_name+'  '+user.reg_email+'  '+user.reg_pass;



	};
	
	
	function validateEmail(email) {
		var re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
		return re.test(email);
	}

	function checkUsernameAjax(username,email){
		try {	
			var query = username+','+email;
			$scope.checkUsernameAjax = A.Query.get({action: 'checkUsername', query: query});
			$scope.checkUsernameAjax.$promise.then(function(){	
				$scope.validUsername = $scope.checkUsernameAjax['validUsername'];
				$scope.validEmail = $scope.checkUsernameAjax['validEmail'];
				if ($scope.validUsername == 'No') {		
					awlert.neutral($scope.checkUsernameAjax['validUsernameMsg'],2000);
					return false;		
				}

				if ($scope.validEmail == 'No') {		
					awlert.neutral($scope.checkUsernameAjax['validEmailMsg'],2000);
					return false;		
				}					
				reg_username = username;
				$localstorage.set('register',reg);
				$state.go('home.register3');				
			},
			function(){}
			)		 
		}
		catch (err) {
			console.log("Error " + err);
		}		
	}
	$scope.processFiles = function(files){
	    angular.forEach(files, function(flowFile, i){
	       var fileReader = new FileReader();
	          fileReader.onload = function (event) {
	            var uri = event.target.result;
					var image = uri;
					var r = Math.floor((Math.random() * 225) + 4000);
					var div = angular.element(document.getElementById('photo-upload')); 
					div.css('background-image','url('+image+')');
					$('#photo-upload i').hide();
					con = true;
					$.ajax({
						url: site_url+'assets/sources/appupload.php',
						data:{
							action: 'register',
							base64: image,
							uid: oneSignalID
						},
						cache: false,
						contentType: "application/x-www-form-urlencoded",				  
						type:"post",
						dataType:'JSON',
						success:function(res){
							
							reg_photo = res['photo'];
							reg_thumb = res['thumb'];
							console.log(reg_thumb);
						}
					});	                
	          };
	          fileReader.readAsDataURL(flowFile.file);
	    });
	};

	$scope.pick = function() {
		if (window.cordova) {
		var options = {
			quality: 40,
			destinationType: Camera.DestinationType.DATA_URL,
			sourceType: Camera.PictureSourceType.PHOTOLIBRARY,
			encodingType: Camera.EncodingType.JPEG,
			allowEdit : true,
		};
		$cordovaCamera.getPicture(options).then(function(imageData) {
			var image = "data:image/jpeg;base64," + imageData;
			reg_photo = site_url+'assets/sources/uploads/'+oneSignalID+'.jpg';
			var div = angular.element(document.getElementById('photo-upload')); 
			div.css('background-image','url('+image+')');
			con = true;
			$.ajax({
				url: site_url+'assets/sources/appupload.php',
				data:{
					action: 'register',
					base64: image,
					uid: oneSignalID
				},
				cache: false,
				contentType: "application/x-www-form-urlencoded",				  
				type:"post",
				success:function(){
				}
			});
		}, function(err) {
		  // error
		});
		} else {
			$('#uploadRegPhoto').click();
		}		
	};		
	
	$ionicViewSwitcher.nextDirection("exit");	
  })
  
  .controller('Register2Ctrl', function($scope, $state,$ionicViewSwitcher,$ionicModal,A,awlert, $ionicLoading, $timeout,$localstorage,$cordovaCamera) {  
	var looking = 2;									
	
	if(config == ''){
		$state.go('loader');
	}
	config = $localstorage.getObject('config');	
	$scope.isActive = true;
	$scope.regBtn = false;
	$scope.girl = true;
	$scope.boy = false;
	lang = $localstorage.getObject('lang');
	alang = $localstorage.getObject('alang');

	$('[data-alid]').each(function(){
	  var id = $(this).attr('data-alid');
	  $(this).text(alang[id].text);
	});
	$('[data-lid]').each(function(){
	  var id = $(this).attr('data-lid');
	  $(this).text(lang[id].text);
	});

	$scope.nexttext = alang[26].text;

	$scope.selectGirl = function(){
		if($scope.girl){
			$scope.girl = false;
			looking = looking-2;
			if(looking == 0){
				$scope.isActive = false;	
			}
			console.log(looking);
		} else {
			$scope.girl = true;	
			$scope.isActive = true;
			looking = looking+2;
			console.log(looking);
		}
	}
	
	$scope.selectGender = function(val){
		if($scope.boy){
			$scope.boy = false;
			looking = looking-1;
			console.log(looking);
			if(looking == 0){
				$scope.isActive = false;	
			}			
		} else {
			$scope.boy = true;	
			$scope.isActive = true;
			looking = looking+1;
			console.log(looking);
		}
	}	
		
  })
  
  .controller('Register3Ctrl', function($scope,$rootScope, $state,$ionicViewSwitcher,$ionicModal,A,awlert, $ionicLoading, $timeout,$localstorage,$cordovaCamera) {
	var gender = 0;
	var reg = $localstorage.get('register');
	lang = $localstorage.getObject('lang');
	alang = $localstorage.getObject('alang');
	config = $localstorage.getObject('config');
	$('[data-alid]').each(function(){
	  var id = $(this).attr('data-alid');
	  $(this).text(alang[id].text);
	});
	$('[data-lid]').each(function(){
	  var id = $(this).attr('data-lid');
	  $(this).text(lang[id].text);
	});

	if(regName == undefined){
		$rootScope.loader();
	}
	$scope.extraHeight = 80;
	$scope.alang = [];
	$scope.lang = [];
	angular.forEach(alang,function(entry) {						  
	  $scope.alang.push({
		id: entry,
		text: entry.text
	  });
	});
	
	angular.forEach(lang,function(entry) {						  
	  $scope.lang.push({
		id: entry,
		text: entry.text
	  });
	});	

	$scope.lang31 = alang[31].text;
	$scope.nexttext = alang[26].text;
	
	$scope.isActive = false;
	$scope.regBtn = false;
	$scope.girl = false;
	$scope.boy = false;
	$scope.name = regName;	
	$scope.genders = config.genders;
	gender = 0;
	var looking = 0;
	$scope.selectGender = function(val){
		$('[data-gender]').removeClass('active');
		$('#gender'+val).addClass('active');
		console.log(val);
		gender = val;
	}
	$scope.selectLooking = function(val){
		$('[data-looking]').removeClass('active');
		$('#looking'+val).addClass('active');
		console.log(val);
		looking = val;
		if(gender > 0){
			$scope.isActive = true;
		}
	}	
	
	$rootScope.updateRegLocation();

	$scope.send = function() {

		var date = $('#birth').val();

		var registerDate =  new Array();
		registerDate = date.split('-');	

		if(date == ''){
			awlert.neutral(alang[6].text,3000);	
			return false;
		}
		if(gender == 0){
			return false;
		}
		if(looking == 0){
			return false;
		}

		var yearVal = parseInt(registerDate[0])+parseInt(plugins['settings']['minRegisterAge']);	
	    var birthdayDate = new Date(yearVal, parseInt(registerDate[1])-1, parseInt(registerDate[2]),0,0,0,0);
	    var todayDate = new Date();

		if(birthdayDate >= todayDate){
			awlert.neutral(lang[856].text+' '+ plugins['settings']['minRegisterAge'],3000);	
			return false;			
		}

		console.log(gender + ' ' + looking);	
		reg = reg +'  '+ date +'  '+ gender;
		console.log(reg);	
		$localstorage.set('register',reg);		
		$scope.regBtn = true;
		var register =  new Array();
		register = reg.split('  ');		
		var dID = oneSignalID;

		$scope.ajaxRequest = A.Reg.get({action : 'register',reg_name: register[0], reg_email: register[1] , reg_pass: register[2], reg_birthday: register[3], reg_gender: gender, reg_looking: looking ,
		reg_photo : reg_photo, reg_thumb : reg_thumb,reg_username : reg_username,reg_lat : reg_lat,reg_lng : reg_lng,reg_city : reg_city,reg_country : reg_country, dID : dID });

		$scope.ajaxRequest.$promise.then(function(){						
			if($scope.ajaxRequest.error == 1){
				awlert.error($scope.ajaxRequest.error_m, 3000);
				$scope.regBtn = false;
				$scope.isActive = true;			
			} else {		
				$localstorage.setObject('user', $scope.ajaxRequest.user);	
				$localstorage.setObject('usPhotos', $scope.ajaxRequest.user.photos);
				usPhotos = $scope.ajaxRequest.user.photos;
				oneSignalID = $scope.ajaxRequest.user.id;
				userId = $scope.ajaxRequest.user.id;
				sape = $scope.ajaxRequest.user.slike;
					var rtnotification = 'notification'+$scope.ajaxRequest.user.id;
					channel.bind(rtnotification, function(data) {
						if(data.id != current_user_id ){
							$rootScope.unreadMessage = true;
							if(!$('.chatNotification').hasClass('is-visible')){		
								$rootScope.playAudio('Notification.wav');
								$('.chatNotification').removeClass('is-visible');
								$('.chatNotificationPhoto').removeClass('sblur');	
								$('.chatNotificationPhoto').css('background-image', 'url('+ data.icon +')');
								$('.chatNotificationContent').text(data.message);
								$timeout(function(){
									if(!$('.chatNotification').hasClass('is-visible')){
										$('.chatNotification').addClass('is-visible');
									}
								},100);				
								$timeout(function(){
									if($('.chatNotification').hasClass('is-visible')){
										$('.chatNotification').removeClass('is-visible');
									}
								},3000);					
							}
						}
					});
					var rtvisit = 'visit'+$scope.ajaxRequest.user.id;
					channel.bind(rtvisit, function(data) {	
						if(!$('.chatNotification').hasClass('is-visible')){	
							$rootScope.playAudio('Notification.wav');
							$('.chatNotification').removeClass('is-visible');
							$('.chatNotificationPhoto').removeClass('sblur');	
							$('.chatNotificationPhoto').css('background-image', 'url('+ data.icon +')');
							$('.chatNotificationContent').text(data.message);
							$timeout(function(){
								if(!$('.chatNotification').hasClass('is-visible')){
									$('.chatNotification').addClass('is-visible');
								}
							},100);				
							$timeout(function(){
								if($('.chatNotification').hasClass('is-visible')){
									$('.chatNotification').removeClass('is-visible');
								}
							},3000);
						}					
					});	
					var rtlike = 'like'+$scope.ajaxRequest.user.id;
					channel.bind(rtlike, function(data) {
						if(!$('.chatNotification').hasClass('is-visible')){

							$rootScope.playAudio('Notification.wav');		
							$('.chatNotification').removeClass('is-visible');
							if(user.premium == 1){
								$('.chatNotificationPhoto').removeClass('sblur');	
							} else {
								$('.chatNotificationPhoto').addClass('sblur');
							}							
							$('.chatNotificationPhoto').css('background-image', 'url('+ data.icon +')');
							$('.chatNotificationContent').text(data.message);
							$timeout(function(){
								if(!$('.chatNotification').hasClass('is-visible')){
									$('.chatNotification').addClass('is-visible');
								}
							},100);				
							$timeout(function(){
								if($('.chatNotification').hasClass('is-visible')){
									$('.chatNotification').removeClass('is-visible');
								}
							},3000);						
						}
					});				
				$localstorage.set('mobileUser',$scope.ajaxRequest.user.app_id);
				$state.go('home.explore');	
			}
		},
		function(){
			awlert.error('Something went wrong. Please try again later',3000);
		}
	)};	
	$ionicViewSwitcher.nextDirection("exit");
  })
  
  .controller('ExploreCtrl', function($scope,$rootScope,$timeout,$window,$ionicSlideBoxDelegate,$ionicViewSwitcher,$state,$sce,$ionicPlatform,preloader,$timeout, $ionicModal,A,$localstorage,Navigation,awlert,$ionicViewSwitcher,currentUser) {
	
	if(url == 'settings'){
		cards = [];
		$rootScope.cards = [];
	}
	url = 'explore';
	goBackGlobal = 'explore';
	current_user_id = 0;

	if(config == '' || user == ''){
		$state.go('loader');
	}

	user = $localstorage.getObject('user');
	config = $localstorage.getObject('config'); 
	lang = $localstorage.getObject('lang');
	alang = $localstorage.getObject('alang');
	app = $localstorage.getObject('app');
	$scope.exploreResult = true;
	$('#ready').removeClass('hidden');
	$rootScope.logged = true;
	$rootScope.me = user;
	$scope.newChat = false;

	$scope.site_name = config.name;
	$scope.registerReward = false;

	$scope.emailVerified = true;

	if(plugins['settings']['forceEmailVerification'] == 'Yes' && user.verified == 0){
		$scope.emailVerified = false;
	}


	if(user.registerReward == 'noData' && plugins['rewards']['newAccountFreeCredit'] > 0){
		$scope.registerReward = true;
	}
	$scope.creditsRewardRegister = plugins['rewards']['newAccountFreeCredit'];

	$scope.noResultImage = site_theme['discover_no_result']['val'];


	$timeout(function() {
		var adH = $('#discoverAdContainer').height();
		$('#discoverNameContainer').css('bottom',adH+'px');
	}, 1500);

	$scope.getRegisterReward = function(){
		$scope.registerReward = false;
		var data = [];
		data.name = '';
		data.icon = site_theme['notification_inapp_credits_icon']['val'];
		data.message = plugins['rewards']['newAccountFreeCredit']+' ' + lang[123].text;
		$rootScope.pushNotif(data,1,2500);
		user.registerReward = 'Yes';
        $rootScope.updateCredits(user.id,plugins['rewards']['newAccountFreeCredit'],'reward','Reward credits for register','newAccountFreeCredit');				
		$rootScope.playAudio('coin.wav');
	}
	$ionicSlideBoxDelegate.slide(0);
	if(rt == ''){
		rt = new Pusher(plugins['pusher']['key'], {
		  encrypted: true,
		  cluster: plugins['pusher']['cluster']

		});
		channel = rt.subscribe(plugins['pusher']['key']);		
	}
	
	var b = $window.innerHeight;
	$scope.h = b - 120;
	if(window.cordova){
		$ionicViewSwitcher.nextDirection("forward");
	} else {
		$ionicViewSwitcher.nextDirection("back");
	}	
	
	$scope.trustSrc = function(src) {
		return $sce.trustAsResourceUrl(src);
	}  
	
	$scope.logo = config.logo;

	$scope.alang = [];
	$scope.lang = [];

	var deltaNow = 0;
	var checkDelta = 0;
	$('#discoverGame').hammer({ direction: Hammer.DIRECTION_HORIZONTAL });
	$('#discoverGame').on('pan', function(e){

		checkDelta = deltaNow-50;		
		if(e.gesture.deltaX < checkDelta){
			return false;
		}		
		deltaNow = e.gesture.deltaX;

		$(this).css("-webkit-transform", "translateX("+deltaNow+"px) rotate("+deltaNow/10+"deg)");
		
	}).on('panend', function(e){
		deltaNow = e.gesture.deltaX;

		if (deltaNow > 150) {
			$(this).css("-webkit-transform", "translateX(400px) rotate(25deg)").css("opacity",0);
			$scope.swipeActivated(deltaNow);
		} 
		else if (deltaNow < -150) {
			$(this).css("-webkit-transform", "translateX(-400px) rotate(-25deg)").css("opacity",0);
			$scope.swipeActivated(deltaNow);
		} 
		else {
			$(this).css("-webkit-transform", "translateX(0px)");
		}
	});

	$scope.swipeActivated = function(x){
		$('#discoverGame').removeClass('vivify fadeIn');
		if(x < 0){
			console.log('left');
			checkDelta = 0;
			deltaNow = 0;
			$scope.nolike();			
		} else {
			console.log('right');
			checkDelta = 0;
			deltaNow = 0;		
			$scope.like();	
		}
		$timeout(function(){
			$('#discoverGame').css("-webkit-transform", "translateX(0px) rotate(0deg)");
			$('#discoverGame').addClass('vivify fadeIn');
		},70);	
	}



	angular.forEach(alang,function(entry) {						  
	  $scope.alang.push({
		id: entry,
		text: entry.text
	  });
	});

	angular.forEach(lang,function(entry) {						  
	  $scope.lang.push({
		id: entry,
		text: entry.text
	  });
	});	

	//load chat
	var chat = function () {
		try {
		  $scope.ajaxRequest2 = A.Game.get({action: 'getChat', id: user.id});
		  $scope.ajaxRequest2.$promise.then(function(){
				$scope.matches = $scope.ajaxRequest2.matches;
				$scope.unread = $scope.ajaxRequest2.unread;
				chats = $scope.matches;
				unread = $scope.unread;
				if(unread != null){
					$scope.unrread = unread.length;
					unread = unread.length;
				}
		  },
		  function(){}
		  )		 
		}
		catch (err) {
			console.log("Error " + err);
		}	
	}

	chat();

	//ADMOB
	/*
	if(show_ad == max_ad){
		if(window.cordova){
			if(window.AdMob) window.AdMob.prepareInterstitial( {adId:adMobI, autoShow:true} );	
			show_ad = 0;	
		}
	}
	show_ad++;
	*/

	$scope.cu2 = [];

	$scope.chatUser = function(url,slide,val) {
		currentUser.selectedUser=val;
		$state.go(url, val);  
	};	
	
	var w = window.innerWidth;
	w = w/2;
	if(w > 200){
		w = 200;
	}

	$scope.w = w;

	s_age = user.sage;
	user_country = user.country;
	user_city = user.city;	
	
	$scope.superLike = 5;
	$scope.uphoto = user.profile_photo;


	$scope.goToChat = function(){
		$ionicViewSwitcher.nextDirection('back'); // 'forward', 'back', etc.
		$state.go('home.matches');		
	}

	$scope.goToSettings = function(){
		$ionicViewSwitcher.nextDirection('forward'); // 'forward', 'back', etc.
		$state.go('home.settings');		
	}	


	

	var gameAction = function (id,action) {
		try {		  
		  A.Meet.get({action: 'game_like',uid1: user.id, uid2: id, uid3: action});		 
		}
		catch (err) {
			console.log("Error " + err);
		}		
	}
	

	$scope.imageLocations = [];	
	var card = function (val) {
		console.log('loading profiles');
		if(val == 4){
			cards = [];
		}
		try {		  
		  $scope.ajaxRequest = A.Game.get({action: 'game',id: user.id});
		  $scope.ajaxRequest.$promise.then(function(){	


		if($scope.ajaxRequest.game != 'error'){
			$scope.ajaxRequest.game.forEach(function(entry) {
				if(cards.indexOf(entry) !== -1) {
						console.log('alredy in game');
					} else {
						if(entry.id != user.id){
							cards.push(entry);								  
							$scope.imageLocations.push(entry.photo);
						}
					}
				});

			$scope.loading = false;
			preloader.preloadImages( $scope.imageLocations )
			.then(function() {
			},
			function() {

			});
			if(val == 1 || val == 4){							
				cu = cards[0].id;
				$scope.cu2 = cards[0];
				$rootScope.cards = cards;
			    $rootScope.aImages = cards[0].full.galleria; 
			}
			$rootScope.cards = cards;
		  }	else {
		  	if(cards == '' || cards == null){
		  		$scope.exploreResult = false;	
		  	}
		  }												
		  },
		  function(){ 
		  	$scope.loading = alang[7].text;

			}
		  )		 
		}
		catch (err) {
			console.log("Error " + err);
		}	
	}

	$scope.cardDestroyed = function(index,act) {
		if(act == 1){
			if ($rootScope.cards[index].isFan == 1){
				$scope.itsaMatch = true;
				var w = window.innerWidth;
				w = w/3;
				$scope.width = w;
				$scope.cu3 = $rootScope.cards[index];
				$scope.myPhoto = user.profile_photo;
				angular.forEach(alang,function(entry) {						  
					$scope.alang.push({
						id: entry,
						text: entry.text
					});
				});			 
			};
		}
	  addCards(1);
	  cu = $rootScope.cards[index].id;
	  $scope.cu2 = $rootScope.cards[index];
	};

	$scope.clickedTimeout = false;
	$scope.clickTimeout = function(){
		$scope.clickedTimeout = true;
		$timeout(function() {
			$scope.clickedTimeout = false;
		}, 900);
	}
   function addCards(v) {
		if(cards.length < 30 && cards.length > 15){
			card(2);
		}
		$timeout(function(){
		    $scope.yesVoted = false;
		    $scope.noVoted = false;
		    cards.splice(0, 1);
		    if(cards.length == 0){
		    	$scope.exploreResult = false;
		    } else {
		    	$rootScope.aImages = cards[0].full.galleria;	
		    }
	  		$rootScope.cards = cards;
		}, 120);    		
   }


	if(cards.length == 0){
		$scope.loading = true;
		card(1);
	} else {
		if(uLat != user.lat){
			uLat = user.lat;
			$scope.loading = true;
			card(4);			
		} else {
			$scope.loading = false;	
			$rootScope.cards = cards;
			console.log(cards);
		}
	}
	


    $scope.yesVoted = false;
    $scope.noVoted = false;
	$scope.like = function(){

		if($scope.clickedTimeout){
			awlert.neutral(alang[265].text, 1400);
			return false;
		}
		if(plugins['discover']['creditForLike'] > 0){

			if(user.credits < plugins['discover']['creditForLike']){ 
				$scope.openCreditsModal();
				return false;
			}

			var data = [];
			data.name = '';
			data.icon = site_theme['notification_inapp_credits_icon']['val'];
			data.message = lang[610].text+' '+plugins['discover']['creditForLike']+' ' + lang[73].text;
			$rootScope.pushNotif(data,1);
			
			$rootScope.updateCredits(user.id,plugins['discover']['creditForLike'],1,'Credits for like');

		
		}

		$('#likeBtn').removeClass('popInLeft');
		$('#likeBtn').removeClass('pulsate');
		$timeout(function(){
			$('#likeBtn').addClass('pulsate');
		},70);

		$ionicSlideBoxDelegate.slide(0);
		$ionicSlideBoxDelegate.update();
		$scope.yesVoted = true;
	 	gameAction(cards[0].id,1);
	 	$scope.cardDestroyed(0,1);

	 	$scope.clickTimeout();	 	
	}

	$scope.nolike = function(){
		if($scope.clickedTimeout){
			awlert.neutral(alang[265].text, 1000);
			return false;
		}		
		$ionicSlideBoxDelegate.slide(0);
		$ionicSlideBoxDelegate.update();
		$scope.noVoted = true;		
		gameAction(cards[0].id,0);

		$('#nolikeBtn').removeClass('popInRight');
		$('#nolikeBtn').removeClass('pulsate');
		$timeout(function(){
			$('#nolikeBtn').addClass('pulsate');
		},70);		  
		$scope.cardDestroyed(0,0);	
		$scope.clickTimeout();			
	}	

	$scope.closeMatchModal = function(){
		$scope.itsaMatch = false;
	}
	
	
	$scope.buySlike = function(){
		user.credits = parseInt(user.credits);
		if(400 > user.credits){
			$scope.openCreditsModal();
		} else {
			$scope.noSlike = false;
			var ma = user.id + ',400,10';
			awlert.neutral(alang[9].text, 3000);	  
			gameAction(cu,3);
			$scope.cardDestroyed(0,1);			
			try {	
			  $scope.ajaxRequest = A.Query.get({action: 'slike', query: ma});
			  $scope.ajaxRequest.$promise.then(function(){		
			  $localstorage.setObject('user',$scope.ajaxRequest.user);
			  user = $localstorage.getObject('user'); 
			  $scope.superLike = user.slike;
				var int = parseInt($scope.superLike);
				$scope.superLike = int-1;
				sape = user.slike;
				sape = sape-1;				
			  },
			  function(){}
			  )		 
			}
			catch (err) {
				console.log("Error " + err);
			}
		}
	};	
	
	$scope.noBtnSlike = function(){
	  $scope.noSlike = false;			
	}	
	

	//SPOTLIGHT
	var spot = function () {
		try {		  
		  $scope.ajaxRequest5 = A.Game.get({action: 'spotlight', id: user.id});
		  $scope.ajaxRequest5.$promise.then(function(){											
				spotlight = $scope.ajaxRequest5.spotlight;
				$rootScope.spotlight = [];
				$rootScope.spotlight = spotlight;
				
		  },
		  function(){}
		  )		 
		}
		catch (err) {
			console.log("Error " + err);
		}	
	}
	if(spotlight == ''){
		spot();
	}	else {
		$rootScope.spotlight = spotlight;	
		spot();
	}

	var loadStories = function (val=0) {
		console.log('loading stories');
		try {		  
			var query = user.lat+','+user.lng+','+user.s_gender;
			$scope.ajaxRequest = A.Query.get({action: 'viewStories', query: query});
			$scope.ajaxRequest.$promise.then(function(){	
				if($scope.ajaxRequest.result != 'empty'){
					storiesGlobal = $scope.ajaxRequest.stories;
					totalDiscoverStories = $scope.ajaxRequest.totalDiscoverStories;
				  }	else {
				  	storiesGlobal = [];
				  	if(storiesGlobal == [] || storiesGlobal == null){
				  	}
				  }												
		  },
		  function(){}
		  )		 
		}
		catch (err) {
			console.log("Error " + err);
		}	
	}

	if(storiesGlobal != undefined){
		if(storiesGlobal.length == 0){
			$timeout(function() {
				loadStories();
			}, 2000);
		}
	}
  })

  .controller('StoriesCtrl', function($scope,$rootScope,$timeout,$window,$ionicSlideBoxDelegate,$ionicViewSwitcher,$state,$sce,$ionicPlatform,preloader,$timeout, $ionicModal,A,$localstorage,Navigation,awlert,$ionicViewSwitcher,currentUser) {
	url = 'stories';
	goBackGlobal = 'stories';
	current_user_id = 0;
	
	if(config == '' || user == ''){
		$state.go('loader');
	}
	user = $localstorage.getObject('user');
	config = $localstorage.getObject('config'); 
	lang = $localstorage.getObject('lang');
	alang = $localstorage.getObject('alang');
	app = $localstorage.getObject('app');

	$scope.exploreStories = true;

	$('#ready').removeClass('hidden');

	$rootScope.logged = true;
	$rootScope.me = user;

	$scope.noResultImage = site_theme['discover_no_result']['val'];

	$ionicSlideBoxDelegate.slide(0);
	if(rt == ''){
		rt = new Pusher(plugins['pusher']['key'], {
		  encrypted: true,
		  cluster: plugins['pusher']['cluster']

		});
		channel = rt.subscribe(plugins['pusher']['key']);		
	}

	var b = $window.innerHeight;
	$scope.h = b - 120;
	if(window.cordova){
		$ionicViewSwitcher.nextDirection("forward");
	} else {
		$ionicViewSwitcher.nextDirection("back");
	}	
	
	$scope.trustSrc = function(src) {
		return $sce.trustAsResourceUrl(src);
	}  
	
	$scope.logo = config.logo;

	$scope.alang = [];
	$scope.lang = [];

	angular.forEach(alang,function(entry) {						  
	  $scope.alang.push({
		id: entry,
		text: entry.text
	  });
	});

	angular.forEach(lang,function(entry) {						  
	  $scope.lang.push({
		id: entry,
		text: entry.text
	  });
	});	


	$scope.cu2 = [];

	$scope.chatUser = function(url,slide,val) {
		currentUser.selectedUser=val;
		$state.go(url, val);  
	};	
	
	var w = window.innerWidth;
	w = w/2;
	if(w > 200){
		w = 200;
	}

	$scope.w = w;

	s_age = user.sage;
	user_country = user.country;
	user_city = user.city;	
	
	$scope.superLike = 5;
	$scope.uphoto = user.profile_photo;


	$scope.goToChat = function(){
		$ionicViewSwitcher.nextDirection('back'); // 'forward', 'back', etc.
		$state.go('home.matches');		
	}

	$scope.goToSettings = function(){
		$ionicViewSwitcher.nextDirection('forward'); // 'forward', 'back', etc.
		$state.go('home.settings');		
	}	

	storyPage = 'discover';

	$scope.storyAll = true;
	$scope.storyPopular = false;
	$scope.tab1 = 'is-active';
   	$scope.onTabShow = function(val,title){
		$scope.tab1 = '';   		
		$scope.tab2 = '';
   		
		if(val == 1){
			 $scope.storyAll = true;
			 $scope.storyPopular = false;	
			 $scope.tab1 = 'is-active';
		}
		if(val == 2){
			 $scope.storyAll = true;
			 $scope.storyPopular = false;	
			 $scope.tab1 = 'is-active';			
			$('#uploadStoryMobile').click();
		}							
	}

	$scope.stories = storiesGlobal;

	stories = function (val=0) {
		try {		  
			var query = user.lat+','+user.lng+','+user.s_gender;
			$scope.ajaxRequest = A.Query.get({action: 'viewStories', query: query});
			$scope.ajaxRequest.$promise.then(function(){	
				if($scope.ajaxRequest.result != 'empty'){
					$scope.loading = false;
					storiesGlobal = $scope.ajaxRequest.stories;
					$rootScope.stories = storiesGlobal;
					$scope.stories = storiesGlobal;
					totalDiscoverStories = $scope.ajaxRequest.totalDiscoverStories;
				  }	else {
				  	storiesGlobal = [];
				  	if(storiesGlobal == [] || storiesGlobal == null){
				  		$scope.exploreStories = false;	
				  	}
				  }												
		  },
		  function(){ 
		  	$scope.loading = alang[7].text;

			}
		  )		 
		}
		catch (err) {
			console.log("Error " + err);
		}	
	}

	stories();

  })

  .controller('locationCtrl', function($scope,$rootScope,$timeout,$window,$ionicSlideBoxDelegate,$ionicViewSwitcher,$state,$sce,$ionicPlatform,preloader,$timeout, $ionicModal,A,$localstorage,Navigation,awlert,$ionicViewSwitcher,currentUser) {
	url = 'location';

	current_user_id = 0;
	user = $localstorage.getObject('user');
	if(config == ''){
		$state.go('loader');
	}

	config = $localstorage.getObject('config'); 
	lang = $localstorage.getObject('lang');
	alang = $localstorage.getObject('alang');
	app = $localstorage.getObject('app');

	$scope.exploreStories = true;

	$('#ready').removeClass('hidden');

	$rootScope.logged = true;
	$rootScope.me = user;

	$scope.noResultImage = site_theme['discover_no_result']['val'];

	$ionicSlideBoxDelegate.slide(0);
	if(rt == ''){
		rt = new Pusher(plugins['pusher']['key'], {
		  encrypted: true,
		  cluster: plugins['pusher']['cluster']

		});
		channel = rt.subscribe(plugins['pusher']['key']);		
	}

	var b = $window.innerHeight;
	$scope.h = b - 120;
	if(window.cordova){
		$ionicViewSwitcher.nextDirection("forward");
	} else {
		$ionicViewSwitcher.nextDirection("back");
	}	
	
	$scope.trustSrc = function(src) {
		return $sce.trustAsResourceUrl(src);
	}  
	
	$scope.logo = config.logo;

	$scope.alang = [];
	$scope.lang = [];

	angular.forEach(alang,function(entry) {						  
	  $scope.alang.push({
		id: entry,
		text: entry.text
	  });
	});

	console.log($scope.alang);
	angular.forEach(lang,function(entry) {						  
	  $scope.lang.push({
		id: entry,
		text: entry.text
	  });
	});	

	
	var w = window.innerWidth;
	w = w/2;
	if(w > 200){
		w = 200;
	}

	$scope.w = w;

	$scope.city = user.city;

		

  })

  .controller('profileCtrl', function($state,$ionicLoading,$rootScope,$ionicActionSheet,$ionicViewSwitcher,$scope,A, $ionicModal,$localstorage,Navigation) {
	url = 'profile-me';
	if(window.cordova){
		$ionicViewSwitcher.nextDirection("forward");
	} else {
		$ionicViewSwitcher.nextDirection("back");
	}

	goBackGlobal = 'profile';

	if(config == '' || user == ''){
		$state.go('loader');
	}	

	user = $localstorage.getObject('user');
	
	config = $localstorage.getObject('config');
	lang = $localstorage.getObject('lang');
	alang = $localstorage.getObject('alang');
	app = $localstorage.getObject('app');
	usPhotos = $localstorage.getObject('usPhotos');
	$('[data-lid]').each(function(){
	  var id = $(this).attr('data-lid');
	  $(this).text(lang[id].text);
	});	


	$('#ready').removeClass('hidden');
	$rootScope.logged = true;
	$rootScope.me = user;

	$scope.showStory = false;

	$scope.alang = [];
	angular.forEach(alang,function(entry) {						  
	  $scope.alang.push({
		id: entry,
		text: entry.text
	  });
	});

	$scope.lang = [];
	angular.forEach(lang,function(entry) {						  
	  $scope.lang.push({
		id: entry,
		text: entry.text
	  });
	});		
	
		
	if(rt == ''){
		rt = new Pusher(plugins['pusher']['key'], {
		  encrypted: true,
		  cluster: plugins['pusher']['cluster']

		});
		channel = rt.subscribe(plugins['pusher']['key']);		
	}	

	$scope.loading = false;
	$scope.bio = user.bio;
	$scope.name = user.name;
	$scope.age = user.age;

	$scope.cashout = true;
	if(plugins['withdrawal']['enabled'] == 'No'){
		$scope.cashout = false;
	}

	$scope.verificationEnabled = true;
	if(plugins['verification']['enabled'] == 'No'){
		$scope.verificationEnabled = false;
	}

	$scope.credits = user.credits;
	if(user.premium == 1){
		$scope.premium = 'Activated';
	} else {
		$scope.premium	= 'No'
	}
		
	
	function userInfo(id){
		try {	
		  $scope.ajaxRequest = A.Device.get({action: 'userProfile', id: id});
		  $scope.ajaxRequest.$promise.then(function(){											
				$localstorage.setObject('user', $scope.ajaxRequest.user);
				$rootScope.me = $scope.ajaxRequest.user;
				if($rootScope.me.story > 0){
					$scope.showStory = true;
				}
				if($rootScope.me.unreadMessagesCount > 0){
					$rootScope.unreadMessage = true;
				} else {
					$rootScope.unreadMessage = false;
				}								
				$rootScope.uphotos = $scope.ajaxRequest.user.photos;	
				$localstorage.setObject('usPhotos',$scope.ajaxRequest.user.photos); 
		  },
		  function(){}
		  )		 
		}
		catch (err) {
		console.log("Error " + err);
		}	
	} 
	userInfo(user.id);   			
  })

  .controller('popularityCtrl', function($state,$rootScope,$ionicViewSwitcher,$scope,A, $ionicModal,$localstorage,Navigation) {
	user = $localstorage.getObject('user');
	lang = $localstorage.getObject('lang');

	alang = $localstorage.getObject('alang');
	site_prices = $localstorage.getObject('prices');
	$scope.spotlightprice = site_prices.spotlight;
	$scope.alang = [];
	$('#ready').removeClass('hidden');
	$rootScope.logged = true;
	$rootScope.me = user;


  })  	

  .controller('SettingsCtrl', function($state,$rootScope,$ionicViewSwitcher,$ionicActionSheet,$scope,A, $ionicModal,$localstorage,Navigation) {
	user = $localstorage.getObject('user');
	lang = $localstorage.getObject('lang');
	if(config == ''){
		$state.go('loader');
	}	
	url = 'settings';
	config = $localstorage.getObject('config');
	alang = $localstorage.getObject('alang');
	site_prices = $localstorage.getObject('prices');
	$scope.spotlightprice = site_prices.spotlight;
	$scope.alang = [];
	$('#ready').removeClass('hidden');
	$rootScope.logged = true;
	$rootScope.me = user;
	$scope.newChat = false;

	$scope.ageMin = plugins['settings']['minRegisterAge'];
	$scope.ageMax = 99;
	$scope.range = {
		from: ag1,
		to: ag2
	};

	if(window.cordova){
		$ionicViewSwitcher.nextDirection("forward");
	} else {
		$ionicViewSwitcher.nextDirection("back");
	}

	$scope.lang = [];
			
								

	angular.forEach(alang,function(entry) {						  
	  $scope.alang.push({
		id: entry.id,
		text: entry.text
	  });
	});

	angular.forEach(lang,function(entry) {						  
	  $scope.lang.push({
		id: entry.id,
		text: entry.text
	  });
	});	

	$scope.city = user.city;
	$scope.country = user.country;
	$scope.s_age = user.sage;
	if(user.looking == 1){
		$scope.gender = lang[120].text;			
	}
	if(user.looking == 2){
		$scope.gender = lang[121].text;
	}
	if(user.looking == 3){
		$scope.gender = lang[122].text;			
	}
	var l = user.looking-1;
	$scope.gender = config.genders[l].text;		

	$scope.updateGender = function() {
	  var hideSheet = $ionicActionSheet.show({
		buttons: config.genders,
		cancelText: alang[2].text,
		cancel: function() {
		  },
		buttonClicked: function(index,val) {
			var gender;
			console.log(val.id);
			$scope.gender = val.text;		
			gender = val.id;	
			var message = user.id+','+gender;
			$scope.ajaxRequest34 = A.Query.get({action: 'updateGender', query: message});
			$scope.ajaxRequest34.$promise.then(function(){											
				$localstorage.setObject('user', $scope.ajaxRequest34.user);
				cards = [];
			});				
		  return true;
		}
	  });
	}		

	if($scope.firstOpen){
		$scope.data = {};
		$scope.data.location = user.city+','+user.country;
		$scope.firstOpen = false;			
	}
	$scope.onAddressSelection = function (location) {
		$scope.data.location = location.name;
		console.log(location);
		var lat = location.geometry.location.lat();
		var lng = location.geometry.location.lng();
		var country;
		var city;

		for (var i = 0; i < location.address_components.length; i++){
		 if(location.address_components[i].types[0] == "country") {
				country = location.address_components[i].long_name;
			}
		 if(location.address_components[i].types[0] == "locality") {
				city = location.address_components[i].long_name;
			}					
		 }
		var message = user.id+','+lat+','+lng+','+city+','+country;
		$scope.ajaxRequest36 = A.Query.get({action: 'updateLocation', query: message});
		$scope.ajaxRequest36.$promise.then(function(){											
			$localstorage.setObject('user', $scope.ajaxRequest36.user);
		});				 
	};


	$scope.initDistance = user.s_radius;
	$scope.distanceMeter = config.plugins.settings.distanceMeter;
	$scope.distanceLimit = config.plugins.settings.distanceLimit;

	$scope.updateDistance = function(e) {
		var message = user.id+','+e;
		$scope.ajaxRequest3 = A.Query.get({action: 'updateSRadius', query: message});
		$scope.ajaxRequest3.$promise.then(function(){											
			$localstorage.setObject('user', $scope.ajaxRequest3.user);
		});			
	};


	$scope.updateAge = function(e,a) {
		ag1 = e;
		ag2 = a;
		var message = user.id+','+e+','+a;
		$scope.ajaxRequest31 = A.Query.get({action: 'updateAge', query: message});
		$scope.ajaxRequest31.$promise.then(function(){											
		});			
	};	

	if(onlineMeet == 1){
		$scope.online = true;
	} else {
		$scope.online = false;
	}

	$scope.updateOnlineCheck = config.plugins.meet.viewOnlyPremiumOnline;

	if($scope.updateOnlineCheck == 'Yes' && user.premium == 0){
		$scope.updateOnlineCheck = false;
	} else {
		$scope.updateOnlineCheck = true;
	}

	$scope.updateOnline = function() {
		if(onlineMeet == 0){
			onlineMeet = 1;
			$scope.online = true;
		} else {
			onlineMeet = 0;
			$scope.online = false;
		}
	};	


	
    $scope.openPremiumModal = function() {
		account_premium = $localstorage.getObject('account_premium');
		$scope.pchat = account_premium.chat;
		$scope.dchatprice = site_prices.chat;
		$scope.config_email = config.paypal;
		$scope.premium_days = p_quantity;
		$scope.currency = config.currency;	
		$rootScope.cp = $localstorage.getObject('premium_package');		
		$scope.premiumModal.show();
			
		$scope.buyPremium = function(c,p,i){
			p_quantity = c;
			p_price = p;
			$scope.premium_days = c;
			$scope.premium_price = p;
			$scope.premium_custom = user.id+','+c;			
			$scope.premiumModal.hide();
			var paypalU = site_url +'app/paypal.php?type=2&amount='+p_price+'&custom='+$scope.premium_custom+'&days='+$scope.premium_days;
			if (window.cordova) {
				cordova.InAppBrowser.open(config.site_url+'pay/index.php?package='+i+'&type=premium', '_blank', 'location=yes');
			} else {
				window.open(config.site_url+'pay/index.php?package='+i+'&type=premium', '_blank', 'location=yes');
			}
		}
	}
	
  })
  
  .controller('VisitorsCtrl', function($scope,$ionicViewSwitcher,$ionicPlatform,$state,Navigation,$localstorage,A,$sce,$ionicScrollDelegate,$interval,currentUser) {
	url = 'visitors';
	lang = $localstorage.getObject('lang');
	if(config == ''){
		$state.go('loader');
	}
	config = $localstorage.getObject('config');
	if(window.cordova){
		$ionicViewSwitcher.nextDirection("forward");
	} else {
		$ionicViewSwitcher.nextDirection("back");
	}

	alang = $localstorage.getObject('alang');
	site_prices = $localstorage.getObject('prices');
	$scope.spotlightprice = site_prices.spotlight;
	$scope.alang = [];

	angular.forEach(alang,function(entry) {						  
	  $scope.alang.push({
		id: entry,
		text: entry.text
	  });
	});
	//ADMOB
	/*
	if(show_ad == max_ad){
		if(window.AdMob) window.AdMob.prepareInterstitial( {adId:adMobI, autoShow:true} );
		
		show_ad = 0;	
	}	
	show_ad++;	
	*/

	if(rt == ''){
		rt = new Pusher(plugins['pusher']['key'], {
		  encrypted: true,
		  cluster: plugins['pusher']['cluster']

		});
		channel = rt.subscribe(plugins['pusher']['key']);	
	}	

	user = $localstorage.getObject('user');
	var aBasic = $localstorage.getObject('account_basic');
	var aPremium = $localstorage.getObject('account_premium');	
	var viewScroll = $ionicScrollDelegate.$getByHandle('userMessageScroll');	
	
	$scope.changePage = function(url,slide,val) {
		if($scope.canSeeVisitors){
			currentUser.selectedUser=val;
			$state.go(url, val); 
		}  
	};	
	
    $scope.show = 1;
	$scope.photo = user.profile_photo;
	var w = window.innerWidth;
	w = w/2;
	if(w > 200){
		w = 200;
	}
	$scope.w = w;
	$scope.noVisitors = false;
	if(user.premium == 0 && aBasic.visits == 0){
		$scope.canSeeVisitors = false;
	} else {
		$scope.canSeeVisitors = true;
		var q = user.id+',visits';
		user.newVisits = 0;
		$localstorage.setObject('user',user);
		A.Query.get({action: 'updateInteractionNotification', query: q});		
	}
	$scope.max = 20;
	
	var visits = function () {
		try {
		  $scope.visitors = visitors;
		  $scope.ajaxRequest = A.Game.get({action: 'getVisitors', id: user.id});
		  $scope.ajaxRequest.$promise.then(function(){
				if($scope.ajaxRequest.visitors != null){				
					$scope.visitors = $scope.ajaxRequest.visitors;
					visitors = $scope.visitors;
				} else {
					$scope.noVisitors = true;	
					visitors = $scope.visitors;
				}	
				console.log(visitors);
		  },
		  function(){$scope.noVisitors = true;}
		  )		 
		}
		catch (err) {
			console.log("Error " + err);
		}	
	}	
	visits();
	$scope.title = alang[10].text;
  })
    
  .controller('MatchCtrl', function($scope,$ionicViewSwitcher,$ionicPlatform,$state,Navigation,$localstorage,A,$sce,$ionicScrollDelegate,$interval,currentUser) {
	user = $localstorage.getObject('user');
	lang = $localstorage.getObject('lang');
	if(config == ''){
		$state.go('loader');
	}
	config = $localstorage.getObject('config');
	alang = $localstorage.getObject('alang');
	site_prices = $localstorage.getObject('prices');
	$scope.firstmeprice = site_prices.first;
	$scope.cienmeprice = site_prices.discover;
	$scope.alang = [];
	$scope.lang = [];

	if(window.cordova){
		$ionicViewSwitcher.nextDirection("forward");
	} else {
		$ionicViewSwitcher.nextDirection("back");
	}

	angular.forEach(alang,function(entry) {						  
	  $scope.alang.push({
		id: entry,
		text: entry.text
	  });
	});

	angular.forEach(lang,function(entry) {						  
	  $scope.lang.push({
		id: entry,
		text: entry.text
	  });
	});	

	//ADMOB
	/*
	if(show_ad == max_ad){
		if(window.AdMob) window.AdMob.prepareInterstitial( {adId:adMobI, autoShow:true} );
		show_ad = 0;	
	}	
	show_ad++;	
	*/
	if(rt == ''){
		rt = new Pusher(plugins['pusher']['key'], {
		  encrypted: true,
		  cluster: plugins['pusher']['cluster']

		});
		channel = rt.subscribe(plugins['pusher']['key']);	
	}	

	url = 'match';
	var aBasic = $localstorage.getObject('account_basic');
	var aPremium = $localstorage.getObject('account_premium');		
	var viewScroll = $ionicScrollDelegate.$getByHandle('userMessageScroll');	
	$scope.changePage = function(url,slide,val,forceUrl='') {
		if($scope.canSeeFans || $scope.canSeeFans == false && $scope.show != 2 || forceUrl == 'mylikes'){
			currentUser.selectedUser=val;
			$state.go(url, val); 
		}
	};	
	$scope.show = 1;
	if(user.premium == 0 && aBasic.fans == 0){
		$scope.canSeeFans = false;
	} else {
		$scope.canSeeFans = true;
		var q = user.id+',fans';
		user.newFans = 0;
		$localstorage.setObject('user',user);
		A.Query.get({action: 'updateInteractionNotification', query: q});			
	}	
   	$scope.onTabShow = function(val,title){
		$scope.show = val;	
		$scope.title = title;				
	    viewScroll.scrollTop(true);
	}
	$scope.photo = user.profile_photo;
	var w = window.innerWidth;
	w = w/2;
	if(w > 200){
		w = 200;
	}
	$scope.w = w;
	$scope.noMatches = false;
	$scope.noLikes = false;
	$scope.noFans = false;
	$scope.noSuperLike= false;
	
	$scope.newlikes = 0;
	$scope.newfans = 0;	
	$scope.max = 20;
	
	var matches = function () {
		try {
		  $scope.matches = matche;
		  $scope.mylikes = mylikes;
		  $scope.myfans = myfans;
		  $scope.superlikes = superlikes;
		  
		  $scope.ajaxRequest = A.Game.get({action: 'getMatches', id: user.id});
		  $scope.ajaxRequest.$promise.then(function(){
				if($scope.ajaxRequest.matches != null){				
					$scope.matches = $scope.ajaxRequest.matches;
					matche = $scope.matches;
				} else {
					$scope.noMatches = true;	
					matche = $scope.matches;
				}
				if($scope.ajaxRequest.mylikes != null){				
					$scope.mylikes = $scope.ajaxRequest.mylikes;
					mylikes = $scope.mylikes;
				} else {
					$scope.noLikes = true;	
					mylikes = $scope.mylikes;
				}
							
				if($scope.ajaxRequest.myfans != null){				
					$scope.myfans = $scope.ajaxRequest.myfans;
					myfans = $scope.myfans;
				} else {
					$scope.noFans = true;	
					myfans = $scope.myfans;
				}							
		  },
		  function(){}
		  )		 
		}
		catch (err) {
			console.log("Error " + err);
		}	
	}	
	matches();
	$scope.title = alang[11].text;
	$scope.changePage = function(url,slide,val) {
		currentUser.selectedUser=val;
		if(window.cordova){
			$state.go(url, val); 
		} else {
			$state.go(url,slide, val); 		
		} 		 
	};		
  })
  
  
  

  .controller('MatchesCtrl', function($scope,$rootScope,$compile,$filter,$timeout,$ionicPlatform,$ionicViewSwitcher,$ionicListDelegate,$state,Navigation,$localstorage,A,$sce,$ionicScrollDelegate,$interval,currentUser) {
	$interval.cancel(chatInterval);

	if(config == '' || user == ''){
		$state.go('loader');
	}
	user = $localstorage.getObject('user');
	config = $localstorage.getObject('config');	
	url = 'messages';
	current_user_id = 0;
	alang = $localstorage.getObject('alang');
	lang = $localstorage.getObject('lang');
	site_prices = $localstorage.getObject('prices');
	$rootScope.me = user;
	$scope.spotlightprice = site_prices.spotlight;
	$scope.alang = [];
	$scope.lang = [];
	$('#ready').removeClass('hidden');
	if(chats == ''){
		$scope.loader = true;
	}

	//clear old realtime notifications
	channel.unbind();
	var aBasic = $localstorage.getObject('account_basic');
	var aPremium = $localstorage.getObject('account_premium');	
	if(rt == ''){
		rt = new Pusher(plugins['pusher']['key'], {
		  encrypted: true,
		  cluster: plugins['pusher']['cluster']

		});
		channel = rt.subscribe(plugins['pusher']['key']);	
	}

	current_user_id = 0;
	var rtnotification = 'notification'+$scope.ajaxRequest.user.id;
	channel.bind(rtnotification, function(data) {
		if(data.id != current_user_id ){
			$rootScope.unreadMessage = true;
			if(!$('.chatNotification').hasClass('is-visible')){		

				$rootScope.playAudio('Notification.wav');
				$('.chatNotification').attr('ng-click',"goTo('home.matches');hideNotification()");
				$compile($('.chatNotification'))($scope);								
				$('.chatNotification').removeClass('is-visible');
				$('.chatNotificationPhoto').removeClass('sblur');	
				$('.chatNotificationPhoto').css('background-image', 'url('+ data.icon +')');
				$('.chatNotificationContent').text(data.message);
				$timeout(function(){
					if(!$('.chatNotification').hasClass('is-visible')){
						$('.chatNotification').addClass('is-visible');
					}
				},100);				
				$timeout(function(){
					if($('.chatNotification').hasClass('is-visible')){
						$('.chatNotification').removeClass('is-visible');
					}
				},3000);					
			}
		}
		chat();
	});
	var rtvisit = 'visit'+$scope.ajaxRequest.user.id;
	channel.bind(rtvisit, function(data) {	
		if(!$('.chatNotification').hasClass('is-visible')){	

			$rootScope.playAudio('Notification.wav');
			$('.chatNotification').attr('ng-click',"goTo('home.visitors');hideNotification()");
			$compile($('.chatNotification'))($scope);		

			$('.chatNotification').removeClass('is-visible');
			$('.chatNotificationPhoto').removeClass('sblur');	
			$('.chatNotificationPhoto').css('background-image', 'url('+ data.icon +')');
			$('.chatNotificationContent').text(data.message);
			$timeout(function(){
				if(!$('.chatNotification').hasClass('is-visible')){
					$('.chatNotification').addClass('is-visible');
				}
			},100);				
			$timeout(function(){
				if($('.chatNotification').hasClass('is-visible')){
					$('.chatNotification').removeClass('is-visible');
				}
			},3000);
		}					
	});	
	var rtlike = 'like'+$scope.ajaxRequest.user.id;
	channel.bind(rtlike, function(data) {
		if(!$('.chatNotification').hasClass('is-visible')){		
			$('.chatNotification').removeClass('is-visible');

			$rootScope.playAudio('Notification.wav');
			$('.chatNotification').attr('ng-click',"goTo('home.match');hideNotification()");
			$compile($('.chatNotification'))($scope);

			if(user.premium == 1){
				$('.chatNotificationPhoto').removeClass('sblur');	
			} else {
				$('.chatNotificationPhoto').addClass('sblur');
			}							
			$('.chatNotificationPhoto').css('background-image', 'url('+ data.icon +')');
			$('.chatNotificationContent').text(data.message);
			$timeout(function(){
				if(!$('.chatNotification').hasClass('is-visible')){
					$('.chatNotification').addClass('is-visible');
				}
			},100);				
			$timeout(function(){
				if($('.chatNotification').hasClass('is-visible')){
					$('.chatNotification').removeClass('is-visible');
				}
			},3000);						
		}
	});

	$scope.spot_price = site_prices.spotlight;

	//ADD TO SPOTLIGHT
	$scope.openSpot = function(){
		$scope.showSpot = true;
	}
	$scope.cancelSpot = function(){
		$scope.showSpot = false;	
	}
	$scope.addToSpotBtn = function(){
		console.log(site_prices.spotlight);
		user.credits = parseInt(user.credits);
		if(user.credits < site_prices.spotlight){
			$scope.openCreditsModal("'"+user.profile_photo+"'");
		} else {
			$scope.showMe = false;
			addToSpotlight();
		}
	}	
	var addToSpotlight = function () {
		try {	
		  $scope.ajaxRequest2 = A.Query.get({action: 'addToSpotlight', query: user.id});
		  $scope.ajaxRequest2.$promise.then(function(){	
		  	$rootScope.spotlight = [];
			spot();
		  },
		  function(){}
		  )		 
		}
		catch (err) {
			console.log("Error " + err);
		}	
	}	
	var w = window.innerWidth;
	w = w/3;
	if(w > 120){
		w = 120;
	}

	$scope.w = w;	
	angular.forEach(alang,function(entry) {						  
	  $scope.alang.push({
		id: entry,
		text: entry.text
	  });
	});
	angular.forEach(lang,function(entry) {					  
	  $scope.lang.push({
		id: entry.id,
		text: entry.text
	  });
	});	

	var viewScroll = $ionicScrollDelegate.$getByHandle('userMessageScroll');	
	if(window.cordova){
		$ionicViewSwitcher.nextDirection("forward");
	} else {
		$ionicViewSwitcher.nextDirection("back");
	}

	$rootScope.showBoostDiscover = false;
    var n = new Date().getTime() / 1000;
    if(user.last_access > n && user.discover > 0){
    	$rootScope.showBoostDiscover = false;
    } else {
    	$rootScope.showBoostDiscover = true;
    }

	$scope.mymatches = [];
	$scope.mylikes = [];
	$scope.myfans = [];
	var matches = function () {
		try {
			$scope.ajaxRequest = A.Game.get({action: 'getMatches', id: user.id});
		  	$scope.ajaxRequest.$promise.then(function(){
				if($scope.ajaxRequest.matches != null){				
					$scope.mymatches = $scope.ajaxRequest.matches;
				}
				if($scope.ajaxRequest.mylikes != null){				
					$scope.mylikes = $scope.ajaxRequest.mylikes;
				}		
				if($scope.ajaxRequest.myfans != null){				
					$scope.myfans = $scope.ajaxRequest.myfans;
				}						
		  },
		  function(){}
		  )		 
		}
		catch (err) {
			console.log("Error " + err);
		}	
	}	
	matches();	



	$scope.changePage = function(url,slide,val) {
		console.log(val);
		currentUser.selectedUser=val;
		if(window.cordova){
			$state.go(url, val); 
		} else {
			$state.go(url,slide, val); 		
		} 		 
	};	
    $scope.show = 1;
	$scope.loadM = parseInt(10);
	$scope.tab1 = 'is-active';
	$scope.showExtra = false;
	$scope.canSeeFans = true;
	viewScroll.scrollTop(true);
   	$scope.onTabShow = function(val){
		if(user.premium == 0 && aBasic.fans == 0 && val == 5){
			$scope.canSeeFans = false;
		} else {
			$scope.canSeeFans = true;
		}	   		
		$scope.tab1 = '';   		
		$scope.tab2 = '';
		$scope.tab3 = '';	
		$scope.tab4 = '';
		$scope.tab5 = '';					
		$scope.tab6 = '';					
		if(val == 1){
		 $scope.all = true;
		 $scope.unread = false;
		 $scope.online = false;	
		 $scope.showExtra = false;	
		 $scope.extra = [];
		 viewScroll.scrollTop(true);
		 $scope.loadM = parseInt(10);
		 $scope.tab1 = 'is-active';
		 $scope.$broadcast('scroll.infiniteScrollComplete');
		}
		if(val == 2){
		 $scope.all = false;
		 $scope.unread = true;
		 $scope.online = false;		 
		 $scope.showExtra = false;
		 $scope.extra = [];		 
		 viewScroll.scrollTop(true);
		 $scope.loadM = parseInt(10);
		 $scope.tab2 = 'is-active';
		 $scope.$broadcast('scroll.infiniteScrollComplete');
		}
		if(val == 3){
		 $scope.all = false;
		 $scope.unread = false;
		 $scope.online = true;
		 $scope.showExtra = false;
		 $scope.extra = [];		 
		 viewScroll.scrollTop(true);
		 $scope.loadM = parseInt(10);
		 $scope.tab3 = 'is-active';	
		 $scope.$broadcast('scroll.infiniteScrollComplete');	 
		}
		if(val == 4){
		 $scope.all = false;
		 $scope.unread = false;
		 $scope.online = false;
		 $scope.showExtra = true;			
		 $scope.extra = $scope.mymatches;
		 viewScroll.scrollTop(true);
		 $scope.loadM = parseInt(10);
		 $scope.tab4 = 'is-active';	
		 $scope.$broadcast('scroll.infiniteScrollComplete');
		}
		if(val == 5){
		 $scope.all = false;
		 $scope.unread = false;
		 $scope.online = false;
		 $scope.showExtra = true;		
		 $scope.extra = $scope.myfans;
		 viewScroll.scrollTop(true);
		 $scope.loadM = parseInt(10);
		 $scope.tab5 = 'is-active';	
		 $scope.$broadcast('scroll.infiniteScrollComplete');	 
		}
		if(val == 6){
		 $scope.all = false;
		 $scope.unread = false;
		 $scope.online = false;
		 $scope.showExtra = true;		
		 $scope.extra = $scope.mylikes;
		 viewScroll.scrollTop(true);
		 $scope.loadM = parseInt(10);
		 $scope.tab6 = 'is-active';	
		 $scope.$broadcast('scroll.infiniteScrollComplete');	 
		}										
	}
	$scope.searching = false;
   	$scope.adn = {};
	$scope.srchchange = function () {

        $scope.matches = null;
        var filtervalue = [];
		var serachData= chats;
		$scope.searching = true;
        for (var i = 0; i <serachData.length; i++) {

            var fltvar = $filter('uppercase')($scope.adn.item);
            var jsval = $filter('uppercase')(serachData[i].name);

            if (jsval.indexOf(fltvar) >= 0) {
                filtervalue.push(serachData[i]);
            }
        }
        if($scope.adn.item.length == 0){
			$scope.ressetserach();
        }
        $scope.matches = filtervalue;

    };

    $scope.ressetserach = function () {
        $scope.adn.item = "";
        $scope.matches = chats;
        $scope.searching = false;
    }

	//ADMOB
	/*
	if(show_ad == max_ad){
		if(window.AdMob) window.AdMob.prepareInterstitial( {adId:adMobI, autoShow:true} );
		
		show_ad = 0;	
	}	
	show_ad++;	
	*/

	$scope.unread = false;
	$scope.online = false;	
	$scope.all = true;	

	
	var chat = function () {
		try {
		  $scope.matches = chats;
		  $scope.ajaxRequest2 = A.Game.get({action: 'getChat', id: user.id});
		  $scope.ajaxRequest2.$promise.then(function(){
				$scope.matches = $scope.ajaxRequest2.matches;
				chats = $scope.matches;
				$scope.loader = false;
				$scope.contacts = 1;
				console.log(chats);
		  },
		  function(){ $scope.loader = false; }
		  )		 
		}
		catch (err) {
			console.log("Error " + err);
		}	
	}

	chat();

	$scope.onItemDelete = function(item) {
		var query = user.id+','+item.id;
		A.Query.get({action: 'del_conv' ,query: query});	
		$('.item-content').css({
		  'transform'         : 'translate3d(0px,0px,0px)'
		});
	};

	$scope.loaderMore = false;
	$scope.loadMore = function(){
		$scope.loaderMore = true;
		$timeout(function(){
			$scope.loaderMore = false;
			$scope.loadM = $scope.loadM + 10;		
		}, 300);		

	}	
	$scope.shouldShowDelete = true;
	$scope.listCanSwipe = true;

	$rootScope.spotlight = [];
	//SPOTLIGHT
	var spot = function () {
		try {		  
		  $scope.ajaxRequest5 = A.Game.get({action: 'spotlight', id: user.id});
		  $scope.ajaxRequest5.$promise.then(function(){											
				spotlight = $scope.ajaxRequest5.spotlight;
				console.log(spotlight);
				
				$rootScope.spotlight = spotlight;
				
		  },
		  function(){}
		  )		 
		}
		catch (err) {
			console.log("Error " + err);
		}	
	}
	if(spotlight == ''){
		spot();
	}	else {
		$rootScope.spotlight = spotlight;	
		spot();
	}

	function unreadMessageCount(id){
		try {	
		  $scope.ajaxRequest = A.Device.get({action: 'unreadMessageCount', id: id});
		  $scope.ajaxRequest.$promise.then(function(){											
				$rootScope.me.unreadMessagesCount = $scope.ajaxRequest.unreadMessageCount;
				if($rootScope.me.unreadMessagesCount > 0){
					$rootScope.unreadMessage = true;
				} else {
					$rootScope.unreadMessage = false;
				}								
				$localstorage.setObject('user', $rootScope.me); 
		  },
		  function(){}
		  )		 
		}
		catch (err) {
		console.log("Error " + err);
		}	
	} 
	unreadMessageCount(user.id);		
		
  })

  .controller('MessagingCtrl', function($state,$scope,$window,$rootScope,$ionicPlatform,$interval,$ionicViewSwitcher,A, $stateParams, Giphy, $ionicScrollDelegate, $timeout, $ionicActionSheet,Navigation,currentUser,$localstorage,$ionicHistory,$ionicPopup,$cordovaCamera) {	
	user = $localstorage.getObject('user');
	alang = $localstorage.getObject('alang');
	config = $localstorage.getObject('config');
	$rootScope.appGifts = $localstorage.getObject('gifts');	
	//currentUser.selectedUser = user;
	if(currentUser.selectedUser){
		chatUser = currentUser.selectedUser;
	} else {
		$state.go('loader');
	}

	$scope.photo = currentUser.selectedUser.photo;
	
	if(currentUser.selectedUser.photo === undefined){
		$scope.photo = currentUser.selectedUser.profile_photo;
	}	

	if(window.cordova){
		$ionicViewSwitcher.nextDirection("forward");
	} else {
		$ionicViewSwitcher.nextDirection("back");
	}
	
	url = 'inchat';
	if (window.cordova) {
		$scope.app = true;
	}
	var gifts = $localstorage.getObject('gifts');
	alang = $localstorage.getObject('alang');
	site_prices = $localstorage.getObject('prices');
	$scope.dailychatprice = site_prices.chat;
	$scope.alang = [];
	$scope.lang = [];
	$scope.focusInput = false;
	$scope.wait = false;
	$scope.viewCredits = false;
	$scope.userCredits = currentUser.selectedUser.credits;
	if(plugins['chat']['viewUserCredits'] == 'Yes'){
		$scope.viewCredits = true;
	}	

	var stickerHeight = $window.innerHeight-300;
	$('.stickers-top').css('height',stickerHeight+'px');

	//ADMOB
	/*
	if(show_ad == max_ad){
		if(window.AdMob) window.AdMob.prepareInterstitial( {adId:adMobI, autoShow:true} );
		
		show_ad = 0;	
	}
	show_ad++;*/

	angular.forEach(alang,function(entry) {						  
	  $scope.alang.push({
		id: entry,
		text: entry.text
	  });
	});

	angular.forEach(lang,function(entry) {						  
	  $scope.lang.push({
		id: entry,
		text: entry.text
	  });
	});

	$scope.gifts = gifts;
	$scope.sendGiftShow = false;
	


	$scope.buyDailyChat = function(){
		user.credits = parseInt(user.credits);
		if(site_prices.chat > user.credits){
			$scope.openCreditsModal();
		} else {
			var ma = user.id + ','+ site_prices.chat;
			$scope.chatLimit = false;
			try {	
			  $scope.ajaxRequest = A.Query.get({action: 'chat_limit', query: ma});
			  $scope.ajaxRequest.$promise.then(function(){		
			  	$localstorage.setObject('user',$scope.ajaxRequest.user);
	            var data = [];
	            data.name = '';
	            data.icon = site_theme['notification_inapp_credits_icon']['val'];
	            data.message = lang[610].text+' '+site_prices.chat+' ' + lang[73].text;
				$rootScope.pushNotif(data,1);			  	
			  },
			  function(){}
			  )		 
			}
			catch (err) {
				console.log("Error " + err);
			}
		}
	};
	

	$scope.processFiles = function(files){
	angular.forEach(files, function(flowFile, i){
	   var fileReader = new FileReader();
	      fileReader.onload = function (event) {
	        var uri = event.target.result;
				var image = uri;
				$scope.$apply(function () {
				  $scope.nmessages.push({
					isMe: true,
					seen:1,
					type: 'image',
					body: image
				  });
				});
				viewScroll.scrollBottom(true);   			
				con = true;
				$.ajax({
					url: site_url+'assets/sources/appupload.php',
					data:{
						action: 'sendChat',
						base64: image,
						uid: user.id,
						rid: currentUser.selectedUser.id
					},
					cache: false,
					contentType: "application/x-www-form-urlencoded",				  
					type:"post",
					dataType:'JSON',
					success:function(){
					}
				});	                
	      };
	      fileReader.readAsDataURL(flowFile.file);
	});
	};	

	$scope.sendPhoto = function(x){
		if (window.cordova) {
		if(x == 1){
			var options = {
				quality: 40,
				destinationType: Camera.DestinationType.DATA_URL,
				sourceType: Camera.PictureSourceType.PHOTOLIBRARY,
				encodingType: Camera.EncodingType.JPEG,
				allowEdit : false,
			};
		}else {
			var options = {
				quality: 40,
				destinationType: Camera.DestinationType.DATA_URL,
				encodingType: Camera.EncodingType.JPEG,
				allowEdit : false,
			};
		}

		$cordovaCamera.getPicture(options).then(function(imageData) {
			var image = "data:image/jpeg;base64," + imageData;
			  $scope.nmessages.push({
				isMe: true,
				seen:1,
				type: 'image',
				body: image
			  });
			$.ajax({
				url: site_url+'assets/sources/appupload.php',
				data:{
					action: 'sendChat',
					base64: image,
					uid: user.id,
					rid: currentUser.selectedUser.id
				},
				cache: false,
				contentType: "application/x-www-form-urlencoded",				  
				type:"post",
				dataType:'JSON',
				success:function(response){
					
				}
			});
		}, function(err) {
		  // error
		});	
		} else {
			$('#uploadSendPhoto').click();
		}	
	}
	$scope.sendGift = function(icon,price){
		$scope.gift_icon = icon;
		$scope.gift_price = price;
		user.credits = parseInt(user.credits);
		if(user.credits < price){
			$scope.openCreditsModal("'"+user.profile_photo+"'");
		} else {
			$scope.sendGiftShow = true;
		}
	}
	$scope.cancelGift = function(){		
		$scope.sendGiftShow = false;
	}	
	$scope.changePage = function(url,slide,val) {
		$state.go(url, val);  
	};
	$interval.cancel(chatInterval);

	/*
	$scope.goToChat = function(){
		$ionicViewSwitcher.nextDirection('back'); // 'forward', 'back', etc.
		$state.go('home.matches');		
	}
	*/
	$scope.actions = true;
	$scope.visible = function(val){
		if(val == 1){
			$scope.actions = false;	
		} else {
			$scope.isGifShown = false;
		}
	}
	var bIds = {};	
	
	$scope.showm = 15;
	
	$scope.loadMoreMen = function(more){
		var total = more + $scope.showm;
		var totalMe = $scope.totalMen - more;
		if(totalMe <= 0 ){
			totalMe = 0;	
			$scope.moreMen = false;
		}
		$scope.totalMen = totalMe;
		$scope.showm = total;
	}
	
	var w = window.innerWidth;
	w = w/2;
	if(w > 200){
		w = 200;
	}
	$scope.w = w;
	var premium = 0;
	var blocked = 0;
	$scope.maxDaily = false;
	$scope.chatLimit = false;
    var viewScroll = $ionicScrollDelegate.$getByHandle('userMessageScroll');
	$scope.messages = [];
	$scope.nmessages = [];   
	$scope.loader = true; 
	var me = 0;
	var you = 0;
	$scope.wait = false;
	var getChat = function (id) {
		try {	
		  $scope.ajaxRequest = A.Chat.get({action: 'userChat', uid1: user.id, uid2: id});
		  $scope.ajaxRequest.$promise.then(function(){		
		  $scope.messages=$scope.ajaxRequest.chat;
		  console.log($scope.messages);
		  if($scope.messages == undefined){
		  	$scope.messages = [];
		  }
		  premium = $scope.ajaxRequest.premium;
		  blocked = $scope.ajaxRequest.blocked;
			angular.forEach($scope.ajaxRequest.chat,function(entry) {					  
				if(entry.isMe == true){
					me++;
				} else {
					you++;
				}
			});			  
			if(me >= 2 && you == 0){
				$scope.wait = true;
			}
			if(blocked == 1){
				var confirmPopup = $ionicPopup.confirm({
				 title: alang[12].text+' '+ currentUser.selectedUser.name,
				 template: currentUser.selectedUser.name +' ' + alang[13].text
				});
				confirmPopup.then(function(res) {
				 if(res) {
					$ionicHistory.goBack();
				 }
				});				  
		 	}
			if(premium == 1){
			 $scope.chatLimit = true;
			}
			if($scope.messages === undefined || $scope.messages.length == 0) {
			  $scope.focusInput = true;
			  $scope.loader = false;	
			  return false;
			}
			if($scope.messages.length > 15){
				$scope.moreMen = true;
				$scope.totalMen = $scope.messages.length - 15;
			}
			console.log($scope.messages.length);
			$scope.loader = false;
			
			$timeout(function() {
				viewScroll.scrollBottom(true);
			}, 750);

			},
			function(){

			}
		  )		 
		}
		catch (err) {
			console.log("Error " + err);
		}	
	}



	var sendMessage = function (val) {
		try {	
			if ($scope.messages === undefined || $scope.messages.length == 0 && $scope.nmessages === undefined || $scope.nmessages.length == 0) {
				 A.Query.get({action: 'today', query: user.id});
			}	

			if(plugins['chat']['transferCreditsMessageToReciever'] == 'Yes' && plugins['chat']['creditsPerMessageGender'] == user.gender){
                $rootScope.updateCredits(currentUser.selectedUser.id,plugins['chat']['creditsPerMessage'],2,'Credits for message recieved');
            }

			if(plugins['chat']['transferCreditsMessageToReciever'] == 'Yes' && plugins['chat']['creditsPerMessageGender'] == allG){
                $rootScope.updateCredits(currentUser.selectedUser.id,plugins['chat']['creditsPerMessage'],2,'Credits for message recieved');                          
            }   


			$scope.ajaxRequest2 = A.Query.get({action: 'sendMessage', query: val});
			$scope.ajaxRequest2.$promise.then(function(){	

		  	},
		  function(){}
		  )		 
		}
		catch (err) {
			console.log("Error " + err);
		}	
	} 	

	
	$scope.name = currentUser.selectedUser.name;

	$scope.age = currentUser.selectedUser.age;
	$scope.city = currentUser.selectedUser.city;
	$scope.id = currentUser.selectedUser.id;
	current_user_id = currentUser.selectedUser.id;
	$scope.status = false;
	$scope.chatLimit = false;
	getChat(currentUser.selectedUser.id);	

	if(currentUser.selectedUser.status == 1 || currentUser.selectedUser.status == 'y'){
		$scope.status = true;
	}	
    $scope.isNew = false;
    $scope.gifs = [];
    $scope.gifQuery = '';
    $scope.isGifShown = false;
    $scope.isGiftShown = false;
    $scope.isGifLoading = false;

	if(goToChatGlobalAction == 'Gift'){
		$scope.isGiftShown = true;	
	}

    $scope.message = '';
	var sendNewChat = 0;

	var tt = true;
	var sent = false;

	function updateLastTypedTime() {
	    lastTypedTime = new Date();
	}	
	

	$scope.writing = false;


	var textarea = $('#chat-input-textarea');
	var typingDelayMillis = 750; // how long user can "think about his spelling" before we show "No one is typing -blank space." message
	updateLastTypedTime();
	$scope.refreshTypingStatus = function(val) {
	    if (val.length < 2) {
			var message = user.id+','+currentUser.selectedUser.id+','+0;
			A.RT.get({action: 'typing', query: message});
			console.log('no typing');
	    } else {
	    	var t = new Date().getTime() - lastTypedTime.getTime();
	    	t = parseInt(t);
	    	if( t > typingDelayMillis){
		    	updateLastTypedTime();
		    	console.log('typing more');
		    	var message = user.id+','+currentUser.selectedUser.id+','+1;
				A.RT.get({action: 'typing', query: message});
			}	    	
	    	console.log('waiting');
	    }
	}
	//setInterval(refreshTypingStatus, 1000);
	//textarea.keypress(refreshTypingStatus);
	//textarea.blur(refreshTypingStatus);
	var typing = 'typing'+user.id+chatUser.id;
	channel.unbind();
    channel.bind(typing, function(data) {
    	$scope.$apply(function () {
	    	if(data.t == 1){
	    		$scope.writing = true;  
	    	} else {
				$scope.writing = false; 
	    	}
    	})  
    });	
    	
	var event = 'chat'+user.id+chatUser.id;
    channel.bind(event, function(data) {
	  sendNewChat = $scope.nmessages.length + 1;
		$scope.$apply(function () {
			$scope.nmessages.push({
				isMe: false,
				seen:1,
				type: data.type,
				body: data.message
			});
			$scope.writing = false;
			var query = user.id+','+currentUser.selectedUser.id;
			A.Query.get({action: 'messageRead' ,query: query});				      
	  	})
		if (window.cordova) {
			//$rootScope.playSound('inchat');
			$rootScope.playAudio('inchat-sound');
		} else {
			//$('#chatSound')[0].play();
			$rootScope.playAudio('inchat-sound');
		}
	  	you++;
		$scope.wait = false;
	  	viewScroll.scrollBottom(true);      
    });		

    $scope.sendText = function(m) {

		if(plugins['chat']['creditsPerMessageEnabled'] == 'Yes'){

            if(user.credits < plugins['chat']['creditsPerMessage']){ 
                $scope.openCreditsModal("'"+user.profile_photo+"'");
                return false;
            }	
            
            var data = [];
            data.name = '';
            data.icon = site_theme['notification_inapp_credits_icon']['val'];
            data.message = lang[610].text+' '+plugins['chat']['creditsPerMessage']+' ' + lang[73].text;
            
            
			if(plugins['chat']['creditsPerMessageGender'] == user.gender){
				$rootScope.pushNotif(data,1);
				$rootScope.updateCredits(user.id,plugins['chat']['creditsPerMessage'],1,'Credits for send chat message');
			}

			if(plugins['chat']['creditsPerMessageGender'] == allG){
				$rootScope.pushNotif(data,1);
				$rootScope.updateCredits(user.id,plugins['chat']['creditsPerMessage'],1,'Credits for send chat message');
			}		

		}

		var message = user.id+'[message]'+currentUser.selectedUser.id+'[message]'+m+'[message]text';  
		sendMessage(message);

		sent = true;
		sendNewChat = $scope.nmessages.length + 1;
		$scope.nmessages.push({
			isMe: true,
			seen:0,
			type: 'text',
			body: m
		});
		
		var send = user.id+'[rt]'+currentUser.selectedUser.id+'[rt]'+user.profile_photo+'[rt]'+user.first_name+'[rt]'+m+'[rt]text';      
		A.RT.get({action: 'message', query: send});	

		var t = user.id+','+currentUser.selectedUser.id+','+0;
		A.RT.get({action: 'typing', query: t});
		console.log('no typing');	  
		viewScroll.scrollBottom(true);
		me++;
		if(me >= 2 && you == 0){
			$scope.wait = true;
		} else {
			$scope.wait = false;
		}	


    }

    $scope.newGif = function(newValue) {
      if (newValue.length) {
        $scope.isGifLoading = true;
        $scope.gifs = [];

        Giphy.search(newValue)
          .then(function(gifs) {
            $scope.gifs = gifs;
            $scope.isGifLoading = false;
          })
      } else {
        _initGiphy();
      }
    }

    $scope.sendGif = function(imageUrl) {

		if(plugins['chat']['creditsPerMessageEnabled'] == 'Yes'){

            if(user.credits < plugins['chat']['creditsPerMessage']){ 
                $scope.openCreditsModal("'"+user.profile_photo+"'");
                return false;
            }	
            
            var data = [];
            data.name = '';
            data.icon = site_theme['notification_inapp_credits_icon']['val'];
            data.message = lang[610].text+' '+plugins['chat']['creditsPerMessage']+' ' + lang[73].text;
            
            
			if(plugins['chat']['creditsPerMessageGender'] == user.gender){
				$rootScope.pushNotif(data,1);
				$rootScope.updateCredits(user.id,plugins['chat']['creditsPerMessage'],1,'Credits for send chat message');
			}
			if(plugins['chat']['creditsPerMessageGender'] == allG){
				$rootScope.pushNotif(data,1);
				$rootScope.updateCredits(user.id,plugins['chat']['creditsPerMessage'],1,'Credits for send chat message');
			}				
		}
		   
		var message = user.id+'[message]'+currentUser.selectedUser.id+'[message]'+imageUrl+'[message]gif';
		sendMessage(message);

		$scope.nmessages.push({
			isMe: true,
			type: 'image',
			body: imageUrl
		});

		var gifData = imageUrl.split(".gif");
		imageUrl = gifData[0]+'.gif';
		var send = user.id+'[rt]'+currentUser.selectedUser.id+'[rt]'+user.profile_photo+'[rt]'+user.first_name+'[rt]'+imageUrl+'[rt]gif';      
		A.RT.get({action: 'message', query: send});	   


		$scope.cmen = '';
		$scope.isGifShown = false;
		$timeout(function() {
			viewScroll.scrollBottom(true);
		}, 100);

    }
	
    $scope.sendGiftBtn = function(imageUrl,price) {
		var m = '<img src="'+imageUrl+'"/>';
		$scope.nmessages.push({
		isMe: true,
		type: 'text',
		body: '<img src="'+imageUrl+'"/>'
		});
		var send = user.id+'[rt]'+currentUser.selectedUser.id+'[rt]'+user.profile_photo+'[rt]'+user.first_name+'[rt]'+imageUrl+'[rt]image';      
		A.RT.get({action: 'message', query: send});      
		var message = user.id+'[message]'+currentUser.selectedUser.id+'[message]'+imageUrl+'[message]gift[message]'+price;
		sendMessage(message);
		$scope.cmen = '';
		$scope.isGiftShown = false;
		viewScroll.scrollBottom(true);

		if(plugins['chat']['transferCreditsGiftToReciever'] == 'Yes'){
			$rootScope.updateCredits(currentUser.selectedUser.id,price,2,'Credits for like');              
		}	 

    }	

    $scope.openGiphy = function() {
		if($scope.isGifShown == true){
			$scope.isGifShown = false; 
		} else {
	  		$scope.isGifShown = true; 		
		}
		$scope.isGiftShown = false;      
		$scope.actions = true;
		$scope.message = '';
    }
    $scope.openGift = function() {
		if($scope.isGiftShown == true){
			$scope.isGiftShown = false; 
		} else {
				$scope.isGiftShown = true; 		
		}
		$scope.isGifShown = false;      
		$scope.actions = true;
		$scope.message = '';
    }

    $scope.closeGifts = function(){
    	$('.stickers').hide();
    	$scope.isGiftShown = false;
    }

    $rootScope.showStickers = false;
    $scope.openStickers = function() {
	if($rootScope.showStickers == true){
		$rootScope.showStickers = false; 
	} else {
  		$rootScope.showStickers = true; 		
	}
	  $scope.isGifShown = false;      
	  $scope.actions = true;
      $scope.message = '';
    }    	
	
    $scope.closeGift = function() {
      $scope.isGiftShown = false;
    }		
	
    $scope.closeGiphy = function() {
      $scope.isGifShown = false;
      $scope.message = '';
    }	

    var _scrollBottom = function(target) {
      target = target || '#type-area';

      viewScroll.scrollBottom(true);
      _keepKeyboardOpen(target);
      if ($scope.isNew) $scope.isNew = false;
    }

    // Warning: Demo purpose only. Stay away from DOM manipulating like this
    var _keepKeyboardOpen = function(target) {
      target = target || '#type-area';

      txtInput = angular.element(document.body.querySelector(target));
      console.log('keepKeyboardOpen ' + target);
      txtInput.one('blur', function() {
        console.log('textarea blur, focus back on it');
        txtInput[0].focus();
      });
    }

    console.log(currentUser);
    // Show the action sheet
    $scope.showUserOptions = function() {
      var hideSheet = $ionicActionSheet.show({
		titleText: alang[14].text,									 
        buttons: [
          { text: alang[15].text },
          { text: alang[16].text },
          { text: alang[17].text +' '+currentUser.selectedUser.name }
        ],
        cancelText: alang[2].text,
        cancel: function() {
            // add cancel code..
          },
        buttonClicked: function(index) {
			if(index == 0){
				$rootScope.openProfileModal(currentUser.selectedUser.id,currentUser.selectedUser.name,$scope.photo,currentUser.selectedUser.age,currentUser.selectedUser.city,currentUser.selectedUser.status);			}
			if(index == 1){
				var query = user.id+','+currentUser.selectedUser.id;
				A.Query.get({action: 'del_conv' ,query: query});
				$state.go('home.matches');
			}
			if(index == 2){
			   var confirmPopup = $ionicPopup.confirm({
				 title: alang[17].text+' '+ currentUser.selectedUser.name,
				 template: alang[18].text +' '+ currentUser.selectedUser.name +'?'
			   });
			
			   confirmPopup.then(function(res) {
				 if(res) {
					var query = user.id+','+currentUser.selectedUser.id;
					A.Query.get({action: 'block' ,query: query});
					$timeout(function(){
						$state.go('home.matches');
					},550);
				 } else {
				   
				 }
			   });
			 };	
			
          return true;
        }
      });
    }

    // Onload
	var _initGiphy = function() {
	  Giphy.trending()
	    .then(function(gifs) {
	      $scope.gifs = gifs;
	    });
	}
	_initGiphy();
	})


	function goToProfile(id){
		angular.element(document.querySelector('#storyUser'+id)).triggerHandler('click');
	}

	function isDate18orMoreYearsOld(day, month, year) {
		var yearVal = parseInt(year)+plugins['settings']['minRegisterAge'];	
	    return new Date(yearVal, month-1, day,0,0,0,0) <= new Date();
	}

	function decodeHTMLEntities(text) {
	    var entities = [
	        ['amp', '&'],
	        ['apos', '\''],
	        ['#x27', '\''],
	        ['#x2F', '/'],
	        ['#39', '\''],
	        ['#47', '/'],
	        ['lt', '<'],
	        ['gt', '>'],
	        ['nbsp', ' '],
	        ['quot', '"']
	    ];

	    for (var i = 0, max = entities.length; i < max; ++i) 
	        text = text.replace(new RegExp('&'+entities[i][0]+';', 'g'), entities[i][1]);

	    return text;
	}
