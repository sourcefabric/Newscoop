<!-- {{ $smarty.template }} -->

{{ list_interviews length=10 constraints=$_constraints }}
    {{ if $gimme->current_interviews_list->at_beginning }}
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
        <td><a href="{{ uripath }}?f_interview_id={{ $gimme->interview->identifier }}">{{ $gimme->interview->title }}</a></td>
        <td>{{ $gimme->interview->questions_begin|camp_date_format:'%Y-%m-%d %H:%i' }} - {{ $gimme->interview->questions_end|camp_date_format:'%Y-%m-%d %H:%i' }}</td>
        <td>{{ $gimme->interview->interview_begin|camp_date_format:'%Y-%m-%d %H:%i' }} - {{ $gimme->interview->interview_end|camp_date_format:'%Y-%m-%d %H:%i' }}</td>
        <td>{{ $gimme->interview->status }}</td>
        <td>{{ $gimme->interview->moderator->name }}</td>
        <td>{{ $gimme->interview->guest->name }}</td>
    </tr>
    
    {{ if $gimme->current_interviews_list->at_end }}
        </table>
    {{ /if }}
{{ /list_interviews }}

{{ if $gimme->prev_list_empty }}
    No interview found
{{ /if }}

<!-- /{{ $smarty.template }} -->