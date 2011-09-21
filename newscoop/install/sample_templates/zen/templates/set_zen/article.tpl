{{ include file="_tpl/_html-head.tpl" }}

<body>

<div class="container">

{{ include file="_tpl/top.tpl" }}
    
{{ if $gimme->article->type_name == "news" }}  
  <div class="row">
    <div class="eightcol" id="content">

<h2 class="post-title">{{ $gimme->article->name }}</h2>
<div class="post-details">Published on {{ $gimme->article->publish_date|camp_date_format:"%e %M %Y" }} in <a href="{{ uri options="section" }}" title="View all posts in {{ $gimme->section->name }}" rel="category tag">{{ $gimme->section->name }}</a><br />
<script type="text/javascript">
{{ list_article_authors }}
$(document).ready(function(){
    $(".slidingDiv{{ $gimme->current_list->index }}").hide();
        $(".show_hide{{ $gimme->current_list->index }}").show();
        $('.show_hide{{ $gimme->current_list->index }}').click(function(){
        $(".slidingDiv{{ $gimme->current_list->index }}").slideToggle();
        });
});
{{ /list_article_authors }}
</script>

{{ list_article_authors }}
<a href="javascript:void(0)" class="show_hide{{ $gimme->current_list->index }}" title="More about {{ $gimme->author->name }}">{{ $gimme->author->name }}</a>
({{ $gimme->author->type|lower }}){{ if !$gimme->current_list->at_end }}, {{ /if }}
{{ /list_article_authors }}
{{ list_article_authors }}
  <div class="slidingDiv{{ $gimme->current_list->index }} authorbio">
      <img src="{{ $gimme->author->picture->imageurl }}" />
      <h4>{{ $gimme->author->name }}</h4>
      <span class="text">{{ $gimme->author->biography->text }}</span>
      <br/><a href="#" class="show_hide{{ $gimme->current_list->index }}">hide</a>
  </div> 
{{ /list_article_authors }}

{{ list_article_locations }}
{{ if $gimme->current_list->at_beginning }}
<br />
Location(s): 
{{ /if }}
  {{ if $gimme->location->enabled }}
    {{ $gimme->location->name }}{{ if $gimme->current_list->at_end }}{{ else }}, {{ /if }}
  {{ /if }}
{{ /list_article_locations }}

</div>
{{ if $gimme->article->content_accessible }}
{{* display comment errors also on top, so user sees them *}}
{{ include file="_tpl/article-comments-errorcheck.tpl" }}
  {{ include file="_tpl/article-ifvideo.tpl" }}
  <div class="full_text">
    <div class="articleinset">
      <div class="articleimage">
      {{ if $gimme->article->has_image(1) }}<img src="{{ url options="image 1 width 300" }}" alt="{{ $gimme->article->image1->description }}" class="thumbnail" />
      <p class="caption">{{ $gimme->article->image1->description }}</p>
      {{ elseif $gimme->article->has_image(2)}}<img src="{{ url options="image 2 width 300" }}" alt="{{ $gimme->article->image2->description }}" class="thumbnail" />
      <p class="caption">{{ $gimme->article->image2->description }}</p>
      {{ /if }}
      </div>
      {{ include file="_tpl/article-ifaudio.tpl" }}
    </div>
{{ $gimme->article->full_text }}
</div>
{{ else }}
  <div class="full_text">{{ $gimme->article->full_text|strip_tags|truncate:500 }}</div>
  <div class="message messageinformation messagesubscription">
    This article is accessible only to registered and logged in users!<br /><br />
{{ include file="_tpl/top-login.tpl" }}
  </div>
{{ /if }}
<br />
{{ include file="_tpl/article-social.tpl" }} 

    </div><!--eightcol-->
    <div class="fourcol last">

{{ if $gimme->article->has_map }}
  <div class="block">
    <h3>Locations</h3>
    {{ map show_locations_list="true" show_reset_link="Show initial Map" width="100%" height="250" }}
  </div>
{{ else }}
{{ include file="_tpl/_banner300x250.tpl" }}
{{ /if }}
    </div><!--fourcol-->
  </div><!--row-->

{{ if $gimme->article->content_accessible }}
  <div class="row">
    <div class="eightcol" id="content">
{{ include file="_tpl/article-gallery.tpl" }}
{{ include file="_tpl/article-comments.tpl" }}
    </div><!--eightcol-->
    <div class="fourcol last">
{{ include file="_tpl/sidebar-related.tpl" }}
{{* include file="_tpl/sidebar-pages.tpl" *}}
{{* include file="_tpl/sidebar-blogroll.tpl" *}}
    </div><!--fourcol-->
  </div><!--row-->
{{ /if }}

{{ else }}{{* article type not news *}}
  <div class="row">
    <div class="twelvecol" id="content">
        <h2 class="page-title">{{ $gimme->article->name }}</h2>
        <div class="full_text full_text_fullwidth">{{ $gimme->article->full_text }}</div>
        <br />
  {{ include file="_tpl/article-comments.tpl" }}
    </div><!--twelvecol-->
  </div><!--row-->
{{ /if }}

  <div class="row" id="furtherarticles">
{{ include file="_tpl/article-mostread.tpl" }}
  </div><!--.row-->
    
{{ include file="_tpl/footer.tpl" }}
  
</div>

</body>
</html>