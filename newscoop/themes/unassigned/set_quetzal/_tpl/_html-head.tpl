<!DOCTYPE html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js"> <!--<![endif]-->
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <title>{{ if $gimme->article->defined }}{{ $gimme->article->name }} | {{ elseif $gimme->section->defined }}{{ $gimme->section->name }} | {{ /if }}{{ $gimme->publication->name }}</title>
    <meta name="viewport" content="width=device-width">
    {{ if empty($siteinfo) }}{{ $siteinfo=['description' => '', 'keywords' => ''] }}{{ /if }}
    {{* if an article is active, meta-description of web page will be article's intro, otherwise it will pull site's description from System Preferences (/Configure/System Preferences) *}}
    <meta name="description" content="{{ if $gimme->article->defined }}{{ $gimme->article->deck|strip_tags:false|strip|escape:'html':'utf-8' }}{{ else }}{{ $siteinfo.description }}{{ /if }}">

    {{* if an article is active, meta-keywords will be generated of article keywords (defined on article edit screen), otherwise it will use site-wide keywords from System Preferences (/Configure/System Preferences) *}}
    <meta name="keywords" content="{{ if $gimme->article->defined }}{{ $gimme->article->keywords }}{{ else }}{{$siteinfo.keywords}}{{ /if }}" />

    <link rel="stylesheet" href="{{ url static_file="_css/bootstrap.min.css"}}">
    <link rel="stylesheet" href="{{ url static_file="_css/quetzal.skin.css"}}">
    <link rel="stylesheet" href="{{ url static_file="_css/quetzal.responsive.css"}}">
    <link rel="stylesheet" href="//fonts.googleapis.com/css?family=Asap:400,700">
    <!--[if lte IE 9]>
       <link rel="stylesheet" href="{{ url static_file="_css/quetzal.ie.css"}}">
    <![endif]-->

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

    <link rel="shortcut icon" href="{{ url static_file='_img/favicon.ico' }}">
    <link rel="apple-touch-icon" href="{{ url static_file='_img/apple-touch-icon.png' }}">

   <!--[if lt IE 9]>
       <script src="//html5shiv.googlecode.com/svn/trunk/html5.js"></script>
       <script>window.html5 || document.write('<script src="{{ url static_file='_js/vendor/html5shiv.js'}}"><\/script>')</script>
   <![endif]-->

   <!-- jQuery Library -->
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
    <script>window.jQuery || document.write('<script src="{{ url static_file='_js/vendor/jquery-1.9.1.min.js'}}"><\/script>')</script>

    <!-- Vendor plugins -->
    {{ if $gimme->template->name == 'front.tpl'}}
    <script src="{{ url static_file='_js/vendor/jquery.cycle2.min.js'}}"></script>
    {{/if}}
    {{ if $gimme->template->name == 'article.tpl'}}
    <script src="{{ url static_file='_js/vendor/galleria/galleria-1.2.9.min.js'}}"></script>
    <link href="{{ url static_file='_css/flowplayer_skin/minimalist.css' }}" rel="stylesheet">
    <script src="{{ url static_file='_js/vendor/flowplayer/flowplayer.min.js' }}"></script>
    {{/if}}
    <script src="{{ url static_file='_js/vendor/jquery.timeago.js'}}"></script>
    {{ if $gimme->template->name == 'front.tpl'}}
    <script>
        $(document).ready(function(){
            var pathname = document.location.pathname;
            if (pathname === "/user") {
                $("#user-active").addClass('active');
            } else if (pathname === "/user/filter/f/a-z"){
                $("#user-all").addClass('active');
            } else if (pathname === "/user/filter/f/a-d"){
                $("#user-ad").addClass('active');
            } else if (pathname === "/user/filter/f/e-k"){
                $("#user-ek").addClass('active');
            } else if (pathname === "/user/filter/f/l-p"){
                $("#user-lp").addClass('active');
            } else if (pathname === "/user/filter/f/q-t"){
                $("#user-qt").addClass('active');
            } else if (pathname === "/user/filter/f/u-z"){
                $("#user-uz").addClass('active');
            } else if (pathname === "/user/editors"){
                $("#user-editors").addClass('active');
            }
        });
    </script>
    {{/if}}
  </head>
