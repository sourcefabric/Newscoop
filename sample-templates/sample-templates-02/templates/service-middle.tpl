<table width="100%" cellspacing="0" cellpadding="0" border="0" align="center">
<tr>
<td valign="top">
{{ list_articles length="1" constraints="type is Service" order="bynumber desc" }}
<div style="float: right;margin-top:10px;">
<span class="plus">
<a target="_blank" href="{{ uri options="template print.tpl" }}">[+] Print version</a>
</span>
</div>
<br>
<p class="naslov">{{ $campsite->article->name }}</p>
<p class="tekst">{{ $campsite->article->full_text }}</p>
{{ /list_articles }}
<br clear="all">
<hr size="1" noshade="">
</td>
</tr>
</table>