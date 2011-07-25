{{ include file="_tpl/_html-head.tpl" }}

<body>

<div class="container">

{{ include file="_tpl/top.tpl" }}
  <div class="row">
    <div class="eightcol" id="contentlist">

<h2 class="post-title">Search results</h2>
                                
{{ list_search_results length="9" order="bypublishdate desc" constraints="type is news" }}

  <div class="post">
      <a title="{{ $gimme->article->name }}" href="{{ uri options="article" }}">
        <img src="{{ if $gimme->article->has_image(2) }}{{ uri options="image 2 width 134"}}{{ else }}{{ uri options="image 1 width 134"}}{{ /if }}" alt="{{ $gimme->article->image->description }}" class="thumbnail">
      </a>
    <h3 class="post-title"><a href="{{ uri options="article" }}" rel="bookmark" title="{{ $gimme->article->name }}">{{ include file="_tpl/article-icons.tpl" }}{{ $gimme->article->name }}</a></h3>
    <p class="post-details">Published on {{ $gimme->article->publish_date|camp_date_format:"%e %M %Y" }} by {{ $gimme->article->author->name }} in {{ $gimme->section->name }}</p>    
    <p>{{ $gimme->article->deck }}</p>
  </div><!--post-->

{{ if $gimme->current_list->at_end }}                                                 

<div class="more_entries">
  <div class="alignleft">{{ if $gimme->current_list->has_previous_elements }}<a href="{{ uripath options="template search.tpl" }}?{{ urlparameters options="previous_items" }} ">&laquo; Newer Entries</a>{{ /if }}</div>
  <div class="alignright">{{ if $gimme->current_list->has_next_elements }}<a href="{{ uripath options="template search.tpl" }}?{{ urlparameters options="next_items" }}">Older Entries &raquo;</a>{{ /if }}</div>
</div><!--more_entries-->
{{ /if }}                
{{ /list_search_results }}                   

{{ if $gimme->prev_list_empty }}
<div class="message messagesearch messageerror">No results found</div>
{{ /if }}           

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