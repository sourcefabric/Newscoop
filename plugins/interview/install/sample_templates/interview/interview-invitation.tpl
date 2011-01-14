Dear {{ $gimme->interview->questioneer->name }},<br>

we like to invite to interview {{ $gimme->interview->title }}:
{{ include file='interview/interview-details.tpl' }}

<p>
You can add your question(s) 
<a href="http://{{ uripath }}?f_interview_id={{ $gimme->interview->identifier }}&amp;interview_action=">here</a>. 

<p>
Best regards,<br>
your {{ $gimme->publication->site }} team.


{{ assign var='subject' value='Hallo' }}
{{ assign var='sender' value='Interview notifier <ich@interview>' }}