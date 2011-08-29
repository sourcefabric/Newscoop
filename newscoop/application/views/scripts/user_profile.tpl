{{extends file="layout.tpl"}}

{{block content}}
<h1>{{ $user }}</h1>

{{ if $user->logged_in }}
<p>Go to <a href="{{ $view->url(['controller' => 'dashboard', 'action' => 'index'], 'default') }}">edit profile</a>.</p>
{{ /if }}

<p>Go to <a href="{{ $view->url(['controller' => 'user', 'action' => 'index'], 'default') }}">User index</a>.</p>

<p>{{ $user->first_name }} {{ $user->last_name }}</p>
<p><em>member from {{ $user->created }}</em></p>

<dl class="profile">
    {{ foreach $profile as $label => $value }}
    <dt>{{ $label }}</dt>
    <dd>{{ $value|default:"n/a" }}</dd>
    {{ /foreach }}
</dl>
{{/block}}
