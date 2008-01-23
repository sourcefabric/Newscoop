<h6>{{ $smarty.template }}</h6>

<table border=1 width=100%>
    <tr><td width=150>Item Id</td><td>{{ $campsite->interviewitem->identifier }}</td></tr>
    <tr><td width=150>Interview Id</td><td>{{ $campsite->interviewitem->interview_id }}</td></tr>
    <tr><td>Questioneer</td><td>{{ $campsite->interviewitem->questioneer->Name }}</td></tr>
    <tr><td>Question</td><td>{{ $campsite->interviewitem->question }}</td></tr>
    <tr><td>Status</td><td>{{ $campsite->interviewitem->status }}</td></tr>
    <tr><td>Answer</td><td>{{ $campsite->interviewitem->answer }}</td></tr>
    <tr><td>Item Order</td><td>{{ $campsite->interviewitem->item_order }}</td></tr>
</table>