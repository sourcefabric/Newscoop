Dear {{ $campsite->interview->questioneer->name }},<br>

we like to invite to interview {{ $campsite->interview->title }}:
{{ include file='interview-details.tpl' }}

<p>
You can add your question <a href="{{ url }}">here</a>. 

<p>
Best regards,<br>
your {{ $campsite->publication->site }} team.


{{ assign var='subject' value='Hallo' }}
{{ assign var='sender' value='Interview notifier <ich@interview>' }}