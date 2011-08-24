<?xml version="1.0" encoding="UTF-8"?><?xml-stylesheet type="text/xsl" href="http://{{ $gimme->publication->site }}/templates/_css/smap.xsl"?><!-- generator="Newscoop/3.5" -->  
<!-- generated-on="{{$smarty.now|date_format:"%a, %d %b %Y %H:%M:%S"}} +0100" --> 
<?xml version='1.0' encoding='UTF-8'?>
<urlset xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd" xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
{{ list_articles ignore_issue="true" ignore_section="true" ignore_languages="true" order="bypublishdate desc" constraints="section is 10" }}
   <url>
      <loc>http://{{ $gimme->publication->site }}{{ uri }}</loc>
      <lastmod>{{ $gimme->article->publish_date|camp_date_format:"%Y-%m-%d" }}</lastmod>
      <changefreq>daily</changefreq>
      <priority>0.8</priority>
   </url>{{ /list_articles }}{{ list_articles ignore_issue="true" ignore_section="true" ignore_languages="true" order="bypublishdate desc" constraints="section is 20" }}
   <url>
      <loc>http://{{ $gimme->publication->site }}{{ uri }}</loc>
      <lastmod>{{ $gimme->article->publish_date|camp_date_format:"%Y-%m-%d" }}</lastmod>
      <changefreq>daily</changefreq>
      <priority>0.6</priority>
   </url>{{ /list_articles }}{{ set_topic name="categories:en" }}{{ list_subtopics }}
   <url>
      <loc>http://{{ $gimme->publication->site }}/{{ $gimme->language->code }}/?tpid={{ $gimme->topic->identifier }}&amp;tpl=1/</loc>
      <changefreq>weekly</changefreq>
      <priority>0.4</priority>
   </url>{{ /list_subtopics }}{{ set_topic name="tags:en" }}{{ list_subtopics }}
   <url>
      <loc>http://{{ $gimme->publication->site }}/{{ $gimme->language->code }}/?tpid={{ $gimme->topic->identifier }}&amp;tpl=1/</loc>
      <changefreq>weekly</changefreq>
      <priority>0.3</priority>
   </url>{{ /list_subtopics }}
</urlset> 