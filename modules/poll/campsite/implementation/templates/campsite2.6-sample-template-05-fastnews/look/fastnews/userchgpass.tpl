<!** include utility-header.tpl>

<div class=rightfloat>
<!** include userinfo.tpl>
</div>

<h1>Edit your user info</h1>

<!** if user loggedin>
	<!** If User modifyaction>
	<!** If User modifyok>
		<p>Password was changed.
		<!** else>
		<p>There was an error on changing password: <!** print user modifyerror>
		<!** include userchgpassform.tpl>
		<!** endif>
	<!** else>
	<!** include userchgpassform.tpl>
	<!** EndIf>
<!** else>
	<p>You are not logged in and not allowed to change password.
<!** endif>

<!** include footer.tpl>
</body>
</html>
