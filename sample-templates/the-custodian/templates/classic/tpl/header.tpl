{{ set_default_publication }}
{{ $gimme->url->reset_parameter("tpl") }}
{{ $gimme->url->reset_parameter("tpid") }}
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
  <meta name="generator" content="Campsite 3.x" /> <!-- leave this for stats -->
  <meta name="description" content="{{ if $gimme->article->defined }}{{ $gimme->article->Deck }}{{ else }}{{$siteinfo.description}}{{ /if }}">
  <meta name="keywords" content="{{if $gimme->article->keywords}}{{$gimme->article->keywords}},{{/if}}{{$siteinfo.keywords}}" />

 <link rel="alternate" type="application/rss+xml" title="{{$gimme->publication->name}}" href="http://{{$gimme->publication->site}}/templates/feed/index-{{ $gimme->language->code }}.rss" />

  <title>{{if $gimme->article->defined}} {{$gimme->article->name}} | {{elseif $gimme->section->defined}} {{$gimme->section->name}} | {{/if}}{{ $siteinfo.title }}</title>

  <link href="http://{{ $gimme->publication->site }}/templates/classic/css/cleanblue/style.css" rel="stylesheet" type="text/css">
  <script type="text/javascript" src="http://{{ $gimme->publication->site }}/templates/classic/js/javascript.js"></script>

  <!-- jquery -->
  <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js"></script>
  
  <!-- fancybox -->
  <script type="text/javascript" src="http://{{ $gimme->publication->site }}/templates/classic/js/fancybox/jquery.fancybox-1.3.1.pack.js"></script>
  <script type="text/javascript" src="http://{{ $gimme->publication->site }}/templates/classic/js/fancybox/jquery.easing-1.3.pack.js"></script>
  <script type="text/javascript" src="http://{{ $gimme->publication->site }}/templates/classic/js/fancybox/jquery.mousewheel-3.0.2.pack.js"></script>
  <link rel="stylesheet" href="http://{{ $gimme->publication->site }}/templates/classic/js/fancybox/jquery.fancybox-1.3.1.css" type="text/css" media="screen" />    

  <!-- jquery tools: Tabs, Tooltip, Scrollable and Overlay (4.05 Kb) -->
  <script src="http://cdn.jquerytools.org/1.2.5/tiny/jquery.tools.min.js"></script>
  <!-- jquery tools: Navigator plugin -->
  <script src="http://{{ $gimme->publication->site }}/templates/classic/js/scrollable.navigator.js"></script>
</head>
