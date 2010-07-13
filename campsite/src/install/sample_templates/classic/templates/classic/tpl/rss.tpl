<?xml version="1.0" encoding="UTF-8"?>
{{ set_default_publication }}
{{ unset_issue }}
{{ php }}
setlocale(LC_TIME, 'en_GB');
{{ /php }}
  
<rss version="2.0"
  xmlns:content="http://purl.org/rss/1.0/modules/content/"
  xmlns:wfw="http://wellformedweb.org/CommentAPI/"
  xmlns:dc="http://purl.org/dc/elements/1.1/"
  xmlns:atom="http://www.w3.org/2005/Atom"
  xmlns:sy="http://purl.org/rss/1.0/modules/syndication/"
  xmlns:media="http://search.yahoo.com/mrss/"
  >

<channel>
  <title>{{ $campsite->publication->name }}</title>
  <atom:link href="{{ url }}" rel="self" type="application/rss+xml" />
  <link>{{ url }}</link>
  <description>The latest news on {{ $campsite->publication->name }}.</description>
  <pubDate>{{ $smarty.now|date_format:"%a, %d %b %Y %T +0000" }}</pubDate>
  <generator>http://code.campware.org/projects/campsite</generator>
  <language>{{ $campsite->publication->default_language->code }}</language>
  <sy:updatePeriod>hourly</sy:updatePeriod>
  <sy:updateFrequency>1</sy:updateFrequency>
  <image>
    <url>{{ url options="root_level"}}/templates/classic/img/logo.png</url>
    <title>{{ $campsite->publication->name }} News</title>
    <link>{{ url options="publication" }}</link>
  </image>
  
{{ list_articles length="20" order="bypublish_date desc" }}
  <item>
    <title>{{ $campsite->article->name }}</title>
    <link>{{ url }}</link>
    <comments>{{ url }}#comments]</comments>
    
    <pubDate>{{ $campsite->article->publish_date|date_format:"%a, %d %b %Y %T +0000" }}</pubDate>
    <dc:creator>{{ $campsite->article->owner->name }}</dc:creator>
    
    {{ list_subtopics }}
      <category><![CDATA[{{ $campsite->topic->name }}]]></category>
    {{ /list_subtopics }}
    
    <guid isPermaLink="false">{{ url }}</guid>
    
    <description><![CDATA[{{ $campsite->article->Lead_and_SMS }}]]></description>

    <content:encoded><![CDATA[{{ $campsite->article->body }}]]></content:encoded>
  
    {{ list_article_images length="10" }}
        {{if $campsite->image->article_index < 99 }}
            <media:content url="{{ url options="root_level }}get_img.php?{{ urlparameters options="image" }}" medium="image">
              <media:title type="html">{{ $campsite->image->description }}</media:title>
            </media:content>
        {{ /if }}
    {{ /list_article_images }}

  </item>
{{ /list_articles }}
 
</channel>

</rss>
