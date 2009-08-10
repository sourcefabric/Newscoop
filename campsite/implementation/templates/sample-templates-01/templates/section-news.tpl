<table border="0" cellpadding="0" cellspacing="0" width="100%">
                
  				  {{ local }}
				  {{ set_publication identifier="5" }}
				  {{ set_current_issue }}
                  {{ list_articles constraints="onfrontpage is off onsection is on" order="bynumber desc" }}
                 <tr>
 				  <td>
					  {{ if $campsite->image->has_image(2) }}
				     <div class="front-slika"><img src="/get_img.php?{{ urlparameters options="image 2" }}" width="72"></div>
					  {{ /if }}
					 {{ if $campsite->article->has_property("deck") && $campsite->article->deck != "" }}
	                 <p class="nadnaslov">{{ $campsite->article->deck }}</p>
	                 {{ /if }}
					 <p class="naslov"><a class="naslov" href="{{ uri options="reset_subtitle_list" }}">{{ $campsite->article->name }}</a></p>   
                     <p class="podnaslov">{{ $campsite->article->byline }}</p> 
					 <p class="tekst">{{ $campsite->article->intro }}  <span class="dalje"><a class="dalje" href="{{ uri options="reset_subtitle_list" }}">full story</a></span></p>
				   </td>
                 </tr>
				   {{ /list_articles }}
				   {{ /local }}
				</table>
