<!-- This is the fastnews article template -->

<!** if article type fastnews>

	<h1><!** print article name></h1>
	<address><!** print article date "%W, %e %M %Y"></address>

	<!** if allowed>
		<!** with fastnews body>
			
			<!** print article body>
						
			<div class="nextprev">
			<!** if prevsubtitles>
				<a href="<!** URI>">Previous subhead</a>
				<!** if nextsubtitles> | <!** endif>
				
			<!** endif>
			<!** if nextsubtitles>
				<a href="<!** URI>">Next subhead</a>
			<!** endif>
			</div>
	
		<!** EndWith>
	<!** else>
	<!** include not_allowed.tpl>
	<!** endif>
<!** else>
	You can't use this template with this article!
<!** endif>
