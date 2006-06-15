<!** include htmlheader.tpl>

<html>
<head>
<title><!** print section name >&#32;(<!** print publication name>, #<!** print issue number>) <!** if issue iscurrent> -- current issue<!** endif></title>

<!** include meta.tpl>

</head>
<body>

<!** include header.tpl>

<!** include sitemap-section.tpl>


<td class=csmain valign=top>

<div class="rightfloat">
<!** include userinfo.tpl>
<!** include printbox.tpl>
</div>

<div class=csmain>

<h1><!** print section name></h1>
<address>Issue #<!** print issue number>, <!** print issue name></address>

<!-- List of articles to be shown on section -->

<!** List article OnSection is on>
	<!** if List start>
		<ul>
	<!** endif>

	<li><b><a href="/look/fastnews/article.tpl?<!** URLParameters>">
	<!** print article name></a></b><!** if not allowed> <img src="/look/fastnews/subscriber.png" width=11 height=11" alt="[S]"><!**endif><br>
	<!** if article type extended>
		<i><!** print article author></i>, <!** print article date "%W, %M %e %Y">
	<!** endif>
	<!** if article type fastnews>
		<!** print article date "%W, %M %e %Y">
	<!** endif>
	<br><!** print article intro><br><br>
	<!** foremptylist>
	<p>There are no articles to be shown on section page
<!** endlist>
</ul>
</td>
<!-- End of articles list to be shown on section -->

</div>

<!** include footer.tpl>
</body>
</html>