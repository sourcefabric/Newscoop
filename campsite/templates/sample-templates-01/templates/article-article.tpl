<td>
  <p class="main-naslov">{{ $campsite->article->name }}</p>
  <p class="podnaslov">{{ $campsite->article->byline }}</p>
   {{ if $campsite->article->has_image(2) }}
   <div style="float:right; margin: 5px;" align="center"><img src="/get_img.php?{{ urlparameters options="image 2" }}"><br/><span class="caption">{{ $campsite->article->image2->description }}</span></div>
   {{ /if }}
  <p class="tekst">{{ $campsite->article->intro }}</p>
  {{ if $campsite->article->content_accessible }}
  <p class="tekst">{{ $campsite->article->full_text }}</p>
  {{ else }}
  <p class="footer">If You want to read whole article You must be subscribed...</p>
  {{ /if }}
</td>
