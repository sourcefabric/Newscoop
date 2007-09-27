<html>
<head>
  <title>Poll Smarty</title>
</head>
<body>


<h3>issues list</h3>

{{ list_issues }}
<li>Issue: <b>{{ $campsite->current_issues_list->current->name }}</b>


  {{*
  <ul>Sections:
  {{ list_sections }}
    <li>
      Section: <b>{{ $campsite->current_sections_list->current->name }}</b>
      
      {{ list_articles }}
        {{ if !$campsite->current_articles_list->end }}
          <li>
            Article: <b>{{ $campsite->current_articles_list->current->name}}</b>
          </li>
        {{ /if }}
      {{ /list_articles }}
    </li>
    {{ /list_sections }}
  </ul>
  *}}
  
</li>

{{ /list_issues }}



Language: {{ $campsite->language->number }}<br>
Publication: {{ $campsite->publication->identifier }}<br>
Issue: {{ $campsite->issue->number }}<br>
Section: {{ $campsite->section->number }}<br>
Article: {{ $campsite->article->number }}<br>
     
{{ list_polls name="polls_list" length="5" item=$smarty.get.poll_item language="default" order="DESC" debug=1}}
   <li>poll: <b>{{ $campsite->current_polls_list->current->name }}</b>/<b>{{ $campsite->current_list->current->name }}</b>,
   list index: <b>{{ $campsite->current_polls_list->getIndex() }}</b>/<b>{{ $campsite->current_list->getIndex() }}</b>,
   column: <b>{{ $campsite->current_polls_list->getColumn() }}</b>/<b>{{ $campsite->current_list->getColumn() }}</b>
   (current polls list/current list)
   <a href="?poll_nr={{ $campsite->current_polls_list->current->number }}&amp;poll_language_id={{ $campsite->current_polls_list->current->language_id }}">display</a>
{{ /list_polls }}
<br>
total count: {{ $campsite->current_polls_list->count }}

<p>

{{ if $campsite->poll->defined() }}
    <table cellspacing="1" cellpadding="4">
    <tr>
      <td bgcolor="#6a6a6a"><font color="#ffffff">Poll</font></td>
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
        {{ $campsite->poll->title }}
      </td>
      <td nowrap valign="top">{{ literal }}{{ $campsite->poll->title }}{{ /literal }}</td>
      <td nowrap valign="top">base</td>
    </tr>
    <tr>
      <td bgcolor="#9cf0ff" nowrap valign="top">Number:</td>
      <td bgcolor="#9cf0ff" valign="top">
        {{ $campsite->poll->number }}
      </td>
      <td nowrap valign="top">{{ literal }}{{ $campsite->poll->poll_nr }}{{ /literal }}</td>
      <td nowrap valign="top">base</td>
    </tr>
    <tr>
      <td bgcolor="#9cf0ff" nowrap valign="top">LanguageId</td>
      <td bgcolor="#9cf0ff" valign="top">
        {{ $campsite->poll->language_id }}
      </td>
      <td nowrap valign="top">{{ literal }}{{ $campsite->poll->language_id }}{{ /literal }}</td>
      <td nowrap valign="top">base</td>
    </tr>
    <tr>
      <td bgcolor="#9cf0ff" nowrap valign="top">Question:</td>
      <td bgcolor="#9cf0ff" valign="top">
        {{ $campsite->poll->question }}
      </td>
      <td nowrap valign="top">{{ literal }}{{ $campsite->poll->question }}{{ /literal }}</td>
      <td nowrap valign="top">base</td>
    </tr>
    <tr>
      <td bgcolor="#9cf0ff" nowrap valign="top">Begin:</td>
      <td bgcolor="#9cf0ff" valign="top">
        {{ $campsite->poll->date_begin }}
      </td>
      <td nowrap valign="top">{{ literal }}{{ $campsite->poll->date_begin }}{{ /literal }}</td>
      <td nowrap valign="top">base</td>
    </tr>
    <tr>
      <td bgcolor="#9cf0ff" nowrap valign="top">End:</td>
      <td bgcolor="#9cf0ff" valign="top">
        {{ $campsite->poll->date_end }}
      </td>
      <td nowrap valign="top">{{ literal }}{{ $campsite->poll->date_end }}{{ /literal }}</td>
      <td nowrap valign="top">base</td>
    </tr>
    <tr>
      <td bgcolor="#9cf0ff" nowrap valign="top">Number of Answers:</td>
      <td bgcolor="#9cf0ff" valign="top">
        {{ $campsite->poll->nr_of_answers }}
      </td>
      <td nowrap valign="top">{{ literal }}{{ $campsite->poll->nr_of_answers }}{{ /literal }}</td>
      <td nowrap valign="top">base</td>
    </tr>
    <tr>
      <td bgcolor="#9cf0ff" nowrap valign="top">Votes:</td>
      <td bgcolor="#9cf0ff" valign="top">
        {{ $campsite->poll->nr_of_votes }}
      </td>
      <td nowrap valign="top">{{ literal }}{{ $campsite->poll->nr_of_votes }}{{ /literal }}</td>
      <td nowrap valign="top">base</td>
    </tr>
    <tr>
      <td bgcolor="#9cf0ff" nowrap valign="top">Votes overall Languges:</td>
      <td bgcolor="#9cf0ff" valign="top">
        {{ $campsite->poll->nr_of_votes_overall }}
      </td>
      <td nowrap valign="top">{{ literal }}{{ $campsite->poll->nr_of_votes_overall }}{{ /literal }}</td>
      <td nowrap valign="top">base</td>
    </tr>
    <tr>
      <td bgcolor="#9cf0ff" nowrap valign="top">Percentage overall Languages:</td>
      <td bgcolor="#9cf0ff" valign="top">
        {{ $campsite->poll->percentage_of_votes_overall }}
      </td>
      <td nowrap valign="top">{{ literal }}{{ $campsite->poll->percentage_of_votes_overall }}{{ /literal }}</td>
      <td nowrap valign="top">base</td>
    </tr>
    <tr>
      <td bgcolor="#9cf0ff" nowrap valign="top">Show after Expiration:</td>
      <td bgcolor="#9cf0ff" valign="top">
        {{ $campsite->poll->is_show_after_expiration }}
      </td>
      <td nowrap valign="top">{{ literal }}{{ $campsite->poll->is_show_after_expiration }}{{ /literal }}</td>
      <td nowrap valign="top">base</td>
    </tr>
    <tr>
      <td bgcolor="#9cf0ff" nowrap valign="top">Show after Expiration:</td>
      <td bgcolor="#9cf0ff" valign="top">
        {{ $campsite->poll->is_show_after_expiration }}
      </td>
      <td nowrap valign="top">{{ literal }}{{ $campsite->poll->is_show_after_expiration }}{{ /literal }}</td>
      <td nowrap valign="top">base</td>
    </tr>
    <tr>
      <td bgcolor="#d4ffa2" nowrap valign="top">Defined:</td>
      <td bgcolor="#d4ffa2" valign="top">
        {{ $campsite->poll->defined }}
      </td>
      <td nowrap valign="top">{{ literal }}{{ $campsite->poll->defined }}{{ /literal }}</td>
      <td nowrap valign="top">custom</td>
    </tr>
    </table>
    
    
    <h2>Poll Answers</h2>
    {{ list_poll_answers }}
        {{ $campsite->current_pollanswers_list->current->answer }}
    {{ /list_poll_answers }}
    
{{ /if }}
    
</body>
</html>
