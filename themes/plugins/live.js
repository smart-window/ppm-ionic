const apiPath = 'https://director.millicast.com/api/director/publish';
const viewerPath  = 'https://director.millicast.com/api/director/subscribe';
const turnUrl = 'https://turn.millicast.com/webrtc/_turn';
var rtlive,loadTime,renderTime,now;
var is_streaming = false;
var liveInterval;
var endStreamPreview = 'Yes';
var streamViewers; 
var bannedFromStream = false;
var privateStreamId = 0;
var privateStreamPrice = 0;

let liveUrl;
let jwt;
var viewStreamId = 0;

let token = plugins['live']['pub_token'];
let streamName = 'live'+user_info.id;
let accountId = plugins['live']['account_id'];

let stream;
let iceServers = [];
let views = [
  {form: 'tokenTxt', param: 'token'},
  {form: 'streamTxt', param: 'streamName'},
  {form: 'viewTxt', param: 'accountId'}
];

function startBroadcast() {
  getICEServers()
    .then(list => {
      iceServers = list;
      liveConnect();
    })
    .catch(e => {
      alert('getICEServers Error: ', e);
      liveConnect();
    });
}

function closeLive(id){
  $('.stream-container').hide();
}

function streamTab(tab){
  $('[data-stream-tab]').hide();
  $('[data-stream-tab="'+tab+'"]').show();
  $('[data-stream-tab-link]').removeClass('is-current');
  $('[data-stream-tab-link="'+tab+'"]').addClass('is-current');  
}

function subtractDate(low, high) {
  var secs = 1000
  var min = secs * 60
  var hour = min * 60
  var day = hour * 24
  var miliSecs = Math.abs(high.getTime() - low.getTime())
  var days = Math.floor(miliSecs / day)
  miliSecs = miliSecs % day
  var hours = Math.floor(miliSecs / hour)
  miliSecs = miliSecs % hour
  var mins = Math.floor(miliSecs / min)
  miliSecs = miliSecs % min
  var sec = Math.floor(miliSecs / secs)
  return {
    day: days,
    hour: hours,
    min: mins,
    sec: sec,
  }
}

function renderTime() {
    now = new Date()
    var sDate = subtractDate(loadTime, now);
    if(sDate.hour > 0){
      var str = sDate.hour+':' + sDate.min + ':' + sDate.sec
    } else {
      var str = sDate.min + ':' + sDate.sec        
    }
    
    timeElement.innerHTML = str
}

function endStream(force='No'){
  $.get( request_source()+'/live.php', {action: 'endStream'},function(data){
    if(force == 'No'){
      window.location.reload();
    }
  });
}

function updateLiveStatus(status){
  if(status == 'private'){
    $('#startPrivateStream').show();
  } else {
    $('#publicStream').hide();
    $('#streamInPrivate').hide();
    $('#privateStream').fadeIn();
    $.get( request_source()+'/live.php', {action: 'status', status: 'public',price: 0});
  }
}

function accessPrivateStream(){
  if(privateStreamPrice > user_info.credits){
    openWidget("purchaseCredits");
    return false;    
  } else {

    var data = [];
    data.name = '';
    data.icon = site_theme['notification_inapp_credits_icon']['val'];
    data.message = site_lang[610].text+' '+privateStreamPrice+' ' + site_lang[73].text;
    
    updateCredits(user_info.id,privateStreamPrice,1,'Credits for access to private stream');
    updateCredits(privateStreamId,privateStreamPrice,0,'Credits recieved for private stream access');

    pushNotif(data,1);

    $('#streamPrivate').hide();
    $('#streamLoading').show();
    $('#streamCurrentVideo').show();
    $("#streamCurrentVideo").prop('muted', false);
    $('#streamInPrivate').show();
    $('#closeAccessPrivateStreamModal').click();
    if(privateStreamId != viewStreamId){
      if(iceServers.length == 0){
          getICEServers()
          .then(list => {
            iceServers = list;
            updateMillicastViewerAuth(privateStreamId)
              .then(d => {
                $.get( request_source()+'/live.php', {action: 'checkBanned',userId: user_info.id,streamId: privateStreamId},function(data){
                  if(data == 'Yes'){
                    $('.stream-container').hide();
                    openWidget('bannedStream');
                    viewStreamId = 0;
                    privateStreamId = 0;
                    goTo('live');
                  } else {
                    var watchingData = user_info.id+','+user_info.first_name+','+user_info.profile_photo+','+privateStreamId;
                    $.get( request_source()+'/live.php', {action: 'watching', query: watchingData} );
                    viewLive('Yes');  
                    viewStreamId = privateStreamId;          
                  }
                });            
              })
              .catch(e => {
                $('#streamLoading').hide();
                $('#streamEnded').show();
                $.get( request_source()+'/live.php', {action: 'endStreamFromViewer', live: privateStreamId} );
              });
          });
        } else {
          updateMillicastViewerAuth(privateStreamId)
            .then(d => {
                $.get( request_source()+'/live.php', {action: 'checkBanned',userId: user_info.id,streamId: privateStreamId},function(data){
                  if(data == 'Yes'){
                    $('.stream-container').hide();
                    openWidget('bannedStream');
                    viewStreamId = 0;
                    privateStreamId = 0;
                    goTo('live');
                  } else {
                    var watchingData = user_info.id+','+user_info.first_name+','+user_info.profile_photo+','+privateStreamId;
                    $.get( request_source()+'/live.php', {action: 'watching', query: watchingData} );
                    viewLive('Yes'); 
                    viewStreamId = privateStreamId;                 
                  }
                });
            })
            .catch(e => {
              $('#streamLoading').hide();
              $('#streamEnded').show();
              $.get( request_source()+'/live.php', {action: 'endStreamFromViewer', live: privateStreamId} );
            });
        } 
    } else {
      $('#streamLoading').hide(); 
    }
  }
}

function startPrivateStream(){
  var streamPrice = $('#streamPrivatePrice').val();
  if(streamPrice == 0 || streamPrice == null){
    return false;
  }
  $('#privateStream').hide();
  $('#publicStream').fadeIn();
  $('#closeStreamPrivate').click();
  $('#streamInPrivate').show();
  $.get( request_source()+'/live.php', {action: 'status', status: 'private',price: streamPrice});
}

function bannedStreamer(){
  openWidget('streamerBanned');
}

function watchLive(id,photo,uid,name,age,streamTime,streamMessage='',streamTimeM,viewers,credits,private,private_price=0) {
  viewStreamId = id;
  rtlive = 'live'+id;
  streamViewers = viewers;
  streamCredits = credits;
  bannedFromStream = false;
  $('[data-show-to-streamer]').hide();
  $('#streamViewers').text(streamViewers);
  $('#streamCredit').text(streamCredits);



  channel.unbind();
  channel.bind(rtlive, rtlivecallback);  
  $('#streamEnded').hide();
  $('#streamLoading').show();
  $('#streamVideoContainer').show();
  $('#streamClose').show();
  $('.stream-container').show();
  $('#liveChatContainer').html('');

  if(uid == user_info.id || user_info.admin == 1){
    $('#endStream').show();
  } else {
    $('#endStream').hide();
  }

  if(private == 'Yes'){
    $('#streamPrivateCredits').text(private_price);
    $('[data-private-stream-credits]').text(private_price);
    $('#streamPrivateProfilePhoto').attr('data-src',photo);
    profilePhoto();
    if(plugins['live']['showPrivateStreamModal'] == 'Yes'){
      $('#showStreamPrivate').show();
    }
    $('#streamPrivate').show();
    $('#streamLoading').hide();
    privateStreamId = id;
    privateStreamPrice = private_price;
  }

  $('#streamTime').text(streamTime);
  $('#streamProfilePhoto').attr('src',photo);
  $('[data-streamProfileLink]').attr('href',site_config.site_url+'@'+uid);
  $('#streamProfileName').text(name+', '+age);
  $('#streamBgLoader').css('background-image', 'url('+photo+')');

  loadTime = new Date(streamTimeM);
  now = new Date();

  timeElement = document.getElementById('streamTime');
  renderTime();

  liveInterval = window.setInterval(renderTime, 1000);

  var watchingData = user_info.id+','+user_info.first_name+','+user_info.profile_photo+','+viewStreamId;
  

  if(plugins['live']['adminCustomMessage'] != ''){
    $('#liveChatContainer').append(`
      <div class="stream__message" >
      <div class="stream-message is-system stream-message--system">
      <div class="stream-message__info">
          <div class="stream-message__content">`+plugins['live']['adminCustomMessage']+`</div>
      </div>        
      </div>
      </div>
    `);    
  }

  //STREAM CUSTOM MESSAGE
  if(streamMessage != ''){
    $('#liveChatContainer').append(`
    <div class="stream__message" id="streamMessage">
    <div class="stream-message is-system stream-message--system stream-message-style">
    <div class="stream-message__media">
    <a class="stream-message__avatar" href='#'>
    <img src="`+photo+`" class="stream-message__avatar-image">
    </a>
    </div>
    <div class="stream-message__info">
    <div class="stream-message__content black-color">`+streamMessage+`</div>
    </div>
    </div>
    </div>
    `);
  }

  if(private == 'No'){
    if(iceServers.length == 0){
      getICEServers()
      .then(list => {
        iceServers = list;
        updateMillicastViewerAuth(viewStreamId)
          .then(d => {
            $.get( request_source()+'/live.php', {action: 'checkBanned',userId: user_info.id,streamId: id},function(data){
              if(data == 'Yes'){
                $('.stream-container').hide();
                openWidget('bannedStream');
                viewStreamId = 0;
                goTo('live');
              } else {
                $.get( request_source()+'/live.php', {action: 'watching', query: watchingData} );
                viewLive();                  
              }
            });            
          })
          .catch(e => {
            $('#streamLoading').hide();
            $('#streamEnded').show();
            $.get( request_source()+'/live.php', {action: 'endStreamFromViewer', live: viewStreamId} );
          });
      });
    } else {
      updateMillicastViewerAuth(viewStreamId)
        .then(d => {
            $.get( request_source()+'/live.php', {action: 'checkBanned',userId: user_info.id,streamId: id},function(data){
              if(data == 'Yes'){
                $('.stream-container').hide();
                openWidget('bannedStream');
                viewStreamId = 0;
                goTo('live');
              } else {
                $.get( request_source()+'/live.php', {action: 'watching', query: watchingData} );
                viewLive();                  
              }
            });
        })
        .catch(e => {
          $('#streamLoading').hide();
          $('#streamEnded').show();
          $.get( request_source()+'/live.php', {action: 'endStreamFromViewer', live: viewStreamId} );
        });
    }
  } else {
    viewStreamId = 0;
  }

  return;
}

$("#sendLiveMessage").on('keyup', function (e) {
    if (e.keyCode === 13) {
        sendLiveMessage();
    }
});

function sendLiveMessage(){
  var message = $('#sendLiveMessage').val();
  if(message == ''){
    return false;
  }
  var data = viewStreamId+';-B-;'+message+';-B-;'+user_info.first_name+';-B-;'+user_info.profile_photo+';-B-;'+user_info.id;
  $.get( request_source()+'/live.php', {action: 'sendLiveMessage', query: data} );
  $('#sendLiveMessage').val('');
  $('#sendLiveMessage').focus();
}

function sendLiveGift(src,price){
  $('[data-gift-img]').attr('src',src);
  $('[data-gift-price]').text(price);
  $('#send-gift').show();
  streamGiftCredits = price;
  streamGiftIcon = src;   
}

function closeSendGift(){
  $('#send-gift').hide();
}

function updateLiveScreen(val){
  if(val == 'full'){
    $("#streamCurrentVideo").css('objectFit', 'cover');
    $('#screenFull').hide();
    $('#screenRegular').show();
  } else {
    $("#streamCurrentVideo").css('objectFit', '');
    $('#screenFull').show();
    $('#screenRegular').hide();
  }
}

function updateSound(val){
  if(val == 'enable'){
    $("#streamCurrentVideo").prop('muted', false);
    $('#streamVolumenOFF').hide();
    $('#streamVolumenON').show();
  } else {
    $("#streamCurrentVideo").prop('muted', true);
    $('#streamVolumenON').hide();
    $('#streamVolumenOFF').show();
  }
}

function viewLive(private='No'){
  var connectId = viewStreamId;
  if(private == 'Yes'){
    connectId = privateStreamId;
  }  
  if (!liveUrl) {
    updateMillicastViewerAuth(connectId)
      .then(d => {
        console.log('Server:', d);
        viewLive();
      })
      .catch(e => {
      });
    return;
  }  
  let conf = {
    iceServers:    iceServers,
    rtcpMuxPolicy: "require",
    bundlePolicy:  "max-bundle"
  };
  let pc     = new RTCPeerConnection(conf);
  pc.ontrack = function (event) {
    let vidWin = document.getElementById('streamCurrentVideo');
    if (vidWin) {
      setTimeout(function(){
        $('#streamLoading').fadeOut('slow');
        var endStreamInterval  = plugins['live']['onlyForPremiumSeconds'] * 1000;
        if(plugins['live']['onlyForPremium'] == 'Yes' && user_info.premium == 0){
          $('#streamOnlyPremium').show();
          setTimeout(function(){
            $('#streamCurrentVideo').remove();
          },endStreamInterval);
        }        
        if(plugins['live']['onlyVerifiedView'] == 'Yes' && user_info.verified == 0){
          if($('#streamOnlyPremium').is(':visible')) {
          } else {
            $('#streamOnlyVerified').show();  
          }
          setTimeout(function(){
            $('#streamCurrentVideo').remove();
          },endStreamInterval);
        }         
      },700);

      vidWin.srcObject = event.streams[0];
    }
    
  };

  let ws    = new WebSocket(liveUrl + '?token=' + jwt);
  ws.onopen = function () {

    let offer = pc.createOffer({
                                 offerToReceiveAudio: true,
                                 offerToReceiveVideo: true
                               }).then(desc => {
      
      pc.setLocalDescription(desc)
        .then(() => {
          let data    = {
            streamId: accountId,
            sdp:      desc.sdp
          }
          let payload = {
            type:    "cmd",
            transId: 0,
            name:    'view',
            data:    data
          }
          ws.send(JSON.stringify(payload));
        })
        .catch(e => {
        })
    }).catch(e => {
    });
  }

  ws.addEventListener('message', evt => {
    let msg = JSON.parse(evt.data);
    if(msg.name == 'inactive'){
      $('#streamVideoContainer').hide();
      $('#streamEnded').show();
      clearInterval(liveInterval);
    } else {
      $('#streamVideoContainer').show();
      $('#streamEnded').hide();      
    }
    switch (msg.type) {
      case "response":
        let data   = msg.data;
        let answer = new RTCSessionDescription({
         type: 'answer',
         sdp:  data.sdp
       });

      pc.setRemoteDescription(answer)
      .then(d => {
      })
      .catch(e => {
      });
      break;
    }
  })
}


var rtlivecallback = function(data) {
  var streamerAction = '';
  if(data.type == 'message'){
    if(user_info.id == data.liveId){
      streamerAction = `
        <div class="stream-message__actions">
            <div class="stream-message__action" role="button" onclick="streamAlert('yes-no', 'Kick User','`+data.name+` will be kicked and banned from your live stream',`+data.userId+`);">
                <div class="stream-message__action-icon">
                    <div class="icon icon--block icon--stretch">
                        <svg class="icon__svg">
                          <svg id="icon-flag" viewBox="0 0 32 32"><path d="M4.53 32H2V0h27v17.98H4.53V32zm21.94-16.45V2.43H4.53v13.12h21.94z"></path></svg>
                        </svg>
                    </div>
                </div>
            </div>
        </div>
      `;
    }

    $('#liveChatContainer').append(`
      <div class="stream__message">
          <div class="stream-message">
              <div class="stream-message__media">
                  <a class="stream-message__avatar" href="`+site_config.site_url+`@`+data.userId+`?from=livestream" target="_blank">
                  <img src="`+data.photo+`" alt="" class="stream-message__avatar-image"></a>
              </div>
              <div class="stream-message__info">
                  <div class="stream-message__header">
                  <a class="stream-message__title has-link" href="`+site_config.site_url+`@`+data.userId+`?from=livestream" target="_blank">`+data.name+`</a></div>
                  <div class="stream-message__content">`+data.message+`</div>
              </div>
              `+streamerAction+`
          </div>
      </div>
      `);
  }

  if(data.type == 'watching' && plugins['live']['announceVisitor'] == 'Yes'){
    $('#liveChatContainer').append(`
    <div class="stream__message">
    <div class="stream-message is-system stream-message--system">
    <div class="stream-message__media">
    <a class="stream-message__avatar" href="`+site_config.site_url+`@`+data.userId+`?from=livestream" target="_blank">
    <img src="`+data.photo+`" class="stream-message__avatar-image">
    </a>
    </div>
    <div class="stream-message__info">
    <div class="stream-message__content purple-color">`+data.name+` has joined the stream</div>
    </div>
    </div>
    </div>
    `);    
    streamViewers = streamViewers+1;
    $('#streamViewers').text(streamViewers);
  }

  if(data.type == 'leave' && plugins['live']['announceVisitorLeave'] == 'Yes'){
    $('#liveChatContainer').append(`
    <div class="stream__message">
    <div class="stream-message is-system stream-message--system">
    <div class="stream-message__media">
    <a class="stream-message__avatar" href="`+site_config.site_url+`@`+data.userId+`?from=livestream" target="_blank">
    <img src="`+data.photo+`" class="stream-message__avatar-image">
    </a>
    </div>
    <div class="stream-message__info">
    <div class="stream-message__content purple-color">`+data.name+` has left the stream</div>
    </div>
    </div>
    </div>
    `); 
    streamViewers = streamViewers-1;   
    $('#streamViewers').text(streamViewers);
  }

  if(data.type == 'banned'){
    $('#liveChatContainer').append(`
    <div class="stream__message">
    <div class="stream-message is-system stream-message--system">
    <div class="stream-message__media">
    <a class="stream-message__avatar" href="#">
    <img src="`+data.photo+`" class="stream-message__avatar-image">
    </a>
    </div>
    <div class="stream-message__info">
    <div class="stream-message__content purple-color">`+data.name+` was banned from the stream</div>
    </div>
    </div>
    </div>
    `); 
    streamViewers = streamViewers-1;   
    $('#streamViewers').text(streamViewers);
    if(user_info.id == data.bannedId){
      $('#streamBanned').show();
      $('#streamCurrentVideo').remove();
      $('.stream__gifts').remove();
      $('.stream__comment-field').remove();
    }
  }  

  if(data.type == 'status'){
    if(data.status == 'private'){
      if(user_info.id != data.liveId){
        $('#streamPrivateCredits').text(data.price);
        $('[data-private-stream-credits]').text(data.price);
        $('#streamPrivateProfilePhoto').attr('data-src',data.photo);
        profilePhoto();
        if(plugins['live']['showPrivateStreamModal'] == 'Yes'){
          $('#showStreamPrivate').show();
        }
        $('#streamPrivate').show();
        $('#streamLoading').hide();
        $('#streamCurrentVideo').hide();
        $("#streamCurrentVideo").prop('muted', true);
        privateStreamId = data.liveId;
        privateStreamPrice = data.price;
      }
    } else {
      $('#streamPrivate').hide();
      $('#streamLoading').hide();
      $('#streamCurrentVideo').show();
      $("#streamCurrentVideo").prop('muted', false);
      $('#streamInPrivate').hide();      
    }    
  }

  if(data.type == 'end'){
    if(user_info.id == data.liveId){
      openWidget('adminEndedStream');
      setTimeout(function(){
        window.location.reload();
      },5000);
    }
  }  

  if(data.type == 'gift'){
    $('#liveChatContainer').append(`
    <div class="stream__message">
    <div class="stream-message is-system stream-message--system">
    <div class="stream-message__media">
    <a class="stream-message__avatar" href="`+site_config.site_url+`@`+data.userId+`?from=livestream" target="_blank">
    <img src="`+data.photo+`" class="stream-message__avatar-image">
    </a>
    </div>
    <div class="stream-message__info">
    <div class="stream-message__content">
    <img src="`+data.gift+`" class="max-w-120" />
    </div>
    </div>
    </div>
    </div>
    `); 
    streamCredits = streamCredits+parseInt(data.credits);   
    $('#streamCredit').text(streamCredits);
  }  

  $('#liveChatScroll').mCustomScrollbar("scrollTo","bottom",{
    scrollEasing:"easeOut"
  });  
};


window.onbeforeunload = function(e) {
  if(is_streaming){
    endStream('Yes');
  }

  if(viewStreamId != 0 && !is_streaming){
    var leaveData = user_info.id+','+user_info.first_name+','+user_info.profile_photo+','+viewStreamId;   
    $.get( request_source()+'/live.php', {action: 'leave', query: leaveData} );    
  }
};
function liveConnect() {
  if (token && !liveUrl || token && !jwt) {

    updateMillicastAuth()
      .then(d => {
        console.log('auth info:', d);
        liveConnect();
      })
      .catch(e => {
      });
    return;
  }


  let pc = new RTCPeerConnection({iceServers: iceServers, bundlePolicy: "max-bundle"});
  stream.getTracks()
    .forEach(track => {
      pc.addTrack(track, stream);
      $('.stream-container').show();
    });

  let ws    = new WebSocket(liveUrl + '?token=' + jwt);
  ws.onopen = function () {
    rtlive = 'live'+user_info.id;
    channel.unbind();
    channel.bind(rtlive, rtlivecallback);    
    let offer = pc.createOffer({
       offerToReceiveAudio: true,
       offerToReceiveVideo: true
      }).then(desc => {

      $('#endStream').show();
      $('#liveChatContainer').html('');
      is_streaming = true;
      viewStreamId = user_info.id;

      if(plugins['live']['adminCustomMessage'] != ''){
        $('#liveChatContainer').append(`
        <div class="stream__message" >
        <div class="stream-message is-system stream-message--system">
        <div class="stream-message__info">
            <div class="stream-message__content">`+plugins['live']['adminCustomMessage']+`</div>
        </div>        
        </div>
        </div>
        `);    
      }

      if(plugins['live']['streamerCustomMessage'] == 'Yes'){
        var customTxt = $('#customLiveMessage').val();

        if(customTxt != ''){
          $('#liveChatContainer').append(`
          <div class="stream__message" id="streamMessage">
          <div class="stream-message is-system stream-message--system stream-message-style">
          <div class="stream-message__media">
          <a class="stream-message__avatar" href='#'>
          <img src="`+user_info.profile_photo+`" class="stream-message__avatar-image">
          </a>
          </div>
          <div class="stream-message__info">
          <div class="stream-message__content black-color">`+customTxt+`</div>
          </div>
          </div>
          </div>
          `);          
        }
        
      } else {
        var customTxt = '';
      }
      
      $.get( request_source()+'/live.php', {action: 'live', message: customTxt} );


      $('#streamProfilePhoto').attr('src',user_info.profile_photo);
      $('[data-streamProfileLink]').attr('href',site_config.site_url+'@'+user_info.id);
      $('#streamProfileName').text(user_info.first_name+', '+user_info.age);
      $('#streamBgLoader').css('background-image', 'url('+user_info.profile_photo+')');
      $('#streamClose').hide();
      streamViewers = 1;
      $('#streamViewers').text(streamViewers);
      $('[data-show-to-streamer]').show();      
      $('#publicStream').hide();
      loadTime = new Date();
      now = new Date();

      timeElement = document.getElementById('streamTime');
      renderTime();
      liveInterval = window.setInterval(renderTime, 1000);

      //set local description and send offer to media server via ws.
      pc.setLocalDescription(desc)
        .then(() => {
          //set required information for media server.
          let data    = {
            name:  streamName,
            sdp:   desc.sdp,
            codec: 'h264'
          }
          //create payload
          let payload = {
            type:    "cmd",
            transId: Math.random() * 10000,
            name:    'publish',
            data:    data
          }
          ws.send(JSON.stringify(payload));
        })
        .catch(e => {
        })
    }).catch(e => {
    });
  }

  ws.addEventListener('message', evt => {
    let msg = JSON.parse(evt.data);
    switch (msg.type) {
      //Handle counter response coming from the Media Server.
      case "response":
        let data   = msg.data;
        let answer = new RTCSessionDescription({
                                                 type: 'answer',
                                                 sdp:  data.sdp + "a=x-google-flag:conference\r\n"
                                               });

        pc.setRemoteDescription(answer)
          .then(d => {
            showViewURL();
          })
          .catch(e => {
          });
        break;
    }
  })
}


function captureVideoFrame(video, format, quality) {
    if (typeof video === 'string') {
        video = document.getElementById(video);
    }

    format = format || 'jpeg';
    quality = quality || 0.92;

    if (!video || (format !== 'png' && format !== 'jpeg')) {
        return false;
    }

    var canvas = document.createElement("CANVAS");

    canvas.width = video.videoWidth;
    canvas.height = video.videoHeight;

    canvas.getContext('2d').drawImage(video, 0, 0);

    var dataUri = canvas.toDataURL('image/' + format, quality);
    var data = dataUri.split(',')[1];
    var mimeType = dataUri.split(';')[0].slice(5)

    var bytes = window.atob(data);
    var buf = new ArrayBuffer(bytes.length);
    var arr = new Uint8Array(buf);

    for (var i = 0; i < bytes.length; i++) {
        arr[i] = bytes.charCodeAt(i);
    }

    var blob = new Blob([ arr ], { type: mimeType });
    return { blob: blob, dataUri: dataUri, format: format };
}

setTimeout(function(){
  var frame = captureVideoFrame('streamCurrentVideo', 'jpeg');
},10000);


function getICEServers() {
  return new Promise((resolve, reject) => {
    let xhr                = new XMLHttpRequest();
    xhr.onreadystatechange = function (evt) {
      if (xhr.readyState == 4) {
        let res = JSON.parse(xhr.responseText), a;
        switch (xhr.status) {
          case 200:
            //returns array.
            if (res.s !== 'ok') {
              a = [];
              //failed to get ice servers, resolve anyway to connect w/ out.
              resolve(a);
              return
            }
            let list = res.v.iceServers;
            a        = [];
            //call returns old format, this updates URL to URLS in credentials path.
            list.forEach(cred => {
              let v = cred.url;
              if (!!v) {
                cred.urls = v;
                delete cred.url;
              }
              a.push(cred);
            });

            resolve(a);
            break;
          default:
            a = [];
            resolve(a);
            break;
        }
      }
    }
    xhr.open("PUT", turnUrl, true);
    xhr.send();
  })
}

function getMedia() {
  return new Promise((resolve, reject) => {
    let constraints = {
      audio:true,
      video: {
        width:  { min: 640, max: 1920, ideal: 1280 },
        height: { min: 480, max: 1080, ideal: 720 },
        frameRate: { min: 10, max: 60, ideal: 24 },
        advanced: [
          // additional constraints go here, tried in order until something succeeds
          // can attempt high level exact constraints, slowly falling back to lower ones
          { aspectRatio: 16/9 },
          { aspectRatio:  4/3 },
        ]
      }
    } 
    navigator.mediaDevices.getUserMedia(constraints)
      .then(str => {
        resolve(str);
      }).catch(err => {
      console.error('Could not get Media: ', err);
      reject(err);
    })
  });
}

function updateMillicastAuth() {
  return new Promise((resolve, reject) => {
    let xhr                = new XMLHttpRequest();
    xhr.onreadystatechange = function (evt) {
      if (xhr.readyState == 4) {
        let res = JSON.parse(xhr.responseText);
        switch (xhr.status) {
          case 200:
            let d = res.data;
            jwt   = d.jwt;
            liveUrl   = d.urls[0];
            resolve(d);
            break;
          default:
            reject(res);
        }
      }
    }
    xhr.open("POST", apiPath, true);
    xhr.setRequestHeader("Authorization", `Bearer ${token}`);
    xhr.setRequestHeader("Content-Type", "application/json");
    xhr.send(JSON.stringify({streamName: streamName}));
  });
}


function updateMillicastViewerAuth(id) {
  return new Promise((resolve, reject) => {
    let xhr                = new XMLHttpRequest();
    xhr.onreadystatechange = function (evt) {
      if (xhr.readyState == 4) {
        let res = JSON.parse(xhr.responseText);
        switch (xhr.status) {
          case 200:
            let d = res.data;
            jwt   = d.jwt;
            liveUrl   = d.urls[0];
            resolve(d);
            break;
          default:
            reject(res);
        }
      }
    }
    var streamName = 'live'+id;
    xhr.open("POST", viewerPath, true);   
    xhr.setRequestHeader("Content-Type", "application/json");  
    xhr.send(JSON.stringify({streamAccountId: accountId, streamName: 'live'+id, unauthorizedSubscribe: true}));
  });
}

function showViewURL() {
  let btn       = document.getElementById('goLive');
  document.getElementById('goLive').setAttribute("style", "display: none;");
}

//sets required data to broadcast and view.
function setParams() {
  //get millicast id from url if undefined in variable above. otherwise use show a form at runtime.
  let params = new URLSearchParams(document.location.search.substring(1));
  if (!token) {//if we have token, bypass this.
    token = params.get('token');//if no token, try url params.
  }
  if (!streamName) {
    streamName = params.get('streamName');
  }
  if (!accountId) {
    accountId = params.get('accountId');
  }

  if (!token || !streamName || !accountId) {
    document.getElementById('form').setAttribute("style", "display: unset;");
    let i, l = views.length;
    for (i = 0; i < l; i++) {
      let item = views[i];
      let txt  = document.getElementById(item.form);
      switch (item.param) {
        case 'token':
          txt.value = !!token ? token : '';
          break;
        case 'streamName':
          txt.value = !!streamName ? streamName : '';
          break;
        case 'accountId':
          txt.value = !!accountId ? accountId : '';
          break;
      }
    }
  }
  if (token) {// && !!url
    updateMillicastAuth()
      .then(d => {
        console.log('auth data:', d);
      })
      .catch(e => {
      })
  }
}


function goLive() {
  endStreamPreview = 'No';
  $('[data-widget-close]').click();
  setParams();
  startBroadcast();
}

function closeLivePreview(){
  $('[data-widget-close="goingLive"]')[0].click();
  if(endStreamPreview == 'Yes'){
    stream.getTracks().forEach(function(track) {
      track.stop();
    });
  }  
}

function openVerifyAccount(){
  $('[data-widget-close="onlyverifiedStream"]').click();
  setTimeout(function(){
    $('#verify-account').show();
  },350);
}

function openLive() {
  endStreamPreview = 'Yes';
  if(plugins['live']['onlyVerifiedStream'] == 'Yes' && user_info.verified == 0){
    openWidget('onlyVerifiedStream');
    return false;
  }
  getMedia()
    .then(str => {
      stream     = str;
      let vidPrev = document.getElementById('liveVideoPreview');
      if (vidPrev) {
        vidPrev.srcObject = stream;
        vidPrev.muted = true;
      }
      let vidWin = document.getElementById('streamCurrentVideo');
      if (vidWin) {
        vidWin.srcObject = stream;
        vidWin.muted = true;
      }      
      openWidget('goingLive'); 
    })
    .catch(e => {
      openWidget('noWebcam');
    });      
}



function streamAlert(type, title, text,userId) {
  var alert = $("body").append(
    "<div id='coolal' class='box-shadow "+ type +"'><h2 class='coolal-title'>" + 
    title +
    "</h2><p class='coolal-text'>" +
    text +
    "</p></div>"   
  );
  
  var coolal = $("#coolal");
  
  //Check what type of alert is being used 
  if ( type == "alert") {
    $("#coolal").append(      
      "<div id='coolal-btnWrapper'><a id='coolal-btn' class='coolal-alert' href='#'>Ok</a></div>"
    );

    var coolalAlert = $(".coolal-alert");

    coolalAlert.click(function(){
      coolal.animate({
        "opacity":"0",
        "top":"70%" 
      }, 200);

      setTimeout(function() {
        coolal.remove();
      }, 500);
    });
  }
  
  if ( type == "yes-no" ) {
    $("#coolal").append(      
      "<div id='coolal-btnWrapper' class='box-shadow'><a id='coolal-btn' class='coolal-yes-no coolal-no' href='#'>No</a><a id='coolal-btn' class='coolal-yes-no coolal-yes' href='#'>Yes</a></div>"
    );

    var yesNo = $(".coolal-yes-no");
    var yes = $(".coolal-yes");
    var no = $(".coolal-no");

    no.click(function(){
      coolal.animate({
        "opacity":"0",
        "top":"70%"
      }, 200);

      setTimeout(function() {
        coolal.remove();
      }, 500);
    });

    yes.click(function(){
      coolal.animate({
        "opacity":"0",
        "top":"70%"
      }, 200);

      setTimeout(function() {
        coolal.remove();
        $.get( request_source()+'/live.php', {action: 'blockUserLive', userId: userId} );
      }, 500);
    });    
  } 
  
  var titleHeight = $(".coolal-title").innerHeight();
  var textHeight = $(".coolal-text").innerHeight();
  var btnWrapperHeight = $("#coolal-btn").height();
 
  if ( type != "" ) {
    $("#coolal").css("height", titleHeight + textHeight + btnWrapperHeight);
  } else { 
    $("#coolal").css("height", titleHeight + textHeight); 
  }  
   
  $("#coolal").animate({
    "opacity":"1",
    "top":"0" 
  }, 1); 
} 

function copyToClipboard() {
    var aux = document.createElement("input");
    aux.setAttribute("value", site_lang[844]['text']);
    document.body.appendChild(aux);
    aux.select();
    document.execCommand("copy");
    document.body.removeChild(aux);
    alert(site_lang[845]['text']);
}

$(window).keyup(function (e) {
    if (e.keyCode == 44 && url == 'live' && plugins['live']['disablePrintScreen']) {
        copyToClipboard();
    }
});

