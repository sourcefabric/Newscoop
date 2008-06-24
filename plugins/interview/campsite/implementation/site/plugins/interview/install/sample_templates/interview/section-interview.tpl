<!-- {{ $smarty.template }} -->

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
        {{ if $smarty.request.interview_action || $campsite->interview_action->defined }}
        
            {{ include file='interview-action.tpl }} 

        {{ elseif $smarty.request.interviewitem_action || $campsite->interviewitem_action->defined }}
            
                {{ include file='interview/interviewitem-action.tpl }} 
                
        {{ elseif $campsite->interview->defined }}
        
            {{ include file='interview/interview-details.tpl' }}
            <br>
            
            {{ if $campsite->interview->in_questions_timeframe }}
                <a href="{{ uripath }}?f_interview_id={{ $campsite->interview->identifier }}&amp;interviewitem_action=form">Add your question</a>
                <br>
                
                {{ list_interviewitems length=1 constraints='status not rejected' }}
                    <a href="{{ uripath }}?f_interview_id={{ $campsite->interview->identifier }}&amp;interviewitem_action=list">List existing questions</a>
                    <br>
                {{ /list_interviewitems }}
                 
             {{ elseif $campsite->interview->in_interview_timeframe }}

                {{ list_interviewitems length=1 constraints='status not rejected' }}
                    <a href="{{ uripath }}?f_interview_id={{ $campsite->interview->identifier }}&amp;interviewitem_action=list">List existing questions</a>
                    <br>
                {{ /list_interviewitems }}

             {{ elseif $campsite->interview->status == 'published' }}

                {{ list_interviewitems length=1 constraints='status not rejected' }}
                    <a href="{{ uripath }}?f_interview_id={{ $campsite->interview->identifier }}&amp;interviewitem_action=list">Show interview</a>
                    <br>
                {{ /list_interviewitems }}

            {{ /if }}

             
        {{ else }}

            {{ if $campsite->user->defined }}
                {{ if $smarty.request.f_interviewnotify == 'on' }}   
                    You will recive interview notifications<br>
                {{ elseif $smarty.request.f_interviewnotify == 'off' }}        
                    You will not recive interview notifications<br>
                {{ /if }}
                    
                {{ if $campsite->user->has_permission('plugin_interview_notify') }}
                    <a href="{{ uripath }}?{{ urlparameters }}&amp;f_interviewnotify=off">Do not notify me about new interviews</a>
                {{ elseif  !$campsite->user->has_permission('plugin_interview_notify') }}
                    <a href="{{ uripath }}?{{ urlparameters }}&amp;f_interviewnotify=on">Notify me about new interviews</a>
                {{ /if }}
                
            {{ else }}
                <a href="javascript: if (confirm('You need to subscribe to use this funtion. Do you want to go to subscription page?')) location.href='{{ uri options="template user_form.tpl" }}'">Notify me about new interviews</a>
            {{ /if }}
                
            <h4>Interviews awaiting questions:</h4>
            {{ include file='interview/interviews-list.tpl' _constraints="status is pending questions_begin equal_smaller curdate() questions_end equal_greater curdate() language is current()"}} 
        
            <h4>Interviews awaiting answers:</h4>
            {{ include file='interview/interviews-list.tpl' _constraints="status is pending interview_begin equal_smaller curdate() interview_end equal_greater curdate() language is current()"}}
            
            <h4>Interviews already answered:</h4>
            {{ include file='interview/interviews-list.tpl' _constraints="status is published language is current()"}}
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

<!-- /{{ $smarty.template }} -->
