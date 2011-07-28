  <div class="subcol fl">
    <div id="pages-3" class="block widget widget_pages"><h3>Pages</h3>
      <ul>
      
{{ list_articles ignore_issue="true" ignore_section="true" constraints="issue is 1 section is 5" }}
      
        <li class="page_item"><a href="{{ uri options="article" }}" title="{{ $gimme->article->name }}">{{ $gimme->article->name }}</a></li>

{{ /list_articles }}

      </ul>
    </div>  
  </div>