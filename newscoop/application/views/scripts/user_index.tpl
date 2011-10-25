{{extends file="layout.tpl"}}

{{block content}}

<div>
    <form method="GET" action="{{ $view->url(['controller' => 'user', 'action' => 'search'], 'default', true) }}">
        <input type="text" name="q"></input>
        <input type="submit" value="search"></input>
    </form>
</div>

<h1>Users index</h1>

<ul class="tabs">
    <li><a href="{{ $view->url(['controller' => 'user', 'action' => 'index'], 'default', true) }}">Active</a></li>
    <li><a href="{{ $view->url(['controller' => 'user', 'action' => 'filter', 'f' => 'a-z'], 'default', true) }}">All</a></li>
    <li><a href="{{ $view->url(['controller' => 'user', 'action' => 'filter', 'f' => 'a-d'], 'default', true) }}">A-D</a></li>
    <li><a href="{{ $view->url(['controller' => 'user', 'action' => 'filter', 'f' => 'e-k'], 'default', true) }}">E-K</a></li>
    <li><a href="{{ $view->url(['controller' => 'user', 'action' => 'filter', 'f' => 'l-p'], 'default', true) }}">L-P</a></li>
    <li><a href="{{ $view->url(['controller' => 'user', 'action' => 'filter', 'f' => 'q-t'], 'default', true) }}">Q-T</a></li>
    <li><a href="{{ $view->url(['controller' => 'user', 'action' => 'filter', 'f' => 'u-z'], 'default', true) }}">U-Z</a></li>
    <li><a href="{{ $view->url(['controller' => 'user', 'action' => 'editors'], 'default', true) }}">Editors</a></li>
</ul>

<ul class="users">
    {{ foreach $users as $user }}
    <li>
        <h3>{{ $user->uname }}</h3>
        {{ if $user->image }}
        <img src="{{ $user->image }}" />
        {{ /if }}
        <hr />
    </li>
    {{ /foreach }}
</ul>

{{include file='paginator_control.tpl'}}

<div class="community_ticker">
{{ list_community_feeds length=5 }}<p>{{ $gimme->community_feed->created }} {{ $gimme->community_feed->message }}<p>{{ /list_community_feeds }}
</div>

{{/block}}
