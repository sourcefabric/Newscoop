<!-- This is the search template -->
<html>
<head>
<title><!** print publication name>&nbsp;-  Search results</title>

<!** include meta.tpl>

</head>
<body>

<!** include header.tpl>

<!** include sitemap.tpl>

<td valign=top>

<div class=rightfloat>
<!** include userinfo.tpl>
</div>

<!** List length 5 SearchResult orderbydate>

	<!** if list start>
		<h2>Search results</h2>
		
		<div><!** Search search.tpl Search><!** Edit Search keywords> <!** Select Search mode> require ALL words<!** EndSearch></div>
	
		<table>
	<!** endif>

	<tr><td bgcolor=<!** if list index odd>#F1F1F1<!** else>white<!** endif>>
	<!** print list index>.</td><td bgcolor=<!** if list index odd>#F1F1F1<!** else>#ffffff<!** endif>><a href="/look/fastnews/article.tpl?<!** URLParameters article>"><!** print article name></a><!** if not allowed> <img src="/look/fastnews/subscriber.png" width=11 height=11" alt="[S]"><!**endif><br> <i><!** print section name></i>, <!** print issue name><div><small><!** print article intro></small></div>
		</td>
	</tr>

	<!** if list end>
		</table>
			<!** if previousitems>
			<a href="<!** URI template search.tpl>">Previous</a>
		<!** else>
			<font color=#BBBBBB>Previous</font>
		<!** endif>
		|
		<!** if nextitems>
			<a href="<!** URI template search.tpl>">Next</a>
		<!** else>
			<font color=#BBBBBB>Next</font>
		<!** endif>
	<!** endif>

<!** ForEmptyList>
	<!** if search action>
		<h1>No results found</h1>
		<p>Please try rephrasing your search, or using less search terms.</p>
	<!** endif>

<!** EndList>

</td>

<!** include footer.tpl>

</body>
</html>
