var checkedUsers = [];


function manageUsers(){
	if(aurl == 'users'){
		checkedUsers = [];
		var search = {};
		var usersData = [];
		var viewedUsers = 0;
		var lastPageSearch = 0;
		var usersPerPage = 50;

		$('.divHidden').css('display','none');


		$('[data-sf]').each(function(){
		    var current = $(this).attr('data-sf');
		    search[current] = $(this).val();
		});
		$('[data-sf]').change(function(){
		    var current = $(this).attr('data-sf');
		    search[current] = $(this).val();
		    
		}); 

		$('#filterDate').change(function(){
		    var checked = $('#filterDate:checked').val();
		    if(checked == 'on'){
		        $('#filterDateOverlay').hide();
		    }else {
		        $('#filterDateOverlay').show();
		    }                
		});
		$('[data-sfc]').each(function(){
		    var current = $(this).attr('data-sfc');
		    var checked = $('[data-sfc='+current+']:checked').val();

		    if(checked != 'on'){
		        search[current] = 'off';
		    }else {
		        search[current] = 'on';
		    }
		    
		});            
		$('[data-sfc]').change(function(){
		    var current = $(this).attr('data-sfc');
		    search[current] = 'off';
		    var checked = $('[data-sfc='+current+']:checked').val();

		    if(checked != 'on'){
		        search[current] = 'off';
		    	if(current == 'online'){
		    		$('#filter_fake').attr('disabled',false);
		    	}		        
		    }else {
		    	if(current == 'online'){
		    		$('#filter_fake').attr('disabled',true);
		    	}
		        search[current] = 'on';
		    }
		});

		$('[data-sf-country]').change(function(){
		    var current = $(this).val();
		    if(current != 'all'){
		        $("[data-sf-city]").html('');
		        $("[data-sf-city]").append('<option value="all" selected>All locations</option>');
		        $("[data-sf-city]").find("option[value='all']").attr("selected",true);
		        $('[data-sf="city"]').val('all');
		        search['city'] = 'all';                    
		        $.ajax({
		            url: request_source()+'/admin.php', 
		            data: {
		                action: 'getCitiesByCountry',
		                country: current
		            },  
		            type: "post",
		            dataType: 'JSON',           
		            success: function(response) {
		                $("[data-sf-city]").html('');
		                $("[data-sf-city]").append('<option value="all" selected>All locations</option>');
		                response.forEach(function(element) {
		                  $("[data-sf-city]").append('<option value="'+element['city']+'">'+element['city']+'</option>');
		                });
		                $('[data-sf-city]').attr('disabled',false);
		            },
		        });
		    } else {
		        $("[data-sf-city]").html('');
		        $("[data-sf-city]").append('<option value="all">All locations</option>');
		        $('[data-sf-city]').attr('disabled',true); 
		        $('[data-sf="country"]').val('all');
		        $('[data-sf="city"]').val('all');
		        search['city'] = 'all';
		    }

		    //console.log(search);
		});                 

		search['action'] = 'search_users';
		search['globalKey'] = globalKey;
		var ajaxData = JSON.stringify(search);

		//update result
		$('[data-update-user-search]').click(function(){
	        $('[data-user-search-result]').hide();
	        $('[data-user-searching]').show();			
		    $.ajax({
		        url: request_source()+'/admin.php', 
		        data: search,  
		        type: "post",
		        dataType: 'JSON',    
		        beforeSend: function(){
		        	$('[data-update-search-btn]').addClass('is-loading is-loading-primary');
		        },    
		        success: function(response) {
		            $('[data-update-search-btn]').removeClass('is-loading');
		            $('[data-search]').show();
			        $('[data-user-search-result]').show();
			        $('[data-user-searching]').hide();
		            $('#usersTable').html('');
		            viewedUsers = 0;
		            usersData = response['data'];

		            var maxResult = usersPerPage;
		            if(usersData.length <= usersPerPage){
		                maxResult = usersData.length;
		            }

		            for (i = 0; i < maxResult; i++) { 
		              viewedUsers++;
		              $('#usersTable').append(response['data'][i]);
		            }

		            $('#totalResult').text(response.total);
		            $('[data-current-total-users]').text(response.total);
		            $('[data-current-view-users]').text(viewedUsers); 
		            tableDropdownEffect();		                                   
		        },
		    });
		})

		//load page first result
		$.ajax({
		    url: request_source()+'/admin.php', 
		    data: search,  
		    type: "post",
		    dataType: 'JSON',        
		    success: function(response) {
		        $('[data-search]').show();
		        usersData = response['data'];
		        for (i = 0; i < usersPerPage; i++) {
		          viewedUsers++; 
		          $('#usersTable').append(response['data'][i]);
		        }
		        $('#totalResult').text(response.total);

		        $('[data-user-search-result]').show();
		        $('[data-user-searching]').hide();
		        $('[data-current-total-users]').text(response.total);
		        $('[data-current-view-users]').text(viewedUsers);
				tableDropdownEffect();		        
		    },
		});

		//next result page
		 $('[data-nextPage-user-search]').click(function(e){
			e.preventDefault();
			$('#customCheckAll').prop("checked",false);		    	
		    if(usersData.length > viewedUsers){
		        $('#usersTable').html('');
		        var maxResult = viewedUsers+usersPerPage;

		        lastPageSearch = usersPerPage;
		        if(usersData.length <= maxResult ){
		            maxResult = usersData.length;
		            lastPageSearch = maxResult;
		        }                    
		        for (i = viewedUsers; i < maxResult; i++) {
		          viewedUsers++;
		          $('#usersTable').append(usersData[i]);
		        }
		        $('[data-current-view-users]').text(viewedUsers);  
		        $('[data-backPage-user-search]').fadeIn(); 
				$('html, body').animate({
					  scrollTop: 0
				}, 250);
		        checkIfChecked();                
		    }  
		    tableDropdownEffect();              
		 });

		 //back page
		 $('[data-backPage-user-search]').click(function(e){
		    
		    $('#usersTable').html('');
		    $('#customCheckAll').prop("checked",false);
		    var firstBack = usersPerPage*2;
		    viewedUsers = viewedUsers-firstBack;
		    console.log(viewedUsers);
		    if(viewedUsers <= 0){
		        $('[data-backPage-user-search]').hide();
		        viewedUsers = 0;
		    } 
		    var maxResult = viewedUsers+usersPerPage;
		    if(usersData.length <= maxResult ){
		        maxResult = usersData.length;
		    }                                
		    for (i = viewedUsers; i < maxResult; i++) {
		      viewedUsers++;
		      $('#usersTable').append(usersData[i]);
		    }
			$('html, body').animate({
				  scrollTop: 0
			}, 250);	
			tableDropdownEffect();	    
		    $('[data-current-view-users]').text(viewedUsers);  
		    checkIfChecked();                                
		 });  	

	}
}

function datePicker(){
	$('[data-toggle="flatpickr"]').each(function() {
	    var t = $(this),
	        a = {
	            mode: void 0 !== t.data("flatpickr-mode") ? t.data("flatpickr-mode") : "single",
	            altInput: void 0 === t.data("flatpickr-alt-input") || t.data("flatpickr-alt-input"),
	            altFormat: void 0 !== t.data("flatpickr-alt-format") ? t.data("flatpickr-alt-format") : "F j, Y",
	            dateFormat: void 0 !== t.data("flatpickr-date-format") ? t.data("flatpickr-date-format") : "Y-m-d",
	            wrap: void 0 !== t.data("flatpickr-wrap") && t.data("flatpickr-wrap"),
	            inline: void 0 !== t.data("flatpickr-inline") && t.data("flatpickr-inline"),
	            static: void 0 !== t.data("flatpickr-static") && t.data("flatpickr-static"),
	            enableTime: void 0 !== t.data("flatpickr-enable-time") && t.data("flatpickr-enable-time"),
	            noCalendar: void 0 !== t.data("flatpickr-no-calendar") && t.data("flatpickr-no-calendar"),
	            appendTo: void 0 !== t.data("flatpickr-append-to") ? document.querySelector(t.data("flatpickr-append-to")) : void 0,
	            onChange: function(e, r) {
	                a.wrap && t.find("[data-toggle]").text(r)
	            }
	        };
	    t.flatpickr(a)
	});	
}


function checkAll(element){

	var check = element.checked;

	//$('[data-checkedUsers-photos]').html('');
	$('[data-more-users]').remove();
	$('[data-check-user]').each(function(){
		$(this).prop("checked",check);
		var uid = $(this).attr('data-check-user');
		var photo = $(this).attr('data-check-user-photo');
		uid = parseInt(uid);
		if(check){
			checkedUsers = checkedUsers.filter(function(item) { 
			    return item !== uid
			});			
			checkedUsers.push(uid);
			if(checkedUsers.length <= 15){
				$('[data-checkedUsers-photos]').append('<div class="avatar avatar-xs" data-avatar="'+uid+'"><img src="'+photo+'" class="avatar-img rounded-circle"></div>');
			}
		} else {
			checkedUsers = checkedUsers.filter(function(item) { 
			    return item !== uid
			});	
			$('[data-avatar='+uid+']').remove();		
		}
	});

	console.log(checkedUsers);
	if(checkedUsers.length == 0){
		$('[data-selected-users]').hide();
	} else {
		$('[data-selected-users-total]').text(checkedUsers.length);
		$('[data-selected-users]').fadeIn();
	}
	if(checkedUsers.length >= 15){	
		var more = checkedUsers.length - 15;
		$('[data-checkedUsers-photos]').append('<span data-more-users style="line-height:37px;padding-left:10px">+'+more+' users</span>');
	}
	//console.log(checkedUsers);
}

function checkUser(element,uid,photo){

	var check = element.checked;

	$('[data-more-users]').remove();
	if(check){
		checkedUsers.push(uid);
		if(checkedUsers.length <= 15){
			$('[data-checkedUsers-photos]').append('<div class="avatar avatar-xs" data-avatar="'+uid+'"><img src="'+photo+'" class="avatar-img rounded-circle"></div>');
		}
	} else {
		checkedUsers = checkedUsers.filter(function(item) { 
		    return item !== uid
		});
		$('[data-avatar='+uid+']').remove();	
	}

	if(checkedUsers.length == 0){
		$('[data-selected-users]').hide();
	} else {
		$('[data-selected-users-total]').text(checkedUsers.length);
		$('[data-selected-users]').fadeIn();
	}

	if(checkedUsers.length >= 15){	
		var more = checkedUsers.length - 15;
		$('[data-checkedUsers-photos]').append('<span data-more-users style="line-height:37px;padding-left:10px">+'+more+' users</span>');
	}

}
