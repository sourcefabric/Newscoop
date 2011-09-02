{{extends file="layout.tpl"}}

{{block content}}
<h1>{{ $user }}</h1>

<ul class="links">
    {{ if $user->logged_in }}
    <li><a href="{{ $view->url(['controller' => 'dashboard', 'action' => 'index'], 'default') }}">Edit profile</a>.</li>
    {{ /if }}
    <li>Go to <a href="{{ $view->url(['controller' => 'user', 'action' => 'index'], 'default') }}">User index</a>.</li>
</ul>

{{ if $user->image() }}
<img src="{{ $user->image }}" alt="{{ $user }}" />
{{ /if }}

<p>{{ $user->first_name }} {{ $user->last_name }}</p>
<p><em>member from {{ $user->created }}</em></p>

<dl class="profile">
    {{ foreach $profile as $label => $value }}
    {{ if !empty($value) }}
    <dt>{{ $label }}</dt>
    <dd>{{ $value|default:"n/a" }}</dd>
    {{ /if }}
    {{ /foreach }}
</dl>
{{/block}}
