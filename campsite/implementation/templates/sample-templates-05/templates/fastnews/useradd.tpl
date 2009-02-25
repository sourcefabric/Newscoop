{{ include file="fastnews/utility-header.tpl" }}

<h1>Subscribe to to {{ $campsite->publication->name }}</h1>

{{ if $campsite->edit_user_action->defined }}
	{{ if $campsite->edit_user_action->ok }}
	{{ include file="fastnews/subscribe.tpl" }}
	{{ else }}
	<p>There was an error on user info: {{ $campsite->edit_user_action->error_message }}
	{{ include file="fastnews/useraddform.tpl" }}
	{{ /if }}
{{ else }}
	{{ include file="fastnews/useraddform.tpl" }}
{{ /if }}
</td>

{{ include file="fastnews/footer.tpl" }}
</body>
</html>