New comment from {{ $username }}: {{ $comment->getSubject()|escape }}<br />
{{ $comment->getMessage()|escape }}<br /><br />
<a href="{{ $publication }}{{ $articleLink }}">Go to Article</a><br />
<a href="{{ $publication }}{{ $articleLink }}#comment-{{ $comment->getId() }}">Go to Comment</a><br />
{{ $view->placeholder('subject')->set("New comment") }}
