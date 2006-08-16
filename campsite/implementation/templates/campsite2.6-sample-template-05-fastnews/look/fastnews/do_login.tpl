<!** if login ok>

<html>
<head><title>logging you in...</title>
<META http-equiv="refresh" content="1;url=/">
</head>
<body>
<script>location.replace("/");</script>
<noscript>
<p>You have logged in successfully. Redirecting you now to the <a href="/"><!** print publication name> home page</a>...</p>
</noscript>
</body>
</html>

<!** else>

<!** include utility-header.tpl>
<!** include userinfo.tpl>
<h1>Login failed</h1>
<p>Sorry, your username and/or password were incorrect. Please go back and try again.</p>
<!** include footer.tpl>
</body>
</html>

<!** endif>


