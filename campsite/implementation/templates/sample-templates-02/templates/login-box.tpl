<table width="100%" cellspacing="0" cellpadding="0" border="0">		   		
{{ if ! $campsite->user->logged_in }}
<tr> 
                <td style="padding-left: 8px"><p class="login-text">Login</p></td>
              </tr>
              <tr> 
                <td height="3" background="/templates/img/crtice.gif"></td>
              </tr>
              <tr> 
                <td style="padding-left: 8px">
<div id="login">
        {{ login_form template="do_login.tpl" submit_button="Login" }}
	<p>Username: {{ camp_edit object="login" attribute="uname" }}</p>
	<p>Password: {{ camp_edit object="login" attribute="password" }}</p>
    {{ /login_form }}
</div>
</td>
<tr>
<td style="padding-left: 8px"><span class="login-text">Subscription</span></td>
</tr>
              <tr> 
                <td height="3" background="/templates/img/crtice.gif"></td>
              </tr>
<tr>
<td  style="padding-left: 8px">
<p class="tekst">If yor are new user you can subscribe on Package Template(No2) <a href="{{ uripath options="template subscribe.tpl" }}?SubsType=paid&{{ urlparameters options="template subscribe.tpl" }}" class="plus">here.</a></p>
</td>
</tr>
{{ else }}
<tr> 
                <td style="padding-left: 8px"><p class="login-text">Login info</p></td>
              </tr>
              <tr> 
                <td height="3" background="/templates/img/crtice.gif"></td>
              </tr>
              <tr> 
  <tr>
<td style="padding-left: 8px"><p class="tekst">You are signed in as:<br><b>{{ $campsite->user->name }}</b></p>
  <p class="tekst" style="margin-top: 5px"><a class="indeks" href="{{ uri options="template logout.tpl" }}">Logout</a></p> 
</td>
</tr>
              <tr> 
                <td height="3" background="/templates/img/crtice.gif"></td>
              </tr>
  {{ /if }}
		    </table>
