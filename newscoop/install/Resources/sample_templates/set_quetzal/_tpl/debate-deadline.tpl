{{ $timestamp = sprintf('@%d', $gimme->debate->date_end) }}
{{ $closingdate=date_create($timestamp) }}
{{ $deadline=$closingdate->setTime(12, 0) }}
{{ $diff=date_diff($deadline, date_create('now')) }}
{{ if $deadline->getTimestamp() > time() }}
    <p class="pull-right">{{ $diff->days }} {{ #days# }}, {{ $diff->h }} {{ #hours# }}, {{ $diff->i }} {{ #minutes# }} {{ #more# }}</p>
{{ else }}
    <p class="pull-right">{{ #discussionClosedOn# }} {{ $deadline->format('j.n.Y') }} at noon</p>	      
{{ /if }}   
