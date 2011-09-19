<!DOCTYPE html>
<html dir="ltr" xmlns="http://www.w3.org/1999/xhtml" lang="{{ $gimme->language->code }}">
<head>
<title>{{ if $gimme->article->defined }}{{ $gimme->article->name }} | {{ /if }}{{ $siteinfo.title }}{{ if !($gimme->article->defined) }} - {{ $siteinfo.description }}{{ /if }}</title>
{{ if $gimme->article->defined }}
<meta property="og:title" content="{{$gimme->article->name|html_entity_decode|regex_replace:'/&(.*?)quo;/':'&quot;'}}" />
<meta property="og:type" content="article" />
<meta property="og:url" content="http://{{ $gimme->publication->site }}{{ uri }}" />
<meta property="og:site_name" content="{{ $siteinfo.title }}" />
<meta property="og:description" content="{{$gimme->article->excerpt|strip_tags:false|strip|escape:'html':'utf-8' }}" />
{{ list_article_images }}
<meta property="og:image" content="{{ $gimme->article->image->imageurl }}" />
{{ /list_article_images }}
{{ if $gimme->section->number == "20" }}<meta property="og:image" content="/_img/miniblog-150x150.jpg" />{{ /if }}
{{ /if }}

<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta name="description" content="{{ if $gimme->article->defined }}{{ $gimme->article->excerpt|strip_tags:false|strip|escape:'html':'utf-8' }}{{ else }}{{ $siteinfo.description }}{{ /if }}">
<meta name="keywords" content="{{ if $gimme->article->defined }}{{ local }}{{ list_article_topics root="tags:en" }}{{ $gimme->topic->name }}{{ if $gimme->current_list->at_end }}{{ else }}, {{ /if }}{{ /list_article_topics }}{{ /local }}{{ else }}{{$siteinfo.keywords}}{{ /if }}" />
<meta name="generator" content="Newscoop 3.6">
<link rel="canonical" href="http://{{ $gimme->publication->site }}{{ uri }}" />

<link rel="stylesheet" href="{{ url static_file='_css/style.css' }}" type="text/css" media="screen">
<link rel="alternate" type="application/rss+xml" title="RSS 2.0" href="http://{{ $gimme->publication->site }}/?tpl=2142">
<link rel="shortcut icon" type="image/x-png" href="{{ url static_file='_img/favicon.png' }}">

  <!-- Grab Google CDNs jQuery. fall back to local if necessary -->
  <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.4/jquery.min.js"></script>
  <script>!window.jQuery && document.write(unescape('%3Cscript src="{{ url static_file='_js/jquery-1.4.4.min.js' }}"%3E%3C/script%3E'))</script>

</head>