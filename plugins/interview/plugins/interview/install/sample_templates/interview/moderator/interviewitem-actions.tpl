{{ if $campsite->interviewitem->status == 'draft' }}
    <a href="{{ uripath }}?f_interviewitemstatus=pending&amp;f_interviewitem_id={{ $campsite->interviewitem->identifier }}&amp;interviewitem_action=list&amp;f_interviewitem_status={{ $smarty.request.f_interviewitem_status}}">Accept</a>
    <a href="{{ uripath }}?f_interviewitemstatus=rejected&amp;f_interviewitem_id={{ $campsite->interviewitem->identifier }}&amp;interviewitem_action=list&amp;f_interviewitem_status={{ $smarty.request.f_interviewitem_status}}">Reject</a> 
    <a href="{{ uripath }}?f_interviewitemstatus=delete&amp;f_interviewitem_id={{ $campsite->interviewitem->identifier }}&amp;interviewitem_action=list&amp;f_interviewitem_status={{ $smarty.request.f_interviewitem_status}}">Delete</a>           
{{ /if }}
    
<a href="{{ uripath }}?interviewitem_action=form&amp;f_interviewitem_id={{ $campsite->interviewitem->identifier }}">Edit</a>