{{ list_articles constraints="type is Article" }}
{{ if $campsite->current_list->index == 1 }}
{{ else }}
		{{ if $campsite->image->has_image(1) }}
			<div class="sport-slika"><img src="/get_img.php?{{ urlparameters options="image 1" }}" border="0"></div>
		{{ /if }}
				  <p class="nadnaslov-front">{{ $campsite->article->deck }}</p>
				  <p class="big-naslov"><a class="naslov" href="{{ uri }}">{{ $campsite->article->name }}</a></p>
				  <p class="tekst-front">{{ $campsite->article->intro }}
				   <span class="dalje"><a class="dalje" href="{{ uri }}">full story<img src="/templates/img/dalje.gif" border="0"></a></span></p><br>
{{ /if }}
{{ /list_articles }}
 