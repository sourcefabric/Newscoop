{{* here we show short bio of article authors *}}
{{ list_article_authors }}            
            <div class="author-info">
                	<img rel="resizable" style="width: 130px; max-width:100%;" alt="{{ $gimme->author->name }}" src="{{ $gimme->author->picture->imageurl }}" />
                    {{ if $gimme->author->user->defined }}<span class="more"><a href="{{ $view->url(['username' => $gimme->author->user->uname], 'user') }}">+  {{ #viewProfile# }}</a></span>{{ /if }}
                    <p><em>{{ $gimme->author->type }}</em></p>
                    <h3>{{ $gimme->author->name }}</h3>
                    {{*<p><em><b>Rockstar Staff*</b></em></p>*}}
                    <p>{{ $gimme->author->biography->text|strip_tags:false|truncate:200 }}</p>
            </div><!-- /.author-info -->
{{ /list_article_authors }}