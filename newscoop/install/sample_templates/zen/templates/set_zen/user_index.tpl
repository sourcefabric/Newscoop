{{extends file="layout.tpl"}}

{{block content}}
<h1>Users index v2</h1>

<ul class="users">
    {{ foreach $users as $user }}
    <li><a href="{{ $view->url(['username' => $user->uname], 'user') }}">{{ $user }}</a></li>
    {{ /foreach }}
</ul>
{{/block}}
