{{extends file="layout.tpl"}}


{{block content}}
  <h1>Topic: {{ $view->topic->getName() }}</h1>

  {{ foreach $view->articles as $article }}
    <section class="art-item clearfix">
        <header>
              <h3><a href="{{ uri options="article" }}">{{ $article->getName() }}</a></h3>
                <p>Published on <time datetime="{{$article->getPublishDate()|date_format:"%Y-%m-%dT%H:%MZ"}}">{{ $article->getPublishDate()|camp_date_format:"%M %d, %Y" }}</time> </p>
        </header>
        <p>{{ include file="_tpl/_edit-article.tpl" }}{{ $article->getData('deck') }}</p>
    </section><!-- /.art-item -->
  {{ /foreach }}
         
  {{include file='paginator_control.tpl'}}
{{/block}}