<!doctype html>
<html lang="en">

<head>
    <title>{{ strip }}
        {{ if $gimme->article->defined }}
          {{ if $gimme->article->SEO_title|trim !== "" }}
              {{ $gimme->article->SEO_title|escape:'html'|trim }} |
          {{ else }}
              {{ $gimme->article->name|escape:'html'|trim }} |
          {{ /if }}
        {{ /if }}
        &nbsp;{{ $gimme->publication->name }}
        {{ /strip }}</title>

<link rel="index" title="{{ $gimme->publication->name }}" href="http://{{ $gimme->publication->site }}">
<meta name="generator" content="Newscoop">
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta name="robots" content="index, follow">
<meta name="description=" content="{{ strip }}
{{ if $gimme->article->SEO_description|strip_tags|trim !== "" }}
    {{ $gimme->article->SEO_description|strip_tags|escape:'html'|trim }}
{{ else }}
    {{ if $gimme->article->deck|strip_tags|trim !== "" }}
        {{ $gimme->article->deck|strip_tags|escape:'html'|trim }}
    {{ else }}
        {{ $gimme->article->full_text|strip_tags|escape:'html'|trim|truncate:150 }}
    {{ /if }}
{{ /if }}
{{ /strip }}" />
{{ strip }}
    {{ if $gimme->template->name == "article.tpl" }}
        <link rel="canonical" href="{{ url options="article" }}" />
    {{ /if }}
    {{ if $gimme->template->name == "section.tpl" }}
        <link rel="canonical" href="{{ url options="section" }}" />
    {{ /if }}
    {{ if $gimme->template->name == "front.tpl" }}
        <link rel="canonical" href="http://{{ $gimme->publication->site }}" />
    {{ /if }}
{{ /strip }}
<meta name="viewport" content="width=device-width; initial-scale=1.0; maximum-scale=1.0;"/>

{{ if $gimme->article->defined }}
  <meta property="og:title" content="{{$gimme->article->name|html_entity_decode|regex_replace:'/&(.*?)quo;/':'&quot;'}}" />
  <meta property="og:type" content="article" />
  <meta property="og:url" content="http://{{ $gimme->publication->site }}{{ uri }}" />
  <meta property="og:site_name" content="{{ $gimme->publication->name }}" />
  <meta property="og:description" content="{{$gimme->article->deck|strip_tags:false|strip|escape:'html':'utf-8' }}" />
  {{ list_article_images }}
  <meta property="og:image" content="{{ $gimme->article->image->imageurl }}" />
  {{ /list_article_images }}
{{ /if }}
{{ if $gimme->url->get_parameter('logout') == 'true' }}
  <META HTTP-EQUIV="Set-Cookie" CONTENT="LoginUserId=; path=/">
  <META HTTP-EQUIV="Set-Cookie" CONTENT="LoginUserKey=; path=/">
  {{ $gimme->url->reset_parameter('logout') }}
  <META HTTP-EQUIV="Refresh" content="0;url={{ uri }}">
{{ /if }} 

<!-- 1140px Grid styles for IE -->
<!--[if lte IE 9]><link rel="stylesheet" href="css/ie.css" type="text/css" media="screen" /><![endif]-->
<!-- The 1140px Grid - http://cssgrid.net/ -->
<link rel="stylesheet" href="{{ url static_file='_css/grid/1140.css' }}" type="text/css" media="screen" />
<!--css3-mediaqueries-js - http://code.google.com/p/css3-mediaqueries-js/ - Enables media queries in some unsupported browsers-->
<!-- <script type="text/javascript" src="{{ url static_file='_js/css3-mediaqueries.js' }}"></script> -->

<link href="{{ url static_file='_css/default-zen/general.css' }}" media="screen" rel="stylesheet" type="text/css" />

<link rel="alternate" type="application/rss+xml" title="RSS 2.0" href="http://{{ $gimme->publication->site }}/?tpl=1341">
    
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js"></script>
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.1/jquery-ui.min.js"></script>

<script type="text/javascript">
// var tb_pathToImage = "{{ url static_file='_img/loadingAnimation.gif' }}";
</script>
</head>
