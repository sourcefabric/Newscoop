<style>
#article-gallery {
  border-top: 1px solid #999;
  border-bottom: 1px solid #999; 
  padding: 10px 0;
  margin: 10px 0; 
}

div.gallery-item {
  width: 64px;
  height: 64px;
  float: left;
  margin: 16px 8px;
  text-align: center;
  vertical-align: middle;
}

#article-gallery:after {
  content: "."; 
  display: block; 
  height: 0; 
  clear: both; 
  visibility: hidden;
} 
</style>

<script type="text/JavaScript">
$(document).ready(function() {
  $("a.grouped_elements").fancybox({
    'transitionIn'  : 'elastic',
    'transitionOut' : 'elastic',
    'speedIn'   : 600, 
    'speedOut'    : 200, 
    'overlayShow' : true
  });
});
</script>


          {{ list_article_images }}
          {{ if $gimme->current_list->count gt 1 }}
          {{ if $gimme->current_list->at_beginning }}
          <div id="article-gallery">          
          <h4>{{ if $gimme->language->name == "English" }}Article gallery{{ else }}Mini galería{{ /if }}:</h4>
          {{ /if }}
              <div class="gallery-item">
                  <a class="grouped_elements" href="{{ $gimme->article->image->imageurl }}" rel="group"><img alt="{{ $gimme->article->image->description }}" src="{{ $gimme->article->image->thumbnailurl }}" /></a>
              </div><!-- /.gallery-item -->
          {{ if $gimme->current_list->at_end }}
          </div><!-- /#article-gallery --> 
          {{ /if }}
          {{ /if }}
          {{ /list_article_images }}
   