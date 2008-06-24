<!** if not user loggedin>
    <form class="form" style="margin-top: 5px; margin-bottom: 5px" action="/" method="post">
    <input type=hidden name="IdLanguage" value="1">
    <input type=hidden name="IdPublication" value="4">
    <input type=hidden name="NrIssue" value="<!** print issue number>">
    <input type=hidden name="login" value="login">
    <input type=hidden name="tpl" value="99">
<table width="100%" cellpadding="0" cellspacing="0" border="0">
  <tr>
   <td colspan="2" height="8"></td>
  </tr>
  <tr>
   <td align="left"><span class="dalje">Name:</span></td><td align="right"><input class="field" type="text" name="LoginUName" maxlength="255"></td>
  </tr>
  <tr>
   <td colspan="2" height="8"></td>
  </tr>
  <tr>
   <td align="left"><span class="dalje">Password:</span></td><td align="right"><input class="field" type="password" name="LoginPassword" maxlength="64"></td>
  </tr>
  <tr>
   <td colspan="2" height="8"></td>
  </tr>
  <tr>
   <td  colspan="2" align="left"><input class="button" type="submit" name="login" value="login"></td>
  </tr>
</table>
</form>
<p class="tekst"><a class="indeks" href="<!** uripath template subscribe.tpl>?SubsType=paid&<!** urlparameters template  subscribe.tpl>">SUBSCRIBE HERE</a></p>
  <!** else>
  <p class="tekst">You are signed in as:<br><b><!** print user name></b></p>
  <p class="tekst" style="margin-top: 5px"><a class="indeks" href="<!** uri template logout.tpl>">Logout</a></p> 
  <!** endif>