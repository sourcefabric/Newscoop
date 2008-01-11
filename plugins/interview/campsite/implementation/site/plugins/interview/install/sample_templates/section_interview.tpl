{{ include file="html_header.tpl" }}

<h4>{{ $smarty.template }}</h4>

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
        {{ if $campsite->user->has_permission('plugin_interview_moderator') }}
        
            <a href="{{ uripath }}?action=interview_create">New Interview</a><p>
            
        {{ /if }}
         
        
            
        {{ if $smarty.request.action || $campsite->interview_action->defined || $campsite->interviewstatus_action->defined }}
        
            {{ include file='interview-action.tpl }} 
                  
        {{ elseif $campsite->interview->defined }}
        
            {{ include file='interview-details.tpl' }}
            
            <a href="{{ uripath }}?interview_id={{ $campsite->interview->identifier }}&amp;action=add_question">
                <input type="button" value="add question">
            </a>
            
            {{ list_interviewitems length=10 }}
                {{ include file='interviewitem-details.tpl show_actions=true }}
            {{ /list_interviewitems }}
            
        {{ else }}
            {{ list_interviews length=10 }}
                {{ include file='interview-details.tpl show_actions=true }}
            {{ /list_interviews }}    
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
