<!DOCTYPE HTML PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><head profile="http://gmpg.org/xfn/11">
<head>
    <title>The Journal</title>

    {{ if $gimme->url->get_parameter('logout') == 'true' }}
  <META HTTP-EQUIV="Set-Cookie" CONTENT="LoginUserId=; path=/">
  <META HTTP-EQUIV="Set-Cookie" CONTENT="LoginUserKey=; path=/">
  {{ $gimme->url->reset_parameter('logout') }}
  <META HTTP-EQUIV="Refresh" content="0;url={{ uri }}">
  {{ /if }} 
  
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
  <meta name="robots" content="index, follow">
    
    <link href="{{ url static_file='_css/style.css' }}" media="screen" rel="stylesheet" type="text/css" >
    <link href="{{ url static_file='_css/default.css' }}" rel="stylesheet" type="text/css" /> 
    <link href="{{ url static_file='_css/shortcodes.css' }}" rel="stylesheet" type="text/css" /> 
    <link href="{{ url static_file='_css/custom.css' }}" rel="stylesheet" type="text/css" />    

    <link rel="alternate" type="application/rss+xml" title="RSS 2.0" href="http://{{ $gimme->publication->site }}/?tpl=1133">
       
    <!--[if IE 6]>
    <script type="text/javascript" src="{{ url static_file='_js/pngfix.js' }}"></script>
    <script type="text/javascript" src="{{ url static_file='_js/menu.js' }}"></script>
    <![endif]-->
       
  <!-- Grab Google CDNs jQuery. fall back to local if necessary -->
  <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.4/jquery.min.js"></script>
  <script>!window.jQuery && document.write(unescape('%3Cscript src="{{ url static_file='_js/jquery-1.4.4.min.js' }}"%3E%3C/script%3E'))</script> 

<script type="text/javascript">
var tb_pathToImage = "{{ url static_file='_img/loadingAnimation.gif' }}";
</script>
<script type="text/javascript" src="{{ url static_file='_js/tabs.js' }}"></script>
<script type="text/javascript" src="{{ url static_file='_js/scripts.js' }}"></script>
<script type="text/javascript" src="{{ url static_file='_js/easing.js' }}"></script>

<link rel="index" title="The Journal" href="http://{{ $gimme->publication->site }}">
<meta name="generator" content="Newscoop">

{{ if $gimme->template->name == "archive.tpl" }}
<link rel="stylesheet" href="{{ url static_file='_css/base/jquery.ui.all.css' }}"> 
<script src="{{ url static_file='_js/jquery-ui-1.8.7.custom.min.js' }}"></script>
<script src="{{ url static_file='_js/jquery.ui.accordion.js' }}"></script>
<script src="{{ url static_file='_js/jquery.ui.widget.js' }}"></script>
<script src="{{ url static_file='_js/jquery.ui.core.js' }}"></script>
<script type="text/javascript"> 
  $(function() {
    $( "#accordion" ).accordion();
  });
  </script> 
{{ /if }}
  
  <style type="text/css">.recentcomments a{display:inline !important;padding:0 !important;margin:0 !important;}</style>

  {{ if $gimme->template->name == "article.tpl" }}
  <!-- thickbox -->
    <script type="text/javascript" src="{{ url static_file='_js/thickbox-compressed.js' }}"></script>
    <link href="{{ url static_file='_css/thickbox.css' }}" rel="stylesheet" type="text/css" />
  <!-- / thickbox -->  
  {{ /if }}  
</head>
