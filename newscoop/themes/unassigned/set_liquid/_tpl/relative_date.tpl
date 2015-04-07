{{ $diff=date_diff(date_create('now'), date_create($date)) }}
<small class="date relative">
{{ if $diff->y }} {{ $diff->y }} {{ if $diff->y > 1 }}{{'years'|translate}}{{ else }}{{'year'|translate}}{{ /if }}{{ /if }}
{{ if $diff->m }} {{ $diff->m }} {{ if $diff->m > 1 }}{{'months'|translate}}{{ else }}{{'month'|translate}}{{ /if }}{{ /if }}
{{ if $diff->d }} {{ $diff->d }} {{ if $diff->d > 1 }}{{'days'|translate}}{{ else }}{{'day'|translate}}{{ /if }}{{ /if }}
{{ if $diff->h && (!$diff->d || empty($short)) }} {{ $diff->h }} {{'hours'|translate}}{{ /if }}
{{ if !$diff->d && $diff->i && (empty($short) || !$diff->h) }} {{ $diff->i }} {{'minutes'|translate}}{{ /if }}
{{ if !$diff->d && !$diff->h && !$diff->i && $diff->s }} {{ $diff->s }} {{'seconds'|translate}}{{ /if }} {{'ago'|translate}}
</small>