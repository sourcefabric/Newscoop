<table width="100%" cellspacing="0" cellpadding="0" border="0"> 
              <tr> 
                <td colspan="5" class="ticker"> </td> 
              </tr> 
              <tr> 
                <td colspan="5"><img src="/templates/img/spacer.gif" width="1" height="2"></td> 
              </tr> 
              <tr> 
                <td width="11"></td> 
                <td width="1" background="/templates/img/bgrmiddle1.gif"></td> 
                <td width="424" valign="top">  
                  <p class="text">Unicode search is supported.</p><hr size="1" noshade>
{{ list_search_results order="bynumber desc" }}
{{ if $campsite->current_list->at_beginning }}
<p class="text">Search results:</p>
{{ /if }}
<p class="text"><span><a class="dalje" href="{{ uri options="article" }}">{{ $campsite->article->name }}</a></span> from {{ $campsite->article->creation_date|camp_date_format:"%e.%m.%Y" }} in section <b>{{ $campsite->section->name }}</b></p>
{{ /list_search_results }}
{{ if $campsite->prev_list_empty }}
      <p class="tekst"><i>No results.</i></p>
{{ /if }}
				  </td> 
                <td width="1" background="/templates/img/bgrmiddle1.gif"></td> 
                <td width="11"></td> 
              </tr> 
            </table>