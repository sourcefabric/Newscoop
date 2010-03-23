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
      <META http-equiv="refresh" content="3;url={{ uri options="issue" }}">
      <p class="tekst">You are successfuly loged in. The home page will be automaticaly loaded. Please wait...<br><br>
If loading fails click <a class="naslov" href="{{ uri options="issue" }}">here</a>.</p>
      {{ else }}
        <p class="tekst">Login error: <font style="color: #CC0000"> {{ $campsite->login_action->error_message }}</font></p>
        <p class="tekst">Check if the username and password were correct and try again.</p>
      {{ /if }}

    </td>
  </tr>

  

 </table>

    <!-- end login centralno -->