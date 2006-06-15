<!-- This is the extended article PRINT template -->

<h2><!** print article name></h2>
<address><!** print article author><br>
<!** print article date "%W, %M %e %Y"></address>

stuff...
<!** if allowed>
	<!** with extended text>
		<!** list subtitle>
			<!** print article text>
		<!** endlist>
	<!** endwith>
<!** else>
  <!** include not_allowed.tpl>
<!** endif>
