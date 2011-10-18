{{ include file="html_header.tpl" }}

<table class="main" cellspacing="0" cellpadding="0">
<tr>
  <td valign="top">
    <div id="breadcrubm">
    {{ breadcrumb }}
    </div>
    {{** main content area **}}
    <table class="content" cellspacing="0" cellpadding="0">
    <tr>
      <td>

    {{ if $gimme->debate->defined }}
    
        <h3>Debate Details</h3>
        
        {{ include file='debate/debate-form-ajax.tpl' included=true}}
        
        <br>
        <a href="{{ uri options="template section-debates.tpl" }}">All Debates</a>
        
    {{ else }}
       
        <p>
        
        <tr>
            <th align="left">Name</th>
            <th>Voting Begin</th>
            <th>Voting End</th>
            <th>Current</th>
            <th>Alloved/Taken Votes</th>
            <th>Votes</th>
        </tr>  
        <tr><td colspan="6"><hr></td></tr>
        {{ local }}
  
        {{ list_debates name="debates_list" length="10" order='bylastmodified ASC' }}
           <tr align="center">
            <td align="left">
                <a href="{{ uri options="template section-debates.tpl" }}">
                    {{ $gimme->debate->name }}
                </a>
            </td>
            <td>{{ $gimme->debate->date_begin|date_format }}</td>
            <td>{{ $gimme->debate->date_end|date_format }}</td>
            <td>{{ if $gimme->debate->is_current }} Y {{ else }} N {{ /if }}</td>
            <td>{{ $gimme->debate->votes_per_user }}/{{ $gimme->debate->user_vote_count }}</td>
            <td>{{ $gimme->debate->votes }}
          </tr>
           
        {{ if  $gimme->current_list->at_end }}
        <tr><td colspan="6"><hr></td></tr>
        <tr>
            <td>{{ $gimme->current_list->count }} Items</td>
            <td colspan="5">
                {{ if $gimme->current_list->has_previous }}
                    <a href="{{ uripath }}?p_debates_list_start={{ $gimme->current_list->previous }}">previous</a>
                {{ else }}
                    previous    
                {{ /if }}
                |
                {{ if $gimme->current_list->has_next }}
                    <a href="{{ uripath }}?p_debates_list_start={{ $gimme->current_list->next }}">next</a>
                {{ else }}
                    next
                {{ /if }}
            </td>
        </tr>
        {{ /if }}
           
        {{ /list_debates }}
        {{ /local }}
    
    {{ /if }}
      
      
      </td>
    </tr>
    </table>
    {{** end main content area **}}
  </td>
  <td valign="top">
    {{ include file="html_rightbar.tpl" }}
  </td>
</tr>
</table>
{{ include file="html_footer.tpl" }}