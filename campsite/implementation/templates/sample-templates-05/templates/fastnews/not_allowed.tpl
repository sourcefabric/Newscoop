{{ $campsite->article->intro }}

<div class=notallowed>

{{ if $campsite->user->logged_in }}
	<p><b>{{ $campsite->user->uname }}</b>, your subscription does not include this article, or your subscription has expired. 
	<p>You will need either a <a href="{{ uri options="template useradd.tpl" }}&SubsType=trial">trial</a> or a
	<a href="{{ uri options="template useradd.tpl" }}&SubsType=paid">paid</a> subscription.
{{ else }}
	{{ if $campsite->user->defined }}
		<p><b>{{ $campsite->user->uname }}</b>, please <a href="{{ uri options="template login.tpl" }}">login</a> in order to view this article.
	{{ else }}
		<p>This article is available only to subscribers. <span>{{ $campsite->publication->name }}</span> is supported through paid subscriptions. If you like our publication, please consider subscribing for full access to our site.</p>

	{{ /if }}
{{ /if }}

</div>
