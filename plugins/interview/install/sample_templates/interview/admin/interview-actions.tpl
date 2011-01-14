<h6>{{ $smarty.template }}</h6>

{{ if $gimme->interview->status == 'draft' }}
    <a href="{{ uripath }}?f_interviewstatus=pending&amp;f_interview_id={{ $gimme->interview->identifier }}">Invite now</a>
    
{{ elseif $gimme->interview->status == 'pending' }}
    <a href="{{ uripath }}?f_interviewstatus=published&amp;f_interview_id={{ $gimme->interview->identifier }}">Set published</a>
    
{{ elseif $gimme->interview->status == 'published' }}
    <a href="{{ uripath }}?f_interviewstatus=rejected&amp;f_interview_id={{ $gimme->interview->identifier }}">Set rejected</a>
    
{{ elseif $gimme->interview->status == 'rejected' }}
    <a href="{{ uripath }}?f_interviewstatus=draft&amp;f_interview_id={{ $gimme->interview->identifier }}">Set draft</a>
    
{{ /if }}
                
<br>
<a href="{{ uripath }}?interview_action=form&amp;f_interview_id={{ $gimme->interview->identifier }}">Edit</a>

<br>
<a href="{{ uripath }}?interviewitem_action=list&amp;f_interview_id={{ $gimme->interview->identifier }}">List items</a>

<br>
<a href="{{ uripath }}?f_interviewstatus=delete&amp;f_interview_id={{ $gimme->interview->identifier }}">Delete</a>