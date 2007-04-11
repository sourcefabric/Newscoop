<table cellspacing="4" cellpadding="0" border="0">
{{ article_comment_form template="search.tpl" submit_button="Send" preview_button="Back" }}
<tr>
  <td align="right">User:</td>
  <td>
    {{ camp_edit object="user" attribute="name" }}
  </td>
</tr>
<tr>
  <td align="right">Subject:</td>
  <td>
    {{ camp_edit object="articlecomment" attribute="subject" }}
  </td>
</tr>
<tr>
  <td align="right" valign="top">Content:</td>
  <td>
    {{ camp_edit object="articlecomment" attribute="Content" }}
  </td>
</tr>
<tr>
  <td colspan="2" align="center">
    {{ /article_comment_form }}
  </td>
</tr>
</table>
