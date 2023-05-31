/* Script on ready
---------------------------------------------------*/	
jQuery(document).ready(function($) {

	

	jQuery(window).on('popstate', function(event) {

		if( jQuery('#resources-page').length ){
			console.log("move back.."+$(location).attr('href'));
			//location.reload(true);
			//window.location.href = $(location).attr('href');
			var url = $(location).attr('href'); //'mysite.com/page/2/?address&s=Search';
			var arr = url.split('/');
			var pageNum = arr[arr.indexOf('page') + 1];

			if( arr.indexOf('page') > 0 ){
				ajax_call(url, pageNum, 0);
			}else{
				ajax_call(url, 1, 0);
			}
		}

	});

	$(document).on('change','.fitem-ck-input', function() {
        
        var filters = $('.fitem-ck-input:checkbox:checked').map( function() {
            return this.value;
        }).get().join("|");
        $('.filters').val(filters);

        if( filters == "" ) {
            $('form.inner-sfilter button').addClass('disabled');
        }else{
        	$('form.inner-sfilter button').removeClass('disabled');
        }
        console.log('checkbox on change filters:'+filters);
    });

    // on result tag single clear
    $(document).on('click','.tag-btn', function(e){
    	event.preventDefault();
        var atag = $(this).attr('data-tag');
        $('input[value="'+atag+'"]').prop('checked', false);

        var filters = $('.fitem-ck-input:checkbox:checked').map( function() {
            return this.value;
        }).get().join("%7C");
        $('.filters').val(filters);
        
        setTimeout(function() {
            console.log('form being submit');
            $('form.inner-sfilter button').trigger('click');
        }, 200 );
    });

    // result tag on clear all
	$(document).on('click','.result-tag-list .clear-btn', function(event){
		event.preventDefault();
		console.log('result tag clear all');
		$('.fitem-ck-input:checkbox').removeAttr('checked');
		url = $('#resources-page').data("url");
		$('.filters').val('');
		ajax_call( url, 1 );
	});

	ajax_call('', $('#resources-page').data('current-page') );

	$(document).on( 'click', '.pagination-outer  a', function( e ) {
		e.preventDefault();
		$('.open-filter a.sfilter-close').trigger('click');
		pagelink = $(this).attr('href');
		pagenumb = $(this).data('page');
		if(!pagelink) return;

		$('html, body').animate({
			scrollTop: $("#resources-page").offset().top - 180
		}, 1000);

		ajax_call( pagelink, pagenumb );
		
	});

	$(document).on( 'submit', 'form.inner-sfilter', function (e) {
		e.preventDefault();

		$('.resources-most-recent').removeClass('open-filter');
		$('body').removeClass('mob-overlay');
		
		console.log('onsubmit ajax call will run');

		if( $(this).find('button.disabled').length && !$('.result-tag-list .tag-btn').length ) {
			return;
		}

		console.log('onsubmit ajax call now running');

		current = parseInt( $('.pagination li.active a').data('page') );
		url = $('#resources-page').data("url");
		filters = $('.fitem-ck-input:checkbox:checked').map( function() {
            return this.value;
        }).get().join("%7C");

        if( $.trim( $('.filters').val() ) == "" ) {
        	ajax_call( url, 1 );
        	return;
        }

		/*if( current > 1 ) {
			url = url + '/page/' + current + '/';
		}*/
		if( filters ) {
			console.log('filter was applied');
			url = url + '?filter=' + filters;
		}
		ajax_call( url, 1 );
	});

	function ajax_call( pagelink='', pagenumb = 1, isPushState = 1 ) {

		if( isPushState ) { // On move back from browser


			if (typeof (history.pushState) != "undefined") {
				var obj = { Title: '', Url: pagelink };
				history.pushState({}, "", pagelink);
			} else {
				console.log("Browser does not support HTML5.");
			}

			filter = $.trim( $('.filters').val() );
			console.log('filter 1='+filter);
			if( filter == "" && !$('.resources-most-recent').length ) {
				//filter = $('#resources-page').data("filter");
				if( window.location.href.indexOf('?filter') > -1 ) {
					filter = window.location.href.split("filter=").pop();
				}
			}
			console.log('filter 2='+filter);

		}else {
			if( window.location.href.indexOf('?filter') > -1 ) {
				filter = window.location.href.split("filter=").pop();
			}
			console.log('on move back - filter ='+filter);
		}

        filter = filter.replaceAll('%7C','|');
		filter = filter.replaceAll('%20',' ');
		filter = filter.replaceAll('+',' ');
		filter = filter.replaceAll('%26','&');
		filter = filter.replaceAll('%2F','/');
		filter = filter.replaceAll('%28','(');
		filter = filter.replaceAll('%29',')');

		$.ajax({
			url: disqo_webinars.ajaxurl,
			type: 'post',
			data: {
				action: 'ajax_disqo_webinars',
				pagelink: pagelink,
				pagenum: pagenumb,
				per_page: $('#resources-page').data("per-page"),
				title: $('#resources-page').data("title"),
				page_id: $('#resources-page').data("page-id"),
				featured_blogid: $('[data-elementor-type=wp-page] #webinar-featured-block').data("webinarid"),
				filter: filter,
			},
			beforeSend: function() {
				/*$('html, body').animate({
				scrollTop: $("#resources-page").offset().top - 180
				}, 1000);*/
				$('#resources-page .loader-over').show();
			},
			success: function( html ) {
				$('#resources-page .resources-results').html(html);
			},
			complete: function() {
				$('#resources-page .loader-over').hide();
			}
		});
	}
});