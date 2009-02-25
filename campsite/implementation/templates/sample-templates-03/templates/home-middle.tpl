<table width="100%" cellpadding="0" cellspacing="0" border="0">
			  <tr>
			    <td align="left"><p class="datum-front">{{ $smarty.now|camp_date_format:"%W, %d. %M %Y." }}</p></td>
			  </tr>
			  
			  <!-- tema dana -->
			  
			  <tr>
			    <td align="left" style="border-top: 1px solid #999999">
				{{ list_articles length="1" constraints="type is Article onfrontpage is on onsection is on" order="bynumber desc" }}
				     {{ if $campsite->image->has_image2 }}
				  <div class="front-slika"><img src="/get_img.php?{{ urlparameters options="image 2" }}" border="0"></div>
				     {{ /if }}
				  <p class="nadnaslov-front">{{ $campsite->article->deck }}</p>
				  <p class="big-naslov"><a class="naslov" href="{{ uri options="reset_subtitle_list" }}">{{ $campsite->article->name }}</a></p>
				  <p class="tekst-front">{{ $campsite->article->intro }}
				   <span class="dalje"><a class="dalje" href="{{ uri options="reset_subtitle_list" }}">full story<img src="/templates/img/dalje.gif" border="0"></a></span></p>
				{{ if $campsite->article->teaser_a != "" }}
                                <div class="tizeri">
				  <ul class="tizeri">
				  <li class="tizer">{{ $campsite->article->teaser_a }}</li>
 				{{ if $campsite->article->teaser_b != "" }} 
				  <li class="tizer">{{ $campsite->article->teaser_b }}</li>
                                  </ul>
                                  </div>
					{{ /if }}
                    {{ /if }}
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
					    
						{{ include file="home-middle-news.tpl" }}
						
					  <!-- end srednje levo -->
					  
					  </td>
					  <td width="13"></td>
					  <td valign="top" width="180">
					  
					  <!-- srednje desno -->
					  
					  {{ include file="home-shadow.tpl" }}
					  
					  <!-- end srednje desno -->
					  
					  </td>
					</tr>
				  </table>
				</td>
			  </tr>
			  
			  <!-- end ostale vesti i program -->
			  
			</table>