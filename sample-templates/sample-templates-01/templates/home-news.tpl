<table border="0" cellpadding="0" cellspacing="0" width="100%">
                
                  {{ list_articles length="3" constraints="type is Article onfrontpage is off onsection is on" order="bynumber asc" }}
                 <tr>
 				  <td>
					  {{ if $campsite->article->has_image(1) }}
				     <div class="front-slika"><img src="/get_img.php?{{ urlparameters options="image 1" }}" width="72"></div>
					  {{ /if }}
					 <p class="naslov"><a class="naslov" href="{{ uri options="reset_subtitle_list" }}">{{ $campsite->article->name }}</a></p>   
                     <p class="podnaslov">{{ $campsite->article->byline }}</p> 
					 <p class="tekst">{{ $campsite->article->intro }} <span class="dalje"><a class="dalje" href="{{ uri options="reset_subtitle_list" }}"> full story</a></span></p>
				   </td>
                 </tr>
				   {{ /list_articles }}
				</table>
