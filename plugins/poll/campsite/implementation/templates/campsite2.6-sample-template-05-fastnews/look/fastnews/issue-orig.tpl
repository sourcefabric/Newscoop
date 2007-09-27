<!-- This is the issue template -->
<html>
<body bgcolor=white>
<!** include header.tpl>
<div align="center">
<table bgcolor=white width=800>
<tr bgcolor=white>
	<td align=center><h1><!** print publication name></h1></td>
	<td><h2><!** print issue name> (#<!** print issue number>) <!** if issue iscurrent> (current issue)<!** endif></h2> on <!** date "%W, %M %e %Y"></td>
	<td align=right><!** include userinfo.tpl></td>
</tr>
<tr bgcolor=white><td colspan=3><hr></td></tr>
<tr>
	<td width="20%" valign=top>
	<table bgcolor=#eaeaea width="100%" bgcolor=#eaeaea>
	<tr><td align=center width="20%"><h3>Sections</h3></td></tr>
	<tr><td><p>
<!-- This is the menu for sections -->
	<!** List Section>
	<li><a href="<!** URI reset_article_list>"><!** Print Section Name></a>
	<!** EndList Section>
<!-- End of sections menu -->
	</td></tr>
	<tr><td><hr size=1 noshade></td>
	<tr>
		<td>
		<b>Search articles</b>
		<!** Search search.tpl Search>
			<p><!** Edit Search keywords> <p><!** Select Search mode> match all keywords<p>
		<!** EndSearch>
		</td>
	</tr>
	</table>
	</td>
	<td colspan=2>
<!-- This is the presentation of issue -->
<!** local>
<!** section off>
<!** List article OnFrontPage is on order bynumber desc>
	<!** if List start>
	<p>List of front-page articles<p>
	<p><font color=red>The following articles were marked as 'Show article on front page' 
		by the journalist. This template contains the 'List article OnFrontPage is on'
		command.</font></p>
		<ul>
		<ul>
	<!** endif>
	<!** if article translated_to ro>
	<p>This article is translated into Romanian
	<!** endif>
	<li><b><a href="<!** URI reset_subtitle_list>">
	<!** print article name></a></b><br>
		<font size=-1 face="arial">
	<!** if article type extended>
		<i><!** print article author></i>, <!** print article date "%W, %M %e, %Y">
	<!** endif>
	<!** if article type fastnews>
		<!** print article date "%W, %M %e, %Y">
	<!** endif>
		</font>
	<br><!** print article intro><br><br>
	<!** if list end>
		</ul>
	<!** endif>
	<!** foremptylist>
	<p>No articles to show on frontpage
<!** EndList>
<!** endlocal>
<!-- End of Issue presentation -->
	</td>
</tr>
</table>
</div>
<!** include footer.tpl>
</body>
</html>
