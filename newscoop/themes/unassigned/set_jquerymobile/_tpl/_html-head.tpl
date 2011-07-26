<!DOCTYPE HTML PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<!--DOCTYPE html--> 
<html>
<head>
{{ if $gimme->url->get_parameter('logout') == 'true' }}
  <META HTTP-EQUIV="Set-Cookie" CONTENT="LoginUserId=; path=/">
  <META HTTP-EQUIV="Set-Cookie" CONTENT="LoginUserKey=; path=/">
  {{ $gimme->url->reset_parameter('logout') }}
  <META HTTP-EQUIV="Refresh" content="0;url={{ uri }}">
{{ /if }} 
  
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
  <meta name="robots" content="index, follow">

  <link rel="stylesheet" href="{{ url static_file='_css/jquery.mobile-1.0a4.1.min.css' }}" />
  <link rel="stylesheet" href="{{ url static_file='_css/jquery.mobile-1.0a4.1.skin-journalist.css' }}" />

  <script src="{{ url static_file='_js/jquery-1.5.min.js' }}"></script>
<script>
$(document).bind("mobileinit", function(){
  //switch off Ajax for forms
//   $.mobile.ajaxFormsEnabled = false;
});
</script>
  <script src="{{ url static_file='_js/jquery.mobile-1.0a4.1.min.js' }}"></script>

  <link rel="alternate" type="application/rss+xml" title="RSS 2.0" href="http://{{ $gimme->publication->site }}/?tpl=1241">
</head>
