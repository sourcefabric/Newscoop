<table border="0" cellpadding="0" cellspacing="0" width="100%">
          <tr>
            <td width="100%"><p class="ostale-vesti">Other news</p>
			{{ list_articles constraints="onsection is off onfrontpage is off" order="bynumber desc" }}
                              <p class="ostale-vesti2"><a class="ostale-vesti2" href="{{ uri options="reset_subtitle_list" }}">{{ $campsite->section->name }} 
{{ if $campsite->article->deck != "" }}
> {{ $campsite->article->deck }}
{{ /if }}
 > {{ $campsite->article->name }}</a></p>
            {{ /list_articles }}
			</td>
          </tr>
        </table>