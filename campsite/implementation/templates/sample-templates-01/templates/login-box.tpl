{{ if ! $campsite->user->logged_in }}
    {{ login_form submit_button="Login" button_html_code="class=\"button\"" template="do_login.tpl" }}
<table width="100%" cellpadding="0" cellspacing="0" border="0">
  <tr>
   <td colspan="2" height="8"></td>
  </tr>
  <tr>
   <td align="left"><span class="dalje">Name:</span></td><td align="right">{{ camp_edit object="login" attribute="uname" html_code="class=\"field\"" }}</td>
  </tr>
  <tr>
   <td colspan="2" height="8"></td>
  </tr>
  <tr>
   <td align="left"><span class="dalje">Password:</span></td><td align="right">{{camp_edit object="login" attribute="password" html_code="class=\"field\"" }}
  </tr>
  <tr>
   <td colspan="2" height="8"></td>
  </tr>
</table>
{{ /login_form }}
<p class="tekst"><a class="indeks" href="{{ uripath options="template subscribe.tpl" }}?SubsType=paid&{{ urlparameters options="template subscribe.tpl" }}">SUBSCRIBE HERE</a></p>
  {{ else }}
  <p class="tekst">You are signed in as:<br><b>{{ $campsite->user->name }}</b></p>
  <p class="tekst" style="margin-top: 5px"><a class="indeks" href="{{ uri options="template logout.tpl" }}">Logout</a></p> 
  {{ /if }}
