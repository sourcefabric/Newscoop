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
});























