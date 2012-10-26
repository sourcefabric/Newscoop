{{ if $gimme->article->comments_enabled }}
  {{ if $gimme->article->comment_count }}
    {{ if $gimme->article->comments_locked }}
        <li><a href="{{ uri options="article template article.tpl" }}#comments">{{ if $gimme->language->name == "English" }}Read comments{{ else }}Lea los comentarios{{ /if }} ({{ $gimme->article->comment_count}})</a>
    {{ else }}
        <li><a href="{{ uri options="article template article.tpl" }}#comments">{{ if $gimme->language->name == "English" }}Read and Post comments{{ else }}Leer y escribir comentarios{{ /if }} ({{ $gimme->article->comment_count}})</a>
    {{ /if }}
  {{ elseif !$gimme->article->comments_locked }}
    <li><a href="{{ uri options="article template article.tpl" }}#comments">{{ if $gimme->language->name == "English" }}Post comments{{ else }}Enviar comentarios{{ /if }}</a>
  {{ /if }}
{{ /if }}