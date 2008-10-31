<table width="100%" cellpadding="0" cellspacing="0" border="0">
			  <tr>
			    <td align="left"><p class="datum-front"><!** date " %W, %d. %M %Y."></p></td>
			  </tr>
			  
			  <!-- tema dana -->
			  
			  <tr>
			    <td align="left" style="border-top: 1px solid #999999">
				<!** list length 1 article type is Article onfrontpage is on onsection is on order bynumber desc>
				     <!** if image 2>
				  <div class="front-slika"><img src="/cgi-bin/get_img?<!** urlparameters image 2>" border="0"></div>
				     <!** endif>
				  <p class="nadnaslov-front"><!** print article deck></p>
				  <p class="big-naslov"><a class="naslov" href="<!** uri reset_subtitle_list>"><!** print article name></a></p>
				  <p class="tekst-front"><!** print article intro>
				   <span class="dalje"><a class="dalje" href="<!** uri reset_subtitle_list>">full story<img src="/look/img/dalje.gif" border="0"></a></span></p>
				<!** if article teaser_a not "">
                                <div class="tizeri">
				  <ul class="tizeri">
				  <li class="tizer"><!** print article teaser_a></li>
 				<!** if article teaser_b not ""> 
				  <li class="tizer"><!** print article teaser_b></li>
                                  </ul>
                                  </div>
					<!** endif>
                    <!** endif>
                    <!** endlist>
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
					    
						<!** include home-middle-news.tpl>
						
					  <!-- end srednje levo -->
					  
					  </td>
					  <td width="13"></td>
					  <td valign="top" width="180">
					  
					  <!-- srednje desno -->
					  
					  <!** include home-shadow.tpl>
					  
					  <!-- end srednje desno -->
					  
					  </td>
					</tr>
				  </table>
				</td>
			  </tr>
			  
			  <!-- end ostale vesti i program -->
			  
			</table>