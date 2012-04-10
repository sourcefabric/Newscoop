{{ $diff=date_diff(date_create('now'), date_create($date)) }}
{{ if $diff->y }} {{ $diff->y }} {{ if $diff->y > 1 }}years{{ else }}year{{ /if }}{{ /if }}
{{ if $diff->m }} {{ $diff->m }} {{ if $diff->m > 1 }}months{{ else }}month{{ /if }}{{ /if }}
{{ if !$diff->y }}
{{ if $diff->d }} {{ $diff->d }} {{ if $diff->d > 1 }}days{{ else }}day{{ /if }}{{ /if }}
{{ if !$diff->d }}
{{ if $diff->h && (!$diff->d || empty($short)) }} {{ $diff->h }} h{{ /if }}
{{ if !$diff->d && $diff->i && (empty($short) || !$diff->h) }} {{ $diff->i }} min{{ /if }}
{{ if !$diff->d && !$diff->h && !$diff->i && $diff->s }} {{ $diff->s }} sec{{ /if }}
{{ /if }}{{ /if }} ago