<table width="100%" cellspacing="0" cellpadding="0">
        {{ local }}
        {{ set_current_issue }}
        {{ list_sections constraints="number smaller 199" }}
              <tr>
                <td height="1" bgcolor="#ffffff"></td>
              </tr>
              <tr>
                <td class="menu" onmouseover="this.style.backgroundColor='#BDE9AD'" style="cursor: pointer;cursor: hand;" onclick="document.location.href='{{ uri options="reset_article_list" }}'"                   onmouseout="this.style.backgroundColor='#9FD98B'"> <p class="index"><img src="/templates/img/indexleft.gif">{{ $campsite->section->name }}</p>
				</td>
              </tr>
		{{ /list_sections }}
		{{ /local }}
			    			  			      
              <tr>
                <td valign="middle" bgcolor="#006B24" height="16" style="padding-left: 3px"><p class="index-naslovi"><img src="/templates/img/indexleft2.gif"> About PT#4</p></td>
              </tr>
        {{ local }}
        {{ list_sections length="3" constraints="number greater 199" }}
			  <tr>
                <td height="1" background="/templates/img/bgrleft2b.gif"></td>
              </tr>	
              <tr>
                <td class="menu-white" 
				  onmouseover="this.style.backgroundColor='#DCF6D2'" style="cursor: pointer;cursor: hand;"
                  onclick="document.location.href='{{ uri options="reset_article_list" }}'" 
                  onmouseout="this.style.backgroundColor='#FFFFFF'">
                  <p class="rubrika">{{ $campsite->section->name }}</p>
				</td>
              </tr>
		{{ /list_sections }}
		{{ /local }}
		      <tr>
			    <td height="1" bgcolor="#ffffff"></td>
			  </tr>
              <tr>
                <td valign="middle" bgcolor="#006B24" height="16" style="padding-left: 3px"><p class="index-naslovi"><img src="/templates/img/indexleft2.gif"> Linkovi</p></td>
              </tr>

              <tr> 
                <td height="2" bgcolor="#ffffff"></td>
              </tr>
                        {{ local }}
                        {{ set_section number="230" }}
			{{ list_articles constraints="type is Link" }}
              <tr>
                <td class="menu2" 
				  onmouseover="this.style.backgroundColor='#A7DD94'" style="cursor: pointer;cursor: hand;"
                  onclick="document.location.href='http://{{ $campsite->article->url }}'"
                  onmouseout="this.style.backgroundColor='#B4E6A2'">
                  <p class="index"><img src="/templates/img/indexleft4.gif"> {{ $campsite->article->name }}</p>
				</td>
              </tr>
             {{ /list_articles }}
             {{ /local }}
              <tr>
			    <td height="1" bgcolor="#ffffff"></td>
			  </tr>
			  			  			  			  
			  
              <tr>
                <td height="18" bgcolor="#ffffff"></td>
              </tr>
			  
			  <!-- banner -->
			  
              <tr>
                <td align="center"><a href="http://www.campware.org/en/camp/campsite_news/" target="_blank"><img src="/templates/img/camplogo.gif" border="0"></a>
<br><br>
<a href="http://www.campware.org/en/camp/livesupport_news/" target="_blank"><img src="/templates/img/lslogo.gif" border="0"></a>
</td>
              </tr>

			  		
            </table>
