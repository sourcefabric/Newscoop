<table border="0" cellpadding="0" cellspacing="0" width="100%">
<tr>
  <td valign="top">
    {{ list_articles constraints="type is Article onfrontpage is on" order="bynumber asc" }}
      <table>
      <tr>
         <td valign="top">
            <p class="main-naslov"><a class="main-naslov" href="{{ uri options="reset_subtitle_list" }}">{{ $campsite->article->name }}</a></p>  
            <p class="tekst">{{ $campsite->article->intro }}</p>
           {{ if $campsite->article->has_attachments }}
           {{ list_article_attachments }}
           <a class="dalje" href="{{ uri options="articleattachment" }}">{{ $campsite->attachment->description }}</a> <span class="tekst">({{ $campsite->attachment->mime_type }}, {{ $campsite->attachment->size_mb }}MB)</span><br>
           {{ /list_article_attachments }}
           {{ /if }}
           <span class="dalje"><a class="dalje" href="{{ uri options="reset_subtitle_list" }}">full story</a></span>
         </td>
         <td>
           {{ if $campsite->image->has_image(2) }}
           <table><tr><td><img src="/get_img.php?{{ urlparameters options="image 2" }}" align="center"></td></tr><tr><td align="center"><span class="caption">{{ $campsite->image2->description }}</span></td></tr></table>
           {{ /if }}
         </td>
      </tr>
      </table>

   {{ /list_articles }}
  </td> 
</tr>
</table>