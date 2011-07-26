 
<body> 

<div data-role="page">
{{ include file="_tpl/jqm_header-page.tpl" }}

  <div data-role="content"> 
    <ul data-role="listview">
      <li data-role="list-divider">{{ $gimme->section->name }}</li>
{{ list_articles length="9" order="bypublishdate desc" ignore_issue="true" constraints="type is news" }}
      <li>{{ strip }}
        <a href="{{ uri options="article" }}">
          {{* if $gimme->current_list->index == 1 *}} 
          <img src="{{ if $gimme->article->has_image(2) }}{{ uri options="image 2 width 80 height 80"}}{{ else }}{{ uri options="image 1 width 80 height 80"}}{{ /if }}" alt="{{ $gimme->article->image->description }}">
          {{* /if *}}
          <h3>{{* if ! $gimme->article->content_accessible }}* {{ /if *}}
          {{ $gimme->article->name }}</h3>
          <p>{{ $gimme->article->deck }}</p>
        </a>
      {{ /strip }}</li>        
      {{ if $gimme->current_list->at_end }}
        {{ if $gimme->current_list->has_previous_elements }}<li><a href="{{ uripath options="section" }}?{{ urlparameters options="previous_items" }}" data-rel="back">&laquo; Newer Entries</a></li>{{ /if }}
        {{ if $gimme->current_list->has_next_elements }}<li><a href="{{ uripath options="section" }}?{{ urlparameters options="next_items" }}">Older Entries &raquo;</a></li>{{ /if }}
        <li><a href="http://{{ $gimme->publication->site }}">Home</a></li>
      {{ /if }}                
{{ /list_articles }}
    </ul>

<br clear="all" />

<!-- collapsible search form -->
{{ include file="_tpl/jqm_search-form-collapsible.tpl" }}

<!-- navigation: all sections -->
{{ include file="_tpl/jqm_nav-sections.tpl" }}

  </div><!-- /content -->

{{ include file="_tpl/jqm_footer.tpl" }}
</div><!-- /page -->

<!--/body>
</html-->
