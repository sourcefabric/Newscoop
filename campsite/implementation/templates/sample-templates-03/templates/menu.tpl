<table width="100%" cellpadding="0" cellspacing="0" border="0" bgcolor="#DDDDDD">
  <tr>
    <td bgcolor="#7F7F7F"><p class="navig-belo">Sections</p></td>
  </tr>
	{{ local }}
        {{ set_publication name="dynamic" }}
        {{ set_issue current }}
        {{ unset_section }}
        {{ unset_article }}
        {{ list_sections constraints="number smaller 99" }}
  <tr>
    <td class="navigacija"><p class="navig-crno"><a class="navig" href="{{ uri options="reset_article_list" }}">{{ $campsite->section->name }}</a></p></td>
  </tr>
        {{ /list_sections }}
		{{ /local }}
</table>