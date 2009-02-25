<table width="100%" cellpadding="0" cellspacing="0" border="0">
        <tr>
          <td height="90" align="left">{{ include file="home-banner.tpl" }}</td>
        </tr>
{{ list_articles constraints="type is Article onfrontpage is off onsection is on" }}
        <tr>
          <td valign="top">
	        {{ if $campsite->image->has_image1 }}
                <div class="front-slika2"><img src="/get_img.php?{{ urlparameters options="image 1" }}" width="72" height="50" border="0"><br><span class="caption">{{ $campsite->image->description }}</span></div>
                 {{ /if }}
			<p class="nadnaslov2">{{ $campsite->article->deck }}</p>
            <p class="naslov2"><a class="naslov2" href="{{ uri }}">{{ $campsite->article->name }}</a></p>                 
            <p class="tekst">{{ $campsite->article->intro }}<a href="{{ uri }}" border="0"><img src="/templates/img/dalje.gif" border="0"></a></p>
		  </td>              
        </tr>
        <tr>
          <td height="1" background="/templates/img/07linija.gif"></td>
		</tr>
		<tr>
		  <td height="2"></td>
		</tr>		
{{ /list_articles }}
      </table>
