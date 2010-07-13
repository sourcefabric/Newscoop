{{ if $campsite->article->comments_enabled }}
  {{ if $campsite->article->comment_count }}
    {{ if $campsite->article->comments_locked }}
        <li><a href="{{ uri options="article template article.tpl" }}#comments">Read comments ({{ $campsite->article->comment_count}})</a>
    {{ else }}
        <li><a href="{{ uri options="article template article.tpl" }}#comments">Read and Post comments ({{ $campsite->article->comment_count}})</a>
    {{ /if }}
  {{ elseif !$campsite->article->comments_locked }}
    <li><a href="{{ uri options="article template article.tpl" }}#comments">Post comments</a>
  {{ /if }}
{{ /if }}