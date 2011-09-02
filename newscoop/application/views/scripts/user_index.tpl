{{extends file="layout.tpl"}}

{{block content}}
<h1>Users index</h1>

<br />
<ul class="users">
    {{ foreach $users as $user }}
    <li>
        <h3><a href="{{ $view->url(['username' => $user->uname], 'user') }}">{{ $user }}</a></h3>
        {{ if $user->image() }}
        <img src="{{ $user->image(30, 30) }}" />
        {{ /if }}
        <hr />
    </li>
    {{ /foreach }}
</ul>
{{/block}}

{{block title}}xy{{/block}}
