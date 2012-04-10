$(document).ready(function() {
	
	/* Input focus/blur */

	$('input[type="text"], input[type="password"], textarea').focus(function() {
		if(this.value==this.defaultValue)this.value='';
	});

	$('input[type="text"], input[type="password"], textarea').blur(function() {
		if(this.value=='')this.value=this.defaultValue;								  
	});
	
	// User list slider
	$('.slider').jcarousel({
		visble: 1,
		scroll: 1
	});
	
	
	if( $(window).width() < 660) {
		
		
		var expandCounter = 0;
		$('.top-menu ul li a').click(function(){
			if (expandCounter == 0) {
				$(this).addClass('active');
				$(this).next('.sub').slideDown('fast');
				expandCounter = 1;
			} else if ($(this).hasClass('active')) {
				$('.top-menu ul li a').removeClass('active');
				$('.sub').slideUp('fast');
				expandCounter = 0;
			} else {
				$('.top-menu ul li a').removeClass('active');
				$('.sub').slideUp('fast');
				$(this).addClass('active');
				$(this).next('.sub').slideDown('fast');
				expandCounter = 1;
			};
			return false;
		});
		
		$('a.cat-trigger').click(function(){
			$('.top-menu ul li a').removeClass('active');
			$('.top-menu ul li .sub').slideUp();
			$(this).next('ul').slideToggle('fast');
			expandCounter = 0;
		});
		
		$('.search-box a.search-trigger').click(function(){
			$('.top-menu ul li a').removeClass('active');
			$('.top-menu ul li .sub').slideUp();
			$(this).toggleClass('active');
			$(this).next('div').slideToggle('fast');
			expandCounter = 0;
		});
		
	} else {
		
		// Man Nav
		$('.top-menu ul li').hover(function(){
			$(this).children('a').addClass('active');
			$(this).children('.sub').slideDown('fast');
		},
		function(){
			$(this).children('a').removeClass('active');
			$(this).children('.sub').slideUp('fast');
		});
	}
	
	
	
	
	// Article Details
	$('article').hover(function(){
		$(this).find('.info').slideDown('fast');
	},
	function(){
		$(this).find('.info').slideUp('fast');
	});


});