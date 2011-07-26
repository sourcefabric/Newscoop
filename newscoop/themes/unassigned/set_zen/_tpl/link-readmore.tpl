<p class="read_more">
<a href="{{ uri options="article" }}" rel="bookmark">{{ $gimme->article->publish_date|camp_date_format:"%M"|truncate:3:'' }} {{ $gimme->article->publish_date|camp_date_format:"%e" }}</a>
  &bull; 
{{ if $gimme->article->comments_enabled && $gimme->article->content_accessible }}
<a href="{{ uri options="article" }}#comments">
    Comments ({{ $gimme->article->comment_count }})
  </a>
{{ else }}
  <a href="{{ uri options="article" }}" rel="bookmark">Read more</a>
{{ /if }}
</p>