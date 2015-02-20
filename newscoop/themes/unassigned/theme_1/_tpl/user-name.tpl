{{ strip }}
{{ if $user->is_active }}
    {{ $user->uname|escape }}
{{ else }}
    {{'inactiveUser'|translate}}
{{ /if }}
{{ /strip }}
