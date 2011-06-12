  <div class="subcol fl">
    <div id="pages-3" class="block widget widget_pages"><h3>Pages</h3>
      <ul>
      
{{ local }}  
{{ set_issue number="1" }}
{{ set_section number="5" }}
{{ list_articles }}
      
        <li class="page_item"><a href="{{ uri options="article" }}" title="{{ $gimme->article->name }}">{{ $gimme->article->name }}</a></li>

{{ /list_articles }}
{{ /local }}

      </ul>
    </div>  
  </div>