<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom" xmlns:media="http://search.yahoo.com/mrss/">
<channel>
<title>{{$campsite->publication->name}}</title>
<link>http://{{$campsite->publication->site}}</link>
<description>{{$siteinfo.description}}</description>
<language>{{ $campsite->language->code }}</language>
<copyright>Copyright {{$smarty.now|date_format:"%Y"}}, {{$campsite->publication->name}}</copyright>
<lastBuildDate>{{$smarty.now|date_format:"%a, %d %b %Y %H:%M:%S"}} +0100</lastBuildDate>
<ttl>60</ttl>
<generator>Campsite</generator>
<image>
<url>http://{{$campsite->publication->site}}/templates/classic/img/logo-rss.jpg</url>
<title>{{$campsite->publication->name}}</title>
<link>http://{{$campsite->publication->site}}</link>
<width>144</width>
<height>19</height>
</image>
<atom:link href="http://{{$campsite->publication->site}}/templates/feed/index-en.rss" rel="self" type="application/rss+xml" />
{{list_articles length="20" order="bypublishdate desc"}}
<item>
<title>{{$campsite->article->name|html_entity_decode|regex_replace:'/&(.*?)quo;/':'&quot;'}}</title>
<link>http://{{$campsite->publication->site}}/ru/{{ $campsite->issue->number }}/{{$campsite->section->url_name}}/{{$campsite->article->number}}</link>
<description>
{{if $campsite->article->has_image(1)}}
&lt;img src="{{$campsite->article->image1->thumbnailurl}}" border="0" align="left" hspace="5" /&gt;
{{/if}}
{{$campsite->article->intro|strip_tags:false|strip|escape:'html':'utf-8'}}
&lt;br clear="all"&gt;
</description>
<category domain="http://{{$campsite->publication->site}}/{{ $campsite->language->code }}/{{ $campsite->issue->number }}/{{$campsite->section->url_name}}">{{$campsite->section->name}}</category>

{{if $campsite->article->author->name}}
<atom:author><atom:name>{{$campsite->article->author->name}}</atom:name></atom:author>
{{/if}}
<pubDate>{{$campsite->article->publish_date|date_format:"%a, %d %b %Y %H:%M:%S"}} +0100</pubDate>
<guid isPermaLink="true">http://{{$campsite->publication->site}}/{{ $campsite->language->code }}/{{ $campsite->issue->number }}/{{$campsite->section->url_name}}/{{$campsite->article->number}}</guid>
</item>
{{/list_articles}}
</channel>
</rss>