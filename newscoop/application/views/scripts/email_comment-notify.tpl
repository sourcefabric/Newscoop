Der Kommentar von {{ $username }} lautet:
{{ $comment->getMessage()|truncate:300 }}

<a href="{{ $publication }}/{{ $article->url }}">Zum Artikel</a>
<a href="{{ $publication }}/{{ $article->url }}#comment-{{ $comment->getId() }}">Zum Kommentar</a>
