{{ if $campsite->interviewitem->status == 'pending' }}
    <a href="{{ uripath }}?interviewitem_action=form&amp;f_interviewitem_id={{ $campsite->interviewitem->identifier }}">Answer</a>
{{ /if }}
    