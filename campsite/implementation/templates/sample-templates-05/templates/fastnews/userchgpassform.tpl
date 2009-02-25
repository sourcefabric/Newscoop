{{ user_form template="userchgpass.tpl" submit_button="Submit" }}
<p>Change password for {{ $campsite->user->name }}, login {{ $campsite->user->uname }}.
<table>
<tr><td>Password:</td><td>{{ camp_edit object="user" attribute="password" }}</td></tr>
<tr><td>Password again:</td><td>{{ camp_edit object="user" attribute="passwordagain" }}</td></tr>
<tr><td colspan=2 align=center>
{{ /user_form }}</td></tr>
</table>
