<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title>Campsite Template/01</title>
<link rel="stylesheet" type="text/css" href="/templates/01-style.css">
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<link rel="alternate" type="application/rss+xml" title="Packaged templates #01" href="http://{{ $campsite->publication->site }}{{ uripath options="template rss.tpl" }}?{{ urlparameters options="template rss.tpl" }}" >
</head>

<body topmargin="5" leftmargin="5" bgcolor="#FFFFFF">
<table width="747" cellpadding="0" cellspacing="0" border="0">

  <!--main baner-->
  
<!-- banner rotator can be placed here -->
  
  <!--end main baner-->
  
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
  
           {{ include file="menu.tpl" }}<!--end index left-->
	  
    </td>
	<td width="8"></td>
    <td width="467" valign="top"> 
	
        <table border="0" cellpadding="0" cellspacing="0" width="100%">
          <tr>
            <td height="15"></td>
          </tr>
          <tr>
            <td>
              {{ include file="home-article.tpl" }}</td>
          </tr>
        </table>

        <table border="0" cellpadding="0" cellspacing="0" width="100%">
		  <tr>
		    <td height="8"></td>
		  </tr>
          <tr>
            <td background="/templates/img/06linija.gif" height="1"></td>
          </tr>
        </table>

        {{ include file="home-rest.tpl" }}
        <table border="0" cellpadding="0" cellspacing="0" width="100%">
		  <tr>
		    <td height="8"></td>
		  </tr>
          <tr>
            <td background="/templates/img/06linija.gif" height="1"></td>
          </tr>
		  <tr>
		    <td height="8"></td>
		  </tr>		  
        </table>

        <table border="0" cellpadding="0" cellspacing="0" width="100%">
		  <tr>
		    <td width="281" height="1"><img src="/templates/img/transpa.gif" width="281" height="1"></td>
			<td width="8" height="1"><img src="/templates/img/transpa.gif" width="8" height="1"></td>
			<td width="178" height="1"><img src="/templates/img/transpa.gif" width="178" height="1"></td>
		  </tr>
          <tr>
            <td width="281" valign="top">
              {{ include file="home-news.tpl" }}</td>		
			  <td width="8"></td>
              <td width="178" valign="top">
			  
			  <!--blok kultura/intervju-->		
			  
                {{ include file="home-culture.tpl" }}</td>

            </tr>
            <tr>
			  <td colspan="3" height="25"></td>
			</tr>
          </table>
       
	  <!--end main middle--> 


    </td>
	<td width="8"></td>
    <td width="133" valign="top" bgcolor="#d3e5f1"> 
	
  </tr>
  
  <!-- footer -->
  
</table>

</body>

</html>
