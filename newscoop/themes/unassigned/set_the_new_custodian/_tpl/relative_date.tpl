{{ $diff=date_diff(date_create('now'), date_create($date)) }}
<small class="date relative">
{{ if $diff->y }} {{ $diff->y }} {{ if $diff->y > 1 }}{{ #years# }}{{ else }}{{ #year# }}{{ /if }}{{ /if }}
{{ if $diff->m }} {{ $diff->m }} {{ if $diff->m > 1 }}{{ #months# }}{{ else }}{{ #month# }}{{ /if }}{{ /if }}
{{ if $diff->d }} {{ $diff->d }} {{ if $diff->d > 1 }}{{ #days# }}{{ else }}{{ #day# }}{{ /if }}{{ /if }}
{{ if $diff->h && (!$diff->d || empty($short)) }} {{ $diff->h }} {{ #hours# }}{{ /if }}
{{ if !$diff->d && $diff->i && (empty($short) || !$diff->h) }} {{ $diff->i }} {{ #minutes# }}{{ /if }}
{{ if !$diff->d && !$diff->h && !$diff->i && $diff->s }} {{ $diff->s }} {{ #seconds# }}{{ /if }} {{ #ago# }}
</small>