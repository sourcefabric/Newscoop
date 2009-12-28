<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title>Campsite Template/04</title>
<link rel="stylesheet" type="text/css" href="/templates/style04.css">
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
</head>

<body marginheight="2" marginwidth="0" bgcolor="#FFFFFF">

<table width="760" cellpadding="0" cellspacing="0">

  <!-- above header -->

  <tr>
    <td height="20" align="right">{{ include file="header-01.tpl" }}
	  
    </td>
  </tr>
  
  <!-- header -->
  
  <!-- header logo and banner -->
  
  <tr>
    <td style="padding: 0px 0px 3px 0px">
	  <table width="100%" cellpadding="0" cellspacing="0" border="0">
        <tr>
          <td>{{ include file="header-04.tpl" }}</td>
        </tr>
      </table>
	</td>
  </tr>
  
  <!-- header index -->
  
  <tr>
    <td bgcolor="#008E31">
      {{ include file="header-02.tpl" }}
	</td>
  </tr>
  
  <tr>
	<td height="1" bgcolor="#FFFFFF"></td>
  </tr>
  
  <!-- date & search box -->
  
  <tr>
    <td>
	  {{ include file="header-03.tpl" }}
	</td>
  </tr>

  <tr>
    <td>
	  <table width="100%" cellspacing="0" cellpadding="0" border="0">
        <tr>
          <td width="155" valign="top">
		  
		    <!-- left menu -->
		  
		    {{ include file="menu.tpl" }}
		  </td>
          <td width="5" background="/templates/img/islinija1.gif"> </td>
          <td width="448" valign="top">
		  
		  <!-- middle column -->

          <table width="100%" cellspacing="0" cellpadding="0" border="0">
		    <tr>
			  <td height="5"></td>
			</tr>
		  </table>

		  <!-- main content -->
		  
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
      <META http-equiv="refresh" content="5;url={{ uri options="issue" }}">
      <p class="text">You are successfuly loged in. The home page will be automaticaly loaded. Please wait...<br><br>
If loading fails click <a class="naslov" href="{{ uri options="issue" }}">here</a>.</p>
      {{ else }}
        <p class="text">Login error: <font style="color: #CC0000"> {{ $campsite->login_action->error_message }}</font></p>
        <p class="text">Check if the username and password were correct and try again.</p>
      {{ /if }}

    </td>
  </tr>

  

 </table>

		 </td>
		 
         <td width="5" background="/templates/img/islinija1.gif"></td>
         <td width="145" valign="top" style="padding-right: 1px">
		 
		   <!-- right column -->
		 
		   {{ include file="right.tpl" }}
		   
		  </td> 
          <td width="1" valign="top" background="/templates/img/bgrright1.gif"></td>
        </tr>
      </table>
    </td>
  </tr>

  <!-- footer -->

  <tr>
    <td height="26" background="/templates/img/bgrfooter.gif">
	  <table width="100%" cellspacing="0" cellpadding="0" border="0">
        {{ include file="footer.tpl" }}
      </table>
	</td>
  </tr>
  <tr>
    <td> </td>
  </tr>
  
</table>

</body>
</html>