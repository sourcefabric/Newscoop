<!-- ******* archive ******** -->	
<table border="0" cellpadding="5" cellspacing="0" width="100%">
  <tr> 
    <td valign="top">
{{ unset_section }}
{{ list_issues length="3" order="bydate desc" }}
  {{ if $campsite->issue->is_current }}
  {{ else }} 
  <tr>
    <td valign="top">
      <p class="tekst-front"><b></b><a class="dalje" href="{{ uripath options="issue" }}">{{ $campsite->issue->name }}, dated {{ $campsite->issue->date->%w, %m %y }}</a></b></p>
      {{ local }}
      {{ unset_section }}
      {{ list_articles constraints="type is Article" }}

        <p class="tekst-front"><a class="dalje" href="{{ uri options="reset_subtitle_list" }}">{{ $campsite->article->name }}</a>, in section <b>{{ $campsite->section->name }}</b></p>

      {{ /list_articles }}                          
      {{ /local }}
    </td>
  </tr>
			  
  {{ if $campsite->current_list->at_end }}

  <!-- forward / back -->

  <tr>
    <td align="center">
      <table border="0" cellpadding="0" cellspacing="0" width="200">
	<tr>
	  <td colspan="3"> </td>
	</tr>
	<tr>
	  <td align="center" width="99">

          {{ if $campsite->current_list->has_previous_elements }}

            <a href="{{ uri options="template archive.tpl" }}?{{ urlparameters }}" class="navigation">Back</a>

          {{ else }}
          {{ /if }}
          
        </td>
        <td width="2"> </td>
	<td align="center" width="99">
        
          {{ if $campsite->current_list->has_next_elements }}

          <a href="{{ uri options="next_items" options="template archive.tpl" }}?{{ urlparameters options="next_items" }}" class="navigation">Forward</a>

          {{ else }}                                     
          {{ /if }}
        </td>
      </tr>
    </table>
  </td>
</tr>

{{ /if }}

{{ /if }}
{{ /list_issues }}                
				  
<!-- ********* end arhiva *********** -->
    </td>
  </tr>
				  
</table>