<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title>Campsite Template/01</title>
<link rel="stylesheet" type="text/css" href="/templates/style03.css">
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
</head>

<body topmargin="5" leftmargin="5" bgcolor="#FFFFFF">
<table width="747" cellpadding="0" cellspacing="0" border="0">
  <!--header-->
  
  <tr> 
    <td colspan="5" height="69">
	  {{ include file="header.tpl" }}</td>
  </tr>
  
  <!-- end header-->
  
  <!--main index-->
  
  <tr> 
    <td colspan="5" valign="top"> 
	  {{ include file="header-01.tpl" }}</td>
  </tr>
  
  <!--end main index-->
  
  <tr>
    <td width="131" valign="top">
	
  <!--index left-->
  
           {{ include file="left.tpl" }}<!--end index left-->
	  
            {{ include file="links.tpl" }}<!-- end linkovi -->
            <!-- banner radio tocak -->
            <table width="100%" cellpadding="0" cellspacing="0" border="0">
              <tr>
                <td height="5" bgcolor="#FFFFFF"></td>
              </tr>
              <tr>
                <td align="center">{{ include file="left-banners.tpl" }}</td>
              </tr>
            </table>
            
            <!-- end banner radio tocak -->
    </td>
	<td width="8"></td>
    <td width="467" valign="top">
{{ if $campsite->edit_subscription_action->ok }}
    <META http-equiv="refresh" content="5;url={{ uri options="issue" }}">
    <p class="tekst-front">Your subscription was created successfuly. The home page will be automaticaly loaded. Please wait...<br><br>
If loading fails click <a class="naslov" href="{{ uri options="issue" }}">here</a>.</p>
{{ else }}
    {{ $campsite->edit_subscription_action->error_message }}
{{ /if }}
<!--end main middle--></td>
    <td width="8"></td>
    <td width="133" valign="top" bgcolor="#E4EEF8"> 
	
    <!--main right--> 
	
        {{ include file="right.tpl" }}</td>
  </tr>
  
  <!-- footer -->
  
  <tr>
    <td colspan="2"></td>
	<td align="center">{{ include file="banner.tpl" }}</td>
	<td colspan="2"></td>
  </tr>
  <tr>
    <td colspan="5" height="25"></td>
  </tr>
  <tr>
	<td align="center" style="padding: 3px 0px 3px 0px" colspan="5"><p class="footer">
{{ include file="footer.tpl" }}</p></td>
  </tr>
  
</table>

</body>

</html>