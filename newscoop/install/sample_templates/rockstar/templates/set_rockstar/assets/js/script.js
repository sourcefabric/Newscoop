$(document).ready(function(){
    $("a.gallery_thumbnail").fancybox({
      type: 'image',
      titlePosition: 'inside',
      transitionIn: 'none',
      transitionOut: 'none',
      centerOnScroll: 'true'
    });
    $("a.gallery_thumbnail").live("mouseenter",function(){$(this).animate({opacity:1},200);});
    $("a.gallery_thumbnail").live("mouseleave",function(){$(this).animate({opacity:0.8},200);});

	/* Poll Ajaxified
	-------------------------------------------------------*/
	$('#poll-button').click(function(){
		$.post($('form[name=debate]').attr("action"),$('form[name=debate]').serialize(),function(data){$('#polldiv').html(data);});
		return false;
	}); 
});
