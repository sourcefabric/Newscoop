{{extends file="layout.tpl"}}

{{block content}}

{{ assign var="userindex" value=1 }}

<h3>{{ #userIndex# }}</h3>

<div class="user-search">
    <form method="GET" action="{{ $view->url(['controller' => 'user', 'action' => 'search'], 'default', true) }}">
        <input type="text" name="q"></input>
        <input type="submit" value="{{ #search1# }}"></input>
    </form>
</div>

<ul id="filter-users" class="tabs simple-list filter">
    <li><a href="{{ $view->url(['controller' => 'user', 'action' => 'index'], 'default', true) }}">{{ #active# }}</a></li>
    <li><a href="{{ $view->url(['controller' => 'user', 'action' => 'filter', 'f' => 'a-z'], 'default', true) }}">{{ #all# }}</a></li>
    <li><a href="{{ $view->url(['controller' => 'user', 'action' => 'filter', 'f' => 'a-d'], 'default', true) }}">A-D</a></li>
    <li><a href="{{ $view->url(['controller' => 'user', 'action' => 'filter', 'f' => 'e-k'], 'default', true) }}">E-K</a></li>
    <li><a href="{{ $view->url(['controller' => 'user', 'action' => 'filter', 'f' => 'l-p'], 'default', true) }}">L-P</a></li>
    <li><a href="{{ $view->url(['controller' => 'user', 'action' => 'filter', 'f' => 'q-t'], 'default', true) }}">Q-T</a></li>
    <li><a href="{{ $view->url(['controller' => 'user', 'action' => 'filter', 'f' => 'u-z'], 'default', true) }}">U-Z</a></li>
    <li><a href="{{ $view->url(['controller' => 'user', 'action' => 'editors'], 'default', true) }}">{{ #editors# }}</a></li>
</ul>

<ul class="users">
    {{ foreach $users as $user }}
    <li><figure>
        	<a href="{{ $view->url(['username' => $user->uname], 'user') }}"><img src="{{ include file="_tpl/user-image.tpl" user=$user width=50 height=50 }}" /></a>
        </figure>
        <h5><a href="{{ $view->url(['username' => $user->uname], 'user') }}">{{ $user->uname }}</a><small>({{ $user->posts_count }} {{ #posts# }})</small></h5>
        <p>{{ if !empty($user['bio']) }}{{ $user['bio']|escape|truncate:100 }}{{ else }}...{{ /if }}</p>
    </li>
    {{ /foreach }}
</ul>

{{include file='_tpl/paginator_control.tpl'}}

{{/block}}
