    <div class="widget block">

      <h3>Also in {{ $gimme->section->name }}</h3>
        <ul class="related_post">
        
{{ local }}        
{{ assign var="curartno" value=$gimme->article->number }}
{{ list_articles length="4" ignore_issue="true" order="bypublishdate desc" constraints="number not $curartno" }}
        
            <li><a href="{{ uri options="article" }}" title="{{ $gimme->article->name }}">{{ $gimme->article->name }}</a></li>
            
{{ /list_articles }} 
{{ /local }}

        </ul>        
    </div>