<!** With tol body>
	<!** if subtitle number 1>
		<table width="100%" border="0" cellpadding="0" cellspacing="0">
		<tr>
		<td>
		<font size="+1" class="normal">
		<!** print article name></font>
		</td>
		<td align="right" valign="bottom">

			<font size="-2" class="normal">

			<!** local>
			<!** if section number is 3><!** else><!** if section number is 7><!** else>

			<!** if prevsubtitles>
			<a href="article.tpl?<!** urlparameters>"><img src="img/ar1.gif" width="9" height="9" border="0" alt="Previous page"></a>&nbsp;
			<!** else><img src="img/ar1g.gif" width="9" height="9" border="0" alt="Previous page">&nbsp;
			<!** endif>

   			<!** List Subtitle>
			<!** if currentsubtitle><font color="#888888"><!** print list index></font>&nbsp;
			<!** else>
			<a href="article.tpl?<!** urlparameters>"><!** print list index></a>&nbsp;
			<!** endif>
			<!** EndList>

			<!** if nextsubtitles>
			<a href="article.tpl?<!** urlparameters>"><img src="img/ar.gif" width="9" height="9" border="0" alt="Next page"></a>&nbsp;
			<!** else><img src="img/arg.gif" width="9" height="9" border="0" alt="Next page">&nbsp;
			<!** endif>

			<!** endif><!** endif>
			<!** endlocal>

			</font>

		</td></tr>
		<tr><td colspan="2" height="4"><img src="img/222pr.gif" width="450" height="1" border="0" alt="">
		</td></tr>
		</table>

		<br>
		<font size="-1" class="normal">
		<i><!** print article deck></i>
		<!** local>
		<!** if section number is 7><!** else>
		<p>by <a href="#author"><!** print article author></a>
		<!** endif>
		<!** endlocal>
		<br>

	<!** else>

		<table width="100%" border="0" cellpadding="0" cellspacing="0">
		<tr>
		<td>
		<font size="+1" class="normal">
		<!** print article name></font>
		</td>
		<td align="right" valign="middle">

			<font size="-2" class="normal">

			<!** local>
			<!** if section number is 3><!** else><!** if section number is 7><!** else>

			<!** if prevsubtitles>
			<a href="article.tpl?<!** urlparameters>"><img src="img/ar1.gif" width="9" height="9" border="0" alt="Previous page"></a>&nbsp;
			<!** else><img src="img/ar1g.gif" width="9" height="9" border="0" alt="Previous page">&nbsp;
			<!** endif>

			<!** List Subtitle>
			<!** if currentsubtitle><font color="#888888"><!** print list index></font>&nbsp;
			<!** else>
			<a href="article.tpl?<!** urlparameters>"><!** print list index></a>&nbsp;
			<!** endif>
			<!** EndList>

			<!** if nextsubtitles>
			<a href="article.tpl?<!** urlparameters>"><img src="img/ar.gif" width="9" height="9" border="0" alt="Next page"></a>&nbsp;
			<!** else><img src="img/arg.gif" width="9" height="9" border="0" alt="Next page">&nbsp;
			<!** endif>

			<!** endif><!** endif>
			<!** endlocal>

			</font>

		</td></tr>
		</table>

		<font size="-1" class="normal">
		Page <!** if subtitle number 2>2<!** endif><!** if subtitle number 3>3<!** endif><!** if subtitle number 4>4<!** endif><!** if subtitle number 5>5<!** endif><!** if subtitle number 6>6<!** endif><!** if subtitle number 7>7<!** endif><!** if subtitle number 8>8<!** endif>:</font>
		<img src="img/222pr.gif" width="450" height="1" border="0" alt="">



	<!** endif>

	<p><!** print article body>

	<!** local>
	<!** if section number is 3><!** else><!** if section number is 7><!** else>

	<center>
	<font size="-2" class="normal">
	<br>
	"<!** print article name>"<br>
	<!** if prevsubtitles>
	<a href="article.tpl?<!** urlparameters>"><img src="img/ar1.gif" width="9" height="9" border="0" alt="Previous page"></a>&nbsp;
	<!** else><img src="img/ar1g.gif" width="9" height="9" border="0" alt="Previous page">&nbsp;
	<!** endif>

	<!** List Subtitle>
	<!** if currentsubtitle><font color="#888888"><!** print list index></font>&nbsp;
	<!** else>
	<a href="article.tpl?<!** urlparameters>"><!** print list index></a>&nbsp;
	<!** endif>
	<!** EndList>

	<!** if nextsubtitles>
	<a href="article.tpl?<!** urlparameters>"><img src="img/ar.gif" width="9" height="9" border="0" alt="Next page"></a>&nbsp;
	<!** else><img src="img/arg.gif" width="9" height="9" border="0" alt="Next page">&nbsp;
	<!** endif>

	</font><p>
	<font size="-1" class="normal">
	<a href="tolprint.tpl?<!** urlparameters allsubtitles>" target="newwindows"><img src="obr/print.gif" width="23" height="22" border="0" alt="Print this article"></a><br>Print this article
	</center>

	<!** endif><!** endif>
	<!** endlocal>

</p>


<!** EndWith>