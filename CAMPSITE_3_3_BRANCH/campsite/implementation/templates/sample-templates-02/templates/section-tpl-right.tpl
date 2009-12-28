<table width="100%" cellspacing="0" cellpadding="0">
              <tr>
                <td>
{{ list_articles length="1" constraints="type is Article onfrontpage is on onsection is off" }}
			      <p class="nadnaslov">{{ $campsite->article->deck }}</p>
				  <p class="naslov"><a class="naslov" href="{{ uri }}">{{ $campsite->article->name }}</a></p>
                  <p class="tekst">{{ $campsite->article->intro }}<a href="{{ uri }}" border="0"><img src="/templates/img/dalje.gif" border="0"></a></p>
{{ if $campsite->article->teaser_a != "" }}                      
<p class="tizeri"><img src="/templates/img/tizer.gif" width="11" height="11"> {{ $campsite->article->teaser_a }}</p>
{{ /if }}
{{ if $campsite->article->teaser_b != "" }}
<p class="tizeri"><img src="/templates/img/tizer.gif" width="11" height="11">                                      {{ $campsite->article->teaser_b }}</p>
{{ /if }}  
{{ /list_articles }}            
                </td>
              </tr>                          
              <tr>
                <td height="1" background="/templates/img/07linija.gif"><img src="img/07linija.gif" width="1" height="1"></td>
              </tr>
			                
              <tr>
                <td valign="top">
{{ list_articles length="1" constraints="type is Photo onfrontpage is on" }}
				  <p class="nadnaslov">PHOTO OF THE WEEK</p>
                  <p class="tekst"><img src="/get_img.php?{{ urlparameters options="image 1" }}"</p>
{{ /list_articles }}
				</td>
              </tr>
            </table>