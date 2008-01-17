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
        {{ if $smarty.request.action || $campsite->interview_action->defined }}
        
            {{ include file='interview-action.tpl }} 
                  
        {{ elseif $campsite->interview->defined }}
        
            {{ include file='interview/interview-details.tpl' }}
            
            {{ if $campsite->interview->can_add_question }}
                <a href="{{ uripath }}?interview_id={{ $campsite->interview->identifier }}&amp;action=add_question">
                    <input type="button" value="add question">
                </a>
            {{ /if }}
                
            {{ list_interviewitems length=10 }}
                {{ include file='interviewitem-details.tpl show_actions=true }}
            {{ /list_interviewitems }}
            
        {{ else }}
        <form name="status">
          <select name="filter_interview_status" onchange="location.href='{{ uripath }}?filter_interview_status='+document.forms['status'].elements['filter_interview_status'].value">
            <option value="">Status</option>
            <option value="pending">pending</option>
            <option value="published">published</option>
          </select>
        </form>
            <table border="1" width="100%">
            <tr><th>Title</th><th>Status</th><th>Moderator</th><th>Guest</th></tr>
            {{ assign var='_status' value=$smarty.request.filter_interview_status|default:'published' }}
            {{ list_interviews length=10 constraints="status is `$_status`" }}
                <tr>
                    <td><a href="{{ uripath }}?f_interview_id={{ $campsite->interview->identifier }}">{{ $campsite->interview->title }}</a></td>
                    <td>{{ $campsite->interview->status }}</td>
                    <td>{{ $campsite->interview->moderator->name }}</td>
                    <td>{{ $campsite->interview->guest->name }}</td>
            {{ /list_interviews }}   
            </table> 
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
