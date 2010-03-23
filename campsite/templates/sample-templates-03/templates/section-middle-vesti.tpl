{{ list_articles constraints="onfrontpage is off type is Article" order="bynumber desc" }}
		{{ if $campsite->article->has_image(2) }}
			<div class="front-slika"><img src="/get_img.php?{{ urlparameters options="image 2" }}" border="0"></div>
		{{ /if }}
				  <p class="nadnaslov-front">{{ $campsite->article->deck }}</p>
				  <p class="big-naslov"><a class="naslov" href="{{ uri }}">{{ $campsite->article->name }}</a></p>
				  <p class="tekst-front">{{ $campsite->article->intro }}
				   <span class="dalje"><a class="dalje" href="{{ uri }}">full story<img src="../img/dalje.gif" border="0"></a></span></p>
{{ /list_articles }}
 
