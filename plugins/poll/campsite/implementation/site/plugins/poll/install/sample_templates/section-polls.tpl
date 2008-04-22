{{ include file="html_header.tpl" }}

<script language="javascript" src="/javascript/prototype/prototype.js"></script>

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

    {{ if $campsite->poll->defined }}
    
        <h3>Poll Details</h3>
        
        {{ include file='poll-form.tpl' included=true}}
        
        <br>
        <a href="{{ uri options="template section-polls.tpl" }}">All Polls</a>
        
    {{ else }}
       
        <p>
        
        <tr>
            <th align="left">Name</th>
            <th>Voting Begin</th>
            <th>Voting End</th>
            <th>Current</th>
            <th>Voted</th>
            <th>Votes</th>
        </tr>  
        <tr><td colspan="6"><hr></td></tr>
        {{ local }}
  
        {{ list_polls name="polls_list" length="10" order='bylastmodified ASC' }}
           <tr align="center">
            <td align="left">
                <a href="{{ uri options="template section-polls.tpl" }}&amp;f_poll_nr={{ $campsite->poll->number }}&amp;f_poll_language_id={{ $campsite->poll->language_id }}">
                    {{ $campsite->poll->name }}
                </a>
            </td>
            <td>{{ $campsite->poll->date_begin|date_format }}</td>
            <td>{{ $campsite->poll->date_end|date_format }}</td>
            <td>{{ if $campsite->poll->is_current }} Y {{ else }} N {{ /if }}</td>
            <td>{{ if $campsite->poll->has_voted }} Y {{ else }} N {{ /if }}</td>
            <td>{{ $campsite->poll->votes }}
          </tr>
           
        {{ if  $campsite->current_list->at_end }}
        <tr><td colspan="6"><hr></td></tr>
        <tr>
            <td>{{ $campsite->current_list->count }} Items</td>
            <td colspan="5">
                {{ if $campsite->current_list->has_previous }}
                    <a href="{{ uripath }}?p_polls_list_start={{ $campsite->current_list->previous }}">previous</a>
                {{ else }}
                    previous    
                {{ /if }}
                |
                {{ if $campsite->current_list->has_next }}
                    <a href="{{ uripath }}?p_polls_list_start={{ $campsite->current_list->next }}">next</a>
                {{ else }}
                    next
                {{ /if }}
            </td>
        </tr>
        {{ /if }}
           
        {{ /list_polls }}
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