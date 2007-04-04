<html>
<head>
  <title>Camp Smarty</title>
</head>
<body>

{{ invalid_tag }}

{{ $smarty.invalid_reference }}

{{ $article->invalid_property }}

{{**** Article ****}}
<table cellspacing="1" cellpadding="4">
<tr>
  <td bgcolor="#6a6a6a"><font color="#ffffff">Article</font></td>
</tr>
</table>
<table cellspacing="1" cellpadding="4">
<tr>
  <td bgcolor="Aqua" align="center" colspan="3">Base Fields</td>
</tr>
<tr>
  <td bgcolor="#9cf0ff" nowrap valign="top">Number:</td>
  <td bgcolor="#9cf0ff" valign="top">
    {{ $article->number }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $article->number }}{{ /literal }}</td>
</tr>
<tr>
  <td bgcolor="#9cf0ff" nowrap valign="top">Type:</td>
  <td bgcolor="#9cf0ff" valign="top">
    {{ $article->type_name }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $article->type_name }}{{ /literal }}</td>
</tr>
<tr>
  <td bgcolor="#9cf0ff" nowrap valign="top">Name:</td>
  <td bgcolor="#9cf0ff" valign="top">
    {{ $article->name }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $article->name }}{{ /literal }}</td>
</tr>
<tr>
  <td bgcolor="#9cf0ff" nowrap valign="top">Publish Date:</td>
  <td bgcolor="#9cf0ff" valign="top">
    {{ $article->publish_date }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $article->publish_date }}{{ /literal }}</td>
</tr>
<tr>
  <td bgcolor="#9cf0ff" nowrap valign="top">Creation Date:</td>
  <td bgcolor="#9cf0ff" valign="top">
    {{ $article->creation_date }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $article->creation_date }}{{ /literal }}</td>
</tr>
<tr>
  <td bgcolor="#9cf0ff" nowrap valign="top">Keywords:</td>
  <td bgcolor="#9cf0ff" valign="top">
    {{ $article->keywords }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $article->keywords }}{{ /literal }}</td>
</tr>
<tr>
  <td bgcolor="#9cf0ff" nowrap valign="top">URL Name:</td>
  <td bgcolor="#9cf0ff" valign="top">
    {{ $article->url_name }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $article->url_name }}{{ /literal }}</td>
</tr>
<tr>
  <td bgcolor="#9cf0ff" nowrap valign="top">Comments Enabled:</td>
  <td bgcolor="#9cf0ff" valign="top">
    {{ $article->comments_enabled }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $article->comments_enabled }}{{ /literal }}</td>
</tr>
<tr>
  <td bgcolor="#9cf0ff" nowrap valign="top">Comments Locked:</td>
  <td bgcolor="#9cf0ff" valign="top">
    {{ $article->comments_locked }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $article->comments_locked }}{{ /literal }}</td>
</tr>
<tr>
  <td bgcolor="#9cf0ff" nowrap valign="top">Last Update:</td>
  <td bgcolor="#9cf0ff" valign="top">
    {{ $article->last_update }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $article->last_update }}{{ /literal }}</td>
</tr>


<tr>
  <td bgcolor="#cdff59" align="center" colspan="3">Custom Fields</td>
</tr>
<tr>
  <td bgcolor="#d4ffa2" nowrap valign="top">Year:</td>
  <td bgcolor="#d4ffa2" valign="top">
    {{ $article->year }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $article->year }}{{ /literal }}</td>
</tr>
<tr>
  <td bgcolor="#d4ffa2" nowrap valign="top">Month:</td>
  <td bgcolor="#d4ffa2" valign="top">
    {{ $article->mon }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $article->mon }}{{ /literal }}</td>
</tr>
<tr>
  <td bgcolor="#d4ffa2" nowrap valign="top">Week Day:</td>
  <td bgcolor="#d4ffa2" valign="top">
    {{ $article->wday }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $article->wday }}{{ /literal }}</td>
</tr>
<tr>
  <td bgcolor="#d4ffa2" nowrap valign="top">Month Day:</td>
  <td bgcolor="#d4ffa2" valign="top">
    {{ $article->mon }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $article->mon }}{{ /literal }}</td>
</tr>
<tr>
  <td bgcolor="#d4ffa2" nowrap valign="top">Year Day:</td>
  <td bgcolor="#d4ffa2" valign="top">
    {{ $article->yday }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $article->yday }}{{ /literal }}</td>
</tr>
<tr>
  <td bgcolor="#d4ffa2" nowrap valign="top">Hour:</td>
  <td bgcolor="#d4ffa2" valign="top">
    {{ $article->hour }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $article->hour }}{{ /literal }}</td>
</tr>
<tr>
  <td bgcolor="#d4ffa2" nowrap valign="top">Minute:</td>
  <td bgcolor="#d4ffa2" valign="top">
    {{ $article->min }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $article->min }}{{ /literal }}</td>
</tr>
<tr>
  <td bgcolor="#d4ffa2" nowrap valign="top">Second:</td>
  <td bgcolor="#d4ffa2" valign="top">
    {{ $article->sec }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $article->sec }}{{ /literal }}</td>
</tr>
<tr>
  <td bgcolor="#d4ffa2" nowrap valign="top">On Front Page:</td>
  <td bgcolor="#d4ffa2" valign="top">
    {{ $article->on_front_page }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $article->on_front_page }}{{ /literal }}</td>
</tr>
<tr>
  <td bgcolor="#d4ffa2" nowrap valign="top">On Section Page:</td>
  <td bgcolor="#d4ffa2" valign="top">
    {{ $article->on_section_page }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $article->on_section_page }}{{ /literal }}</td>
</tr>
<tr>
  <td bgcolor="#d4ffa2" nowrap valign="top">Published:</td>
  <td bgcolor="#d4ffa2" valign="top">
    {{ $article->is_published }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $article->is_published }}{{ /literal }}</td>
</tr>
<tr>
  <td bgcolor="#d4ffa2" nowrap valign="top">Public:</td>
  <td bgcolor="#d4ffa2" valign="top">
    {{ $article->is_public }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $article->is_public }}{{ /literal }}</td>
</tr>
<tr>
  <td bgcolor="#d4ffa2" nowrap valign="top">Indexed:</td>
  <td bgcolor="#d4ffa2" valign="top">
    {{ $article->is_indexed }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $article->is_indexed }}{{ /literal }}</td>
</tr>


<tr>
  <td bgcolor="#d4ffa2" nowrap valign="top">Publication:</td>
  <td bgcolor="#d4ffa2" valign="top">
    {{ $article->publication }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $article->publication }}{{ /literal }}</td>
</tr>
<tr>
  <td bgcolor="#d4ffa2" nowrap valign="top">Issue:</td>
  <td bgcolor="#d4ffa2" valign="top">
    {{ $article->issue }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $article->issue }}{{ /literal }}</td>
</tr>
<tr>
  <td bgcolor="#d4ffa2" nowrap valign="top">Section:</td>
  <td bgcolor="#d4ffa2" valign="top">
    {{ $article->section }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $article->section }}{{ /literal }}</td>
</tr>
<tr>
  <td bgcolor="#d4ffa2" nowrap valign="top">Language:</td>
  <td bgcolor="#d4ffa2" valign="top">
    {{ $article->language }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $article->language }}{{ /literal }}</td>
</tr>
<tr>
  <td bgcolor="#d4ffa2" nowrap valign="top">Owner:</td>
  <td bgcolor="#d4ffa2" valign="top">
    {{ $article->owner }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $article->owner }}{{ /literal }}</td>
</tr>


<tr>
  <td bgcolor="#d4ffa2" nowrap valign="top">Intro:</td>
  <td bgcolor="#d4ffa2" valign="top">
    {{ $article->type->fastnews->intro }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $article->type->fastnews->intro }}{{ /literal }}</td>
</tr>
<tr>
  <td bgcolor="#d4ffa2" nowrap valign="top">Body:</td>
  <td bgcolor="#d4ffa2" valign="top">
    {{ $article->type->fastnews->body }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $article->type->fastnews->body }}{{ /literal }}</td>
</tr>
</table>

</body>
</html>
