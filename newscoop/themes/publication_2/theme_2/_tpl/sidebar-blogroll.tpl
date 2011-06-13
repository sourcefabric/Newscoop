<div class="subcol fr">
  <div class="block widget widget_links"><h3>Links</h3>
    <ul class='xoxo blogroll'>
    
{{ local }}  
{{ set_issue number="1" }}
{{ set_section number="15" }}
{{ list_articles }}
      
        <li><a href="{{ $gimme->article->url }}" title="{{ $gimme->article->name }}">{{ $gimme->article->name }}</a></li>

{{ /list_articles }}
{{ /local }}    
    
    </ul>
  </div>
</div>  