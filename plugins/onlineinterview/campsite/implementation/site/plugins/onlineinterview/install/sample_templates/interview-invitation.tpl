Dear {{ $campsite->interview->invitee->name }},<br>

we like to invite to following interview:
{{ include file='interview-details.tpl' }}

<p>
You can add your question <a href="{{ url }}">here</a>. 

<p>
Best regards,<br>
your {{ $campsite->publication->site }} team.


{{ assign var='subject' value='Hallo' }}
{{ assign var='sender' value='Interview notifier <ich@interview>' }}