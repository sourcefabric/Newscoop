<table width="145" cellspacing="0" cellpadding="0" border="0">		   		   
              <tr> 
                <td><p class="anketa1"><img src="/templates/img/anketa.gif" border="0"> Login</p></td>
              </tr>
              <tr> 
                <td style="padding-left: 14px">
{{ if ! $campsite->user->logged_in }}
<div id="login">
        {{ login_form template="do_login.tpl" submit_button="Login" }}
	<p>Username: {{ camp_edit object="login" attribute="uname" }}</p>
	<p>Password: {{ camp_edit object="login" attribute="password" }}</p>
    {{ /login_form }}
</div>
<span class="subscribe"><a href="{{ uripath options="template subscribe.tpl" }}?SubsType=paid&{{ urlparameters options="template subscribe.tpl" }}">SUBSCRIBE HERE</a></span>
{{ else }}
  <p class="text">You are signed in as:<br><b>{{ $campsite->user->name }}</b></p>
  <p class="text" style="margin-top: 5px"><a class="indeks" href="{{ uri options="template logout.tpl" }}">Logout</a></p> 
  {{ /if }}
</td>
			  </tr>
              <tr> 
                <td height="1" background="/templates/img/bgrmiddle2.gif"></td>
              </tr>
		    </table>
  