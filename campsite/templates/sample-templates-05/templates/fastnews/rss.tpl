<?xml version="1.0" encoding="UTF-8"?>
<?xml-stylesheet type="text/xsl" href="/rss.xsl"?>

<rss version="2.0">

{{ local }}{{ set_current_publication }}
	<channel>
		<title>{{ $campsite->publication->name }}</title>
		<link>%{URL}</link>
		
		<!-- insert description of this feed -->
		<description>
			All recent articles from {{ $campsite->publication->name }}.
		</description>
		
		<lastBuildDate>%{DATE}</lastBuildDate>

		<generator>http://%{SERVER}/</generator>

		<!-- insert email address for contact person -->
		<managingEditor>%{EMAIL}</managingEditor>
	
		<!-- here are the items which will appear in the feed. be sure to limit the list length -->
		<!-- the description texts will be htmlspecialchars-encoded later by PHP for RSS 2.0 compliance -->
		{{ list_articles length="20" order="bydate desc" }}

		<item>
			<title>{{ $campsite->article->name }}</title>
			<description>{{ $campsite->article->intro }}{{ if ! $campsite->article->content_accessible }} <img src="/templates/fastnews/subscriber.png" width=11 height=11" alt="[S]">{{ /if }}</description>
			<link>http://%{SERVER}/tpl/fastnews/article.tpl?{{ urlparameters }}</link>
			<pubDate>{{ $campsite->article->date }}</pubDate>
		</item>

		{{ /list_articles }}
		
	</channel>
{{ /local }}
</rss>
