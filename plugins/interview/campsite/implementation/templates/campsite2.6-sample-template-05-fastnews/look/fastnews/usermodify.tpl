<!** include utility-header.tpl>

<div class=rightfloat>
<!** include userinfo.tpl>
</div>

<h1>Edit your user info</h1>


<!** if user loggedin>
	<!** If User modifyaction>
	<!** If User modifyok>
		<p>User information was updated.
		<!** else>
		<p>There was an error on user info: <!** print user modifyerror>
		<!** include usermodifyform.tpl>
		<!** endif>
	<!** else>
	<!** include usermodifyform.tpl>
	<!** EndIf>
<!** else>
	<p>You are not logged in and not allowed to change user info.
<!** endif>
</td>

<!** include footer.tpl>
</body>
</html>