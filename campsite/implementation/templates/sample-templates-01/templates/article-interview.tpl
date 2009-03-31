<td>
<p class="nadnaslov">{{ $campsite->article->deck }}</p>
             <p class="main-naslov">{{ $campsite->article->name }}</p>
             {{ if $campsite->article->byline != "" }}
             {{ if $campsite->image->has_image2 }}
             <div style="float:right; margin: 5px;><img src="/get_img.php?{{ urlparameters options="image 2" }}"><br/><span class="caption">{{ $campsite->image2->description }}</span></div>
             {{ /if }}
             <p class="podnaslov">{{ $campsite->article->byline }}</p>
             {{ /if }}
             <p class="tekst">{{ $campsite->article->intro }}</p>
{{ if $campsite->article->content_accessible }}
             <p class="tekst">{{ $campsite->article->full_text }}</p>
{{ else }}
<p class="footer">You must be subscribed to read full story...</p>
{{ /if }}
             {{  }}
</td>