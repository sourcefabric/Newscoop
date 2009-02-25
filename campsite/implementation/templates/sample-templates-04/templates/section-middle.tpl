<table width="100%" cellspacing="0" cellpadding="0" border="0">
		  <tr>
		  <td colspan="5" class="ticker"> </td>
		  </tr>	  
  		  <tr>
		  <td colspan="5"><img src="/templates/img/spacer.gif" width="1" height="2"></td>
		  </tr>	  

            <tr>
              <td width="11"></td>
              <td width="1" background="/templates/img/bgrmiddle1.gif"></td>
              <td width="424" valign="top">
			  
			    <!-- main story -->
			  <table width="100%" cellspacing="0" cellpadding="0" border="0">
                  <tr> 
                    <td height="1" background="/templates/img/bgrmiddle2.gif"></td>
                  </tr>				
				  {{ list_articles length="1" constraints="type is Article onfrontpage is on" order="bynumber desc" }}
                  {{ if $campsite->current_list->at_beginning }}

                  <tr>
                    <td onmouseover="this.style.backgroundColor='#DCF6D2'" style="cursor:pointer;cursor: hand;"
                      onclick="document.location.href='{{ uri options="reset_subtitle_list" }}'" 
                      onmouseout="this.style.backgroundColor='#ffffff'" valign="top">
					  {{ if $campsite->image->has_image2 }} 
					  <div class="front-slika1"><img src="/get_img.php?{{ urlparameters options="image 2" }}" border="0"></div>
					  {{ /if }}
					  <table width="100%" cellpadding="0" cellspacing="0" border="0">
                        <tr> 
                          <td height="1" background="/templates/img/bgrmiddle2.gif"></td>
                        </tr>					  
					  </table>
                      <p class="main-naslov">{{ $campsite->article->name }}</p>
					  <p class="text">{{ $campsite->article->intro }}</p>
				      {{ if $campsite->article->teaser_a != "" }}
					  <p class="tizeri"><img src="/templates/img/tizer.gif" width="8" height="5">{{ $campsite->article->teaser_a }}</p>
					  {{ /if }}
					  {{ if $campsite->article->teaser_b != "" }}
 					  <p class="tizeri"><img src="/templates/img/tizer.gif" width="8" height="5">{{ $campsite->article->teaser_b }}</p>
					  {{ /if }}
				    </td>
                 </tr>
				  {{ /if }}
				  {{ /list_articles }}
                </table>
			<table width="100%" cellspacing="0" cellpadding="0" border="0">
<tbody>
<tr>
<td height="1" background="/templates/img/bgrmiddle2.gif">
</td>
</tr>
</tbody>
</table>	 

				 
				 <!-- three stories -->
			   {{ list_articles length="3" constraints="type is Article onsection is on onfrontpage is off" order="bynumber desc" }} 				 
                 <table width="100%" cellspacing="0" cellpadding="0" border="0">				 				 
                   <tr>
                     <td valign="top" style="cursor: hand;cursor:pointer;"
                       onclick="document.location.href='{{ uri options="reset_subtitle_list" }}'" 
					   onmouseover="this.style.backgroundColor='#DCF6D2'" 
                       onmouseout="this.style.backgroundColor='#ffffff'">
					   <div class="front-slika2" style="float:left;margin-bottom: 4px; "><img src="/get_img.php?{{ urlparameters options="image 1" }}" width="117" border="0"></div>
					   <p class="nadnaslov">{{ $campsite->article->deck }}</p>
					    <p class="naslov">{{ $campsite->article->name }}</p>
                       <p class="text">{{ $campsite->article->intro }}<span class="plus"><a href="{{ uri options="reset_subtitle_list" }}" class="pretrazivanje">full story</a> </span></p>                      </td>
                   </tr>
                 </table>
				{{ /list_articles }}				 
				<table width="100%" cellspacing="0" cellpadding="0" border="0">
<tbody>
<tr>
<td height="1" background="/templates/img/bgrmiddle2.gif">
</td>
</tr>
</tbody>
</table>	  
				 <!-- titles -->
				 
                 <table width="100%" cellspacing="0" cellpadding="0" border="0">
				   <tr>
				     <td colspan="2" height="15"></td>
				   </tr>
				   {{ list_articles constraints="type is Article onsection is off onfrontpage is off" order="bynumber desc" }}
                   <tr>
                     <td width="18" align="right" valign="top" style="padding-top: 6px"><img src="/templates/img/tizer.gif" border="0"></td>
                     <td valign="top"><p class="naslovi"><a class="naslovi" href="{{ uri options="reset_subtitle_list" }}"><b>{{ $campsite->article->name }}</b>, {{ $campsite->article->byline }}</a></p></td>
				   </tr>
				   {{ /list_articles }}
				   <tr>
				     <td colspan="2" height="15"></td>
				   </tr>				 				   
                 </table>
				 
				 <table width="100%" cellpadding="0" cellspacing="0" border="0">
                   <tr> 
                     <td height="1" background="/templates/img/bgrmiddle2.gif"></td>
                   </tr>
				   <tr>
				     <td height="35"><div class="front-slika3"></div><p class="rubrika"> </p>
		             </td>
				   </tr>
                </table>			   </td>
               <td width="1" background="/templates/img/bgrmiddle1.gif"></td>
               <td width="11"></td>
             </tr>
           </table>