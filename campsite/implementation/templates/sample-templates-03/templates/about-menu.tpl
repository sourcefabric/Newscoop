<table width="100%" cellpadding="0" cellspacing="0" border="0">
				    <tr>
			          <td colspan="2" bgcolor="#FFFFFF"><img src="/templates/img/servis-top.gif"></td>
			        </tr>
	{{ local }}
        {{ set_publication name="dynamic" }}
        {{ unset_issue }}
        {{ unset_article }}
        {{ list_sections constraints="number greater 99" }}
        {{ if $campsite->section->number == 230 }}
        {{ else }}
			        <tr>
					  <td width="1" bgcolor="#DDDDDD"></td>
			          <td class="servis" 
					  onmouseover="this.style.borderColor='#CC0000'" style="CURSOR: hand" 
                      onclick="document.location.href='#'" 
                      onmouseout="this.style.borderColor='#DDDDDD'"><p class="servis">+ <a class="servis" href="{{ uri options="reset_article_list" }}">{{ $campsite->section->name }}</a></p></td>
			        </tr>
					<tr>
			          <td colspan="2" height="1" bgcolor="#DDDDDD"></td>
			        </tr>
			        <tr>
			          <td  colspan="2" height="1" bgcolor="#FFFFFF"></td>
			        </tr>
                {{ /if }}
		{{ /list_sections }}
		{{ /local }}
			      </table>
