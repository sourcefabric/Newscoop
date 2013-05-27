{{ $diff=date_diff(date_create('now'), date_create($date)) }}
{{ if $diff->y }} {{ $diff->y }} {{ if $diff->y > 1 }}{{ #yearS# }}{{ else }}{{ #year# }}{{ /if }}{{ /if }}
{{ if $diff->m }} {{ $diff->m }} {{ if $diff->m > 1 }}{{ #monthS# }}{{ else }}{{ #month# }}{{ /if }}{{ /if }}
{{ if !$diff->y }}
{{ if $diff->d }} {{ $diff->d }} {{ if $diff->d > 1 }}{{ #dayS# }}{{ else }}{{ #day# }}{{ /if }}{{ /if }}
{{ if !$diff->d }}
{{ if $diff->h && (!$diff->d || empty($short)) }} {{ $diff->h }} {{ #hours# }}{{ /if }}
{{ if !$diff->d && $diff->i && (empty($short) || !$diff->h) }} {{ $diff->i }} {{ #minutes# }}{{ /if }}
{{ if !$diff->d && !$diff->h && !$diff->i && $diff->s }} {{ $diff->s }} {{ #seconds# }}{{ /if }}
{{ /if }}{{ /if }} {{ #ago# }}