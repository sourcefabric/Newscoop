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
    {{ $article->mon }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $article->mon }}{{ /literal }}</td>
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
    {{ $article->template }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $article->template }}{{ /literal }}</td>
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
    {{ $article->publication }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $article->publication }}{{ /literal }}</td>
  <td nowrap valign="top">custom</td>
</tr>
<tr>
  <td bgcolor="#d4ffa2" nowrap valign="top">Issue:</td>
  <td bgcolor="#d4ffa2" valign="top">
    {{ $article->issue }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $article->issue }}{{ /literal }}</td>
  <td nowrap valign="top">custom</td>
</tr>
<tr>
  <td bgcolor="#d4ffa2" nowrap valign="top">Section:</td>
  <td bgcolor="#d4ffa2" valign="top">
    {{ $article->section }}
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
    {{ $article->owner }}
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

</body>
</html>
