
{{extends file="layout.tpl"}}

{{block content}}
<div class="bloger_news_items">
  <div>

<h1>{{ $gimme->issue->name }}</h1>

{{ list_sections }}
<h2 class="title">{{ $gimme->section->name }}</h2>
<ul>
{{ list_articles constraints="type not poll" }}


<li class="news_item">

  {{ image rendition="section" }}
    <a href="{{url options="article"}}" class="thumbnail">
      <img src="{{ $image->src }}"  alt="{{ $image->caption }} (photo: {{ $image->photographer }})"   />
      {{ include file="_tpl/article_icons.tpl" }}
    </a>
  {{/image}}

  <div class="content content_text">

    <h6 class="info">{{list_article_authors}}
     {{if $gimme->current_list->index!=1}},&nbsp;{{/if}}

     {{ if $gimme->author->biography->first_name }}
     {{ $gimme->author->biography->first_name }} {{
     $gimme->author->biography->last_name }}
     {{ else }}
     {{ $gimme->author->name }}
     {{ /if }}
     {{if $gimme->current_list->at_end}}
     &nbsp;-&nbsp;{{/if}}{{/list_article_authors}}{{ $gimme->article->publish_date|camp_date_format:"%d.%m.%Y, %H:%i" }}</h6>
     <h3 class="title"><a href="{{url options="article"}}">{{$gimme->article->name}}</a></h3>

     <p>{{$gimme->article->deck|strip_tags}}</p>
  </div>
</li>




{{ /list_articles }}
</ul>
{{ /list_sections }}
</div>
</div>

{{/block}}