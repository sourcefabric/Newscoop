<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom" xmlns:media="http://search.yahoo.com/mrss/">
<channel>
<title>{{$gimme->publication->name}}</title>
<link>http://{{$gimme->publication->site}}</link>
<description>{{$siteinfo.description}}</description>
<language>{{ $gimme->language->code }}</language>
<copyright>Copyright {{$smarty.now|date_format:"%Y"}}, {{$gimme->publication->name}}</copyright>
<lastBuildDate>{{$smarty.now|date_format:"%a, %d %b %Y %H:%M:%S"}} +0100</lastBuildDate>
<ttl>60</ttl>
<generator>Newscoop</generator>
<image>
<url>{{ url static_file="_img/logo.png" }}</url>
<title>{{$gimme->publication->name}}</title>
<link>http://{{$gimme->publication->site}}</link>
</image>
<atom:link href="http://{{ $gimme->publication->site }}/en/static/rss" rel="self" type="application/rss+xml" />
{{list_articles length="20" ignore_issue="true" ignore_section="true" constraints="type is news" order="bypublishdate desc"}}
<item>
<title>{{$gimme->article->name|html_entity_decode|regex_replace:'/&(.*?)quo;/':'&quot;'}}</title>
<link>http://{{$gimme->publication->site}}/{{ $gimme->language->code }}/{{$gimme->issue->url_name}}/{{$gimme->section->url_name}}/{{$gimme->article->number}}</link>
<description>
{{ list_article_images length="1" }}
&lt;img src="{{$gimme->article->image->thumbnailurl}}" border="0" align="left" hspace="5" /&gt;
{{ /list_article_images }}
{{$gimme->article->deck|strip_tags:false|strip|escape:'html':'utf-8'}}
&lt;br clear="all"&gt;
</description>
<category domain="http://{{$gimme->publication->site}}/{{ $gimme->language->code }}/{{ $gimme->issue->url_name }}/{{$gimme->section->url_name}}">{{$gimme->section->name}}</category>

<atom:author><atom:name>{{ $gimme->article->author->name }}</atom:name></atom:author>

<pubDate>{{$gimme->article->publish_date|date_format:"%a, %d %b %Y %H:%M:%S"}} +0100</pubDate>
<guid isPermaLink="true">http://{{$gimme->publication->site}}/{{ $gimme->language->code }}/{{ $gimme->issue->url_name }}/{{$gimme->section->url_name}}/{{$gimme->article->number}}</guid>
</item>
{{/list_articles}}
</channel>
</rss>
