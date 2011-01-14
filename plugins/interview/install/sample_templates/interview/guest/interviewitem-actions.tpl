{{ if $gimme->interviewitem->status == 'pending' }}
    <a href="{{ uripath }}?interviewitem_action=form&amp;f_interviewitem_id={{ $gimme->interviewitem->identifier }}">Answer</a>
{{ /if }}
    