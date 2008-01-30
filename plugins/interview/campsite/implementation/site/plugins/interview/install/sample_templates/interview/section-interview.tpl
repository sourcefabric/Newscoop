{{ include file="html_header.tpl" }}

<h6>{{ $smarty.template }}</h6>

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
            
            {{ if $campsite->interview->in_question_timeframe }}
                <a href="{{ uripath }}?f_interview_id={{ $campsite->interview->identifier }}&amp;interviewitem_action=form">Add your question</a>
            
                {{ list_interviewitems length=1 constraints='status not rejected' }}
                    <a href="{{ uripath }}?f_interview_id={{ $campsite->interview->identifier }}&amp;interviewitem_action=list">List existing questions</a>
                {{ /list_interviewitems }}
                 
             {{ elseif $campsite->interview->in_interview_timeframe }}

                {{ list_interviewitems length=1 constraints='status not rejected' }}
                    <a href="{{ uripath }}?f_interview_id={{ $campsite->interview->identifier }}&amp;interviewitem_action=list">List existing questions</a>
                {{ /list_interviewitems }}

             {{ elseif $campsite->interview->status == 'published' }}

                {{ list_interviewitems length=1 constraints='status not rejected' }}
                    <a href="{{ uripath }}?f_interview_id={{ $campsite->interview->identifier }}&amp;interviewitem_action=list">Show interview</a>
                {{ /list_interviewitems }}

            {{ /if }}

             
        {{ else }}
        
            {{ assign var='_now' value=$smarty.now|camp_date_format:'%Y-%m-%d' }}
        
            <h6>Interviews awaiting questions</h6>
            {{ include file='interview/interviews-list.tpl' _constraints="status is pending questions_begin equal_smaller `$_now` questions_end equal_greater `$_now` language_id is `$campsite->language->number`"}} 
        
            <h6>Interviews awaiting answers</h6>
            {{ include file='interview/interviews-list.tpl' _constraints="status is pending interview_begin equal_smaller `$_now` interview_end equal_greater `$_now` language_id is `$campsite->language->number`"}}
            
            <h6>Interviews already answered</h6>
            {{ include file='interview/interviews-list.tpl' _constraints="status is published language_id is `$campsite->language->number`"}}
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
