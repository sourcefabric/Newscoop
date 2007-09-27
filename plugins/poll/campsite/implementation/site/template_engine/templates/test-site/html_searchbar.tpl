{{ strip }}
<table id="search" cellspacing="0" cellpadding="0">
<tr>
  <td>
    {{ search_form submit_button="search" }}
      {{ camp_edit object="search" attribute="keywords" }}
    {{ /search_form }}
  </td>
</tr>
</table>
{{ /strip }}