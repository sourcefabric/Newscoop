{{ include file="_tpl/_html-head.tpl" }}
<body> 

<div data-role="page">
{{ include file="_tpl/jqm_header-page.tpl" }}
  <div data-role="content">
    <ul data-role="listview">
      <li data-role="list-divider">Search results</li>
{{ list_search_results length="6" order="bypublishdate desc" constraints="type is news" }}
      <li>{{ strip }}
        <a href="{{ uri options="article" }}">
          {{* if $gimme->current_list->index == 1 *}} 
          <img style="background: {{ if $gimme->article->has_image(2) }}url({{uri options="image 2 width 150 height 150"}}){{ else }}url({{uri options="image 1"}} width 150 height 150"){{ /if }} no-repeat center center; width: 80px; height: 80px"  />
          {{* /if *}}
          <h3>{{* if ! $gimme->article->content_accessible }}* {{ /if *}}
          {{ $gimme->article->name }}</h3>
          <p>{{ $gimme->article->deck }}</p>
        </a>
      {{ /strip }}</li>        
      {{ if $gimme->current_list->at_end }}
        {{ if $gimme->current_list->has_previous_elements }}<li><a href="{{ uripath options="template search.tpl" }}?{{ urlparameters options="previous_items" }}" data-rel="back">&laquo; Newer Entries</a></li>{{ /if }}
        {{ if $gimme->current_list->has_next_elements }}<li><a href="{{ uripath options="template search.tpl" }}?{{ urlparameters options="next_items" }}">Older Entries &raquo;</a></li>{{ /if }}
      {{ /if }}                
{{ /list_search_results }}       
    </ul>

    <br clear="all" />
                  
{{ if $gimme->prev_list_empty }}
    <h2>
      No results found...
    </h2>
{{ /if }}  

    <div data-role="collapsible" data-collapsed="true">
      <h3>Search</h3>
      <div class="ui-body ui-body-c">
      {{ search_form template="jqm_search.tpl" submit_button="Search" button_html_code="data-role=\"button\" data-icon=\"search\" data-iconpos=\"notext\"" }}
        {{ camp_edit object="search" attribute="keywords" html_code="value=\"Search\" id=\"s\"" }}
        <!--button class="replace" type="submit" name="submit"></button-->
      {{ /search_form }}
      </div>
    </div><!-- collapsible -->

  </div><!--content-->
{{ include file="_tpl/jqm_footer.tpl" }}
</div><!-- /page -->

</body>
</html>