<table border="0" cellpadding="0" cellspacing="0" width="100%">
<tr> 
   <td width="330" bgcolor="#3878af" valign="top" align="center"><p class="datum">My Town, {{ $smarty.now|camp_date_format:"%M %d, %Y" }}</p></td>
   <td width="417" valign="top">
     <table border="0" cellpadding="0" cellspacing="0" width="100%" bgcolor="#3878af">
     <tr>
      {{ local }}
      {{ set_current_issue }}
      {{ list_sections constraints="number smaller 51" }}
      <td width="83"><img border="0" src="/templates/img/04bgmeni.gif" align="left">
      <p class="main-index" align="center"><a class="main-index" href="{{ uri options="section reset_article_list" }}">{{ $campsite->section->name }}</a></p>
      </td>
      {{ /list_sections }}
      {{ /local }}
      </tr>
      </table>
  </td>
</tr>
</table>