{{ strip }}
<table id="rightbar" cellspacing="0" cellpadding="0">
<tr>
  <td>right bar</td>
</tr>
<tr>
  <td>
    {{ login_form submit_button="Login" }}
      UId: {{ camp_edit object="user" attribute="uname" }}
      Password: {{camp_edit object="user" attribute="password" }}
    {{ /login_form }}
  </td>
</tr>
</table>
{{ /strip }}