<!** local>

	<!** issue off>


	<!** List length 10 article order bynumber desc>

	<!** if section number is 4><!** if list start><center><b>MEDIA ARTICLES</b></center><!** endif><p><!** endif>

<!** if list index 1>
There are no articles published in this section for this month. Below, please find the list of available articles from previous months.<p>
<!** endif>

	<!** if article type tol>
		<a href="article.tpl?<!** urlparameters>"><!** print article name></a><br>
		<i><!** print article deck></i><br>
		<!** if section number is 7><!** else>by <!** print article author><br><!** endif>
		<!** print article date %e> <!** print article date %M> <!** print article date %Y><p>
	<!** endif>


	<!** if list end>
	<!** if previousitems>
		<a href="section.tpl?<!** urlparameters>">Previous</a>
	<!** endif>
&nbsp;&nbsp;&nbsp;
	<!** if nextitems>
		<a href="section.tpl?<!** urlparameters>">Next</a>
	<!** endif>
	<!** endif>

	<!** endlist>

	<!** issue current>
<!** endlocal>
<br>