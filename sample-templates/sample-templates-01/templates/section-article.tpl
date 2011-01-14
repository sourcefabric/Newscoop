<table border="0" cellpadding="0" cellspacing="0" width="100%">
<tr>
   <td valign="top">
   {{ local }}
   {{ set_publication identifier="5" }}
   {{ set_current_issue }}
   {{ list_articles length="1" constraints="type is Article onfrontpage is on" order="bynumber desc" }}
   {{ if $campsite->current_list->at_beginning }}
     <table>
     <tr>
        <td valign="top">
     <p class="main-naslov"><a class="main-naslov" href="{{ uri options="reset_subtitle_list" }}">{{ $campsite->article->name }}</a></p>  
     <p class="podnaslov">{{ $campsite->article->byline }}</p>
          <p class="tekst">{{ $campsite->article->intro }}
          <span class="dalje"><a class="dalje" href="{{ uri options="reset_subtitle_list" }}">full story</a></span></p>
        </td>
        <td align="center" valign="top">
         {{ if $campsite->article->has_image(2) }}
         <img src="/get_img.php?{{ urlparameters options="image 2" }}" align="center"><br>
         <span class="caption">{{ $campsite->article->image2->description }}</span></div>
        </td>
     </tr>
     </table>
     {{ /if }}
     
   {{ /if }}
   {{ /list_articles }}
   {{ /local }}
   </td> 
</tr>
</table>
