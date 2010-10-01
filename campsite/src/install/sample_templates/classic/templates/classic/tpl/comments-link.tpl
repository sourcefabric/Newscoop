{{ if $campsite->article->comments_enabled }}
  {{ if $campsite->article->comment_count }}
    {{ if $campsite->article->comments_locked }}
        <li><a href="{{ uri options="article template article.tpl" }}#comments">{{ if $campsite->language->name == "English" }}Read comments{{ else }}Lea los comentarios{{ /if }} ({{ $campsite->article->comment_count}})</a>
    {{ else }}
        <li><a href="{{ uri options="article template article.tpl" }}#comments">{{ if $campsite->language->name == "English" }}Read and Post comments{{ else }}Leer y escribir comentarios{{ /if }} ({{ $campsite->article->comment_count}})</a>
    {{ /if }}
  {{ elseif !$campsite->article->comments_locked }}
    <li><a href="{{ uri options="article template article.tpl" }}#comments">{{ if $campsite->language->name == "English" }}Post comments{{ else }}Enviar comentarios{{ /if }}</a>
  {{ /if }}
{{ /if }}