<h6>{{ $smarty.template }}</h6>

<table border="1" width="100%">
    <tr><th>Title</th><th>Status</th><th>Moderator</th><th>Guest</th></tr>
    
    {{ list_interviews length=10 constraints=$_constraints }}
        <tr>
            <td><a href="{{ uripath }}?f_interview_id={{ $campsite->interview->identifier }}">{{ $campsite->interview->title }}</a></td>
            <td>{{ $campsite->interview->status }}</td>
            <td>{{ $campsite->interview->moderator->name }}</td>
            <td>{{ $campsite->interview->guest->name }}</td>
    {{ /list_interviews }}   
</table> 