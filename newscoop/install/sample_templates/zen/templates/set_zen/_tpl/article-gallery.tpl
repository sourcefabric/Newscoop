
    {{ list_article_images }}
    {{if $gimme->current_list->count > 2}}
      {{if $gimme->current_list->at_beginning}}
<div class="block">
  <script type="text/javascript" src="{{ url static_file='_js/jquery.mousewheel-3.0.4.pack.js' }}"></script>
  <script type="text/javascript" src="{{ url static_file='_js/jquery.fancybox-1.3.4.pack.js' }}"></script>      
  <script type="text/javascript" src="{{ url static_file='_js/jquery.easing-1.3.pack.js' }}"></script>  
  <link rel="stylesheet" type="text/css" href="{{ url static_file='_css/fancybox/fancybox.css' }}" media="screen" />
    <script type="text/javascript">
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
    </script>
    <div class="image-gallery-container">
      <h3>Article Gallery</h3>
      {{/if}}
      {{if $gimme->image->article_index > 2}}
        <a href="{{uri options="image"}}" rel="gallery" class="gallery_thumbnail" title="{{$gimme->image->description|escape}}">
          <img src="{{uri options="image height 120"}}" alt="{{$gimme->image->description|escape}}" height="120" />
        </a>
      {{/if}}
      {{if $gimme->current_list->at_end}}
    </div>
</div>
      {{/if}}
    {{/if}}
    {{/list_article_images}}