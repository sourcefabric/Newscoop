<td>
<p class="nadnaslov">{{ $campsite->article->nadnaslov }}</p>
             <p class="main-naslov">{{ $campsite->article->name }}</p>
             {{ if $campsite->article->podnaslov != "" }}
             {{ if $campsite->image->has_image2 }}
             <div style="float:right; margin: 5px;><img src="/get_img.php?{{ urlparameters options="image 2" }}"><br/><span class="caption">{{ $campsite->image2->description }}</span></div>
             {{ /if }}
             <p class="podnaslov">{{ $campsite->article->podnaslov }}</p>
             {{ /if }}
             {{ if $campsite->article->autor != "" }}
             <p class="blok-podnaslov">{{ $campsite->article->autor }}</p>
             {{ /if }}
             <p class="tekst">{{ $campsite->article->intro }}</p>
{{ if $campsite->article->content_accesible }}
             <p class="tekst">{{ $campsite->article->tekst }}</p>
             {{ if $campsite->article->antrfile != "" }}
             <div style="background-color:#E4EEF8;"><p class="tekst">{{ $campsite->article->antrfile }}</p></div>
{{ else }}
<p class="footer">Ukoliko želite da pročitate ceo tekst morate biti pretplaćeni. To možete uraditi ovde...</p>
{{ /if }}
             {{ /if }}
</td>