<html>
{{ list_articles }}
	Type: {{ $campsite->article->type_name }}; Name: {{ $campsite->article->name }}
	<p>Title: {{ $campsite->article->title }};
		Art Title: {{ $campsite->article->art->title }};
		Special Title: {{ $campsite->article->special->title }}
	<p>Author: {{ $campsite->article->author }};
		Special Author: {{ $campsite->article->special->author }};
		Tol Author: {{ $campsite->article->tol->author }}
	{{ if ! $campsite->current_list->at_end }}
		<hr>
	{{ /if }}
{{ /list_articles }}
</html>
