<table width="100%" cellspacing="0" cellpadding="0" border="0"> 
             <tr>
                <td width="424" valign="top">  
                  <p class="tekst">Unicode search is supported.</p><hr size="1" noshade>
{{ list_search_results order="bynumber desc" }}
{{ if $campsite->current_list->at_beginning }}
<p class="naslov2">Search results:</p>
{{ /if }}
<p class="tekst"><span class="indeks"><a class="plus" href="{{ uri options="reset_subtitle_list" }}">{{ $campsite->article->name }}</a></span> from {{ $campsite->article->creation_date|camp_date_format:"%e.%m.%Y" }} in section <b>{{ $campsite->section->name }}</b></p>
{{ /list_search_results }}
{{ if $campsite->prev_list_empty }}
      <p class="tekst"><i>No results.</i></p>
{{ /if }}
				  </td> 
              </tr> 
            </table>