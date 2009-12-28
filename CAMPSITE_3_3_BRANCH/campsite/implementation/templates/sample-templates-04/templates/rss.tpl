<?xml version="1.0" encoding="UTF-8"?>
<rss version="2.0">
    <channel>
        <title>Packaged Templates #04 RSS</title>
        <link>http://www.mediaonweb.org</link>
        <description>PT#04 RSS</description>
        <copyright>Copyright PT#04</copyright>
        <language>en</language>
        <generator>Campsite RSS 1.0</generator>
        {{ set_language name="english" }}
        {{ set_current_issue }}
        {{ list_articles constraints="type is article" order="bynumber desc" }}
        <item>
            <title>{{ $campsite->article->name }}</title>
            <link>http://{{ $campsite->publication->site }}{{ uri options="reset_subtitle_list" }}</link>
            <description>{{ $campsite->article->intro }}</description>
        </item>
        {{ /list_articles }}
    </channel>
</rss>