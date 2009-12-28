{{ list_articles length="1" constraints="type is Article onfrontpage is on onsection is off" }}
<table width="100%" cellspacing="0" cellpadding="0" border="0">
			  <tr>
                <td valign="top">
			      <p class="nadnaslov">{{ $campsite->article->deck }}</p>
				  <p class="main-naslov"><a class="main-naslov" href="{{ uri }}">{{ $campsite->article->name }}</a></p>
{{ if $campsite->article->has_image(2) }}
				  <div class="front-slika"><img src="/get_img.php?{{ urlparameters options="image 2" }}" border="0"><br><span class="caption">{{ $campsite->image2->description }}</span></div>
{{ /if }}
				  <p class="tekst">{{ $campsite->article->intro }}<a href="{{ uri }}" border="0"><img src="/templates/img/dalje.gif" border="0"></a></p>
				</td>
              </tr>
            </table>
{{ /list_articles }}
