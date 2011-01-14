<table width="100%"  border="0" cellspacing="0" cellpadding="0">
  <tr bgcolor="#0a254e">
    <td colspan="3" height="2"></td>
  </tr>
  <tr>
    <td width="1" bgcolor="#0a254e"></td>
    <td bgcolor="#d4d4d7">
<table border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td height="2"></td>
</tr>
<tr>
<td>
<p class="footer">
{{ list_sections constraints="number smaller 199" }}
{{ if $campsite->current_list->index == 1 }}
<a href="{{ uri }}" class="footer">{{ $campsite->section->name }}</a>
{{ else }}
 - <a href="{{ uri }}" class="footer">{{ $campsite->section->name }}</a>
{{ /if }}
{{ /list_sections }}
</p>
</td>
</tr>
<tr>
<td>
<p class="footer">Copyright © <b>Package Tempaltes (No #2) 2005</b>, {{ local }}
{{ unset_issue }}
{{ list_sections constraints="number is 210" }}
<a href="{{ uri }}" class="footer">Contact</a></p>
{{ /list_sections }}
{{ /local }}
</td>
</tr>
<tr>
<td height="2"></td>
</tr>
</table>

</td>
    <td width="1" bgcolor="#0a254e"></td>
  </tr>
  <tr bgcolor="#0a254e">
    <td colspan="3" height="2"></td>
  </tr>
</table>
