<!doctype html public "-//W3C//DTD HTML 4.0 Transitional//EN" "http://www.w3.org/TR/REC html40/loose.dtd">
<head>
<meta http-equiv="content-type" content="text/html; charset=iso-8859-2">
<title>Transitions Online</title>
<meta name="description" content="">
<meta name="keywords" content="">
<style type="text/css">
<!--
.normal {  font-family: Arial, Helvetica, sans-serif}
.normal1 {  font-family: Arial, Helvetica, sans-serif; text-decoration: none; font-size: 12px}
.tick {  font-family: Arial, Helvetica, sans-serif; font-size: 12px; text-decoration: none}
.search {  font-family: Arial, Helvetica, sans-serif; font-size: 11px; font-weight: bold; color: #000000}
-->
</style>
<script language="JavaScript">
<!--
<!--
function MM_reloadPage(init) {  //reloads the window if Nav4 resized
  if (init==true) with (navigator) {if ((appName=="Netscape")&&(parseInt(appVersion)==4)) {
    document.MM_pgW=innerWidth; document.MM_pgH=innerHeight; onresize=MM_reloadPage; }}
  else if (innerWidth!=document.MM_pgW || innerHeight!=document.MM_pgH) location.reload();
}
MM_reloadPage(true);
// -->

function MM_findObj(n, d) { //v4.0
  var p,i,x;  if(!d) d=document; if((p=n.indexOf("?"))>0&&parent.frames.length) {
    d=parent.frames[n.substring(p+1)].document; n=n.substring(0,p);}
  if(!(x=d[n])&&d.all) x=d.all[n]; for (i=0;!x&&i<d.forms.length;i++) x=d.forms[i][n];
  for(i=0;!x&&d.layers&&i<d.layers.length;i++) x=MM_findObj(n,d.layers[i].document);
  if(!x && document.getElementById) x=document.getElementById(n); return x;
}

function MM_showHideLayers() { //v3.0
  var i,p,v,obj,args=MM_showHideLayers.arguments;
  for (i=0; i<(args.length-2); i+=3) if ((obj=MM_findObj(args[i]))!=null) { v=args[i+2];
    if (obj.style) { obj=obj.style; v=(v=='show')?'visible':(v='hide')?'hidden':v; }
    obj.visibility=v; }
}
//-->
</script>
</head>
<body background="img/back.gif" topmargin="0" leftmargin="0" marginwidth="0" marginheight="0" link="#630000" vlink="#630000">

<div id="Layer1" style="position:absolute; left:330px; top:215px; width:133px; height:140px; z-index:1; background-color: #cfd6cf; layer-background-color: #660000; border: 1px none #000000; visibility: hidden"> 
  <table width="100%" border="0" cellspacing="0" cellpadding="0" height="140">

        <form name="form1" method="post" action="search.tpl" class="search">

    <tr><td height="5">
    </td></tr>

    <tr> 
      <td valign="top" valign="top"> 
        <center><b class="tick">Search TOL</b></center>
	</td></tr>
	<tr><td valign="middle">
          <div align="center" class="search">Aug 2000 - present: 
		<input type=hidden name="IdLanguage" value="1">
		<input type=hidden name="IdPublication" value="<!** print publication identifier>">
		<input type=hidden name="NrIssue" value="<!** print issue number>">
		<input type=hidden name="NrSection" value="<!** print section number>">
		<input type="text" name="SearchKeywords" maxlength="160" size="8">&nbsp;
		<input type="submit" name="search" value="Go">
          </div>
	</td></tr>

        </form>
        <form name="form2" method="get" action="http://archive.tol.cz/cgi-bin/htsearch" class="search">

	<tr><td valign="middle">	

          <div align="center" class="search">Jun 1997 - Jul 2000: 
		<INPUT TYPE=HIDDEN NAME=config VALUE="archive">
		<INPUT TYPE=HIDDEN NAME=method VALUE="and">
            <input type="text" name="words" size="8" maxlength="160">&nbsp;
            <input type="submit" name="Submit2" value="Go">
          </div>
	</td></tr>

        </form>

	<tr><td valign="bottom">
        <div align="center"> 
          <p><a href="#" onClick="MM_showHideLayers('Layer1','','hide')"><img src="obr/search1.gif" width="96" height="14" border="0" alt="click to close search window"></a></p>
        </div>
      </td>
    </tr>
	<tr><td height="5">
	</td></tr>
  </table>
</div>


<table width="579" border="0" cellspacing="0" cellpadding="0">
<!-- banner goes here-->	
<tr>	<td colspan="2" align="right" bgcolor="#630000" rowspan="5"><a href="http://www.socres.org/budapest/"><img src="obr/privacy3.gif" width="468" height="60" border="0" alt="Ad here" vspace="5"></a></td>
	<td bgcolor="#630000" height="5"></td></tr>
<tr>	<td bgcolor="#630000" valign="bottom"><a href="login.tpl?<!** urlparameters>"><img src="img/rlogin.gif" width="54" height="17" border="0" alt="Login"></a></td>
</tr>
<tr>	<td bgcolor="#630000" valign="middle"><a href="http://archive.tol.cz/join.html"><img src="img/rjoin.gif" width="77" height="16" border="0" alt="Join TOL"></a></td>
</tr>
<tr>	<td bgcolor="#630000" valign="top"><a href="http://archive.tol.cz/autopasswd.html"><img src="img/rlpass.gif" width="83" height="26" border="0" alt="Lost password?"></a></td>
</tr>
<tr>	<td bgcolor="#630000" height="5"></td>
</tr>

<tr>	<td></td>
	<td align="right" valign="top">
	<img src="img/sttol.gif" width="396" height="18" border="0" alt="Transitions Online"></td>
	<td bgcolor="#630000"></td>
</tr>
<tr>	<td width="73" valign="top">
	<table width="73" border="0" cellspacing="0" cellpadding="0">
		<tr><td><!** local><!** issue current><!** article off><!** section number 1><a href="section.tpl?<!** urlparameters>"><img src="img/navinfoc.gif" width="73" height="16" border="0" alt="In Focus"></a><!** section off><!** endlocal></td></tr><tr><td><img src="img/navstr.gif" width="57" height="1" border="0" alt=""></td></tr>
		<tr><td><!** local><!** issue current><!** article off><!** section number 2><a href="section.tpl?<!** urlparameters>"><img src="img/navfeatu.gif" width="73" height="21" border="0" alt="Features"></a><!** section off><!** endlocal></td></tr><tr><td><img src="img/navstr.gif" width="57" height="1" border="0" alt=""></td></tr>
		<tr><td><!** local><!** issue current><!** article off><!** section number 3><a href="section.tpl?<!** urlparameters>"><img src="img/navopini.gif" width="73" height="22" border="0" alt="Opinions"></a><!** section off><!** endlocal></td></tr><tr><td><img src="img/navstr.gif" width="57" height="1" border="0" alt=""></td></tr>
		<tr><td><!** local><!** issue current><!** article off><!** section number 4><a href="section.tpl?<!** urlparameters>"><img src="img/navmedia.gif" width="73" height="22" border="0" alt="Media"></a><!** section off><!** endlocal></td></tr><tr><td><img src="img/navstr.gif" width="57" height="1" border="0" alt=""></td></tr>
		<tr><td><!** local><!** issue current><!** article off><!** section number 5><a href="section.tpl?<!** urlparameters>"><img src="img/navbooks.gif" width="73" height="21" border="0" alt="Books"></a><!** section off><!** endlocal></td></tr><tr><td><img src="img/navstr.gif" width="57" height="1" border="0" alt=""></td></tr>
		<tr><td><!** local><!** issue current><!** article off><!** section number 6><a href="section.tpl?<!** urlparameters>"><img src="img/navweeki.gif" width="73" height="31" border="0" alt="Week in Review"></a><!** section off><!** endlocal></td></tr><tr><td><img src="img/navstr.gif" width="57" height="1" border="0" alt=""></td></tr>
		<tr><td><!** local><!** issue current><!** article off><!** section number 7><a href="section.tpl?<!** urlparameters>"><img src="img/navinthe.gif" width="73" height="31" border="0" alt="In Their Own Words"></a><!** section off><!** endlocal></td></tr><tr><td><img src="img/navstr.gif" width="57" height="1" border="0" alt=""></td></tr>
		<tr><td><!** local><!** issue current><!** article off><!** section number 8><a href="section.tpl?<!** urlparameters>"><img src="img/navlette.gif" width="73" height="28" border="0" alt="Letters to the Editor"></a><!** section off><!** endlocal></td></tr>
		<tr><td height="30"></td></tr>
		<tr><td><a href="http://archive.tol.cz/about.html"><img src="img/navabout.gif" width="73" height="17" border="0" alt="About TOL"></a></td></tr><tr><td><img src="img/navstr.gif" width="57" height="1" border="0" alt=""></td></tr>
		<tr><td><a href="http://archive.tol.cz/join.html"><img src="img/navsubsc.gif" width="73" height="22" border="0" alt="Join TOL"></a></td></tr><tr><td><img src="img/navstr.gif" width="57" height="1" border="0" alt=""></td></tr>
		<tr><td><a href="http://archive.tol.cz/links1.html"><img src="img/navcount.gif" width="73" height="26" border="0" alt="Country Files"></a></td></tr><tr><td><img src="img/navstr.gif" width="57" height="1" border="0" alt=""></td></tr>
		<tr><td><a href="http://archive.tol.cz/newsstan.html"><img src="img/navnewss.gif" width="73" height="21" border="0" alt="Newsstand"></a></td></tr><tr><td><img src="img/navstr.gif" width="57" height="1" border="0" alt=""></td></tr>
		<tr><td><a href="http://archive.tol.cz/archive.html"><img src="img/navarchi.gif" width="73" height="21" border="0" alt="Archive"></a></td></tr><tr><td><img src="img/navstr.gif" width="57" height="1" border="0" alt=""></td></tr>
		<tr><td><!** local><!** issue current><!** article off><!** section number 14><a href="section.tpl?<!** urlparameters>"><img src="img/navjobs.gif" width="73" height="19" border="0" alt="Jobs"></a><!** section off><!** endlocal></td></tr><tr><td><img src="img/navstr.gif" width="57" height="1" border="0" alt=""></td></tr>
		<tr><td><a href="http://archive.tol.cz/mediakit/index.html"><img src="img/navadinf.gif" width="73" height="25" border="0" alt="Ad Info"></a></td></tr><tr><td><img src="img/navstr.gif" width="57" height="1" border="0" alt=""></td></tr>
		<tr><td><a href="http://archive.tol.cz/sponpart/partners.html"><img src="img/navpartn.gif" width="73" height="21" border="0" alt="Partners"></a></td></tr><tr><td><img src="img/navstr.gif" width="57" height="1" border="0" alt=""></td></tr>
		<tr><td><a href="http://archive.tol.cz/sponpart/index.html"><img src="img/navspons.gif" width="73" height="21" border="0" alt="Sponsors"></a></td></tr>
	</table>
	</td>
	<td align="right" valign="top">
	<table width="396" border="0" cellspacing="0" cellpadding="0">
<tr><td><font class="tick">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</font></td>
<td valign="top" width="291">
<br><script language="JavaScript1.2">
/*
Pausing updown message scroller- 
Last updated: 99/07/05 (Bugs fixed, ability to specify background image for scroller)
 Dynamic Drive (www.dynamicdrive.com)
For full source code, installation instructions,
100's more DHTML scripts, and Terms Of
Use, visit dynamicdrive.com
*/

//configure the below five variables to change the style of the scroller
var scrollerwidth=200
var scrollerheight=70
var scrollerbgcolor='#ffffff'
//set below to '' if you don't wish to use a background image
var scrollerbackground='img/tickback.gif'

//configure the below variable to change the contents of the scroller
var messages=new Array()


<!** local>
<!** list length 1 issue order bynumber desc>

<!** section off>

<!** list section order bynumber asc>

<!** if list row 2>
<!** list article order bynumber desc>

<!** if list index 1>
messages[0]="<a href='article.tpl?<!** urlparameters>'><font size='-2' class='tick' color='#000000'><b><!** print article name></b><br><!** print section name>, <!** print article date %e> <!** print article date %M></font></a>"
<!** endif>
<!** if list index 2>
messages[1]="<a href='article.tpl?<!** urlparameters>'><font size='-2' class='tick' color='#000000'><b><!** print article name></b><br><!** print section name>, <!** print article date %e> <!** print article date %M></font></a>"
<!** endif>
<!** if list index 3>
messages[2]="<a href='article.tpl?<!** urlparameters>'><font size='-2' class='tick' color='#000000'><b><!** print article name></b><br><!** print section name>, <!** print article date %e> <!** print article date %M></font></a>"
<!** endif>

<!** endlist>
<!** endif>

<!** if list row 4>
<!** list article isnoton frontpage order bynumber desc>

<!** if list index 1>
messages[3]="<a href='article.tpl?<!** urlparameters>'><font size='-2' class='tick' color='#000000'><b><!** print article name></b><br><!** print section name>, <!** print article date %e> <!** print article date %M></font></a>"
<!** endif>
<!** if list index 2>
messages[4]="<a href='article.tpl?<!** urlparameters>'><font size='-2' class='tick' color='#000000'><b><!** print article name></b><br><!** print section name>, <!** print article date %e> <!** print article date %M></font></a>"
<!** endif>
<!** if list index 3>
messages[5]="<a href='article.tpl?<!** urlparameters>'><font size='-2' class='tick' color='#000000'><b><!** print article name></b><br><!** print section name>, <!** print article date %e> <!** print article date %M></font></a>"
<!** endif>

<!** endlist>
<!** endif>


<!** if list row 3>
<!** list article isnoton frontpage order bynumber desc>

<!** if list index 1>
messages[6]="<a href='article.tpl?<!** urlparameters>'><font size='-2' class='tick' color='#000000'><b><!** print article name></b><br><!** print section name>, <!** print article date %e> <!** print article date %M></font></a>"
<!** endif>

<!** endlist>
<!** endif>


<!** if list row 7>
<!** list article isnoton frontpage isnoton section order bynumber desc>

<!** if list index 1>
messages[7]="<a href='article.tpl?<!** urlparameters>'><font size='-2' class='tick' color='#000000'><b><!** print article name></b><br><!** print section name>, <!** print article date %e> <!** print article date %M></font></a>"
<!** endif>
<!** if list index 2>
messages[8]="<a href='article.tpl?<!** urlparameters>'><font size='-2' class='tick' color='#000000'><b><!** print article name></b><br><!** print section name>, <!** print article date %e> <!** print article date %M></font></a>"
<!** endif>

<!** endlist>
<!** endif>


<!** endlist>
<!** endlist>
<!** endlocal>


///////Do not edit pass this line///////////////////////

if (messages.length>1)
i=2
else
i=0

function move1(whichlayer){
tlayer=eval(whichlayer)
if (tlayer.top>0&&tlayer.top<=5){
tlayer.top=0
setTimeout("move1(tlayer)",3000)
setTimeout("move2(document.main.document.second)",3000)
return
}
if (tlayer.top>=tlayer.document.height*-1){
tlayer.top-=5
setTimeout("move1(tlayer)",100)
}
else{
tlayer.top=scrollerheight
tlayer.document.write(messages[i])
tlayer.document.close()
if (i==messages.length-1)
i=0
else
i++
}
}

function move2(whichlayer){
tlayer2=eval(whichlayer)
if (tlayer2.top>0&&tlayer2.top<=5){
tlayer2.top=0
setTimeout("move2(tlayer2)",3000)
setTimeout("move1(document.main.document.first)",3000)
return
}
if (tlayer2.top>=tlayer2.document.height*-1){
tlayer2.top-=5
setTimeout("move2(tlayer2)",100)
}
else{
tlayer2.top=scrollerheight
tlayer2.document.write(messages[i])
tlayer2.document.close()
if (i==messages.length-1)
i=0
else
i++
}
}

function move3(whichdiv){
tdiv=eval(whichdiv)
if (tdiv.style.pixelTop>0&&tdiv.style.pixelTop<=5){
tdiv.style.pixelTop=0
setTimeout("move3(tdiv)",3000)
setTimeout("move4(second2)",3000)
return
}
if (tdiv.style.pixelTop>=tdiv.offsetHeight*-1){
tdiv.style.pixelTop-=5
setTimeout("move3(tdiv)",100)
}
else{
tdiv.style.pixelTop=scrollerheight
tdiv.innerHTML=messages[i]
if (i==messages.length-1)
i=0
else
i++
}
}

function move4(whichdiv){
tdiv2=eval(whichdiv)
if (tdiv2.style.pixelTop>0&&tdiv2.style.pixelTop<=5){
tdiv2.style.pixelTop=0
setTimeout("move4(tdiv2)",3000)
setTimeout("move3(first2)",3000)
return
}
if (tdiv2.style.pixelTop>=tdiv2.offsetHeight*-1){
tdiv2.style.pixelTop-=5
setTimeout("move4(second2)",100)
}
else{
tdiv2.style.pixelTop=scrollerheight
tdiv2.innerHTML=messages[i]
if (i==messages.length-1)
i=0
else
i++
}
}

function startscroll(){
if (document.all){
move3(first2)
second2.style.top=scrollerheight
second2.style.visibility='visible'
}
else if (document.layers){
document.main.visibility='show'
move1(document.main.document.first)
document.main.document.second.top=scrollerheight+5
document.main.document.second.visibility='show'
}
}

window.onload=startscroll


</script>

<ilayer id="main" width=&{scrollerwidth}; height=&{scrollerheight}; bgColor=&{scrollerbgcolor}; background=&{scrollerbackground}; visibility=hide>
<layer id="first" left=0 top=1 width=&{scrollerwidth};>
<script language="JavaScript1.2">

if (document.layers)
document.write(messages[0])

</script>
</layer>
<layer id="second" left=0 top=0 width=&{scrollerwidth}; visibility=hide>
<script language="JavaScript1.2">

if (document.layers)
document.write(messages[1])

</script>
</layer>
</ilayer>

<script language="JavaScript1.2">

if (document.all){
document.writeln('<span id="main2" style="position:relative;width:'+scrollerwidth+';height:'+scrollerheight+';overflow:hiden;background-color:'+scrollerbgcolor+' ;background-image:url('+scrollerbackground+')">')
document.writeln('<div style="position:absolute;width:'+scrollerwidth+';height:'+scrollerheight+';clip:rect(0 '+scrollerwidth+' '+scrollerheight+' 0);left:0;top:0">')
document.writeln('<div id="first2" style="position:absolute;width:'+scrollerwidth+';left:0;top:1;">')
document.write(messages[0])
document.writeln('</div>')
document.writeln('<div id="second2" style="position:absolute;width:'+scrollerwidth+';left:0;top:0;visibility:hidden">')
document.write(messages[1])
document.writeln('</div>')
document.writeln('</div>')
document.writeln('</span>')
}

</script>

			</td>
			<td valign="top" align="right" width="105"><img src="img/logto.gif" width="105" height="90" border="0" alt="Transitions Online"></td>
		</tr>
		<tr>	<td valign="top" colspan="3" width="396">
			<img src="img/stult1.gif" width="396" height="21" border="0" alt="Transitions Online"></td>
		</tr>
		<tr>	<td valign="top" colspan="3" width="396">
			<table width="396" border="0" cellspacing="0" cellpadding="0">
				<tr>
				<td><font class="tick">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</font></td>
				<td valign="top">

<br>
<font size="-1" class="normal">




<!** local>
<!** issue current>
<!** section number 1>
<!** list length 1 article ison frontpage order bynumber desc>
<!** if article type homeintro>
<!** print article body>
<!** endif>
<!** endlist>
<!** endlocal>

<p>
<center><img src="img/222pr.gif" width="340" height="1" border="0" alt=""></center>
<br>


<center><b><a href="http://archive.tol.cz/join.html">Register now</a> <font color="#656565">to gain full access to <i>TOL</i>!</font></b><br>
<b><font color="#656565">Find out more about <i>TOL</i> by reading our</font><br>
<a href="http://archive.tol.cz/faq.html">Frequently Asked Questions</a></b>
</center><br>
<center><img src="img/222pr.gif" width="340" height="1" border="0" alt=""></center>
<br>

<!-- WEEK IN REVIEW START

<!** local><!** list length 1 issue order bynumber desc><!** section number 6><!** list length 1 article order bynumber desc>
<font size="+1"><a href="article.tpl?<!** urlparameters>"><b>Week in Review</b></a>
<br><img src="img/free.gif" border="0" width="23" height"9" alt="Free access for a week"><br>
<!** print article name></font><!** endlist>

<br><font size="-1">

<!** local><!** article off><!** section off><!** section number 16>
<!** list length 1 article order bynumber desc>
<!** if article type ourtake>

<table width="100%" border="0" cellspacing="3" cellpadding="3" bgcolor="#e5e5e5"><td><font size="2" class="normal"><a href="article.tpl?<!** urlparameters>"><b>OUR TAKE: <!** print article name></b></a></font><br><img src="img/free.gif" border="0" width="23" height"9" alt="Free access for a week"></td></table>

<!** endif>
<!** endlist>
<!** endlocal>

	<!** List length 1 article order bynumber desc>
	<!** if article type wir>
		<!** include wirtoc.tpl>
	<!** endif>
	<!** endlist>
<!** endlist>
<!** endlocal>


</font>

<p>
<center><img src="img/222pr.gif" width="340" height="1" border="0" alt=""></center>
<br>


END WIR //-->


<center><b>
A <i>TOL</i> exclusive: <a href="http://archive.tol.cz/links1.html">Annual Reports!</a> <font color="#656565"><br>
Extensive year-in-review surveys of the countries in the region, from top analysts in both East and West.
</font></b>
</center>

<p>
<center><img src="img/222pr.gif" width="340" height="1" border="0" alt=""></center>

<br>
<div align="center"><font size="2" class="normal">
Copyright &copy; 2001<br><strong>Transitions Online</strong></font><br>
<font size="-2" class="normal">
All rights reserved.<br>Chlumova 22, 130 00 Praha 3, Czech Republic, Tel.:
(420 2) 2278 0805,<br>Fax: (420 2) 2278 0804.<br>
E-mail: <a href="mailto:transitions@tol.cz">transitions@tol.cz</a>
<br>Powered by<br><a href="http://www.hp.cz" target="newwindows"><img src="obr/hplogo.gif" width="56" height="52" border="0" alt="Hewlett-Packard"></a>
<!-- 
<br><a href="http://www.redhat.org" target="newwindows"><img src="obr/rhlogo.gif" width="34" height="35" border="0" alt="Red Hat"></a>
<br><a href="http://www.apache.org" target="newwindows"><img src="obr/aplogo.gif" width="140" height="18" border="0" alt="Apache"></a>
//-->
</font>
<p><br>


				</td>
				<td><font class="tick">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</font></td>
				</tr>
			</table>
		</tr>

	</table>
	</td>
	<td bgcolor="#630000" valign="top">
	<table width="107" border="0" cellspacing="0" cellpadding="0">
		<tr><td valign="top" colspan="2">
		<img src="img/logol.gif" width="107" height="111" border="0" alt="Transitions Online"></td></tr>
		<tr><td colspan="2"><br><br><a href="#" onMouseOver="MM_showHideLayers('Layer1','','show')"><img src="img/rsearch.gif" width="93" height="14" border="0" alt="Search TOL" name="ad"></a></td></tr>
		<tr><td colspan="2"><a href="http://archive.tol.cz/mediakit/index.html"><img src="img/radinfo.gif" width="93" height="15" border="0" alt="Ad info"></a></td></tr>
		<tr><td colspan="2"><a href="mailto:transitions@tol.cz"><img src="img/remail.gif" width="93" height="14" border="0" alt="Email us"></a></td></tr>
		<tr><td colspan="2" align="center"><p><br>
		<!** local><!** issue current><!** section number 6><a href="section.tpl?<!** urlparameters>"><img src="obr/weekban1.gif" width="105" height="28" border="0" alt="Week in Review/Our Take"><br><!** list length 1 article order bynumber desc><font size="-2" color="#ffffff" class="normal1"><!** print article name></font></a><!** endlist></td></tr>
		<!** endlocal>
		<tr><td colspan="2" align="center"><p><br>
		<a href="http://archive.tol.cz/russian/index.html"><img src="img/rrus.gif" width="105" height="50" border="0" alt="Selected TOL articles in Russian"></a>

<!-- 

<!** if user loggedin>
   <br><a href="userchgpass.tpl?<!** urlparameters>"><font size="-1" color="#ffffff" class="normal1"><b>> Change</b></font></a><br><a href="userchgpass.tpl?<!** urlparameters>"><font size="-1" color="#ffffff" class="normal1"><b>password</b></font></a>

<!** else>
    <!** if user defined>
	<br><a href="login.tpl?<!** urlparameters>"><font size="-1" color="#ffffff" class="normal1"><b>> Login</b></font></a>
    <!** else>
	<br><a href="login.tpl?<!** urlparameters>"><font size="-1" color="#ffffff" class="normal1"><b>> Login</b></font></a>
	<br><a href="http://archive.tol.cz/join.html"><font size="-1" color="#ffffff" class="normal1"><b>> Join <i>TOL</i></b></font></a>
    <!** endif>
<!** endif>
	<p><a href="http://archive.tol.cz/autopasswd.html"><font size="-1" color="#ffffff" class="normal1"><b>> Lost<br>password?</b></font></a>
	</td></tr>

//-->	

		</td></tr>
	</table>
	</td>

</tr>
</table>
</body>
</html>
<!-- doc end -->