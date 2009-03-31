{{ $campsite->article->intro }}

<div class=notallowed>

{{ if $campsite->user->logged_in }}
	<p><b>{{ $campsite->user->uname }}</b>, your subscription does not include this article, or your subscription has expired. 
	<p>You will need a <a href="{{ uri options="template fastnews/useredit.tpl" }}">subscription</a>.
{{ else }}
	{{ if $campsite->user->defined }}
		<p><b>{{ $campsite->user->uname }}</b>, please <a href="{{ uri options="template fastnews/login.tpl" }}">login</a> in order to view this article.
	{{ else }}
		<p>This article is available only to subscribers. <span>{{ $campsite->publication->name }}</span> is supported through paid subscriptions. If you like our publication, please consider subscribing for full access to our site.</p>

	{{ /if }}
{{ /if }}

</div>
