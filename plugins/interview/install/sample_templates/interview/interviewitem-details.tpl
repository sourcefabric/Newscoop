<!-- {{ $smarty.template }} -->

<table border="1" width=100%>
    <tr><td>Item Id:</td><td>{{ $gimme->interviewitem->identifier }}</td></tr>
    <tr><td width=150>Interview Id:</td><td>{{ $gimme->interviewitem->interview_id }}</td></tr>
    <tr><td width=150>Questioneer:</td><td>{{ $gimme->interviewitem->questioneer->Name }}</td></tr>
    <tr><td>Question:</td><td>{{ $gimme->interviewitem->question }}</td></tr>
    <tr><td>Status:</td><td>{{ $gimme->interviewitem->status }}</td></tr>
    <tr><td>Answer:</td><td>{{ $gimme->interviewitem->answer }}</td></tr>
    <tr><td>Order</td><td>{{ $gimme->interviewitem->order }}</td></tr>
</table>

<!-- /{{ $smarty.template }} -->