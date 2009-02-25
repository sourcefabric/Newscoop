<!-- This is the search template -->
<html>
<head>
<title>{{ $campsite->publication->name }}&nbsp;-  Search results</title>

{{ include file="fastnews/meta.tpl" }}

</head>
<body>

{{ include file="fastnews/header.tpl" }}

{{ include file="fastnews/sitemap.tpl" }}

<td valign=top>

<div class=rightfloat>
{{ include file="fastnews/userinfo.tpl" }}
</div>

{{ list_search_results length="5" order="bydate" }}

	{{ if $campsite->current_list->at_beginning }}
		<h2>Search results</h2>
		
		<div>{{ search_form template="search.tpl" submit_button="Search" }}{{ camp_edit object="search" attribute="keywords" }} {{ camp_select object="search" attribute="mode" }} require ALL words{{ /search_form }}</div>
	
		<table>
	{{ /if }}

	<tr><td bgcolor={{ if $campsite->current_list->index == "odd" }}#F1F1F1{{ else }}white{{ /if }}>
	{{ $campsite->list->index }}.</td><td bgcolor={{ if $campsite->current_list->index == "odd" }}#F1F1F1{{ else }}#ffffff{{ /if }}><a href="/tpl/fastnews/article.tpl?{{ urlparameters options="article" }}">{{ $campsite->article->name }}</a>{{ if ! $campsite->article->content_accesible }} <img src="/templates/fastnews/subscriber.png" width=11 height=11" alt="[S]">{{ /if }}<br> <i>{{ $campsite->section->name }}</i>, {{ $campsite->issue->name }}<div><small>{{ $campsite->article->intro }}</small></div>
		</td>
	</tr>

	{{ if $campsite->current_list->at_end }}
		</table>
			{{ if $campsite->current_list->has_previous_elements }}
			<a href="{{ uri options="template search.tpl" }}">Previous</a>
		{{ else }}
			<font color=#BBBBBB>Previous</font>
		{{ /if }}
		|
		{{ if $campsite->current_list->has_next_elements }}
			<a href="{{ uri options="next_items" options="template search.tpl" }}">Next</a>
		{{ else }}
			<font color=#BBBBBB>Next</font>
		{{ /if }}
	{{ /if }}

{{ /list_search_results }}
{{ if $campsite->prev_list_empty }}
	{{ if $campsite->search_articles_action->defined }}
		<h1>No results found</h1>
		<p>Please try rephrasing your search, or using less search terms.</p>
	{{ /if }}

{{ /if }}

</td>

{{ include file="fastnews/footer.tpl" }}

</body>
</html>
