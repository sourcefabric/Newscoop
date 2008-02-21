<!-- {{ $smarty.template }} -->

<table border=1 width=100%>
    <tr><td width=150>InterviewId</td><td>{{ $campsite->interview->identifier }}</td></tr>
    <tr><td>Title</td><td>{{ $campsite->interview->title }}</td></tr>
    <tr><td>Language</td><td>{{ $campsite->interview->language->Name }}</td></tr>
    <tr><td>Moderator</td><td>{{ $campsite->interview->moderator->Name }}</td></tr>
    <tr><td>Guest</td><td>{{ $campsite->interview->guest->Name }}</td></tr>
    <tr><td>Thumbnail</td><td>{{ if $campsite->interview->image->thumbnailurl }}<img src="{{ $campsite->interview->image->thumbnailurl }}">{{ /if }}</td></tr>
    <tr><td>Description (short)</td><td>{{ $campsite->interview->description_short }}</td></tr>
    <tr><td>Description</td><td>{{ $campsite->interview->description }}</td></tr>
    <tr><td>Interview Begin</td><td>{{ $campsite->interview->interview_begin|date_format }}</td></tr>
    <tr><td>Interview End</td><td>{{ $campsite->interview->interview_end|date_format }}</td></tr>
    <tr><td>Questions Begin</td><td>{{ $campsite->interview->questions_begin|date_format }}</td></tr>
    <tr><td>Questions End</td><td>{{ $campsite->interview->questions_end|date_format }}</td></tr>
    <tr><td>Questions Limit</td><td>{{ $campsite->interview->questions_limit }}</td></tr>
    <tr><td>Status</td><td>{{ $campsite->interview->status }}</td></tr>
    <tr><td>Order</td><td>{{ $campsite->interview->order }}</td></tr>
</table>


<!-- /{{ $smarty.template }} -->