<!** include htmlheader.tpl>

<!-- This is the issue template -->

<html>
<head>
<title><!** print article name> (<!** print publication name>, #<!** print issue number>) <!** if issue iscurrent> -- current issue<!** endif></title>

<!** include meta.tpl>

</head>
<body>

<!** include header.tpl>

<!** include sitemap-issue.tpl>

<!-- This is the presentation of issue -->
<td valign=top class="artlist">

<div class="rightfloat">
<!** include userinfo.tpl>
<!** include printbox.tpl>
</div>

<div class=csmain>

<h1>Welcome to Fastnews</h1>

<!** if issue iscurrent>
<p><small>Note, this is a demonstration site only! Don't expect this site to stick around for long!</small></p>
<!** endif>

<!** local>
<!** section off>
<!** List article OnFrontPage is on order bynumber desc>

	<div style="clear:left">
	<p><b><a href="/look/fastnews/article.tpl?<!** URLParameters reset_subtitle_list>">
	<!** print article name></a></b><!** if not allowed> <img src="/look/fastnews/subscriber.png" width=11 height=11" alt="[S]"><!**endif><br>
	<small><!** print article date "%W, %M %e, %Y"></small><br>
	<!** if article translated_to ro>
		-- [ro]
	<!** endif>

	<!** print article intro></p>
	</div>

	<!** foremptylist>
	<p>No articles to show on frontpage
<!** EndList>
<!** endlocal>
<!-- End of Issue presentation -->
</div>
</td>

<!** include footer.tpl>

</body>
</html>
