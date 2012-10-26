<table width="100%" cellpadding="0" cellspacing="0" border="0">
			  <tr>
			    <td align="left">
{{ list_articles length="1" constraints="type is Service" order="bynumber desc" }}
<div class="tizeri" style="width:100px;float:right;"><ul class="tizeri"><li class="tizer"><a href="{{ uri options="template print.tpl" }}">Print article</a></li></ul></div>
<p class="datum-front">{{ $smarty.now|camp_date_format:"%W, %d. %M %Y." }}</p></td>
			  </tr>
			  
			  <!-- tema dana -->
			  
			  <tr>
			    <td align="left" style="border-top: 1px solid #999999">
                        <p class="big-naslov">{{ $campsite->article->name }}</p> 
                        <p class="tekst-front">{{ $campsite->article->full_text }}</p> 
{{ /list_articles }}

				</td>
			  </tr>
			  
			  <!-- end tema dana -->
			  
			  <tr>
			    <td height="1" bgcolor="#999999"></td>
			  </tr>
			  
			  <!-- ostale vesti i program -->
			  
			  <tr>
			    <td>
				  <table width="100%" cellpadding="0" cellspacing="0" border="0">
				    <tr>
					  <td valign="top">
					  
					  <!-- srednje levo -->
					    
						{{ include file="section-middle-news.tpl" }}
						
					  <!-- end srednje levo -->
					  
					  <!-- srednje desno -->
					  
				      <!-- end srednje desno --></td>
				    </tr>
				  </table>
				</td>
			  </tr>
			  
			  <!-- end ostale vesti i program -->
			  
			</table>
