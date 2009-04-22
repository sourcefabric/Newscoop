<table width="466"  border="0" cellspacing="5" cellpadding="0">
      <tr>
<td>
{{ list_articles length="1" constraints="type is Service" order="bynumber desc" }}
<p class="main-naslov">{{ $campsite->article->name }}</p>
<p class="tekst">{{ $campsite->article->full_text }}</p>
{{ /list_articles }}
</td>
      </tr>
    </table>

