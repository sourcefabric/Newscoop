{{ include file="_tpl/_html-head.tpl" }}

<body>

<div class="container">

{{ include file="_tpl/top.tpl" }}
  <div class="row">
    <div class="eightcol" id="contentarchive">

{{ list_issues constraints="number not 1" order="bypublishdate desc" }}
<h2 class="post-title">{{ $gimme->issue->name }}</h2>

{{ list_sections }}
  {{ list_articles }}
    {{ if $gimme->current_articles_list->at_beginning }}
<div class="block">
    <h3><a href="{{ uri options="section" }}">{{ $gimme->section->name }}</a></h3>
    {{ /if }}   

  <div class="post">
      <a title="{{ $gimme->article->name }}" href="{{ uri options="article" }}">
        <img src="{{ if $gimme->article->has_image(2) }}{{ uri options="image 2 width 134"}}{{ else }}{{ uri options="image 1 width 134"}}{{ /if }}" alt="{{ $gimme->article->image->description }}" class="thumbnail">
      </a>
    <h4 class="post-title"><a href="{{ uri options="article" }}" rel="bookmark" title="{{ $gimme->article->name }}">{{ include file="_tpl/article-icons.tpl" }}{{ $gimme->article->name }}</a></h4>
    <p class="post-details">Published on {{ $gimme->article->publish_date|camp_date_format:"%e %M %Y" }} by {{ $gimme->article->author->name }} in {{ $gimme->section->name }}</p>    
    <p>{{ $gimme->article->deck }}</p>
  </div><!--post-->

    {{ if $gimme->current_articles_list->at_end }}
</div><!-- class="block" -->
    {{ /if }}   
  {{ /list_articles }}
{{ /list_sections }}                   
<br clear="all" />
{{ /list_issues }}          


    </div><!--eightcol-->
    <div class="fourcol last">

{{ include file="_tpl/_banner300x250.tpl" }}

{{ include file="_tpl/sidebar-pages.tpl" }}

{{ include file="_tpl/sidebar-blogroll.tpl" }}
    </div><!--fourcol-->
  </div><!--row-->

  <div class="row" id="furtherarticles">
{{ include file="_tpl/article-mostread.tpl" }}
  </div><!--.row-->
    
{{ include file="_tpl/footer.tpl" }}
  
</div>

</body>
</html>