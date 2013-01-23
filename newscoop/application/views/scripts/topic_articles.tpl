{{extends file="layout.tpl"}}


{{block content}}
  <h1>Topic: {{ $view->topic->getName() }}</h1>

  {{ foreach $view->articles as $article }}
    {{ set_article number=$article->getNumber() }}
    <section class="art-item clearfix">
        <header>
              <h3><a href="{{ uri options="article" }}">{{ $gimme->article->name }}</a></h3>
              <p>
                <span class="right">{{ include file="_tpl/article-icons.tpl" }}</span>
                Published on 
                <time datetime="{{$gimme->article->publish_date|date_format:"%Y-%m-%dT%H:%MZ"}}">{{ $gimme->article->publish_date|camp_date_format:"%M %d, %Y" }}</time> by 
                {{ assign counter value=1 }}
                {{ foreach $article->getArticleAuthors() as $author }}
                  {{ $author->getFullName() }} 
                  {{ if count($article->getArticleAuthors()) > 1 and $counter < count($article->getArticleAuthors()) }},{{/if}}
                  {{ assign counter value=$counter+1 }}
                {{ /foreach }}
              </p>
        </header>
        {{ include file="_tpl/img/img_250x167.tpl" where="section" }}
        <p>{{ include file="_tpl/_edit-article.tpl" }}{{ $gimme->article->deck }}</p>
    </section>
  {{ /foreach }}
{{/block}}