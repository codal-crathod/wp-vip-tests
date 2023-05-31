/* Script on ready
---------------------------------------------------*/	
jQuery(document).ready(function($) {

	var getUrlParameter = function getUrlParameter(sParam) {
		var sPageURL = window.location.search.substring(1),
		sURLVariables = sPageURL.split('&'),
		sParameterName,
		i;

		for (i = 0; i < sURLVariables.length; i++) {
			sParameterName = sURLVariables[i].split('=');

			if (sParameterName[0] === sParam) {
				return sParameterName[1] === undefined ? true : decodeURIComponent(sParameterName[1]);
			}
		}
		return false;
	};

	jQuery(window).on('popstate', function(event) {

		if( jQuery('#search-page').length ){

			console.log("move back.."+$(location).attr('href'));

			var url = $(location).attr('href'); //'mysite.com/?page=2&s=Search';
			var pageNum = getUrlParameter('page');

			if( pageNum > 0 ){
				ajax_call(url, pageNum, 0);
			}else{
				ajax_call(url, 1, 0);
			}
		}

	});

	ajax_call('', $('#search-page').data('current-page') );

	$(document).on( 'click', '.pagination-outer  a', function( e ) {
		e.preventDefault();
		pagelink = $(this).attr('href');
		pagenumb = $(this).data('page');
		if(!pagelink) return;

		/*$('html, body').animate({
			scrollTop: $("#search-page").offset().top - 180
		}, 1000);*/

		ajax_call( pagelink, pagenumb );
		
	});


	function ajax_call( pagelink='', pagenumb = 1, isPushState = 1 ) {

		if( isPushState ) {
			if (typeof (history.pushState) != "undefined") {
				var obj = { Title: '', Url: pagelink };
				history.pushState({}, "", pagelink);
			} else {
				console.log("Browser does not support HTML5.");
			}
		}

		$.ajax({
			url: disqo_search.ajaxurl,
			type: 'post',
			data: {
				action: 'ajax_disqo_search',
				pagelink: pagelink,
				pagenum: pagenumb,
				stack: disqo_search.searchstack,
				s: disqo_search.s
				//page_id: $('#search-page').data("page-id"),
			},
			beforeSend: function() {
				$('#search-page .loader-over').show();
			},
			success: function( html ) {
				$('#search-page .search-results').html(html);
			},
			complete: function() {
				$('#search-page .loader-over').hide();
				$('html, body').animate({
				scrollTop: $("#search-page").offset().top - 180
				}, 1000);
			}
		});
	}
});