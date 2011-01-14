<!-- {{ $smarty.template }} -->

<table border=1 width=100%>
    <tr><td width=150>InterviewId</td><td>{{ $gimme->interview->identifier }}</td></tr>
    <tr><td>Title</td><td>{{ $gimme->interview->title }}</td></tr>
    <tr><td>Language</td><td>{{ $gimme->interview->language->Name }}</td></tr>
    <tr><td>Moderator</td><td>{{ $gimme->interview->moderator->Name }}</td></tr>
    <tr><td>Guest</td><td>{{ $gimme->interview->guest->Name }}</td></tr>
    <tr><td>Thumbnail</td><td>{{ if $gimme->interview->image->thumbnailurl }}<img src="{{ $gimme->interview->image->thumbnailurl }}">{{ /if }}</td></tr>
    <tr><td>Description (short)</td><td>{{ $gimme->interview->description_short }}</td></tr>
    <tr><td>Description</td><td>{{ $gimme->interview->description }}</td></tr>
    <tr><td>Interview Begin</td><td>{{ $gimme->interview->interview_begin|camp_date_format:'%Y-%m-%d %H:%i' }}</td></tr>
    <tr><td>Interview End</td><td>{{ $gimme->interview->interview_end|camp_date_format:'%Y-%m-%d %H:%i' }}</td></tr>
    <tr><td>Questions Begin</td><td>{{ $gimme->interview->questions_begin|camp_date_format:'%Y-%m-%d %H:%i' }}</td></tr>
    <tr><td>Questions End</td><td>{{ $gimme->interview->questions_end|camp_date_format:'%Y-%m-%d %H:%i' }}</td></tr>
    <tr><td>Questions Limit</td><td>{{ $gimme->interview->questions_limit }}</td></tr>
    <tr><td>Status</td><td>{{ $gimme->interview->status }}</td></tr>
    <tr><td>Order</td><td>{{ $gimme->interview->order }}</td></tr>
</table>


<!-- /{{ $smarty.template }} -->