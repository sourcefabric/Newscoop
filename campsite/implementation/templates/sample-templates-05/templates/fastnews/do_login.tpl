{{ if $campsite->login_action->ok }}

<html>
<head><title>logging you in...</title>
<META http-equiv="refresh" content="1;url=/">
</head>
<body>
<script>location.replace("/");</script>
<noscript>
<p>You have logged in successfully. Redirecting you now to the <a href="/">{{ $campsite->publication->name }} home page</a>...</p>
</noscript>
</body>
</html>

{{ else }}

{{ include file="fastnews/utility-header.tpl" }}
{{ include file="fastnews/userinfo.tpl" }}
<h1>Login failed</h1>
<p>{{ $campsite->login_action->error_message }}</p>
<p>Sorry, your username and/or password were incorrect. Please go back and try again.</p>
{{ include file="fastnews/footer.tpl" }}
</body>
</html>

{{ /if }}


