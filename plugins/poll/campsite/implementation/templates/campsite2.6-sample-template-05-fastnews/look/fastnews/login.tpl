
<html>
<head>
<title><!** print article name> (<!** print publication name>, #<!** print issue number>) <!** if issue iscurrent> -- current issue<!** endif></title>

<!** include rss-meta.tpl>

<link rel="stylesheet" media="screen" href="/look/fastnews/fastnews.css" type="text/css">

</head>
<body>

<!**local><!** issue current>
<!** include header.tpl>

<!** include sitemap.tpl>
<!** endlocal>

<td valign=top>

<h1>Log in 
<!** if publication defined>
	to <!** print publication name>
<!** endif>
</h1>

<!** include login-form.tpl>

<p>If you don't have a username and password, you'll need to <b>subscribe</b> before you can access certain content:</p>

<p class="opt"><a href="<!** URI template useradd.tpl>&SubsType=trial">Free trial subscription</a></p>
<p class="opt"><a href="<!** URI template useradd.tpl>&SubsType=paid">Buy a full subscription</a></p>

<!** include footer.tpl>
</body>
</html>