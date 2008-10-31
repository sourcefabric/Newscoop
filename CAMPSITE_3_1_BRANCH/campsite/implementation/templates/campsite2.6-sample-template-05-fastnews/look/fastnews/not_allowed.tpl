<!** print article intro>

<div class=notallowed>

<!** if user loggedin>
	<p><b><!** print user uname></b>, your subscription does not include this article, or your subscription has expired. 
	<p>You will need either a <a href="<!** URI template useradd.tpl>&SubsType=trial">trial</a> or a
	<a href="<!** URI template useradd.tpl>&SubsType=paid">paid</a> subscription.
<!** else>
	<!** if user defined>
		<p><b><!** print User uname></b>, please <a href="<!** URI template login.tpl>">login</a> in order to view this article.
	<!** else>
		<p>This article is available only to subscribers. <span><!** print publication name></span> is supported through paid subscriptions. If you like our publication, please consider subscribing for full access to our site.</p>

	<!**endif>
<!** endif>

</div>
