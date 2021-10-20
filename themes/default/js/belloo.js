user_info.credits = parseInt(user_info.credits);
var game_array = new Array();
var addStoriesToAlbum = new Array();
var game_loading_profiles = false;
var meet_age_array = user_info.s_age.split(",");
var meet_age = meet_age_array[0]+','+meet_age_array[1];
var meet_gender = user_info.s_gender;
var meet_radius = user_info.s_radius;
var meet_online = 0;
var meet_limit = 0;
var eventBind,typingBind;
var height_2 = $(window).height();
var backspace_alert = 0;
var searchIndex = [];
var emojiInit = false;
var $flkty;
var d_url = 'about';
var offset;
var globalTimeout = null;
var in_videocall = false;
var search_users = false;
var endedVideocall = false;
var called = false;	
var peer;
var callId;
var payment_method = 0;
var video_user = 0;
var meet_pages = 0;	
var videocall_user = 0;	
var sec = 0;
var gift_price = 0;
var photos_count = 0;	
var timer;
var callSound;
var callLongWait;
var profile_url;
var current_user_chat;
var current_user;
var user_name;
var title = 0;	
var my_profile = 0;		
var galleria_photos;
var noti = 0;
var profile_slider_open = false;
var slider = 0;
var lastTypedTime = new Date(0);
var timeoutId = null;
var multipleUpload = true;
var currentStoryUserData= [];
var faviconInt = 0;
var game_array_max = 0;
var fixHeightInterval;
var adInterval;
var streamGiftCredits,streamGiftIcon;
var creditsPackage;
var premiumPackage;

window.favicon = new Favico({
	animation : 'popFade'
});

if(inIframe() !== true){
	$(window).on('load resize',function(){
	    if($(window).width() < 768){
	    	$('#goToMobile').show();
	        window.location = site_config.site_url+'mobile';
	    }
	}); 
}
var disableScroll = false;
var scrollPos = 0;
function stopScroll() {
    disableScroll = true;
    scrollPos = $(window).scrollTop();
}
function enableScroll() {
    disableScroll = false;
}

if(user_info.credits < 0){
	fixUserCredits();
}


//favicon notification


$('.js-trigger').on('click', function() {
    $('.chattt').toggleClass('show-me'); 
    $('.buttonChat').toggleClass('show-me'); 
});
$('[data-profile-popup]').click(function(){
  if( $(".User-Dropdown").hasClass( "U-open" ) ){
    $('.User-Dropdown').removeClass("U-open");
  }
  else {
    $('.User-Dropdown').addClass("U-open");
  }
});


var chatCo = $('#chatCount').text(); 
var visitCo = $('[data-visit-count]').text(); 
if(chatCo > 0){
    $('#chatCount').show();
}
if(visitCo > 0){
    $('[data-visit-count]').show();
}

var fullscreen = (function(document, console) {
    var methods = ["requestFullscreen", "mozRequestFullScreen", "webkitRequestFullscreen", "msRequestFullscreen"],
        exits = ["exitFullscreen", "mozCancelFullScreen", "webkitExitFullscreen", "webkitCancelFullScreen", "msExitFullscreen"],
        img = new Image,
        method = methods.filter(function(m) {
            return m in img
        }).shift(),
        exit = exits.filter(function(m) {
            return m in document
        }).shift(),
        fun = function(el,leaveFullScreen) {
            method && el[method]()
            $(document).one("leftfullscreen", leaveFullScreen)
        },
        isFull = fun.isFull = function() {
            return document.fullscreen || document.mozFullScreen || document.webkitIsFullScreen || document.msFullscreenElement
        }
    $(document).on("webkitfullscreenchange mozfullscreenchange MSFullscreenChange msfullscreenchange fullscreenchange", function() {
        //             console.log(["fsc",e.type,fullscreen.isFull()])
        if(!isFull())
            $(document).trigger("leftfullscreen")
        else
            $(document).trigger("enterfullscreen")
    })
    return fun
})(document, console)


var tempVisitUsersArr = new Array();
var tempLikeUsersArr = new Array();
function checkFUR(){
	var check = Math.floor(Math.random()*(100-1+1)+1);

	if(plugins['fakeUsersInteractions']['enabled'] == 'Yes'){
		if(plugins['fakeUsersInteractions']['visitBackChance'] > check && profile_info.status == "y" && profile_info.fake == 1 && profile_info.id != user_info.id){
			var time = Math.floor(Math.random()*(45000-5000+1)+5000);
			var randId = Math.random();
			var thisUserId = profile_info.id,thisUserName = profile_info.first_name,thisUserPhoto=profile_info.profile_photo;
			tempVisitUsersArr[randId] = profile_info.id; 
			setTimeout(function(){
				var addvisit = tempVisitUsersArr[randId]+','+user_info.id;
				var randomNumber = Math.random();
				tempLikeUsersArr[randomNumber] = tempVisitUsersArr[randId]; 
				//interaction(thisUserId,thisUserName,thisUserPhoto,site_lang[657]['text'],'visit');
				$.get( aUrl, { action: 'addVisit', query: addvisit } );
				if(plugins['fakeUsersInteractions']['likeVisitorChance'] > check && profile_info.status == "y" && profile_info.fake == 1){
					var time2 = Math.floor(Math.random()*(15000-3000+1)+3000);
					setTimeout(function(){
						$.get( aUrl, { action: 'game_like', uid1: tempLikeUsersArr[randomNumber], uid2: user_info.id, uid3: 1 } );		
					},time2);
				}										
			},time);
		}
	}	
}




function startInactivityCheck() {
    timeoutId = window.setTimeout(function(){
    	if(url != 'live'){
			rt.disconnect();
    	}
    }, 3 * 60 * 1000);
};

startInactivityCheck();
function userActivityDetected(){
	console.log('activity detected');
    if(timeoutId !== null) {
        window.clearTimeout(timeoutId);
    }
    startInactivityCheck();
};

$(document).keyup(function(e) {
  if (e.keyCode === 27) {
  }
});	

function upMedia(val=''){
    upType = 1;

    if(val == 'storyAlbum'){
    	upType = 6;
    }
    if(val == 'editStoryAlbum'){
    	upType = 7;
    }    
	document.getElementById("uploadContent").click();
}

function upProfilePoto(){
    upType = 5;
	document.getElementById("uploadContent").click();
	multipleUpload = false;
}



$('.grayScale').hover(function(){
	$(this).removeClass('grayScale'); 
  },function(){
	$(this).addClass('grayScale'); 
});
function closeSlider(){
	if ($('.slider-profile').hasClass('opened')) {
		slider.classList.add('closed');
		slider.classList.remove('opened');
		$('.close-slider-btn').hide();
	}	
}
slider = document.querySelector('.slider-profile');
var getRandomItem = function (list) {
  return Math.floor(Math.random()*list.length);
}
var Notifications = function(data) {
  this.name = data.name;
  this.message = data.message;
  this.picture = data.icon;
  this.bg = data.background;
  this.color = data.color;
  this.elem;
  this.duration = 4000;
  this.create();
  this.animOptions = {
    duration: 400,
    easing: 'easeInOut'
  } 
  openWidget('userInteraction');  
  this.show = function() {
    $(this.elem).velocity(
      {
        opacity: [1, 0],
        marginTop: [0, 20]
      }, this.animOptions
    );
    this.hide();
  }
  this.hide = function() {
    var opts = this.animOptions;
    opts['delay'] = this.duration;
    opts['display'] = 'none';
    $(this.elem).velocity(
      {
        opacity: [0, 1],
        marginTop: [-75, 0]
      }, opts
    );
  }
};
Notifications.prototype.create = function() {
	var box =document.createElement('div');
    box.classList.add('n-box');
    var close = document.createElement('div');
    close.classList.add('n-close');
    close.innerHTML = '';
    var picture =  document.createElement('div');
    picture.classList.add('n-picture');
    picture.style.backgroundImage = "url('"+this.picture+"')";
    var body = document.createElement('div');
    body.classList.add('n-body');
    var message = document.createElement('span');
    message.classList.add('n-message');
    message.style.color = site_theme['notification_inapp_credits_color']['val'];
    box.style.background = site_theme['notification_inapp_credits_bg']['val'];
    message.innerHTML = this.message;
    box.appendChild(picture);
    box.appendChild(message);
    var wrapper = document.getElementById('middleContent'); 
    wrapper.appendChild(box);
    $('#middleContent').show();
    profilePhoto();
    this.elem = box;
}
var Webnotifications = [];
function pushNotif(data,type=0){

	//credit notification
	if(type==1){
		var n = new Notifications(data);
		n.show();
		Webnotifications.push(n);
		//$('#coinSound')[0].play();  	
	}

	//user interaction
	if(type==2){
		var notifyId = Math.floor(Math.random() * 10000000);
		if(!$('[data-widget="userInteraction"]').hasClass('lw-open')) {
			openWidget('userInteraction');
		}


		var actionButtons = '';

		if(site_theme['notification_inapp_actions_emoji']['val'] == 'Yes'){
			actionButtons = `
	        	<button class="lw-btn lw-btn_color" style="color:`+data.btnColor+`" 
	        		onclick="closeWidgetItem(`+notifyId+`); goToProfile(`+data.id+`);" data-lw-ripple>
	            	👀
	        	</button>
	        	<button class="lw-btn lw-btn_color" style="color:`+data.btnColor+`"
	        	 onclick="closeWidgetItem(`+notifyId+`); goToChat(`+data.id+`);" data-lw-ripple>
	        		<span class="wave">👋</span>
	        	</button>
	        	<button class="lw-btn lw-btn_color" style="color:`+data.btnColor+`"
	        	 onclick="closeWidgetItem(`+notifyId+`); likeFromNotification(`+data.id+`);" data-lw-ripple>
	        		😍
	        	</button>
			`;
		} else {
			actionButtons = `
	        	<button class="lw-btn lw-btn_color" style="color:`+data.btnColor+`" 
	        	 onclick="closeWidgetItem(`+notifyId+`); goToProfile(`+data.id+`);" data-lw-ripple>
	            	`+site_lang[46]['text']+`
	        	</button>
	        	<button class="lw-btn lw-btn_color" style="color:`+data.btnColor+`"
	        	 onclick="closeWidgetItem(`+notifyId+`); goToChat(`+data.id+`);" data-lw-ripple>
	        		`+site_lang[466]['text']+`
	        	</button>
	        	<button class="lw-btn lw-btn_color" style="color:`+data.btnColor+`" 
	        		onclick="closeWidgetItem(`+notifyId+`); likeFromNotification(`+data.id+`);" data-lw-ripple>
	        		`+site_lang[298]['text']+`
	        	</button>
			`;
		}	

		$('[data-widget-container="userInteraction"]').append(`
	        <div class="lw-item lw-item_white" data-widget-remove="`+notifyId+`" data-user-notification="`+data.id+data.action+`" style="background: `+data.background+`;">
	            <button class="lw-close" onclick="closeWidgetItem(`+notifyId+`);" >
	                <i class="icon icon--white ion-close-round"></i>
	            </button>
	            <div class="lw-wrap">
	                <div class="lw-user">
                    	<div class="lw-preview lw-pic profile-photo box-shadow interactionPhoto" style="border-radius: 5px"
                    	data-src="`+data.icon+`"></div>
	                    <div class="lw-group">
	                        <div class="lw-content" style="color:`+data.color+`"><strong>`+data.name+`</strong> `+data.message+`</div>
	                    </div>
	                </div>
	            </div>
	            <div class="lw-foot lw-foot_flex" style="background:`+data.btnBg+`;">
	            	`+actionButtons+`
	            </div>
	        </div>
		`);	

		loadWidgetItem('userInteraction');
		profilePhoto();

	}	

	//browser notification
	if(type==0){
		Notification.requestPermission(function(log) {
			console.log(log);
			if (log == "granted") {
			 var n = new Notification( data.name,{ 
			    body: data.message,
			    icon : data.icon
			 });
				n.onclick = function(){
					window.location.href = site_url()+'chat/'+data.id+'/notification';
					window.focus();
					n.close();
				};         
			 } else {
			  var n = new Notifications(data);
			  n.show();
			  Webnotifications.push(n);         	
			 }
		}); 	
	}

};

var typingDelayMillis = 750; // how long user can "think about his spelling" before we show "No one is typing -blank space." messag
var t = false;


var rtcallback = function(data) {
	if(url == 'chat'){
		if(data.notification_chat != false){
			$('#usersFriends').html(data.notification_chat);
			scroller();
			profilePhoto();
			if(data.id == current_user_id){
				$('[data-chat]').removeClass('brick--outline');
				$('#user'+data.id).addClass('brick--outline');	
				$('#mark'+data.id).remove();				
			}
		}					
	}	   
	if(data.id != current_user_id){	
		$('#notiSound')[0].play();		
		var a = parseInt(data.unread);								  
		title = a;
		faviconInt = faviconInt+a;
		window.favicon.badge(faviconInt);
		$('#chatCount').text(title); 
		$('#chatCount').fadeIn();
		
		$('#user'+current_user_id).addClass('brick--outline');	
		document.title = '( '+title+' ) '+ site_title();
		var notiEl = $('[data-user-notification='+data.id+data.action+']')[0];

		if(url == 'chat'){
			var dataNotif = [];
			dataNotif.name = data.name;
			dataNotif.icon = data.icon;
			dataNotif.message = data.message;
			pushNotif(dataNotif,1);
		} else {
			if (!notiEl) {
			    interaction(data.id,data.name,data.icon,data.message,data.action);
			} 			
		}			
	}	
};

var rtnotification = 'notification'+user_info.id;
channel.bind(rtnotification, rtcallback);	



var rtvisit = 'visit'+user_info.id;
channel.bind(rtvisit, function(data) {
	console.log(data);
	faviconInt = faviconInt+1;
	window.favicon.badge(faviconInt);
	var notiEl = $('[data-user-notification='+data.id+data.action+']')[0];
	if (!notiEl) {

		if(url == 'chat' && current_user == data.id){
			var dataNotif = [];
			dataNotif.name = data.name;
			dataNotif.icon = data.icon;
			dataNotif.message = data.message;
			pushNotif(dataNotif,1);			
		} else {
			$('#notiSound')[0].play();
			interaction(data.id,data.name,data.icon,data.message,data.action);
		}
	   
	}									
});	

var rtlike = 'like'+user_info.id;
channel.bind(rtlike, function(data) {
	faviconInt = faviconInt+1;
	window.favicon.badge(faviconInt);	
	var notiEl = $('[data-user-notification='+data.id+data.action+']')[0];
	if (!notiEl) {
		$('#notiSound')[0].play();
	    interaction(data.id,data.name,data.icon,data.message,data.action);
	}					
});	


if(url != 'discover'){
	//updateDiscoverResults();
}

function updateSeo(url){
	if(seo_lang[url] != undefined){
		if(url == 'profile'){
			if(profile_info.name != undefined){
				document.title = profile_info.name + ", " + profile_info.age + ", " + profile_info.city + " | " + seo_lang[url][1]['text'];
				$("meta[property='og\\:title']").attr('content',  profile_info.name + ", " + profile_info.age + ", " + profile_info.city + " | " + seo_lang[url][1]['text']);
				if(profile_info.username == ''){
					profile_info.username = profile_info.id;
				}
				$("meta[property='og\\:url']").attr('content',  site_config.site_url+'@'+profile_info.username);
				$("meta[property='og\\:image']").attr('content',  profile_info.profile_photo);
				$("meta[property='og\\:description']").attr('content',  seo_lang[url][2]['text']);
			}
		} else {
			document.title = seo_lang[url][1]['text'];	
		}
		
		$('meta[name="keywords"]').attr('content', seo_lang[url][3]['text']);
		$('meta[name="description"]').attr('content', seo_lang[url][2]['text']);
	}
}

switch (url) {

	case "profile":

		$("meta[property='og\\:url']").attr("content",site_config.site_url+'@'+profile_info.username);					
		$("meta[property='og\\:title']").attr("content",profile_info.name + ", " + profile_info.age + " | " + site_config.name);					
		$("meta[property='og\\:image']").attr("content",profile_info.profile_photo);

		$('#data-content').css("opacity","1");		
		$('#leftProfile').show();
		window.history.pushState("profile",profile_info.name + ", " + profile_info.age + " | " + site_title(),site_config.site_url+'@'+profile_info.username);							
		$('#data-content').css("opacity","1");
		
		$('.profile-content').css("overflow-y","hidden");	
		setTimeout(function(){
			profileLinks();
			game_btns2();
			profilePhoto();
			fullHeightWall();
			$('.profile-content').css("overflow-y","auto");
			var profileContentHeight = $('.profile-content').height();
			console.log(profileContentHeight);
			var footerBottom = profileContentHeight - 241;
			//$('.footer-content').addClass('footer-content-profile');
			//$('.footer-content').css('bottom',-footerBottom+'px');			
		},450);
		checkFUR();
		updateSeo('profile');
		userActivityDetected();
		$('[data-left-profile-photo]').attr('data-src',profile_info.profile_photo);
		storyLoader(profile_info.story,profile_info.stories,profile_info.status_info);
		checkScrollBars('hidden');
		
	break;

	case "live":
		fullHeightWall();
	break;

	case "discover":
		storyPage = 'discover';
		profilePhoto();
		updateDiscoverResults();
		discoverStoriesPreview();
		setTimeout(function(){
			game_start();
			game_btns2();
		},800);
		scroller();	
		profilePhoto();

		if(site_theme['design_style_wide']['val'] == 'Yes'){
			checkScrollBars('scroll');
		} else {
			checkScrollBars('scroll');	
		}
		fullHeightWall(85);
		userActivityDetected();	
		fadeOutStories();
		updateSeo('discover');			
	break;
	case 'meet':
		$('[data-wide-spotlight]').show();
		meetPagination();
		meetFilter();
		filterBtn();
		userActivityDetected();	

		if(plugins['meet']['viewOnlyPremium'] == 'Yes'){
			meetPremiumNotification(1);					
		}
		window.history.pushState("meet",site_title(),site_config.site_url+"meet");
		updateSeo('meet');
	break;

	case "popular":
		if(plugins['populars']['viewOnlyPremium'] == 'Yes'){
			meetPremiumNotification(1);					
		}
		updateSeo('popular');
	break;
	case "credits":
		//fullHeightWall();
		checkScrollBars('scroll');
		profilePhoto();
		updateSeo('credits');

	break;

	case "premium":
		//fullHeightWall();
		checkScrollBars('scroll');
		profilePhoto();
		updateSeo('premium');
	break;	

	case "cookies":
		checkScrollBars('scroll');
		updateSeo('cookies');
	break;	
	case "privacy":
		checkScrollBars('scroll');
		updateSeo('privacy');
	break;	
	case "terms":
		checkScrollBars('scroll');
		updateSeo('terms');
	break;			
	case "chat":
		checkScrollBars('hidden');
		if(site_theme['design_style']['val'] == 'Top-Menu'){
			fullHeightChat();
		} else {
			fullHeightChat(60);
		}
		
		if(profile_info.id > 0){
			$('#r_id').val(profile_info.id);
			$('#rid').val(profile_info.id);
			current_user = profile_info.id;
			current_user_id = profile_info.id;
			user_name = profile_info.name;	
			$('#user'+profile_info.id).addClass('brick--outline');				
			chatMessage();
			videocallBtn();
			giftBtn();
			userActivityDetected();
			$('[data-left-profile-photo]').attr('data-src',profile_info.profile_photo);
			profilePhoto();
			storyLoader(profile_info.story,profile_info.stories,profile_info.status_info);
		}
		updateSeo('chat');
	break;	

	case "settings":			
		profileForms();
		userActivityDetected();	
		profilePhoto();			
	break;			
	default:
	profilePhoto();			
	break;

}	



function discoverStoriesPreview(){
	$('[data-discover-story-video]').each(function(){
		var id = $(this).attr('data-discover-story-video');
	    var canvas = document.getElementById('videoStoryCanvas'+id);
	    var bgimage = document.getElementById('videoStoryBG'+id);
	    var video = document.getElementById('videoStory'+id);

	    var image = new Image();
	    var MAX_WIDTH = 180;
		var MAX_HEIGHT = 120;
	    var width = video.videoWidth;
	    var height = video.videoHeight;

	    if (width > height) {
	      if (width > MAX_WIDTH) {
	        height *= MAX_WIDTH / width;
	        width = MAX_WIDTH;
	      }
	    } else {
	      if (height > MAX_HEIGHT) {
	        width *= MAX_HEIGHT / height;
	        height = MAX_HEIGHT;
	      }
	    }

	    canvas.width = width;
	    canvas.height = height;
	    var ctx = canvas.getContext("2d");
	    ctx.scale(1,1);
	    ctx.drawImage(video, 0, 0, width, height);
	   
		image.id = "pic";
		image.src = canvas.toDataURL();
		bgimage.style.backgroundImage = "url("+image.src+")";	
	})	
}

function menuLinks(){
	$('[data-murl]').click(function(e){
		e.preventDefault();
		userActivityDetected();
		var murl = $(this).attr('data-murl');
		console.log('Current page '+murl);
		var menu = $(this);
		ajaxLoad(1);
		storyPage = '';
		$('#leftProfile').hide();
		$('.User-Dropdown').removeClass("U-open");
		updateSeo(murl);

		
		/*		
	    $.ajax({
	        url: request_source()+'/belloo.php', 
	        data: {
	            action:"online_now"
	        },  
	        type: "post",
	        success: function(response) {
	            $('.chat__wrapper').html(response.r);
	            $('#onlineFriendsCount').text(response.total)
	        }
	    }); */ 
	    $('[data-wide-spotlight]').hide(); 
	    
		switch (murl) {
			case "discover":	
				$('#data-content').css("opacity","0.5");
				if(site_theme['design_style_wide']['val'] == 'Yes'){
					checkScrollBars('scroll');
				} else {
					checkScrollBars('scroll');	
				}
				storyPage = 'discover';	
				profile_info = '';		
				$.ajax({
					url: request_source()+'/user.php', 
					data: {
						action:"discover"						
					},	
					type: "post",			
					success: function(response) {
						$('#data-content').html(response);
						window.history.pushState("discover",site_title(),site_config.site_url+"discover");
						game_start();
						game_btns2();	
						
						fullHeightWall(85);
						ajaxLoad(2);
						profilePhoto();
						scroller();

						fadeOutStories();
						
						$('#data-content').css("opacity","1");							
					},
				});				
			break;	

			case "live":
				$.ajax({
					url: request_source()+'/user.php', 
					data: {
						action:"live"						
					},	
					type: "post",			
					success: function(response) {
						$('#data-content').html(response);
						window.history.pushState("live",site_title(),site_config.site_url+"live");
						fullHeightWall();
						ajaxLoad(2);
						scroller();
						$("#sendLiveMessage").on('keyup', function (e) {
						    if (e.keyCode === 13) {
						        sendLiveMessage();
						    }
						});

						$('#data-content').css("opacity","1");							
					},
				});				
				
			break;		
			case "meet":	
				$('#data-content').css("opacity","0.5");
				checkScrollBars('scroll');
				profile_info = '';
				$.ajax({
					url: request_source()+'/user.php', 
					data: {
						action:"meet_back"						
					},	
					type: "post",			
					success: function(response) {
						$('#data-content').html(response);
						$('[data-wide-spotlight]').show();
						filterBtn();	
						scroller();
						profilePhoto();
						meetFilter();
						meetPagination();
						ajaxLoad(2);
						
						if(plugins['meet']['viewOnlyPremium'] == 'Yes'){
							meetPremiumNotification(1);					
						}						
						var dataHeight = $('body').height();
						//$('#site-content').css('height',dataHeight+'px');	
						if(site_theme['design_style_wide']['val'] == 'Yes'){
							$('.add-yourself').click(function(){
								$('#add-spotlight').show();
							});
						}		
						locInitialize();				
						window.history.pushState("meet",site_title(),site_config.site_url+'meet');							
						$('#data-content').css("opacity","1");							
					},
				});				
			break;
			case "popular":	
				$('#data-content').css("opacity","0.5");
				checkScrollBars('scroll');
				profile_info = '';
				$.ajax({
					url: request_source()+'/user.php', 
					data: {
						action:"popular"						
					},	
					type: "post",			
					success: function(response) {
						$('#data-content').html(response);	
						scroller();
						profilePhoto();
						ajaxLoad(2);
						if(plugins['populars']['viewOnlyPremium'] == 'Yes'){
							meetPremiumNotification(1);					
						}						
						window.history.pushState("populars",site_title(),site_config.site_url+"popular");							
						$('#data-content').css("opacity","1");							
					},
				});				
			break;
			case "credits":	
				$('#data-content').css("opacity","0.5");
				profile_info = '';
				$.ajax({
					url: request_source()+'/user.php', 
					data: {
						action:"credits"						
					},	
					type: "post",			
					success: function(response) {
						$('#data-content').html(response);	
						scroller();
						profilePhoto();
						ajaxLoad(2);
						//fullHeightWall();
						checkScrollBars('scroll');					
						window.history.pushState("populars",site_title(),site_config.site_url+"credits");							
						$('#data-content').css("opacity","1");							
					},
				});				
			break;
			case "withdraw":	
				$('#data-content').css("opacity","0.5");
				checkScrollBars('scroll');	
				profile_info = '';			
				$.ajax({
					url: request_source()+'/user.php', 
					data: {
						action:"withdraw"						
					},	
					type: "post",			
					success: function(response) {
						$('#data-content').html(response);	
						scroller();
						profilePhoto();
						ajaxLoad(2);
						window.history.pushState("withdraw",site_title(),site_config.site_url+"withdraw");							
						$('#data-content').css("opacity","1");							
					},
				});				
			break;			
			case "premium":	
				$('#data-content').css("opacity","0.5");
				checkScrollBars('scroll');
				profile_info = '';
				$.ajax({
					url: request_source()+'/user.php', 
					data: {
						action:"premium"						
					},	
					type: "post",			
					success: function(response) {
						$('#data-content').html(response);	
						scroller();
						profilePhoto();
						ajaxLoad(2);
						window.history.pushState("populars",site_title(),site_config.site_url+"premium");							
						$('#data-content').css("opacity","1");	
						$('[data-premium-send]').on('click', function(e){
							 e.preventDefault();
							var price = $(this).attr('data-price');
							var days = $(this).attr('data-premium-send');
							var months = days/30;
							$('#payment-custom3').val(user_info.id+','+days);
							$('#payment-amount3').val(price);
							$('#payment-time3').val(months);
							$('#payment-name3').val(site_config.name + ' - ' + days + ' ' + site_lang[332]['text']);

							$('#buy-premium').submit();
						});												
					},
				});				
			break;
			case "chat":	
				$('#data-content').css("opacity","0.5");
				title = 0;
				$('#chatCount').text(title); 
				$('#chatCount').hide();			
				checkScrollBars('hidden');										
				$.ajax({
					url: request_source()+'/user.php', 
					data: {
						action:"chat-menu"						
					},	
					type: "post",			
					success: function(response) {
						$('#data-content').html(response);
						if(noChat){
							$('#data-content').css("opacity","1");	
							window.history.pushState("",site_title(),site_config.site_url+"chat");
							ajaxLoad(2);
							profilePhoto();					
						} else {
							scroller();
							profilePhoto();
							chatMessage();
							game_btns2();
							videocallBtn();
							giftBtn();
							ajaxLoad(2);							
							$('#user'+profile_info.id).addClass('brick--outline');
							$('[data-left-profile-photo]').attr('data-src',profile_info.profile_photo);							
							window.history.pushState("",site_title(),site_config.site_url+"chat");							
							$('#data-content').css("opacity","1");
						}	
						if(site_theme['design_style']['val'] == 'Left-Menu'){
							fullHeightChat(60);
						} else {
							fullHeightChat();
						}													
					}
				});				
			break;				
			case "popularity":	
				$('#data-content').css("opacity","0.5");
				checkScrollBars('scroll');	
				profile_info = '';			
				$.ajax({
					url: request_source()+'/user.php', 
					data: {
						action:"popularity"						
					},	
					type: "post",			
					success: function(response) {
						$('#data-content').html(response);	
						scroller();
						profilePhoto();
						ajaxLoad(2);
						fullHeightWall();
						window.history.pushState("populars",site_title(),site_config.site_url+"credits");							
						$('#data-content').css("opacity","1");							
					},
				});				
			break;										
			case "fans":	
				$('#data-content').css("opacity","0.5");
				checkScrollBars('scroll');
				profile_info = '';				
				$.ajax({
					url: request_source()+'/user.php', 
					data: {
						action:"fans"						
					},	
					type: "post",			
					success: function(response) {
						$('#data-content').html(response);	
						scroller();
						profilePhoto();
						ajaxLoad(2);
						window.history.pushState("fans",site_title(),site_config.site_url+"fans");							
						$('#data-content').css("opacity","1");							
					},
				});				
			break;
			case "visits":	
				$('#data-content').css("opacity","0.5");
				checkScrollBars('scroll');	
				profile_info = '';			
				$.ajax({
					url: request_source()+'/user.php', 
					data: {
						action:"visits"						
					},	
					type: "post",			
					success: function(response) {
						$('#data-content').html(response);	
						scroller();
						profilePhoto();
						ajaxLoad(2);
						$('.videocall').html('<i class="mdi-action-settings"></i>');
						window.history.pushState("visits",site_title(),site_config.site_url+"visits");							
						$('#data-content').css("opacity","1");							
					},
				});				
			break;				
			case "mylikes":	
				$('#data-content').css("opacity","0.5");
				checkScrollBars('scroll');
				profile_info = '';				
				$.ajax({
					url: request_source()+'/user.php', 
					data: {
						action:"mylikes"						
					},	
					type: "post",			
					success: function(response) {
						$('#data-content').html(response);	
						scroller();
						profilePhoto();
						game_btns2();
						ajaxLoad(2);
						window.history.pushState("mylikes",site_title(),site_config.site_url+"mylikes");							
						$('#data-content').css("opacity","1");							
					},
				});				
			break;			
			case "matches":	
				$('#data-content').css("opacity","0.5");
				checkScrollBars('scroll');	
				profile_info = '';			
				$.ajax({
					url: request_source()+'/user.php', 
					data: {
						action:"matches"						
					},	
					type: "post",			
					success: function(response) {
						$('#data-content').html(response);	
						scroller();
						profilePhoto();
						ajaxLoad(2);
						window.history.pushState("matches",site_title(),site_config.site_url+"matches");							
						$('#data-content').css("opacity","1");							
					},
				});				
			break;

			case "cookies":
				checkScrollBars('scroll');
			break;	
			case "privacy":
				checkScrollBars('scroll');
			break;	
			case "terms":
				checkScrollBars('scroll');
			break;			

		}
	});	
}	
menuLinks();
function likeChart(){
	$('.safari').percentPie({
	    width: 80,
	    trackColor: "#CCCCCC",
	    barColor1: "#F50C41",
	    barColor2: "#3642D5",
	    barWeight: 8,
	    fps: 60
	});	
}



function interestSuggest(){
	$('[data-interest-add]').click(function(){
		var val = $(this).attr('data-interest-add');
		var html = $('#new-int').html();
		$('#new-int').html(html+'<div class="int"><span>'+ val +'</span></div>');
		$('#searchBox').val('');
		$('#searchResults').addClass('hiddden');
		$.ajax({
			url: request_source()+'/user.php', 
			data: {
				action:"add_interest",
				name: val
			},	
			type: "post",			
			success: function(response) {
			}
		});			
	});
}
function deleteInterest(val){
	$('[data-interest='+val+']').hide();
	$.ajax({
		url: request_source()+'/user.php', 
		data: {
			action:"del_interest",
			id: val
		},	
		type: "post",			
		success: function(response) {
		}
	});			
}
function profilePhoto(){
	$(".profile-photo").each(function(){
		var src = $(this).attr("data-src");
		$(this).css('background-image', 'url('+src+')');			
	});

	$(".profile-photo").preload(function(){
		$(this).show();
	});		
}
function profileLinks(){

	$flkty = new Flickity('.stories-wrap', {
		contain: true,
		prevNextButtons: false,
		freeScroll: true,
		pageDots: false,
		cellAlign: 'left',
		adaptiveHeight: false,
		setGallerySize: false,
		selectedAttraction: 0.05,
		freeScrollFriction: .1
	});

	setTimeout(function(){
		$('#highlightedStories').css('opacity',1);
	},50);

	$('.gift--hover').hover(function(){
		var src = $('.giftHover').attr('src');
		var dsrc = $('.giftHover').attr('data-src');
		$('.giftHover').attr('src',dsrc);
		$('.giftHover').attr('data-src',src);
	  },function(){
	  	var src = $('.giftHover').attr('src');
		var dsrc = $('.giftHover').attr('data-src');	  	
		$('.giftHover').attr('src',dsrc);
		$('.giftHover').attr('data-src',src);		
	}); 

	if(user_info.id != profile_info.id){
		setTimeout(function(){
			var chatBtn = $('[data-url="chat"]').offset();	
			$('.profile-menu__fast-message').css('top',chatBtn.top+30+'px');
			$('.profile-menu__fast-message').css('left',chatBtn.left-95+'px');
			$('.profile-menu__fast-message').show();
		},500)
	}

	if(url == 'profile'){
		$('#verifyAccount').click(function(){
			$('#verify-account').show();
		})
		var offset1 = $('.js-profile-languages-container').offset();
		var offset2 = $('.profile-info-v2__right div').last().offset();

		var offsetVal = offset1.top;
		if(offset1.top < offset2.top){
			offsetVal = offset2.top + 150;
		}

		var profileHeight = offsetVal - 50;

		if(profile_info.total_photos == 0){
			profileHeight = profileHeight+100;
		}

		if(site_theme['design_style']['val'] == 'Top-Menu'){
			if(profile_info.total_photos > 0){
				profileHeight = profileHeight+150;
			}			
		}
		$('.profile-info-v2').css('height',profileHeight+'px');

		setTimeout(function(){
			var profileVideos = document.getElementsByClassName("profileVideo");
			for (i = 0; i < profileVideos.length; i++) {
				var profileVideo = document.getElementsByClassName("profileVideo")[i];
				var id = profileVideo.id;
				if(profileVideo.readyState > 0) {
					var minutes = parseInt(profileVideo.duration / 60, 10);
					var seconds = parseInt(profileVideo.duration % 60);
					$('[data-video-time="'+id+'"]').html(` 
						<svg style="position:absolute;top: 0px;right: -15px;" xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
					`+minutes+':'+seconds);
				}
			}
		},100);

		$('[data-profile-video]').click(function(){
	        var video = this;
	        var private = $(video).attr('data-private');
	        var view = $(video).attr('data-view');

	        if(private == 1 && view == 0){
	        	$(video)[0].play(); 
	        	setTimeout(function(){
	        		$(video)[0].pause();
	        		$(video)[0].currentTime = 0; 
	        		
					showAskPrivate(); 
	        	},2500)
	        } else {
	        	$(video).css('opacity',0);
	        	$(video).hide();
		        $(video).addClass("big");
		        $('.overlay-dark').show();
		        setTimeout(function(){
					$(video).show();
					$('.overlay-dark').hide();
		        },250)

		        setTimeout(function(){
		        	$(video).css('opacity',1);
		        	$(video)[0].play();
		        },300)
		        
		        fullscreen(video,function(e) {
		            $(video).removeClass("big");
		            $(video)[0].currentTime = 0; 
		            $(video)[0].pause();
		        })
	        }
	    });

		var viewer = new slimLightbox();
		likeChart();

		if(profile_info.total_photos_public > 2){
			
		}

		$(".profile-videos").mCustomScrollbar({
			autoHideScrollbar:true,		
	        axis: "x",
	        theme: "dark-3",
	        alwaysShowScrollbar: 2,
	        advanced: {
	            autoExpandHorizontalScroll: !0
	        },
			mouseWheel:{
				preventDefault: true,
				deltaFactor: 120
			}                
		});	

		$('#addPhotos').click(function(){
			$('.uploadphotos').show();
	    	$('[data-upload-proccess=2]').hide();
	    	$('[data-upload-proccess=1]').show();
	    	$('[data-upload-header=2]').hide();
	    	$('[data-upload-header=1]').show(); 			
		})

		$('#addPhotos2').click(function(){
			$('.uploadphotos').show();
		})				
	}

	$('[data-url]').click(function(){
		var uid = $(this).attr('data-uid');
		var durl = $(this).attr('data-url');
		var index = $(this).attr('data-index');


		switch (durl) {
			case "chat":
				d_url = 'chat';
				if(user_info.guest == 1){
					window.location.href= site_config.site_url+'logout';
					return false;
				}				
				title = 0;

				var openGifts = $(this).attr('data-gift');
				$('#chatCount').text(title); 
				$('#chatCount').hide();								
				$('#data-content').css("opacity","0.5");
				ajaxLoad(1);
				$.ajax({
					url: request_source()+'/user.php', 
					data: {
						action:"chat",
						id : uid
					},	
					type: "post",			
					success: function(response) {
						$('#data-content').html(response);
						$('#r_id').val(uid);
						$('#data-content').css("opacity","1");
						if(site_theme['design_style']['val'] == 'Left-Menu'){
							fullHeightChat(60);
						} else {
							fullHeightChat();
						}					
					},
					complete: function(){	
						checkScrollBars('hidden');						
						chatMessage();
						profilePhoto();
						scroller();
						game_btns2();
						videocallBtn();	
						giftBtn();
						if(site_theme['design_style']['val'] == 'Left-Menu'){
							fullHeightChat(60);
						} else {
							fullHeightChat();
						}					
						
						ajaxLoad(2);
						if(openGifts == 'Yes'){
							showChatGifts();
						}						
						storyLoader(profile_info.story,profile_info.stories,profile_info.status_info);						
						window.history.pushState("chat",profile_info.name + ", " + profile_info.age + " | " + site_title(),site_config.site_url+'chat/'+profile_info.id+'/'+profile_info.link);
					}
				});					
			break;
			case "photos":
				$('.uploadphotos').show();
		    	$('[data-upload-proccess=1]').hide();
		    	$('[data-upload-proccess=2]').show();
		    	$('[data-upload-header=1]').hide();
		    	$('[data-upload-header=2]').show(); 
			break;					
		}
	});		
}


function openMediaManager(){
	$('#closeBasicInfo').click();

	setTimeout(function(){
		$('.uploadphotos').show();
		$('[data-upload-proccess=1]').hide();
		$('[data-upload-proccess=2]').show();
		$('[data-upload-header=1]').hide();
		$('[data-upload-header=2]').show(); 
	},150)	
}

function reportUser(uid,name,photo){

	openWidget("reportProfile");
	$('#reportUserName').text(name);
	$('#reportUserPhoto').attr('data-src',photo);
	profilePhoto();

	var reason = $("input[name='reportReason']:checked").next('span').text();

	$('#reportProfileBtn').attr('onClick','reportUserAjax('+uid+',"'+reason+'")');

	/*
	swal({   title: site_lang[326]['text'],
		text: site_lang[327]['text']+' '+name,
		imageUrl: photo,
		showCancelButton: true,
		confirmButtonColor: "#09c66e",
		confirmButtonText: site_lang[259]['text'],
		cancelButtonText: site_lang[195]['text'],
		closeOnConfirm: true
		}, function(){

	}); */
}


function reportUserAjax(uid,reason){
	var reason = $("input[name='reportReason']:checked").next('span').text();
	var query = user_info.id+','+uid+','+reason;     
	$.get( aUrl, {action: 'block', query: query} ,function( data ) {
		window.location.href = site_config.site_url+'meet';
	});
}

function deleteConv(t, e, o) {
    swal({
        title: site_lang[528].text,
        text: " ",
        imageUrl: o,
        showCancelButton: !0,
        confirmButtonColor: "#09c66e",
        confirmButtonText: site_lang[292].text,
        cancelButtonText: site_lang[195].text,
        closeOnConfirm: !0
    }, function() {
        $.ajax({
            url: request_source() + "/belloo.php",
            data: {
                action: "del_conv",
                id: t
            },
            dataType: "JSON",
            type: "post",
            success: function(t) {},
            complete: function() {
            	$("#user"+t).remove();
                $("[data-chat]").first().click();
            }
        })
    })
}

function unblockUser(id){
	var query = user_info.id+','+id;
	$.get( aUrl, { action: 'unblockUser', query: query } );
	setTimeout(function(){
		$('[data-unblock-user='+id+']').attr('onclick','goToProfile('+id+')');
		$('[data-unblock-user='+id+']').text(site_lang[709]['text']);
	},250);
}

function goToMeet(){
	$('[data-murl="meet"]').click();
}

function goTo(go){
	$('#data-content').css("opacity","0.5");
	$('[data-wide-spotlight]').hide(); 
	ajaxLoad(1);

	if(go == 'live'){
		streamViewers = 0;	
		var leaveData = user_info.id+','+user_info.first_name+','+user_info.profile_photo+','+viewStreamId;		
		$.get( request_source()+'/live.php', {action: 'leave', query: leaveData} );
	}	

	$.ajax({
		url: request_source()+'/user.php', 
		data: {
			action: go						
		},	
		type: "post",			
		success: function(response) {
			$('#data-content').html(response);	
			scroller();
			profilePhoto();
			ajaxLoad(2);
			fullHeightWall();

			if(go == 'live'){
				$("#sendLiveMessage").on('keyup', function (e) {
				    if (e.keyCode === 13) {
				        sendLiveMessage();
				    }
				});	
			}

			window.history.pushState("goTo",site_title(),site_config.site_url+go);							
			$('#data-content').css("opacity","1");	
			checkScrollBars('scroll');
			if(go == 'premium'){
				$('[data-premium-send]').on('click', function(e){
					 e.preventDefault();
					var price = $(this).attr('data-price');
					var days = $(this).attr('data-premium-send');

					var months = days/30;
					$('#payment-custom3').val(user_info.id+','+days);
					$('#payment-amount3').val(price);
					$('#payment-time3').val(months);
					$('#payment-name3').val(site_config.name + ' - ' + days + ' ' + site_lang[332]['text']);	
					$('#buy-premium').submit();
				});				
			}								
			updateSeo(go);
		},
	});		
}
function fastMessage(r_id){
	var mob = 0;
	var messageVal = $('#fastMessageVal').val();

	if(messageVal == ''){
		return false;
	}

	if(plugins['chat']['creditsPerMessageEnabled'] == 'Yes'){

        if(user_info.credits < plugins['chat']['creditsPerMessage']){ 
            openWidget("purchaseCredits");
            return false;
        }	
        
        var data = [];
        data.name = '';
        data.icon = site_theme['notification_inapp_credits_icon']['val'];
        data.message = site_lang[610].text+' '+plugins['chat']['creditsPerMessage']+' ' + site_lang[73].text;
        
		if(plugins['chat']['creditsPerMessageGender'] == user_info.gender){
			updateCredits(user_info.id,plugins['chat']['creditsPerMessage'],1,'Credits for send chat message');
			pushNotif(data,1);
		}
		if(plugins['chat']['creditsPerMessageGender'] == allG){
			updateCredits(user_info.id,plugins['chat']['creditsPerMessage'],1,'Credits for send chat message');
			pushNotif(data,1);
		}		
	}

	var message = user_info.id+'[message]'+r_id+'[message]'+messageVal+'[message]text[message]fast';
	var send = user_info.id+'[rt]'+r_id+'[rt]'+user_info.profile_photo+'[rt]'+user_info.first_name+'[rt]'+messageVal+'[rt]text';      

	$.get( gUrl, {action: 'message', query: send} );		
	$.get( aUrl, {action: 'sendMessage', query: message} );

	$('.fast-message__content').hide();
	$('.fast-message__footer').hide();
	$('.fast-message__state').fadeIn();
}
function closeFastMessage() {
	$('.profile-menu__fast-message').hide();
}
function goToChat(uid){
	d_url = 'chat';
    $('.chattt').toggleClass('show-me'); 
    $('.buttonChat').toggleClass('show-me');
	title = 0;
	$('#chatCount').text(title); 
	$('#chatCount').hide();								
	$('#data-content').css("opacity","0.5");
	$.ajax({
		url: request_source()+'/user.php', 
		data: {
			action:"chat",
			id : uid
		},	
		type: "post",			
		success: function(response) {
			$('#data-content').html(response);
			$('#r_id').val(uid);
			$('#data-content').css("opacity","1");						
		},
		complete: function(){	
			profileLinks();
			chatMessage();
			profilePhoto();
			scroller();
			game_btns2();
			videocallBtn();	
			giftBtn();
			if(site_theme['design_style']['val'] == 'Top-Menu'){
				fullHeightChat();
			} else {
				fullHeightChat(60);
			}			
			window.history.pushState("chat",profile_info.name + ", " + profile_info.age + " | " + site_title(),site_config.site_url+'chat/'+profile_info.id+'/'+profile_info.link);
		}
	});			
}
function goToProfile(uid,i = 0,b = 0){
	$('body').scrollTop(0);
	checkScrollBars('hidden');
	ajaxLoad(1);

	if(uid == user_info.id){
		$('#data-content').css("opacity","0.6");			
		$.ajax({
			url: request_source()+'/belloo.php', 
			data: {
				action:"wall",
				id : uid,
				b : b
			},	
			type: "post",			
			success: function(response) {
				$('#data-content').html(response);
				if(b == 1){
					$('#fastMessageVal').focus();
				}	
				$('#leftProfile').show();
				$('[data-left-profile-photo]').attr('data-src',profile_info.profile_photo);					
				window.history.pushState("profile",profile_info.name + ", " + profile_info.age + " | " + site_title(),site_config.site_url+'@'+profile_info.username);							
				document.title = profile_info.name + ", " + profile_info.age + ", " + profile_info.city + " | " + site_title();	
				$('#data-content').css("opacity","1");
				$('[data-wide-spotlight]').hide(); 
				profilePhoto();
				profileLinks();
				game_btns2();
				ajaxLoad(2);
				$('.profile-content').css("overflow-y","auto");
				storyLoader(profile_info.story,profile_info.stories,profile_info.status_info);
				fullHeightWall();
				updateSeo('profile');			
			}
		})

		return false;	
	}

	profile_slider_open = false;	
	$('#data-content').css("opacity","0.6");
	$.ajax({
		url: request_source()+'/belloo.php', 
		data: {
			action:"wall",
			id : uid,
			b : b
		},	
		type: "post",			
		success: function(response) {
			$('#data-content').html(response);
			if(b == 1){
				$('#fastMessageVal').focus();
			}
			$("meta[property='og\\:url']").attr("content",site_config.site_url+'@'+profile_info.username);					
			$("meta[property='og\\:title']").attr("content",profile_info.name + ", " + profile_info.age + " | " + site_config.name);					
			$("meta[property='og\\:image']").attr("content",profile_info.profile_photo);
			
			$('[data-wide-spotlight]').hide(); 
			checkFUR();
			$('#leftProfile').show();
			$('[data-left-profile-photo]').attr('data-src',profile_info.profile_photo);					
			window.history.pushState("profile",profile_info.name + ", " + profile_info.age + " | " + site_title(),site_config.site_url+'@'+profile_info.username);							
			$('#data-content').css("opacity","1");
			profilePhoto();
			profileLinks();	
			game_btns2();
			ajaxLoad(2);
			$('.profile-content').css("overflow-y","auto");
			storyLoader(profile_info.story,profile_info.stories,profile_info.status_info);
			fullHeightWall();

			if(plugins['adsWeb']['enabled'] == 'Yes'){
			}
		}
	});
	updateSeo('profile');
}
function privateLinks(){
	$('#ask-permission').click(function(){
		$('#data-content').css("opacity","0.5");
		url = "chat";
		$.ajax({
			url: request_source()+'/user.php', 
			data: {
				action:"chat_p",
				id : profile_info.id
			},	
			type: "post",			
			success: function(response) {
				window.location.href = site_config.site_url+'chat/'+profile_info.id+'/'+profile_info.link	
			},
			complete: function(){
			}
		});
	});	
	$('#buy-permission').click(function(){
			if(user_info.credits < site_prices.private){
				openWidget("purchaseCredits");
			} else {											
swal({   title: site_lang[191]['text'],   text: site_lang[192]['text']+' '+profile_info.first_name+' '+ site_lang[193]['text'] +' '+ site_prices.private +' ' + site_lang[73]['text'],   imageUrl: profile_info.profile_photo,   showCancelButton: true,   confirmButtonColor: "#09c66e",   confirmButtonText: site_lang[194]['text'], cancelButtonText: site_lang[195]['text'],   closeOnConfirm: true }, function(){
				$.ajax({
					url: request_source()+'/user.php', 
					data: {
						action:"p_access",
						id : profile_info.id
					},	
					type: "post",			
					success: function(response) {
						$('.profile-menu .selected').click();
					}
				});
			});
		}
	});			
}
//CHAT							
var sendPhoto = { 
	success: function(data) {
		if(mobile == false){
			$(".chat").append(data);
			$('.chat').mCustomScrollbar("destroy");
			photosChatWall();
			profilePhoto();
			scroller();
		} else {
			$(".list-chats").append(data);
		}
	},
	resetForm: true
}; 	
function startGalleria(data) {
	var d = [];
    if(url == "discover"){
		$(".liked").hide();
		$(".disliked").hide();			
        Galleria.loadTheme(theme_source()+'/galleria/themes/classic/galleria.classic.js');
		var y = $(window).height();
		y = y-60;
		if(mobile == true){
			Galleria.configure({
				thumbnails: "hide"
			});				
			Galleria.run(".discover",{
				autoplay: true,
				height: y,
				dataSource: data,
				transition: "fade",
				imageCrop: true
			});				
		} else {
			Galleria.configure({
			    show: 0
			});
			Galleria.run(".discover",{
				autoplay: true,
				dataSource: data,
				transition: "fade",
				imageCrop: false
			});				
		}
        Galleria.ready(function(options){
            this.attachKeyboard({
                left: this.prev,
                right: this.next
            });
        });    
    }
    if(url == "profile"){		
        Galleria.loadTheme(theme_source()+'/galleria/themes/classic/galleria.classic.min.js');
		var y = $('body').height();
		y = y-60;
		var z = y - 120;
		console.log('gall height: '+z);
		$('.gall').css('height',z+'px');
        Galleria.ready(function(options){
            this.attachKeyboard({
                left: this.prev,
                right: this.next
            });
			this.bind('image', function(e) {
				var string = e.galleriaData.big;
				if (string.indexOf("private") !=-1) {
					$('.hero-image-private').show();	
				} else {
					$('.hero-image-private').hide();
				}
			});				
        });    
    }		
}



function meetPagination(){

	game_btns2(); 
	$("[data-meet]").click(function() {

		var p = $(this).attr('data-meet');
		$('#meet_filter_limit').val(p); 
		var g = 0;
		if($('#check-1').is(":checked") ){
			g = g+2;
		}
		if($('#check-2').is(":checked") ){
			g = g+1;
		}
		if(p > meet_limit && plugins['meet']['creditsPerPage'] > 0){
			 if(user_info.credits < plugins['meet']['creditsPerPage']){
				openWidget("purchaseCredits");
				return false;
			 }			
			var data = [];
			data.name = '';
			data.icon = site_theme['notification_inapp_credits_icon']['val'];
			data.message = site_lang[610].text+' '+plugins['meet']['creditsPerPage']+' ' + site_lang[73].text;
			pushNotif(data,1);
			user_info.credits = user_info.credits - plugins['meet']['creditsPerPage'];
			updateCredits(user_info.id,plugins['meet']['creditsPerPage'],1,'Credits for meet page');
		}

		$('#meet_section').css("opacity","0.5");
		ajaxLoad(1);		
		meet_age = fage1+','+fage2;
		meet_gender = $('#meet_filter_gender').val();
		meet_online = $('#meet_filter_online').val();
		meet_limit = $('#meet_filter_limit').val();	
		var searchUsername = $('#searchUsername').val();	
		$('body').animate({
			  scrollTop: 0
		}, 500);
		$.ajax({
			url: request_source()+'/user.php', 
			data: {
				action:"meet_filter",
				age: meet_age,
				gender: meet_gender,
				radius: meet_radius,
				online: meet_online,
				limit: meet_limit,
				username: searchUsername					
			},	
			type: "post",			
			success: function(response) {
				//$('.wall').mCustomScrollbar("destroy");					
				$('#meet_section').html(response);					
				scroller();	
				profilePhoto();
				meetPagination();
				ajaxLoad(2);
				$('#meet_section').css("opacity","1");							
			},
		});
	});		
}

function updateDiscoverResults(){
	$.ajax({
		data: {
			action: "game",
			id: user_info.id
		},
		url:   request_source()+'/api.php',
		type:  'get',
		dataType: 'JSON',
		success:  function (response) {	
			if(response['game'] != 'error'){
				game_array = response['game'];
				game_array_max = game_array.length;
				

				response['game'].forEach(function(entry) {
					var photos = entry.photos;					  
					photos.forEach(function(photo) {
						$(".preload-photos").append("<img src="+photo.image+" style='opacity:0'/>");
					});												  
				});	
			}
		}
	});
}


function fixUserCredits(){
	$.ajax({
		data: {
			action: "fixUserCredits",
			id: user_info.id
		},
		url:   request_source()+'/api.php',
		type:  'GET',
		dataType: 'JSON',
		success:  function (response) {}
	});	
}

function meetFilter(){
	$('#meet_section').css("opacity","0.5");
	
	var val = 0;
	val = meet_radius;
	$('#slider-distance').text(val +' '+ distanceMeter);				
	$("#slider-range").slider({
		range: true,
		min: 18,
		max: 80,
		step: 1,
		values: [fage1, fage2],
		slide: function (e, ui) {
			var val = ui.values[0];
			var val2 = ui.values[1];
			fage1 = val;
			fage2 = val2;
			meet_age = fage1+','+fage2;
			if(val2 == 80){
				$('#slider-range-age2').text(val2+'+');
			} else {
				$('#slider-range-age2').text(val2);
			}
			$('#slider-range-age1').text(val);
		}
	});	
	$("#slider-range2").slider({
		range: true,
		min: 1,
		max: distanceLimit,
		step: 1,
		values: [1,meet_radius],
		slide: function (e, ui) {
			var val = ui.values[1];
			meet_radius = val;
			$('#slider-distance').text(val +' '+ distanceMeter);										
		}
	});
	$('.ui-slider-range').click(function(e){
		e.preventDefault;
		return false;
	});
	$('#slider-range2 .ui-slider-handle').first().remove();

	var searchUsername = $('#searchUsername').val();
	$.ajax({
		url: request_source()+'/user.php', 
		data: {
			action:"meet_filter",
			age: meet_age,
			gender: meet_gender,
			radius: meet_radius,
			online: meet_online,
			limit: meet_limit,
			username: searchUsername				
		},	
		type: "post",			
		success: function(response) {
			$('.wall').mCustomScrollbar("destroy");					
			$('#meet_section').html(response);
			scroller();	
			profilePhoto();	
			meetPagination();
			setTimeout(function(){
				var windowHeight = $('.wall').height() - 100;
				console.log(windowHeight);
				$('body').css('height',windowHeight+'px');
			},1500)
			
			$('#meet_section').css("opacity","1");
		},
	});	

	$("[data-filter]").click(function() {
		$('#meet_section').css("opacity","0.5");
		var h = $('.wall').height();
		//$('.wall').height(h+225);
		if(url == "search"){
			$('.search-post').hide();
		} else {
			$('.wall-post').hide();           
		}        
		$('[data-close-filter]').hide();
		$('.post-btn-right').show();			
		var g = 0;
		if($('#check-1').is(":checked") ){
			g = g+2;
		}
		if($('#check-2').is(":checked") ){
			g = g+1;
		}	
		var searchUsername = $('#searchUsername').val();

		meet_age = fage1+','+fage2;
		meet_gender = $('#meet_filter_gender').val();
		meet_online = $('#meet_filter_online').val();
		meet_limit = 0;
		$('[data-open-meet-filter]').show();			
		$.ajax({
			url: request_source()+'/user.php', 
			data: {
				action:"meet_filter",
				age: meet_age,
				gender: meet_gender,
				radius: meet_radius,
				online: meet_online,
				limit: meet_limit,
				username: searchUsername					
			},	
			type: "post",			
			success: function(response) {
				$('.wall').mCustomScrollbar("destroy");					
				$('#meet_section').html(response);
				scroller();	
				profilePhoto();	
				meetPagination();
				$('#meet_section').css("opacity","1");
				updateDiscoverResults();							
			},
		});
	});		
	$('[data-action]').click(function(){
		var action = $(this).attr('data-action');
		switch (action) {		
			case "online":
				$('#allusers').removeClass("selected");
				$(this).addClass("selected");
				$('#meet_filter_online').val(1);
				$('#meet_filter_limit').val(0); 	
				$('#meet_section').css("opacity","0.5");					
				var g = 0;
				if($('#check-1').is(":checked") ){
					g = g+2;
				}
				if($('#check-2').is(":checked") ){
					g = g+1;
				}	
				var age = fage1+','+fage2;
				var gender = $('#meet_filter_gender').val();
				var online = $('#meet_filter_online').val();
				var limit = 0;			
				var radius = meet_radius;
				if(plugins['meet']['viewOnlyPremiumOnline'] == 'Yes'){
					meetPremiumNotification(2);					
				}	
				var searchUsername = $('#searchUsername').val();			
				$.ajax({
					url: request_source()+'/user.php', 
					data: {
						action:"meet_filter",
						age: age,
						gender: gender,
						radius: radius,
						online: online,
						limit: limit,
						username: searchUsername
					},	
					type: "post",			
					success: function(response) {
						$('.wall').mCustomScrollbar("destroy");					
						$('#meet_section').html(response);
						scroller();	
						profilePhoto();	
						meetPagination();
						$('#meet_section').css("opacity","1");							
					},
				});		
			break;
			case "allusers":
				$('#onlineusers').removeClass("selected");
				$(this).addClass("selected");
				$('#meet_filter_online').val(0);
				$('#meet_filter_limit').val(0); 					
				$('#meet_section').css("opacity","0.5");
				var g = 0;
				if($('#check-1').is(":checked") ){
					g = g+2;
				}
				if($('#check-2').is(":checked") ){
					g = g+1;
				}
				var searchUsername = $('#searchUsername').val();
				if(plugins['meet']['viewOnlyPremium'] == 'Yes'){
					meetPremiumNotification(1);					
				}					
				var age = fage1+','+fage2;
				var gender = $('#meet_filter_gender').val();
				var online = $('#meet_filter_online').val();
				var limit = 0;		
				var radius = meet_radius;
				$.ajax({
					url: request_source()+'/user.php', 
					data: {
						action:"meet_filter",
						age: age,
						gender: gender,
						radius: radius,
						online: online,
						limit: limit,
						username: searchUsername
					},	
					type: "post",			
					success: function(response) {
						$('.wall').mCustomScrollbar("destroy");					
						$('#meet_section').html(response);
						scroller();	
						profilePhoto();	
						meetPagination();
						$('#meet_section').css("opacity","1");							
					},
				});		
			break;	
		}
	});	
}
$(".photo").each(function(){
	$(this).hover(function(){
		$(this).find('.data').fadeIn(); 
	  },function(){
		$(this).find('.data').fadeOut(); 
	});        
	var src = $(this).attr("data-src");
	$(this).css('background-image', 'url('+src+')');
});	
$("[data-act]").each(function(){
	if(mobile === true && url == 'chat'){
		$(this).show();
		$(this).css("font-size",24);	
	}
});		
$('[data-act]').click(function(e){
	e.preventDefault();
	var action = $(this).attr('data-act');
	if(action == "mphotos"){
		if($('#manage-photos').is(':visible')) {
			$('#manage-photos').hide();
		} else {
			$('#manage-photos').show();
		}
	}
	if(action == "gift"){
		if($('#send-gift').is(':visible')) {
			$('#send-gift').hide();
		} else {
			$('#send-gift').show();
			$('#g-name').html(profile_info.name);
			$('#g-name2').html(profile_info.name);					
		}
	}
	if(action == "like"){
		var uid = profile_info.id;
		$.ajax({
			url: request_source()+'/user.php', 
			data: {
				action:"game_like",
				id : uid,
				like: 1
			},	
			type: "post",
			beforeSend: function(){
				console.log('like');
			},
			success: function(response) {
				console.log(response);
				game_start();
			},
			complete: function(){
			}
		});
	}		
});	
$('#insta-import').on('click',function(e){
swal({   title: "Instagram",   text: site_lang[329]['text'],   type: "input",   showCancelButton: true, showLoaderOnConfirm: true,  closeOnConfirm: false,   animation: "slide-from-top",   inputPlaceholder: site_lang[331]['text'] }, function(inputValue){   if (inputValue === false) return false;      if (inputValue === "") {     swal.showInputError(site_lang[330]['text']);     return false   } 
	$.ajax({
		url: request_source()+'/user.php', 
		data: {
			action:"instagram",
			insta: inputValue
		},	
		type: "post",
		beforeSend: function() {
		},		
		success: function(response) {
			if(mobile == true){
				goToProfile(user_info.id);
			} else {
				window.location.href= site_config.site_url+'profile/'+user_info.id+'/photo';
			}
		}
	});																																																																																																																																																													});									   
});

//upload images

$('.add-photos').click(function(){
    document.getElementById("uploadContent").click();
    upType = 1;
});

$('#verify-account-btn').click(function(){
	document.getElementById("uploadContent").click();
	upType = 3;
});	

var ph = 0;
var upphotos = [];
var extFilter = ["jpg", "jpeg", "png", "mp4", "ogg", "webm"];
var storyAlbumFilter = ["video/3gpp", "video/mpeg", "video/mp4","video/webm","video/ogg"];


$("#upload-area").dmUploader({
	url: site_url()+'/assets/sources/upload.php',
	extFilter: extFilter,
	multiple: multipleUpload,
	onFileExtError: function(file){
		swal({ title: site_lang[811]['text'], text: site_lang[596]['text'],   type: 'info' }, function(){ });
	},
    onNewFile: function(id, file){
    	var fileUrl = URL.createObjectURL(file);

    	var fileType = file.type;
		var storyAlbumFilterResponse = (storyAlbumFilter.indexOf(fileType) > -1); 
		
        if(file.size > site_config.max_upload){
            var maxAllowed = site_config.max_upload / 1024 / 1024;
            swal({   title: site_lang[810]['text'], text: site_lang[809]['text']+' ('+maxAllowed+') MB',   type: 'info' }, function(){ });
            return false;
        }

    	if(upType == 1){
	    	$('[data-upload-proccess=1]').hide();
	    	$('[data-upload-proccess=2]').show();
	    	$('[data-upload-header=1]').hide();
	    	$('[data-upload-header=2]').show();    	
			createPreview(file, fileUrl,id);
		}

    	if(upType == 9){//inchat photo  	
			createPreview(file, fileUrl,id,'chat');
			profilePhoto();
			$('.chat').mCustomScrollbar("destroy");
			scroller();
		}		

		if(upType == 5){
			$('[data-force-profile-photo=1]').attr('data-src',fileUrl);
			$('[data-force-profile-photo=1]').attr('id','gray'+id);
			$('[data-force-profile-photo=1]').addClass('uploadingGray');
			$('[data-force-profile-photo=2]').show();
			profilePhoto();	
		}

		if(upType == 6){
			if(storyAlbumFilterResponse){
				alert(site_lang[674]['text']);
				return false;
			}
			$('[data-story-album]').attr('data-src',fileUrl);
			$('[data-story-album]').attr('id','albumPhoto'+id);
			$('[data-story-album]').addClass('uploadingGray');
			profilePhoto();	
		}		
		
    },	
	onUploadProgress: function(id,percent){
		$('#upload'+id).css('width',percent+'%');
	},
	onDragEnter: function(){
		upType = 1;
		$('.file-upload').addClass('dragBorder');
	},
	onDragLeave: function(){
		$('.file-upload').removeClass('dragBorder');
	},
	onComplete: function(){
	},
	onUploadSuccess: function(id, file){
  	
	if(upType == 6){
		$('[data-story-album]').removeClass('uploadingGray');
	}
	$('#gray'+id).removeClass('uploadingGray');
	$('[data-upload-progress="upload'+id+'"]').fadeOut();
	$('[data-force-profile-photo=2]').text(site_lang[594]['text']+'...');
	$('[data-force-profile-photo=3]').hide();

    upphotos[0] = file;
    var photoPath = file.path;


    //profile media upload
    if(upType == 1){  

		$('[data-manage-media="'+id+'"]').attr('data-manage-media-path',photoPath);

		var reloadUserProfile = 0;
		$.ajax({
		  type: "POST",
		  url: request_source()+'/belloo.php',
		  data: {
		    action: 'uploadMedia',
		    media: upphotos
		  },
		  dataType: 'JSON',
		  success: function(response) {
			var managePhotoProfile = '';
			if(file.video == 0){
				managePhotoProfile = '<li><a href="#" onclick="manageUserMedia('+response.data.id+',1)">'+site_lang[636]['text']+'</a></li>';
			}
			
			if(user_info.profile_photo.includes('no_user') && file.video == 0){
				$('.User-Avatar').attr('data-src',file.path);
				$('.left-profile').attr('data-src',file.path);
				profilePhoto();
			}
			$('[data-manage-media="'+id+'"]').attr('data-my-media',response.data.id);
			
			$('[data-manage-media="'+id+'"]').append(`
				<div class="dropdown-media" >
				<input class="dropdown-toggle" type="text">
				<div class="dropdown-text" style="display: none"></div>
				<ul class="dropdown-content-media">
				`+managePhotoProfile+`
				<li><a href="#" onclick="manageUserMedia(`+response.data.id+`,4)">`+site_lang[637]['text']+`</a></li>        
				<li><a href="#" onclick="manageUserMedia(`+response.data.id+`,2)" style="color:#e40404">`+site_lang[638]['text']+`</a></li>
				</ul>
				</div>
			`);	
			$("#checkbox"+id).attr('data-my-media-private',response.data.id);
			$("#checkbox"+id).attr("onclick","manageUserMedia("+response.data.id+",3)");

			reloadUserProfile = 1;
			setTimeout(function(){
				if(reloadUserProfile == 1){
					goToProfile(user_info.id);
				}
			},1500);					  	
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

    if(upType == 9){ //chat photo
		var r_id = $("#r_id").val();
		var messageVal = photoPath;

		var sendType = 'image';
		if(file.video == 1){
			sendType = 'video';
		}
		var message = user_info.id+'[message]'+r_id+'[message]'+messageVal+'[message]'+sendType;  
		var send = user_info.id+'[rt]'+r_id+'[rt]'+user_info.profile_photo+'[rt]'+user_info.first_name+'[rt]'+messageVal+'[rt]'+sendType;

		$.get( gUrl, {action: 'message', query: send} );		   	
		$.get( aUrl, {action: 'sendMessage', query: message} );
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
	      url: request_source()+'/belloo.php',
	      data: {
	        action: 'verifyAccount',
	        media: upphotos
	      },
	      success: function(t) {
	      	  $('#verify-account').hide();
	          swal({   title: site_lang[600]['text'],   text: site_lang[601]['text'], type: "success" }, function(){ });
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



$('#add-photos,#add-photos-big').on('click', function(e){
	 e.preventDefault();
	$("#add-photos-file").click(); 
});
$('#private-photos').on('click', function(e){
	 e.preventDefault();
	$("#add-private-photos-file").click(); 
});	
$("#add-photos-file").change(function() {
	$("#add-photos-form").submit();
});	
$("#add-private-photos-file").change(function() {
	$("#add-private-photos-form").submit();
});		
$("[data-settings]").click(function() {
	$('.header-settings').removeClass("selected");									   
	$(this).addClass("selected");		
	var i = $(this).attr('data-settings');
	$("[data-settings-page]").hide();
	$('[data-settings-page='+i+']').fadeIn();
});

$("[data-noti]").click(function() {
	var val = '';
	var col = $(this).attr('data-noti');
	if($(this).hasClass('active')){
		$(this).attr('data-noti-val',0);
		$(this).removeClass('active');
	} else {
		$(this).attr('data-noti-val',1);
		$(this).addClass('active');
	}

	var i = 0;
	$("[data-noti='"+col+"']").each(function(data) {	
		var a = $(this).attr('data-noti-val');
		if(i < 2){
			val = val+a+',';
		} else {
			val = val+a;	
		}
		i++;
	});
	$.ajax({
		url: request_source()+'/user.php', 
		data: {
			action:"user_notifications",
			val: val,
			col: col
		},	
		type: "post",
		success: function(response) {
		}
	});		
});	
 	
$("[data-payment]").click(function() {
	$('.sm').addClass("grayS");									   
	$(this).removeClass("grayS");		
	$('.sm').css('border-color', '#f1f1f1');								   
	$(this).css("border-color","#096DC9");		
	payment_method = $(this).attr('data-payment');
});		
$('#payment-submit').click(function(){
	var price = $('#payment-select').find(':selected').attr('data-price');
	var quantity = $('#payment-select').find(':selected').attr('data-quantity');		
	$('#payment-custom').val(user_info.id+','+quantity);
	$('#payment-custom2').val(user_info.id+','+quantity);
	$('#payment-amount,#payment-amount2').val(price);
	$('#payment-name,#payment-name2').val(site_config.name + ' ' + quantity + ' '+site_lang[73]['text']);		
	if(payment_method == 0){
		swal({   title: site_lang[333]['text'],   text: site_lang[196]['text'],   type: 'error' }, function(){ });
		return false;			
	}
	if(payment_method == 1){
		$('#method01').submit();
	}
	if(payment_method == 2){
		$('#method02').submit();
	}
	if(payment_method == 4){
	var name = site_config.name + ' ' + quantity + ' '+site_lang[73]['text'];
	var encode = 'amount='+quantity+'callback_url='+site_config.site_url+'credits-okcredit_name='+name+'cuid='+user_info.id+'currency='+site_config.currency+'display_type=userprice='+price+'v=web';			
	$.ajax({ 
		type: "POST", 
		url: request_source() + "/user.php",
		data: {
			action: 'fortumo',
			encode: encode
		},
		success: function(response){
			var md5 = response;
			var callback = encodeURI(site_config.site_url+'credits-ok');
			name = encodeURI(name);
			var href= 'http://pay.fortumo.com/mobile_payments/'+site_config.fortumo+'?amount='+quantity+'&callback_url='+callback+'&credit_name='+name+'&cuid='+user_info.id+'&currency='+site_config.currency+'&display_type=user&price='+price+'&v=web&sig='+md5;
			window.location.href = href;				
		}
	});				
	}
	if(payment_method == 3){
		price = price*100;
		var app = 1;
		var handler = StripeCheckout.configure({
			key: site_config.stripe,
			image: site_config.logo,
			locale: 'auto',
			token: function(token) {
				$.ajax({
					url: request_source()+'/stripe.php', 
					data: {
						token:token.id,
						price: price,
						app: app,
						quantity: quantity,
						uid: user_info.id,
						de: site_config.name + ' ' + quantity + ' '+site_lang[73]['text']
					},	
					type: "post",
					success: function(response) {
					},
					complete: function(){
						if(app == 1){
							window.location.href = site_url()+'credits-ok';
						}
					}
				});
			}
		});
		handler.open({
			name: site_config.name,
			description: site_config.name + ' ' + quantity + ' '+site_lang[73]['text'],
			amount: price
		});
		$(window).on('popstate', function() {
			handler.close();
		});				
	}
});



$('.ovl-close').on("click", function() {
	if($(this).hasClass('closeSendGift')){
		$('#send-gift').hide();
		return false;
	}
	if($('.ovl').is(':visible')) {
		//$('.ovl').hide();
		$('.ovl-frame').addClass('swoopOutBottom');
		setTimeout(function(){
			$('.ovl').hide();
			$('.ovl-frame').removeClass('swoopOutBottom');
		},500);
	} 
});	
$('.closeModal').on("click", function() {
	if($('.ovl').is(':visible')) {
		$('.ovl-frame').addClass('swoopOutBottom');
		setTimeout(function(){
			$('.ovl').hide();
			$('.ovl-frame').removeClass('swoopOutBottom');
		},500);
	} 
});		
$('[data-spotlight]').click(function(){
	$('body').find(".photos .selected").removeClass('selected');								 
	$(this).addClass('selected');
	var psrc = $(this).attr('data-src');
	$('#s_photo').val(psrc);
});	
$('.add-yourself').click(function(){
	$('#add-spotlight').show();
});	
$('#s_close').click(function(){
	$('#add-spotlight').hide();
});

function showRiseUp(){
	$('#rise-up').show();
}
function showWithdraw(){
	$('#showWithdraw').show();
}
function showAskPrivate(){
	if(user_info.guest == 0){
		$('#askPrivateName').text(profile_info.name);
		$('#askPrivatePhoto').attr('data-src',profile_info.profile_photo);
		$('#askPrivate').show();
		profilePhoto();
	}
}
function showSendCredit(){
	if(user_info.guest == 0){
		$('#sendCreditPhoto').attr('data-src',profile_info.profile_photo);
		$('.sendCreditName').text(profile_info.name);
		profilePhoto();
		$('#showSendCredit').show();
	}
}
function showDiscover(){
	$('#discover100').show();
}
function showSpotlight(){
	$('#add-spotlight').show();
}	
setTimeout(function(){
	$('#newNotification').show();	
},2000);
function riseUp(){
	 if(user_info.credits < site_prices.first){
		openWidget("purchaseCredits");
	 } else {
		$.ajax({ 
			type: "POST", 
			url: request_source() + "/belloo.php",
			data: {
				action : "riseUp",
				price : site_prices.first
			},
			success: function(response){
				window.location.href = site_config.site_url+'credits';
			}
		});	
	 }	
}
function dailyChat(){
	 if(user_info.credits < site_prices.chat){
		openWidget("purchaseCredits")
	 } else {
		$.ajax({ 
			type: "POST", 
			url: request_source() + "/belloo.php",
			data: {
				action : "dailyChat",
				price : site_prices.chat
			},
			success: function(response){
				window.location.reload();
			}
		});	
	 }	
}
function buyPrivate(){
	if(user_info.credits < site_prices.private){
		openWidget("purchaseCredits");
	} else {											
	swal({   title: site_lang[191]['text'],   text: site_lang[192]['text']+' '+profile_info.first_name+' '+ site_lang[193]['text'] +' '+ site_prices.private +' ' + site_lang[73]['text'],   imageUrl: profile_info.profile_photo,   showCancelButton: true,   confirmButtonColor: "#09c66e",   confirmButtonText: site_lang[194]['text'], cancelButtonText: site_lang[195]['text'],   closeOnConfirm: true }, function(){
		$.ajax({
			url: request_source()+'/user.php', 
			data: {
				action:"p_access",
				id : profile_info.id
			},	
			type: "post",			
			success: function(response) {
				window.location.reload();
			}
		});
	});
	}	
}
function sendCreditNow(){
	var a = $("#sendCreditAmount").val(),
	m = $("#sendCreditMessage").val();
	if(a == ''){
		swal({   title: "Error",   text: site_lang[584]['text'], type: "warning" }, function(){ });
		return false;		
	}

	if(a < 1){
		swal({   title: "Error",   text: site_lang[584]['text'], type: "warning" }, function(){ });
		return false;		
	}

	if (user_info.credits < a) {
		swal({   title: "Error",   text: site_lang[817]['text'], type: "warning" }, function(){
			openWidget("purchaseCredits");
		});
		return false;			
	}  else {
	 swal({
	    title: site_lang[549]['text'],
	    text: site_lang[586]['text'] + ' ' + a + " "+ site_lang[128]['text']  + ' ' + site_lang[587]['text']  + " " + profile_info.name,
	    imageUrl: profile_info.profile_photo,
	    showCancelButton: !0,
	    confirmButtonText: site_lang[588]['text'],
	    closeOnConfirm: true
	}, function() {
		var messageVal = '';
		var message = user_info.id+'[message]'+profile_info.id+'[message]'+messageVal+'[message]credits'+'[message]'+a;
		var send = user_info.id+'[rt]'+profile_info.id+'[rt]'+user_info.profile_photo+',[rt]'+user_info.first_name+'[rt]'+messageVal+'[rt]credits[rt]'+a;      

    	user_info.credits = user_info.credits - a;
    	user_info.credits = parseInt(user_info.credits);
		$('.userCredits').text(user_info.credits);
		$.get( gUrl, {action: 'message', query: send} );        
		$.get( aUrl, {action: 'sendMessage', query: message} );	
		$('#showSendCredit').hide();

        var data = [];
        data.name = '';
        data.icon = site_theme['notification_inapp_credits_icon']['val'];
        data.message = a + " "+ site_lang[128]['text']  + ' ' + site_lang[587]['text']  + " " + profile_info.name;
        pushNotif(data,1);

		//$('.profile-menu__chat-btn').click();
	})
	}	
}
function withdrawNow(){
	var t = user_info.payout,
	e = user_info.credits,
	p = $('#paypalEmail').val();
	if(p == ''){
		swal({   title: "Error",   text: site_lang[589]['text'], type: "warning" }, function(){ });
		return false;		
	}
	if (user_info.payout < plugins['withdrawal']['minRequired']) {
		swal({   title: "Error",   text: site_lang[585]['text'], type: "warning" }, function(){ });
		return false;	    
	}  else {
	 swal({
	    title: site_lang[590]['text'],
	    text: site_lang[591]['text'] + ' ' + t + " " + plugins['settings']['currency'] + " " + site_lang[592]['text'] + " " + p,
	    imageUrl: user_info.profile_photo,
	    showCancelButton: !0,
	    confirmButtonText: site_lang[569]['text'],
	    closeOnConfirm: !1
	}, function() {
	     $.ajax({
	        type: "POST",
	        url: request_source() + "/api.php",
	        data: {
	            action: "withdraw",
	            paypal: p,
	            uid: user_info.id,
	            credits: e,
	            money: t
	        },
	        success: function(t) {
	            window.location.reload();
	        }
	    })
	})
	}
}
function discover100(){
	 if(user_info.credits < site_prices.discover){
		openWidget("purchaseCredits");
	 } else {
		$.ajax({ 
			type: "POST", 
			url: request_source() + "/belloo.php",
			data: {
				action : "discover100",
				price : site_prices.discover
			},
			success: function(response){
				window.location.href = site_config.site_url+'credits';
			}
		});	
	 }	
}

$('#add-sphoto,#add-sphoto2').on('click', function(e){
	 e.preventDefault();
	 if(user_info.credits < site_prices.spotlight){
		openWidget("purchaseCredits");
	 } else {
		$("#add-photo-spotlight").submit(); 	 			 
	 }
})
$('#add-photo-spotlight').submit(function(e) {
	e.preventDefault();	
	var photo = $('#s_photo').val();
	if(photo.length == 0){ alert(site_lang[197]['text']); return false};
	var uid = user_info.id;
	$.get( aUrl, {action: 'addToSpotlight', query: uid});

	window.location.href = site_config.site_url+'meet';		
});	

function manageUserMedia(pid,act=0,photo=''){
	if(act == 1){
		$.ajax({ 
			type: "POST",
			url: request_source() + "/api.php",
			data: {
				action : "manage",
				uid : user_info.id,				
				pid : pid,
				profile : 1,
				block : 0,
				unblock : 0,
				story : 0,				
				del : 0
			},
			success: function(response){
				window.location.reload();
			}
		});		
	}

	if(act == 2){
		$('[data-my-photo-id='+pid+']').remove();
		$('[data-my-media='+pid+']').remove();		
		$.ajax({ 
			type: "POST",
			url: request_source() + "/api.php",
			data: {
				action : "manage",
				uid : user_info.id,				
				pid : pid,
				profile : 0,
				block : 0,
				unblock : 0,
				story : 0,				
				del : 1
			},
			success: function(response){
				goToProfile(user_info.id);
			}
		});	
	}

	if(act == 4){
		var data = [];
		data.name = '';
		data.icon = '';
		data.message = site_lang[685]['text'];
		pushNotif(data,1);		
		$.ajax({ 
			type: "POST",
			url: request_source() + "/api.php",
			data: {
				action : "manage",
				uid : user_info.id,				
				pid : pid,
				profile : 0,
				block : 0,
				unblock : 0,
				story : 1,				
				del : 0
			},
			success: function(response){
			}
		});	
	}

	if(act == 3){
	    var checked = $('[data-my-media-private='+pid+']:checked').val();
	    console.log(checked);
	    if(checked != 'on'){
			$.ajax({ 
				type: "POST",
				url: request_source() + "/api.php",
				data: {
					action : "manage",
					uid : user_info.id,					
					pid : pid,
					profile : 0,
					block : 0,
					unblock : 1,
					story : 0,					
					del : 0
				},
				success: function(response){
				}
			});
	    } else {
			$.ajax({ 
				type: "POST",
				url: request_source() + "/api.php",
				data: {
					action : "manage",
					uid : user_info.id,					
					pid : pid,
					profile : 0,
					block : 1,
					unblock : 0,
					story : 0,					
					del : 0
				},
				success: function(response){
				}
			});
	    }		
	}
		
}
	
function filterBtn() {
	$('[data-open-meet-filter]').click(function(){
		url = $(this).attr('data-url');
		var h = $('.wall').height();
		$('.wall').height(h-225);
		$(this).hide();
		if(url == "search"){
			$('.search-post').show();
		} else {
			$('.wall-post').fadeIn();
			$('#update-status').focus();            
		}
		$('[data-close-filter]').show();
	});
	$('[data-close-filter]').click(function(){
		var h = $('.wall').height();
		$('.wall').height(h+225);
		if(url == "search"){
			$('.search-post').hide();
		} else {
			$('.wall-post').hide();           
		}        
		$('[data-close-filter]').hide();
		$('[data-open-meet-filter]').show();
	}); 
}
$('#refresh-notification').click(function(){
    $(this).toggleClass('rotate');
    $(this).toggleClass('rotate-rest');
});
$('.load-more').click(function(){
    var a = $('#user-spot').scrollTop();
	 $('#user-spot').mCustomScrollbar('scrollTo','-=500');														      
});     

//profilePhoto();

$(".wall").bind('scroll', function() {
   var top = $(this).scrollTop();
   if(top > 100 ){
   }else {
   }
});    
$("[class^=post-photo]").each(function(){
    var src = $(this).attr("data-src");
    $(this).css('background-image', 'url('+src+')');       
});
function photosChatWall(){
	$("[class^=post-photo]").each(function(){
		var src = $(this).attr("data-src");
		$(this).css('background-image', 'url('+src+')');       
	});		
	$("[class^=post-photo]").hover(function(){
		var instance = $(this).attr("data-instance");
		if ($("#viewer" + instance)[0]){ 
		} else {
			$(".fbphotobox-overlay").remove();
			$(".fbphotobox-main-container").remove();
			$(".fbphotobox-fc-main-container").remove();
			$(".fbphotobox-main-container").remove();
			if(url == "chat" || url == "profile"){
				$("[data-instance='" + instance +"']").fbPhotoBox({
					rightWidth: 0.1,
					leftBgColor: "black",
					rightBgColor: "white",
					footerBgColor: "black",
					overlayBgColor: "#1D1D1D",
					profile: false,
					instance: instance
				}); 
			} else {
				$("[data-instance='" + instance +"']").fbPhotoBox({
					rightWidth: 350,
					leftBgColor: "black",
					rightBgColor: "white",
					footerBgColor: "black",
					overlayBgColor: "#1D1D1D",
					profile: false,
					instance: instance
				});                
			}
		} 
	});
}
photosChatWall();
$('#profile-menu').dropdown({
  inDuration: 300,
  outDuration: 225,
  constrain_width: false, 
  hover: false, 
  alignment: 'center', 
  gutter: 0, 
  belowOrigin: false 
}
);    
function scroller(){
	$(".chat").mCustomScrollbar({
		autoHideScrollbar:true,
		theme:"dark-3",
		setTop: 100000,
		scrollButtons:{
			enable: true 
		},
		mouseWheel:{
			preventDefault: true,
			deltaFactor: 120
		}                
	});

		
	$(".scroll").mCustomScrollbar({
		autoHideScrollbar:true,
		theme:"dark-3",
		scrollbarPosition: "outside",			
		scrollButtons:{
			enable: true 
		},
		mouseWheel:{
			preventDefault: true,
			deltaFactor: 90
		}                
	}); 	

	$("[data-wide-spotlight-scroll]").mCustomScrollbar({
		autoHideScrollbar:true,		
        axis: "x",
        theme: "dark-3",
        alwaysShowScrollbar: 2,
        scrollbarPosition: "outside",
        advanced: {
            autoExpandHorizontalScroll: !0
        },
		mouseWheel:{
			preventDefault: true,
			deltaFactor: 180
		}                
	});

    $(".stories-discover-feed").mCustomScrollbar({
		autoHideScrollbar:true,		
        axis: "x",
        theme: "dark-3",
        alwaysShowScrollbar: 2,
        scrollbarPosition: "outside",
        advanced: {
            autoExpandHorizontalScroll: !0
        },
		mouseWheel:{
			preventDefault: true,
			deltaFactor: 120
		} 
    });

    $('[data-story-discover-next]').click(function(){
    	$(".stories-discover-feed").mCustomScrollbar("scrollTo", '-=740');	
    })
    $('[data-story-discover-prev]').click(function(){
    	$(".stories-discover-feed").mCustomScrollbar("scrollTo", '+=740');	
    })    
 	$(".stories-discover").show();	
	$("#user-spot").mCustomScrollbar({
		autoHideScrollbar:true,
		theme:"dark",
		scrollbarPosition: "outside",
		scrollButtons:{
			enable: true 
		},
		mouseWheel:{
			preventDefault: true,
			deltaFactor: 80
		}                
	});
}
scroller();
	

function chat_notification(){
	noti = 0;
	var curl = window.location.href; 	
	/*	
	$.ajax({ 
		type: "POST", 
		dataType: "JSON",
		url: request_source() + "/chat.php",
		data: {
			action : "notification",
			user : 0
		},
		success: function(response){
			response.forEach(function(entry) {
				if(noti == 0){
					$('#notiSound')[0].play();	
					noti = 1;
				}												  
				title = title+1;
				$('#chatCount').text(title); 
				$('#chatCount').fadeIn();
				document.title = '( '+title+' ) '+ site_title();
				if ( $('[data-chat='+entry+']').length ) {
				} else {
					new_message();
				}
			});
		},
		complete: function(){
			setTimeout(function(){ chat_notification(); }, 5000);
		}
	});	
	*/		
}
chat_notification();	
function new_message(){
		$.ajax({ 
			type: "POST", 
			url: request_source() + "/chat.php",
			data: {
				action : "new"
			},
			success: function(response){
				$('#chat-container').html(response);
				sidebarChat();
				profilePhoto();
				scroller();
				$('#chat-filter').val(5).change();
			}
		});		
}

function updateLastTypedTime() {
    lastTypedTime = new Date();
}

function refreshTypingStatus(user) {
	console.log('typing started');
    if (!$('#chat-message').is(':focus') || $('#chat-message').text() == '') {
       	var message = user_info.id+','+user+','+0;
       	console.log('typing ended');
       	$.get( gUrl, { action: 'typing', query: message } );
    } else {
    	var t = new Date().getTime() - lastTypedTime.getTime();
    	t = parseInt(t);
    	if( t > typingDelayMillis){
	    	updateLastTypedTime();
	    	console.log('typing more');
	    	if($('#chat-message').text().length > 2){
		    	var message = user_info.id+','+user+','+1;
				$.get( gUrl, { action: 'typing', query: message } );
	    	}
		}
    }
}

var endVideocallBind = 'videocall'+user_info.id;
channel.bind(endVideocallBind, function(data) {
	finishCall(false);  	   
});

function current_chat(user) {
	var mob = 0;
	if (mobile == true){
		mob = 1	
	}
	
	//channel.unbind(eventBind);
	channel.unbind();
	//setInterval(refreshTypingStatus, 1000);

	channel.bind(rtnotification, rtcallback);	
	typingBind = 'typing'+user_info.id+user;
    channel.bind(typingBind, function(data) {
    	if(data.t == 1){
    		//console.log('typing');
    		$('.iswriting').fadeOut(); 
    		$('.isRead').hide();
    		$('.iswriting').fadeIn();
    	} else {
			//console.log('stop typing');
			$('.iswriting').hide();
			$('.iswriting').hide();
    	}  	   
    });


	eventBind = 'chat'+user_info.id+user;	
    channel.bind(eventBind, function(data) {
		if(data.id == current_user_id){
			$('.iswriting').hide();
			$.ajax({
				data: {
					action: "read",
					id: data.id
				},		 
				url: request_source()+'/chat.php',	
				type:  'post',
				dataType: 'JSON',
				success: function(response) {	
				},
			});	

			$('.chat').mCustomScrollbar("destroy");
			$(".chat").append(data.chatHeaderRight);							
			$('#chatSound')[0].play();
			scroller();
			profilePhoto();	
		}     
    });
    /*	
	$.ajax({
		data: {
		action: "current",	
		uid: user,
		mobile: mob
		},		 
		url: request_source()+'/chat.php',	
		type:  'post',
		dataType: 'JSON',
		success: function(response) 
		{	
		if(response.result == 1){
				if(mobile == true){
					$(".list-chats").append(response.chat);
					$('html, body').animate({
						  scrollTop: $("#bottom").offset().top
					}, 1000);							
				} else {
					$('.chat').animate({
						  scrollTop: 10000
					}, 500);											
					$(".chat").append(response.chat);							
					$('#chatSound')[0].play();						
				}
				profilePhoto();
			}
		},
		complete: function() {		
			current_user_chat = setTimeout(function() { current_chat(current_user) }, 3000);	
		}
	});
	*/
}
$('[data-premium]').on("click", function() {
		if($('#payment_module').is(':visible')) {
			closeWidgetItem("purchaseCredits")
		} else {
			openWidget("purchaseCredits");
		}
});	
function rightChatLink(id,c){	
	$('.iswriting').hide();
	$('[data-chat]').removeClass('brick--outline');
	$('#user'+id).addClass('brick--outline');	
	$('#mark'+id).remove();		
	var value = c;		
	var a = parseInt($('#chatCount').text());
	var b = parseInt(value);
	var chatCount = a - b;
	$('#chatCount').text(chatCount);
	if(chatCount <= 0){ 
		$('#chatCount').hide();			
	}
	$('#chat-container').append(`
		<div id="chat-container-loader"
		style="position:absolute;left:0;top:0;width:100%;height:100%;background:rgba(255,255,255,.2);z-index:99"></div>
	`);
	ajaxLoad(1);
	current_user_id = id;

	if($('.send-gift').is(':visible') === false) {
		$('.send-gift').hide();
		setTimeout(function(){
			$('.send-gift').show();
		},1200);	
	}

	$.ajax({
		url: request_source()+'/user.php', 
		data: {
			action:"chat-inchat",
			id : id
		},	
		type: "post",
		dataType: 'JSON',			
		success: function(response) {
			$('.chat').mCustomScrollbar("destroy");
			$('#chat-container').html(response.chat);
			profile_info = response.profile;
			$('#giftUserName').html(profile_info.first_name);
			$('#chatHeader').html(response.chatHeader);
			$('#chatHeaderRight').html(response.chatHeaderRight);
			$('#chat-container').css("opacity","1");
			$('#r_id').val(profile_info.id);
			$('#rid').val(profile_info.id);
			$('#writingPhoto').attr('data-src',profile_info.profile_photo);
			$('#writingName').text(profile_info.first_name);
			$('#readPhoto').attr('data-src',profile_info.profile_photo);
			$('#readName').text(profile_info.first_name);
			if(response.read == 1){
				var a = site_lang[532]['text']+' <i class="icon ion-android-done-all" style="font-size:15px"></i>';
				$('#readStatus').html(a);
			} else {
				$('#readStatus').text(site_lang[533]['text']);
			}		
			current_user = profile_info.id;
			document.title = site_title();
			$("#chat-message").focus();
			profilePhoto();
			scroller();								
				
			game_btns2();
			var m = $( "#me" ).length;
			var y = $( "#you" ).length;
			if(m == 0){
				$('.isRead').hide();
			}
			if(m >= 1 && y <= 0){
				$('.chat-gifts').show();
			} else {
				$('.chat-gifts').hide();
			}
			

			ajaxLoad(2);		
			current_chat(profile_info.id);
			storyLoader(profile_info.story,profile_info.stories,profile_info.status_info);
			profilePhoto();
 			
			if(site_theme['design_style']['val'] == 'Left-Menu'){
				fullHeightChat(60);
			} else {
				fullHeightChat();
			}

			window.history.pushState("chat",site_title(),site_config.site_url+'chat/'+profile_info.id+'/'+profile_info.link);				
		}
	});			
}


function chatMessage(){
	$('#chat-send').click(function(e) {
		$('#c-send').submit();							 
	});
	

	var m = $( "#me" ).length;
	var y = $( "#you" ).length;
	if(m == 0){
		$('.isRead').hide();
	}
	if(m >= 1 && y <= 0){
		$('.chat-gifts').show();
	} else {
		$('.chat-gifts').hide();
	}
	$("#chat-message").on('keydown', function(e) {  
	    if(e.keyCode == 13){
	        e.preventDefault();
	        $('#c-send').submit();
	    }
	});	
	$("#chat-message").focus();
	$('#chat-message').on('input change', e=>{
	  //emojify($('#chat-message').get(0));
	});
	$('#send-photo').on('click', function(e){
		 e.preventDefault();
	    document.getElementById("uploadContent").click();
	    upType = 9;
	});
	$("#photo-to-send").change(function() {
		$("#sendPhoto").submit();
	});
	$('#sendPhoto').submit(function() { 
			$(this).ajaxSubmit(sendPhoto);  			
			return false; 
	});	
	var r_id = $('#r_id').val();
	$("#chat-message").on('keydown', function(e) {
		refreshTypingStatus(r_id)
	});
	current_chat(r_id);		
	$('#c-send').submit(function(e) {
		e.preventDefault();
		var r_id = $('#r_id').val();
		var messageVal = $("#chat-message").html().replace(/&nbsp;/g, '');		
		var mob = 0;
		if (mobile == true){
			mob = 1;
		}	
		var me = Math.floor(Math.random() * 10000000);  		
		if(messageVal.length == 0){ return false};		
		var str = $("#chat-message").text();	
		if(($.trim( str )).length==0){ return false};	

		if(plugins['chat']['creditsPerMessageEnabled'] == 'Yes'){

            if(user_info.credits < plugins['chat']['creditsPerMessage']){ 
                openWidget("purchaseCredits");
                return false;
            }	
            
            var data = [];
            data.name = '';
            data.icon = site_theme['notification_inapp_credits_icon']['val'];
            data.message = site_lang[610].text+' '+plugins['chat']['creditsPerMessage']+' ' + site_lang[73].text;
            
			if(plugins['chat']['creditsPerMessageGender'] == user_info.gender){
				updateCredits(user_info.id,plugins['chat']['creditsPerMessage'],1,'Credits for send chat message');
				pushNotif(data,1);
			}
			if(plugins['chat']['creditsPerMessageGender'] == allG){
				updateCredits(user_info.id,plugins['chat']['creditsPerMessage'],1,'Credits for send chat message');
				pushNotif(data,1);
			}	
			if(plugins['chat']['transferCreditsMessageToReciever'] == 'Yes' && plugins['chat']['creditsPerMessageGender'] == user_info.gender){
                updateCredits(r_id,plugins['chat']['creditsPerMessage'],2,'Credits for message recieved');                
            }
			if(plugins['chat']['transferCreditsMessageToReciever'] == 'Yes' && plugins['chat']['creditsPerMessageGender'] == allG){
                updateCredits(r_id,plugins['chat']['creditsPerMessage'],2,'Credits for message recieved');                
            }            
		}

		//hide gif modal
		showEmoji(0);
		openGif(0);


		//before send
		var n = $( ".js-message-block" ).length;
		$('.isRead').hide();
		var m = $( "#me" ).length;
		var y = $( "#you" ).length;
		if(m == 0){
			$('.isRead').hide();
		}				
		if(m == 0 && y == 0){
			newChat();
		}
		var message2 = messageVal;
		$('.chat').append('<div class="js-message-block" data-me="'+me+'" style="opcaity:0.5" id="me"><div class="message"><div class="brick brick--xsm brick--hover"><div class="brick-img profile-photo" data-src="'+ user_info.profile_photo +'"></div></div><div class="message__txt"><span class="lgrey message__time" style="margin-right: 15px;"></span><div class="message__name lgrey">'+ user_info.first_name +'</div><p class="montserrat chat-text">'+message2+'</p></div></div></div>	')					
		$('.chat').mCustomScrollbar("destroy");
		profilePhoto();
		scroller();							
		$('#chat-message').text("");
		if(m >= 1 && y <= 0){
			$('.chat-gifts').show();
		}	

		//send message
		var message = user_info.id+'[message]'+r_id+'[message]'+messageVal+'[message]text';
		var send = user_info.id+'[rt]'+r_id+'[rt]'+user_info.profile_photo+'[rt]'+user_info.first_name+'[rt]'+messageVal+'[rt]text';      

		$.get( gUrl, {action: 'message', query: send} );		
		$.get( aUrl, {action: 'sendMessage', query: message} );
	});	
}

function newChat(){
	$.ajax({ 
		type: "POST",
		url: request_source() + "/chat.php",
		data: {
			action : "today"
		},
		success: function(response){	
		}
	});			
}
function profileForms(){
	$('#add-credits').click(function(){
		if($('#payment_module').is(':visible')) {
			closeWidgetItem("purchaseCredits")
		} else {
			openWidget("purchaseCredits");
		}
	});

	$('[data-update-profile-info]').change(function(){
		$('#update-profile').submit();
	});

	$('#update-profile').submit(function(e) {
		e.preventDefault();	
		var findme = "Error";
		console.log($(this).serialize());
		$.ajax({ 
			data:  $(this).serialize(),
			url:   request_source()+'/user.php',
			type:  'post',
			beforeSend: function(){	

			$('#upd-btn').html(site_lang[648]['text']+'...');
			},
			success: function(response){
				$('#update-error').hide();
				$('#update-success').hide();				
				if ( response.indexOf(findme) > -1 ) {
					response = response.replace('Error','');
					$('#update-error').html(response);
					$('#update-error').show();
					$("#upd-btn").html(site_lang[135]['text']);
				} else {
					$('#editUsername').val($('#updateUsername').val());
					$('#editEmail').val($('#updateEmail').val());
					$('#update-success').html(site_lang[203]['text']);
					$('#update-success').show();
					$("#upd-btn").html(site_lang[135]['text']);
				}
				setTimeout(function(){
					$('#update-success').fadeOut();					
				},1050)					
			}
		});	
	});

	$('#change-pwd-btn').change(function(){
		$('#change-password').submit();
	});

	$('#change-password').submit(function(e) {
		e.preventDefault();	
		var findme = "Error";
		$.ajax({ 
			data:  $(this).serialize(),
			url:   request_source()+'/user.php',
			type:  'post',
			beforeSend: function(){	
			$('#change-pwd-btn').html(site_lang[648]['text']+'...');
			},
			success: function(response){
				if ( response.indexOf(findme) > -1 ) {
					swal({   title: site_lang[130]['text'],   text: response,   type: 'warning' }, function(){

					});
					$("#change-pwd-btn").html(site_lang[130]['text']);
				} else {
					swal({   title: site_lang[130]['text'],   text: site_lang[336]['text'],   type: 'success' }, function(){
						
					});					
					$("#change-pwd-btn").html(site_lang[130]['text']);
				}					
			}
		});	
	});				
}


function deleteProfile(){
	swal({
		title: site_lang[204]['text'],
		text: site_lang[205]['text'],
		confirmButtonText: site_lang[206]['text'],				
		type: "warning",
		showCancelButton: true,				
		},
		function(){
			$.ajax({ 
				data: {
					action: 'delete_profile',
					uid: user_info.id	
				},
				url:   request_source()+'/user.php',
				type:  'post',
				beforeSend: function(){	
				},
				success: function(response){
					window.location.href = site_config.site_url;
				}
			});	
		});		
}
profileForms();
function locInitialize() {
	if(url == 'meet'){
		TeleportAutocomplete.init('#loc').on('change', function(value) {
			var lat = value.latitude;
			var lng = value.longitude;
			var city = value.name;
			var country = value.country;
			var cityID = value.geonameId;

			if(lat != user_info.lat){
				locChanged(lat,lng,city,country);				
			}
			
		});	
	}					
}
locInitialize();
function locChanged(lat,lng,city,country){
	user_info.lat = lat;
	if(site_theme['design_style']['val'] == 'Left-Menu'){
		$('[data-menu-city]').text(city);
	}
	$.ajax({
		data: {
			action: "update_user_meet",
			lat: lat,
			lng: lng,
			city: city,			
			country: country 
		},
		url:   request_source()+'/belloo.php',
		type:  'post',
		success:  function (response) {

			$('[data-spotlight-update=0]').remove();
			$('[data-spotlight-update=1]').html(response);
			profilePhoto();
			$('.add-yourself').click(function(){
				$('#add-spotlight').show();
			});
		
			var currentHeight = $('.wall').height()+70;
			$('.left-menu-margin').css('height',currentHeight+'px');
		}
	});
}

function locInitializeSettings() {
	TeleportAutocomplete.init('#settingsLoc').on('change', function(value) {
		var lat = value.latitude;
		var lng = value.longitude;
		var city = value.name;
		var country = value.country;
		var cityID = value.geonameId;
		$('#locality').val(city);
		$('#lat').val(lat);	
		$('#lng').val(lng);
		$('#country').val(country);					
		if(lat != user_info.lat){
			locChanged(lat,lng,city,country);				
		}
		
	});	
}

locInitializeSettings();


function fillInAddress() {
  var place = autocomplete.getPlace();
  for (var component in componentForm) {
	document.getElementById(component).value = '';
	document.getElementById(component).disabled = false;
  }
var lat = place.geometry.location.lat(),
	lng = place.geometry.location.lng();
	document.getElementById('lat').value = lat;
	document.getElementById('lng').value = lng;
  for (var i = 0; i < place.address_components.length; i++) {
	var addressType = place.address_components[i].types[0];
	if (componentForm[addressType]) {
	  var val = place.address_components[i][componentForm[addressType]];
	  document.getElementById(addressType).value = val;
	}
  }
}
function game_start(val=0){
	if(val == 1){
		if(game_loading_profiles == false){
			game_loading_profiles = true;
			$.ajax({
				data: {
					action: "game",
					id: user_info.id
				},
				url:   request_source()+'/api.php',
				type:  'get',
				dataType: 'JSON',
				success:  function (response) {	
					response['game'].forEach(function(entry) {
						var photos = entry.photos;					  
						photos.forEach(function(photo) {
							$(".preload-photos").append("<img src="+photo.image+" style='opacity:0'/>");
						});												  
					});			
					game_array = game_array.concat(response['game']);
					game_array = removeDuplicates(game_array, "id");


					if(game_array_max > 11){
						$('[data-discover-next-people]').html('');
						var nextUsers = 5;
						if(game_array.length < 5){
							nextUsers = game_array.length;
						}
						for (var i = 1; i < nextUsers; i++) {
							var delay = i*50;
							$('[data-discover-next-people]').append('<div data-discover-next-people-curent="'+ game_array[i].id +'" class="brick brick--lg brick--stroke"><div class="brick-img profile-photo" data-src="'+game_array[i].photo+'" style="opacity:0.7"></div></div>');
						}
					}
					game_array_max = game_array.length;			
					game_loading_profiles = false;
				}
			});			
		}
	}
	if(game_array.length > 0){
			if(mobile == true){
				$("#dis_name").html('<a href="mobile.php?page=profile&id='+game_array[0].id+'"><b>'+ game_array[0].name +'</b>, '+ game_array[0].age +' <span style="font-size:12px;">'+ game_array[0].status +'</span></a>');
			} else {
				$("#dis_name").html('<a href="javascript:;" onclick="goToProfile('+game_array[0].id+')"><b>'+ game_array[0].name +'</b>, '+ game_array[0].age +'</a>');
				$("#dis_report").click(function(){ reportUser(game_array[0].id,'"'+game_array[0].name+'"','"'+game_array[0].photo+'"'); });
				$("#dis_block").click(function(){ reportUser(game_array[0].id,'"'+game_array[0].name+'"','"'+game_array[0].photo+'"'); });	
				$("#dis_chat").click(function(){ goToChat(game_array[0].id); });	
			}
			//$("#dis_profile_photo").attr('data-src', game_array[0].photo);
			$("[data-like-game]").attr('data-id', game_array[0].id);
			$('[data-game-photo]').attr('data-src', game_array[0].photo);
			if(mobile == true){
				$("#dis_distance").html(game_array[0].distance + ' KM');
			} else {
			  //$("#dis_fans").html(game_array[0].total);	
			}

			if(game_array_max > 11){
				$('[data-discover-next-people]').html('');
				var nextUsers = 5;
				if(game_array.length < 5){
					nextUsers = game_array.length;
				}
				for (var i = 1; i < nextUsers; i++) {
					var delay = i*50;
					$('[data-discover-next-people]').append('<div data-discover-next-people-curent="'+ game_array[i].id +'" class="brick brick--lg brick--stroke"><div class="brick-img profile-photo" data-src="'+game_array[i].photo+'" style="opacity:0.7"></div></div>');
				}
			}
		
			$("#dis_city").html(game_array[0].city);
			galleria_photos = game_array[0].photos;
			profilePhoto();			
			startGalleria(galleria_photos);	
			$("#discoverStories").attr("onclick","openStory("+game_array[0].story+","+game_array[0].id+")");
			storyLoader(game_array[0].story,game_array[0].stories,game_array[0].status);
				
	} else {
		console.log('discover game loading profiles');
		$.ajax({
			data: {
				action: "game",
				id: user_info.id
			},
			url:   request_source()+'/api.php',
			type:  'get',
			dataType: 'JSON',
			success:  function (response) {	
				
				if(response['game'] == 'error'){
					$('[data-discover-result=0]').fadeIn();
					$('[data-discover-result=1]').hide();
					return false;					
				}
				
				game_array = response['game'];
				game_array_max = game_array.length;
				console.log(game_array_max+' discover');
				console.log(game_array);
				game_array.forEach(function(entry) {
					var photos = entry.photos;					  
					photos.forEach(function(photo) {
						$(".preload-photos").append("<img src="+photo.image+" style='opacity:0'/>");
					});												  
				});				
				if(mobile == true){
				$("#dis_name").html('<a href="mobile.php?page=profile&id='+game_array[0].id+'"><b>'+ game_array[0].name +'</b>, '+ game_array[0].age +' <span style="font-size:12px;">'+ game_array[0].status +'</span></a>');
				} else {
				$("#dis_name").html('<a href="javascript:;" onclick="goToProfile('+game_array[0].id+')"><b>'+ game_array[0].name +'</b>, '+ game_array[0].age +'<div id="dcheck"></div></a>');
				$("#dis_report").click(function(){ reportUser(game_array[0].id,'"'+game_array[0].name+'"','"'+game_array[0].photo+'"'); });
				$("#dis_block").click(function(){ reportUser(game_array[0].id,'"'+game_array[0].name+'"','"'+game_array[0].photo+'"'); });	
				$("#dis_chat").click(function(){ goToChat(game_array[0].id); });	
				}
				//$("#dis_profile_photo").attr('data-src', game_array[0].photo);
				$("[data-like-game]").attr('data-id', game_array[0].id);
				$('[data-game-photo]').attr('data-src', game_array[0].photo);
				if(mobile == true){
				} else {
				  //$("#dis_fans").html(game_array[0].total);	
				}

				$('[data-discover-next-people]').html('');


				if(game_array_max > 11){
					var nextUsers = 5;
					if(game_array.length < 5){
						nextUsers = game_array.length;
					}
					for (var i = 1; i < nextUsers; i++) {
						var delay = i*50;
						$('[data-discover-next-people]').append('<div data-discover-next-people-curent="'+ game_array[i].id +'" class="brick brick--lg brick--stroke"><div class="brick-img profile-photo" data-src="'+game_array[i].photo+'" style="opacity:0.7"></div></div>');
					}
				}

				

				$("#dis_city").html(game_array[0].city);
				galleria_photos = game_array[0].photos;

				$("#discoverStories").attr("onclick","openStory("+game_array[0].story+","+game_array[0].id+")");
				storyLoader(game_array[0].story,game_array[0].stories,game_array[0].status);
						
				profilePhoto();			
				startGalleria(galleria_photos);
				
			}
		});	
	}

	if(game_array.length == 1){
		$('[data-discover-result=0]').fadeIn();
		$('[data-discover-result=1]').hide();
	} else {
		$('[data-discover-result=0]').hide();
		$('[data-discover-result=1]').fadeIn();
	}

}
function game_btns2(){
	if(url != 'discover'){
		$('[data-meet-user]').hover(function(){
			var id = $(this).attr('data-meet-user');
			$('[data-meet-button='+id+']').show();
		  },function(){
		  	var id = $(this).attr('data-meet-user');
			$('[data-meet-button='+id+']').hide();
		});		
	}


	$('[data-like-game]').click(function(){

		if(user_info.guest == 1){
			window.location.href= site_config.site_url+'logout';
			return false;
		}									  	
		var uid = $(this).attr('data-id');
		var val = $(this).attr('data-like-game');
		if(plugins['discover']['creditForLike'] > 0 && val == 1){

			if(user_info.credits < plugins['discover']['creditForLike']){ 
				openWidget("purchaseCredits");
				return false;
			}
			var data = [];
			data.name = '';
			data.icon = site_theme['notification_inapp_credits_icon']['val'];
			data.message = site_lang[610].text+' '+plugins['discover']['creditForLike']+' ' + site_lang[73].text;
			pushNotif(data,1);
			updateCredits(user_info.id,plugins['discover']['creditForLike'],1,'Credits for like');
			$('.top-menu-credits').removeClass('vivify');
			$('.top-menu-credits').removeClass('pulsate');
			setTimeout(function(){
				$('.top-menu-credits').addClass('vivify');
				$('.top-menu-credits').addClass('pulsate');			
			},50)			
		}

		$(this).removeClass('vivify');
		$(this).removeClass('pulsate');
		$(this).removeClass('popIn');	
		$('[data-game-photo]').removeClass('vivify');
		$('[data-game-photo]').removeClass('popIn');			
		setTimeout(function(){
			$('[data-game-photo]').addClass('vivify');
			$('[data-game-photo]').addClass('popIn');			
		},200)
		setTimeout(function(){
			$('[data-like-game='+val+']').addClass('vivify');
			$('[data-like-game='+val+']').addClass('pulsate');			
		},80)		

		if(url == 'discover'){
			profilePhoto();
			setTimeout(function(){
				game_array.splice(0, 1);

				if(game_array.length == 15){
					game_start(1);
				} else {
					game_start();	
				}

				console.log(game_array.length);
				if(game_array.length == 0){
					$('[data-discover-result=0]').fadeIn();
					$('[data-discover-result=1]').hide();					
				}
				$('[data-discover-next-people-current='+game_array[0].id+']').addClass('vivify swoopOutLeft');
				setTimeout(function(){
					$('[data-discover-next-people-current='+game_array[0].id+']').remove();	
				},100)
				
			},250);		
		}

		if(url == 'meet'){
			setTimeout(function(){
				$('[data-meet-button="meetLikeBtn'+uid+'"]').addClass('vivify');
				$('[data-meet-button="meetLikeBtn'+uid+'"]').addClass('pulsate');
				if(val == 1){
					$('[data-meet-button="meetLikeBtn'+uid+'"]').attr('data-like-game',0);
					$('[data-meet-button="meetLikeBtn'+uid+'"]').html('<svg class="icon" viewBox="0 0 60 60" style="width: 40px;height: 40px"><polygon fill="currentColor" points="45.82 16.12 43.7 14 29.91 27.79 16.12 14 14 16.12 27.79 29.91 14 43.7 16.12 45.82 29.91 32.03 43.7 45.82 45.82 43.7 32.03 29.91"></polygon></svg>');
				} else {
					$('[data-meet-button="meetLikeBtn'+uid+'"]').attr('data-like-game',1);
					$('[data-meet-button="meetLikeBtn'+uid+'"]').html('<svg fill="#fff" viewBox="0 0 32 32" width="100%" height="100%"><path fill="#ff5722" d="M16 12.06a3.85 3.85 0 0 0-3.12-1.61 3.94 3.94 0 0 0-3.88 4c0 3.6 4.69 8.13 7 8.13 2.31 0 7-4.53 7-8.14 0-2.2-1.74-3.99-3.88-3.99-1.24 0-2.4.6-3.12 1.61z"></path></svg>');
				}
				if(url == 'meet'){
					if(val == 0){
						$('[data-you-like='+uid+']').hide();	
					} else {
						$('[data-you-like='+uid+']').show();
					}
				}						
			},50)				
		}

		if(url == 'profile'){
			if(val == 1){
				$(this).hide();
				$('[data-like-game=0]').show();
			} else {
				$(this).hide();
				$('[data-like-game=1]').show();
			}
		}

		$.get( aUrl, { action: 'game_like', uid1: user_info.id, uid2: uid, uid3: val } );					
	});				
}


function likeFromNotification(uid){
	if(plugins['discover']['creditForLike'] > 0){

		if(user_info.credits < plugins['discover']['creditForLike']){ 
			openWidget("purchaseCredits");
			return false;
		}
		var data = [];
		data.name = '';
		data.icon = site_theme['notification_inapp_credits_icon']['val'];
		data.message = site_lang[610].text+' '+plugins['discover']['creditForLike']+' ' + site_lang[73].text;
		pushNotif(data,1);
		updateCredits(user_info.id,plugins['discover']['creditForLike'],1,'Credits for like');
		user_info.credits = user_info.credits - plugins['discover']['creditForLike'];
		$('.userCredits').text(user_info.credits);
		$('.top-menu-credits').removeClass('vivify');
		$('.top-menu-credits').removeClass('pulsate');
		setTimeout(function(){
			$('.top-menu-credits').addClass('vivify');
			$('.top-menu-credits').addClass('pulsate');			
		},50)			
	}	
	$.get( aUrl, { action: 'game_like', uid1: user_info.id, uid2: uid, uid3: 1 } );	
}

function giftBtn(){
	$('.send-gift').click(function(){
		var gsrc = $(this).attr('data-src');
		gift_price = $(this).attr('data-gprice');
		$('#g_src').val(gsrc);
		$('#g_id').val(profile_info.id);
		$('#g_price').val(gift_price);
		$('[data-gift-img]').attr('src',gsrc);
		$('[data-gift-price]').text(gift_price);
		$('#send-gift').show();	
		
	});	
	$('#g_close').click(function(e){
		e.preventDefault();
		$('#send-gift').hide();
	});
	
	
	$('#carousel-gifts').slick({
		  centerMode: false,
		  centerPadding: '60px',
		  slidesToShow: 6,
		  slidesToScroll: 1,
		  responsive: [
		    {
		      breakpoint: 768,
		      settings: {
		        arrows: false,
		        centerMode: true,
		        centerPadding: '40px',
		        slidesToShow: 3
		      }
		    },
		    {
		      breakpoint: 480,
		      settings: {
		        arrows: false,
		        centerMode: true,
		        centerPadding: '40px',
		        slidesToShow: 1
		      }
		    }
		  ],
		  dots: false,
		  arrows:false,		
		  autoplay: true,
		  autoplaySpeed: 500,
	});	
}

$('#send-gift-btn').on('click', function(e){
	 e.preventDefault();

 	if(url == 'live'){
 		if(streamGiftCredits < user_info.credits){
			var data = user_info.id+','+user_info.first_name+','+user_info.profile_photo+','+viewStreamId+','+streamGiftIcon+','+streamGiftCredits;
			$.get(request_source()+'/live.php', {action: 'sendLiveGift', query: data} );

			$('#send-gift').hide();
			$('#showGiftModal').hide();
			user_info.credits = user_info.credits - streamGiftCredits;
			$('.userCredits').hide();
			$('.userCredits').show();
			$('.userCredits').html('<b>'+user_info.credits+'</b>');
		} else {
			$('#send-gift').hide();
			openWidget("purchaseCredits");
		}	  		
 	} else {
		if(gift_price > user_info.credits){
			$('#send-gift').hide();
			openWidget("purchaseCredits");
		} else {
			$("#send-gift-form").submit(); 			 	
		}
 	}
});
$('#send-gift-form').submit(function(e) {
	e.preventDefault();	
	var gift = $('#g_src').val();
	var findme = 'error';
	if(gift.length == 0){ alert(site_lang[198]['text']); return false};

	var me = Math.floor(Math.random() * 10000000);  
	$('.chat').append('<div class="js-message-block" data-me="'+me+'" style="opcaity:0.5" id="me"><div class="message"><div class="brick brick--xsm brick--hover"><div class="brick-img profile-photo" data-src="'+ user_info.profile_photo +'"></div></div><div class="message__txt"><span class="lgrey message__time" style="margin-right: 15px;"></span><div class="message__name lgrey">'+ user_info.first_name +'</div><p class="montserrat chat-text"><div class="message__pic_" style="cursor:pointer;border:none"><img  src="'+gift+'" /></div></p></div></div></div>	')					
	$('.chat').mCustomScrollbar("destroy");
	profilePhoto();
	scroller();	
	var data = [];
	data.name = '';
	data.icon = gift;
	data.message = site_lang[610].text+' '+gift_price+' ' + site_lang[73].text;
	pushNotif(data,1);
	var r_id = $("#r_id").val();
	var messageVal = gift;
	var message = user_info.id+'[message]'+r_id+'[message]'+messageVal+'[message]gift[message]'+gift_price;  
	var send = user_info.id+'[rt]'+r_id+'[rt]'+user_info.profile_photo+'[rt]'+user_info.first_name+'[rt]'+messageVal+'[rt]gift';      
	$('#send-gift').hide();
	$('#showGiftModal').hide();

	user_info.credits = user_info.credits - gift_price;
    $('.userCredits').hide();
    $('.userCredits').show();
    $('.userCredits').html('<b>'+user_info.credits+'</b>');

	$.get( gUrl, {action: 'message', query: send} );		   	
	$.get( aUrl, {action: 'sendMessage', query: message} );

	if(plugins['chat']['transferCreditsGiftToReciever'] == 'Yes'){
        updateCredits(r_id,gift_price,2,'Credits for gift recieved');                
    }

});
function showChatGifts(){
	$('#giftUserNameModal').text(profile_info.first_name);
	$('#giftUserPhoto').attr('data-src',profile_info.profile_photo);
	$('#showGiftModal').show();
	profilePhoto();
}




//Videocall system
function updatePeer(peer) {
  $.ajax({
	url: request_source()+'/videocall.php', 
	data: {
		action:"update",
		peer: peer
	 },	
	type: "post",
	success: function(data) {	
	}
  });
}
function setInVideoCall() {
  clickedStartVideocall = false;
  $.ajax({
	url: request_source()+'/videocall.php',
	data: {
		action:"invideocall"
	 },			
	type: "post",
	success: function(data) {	
	}
  });
}
function saveCall(c_id,r_id){
    $.ajax({
      url: request_source()+'/videocall.php',
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
      url: request_source()+'/videocall.php',
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
	url: request_source()+'/videocall.php',
	data: {
		action:"callStatus",
		callId: callId
	 },			
	type: "post",
	success: function(data) {	}
  });
}
function peerConnect(con) {
	if(con == 1){
		peer.destroy();

	}
   			
	peer = new Peer({secure:true});
	peer.on('open', function(){
		updatePeer(peer.id);
		endedVideocall = false;
		console.log('videocall active'+ peer.id);
	});
	peer.on('close', function(){
		//console.log('videocall closed');
	});	
	peer.on('disconnected', function() {
		//console.log('videocall disconnected');										 
	});	
	peer.on('call', function(call){
		$.ajax({
			url: request_source()+'/videocall.php', 
			data: {
			action: "income",
			peer: call.peer,		
			 },		
			type: "post",
			dataType: 'JSON',	
			success: function(data) {
				$("#turn-video").click(function(){
					$(this).toggleClass('on');
					window.localStream.getVideoTracks()[0].enabled = !(window.localStream.getVideoTracks()[0].enabled);
					var check = $(this).hasClass( "on" );
					if(check === true){
						$('.profile-photo1 video').hide();
						$('.profile-photo1 img').show();							
					}else {
						$('.profile-photo1 video').show();
						$('.profile-photo1 img').hide();							
					}
				});
				$("#turn-mic").click(function(){
						$(this).toggleClass('on');					  
						window.localStream.getAudioTracks()[0].enabled = !(window.localStream.getAudioTracks()[0].enabled);
				});				
				video_user = data.id;
				$('#call-name').html(data.name);				
				$('.ball-container').css("background-image",'url(' + data.photo + ')');
				$('.videopb').css("background-image",'url(' + data.photo + ')');
				$('#text_videocall').html(data.name+" " +site_lang[337]['text']);
				$('.videocall-notify').fadeIn();
				
				callId = call.peer+peer.id;
				callSound = setInterval(function(){
					$('#callSound')[0].play();
				},4000);
				setTimeout(function() {
				 $('body').toggleClass('anim-start');
				}, 100);
				console.log(call)
				$("#acept-video").click(function(){ aceptcall(call); });
				$("#reject-video").click(function(){ rejectVideo(call.peer); });
				call.on('close', function(){
					if(endedVideocall === false) {						
						finishCall(false);
					} 					
					console.log('call ended');
				});					
			}
		});						 
		
	});
	peer.on('error', function(err){
		if(err.type === "network") {						
		} else {
		}
	});
}

if(plugins['videocall']['enabled'] == "Yes"){
	peerConnect(0);	
}

function rejectVideo(rejected){		
	peerConnect(1);
	$('.videocall-notify').fadeOut();
	in_videocall = false;
	setTimeout(function() {
 		$('body').toggleClass('anim-start');
	}, 2000);
	clearInterval(callSound);
	$('#callSound')[0].pause();	

}

setInterval(function(){ 
	if(in_videocall === false){
		if(plugins['videocall']['enabled'] == "Yes"){
				peerConnect(1);
			}
		} 
}, 50000);

function videocall(callfrom,callto) {
var pid = "";	
$.ajax({
	url: request_source()+'/videocall.php', 
	data: {
	action: "getpeerid",
		id: callto,		
	},		
	type: "post",
	dataType: 'JSON',	
	success: function(data) {	
			pid = data.peer;
			callId = peer.id+pid;
			if(data.status == 2) {
				swal({   title: "</3",   text: data.name + ' ' + site_lang[207]["text"],   imageUrl: data.photo }, function(){ });
				return false;
			}
			else if(data.status == 0) {	
				swal({   title: "T_T",   text: data.name + ' ' + site_lang[208]["text"],   imageUrl: data.photo }, function(){ });
				return false;
			} else {

			$('#call_status').html(site_lang[209]["text"]);
			$('#call-name').html(data.name);	
			startVideoCall(pid);
			$('.videocall-container').show();
			callSound = setInterval(function(){
				$('#callSound')[0].play();
			},4000);
			video_user = data.id;
			$('.profile-photo2').prop('src', data.photo);
			$('.videopb').css("background-image",'url(' + data.photo + ')');
			$('.avatar-profile').hide();				   
			$('.profile-photo2').animate({								 
				left: '39%',
				top: '25%'
				}, 1000, function() {
			});					
					
		}			
	}
});	
}

function videocallBtn(){

	$('.ms-emoji').msEmoji({
		input: ".ms-emoji-input",
		lang: ""
	});

	$( "#videocall" ).click(function(e) {
		e.preventDefault();
		if(clickedStartVideocall){
			return false;
		}
		clickedStartVideocall = true;		
		console.log('starting videocall');
		if(in_videocall === true) {
			swal({   title: "Error", text: site_lang[210]["text"],   type: "error"}, function(){ });	
			return false;	
		}	
		if(user_info.premium == 0 && account_basic.videocall == 0 && plugins['videocall']['freeUserCall'] == 'No') {
			var dataNotif = [];
			dataNotif.name = site_lang['449']['text'];
			dataNotif.icon = user_info.profile_photo;
			dataNotif.message = site_lang['211']['text'];
			pushNotif(dataNotif,1);
			goTo("premium");
			return false;	
		}			
		video_user = $('#r_id').val();
		videocall_user = $('#r_id').val();
		$.ajax({
			url: request_source()+'/videocall.php', 
			data: {
				action:"check",
				id: videocall_user
			 },	
			type: "post",
			success: function(data) {	
				if(data == 1){
					if(plugins['videocall']['creditsPerMinEnabled'] == "Yes"){
						swal({
							title: site_lang[381]['text']+" "+profile_info.first_name,
							text: site_lang[611]['text']+" "+ plugins['videocall']['creditsPerMin'] +' '+ site_lang[612]['text'],
							imageUrl: profile_info.profile_photo,
							showCancelButton: true,
						}, function(isConfirm){
							if (!isConfirm){
								return false;	
							} else {
								if(user_info.credits < plugins['videocall']['creditsPerMin']){ 
									openWidget("purchaseCredits");
									return false;
								}	
								var data = [];
								data.name = '';
								data.icon = site_theme['notification_inapp_credits_icon']['val'];
								data.message = site_lang[610].text+' '+plugins['videocall']['creditsPerMin']+' ' + site_lang[73].text;
								pushNotif(data,1);
								updateCredits(user_info.id,plugins['videocall']['creditsPerMin'],1,'Credits for Videocall');
						    	

								//start videocall
								videocall(peer.id,$('#r_id').val());

					            if(plugins['videocall']['creditsPerSecondTransfer'] == 'Yes'){
					                updateCredits(video_user,plugins['videocall']['creditsPerMin'],2,'Credits for videocall per min');                
					            }									
							}
						});	
					} else {

						//start videocall					
						videocall(peer.id,$('#r_id').val());
					}
				} else {
					swal({   title: profile_info.first_name+' ' +site_lang[382]["text"] ,   text: profile_info.first_name+' '+ site_lang[383]["text"],   imageUrl: profile_info.profile_photo,   showCancelButton: false,  confirmButtonText: site_lang[384]["text"],   closeOnConfirm: true }, function(){});						
				}
			}
		});	
	});	
}

function aceptcall(call) {
	
	//$('#callSound')[0].pause();
	var video = document.querySelector("#my-video");	
	if (navigator.mediaDevices.getUserMedia) {       
	    navigator.mediaDevices.getUserMedia({audio: true, video: true})
	  .then(function(stream) {
		$('#call_status').html(site_lang[209]["text"]);	
		$('.videocall-notify').fadeOut();										   
		$('.videocall-container').fadeIn();	
		$('.profile-photo2').animate({								 
			left: '39%',
			top: '25%'
			}, 1000, function() {
	  	});	
	  	in_videocall = true;
	  	updateCallStatus(callId);
	  	called = true;
		//record
		window.stream = stream;	
		if(plugins['recordVideocall']['enabled'] == 'Yes'){
			if(plugins['recordVideocall']['filterGender'] == user_info.gender){
				startRecording();
			}
			if(plugins['recordVideocall']['filterGender'] == allG){
				startRecording();
			}			
		}
		video.srcObject = stream;
		
	    window.localStream = stream;
		call.answer(stream);
		makeTheCall(call);
	  })
	  .catch(function(error) {

	  	console.log(error);
	  	swal({
			title: site_lang[212]["text"],
			text: site_lang[213]["text"],
			type: "error",
			},
			function(isConfirm){
				if (isConfirm){
					location.reload();	
				}
			});
	  });
	}  		

}


var clickedStartVideocall = false;
function startVideoCall (callto) {
  // Get audio/video stream
  	var video = document.querySelector("#my-video");
  	called = false;
 
	if (navigator.mediaDevices.getUserMedia) {       
	    navigator.mediaDevices.getUserMedia({audio: true, video: true})
	  .then(function(stream) {
		video.srcObject = stream;
		window.stream = stream;
		//record
		if(plugins['recordVideocall']['enabled'] == 'Yes'){
			if(plugins['recordVideocall']['filterGender'] == user_info.gender){
				startRecording();
			}
			if(plugins['recordVideocall']['filterGender'] == allG){
				startRecording();
			}			
		}
		saveCall(user_info.id,video_user);
		in_videocall = true;
		

		setInVideoCall();

	    $('#call_status').html(site_lang[381]["text"]);		
		$("#turn-video").click(function(){
			$(this).toggleClass('on');
			window.localStream.getVideoTracks()[0].enabled = !(window.localStream.getVideoTracks()[0].enabled);
			var check = $(this).hasClass( "on" );
			if(check === true){
				$('.profile-photo1 video').hide();
				$('.profile-photo1 img').show();							
			}else {
				$('.profile-photo1 video').show();
				$('.profile-photo1 img').hide();							
			}
		});
		$("#turn-mic").click(function(){
				$(this).toggleClass('on');					  
				window.localStream.getAudioTracks()[0].enabled = !(window.localStream.getAudioTracks()[0].enabled);
		});	
	    window.localStream = stream;
		var call = peer.call(callto, window.localStream);
		makeTheCall(call);
	  })
	  .catch(function(err) {
	    console.log(err); 
	    if (err.name == "NotFoundError" || err.name == "DevicesNotFoundError") {
	        //required track is missing 
	    } else if (err.name == "NotReadableError" || err.name == "TrackStartError") {
	        //webcam or mic are already in use 
	    } else if (err.name == "OverconstrainedError" || err.name == "ConstraintNotSatisfiedError") {
	        //constraints can not be satisfied by avb. devices 
	    } else if (err.name == "NotAllowedError" || err.name == "PermissionDeniedError") {
	        //permission denied in browser 
	    } else if (err.name == "TypeError" || err.name == "TypeError") {
	        //empty constraints object 
	    } else {
	       console.log('unknown error');
	    }
	  	swal({
			title: site_lang[212]["text"],
			text: site_lang[213]["text"],
			type: "error",
			},
			function(isConfirm){
				if (isConfirm){
					finishCall(false,'Yes');
					clickedStartVideocall = false;
				}
			});
	  });
	}  
}

function finishCallRT(r_id,duration){
	var messageVideocall = r_id+',';
	$.get( gUrl, {action: 'endVideocall', query: messageVideocall} );

	var messageVal = site_lang[857]['text']+' '+duration;

	var message = user_info.id+'[rt]'+r_id+'[rt]'+messageVal+'[rt]videocall';
	$.get( aUrl, {action: 'sendMessage', query: message} );	

	var send = user_info.id+'[rt]'+r_id+'[rt]'+user_info.profile_photo+'[rt]'+user_info.first_name+'[rt]'+messageVal+'[rt]videocall';      
	$.get( gUrl, {action: 'message', query: send} );		
	
	$('.chat').append(`
		<div class="js-message-block" id="me">
			<div class="message">
				<div class="brick brick--xsm brick--hover">
					<div class="brick-img profile-photo" style="background-image:url(`+user_info.profile_photo+`)"></div>
				</div>
				<div class="message__txt">
					<span class="lgrey message__time" style="margin-right: 15px;"></span>
					<div class="message__name lgrey">`+ user_info.first_name +`</div>
					<p class="montserrat chat-text">`+messageVal+`</p>
				</div>
			</div>
		</div>`);	
	$('.chat').mCustomScrollbar("destroy");
	scroller();
}

function finishCall(val,fromError='No') {
	var minu = $('#minutes').text();
	var secu = $('#seconds').text();	

	var c_id;
	var r_id;
	console.log('call id:'+callId);
	if(called){
		c_id = video_user;
		r_id = user_info.id;
	} else {
		c_id = user_info.id;
		r_id = video_user;
	}

	if(val === true){
		var duration = minu + ':'+ secu;
		finishCallRT(video_user,duration);		
		endCall(callId,minu,secu,sec);
	}


	
	console.log('caller id:'+c_id);
	sec = 0;
	clearInterval(window.timer);
	clearInterval(callSound);

	$('#minutes').text('00');
	$('#seconds').text('00');


	$('.videocall-notify').fadeOut();
	$('.videocall-container').fadeOut();
	in_videocall = false;
	endedVideocall = val;

	$('.profile-photo2').animate({								 
		left: '380px',
		top: '130px'
		}, 100, function() {
	});
	$('.videopb').css('background-color','#000');
	$('.profile-photo1').animate({								 
		left: '37%',
		top: '54%'
		}, 500, function() {
			$('.profile-photo2').show();
			$('.video').hide();
	});	


	$('.videocall-container').fadeOut();
	$('#message_status').remove();
	
	if (typeof window.localStream !== 'undefined'){
	    window.localStream.getAudioTracks().forEach(function(track) {
	        track.stop();
	    });

	    window.localStream.getVideoTracks().forEach(function(track) {
	        track.stop();
	    });
	}
	
	if (typeof window.stream !== 'undefined'){
	    window.stream.getAudioTracks().forEach(function(track) {
	        track.stop();
	    });
	    window.stream.getVideoTracks().forEach(function(track) {
	        track.stop();
	    });	
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
	
	if(fromError == 'No'){
		if(plugins['recordVideocall']['enabled'] == 'Yes'){
			if(plugins['recordVideocall']['filterGender'] == user_info.gender){
				if(called){
					stopRecording(user_info.id,callId,true);
				}else{
					stopRecording(user_info.id,callId,false);	
				}
			}
			if(plugins['recordVideocall']['filterGender'] == allG){
				if(called){
					stopRecording(user_info.id,callId,true);
				}else{
					stopRecording(user_info.id,callId,false);	
				}
			}			
		}
	}	
}
function makeTheCall (call) {
  var in_call = false;
  
  var video1 = document.querySelector("#their-video");
  var video2 = document.querySelector("#video-chat");

  var photo = $('.profile-photo2').attr('src');
	  
	call.on('stream', function(stream){

		
		in_call = true;
		in_videocall = true;
		setInVideoCall();
		console.log(stream);
		clearInterval(callSound);
		$('#callSound')[0].pause();		
		video1.srcObject = stream;
		video2.srcObject = stream;
		$('.videopb').css('background-color','#000');
		$('.profile-photo1').animate({								 
			left: '80%',
			top: '80%'
			}, 500, function() {
				$('.profile-photo2').hide();
				$('.video').show();
		});
		$('.video-control').animate({								 
			top: '80%'
			}, 200, function() {
		});		
		$('#chat-call').on('click', function() {
		  $('.videocall-chat').show();
		  $('.videocall-chat').draggable();
		  $('.videocall-container').hide();
		});				
	});
	clearInterval(window.timer);
	var videocallTimer = 0;
	window.timer = setInterval(function () {
		document.getElementById("seconds").innerHTML = pad(++sec % 60);
		document.getElementById("minutes").innerHTML = pad(parseInt(sec / 60, 10));	
		if(!called && plugins['videocall']['creditsPerMinEnabled'] == "Yes" && videocallTimer == 60){
			videocallTimer = 0;
	        var noti_data = [];
	        noti_data.name = '';
            if(user_info.credits < plugins['videocall']['creditsPerMin']){
                finishCall(true);
                return false;
            }              
            noti_data.icon = site_theme['notification_inapp_credits_icon']['val'];
            noti_data.message = site_lang[612]['text']+'<br>'+site_lang[610].text+' '+plugins['videocall']['creditsPerMin']+' ' + site_lang[73].text;
            updateCredits(user_info.id,plugins['videocall']['creditsPerMin'],1,'Credits for videocall per minute'); 
	        pushNotif(noti_data,1);	
            if(plugins['videocall']['creditsPerSecondTransfer'] == 'Yes'){
                updateCredits(video_user,plugins['videocall']['creditsPerMin'],2,'Credits for videocall per min');                
            }	        		
		}
		videocallTimer = videocallTimer + 1;
	}, 1000);	
	window.existingCall = call;
	call.on('close', function(){
		console.log('call ended');
	});	
  
}	
$( ".videocall-chat" ).dblclick(function() {
	  $('.videocall-chat').hide();
	  $('.videocall-container').fadeIn();
});	
$( "#their-video" ).dblclick(function() {
	  $('.videocall-chat').fadeIn();
	  $('.videocall-chat').draggable();
	  $('.videocall-container').hide();
});	
function clean_galleria() {
	var data = [];
	Galleria.run('.gall', {
		dataSource: data
	});		
}	
function escapeHtml(text) {
  var map = {
	'&': '&amp;',
	'<': '&lt;',
	'>': '&gt;',
	'"': '&quot;',
	"'": '&#039;'
  };
  return text.replace(/[&<>"']/g, function(m) { return map[m]; });
}

$('body').keyup(function(e) {
		switch (e.keyCode) {
			case 13:  
				if(url == 'profile'){
					if($('#searchBox').is(':focus')){
						var html = $('#new-int').html();
						var name = $('#searchBox').val();
						$('#new-int').html(html+'<div class="int"><span>'+ $('#searchBox').val() +'</span></div>');
						$('#searchBox').val('');
						$('#searchResults').addClass('hiddden');
						$.ajax({
							url: request_source()+'/user.php', 
							data: {
								action:"add_interest",
								name: name
							},	
							type: "post",			
							success: function(response) {
							}
						});						
					}
				}
			break;			
		}
});		
function pad(val) {
	return val > 9 ? val : "0" + val;
} 
var w = window,
	d = document,
	e = d.documentElement,
	g = d.getElementsByTagName('body')[0],
	x = w.innerWidth || e.clientWidth || g.clientWidth,
	y = w.innerHeight|| e.clientHeight|| g.clientHeight;
var left = x/2 - 38;
$("#sendmessage input").focusout(function(){
	if($(this).val() == ""){
		$(this).val("Send message...");
	}
});
$("#sendmessage input").focus(function(){
	if($(this).val() == "Send message..."){
		$(this).val("");
	}
});	
$(".friend").each(function(){		
	$(this).click(function(){
		var childOffset = $(this).offset();
		var parentOffset = $(this).parent().parent().offset();
		var childTop = childOffset.top - parentOffset.top;
		var clone = $(this).find('img').eq(0).clone();
		var top = childTop+12+"px";
		$(clone).css({'top': top}).addClass("floatingImg").appendTo("[data-chatbox]");									
		setTimeout(function(){$("#profile p").addClass("animate");$("#profile").addClass("animate");}, 100);
		setTimeout(function(){
			$("#chat-messages").addClass("animate");
			$('.cx, .cy').addClass('s1');
			setTimeout(function(){$('.cx, .cy').addClass('s2');}, 100);
			setTimeout(function(){$('.cx, .cy').addClass('s3');}, 200);			
		}, 150);														
		$('.floatingImg').velocity({
			'width': "68px",
			'left': left,
			'top':'20px'
		}, 200);
		var name = $(this).find("p strong").html();
		var email = $(this).find("p span").html();														
		$("#profile p").html(name);
		$("#profile span").html(email);			
		$(".message").not(".right").find("img").attr("src", $(clone).attr("src"));									
		$('#friendslist').velocity("fadeOut", { duration: 300 });
		$('#chatview').velocity("fadeIn", { duration: 500 });
		$('#close').unbind("click").click(function(){				
			$("#chat-messages, #profile, #profile p").removeClass("animate");
			$('.cx, .cy').removeClass("s1 s2 s3");
			$('.floatingImg').velocity({
				'width': "40px",
				'top':top,
				'left': '12px'
			}, 200, function(){$('.floatingImg').remove()});				
			setTimeout(function(){
				$('#chatview').velocity("fadeOut", { duration: 300 });
				$('#friendslist').velocity("fadeIn", { duration: 500 });			
			}, 50);
		});
	});
});			


var inputs = document.querySelectorAll('.file-input')

for (var i = 0, len = inputs.length; i < len; i++) {
  customInput(inputs[i])
}

function customInput (el) {
  const fileInput = el.querySelector('[type="file"]')
  const label = el.querySelector('[data-js-label]')
  
  fileInput.onchange =
  fileInput.onmouseout = function () {
    if (!fileInput.value) return
    
    var value = fileInput.value.replace(/^.*[\\\/]/, '')
    el.className += ' -chosen'
    label.innerText = value
  }
}


 
  
function createPreview(file, fileContents,id,custom='') {
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

	if(custom == ''){
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

	if(custom == 'chat'){
		var $displayElement = $('<div class="js-message-block" id="me">\
			<div class="message">\
	        	<div class="brick brick--xsm brick--hover">\
	        	<div class="brick-img profile-photo" data-src="'+user_info.profile_photo+'"></div>\
	        </div>\
			<div class="message__txt">\
				<div class="message__name lgrey">'+user_info.first_name+'</div>\
				<p class="montserrat chat-text"></p>\
				<div class="message__pic_" style="cursor:pointer;">\
					<div class="progress" data-upload-progress="upload'+id+'" style="z-index:99"><div class="determinate" id="upload'+id+'" style="width: 0%"></div></div>\
				</div>\
	        </div>\
	    </div>\
	    </div>\
	    </div>\
	    </div>');
		$displayElement.find('.message__pic_').append($previewElement);
		$('.chat').append($displayElement);			
	}
}


function checkScrollBars(overflow){
	$('body').scrollTop(0);
	fullHeightWall();
	if(overflow == 'hidden'){
		$('body').css('overflow-x','hidden');
		$('body').css('overflow-y','hidden');
		$('body').css({marginRight:'7px'});
		if(site_theme['design_style']['val'] == 'Top-Menu'){
			$('.header').css({paddingRight:'17px'});
		}
	} else {
		$('body').css('overflow-x','hidden');
		$('body').css('overflow-y',overflow);
		$('body').css({marginRight:'0px'});
		if(site_theme['design_style']['val'] == 'Top-Menu'){
			$('.header').css({paddingRight:'0px'});
		}		
	}	
}



function fullHeightWall(val=0){
	w = window,
    d = document,
    e = d.documentElement,
    g = d.getElementsByTagName('body')[0],
    x = w.innerWidth || e.clientWidth || g.clientWidth,
    h = w.innerHeight|| e.clientHeight|| g.clientHeight; 	
	var setH = h+val;
	if(url == 'credits' || url == 'popularity'){
		setH = $('.payment-promo').height();
	}
	var setHBody = setH + 150;

	if(url == 'live'){
		var streamHeight = setH;
		$('.stream').css('height',streamHeight+'px');
	}
	if(setHBody > 450){
		$('.wall').css('height',setH+'px');
		$('.real').css('height',setH+'px');
		$('body').css('height',setHBody+'px');
		clearInterval(fixHeightInterval);
	} else {
		fixHeightInterval = setInterval(fullHeightWall(),100);
	}
}



function fullWidthClass(c,val=0){
	var width = $('.wall').width();
	$('.'+c).css('width',width+'px');
}
function fixSiteContentWidth(){
	if(site_theme['design_style_wide']['val'] == 'Yes'){
		var mainW = $('#wide-main').width();
		var colW = $('.s2').width();
		colW = colW * 2;
		var updateW = mainW - colW - 1;
		$('#site-content').css('width',updateW+'px');
		$('#site-content').css('height',$('.s2').height()+'px');
		$('#wide-main').css('height',$('.s2').height()+'px');
		$('#data-content').css('height',$('.s2').height()+'px');
		$('#data-content').css('width',mainW+'px');
	}
}

function fullHeightChat(val=0){
	w = window,
    d = document,
    e = d.documentElement,
    g = d.getElementsByTagName('body')[0],
    x = w.innerWidth || e.clientWidth || g.clientWidth,
    h = w.innerHeight|| e.clientHeight|| g.clientHeight; 		
	var chatHeight = h-255;
	var chatRightHeight = h-170;
	chatHeight = chatHeight+val;
	chatRightHeight = chatRightHeight+val;	
	$('#chat-container').css('height',chatHeight+'px');
	$('.chat-right').css('height',chatRightHeight+'px');

	setTimeout(function(){
		var viewer = new slimLightbox(); //ligthbox
	},500);
	
	
}



function ajaxLoad(s){
	if(s == 1){
		NProgress.start();
	}else {
		NProgress.done();
	}
}
  
function changeEmail(){
	swal({
		title: site_lang[640]['text'],
		text: site_lang[641]['text']+":",
		type: "input",
		showCancelButton: true,
		closeOnConfirm: false,
		animation: "slide-from-top",
		inputPlaceholder: "",
		showLoaderOnConfirm: true
	},
	function(inputValue){
	if (inputValue === false) return false;

	if (inputValue === "") {
		swal.showInputError(site_lang[642]['text']);
		return false
	}
	$.ajax({
		url: request_source()+'/belloo.php', 
		data: {
		    action:"updateVerifyEmail",
		    email: inputValue
		},  
		type: "post",
		dataType: 'JSON',
		success: function(response) {
			if(response['error'] == 1){
				swal("Error", response['reason'], "error");
			} else {
				$('[data-current-email-verify]').text(inputValue);
				swal(site_lang[643]['text'], "", "success");				
			}
		}
	});	  

	});	
}

function skipUploadPhoto(){
  	$('[data-force-profile-photo="10"]').hide();
  	goToProfile(user_info.id);	
	$.ajax({
		url: request_source()+'/belloo.php', 
		data: {
		    action:"skipUploadPhoto"
		},  
		type: "post",
		dataType: 'JSON',
		success: function(response) {
		}
	});		
}



window.onresize = function() {
	fullHeightWall();
	if(site_theme['design_style']['val'] == 'Left-Menu'){
		fullHeightChat(60);
	} else {
		fullHeightChat();
	}
    
    //console.log('resize');
    /*   
    if(x <= 1050){
        d.getElementById('goToMobile').style.display = "block";
        window.location= '<?= siteConfig('mobile_site');?>';    
    } */
}



var widgetTimeoutInterval = 0;
var currentWidget;

/*
setTimeout(function(){
	$('[data-widget]').each(function(){
		if($(this).hasClass('lw-open')) {
			var timeout = $(this).attr('data-widget-timeout');
			currentWidget = $(this).attr('data-widget');
			console.log(currentWidget);
			setTimeout(function(){
				$('[data-widget-close="'+currentWidget+'"]').click();	
			},timeout);
		}
	})
},500)
*/


function openWidget(widget){
	$('.User-Dropdown').removeClass("U-open");
	var wt = document.querySelector('.lw-widget[data-widget="'+widget+'"]');
	showWidget(wt);
	if(widget == 'userCreateStoryAlbum'){
		$('[data-select-stories="0"]').fadeIn();
		$('[data-select-stories="1"]').hide();	
		$('[data-album-stories-title]').html(site_lang[669]['text']);			
	}
}


setTimeout(function(){
	//openWidget("purchaseCredits");
	setTimeout(function(){	

	},1000);
},500);



function showWidget(widget) {
	var overlay = widget.querySelector('.lw-overlay'),
	    items = widget.querySelectorAll('.lw-item'),
	    powered = widget.querySelector('.lw-powered');

	widget.classList.add('lw-open');
	setTimeout(function () {
	    widget.classList.add('lw-animate');
	    if (powered != undefined) {
	        powered.classList.add('lw-animate');
	        setTimeout(function () {
	            powered.classList.remove('lw-animate');
	            powered.classList.add('lw-visible');
	        }, 400);
	    }
	}, 10);

	items.forEach(function (item) {
	    item.classList.add('lw-animate');
	    setTimeout(function () {
	        item.classList.remove('lw-animate');
	        item.classList.add('lw-visible');
	        if (items.length > 1) {
	            if (!item.dataset.height) {
	                item.dataset.height = window.getComputedStyle(item).height;
	            }
	            if (!item.dataset.marginTop) {
	                item.dataset.marginTop = window.getComputedStyle(item).marginTop;
	            }
	        }
	    }, 400);
	});
}

function loadWidgetItem(widget) {
	var widget = document.querySelector('.lw-widget[data-widget="'+widget+'"]');
	var items = widget.querySelectorAll('.lw-item');

	items.forEach(function (item) {
	    item.classList.add('lw-animate');
	    setTimeout(function () {
	        item.classList.remove('lw-animate');
	        item.classList.add('lw-visible');
	        if (items.length > 1) {
	            if (!item.dataset.height) {
	                item.dataset.height = window.getComputedStyle(item).height;
	            }
	            if (!item.dataset.marginTop) {
	                item.dataset.marginTop = window.getComputedStyle(item).marginTop;
	            }
	        }
	    }, 300);
	});
	$('.lw-item').each(function(){
		if($(this).hasClass('lw-visible')) {
			var currentNotification = $(this).attr('data-widget-remove');

			$('[data-widget-close="'+currentNotification+'"]').on( "click", "tr", function() {
				setTimeout(function(){
					$('[data-widget-remove="'+currentNotification+'"]').remove();	
				},400);				
			});				
			setTimeout(function(){
				$('[data-widget-close="'+currentNotification+'"]').click();	
			},9600);
			setTimeout(function(){
				$('[data-widget-remove="'+currentNotification+'"]').remove();	
			},9900);			
		}
	})
}

function closeWidgetItem(widget) {
	$('[data-widget-remove="'+widget+'"]').remove();	
}



$('#premiumModalSlick').slick({
	  centerMode: false,

	  slidesToShow: 1,
	  slidesToScroll: 1,
	  dots: false,
	  arrows:false,		
	  autoplay: true,
	  autoplaySpeed: 1000,
});


$('#premiumModalSlick').on('swipe', function(event, slick, direction){
	var currentSlide = $('#premiumModalSlick').slick('slickCurrentSlide');
    var slides = document.querySelectorAll('.lw-slide'),
        dots = document.querySelectorAll('.lw-dot');

    dots.forEach(function(dot, i) {
	    dots.forEach(function(dot, i) {
	        dot.classList.remove('lw-active');
	    });
    });
    dots[currentSlide].classList.add('lw-active');
});



function removeDuplicates(originalArray, prop) {
	var newArray = [];
	var lookupObject  = {};

	for(var i in originalArray) {
	lookupObject[originalArray[i][prop]] = originalArray[i];
	}

	for(i in lookupObject) {
	 newArray.push(lookupObject[i]);
	}
	return newArray;
}


function openFilterSearch(){
	document.getElementById("openSearchFilter").click();
}


function interaction(id,name,photo,message,action){
	var data = [];
	
	if(action == 'visit'){
		data.color = site_theme['notification_inapp_visit_color']['val'];
		data.background = site_theme['notification_inapp_visit_bg']['val'];
		data.btnBg = site_theme['notification_inapp_visit_btn_bg']['val'];
		data.btnColor = site_theme['notification_inapp_visit_btn_color']['val'];
	}
	if(action == 'like'){
		data.color = site_theme['notification_inapp_like_color']['val'];
		data.background = site_theme['notification_inapp_like_bg']['val'];
		data.btnBg = site_theme['notification_inapp_like_btn_bg']['val'];
		data.btnColor = site_theme['notification_inapp_like_btn_color']['val'];
	}
	if(action == 'message'){
		data.color = site_theme['notification_inapp_message_color']['val'];
		data.background = site_theme['notification_inapp_message_bg']['val'];
		data.btnBg = site_theme['notification_inapp_message_btn_bg']['val'];
		data.btnColor = site_theme['notification_inapp_message_btn_color']['val'];
	}		

	data.id = id;
	data.action = action;
	data.name = name;
	data.icon = photo;
	data.message = message;
	pushNotif(data,2);		
}



function getGiphy(type,q=''){
	var gifUrl;
	$('.gifImages').css('display','grid');
	if(type == 'trending' || q == ''){
		gifUrl = 'https://api.giphy.com/v1/gifs/trending?limit=24&api_key=2n2rOkvKWUwIVyydayTpL52AK4iD9qeo';
	} else {
		gifUrl = 'https://api.giphy.com/v1/gifs/search?q='+q+'&limit=24&api_key=2n2rOkvKWUwIVyydayTpL52AK4iD9qeo';
	}
	$.get( gifUrl, function( result ) {
	    var data = result.data;
	    var output = "";
	    var delay = 0;
	    for (var index in data){
	      var gifObject = data[index];
	      var gifURL = gifObject.images.original.url;
	      var gifURLPreview = gifObject.images.preview_gif.url;
	      var sendGif = '"'+gifURL+'"';
	      delay = index*50;
	      output += `
			<figure class="snip1205 vivify popInBottom delay-`+delay+`">
				<img src="`+gifURLPreview+`"/>
				<i class="ion-android-send" onclick='sendGif(`+sendGif+`)'></i>
			</figure>
	      `;

	    }
	    $('.gifImages').html(output);
	});
}

function sendGif(gif){

	if(plugins['chat']['creditsPerMessageEnabled'] == 'Yes'){

        if(user_info.credits < plugins['chat']['creditsPerMessage']){ 
            openWidget("purchaseCredits");
            return false;
        }	
        
        var data = [];
        data.name = '';
        data.icon = site_theme['notification_inapp_credits_icon']['val'];
        data.message = site_lang[610].text+' '+plugins['chat']['creditsPerMessage']+' ' + site_lang[73].text;
        
		if(plugins['chat']['creditsPerMessageGender'] == user_info.gender){
			updateCredits(user_info.id,plugins['chat']['creditsPerMessage'],1,'Credits for send chat message');
			pushNotif(data,1);
		}
		if(plugins['chat']['creditsPerMessageGender'] == allG){
			updateCredits(user_info.id,plugins['chat']['creditsPerMessage'],1,'Credits for send chat message');
			pushNotif(data,1);
		}				
	}	
	$('[data-gif-btn]').removeClass('active');
	$('[data-show-send-gif="1"]').hide();	
	$('.gifImages').html('');

	var me = Math.floor(Math.random() * 10000000);  
	$('.chat').append('<div class="js-message-block" data-me="'+me+'" style="opcaity:0.5" id="me"><div class="message"><div class="brick brick--xsm brick--hover"><div class="brick-img profile-photo" data-src="'+ user_info.profile_photo +'"></div></div><div class="message__txt"><span class="lgrey message__time" style="margin-right: 15px;"></span><div class="message__name lgrey">'+ user_info.first_name +'</div><p class="montserrat chat-text"><div class="message__pic_" style="cursor:pointer;border:none"><img  src="'+gif+'" /></div></p></div></div></div>	')					
	$('.chat').mCustomScrollbar("destroy");
	profilePhoto();
	scroller();	

	var r_id = $("#r_id").val();
	var messageVal = gif;
	var message = user_info.id+'[message]'+r_id+'[message]'+messageVal+'[message]gif';  
	var send = user_info.id+'[rt]'+r_id+'[rt]'+user_info.profile_photo+'[rt]'+user_info.first_name+'[rt]'+messageVal+'[rt]gif';      

	$.get( gUrl, {action: 'message', query: send} );		   	
	$.get( aUrl, {action: 'sendMessage', query: message} );	
}

function openGif(val=0){
	
	if(val == 0){
		$('[data-gif-btn]').removeClass('active');
		$('[data-show-send-gif="1"]').hide();
		$('.gifImages').html('');
	} else {	
		if( $("[data-gif-btn]").hasClass("active") ){
			$('[data-gif-btn]').removeClass('active');
			$('[data-show-send-gif="1"]').hide();
			$('.gifImages').html('');
		} else {
			showEmoji(0);
			$('[data-gif-btn]').addClass('active');
			$('[data-show-send-gif="1"]').show();
			var val = $('[data-gif-input]').val();
			$('[data-gif-input]').focus();
			if(val == ''){
				getGiphy('trending');
			} else {
				getGiphy('search',val);
			}
			
		}	
	}
}

function selectEmoji(emoji){
	var currentMessage = $('#chat-message').text();
	$('#chat-message').text(currentMessage+' '+emoji);
	$('#chat-message').focus();
	$('#chat-message').focusTextToEnd();
}

function showEmoji(val=0){
	if(val == 0){
		$('.showEmoji').hide();
	} else {
		if($('.showEmoji').is(':visible')) {
			$('.showEmoji').hide();
		} else {
			openGif(0);
			$('.showEmoji').show();
		}	
	}
	
}

setTimeout(function(){
	$('.ms-emoji').msEmoji({
		input: ".ms-emoji-input",
		lang: ""
	});	
},500);


function fadeOutStories(){
	var fadeOutLoader = totalDiscoverStories * 50;
	$('[data-story-loading]').show();
	setTimeout(function(){
		discoverStoriesPreview();
		for (var i = 1; i <= 8; i++) {
				$('[data-story-loading='+i+']').addClass('fadeOut delay-'+fadeOutLoader);
				fadeOutLoader = fadeOutLoader + 50;
			}	
	},2700);

	setTimeout(function(){
		for (var i = 1; i <= 8; i++) {
				$('[data-story-loading='+i+']').remove();
				fadeOutLoader = fadeOutLoader + 50;
			}
	},3700);

	scroller();
}


function createStoryAlbum(action=0){
	var album = $('#storyAlbumName').val();

	if(upphotos.length == 0){
		swal({   title: site_lang[671]['text'],   text: site_lang[673]['text'], type: "warning" }, function(){ });
		return false;	
	}
	if(album == ''){
		swal({   title: site_lang[671]['text'],   text: site_lang[672]['text'], type: "warning" }, function(){ });
		return false;
	}


	$('[data-story-album-div]').hide();
	$('[data-select-stories="0"]').hide();
	$('[data-select-stories="1"]').fadeIn();

	if(action == 0){
		$('[data-album-stories-btn=2]').hide();
		$('[data-album-stories-btn=1]').show();		
		$('[data-storyId]').attr('data-select',1);
	    $('[data-storyId]').html(`
	    	<svg xmlns="http://www.w3.org/2000/svg" width="29" height="29" viewBox="0 0 24 24" fill="none" stroke="#1b1b1b" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
	    `);		
		$("[data-story-selected]").removeClass("expand");
		addStoriesToAlbum = [];			
	}

	var randomId = Math.floor(Math.random()*(45000-5000+1)+5000);	

	if(action == 1){

		$('[data-select-stories="0"]').hide();
		$('[data-select-stories="1"]').fadeIn();
		if(addStoriesToAlbum.length == 0){
			swal({   title: site_lang[671]['text'],   text: site_lang[675]['text'], type: "warning" }, function(){ });
			return false;
		}
		$('#addNewAlbum').after(`
			<a class="story-album vivify popIn" id="randomId`+randomId+`" style="background-image: url('`+upphotos[0]['path']+`');" href="#">
			    <div class="story-album__overlay"></div>
			    <div class="story-album__info">
			      <div class="story-album__title">`+album+`</div>
			    </div>
			</a>
		`);
		$.ajax({
		  type: "POST",
		  url: request_source()+'/belloo.php',
		  data: {
		    action: 'uploadMedia',
		    media: upphotos,
		    album: album,
		    stories: addStoriesToAlbum
		  },
		  dataType: 'JSON',
		  success: function(response) {
		  	console.log(response['stories']);
		  	$('#randomId'+randomId).click(function(){
		  		openStoryAlbum(JSON.parse(response['stories']));
		  	});
		  	reloadStoryAlbumsList();
		  }
		});		
	}	

}

function removeFromArray(arr) {
    var what, a = arguments, L = a.length, ax;
    while (L > 1 && arr.length) {
        what = a[--L];
        while ((ax= arr.indexOf(what)) !== -1) {
            arr.splice(ax, 1);
        }
    }
    return arr;
}

function selectStoryToAlbum(div){
    var val = $(div).attr('data-select');
    var sid = $(div).attr('data-storyId');
    if(val == 1){
        $(div).html(`
            <svg xmlns="http://www.w3.org/2000/svg" width="29" height="29" viewBox="0 0 24 24" fill="none" stroke="#1b1b1b" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
        `);
        $(div).attr('data-select',0);
        $(div).css('background','#000');
        $(div).find('svg').css('stroke','#fff');
        addStoriesToAlbum.push(sid);
    } else {
        $(div).html(`
            <svg xmlns="http://www.w3.org/2000/svg" width="29" height="29" viewBox="0 0 24 24" fill="none" stroke="#1b1b1b" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
        `);
        $(div).attr('data-select',1);
        $(div).css('background','#fff');
        $(div).find('svg').css('stroke','#000');        
        removeFromArray(addStoriesToAlbum, sid);
    }
    $("[data-story-selected="+sid+"]").toggleClass("expand");
    console.log(addStoriesToAlbum);
}

function editAlbumStories(album,stories,name,photo){
	openWidget('userCreateStoryAlbum');
	$('[data-storyId]').attr('data-select',1);
    $('[data-storyId]').html(`
    	<svg xmlns="http://www.w3.org/2000/svg" width="29" height="29" viewBox="0 0 24 24" fill="none" stroke="#1b1b1b" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
    `);		
	$("[data-story-selected]").removeClass("expand");
	addStoriesToAlbum = [];	
	$('[data-story-album-div]').hide();
	$('[data-story-album-edit]').attr('data-src',photo);
	profilePhoto();
	$('[data-album-stories-title]').html(site_lang[681]['text'] +' '+ name);
	$('[data-album-stories-btn=1]').hide();
	$('[data-album-stories-btn=2]').show();
	$('[data-album-stories-btn=2]').attr('data-album',album);
	$('[data-album-stories-btn=2]').attr('data-album-photo',photo);
	$('[data-album-stories-btn=2]').attr('data-album-name',name);
	$('[data-storyId]').css('background','#fff');
	var data = stories.split(",");
	data.forEach(function(val) {
		$('[data-storyId='+val+']').attr('data-select',0);
        $('[data-storyId='+val+']').css('background','#000');		
        $('[data-storyId='+val+']').html(`
            <svg xmlns="http://www.w3.org/2000/svg" width="29" height="29" viewBox="0 0 24 24" fill="none" stroke="#1b1b1b" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
        `);		
        $('[data-storyId='+val+']').find('svg').css('stroke','#fff');
		$("[data-story-selected="+val+"]").addClass("expand");
		 addStoriesToAlbum.push(val);
	}); 

	$('[data-select-stories="0"]').hide();
	$('[data-select-stories="1"]').fadeIn();	   
}

function deleteStoryAlbum(div){
	var album = $(div).attr('data-album');
	var photo = $(div).attr('data-album-photo');
	swal({   title: site_lang[682]['text'],   text: site_lang[683]['text'],   imageUrl: photo,   showCancelButton: true,   confirmButtonColor: "#09c66e",   confirmButtonText: site_lang[259]['text'], cancelButtonText: site_lang[195]['text'],   closeOnConfirm: true }, function(){
		reloadStoryAlbumsList('delete',album);
		$.get( aUrl, { action: 'deleteStoryAlbum', query: album } );

	});
}

function updateStoryAlbum(div){
	var album = $(div).attr('data-album');
	var name = $(div).attr('data-album-name');	
	var photo = $(div).attr('data-album-photo');	
	var query = album+';'+addStoriesToAlbum+';'+name+';'+photo;
	if(addStoriesToAlbum.length == 0){
		swal({   title: site_lang[671]['text'],   text: site_lang[675]['text'], type: "warning" }, function(){ });
		return false;
	}
	$('[data-widget-close="userCreateStoryAlbum"]').click();
	$.get( aUrl, { action: 'updateStoryAlbum', query: query } , function( data ) {
		$('body').append(data);
		console.log(data);
	});
	$("[data-story-edit-album-btn="+album+"]").attr("onclick","editAlbumStories("+album+",'"+addStoriesToAlbum+"','"+name+"')");	
}


function reloadStoryAlbumsList(action='',val=0){	
	if(action == 'delete'){
		$('[data-story-album='+val+']').remove();
	}	
	$('[data-widget-close="userCreateStoryAlbum"]').click();
	$('#highlightedStories').css('display','block');
	$flkty.destroy();
	$flkty = new Flickity('.stories-wrap', {
		contain: true,
		prevNextButtons: false,
		freeScroll: true,
		pageDots: false,
		cellAlign: 'left',
		adaptiveHeight: false,
		setGallerySize: false,
		selectedAttraction: 0.05,
		freeScrollFriction: .1
	});	

	setTimeout(function(){
		$('#highlightedStories').css('opacity',1);
		$('[data-select-stories="1"]').hide();
		$('[data-select-stories="0"]').fadeIn();
		$("[data-story-selected]").removeClass("expand");	
    	
        $('.circleclose').html(`
            <svg xmlns="http://www.w3.org/2000/svg" width="29" height="29" viewBox="0 0 24 24" fill="none" stroke="#1b1b1b" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
        `);	
        $('.circleclose').attr('data-select',1);
        addStoriesToAlbum = [];	
        $('#storyAlbumName').val('');
        $('[data-story-album]').attr('data-src','');
        profilePhoto();			
	},50);	
}

$(".fullscreen-bg__video").hover(function () {
    $(this)[0].play();
}, function () {
    var el = $(this)[0];
    el.pause();
    el.currentTime = 0;
});



function proceedCheckout(){
	var winH = h-70;
	window.location.href = site_config.site_url+'pay/index.php?package='+creditsPackage+'&type=credits';	
}

function updateCreditSelection(el){
	$('.payment-package').removeClass('is-selected');
	$('.payment-package').addClass('not-selected');
	$(el).removeClass('not-selected');
	$(el).addClass('is-selected');
	$('#selectCreditPaymentMethod').fadeIn();
	var price = $(el).attr('data-price');
	var credits = $(el).attr('data-credits');
	creditsPackage = $(el).attr('data-package');
	$('[data-payment-credits]').attr('data-payment-credits',credits);
	$('[data-payment-price]').attr('data-payment-price',price);	

	$('[data-payment-name]').val(site_config.name + ' - '+ credits +' '+ site_lang[73]['text']);
	$('[data-payment-amount]').val(price);	
	$('[data-payment-custom]').val(user_info.id+','+credits);

	$('#creditsPackageSelected').text(credits +' '+ site_lang[73]['text'] +' - '+ price +' '+ plugins['settings']['currency']);
	$('#payment_module').animate({
		  scrollTop: 150
	}, 150);
}	


function purchaseCredits(el){
	var price = $(el).attr('data-payment-price');
	var quantity = $(el).attr('data-payment-credits');	
	var type = $(el).attr('data-payment-type');

	switch (type) {
		case "paypal":
			$('#paypalForm').submit();
		break;
		case "paygol":
			$('#paygolForm').submit();
		break;		
		case "fortumo":
			var name = site_config.name + ' ' + quantity + ' '+site_lang[73]['text'];
			var encode = 'amount='+quantity+'callback_url='+site_config.site_url+'credits-okcredit_name='+name+'cuid='+user_info.id+'currency='+site_config.currency+'display_type=userprice='+price+'v=web';			
			$.ajax({ 
				type: "GET", 
				url: request_source() + "/api.php",
				data: {
					action: 'fortumo',
					encode: encode
				},
				dataType:'JSON',
				success: function(response){
					var md5 = response['encode'];
					var callback = encodeURI(site_config.site_url+'credits-ok');
					name = encodeURI(name);
					var href= 'http://pay.fortumo.com/mobile_payments/'+plugins['fortumo']['id']+'?amount='+quantity+'&callback_url='+callback+'&credit_name='+name+'&cuid='+user_info.id+'&currency='+site_config.currency+'&display_type=user&price='+price+'&v=web&sig='+md5;
					window.open(href,'popUpWindow','height=650,width=600,left=450,top=100,resizable=yes,scrollbars=yes,toolbar=yes,menubar=no,location=no,directories=no, status=yes');									
				}
			});		
		break;		
	}
}

function purchasePremium(el){
	var package = $(el).attr('data-premium-package');
	var winH = h-70;
	window.location.href = site_config.site_url+'pay/index.php?package='+package+'&type=premium';	
	/*
	var days = $(el).attr('data-premium-days');
	var name = site_config.name + ' ' + days + ' '+site_lang[332]['text'];
	$('#premiumName').val(name);
	$('#premiumPrice').val(price);
	$('#premiumDays').val(days);
	$('#premiumCustom').val(user_info.id+','+days);
	$('#paypalSubscribe').submit(); */
}

function proceedCheckout(){
	var winH = h-70;
	window.location.href = site_config.site_url+'pay/index.php?package='+creditsPackage+'&type=credits';	
}
function meetPremiumNotification(val=0){
	if(val == 1){
		var dataNotif = [];
		dataNotif.name = site_lang['449']['text'];
		dataNotif.icon = user_info.profile_photo;
		dataNotif.message = site_lang['696']['text'];
		pushNotif(dataNotif,1);						
	}
	if(val == 2){
		var dataNotif = [];
		dataNotif.name = site_lang['449']['text'];
		dataNotif.icon = user_info.profile_photo;
		dataNotif.message = site_lang['695']['text'];
		pushNotif(dataNotif,1);						
	}			
}


function selectInterest(div){
    var val = $(div).attr('data-select');
    var id = $(div).attr('data-id');
    var query = user_info.id+','+id;
    if(val == 1){
        $(div).attr('data-select',0);
        $(div).attr('data-checked',0);
        $("[data-interest-checked="+id+"]").hide();
        $.get( aUrl, { action: 'del_interest', query: query } ); 
    } else {
        $(div).attr('data-checked',1);
        $(div).attr('data-select',1);
        $("[data-interest-checked="+id+"]").show();
		$.get( aUrl, { action: 'add_interest', query: query } );        
    }
    $("[data-interest-selected="+id+"]").toggleClass("expand");
}


function showInterests(){

	if($('#myInterests').is(':visible')) {
		$('#allInterests').show();
		$('#myInterests').hide();
		$('#addInterestBtn').hide();
		$('[data-svg="profileInterestPencile"]').hide();
		$('[data-svg="profileInterestCheck"]').show();

	} else {
		var interests = '';

		$('[data-select-interest-id]').each(function(index,el){
			var ch = $(el).attr('data-checked');
			var name = $(el).attr('data-name');
			var icon = $(el).attr('data-icon');
			if(ch == 1){
				interests=interests+`
					<div class="container-grid-interest interestsBorderRadius box-shadow" style="background-image:url('`+icon+`')">                            
						<div class="interestName">
							<span class="text-shadow">`+name+`</span>
						</div>
					</div>
				`;
			}
		});
		$('[data-svg="profileInterestPencile"]').show();
		$('[data-svg="profileInterestCheck"]').hide();
		$('#addInterestBtn').show();
		$('#myInterests').html(interests);		
		$('#allInterests').hide();
		$('#myInterests').show();
	}
}


function showProfileQuestions(el){
	var edit = $(el).attr('data-profileEdit');
	if(edit == 0) {
		$('[data-answer="edit"').fadeIn();
		$('[data-answer="visible"').hide();
		$('[data-svg="profileQuestionPencile"]').hide();
		$('[data-svg="profileQuestionCheck"]').show();
		$(el).attr('data-profileEdit',1);	
	} else {
		$('[data-answer="visible"').fadeIn();
		$('[data-answer="edit"').hide();
		$('[data-svg="profileQuestionPencile"]').show();
		$('[data-svg="profileQuestionCheck"]').hide();	
		$(el).attr('data-profileEdit',0);	
	}
}

channel.bind(themeUpdate,function(data){
	if(user_info.admin == 1){
		if(data['reload'] == 1){
			window.location.reload();
		} else {
			$.ajax({
				url: request_source()+'/belloo.php', 
				data: {
					action:"themeStylesReload",
					preset:data['preset'],
					theme:data['theme']						
				},	
				type: "post",			
				success: function(response) {
					$('#themeData').html(response);							
				},
			});
		}
	}	
});


function showProfileBio(el){
	var edit = $(el).attr('data-profileBio');
	if(edit == 0) {
		$('[data-user-bio="edit"').fadeIn();
		$('[data-user-bio="visible"').hide();
		$('[data-svg="profileBioPencile"]').hide();
		$('[data-svg="profileBioCheck"]').show();
		$(el).attr('data-profileBio',1);	
	} else {
		$('[data-user-bio="visible"').fadeIn();
		$('[data-user-bio="edit"').hide();
		$('[data-svg="profileBioPencile"]').show();
		$('[data-svg="profileBioCheck"]').hide();	
		$(el).attr('data-profileBio',0);
		//var bioMessage = nl2br($('#userBio').val());
		//$('#userBio').val(bioMessage);
		var bio = $('#userBio').html();
		var bioUrl = $('#userBioUrl').val();
		var bioUrlMessage = $('#userBioUrlLink').val();
		var bioUrlQuery = bioUrl+'**message**'+bioUrlMessage;
		$('[data-user-bio="visible"]').html(nl2br(bio));
		var query = user_info.id+'[divider]'+bio+'[divider]'+bioUrlQuery;
		$.get( aUrl, { action: 'updateUserBio', query: query },
			function(result) {
				var data = JSON.parse(result);
				var url = data['url'];
				var bioLink = bio;
				if(url != 'No'){
					var showUrl = `<br><br><a href="`+data['url']+`" target="_blank">
						`+data['urlMessage']+`
					</a>`;	
					bioLink = bioLink+showUrl;			
					$('[data-user-bio="visible"]').html(nl2br(bioLink));	
				}
				
		});				
	}
}

function updateProfileQuestionAnswer(el){
	var q = $(el).attr('data-profile-question-answer');
	var method = $(el).attr('data-question-method');
	if(method == 'text'){
		var answer = $(el).val();
	}
	if(method == 'select'){
		var answer = $( "[data-profile-question-answer="+q+"] option:selected").text();
	}	

	if(answer == site_lang[712]['text']){
		return false;
	}
	$("[data-question-answer="+q+"]").text(answer);
	$("[data-question-answer="+q+"]").css('color',site_theme['secundary_color_profile_left']['val'])
	var query = user_info.id+','+q+','+answer;
	$.get( aUrl, { action: 'updateUserExtended', query: query } );	
}


var delayTimer = (function(){
  var timer = 0;
  return function(callback, ms){
    clearTimeout (timer);
    timer = setTimeout(callback, ms);
  };
})();


function nl2br (str, is_xhtml) {
    if (typeof str === 'undefined' || str === null) {
        return '';
    }
    var breakTag = (is_xhtml || typeof is_xhtml === 'undefined') ? '<br />' : '<br>';
    return (str + '').replace(/([^>\r\n]?)(\r\n|\n\r|\r|\n)/g, '$1' + breakTag + '$2');
}

setTimeout(function(){
	profilePhoto();	
},150)

function popularUsers(zone){
	console.log(zone);
}

(function($){$.fn.percentPie=function(options){var settings=$.extend({width:100,trackColor:"EEEEEE",barColor1:"007AFF",barColor2:"F50C41",barWeight:30,startPercent:0,fps:60},options);var percentage=$(this).data('percent')/100;this.css({width:settings.width,height:settings.width});var that=this,hoverPolice=!1,canvasWidth=settings.width,canvasHeight=canvasWidth,id=$('canvas').length,canvasElement=$('<canvas id="'+id+'" width="'+canvasWidth+'" height="'+canvasHeight+'"></canvas>'),canvas=canvasElement.get(0).getContext("2d"),centerX=canvasWidth/2,centerY=canvasHeight/2,radius=settings.width/2-settings.barWeight/2;counterClockwise=!1,fps=1000/settings.fps/2,update=.01;this.angle=settings.startPercent;this.drawArc=function(startAngle,percentFilled,color1,color2){var drawingArc=!0;canvas.beginPath();canvas.arc(centerX,centerY,radius,(Math.PI/180)*(startAngle*360-90),(Math.PI/180)*(percentFilled*360-90),counterClockwise);var grd=canvas.createLinearGradient(0,0,settings.width,0);grd.addColorStop(0,color1);grd.addColorStop(1,color2);canvas.strokeStyle=grd;canvas.lineWidth=settings.barWeight;canvas.stroke();drawingArc=!1}
this.fillChart=function(stop){$({numberValue:0}).animate({numberValue:percentage*100},{duration:fps*100,easing:'linear',step:function(){that.find('.tag').text(Math.ceil(this.numberValue)+'%')}});var loop=setInterval(function(){hoverPolice=!0;canvas.clearRect(0,0,canvasWidth,canvasHeight);that.drawArc(0,360,settings.trackColor,settings.trackColor);that.angle+=update;that.drawArc(settings.startPercent,that.angle,settings.barColor1,settings.barColor2);if(that.angle>stop){clearInterval(loop);hoverPolice=!1}},fps)}
this.fillChart(percentage);this.append(canvasElement);return this}}(jQuery));

function viewVideo(video){
  var win = window.open(video, '_blank');
  win.focus();	
}
(function() {
  'use strict';

  function ctrls() {
    var _this = this;

    this.counter = 10;
    this.els = {
      decrement: document.querySelector('.ctrl__button--decrement'),
      counter: {
        container: document.querySelector('.ctrl__counter'),
        num: document.querySelector('.ctrl__counter-num'),
        input: document.querySelector('.ctrl__counter-input')
      },
      increment: document.querySelector('.ctrl__button--increment')
    };

    this.decrement = function() {
      var counter = _this.getCounter();
      var nextCounter = (_this.counter-9 > 10) ? counter-10 : counter;
      _this.setCounter(nextCounter);
    };

    this.increment = function() {
      var counter = _this.getCounter();
      var nextCounter = (counter < 9999999999) ? counter+10 : counter;
      _this.setCounter(nextCounter);
    };

    this.getCounter = function() {
      return _this.counter;
    };

    this.setCounter = function(nextCounter) {
      _this.counter = nextCounter;
    };

    this.debounce = function(callback) {
      setTimeout(callback, 100);
    };

    this.render = function(hideClassName, visibleClassName) {
      _this.els.counter.num.classList.add(hideClassName);

      setTimeout(function() {
        _this.els.counter.num.innerText = _this.getCounter();
        _this.els.counter.input.value = _this.getCounter();
        _this.els.counter.num.classList.add(visibleClassName);
      }, 100);

      setTimeout(function() {
        _this.els.counter.num.classList.remove(hideClassName);
        _this.els.counter.num.classList.remove(visibleClassName);
      }, 1100);
    };

    this.ready = function() {
      _this.els.decrement.addEventListener('click', function() {
        _this.debounce(function() {
          _this.decrement();
          _this.render('is-decrement-hide', 'is-decrement-visible');
        });
      });

      _this.els.increment.addEventListener('click', function() {
        _this.debounce(function() {
          _this.increment();
          _this.render('is-increment-hide', 'is-increment-visible');
        });
      });

      _this.els.counter.input.addEventListener('input', function(e) {
        var parseValue = parseInt(e.target.value);
        if (!isNaN(parseValue) && parseValue >= 0) {
          _this.setCounter(parseValue);
          _this.render();
        }
      });

      _this.els.counter.input.addEventListener('focus', function(e) {
        _this.els.counter.container.classList.add('is-input');
      });

      _this.els.counter.input.addEventListener('blur', function(e) {
        _this.els.counter.container.classList.remove('is-input');
        _this.render();
      });
    };
  };

  // init
  var controls = new ctrls();
  document.addEventListener('DOMContentLoaded', controls.ready);
})();

window.onpopstate = function() {
	setTimeout(function(){
		var currentUrl = window.location.href;
		var lastSlash = currentUrl.match(/\/([^\/]+)\/?$/)[1];
		window.location.href = site_url()+lastSlash;
	},10);
}; window.history.pushState({}, '');