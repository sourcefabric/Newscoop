{{ set_default_publication }}
{{ $campsite->url->reset_parameter("tpl") }}
{{ $campsite->url->reset_parameter("tpid") }}
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta name="generator" content="Campsite 3.x" /> <!-- leave this for stats -->
{{ if $campsite->article->defined }}
  <meta name="description" content="{{ $campsite->article->Deck }}">
  <meta name="keywords" content="{{ list_article_topics }}{{ $campsite->topic->name }}{{ if $campsite->current_article_topics_list->at_end }}{{ else }}, {{ /if }}{{ /list_article_topics }}"> 
{{ /if }}
<title>
    {{ $campsite->publication->name }}
    {{ if $campsite->section->defined }}
        | {{ $campsite->section->name }}
    {{ /if }}
    {{ if $campsite->topic->defined }}
        | {{ $campsite->topic->name }}
    {{ /if }}
    {{ if $campsite->article->defined }}
        | {{ $campsite->article->name }}
    {{ /if }}
</title>
<LINK href="/templates/classic/css/cleanblue/style.css" rel="stylesheet" type="text/css">
<script type="text/javascript" src="/templates/classic/js/javascript.js"></script>
<link rel="alternate" type="application/rss+xml" title="RSS" href="{{ url options="template classic/tpl/rss.tpl" }}" />
</head>
