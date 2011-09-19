        <div class="textwidget">
{{ if $gimme->article->defined }} 
{{ list_article_authors }}
{{ if $gimme->current_list->at_beginning }}
        {{ if $gimme->language->english_name == "English" }}<h3>About the author</h3>{{ /if }}
        {{ if $gimme->language->english_name == "Spanish" }}<h3>Sobre el autor</h3>{{ /if }}
        {{ if $gimme->language->english_name == "Polish" }}<h3>O autorze</h3>{{ /if }}
        {{ if $gimme->language->english_name == "Russian" }}<h3>Об авторе</h3>{{ /if }}
{{ /if }}
{{ if $gimme->author->picture->defined }}
            <img src="{{ $gimme->author->picture->imageurl }}" width="180" border="0">
            <em>{{ $gimme->author->name }}</em><br />
            {{ $gimme->author->biography->text }}
{{ /if }}            
{{ /list_article_authors }}
{{ else }}       
{{ list_articles length="1" ignore_issue="true" order="bypublishdate desc" constraints="type is post" }}        
{{ list_article_authors }}
{{ if $gimme->current_list->at_beginning }}
        {{ if $gimme->language->english_name == "English" }}<h3>About the {{ if $gimme->article->defined }}post{{ /if }} author</h3>{{ /if }}
        {{ if $gimme->language->english_name == "Spanish" }}<h3>Sobre el {{ if $gimme->article->defined }}post{{ /if }} author</h3>{{ /if }}
        {{ if $gimme->language->english_name == "Polish" }}<h3>O {{ if $gimme->article->defined }}post{{ /if }} autorze</h3>{{ /if }}
        {{ if $gimme->language->english_name == "Russian" }}<h3>О {{ if $gimme->article->defined }}post{{ /if }} author</h3>{{ /if }}
{{ /if }}
{{ if $gimme->author->picture->defined }}
            <img src="{{ $gimme->author->picture->imageurl }}" width="180" border="0">
            <em>{{ $gimme->author->name }}</em><br />
            {{ $gimme->author->biography->text }}
{{ /if }}            
{{ /list_article_authors }}            
{{ /list_articles }}            
{{ /if }}
        </div>