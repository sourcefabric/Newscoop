{{ set_default_publication }}
{{ $gimme->url->reset_parameter('tpl') }}
{{ $gimme->url->reset_parameter('tpid') }}
<!DOCTYPE HTML PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><head profile="http://gmpg.org/xfn/11">

    <title>The Journal</title>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
  <meta name="robots" content="index, follow">
    
    <link href="http://{{ $gimme->publication->site }}/templates/set_thejournal/_css/style.css" media="screen" rel="stylesheet" type="text/css" >
    <link href="http://{{ $gimme->publication->site }}/templates/set_thejournal/_css/default.css" rel="stylesheet" type="text/css" /> 
    <link href="http://{{ $gimme->publication->site }}/templates/set_thejournal/_css/shortcodes.css" rel="stylesheet" type="text/css" /> 
    <link href="http://{{ $gimme->publication->site }}/templates/set_thejournal/_css/custom.css" rel="stylesheet" type="text/css" />    

    <link rel="alternate" type="application/rss+xml" title="RSS 2.0" href="http://{{ $gimme->publication->site }}/?tpl=1133">
       
    <!--[if IE 6]>
    <script type="text/javascript" src="http://{{ $gimme->publication->site }}/templates/set_thejournal/_js/pngfix.js"></script>
    <script type="text/javascript" src="http://{{ $gimme->publication->site }}/templates/set_thejournal/_js/menu.js"></script>
    <![endif]-->
       
  <!-- Grab Google CDNs jQuery. fall back to local if necessary -->
  <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.4/jquery.min.js"></script>
  <script>!window.jQuery && document.write(unescape('%3Cscript src="http://{{ $gimme->publication->site }}/templates/set_thejournal/_js/jquery-1.4.4.min.js"%3E%3C/script%3E'))</script> 

<script type="text/javascript">
var tb_pathToImage = 'http://{{ $gimme->publication->site }}/templates/set_thejournal/_img/loadingAnimation.gif';
</script>
<script type="text/javascript" src="http://{{ $gimme->publication->site }}/templates/set_thejournal/_js/tabs.js"></script>
<script type="text/javascript" src="http://{{ $gimme->publication->site }}/templates/set_thejournal/_js/scripts.js"></script>
<script type="text/javascript" src="http://{{ $gimme->publication->site }}/templates/set_thejournal/_js/easing.js"></script>

<link rel="index" title="The Journal" href="http://{{ $gimme->publication->site }}">
<meta name="generator" content="Newscoop">

{{ if $gimme->template->name == "set_thejournal/archive.tpl" }}
<link rel="stylesheet" href="http://{{ $gimme->publication->site }}/templates/set_thejournal/_css/base/jquery.ui.all.css"> 
<script src="http://{{ $gimme->publication->site }}/templates/set_thejournal/_js/jquery-ui-1.8.7.custom.min.js"></script>
<script src="http://{{ $gimme->publication->site }}/templates/set_thejournal/_js/jquery.ui.accordion.js"></script>
<script src="http://{{ $gimme->publication->site }}/templates/set_thejournal/_js/jquery.ui.widget.js"></script>
<script src="http://{{ $gimme->publication->site }}/templates/set_thejournal/_js/jquery.ui.core.js"></script>
<script type="text/javascript"> 
  $(function() {
    $( "#accordion" ).accordion();
  });
  </script> 
{{ /if }}
  
  <style type="text/css">.recentcomments a{display:inline !important;padding:0 !important;margin:0 !important;}</style>

  {{ if $gimme->template->name == "set_thejournal/article.tpl" }}
  <!-- thickbox -->
    <script type="text/javascript" src="http://{{ $gimme->publication->site }}/templates/set_thejournal/_js/thickbox-compressed.js"></script>
    <link href="http://{{ $gimme->publication->site }}/templates/set_thejournal/_css/thickbox.css" rel="stylesheet" type="text/css" />
  <!-- / thickbox -->  
  {{ /if }}  
</head>
