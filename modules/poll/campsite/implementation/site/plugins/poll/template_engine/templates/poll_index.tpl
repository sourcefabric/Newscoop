<html>
<head>
  <title>Poll Smarty</title>
</head>
<body>


{{**** Poll ****}}
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

     
{{ list_polls name="NAME" length="3" columns="2" language="default" order="DESC" }}

{{ /list_polls }}

</body>
</html>
