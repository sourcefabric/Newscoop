<table width="100%" cellspacing="0" cellpadding="0" border="0">	  
            <tr>
              <td width="11"></td>
              <td width="1" background="/templates/img/bgrmiddle1.gif"></td>
              <td width="424" valign="top">
			  
			    <!-- main story -->
			  {{ list_articles length="1" constraints="type is Article onfrontpage is on onsection is on" order="bynumber desc" }}
                  {{ if $campsite->current_list->at_beginning }}
                <table width="100%" cellspacing="0" cellpadding="0" border="0">
                  <tr> 
                    <td height="1" background="/templates/img/bgrmiddle2.gif"></td>
                  </tr>				
                  <tr> 
                    <td onmouseover="this.style.backgroundColor='#DCF6D2'" style="cursor:pointer;cursor: hand;"
                      onclick="document.location.href='{{ uri options="reset_article_list" }}'" 
                      onmouseout="this.style.backgroundColor='#ffffff'" valign="top">
					  {{ if $campsite->image->has_image(2) }}
					  <div class="front-slika1"><img src="/get_img.php?{{ urlparameters options="image 2" }}" border="0"></div>
					  {{ /if }}
					  <table width="100%" cellpadding="0" cellspacing="0" border="0">
                        <tr> 
                          <td height="1" background="/templates/img/bgrmiddle2.gif"></td>
                        </tr>					  
					  </table>
                      <p class="main-naslov">{{ $campsite->article->name }}</p>
					  <p class="text">{{ $campsite->article->intro }}
					  {{ if $campsite->article->teaser_a != "" }}
				       <p class="tizeri"><img src="/templates/img/tizer.gif" width="8" height="5">{{ $campsite->article->teaser_a }}</p>
					  {{ /if }}
  					  {{ if $campsite->article->teaser_b != "" }}
					   <p class="tizeri"><img src="/templates/img/tizer.gif" width="8" height="5">{{ $campsite->article->teaser_a }}</p>
					  {{ /if }}
					 </td>
                   </tr>
                 </table>
				 {{ /if }}
		         {{ /list_articles }}	
				 
				 
                 <table width="100%" cellspacing="0" cellpadding="0" border="0">				 
                   <tr> 
                     <td height="1" background="/templates/img/bgrmiddle2.gif"></td>
                   </tr>
				 </table>
				 
				 <!-- three stories -->
				 
                 <table width="100%" cellspacing="0" cellpadding="0" border="0">				 				 
                   <tr> 
				   {{ local }}
				   {{ set_section number="10" }}
				   {{ list_articles length="1" constraints="type is Article onsection is on onfrontpage is off" order="bynumber desc" }} 
                     <td width="140" valign="top" 
					   onmouseover="this.style.backgroundColor='#DCF6D2'" style="cursor:pointer;cursor: hand;"
                       onclick="document.location.href='{{ uri options="reset_subtitle_list" }}'" 
                       onmouseout="this.style.backgroundColor='#ffffff'">
					   <p class="nadnaslov">{{ $campsite->article->deck }}</p>
					   <p class="naslov">{{ $campsite->article->name }}</p> 
					   {{ if $campsite->image->has_image(1) }}
                       <div class="front-slika2"><img src="/get_img.php?{{ urlparameters options="image 1" }}" border="0"></div> 
					   {{ /if }}
                       <p class="text">{{ $campsite->article->intro }}</p>
                     </td>
					 {{ /list_articles }}
					 {{ /local }}
                     <td width="1" background="/templates/img/bgrmiddle1.gif"></td>
                                  
                     {{ local }}
				   {{ set_section number="20" }}
				   {{ list_articles length="1" constraints="type is Article onsection is on onfrontpage is off" order="bynumber desc" }} 
                     <td width="140" valign="top" 
					   onmouseover="this.style.backgroundColor='#DCF6D2'" style="cursor:pointer;cursor: hand;"
                       onclick="document.location.href='{{ uri options="reset_subtitle_list" }}'" 
                       onmouseout="this.style.backgroundColor='#ffffff'">
					   <p class="nadnaslov">{{ $campsite->article->deck }}</p>
					   <p class="naslov">{{ $campsite->article->name }}</p> 
					   {{ if $campsite->image->has_image(1) }}
                       <div class="front-slika2"><img src="/get_img.php?{{ urlparameters options="image 1" }}" border="0"></div> 
					   {{ /if }}
                       <p class="text">{{ $campsite->article->intro }}</p>
                     </td>
					 {{ /list_articles }}
					 {{ /local }}
					 
                     <td width="1" background="/templates/img/bgrmiddle1.gif"></td>
					 
                     {{ local }}
				   {{ set_section number="30" }}
				   {{ list_articles length="1" constraints="type is Article onsection is on onfrontpage is off" order="bynumber desc" }} 
                     <td width="140" valign="top" 
					   onmouseover="this.style.backgroundColor='#DCF6D2'" style="cursor:pointer;cursor: hand;"
                       onclick="document.location.href='{{ uri options="reset_subtitle_list" }}'" 
                       onmouseout="this.style.backgroundColor='#ffffff'">
					   <p class="nadnaslov">{{ $campsite->article->deck }}</p>
					   <p class="naslov">{{ $campsite->article->name }}</p> 
					   {{ if $campsite->image->has_image(1) }}
                       <div class="front-slika2"><img src="/get_img.php?{{ urlparameters options="image 1" }}" border="0"></div> 
					   {{ /if }}
                       <p class="text">{{ $campsite->article->intro }}</p>
                     </td>
					 {{ /list_articles }}
					 {{ /local }}
                   </tr>
                 </table>
				 
                 <table width="100%" cellspacing="0" cellpadding="0" border="0">
                   <tr> 
                     <td height="1" background="/templates/img/bgrmiddle2.gif"></td>
                   </tr>
				 </table>
				 
				 <!-- titles -->
				 
                 <table width="100%" cellspacing="0" cellpadding="0" border="0">
				   <tr>
				     <td colspan="2" height="15"></td>
				   </tr>
                  {{ list_articles constraints="type is Article onFrontPage is off onsection is off" order="bynumber desc" }}				 
                   <tr>
                     <td width="18" align="right" valign="top" style="padding-top: 6px"><img src="/templates/img/tizer.gif" border="0"></td>
                     <td valign="top"><p class="naslovi"><a class="naslovi" href="{{ uri options="reset_subtitle_list" }}"><strong>{{ $campsite->article->name }}</strong> - {{ $campsite->article->byline }}</a></p></td>
				   </tr>
                   {{ /list_articles }}
                   		   <tr>
				     <td colspan="2" height="15"></td>
				   </tr>				 				   
                 </table>
				 
				 <table width="100%" cellpadding="0" cellspacing="0" border="0">
                   <tr> 
                     <td height="1" background="img/bgrmiddle2.gif"></td>
                   </tr>
				   <tr>
				     <td height="35"></td>
				   </tr>
                 </table>
				 
			   </td>
               <td width="1" background="/templates/img/bgrmiddle1.gif"></td>
               <td width="11"></td>
             </tr>
           </table>