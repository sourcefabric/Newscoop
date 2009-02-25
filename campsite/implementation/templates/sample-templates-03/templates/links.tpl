<table width="100%" cellpadding="0" cellspacing="0" border="0">
  <tr>
    <td><p class="navig-crno" style="color: #CC0000; margin-top: 8px; margin-bottom: 8px">Campsite LINKS</p></td>
  </tr>
</table>
<table style="border: 1px solid #666666" width="100%" cellpadding="0" cellspacing="0" border="0">
{{ local }}
{{ set_publication name="dynamic" }}
{{ unset_issue }}
{{ set_section number="230" }}
{{ list_articles constraints="type is link" order="bynumber desc" }}
			  <tr>
			    <td style="border-bottom: 1px solid #666666"><p class="link"><a class="crno-podvuceno" href="http://{{ $campsite->article->url }}">{{ $campsite->article->name }}</a></p></td>
			  </tr>					  			  	
{{ /list_articles }}
{{ /local }}  
			</table>
