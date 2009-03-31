{{ local }}
<table width="100%" cellspacing="0" cellpadding="0" border="0" bordercolor="#005496" align="left">				
              <tr> 
                <td bgcolor="#005496" height="21" ><p class="index-naslovi"><a href="/" class="index-naslovi">Home</a></p></td>
              </tr>
			  <tr>
			    <td height="3"></td>
			  </tr>			  
              <tr> 
                <td bgcolor="#A6A6A6" height="16"><p class="index-naslovi">sections</p></td>
              </tr>
              {{ set_current_issue }}
              {{ list_sections constraints="number smaller 200" }}
              <tr> 
      	        <td class="menu"><p class="index"><a class="index" href="{{ uri }}">{{ $campsite->section->name }}</a></p></td>
	          </tr>
	          <tr>
	  	        <td height="3" background="/templates/img/crtice.gif"></td>
              </tr>
              {{ /list_sections }}
              <tr>
                <td bgcolor="#A6A6A6" height="16"><p class="index-naslovi">About</p></td>
              </tr>
              {{ list_sections constraints="number greater 199" }}
              {{ if $campsite->section->number != 230 }}
              <tr> 
      	        <td class="menu"><p class="index"><a class="index" href="{{ uri options="reset_article_list" }}">{{ $campsite->section->name }}</a></p></td>
	          </tr>
	          <tr>
	  	        <td height="3" background="/templates/img/crtice.gif"></td>
              </tr>                      
              {{ /if }}
              {{ /list_sections }}   
	          <tr> 
                <td bgcolor="#B3B3B3" height="16" width="132"><p class="index-naslovi">Links</p></td>
              </tr>
                {{ set_section number="230" }}
                {{ list_articles constraints="type is Link" }}
              <tr> 
      	        <td class="menu"><p class="index"><a class="index" href="http://{{ $campsite->article->url }}" target="_blank">{{ $campsite->article->name }}</a></p></td>
	          </tr>
	          <tr>
	  	        <td height="3" background="/templates/img/crtice.gif"></td>
              </tr>   
              {{ /list_articles }}
            </table>
<br>
{{ /local }}              
