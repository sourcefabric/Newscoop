{{extends file="layout.tpl"}}


{{block content}}
  <h1>Topic: {{ $view->topic->getName() }}</h1>

  {{ foreach $view->articles as $article }}
    <section class="art-item clearfix">
        <header>
              <h3><a href="{{ uri options="article" }}">{{ $article->getName() }}</a></h3>
                <p>Published on 
                  <time datetime="{{$article->getPublishDate()|date_format:"%Y-%m-%dT%H:%MZ"}}">
                    {{ $article->getPublishDate()|camp_date_format:"%M %d, %Y" }}
                  </time> by 
                  {{ assign counter value=1 }}
                  {{ foreach $article->getArticleAuthors() as $author }}
                    {{ $author->getFullName() }} 
                    {{ if count($article->getArticleAuthors()) > 1 and $counter < count($article->getArticleAuthors()) }},{{/if}}
                    {{ assign counter value=$counter+1 }}
                  {{ /foreach }}
                </p>
        </header>
        <p>{{ include file="_tpl/_edit-article.tpl" }}{{ $article->getData('deck') }}</p>
    </section>
  {{ /foreach }}
         
  {{include file='paginator_control.tpl'}}
{{/block}}