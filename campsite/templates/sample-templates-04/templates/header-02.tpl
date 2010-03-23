<table cellspacing="0" cellpadding="0" border="0">
        <tr>
        {{ local }}
        {{ set_current_issue }}
        {{ list_sections constraints="number smaller 51" }}
          <td class="index" align="center"><p class="main-index"><img src="/templates/img/meni.gif" width="6" height="6"><a class="main-index" href="{{ uri options="reset_article_list" }}">{{ $campsite->section->name }}</a></p></td>
          <td width="1"><img src="/templates/img/bgrmenilinija.gif" width="1" height="14"></td>
		{{ /list_sections }}
		{{ /local }}
<td>
<table>
<tr>
<td style="padding-left: 250px; margin: 5px 0 0 0;" valign="middle">
<a href="http://{{ $campsite->publication->site }}{{ uripath options="template rss.tpl" }}?{{ urlparameters options="template rss.tpl" }}"><img src="/templates/img/rss.gif" border="0"></a>
</td>
</tr>
</table>
</td>
        </tr>
      </table>