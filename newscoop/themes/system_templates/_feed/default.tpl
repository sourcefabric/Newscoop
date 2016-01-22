{{ set_language name="English" }}
<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom" xmlns:media="http://search.yahoo.com/mrss/">
  <channel>
    <title>{{$gimme->publication->meta_title}}</title>
    <link>https://{{$gimme->publication->site}}</link>
    <description>{{$gimme->publication->meta_description}}</description>
    <language>{{ $gimme->language->code }}</language>
    <copyright>Copyright {{$smarty.now|date_format:"%Y"}}, {{$gimme->publication->name}}</copyright>
    <lastBuildDate>{{$smarty.now|date_format:"%a, %d %b %Y %H:%M:%S"}} +0100</lastBuildDate>
    <ttl>60</ttl>
    <generator>Newscoop</generator>
    <image>
      <url>https:{{ url static_file="_img/logo.png" }}</url>
      <title>{{$gimme->publication->meta_title}}</title>
      <link>http://{{$gimme->publication->site}}</link>
    </image>
    <atom:link href="http://{{ $gimme->publication->site }}{{ generate_url route="newscoop_feed" }}" rel="self" type="application/rss+xml" />
    {{ list_articles length="30" name="recent_articles" ignore_section="true" ignore_issue="true" ignore_publication="true" order="bypublishdate desc"}}
    <item>
      <title>{{$gimme->article->name|html_entity_decode|regex_replace:'/&(.*?)quo;/':'&quot;'}}</title>
      <link>https://{{ $gimme->publication->site }}/+{{ $gimme->article->webcode }}</link>
      <description>
        {{ list_article_images length="1" }}
          &lt;img src="//{{$gimme->publication->site}}/{{$gimme->article->image->getImageUrl(600, 400)}}" border="0" align="left" hspace="5" /&gt;
        {{ /list_article_images }}
        {{$gimme->article->deck|strip_tags:false|strip|escape:'html':'utf-8'}}
        &lt;br clear="all"&gt;
      </description>
      <category domain="{{ uri name="section" }}">{{$gimme->section->name}}</category>
      <atom:author>
        <atom:name>{{ $gimme->article->author->name }}</atom:name>
      </atom:author>
      <pubDate>{{$gimme->article->publish_date|date_format:"%a, %d %b %Y %H:%M:%S"}} +0100</pubDate>
      <guid isPermaLink="true">{{ uri name="article" }}</guid>
    </item>
    {{/list_articles}}
  </channel>
</rss>
