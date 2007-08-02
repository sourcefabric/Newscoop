{{ strip }}
<!DOCTYPE html
    PUBLIC "-//W3C//DTD HTML 4.01//EN"
    "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
  <title>{{ $campsite->page_title }} - {{ $campsite->prefs->site_title }}</title>
  <meta http-equiv="content-type" content="text/html; charset=utf-8" />
  <meta name="generator" content="Campsite - {{ $campsite->prefs->campsite_url }}" />
  <meta name="description" content="{{ $campsite->prefs->site_description }}" />  <meta name="keywords" content="{{ $campsite->prefs->site_keywords }}" />

  <link rel="stylesheet" type="text/css" href="{{ $campsite->sys->css_file }}" />
</head>
<body>
{{ include file="html_topmenu.tpl" }}
<table id="header" cellspacing="0" cellpadding="0">
<tr>
  <td>
    The Header
    <div class="datetime">
      {{ $smarty.now|camp_date_format:"%d %M %Y %h:%i:%s" }}
    </div>
  </td>
</tr>
</table>
{{ include file="html_searchbar.tpl" }}
{/strip}