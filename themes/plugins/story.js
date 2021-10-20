var holdActive = false;
var holdDelay = 200;
var uploadingStoryFakeId;
var newStoryUpload = false;
var storyUploaded = false;
var isStoryBack = false;
var viewingStory = 0;
var preloadData = new Array();
var storyId;
var noMoreStories = false;
var chatStoryUrl;
var chatStoryType;
var storyContainerOpen = false;
var storyAdmin = false;
var videoPreloadCount = 0;

if(mobileSite === true){
    console.log(user);
    console.log('test');
    var user_info = user;
    console.log(user_info);
}

if (typeof user_info === 'undefined') {
    var user_info = [];
    user_info.id = 0;
}


(function() {

    var defaults;
    var video;
    var img;
    var thisTimeline;
    var start = 0;
    var storyTime;
    var storySpinner;
    var imageInterval; 
    var isVideo = false;
    var isClosed = false;

    
    var storyCredits;

    var storyUser;
    var storyIcon;
    var storyName;
    var storyReview;
    var storyPaused;
    var myStory = 0;

    this.Story = function(data) {

    	// Default parameters if non provided.
        defaults = {
            playlist: data
        };

        if (arguments[0] && typeof arguments[0] === "object") {
            this.options = extendDefaults(defaults, arguments[0]);
        }

        try {
            if (defaults.playlist == null || defaults.playlist == '') {
                console.log('No story provided.');
                storyContainerOpen = false;
                return false;
            }
        } catch (e) {
            console.log(e);
            storyContainerOpen = false;
            return false;
        }

        
        
        var Div = document.getElementById('storytime');
        storyId = defaults.playlist[0].sid;
        storyUser = defaults.playlist[0].uid;
        storyIcon= defaults.playlist[0].icon;
        storyName= defaults.playlist[0].title;


        
        var baseHTML = '<div class="storytime" style="opacity: 0; display: none;">' +
			'<div class="story-cover"></div>' +
			'<div class="story-window" id="storyWindow">' +  
            //'<div style="position:absolute;z-index:9;top: 100px;right: 20px;color: #fff;text-shadow: 2px 2px 15px rgba(0, 0, 0, 0.15);font-size: 12px;" data-video-time="">1:23</div>'+                 
                '<div id="chat-input" data-story-chat-input class="">'+
                    '<input type="text" id="replyStoryMessage">'+
                '</div>'+
              '<div id="chat-input-send" data-story-chat-input><i onclick="sendMessageFromStory('+storyUser+')" class="icon icon--lg icon--white ion-android-send"></i>'+
            '</div>'+             
			'<a href="#" class="story-arrow left" onclick="socialStory.prev();"></a><a href="#" class="story-arrow right" onclick="socialStory.next();"></a>' +
				'<div class="story-nav">' +
					'<div class="story-nav-left">'+
                    '<div class="paused"><span>'+site_lang[614]['text']+'</span></div>'+
                    '<div class="story-icon vivify popIn box-shadow" onclick="socialStory.close(); goToProfile('+storyUser+');"></div> <span class="story-text text-shadow" onclick="socialStory.close(); goToProfile('+storyUser+');" style="cursor:pointer;"></span><span class="story-date text-shadow"></span></div>' +
                    '<div class="story-nav-right">'+
                        '<div class="storySettings"><i class="icon ion-android-more-horizontal"></i></div>'+                    
                        '<div class="storyCredit" data-my-story="'+myStory+'"><img class="top-menu-credits" src="'+site_config.theme_url+'/images/coin.png" style="width: 30px;position: absolute;left: 3px;top: 0px"><span data-story-credits></span></div>'+                      
                        '<a href="javascript:;" class="close story-close" onclick="socialStory.close();"></a>'+
                    '</div>'+
				'</div>' +
				'<div class="story-timeline"></div>' +
                '<div class="story-video">' +

				'</div>' +
				'<div class="spinner">' +
					'<div class="bounce1"></div>' +
					'<div class="bounce2"></div>' +
					'<div class="bounce3"></div>' +
				'</div>' +                  
			'</div>' +      
		'</div>';

        var timelineHTML = '';

        // Add HTML to storytime div element
        Div.innerHTML = baseHTML;


        var icon = document.getElementsByClassName('story-icon')[0];
        icon.setAttribute("style", "width:50px;height:50px;background-image:url("+defaults.playlist[0].icon+")");

        //$('.story-video').mousedown(onMouseDown);
        //$('.story-video').mouseup(onMouseUp);

        $('#replyStoryMessage').focus( function() {
          replyStory(1);
        });
        $('#replyStoryMessage').blur( function() {
          replyStory(2);
        });

        if(plugins['story']['storyCredits'] == 'Yes'){
            $('.storyCredit').click(function(){

                if(myStory == 1){
                    var storyCreditsArray = {};
                    var creditsBtn = new Array();
                    storyCreditsArray = JSON.parse("[" + plugins['story']['storyCreditsValues'] + "]");
                    storyCreditsArray.forEach(function(element) {
                        creditsBtn[element+' '+site_lang[285]["text"]] = start+','+storyId+','+element+',Credits';
                    }); 
                     
                    var storyCredits = new ActionSheet({
                        buttons: creditsBtn
                    });

                    storyCredits.show(); 
                }          
            });            
        } else {
            $('.storyCredit').hide();
        }


        $('.storySettings').click(function(){
             if(plugins['story']['storyCredits'] == 'Yes'){
                var as = new ActionSheet({
                    buttons: {
                        '620': function(e){
                            this.hide();
                            holdActive = false;
                        },
                        '619': function(e){
                            this.hide();
                            deleteStory.show();
                        },                    
                        '618': function(e){
                            storyCredits.show();
                            this.hide();
                        }
                    }
                });
            } else {
                var as = new ActionSheet({
                    buttons: {
                        '620': function(e){
                            this.hide();
                            holdActive = false;
                        },
                        '619': function(e){
                            this.hide();
                            deleteStory.show();
                        }                   
                    }
                });                
            }
            var deleteStory = new ActionSheet({
                buttons: {
                    '616': function(e){
                        deleteCurrentStory(start,storyId);
                        this.hide();
                    },                  
                    '617': function(e){
                        this.hide();
                        as.show(); 
                    },
                }
            });


            var storyCreditsArray = {};
            
            var creditsBtn = new Array();
            storyCreditsArray = JSON.parse("[" + plugins['story']['storyCreditsValues'] + "]");
            storyCreditsArray.forEach(function(element) {
                creditsBtn[element+' '+site_lang[285]["text"]] = start+','+storyId+','+element+',Credits';
            }); 
             
            var storyCredits = new ActionSheet({
                buttons: creditsBtn
            });


            as.show(); 
        });

        // Create timeline elements by looping thorugh story items
        var i;
        for (i = 0; i < defaults.playlist.length; i++) {
            timelineHTML = timelineHTML + '<div class="story-timeline-item"><div class="story-timeline-line"></div><div class="story-timeline-line-active story-active-' + i + '" style="width: 0%;"></div></div>';
        }

        // Add timeline HTML to storytime div element
        var storyTimeline = document.getElementsByClassName('story-timeline')[0];
        storyTimeline.innerHTML = timelineHTML;
    };

    // Utility method to extend defaults with user options
    function extendDefaults(source, properties) {
        var property;
        for (property in properties) {
            if (properties.hasOwnProperty(property)) {
                source[property] = properties[property];
            }
        }
        return source;
    }

    function launch() {
    	// Get HTML elements

        if(storyUploaded == true || isStoryBack == true){
            start = defaults.playlist.length;
            start = start-1;
            storyUploaded = false;
            isStoryBack = false;
            $('.story-timeline-line-active').css('width','100%');
        }

        storyTime = document.getElementsByClassName('storytime')[0];
        storySpinner = document.getElementsByClassName('spinner')[0];
        thisTimeline = document.getElementsByClassName('story-active-' + start)[0];

        var text = document.getElementsByClassName('story-text')[0];
        var date = document.getElementsByClassName('story-date')[0];
        var storyContainer = document.getElementsByClassName('story-video')[0];
        isClosed = false;
        storyContainerOpen = true;
        // Show the Social Story Pop-up
        if (start < 0) {
            storyTime.setAttribute("style", "display: block; opacity: 0;");
        } else {
            storyTime.setAttribute("style", "display: block; opacity: 1;");
        }

        
        storyId = defaults.playlist[start].sid;
        storyUser = defaults.playlist[start].uid;
        storyCredits = defaults.playlist[start].credits;
        storyPurchased = defaults.playlist[start].purchased;
        storyReview = defaults.playlist[start].review;
        $('[data-story-credits]').text(storyCredits);
        if(storyUser == user_info.id){
            myStory = 1;
            $('.storySettings').show();
        } else {
            myStory = 0;
        }
        storySpinner.style.display = 'none';
        setTimeout(function() {
            storyTime.setAttribute("style", "display: block; opacity: 1;");
        }, 10);

        text.innerHTML = defaults.playlist[start].title;
        date.innerHTML = defaults.playlist[start].date;


        var storyType = '';
        clearInterval(imageInterval);
        thisTimeline.style.width = '0%';
        $('#replyStoryMessage').val('');

        chatStoryType = defaults.playlist[start].stype;
        chatStoryUrl = defaults.playlist[start].url;
        if(defaults.playlist[start].stype == 'video'){
            storyContainer.innerHTML = '<video class="story-next videoStory" src="" playsinline></video><div class="leftSection" onclick="socialStory.prev();"></div><div class="rightSection" onclick="socialStory.next();"></div>';
            video = document.getElementsByClassName("videoStory")[0];

            // Set source for new video and load it into page
            video.src = defaults.playlist[start].url;
            video.load();

            thisTimeline.style.width = '0%'
            var videoStoryLoading = false;
            var videoIsPlaying = false;
            var videoPlay = setInterval(function(){
                if(video.currentTime > 0.7){
                    videoIsPlaying = true;
                    clearInterval(videoPlay);

                } else {                   
                    video.play();
                    videoIsPlaying = true;
                    clearInterval(videoPlay);                     
                }
            },50)
            if (video.readyState == 0 ) {
                videoStoryLoading = true;
                setTimeout(function(){
                    if(videoStoryLoading){
                        storySpinner.style.display = 'block';
                    }
                },150)
                
            }
            video.oncanplay = function() {
                storySpinner.style.display = 'none';
                videoStoryLoading = false;
                video.play();
                document.getElementsByClassName('story-video')[0].setAttribute("style", "min-width: " + video.offsetWidth + "px;");
                video.muted = false;
            };


            video.addEventListener('timeupdate', timeUpdate, false);

            video.addEventListener('ended', videoEnded, false);
            isVideo = true;
        } else {
            video = '';
            storySpinner.style.display = 'none';
            thisTimeline.style.width = '0%';
            storyContainer.innerHTML = '<img class="story-next videoStory" src="" /><div class="leftSection" onclick="socialStory.prev();"></div><div class="rightSection" onclick="socialStory.next();"></div>';  
            photo = document.getElementsByClassName("videoStory")[0];
            photo.src = defaults.playlist[start].url;
            timeUpdateImg(defaults.playlist[start].duration);  
            isVideo = false;  
                 
        }

        window.clearTimeout(storyPaused);

        if(storyCredits > 0 && storyPurchased == 0 && myStory == 0 && !storyAdmin){         
            $('.storyCredit').fadeIn();
            $('.story-video').addClass('blurred');

            if($('.story-blur').length > 0){
                $('.story-blur').remove();    
            }
            $('.story-window').append('<div class="story-blur gradient8 box-shadow" onclick="socialStory.purchaseStory('+storyId+','+storyCredits+')"><span>'+site_lang[615]['text']+' '+ storyCredits +' '+site_lang[285]['text']+'</span></div>');
            $('[data-story-chat-input]').hide();
            /*
            storyPaused = setTimeout(function(){
                if(isVideo){
                    video.pause();
                    showPause(1); 
                }
            },1000); */
        } else {
            $('.storyCredit').hide();
            $('[data-story-chat-input]').show();
            $('.story-video').removeClass('blurred');
            $('.story-blur').remove();
            if(myStory == 1 && !storyAdmin){
                $('.storyCredit').fadeIn();
                $('[data-story-chat-input]').hide();           
            }            
        }


        if(plugins['story']['storyCredits'] == 'No'){
            $('.storyCredit').remove();
        }
        
        //story review
        if(storyReview == 'Yes' && plugins['story']['showStoryInReview'] == 'No'){         
            $('.storyCredit').fadeIn();
            
            //$('.story-video').addClass('blurred');

            if($('.story-blur').length > 0){
                $('.story-blur').remove();    
            }

            $('.story-window').append('<div class="story-blur box-shadow" style="background:#fff"><span style="color:#222">'+site_lang[697]['text']+'</span></div>');
            $('[data-story-chat-input]').hide();
            /*
            storyPaused = setTimeout(function(){
                if(isVideo){
                    video.pause();
                    showPause(1); 
                }
            },1000); */
        }

        //if guest user
        if(user_info != undefined){
            if(user_info.id == 0){
                $('[data-story-chat-input]').hide(); 
            }
        }

        if(plugins['story']['enableMessage'] == 'No'){  
            $('[data-story-chat-input]').hide();
        }

        if(storyAdmin){
            $('.storyCredit').hide();
            $('.storySettings').hide();
            $('[data-story-chat-input]').hide();
            $('.story-blur').remove();
            $('.story-video').removeClass('blurred');
        }

    }

    function timeUpdate() {
        if(!newStoryUpload){
            if(!holdActive){
                var percentage = Math.ceil((100 / video.duration) * video.currentTime);
                thisTimeline.style.width = percentage + '%';
            } else {
                video.pause();
            }
        }
    }

    function timeUpdateImg(val) {
        if(!newStoryUpload){
            var ct = 0;
            imageInterval = setInterval(function(){
                if(!holdActive){

                    showPause(2);
                    var percentage = Math.ceil((100 / val) * ct);
                    thisTimeline.style.width = percentage + '%';
                    ct=ct+100;

                    if(isClosed){
                        clearInterval(imageInterval);
                        thisTimeline.style.width = '0%';
                    }

                    if(ct == val){
                        clearInterval(imageInterval);
                        if(start >= 0){
                            next();    
                        }
                    }
                }
            },100);
        }
    }   

    function purchaseStory(sid,credits){

            if(user_info.id == 0){ 
                window.location.href = siteUrl+'connect';
                return false;
            }  

            if(user_info.credits < credits){ 
                openWidget("purchaseCredits");
                return false;
            }
            var data = [];
            data.name = '';
            data.icon = site_theme['notification_inapp_credits_icon']['val'];
            data.message = site_lang[610].text+' '+credits+' ' + site_lang[73].text;
            if(mobileSite){
                pushNotifMobile(data,1);
            } else {
                pushNotif(data,1);  
            }
            updateCredits(user_info.id,credits,1,'Credits for purchase story');

            if(plugins['story']['storyCreditsPurchaseTransfer'] == 'Yes'){
                updateCredits(storyUser,credits,2,'Credits for story purchased');                
            }

            purchaseUserStory(user_info.id,sid);
            user_info.credits = user_info.credits - credits;
            $('.userCredits').text(user_info.credits);
            $('.top-menu-credits').removeClass('vivify');
            $('.top-menu-credits').removeClass('pulsate');
            setTimeout(function(){
                $('.top-menu-credits').addClass('vivify');
                $('.top-menu-credits').addClass('pulsate');         
            },50);

            $('.storyCredit').hide();
            $('[data-story-chat-input]').show();
            $('.story-video').removeClass('blurred');
            $('.story-blur').remove();
            if(isVideo){
                video.currentTime = 0;
                video.play();
                showPause(1);
            }
            defaults.playlist[start].purchased = 1;
            sendMessageFromStory(storyUser,credits);
    }

    function updateStoryCredit(s,sid,credits){
            defaults.playlist[s].credits = credits;
            updateStoryCredits(sid,credits);
    }

    function deleteCurrentStory(s,sid){
            defaults.playlist.splice(s,1);
            delStory(sid);

            if(defaults.playlist.length == 0){
                socialStory.close();
                noMoreStories = true;
                $('[data-storyPath]').html(`
                <svg class="stopCircular storyOffOnline" viewBox="25 25 50 50">
                    <circle id="storyPath" cx="50" cy="50" r="20" fill="none" stroke-width="2" stroke-miterlimit="10"/>
                </svg>`);                    
            } else {
                socialStory = new Story({
                    playlist: defaults.playlist
                }); 
                socialStory.launch();
            }            
    }        

 

    function showPause(val) {
        if(val == 1){
            $('.paused').show();
        } else {
            $('.paused').hide();
        }
    }
    function replyStory(val) {
        if(val == 1){
            holdActive = true;
            if(isVideo){
                video.pause();
            }  
            showPause(1);      
        } else {
            holdActive = false;
            if(isVideo){
                video.play();
            } 
            showPause(2);       
        }
    }
    function videoEnded() {
    	// Remove all event listeners on video end so they don't get duplicated.
        video.removeEventListener('timeupdate', timeUpdate);
        video.removeEventListener('ended', videoEnded);
        if(start >= 0){
            next();    
        }
    }

    function next() {
        //if(holdActive){return false;}
        start++;
        // If next video doesn't exist (i.e. the previous video was the last) then close the Social Story popup
        if (start >= defaults.playlist.length) {
            setTimeout(function() {
                if(storyPage == 'discover' && viewingStory < totalDiscoverStories){
                    nextStory++;

                    /*
                    socialStory = new Story({
                        playlist: carol
                    }); 
                    launch();                       
                    */
                    //close();
                    viewingStory++;
                    if(mobileSite){
                        angular.element(document.querySelector('#story'+viewingStory)).triggerHandler('click');
                    } else {
                        $('[data-current-story-position='+viewingStory+']').click();  
                    }
                } else {
                    close();
                }

                return false;
            }, 10);
        } else {
        	// Otherwise run the next video
            showPause(2);
            thisTimeline.style.width = '100%';
            launch(start);
        }
    }

    function prev() {
    	if(holdActive){return false;} 
        if (start != 0) {
            thisTimeline.style.width = '0%';
        }


        start--;
        console.log(start);
        if (start < 0) {
            start = 0;
            if(viewingStory > 1){
                viewingStory--;
                if(mobileSite){
                    angular.element(document.querySelector('#story'+viewingStory)).triggerHandler('click');
                } else {
                    $('[data-current-story-position='+viewingStory+']').click();  
                }
                isStoryBack = true;                
            } else {
                return false;    
            }
            
        } else {
        	// Otherwise run the previous video
            showPause(2);
            launch(start);
        }
    }

    function close() {

        if(isVideo){
            video.pause();
        }
        video = '';
        showPause(2);
        isClosed = true;
        holdActive = false;
        start = -1;
        storyContainerOpen = false;
        clearInterval(imageInterval);
        storyTime.setAttribute("display", "none");
        setTimeout(function() {

            var i;
            for (i = 1; i < defaults.playlist.length; i++) {
                document.getElementsByClassName('story-timeline-line-active')[i].setAttribute("style", "width: 0%;");
            }
            $('.videoStory').remove();
            storyTime.remove();
        }, 150);
    }
   
    Story.prototype.launch = function(num) {
    	if(!num) { var num = 0;}
        start = num;
        launch();
    };

    Story.prototype.replyStory = function() {
        replyStory();
    };
    Story.prototype.purchaseStory = function(sid,credits){
        purchaseStory(sid,credits);
    };
    Story.prototype.updateStoryCredit = function(s,sid,credits){
        updateStoryCredit(s,sid,credits);
    };    
    
    Story.prototype.next = function() {
        next();
    };    

    Story.prototype.prev = function() {
        prev();
    };

    Story.prototype.close = function() {
        close();
    };

}());


function purchaseUserStory(uid,sid) {
    var query = uid+','+sid;
    $.get( aUrl, { action: 'purchaseStory', query: query } );
}

function updateStoryCredits(sid,credits) {
    var query = sid+','+credits;
    $.get( aUrl, { action: 'storyPrice', query: query } );
}


function delStory(sid) {
    var query = sid;
    $.get( aUrl, { action: 'deleteStory', query: query } );
}


function onMouseDown(){
    holdStarter = setTimeout(function() {
        holdStarter = null;
        holdActive = true;
        /*
        if(isVideo){
            video.pause();
        }   
        showPause(1);*/
    }, holdDelay);
}
function onMouseUp(){
    if (holdStarter) {
        clearTimeout(holdStarter);
        console.log('Clicked!');
    }
    else if (holdActive) {
        setTimeout(function() {
            holdActive = false;
        },100);

        /*
        if(isVideo){
            video.play();
        }  
        showPause(2);*/          
    }
}


$("#upload-story").dmUploader({
    url: siteUrl+'assets/sources/upload.php',
    extFilter: ["jpg", "jpeg", "png", "mp4", "ogg", "webm"],
    multiple: false,
    onFileExtError: function(file){
        swal({ title: site_lang[811]['text'], text: site_lang[596]['text'],   type: 'info' }, function(){ });        
    },
    onNewFile: function(id, file){
        console.log(id);
        if(file.size > site_config.max_upload){
            var maxAllowed = site_config.max_upload / 1024 / 1024;
            swal({   title: site_lang[810]['text'], text: site_lang[809]['text']+' ('+maxAllowed+') MB',   type: 'info' }, function(){ });
            return false;
        }
        var fileUrl = URL.createObjectURL(file);
        var newStory= {}; 
        var dataArr = new Array(0);
        var upStoryType = uploadedStoryType(file, fileUrl,id);
        uploadingStoryFakeId = Math.floor(Math.random()*(90000-10000+1)+10000);
        newStoryUpload = true;
        newStory['credits'] = 0;
        newStory['date'] = site_lang[594]['text']+'...';
        newStory['icon'] = user_info.profile_photo;
        newStory['title'] = user_info.first_name;
        newStory['uid'] = user_info.id;
        newStory['sid'] = uploadingStoryFakeId;
        newStory['id'] = uploadingStoryFakeId;
        newStory['purchased'] = 0;
        newStory['stype'] = upStoryType;
        newStory['url'] = fileUrl;
        newStory['src'] = fileUrl;
        newStory['review'] = 'No';

        var newStoryJSON = eval(newStory); 
        dataArr.push(newStoryJSON);
        console.log(dataArr);
        storyLoader(1,dataArr,1,1);          
    },  
    onUploadProgress: function(id,percent){
        var uploadingStoryTimeline = document.getElementsByClassName('story-active-0')[0];
        uploadingStoryTimeline.style.width = percent+'%';
    },
    onComplete: function(){
    },
    onUploadSuccess: function(id, file){
    noMoreStories = false;
    upphotos[0] = file;
    var photoPath = file.path;
    newStoryUpload = false;
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
          dataType:'JSON',
          success: function(response) {
            var story = response;
            user_info.stories = response['storiesProfile'];
            storyUploaded = true;    
            storyLoader(story['story'],story['stories'],1,1);
            $('[data-upload-story]').show();

          }
        });         
    }
  }
  
}); 

function sendMessageFromStory(id,fromCredits=0){

    var r_id = id;
    var messageVal = $('#replyStoryMessage').val();
    if(fromCredits > 0){
        messageVal = site_lang[678]['text'];
    }
    var message = user_info.id+'[message]'+r_id+'[message]'+messageVal+'[message]story'+'[message]'+storyId+'[message]'+fromCredits;
    var send = user_info.id+'[rt]'+r_id+'[rt]'+user_info.profile_photo+'[rt]'+user_info.first_name+'[rt]'+messageVal+'[rt]story[rt]'+chatStoryUrl+'[rt]'+chatStoryType;      

    $('#replyStoryMessage').val('');

    if(fromCredits == 0){
        var data = [];
        data.name = '';
        if(plugins['story']['sendMessage'] > 0){  
            if(user_info.credits < plugins['story']['sendMessage']){
                openWidget("purchaseCredits");
                return false;
            }              
            data.icon = site_theme['notification_inapp_credits_icon']['val'];
            data.message = site_lang[662]['text']+'<br>'+site_lang[610].text+' '+plugins['story']['sendMessage']+' ' + site_lang[73].text;
            updateCredits(user_info.id,plugins['story']['sendMessage'],1,'Credits for purchase story'); 
            if(plugins['story']['storyCreditsMessageTransfer'] == 'Yes'){
                updateCredits(id,plugins['story']['sendMessage'],2,'Credits for message from story');                
            }            
        } else {
            data.icon = user_info.profile_photo;
            data.message = site_lang[662]['text'];
        }

        if(mobileSite){
            pushNotifMobile(data,1);
        } else {
            pushNotif(data,1);  
        }
        
    }       
    $.get( gUrl, {action: 'message', query: send} );        
    $.get( aUrl, {action: 'sendMessage', query: message} );

}

var upStoryContent = document.getElementById('uploadStoryContent');
function upStory(){
    uploadStory = true;
    $('[data-upload-story]').hide();
    upType = 4;
    document.getElementById("uploadStoryContent").click();
    uploadingStory();   
    console.log('upload story');
    multipleUpload = false;
    document.body.onfocus = checkIfCanceled;
}

function upStoryDiscover(){
    uploadStory = true;
    $('[data-discover-upload-story="1"]').hide();
    upType = 4;
    document.getElementById("uploadStoryContent").click();
    multipleUpload = false; 
    document.body.onfocus = checkIfCanceled;
}

function upStoryMobile(){
    uploadStory = true;
    //$('[data-discover-upload-story="1"]').hide();
    upType = 4;
    document.getElementById("uploadStoryContent").click();
    multipleUpload = false; 
    document.body.onfocus = checkIfCanceled;
}

function checkIfCanceled(){
    document.body.onfocus = null;
    $('[data-storyRoller]').removeClass('roller');
    $('[data-storyRoller]').addClass('stopRoller');
    $('[data-storyPath]').html(`
        <svg class="stopCircular storyOn" viewBox="25 25 50 50">
            <circle id="storyPath" cx="50" cy="50" r="20" fill="none" stroke-width="2" stroke-miterlimit="10"/>
        </svg>`);  
    $('[data-upload-story]').show();    
}

function openStory(val,uid=0){
    if(val >= 1){
        storyPage = 'profile';
        if(uid != 0){
            openStoryProfile(uid);
        } else {
            openStoryProfile(profile_info.id);
        }
    }
}

function openStoryLanding(data){
    
    socialStory = new Story({
        playlist: data
    });
    socialStory.launch();
    preloadStories(data);
}

var isStoryLoading = false;
function openStoryDiscover(element,uid,openAdmin=false){
    console.log('launch story');
    storyPage = 'discover';
    viewingStory = $(element).attr('data-current-story-position');
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
                console.log(response['stories']);
                currentStoryUserData =  response['stories'];
                socialStory = new Story({
                    playlist: currentStoryUserData
                });
                preloadStories(currentStoryUserData);
                storyAdmin = openAdmin;
                socialStory.launch();
                isStoryLoading = false;
                $('[data-story-loader='+uid+']').hide();    
            }
        });
    }   
}

function openStoryProfile(uid,openAdmin=false){
    console.log('launch story');
    if(!isStoryLoading){
        isStoryLoading = true;
        uploadingStory();
        $.ajax({
            url: request_source()+'/api.php', 
            data: {
                action:"viewStory",
                uid: uid
            },  
            type: "GET",
            dataType: 'JSON',
            success: function(response) {
                console.log(response['stories']);
                currentStoryUserData =  response['stories'];
                socialStory = new Story({
                    playlist: currentStoryUserData
                });
                preloadStories(currentStoryUserData);
                storyAdmin = openAdmin;
                endStoryloading();
                socialStory.launch();
                isStoryLoading = false;    
            }
        });
    }   
}

function openStoryAlbum(stories){
    preloadStories(stories);

    socialStory = new Story({
        playlist: stories
    });
    socialStory.launch();    
}

function storyLoader(story,stories,status,up=0){

    if(plugins['story']['enabled'] == 'No'){
        return false;
    }

    var storyLoaderTimeout = 500;
    if(up == 1){
        storyLoaderTimeout = 50;
    }
    if(story >= 1 && up == 0){
        preloadStories(JSON.parse(stories)); 
    }
    if(story >= 1 && up == 1){
        preloadStories(stories); 
    }    
    $("[data-loading-story]").show();
    $('[data-storyRoller]').removeClass('stopRoller');
    $('[data-storyRoller]').addClass('roller'); 

    $('[data-storyRoller]').removeClass('roller');
    $('[data-storyRoller]').addClass('stopRoller');
    $('[data-storyPath]').html(`
    <svg class="stopCircular storyOff" viewBox="25 25 50 50">
        <circle id="storyPath" cx="50" cy="50" r="20" fill="none" stroke-width="2" stroke-miterlimit="10"/>
    </svg>`);

    if(status >= 1){
        $('[data-storyPath]').html(`
        <svg class="stopCircular storyOffOnline" viewBox="25 25 50 50">
            <circle id="storyPath" cx="50" cy="50" r="20" fill="none" stroke-width="2" stroke-miterlimit="10"/>
        </svg>`);                   
    }

    if(story >= 1){
        $('[data-storyPath]').html(`
            <svg class="circular" viewBox="25 25 50 50">
                <circle id="storyPath" class="path" cx="50" cy="50" r="20" fill="none" stroke-width="2" stroke-miterlimit="10"/>
            </svg>`);

        setTimeout(function(){
            $('[data-storyRoller]').removeClass('roller');
            $('[data-storyRoller]').addClass('stopRoller');

            if(story >= 1){
                if(up == 1){
                    currentStoryUserData = stories;
                    socialStory = new Story({
                        playlist: currentStoryUserData
                    }); 
                    socialStory.launch();               
                } else {
                    currentStoryUserData =  JSON.parse(stories);
                    socialStory = new Story({
                        playlist: currentStoryUserData
                    });
                }
                $('[data-storyPath]').html(`
                <svg class="stopCircular storyOn" viewBox="25 25 50 50">
                    <circle id="storyPath" cx="50" cy="50" r="20" fill="none" stroke-width="2" stroke-miterlimit="10"/>
                </svg>`);           
            } else {
                $('[data-storyPath]').html(`
                <svg class="stopCircular storyOff" viewBox="25 25 50 50">
                    <circle id="storyPath" cx="50" cy="50" r="20" fill="none" stroke-width="2" stroke-miterlimit="10"/>
                </svg>`);           
            }   
        },storyLoaderTimeout);      
    }
    
}


function endStoryloading(){

    if(plugins['story']['enabled'] == 'No'){
        return false;
    }

    $("[data-loading-story]").show();
    $('[data-storyRoller]').removeClass('stopRoller');
    $('[data-storyRoller]').addClass('roller'); 

    $('[data-storyRoller]').removeClass('roller');
    $('[data-storyRoller]').addClass('stopRoller');
    $('[data-storyPath]').html(`
    <svg class="stopCircular storyOff" viewBox="25 25 50 50">
        <circle id="storyPath" cx="50" cy="50" r="20" fill="none" stroke-width="2" stroke-miterlimit="10"/>
    </svg>`);

    $('[data-storyPath]').html(`
    <svg class="stopCircular storyOn" viewBox="25 25 50 50">
        <circle id="storyPath" cx="50" cy="50" r="20" fill="none" stroke-width="2" stroke-miterlimit="10"/>
    </svg>`); 
          
}

function uploadingStory(){
    $("[data-loading-story]").show();
    $('[data-storyRoller]').removeClass('stopRoller');
    $('[data-storyRoller]').addClass('roller'); 

    $('[data-storyRoller]').removeClass('roller');
    $('[data-storyRoller]').addClass('stopRoller');
    $('[data-storyPath]').html(`
        <svg class="circular" viewBox="25 25 50 50">
            <circle id="storyPath" class="path" cx="50" cy="50" r="20" fill="none" stroke-width="2" stroke-miterlimit="10"/>
        </svg>`);
}

function preloadStories(stories){
  const videoFileUrls = [];
  videoPreloadCount = 0;
  $('#loadingVideos').html('');
    for (var i = 0; i < stories.length; i++) {
        if(stories[i]['stype'] == 'video' && videoPreloadCount < 5){
            videoPreloadCount++;
            console.log("video preload started");
            videoFileUrls.push(stories[i]['url']);
            preloadVideo(stories[i]['id'],stories[i]['url']);
        }
        if(stories[i]['stype'] != 'video'){
            $('#loadingImages').append('<img class="lazy" data-src="'+stories[i]['url']+'" src="'+stories[i]['url']+'"" />');
            preloadImages();
        }
    }
    if(siteUrl.indexOf("https") > 0 ) {
        window.caches.open('video-pre-cache')
        .then(cache => Promise.all(videoFileUrls.map(videoFileUrl => fetchAndCache(videoFileUrl, cache))));
    }
    function fetchAndCache(videoFileUrl, cache) {
        return cache.match(videoFileUrl)
        .then(cacheResponse => {
          if (cacheResponse) {
            return cacheResponse;
          }
          return fetch(videoFileUrl)
          .then(networkResponse => {
            cache.put(videoFileUrl, networkResponse.clone());
            return networkResponse;
          });
        });
    }  
}

function preloadImages() {
  var lazyloadImages;
  console.log('preloading images');  
    document.addEventListener("DOMContentLoaded", function() {
      if ("IntersectionObserver" in window) {
        lazyloadImages = document.querySelectorAll(".lazy");
        console.log("image preload");
        var imageObserver = new IntersectionObserver(function(entries, observer) {
          entries.forEach(function(entry) {
            if (entry.isIntersecting) {
              var image = entry.target;
              image.src = image.dataset.src;
              image.classList.remove("lazy");
              imageObserver.unobserve(image);
              console.log('image loaded');
            }
          });
        });

        lazyloadImages.forEach(function(image) {
          imageObserver.observe(image);
        });
      } else {  
        var lazyloadThrottleTimeout;
        lazyloadImages = document.querySelectorAll(".lazy");
        
        function lazyload () {
          if(lazyloadThrottleTimeout) {
            clearTimeout(lazyloadThrottleTimeout);
          }    

          lazyloadThrottleTimeout = setTimeout(function() {
            var scrollTop = window.pageYOffset;
            lazyloadImages.forEach(function(img) {
                if(img.offsetTop < (window.innerHeight + scrollTop)) {
                  img.src = img.dataset.src;
                  img.classList.remove('lazy');
                }
            });
            if(lazyloadImages.length == 0) { 
              document.removeEventListener("scroll", lazyload);
              window.removeEventListener("resize", lazyload);
              window.removeEventListener("orientationChange", lazyload);
            }
          }, 20);
        }

        document.addEventListener("scroll", lazyload);
        window.addEventListener("resize", lazyload);
        window.addEventListener("orientationChange", lazyload);
      }
    })
}

function preloadVideo(id,src){
    /*
    if( navigator.userAgent.match(/Android/i)
     || navigator.userAgent.match(/webOS/i)
     || navigator.userAgent.match(/iPhone/i)
     || navigator.userAgent.match(/iPad/i)
     || navigator.userAgent.match(/iPod/i)
     || navigator.userAgent.match(/BlackBerry/i)
     || navigator.userAgent.match(/Windows Phone/i)){
        return false;
     }
    if($("#" + id).length == 0) {
        var pVideo = $('<video />', {
            id: id,
            src: src,
            type: 'video/mp4',
            muted: true,
            preload: 'auto',
            style: 'display:none',
            controls: false
        });
        pVideo.appendTo($('#loadingVideos'));
        var vid = document.getElementById(id);
        vid.onloadstart = function() {
        };

        vid.onloadedmetadata = function() {
          vid.currentTime = 3;
          vid.setAttribute('preload', "auto");
          //vid.play();
          vid.pause();
        };

        vid.onloadeddata = function() {
          vid.currentTime = 0;
        }; 
    } */   
}

function uploadedStoryType(file, fileContents,id) {
    var $storyType = '';
    switch (file.type) {
        case 'image/png':
        case 'image/jpeg':
        case 'image/gif':
            $storyType = 'image';
        break;
        case 'video/mp4':
        case 'video/webm':
        case 'video/ogg':
            $storyType = 'video';
        break;
        default:
        break;
    }
    return $storyType;
}

document.onkeydown = checkKey;

function checkKey(e) {
    e = e || window.event;
    if (e.keyCode == '37') {
        if(storyContainerOpen === true){
            Story.prototype.prev();
        }
    }
    else if (e.keyCode == '39') {
         if(storyContainerOpen === true){
            Story.prototype.next();
        }     
    }
    else if (e.keyCode == '27') {
        if(storyContainerOpen === true){
            Story.prototype.close();
        }        
    }    
}

function updateCredits(uid,amount,type,reason,reward='') {
    var credits = parseInt(amount);
    if(type == 1){
         playAudio('coin.wav');
         user_info.credits = user_info.credits - credits;
    } else {
        //user_info.credits = user_info.credits + credits;
    }
    user_info.credits = parseInt(user_info.credits);
    $('.userCredits').hide();
    $('.userCredits').show();
    $('.userCredits').html('<b>'+user_info.credits+'</b>');

    if(mobileSite){
        
    }
    var query = uid+','+amount+','+type+','+reason+','+reward;
    $.get( aUrl, { action: 'updateCredits', query: query } );
}

function pushNotifMobile(data,type=0,time=1000){
    if(!$('.chatNotification').hasClass('is-visible')){     
        $('.chatNotification').attr('ng-click',"hideNotification()");                           
        $('.chatNotification').removeClass('is-visible');
        $('.chatNotificationPhoto').removeClass('sblur');   
        $('.chatNotificationPhoto').css('background-image', 'url('+ data.icon +')');
        $('.chatNotificationContent').html(data.message);
        setTimeout(function(){
            if(!$('.chatNotification').hasClass('is-visible')){
                $('.chatNotification').addClass('is-visible');
            }
        },100);             
        setTimeout(function(){
            if($('.chatNotification').hasClass('is-visible')){
                $('.chatNotification').removeClass('is-visible');
            }
        },time);                    
    }
}

function playAudio(sound) {
    var audio = new Audio(siteUrl+'assets/sounds/'+sound);
    audio.play();
}