(function($) {
	var source = theme_source();
	$(function() {
		var	$window = $(window),
			$body = $('body'),
			$header = $('#header'),
			$banner = $('#banner');
			$("[data-login]").on('click', function() {
				if($('#login-form').is(':visible')) {
					$('#login-form').hide();
					$('#register-form').show();
					$('#recover-form').hide();
					$(this).text(site_lang[1].text);
					$('.right .box').css('height','585px');
				} else {
					$('#login-form').fadeIn();
					$('#register-form').hide();
					$('#recover-form').hide();
					$('html,body').scrollTop(0);
					$(this).text(site_lang[8].text);
					$('.right .box').css('height','445px');
				}
			});
			$("#recover-pwd").on('click', function() {
					$('#login-form').hide();
					$('#register-form').hide();
					$('#recover-form').fadeIn();
			});			
			$("#go-to-register-form").on('click', function() {
				$('#login-form').hide();
				$('#recover-form').hide();
				$('#register-form').fadeIn();
				$('.right .box').css('height','585px');
			});	
			
			$("[data-lang]").click(function() {
				var lang = $(this).attr('data-lang');
				window.location.href = "index.php?page=index&lang="+lang;
			});


	});
})(jQuery);