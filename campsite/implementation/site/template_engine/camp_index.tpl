<html>
<head>
  <title>Camp Smarty</title>
</head>
<body>

{{ invalid_tag }}

{{ $smarty.invalid_reference }}

{{ $article->invalid_property }}


{{**** Language ****}}
<table cellspacing="1" cellpadding="4">
<tr>
  <td bgcolor="#6a6a6a"><font color="#ffffff">Language</font></td>
</tr>
</table>
<table cellspacing="1" cellpadding="4">
<tr>
  <td bgcolor="Aqua" align="center" colspan="3">Fields</td>
  <td bgcolor="Aqua" align="center">Type</td>
</tr>
<tr>
  <td bgcolor="#9cf0ff" nowrap valign="top">Name:</td>
  <td bgcolor="#9cf0ff" valign="top">
    {{ $language->name }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $language->name }}{{ /literal }}</td>
  <td nowrap valign="top">base</td>
</tr>
<tr>
  <td bgcolor="#9cf0ff" nowrap valign="top">Number:</td>
  <td bgcolor="#9cf0ff" valign="top">
    {{ $language->number }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $language->number }}{{ /literal }}</td>
  <td nowrap valign="top">base</td>
</tr>
<tr>
  <td bgcolor="#9cf0ff" nowrap valign="top">English Name:</td>
  <td bgcolor="#9cf0ff" valign="top">
    {{ $language->english_name }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $language->english_name }}{{ /literal }}</td>
  <td nowrap valign="top">base</td>
</tr>
<tr>
  <td bgcolor="#9cf0ff" nowrap valign="top">Code:</td>
  <td bgcolor="#9cf0ff" valign="top">
    {{ $language->code }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $language->code }}{{ /literal }}</td>
  <td nowrap valign="top">base</td>
</tr>
<tr>
  <td bgcolor="#d4ffa2" nowrap valign="top">Defined:</td>
  <td bgcolor="#d4ffa2" valign="top">
    {{ $language->defined }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $language->defined }}{{ /literal }}</td>
  <td nowrap valign="top">custom</td>
</tr>
</table>



{{**** Publication ****}}
<table cellspacing="1" cellpadding="4">
<tr>
  <td bgcolor="#6a6a6a"><font color="#ffffff">Publication</font></td>
</tr>
</table>
<table cellspacing="1" cellpadding="4">
<tr>
  <td bgcolor="Aqua" align="center" colspan="3">Fields</td>
  <td bgcolor="Aqua" align="center">Type</td>
</tr>
<tr>
  <td bgcolor="#9cf0ff" nowrap valign="top">Name:</td>
  <td bgcolor="#9cf0ff" valign="top">
    {{ $publication->name }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $publication->name }}{{ /literal }}</td>
  <td nowrap valign="top">base</td>
</tr>
<tr>
  <td bgcolor="#9cf0ff" nowrap valign="top">Identifier:</td>
  <td bgcolor="#9cf0ff" valign="top">
    {{ $publication->identifier }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $publication->identifier }}{{ /literal }}</td>
  <td nowrap valign="top">base</td>
</tr>
<tr>
  <td bgcolor="#d4ffa2" nowrap valign="top">Site:</td>
  <td bgcolor="#d4ffa2" valign="top">
    {{ $publication->site }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $publication->site }}{{ /literal }}</td>
  <td nowrap valign="top">custom</td>
</tr>
<tr>
  <td bgcolor="#d4ffa2" nowrap valign="top">Defined:</td>
  <td bgcolor="#d4ffa2" valign="top">
    {{ $publication->defined }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $publication->defined }}{{ /literal }}</td>
  <td nowrap valign="top">custom</td>
</tr>
</table>


{{**** Issue ****}}
<table>
<tr>
  <td bgcolor="#6a6a6a"><font color="#ffffff">Issue</font></td>
</tr>
</table>
<table cellspacing="1" cellpadding="4">
<tr>
  <td bgcolor="Aqua" align="center" colspan="3">Fields</td>
  <td bgcolor="Aqua" align="center">Type</td>
</tr>
<tr>
  <td bgcolor="#9cf0ff" nowrap valign="top">Name:</td>
  <td bgcolor="#9cf0ff" valign="top">
    {{ $issue->name }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $issue->name }}{{ /literal }}</td>
  <td nowrap valign="top">base</td>
</tr>
<tr>
  <td bgcolor="#9cf0ff" nowrap valign="top">Number:</td>
  <td bgcolor="#9cf0ff" valign="top">
    {{ $issue->number }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $issue->number }}{{ /literal }}</td>
  <td nowrap valign="top">base</td>
</tr>
<tr>
  <td bgcolor="#d4ffa2" nowrap valign="top">Year:</td>
  <td bgcolor="#d4ffa2" valign="top">
    {{ $issue->year }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $issue->year }}{{ /literal }}</td>
  <td nowrap valign="top">custom</td>
</tr>
<tr>
  <td bgcolor="#d4ffa2" nowrap valign="top">Month:</td>
  <td bgcolor="#d4ffa2" valign="top">
    {{ $issue->mon }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $issue->mon }}{{ /literal }}</td>
  <td nowrap valign="top">custom</td>
</tr>
<tr>
  <td bgcolor="#d4ffa2" nowrap valign="top">Week Day:</td>
  <td bgcolor="#d4ffa2" valign="top">
    {{ $issue->wday }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $issue->wday }}{{ /literal }}</td>
  <td nowrap valign="top">custom</td>
</tr>
<tr>
  <td bgcolor="#d4ffa2" nowrap valign="top">Month Day:</td>
  <td bgcolor="#d4ffa2" valign="top">
    {{ $issue->mday }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $issue->mday }}{{ /literal }}</td>
  <td nowrap valign="top">custom</td>
</tr>
<tr>
  <td bgcolor="#d4ffa2" nowrap valign="top">Year Day:</td>
  <td bgcolor="#d4ffa2" valign="top">
    {{ $issue->yday }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $issue->yday }}{{ /literal }}</td>
  <td nowrap valign="top">custom</td>
</tr>
<tr>
  <td bgcolor="#d4ffa2" nowrap valign="top">Hour:</td>
  <td bgcolor="#d4ffa2" valign="top">
    {{ $issue->hour }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $issue->hour }}{{ /literal }}</td>
  <td nowrap valign="top">custom</td>
</tr>
<tr>
  <td bgcolor="#d4ffa2" nowrap valign="top">Minute:</td>
  <td bgcolor="#d4ffa2" valign="top">
    {{ $issue->min }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $issue->min }}{{ /literal }}</td>
  <td nowrap valign="top">custom</td>
</tr>
<tr>
  <td bgcolor="#d4ffa2" nowrap valign="top">Second:</td>
  <td bgcolor="#d4ffa2" valign="top">
    {{ $issue->sec }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $issue->sec }}{{ /literal }}</td>
  <td nowrap valign="top">custom</td>
</tr>
<tr>
  <td bgcolor="#9cf0ff" nowrap valign="top">Date:</td>
  <td bgcolor="#9cf0ff" valign="top">
    {{ $issue->date }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $issue->date }}{{ /literal }}</td>
  <td nowrap valign="top">base</td>
</tr>
<tr>
  <td bgcolor="#9cf0ff" nowrap valign="top">Publish Date:</td>
  <td bgcolor="#9cf0ff" valign="top">
    {{ $issue->publish_date }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $issue->publish_date }}{{ /literal }}</td>
  <td nowrap valign="top">base</td>
</tr>
<tr>
  <td bgcolor="#d4ffa2" nowrap valign="top">Template:</td>
  <td bgcolor="#d4ffa2" valign="top">
    {{ $issue->template->name }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $issue->template->name }}{{ /literal }}</td>
  <td nowrap valign="top">custom</td>
</tr>
<tr>
  <td bgcolor="#d4ffa2" nowrap valign="top">Defined:</td>
  <td bgcolor="#d4ffa2" valign="top">
    {{ $issue->defined }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $issue->defined }}{{ /literal }}</td>
  <td nowrap valign="top">custom</td>
</tr>
</table>



{{**** Section ****}}
<table>
<tr>
  <td bgcolor="#6a6a6a"><font color="#ffffff">Section</font></td>
</tr>
</table>
<table cellspacing="1" cellpadding="4">
<tr>
  <td bgcolor="Aqua" align="center" colspan="3">Fields</td>
  <td bgcolor="Aqua" align="center">Type</td>
</tr>
<tr>
  <td bgcolor="#9cf0ff" nowrap valign="top">Name:</td>
  <td bgcolor="#9cf0ff" valign="top">
    {{ $section->name }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $section->name }}{{ /literal }}</td>
  <td nowrap valign="top">base</td>
</tr>
<tr>
  <td bgcolor="#9cf0ff" nowrap valign="top">Number:</td>
  <td bgcolor="#9cf0ff" valign="top">
    {{ $section->number }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $section->number }}{{ /literal }}</td>
  <td nowrap valign="top">base</td>
</tr>
<tr>
  <td bgcolor="#9cf0ff" nowrap valign="top">Description:</td>
  <td bgcolor="#9cf0ff" valign="top">
    {{ $section->description }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $section->description }}{{ /literal }}</td>
  <td nowrap valign="top">base</td>
</tr>
<tr>
  <td bgcolor="#d4ffa2" nowrap valign="top">Template:</td>
  <td bgcolor="#d4ffa2" valign="top">
    {{ $section->template->name }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $section->template->name }}{{ /literal }}</td>
  <td nowrap valign="top">custom</td>
</tr>
<tr>
  <td bgcolor="#d4ffa2" nowrap valign="top">Defined:</td>
  <td bgcolor="#d4ffa2" valign="top">
    {{ $section->defined }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $section->defined }}{{ /literal }}</td>
  <td nowrap valign="top">custom</td>
</tr>
</table>


{{**** Article ****}}
<table cellspacing="1" cellpadding="4">
<tr>
  <td bgcolor="#6a6a6a"><font color="#ffffff">Article</font></td>
</tr>
</table>
<table cellspacing="1" cellpadding="4">
<tr>
  <td bgcolor="Aqua" align="center" colspan="3">Fields</td>
  <td bgcolor="Aqua" align="center">Type</td>
</tr>
<tr>
  <td bgcolor="#9cf0ff" nowrap valign="top">Name:</td>
  <td bgcolor="#9cf0ff" valign="top">
    {{ $article->name }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $article->name }}{{ /literal }}</td>
  <td nowrap valign="top">base</td>
</tr>
<tr>
  <td bgcolor="#9cf0ff" nowrap valign="top">Number:</td>
  <td bgcolor="#9cf0ff" valign="top">
    {{ $article->number }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $article->number }}{{ /literal }}</td>
  <td nowrap valign="top">base</td>
</tr>
<tr>
  <td bgcolor="#9cf0ff" nowrap valign="top">Keywords:</td>
  <td bgcolor="#9cf0ff" valign="top">
    {{ $article->keywords }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $article->keywords }}{{ /literal }}</td>
  <td nowrap valign="top">base</td>
</tr>
<tr>
  <td bgcolor="#9cf0ff" nowrap valign="top">Type:</td>
  <td bgcolor="#9cf0ff" valign="top">
    {{ $article->type_name }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $article->type_name }}{{ /literal }}</td>
  <td nowrap valign="top">base</td>
</tr>


<tr>
  <td bgcolor="#d4ffa2" nowrap valign="top">Year:</td>
  <td bgcolor="#d4ffa2" valign="top">
    {{ $article->year }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $article->year }}{{ /literal }}</td>
  <td nowrap valign="top">custom</td>
</tr>
<tr>
  <td bgcolor="#d4ffa2" nowrap valign="top">Month:</td>
  <td bgcolor="#d4ffa2" valign="top">
    {{ $article->mon }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $article->mon }}{{ /literal }}</td>
  <td nowrap valign="top">custom</td>
</tr>
<tr>
  <td bgcolor="#d4ffa2" nowrap valign="top">Week Day:</td>
  <td bgcolor="#d4ffa2" valign="top">
    {{ $article->wday }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $article->wday }}{{ /literal }}</td>
  <td nowrap valign="top">custom</td>
</tr>
<tr>
  <td bgcolor="#d4ffa2" nowrap valign="top">Month Day:</td>
  <td bgcolor="#d4ffa2" valign="top">
    {{ $article->mday }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $article->mday }}{{ /literal }}</td>
  <td nowrap valign="top">custom</td>
</tr>
<tr>
  <td bgcolor="#d4ffa2" nowrap valign="top">Year Day:</td>
  <td bgcolor="#d4ffa2" valign="top">
    {{ $article->yday }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $article->yday }}{{ /literal }}</td>
  <td nowrap valign="top">custom</td>
</tr>
<tr>
  <td bgcolor="#d4ffa2" nowrap valign="top">Hour:</td>
  <td bgcolor="#d4ffa2" valign="top">
    {{ $article->hour }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $article->hour }}{{ /literal }}</td>
  <td nowrap valign="top">custom</td>
</tr>
<tr>
  <td bgcolor="#d4ffa2" nowrap valign="top">Minute:</td>
  <td bgcolor="#d4ffa2" valign="top">
    {{ $article->min }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $article->min }}{{ /literal }}</td>
  <td nowrap valign="top">custom</td>
</tr>
<tr>
  <td bgcolor="#d4ffa2" nowrap valign="top">Second:</td>
  <td bgcolor="#d4ffa2" valign="top">
    {{ $article->sec }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $article->sec }}{{ /literal }}</td>
  <td nowrap valign="top">custom</td>
</tr>

<tr>
  <td bgcolor="#9cf0ff" nowrap valign="top">Creation Date:</td>
  <td bgcolor="#9cf0ff" valign="top">
    {{ $article->creation_date }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $article->creation_date }}{{ /literal }}</td>
  <td nowrap valign="top">base</td>
</tr>
<tr>
  <td bgcolor="#9cf0ff" nowrap valign="top">Publish Date:</td>
  <td bgcolor="#9cf0ff" valign="top">
    {{ $article->publish_date }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $article->publish_date }}{{ /literal }}</td>
  <td nowrap valign="top">base</td>
</tr>

<tr>
  <td bgcolor="#d4ffa2" nowrap valign="top">Template:</td>
  <td bgcolor="#d4ffa2" valign="top">
    {{ $article->template->name }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $article->template->name }}{{ /literal }}</td>
  <td nowrap valign="top">custom</td>
</tr>

<tr>
  <td bgcolor="#d4ffa2" nowrap valign="top">Intro:</td>
  <td bgcolor="#d4ffa2" valign="top">
    {{ $article->type->fastnews->intro }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $article->type->fastnews->intro }}{{ /literal }}</td>
  <td nowrap valign="top">custom</td>
</tr>
<tr>
  <td bgcolor="#d4ffa2" nowrap valign="top">Body:</td>
  <td bgcolor="#d4ffa2" valign="top">
    {{ $article->type->fastnews->body }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $article->type->fastnews->body }}{{ /literal }}</td>
  <td nowrap valign="top">custom</td>
</tr>

<tr>
  <td bgcolor="#9cf0ff" nowrap valign="top">URL Name:</td>
  <td bgcolor="#9cf0ff" valign="top">
    {{ $article->url_name }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $article->url_name }}{{ /literal }}</td>
  <td nowrap valign="top">base</td>
</tr>
<tr>
  <td bgcolor="#9cf0ff" nowrap valign="top">Comments Enabled:</td>
  <td bgcolor="#9cf0ff" valign="top">
    {{ $article->comments_enabled }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $article->comments_enabled }}{{ /literal }}</td>
  <td nowrap valign="top">base</td>
</tr>
<tr>
  <td bgcolor="#9cf0ff" nowrap valign="top">Comments Locked:</td>
  <td bgcolor="#9cf0ff" valign="top">
    {{ $article->comments_locked }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $article->comments_locked }}{{ /literal }}</td>
  <td nowrap valign="top">base</td>
</tr>
<tr>
  <td bgcolor="#9cf0ff" nowrap valign="top">Last Update:</td>
  <td bgcolor="#9cf0ff" valign="top">
    {{ $article->last_update }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $article->last_update }}{{ /literal }}</td>
  <td nowrap valign="top">base</td>
</tr>

<tr>
  <td bgcolor="#d4ffa2" nowrap valign="top">On Front Page:</td>
  <td bgcolor="#d4ffa2" valign="top">
    {{ $article->on_front_page }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $article->on_front_page }}{{ /literal }}</td>
  <td nowrap valign="top">custom</td>
</tr>
<tr>
  <td bgcolor="#d4ffa2" nowrap valign="top">On Section Page:</td>
  <td bgcolor="#d4ffa2" valign="top">
    {{ $article->on_section_page }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $article->on_section_page }}{{ /literal }}</td>
  <td nowrap valign="top">custom</td>
</tr>
<tr>
  <td bgcolor="#d4ffa2" nowrap valign="top">Published:</td>
  <td bgcolor="#d4ffa2" valign="top">
    {{ $article->is_published }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $article->is_published }}{{ /literal }}</td>
  <td nowrap valign="top">custom</td>
</tr>
<tr>
  <td bgcolor="#d4ffa2" nowrap valign="top">Public:</td>
  <td bgcolor="#d4ffa2" valign="top">
    {{ $article->is_public }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $article->is_public }}{{ /literal }}</td>
  <td nowrap valign="top">custom</td>
</tr>
<tr>
  <td bgcolor="#d4ffa2" nowrap valign="top">Indexed:</td>
  <td bgcolor="#d4ffa2" valign="top">
    {{ $article->is_indexed }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $article->is_indexed }}{{ /literal }}</td>
  <td nowrap valign="top">custom</td>
</tr>

<tr>
  <td bgcolor="#d4ffa2" nowrap valign="top">Publication:</td>
  <td bgcolor="#d4ffa2" valign="top">
    {{ $article->publication->name }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $article->publication }}{{ /literal }}</td>
  <td nowrap valign="top">custom</td>
</tr>
<tr>
  <td bgcolor="#d4ffa2" nowrap valign="top">Issue:</td>
  <td bgcolor="#d4ffa2" valign="top">
    {{ $article->issue->name }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $article->issue }}{{ /literal }}</td>
  <td nowrap valign="top">custom</td>
</tr>
<tr>
  <td bgcolor="#d4ffa2" nowrap valign="top">Section:</td>
  <td bgcolor="#d4ffa2" valign="top">
    {{ $article->section->name }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $article->section }}{{ /literal }}</td>
  <td nowrap valign="top">custom</td>
</tr>
<tr>
  <td bgcolor="#d4ffa2" nowrap valign="top">Language:</td>
  <td bgcolor="#d4ffa2" valign="top">
    {{ $article->language->name }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $article->language->name }}{{ /literal }}</td>
  <td nowrap valign="top">custom</td>
</tr>
<tr>
  <td bgcolor="#d4ffa2" nowrap valign="top">Owner:</td>
  <td bgcolor="#d4ffa2" valign="top">
    {{ $article->owner->name }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $article->owner }}{{ /literal }}</td>
  <td nowrap valign="top">custom</td>
</tr>

<tr>
  <td bgcolor="#d4ffa2" nowrap valign="top">Defined:</td>
  <td bgcolor="#d4ffa2" valign="top">
    {{ $article->defined }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $article->defined }}{{ /literal }}</td>
  <td nowrap valign="top">custom</td>
</tr>
<tr>
  <td bgcolor="#d4ffa2" nowrap valign="top">Translated to:</td>
  <td bgcolor="#d4ffa2" valign="top">
    {{ $article->translated_to('ro') }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $article->translated_to('ro') }}{{ /literal }}</td>
  <td nowrap valign="top">custom</td>
</tr>
<tr>
  <td bgcolor="#d4ffa2" nowrap valign="top">Has Attachments:</td>
  <td bgcolor="#d4ffa2" valign="top">
    {{ $article->has_attachments }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $article->has_attachments }}{{ /literal }}</td>
  <td nowrap valign="top">custom</td>
</tr>
</table>


{{**** Image ****}}
<table cellspacing="1" cellpadding="4">
<tr>
  <td bgcolor="#6a6a6a"><font color="#ffffff">Image</font></td>
</tr>
</table>
<table cellspacing="1" cellpadding="4">
<tr>
  <td bgcolor="Aqua" align="center" colspan="3">Fields</td>
  <td bgcolor="Aqua" align="center">Type</td>
</tr>
<tr>
  <td bgcolor="#9cf0ff" nowrap valign="top">Number:</td>
  <td bgcolor="#9cf0ff" valign="top">
    {{ $image->number }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $image->number }}{{ /literal }}</td>
  <td nowrap valign="top">base</td>
</tr>
<tr>
  <td bgcolor="#9cf0ff" nowrap valign="top">Photographer:</td>
  <td bgcolor="#9cf0ff" valign="top">
    {{ $image->photographer }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $image->photographer }}{{ /literal }}</td>
  <td nowrap valign="top">base</td>
</tr>
<tr>
  <td bgcolor="#9cf0ff" nowrap valign="top">Place:</td>
  <td bgcolor="#9cf0ff" valign="top">
    {{ $image->place }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $image->place }}{{ /literal }}</td>
  <td nowrap valign="top">base</td>
</tr>
<tr>
  <td bgcolor="#9cf0ff" nowrap valign="top">Description:</td>
  <td bgcolor="#9cf0ff" valign="top">
    {{ $image->description }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $image->description }}{{ /literal }}</td>
  <td nowrap valign="top">base</td>
</tr>
<tr>
  <td bgcolor="#9cf0ff" nowrap valign="top">Date:</td>
  <td bgcolor="#9cf0ff" valign="top">
    {{ $image->date }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $image->date }}{{ /literal }}</td>
  <td nowrap valign="top">base</td>
</tr>


<tr>
  <td bgcolor="#d4ffa2" nowrap valign="top">Year:</td>
  <td bgcolor="#d4ffa2" valign="top">
    {{ $image->year }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $image->year }}{{ /literal }}</td>
  <td nowrap valign="top">custom</td>
</tr>
<tr>
  <td bgcolor="#d4ffa2" nowrap valign="top">Month:</td>
  <td bgcolor="#d4ffa2" valign="top">
    {{ $image->mon }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $image->mon }}{{ /literal }}</td>
  <td nowrap valign="top">custom</td>
</tr>
<tr>
  <td bgcolor="#d4ffa2" nowrap valign="top">Week Day:</td>
  <td bgcolor="#d4ffa2" valign="top">
    {{ $image->wday }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $image->wday }}{{ /literal }}</td>
  <td nowrap valign="top">custom</td>
</tr>
<tr>
  <td bgcolor="#d4ffa2" nowrap valign="top">Month Day:</td>
  <td bgcolor="#d4ffa2" valign="top">
    {{ $image->mday }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $image->mday }}{{ /literal }}</td>
  <td nowrap valign="top">custom</td>
</tr>
<tr>
  <td bgcolor="#d4ffa2" nowrap valign="top">Year Day:</td>
  <td bgcolor="#d4ffa2" valign="top">
    {{ $image->yday }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $image->yday }}{{ /literal }}</td>
  <td nowrap valign="top">custom</td>
</tr>
<tr>
  <td bgcolor="#d4ffa2" nowrap valign="top">Hour:</td>
  <td bgcolor="#d4ffa2" valign="top">
    {{ $image->hour }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $image->hour }}{{ /literal }}</td>
  <td nowrap valign="top">custom</td>
</tr>
<tr>
  <td bgcolor="#d4ffa2" nowrap valign="top">Minute:</td>
  <td bgcolor="#d4ffa2" valign="top">
    {{ $image->min }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $image->min }}{{ /literal }}</td>
  <td nowrap valign="top">custom</td>
</tr>
<tr>
  <td bgcolor="#d4ffa2" nowrap valign="top">Second:</td>
  <td bgcolor="#d4ffa2" valign="top">
    {{ $image->sec }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $image->sec }}{{ /literal }}</td>
  <td nowrap valign="top">custom</td>
</tr>

<tr>
  <td bgcolor="#d4ffa2" nowrap valign="top">Defined:</td>
  <td bgcolor="#d4ffa2" valign="top">
    {{ $image->defined }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $image->defined }}{{ /literal }}</td>
  <td nowrap valign="top">custom</td>
</tr>
</table>


{{**** Attachment ****}}
<table cellspacing="1" cellpadding="4">
<tr>
  <td bgcolor="#6a6a6a"><font color="#ffffff">Attachment</font></td>
</tr>
</table>
<table cellspacing="1" cellpadding="4">
<tr>
  <td bgcolor="Aqua" align="center" colspan="3">Fields</td>
  <td bgcolor="Aqua" align="center">Type</td>
</tr>
<tr>
  <td bgcolor="#9cf0ff" nowrap valign="top">File Name:</td>
  <td bgcolor="#9cf0ff" valign="top">
    {{ $attachment->file_name }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $attachment->file_name }}{{ /literal }}</td>
  <td nowrap valign="top">base</td>
</tr>
<tr>
  <td bgcolor="#9cf0ff" nowrap valign="top">Mime Type:</td>
  <td bgcolor="#9cf0ff" valign="top">
    {{ $attachment->mime_type }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $attachment->mime_type }}{{ /literal }}</td>
  <td nowrap valign="top">base</td>
</tr>
<tr>
  <td bgcolor="#9cf0ff" nowrap valign="top">Extension:</td>
  <td bgcolor="#9cf0ff" valign="top">
    {{ $attachment->extension }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $attachment->extension }}{{ /literal }}</td>
  <td nowrap valign="top">base</td>
</tr>
<tr>
  <td bgcolor="#d4ffa2" nowrap valign="top">Description:</td>
  <td bgcolor="#d4ffa2" valign="top">
    {{ $attachment->description }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $attachment->description }}{{ /literal }}</td>
  <td nowrap valign="top">custom</td>
</tr>
<tr>
  <td bgcolor="#9cf0ff" nowrap valign="top">Size B:</td>
  <td bgcolor="#9cf0ff" valign="top">
    {{ $attachment->size_b }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $attachment->size_b }}{{ /literal }}</td>
  <td nowrap valign="top">base</td>
</tr>
<tr>
  <td bgcolor="#d4ffa2" nowrap valign="top">Size KB:</td>
  <td bgcolor="#d4ffa2" valign="top">
    {{ $attachment->size_kb }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $attachment->size_kb }}{{ /literal }}</td>
  <td nowrap valign="top">custom</td>
</tr>
<tr>
  <td bgcolor="#d4ffa2" nowrap valign="top">Size MB:</td>
  <td bgcolor="#d4ffa2" valign="top">
    {{ $attachment->size_mb }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $attachment->size_mb }}{{ /literal }}</td>
  <td nowrap valign="top">custom</td>
</tr>
<tr>
  <td bgcolor="#d4ffa2" nowrap valign="top">Defined:</td>
  <td bgcolor="#d4ffa2" valign="top">
    {{ $attachment->defined }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $attachment->defined }}{{ /literal }}</td>
  <td nowrap valign="top">custom</td>
</tr>
</table>


{{**** Topic ****}}
<table cellspacing="1" cellpadding="4">
<tr>
  <td bgcolor="#6a6a6a"><font color="#ffffff">Topic</font></td>
</tr>
</table>
<table cellspacing="1" cellpadding="4">
<tr>
  <td bgcolor="Aqua" align="center" colspan="3">Fields</td>
  <td bgcolor="Aqua" align="center">Type</td>
</tr>
<tr>
  <td bgcolor="#9cf0ff" nowrap valign="top">Identifier:</td>
  <td bgcolor="#9cf0ff" valign="top">
    {{ $topic->identifier }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $topic->identifier }}{{ /literal }}</td>
  <td nowrap valign="top">base</td>
</tr>
<tr>
  <td bgcolor="#d4ffa2" nowrap valign="top">Name:</td>
  <td bgcolor="#d4ffa2" valign="top">
    {{ $topic->name }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $topic->name }}{{ /literal }}</td>
  <td nowrap valign="top">custom</td>
</tr>
<tr>
  <td bgcolor="#d4ffa2" nowrap valign="top">Defined:</td>
  <td bgcolor="#d4ffa2" valign="top">
    {{ $topic->defined }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $topic->defined }}{{ /literal }}</td>
  <td nowrap valign="top">custom</td>
</tr>
</table>


{{**** User ****}}
<table cellspacing="1" cellpadding="4">
<tr>
  <td bgcolor="#6a6a6a"><font color="#ffffff">User</font></td>
</tr>
</table>
<table cellspacing="1" cellpadding="4">
<tr>
  <td bgcolor="Aqua" align="center" colspan="3">Fields</td>
  <td bgcolor="Aqua" align="center">Type</td>
</tr>
<tr>
  <td bgcolor="#9cf0ff" nowrap valign="top">Identifier:</td>
  <td bgcolor="#9cf0ff" valign="top">
    {{ $user->identifier }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $user->identifier }}{{ /literal }}</td>
  <td nowrap valign="top">base</td>
</tr>
<tr>
  <td bgcolor="#9cf0ff" nowrap valign="top">Name:</td>
  <td bgcolor="#9cf0ff" valign="top">
    {{ $user->name }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $user->name }}{{ /literal }}</td>
  <td nowrap valign="top">base</td>
</tr>
<tr>
  <td bgcolor="#9cf0ff" nowrap valign="top">User Name:</td>
  <td bgcolor="#9cf0ff" valign="top">
    {{ $user->uname }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $user->uname }}{{ /literal }}</td>
  <td nowrap valign="top">base</td>
</tr>
<tr>
  <td bgcolor="#9cf0ff" nowrap valign="top">EMail:</td>
  <td bgcolor="#9cf0ff" valign="top">
    {{ $user->email }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $user->email }}{{ /literal }}</td>
  <td nowrap valign="top">base</td>
</tr>
<tr>
  <td bgcolor="#9cf0ff" nowrap valign="top">City:</td>
  <td bgcolor="#9cf0ff" valign="top">
    {{ $user->city }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $user->city }}{{ /literal }}</td>
  <td nowrap valign="top">base</td>
</tr>
<tr>
  <td bgcolor="#9cf0ff" nowrap valign="top">Street Address:</td>
  <td bgcolor="#9cf0ff" valign="top">
    {{ $user->str_address }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $user->str_address }}{{ /literal }}</td>
  <td nowrap valign="top">base</td>
</tr>
<tr>
  <td bgcolor="#9cf0ff" nowrap valign="top">State:</td>
  <td bgcolor="#9cf0ff" valign="top">
    {{ $user->state }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $user->state }}{{ /literal }}</td>
  <td nowrap valign="top">base</td>
</tr>
<tr>
  <td bgcolor="#d4ffa2" nowrap valign="top">Country:</td>
  <td bgcolor="#d4ffa2" valign="top">
    {{ $user->country }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $user->country }}{{ /literal }}</td>
  <td nowrap valign="top">base</td>
</tr>
<tr>
  <td bgcolor="#9cf0ff" nowrap valign="top">Phone:</td>
  <td bgcolor="#9cf0ff" valign="top">
    {{ $user->phone }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $user->phone }}{{ /literal }}</td>
  <td nowrap valign="top">base</td>
</tr>
<tr>
  <td bgcolor="#9cf0ff" nowrap valign="top">Contact:</td>
  <td bgcolor="#9cf0ff" valign="top">
    {{ $user->contact }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $user->contact }}{{ /literal }}</td>
  <td nowrap valign="top">base</td>
</tr>
<tr>
  <td bgcolor="#9cf0ff" nowrap valign="top">Postal Code:</td>
  <td bgcolor="#9cf0ff" valign="top">
    {{ $user->postal_code }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $user->postal_code }}{{ /literal }}</td>
  <td nowrap valign="top">base</td>
</tr>
<tr>
  <td bgcolor="#9cf0ff" nowrap valign="top">Employer:</td>
  <td bgcolor="#9cf0ff" valign="top">
    {{ $user->employer }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $user->employer }}{{ /literal }}</td>
  <td nowrap valign="top">base</td>
</tr>
<tr>
  <td bgcolor="#9cf0ff" nowrap valign="top">Position:</td>
  <td bgcolor="#9cf0ff" valign="top">
    {{ $user->position }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $user->position }}{{ /literal }}</td>
  <td nowrap valign="top">base</td>
</tr>
<tr>
  <td bgcolor="#9cf0ff" nowrap valign="top">Interests:</td>
  <td bgcolor="#9cf0ff" valign="top">
    {{ $user->interests }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $user->interests }}{{ /literal }}</td>
  <td nowrap valign="top">base</td>
</tr>
<tr>
  <td bgcolor="#9cf0ff" nowrap valign="top">How:</td>
  <td bgcolor="#9cf0ff" valign="top">
    {{ $user->how }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $user->how }}{{ /literal }}</td>
  <td nowrap valign="top">base</td>
</tr>
<tr>
  <td bgcolor="#9cf0ff" nowrap valign="top">Languages:</td>
  <td bgcolor="#9cf0ff" valign="top">
    {{ $user->languages }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $user->languages }}{{ /literal }}</td>
  <td nowrap valign="top">base</td>
</tr>
<tr>
  <td bgcolor="#9cf0ff" nowrap valign="top">Improvements:</td>
  <td bgcolor="#9cf0ff" valign="top">
    {{ $user->improvements }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $user->improvements }}{{ /literal }}</td>
  <td nowrap valign="top">base</td>
</tr>
<tr>
  <td bgcolor="#9cf0ff" nowrap valign="top">Field 1:</td>
  <td bgcolor="#9cf0ff" valign="top">
    {{ $user->field1 }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $user->field1 }}{{ /literal }}</td>
  <td nowrap valign="top">base</td>
</tr>
<tr>
  <td bgcolor="#9cf0ff" nowrap valign="top">Field 2:</td>
  <td bgcolor="#9cf0ff" valign="top">
    {{ $user->field2 }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $user->field2 }}{{ /literal }}</td>
  <td nowrap valign="top">base</td>
</tr>
<tr>
  <td bgcolor="#9cf0ff" nowrap valign="top">Field 3:</td>
  <td bgcolor="#9cf0ff" valign="top">
    {{ $user->field3 }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $user->field3 }}{{ /literal }}</td>
  <td nowrap valign="top">base</td>
</tr>
<tr>
  <td bgcolor="#9cf0ff" nowrap valign="top">Field 4:</td>
  <td bgcolor="#9cf0ff" valign="top">
    {{ $user->field4 }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $user->field4 }}{{ /literal }}</td>
  <td nowrap valign="top">base</td>
</tr>
<tr>
  <td bgcolor="#9cf0ff" nowrap valign="top">Field 5:</td>
  <td bgcolor="#9cf0ff" valign="top">
    {{ $user->field5 }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $user->field5 }}{{ /literal }}</td>
  <td nowrap valign="top">base</td>
</tr>
<tr>
  <td bgcolor="#9cf0ff" nowrap valign="top">Text 1:</td>
  <td bgcolor="#9cf0ff" valign="top">
    {{ $user->text1 }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $user->text1 }}{{ /literal }}</td>
  <td nowrap valign="top">base</td>
</tr>
<tr>
  <td bgcolor="#9cf0ff" nowrap valign="top">Text 2:</td>
  <td bgcolor="#9cf0ff" valign="top">
    {{ $user->text2 }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $user->text2 }}{{ /literal }}</td>
  <td nowrap valign="top">base</td>
</tr>
<tr>
  <td bgcolor="#9cf0ff" nowrap valign="top">Text 3:</td>
  <td bgcolor="#9cf0ff" valign="top">
    {{ $user->text3 }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $user->text3 }}{{ /literal }}</td>
  <td nowrap valign="top">base</td>
</tr>
<tr>
  <td bgcolor="#9cf0ff" nowrap valign="top">Title:</td>
  <td bgcolor="#9cf0ff" valign="top">
    {{ $user->title }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $user->title }}{{ /literal }}</td>
  <td nowrap valign="top">base</td>
</tr>
<tr>
  <td bgcolor="#9cf0ff" nowrap valign="top">Age:</td>
  <td bgcolor="#9cf0ff" valign="top">
    {{ $user->age }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $user->age }}{{ /literal }}</td>
  <td nowrap valign="top">base</td>
</tr>
<tr>
  <td bgcolor="#d4ffa2" nowrap valign="top">Defined:</td>
  <td bgcolor="#d4ffa2" valign="top">
    {{ $user->defined }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $user->defined }}{{ /literal }}</td>
  <td nowrap valign="top">custom</td>
</tr>
</table>


{{**** Audioclip ****}}
<table cellspacing="1" cellpadding="4">
<tr>
  <td bgcolor="#6a6a6a"><font color="#ffffff">Audioclip</font></td>
</tr>
</table>
<table cellspacing="1" cellpadding="4">
<tr>
  <td bgcolor="Aqua" align="center" colspan="3">Fields</td>
  <td bgcolor="Aqua" align="center">Type</td>
</tr>
<tr>
  <td bgcolor="#9cf0ff" nowrap valign="top">Title:</td>
  <td bgcolor="#9cf0ff" valign="top">
    {{ $audioclip->title }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $audioclip->title }}{{ /literal }}</td>
  <td nowrap valign="top">base</td>
</tr>
<tr>
  <td bgcolor="#9cf0ff" nowrap valign="top">Creator:</td>
  <td bgcolor="#9cf0ff" valign="top">
    {{ $audioclip->creator }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $audioclip->creator }}{{ /literal }}</td>
  <td nowrap valign="top">base</td>
</tr>
<tr>
  <td bgcolor="#9cf0ff" nowrap valign="top">Genre:</td>
  <td bgcolor="#9cf0ff" valign="top">
    {{ $audioclip->genre }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $audioclip->genre }}{{ /literal }}</td>
  <td nowrap valign="top">base</td>
</tr>
<tr>
  <td bgcolor="#9cf0ff" nowrap valign="top">Length:</td>
  <td bgcolor="#9cf0ff" valign="top">
    {{ $audioclip->length }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $audioclip->length }}{{ /literal }}</td>
  <td nowrap valign="top">base</td>
</tr>
<tr>
  <td bgcolor="#9cf0ff" nowrap valign="top">Year:</td>
  <td bgcolor="#9cf0ff" valign="top">
    {{ $audioclip->year }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $audioclip->year }}{{ /literal }}</td>
  <td nowrap valign="top">base</td>
</tr>
<tr>
  <td bgcolor="#9cf0ff" nowrap valign="top">Bitrate:</td>
  <td bgcolor="#9cf0ff" valign="top">
    {{ $audioclip->bitrate }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $audioclip->bitrate }}{{ /literal }}</td>
  <td nowrap valign="top">base</td>
</tr>
<tr>
  <td bgcolor="#9cf0ff" nowrap valign="top">Samplerate:</td>
  <td bgcolor="#9cf0ff" valign="top">
    {{ $audioclip->samplerate }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $audioclip->samplerate }}{{ /literal }}</td>
  <td nowrap valign="top">base</td>
</tr>
<tr>
  <td bgcolor="#9cf0ff" nowrap valign="top">Album:</td>
  <td bgcolor="#9cf0ff" valign="top">
    {{ $audioclip->album }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $audioclip->album }}{{ /literal }}</td>
  <td nowrap valign="top">base</td>
</tr>
<tr>
  <td bgcolor="#9cf0ff" nowrap valign="top">Description:</td>
  <td bgcolor="#9cf0ff" valign="top">
    {{ $audioclip->description }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $audioclip->description }}{{ /literal }}</td>
  <td nowrap valign="top">base</td>
</tr>
<tr>
  <td bgcolor="#9cf0ff" nowrap valign="top">Format:</td>
  <td bgcolor="#9cf0ff" valign="top">
    {{ $audioclip->format }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $audioclip->format }}{{ /literal }}</td>
  <td nowrap valign="top">base</td>
</tr>
<tr>
  <td bgcolor="#9cf0ff" nowrap valign="top">Label:</td>
  <td bgcolor="#9cf0ff" valign="top">
    {{ $audioclip->label }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $audioclip->label }}{{ /literal }}</td>
  <td nowrap valign="top">base</td>
</tr>
<tr>
  <td bgcolor="#9cf0ff" nowrap valign="top">Composer:</td>
  <td bgcolor="#9cf0ff" valign="top">
    {{ $audioclip->composer }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $audioclip->composer }}{{ /literal }}</td>
  <td nowrap valign="top">base</td>
</tr>
<tr>
  <td bgcolor="#9cf0ff" nowrap valign="top">Channels:</td>
  <td bgcolor="#9cf0ff" valign="top">
    {{ $audioclip->channels }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $audioclip->channels }}{{ /literal }}</td>
  <td nowrap valign="top">base</td>
</tr>
<tr>
  <td bgcolor="#9cf0ff" nowrap valign="top">Rating:</td>
  <td bgcolor="#9cf0ff" valign="top">
    {{ $audioclip->rating }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $audioclip->rating }}{{ /literal }}</td>
  <td nowrap valign="top">base</td>
</tr>
<tr>
  <td bgcolor="#9cf0ff" nowrap valign="top">Track No:</td>
  <td bgcolor="#9cf0ff" valign="top">
    {{ $audioclip->track_no }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $audioclip->track_no }}{{ /literal }}</td>
  <td nowrap valign="top">base</td>
</tr>
<tr>
  <td bgcolor="#9cf0ff" nowrap valign="top">Disk No:</td>
  <td bgcolor="#9cf0ff" valign="top">
    {{ $audioclip->disk_no }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $audioclip->disk_no }}{{ /literal }}</td>
  <td nowrap valign="top">base</td>
</tr>
<tr>
  <td bgcolor="#9cf0ff" nowrap valign="top">Lyrics:</td>
  <td bgcolor="#9cf0ff" valign="top">
    {{ $audioclip->lyrics }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $audioclip->lyrics }}{{ /literal }}</td>
  <td nowrap valign="top">base</td>
</tr>
<tr>
  <td bgcolor="#9cf0ff" nowrap valign="top">Copyright:</td>
  <td bgcolor="#9cf0ff" valign="top">
    {{ $audioclip->copyright }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $audioclip->copyright }}{{ /literal }}</td>
  <td nowrap valign="top">base</td>
</tr>
<tr>
  <td bgcolor="#d4ffa2" nowrap valign="top">Defined:</td>
  <td bgcolor="#d4ffa2" valign="top">
    {{ $audioclip->defined }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $audioclip->defined }}{{ /literal }}</td>
  <td nowrap valign="top">custom</td>
</tr>
</table>


{{**** Article Comment ****}}
<table cellspacing="1" cellpadding="4">
<tr>
  <td bgcolor="#6a6a6a"><font color="#ffffff">Article Comment</font></td>
</tr>
</table>
<table cellspacing="1" cellpadding="4">
<tr>
  <td bgcolor="Aqua" align="center" colspan="3">Fields</td>
  <td bgcolor="Aqua" align="center">Type</td>
</tr>
<tr>
  <td bgcolor="#9cf0ff" nowrap valign="top">Identifier:</td>
  <td bgcolor="#9cf0ff" valign="top">
    {{ $comment->identifier }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $comment->identifier }}{{ /literal }}</td>
  <td nowrap valign="top">base</td>
</tr>
<tr>
  <td bgcolor="#9cf0ff" nowrap valign="top">Reader EMail:</td>
  <td bgcolor="#9cf0ff" valign="top">
    {{ $comment->reader_email }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $comment->reader_email }}{{ /literal }}</td>
  <td nowrap valign="top">base</td>
</tr>
<tr>
  <td bgcolor="#9cf0ff" nowrap valign="top">Submit Date:</td>
  <td bgcolor="#9cf0ff" valign="top">
    {{ $comment->submit_date }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $comment->submit_date }}{{ /literal }}</td>
  <td nowrap valign="top">base</td>
</tr>
<tr>
  <td bgcolor="#9cf0ff" nowrap valign="top">Subject:</td>
  <td bgcolor="#9cf0ff" valign="top">
    {{ $comment->subject }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $comment->subject }}{{ /literal }}</td>
  <td nowrap valign="top">base</td>
</tr>
<tr>
  <td bgcolor="#9cf0ff" nowrap valign="top">Content:</td>
  <td bgcolor="#9cf0ff" valign="top">
    {{ $comment->content }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $comment->content }}{{ /literal }}</td>
  <td nowrap valign="top">base</td>
</tr>
<tr>
  <td bgcolor="#9cf0ff" nowrap valign="top">Level:</td>
  <td bgcolor="#9cf0ff" valign="top">
    {{ $comment->level }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $comment->level }}{{ /literal }}</td>
  <td nowrap valign="top">base</td>
</tr>
<tr>
  <td bgcolor="#d4ffa2" nowrap valign="top">Defined:</td>
  <td bgcolor="#d4ffa2" valign="top">
    {{ $comment->defined }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $comment->defined }}{{ /literal }}</td>
  <td nowrap valign="top">custom</td>
</tr>
</table>


{{**** Template ****}}
<table cellspacing="1" cellpadding="4">
<tr>
  <td bgcolor="#6a6a6a"><font color="#ffffff">Template</font></td>
</tr>
</table>
<table cellspacing="1" cellpadding="4">
<tr>
  <td bgcolor="Aqua" align="center" colspan="3">Fields</td>
  <td bgcolor="Aqua" align="center">Type</td>
</tr>
<tr>
  <td bgcolor="#9cf0ff" nowrap valign="top">Name:</td>
  <td bgcolor="#9cf0ff" valign="top">
    {{ $template->name }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $template->name }}{{ /literal }}</td>
  <td nowrap valign="top">base</td>
</tr>
<tr>
  <td bgcolor="#d4ffa2" nowrap valign="top">Type:</td>
  <td bgcolor="#d4ffa2" valign="top">
    {{ $template->type }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $template->type }}{{ /literal }}</td>
  <td nowrap valign="top">custom</td>
</tr>
<tr>
  <td bgcolor="#d4ffa2" nowrap valign="top">Defined:</td>
  <td bgcolor="#d4ffa2" valign="top">
    {{ $template->defined }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $template->defined }}{{ /literal }}</td>
  <td nowrap valign="top">custom</td>
</tr>
</table>


{{**** Subscription ****}}
<table cellspacing="1" cellpadding="4">
<tr>
  <td bgcolor="#6a6a6a"><font color="#ffffff">Subscription</font></td>
</tr>
</table>
<table cellspacing="1" cellpadding="4">
<tr>
  <td bgcolor="Aqua" align="center" colspan="3">Fields</td>
  <td bgcolor="Aqua" align="center">Type</td>
</tr>
<tr>
  <td bgcolor="#9cf0ff" nowrap valign="top">Currency:</td>
  <td bgcolor="#9cf0ff" valign="top">
    {{ $subscription->currency }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $subscription->currency }}{{ /literal }}</td>
  <td nowrap valign="top">base</td>
</tr>
<tr>
  <td bgcolor="#d4ffa2" nowrap valign="top">Type:</td>
  <td bgcolor="#d4ffa2" valign="top">
    {{ $subscription->type }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $subscription->type }}{{ /literal }}</td>
  <td nowrap valign="top">custom</td>
</tr>
<tr>
  <td bgcolor="#d4ffa2" nowrap valign="top">Active:</td>
  <td bgcolor="#d4ffa2" valign="top">
    {{ $subscription->active }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $subscription->active }}{{ /literal }}</td>
  <td nowrap valign="top">custom</td>
</tr>
<tr>
  <td bgcolor="#d4ffa2" nowrap valign="top">Defined:</td>
  <td bgcolor="#d4ffa2" valign="top">
    {{ $subscription->defined }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $subscription->defined }}{{ /literal }}</td>
  <td nowrap valign="top">custom</td>
</tr>
</table>


</body>
</html>
