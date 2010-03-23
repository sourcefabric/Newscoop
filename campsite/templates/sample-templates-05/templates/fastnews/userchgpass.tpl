{{ include file="fastnews/htmlheader.tpl" }}

{{ include file="fastnews/utility-header.tpl" }}

<div class=rightfloat>
{{ include file="fastnews/userinfo.tpl" }}
</div>

<h1>Edit your user info</h1>

{{ if $campsite->user->logged_in }}
	{{ if $campsite->edit_user_action->defined }}
	{{ if $campsite->edit_user_action->ok }}
		<p>Password was changed.
		{{ else }}
		<p>There was an error on changing password: {{ $campsite->edit_user_action->error_message }}
		{{ include file="fastnews/userchgpassform.tpl" }}
		{{ /if }}
	{{ else }}
	{{ include file="fastnews/userchgpassform.tpl" }}
	{{ /if }}
{{ else }}
	<p>You are not logged in and not allowed to change password.
{{ /if }}

{{ include file="fastnews/footer.tpl" }}
</body>
</html>
