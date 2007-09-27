{{ strip }}
<table id="leftbar" cellspacing="0" cellpadding="0">
<th>{{ tr }}Sections{{ /tr }}</th>
<tr>
  <td>
  {{** list_sections name="sections" length="0" columns="1" **}}
    <ul id="lists">
      <li><a
        href="">{{ $section->name }}</a></li>
    </ul>
  {{** /list_sections **}}
  </td>
</tr>
<tr>
  <td>
    <a href=""><img src="campsite_logo.png" /></a>
    <br />
    <a href=""><img src="campcaster_logo.png" /></a>
  </td>
</tr>
</table>
{{ /strip }}