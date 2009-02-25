<table width="100%" cellpadding="0" cellspacing="0" border="0">
			  <tr>
			    <td align="left"><p class="datum-front">{{ $smarty.now|camp_date_format:"%W, %d. %M %Y." }}</p></td>
			  </tr>
			  
			  <!-- tema dana -->
			  
			  <tr>
			    <td align="left" style="border-top: 1px solid #999999">
				<p class="nadnaslov-front">Unicode search is supported.</p><hr size="1" noshade>
{{ list_search_results order="bynumber desc" }}
{{ if $campsite->current_list->at_beginning }}
<p class="big-naslov">Search results:</p>
{{ /if }}
<p class="tekst-front"><a class="dalje" href="{{ uri options="reset_subtitle_list" }}">{{ $campsite->article->name }}</a> from {{ $campsite->article->creation_date|camp_date_format:"%e.%m.%Y" }} in section <b>{{ $campsite->section->name }}</b></p>
{{ /list_search_results }}
{{ if $campsite->prev_list_empty }}
      <p class="tekst-front"><i>No results.</i></p>
{{ /if }}

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
					    
						
						
					  <!-- end srednje levo -->
					  
					  <!-- srednje desno -->
					  
				      <!-- end srednje desno --></td>
				    </tr>
				  </table>
				</td>
			  </tr>
			  
			  <!-- end ostale vesti i program -->
			  
			</table>
