		<font size="+1" class="normal"><b><i><!** print article name></i></b></font><br>
		<img src="img/222pr.gif" width="450" height="1" border="0" alt="">
		<b>Ourtake: <!** print article ourtake></b>
		<img src="img/222pr.gif" width="450" height="1" border="0" alt="">
		<ul>
	<!** With wir body>
		<!** list subtitle>
		<!** if list index 1>
			<!** if currentsubtitle><!** else>
			<li><a href="article.tpl?<!** urlparameters fromstart>"><b><!** print article title></b></a>
			<!** endif>
		<!** else>
			<!** if currentsubtitle>
			<!** else><li><a href="article.tpl?<!** urlparameters>"><!** print subtitle name></a>
			<!** endif>
		<!** endif>
		<!** endlist>
		</ul>
		<!** list subtitle>
		<!** if list index 1>
			<!** if currentsubtitle>
			<b><!** print article title></b><p>
			<!** endif>
		<!** endif>
		<!** endlist>
		<!** print article body>
	<!** endwith>
