{{ if $campsite->interviewitem->status == 'draft' }}
    <a href="{{ uripath }}?f_interviewitemstatus=pending&amp;f_interviewitem_id={{ $campsite->interviewitem->identifier }}">Accept</a>
    <a href="{{ uripath }}?f_interviewitemstatus=offline&amp;f_interviewitem_id={{ $campsite->interviewitem->identifier }}">Reject</a> 
    <a href="{{ uripath }}?f_interviewitemstatus=delete&amp;f_interviewitem_id={{ $campsite->interviewitem->identifier }}">Delete</a>           
{{ /if }}
    
<a href="{{ uripath }}?interviewitem_action=form&amp;f_interviewitem_id={{ $campsite->interviewitem->identifier }}">Edit</a>