{{* We used http://html5boilerplate.com/ as starting point for this theme *}}
<!doctype html>
<!-- paulirish.com/2008/conditional-stylesheets-vs-css-hacks-answer-neither/ -->
<!--[if lt IE 7 ]> <html class="no-js ie6" lang="en"> <![endif]-->
<!--[if IE 7 ]>    <html class="no-js ie7" lang="en"> <![endif]-->
<!--[if IE 8 ]>    <html class="no-js ie8" lang="en"> <![endif]-->
<!--[if (gte IE 9)|!(IE)]><!--> <html class="no-js" lang="en"> <!--<![endif]-->
<head>
  <meta charset="utf-8">

  <!-- Always force latest IE rendering engine (even in intranet) & Chrome Frame
       Remove this if you use the .htaccess -->
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">

  <title>{{ if $gimme->article->defined }}{{ $gimme->article->name }} | {{ elseif $gimme->section->defined }}{{ $gimme->section->name }} | {{ /if }}{{ $gimme->publication->name }}</title>
  <meta name="author" content="Ljuba Rankovic" >
{{ if empty($siteinfo) }}{{ $siteinfo=['description' => '', 'keywords' => ''] }}{{ /if }}
  {{* if an article is active, meta-description of web page will be article's intro, otherwise it will pull site's description from System Preferences (/Configure/System Preferences) *}}
  <meta name="description" content="{{ if $gimme->article->defined }}{{ $gimme->article->deck|strip_tags:false|strip|escape:'html':'utf-8' }}{{ else }}{{ $siteinfo.description }}{{ /if }}">
  {{* if an article is active, meta-keywords will be generated of article keywords (defined on article edit screen), otherwise it will use site-wide keywords from System Preferences (/Configure/System Preferences) *}}
  <meta name="keywords" content="{{ if $gimme->article->defined }}{{ $gimme->article->keywords }}{{ else }}{{$siteinfo.keywords}}{{ /if }}" />
  <meta name="generator" content="Bluefish 2.0.3" >  

  <!-- RSS & Pingback -->
  <link rel="alternate" type="application/rss+xml" title="RSS 2.0" href="http://{{ $gimme->publication->site }}/en/static/rss/">

{{ if $gimme->article->defined }}{{* Open Graph protocol metatags for Facebook sharing *}}
  <meta property="og:title" content="{{$gimme->article->name|html_entity_decode|regex_replace:'/&(.*?)quo;/':'&quot;'}}" />
  <meta property="og:type" content="article" />
  <meta property="og:url" content="http://{{ $gimme->publication->site }}{{ uri }}" />
  <meta property="og:site_name" content="{{ $gimme->publication->name }}" />
  <meta property="og:description" content="{{$gimme->article->deck|strip_tags:false|strip|escape:'html':'utf-8' }}" />
{{ list_article_images }}
  <meta property="og:image" content="{{ $gimme->article->image->imageurl }}" />
{{ /list_article_images }}
{{ /if }}

  <!-- Mobile viewport optimized: j.mp/bplateviewport -->
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- Place favicon.ico & apple-touch-icon.png in the root of your domain and delete these references -->
  <link rel="shortcut icon" href="{{ url static_file='_img/favicon.ico' }}">
  <link rel="apple-touch-icon" href="{{ url static_file='_img/apple-touch-icon.png' }}">


  <!-- CSS: implied media="all" -->
  <link rel="stylesheet" href="{{ url static_file='_css/main.css?v=2' }}">


  <!-- Uncomment if you are specifically targeting less enabled mobile browsers
  <link rel="stylesheet" media="handheld" href="{{ url static_file='_css/handheld.css?v=2' }}"> -->

{{ if !($gimme->template->name == "article.tpl") }}  
  <!-- styles for sidebar tabs - jquery ui custom build package; not needed on article page -->
  <link rel="stylesheet" href="{{ url static_file='_css/ui-lightness/jquery-ui.custom.css' }}" />
{{ /if }}

{{ if $gimme->template->name == "article.tpl" }}  
  <!-- styles for fancybox, used on article page -->
  <link rel="stylesheet" href="{{ url static_file='_css/fancybox/jquery.fancybox-1.3.4.css' }}" />    
{{ /if }}

  <!-- Although all JavaScript is the bottom, Jquery needs to be here for Newscoop's Geolocation/Map functionality -->
  <!-- Grab Google CDN's jQuery, with a protocol relative URL; fall back to local if necessary -->

  <script src="{{ url static_file='_js/libs/jquery.min.js' }}"></script>

  <!-- All JavaScript at the bottom, except for Modernizr which enables HTML5 elements & feature detects -->
  <script src="{{ url static_file='_js/libs/modernizr-1.7.min.js' }}"></script>

  <!-- Video.js -->
  <link href="{{ url static_file="_css/video-js.css" }}" rel="stylesheet">
  <script src="{{ url static_file="_js/video.js" }}"></script>

  <!-- picturefill -->
  <script src="{{ url static_file="_js/matchmedia.js" }}"></script>
  <script src="{{ url static_file="_js/picturefill.js" }}"></script>

</head>
