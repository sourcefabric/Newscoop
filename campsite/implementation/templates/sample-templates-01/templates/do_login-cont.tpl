<!-- login centralno -->
		  
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td align="left"><p class="nadnaslov" style="text-transform: lowercase">{{ $smarty.now|camp_date_format:"%W, %e. %M %Y." }}</p></td>
  </tr>
  <tr>
    <td height="1" bgcolor="#999999"></td>
  </tr>

  <tr>
    <td valign="top" style="padding-top:10px">

      {{ if $campsite->login_action->ok }}
      <META http-equiv="refresh" content="5;url={{ uri options="template home.tpl" }}">
      <p class="tekst">You are successfuly loged in. Home page will be atuomaticly loaded. Please wait...<br><br>
If loading fail click <a class="naslov" href="{{ uri options="template home.tpl" }}">here</a>.</p>
      {{ else }}
        <p class="tekst">Login error: <font style="color: #CC0000"> {{ $campsite->login_action->error_message }}</font></p>
        <p class="tekst">Check if You are correctly input username and password and try again.</p>
      {{ /if }}

    </td>
  </tr>

  

 </table>

    <!-- end login centralno -->