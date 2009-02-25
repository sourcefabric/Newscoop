<table width="461"  border="0" align="center" cellpadding="0" cellspacing="8"> 
        <tr> 
          <td width="247" valign="top">
<p class="tekst">Unicode search is supported...</p><hr size="1" noshade>
{{ list_search_results order="bynumber desc" }}
{{ if $campsite->current_list->at_beginning }}
<p class="tekst">Results:</p>
{{ /if }}
<p class="tekst"><span><a class="dalje" href="{{ uri options="reset_subtitle_list" }}">{{ $campsite->article->name }}</a></span> od {{ $campsite->article->creation_date|camp_date_format:"%e.%m.%Y" }} from section <b>{{ $campsite->section->name }}</b></p>
{{ /list_search_results }}
{{ if $campsite->prev_list_empty }}
      <p class="tekst"><i>Search results returned no articles... Please try with different parameters.</i></p>
{{ /if }}
          </td> 
        </tr> 
</table>