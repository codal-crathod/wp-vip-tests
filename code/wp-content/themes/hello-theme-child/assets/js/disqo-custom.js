/**
 * Equal Height JS
 */
jQuery(document).ready(function ($) {
	var equalHeight = function () {
		var maxHeight = 0;
		jQuery('.card-text').each(function () {
			if (jQuery(this).height() > maxHeight) {
			maxHeight = jQuery(this).height();
			}
		});
		jQuery('.card-text').css('height', maxHeight);
		var maxHeight = 0;
		jQuery('.card-heading').each(function () {
			if (jQuery(this).height() > maxHeight) {
			maxHeight = jQuery(this).height();
			}
		});
		jQuery('.card-heading').css('height', maxHeight);
		var maxHeight = 0;
		jQuery('.icon-box').each(function () {
			if (jQuery(this).height() > maxHeight) {
			maxHeight = jQuery(this).height();
			}
		});
		jQuery('.icon-box .elementor-widget-container').css('height', maxHeight);
	};
	if(jQuery(window).width() > 767)
    {
		equalHeight();
    }
	jQuery(".jet-menu-item-has-children a").on('mouseenter',function(){
			jQuery('body').addClass("menu-grey-overlay");		
		
	});
	jQuery("ul.jet-menu").on('mouseleave',function(){
			jQuery('body').removeClass("menu-grey-overlay");
	});
	
	jQuery('.our-people-slider').slick({
		dots: true,
		infinite: true,
		speed: 300,
		draggable: true,
		slidesToShow: 1,
	   	variableWidth: false,
	   	appendDots: $(".slide-m-dots"),
      	prevArrow: $(".slide-m-prev"),
      	nextArrow: $(".slide-m-next"),
	   	responsive: [{
			breakpoint: 1920,
				settings: {
					variableWidth: false
				}
			},{
			breakpoint: 991,
				settings: {
					variableWidth: true
				}
		}],

	});
	jQuery('.investors-slider').slick({
		dots: true,
		infinite: true,
		speed: 300,
		draggable: true,
		slidesToShow: 1,
	  	variableWidth: false,
	  	appendDots: $(".slide-m-dots"),
	    prevArrow: $(".slide-m-prev"),
	    nextArrow: $(".slide-m-next"),
	   	responsive: [{
			breakpoint: 1920,
				settings: {
					variableWidth: false
				}
			},{
			breakpoint: 991,
				settings: {
					variableWidth: true
				}
		}], 
	});

	
	  function closeModel(){
        $('.error').remove();
         $('.model').removeClass('open');
         jQuery('body').removeClass("body-overflow");	
    }
    function closeModelSelf(thismodel){
    	jQuery('body').removeClass("body-overflow");
        $('.error').remove();
        if(thismodel !== undefined)
        {
            $(thismodel).removeClass('open');
            
        }
    }
   
    function openModel(selector){
        selector.addClass('open');
        jQuery('body').toggleClass("body-overflow");	
    }
    $('body').on('click','[data-function=model]',function(e){
        e.preventDefault();
        var target = $(this).data('target');
        
        if( target == '#linkedin-popup' ) {
            var imghtml = $(this).parent('.elementor-widget-wrap').find('.elementor-widget-theme-post-featured-image .elementor-widget-container a').html();
            imghtml = imghtml.replace('loading="lazy"', "");
            $( target).find('.model-body .team-details figure').html(imghtml);
            $( target).find('.model-body .team-details .name-block h3').text($(this).parent('.elementor-widget-wrap').find('.elementor-widget-theme-post-title .elementor-heading-title').text());
            $( target).find('.model-body .team-details .name-block .lk-position').text($(this).parent('.elementor-widget-wrap').find('.elementor-widget-post-info [team-position]').text());
            $( target).find('.model-body .team-details .name-block .lk-button').html($(this).parent('.elementor-widget-wrap').find('.elementor-widget-button .elementor-button-wrapper').html());
            $( target).find('.model-body .team-details .team-caption').html($(this).parent('.elementor-widget-wrap').find('.elementor-widget-theme-post-content .elementor-widget-container').html());
        }

        $( target).addClass('open');
        jQuery('body').toggleClass("body-overflow");
       
    })
    $("body").on('click','.model-close', function(e) {
            e.preventDefault();
           closeModel();
          	// jQuery('body').toggleClass("body-overflow");
    })
    $("body").on('click','.model-close-self', function(e) {
            e.preventDefault();
            var thismodel=$(this).closest('.model');
           closeModelSelf(thismodel);
           //jQuery('body').toggleClass("body-overflow");

    })
    $('.model').on('click', function(e) {
        if (!$(e.target).hasClass('model-contain') && $(e.target).parents('.model-contain').length == 0) {
            $('.model').removeClass('open');
            $('.error').remove();
            $('.select-clone-list').removeClass('open');
            $('.model-close').trigger('click');
            jQuery('body').removeClass("screen-overflow");	
        }
    })
    window.document.onkeydown = function(e)
    {
        if (!e) e = event;
        if (e.keyCode == 27) {
            $('.error').remove();
            $('.model').removeClass('open');
            $('.select-clone-list').removeClass('open')
        }
    }
});

/**
 * Contact us
 */
jQuery(document).ready(function( $ ){
    $('#contact_main_form form').on('submit', function(event){
		$(this).append('<div class="overlay"><div class="loader"></div></div>');
		var selection = $('#contact_main_form form #form-field-name').val().replace(" " , "-").toLowerCase();
		window.location.href = '/contact-' + selection;
		return false;
	});
	$( "#form-field-name" ).select2({minimumResultsForSearch: -1});
	$(".disqo_main_form #contact_main_form .select2-selection__rendered").attr("title","");	
	$('.disqo_main_form #contact_main_form #form-field-name').on('change', function(){
		$(".select2-selection__rendered").attr("title","");	
	});
	if( $('.contact_us_link').length ){
		var contact_us_link = $('.contact_us_link').text();
		$('.header_contact_button a').attr('href', contact_us_link);
	}
});

/**
 * Search form
 */
jQuery(document).ready(function () {
		
	jQuery(".search-form").on('click',function(e){
			e.preventDefault();
			jQuery(".search-form-input").removeClass("hide");
			jQuery(".search-form-input").slideToggle();
			
			jQuery('body').toggleClass("screen-overflow");	
			jQuery(".search-form-enable").toggleClass("hide");
			jQuery(".search-form").toggleClass("hide");
	});
	jQuery(".search-form-enable").on('click',function(e){
			e.preventDefault();
			jQuery('body').toggleClass("screen-overflow");
			jQuery('.elementor-search-form').find("input[type=search]").val("");
			jQuery(".search-form-input").slideToggle();
			jQuery(".search-form").toggleClass("hide");
			jQuery(".search-form-enable").toggleClass("hide");
		});
	jQuery(".jet-mobile-menu__toggle").on('click',function(e){
		e.preventDefault();
		setTimeout(myGreeting, 100);
		function myGreeting() {
			 jQuery( ".jet-mobile-menu__container-inner .login-sticky" ).remove();
		  jQuery( ".login-sticky .elementor-button-wrapper" ).clone();
			console.log(jQuery( ".login-sticky .elementor-button-wrapper" ).clone());
			jQuery('.jet-mobile-menu__container-inner').append(jQuery( ".login-sticky" ).clone());
			jQuery( ".jet-mobile-menu__container-inner .login-sticky" ).removeClass("hide");			
		}		
	});		
});
/**
 * Contact Email Validation
 */
/*const emailInput = document.querySelector("#form-field-field_881c52e");
emailInput.addEventListener("input", function(e) {
    const emailInputValue = e.currentTarget.value;

    //if( /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6}$/.test(emailInputValue) != true) {
    if (/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/.test(emailInputValue)  != true) {
        e.target.setCustomValidity('Yes.');
    } else {
      e.preventDefault();
    }
 }); */

/**
 * Filter toggle
 */
jQuery(document).ready(function($) {
	
	$(document).on('click','.resources-most-recent .is_light_secondary a', function(event){
		event.preventDefault();
		$('.resources-most-recent').toggleClass('open-filter');
		$('body').toggleClass('mob-overlay');

		$('.fitem-ck-input:checkbox').each(function(){
			
			var default_sel = $(this).attr('default');
			if( typeof default_sel == 'undefined' ){
				$(this).removeAttr('checked');
			}else{
				$(this).prop('checked', true);
			}
		});
		
		set_filter_hidden();
	});
	$(document).on('click','.sfilter-close', function(event){
		event.preventDefault();
		$('.resources-most-recent').toggleClass('open-filter');
		$('body').toggleClass('mob-overlay');
		
		$('.fitem-ck-input:checkbox').each(function(){
			
			var default_sel = $(this).attr('default');
			if( typeof default_sel == 'undefined' ){
				$(this).removeAttr('checked');
			} 
		});
		
		set_filter_hidden();
	});
	$(document).on('click','.sfilter-footer .clear-btn', function(event){
		event.preventDefault();
		console.log('clear all');
		$('.fitem-ck-input:checkbox').removeAttr('checked');
		$('form.inner-sfilter button').addClass('disabled');
		set_filter_hidden();
	}); 
	
	
	$(document).on('click','.btn-tgl', function(event){
		event.preventDefault();
		$(this).parent().toggleClass('open-menu');
		$(this).next('ul').slideToggle('slow');
	});
	
	function set_filter_hidden() {
			var filters = $('input:checkbox:checked').map( function() {
				return this.value;
			}).get().join("%7C");
			$('.filters').val(filters);
			if( $.trim( $('.filters').val() ) == "" ) {
                $('form.inner-sfilter button').removeClass('disabled');
            }else{
				$('form.inner-sfilter button').addClass('disabled');
			}
			console.log('filters==='+filters);
	}

});

/**
 * Animation tab
 */
jQuery(document).ready(function($) {
	
	// defaults
	var defaults = { animation:"fade", animationSpeed:500, autoHeight:true, slideDirection:"fade"},
		options;
		
	// tabs function
	$.fn.aniTabs = function(params){
		var options = $.extend({}, defaults, params);
		
		// click event
		$(this).click(function(e){
			e.preventDefault();
			if($(this).closest(".mtabs-item.active").length<1){
				// if fade (set transition speed)
				if(options.animation=="fade"){
					$(this).closest(".mtabs").find(".mtabs-wrap:first .mtabs-content").css({"-webkit-transition-duration":(options.animationSpeed/1000)+"s", "-ms-transition-duration":(options.animationSpeed/1000)+"s", "transition-duration":(options.animationSpeed/1000)+"s"});
				}
				// if slide (set animation speed)
				if(options.animation=="slide"){
					 $(this).closest(".mtabs").find(".mtabs-wrap:first .mtabs-content").addClass("slide").css({"-webkit-animation-duration":(options.animationSpeed/1000)+"s", "-ms-animation-duration":(options.animationSpeed/1000)+"s", "animation-duration":(options.animationSpeed/1000)+"s"});
				}	
											
				// set auto height
				if(options.autoHeight){
					$(this).closest(".mtabs").find(".mtabs-wrap:first").css({"-webkit-transition-duration":(options.animationSpeed/1000)+"s", "-ms-transition-duration":(options.animationSpeed/1000)+"s", "transition-duration":(options.animationSpeed/1000)+"s"});
				}							
				// base functionality
				var currentHref = $(this).attr("href"); // get current href
				$(this).closest(".mtabs-item").siblings(".mtabs-item").removeClass("active"); // remove .active in closest nav
				$(this).closest(".mtabs-item").addClass("active"); // add .active to closest item
				var oldHeight = $(currentHref).siblings(".mtabs-content.active").height(); // get old height
				$(currentHref).closest(".mtabs-wrap").css("height",oldHeight+"px"); // set old height		
				// if slide (animation out)
				if(options.animation=="slide"){
					// if direction is left
					if(options.slideDirection=="left"){
						$(this).closest(".mtabs").find(".mtabs-wrap:first .mtabs-content").removeClass("slideOutLeft slideInRight moved");					
						$(currentHref).siblings(".mtabs-content.active").addClass("slideOutLeft moved");
					}
					// if direction is right
					if(options.slideDirection=="right"){
						$(this).closest(".mtabs").find(".mtabs-wrap:first .mtabs-content").removeClass("slideOutRight slideInLeft moved");					
						$(currentHref).siblings(".mtabs-content.active").addClass("slideOutRight moved");
					}					
				}								
				$(currentHref).addClass("active").siblings(".mtabs-content").removeClass("active"); // change tab
				var newHeight = $(currentHref).height(); // get new height	
				$(currentHref).closest(".mtabs-wrap").css("height",newHeight+"px"); // set new height
				// if slide (animation in)
				if(options.animation=="slide"){
					// if direction is left
					if(options.slideDirection=="left"){					
						$(currentHref).addClass("slideInRight");
					}
					// if direction is right
					if(options.slideDirection=="right"){					
						$(currentHref).addClass("slideInLeft");
					}					
				}				
				// some fixes when animation's done
				setTimeout(function(){
					// set height:auto
					$(currentHref).closest(".mtabs-wrap").css("height","auto");
					// if slide (remove classes)
					if(options.animation=="slide"){
						 $(currentHref).siblings(".mtabs-content").removeClass("slideOutLeft slideOutRight moved");
					}						
				}, options.animationSpeed)					
			 
			}
		})
		return this;
	};
	
	
	$(".mtabs .mtabs-link").aniTabs();  
	
   	$('.only-mob .mtabs-link').click(function(e){ 

	   $('html, body').animate({scrollTop: $('.only-mob').offset().top -50},300);
        if($('.mtabs-content').is(':animated')) return;
	    e.preventDefault();
        if($(this).parent('.main-tabs').hasClass('active')){
            $(this).parent('.main-tabs').removeClass('active');
            $(this).next('.mtabs-content').slideUp('slow');
        }else{
            $('.only-mob .mtabs-content').slideUp()
            $('.main-tabs').removeClass('active');
            $(this).parent('.main-tabs').addClass('active');
            $(this).next('.mtabs-content').slideDown('slow'); 
        }
    })
});

/* Pricing page tabination */


jQuery(document).ready(function( $ ){
	//jQuery('.mtabs-link.first-tab').trigger('click');
	$('.pricing_dropdown .elementor-field-type-submit').remove();
    $('.pricing_tabs_ul li a').on('click', function(event){
		event.preventDefault();
		var tab_id = $(this).data('tab');
		window.location.hash = tab_id;
	});
	$('#form-field-pricing_so').on('change', function(event){
		event.preventDefault();
		var tab_id = $(this).val();
		window.location.hash = tab_id;
	});
	var tab_hash = window.location.hash.substr(1);
	if (tab_hash !== '')
	{
		if ($("." + tab_hash).length > 0) {
			active_hash(tab_hash);
		} else {
			no_hash();
		}
	}
	$(window).on('hashchange', function(e){
		var tab_hash = window.location.hash.substr(1);
		if (tab_hash !== '')
		{
			if ($("." + tab_hash).length > 0) {
				active_hash(tab_hash);
			} else {
				no_hash();
			}
		} else {
			no_hash();
		}
	});
	function active_hash(tab_hash){
		$('.pricing_tabs_ul li').removeClass('active_tab');
		$('.pricing_tabs_ul li a[data-tab = '+tab_hash+']').parent().addClass('active_tab');
		$('#form-field-pricing_so').val(tab_hash);
		$('.pricing_tab_content').removeClass('active_pricing');
		$('.' + tab_hash).addClass('active_pricing', 2000, myCallback);
	}
	function no_hash(){
		$('.pricing_tabs_ul li').removeClass('active_tab');
		$('.pricing_tab_content').removeClass('active_pricing');
		$('#form-field-pricing_so').val('es_pricing');
		$('.pricing_tabs_ul li:first').addClass('active_tab');
		$('.pricing_tab_content:first').addClass('active_pricing', 2000, myCallback);
	}
	function myCallback() {
		setTimeout(function () {
			$(".pricing_tab_content").removeClass("active_pricing");
		}, 3000);
	}
});

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