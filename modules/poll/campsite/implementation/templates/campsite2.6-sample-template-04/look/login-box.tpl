<table width="145" cellspacing="0" cellpadding="0" border="0">		   		   
              <tr> 
                <td><p class="anketa1"><img src="/look/img/anketa.gif" border="0"> Login</p></td>
              </tr>
              <tr> 
                <td style="padding-left: 14px">
<!** if not user loggedin>
<div id="login">
        <!** Login do_login.tpl Login>
	<p>Username: <!** Edit Login uname></p>
	<p>Password: <!** Edit Login password></p>
    <!** endlogin>
</div>
<span class="subscribe"><a href="<!** uripath template subscribe.tpl>?SubsType=paid&<!** urlparameters template subscribe.tpl>">SUBSCRIBE HERE</a></span>
<!** else>
  <p class="text">You are signed in as:<br><b><!** print user name></b></p>
  <p class="text" style="margin-top: 5px"><a class="indeks" href="<!** uri template logout.tpl>">Logout</a></p> 
  <!** endif>
</td>
			  </tr>
              <tr> 
                <td height="1" background="/look/img/bgrmiddle2.gif"></td>
              </tr>
		    </table>
  