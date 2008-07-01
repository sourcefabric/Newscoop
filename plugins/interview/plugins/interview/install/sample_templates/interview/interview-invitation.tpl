Dear {{ $campsite->interview->questioneer->name }},<br>

we like to invite to interview {{ $campsite->interview->title }}:
{{ include file='interview/interview-details.tpl' }}

<p>
You can add your question(s) 
<a href="http://{{ uripath }}?f_interview_id={{ $campsite->interview->identifier }}&amp;interview_action=">here</a>. 

<p>
Best regards,<br>
your {{ $campsite->publication->site }} team.


{{ assign var='subject' value='Hallo' }}
{{ assign var='sender' value='Interview notifier <ich@interview>' }}