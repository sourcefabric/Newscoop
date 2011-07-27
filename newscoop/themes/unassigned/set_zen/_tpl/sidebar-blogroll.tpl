<div class="subcol fr">
  <div class="block widget widget_links"><h3>Links</h3>
    <ul class='xoxo blogroll'>
    
{{ list_articles ignore_issue="true" ignore_section="true" constraints="issue is 1 section is 15" }}
      
        <li><a href="{{ $gimme->article->url }}" title="{{ $gimme->article->name }}">{{ $gimme->article->name }}</a></li>

{{ /list_articles }}    
    
    </ul>
  </div>
</div>  