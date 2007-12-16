<html>
<head>
  <title>Camp Smarty</title>
</head>
<body>

<h3>issues list</h3>
{{ list_issues length="2" columns="3" name='sample_name' constraints="name greater a" order='byDate asc' }}
{{ if $campsite->current_list->at_beginning }}
<li>count: {{ $campsite->current_list->count }}</li>
{{ /if }}
<li>issue: <b>{{ $campsite->current_issues_list->current->name }}</b>/<b>{{ $campsite->current_list->current->name }}</b>/<b>{{ $campsite->issue->name }}</b>,
   list index: <b>{{ $campsite->current_issues_list->index }}</b>/<b>{{ $campsite->current_list->index }}</b>,
   column: <b>{{ $campsite->current_issues_list->column }}</b>/<b>{{ $campsite->current_list->column }}</b>
   (current issues list/current list/context)
</li>
{{ if $campsite->current_list->at_end }}
    <li>has next elements: {{ $campsite->current_list->hasNextElements() }}</li>
{{ /if }}
{{ /list_issues }}


<h3>sections list</h3>
{{ list_sections length="3" columns="2" name='sample_name' constraints="name greater a number greater 0" }}
{{ if $campsite->current_list->at_beginning }}
<li>count: {{ $campsite->current_list->count }}</li>
{{ /if }}
<li>section: <b>{{ $campsite->current_sections_list->current->name }}</b>/<b>{{ $campsite->current_list->current->name }}</b>/<b>{{ $campsite->section->name }}</b>,
   list index: <b>{{ $campsite->current_sections_list->index }}</b>/<b>{{ $campsite->current_list->index }}</b>,
   column: <b>{{ $campsite->current_sections_list->column }}</b>/<b>{{ $campsite->current_list->column }}</b>
   (current sections list/current list/context)
</li>
{{ if $campsite->current_list->at_end }}
    <li>has next elements: {{ $campsite->current_list->hasNextElements() }}</li>
{{ /if }}
{{ /list_sections }}


<h3>articles list</h3>
{{ list_articles length="3" columns="2" name='sample_name' }}
{{ if $campsite->current_list->at_beginning }}
<li>count: {{ $campsite->current_list->count }}</li>
{{ /if }}
<li>article: <b>{{ $campsite->current_articles_list->current->name }}</b>/<b>{{ $campsite->current_list->current->name }}</b>/<b>{{ $campsite->article->name }}</b>,
   list index: <b>{{ $campsite->current_articles_list->index }}</b>/<b>{{ $campsite->current_list->index }}</b>,
   column: <b>{{ $campsite->current_articles_list->column }}</b>/<b>{{ $campsite->current_list->column }}</b>
   (current articles list/current list/context)
</li>
{{ if $campsite->current_list->at_end }}
    <li>has next elements: {{ $campsite->current_list->hasNextElements() }}</li>
{{ /if }}
{{ /list_articles }}


<h3>article attachments list</h3>
{{ list_article_attachments length="3" columns="2" name='sample_name' }}
{{ if $campsite->current_list->at_beginning }}
<li>count: {{ $campsite->current_list->count }}</li>
{{ /if }}
<li>article attachment: <b>{{ $campsite->current_article_attachments_list->current->file_name }}</b>/<b>{{ $campsite->current_list->current->file_name }}</b>/<b>{{ $campsite->attachment->file_name }}</b>,
   list index: <b>{{ $campsite->current_article_attachments_list->index }}</b>/<b>{{ $campsite->current_list->index }}</b>,
   column: <b>{{ $campsite->current_article_attachments_list->column }}</b>/<b>{{ $campsite->current_list->column }}</b>
   (current article attachments list/current list/context)
</li>
{{ if $campsite->current_list->at_end }}
    <li>has next elements: {{ $campsite->current_list->hasNextElements() }}</li>
{{ /if }}
{{ /list_article_attachments }}


<h3>article comments list</h3>
{{ list_article_comments length="3" columns="2" name='sample_name' order='byDate asc' }}
{{ if $campsite->current_list->at_beginning }}
<li>count: {{ $campsite->current_list->count }}</li>
{{ /if }}
<li>article comment: <b>{{ $campsite->current_article_comments_list->current->subject }}</b>/<b>{{ $campsite->current_list->current->subject }}</b>/<b>{{ $campsite->comment->subject }}</b>,
   list index: <b>{{ $campsite->current_article_comments_list->index }}</b>/<b>{{ $campsite->current_list->index }}</b>,
   column: <b>{{ $campsite->current_article_comments_list->column }}</b>/<b>{{ $campsite->current_list->column }}</b>
   (current article comments list/current list/context)
</li>
{{ if $campsite->current_list->at_end }}
    <li>has next elements: {{ $campsite->current_list->hasNextElements() }}</li>
{{ /if }}
{{ /list_article_comments }}


<h3>article images list</h3>
{{ list_article_images length="3" columns="2" name='sample_name' }}
{{ if $campsite->current_list->at_beginning }}
<li>count: {{ $campsite->current_list->count }}</li>
{{ /if }}
<li>article image: <b>{{ $campsite->current_article_images_list->current->description }}</b>/<b>{{ $campsite->current_list->current->description }}</b>/<b>{{ $campsite->image->description }}</b>,
   list index: <b>{{ $campsite->current_article_images_list->index }}</b>/<b>{{ $campsite->current_list->index }}</b>,
   column: <b>{{ $campsite->current_article_images_list->column }}</b>/<b>{{ $campsite->current_list->column }}</b>
   (current article images list/current list/context)
</li>
{{ if $campsite->current_list->at_end }}
    <li>has next elements: {{ $campsite->current_list->hasNextElements() }}</li>
{{ /if }}
{{ /list_article_images }}


<h3>article topics list</h3>
{{ list_article_topics length="3" columns="2" name='sample_name' }}
{{ if $campsite->current_list->at_beginning }}
<li>count: {{ $campsite->current_list->count }}</li>
{{ /if }}
<li>article topic: <b>{{ $campsite->current_article_topics_list->current->name }}</b>/<b>{{ $campsite->current_list->current->name }}</b>/<b>{{ $campsite->topic->name }}</b>,
   list index: <b>{{ $campsite->current_article_topics_list->index }}</b>/<b>{{ $campsite->current_list->index }}</b>,
   column: <b>{{ $campsite->current_article_topics_list->column }}</b>/<b>{{ $campsite->current_list->column }}</b>
   (current article topics list/current list/context)
</li>
{{ if $campsite->current_list->at_end }}
    <li>has next elements: {{ $campsite->current_list->hasNextElements() }}</li>
{{ /if }}
{{ /list_article_topics }}


<h3>article audio attachments list</h3>
{{ list_article_audio_attachments length="3" columns="2" name='sample_name' }}
{{ if $campsite->current_list->at_beginning }}
<li>count: {{ $campsite->current_list->count }}</li>
{{ /if }}
<li>article audio attachment: <b>{{ $campsite->current_article_audio_attachments_list->current->title }}</b>/<b>{{ $campsite->current_list->current->title }}</b>/<b>{{ $campsite->audioclip->title }}</b>,
   list index: <b>{{ $campsite->current_article_audio_attachments_list->index }}</b>/<b>{{ $campsite->current_list->index }}</b>,
   column: <b>{{ $campsite->current_article_audio_attachments_list->column }}</b>/<b>{{ $campsite->current_list->column }}</b>
   (current article audio attachments list/current list/context)
</li>
{{ if $campsite->current_list->at_end }}
    <li>has next elements: {{ $campsite->current_list->hasNextElements() }}</li>
{{ /if }}
{{ /list_article_audio_attachments }}


<h3>search results list</h3>
{{ list_search_results length="3" columns="2" name='sample_name' }}
{{ if $campsite->current_list->at_beginning }}
<li>count: {{ $campsite->current_list->count }}</li>
{{ /if }}
<li>search result: <b>{{ $campsite->current_search_results_list->current->name }}</b>/<b>{{ $campsite->current_list->current->name }}</b>/<b>{{ $campsite->article->name }}</b>,
   list index: <b>{{ $campsite->current_search_results_list->index }}</b>/<b>{{ $campsite->current_list->index }}</b>,
   column: <b>{{ $campsite->current_search_results_list->column }}</b>/<b>{{ $campsite->current_list->column }}</b>
   (current search results list/current list/context)
</li>
{{ if $campsite->current_list->at_end }}
    <li>has next elements: {{ $campsite->current_list->hasNextElements() }}</li>
{{ /if }}
{{ /list_search_results }}


{{ local }}
{{ unset_topic }}
{{ if $campsite->topic->defined }}
    <h3>subtopics of topic {{ $campsite->topic->name }}</h3>
{{ else }}
    <h3>root topics</h3>
{{ /if }}
{{ list_subtopics length="4" columns="2" name='sample_name' }}
{{ if $campsite->current_list->at_beginning }}
<li>count: {{ $campsite->current_list->count }}</li>
{{ /if }}
<li>subtopic: <b>{{ $campsite->current_subtopics_list->current->name }}</b>/<b>{{ $campsite->current_list->current->name }}</b>/<b>{{ $campsite->topic->name }}</b>,
   list index: <b>{{ $campsite->current_subtopics_list->index }}</b>/<b>{{ $campsite->current_list->index }}</b>,
   column: <b>{{ $campsite->current_subtopics_list->column }}</b>/<b>{{ $campsite->current_list->column }}</b>
   (current subtopics list/current list/context)
</li>
{{ if $campsite->current_list->at_end }}
    <li>has next elements: {{ $campsite->current_list->hasNextElements() }}</li>
{{ /if }}
{{ /list_subtopics }}
{{ /local }}


<h3>subtitles list</h3>
{{ list_subtitles length="2" columns="2" name='sample_name' constraints="invalid constraints" order='invalid order' invalid_parameter="invalid" }}
<li>subtitle: <b>{{ $campsite->current_subtitles_list->getCurrent() }}</b>/<b>{{ $campsite->current_list->getCurrent() }}</b>,
   list index: <b>{{ $campsite->current_subtitles_list->getIndex() }}</b>/<b>{{ $campsite->current_list->getIndex() }}</b>,
   column: <b>{{ $campsite->current_subtitles_list->getColumn() }}</b>/<b>{{ $campsite->current_list->getColumn() }}</b>
   (current subtitles list/current list)
</li>
{{ /list_subtitles }}


{{ if $campsite->hasProperty('invalid_property') }}
	<h3>Context error: 'invalid_property' was reported as valid.</h3>
{{ /if }}


{{ invalid_tag }}


{{ $smarty.invalid_reference }}


{{ $campsite->article->invalid_property }}


{{ set_language invalid_property="1" }}


{{ set_publication invalid_property="6" }}


{{ set_publication identifier="invalid_value" }}


{{ set_issue invalid_property="1" }}


{{ set_section invalid_property="1" }}


{{ set_article invalid_property="143" }}


{{ $campsite->invalid_property }}


{{** HTMLEncoding **}}
<table cellspacing="1" cellpadding="4">
<tr>
  <td bgcolor="#6a6a6a"><font color="#ffffff">HTMLEncoding</font></td>
</tr>
</table>
<table cellspacing="1" cellpadding="4">
<tr>
  <td bgcolor="#9cf0ff">
    Default HTMLEncoding value:
  </td>
  <td bgcolor="#9cf0ff" align="center">
    {{ if $campsite->htmlencoding eq false }}
      false
    {{ else }}
      {{ $campsite->htmlencoding }}
    {{ /if }}
  </td>
  <td>
    {{ literal }}
      {{ $campsite->htmlencoding }}
    {{ /literal }}
  </td>
</tr>
<tr>
  <td bgcolor="#9cf0ff">
    Enabling HTMLEncoding:
  </td>
  <td bgcolor="#9cf0ff" align="center">
    {{ enable_html_encoding }}

    {{ if $campsite->htmlencoding eq false }}
      false
    {{ else }}
      {{ $campsite->htmlencoding }}
    {{ /if }}
  </td>
  <td>
    {{ literal }}
      {{ enable_html_encoding }}
    {{ /literal }}
  </td>
</tr>
<tr>
  <td bgcolor="#9cf0ff">
    Disabling HTMLEncoding:
  </td>
  <td bgcolor="#9cf0ff" align="center">
    {{ disable_html_encoding }}

    {{ if $campsite->htmlencoding eq false }}
      false
    {{ else }}
      {{ $campsite->htmlencoding }}
    {{ /if }}
  </td>
  <td>
    {{ literal }}
      {{ disable_html_encoding }}
    {{ /literal }}
  </td>
</tr>
</table><br />


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
    {{ $campsite->language->name }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $campsite->language->name }}{{ /literal }}</td>
  <td nowrap valign="top">base</td>
</tr>
<tr>
  <td bgcolor="#9cf0ff" nowrap valign="top">Number:</td>
  <td bgcolor="#9cf0ff" valign="top">
    {{ $campsite->language->number }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $campsite->language->number }}{{ /literal }}</td>
  <td nowrap valign="top">base</td>
</tr>
<tr>
  <td bgcolor="#9cf0ff" nowrap valign="top">English Name:</td>
  <td bgcolor="#9cf0ff" valign="top">
    {{ $campsite->language->english_name }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $campsite->language->english_name }}{{ /literal }}</td>
  <td nowrap valign="top">base</td>
</tr>
<tr>
  <td bgcolor="#9cf0ff" nowrap valign="top">Code:</td>
  <td bgcolor="#9cf0ff" valign="top">
    {{ $campsite->language->code }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $campsite->language->code }}{{ /literal }}</td>
  <td nowrap valign="top">base</td>
</tr>
<tr>
  <td bgcolor="#d4ffa2" nowrap valign="top">Defined:</td>
  <td bgcolor="#d4ffa2" valign="top">
    {{ $campsite->language->defined }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $campsite->language->defined }}{{ /literal }}</td>
  <td nowrap valign="top">custom</td>
</tr>
</table>


<table cellspacing="1" cellpadding="4">
<tr>
  <td bgcolor="#dfdfdf" nowrap valign="top">
    {{ unset_language }}
    Unset by
  </td>
  <td bgcolor="#dfdfdf">
    {{ literal }}{{ unset_language }}{{ /literal }}
  </td>
</tr>
<tr>
  <td bgcolor="#ffcc66" nowrap valign="top">Name:</td>
  <td bgcolor="#ffcc66" valign="top">
    {{ $campsite->language->english_name }}
  </td>
</tr>
<tr>
  <td bgcolor="#ffcc66" nowrap valign="top">Defined:</td>
  <td bgcolor="#ffcc66" valign="top">
    {{ $campsite->language->defined }}
  </td>
</tr>
<tr>
  <td bgcolor="#dfdfdf" nowrap valign="top">
    {{ set_language name="English" }}
    Set by
  </td>
  <td bgcolor="#dfdfdf">
    {{ literal }}{{ set_language name="English" }}{{ /literal }}
  </td>
</tr>
<tr>
  <td bgcolor="#ffcc66" nowrap valign="top">Name:</td>
  <td bgcolor="#ffcc66" valign="top">
    {{ $campsite->language->english_name }}
  </td>
</tr>
<tr>
  <td bgcolor="#ffcc66" nowrap valign="top">Defined:</td>
  <td bgcolor="#ffcc66" valign="top">
    {{ $campsite->language->defined }}
  </td>
</tr>
</table>
<br />


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
    {{ $campsite->publication->name }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $campsite->publication->name }}{{ /literal }}</td>
  <td nowrap valign="top">base</td>
</tr>
<tr>
  <td bgcolor="#9cf0ff" nowrap valign="top">Identifier:</td>
  <td bgcolor="#9cf0ff" valign="top">
    {{ $campsite->publication->identifier }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $campsite->publication->identifier }}{{ /literal }}</td>
  <td nowrap valign="top">base</td>
</tr>
<tr>
  <td bgcolor="#d4ffa2" nowrap valign="top">Site:</td>
  <td bgcolor="#d4ffa2" valign="top">
    {{ $campsite->publication->site }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $campsite->publication->site }}{{ /literal }}</td>
  <td nowrap valign="top">custom</td>
</tr>
<tr>
  <td bgcolor="#d4ffa2" nowrap valign="top">Defined:</td>
  <td bgcolor="#d4ffa2" valign="top">
    {{ $campsite->publication->defined }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $campsite->publication->defined }}{{ /literal }}</td>
  <td nowrap valign="top">custom</td>
</tr>
</table>


<table cellspacing="1" cellpadding="4">
<tr>
  <td bgcolor="#dfdfdf" nowrap valign="top">
    {{ unset_publication }}
    Unset by
  </td>
  <td bgcolor="#dfdfdf">
    {{ literal }}{{ unset_publication }}{{ /literal }}
  </td>
</tr>
<tr>
  <td bgcolor="#ffcc66" nowrap valign="top">Name:</td>
  <td bgcolor="#ffcc66" valign="top">
    {{ $campsite->publication->name }}
  </td>
</tr>
<tr>
  <td bgcolor="#ffcc66" nowrap valign="top">Defined:</td>
  <td bgcolor="#ffcc66" valign="top">
    {{ $campsite->publication->defined }}
  </td>
</tr>
<tr>
  <td bgcolor="#dfdfdf" nowrap valign="top">
    {{ set_publication identifier="1" }}
    Set by
  </td>
  <td bgcolor="#dfdfdf">
    {{ literal }}{{ set_publication identifier="1" }}{{ /literal }}
  </td>
</tr>
<tr>
  <td bgcolor="#ffcc66" nowrap valign="top">Name:</td>
  <td bgcolor="#ffcc66" valign="top">
    {{ $campsite->publication->name }}
  </td>
</tr>
<tr>
  <td bgcolor="#ffcc66" nowrap valign="top">Defined:</td>
  <td bgcolor="#ffcc66" valign="top">
    {{ $campsite->publication->defined }}
  </td>
</tr>
</table>
<br />


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
    {{ $campsite->issue->name }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $campsite->issue->name }}{{ /literal }}</td>
  <td nowrap valign="top">base</td>
</tr>
<tr>
  <td bgcolor="#9cf0ff" nowrap valign="top">Number:</td>
  <td bgcolor="#9cf0ff" valign="top">
    {{ $campsite->issue->number }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $campsite->issue->number }}{{ /literal }}</td>
  <td nowrap valign="top">base</td>
</tr>
<tr>
  <td bgcolor="#d4ffa2" nowrap valign="top">Year:</td>
  <td bgcolor="#d4ffa2" valign="top">
    {{ $campsite->issue->year }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $campsite->issue->year }}{{ /literal }}</td>
  <td nowrap valign="top">custom</td>
</tr>
<tr>
  <td bgcolor="#d4ffa2" nowrap valign="top">Month:</td>
  <td bgcolor="#d4ffa2" valign="top">
    {{ $campsite->issue->mon }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $campsite->issue->mon }}{{ /literal }}</td>
  <td nowrap valign="top">custom</td>
</tr>
<tr>
  <td bgcolor="#d4ffa2" nowrap valign="top">Week Day:</td>
  <td bgcolor="#d4ffa2" valign="top">
    {{ $campsite->issue->wday }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $campsite->issue->wday }}{{ /literal }}</td>
  <td nowrap valign="top">custom</td>
</tr>
<tr>
  <td bgcolor="#d4ffa2" nowrap valign="top">Month Day:</td>
  <td bgcolor="#d4ffa2" valign="top">
    {{ $campsite->issue->mday }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $campsite->issue->mday }}{{ /literal }}</td>
  <td nowrap valign="top">custom</td>
</tr>
<tr>
  <td bgcolor="#d4ffa2" nowrap valign="top">Year Day:</td>
  <td bgcolor="#d4ffa2" valign="top">
    {{ $campsite->issue->yday }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $campsite->issue->yday }}{{ /literal }}</td>
  <td nowrap valign="top">custom</td>
</tr>
<tr>
  <td bgcolor="#d4ffa2" nowrap valign="top">Hour:</td>
  <td bgcolor="#d4ffa2" valign="top">
    {{ $campsite->issue->hour }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $campsite->issue->hour }}{{ /literal }}</td>
  <td nowrap valign="top">custom</td>
</tr>
<tr>
  <td bgcolor="#d4ffa2" nowrap valign="top">Minute:</td>
  <td bgcolor="#d4ffa2" valign="top">
    {{ $campsite->issue->min }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $campsite->issue->min }}{{ /literal }}</td>
  <td nowrap valign="top">custom</td>
</tr>
<tr>
  <td bgcolor="#d4ffa2" nowrap valign="top">Second:</td>
  <td bgcolor="#d4ffa2" valign="top">
    {{ $campsite->issue->sec }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $campsite->issue->sec }}{{ /literal }}</td>
  <td nowrap valign="top">custom</td>
</tr>
<tr>
  <td bgcolor="#9cf0ff" nowrap valign="top">Date:</td>
  <td bgcolor="#9cf0ff" valign="top">
    {{ $campsite->issue->date }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $campsite->issue->date }}{{ /literal }}</td>
  <td nowrap valign="top">base</td>
</tr>
<tr>
  <td bgcolor="#9cf0ff" nowrap valign="top">Publish Date:</td>
  <td bgcolor="#9cf0ff" valign="top">
    {{ $campsite->issue->publish_date }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $campsite->issue->publish_date }}{{ /literal }}</td>
  <td nowrap valign="top">base</td>
</tr>
<tr>
  <td bgcolor="#d4ffa2" nowrap valign="top">Template:</td>
  <td bgcolor="#d4ffa2" valign="top">
    {{ $campsite->issue->template->name }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $campsite->issue->template->name }}{{ /literal }}</td>
  <td nowrap valign="top">custom</td>
</tr>
<tr>
  <td bgcolor="#d4ffa2" nowrap valign="top">Defined:</td>
  <td bgcolor="#d4ffa2" valign="top">
    {{ $campsite->issue->defined }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $campsite->issue->defined }}{{ /literal }}</td>
  <td nowrap valign="top">custom</td>
</tr>
</table>


<table cellspacing="1" cellpadding="4">
<tr>
  <td bgcolor="#dfdfdf" nowrap valign="top">
    {{ unset_issue }}
    Unset by
  </td>
  <td bgcolor="#dfdfdf">
    {{ literal }}{{ unset_issue }}{{ /literal }}
  </td>
</tr>
<tr>
  <td bgcolor="#ffcc66" nowrap valign="top">Name:</td>
  <td bgcolor="#ffcc66" valign="top">
    {{ $campsite->issue->name }}
  </td>
</tr>
<tr>
  <td bgcolor="#ffcc66" nowrap valign="top">Defined:</td>
  <td bgcolor="#ffcc66" valign="top">
    {{ $campsite->issue->defined }}
  </td>
</tr>
<tr>
  <td bgcolor="#dfdfdf" nowrap valign="top">
    {{ set_issue number="1" }}
    Set by
  </td>
  <td bgcolor="#dfdfdf">
    {{ literal }}{{ set_issue number="1" }}{{ /literal }}
  </td>
</tr>
<tr>
  <td bgcolor="#ffcc66" nowrap valign="top">Name:</td>
  <td bgcolor="#ffcc66" valign="top">
    {{ $campsite->issue->name }}
  </td>
</tr>
<tr>
  <td bgcolor="#ffcc66" nowrap valign="top">Defined:</td>
  <td bgcolor="#ffcc66" valign="top">
    {{ $campsite->issue->defined }}
  </td>
</tr>
</table>
<br />


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
    {{ $campsite->section->name }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $campsite->section->name }}{{ /literal }}</td>
  <td nowrap valign="top">base</td>
</tr>
<tr>
  <td bgcolor="#9cf0ff" nowrap valign="top">Number:</td>
  <td bgcolor="#9cf0ff" valign="top">
    {{ $campsite->section->number }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $campsite->section->number }}{{ /literal }}</td>
  <td nowrap valign="top">base</td>
</tr>
<tr>
  <td bgcolor="#9cf0ff" nowrap valign="top">Description:</td>
  <td bgcolor="#9cf0ff" valign="top">
    {{ $campsite->section->description }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $campsite->section->description }}{{ /literal }}</td>
  <td nowrap valign="top">base</td>
</tr>
<tr>
  <td bgcolor="#d4ffa2" nowrap valign="top">Template:</td>
  <td bgcolor="#d4ffa2" valign="top">
    {{ $campsite->section->template->name }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $campsite->section->template->name }}{{ /literal }}</td>
  <td nowrap valign="top">custom</td>
</tr>
<tr>
  <td bgcolor="#d4ffa2" nowrap valign="top">Defined:</td>
  <td bgcolor="#d4ffa2" valign="top">
    {{ $campsite->section->defined }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $campsite->section->defined }}{{ /literal }}</td>
  <td nowrap valign="top">custom</td>
</tr>
</table>


<table cellspacing="1" cellpadding="4">
<tr>
  <td bgcolor="#dfdfdf" nowrap valign="top">
    {{ unset_section }}
    Unset by
  </td>
  <td bgcolor="#dfdfdf">
    {{ literal }}{{ unset_section }}{{ /literal }}
  </td>
</tr>
<tr>
  <td bgcolor="#ffcc66" nowrap valign="top">Name:</td>
  <td bgcolor="#ffcc66" valign="top">
    {{ $campsite->section->name }}
  </td>
</tr>
<tr>
  <td bgcolor="#ffcc66" nowrap valign="top">Defined:</td>
  <td bgcolor="#ffcc66" valign="top">
    {{ $campsite->section->defined }}
  </td>
</tr>
<tr>
  <td bgcolor="#dfdfdf" nowrap valign="top">
    {{ set_section number="10" }}
    Set by
  </td>
  <td bgcolor="#dfdfdf">
    {{ literal }}{{ set_section number="10" }}{{ /literal }}
  </td>
</tr>
<tr>
  <td bgcolor="#ffcc66" nowrap valign="top">Name:</td>
  <td bgcolor="#ffcc66" valign="top">
    {{ $campsite->section->name }}
  </td>
</tr>
<tr>
  <td bgcolor="#ffcc66" nowrap valign="top">Defined:</td>
  <td bgcolor="#ffcc66" valign="top">
    {{ $campsite->section->defined }}
  </td>
</tr>
</table>
<br />


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
    {{ $campsite->article->name }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $campsite->article->name }}{{ /literal }}</td>
  <td nowrap valign="top">base</td>
</tr>
<tr>
  <td bgcolor="#9cf0ff" nowrap valign="top">Number:</td>
  <td bgcolor="#9cf0ff" valign="top">
    {{ $campsite->article->number }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $campsite->article->number }}{{ /literal }}</td>
  <td nowrap valign="top">base</td>
</tr>
<tr>
  <td bgcolor="#9cf0ff" nowrap valign="top">Keywords:</td>
  <td bgcolor="#9cf0ff" valign="top">
    {{ $campsite->article->keywords }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $campsite->article->keywords }}{{ /literal }}</td>
  <td nowrap valign="top">base</td>
</tr>
<tr>
  <td bgcolor="#9cf0ff" nowrap valign="top">Type:</td>
  <td bgcolor="#9cf0ff" valign="top">
    {{ $campsite->article->type_name }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $campsite->article->type_name }}{{ /literal }}</td>
  <td nowrap valign="top">base</td>
</tr>
<tr>
  <td bgcolor="#d4ffa2" nowrap valign="top">Year:</td>
  <td bgcolor="#d4ffa2" valign="top">
    {{ $campsite->article->year }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $campsite->article->year }}{{ /literal }}</td>
  <td nowrap valign="top">custom</td>
</tr>
<tr>
  <td bgcolor="#d4ffa2" nowrap valign="top">Month:</td>
  <td bgcolor="#d4ffa2" valign="top">
    {{ $campsite->article->mon }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $campsite->article->mon }}{{ /literal }}</td>
  <td nowrap valign="top">custom</td>
</tr>
<tr>
  <td bgcolor="#d4ffa2" nowrap valign="top">Week Day:</td>
  <td bgcolor="#d4ffa2" valign="top">
    {{ $campsite->article->wday }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $campsite->article->wday }}{{ /literal }}</td>
  <td nowrap valign="top">custom</td>
</tr>
<tr>
  <td bgcolor="#d4ffa2" nowrap valign="top">Month Day:</td>
  <td bgcolor="#d4ffa2" valign="top">
    {{ $campsite->article->mday }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $campsite->article->mday }}{{ /literal }}</td>
  <td nowrap valign="top">custom</td>
</tr>
<tr>
  <td bgcolor="#d4ffa2" nowrap valign="top">Year Day:</td>
  <td bgcolor="#d4ffa2" valign="top">
    {{ $campsite->article->yday }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $campsite->article->yday }}{{ /literal }}</td>
  <td nowrap valign="top">custom</td>
</tr>
<tr>
  <td bgcolor="#d4ffa2" nowrap valign="top">Hour:</td>
  <td bgcolor="#d4ffa2" valign="top">
    {{ $campsite->article->hour }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $campsite->article->hour }}{{ /literal }}</td>
  <td nowrap valign="top">custom</td>
</tr>
<tr>
  <td bgcolor="#d4ffa2" nowrap valign="top">Minute:</td>
  <td bgcolor="#d4ffa2" valign="top">
    {{ $campsite->article->min }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $campsite->article->min }}{{ /literal }}</td>
  <td nowrap valign="top">custom</td>
</tr>
<tr>
  <td bgcolor="#d4ffa2" nowrap valign="top">Second:</td>
  <td bgcolor="#d4ffa2" valign="top">
    {{ $campsite->article->sec }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $campsite->article->sec }}{{ /literal }}</td>
  <td nowrap valign="top">custom</td>
</tr>

<tr>
  <td bgcolor="#9cf0ff" nowrap valign="top">Creation Date:</td>
  <td bgcolor="#9cf0ff" valign="top">
    {{ $campsite->article->creation_date }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $campsite->article->creation_date }}{{ /literal }}</td>
  <td nowrap valign="top">base</td>
</tr>
<tr>
  <td bgcolor="#9cf0ff" nowrap valign="top">Publish Date:</td>
  <td bgcolor="#9cf0ff" valign="top">
    {{ $campsite->article->publish_date }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $campsite->article->publish_date }}{{ /literal }}</td>
  <td nowrap valign="top">base</td>
</tr>

<tr>
  <td bgcolor="#d4ffa2" nowrap valign="top">Template:</td>
  <td bgcolor="#d4ffa2" valign="top">
    {{ $campsite->article->template->name }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $campsite->article->template->name }}{{ /literal }}</td>
  <td nowrap valign="top">custom</td>
</tr>

<tr>
  <td bgcolor="#d4ffa2" nowrap valign="top">Intro:</td>
  <td bgcolor="#d4ffa2" valign="top">
    {{ $campsite->article->type->fastnews->intro }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $campsite->article->type->fastnews->intro }}{{ /literal }}</td>
  <td nowrap valign="top">custom</td>
</tr>
<tr>
  <td bgcolor="#d4ffa2" nowrap valign="top">Body:</td>
  <td bgcolor="#d4ffa2" valign="top">
    {{ $campsite->article->type->fastnews->body }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $campsite->article->type->fastnews->body }}{{ /literal }}</td>
  <td nowrap valign="top">custom</td>
</tr>

<tr>
  <td bgcolor="#9cf0ff" nowrap valign="top">URL Name:</td>
  <td bgcolor="#9cf0ff" valign="top">
    {{ $campsite->article->url_name }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $campsite->article->url_name }}{{ /literal }}</td>
  <td nowrap valign="top">base</td>
</tr>
<tr>
  <td bgcolor="#9cf0ff" nowrap valign="top">Comments Enabled:</td>
  <td bgcolor="#9cf0ff" valign="top">
    {{ $campsite->article->comments_enabled }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $campsite->article->comments_enabled }}{{ /literal }}</td>
  <td nowrap valign="top">base</td>
</tr>
<tr>
  <td bgcolor="#9cf0ff" nowrap valign="top">Comments Locked:</td>
  <td bgcolor="#9cf0ff" valign="top">
    {{ $campsite->article->comments_locked }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $campsite->article->comments_locked }}{{ /literal }}</td>
  <td nowrap valign="top">base</td>
</tr>
<tr>
  <td bgcolor="#9cf0ff" nowrap valign="top">Last Update:</td>
  <td bgcolor="#9cf0ff" valign="top">
    {{ $campsite->article->last_update }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $campsite->article->last_update }}{{ /literal }}</td>
  <td nowrap valign="top">base</td>
</tr>

<tr>
  <td bgcolor="#d4ffa2" nowrap valign="top">On Front Page:</td>
  <td bgcolor="#d4ffa2" valign="top">
    {{ $campsite->article->on_front_page }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $campsite->article->on_front_page }}{{ /literal }}</td>
  <td nowrap valign="top">custom</td>
</tr>
<tr>
  <td bgcolor="#d4ffa2" nowrap valign="top">On Section Page:</td>
  <td bgcolor="#d4ffa2" valign="top">
    {{ $campsite->article->on_section_page }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $campsite->article->on_section_page }}{{ /literal }}</td>
  <td nowrap valign="top">custom</td>
</tr>
<tr>
  <td bgcolor="#d4ffa2" nowrap valign="top">Published:</td>
  <td bgcolor="#d4ffa2" valign="top">
    {{ $campsite->article->is_published }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $campsite->article->is_published }}{{ /literal }}</td>
  <td nowrap valign="top">custom</td>
</tr>
<tr>
  <td bgcolor="#d4ffa2" nowrap valign="top">Public:</td>
  <td bgcolor="#d4ffa2" valign="top">
    {{ $campsite->article->is_public }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $campsite->article->is_public }}{{ /literal }}</td>
  <td nowrap valign="top">custom</td>
</tr>
<tr>
  <td bgcolor="#d4ffa2" nowrap valign="top">Indexed:</td>
  <td bgcolor="#d4ffa2" valign="top">
    {{ $campsite->article->is_indexed }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $campsite->article->is_indexed }}{{ /literal }}</td>
  <td nowrap valign="top">custom</td>
</tr>

<tr>
  <td bgcolor="#d4ffa2" nowrap valign="top">Publication:</td>
  <td bgcolor="#d4ffa2" valign="top">
    {{ $campsite->article->publication->name }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $campsite->article->publication }}{{ /literal }}</td>
  <td nowrap valign="top">custom</td>
</tr>
<tr>
  <td bgcolor="#d4ffa2" nowrap valign="top">Issue:</td>
  <td bgcolor="#d4ffa2" valign="top">
    {{ $campsite->article->issue->name }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $campsite->article->issue }}{{ /literal }}</td>
  <td nowrap valign="top">custom</td>
</tr>
<tr>
  <td bgcolor="#d4ffa2" nowrap valign="top">Section:</td>
  <td bgcolor="#d4ffa2" valign="top">
    {{ $campsite->article->section->name }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $campsite->article->section }}{{ /literal }}</td>
  <td nowrap valign="top">custom</td>
</tr>
<tr>
  <td bgcolor="#d4ffa2" nowrap valign="top">Language:</td>
  <td bgcolor="#d4ffa2" valign="top">
    {{ $campsite->article->language->name }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $campsite->article->language->name }}{{ /literal }}</td>
  <td nowrap valign="top">custom</td>
</tr>
<tr>
  <td bgcolor="#d4ffa2" nowrap valign="top">Owner:</td>
  <td bgcolor="#d4ffa2" valign="top">
    {{ $campsite->article->owner->name }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $campsite->article->owner->name }}{{ /literal }}</td>
  <td nowrap valign="top">custom</td>
</tr>

<tr>
  <td bgcolor="#d4ffa2" nowrap valign="top">Defined:</td>
  <td bgcolor="#d4ffa2" valign="top">
    {{ $campsite->article->defined }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $campsite->article->defined }}{{ /literal }}</td>
  <td nowrap valign="top">custom</td>
</tr>
<tr>
  <td bgcolor="#d4ffa2" nowrap valign="top">Translated to:</td>
  <td bgcolor="#d4ffa2" valign="top">
    {{ $campsite->article->translated_to('ro') }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $campsite->article->translated_to('ro') }}{{ /literal }}</td>
  <td nowrap valign="top">custom</td>
</tr>
<tr>
  <td bgcolor="#d4ffa2" nowrap valign="top">Has Attachments:</td>
  <td bgcolor="#d4ffa2" valign="top">
    {{ $campsite->article->has_attachments }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $campsite->article->has_attachments }}{{ /literal }}</td>
  <td nowrap valign="top">custom</td>
</tr>
</table>


<table cellspacing="1" cellpadding="4">
<tr>
  <td bgcolor="#dfdfdf" nowrap valign="top">
    {{ unset_article }}
    Unset by
  </td>
  <td bgcolor="#dfdfdf">
    {{ literal }}{{ unset_article }}{{ /literal }}
  </td>
</tr>
<tr>
  <td bgcolor="#ffcc66" nowrap valign="top">Name:</td>
  <td bgcolor="#ffcc66" valign="top">
    {{ $campsite->article->name }}
  </td>
</tr>
<tr>
  <td bgcolor="#ffcc66" nowrap valign="top">Defined:</td>
  <td bgcolor="#ffcc66" valign="top">
    {{ $campsite->article->defined }}
  </td>
</tr>
<tr>
  <td bgcolor="#dfdfdf" nowrap valign="top">
    {{ set_article number="140" }}
    Set by
  </td>
  <td bgcolor="#dfdfdf">
    {{ literal }}{{ set_article number="140" }}{{ /literal }}
  </td>
</tr>
<tr>
  <td bgcolor="#ffcc66" nowrap valign="top">Name:</td>
  <td bgcolor="#ffcc66" valign="top">
    {{ $campsite->article->name }}
  </td>
</tr>
<tr>
  <td bgcolor="#ffcc66" nowrap valign="top">Defined:</td>
  <td bgcolor="#ffcc66" valign="top">
    {{ $campsite->article->defined }}
  </td>
</tr>
</table>
<br />


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
    {{ $campsite->image->number }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $campsite->image->number }}{{ /literal }}</td>
  <td nowrap valign="top">base</td>
</tr>
<tr>
  <td bgcolor="#9cf0ff" nowrap valign="top">Photographer:</td>
  <td bgcolor="#9cf0ff" valign="top">
    {{ $campsite->image->photographer }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $campsite->image->photographer }}{{ /literal }}</td>
  <td nowrap valign="top">base</td>
</tr>
<tr>
  <td bgcolor="#9cf0ff" nowrap valign="top">Place:</td>
  <td bgcolor="#9cf0ff" valign="top">
    {{ $campsite->image->place }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $campsite->image->place }}{{ /literal }}</td>
  <td nowrap valign="top">base</td>
</tr>
<tr>
  <td bgcolor="#9cf0ff" nowrap valign="top">Description:</td>
  <td bgcolor="#9cf0ff" valign="top">
    {{ $campsite->image->description }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $campsite->image->description }}{{ /literal }}</td>
  <td nowrap valign="top">base</td>
</tr>
<tr>
  <td bgcolor="#9cf0ff" nowrap valign="top">Date:</td>
  <td bgcolor="#9cf0ff" valign="top">
    {{ $campsite->image->date }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $campsite->image->date }}{{ /literal }}</td>
  <td nowrap valign="top">base</td>
</tr>


<tr>
  <td bgcolor="#d4ffa2" nowrap valign="top">Year:</td>
  <td bgcolor="#d4ffa2" valign="top">
    {{ $campsite->image->year }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $campsite->image->year }}{{ /literal }}</td>
  <td nowrap valign="top">custom</td>
</tr>
<tr>
  <td bgcolor="#d4ffa2" nowrap valign="top">Month:</td>
  <td bgcolor="#d4ffa2" valign="top">
    {{ $campsite->image->mon }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $campsite->image->mon }}{{ /literal }}</td>
  <td nowrap valign="top">custom</td>
</tr>
<tr>
  <td bgcolor="#d4ffa2" nowrap valign="top">Week Day:</td>
  <td bgcolor="#d4ffa2" valign="top">
    {{ $campsite->image->wday }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $campsite->image->wday }}{{ /literal }}</td>
  <td nowrap valign="top">custom</td>
</tr>
<tr>
  <td bgcolor="#d4ffa2" nowrap valign="top">Month Day:</td>
  <td bgcolor="#d4ffa2" valign="top">
    {{ $campsite->image->mday }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $campsite->image->mday }}{{ /literal }}</td>
  <td nowrap valign="top">custom</td>
</tr>
<tr>
  <td bgcolor="#d4ffa2" nowrap valign="top">Year Day:</td>
  <td bgcolor="#d4ffa2" valign="top">
    {{ $campsite->image->yday }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $campsite->image->yday }}{{ /literal }}</td>
  <td nowrap valign="top">custom</td>
</tr>
<tr>
  <td bgcolor="#d4ffa2" nowrap valign="top">Hour:</td>
  <td bgcolor="#d4ffa2" valign="top">
    {{ $campsite->image->hour }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $campsite->image->hour }}{{ /literal }}</td>
  <td nowrap valign="top">custom</td>
</tr>
<tr>
  <td bgcolor="#d4ffa2" nowrap valign="top">Minute:</td>
  <td bgcolor="#d4ffa2" valign="top">
    {{ $campsite->image->min }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $campsite->image->min }}{{ /literal }}</td>
  <td nowrap valign="top">custom</td>
</tr>
<tr>
  <td bgcolor="#d4ffa2" nowrap valign="top">Second:</td>
  <td bgcolor="#d4ffa2" valign="top">
    {{ $campsite->image->sec }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $campsite->image->sec }}{{ /literal }}</td>
  <td nowrap valign="top">custom</td>
</tr>

<tr>
  <td bgcolor="#d4ffa2" nowrap valign="top">Defined:</td>
  <td bgcolor="#d4ffa2" valign="top">
    {{ $campsite->image->defined }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $campsite->image->defined }}{{ /literal }}</td>
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
    {{ $campsite->attachment->file_name }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $campsite->attachment->file_name }}{{ /literal }}</td>
  <td nowrap valign="top">base</td>
</tr>
<tr>
  <td bgcolor="#9cf0ff" nowrap valign="top">Mime Type:</td>
  <td bgcolor="#9cf0ff" valign="top">
    {{ $campsite->attachment->mime_type }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $campsite->attachment->mime_type }}{{ /literal }}</td>
  <td nowrap valign="top">base</td>
</tr>
<tr>
  <td bgcolor="#9cf0ff" nowrap valign="top">Extension:</td>
  <td bgcolor="#9cf0ff" valign="top">
    {{ $campsite->attachment->extension }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $campsite->attachment->extension }}{{ /literal }}</td>
  <td nowrap valign="top">base</td>
</tr>
<tr>
  <td bgcolor="#d4ffa2" nowrap valign="top">Description:</td>
  <td bgcolor="#d4ffa2" valign="top">
    {{ $campsite->attachment->description }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $campsite->attachment->description }}{{ /literal }}</td>
  <td nowrap valign="top">custom</td>
</tr>
<tr>
  <td bgcolor="#9cf0ff" nowrap valign="top">Size B:</td>
  <td bgcolor="#9cf0ff" valign="top">
    {{ $campsite->attachment->size_b }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $campsite->attachment->size_b }}{{ /literal }}</td>
  <td nowrap valign="top">base</td>
</tr>
<tr>
  <td bgcolor="#d4ffa2" nowrap valign="top">Size KB:</td>
  <td bgcolor="#d4ffa2" valign="top">
    {{ $campsite->attachment->size_kb }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $campsite->attachment->size_kb }}{{ /literal }}</td>
  <td nowrap valign="top">custom</td>
</tr>
<tr>
  <td bgcolor="#d4ffa2" nowrap valign="top">Size MB:</td>
  <td bgcolor="#d4ffa2" valign="top">
    {{ $campsite->attachment->size_mb }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $campsite->attachment->size_mb }}{{ /literal }}</td>
  <td nowrap valign="top">custom</td>
</tr>
<tr>
  <td bgcolor="#d4ffa2" nowrap valign="top">Defined:</td>
  <td bgcolor="#d4ffa2" valign="top">
    {{ $campsite->attachment->defined }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $campsite->attachment->defined }}{{ /literal }}</td>
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
    {{ $campsite->topic->identifier }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $campsite->topic->identifier }}{{ /literal }}</td>
  <td nowrap valign="top">base</td>
</tr>
<tr>
  <td bgcolor="#d4ffa2" nowrap valign="top">Name:</td>
  <td bgcolor="#d4ffa2" valign="top">
    {{ $campsite->topic->name }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $campsite->topic->name }}{{ /literal }}</td>
  <td nowrap valign="top">custom</td>
</tr>
<tr>
  <td bgcolor="#d4ffa2" nowrap valign="top">Defined:</td>
  <td bgcolor="#d4ffa2" valign="top">
    {{ $campsite->topic->defined }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $campsite->topic->defined }}{{ /literal }}</td>
  <td nowrap valign="top">custom</td>
</tr>
</table>


<table cellspacing="1" cellpadding="4">
<tr>
  <td bgcolor="#dfdfdf" nowrap valign="top">
    {{ unset_topic }}
    Unset by
  </td>
  <td bgcolor="#dfdfdf">
    {{ literal }}{{ unset_topic }}{{ /literal }}
  </td>
</tr>
<tr>
  <td bgcolor="#ffcc66" nowrap valign="top">Name:</td>
  <td bgcolor="#ffcc66" valign="top">
    {{ $campsite->topic->name }}
  </td>
</tr>
<tr>
  <td bgcolor="#ffcc66" nowrap valign="top">Defined:</td>
  <td bgcolor="#ffcc66" valign="top">
    {{ $campsite->topic->defined }}
  </td>
</tr>
<tr>
  <td bgcolor="#dfdfdf" nowrap valign="top">
    {{ set_topic name="Open Source:en" }}
    Set by
  </td>
  <td bgcolor="#dfdfdf">
    {{ literal }}{{ set_topic name="Open Source:en" }}{{ /literal }}
  </td>
</tr>
<tr>
  <td bgcolor="#ffcc66" nowrap valign="top">Name:</td>
  <td bgcolor="#ffcc66" valign="top">
    {{ $campsite->topic->name }}
  </td>
</tr>
<tr>
  <td bgcolor="#ffcc66" nowrap valign="top">Defined:</td>
  <td bgcolor="#ffcc66" valign="top">
    {{ $campsite->topic->defined }}
  </td>
</tr>
</table>
<br />


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
    {{ $campsite->user->identifier }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $campsite->user->identifier }}{{ /literal }}</td>
  <td nowrap valign="top">base</td>
</tr>
<tr>
  <td bgcolor="#9cf0ff" nowrap valign="top">Name:</td>
  <td bgcolor="#9cf0ff" valign="top">
    {{ $campsite->user->name }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $campsite->user->name }}{{ /literal }}</td>
  <td nowrap valign="top">base</td>
</tr>
<tr>
  <td bgcolor="#9cf0ff" nowrap valign="top">User Name:</td>
  <td bgcolor="#9cf0ff" valign="top">
    {{ $campsite->user->uname }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $campsite->user->uname }}{{ /literal }}</td>
  <td nowrap valign="top">base</td>
</tr>
<tr>
  <td bgcolor="#9cf0ff" nowrap valign="top">EMail:</td>
  <td bgcolor="#9cf0ff" valign="top">
    {{ $campsite->user->email }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $campsite->user->email }}{{ /literal }}</td>
  <td nowrap valign="top">base</td>
</tr>
<tr>
  <td bgcolor="#9cf0ff" nowrap valign="top">City:</td>
  <td bgcolor="#9cf0ff" valign="top">
    {{ $campsite->user->city }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $campsite->user->city }}{{ /literal }}</td>
  <td nowrap valign="top">base</td>
</tr>
<tr>
  <td bgcolor="#9cf0ff" nowrap valign="top">Street Address:</td>
  <td bgcolor="#9cf0ff" valign="top">
    {{ $campsite->user->str_address }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $campsite->user->str_address }}{{ /literal }}</td>
  <td nowrap valign="top">base</td>
</tr>
<tr>
  <td bgcolor="#9cf0ff" nowrap valign="top">State:</td>
  <td bgcolor="#9cf0ff" valign="top">
    {{ $campsite->user->state }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $campsite->user->state }}{{ /literal }}</td>
  <td nowrap valign="top">base</td>
</tr>
<tr>
  <td bgcolor="#d4ffa2" nowrap valign="top">Country:</td>
  <td bgcolor="#d4ffa2" valign="top">
    {{ $campsite->user->country }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $campsite->user->country }}{{ /literal }}</td>
  <td nowrap valign="top">base</td>
</tr>
<tr>
  <td bgcolor="#9cf0ff" nowrap valign="top">Phone:</td>
  <td bgcolor="#9cf0ff" valign="top">
    {{ $campsite->user->phone }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $campsite->user->phone }}{{ /literal }}</td>
  <td nowrap valign="top">base</td>
</tr>
<tr>
  <td bgcolor="#9cf0ff" nowrap valign="top">Contact:</td>
  <td bgcolor="#9cf0ff" valign="top">
    {{ $campsite->user->contact }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $campsite->user->contact }}{{ /literal }}</td>
  <td nowrap valign="top">base</td>
</tr>
<tr>
  <td bgcolor="#9cf0ff" nowrap valign="top">Postal Code:</td>
  <td bgcolor="#9cf0ff" valign="top">
    {{ $campsite->user->postal_code }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $campsite->user->postal_code }}{{ /literal }}</td>
  <td nowrap valign="top">base</td>
</tr>
<tr>
  <td bgcolor="#9cf0ff" nowrap valign="top">Employer:</td>
  <td bgcolor="#9cf0ff" valign="top">
    {{ $campsite->user->employer }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $campsite->user->employer }}{{ /literal }}</td>
  <td nowrap valign="top">base</td>
</tr>
<tr>
  <td bgcolor="#9cf0ff" nowrap valign="top">Position:</td>
  <td bgcolor="#9cf0ff" valign="top">
    {{ $campsite->user->position }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $campsite->user->position }}{{ /literal }}</td>
  <td nowrap valign="top">base</td>
</tr>
<tr>
  <td bgcolor="#9cf0ff" nowrap valign="top">Interests:</td>
  <td bgcolor="#9cf0ff" valign="top">
    {{ $campsite->user->interests }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $campsite->user->interests }}{{ /literal }}</td>
  <td nowrap valign="top">base</td>
</tr>
<tr>
  <td bgcolor="#9cf0ff" nowrap valign="top">How:</td>
  <td bgcolor="#9cf0ff" valign="top">
    {{ $campsite->user->how }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $campsite->user->how }}{{ /literal }}</td>
  <td nowrap valign="top">base</td>
</tr>
<tr>
  <td bgcolor="#9cf0ff" nowrap valign="top">Languages:</td>
  <td bgcolor="#9cf0ff" valign="top">
    {{ $campsite->user->languages }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $campsite->user->languages }}{{ /literal }}</td>
  <td nowrap valign="top">base</td>
</tr>
<tr>
  <td bgcolor="#9cf0ff" nowrap valign="top">Improvements:</td>
  <td bgcolor="#9cf0ff" valign="top">
    {{ $campsite->user->improvements }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $campsite->user->improvements }}{{ /literal }}</td>
  <td nowrap valign="top">base</td>
</tr>
<tr>
  <td bgcolor="#9cf0ff" nowrap valign="top">Field 1:</td>
  <td bgcolor="#9cf0ff" valign="top">
    {{ $campsite->user->field1 }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $campsite->user->field1 }}{{ /literal }}</td>
  <td nowrap valign="top">base</td>
</tr>
<tr>
  <td bgcolor="#9cf0ff" nowrap valign="top">Field 2:</td>
  <td bgcolor="#9cf0ff" valign="top">
    {{ $campsite->user->field2 }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $campsite->user->field2 }}{{ /literal }}</td>
  <td nowrap valign="top">base</td>
</tr>
<tr>
  <td bgcolor="#9cf0ff" nowrap valign="top">Field 3:</td>
  <td bgcolor="#9cf0ff" valign="top">
    {{ $campsite->user->field3 }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $campsite->user->field3 }}{{ /literal }}</td>
  <td nowrap valign="top">base</td>
</tr>
<tr>
  <td bgcolor="#9cf0ff" nowrap valign="top">Field 4:</td>
  <td bgcolor="#9cf0ff" valign="top">
    {{ $campsite->user->field4 }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $campsite->user->field4 }}{{ /literal }}</td>
  <td nowrap valign="top">base</td>
</tr>
<tr>
  <td bgcolor="#9cf0ff" nowrap valign="top">Field 5:</td>
  <td bgcolor="#9cf0ff" valign="top">
    {{ $campsite->user->field5 }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $campsite->user->field5 }}{{ /literal }}</td>
  <td nowrap valign="top">base</td>
</tr>
<tr>
  <td bgcolor="#9cf0ff" nowrap valign="top">Text 1:</td>
  <td bgcolor="#9cf0ff" valign="top">
    {{ $campsite->user->text1 }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $campsite->user->text1 }}{{ /literal }}</td>
  <td nowrap valign="top">base</td>
</tr>
<tr>
  <td bgcolor="#9cf0ff" nowrap valign="top">Text 2:</td>
  <td bgcolor="#9cf0ff" valign="top">
    {{ $campsite->user->text2 }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $campsite->user->text2 }}{{ /literal }}</td>
  <td nowrap valign="top">base</td>
</tr>
<tr>
  <td bgcolor="#9cf0ff" nowrap valign="top">Text 3:</td>
  <td bgcolor="#9cf0ff" valign="top">
    {{ $campsite->user->text3 }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $campsite->user->text3 }}{{ /literal }}</td>
  <td nowrap valign="top">base</td>
</tr>
<tr>
  <td bgcolor="#9cf0ff" nowrap valign="top">Title:</td>
  <td bgcolor="#9cf0ff" valign="top">
    {{ $campsite->user->title }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $campsite->user->title }}{{ /literal }}</td>
  <td nowrap valign="top">base</td>
</tr>
<tr>
  <td bgcolor="#9cf0ff" nowrap valign="top">Age:</td>
  <td bgcolor="#9cf0ff" valign="top">
    {{ $campsite->user->age }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $campsite->user->age }}{{ /literal }}</td>
  <td nowrap valign="top">base</td>
</tr>
<tr>
  <td bgcolor="#d4ffa2" nowrap valign="top">Defined:</td>
  <td bgcolor="#d4ffa2" valign="top">
    {{ $campsite->user->defined }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $campsite->user->defined }}{{ /literal }}</td>
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
    {{ $campsite->audioclip->title }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $campsite->audioclip->title }}{{ /literal }}</td>
  <td nowrap valign="top">base</td>
</tr>
<tr>
  <td bgcolor="#9cf0ff" nowrap valign="top">Creator:</td>
  <td bgcolor="#9cf0ff" valign="top">
    {{ $campsite->audioclip->creator }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $campsite->audioclip->creator }}{{ /literal }}</td>
  <td nowrap valign="top">base</td>
</tr>
<tr>
  <td bgcolor="#9cf0ff" nowrap valign="top">Genre:</td>
  <td bgcolor="#9cf0ff" valign="top">
    {{ $campsite->audioclip->genre }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $campsite->audioclip->genre }}{{ /literal }}</td>
  <td nowrap valign="top">base</td>
</tr>
<tr>
  <td bgcolor="#9cf0ff" nowrap valign="top">Length:</td>
  <td bgcolor="#9cf0ff" valign="top">
    {{ $campsite->audioclip->length }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $campsite->audioclip->length }}{{ /literal }}</td>
  <td nowrap valign="top">base</td>
</tr>
<tr>
  <td bgcolor="#9cf0ff" nowrap valign="top">Year:</td>
  <td bgcolor="#9cf0ff" valign="top">
    {{ $campsite->audioclip->year }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $campsite->audioclip->year }}{{ /literal }}</td>
  <td nowrap valign="top">base</td>
</tr>
<tr>
  <td bgcolor="#9cf0ff" nowrap valign="top">Bitrate:</td>
  <td bgcolor="#9cf0ff" valign="top">
    {{ $campsite->audioclip->bitrate }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $campsite->audioclip->bitrate }}{{ /literal }}</td>
  <td nowrap valign="top">base</td>
</tr>
<tr>
  <td bgcolor="#9cf0ff" nowrap valign="top">Samplerate:</td>
  <td bgcolor="#9cf0ff" valign="top">
    {{ $campsite->audioclip->samplerate }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $campsite->audioclip->samplerate }}{{ /literal }}</td>
  <td nowrap valign="top">base</td>
</tr>
<tr>
  <td bgcolor="#9cf0ff" nowrap valign="top">Album:</td>
  <td bgcolor="#9cf0ff" valign="top">
    {{ $campsite->audioclip->album }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $campsite->audioclip->album }}{{ /literal }}</td>
  <td nowrap valign="top">base</td>
</tr>
<tr>
  <td bgcolor="#9cf0ff" nowrap valign="top">Description:</td>
  <td bgcolor="#9cf0ff" valign="top">
    {{ $campsite->audioclip->description }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $campsite->audioclip->description }}{{ /literal }}</td>
  <td nowrap valign="top">base</td>
</tr>
<tr>
  <td bgcolor="#9cf0ff" nowrap valign="top">Format:</td>
  <td bgcolor="#9cf0ff" valign="top">
    {{ $campsite->audioclip->format }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $campsite->audioclip->format }}{{ /literal }}</td>
  <td nowrap valign="top">base</td>
</tr>
<tr>
  <td bgcolor="#9cf0ff" nowrap valign="top">Label:</td>
  <td bgcolor="#9cf0ff" valign="top">
    {{ $campsite->audioclip->label }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $campsite->audioclip->label }}{{ /literal }}</td>
  <td nowrap valign="top">base</td>
</tr>
<tr>
  <td bgcolor="#9cf0ff" nowrap valign="top">Composer:</td>
  <td bgcolor="#9cf0ff" valign="top">
    {{ $campsite->audioclip->composer }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $campsite->audioclip->composer }}{{ /literal }}</td>
  <td nowrap valign="top">base</td>
</tr>
<tr>
  <td bgcolor="#9cf0ff" nowrap valign="top">Channels:</td>
  <td bgcolor="#9cf0ff" valign="top">
    {{ $campsite->audioclip->channels }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $campsite->audioclip->channels }}{{ /literal }}</td>
  <td nowrap valign="top">base</td>
</tr>
<tr>
  <td bgcolor="#9cf0ff" nowrap valign="top">Rating:</td>
  <td bgcolor="#9cf0ff" valign="top">
    {{ $campsite->audioclip->rating }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $campsite->audioclip->rating }}{{ /literal }}</td>
  <td nowrap valign="top">base</td>
</tr>
<tr>
  <td bgcolor="#9cf0ff" nowrap valign="top">Track No:</td>
  <td bgcolor="#9cf0ff" valign="top">
    {{ $campsite->audioclip->track_no }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $campsite->audioclip->track_no }}{{ /literal }}</td>
  <td nowrap valign="top">base</td>
</tr>
<tr>
  <td bgcolor="#9cf0ff" nowrap valign="top">Disk No:</td>
  <td bgcolor="#9cf0ff" valign="top">
    {{ $campsite->audioclip->disk_no }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $campsite->audioclip->disk_no }}{{ /literal }}</td>
  <td nowrap valign="top">base</td>
</tr>
<tr>
  <td bgcolor="#9cf0ff" nowrap valign="top">Lyrics:</td>
  <td bgcolor="#9cf0ff" valign="top">
    {{ $campsite->audioclip->lyrics }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $campsite->audioclip->lyrics }}{{ /literal }}</td>
  <td nowrap valign="top">base</td>
</tr>
<tr>
  <td bgcolor="#9cf0ff" nowrap valign="top">Copyright:</td>
  <td bgcolor="#9cf0ff" valign="top">
    {{ $campsite->audioclip->copyright }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $campsite->audioclip->copyright }}{{ /literal }}</td>
  <td nowrap valign="top">base</td>
</tr>
<tr>
  <td bgcolor="#d4ffa2" nowrap valign="top">Defined:</td>
  <td bgcolor="#d4ffa2" valign="top">
    {{ $campsite->audioclip->defined }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $campsite->audioclip->defined }}{{ /literal }}</td>
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
    {{ $campsite->comment->identifier }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $campsite->comment->identifier }}{{ /literal }}</td>
  <td nowrap valign="top">base</td>
</tr>
<tr>
  <td bgcolor="#9cf0ff" nowrap valign="top">Reader EMail:</td>
  <td bgcolor="#9cf0ff" valign="top">
    {{ $campsite->comment->reader_email }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $campsite->comment->reader_email }}{{ /literal }}</td>
  <td nowrap valign="top">base</td>
</tr>
<tr>
  <td bgcolor="#9cf0ff" nowrap valign="top">Submit Date:</td>
  <td bgcolor="#9cf0ff" valign="top">
    {{ $campsite->comment->submit_date }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $campsite->comment->submit_date }}{{ /literal }}</td>
  <td nowrap valign="top">base</td>
</tr>
<tr>
  <td bgcolor="#9cf0ff" nowrap valign="top">Subject:</td>
  <td bgcolor="#9cf0ff" valign="top">
    {{ $campsite->comment->subject }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $campsite->comment->subject }}{{ /literal }}</td>
  <td nowrap valign="top">base</td>
</tr>
<tr>
  <td bgcolor="#9cf0ff" nowrap valign="top">Content:</td>
  <td bgcolor="#9cf0ff" valign="top">
    {{ $campsite->comment->content }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $campsite->comment->content }}{{ /literal }}</td>
  <td nowrap valign="top">base</td>
</tr>
<tr>
  <td bgcolor="#9cf0ff" nowrap valign="top">Level:</td>
  <td bgcolor="#9cf0ff" valign="top">
    {{ $campsite->comment->level }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $campsite->comment->level }}{{ /literal }}</td>
  <td nowrap valign="top">base</td>
</tr>
<tr>
  <td bgcolor="#d4ffa2" nowrap valign="top">Defined:</td>
  <td bgcolor="#d4ffa2" valign="top">
    {{ $campsite->comment->defined }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $campsite->comment->defined }}{{ /literal }}</td>
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
    {{ $campsite->template->name }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $campsite->template->name }}{{ /literal }}</td>
  <td nowrap valign="top">base</td>
</tr>
<tr>
  <td bgcolor="#d4ffa2" nowrap valign="top">Type:</td>
  <td bgcolor="#d4ffa2" valign="top">
    {{ $campsite->template->type }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $campsite->template->type }}{{ /literal }}</td>
  <td nowrap valign="top">custom</td>
</tr>
<tr>
  <td bgcolor="#d4ffa2" nowrap valign="top">Defined:</td>
  <td bgcolor="#d4ffa2" valign="top">
    {{ $campsite->template->defined }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $campsite->template->defined }}{{ /literal }}</td>
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
    {{ $campsite->subscription->currency }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $campsite->subscription->currency }}{{ /literal }}</td>
  <td nowrap valign="top">base</td>
</tr>
<tr>
  <td bgcolor="#d4ffa2" nowrap valign="top">Type:</td>
  <td bgcolor="#d4ffa2" valign="top">
    {{ $campsite->subscription->type }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $campsite->subscription->type }}{{ /literal }}</td>
  <td nowrap valign="top">custom</td>
</tr>
<tr>
  <td bgcolor="#d4ffa2" nowrap valign="top">Active:</td>
  <td bgcolor="#d4ffa2" valign="top">
    {{ $campsite->subscription->active }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $campsite->subscription->active }}{{ /literal }}</td>
  <td nowrap valign="top">custom</td>
</tr>
<tr>
  <td bgcolor="#d4ffa2" nowrap valign="top">Defined:</td>
  <td bgcolor="#d4ffa2" valign="top">
    {{ $campsite->subscription->defined }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $campsite->subscription->defined }}{{ /literal }}</td>
  <td nowrap valign="top">custom</td>
</tr>
</table>


</body>
</html>
