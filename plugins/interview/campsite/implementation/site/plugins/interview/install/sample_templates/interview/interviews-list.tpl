<!-- {{ $smarty.template }} -->

{{ list_interviews length=10 constraints=$_constraints }}
    {{ if $campsite->current_interviews_list->at_beginning }}
    <table border="1" width="100%">
        <tr>
            <th>Title</th>
            <th>Questios Timeframe</th>
            <th>Interview Timeframe</th>
            <th>Status</th>
            <th>Moderator</th>
            <th>Guest</th>
        </tr>
    {{ /if }}
    
    <tr>
        <td><a href="{{ uripath }}?f_interview_id={{ $campsite->interview->identifier }}">{{ $campsite->interview->title }}</a></td>
        <td>{{ $campsite->interview->questions_begin|camp_date_format:'%Y-%m-%d %H:%i' }} - {{ $campsite->interview->questions_end|camp_date_format:'%Y-%m-%d %H:%i' }}</td>
        <td>{{ $campsite->interview->interview_begin|camp_date_format:'%Y-%m-%d %H:%i' }} - {{ $campsite->interview->interview_end|camp_date_format:'%Y-%m-%d %H:%i' }}</td>
        <td>{{ $campsite->interview->status }}</td>
        <td>{{ $campsite->interview->moderator->name }}</td>
        <td>{{ $campsite->interview->guest->name }}</td>
    </tr>
    
    {{ if $campsite->current_interviews_list->at_end }}
        </table>
    {{ /if }}
{{ /list_interviews }}

{{ if !$campsite->current_interviews_list->count }}
    No interview found
{{ /if }}

<!-- /{{ $smarty.template }} -->