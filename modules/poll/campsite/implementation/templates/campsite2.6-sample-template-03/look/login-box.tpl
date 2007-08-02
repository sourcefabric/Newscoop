<table width="100%" cellpadding="0" cellspacing="0" border="0">
				    <tr>
					  <td><p class="navig-belo" style="color: #666666"><img src="/look/img/crveni-box.gif" hspace="5" vspace="0">Login</p></td>
					</tr>
					<tr>
					  <td height="1" background="/look/img/anketa-tackice.gif"></td>
					</tr>
					<tr>
					  <td>
					    <!** if not user loggedin>
<div id="login">
        <!** Login do_login.tpl Login>
	<p>Username: <!** Edit Login uname></p>
	<p>Password: <!** Edit Login password></p>
    <!** endlogin>
</div>
<!** else>
  <p class="tekst-front">You are signed in as:<br><b><!** print user name></b></p>
  <p class="tekst-front" style="margin-top: 5px"><a class="dalje" href="<!** uri template logout.tpl>">Logout</a></p> 
  <!** endif>
					  </td>
					</tr>
					<tr>
					  <td height="1" background="/look/img/anketa-tackice.gif"></td>
					</tr>
<!** if not user loggedin>
					<tr>
					  <td>
					    <p class="tekst-front" style="color:#CC0000;"><b>Subscription</b></p>
                                            <p class="tekst-front">If you are new user you can subscribe on Package Templates <a href="<!** uripath template subscribe.tpl>?SubsType=paid&<!** urlparameters template subscribe.tpl>" class="dalje">here.</a></p>
					  </td>
					</tr>
					<tr>
					  <td height="1" background="/look/img/anketa-tackice.gif"></td>
					</tr>
<!** endif>
															
				  </table>
