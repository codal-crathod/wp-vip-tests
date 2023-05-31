/* Script on ready
---------------------------------------------------*/	
jQuery(document).ready(function($) {

	$.ajax({
		url: disqo_related.ajaxurl,
		type: 'post',
		data: {
			action: 'ajax_disqo_related',
			by_tags: $('#disqo-related-block').data("by-tags"),
			ex_tags: $('#disqo-related-block').data("ex-tags"),
			selected: $('#disqo-related-block').data("selected"),
		},
		beforeSend: function() {
			$('#disqo-related-block .loader-over').show();
		},
		success: function( html ) {
			$('#disqo-related-block .related-results').html(html);
		},
		complete: function() {
			$('#disqo-related-block .loader-over').hide();
		}
	});

});