<!-- This is the extended article template -->
<!** if article type extended>

	<h1><!** print article name></h1>
	<address><!** print article author><br>
	<!** print article date "%W, %M %e %Y"></address>

	<!** if allowed>
		
		<!** With extended text>
			<!** print article text>
	
			<div class="nextprev">
			<!** if prevsubtitles>
			<a href="<!** URI>">Previous subhead</a>
				<!** if nextsubtitles>
				|
				<!** endif>
			<!** endif>
			<!** if nextsubtitles>
			<a href="<!** URI>">Next subhead</a>
			<!** endif>
			</div>
	
		<!** EndWith>
	<!** else>
		<!** include not_allowed.tpl>
	<!** endif>
<!** endif>
