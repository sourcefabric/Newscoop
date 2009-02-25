<table width="100%" cellspacing="0" cellpadding="0">
{{ list_articles length="2" constraints="type is Article topic is Home right:en" }}
              <tr>
                <td>
			      <p class="nadnaslov">{{ $campsite->article->deck }}</p>
				  <p class="naslov"><a class="naslov" href="{{ uri }}">{{ $campsite->article->name }}</a></p>
                  <p class="tekst">{{ $campsite->article->intro }}<a href="{{ uri }}" border="0"><img src="/templates/img/dalje.gif" border="0"></a></p>
{{ if $campsite->article->teaser_a != "" }}                      
<p class="tizeri"><img src="/templates/img/tizer.gif" width="11" height="11"> {{ $campsite->article->teaser_a }}</p>
{{ /if }}
{{ if $campsite->article->teaser_b != "" }}
<p class="tizeri"><img src="/templates/img/tizer.gif" width="11" height="11">                                      {{ $campsite->article->teaser_b }}</p>
{{ /if }}  
                </td>
              </tr>                          
              {{ if $campsite->current_list->index == 1 }}
              <tr>
                <td height="1" background="/templates/img/07linija.gif"><img src="img/07linija.gif" width="1" height="1"></td>
              </tr>
              {{ /if }}
{{ /list_articles }}			                
            </table>