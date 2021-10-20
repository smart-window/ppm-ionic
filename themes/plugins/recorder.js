'use strict';
const mediaSource = new MediaSource();
mediaSource.addEventListener('sourceopen', handleSourceOpen, false);
let mediaRecorder;
let recordedBlobs;
let sourceBuffer;

//startRecording();
//stopRecording();
function playRecVideo(){
  const superBuffer = new Blob(recordedBlobs, {type: 'video/webm'});
  recordedVideo.src = null;
  recordedVideo.srcObject = null;
  recordedVideo.src = window.URL.createObjectURL(superBuffer);
  recordedVideo.controls = true;
  recordedVideo.play();
}

function uploadRecVideo(userId,callId,called){
  const blob = new Blob(recordedBlobs, {type: 'video/webm'});
  var fileReader = new FileReader();
  fileReader.onload = function (event) {
    var uri = event.target.result;
    var video = uri;
    $.ajax({
      url: siteUrl+'assets/sources/appupload.php',
      data:{
        action: 'videoRecord',
        base64: video,
        uid: userId,
        callId: callId,
        called: called
      },
      cache: false,
      contentType: "application/x-www-form-urlencoded",         
      type:"post",
      dataType:'JSON',
      success:function(response){
        console.log(response);
      }
    });                 
  };
  fileReader.readAsDataURL(blob);

}

function handleSourceOpen(event) {
  //console.log('MediaSource opened');
  sourceBuffer = mediaSource.addSourceBuffer('video/webm; codecs="vp8"');
}

function handleDataAvailable(event) {
  if (event.data && event.data.size > 0) {
    recordedBlobs.push(event.data);
  }
}

function startRecording() {
  //console.log('rec started');
  recordedBlobs = [];
  let options = {mimeType: 'video/webm;codecs=vp9'};
  if (!MediaRecorder.isTypeSupported(options.mimeType)) {
    console.error(`${options.mimeType} is not Supported`);
    options = {mimeType: 'video/webm;codecs=vp8'};
    if (!MediaRecorder.isTypeSupported(options.mimeType)) {
      console.error(`${options.mimeType} is not Supported`);
      options = {mimeType: 'video/webm'};
      if (!MediaRecorder.isTypeSupported(options.mimeType)) {
        console.error(`${options.mimeType} is not Supported`);
        options = {mimeType: ''};
      }
    }
  }

  try {
    mediaRecorder = new MediaRecorder(window.stream, options);
  } catch (e) {
    console.error('Exception while creating MediaRecorder:', e);
    return;
  }

  mediaRecorder.onstop = (event) => {
    console.log('Recorder stopped: ', event);
  };
  mediaRecorder.ondataavailable = handleDataAvailable;
  mediaRecorder.start(10); // collect 10ms of data
  //console.log('MediaRecorder started', mediaRecorder);
}

function stopRecording(userId,callId,called) {
  mediaRecorder.stop();
  console.log('Rec ended: ', recordedBlobs);
  uploadRecVideo(userId,callId,called);
}







