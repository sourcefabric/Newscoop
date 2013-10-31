{{ $timestamp = sprintf('@%d', $gimme->debate->date_end) }}
{{ $closingdate=date_create($timestamp) }}
{{ $deadline=$closingdate->setTime(12, 0) }}
{{ $diff=date_diff($deadline, date_create('now')) }}
{{ if $deadline->getTimestamp() > time() }}
    <p>{{ $diff->days }} {{ #days# }}, {{ $diff->h }} {{ #hours# }}, {{ $diff->i }} {{ #minutes# }} more {{ if $gimme->article->comment_count }}<span class="comm">{{ $gimme->article->comment_count }}</span>{{ /if }}</p>
{{ else }}
    <p>Discussion closed on {{ $deadline->format('j.n.Y') }} at noon {{ if $gimme->article->comment_count }}<a href="{{ url}}#comments"><span class="comm">{{ $gimme->article->comment_count }}</span></a>{{ /if }}</p> 						      
{{ /if }}   
