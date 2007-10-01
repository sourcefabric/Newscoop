<html>
<head>
  <title>Poll Smarty</title>
</head>
<body>

{{*
<h3>issues list</h3>

{{ list_issues }}
<li>Issue: <b>{{ $campsite->current_issues_list->current->name }}</b>


  <ul>
  {{ list_sections }}
    <li>
      Section: <b>{{ $campsite->current_sections_list->current->name }}</b>
      
      {{ list_articles }}
 
          <li>
            Article: <b>{{ $campsite->current_articles_list->currenty}}</b>
          </li>
   
      {{ /list_articles }}
    </li>
    {{ /list_sections }}
  </ul>

  
</li>

{{ /list_issues }}
*}}

<hr>
<h4> Start-Parameters:</h4>
Language: {{ $campsite->language->number }}<br>
Publication: {{ $campsite->publication->identifier }}<br>
Issue: {{ $campsite->issue->number }}<br>
Section: {{ $campsite->section->number }}<br>
Article: {{ $campsite->article->number }}<br>

<hr>
<h4>Poll-List</h4>     
{{ list_polls name="polls_list" length="5" item=$smarty.get.poll_item language="default" order="bybegin DESCS" constraints="number greater 1" }}
   <li>poll: <b>{{ $campsite->current_polls_list->current->name }}</b>/<b>{{ $campsite->current_list->current->name }}</b>,
   list index: <b>{{ $campsite->current_polls_list->getIndex() }}</b>/<b>{{ $campsite->current_list->getIndex() }}</b>,
   column: <b>{{ $campsite->current_polls_list->getColumn() }}</b>/<b>{{ $campsite->current_list->getColumn() }}</b>
   (current polls list/current list)
   <a href="?poll_nr={{ $campsite->current_polls_list->current->number }}&amp;poll_language_id={{ $campsite->current_polls_list->current->language_id }}">display</a>
{{ /list_polls }}
<br>
total count: {{ $campsite->current_polls_list->count }}

<hr>

{{ if $campsite->poll->in_time }}
    {{$campsite->poll->register_voting }}

    <h3>Poll Form</h3>
    
    <form name="poll_{{ $campsite->poll->identifier }}">
        {{ $campsite->poll->form_hidden }}
        
        Title: {{ $campsite->poll->title }}<br>
        Question: {{ $campsite->poll->question }}<br>
        <br>
        
        {{ if $campsite->poll->votable }}
        {{ list_poll_answers }}
            {{ $campsite->current_pollanswers_list->current->form_radio }}
            
            {{* 
            like:
            <input type="radio" name="{{ $campsite->current_pollanswers_list->current->identifier }}" >
            *}}
            {{ $campsite->current_pollanswers_list->current->answer }}<br>
        {{ /list_poll_answers }}
    
        <input type="submit">
        <p>
        {{ /if }}
        
        {{ list_poll_answers }}
            {{ $campsite->current_pollanswers_list->current->nr_answer }}:
            {{ strip }}
            <img src="/css/mainbarlinks.png" width="1" height="10" />
            <img src="/css/mainbar.png" width="{{ $campsite->current_pollanswers_list->current->percentage }}" height="10px"/>
            <img src="/css/mainbarrechts.png" width="1" height="10" />
            {{ /strip }}
            ({{ $campsite->current_pollanswers_list->current->nr_of_votes }}/{{ $campsite->poll->nr_of_votes }} Votes, {{ $campsite->current_pollanswers_list->current->percentage }}%)
            <br>
        {{ /list_poll_answers }}
    </form>
    <hr>
    
    <table cellspacing="1" cellpadding="4">
    <tr>
      <td bgcolor="#6a6a6a"><font color="#ffffff">Poll Data</font></td>
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
        {{ $campsite->poll->date_begin|date_format }}
      </td>
      <td nowrap valign="top">{{ literal }}{{ $campsite->poll->date_begin }}{{ /literal }}</td>
      <td nowrap valign="top">base</td>
    </tr>
    <tr>
      <td bgcolor="#9cf0ff" nowrap valign="top">End:</td>
      <td bgcolor="#9cf0ff" valign="top">
        {{ $campsite->poll->date_end|date_format }}
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
    
{{ /if }}
    
</body>
</html>
