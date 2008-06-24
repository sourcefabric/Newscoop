<!DOCTYPE html
    PUBLIC "-//W3C//DTD HTML 4.01//EN"
    "http://www.w3.org/TR/html4/strict.dtd">
{{ if $campsite->url->get_parameter('logout') == 'true' }}
<META HTTP-EQUIV="Set-Cookie" CONTENT="LoginUserId=; path=/">
<META HTTP-EQUIV="Set-Cookie" CONTENT="LoginUserKey=; path=/">
{{ $campsite->url->reset_parameter('logout') }}
<META HTTP-EQUIV="Refresh" content="0;url={{ uri }}">
{{ /if }}
<html>
<head>
  <title>{{ $siteinfo.title }}</title>
  <meta http-equiv="Content-type" content="{{ $siteinfo.content_type }}" />
  <meta name="generator" content="{{ $siteinfo.generator }}" />
  <meta name="description" content="{{ $siteinfo.description }}" />
  <meta name="keywords" content="{{ $siteinfo.keywords }}" />

  <link rel="stylesheet" type="text/css" href="/{{ $siteinfo.templates_path }}/css/style.css" />
  
  {{ if $campsite->interviewstatus_action->defined }}
    <script language="javascript">
    {{ if $campsite->interviewstatus_action->is_error }}
        alert("{{ $campsite->interviewstatus_action->error_message }}");
    {{ else }}
        alert("Status of interview \"{{ $campsite->interview->title }}\" sucessfully switched to {{ $campsite->interview->status }}.")
    {{ /if }}
    </script>
  {{ /if }}
  
</head>
<body>
<div align="center">
<div id="container">
{{ include file="html_topmenu.tpl" }}
<table class="header" cellspacing="0" cellpadding="0">
<tr>
  <td>
    <a href="/"><img
      src="/{{ $siteinfo.templates_path }}/img/thenewspaper.png" /></a>
    <div class="datetime">
      Today: {{ $smarty.now|camp_date_format:"%d %M %Y" }}
    </div>
  </td>
  <td>
    <div id="searchform">
    {{ search_form template="search.tpl" submit_button="Search" html_code="class=\"submitbutton\"" }}
      {{ camp_edit object="search" attribute="keywords" }}
    {{ /search_form }}
    </div>
  </td>
</tr>
</table>
{{ include file="html_mainmenu.tpl" }}
