<table class="service" cellspacing="0" cellpadding="0">
<tr>
  <td>
    <table width="100%" cellspacing="0" cellpadding="0" border="0">
    <tr>
      <td>
      {{ list_articles length="1" order="bynumber desc" name="service" }}
	<p class="article_name">{{ $campsite->article->name }}</p>
        <p class="article_fulltext">{{ $campsite->article->full_text }}</p>
      {{ /list_articles }}
      </td>
    </tr>
    </table>
  </td>
</tr>
</table>