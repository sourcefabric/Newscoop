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
            <h3 class="title"><a href="{{url options="article"}}">{{$gimme->article->title}}</a></h3>
            {{ if !$gimme->article->content_accessible }}
            <span class="premium_label">{{'premium'|translate}}</span>
            {{/if}}
            <p>{{$gimme->article->deck|strip_tags|truncate:200:"...":false}}</p>
        </div>