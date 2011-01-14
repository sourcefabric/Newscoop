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
        {{ if $gimme->user->has_permission('plugin_interview_admin') }} 
        
            <a href="{{ uripath }}?interview_action=form">New Interview</a><p>
                
            
            {{ if $smarty.request.interview_action || $gimme->interview_action->defined }}
            
                {{ include file='interview/admin/interview-action.tpl }} 
    
            {{ elseif $smarty.request.interviewitem_action || $gimme->interviewitem_action->defined }}
            
                {{ include file='interview/admin/interviewitem-action.tpl }} 
           
             {{ elseif $gimme->interviewitem->defined }}
            
                {{ include file='interview/interviewitem-details.tpl' }}
                {{ include file='interview/admin/interviewitem-actions.tpl' }}
    
                
            {{ elseif $gimme->interview->defined }}
            
                {{ include file='interview/interview-details.tpl' }}
                {{ include file='interview/admin/interview-actions.tpl' }}
                        
            {{ else }}
            
                {{ include file='interview/admin/interviews-list.tpl' }}
                
            {{ /if }}  
        
        {{ else }}
            No permission
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
