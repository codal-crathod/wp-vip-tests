/* Join the team section */

jQuery(document).ready(function ($) {
	$('.m-section-sidebar .tab-btn').on('click', function(){
		$('.no-job-data').hide();
		$('.location-card-content-links').show();
		$('.location-card').show();
		$('#job_key_search').val('');
		var tab = $(this).data('tab');
		$('.m-section-sidebar .tab-btn').removeClass('opened-on-load');
		$(this).addClass('opened-on-load');
		$('.location-card').removeClass('active-card');
		if(tab == '#tab-all'){
			$('.tab').addClass('tab-active');
		} else {
			$('.tab').removeClass('tab-active');
			$(tab).addClass('tab-active');
		}
	});
	$('.acc-title').on('click', function(){
		
		var acc = $(this).attr('id');
		$(this).parent().toggleClass('active-card');
	});
	$('.acc-title').on('keypress', function(e){
		var keycode = (e.keyCode ? e.keyCode : e.which);
		if(keycode == 13){
			var acc = $(this).attr('id');
			$(this).parent().toggleClass('active-card');
		}
	});
	$('#job_key_search').on('keypress', function(e){
		var word = $(this).val().toLowerCase();
		if(word == ''){
			var keycode = (e.keyCode ? e.keyCode : e.which);
			if(keycode == 32){
				return false;
			}
		}
	});
	$('#job_key_search').on('keypress', function(e){
		var word = $(this).val().toLowerCase();
		var keycode = (e.keyCode ? e.keyCode : e.which);
		if(keycode == 32 && word == ''){
			return false;
		}
		if(keycode == 46 || keycode == 52 || keycode == 54 || keycode == 56){
			return false;
		}
	});

	$('#job_key_search').on('keyup', function(e){
		$('.m-section-sidebar .tab-btn').removeClass('opened-on-load');
		$('.full-list').addClass('opened-on-load');
		$('.tab').addClass('tab-active');
		var word = $(this).val().toLowerCase();
		if(word == ' '){
			return false;
		}
		if(word != '' && word != ' '){
			$('.location-card-content').each(function( index ) {
				var $this = $(this);
				var LiCount = 0;
				$this.children(".location-card-content-links").each(function(){
					var res = $(this).find('a').text().toLowerCase();
					if (res.search(word) !== -1) {
						LiCount++;
						$(this).parent().parent().parent().show();
						$(this).parent().parent().parent().addClass('active-card');
						$(this).parent().parent().parent().parent().parent().addClass('tab-active');
						$(this).show();
					} else {
						$(this).hide();
						if(LiCount == 0){
							$(this).parent().parent().parent().hide();
							$(this).parent().parent().parent().removeClass('active-card');
						}
					}
				});
			});
			$('.accordion').each(function( index ) {
				if($(this).children(':visible').length == 0) {
					$(this).parent().removeClass('tab-active');
				} else {

				}
			});
			if($('.tabs-container').children(':visible').length == 0) {
				$('.no-job-data').show();
			} else {
				$('.no-job-data').hide();
			}
		} else {
			$('.location-card-content-links').show();
			$('.tab').addClass('tab-active');
			$('.location-card').show();
			$('.location-card').removeClass('active-card');
		}
	});
	
});