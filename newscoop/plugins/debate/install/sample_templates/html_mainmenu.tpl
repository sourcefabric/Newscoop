<table class="search" cellspacing="0" cellpadding="0">
<tr>
  <td>
    <div id="topicsmenu">
    <ul>
    {{ list_sections name="sections" constraints="number smaller 200" }}
      <li><a
	href="{{ uri options="section" }}">{{ $gimme->section->name }}</a></li>
    {{ /list_sections }}
    
    {{ local }}
    <li><a href="{{ uri options="template section-polls.tpl" }}">Polls</a></li>
    {{ /local }}
    </ul>
    </div>
  </td>
</tr>
</table>
