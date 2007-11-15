<!** include utility-header.tpl>

<h1>Subscribe to to <!** print publication name></h1>

<!** If User addaction>
	<!** If User addok>
	<!** include subscribe.tpl>
	<!** else>
	<p>There was an error on user info: <!** print user adderror>
	<!** include useraddform.tpl>
	<!** endif>
<!** else>
	<!** include useraddform.tpl>
<!** EndIf>
</td>

<!** include footer.tpl>
</body>
</html>