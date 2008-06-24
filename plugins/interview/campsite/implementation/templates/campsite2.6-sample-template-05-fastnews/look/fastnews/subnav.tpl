<!** if allowed>

<!** local article>

<!** if article type fastnews>
	<!** with fastnews body>
		<!** list subtitle>
			<!** if list start>
				<div class="subheads">
				<p class="opt"><b>Article Outline</b></p>
				<ol>
			<!** endif>
	
			<!** if currentsubtitle>
				<li value="<!** print list index>"><!** print subtitle name></li>
			<!** else>
				<li value="<!** print list index>"><a href="<!** URI>"><!** print subtitle name></a></li>
			<!** endif>
	
			<!** if list end>
				</ol>
				<p class=opt><small><a href="<!** uri template article-complete.tpl >">single page view</a></small></p>
				</div>
			<!** endif>
		<!** EndList>
	<!** endwith>
<!** endif>

<!** if article type extended>
	<!** with extended text>
		<!** list subtitle>
			<!** if list start>
				<div class="subheads">
				<p class="opt"><b>Article Outline</b></p>
				<ol>
			<!** endif>
	
			<!** if currentsubtitle>
				<li value="<!** print list index>"><!** print subtitle name></li>
			<!** else>
				<li value="<!** print list index>"><a href="<!** URI>"><!** print subtitle name></a></li>
			<!** endif>
	
			<!** if list end>
				</ol>
				<p class=opt><small><a href="<!** uri reset_subtitle_list template article-complete.tpl >">single page view</a></small></p>
				</div>
			<!** endif>
		<!** EndList>
	<!** endwith>
<!** endif>

<!** endlocal>
<!**endif>