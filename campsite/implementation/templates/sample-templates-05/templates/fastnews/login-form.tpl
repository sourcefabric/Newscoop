{{ login_form template="do_login.tpl" submit_button="Login" }}
<table><tr><td>Username:</td><td>{{ camp_edit object="login" attribute="uname" }}</td>
<tr><td>Password:</td><td>{{ camp_edit object="login" attribute="password" }}</td></tr>
<tr><td/><td>{{ /login_form }}</td></tr>
</table>
