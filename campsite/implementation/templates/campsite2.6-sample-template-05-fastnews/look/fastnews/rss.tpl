<?xml version="1.0" encoding="UTF-8"?>
<?xml-stylesheet type="text/xsl" href="/rss.xsl"?>

<rss version="2.0">

<!** local><!** publication current>
	<channel>
		<title><!** print publication name></title>
		<link>%{URL}</link>
		
		<!-- insert description of this feed -->
		<description>
			All recent articles from <!** print publication name>.
		</description>
		
		<lastBuildDate>%{DATE}</lastBuildDate>

		<generator>http://%{SERVER}/</generator>

		<!-- insert email address for contact person -->
		<managingEditor>%{EMAIL}</managingEditor>
	
		<!-- here are the items which will appear in the feed. be sure to limit the list length -->
		<!-- the description texts will be htmlspecialchars-encoded later by PHP for RSS 2.0 compliance -->
		<!** list length 20 article order bydate desc>

		<item>
			<title><!** print article name></title>
			<description><!** print article intro><!** if not allowed> <img src="/look/fastnews/subscriber.png" width=11 height=11" alt="[S]"><!**endif></description>
			<link>http://%{SERVER}/look/fastnews/article.tpl?<!** URLParameters></link>
			<pubDate><!** print article date></pubDate>
		</item>

		<!** endlist>
		
	</channel>
<!** endlocal>
</rss>
