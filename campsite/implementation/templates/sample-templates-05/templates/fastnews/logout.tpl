{{ if $campsite->url->get_parameter('logout') == 'true' }}
<META HTTP-EQUIV="Set-Cookie" CONTENT="LoginUserId=; path=/">
<META HTTP-EQUIV="Set-Cookie" CONTENT="LoginUserKey=; path=/">
{{ $campsite->url->reset_parameter('logout') }}
<META HTTP-EQUIV="Refresh" content="0;url={{ uri }}">
{{ /if }}
{{ include file="fastnews/utility-header.tpl" }}

<div class=rightfloat>
{{ include file="fastnews/userinfo.tpl" }}
</div>

<h1>Logged out</h1>

<p>You've been logged out. You can log in again here, if you wish:</p>

{{ include file="fastnews/login-form.tpl" }}

{{ include file="fastnews/footer.tpl" }}
</body>
</html>
