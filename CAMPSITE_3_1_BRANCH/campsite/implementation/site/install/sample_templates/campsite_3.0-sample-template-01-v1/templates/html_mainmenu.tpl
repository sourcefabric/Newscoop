<table class="search" cellspacing="0" cellpadding="0">
<tr>
  <td>
    <div id="topicsmenu">
    <ul>
    {{ list_sections name="sections" constraints="number smaller 200" }}
      <li><a
	href="{{ uri options="section" }}">{{ $campsite->section->name }}</a></li>
    {{ /list_sections }}
    </ul>
    </div>
  </td>
</tr>
</table>
