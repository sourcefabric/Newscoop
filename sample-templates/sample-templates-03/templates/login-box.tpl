<table width="100%" cellpadding="0" cellspacing="0" border="0">
				    <tr>
					  <td><p class="navig-belo" style="color: #666666"><img src="/templates/img/crveni-box.gif" hspace="5" vspace="0">Login</p></td>
					</tr>
					<tr>
					  <td height="1" background="/templates/img/anketa-tackice.gif"></td>
					</tr>
					<tr>
					  <td>
					    {{ if ! $campsite->user->logged_in }}
<div id="login">
        {{ login_form template="do_login.tpl" submit_button="Login" }}
	<p>Username: {{ camp_edit object="login" attribute="uname" }}</p>
	<p>Password: {{ camp_edit object="login" attribute="password" }}</p>
    {{ /login_form }}
</div>
{{ else }}
  <p class="tekst-front">You are signed in as:<br><b>{{ $campsite->user->name }}</b></p>
  <p class="tekst-front" style="margin-top: 5px"><a class="dalje" href="{{ uri options="template logout.tpl" }}">Logout</a></p> 
  {{ /if }}
					  </td>
					</tr>
					<tr>
					  <td height="1" background="/templates/img/anketa-tackice.gif"></td>
					</tr>
{{ if ! $campsite->user->logged_in }}
					<tr>
					  <td>
					    <p class="tekst-front" style="color:#CC0000;"><b>Subscription</b></p>
                                            <p class="tekst-front">If you are new user you can subscribe on Package Templates <a href="{{ uripath options="template subscribe.tpl" }}?SubsType=paid&{{ urlparameters options="template subscribe.tpl" }}" class="dalje">here.</a></p>
					  </td>
					</tr>
					<tr>
					  <td height="1" background="/templates/img/anketa-tackice.gif"></td>
					</tr>
{{ /if }}
															
				  </table>
