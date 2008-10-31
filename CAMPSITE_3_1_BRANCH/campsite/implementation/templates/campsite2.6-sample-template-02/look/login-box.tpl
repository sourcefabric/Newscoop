<table width="100%" cellspacing="0" cellpadding="0" border="0">		   		
<!** if not user loggedin>
<tr> 
                <td style="padding-left: 8px"><p class="login-text">Login</p></td>
              </tr>
              <tr> 
                <td height="3" background="/look/img/crtice.gif"></td>
              </tr>
              <tr> 
                <td style="padding-left: 8px">
<div id="login">
        <!** Login do_login.tpl Login>
	<p>Username: <!** Edit Login uname></p>
	<p>Password: <!** Edit Login password></p>
    <!** endlogin>
</div>
</td>
<tr>
<td style="padding-left: 8px"><span class="login-text">Subscription</span></td>
</tr>
              <tr> 
                <td height="3" background="/look/img/crtice.gif"></td>
              </tr>
<tr>
<td  style="padding-left: 8px">
<p class="tekst">If yor are new user you can subscribe on Package Template(No2) <a href="<!** uripath template subscribe.tpl>?SubsType=paid&<!** urlparameters template subscribe.tpl>" class="plus">here.</a></p>
</td>
</tr>
<!** else>
<tr> 
                <td style="padding-left: 8px"><p class="login-text">Login info</p></td>
              </tr>
              <tr> 
                <td height="3" background="/look/img/crtice.gif"></td>
              </tr>
              <tr> 
  <tr>
<td style="padding-left: 8px"><p class="tekst">You are signed in as:<br><b><!** print user name></b></p>
  <p class="tekst" style="margin-top: 5px"><a class="indeks" href="<!** uri template logout.tpl>">Logout</a></p> 
</td>
</tr>
              <tr> 
                <td height="3" background="/look/img/crtice.gif"></td>
              </tr>
  <!** endif>
		    </table>
