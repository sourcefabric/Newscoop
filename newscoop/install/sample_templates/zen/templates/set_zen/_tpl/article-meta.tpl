<h3>Meta</h3>

<div class="post-details">Last updated on {{ $gimme->article->last_update|camp_date_format:"%e %M %Y" }}</div>
<div class="post-details">Permalink: <a href="http://{{ $gimme->publication->site }}/{{ $gimme->article->webcode }}">{{ $gimme->article->webcode }}</a></div>
<div class="post-details">Topics: {{ list_article_topics }}{{ $gimme->topic->name }}{{ if $gimme->current_list->at_end }}{{ else }}, {{ /if }}{{ /list_article_topics }}</div>