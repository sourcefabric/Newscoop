{{ if $gimme->interviewitem->status == 'draft' }}
    <a href="{{ uripath }}?f_interviewitemstatus=pending&amp;f_interviewitem_id={{ $gimme->interviewitem->identifier }}">Accept</a>
    <a href="{{ uripath }}?f_interviewitemstatus=rejected&amp;f_interviewitem_id={{ $gimme->interviewitem->identifier }}">Reject</a> 
    <a href="{{ uripath }}?f_interviewitemstatus=delete&amp;f_interviewitem_id={{ $gimme->interviewitem->identifier }}">Delete</a>           
{{ /if }}
    
<a href="{{ uripath }}?interviewitem_action=form&amp;f_interviewitem_id={{ $gimme->interviewitem->identifier }}">Edit</a>