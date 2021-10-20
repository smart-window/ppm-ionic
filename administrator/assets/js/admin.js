var weekDays = ['monday','tuesday','wednesday','thursday','friday','saturday','sunday'];
var filterMediaByUserId = 0;
var uploadUserMediaId;
var uploadUserMediaType;
var gUrl = request_source()+'/rt.php';
var aUrl = request_source()+'/api.php';
var editingModerator;
var uploadJSONType = '';
var themeType = 'Desktop';
var softwareUpdateInfo = '';
var softwareUpdateSelect = '';
var softwareUpdateVersion = '';
var checkFakeUserMessages;
var lastSevenDaysChartUsers = '';

function mainDashboard(){
	if(aurl == 'main_dashboard'){

      if(c.softwareVersion > sVersion){
          $('[data-main-dashboard]').prepend(`<div class="alert alert-soft-success alert-dismissible d-flex align-items-center card-margin"  style="cursor: pointer;" onclick="goTo('update')" data-update-software-main-dashboard>
              <i class="material-icons mr-3">update</i>
              <div class="text-body">Software update available<br>Update to version `+c.softwareVersion+`<br><strong>Click here for <a href="#">update the software</a></strong></div>
          </div>`);
      }    
	    (function() {
	        'use strict';
	        Charts.init()
	        var mainDashChart = function(id, type = 'line', options = {}) {
	            options = Chart.helpers.merge({
	                elements: {
	                    line: {
	                        fill: 'start',
	                        backgroundColor: settings.charts.colors.area
	                    }
	                }
	            }, options)
	            var data = {
	                labels: ["", "", "", "", "", "", ""],
	                datasets: [{
                      label: "Traffic",
                      data: lastSevenDaysChartUsers,
                  },]
	            }
	            Charts.create(id, type, options, data)
	        }
	        mainDashChart('#mainDashChart');
	    })();
	}
}

function themesPreview(type){
  if(type == 'Desktop' || type == 'Landing'){
    console.log('test');
    const $iframeWrapper = $('#iframe-wrapper');
    const $iframe = $('#iframe');
    scaleIt($iframeWrapper,$iframe);
    window.addEventListener('resize', scaleIt);
  }
}

function scaleIt($iframeWrapper,$iframe) {
  const wrapperWidth = $iframeWrapper.width();
  const iframeWidth = $iframe.width();
  let scale = 1;
  console.log(wrapperWidth);
  if (wrapperWidth <= iframeWidth) {
    scale = 1 - ((iframeWidth - wrapperWidth) / iframeWidth)
  } else {
    scale = 1 + ((wrapperWidth - iframeWidth) / wrapperWidth)
  }
  $iframe.css('transform', `scale(${scale})`);
}

function getUrlVars(){
    var vars = [], hash;
    var hashes = window.location.href.slice(window.location.href.indexOf('?') + 1).split('&');
    for(var i = 0; i < hashes.length; i++)
    {
        hash = hashes[i].split('=');
        vars.push(hash[0]);
        vars[hash[0]] = hash[1];
    }
    return vars;
}

/*
if(aurl == 'themes'){
  var type = getUrlVars()["type"];
  if(type === undefined){
    window.location.href = site_config.site_url+'admin';
  }
  if(x <= 1300){
      //$('[data-toggle="sidebar"]').trigger('click');
      $('[data-theme-row]').removeClass('col-xl-10');
      $('[data-theme-row]').addClass('col-xl-12');
      themesPreview(type);
  }  
  themesPreview(type);
}
*/


function goTo(go,p="",c="",back=false,fromEditProfile=false){
    if(go == 'themesMobile'){
      alert('We are working in the mobile presets, we will release this section soon');
      return false;
    }  
    if(!fromEditProfile){
      $('#admin-content').css("opacity","0.5"); 
    }
    clearInterval(checkFakeUserMessages);
    $('html,body').css('overflow','auto');
    var body = $("html, body");
    body.stop().animate({scrollTop:0}, 500, 'swing', function() { 
    });  
    if(aurl == 'main_dashboard'){
      $('.chart').hide();
    }
    if(go == 'mediaPhotos'){
        if(c != '' && c != 0){
          filterMediaByUserId = c;
        } else {
          filterMediaByUserId = 0;
        }
    }               
    $.ajax({
        url: request_source()+'/admin.php', 
        data: {
            action: 'changePage',
            page: go,
            plugin: p,
            category: c,
            globalKey: globalKey                     
        },  
        type: "post",          
        success: function(response) {
            if(fromEditProfile){
              if(go == 'mediaPhotos'){
                $('#mediaProfileAdmin').html(response);
                $('#admin-content').css("opacity","1"); 
                $('[data-media-filter-col="userid"]').hide();
                $('[data-media-filter-col="uploaded"]').hide();
                $('[data-media-filter-col').removeClass('col-sm-2');
                $('[data-media-filter-col').addClass('col-sm-4');
                $('#searchMediaByIdClose').remove();
                $('#userCol').remove();
                $('#mediaContainer').css('padding-top','0px');
                search['uploaded'] = edit_info.fake;                 
              }
              if(go == 'userVideocalls'){
                $('#userVideocallsAdmin').html(response);
                search['uid'] = edit_info.id;
              }
            } else {
              $('#admin-content').html(response);
              $('#admin-content').css("opacity","1"); 
              window.history.pushState("admin",'',site_config.site_url+'index.php?page=admin&p='+go);
            }
          
            if(go == 'themes' || go == 'themesLanding'){
                themeType = p;
                console.log(themeType);
                if(c == 'myPresets'){
                  //var elmnt = document.getElementById(p);
                  //elmnt.scrollIntoView(true);
                }
                if( x >= 1280 && x <= 1365){
                  window.addEventListener('load', function(){
                    $('[data-toggle="sidebar"]').trigger('click');
                    $('[data-theme-row]').removeClass('col-xl-10');
                    $('[data-theme-row]').addClass('col-xl-12');
                  });
                }
                if (x < 1280){
                  alert('To use the theme editor, access with a laptop or desktop device');
                  goTo('main_dashboard');
                } 
                themesPreview(p);
                checkIframeLoaded();
                presetActions();
                window.history.pushState("admin",'',site_config.site_url+'index.php?page=admin&p='+go+'&type='+p);
            }

            if(go == 'mediaPhotos'){
                if(p == 'Pending'){
                  $('#filter_status').val(0);
                  search['status'] = 0;
                  loadDataMedia();
                } else {
                  $('#filter_category').val(p);
                  search['mediatype'] = p;
                  if(p == 'story'){
                    $('#reUploadStoryBulk').show();
                  } else {
                    $('#reUploadStoryBulk').hide();
                  }                   
                  loadDataMedia();
                }
            }            
            if(go == 'users'){
                manageUsers();
                datePicker();
            }
            if(go == 'main_dashboard'){
                mainDashboard();
            }  

            if(go == 'chats'){
              fullHeightElement('[data-full-height-chat]',150,'hidden');
              fullHeightElement('[data-full-height-chat-right]',250,'hidden');

              $('#search_chat_member').keyup(function(e) {
                filterUserChatList($(this).val());
              });

              $('#search_chat_member').on('search', function(){
                filterUserChatList($(this).val());
              });    
              
              sendChatAdmin();              
            }                                                
        },
    });     
}        

function checkIframeLoaded() {
    var iframe = document.getElementById('iframe');
    var iframeDoc = iframe.contentDocument || iframe.contentWindow.document;
    if (  iframeDoc.readyState  == 'complete' ) {
        $('#iframe').fadeIn('fast');
        return;
    } 
    window.setTimeout(checkIframeLoaded, 100);
}

function bulkActionProgress(text,progress){
  $('#bulkAdminContainer').show();
  var current = $('#bulkAdminProgress').width() / $('#bulkAdminProgress').parent().width() * 100;
  current = parseInt(current);
  current = current+5;
  progress = parseInt(progress); 
  //console.log('Current width:'+current);
  //console.log('Current progress:'+progress);
  if(progress > current){
    $('#bulkAdminPercent').text(progress+'%');
    $('#bulkAdminText').text(text);
    $('#bulkAdminProgress').css('width',progress+'%');    
  }
  if(progress == 100){
    $('#bulkAdminText').text('Done!');
    setTimeout(function(){
      $('#bulkAdminContainer').fadeOut();
      $('#bulkAdminPercent').text('0%');
      $('#bulkAdminProgress').css('width','0%');
    },1000);
  }
}

function adminBulkAction(action,page='',ban=''){
    switch (action){

       case "delete":
          if(page == 'videocall'){
            var m = "Proceed to delete "+checkedData.length+' videocalls';
            swal({title: 'Delete videocalls',text: m,type: "warning",   showCancelButton: true,   confirmButtonColor: "#09c66e",   confirmButtonText: 'Yes', cancelButtonText: 'Cancel',   closeOnConfirm: true },
                function(){
                checkedData.forEach(function(element,index) {
                  var data = {};
                  index = index+1;
                  data['action'] = 'deleteVideocall';
                  data['videocall'] = element;
                  adminDeleteData(data,1,index);
                });
                var currentTotal = $('#videocallsCount').text();
                currentTotal = parseInt(currentTotal);
                $('#videocallsCount').text(currentTotal - checkedData.length);                
                goTo('videocall');                
            }); 
          }

          if(page == 'users'){
            var m = "Proceed to delete "+checkedUsers.length+' users';
            swal({title: 'Delete profiles',text: m,type: "warning",   showCancelButton: true,   confirmButtonColor: "#09c66e",   confirmButtonText: 'Yes', cancelButtonText: 'Cancel',   closeOnConfirm: true },
                function(){
                checkedUsers.forEach(function(element,index) {
                  index = index+1;
                  adminDeleteProfile(element,1,'No,',index);
                });
                goTo('users');                
            }); 
          } 

          if(page == 'reportedUsers'){
            var m = "Proceed to delete "+checkedData.length+' users';
            swal({title: 'Delete profiles',text: m,type: "warning",   showCancelButton: true,   confirmButtonColor: "#09c66e",   confirmButtonText: 'Yes', cancelButtonText: 'Cancel',   closeOnConfirm: true },
                function(){
                checkedData.forEach(function(element,index) {
                  index = index+1;
                  var e = element.toString();
                  var elementArr = e.split('22900');
                  adminDeleteProfile(elementArr[1],1,'No,',index);
                });
                goTo('reportedUsers');                
            }); 
          } 

          if(page == 'media'){
            var m = "Proceed to delete "+checkedData.length+' media objects';
            swal({title: 'Delete media',text: m,type: "warning",   showCancelButton: true,   confirmButtonColor: "#09c66e",   confirmButtonText: 'Proceed', cancelButtonText: 'Cancel',   closeOnConfirm: true },
                function(){
                checkedData.forEach(function(element,index) {
                  var data = {};
                  index = index+1;
                  data['action'] = 'deleteMedia';
                  data['mediaId'] = element;
                  data['mediaType'] = $('[data-media-id='+element+']').attr('data-media-type');
                  data['mediaIdStory'] = $('[data-media-id='+element+']').attr('data-media-id-story');;
                  adminDeleteData(data,1,index);
                });
                var currentTotal = $('#totalMediaAll').text();
                currentTotal = parseInt(currentTotal);
                $('#totalMediaAll').text(currentTotal - checkedData.length); 
                setTimeout(function(){
                  loadDataMedia();                  
                },350);               
                
            }); 
          }

       break;

       case "approve":
          if(page == 'media'){
            var m = "Proceed to approve "+checkedData.length+' media objects';
            swal({title: 'Approve media',text: m,type: "warning",   showCancelButton: true,   confirmButtonColor: "#09c66e",   confirmButtonText: 'Proceed', cancelButtonText: 'Cancel',   closeOnConfirm: true },
                function(){
                checkedData.forEach(function(element,index) {
                  var data = {};
                  index = index+1;
                  data['action'] = 'updateMedia';
                  data['method'] = 'approveMedia';
                  data['val'] = 1;
                  data['mediaId'] = element;
                  data['mediaType'] = $('[data-media-id='+element+']').attr('data-media-type');
                  data['mediaIdStory'] = $('[data-media-id='+element+']').attr('data-media-id-story');;
                  adminUpdateData(data,1,'','','',index);
                });               
                loadDataMedia();                
            }); 
          }       
       break;

       case 'reUploadStory':
          if(page == 'media'){
            var m = "Proceed to re-upload "+checkedData.length+' stories';
            swal({title: 'Re-upload stories',text: m,type: "warning",   showCancelButton: true,   confirmButtonColor: "#09c66e",   confirmButtonText: 'Proceed', cancelButtonText: 'Cancel',   closeOnConfirm: true },
                function(){
                checkedData.forEach(function(element,index) {
                  var data = {};
                  index = index+1;
                  data['action'] = 'updateMedia';
                  data['method'] = 'reUploadStory';
                  data['mediaId'] = element;
                  data['mediaType'] = $('[data-media-id='+element+']').attr('data-media-type');
                  data['mediaIdStory'] = $('[data-media-id='+element+']').attr('data-media-id-story');;
                  adminUpdateData(data,1,'','','',index);
                });                
                loadDataMedia();                
            }); 
          } 
       break;

       case "onlineDay":
          var m = "Proceed to update online day of "+checkedUsers.length+' users';
          $('[data-btn="onlineDay"]').addClass('is-loading');
          swal({title: 'Update online day',text: m,type: "info",   showCancelButton: true,   confirmButtonColor: "#09c66e",   confirmButtonText: 'Yes', cancelButtonText: 'Cancel',   closeOnConfirm: true },
              function(){
              checkedUsers.forEach(function(element,index) {
                var data = {};
                weekDays.forEach(function(day) {
                  var checked = $('[data-online-day="'+day+'"]:checked').val();
                  if(checked != 'on'){
                      data[day] = 0;
                  } else {
                      data[day] = 1;
                  }                     
                });
                data['action'] = 'updateOnlineDay';
                data['uid'] = element;
                index = index+1;
                adminUpdateData(data,1,'','','',index);
              });

              var data = {};
              data['action'] = 'updateOnlineDayCron';
              adminUpdateData(data,1);              
              goTo('users');  
              $('[data-btn="onlineDay"]').removeClass('is-loading');
              $('#modal-update-onlineDay').modal('toggle');              
          });                  
       break;

       case "ban":

          if(page == 'reportedUsers'){
            var m = "Proceed to Delete and BAN "+checkedData.length+' users?';
            swal({title: 'Delete and BAN',text: m,type: "warning",   showCancelButton: true,   confirmButtonColor: "#09c66e",   confirmButtonText: 'Yes', cancelButtonText: 'Cancel',   closeOnConfirm: true },
                function(){
                checkedData.forEach(function(element,index) {
                  index = index+1;
                  var e = element.toString();
                  var elementArr = e.split('22900');
                  adminDeleteProfile(elementArr[1],1,ban,index);
                });
                goTo('reportedUsers');                                
            }); 
          }  

          if(page == 'users'){
            var m = "Proceed to Delete and BAN "+checkedUsers.length+' users?';
            swal({title: 'Delete profiles',text: m,type: "warning",   showCancelButton: true,   confirmButtonColor: "#09c66e",   confirmButtonText: 'Yes', cancelButtonText: 'Cancel',   closeOnConfirm: true },
                function(){
                checkedUsers.forEach(function(element,index) {
                  index = index+1;
                  adminDeleteProfile(element,1,ban,index);
                });
                var currentTotal = $('#bannedUsersCount').text();
                currentTotal = parseInt(currentTotal);
                $('#bannedUsersCount').text(currentTotal + checkedUsers.length);                
                goTo('users');                
            }); 
          }                  
       break;

       case "unban": 
          if(page == 'bannedUsers'){
            var m = "Proceed to Unban emails of "+checkedData.length+' users';
            swal({title: 'Unban Users',text: m,type: "warning",   showCancelButton: true,   confirmButtonColor: "#09c66e",   confirmButtonText: 'Yes, Unban', cancelButtonText: 'Cancel',   closeOnConfirm: true },
                function(){
                checkedData.forEach(function(element,index) {
                  var data = {};
                  data['action'] = 'unbanEmail';
                  data['email'] = element;
                  index = index+1;
                  adminUpdateData(data,1,'','','',index); 
                });
                var currentTotal = $('#bannedUsersCount').text();
                currentTotal = parseInt(currentTotal);
                $('#bannedUsersCount').text(currentTotal - checkedData.length);
                goTo('bannedUsers');                
            }); 
          }                  
       break; 

       case "unbanIP": 
          if(page == 'bannedIP'){
            var m = "Proceed to Unban "+checkedData.length+' IP';
            swal({title: 'Unban IP',text: m,type: "warning",   showCancelButton: true,   confirmButtonColor: "#09c66e",   confirmButtonText: 'Yes, Unban', cancelButtonText: 'Cancel',   closeOnConfirm: true },
                function(){
                checkedData.forEach(function(element,index) {
                  var data = {};
                  data['action'] = 'unbanIP';
                  data['ip'] = element;
                  index = index+1;
                  adminUpdateData(data,1,'','','',index); 
                });
                var currentTotal = $('#bannedIPCount').text();
                currentTotal = parseInt(currentTotal);
                $('#bannedIPCount').text(currentTotal - checkedData.length);
                goTo('bannedIP');                
            }); 
          }                  
       break;

       case "approveVerification": 
          if(page == 'verifyUsers'){
            var m = "Proceed to APPROVE verification of "+checkedData.length+' users';
            swal({title: 'Approve Verification',text: m,type: "warning",   showCancelButton: true,   confirmButtonColor: "#09c66e",   confirmButtonText: 'Yes, Approve', cancelButtonText: 'Cancel',   closeOnConfirm: true },
                function(){
                checkedData.forEach(function(element,index) {
                  var data = {};
                  data['action'] = 'approveUserVerification';
                  data['uid'] = element;
                  data['approve'] = 1;
                  index = index+1;
                  adminUpdateData(data,1,'','','',index); 
                });
                var currentTotal = $('#pendingUsersVerificationLabel').text();
                currentTotal = parseInt(currentTotal);
                $('#pendingUsersVerificationLabel').text(currentTotal - checkedData.length); 
                goTo('verifyUsers');                
            }); 
          }                  
       break; 

       case "deniVerification": 
          if(page == 'verifyUsers'){
            var m = "Proceed to DENY verification of "+checkedData.length+' users';
            swal({title: 'Deny Verification',text: m,type: "warning",   showCancelButton: true,   confirmButtonColor: "#09c66e",   confirmButtonText: 'Yes, Unapprove', cancelButtonText: 'Cancel',   closeOnConfirm: true },
                function(){
                checkedData.forEach(function(element,index) {
                  var data = {};
                  data['action'] = 'approveUserVerification';
                  data['uid'] = element;
                  data['approve'] = 0;
                  index = index+1;
                  adminUpdateData(data,1,'','','',index); 
                });
                var currentTotal = $('#pendingUsersVerificationLabel').text();
                currentTotal = parseInt(currentTotal);
                $('#pendingUsersVerificationLabel').text(currentTotal - checkedData.length); 
                goTo('verifyUsers');                
            }); 
          }                  
       break; 

       case "removeFromReportList": 
          if(page == 'reportedUsers'){
            var m = "Proceed to remove from report "+checkedData.length+' users';
            swal({title: 'Remvoe from report list',text: m,type: "warning",   showCancelButton: true,   confirmButtonColor: "#09c66e",   confirmButtonText: 'Yes', cancelButtonText: 'Cancel',   closeOnConfirm: true },
                function(){
                checkedData.forEach(function(element,index) {
                  var e = element.toString();
                  var elementArr = e.split('22900');                  
                  var data = {};
                  data['action'] = 'removeFromReportList';
                  data['uid'] = elementArr[1];
                  index = index+1;
                  adminUpdateData(data,1,'','','',index); 
                });
                var currentTotal = $('#reportedUsersCount').text();
                currentTotal = parseInt(currentTotal);
                $('#reportedUsersCount').text(currentTotal - checkedData.length); 
                goTo('reportedUsers');                
            }); 
          }                  
       break;                                   
      
       default: 
           console.log('admin bulk function');
    }
}

function adminUpdateData(data,bulk=0,php=0,customVal='',val='',index=0){

    if(bulk == 1 && php == 0){
        $.ajax({ 
            data: data,
            url:   request_source()+'/admin.php',
            type:  'post',
            success: function(response){
              if(data['action'] == 'updateOnlineDay'){
                var text = 'Update Online Day - '+data['uid'];
                var outOff = checkedUsers.length;
                var progress = (index * 100) / outOff;
                progress = parseInt(progress);
                progress = progress.toFixed(0);
                console.log('Progress:'+progress);
                if(progress >= 97){
                  progress = 100;
                }     
                bulkActionProgress(text,progress);                   
              }

              if(data['action'] == 'updateMedia'){
                if(data['method'] == 'approveMedia'){
                  var text = 'Approve Media - '+ index;
                  var outOff = checkedData.length;
                  var progress = (index * 100) / outOff;
                  progress = parseInt(progress);
                  progress = progress.toFixed(0);
                  if(progress >= 97){
                    progress = 100;
                  }     
                  bulkActionProgress(text,progress);                     
                }
                if(data['method'] == 'reUploadStory'){
                  var text = 'Re-uploading story - '+ index;
                  var outOff = checkedData.length;
                  var progress = (index * 100) / outOff;
                  progress = parseInt(progress);
                  progress = progress.toFixed(0);
                  if(progress >= 97){
                    progress = 100;
                  }     
                  bulkActionProgress(text,progress);                     
                }                                                
              }              

              if(data['action'] == 'unbanEmail'){
                var text = 'Unban email - '+index;
                var outOff = checkedData.length;
                var progress = (index * 100) / outOff;
                progress = progress.toFixed(0);
                if(progress >= 97){
                  progress = 100;
                }     
                bulkActionProgress(text,progress);                   
              }

              if(data['action'] == 'unbanIP'){
                var text = 'Unban IP - '+index;
                var outOff = checkedData.length;
                var progress = (index * 100) / outOff;
                progress = parseInt(progress);
                progress = progress.toFixed(0);
                if(progress >= 97){
                  progress = 100;
                }     
                bulkActionProgress(text,progress);                   
              }              


            }
        }); 
    }

    if(bulk == 0 && php == 0){ 

        if(data['method'] == 'changeCreditPrice'){
          data['val'] = $('#storyPriceSelect'+data['mediaId']).val();
          console.log($('#storyPriceSelect'+data['mediaId']).val());
        }        
        $.ajax({ 
            data: data,
            url:   request_source()+'/admin.php',
            type:  'post',
            success: function(response){   
              if(data['method'] == 'mediaSetPublic'){
                $('[data-media-public='+data['mediaId']+']').text('Public');
                $('[data-media-public='+data['mediaId']+']').removeClass('badge-light');
                $('[data-media-public='+data['mediaId']+']').removeClass('badge-dark');
                $('[data-media-public='+data['mediaId']+']').addClass('badge-light');
                $('[data-media-dropdown-private='+data['mediaId']+']').show();
                $('[data-media-dropdown-public='+data['mediaId']+']').hide();
              }
              if(data['method'] == 'mediaSetPrivate'){
                $('[data-media-public='+data['mediaId']+']').text('Private');
                $('[data-media-public='+data['mediaId']+']').removeClass('badge-light');
                $('[data-media-public='+data['mediaId']+']').removeClass('badge-dark');
                $('[data-media-public='+data['mediaId']+']').addClass('badge-dark');                
                $('[data-media-dropdown-private='+data['mediaId']+']').hide();
                $('[data-media-dropdown-public='+data['mediaId']+']').show();               
              } 

              if(data['method'] == 'approveMedia'){
                $('#approveMedia'+data['mediaId']).html(data['html']);
                if(data['val'] == 1){
                  $('[data-media-dropdown-approve='+data['mediaId']+']').hide();
                  $('[data-media-dropdown-pending='+data['mediaId']+']').show();
                } else {
                 $('[data-media-dropdown-approve='+data['mediaId']+']').show();
                 $('[data-media-dropdown-pending='+data['mediaId']+']').hide();
                }
              }

              if(data['method'] == 'reUploadStory'){
                $('#approveMedia'+data['mediaId']).html(data['html']);
                $('[data-media-dropdown-reupload-story='+data['mediaId']+']').hide();
              } 

              if(data['method'] == 'setAsProfilePhoto'){
                $('[data-media-profile-photo='+data['mediaUid']+']').attr('src',data['mediaThumb']);
              }                

              if(data['method'] == 'changeCreditPrice'){
                if(data['val'] == 0){
                  var dataVal = 'FREE';
                } else {
                  var dataVal = data['val']+' Credits';
                }
                $('[data-media-public='+data['mediaId']+']').text(dataVal);
              } 


              if(data['method'] == 'uploadToStory' || data['method'] == 'uploadToProfile'){
                setTimeout(function(){
                  loadDataMedia();
                  $("html, body").stop().animate({scrollTop:0}, 500, 'swing', function() { 
                  });                  
                },350);
              }             
            }
        });      
    }

    if(php == 1){

      var ajaxData = {};
      if(customVal == 'removeFromReportList'){
        ajaxData['action'] = customVal;
        ajaxData['uid'] = data;
        var currentTotal = $('#reportedUsersCount').text();
        currentTotal = parseInt(currentTotal);
        $('#reportedUsersCount').text(currentTotal - 1);                     
      }  

      if(customVal == 'withdrawComplete' || customVal == 'withdrawCanceled'){
        ajaxData['action'] = customVal;
        ajaxData['id'] = data;                    
      }        

      if(customVal == 'approveUserVerification'){
          ajaxData['action'] = 'approveUserVerification';
          ajaxData['uid'] = data;
          ajaxData['approve'] = 1;
          var currentTotal = $('#pendingUsersVerificationLabel').text();
          currentTotal = parseInt(currentTotal);
          $('#pendingUsersVerificationLabel').text(currentTotal - 1);                     
      } 

      if(customVal == 'noapproveUserVerification'){
          ajaxData['action'] = 'approveUserVerification';
          ajaxData['uid'] = data;
          ajaxData['approve'] = 0;
          var currentTotal = $('#pendingUsersVerificationLabel').text();
          currentTotal = parseInt(currentTotal);
          $('#pendingUsersVerificationLabel').text(currentTotal - 1);                       
      }        

      if(customVal == 'unbanUser'){
          ajaxData['action'] = 'unbanEmail';
          ajaxData['email'] = data;
          var currentTotal = $('#bannedUsersCount').text();
          currentTotal = parseInt(currentTotal);
          $('#bannedUsersCount').text(currentTotal - 1);          
      }

      if(customVal == 'unbanIP'){
          ajaxData['action'] = 'unbanIP';
          ajaxData['ip'] = data;
          var currentTotal = $('#bannedIPCount').text();
          currentTotal = parseInt(currentTotal);
          $('#bannedIPCount').text(currentTotal - 1);          
      }      

      $.ajax({ 
          data: ajaxData,
          url:   request_source()+'/admin.php',
          type:  'post',
          success: function(response){
            if(customVal == 'removeFromReportList'){
              goTo('reportedUsers');
            }
            if(customVal == 'unbanUser'){
              goTo('bannedUsers');
            } 
            if(customVal == 'unbanIP'){
              goTo('bannedIP');
            } 
            if(customVal == 'withdrawComplete' || customVal == 'withdrawCanceled'){
              goTo('withdrawal');
            }                       
            if(customVal == 'approveUserVerification' || customVal == 'noapproveUserVerification'){
              goTo('verifyUsers');
              setTimeout(function(){
                var pv = $(".data-search-verifications").length;
                $('#pendingUsersVerificationLabel').text(pv);
              },350)
            }              
          }
      });  

    }             
}

function adminDeleteProfile(uid,bulk=0,ban='No,',index=0){
    if(bulk == 1){     
        $.ajax({ 
            data: {
                action: 'delete_profile',
                uid : uid,
                ban : ban,
                globalKey: globalKey
            },
            url:   request_source()+'/admin.php',
            type:  'post',
            beforeSend: function(){ 
            },
            success: function(response){

              var text = 'Removing '+uid;
              var outOff = checkedUsers.length;
              if(outOff == 0){
                outOff = checkedData.length;
              }
              var progress = (index * 100) / outOff;
              progress = progress.toFixed(0);
              if(progress >= 97){
                progress = 100;
              }                  
              bulkActionProgress(text,progress);    

            }
        }); 
    } else {
        swal({
            title: 'Account termination',
            text: 'The data will be lost without recovery, continue?',
            confirmButtonText: "Yes, delete it!",               
            type: "warning",
            showCancelButton: true,             
            },
            function(){
            $.ajax({ 
                data: {
                    action: 'delete_profile',
                    uid : uid,
                    ban : ban,
                    globalKey: globalKey
                },
                url:   request_source()+'/admin.php',
                type:  'post',
                beforeSend: function(){ 
                },
                success: function(response){
                    goTo('users');
                }
            }); 
        }); 
    }            
}

function adminDeleteData(data,bulk=0,index=0){
    if(bulk == 1){
        $.ajax({ 
            data: data,
            url:   request_source()+'/admin.php',
            type:  'post',
            success: function(response){ 

              var text = 'Bulk '+index;

              if(data['action'] == 'deleteVideocall'){
                text = 'Deleting '+index;
              }  

              if(data['action'] == 'deleteMedia'){
                text = 'Deleting '+index;
              } 

              var outOff = checkedUsers.length;
              if(outOff == 0){
                outOff = checkedData.length;
              }
              var progress = (index * 100) / outOff;
              progress = progress.toFixed(0);
              if(progress >= 97){
                progress = 100;
              }    

              bulkActionProgress(text,progress);

            }
        }); 
    } else {

        if(data['action'] == 'deleteChatMessage'){
            $.ajax({ 
                data: data,
                url:   request_source()+'/admin.php',
                type:  'post',
                success: function(response){
                  if(data['action'] == 'deleteChatMessage'){
                    $('[data-chat-id='+data['id']+']').remove();
                  }
                }
            }); 
        } else {
          swal({
              title: 'Data delete',
              text: 'The data will be lost without recovery, continue?',
              confirmButtonText: "Yes, delete it!",               
              type: "warning",
              showCancelButton: true,             
              },
              function(){
              $.ajax({ 
                  data: data,
                  url:   request_source()+'/admin.php',
                  type:  'post',
                  success: function(response){
                    if(data['action'] == 'deleteVideocall'){
                      var currentTotal = $('#videocallsCount').text();
                      currentTotal = parseInt(currentTotal);
                      $('#videocallsCount').text(currentTotal - 1);
                      goTo('videocall');
                    }
                    if(data['action'] == 'deleteLive'){
                      var currentTotal = $('#livesCount').text();
                      currentTotal = parseInt(currentTotal);
                      $('#livesCount').text(currentTotal - 1);
                      goTo('live');
                    }                    
                  },
                  complete: function(response){
                    if(data['action'] == 'deleteMedia'){
                      loadDataMedia();
                    }                  
                  }
              }); 
          });
        } 
    }            
}




var checkedData = [];
function checkAllData(element){

    var check = element.checked;

    //$('[data-checkedUsers-photos]').html('');
    //$('[data-more-users]').remove();
    $('[data-check-search]').each(function(){
        $(this).prop("checked",check);
        var id = $(this).attr('data-check-search');
        id = parseInt(id);
        if(check){
            checkedData = checkedData.filter(function(item) { 
                return item !== id
            });         
            checkedData.push(id);
            if(checkedData.length <= 15){
                //$('[data-checked]').append('<div class="avatar avatar-xs" data-avatar="'+uid+'"><img src="'+photo+'" class="avatar-img rounded-circle"></div>');
            }
        } else {
            checkedData = checkedData.filter(function(item) { 
                return item !== id
            });        
        }
    });

    console.log(checkedData);
    if(checkedData.length == 0){
        $('[data-selected-data]').hide();
    } else {
        $('[data-selected-data-total]').text(checkedData.length);
        $('[data-selected-data]').fadeIn();
    }

}

function getData(table,col,filter='',handleData){
    $.ajax({ 
      data: {
          action: 'getData',
          table : table,
          col: col,
          filter : filter,
          globalKey: globalKey
      },
      url:   request_source()+'/admin.php',
      type:  'post',
      beforeSend: function(){ 
      },
      success: function(response){
        handleData(response)
      }
  }); 
}

function clearUserMediaFilter(){
  $('#filter_id').val('');
  search['search'] = '';
  loadDataMedia();
}

function checkIfChecked(){
    checkedData.forEach(function(item){
      $('[data-check-search='+item+']').prop("checked",true);
    });
    checkedUsers.forEach(function(item){
      $('[data-check-user='+item+']').prop("checked",true);
    });    
}

function checkData(element,id){

    var check = element.checked;
    console.log(check);
    //$('[data-more-data]').remove();
    if(check){
        checkedData.push(id);
    } else {    
        checkedData = checkedData.filter(function(item) { 
            return item !== id
        });  
    }

    if(checkedData.length == 0){
        $('[data-selected-data]').hide();
    } else {
        $('[data-selected-data-total]').text(checkedData.length);
        $('[data-selected-data]').fadeIn();
    }
    console.log(checkedData.length);
}        

$('.divHidden').css('display','none');

$('[data-current-menu='+currentMenu+']').addClass('active');
$('[data-current-menu-collapse='+currentMenuCollapse+']').addClass('open');
$('[data-current-menu-collapse='+currentMenuCollapse+']').addClass('active');
$('[data-current-menu-collapse-ul='+currentMenuCollapse+']').addClass('show');

var tdiv = document.getElementById('loadThemes');

//CHECK VALID LICENSE
 
if(c.premium == 0){
    $('[data-suscribePremium]').fadeIn();
}
if(c.active == 1){
    $('[data-licenseStatus]').html(`
        <span class="ml-auto d-flex align-items-center">
            <span class="badge badge-success">Active</span>
        </span> `);                    
}


if(aurl == 'users'){
    manageUsers();
}
if(aurl == 'main_dashboard'){
    mainDashboard();
    setTimeout(function(){
      $('#usersChart').click();
    },1000)
}       



function presetActions(){
  $('[data-edit-preset]').click(function(e){
      var action = $(this).attr('data-edit-preset-action');
      var preset = $(this).attr('data-edit-preset');
      var alias = $(this).attr('data-edit-preset-alias');
      if(action == 'rename'){
          swal({
            title: "Rename",
            text: "Set the new name:",
            type: "input",
            showCancelButton: true,
            closeOnConfirm: false,
            inputPlaceholder: ""
          }, function (inputValue) {
            if (inputValue === false) return false;
            if (inputValue === "") {
              swal.showInputError("You need to write something!");
              return false
            }
            editPreset(preset,action,inputValue);
            $('[data-preset-alias="'+preset+'"]').text(inputValue);
            $('[data-preset-last-update="'+preset+'"]').text('1 second ago');
            swal("Complete", "Preset name updated", "success");
          });
      }
      if(action == 'duplicate'){
          editPreset(preset,action,alias,1);
      }
      if(action == 'delete'){
          swal({
            title: "Are you sure?",
            text: "Your will not be able to recover this preset design",
            type: "warning",
            showCancelButton: true,
            confirmButtonClass: "btn-danger",
            confirmButtonText: "Yes, delete it!",
            closeOnConfirm: true
          },
          function(){
            editPreset(preset,action);
            setTimeout(function(){
              $('[data-preset-row="'+preset+'"').fadeOut('slow');
            },600);
          
          });                
      }
  }) 



  $('[data-export-preset-json]').click(function(){
      var preset = $(this).attr('data-export-preset-json');
      var name = $(this).attr('data-export-preset-json-name');
      $.ajax({ 
          data: {
              action: 'exportJSON',
              preset : preset,
              name : name,
              globalKey: globalKey
          },
          url:   request_source()+'/admin.php',
          type:  'post',
          dataType: 'JSON',
          beforeSend: function(){ 
          },
          success: function(response){
              //goTo('users');
              downloadJSON(response.url,response.name);
              //console.log(response);
          }
      });
  });
}


function editPreset(preset,action,val=0,reload=0){

$.ajax({ 
    data: {
        action: 'editCurrentPreset',
        preset : preset,
        val: val,
        editAction : action,
        globalKey: globalKey
    },
    url:   request_source()+'/admin.php',
    type:  'post',
    dataType: 'JSON',
    beforeSend: function(){ 
    },
    success: function(response){
        if(response.reload == 'Desktop'){
            goTo('themes','Desktop','myPresets');
        }
        if(response.reload == 'Landing'){
            goTo('themesLanding','Landing','myPresets');
        }        
    }
});        
}
function downloadJSON(url, name){
var link = document.createElement("a");
link.download = name;
link.href = url;
link.click();
}

function createPresetModal(e,name,theme,image,selected){
  e.preventDefault();
  var presetName = name;
  var presetScreenshot = image;
  var presetData = '';

  $('#newPresetName').text(presetName);
  $('#newPresetData').val(presetData);
  $('#newPresetTheme').val(theme);
  var rand = Math.floor(Math.random() * 100000) + 1;
  $('#newPreset').val(theme+'-'+rand);
  $('#newPresetAlias').val(presetName+' new preset '+rand);
  $('#newPresetScreenshot').attr('src',presetScreenshot);
  $("#modalPresets").modal();
}

$('#newPresetForm').submit(function(e){
  e.preventDefault();
  var id = $('#newPreset').val();
  var alias = $('#newPresetAlias').val();
  var data = $('#newPresetData').val();
  var theme = $('#newPresetTheme').val();
  var base = $('#newPresetName').text();
  $.ajax({ 
      data: {
          action: 'addPreset',
          base : base,
          preset : id,
          alias: alias,
          theme : theme,
          data : data,
          globalKey: globalKey
      },
      url:   request_source()+'/admin.php',
      type:  'post',
      success: function(response){   
        
        if(response == 1){ //landing
          goTo('themesLanding','Landing');  
        } else {
          goTo('themes','Desktop');
        }
        
        setTimeout(function(){
          window.open(site_config.site_url+'administrator/editor/theme/'+theme+'/'+id, '_blank');
          $('#modalPresets').modal('hide');
        },1500)
        
      }
  });        
})


function openWidget(widget,el=''){
  var wt = document.querySelector('.lw-widget[data-widget="'+widget+'"]');
  showWidget(wt);
  if(widget == 'manageGift'){
    var edit = $(el).attr('data-edit-gift');
    if(edit == 1){
      var gid = $(el).attr('data-gift-id'); 
      var giftName = $(el).attr('data-gift-name');
      var giftPrice = $(el).attr('data-gift-price');
      var giftIcon = $(el).attr('data-gift-icon');
      $('#giftModalTitle').text('Edit: '+giftName);
      $('[data-gift-uploaded]').attr('data-src',giftIcon);
      $('#giftName').val(giftName);
      $('#giftPrice').val(giftPrice);
      $('#giftIcon').val(giftIcon);
      $('#giftId').val(gid);
      profilePhoto();
      $('#removeGift').show();
    } else {
      $('#giftModalTitle').text('Add new gift');
      $('#giftId').val(0);
      $('#giftName').val('');
      $('#giftPrice').val('');
      $('#giftIcon').val('');
      $('[data-gift-uploaded]').css('background-image','url()');
      $('#removeGift').hide();      
    }
  }
  if(widget == 'manageInterest'){
    var edit = $(el).attr('data-edit-interest');
    if(edit == 1){
      var gid = $(el).attr('data-interest-id'); 
      var giftName = $(el).attr('data-interest-name');
      var giftIcon = $(el).attr('data-interest-icon');
      $('#interestModalTitle').text('Edit: '+giftName);
      $('[data-gift-uploaded]').attr('data-src',giftIcon);
      $('#interestName').val(giftName);
      $('#interestIcon').val(giftIcon);
      $('#interestId').val(gid);
      profilePhoto();
      $('#removeInterest').show();
    } else {
      $('#interestModalTitle').text('Add new interest');
      $('#interestId').val(0);
      $('#interestName').val('');
      $('#interestPrice').val('');
      $('#interestIcon').val('');
      $('#removeInterest').hide();
      $('[data-gift-uploaded]').css('background-image','url()');      
    }
  }  
}

function manageGift(action){
  $.ajax({ 
      data: {
        action: action,
        name: $('#giftName').val(),
        id: $('#giftId').val(),
        price: $('#giftPrice').val(),
        icon: $('#giftIcon').val()
      },
      url:   request_source()+'/admin.php',
      type:  'post',
      dataType: 'JSON',
      success: function(response){
        $('#giftsContainer').html(response.gifts);
        document.getElementById("closeManageGift").click();
      }
  });   
}

function addLanguage(action,el){

  
  var check = $(el).hasClass( "is-loading" );
  if(check === true){  
    console.log('adding language');
    return false;
  }
  
  if($('#langName').val() == ''){
    return false;
  }
  if($('#langPrefix').val() == ''){
    return false;
  }  
  $(el).addClass('is-loading');
  $.ajax({ 
      data: {
        action: action,
        name: $('#langName').val(),
        prefix: $('#langPrefix').val()
      },
      url:   request_source()+'/admin.php',
      type:  'post',
      dataType: 'JSON',
      success: function(response){
        goTo('languages');
        setTimeout(function(){
          $('#langName').val('');
          $('#langPrefix').val('en');
        },500);
        document.getElementById("closeAddLanguage").click();
        $(el).removeClass('is-loading')
      }
  });   
}

function deleteLanguage(id){
  var m = "Proceed to delete language";
  swal({title: 'Delete language',text: m,type: "warning",showCancelButton: true,confirmButtonColor: "#09c66e",   confirmButtonText: 'Yes,Delete', cancelButtonText: 'Cancel',   closeOnConfirm: true },
      function(){
      $.ajax({ 
          data: {
            action: 'deleteLanguage',
            id: id
          },
          url:   request_source()+'/admin.php',
          type:  'post',
          dataType: 'JSON',
          success: function(response){
            goTo('languages');
          }
      });               
  });     
}

function removeFromSpotlight(id){
  var m = "Proceed to remove this user from the spotlight";
  swal({title: 'Remove from Spotlight',text: m,type: "warning",showCancelButton: true,confirmButtonColor: "#09c66e",   confirmButtonText: 'Yes,Delete', cancelButtonText: 'Cancel',   closeOnConfirm: true },
      function(){
      $('[data-user-spotlight='+id+']').remove();
      $.ajax({ 
          data: {
            action: 'removeFromSpotlight',
            time: id
          },
          url:   request_source()+'/admin.php',
          type:  'post',
          dataType: 'JSON',
          success: function(response){
            
          }
      });               
  });     
}

function manageInterest(action){
  $.ajax({ 
      data: {
        action: action,
        name: $('#interestName').val(),
        id: $('#interestId').val(),
        icon: $('#interestIcon').val()
      },
      url:   request_source()+'/admin.php',
      type:  'post',
      dataType: 'JSON',
      success: function(response){
        $('#interestsContainer').html(response.interest);
        document.getElementById("closeManageInterest").click();
      }
  });   
}

function uploadImage(el){
  upType = $(el).attr('data-uptype');
  document.getElementById("uploadContent").click();
  if(upType == 'gesture' || upType == 'watermark'){
    $(el).addClass('is-loading');
  }  
}


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


function profilePhoto(){
  $(".profile-photo").each(function(){
    var src = $(this).attr("data-src");
    $(this).css('background-image', 'url('+src+')');      
  });
 
}
profilePhoto();

function sendUpdateProfile(){
  setTimeout(function(){
    $('#update-profile').submit();
  },2000);
}

function profileForms(){

  $('[data-update-profile-info]').change(function(){
    $('#update-profile').submit();
  });

  $('#update-profile').submit(function(e) {
    e.preventDefault(); 
    var findme = "Error";
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
          $('#new_password').val('');
          $('.lw-close').click();
        }         
      }
    }); 
  });      
}

profileForms();



function fullHeightElement(card,val=0,overflow=""){
  w = window,
    d = document,
    e = d.documentElement,
    g = d.getElementsByTagName('body')[0],
    x = w.innerWidth || e.clientWidth || g.clientWidth,
    h = w.innerHeight|| e.clientHeight|| g.clientHeight;  
  var setH = h-val;

  $(card).css('height',setH+'px');



}

function filterCreditsHistory(el){
  var filter = $(el).attr('data-credits-filter');
  $('[data-credits-filter]').removeClass('bg-white');
  $(el).addClass('bg-white');
  if(filter == 'all'){
    $('[data-credits-filter-val]').removeClass('d-flex').addClass('d-flex');;
  } else {
    $('[data-credits-filter-val]').removeClass('d-flex');
    $('[data-credits-filter-val]').hide();
    $('[data-credits-filter-val="'+filter+'"]').addClass('d-flex');
  }
}

function filterSoftwareUpdates(val){
  if(val == 'all'){
    $('[data-update]').show();
  } else {
    $('[data-update]').hide();
    $('[data-update="'+val+'"]').show();
  }
}

if(aurl == 'user'){
  fullHeightElement('[data-full-height-activity]',320);
  fullHeightElement('[data-full-height-credits]',480);
  fullHeightElement('[data-full-height-chat]',200,'hidden');
  fullHeightElement('[data-full-height-chat-right]',300,'hidden');

  $('#search_chat_member').keyup(function(e) {
    filterUserChatList($(this).val());
  });

  $('#search_chat_member').on('search', function(){
    filterUserChatList($(this).val());
  });    
  
  locInitializeSettings();
  sendMessageAdmin();
}

function hoverFriendList(el,enter){
  if(enter == 1){
    $(el).removeClass('bg-light');
  } else {
    $(el).addClass('bg-light');
  }
}

function filterUserChatList(search){
  var search = search.toLowerCase();
  if(search != ''){
    $('[data-search-bind]').removeClass('d-flex');
    $('[data-search-bind]').hide();
    $('[data-search-bind]').each(function(){
      var filter = $(this).attr('data-search-bind');
      var c = filter.includes(search);

      if(c){
        $('[data-search-bind="'+filter+'"]').addClass('d-flex');       
        $('[data-search-bind="'+filter+'"]').show();
      }
    })
  } else {
    $('[data-search-bind]').addClass('d-flex');
    $('[data-search-bind]').show();
  }
}


function updateAnswers(val,c){
  var question = val;
  var answer;
  var i = 0;
  $('[data-btn-q='+val+']').addClass('is-loading');
  $('.q-'+val).each(function(entry) {
    answerId = $(this).attr('data-answer');
    answer = $(this).text();
    console.log(answer);
    i++;
    $.ajax({
      data: {
        action: 'updateAnswer',  
          qid: val,
          answer: answer,
          answerId: answerId
         },    
      url: request_source()+'/admin.php', 
      type:  'post',    
      success: function(response) { 
        $('#ajaxAnswers').html(response);
      }
    });   
  })     
}

if(aurl == 'premium'){
    var cta1 = $(".step1CTA");
    var cta2 = $(".step2CTA");
    var step1 = $(".step1 .stepDetails");
    var step2 = $(".step2 .stepDetails");
    var step3 = $(".step3 .stepDetails");

    // First Cta click ----------------

    cta1.click(function() {
      // Details Scroll
      var step2DetPrice = anime({
        targets: ".details",
        translateY: "112px",
        easing: "easeOutExpo",
        duration: "680",
        delay: "380"
      });

      // Details Plan Price Show

      var step2Details = anime({
        targets: ".detPrice",
        height: "114px",
        opacity: "1",
        easing: "easeOutExpo",
        duration: "680",
        delay: "480"
      });

      // Step 2 Container Animation

      var step2Show = anime({
        targets: ".step2 .stepDetails",
        opacity: 1,
        height: "458px",
        easing: "easeOutExpo",
        duration: "680",
        delay: "480"
      });

      $(".step2 .stepCount").addClass("active");

      // Step 1 Container Animation

      var step1Hide = anime({
        targets: ".step1 .stepDetails",
        height: "0",
        easing: "easeOutExpo",
        duration: "680"
      });

      $(".step1 .stepCount").removeClass("active");
      $(".step1 .stepCount").addClass("done");

      // Header Work

      var step1Hide = anime({
        targets: ".stepHeader1 h3 p",
        opacity: 0,
        translateY: ["0", "-20px"],
        easing: "easeOutExpo",
        duration: "320",
        delay: "0"
      });

      var step1Hide = anime({
        targets: ".step1 .stepHeader h3 span",
        opacity: 1,
        translateY: ["20px", "0"],
        easing: "easeOutExpo",
        duration: "320",
        delay: "0"
      });

      var step1HeaderShow = anime({
        targets: ".step2 .stepDetails",
        opacity: 1,
        easing: "easeOutExpo",
        duration: "1200",
        delay: "880"
      });

      // Scrolling

      $("html").scrollTo({ top: "166px", left: "0" }, 680);
    });

    // Change click -----------------------

    $(".step1Change").click(function() {
      // Switch Panels

      var hr = anime({
        targets: ".stuff",
        opacity: 1,
        easing: "easeOutExpo",
        duration: "340",
        delay: 480
      });

      var hr = anime({
        targets: ".summaryWrapp",
        opacity: 0,
        easing: "easeOutExpo",
        duration: "340",
        delay: 480
      });

      $(".stuff").delay(0).queue(function(next) {
        $(".stuff").css("display", "flex");
        next();
      });

      $(".summaryWrapp").delay(680).queue(function(next) {
        $(".summaryWrapp").css("display", "none");
        next();
      });

      var step2DetPrice = anime({
        targets: ".detPrice",
        height: "0",
        opacity: "0",
        easing: "easeOutExpo",
        duration: "680",
        delay: "380"
      });

      var hr = anime({
        targets: ".line",
        height: ["90%", "90%"],
        easing: "easeOutExpo",
        duration: "680",
        delay: 330
      });

      // Details Scroll

      var step2Details = anime({
        targets: ".details",
        translateY: "0px",
        easing: "easeOutExpo",
        duration: "680",
        delay: "480"
      });

      // Step 3 Container Animation

      var step2Hide = anime({
        targets: ".step3 .stepDetails",
        height: "0",
        easing: "easeOutExpo",
        duration: "680"
      });

      // Step 2 Animation

      var step2Hide = anime({
        targets: ".step2 .stepDetails",
        height: "0",
        easing: "easeOutExpo",
        duration: "680"
      });

      $(".step2 .stepCount").removeClass("active");
      $(".step3 .stepCount").removeClass("active");
      $(".step1 .stepCount").removeClass("done");
      $(".step2 .stepCount").removeClass("done");

      // Step 1 Animation

      var step1Hide = anime({
        targets: ".step1 .stepDetails",
        opacity: 1,
        height: "572px",
        easing: "easeOutExpo",
        duration: "680",
        delay: "480"
      });

      // Header Work

      var step1Show = anime({
        targets: ".stepHeader1 h3 p",
        opacity: 1,
        translateY: ["-20px", "0"],
        easing: "easeOutExpo",
        duration: "320",
        delay: "0"
      });

      var step1Hide = anime({
        targets: ".step1 .stepHeader h3 span",
        opacity: 0,
        translateY: ["0", "20px"],
        easing: "easeOutExpo",
        duration: "320",
        delay: "0"
      });

      /// Step 2 - Header Work

      // Header Work

      var step1Show = anime({
        targets: ".stepHeader2 h3 p",
        opacity: 1,
        translateY: ["-20px", "0"],
        easing: "easeOutExpo",
        duration: "320",
        delay: "0"
      });

      var step1Hide = anime({
        targets: ".step2 .stepHeader h3 span",
        opacity: 0,
        translateY: ["0", "20px"],
        easing: "easeOutExpo",
        duration: "320",
        delay: "0"
      });

      $(".step1 .stepCount").addClass("active");

      // Scrolling

      $("html").scrollTo({ top: "166px", left: "0" }, 680);
    });

    // --------------------------------

    // Step #2 Cta click

    cta2.click(function() {
      // Switch Panels

      var hr = anime({
        targets: ".stuff",
        opacity: 0,
        easing: "easeOutExpo",
        duration: "340",
        delay: 480
      });

      var hr = anime({
        targets: ".summaryWrapp",
        opacity: 1,
        easing: "easeOutExpo",
        duration: "340",
        delay: 480
      });

      $(".stuff").delay(680).queue(function(next) {
        $(".stuff").css("display", "none");
        next();
      });

      $(".summaryWrapp").delay(0).queue(function(next) {
        $(".summaryWrapp").css("display", "flex");
        next();
      });

      // Line Animation

      var hr = anime({
        targets: ".line",
        height: ["90%", "32%"],
        easing: "easeOutExpo",
        duration: "680",
        delay: 270
      });

      // Details Scroll

      var step2Details = anime({
        targets: ".details",
        translateY: "230px",
        easing: "easeOutExpo",
        duration: "680",
        delay: "480"
      });

      // Step 3 Animation

      var step2Show = anime({
        targets: ".step3 .stepDetails",
        opacity: 1,
        height: ["0", "386px"],
        easing: "easeOutExpo",
        duration: "680",
        delay: "480"
      });

      $(".step3 .stepCount").addClass("active");
      $(".step2 .stepCount").addClass("done");

      // Step 2 Animation

      var step1Hide = anime({
        targets: ".step2 .stepDetails",
        height: "0",
        easing: "easeOutExpo",
        duration: "680"
      });

      // Header Work

      var step1Hide = anime({
        targets: ".stepHeader2 h3 p",
        opacity: 0,
        translateY: ["0", "-20px"],
        easing: "easeOutExpo",
        duration: "320",
        delay: "0"
      });

      var step1Hide = anime({
        targets: ".step2 .stepHeader h3 span",
        opacity: 1,
        translateY: ["20px", "0"],
        easing: "easeOutExpo",
        duration: "320",
        delay: "0"
      });

      var step1HeaderShow = anime({
        targets: ".step3 .stepDetails",
        opacity: 1,
        easing: "easeOutExpo",
        duration: "1200",
        delay: "880"
      });

      $(".step2 .stepCount").removeClass("active");

      // Scrolling

      $("html").scrollTo({ top: "280px", left: "0" }, 680);
    });

    // ---------------------

    // Step #2 Change click

    $(".step2Change").click(function() {
      // Switch Panels

      var hr = anime({
        targets: ".stuff",
        opacity: 1,
        easing: "easeOutExpo",
        duration: "680",
        delay: 480
      });

      var hr = anime({
        targets: ".summaryWrapp",
        opacity: 0,
        easing: "easeOutExpo",
        duration: "680",
        delay: 480
      });

      $(".stuff").delay(0).queue(function(next) {
        $(".stuff").css("display", "flex");
        next();
      });

      $(".summaryWrapp").delay(680).queue(function(next) {
        $(".summaryWrapp").css("display", "none");
        next();
      });

      //-------------------------

      var hr = anime({
        targets: ".line",
        height: ["85%", "85%"],
        easing: "easeOutExpo",
        duration: "680",
        delay: 330
      });

      // Details Scroll

      var step2Details = anime({
        targets: ".details",
        translateY: "112px",
        easing: "easeOutExpo",
        duration: "680",
        delay: "480"
      });

      // Step 2 Animation

      var step2Hide = anime({
        targets: ".step3 .stepDetails",
        height: ["230px", "0"],
        easing: "easeOutExpo",
        duration: "680"
      });

      $(".step3 .stepCount").removeClass("active");
      $(".step2 .stepCount").removeClass("done");

      // Step 1 Animation

      var step1Hide = anime({
        targets: ".step2 .stepDetails",
        opacity: 1,
        height: ["0", "458px"],
        easing: "easeOutExpo",
        duration: "680",
        delay: "480"
      });

      // Header Work

      var step1Show = anime({
        targets: ".stepHeader2 h3 p",
        opacity: 1,
        translateY: ["-20px", "0"],
        easing: "easeOutExpo",
        duration: "320",
        delay: "0"
      });

      var step1Hide = anime({
        targets: ".step2 .stepHeader h3 span",
        opacity: 0,
        translateY: ["0", "20px"],
        easing: "easeOutExpo",
        duration: "320",
        delay: "0"
      });

      $(".step2 .stepCount").addClass("active");

      // Scrolling

      $("html").scrollTo({ top: "166px", left: "0" }, 680);
    });

    /*
    $(window).scroll(function(){
        if ($(window).scrollTop() >= $('.header').height()) {
             $(".details").addClass('fixed');

        }

      else {
         $(".details").removeClass('fixed');
      }
    });

    */

    // Step #2 Change click

    $(".detHeader a").click(function() {
      $(".detPurchase").toggleClass("marg");
       $(".second").toggleClass("viewed");



      var detListHide = anime({
        targets: ".detHeader a svg",
        rotate: "+=180deg",
        opacity: 1,
        easing: "easeOutExpo",
        duration: "230"
      });
    });



    // Product Mapping




    // Features Arrays -------------------

    var f_Unlimited = ['Live chat support','50.000 Aritificial inteligence replies', 'High quality fake users profiles', 'Adult fake users profile'];


    var f_VIP = ['Live chat support','20.000 Aritificial inteligence replies', 'High quality fake users profiles', 'Adult fake users profile'];

    var f_Connect = ['Live chat support','5.000 Aritificial inteligence replies', 'High quality fake users profiles', 'Adult fake users profile'];

    // Benefits Arrays -------------------

    var b_VIP = ['Unlimited software installation','Access to ALL premium themes', 'Access to ALL premium plugins'];

    var b_Unlimited = ['Unlimited software installation','Access to Landing premium themes', 'Access to ALL premium plugins'];

    var b_Connect = [];


    /// Initial Unlimited Load -----
    var featureList = $('.features')
    $.each(f_Unlimited, function(i) {
        var li = $('<li/>').appendTo(featureList);  
        var p = $('<p>').text(f_Unlimited[i]).appendTo(li);

    });
    var benefitsList = $('.benefits')

    $.each(b_Unlimited, function(i) {
        var li = $('<li/>').appendTo(benefitsList);  
        var p = $('<p>').text(b_Unlimited[i]).appendTo(li);

    });        
    /// -----------




    /// VIP Hover -----
    $("#VIP").hover(function() {
       $(".detTitle").html('VIP');
       $(".features li").empty();
       $(".benefits li").empty();               
       $(".benefits").show();
        $(".features li").empty();
        $.each(f_VIP, function(i) {
            var li = $('<li/>').appendTo(featureList);  
            var p = $('<p>').text(f_VIP[i]).appendTo(li);

        });
        $.each(b_VIP, function(i) {
            var li = $('<li/>').appendTo(benefitsList);  
            var p = $('<p>').text(b_VIP[i]).appendTo(li);
        });            
    }); 

    /// -----------

    /// Unlimited Hover -----

    $("#Unlimited").hover(function() {
       $(".detTitle").html("Unlimited" + "<span>Most Popular</span>");
       $(".features li").empty();
       $(".benefits li").empty();
       $(".benefits").show();
        $.each(f_Unlimited, function(i) {
          
            var li = $('<li/>').appendTo(featureList);  
            var p = $('<p>').text(f_Unlimited[i]).appendTo(li);

        });
      
      $.each(b_Unlimited, function(i) {
            var li = $('<li/>').appendTo(benefitsList);  
            var p = $('<p>').text(b_Unlimited[i]).appendTo(li);

        });
      
    }); 

    /// -----------

    /// Combo Hover -----

    $("#Combo").hover(function() {
      
       $(".detTitle").html("Combo");
      $(".features li").empty();
      $(".benefits li").empty();
       $(".benefits").show();
    $.each(f_Unlimited, function(i) {
      
        var li = $('<li/>').appendTo(featureList);  
        var p = $('<p>').text(f_Unlimited[i]).appendTo(li);

    });
      
      
    $.each(b_Combo, function(i) {
      
        var li = $('<li/>').appendTo(benefitsList);  
        var p = $('<p>').text(b_Combo[i]).appendTo(li);

    });
      
    }); 

    /// -----------

    /// Connect Hover -----

    $("#Connect").hover(function() {
      
       $(".detTitle").html("Connect Domain");
      
      $(".features li").empty();
      $(".benefits").hide();
      
    $.each(f_Connect, function(i) {
      
        var li = $('<li/>').appendTo(featureList);  
        var p = $('<p>').text(f_Connect[i]).appendTo(li);

    });
      
      
    $.each(b_Connect, function(i) {
      
        var li = $('<li/>').appendTo(benefitsList);  
        var p = $('<p>').text(b_Connect[i]).appendTo(li);

    });
      
    }); 

    /// -----------


    // Cycle Switcher -------------------------------



    $("#3Years").hover(function() {
         $(".detHeader div p ").html("3 Years Subscription");
      $(".detPrice div p ").html("$360.00 <small>($10.00 × 36 months)</small><span class='priceSave'>Save 55%</span>");
      
       $(".benefits p").empty();
      
      $.each(b_Unlimited, function(i) {
      
        var li = $('<li/>').appendTo(benefitsList);  
        var p = $('<p>').text(b_Unlimited[i]).appendTo(li);

    });
      
      
    });

    $("#2Years").hover(function() {
         $(".detHeader div p ").html("2 Years Subscription");
      $(".detPrice div p ").html("$264.00 <small>($11.00 × 24 months)</small><span class='priceSave'>Save 50%</span>");
      
       $(".benefits p").empty();
      
      $.each(b_Unlimited, function(i) {
      
        var li = $('<li/>').appendTo(benefitsList);  
        var p = $('<p>').text(b_Unlimited[i]).appendTo(li);

    });
      
      
    });

    $("#1Year").hover(function() {
         $(".detHeader div p ").html("Yearly Subscription");
      $(".detPrice div p ").html("$168.00 <small>($14.00 × 12 months)</small><span class='priceSave'>Save 45%</span>");
      
      $(".benefits p").empty();
      
      $.each(b_Unlimited, function(i) {
      
        var li = $('<li/>').appendTo(benefitsList);  
        var p = $('<p>').text(b_Unlimited[i]).appendTo(li);

    });
      
      
    });

    $("#Month").mouseenter(function() {
      

        
         $(".detHeader div p ").html("Monthly Subscription");
      $(".detPrice div p ").html("$16.00 <small>/month to month</small><span style='opacity:0;' class='priceSave'>Save 45%</span>");
      
      $(".benefits li").remove();
      
      
        var p = $('</p>').text(" Monthly subscriptions do not include a free domain, premium apps and ad vouchers.").appendTo(".benefits");


      


      
    });


    /// Plans & Cycles Hover Logic -----
       
      $('#1year.planPrice button').addClass('active');

     var PlanBd = anime({ 
        targets: ['#Unlimited object' , '#1Year object'],  
        opacity: 1,
        easing: "easeOutExpo",
        duration: "230"
      });
     

    $(".product").mouseenter(function() { 
       

      $('.product .planPrice button').removeClass('active');
      $(this).find('.planPrice button').addClass('active');
      
      var PlanBd = anime({
        targets: '.product object',
        opacity: 0, 
       
        easing: "easeOutExpo",
        duration: "230",
        
      });
      
      var PlanBd = anime({
        targets: this.querySelectorAll('object'),
        opacity: 1,
        easing: "easeOutExpo",
        duration: "230",

      });
      
    });
     

    $(".cycle").mouseenter(function() { 
       

      $('.cycle .planPrice button').removeClass('active');
      $(this).find('.planPrice button').addClass('active');
      
      var PlanBd = anime({
        targets: '.cycle object',
        opacity: 0, 
       
        
      });
      
      var PlanBd = anime({
        targets: this.querySelectorAll('object'),
        opacity: 1,

      });
      
    });
}


function uploadMediaAdmin(uid,type=''){
  upType = 'uploadMediaAdmin';
  uploadUserMediaId = uid;
  uploadUserMediaType = type;

  if($('#modal-upload-media').is(':visible')){ } else {
    $('.dz-preview').html('');
  }
  $('#modalBodyUploadMediaInstagram').hide();
  $('#uploadDataInstagram').hide();
  $('#modalBodyUploadMedia').show();
  $('#uploadContent').click();
  if(type == 'story'){
    $('[data-uploadUserStory="story"]').show();
    $('[data-uploadUserMedia="media"]').hide();
  } else {
    $('[data-uploadUserStory="story"]').hide();
    $('[data-uploadUserMedia="media"]').show();    
  }
}

$('input[type="number"]').on('keyup',function(){
    v = parseInt($(this).val());
    min = parseInt($(this).attr('min'));
    max = parseInt($(this).attr('max'));
    if (v < min){
        $(this).val(min);
    } else if (v > max){
        $(this).val(max);
    }
});

$('#instagramUsername').on('keyup',function(){
  var val = $(this).val();
  val = val.replace('https://www.instagram.com/', "");
  val = val.replace('/', "");
  $(this).val(val);
});

var uploadMediaArray = [];

function openModalUploadInstagram(el){
  var uid = $(el).attr('data-current-media-user');
  uploadUserMediaId = uid;
  upType = 'uploadMediaFromInstagramAdmin';

  $('.dz-preview').html('');
  
  $('#instagramForm').show();
  $('#uploadMoreInstagram').hide();
  $('#uploadDataInstagram').show();
  $('#modalBodyUploadMedia').hide();
  $('#modalBodyUploadMediaInstagram').show();

  $('[data-uploadUserStory="story"]').hide();
  $('[data-uploadUserMedia="media"]').hide();

  $('#modal-upload-media').modal({
      backdrop: 'static',
      keyboard: false
  });  

}

function uploadFromInstagram(){

  $('#modalBodyUploadMediaInstagram').addClass('is-loading is-loading-lg');
  $('#instagramForm').hide();
  $('#uploadDataInstagram').hide();
  $.ajax({
    type: "GET",
    url: site_config.site_url+'plugins/instagram/getMediaByUsername.php',
    data: {
      username: $('#instagramUsername').val(),
      order: $('#instagramOrder').val(),
      limit: $('#instagramLimit').val(),
      type: $('#instagramType').val(),
      uid: uploadUserMediaId
    },
    dataType: 'JSON',
    success: function(resp) {
      $('#modalBodyUploadMediaInstagram').removeClass('is-loading is-loading-lg');
      if(resp.length == 0){
        $('#instagramForm').show();
        $('#instagramNoData').show();
        setTimeout(function(){
          $('#instagramNoData').hide();
        },3500);
        return false;
      }      
      resp.forEach(function(element,index) {
        var globalElement = element;
        $.ajax({
          type: "GET",
          url: site_config.site_url+'assets/sources/upload.php',
          data: {
            fromUrl: element.src
          },
          dataType: 'JSON',
          success: function(response) {
            uploadMediaArray[0] = response;
            createPreviewInstagram(response.video,response.path);
            console.log(response);
            $.ajax({
              type: "POST",
              url: request_source()+'/belloo.php',
              data: {
                action: 'uploadStory',
                media: uploadMediaArray,
                adminPanel: true,
                uid: uploadUserMediaId,
                ig_id: globalElement.ig_id,
                uploadTo: $('#instagramUploadTo').val(),
                creditsPhoto: $('#instagramPricePhotos').val(),
                creditsVideo: $('#instagramPriceVideos').val()
              },
              dataType: 'JSON',
              success: function(response) {

              }
            });
          }
        });        
      });
      $('#uploadMoreInstagram').show();

    }
  });  
      
}

function closeUploadMediaAdmin(){
  loadDataMedia();
}



var extFilter = ["jpg", "jpeg", "png","svg","mp4", "ogg", "webm"];
$("#upload-area").dmUploader({
  url: site_url+'assets/sources/upload.php',
  multiple: true,
  extFilter: extFilter,
  onFileExtError: function(file){
    swal({ title: site_lang[811]['text'], text: site_lang[596]['text'],   type: 'info' }, function(){ });
  }, 
  onNewFile: function(id, file){
    var fileUrl = URL.createObjectURL(file);

    if(file.size > site_config.max_upload){
        var maxAllowed = site_config.max_upload / 1024 / 1024;
        swal({   title: site_lang[810]['text'], text: site_lang[809]['text']+' ('+maxAllowed+') MB',   type: 'info' }, function(){ });
        return false;
    }

    if(upType == 'gift' || upType == 'interest'){
      $('[data-gift-uploaded]').attr('data-src',fileUrl);
      profilePhoto();
      $('[data-gift-uploaded]').addClass('uploadingGray');
    }

    if(upType == 'uploadMediaAdmin'){        
      $('#modal-upload-media').modal({
          backdrop: 'static',
          keyboard: false
      }); 
      createPreview(file, fileUrl,id);
      $("#modalBodyUploadMedia").animate({ scrollTop: $('.dz-preview').height() }, 250);
    }

  },  
  onUploadProgress: function(id,percent){
    $('#upload'+id).css('width',percent+'%');
    if(percent >= 98){
      $('[data-upload-progress="upload'+id+'"]').hide();
      $('[data-upload-media-complete="'+id+'"]').show();
    }
  },
  onComplete: function(){
  
  },
  onUploadSuccess: function(id, file){
    if(upType == 'gift'){
      $('[data-gift-uploaded]').removeClass('uploadingGray');
      $('#giftIcon').val(file.path);
    }
    if(upType == 'interest'){
      $('[data-gift-uploaded]').removeClass('uploadingGray');
      $('#interestIcon').val(file.path);
    }    
    if(upType == 'gesture' || upType == 'watermark'){
      $('#gestureBtn').removeClass('is-loading');
      $('#gesture').val(file.path);
      $('#gesturePreview').attr('src',file.path);
      $('#gesture').trigger('change');
    }

    if(upType == 'uploadMediaAdmin'){
      uploadMediaArray[0] = file;
      if(uploadUserMediaType != 'story'){
        $.ajax({
          type: "POST",
          url: request_source()+'/belloo.php',
          data: {
            action: 'uploadMedia',
            media: uploadMediaArray,
            adminPanel: true,
            uid: uploadUserMediaId
          },
          dataType: 'JSON',
          success: function(response) {

          }
        });  
      } else {
        $.ajax({
          type: "POST",
          url: request_source()+'/belloo.php',
          data: {
            action: 'uploadStory',
            media: uploadMediaArray,
            adminPanel: true,
            uid: uploadUserMediaId
          },
          dataType: 'JSON',
          success: function(response) {

          }
        });  
      }    
    }

  }
  
});



function createPreview(file, fileContents,id) {
  var $previewElement = '';
  switch (file.type) {
    case 'image/png':
    case 'image/jpeg':
    case 'image/gif':
      $previewElement = $('<img src="'+fileContents+'" class="avatar-img rounded" />');
      break;
    case 'video/mp4':
    case 'video/webm':
    case 'video/ogg':
      $previewElement = $('<video autoplay muted loop width="100%" height="100%" style="object-fit:cover;border-radius:5px"><source src="' + fileContents + '" type="' + file.type + '"></video>');
      break;
    default:
      break;
  }

  var $displayElement = $(`
    <li class="list-group-item dz-processing dz-image-preview" data-manage-media="`+id+`" data-manage-media-path>
        <div class="progress" data-upload-progress="upload`+id+`" style="height:4px;margin-left:0px;margin-right:5px;margin-bottom:5px">
          <div class="determinate" id="upload`+id+`" style="width: 0%;height:4px"></div>
        </div>         
        <div class="form-row align-items-center" style="margin">
            <div class="col-auto">           
                <div class="avatar" style="width:99px;height:99px"></div>
            </div>
            <div class="col">
                <div class="font-weight-bold">`+textAbstract(file.name,35)+`</div>
                <p class="small text-muted mb-0" data-dz-size="">`+file.type+`</p>
            </div>
            <div class="col-auto">
                <a href="javascript:;" class="text-muted-light" style="display:none" data-upload-media-complete="`+id+`">
                    <i class="material-icons text-success">check_circle</i>
                </a>
            </div>
        </div>
    </li>
  `);

  $displayElement.find('.avatar').append($previewElement);
  $('.dz-preview').append($displayElement);

}

function createPreviewInstagram(isVideo,fileContents,id='') {
  var $previewElement = '';
  var fileType = 'Video';
  if(isVideo == 1){
    $previewElement = $('<video autoplay muted loop width="100%" onloadedmetadata="this.muted = true" height="100%" style="object-fit:cover;border-radius:5px"><source src="' + fileContents + '"></video>');
  } else {
    fileType = 'Photo';
    $previewElement = $('<img src="'+fileContents+'" class="avatar-img rounded" />');
  }
  var name = '';
  name = fileContents.replace(site_config.site_url+'assets/sources/uploads/', "");
  var $displayElement = $(`
    <li class="list-group-item dz-processing dz-image-preview">         
        <div class="form-row align-items-center" style="margin">
            <div class="col-auto">           
                <div class="avatar media-loading" style="width:99px;height:99px;"></div>
            </div>
            <div class="col">
                <div class="font-weight-bold">`+textAbstract(name,35)+`</div>
                <p class="small text-muted mb-0" data-dz-size="">`+fileType+`</p>
            </div>
            <div class="col-auto">
                <a href="javascript:;" class="text-muted-light">
                    <i class="material-icons text-success">check_circle</i>
                </a>
            </div>
        </div>
    </li>
  `);

  $displayElement.find('.avatar').append($previewElement);
  $('.dz-preview').append($displayElement);

}


function textAbstract(text, length) {
    if (text == null) {
        return "";
    }
    if (text.length <= length) {
        return text;
    }
    text = text.substring(0, length);
    last = text.lastIndexOf("_");
    text = text.substring(0, last);
    return text + "...";
}

function delay(callback, ms) {
  var timer = 0;
  return function() {
    var context = this, args = arguments;
    clearTimeout(timer);
    timer = setTimeout(function () {
      callback.apply(context, args);
    }, ms || 0);
  };
}

function tableDropdownEffect(){
  $('[data-table-dropdown]').on('show.bs.dropdown', function (e) {
    $(this).parent('td').parent('tr').css('background','#F7F9FA');
  })   
  $('[data-table-dropdown]').on('hide.bs.dropdown', function (e) {
    $(this).parent('td').parent('tr').css('background','#F3F5F6');
  })   
}

$('[data-toggle="tab"]').click(function(){
  var tabUrl = $(this).attr('data-tab-url');
  if(tabUrl == 'chat'){
    $('html,body').css('overflow','hidden');
  } else {
    $('html,body').css('overflow','auto');
  }
})

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
    
  }); 
}

function locInitializeCreateUser() {
  TeleportAutocomplete.init('#createUserLoc').on('change', function(value) {
    var lat = value.latitude;
    var lng = value.longitude;
    var city = value.name;
    var country = value.country;
    var cityID = value.geonameId;
    $('#locality').val(city);
    $('#lat').val(lat); 
    $('#lng').val(lng);
    $('#country').val(country);         
    
  }); 
}

locInitializeCreateUser();

function viewChat(el){
  var uid = $(el).attr('data-uid');
  var cid = $(el).attr('data-cid');

  if(aurl == 'chats'){
    $('#fake_id').val($(el).attr('data-id'));
    $('#fake_name').val($(el).attr('data-name'));
    $('#fake_photo').val($(el).attr('data-photo'));
    $('[data-new-message='+$(el).attr('data-chatid')+']').hide();
  }

  $('body').removeClass('selectedChat');
  $(".rightSectionChatUser").each(function(){
    $(this).removeClass('selectedChat');
  });
  $(el).addClass('selectedChat');
  $('#chat_result').css('opacity','0.5');
  $('#r_id').val(cid);
  $('#chat-message').focus();
  $.ajax({
    type: "POST",
    url: request_source()+'/admin.php',
    data: {
      action: 'loadChatAdmin',
      uid: uid,
      cid: cid,
      globalKey: globalKey
    },
    success: function(response) {
      $('#chat_result').html(response);
      $('#chat_result').css('opacity','1');
      setTimeout(function(){
        $('#chat_container').animate({scrollTop:99999}, 200, 'swing', function() { });       
      },150)
      
    }
  });    
}


function sendMessageAdmin(){
  $('#c-send').submit(function(e) {
    e.preventDefault();
    //console.log('test');
    var r_id = $('#r_id').val();
    var messageVal = $("#chat-message").val();    
    var mob = 0;

    var me = Math.floor(Math.random() * 10000000);      
    if(messageVal.length == 0){ return false };    

    var message2 = messageVal;
    $('#chat_result').append(`
      <div class="media py-3">
          <a href="#" class="avatar avatar-sm mr-3">
              <img src="`+edit_info.profile_photo+`" class="avatar-img rounded-circle" alt="avatar">
          </a>
          <div class="media-body">
              <div class="d-flex align-items-center">
                  <div class="flex">
                      <a href="#" class="text-body bold">`+edit_info.first_name+`</a>
                  </div>
                  <small class="text-muted" style="font-size:11px">Now</small>
              </div>
              <div>`+message2+`</div>
          </div>
      </div>      
      `);          
           
    $('#chat-message').val("");
    $('#chat_container').animate({scrollTop:99999}, 200, 'swing', function() { }); 


    fakeMessagesTab('New');

    //send message
    var message = edit_info.id+'[message]'+r_id+'[message]'+messageVal+'[message]text';
    var send = edit_info.id+'[rt]'+r_id+'[rt]'+edit_info.profile_photo+'[rt]'+edit_info.first_name+'[rt]'+messageVal+'[rt]text';      

    $.get( gUrl, {action: 'message', query: send} );    
    $.get( aUrl, {action: 'sendMessage', query: message} );
  });  
}


function sendChatAdmin(){
  $('#c-send').submit(function(e) {
    e.preventDefault();
    //console.log('test');
    var r_id = $('#r_id').val();
    var messageVal = $("#chat-message").val();    
    var mob = 0;
    var id = $('#fake_id').val();
    var name = $('#fake_name').val();
    var photo = $('#fake_photo').val();

    var me = Math.floor(Math.random() * 10000000);      
    if(messageVal.length == 0){ return false };    

    var message2 = messageVal;
    $('#chat_result').append(`
      <div class="media py-3">
          <a href="#" class="avatar avatar-sm mr-3">
              <img src="`+photo+`" class="avatar-img rounded-circle" alt="avatar">
          </a>
          <div class="media-body">
              <div class="d-flex align-items-center">
                  <div class="flex">
                      <a href="#" class="text-body bold">`+name+`</a>
                  </div>
                  <small class="text-muted" style="font-size:11px">Now</small>
              </div>
              <div>`+message2+`</div>
          </div>
      </div>      
      `);          
           
    $('#chat-message').val("");
    $('#chat_container').animate({scrollTop:99999}, 200, 'swing', function() { }); 

    fakeMessagesTab('New');
    //send message
    var message = id+'[message]'+r_id+'[message]'+messageVal+'[message]text';
    var send = id+'[rt]'+r_id+'[rt]'+photo+'[rt]'+name+'[rt]'+messageVal+'[rt]text';      

    $.get( gUrl, {action: 'message', query: send} );    
    $.get( aUrl, {action: 'sendMessage', query: message} );
  });  
}


function openModerationPermission(id,data){
  $('#modTag').text(id);
  editingModerator = id;
  data.forEach(function(mod) {
    $('#moderationPermission'+mod['setting']).prop("checked",false);
    var checked = mod['setting_val'];
    if(checked != 'Yes'){
        $('#moderationPermission'+mod['setting']).prop("checked",false);
    } else {
        $('#moderationPermission'+mod['setting']).prop("checked",true);
    }                     
  });  
  $('#modal-update-moderator-permission').modal('toggle');
}


function updateModerationPermission(val,setting){
  if(val){
    val = 'Yes';
  } else {
    val = 'No';
  }
  console.log(val);
  $.ajax({
      url: request_source()+'/admin.php', 
      data: {
          action: 'updateModeratorPermission',
          val: val,
          id: editingModerator,
          setting: setting,
          globalKey: globalKey
      },  
      type: "post",        
      success: function(response) {}
  });    
}

function adminProfileActions(action,val='',custom=''){

  var title = '';
  var desc = '';
  var content = '';
  var custom = '';
  var col = '';

  $('#editModalUpdateBtn').show();

  if(action == 'addCredits'){
    title = 'Add credits to '+edit_info.name;
    desc = '<span class="text-warning">Warning!</span> if '+edit_info.name+' already has credits it will be automatically deleted for the new amount';
    custom = 'addCreditsInput';
    col = 'credits';
    content = `
      <div class="lw-inline">
          <div class="lw-field">
              <div class="lw-label">Credits</div>
              <div class="lw-group">
                <input class="lw-input bg-light" id="addCreditsInput" type="number" placeholder="Enter credits amount" required>
              </div>
          </div>
      </div>
    `;
  }

  if(action == 'addPremium'){
    title = 'Add premium to '+edit_info.name;
    desc = '<span class="text-warning">Warning!</span> if '+edit_info.name+' already has premium it will be automatically deleted for the new amount';
    custom = 'addPremiumInput';
    col = 'premium';
    content = `
      <div class="lw-inline">
          <div class="lw-field">
              <div class="lw-label">Premium</div>
              <div class="lw-group">
                <input class="lw-input bg-light" id="addPremiumInput" type="number" placeholder="Enter premium in days" required>
              </div>
          </div>
      </div>
    `;
  } 

  if(action == 'viewPassword'){
    $('#editModalUpdateBtn').hide();
    title = 'Viewing password of '+edit_info.name;
    desc = '';
    custom = '';
    col = '';
    content = `
      <div class="lw-inline">
          <div class="lw-field">
              <div class="lw-label">Password:</div>
              <div class="lw-group">
                <input class="lw-input bg-light" type="text" readonly value="`+edit_info.password+`" required>
              </div>
          </div>
      </div>
    `;
  }    

  $('#editModalTitle').html(title);
  $('#editModalDesc').html(desc);
  $('#editModalContent').html(content);

  var onclick = 'updateProfileData("'+action+'","'+val+'","'+col+'","'+custom+'")';
  $('#editModalUpdateBtn').attr('onclick',onclick);
  openWidget('userEditFromAdmin');  
}

function endStreamAdmin(stream,sb=0){
  $.get( request_source()+'/live.php', {action: 'endStream',stream: stream,sb: sb},function(data){

    if(sb == 0){
      swal({
          title: 'Live Ended',
          text: 'Live ended successfully',
          type: "success"
      }, function(t) { 
      })
    }
    if(sb == 1){
      swal({
          title: 'Streamer Banned',
          text: 'Streamer Banned successfully',
          type: "success"
      }, function(t) { 
      })
    } 
    if(sb == 2){
      swal({
          title: 'Streamer Unbanned',
          text: 'Streamer Unbanned successfully',
          type: "success"
      }, function(t) { 
      })
    }
    if(sb == 3){
      swal({
          title: 'Live Ended and Streamer Banned',
          text: 'Live Ended and Streamer Banned successfully',
          type: "success"
      }, function(t) { 
      })
    }    

    goTo('live');        
  });
}

function updateProfileData(action,val='',col='',custom=''){

  if(val === ''){
    val = $('#'+custom).val();
  }

  if(action == 'addCredits'){
    $('#editUserCreditsText').text(val);
  }
  if(action == 'addPremium'){
    $('#editUserPremiumText').text('YES');
  }  

  if(action == 'setAdministrator' || action == 'setPopular' || action == 'setVerified' 
    || action == 'addToSpotlight' || action == 'setFakeUser'){ 
    if(action == 'setAdministrator' || action == 'setFakeUser'){
      $('#setAdministratorText').text(custom);  
    }
    toastr.success('Updated!', 'User data successfully updated.');   
  }
  $.ajax({
      url: request_source()+'/admin.php', 
      data: {
          action: 'updateDataProfile',
          method: action,
          col: col,
          val: val,
          uid: edit_info.id,
          custom: custom,
          globalKey: globalKey
      },  
      type: "post",        
      success: function(response) { 
        $('.lw-close').click();                
      }
  });   
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

  $("[data-profile-question-answer-update="+q+"]").fadeIn();
  setTimeout(function(){  
    $( "[data-profile-question-answer-update="+q+"]").fadeOut();
  },2000);
  var query = edit_info.id+','+q+','+answer;
  $.get( aUrl, { action: 'updateUserExtended', query: query } );  
}

function selectInterest(div){
    var val = $(div).attr('data-select');
    var id = $(div).attr('data-id');
    var query = edit_info.id+','+id;
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

    $("[data-select-interest-id="+id+"]").toggleClass("grayScale");
}


function updateLanguageVisible(val,id){
  if(val){
    val = 1;
    $('[data-lang-novisibile='+id+']').hide();
    $('[data-lang-visibile='+id+']').show();
  } else {
    val = 0;
    $('[data-lang-novisibile='+id+']').show();
    $('[data-lang-visibile='+id+']').hide();    
  }
  $.ajax({
      url: request_source()+'/admin.php', 
      data: {
          action: 'lang_visible',
          val: val,
          id: id,
          globalKey: globalKey
      },  
      type: "post",        
      success: function(response) {}
  });    
}

var proxyUrl = 'https://belloo-cors.herokuapp.com/';
function translate(translateTo, text,idVal,langId, translateFrom = 'en') {
    return new Promise((resolve, reject) => {
        const url = "https://translate.googleapis.com/translate_a/single?client=gtx&sl="+ translateFrom + "&tl=" + translateTo + "&dt=t&q=" + encodeURI(text);
        fetch(proxyUrl+url).then(response => {
            response.json().then(data => {
                updateAutoTranslate(data[0][0][0],idVal,langId)
            }, reject)
        }, reject)
    });
}

function updateAutoTranslate(text,idVal,id){
  $('#'+idVal+id).val(text);
  $('#'+idVal+id).trigger('change');
}

function sleep(ms) {
  return new Promise(resolve => setTimeout(resolve, ms));
}

const autoTranslateSoon = async _ => {
  for (let index = 0; index < $('[data-edit-lang]').length; index++) {
    await sleep(200);
    var $data = $('[data-edit-lang]').eq(index);
    var text = $data.attr('data-lang-text');
    var idVal = $data.attr('data-idVal');
    var langId = $data.attr('data-edit-lang'); 
    translate(translateTo,text,idVal,langId).then(updateAutoTranslate);
  }
}

function autoTranslate(){
  alert('This feature will be available soon');
}


function exportLanguage(id,name,prefix){
  $.ajax({ 
      data: {
          action: 'exportJSONLanguage',
          id : id,
          name : name,
          prefix : prefix,
          globalKey: globalKey
      },
      url:   request_source()+'/admin.php',
      type:  'post',
      dataType: 'JSON',
      beforeSend: function(){ 
      },
      success: function(response){
          //goTo('users');
          downloadJSON(response.url,response.name);
          //console.log(response);
      }
  });  
}


function upJSONfile(type){
  uploadJSONType = type; 

  if(type == 'language'){
    var check = $('#importLanguage').hasClass( "is-loading" );
    if(check === true){  
      console.log('adding language');
      return false;
    }     
    $('#importLanguage').addClass('is-loading');
  }

  if(type == 'preset'){
    var check = $('#importPreset').hasClass( "is-loading" );
    if(check === true){  
      console.log('adding preset');
      return false;
    }     
    $('#importPreset').addClass('is-loading');
  }  
  document.getElementById("uploadContentJSON").click();
}  

$('#register').submit(function(e) {
  e.preventDefault();
  var findme = "Error";
  $('#createNewUserBtn').addClass('is-loading');  
  $.ajax({
      data:  $(this).serialize(),
      url:   request_source()+'/user.php',
      type:  'post',
      beforeSend: function () {
        $('#error').hide();
      },      
      success:  function (response) {   
        if ( response.indexOf(findme) > -1 ) {
          response = response.replace('Error','');
              swal({
                  title: 'Account not created',
                  text: response,
                  type: "error"
              }, function(t) { 
              })
        $('#createNewUserBtn').removeClass('is-loading');
        } else {
           window.location=site_config['site_url']+'index.php?page=admin&p=user&id='+response;
        }
      }
  });         
});


if(aurl == 'themes'){

  if( x >= 1280 && x <= 1365){
    window.addEventListener('load', function(){
      //$('[data-toggle="sidebar"]').trigger('click');
      //$('[data-theme-row]').removeClass('col-xl-10');
      //$('[data-theme-row]').addClass('col-xl-12');
    });
  }
  if (x < 1280){
    alert('To use the theme editor, access with a laptop or desktop device');
    goTo('main_dashboard');
  }
  
  
  setTimeout(function(){
    checkIframeLoaded();
  },500);
  setTimeout(function(){
    themesPreview(themeType);
  },300);  
  presetActions();        
}