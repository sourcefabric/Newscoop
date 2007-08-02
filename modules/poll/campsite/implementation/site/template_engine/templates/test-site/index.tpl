{{ include file="html_header.tpl" }}
{{ strip }}
<table id="maintable" cellspacing="0" cellpadding="0">
<tr>
  <td>
    {{ include file="html_leftbar.tpl" }}
  </td>
  <td>
    {{** main content area **}}
    <table id="content" cellspacing="0" cellpadding="0">
    <tr>
    {{ if $campsite->article->defined }}
      <td>
        {{ include file="article.tpl" }}
      </td>
    {{ /else }}
      <td>
        {{ include file="highlights.tpl" }}
      </td>
      <td>
        {{ include file="newsflashes.tpl" }}
      </td>
    {{ /if }}
    </tr>
    </table>
    {{** end main content area **}}
  </td>
  <td>
    {{ include file="html_rightbar.tpl" }}
  </td>
</tr>
</table>
{{ /strip }}
{{ include file="html_footer.tpl" }}