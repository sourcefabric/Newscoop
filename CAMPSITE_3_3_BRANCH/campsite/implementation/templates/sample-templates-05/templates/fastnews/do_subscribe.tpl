<html>
<body bgcolor=white>
{{ if $campsite->edit_subscription_action->ok }}
	<META http-equiv="refresh" content="0;url={{ uri options="issue" }}">
{{ else }}
	{{ $campsite->edit_subscription_action->error_message }}
{{ /if }}
</body>
</html>
