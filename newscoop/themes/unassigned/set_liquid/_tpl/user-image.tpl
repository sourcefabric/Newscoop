{{ strip }}
    {{ if empty($size) }}
        {{ $size = "" }}
    {{ /if }}

    {{ if $size == "big" }}
        {{ $box = 113 }}
    {{ elseif $size == "small" }}
        {{ $box = 35 }}
    {{ else }}
        {{ $box = 64 }}
        {{ $size = "mid" }}
    {{ /if }}

    {{ if empty($class) }}
        {{ $class = "" }}
    {{ /if }}

    {{ if $user->is_active && $user->image($box, $box) }}
    <img alt="{{ $user->uname|escape }}" src="{{ $user->image($box, $box) }}" class="{{ $class|escape }}" />
    {{ else }}
    <img alt="{{ $user->uname|escape }}" src="{{ url static_file="_img/user-thumb-`$size`-default.jpg" }}" class="{{ $class|escape }}" />
    {{ /if }}
{{ /strip }}
