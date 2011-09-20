{{extends file="layout.tpl"}}

{{block content}}

<div>
    <form method="post" action="{{ $view->url() }}">
        <input type="text" name="users_search"></input>
        <input type="submit" value="search"></input>
    </form>
</div>

<h1>Users index</h1>

<table>
    <tbody>
        <tr>
            <td><a href="{{ $view->url([], 'user-active') }}">Active</a></td>
            <td><a href="{{ $view->url(['user-listing' => 'A-Z'], 'user-list') }}">All</a></td>
            <td><a href="{{ $view->url(['user-listing' => 'A-D'], 'user-list') }}">A-D</a></td>
            <td><a href="{{ $view->url(['user-listing' => 'E-K'], 'user-list') }}">E-K</a></td>
            <td><a href="{{ $view->url(['user-listing' => 'L-P'], 'user-list') }}">L-P</a></td>
            <td><a href="{{ $view->url(['user-listing' => 'Q-T'], 'user-list') }}">Q-T</a></td>
            <td><a href="{{ $view->url(['user-listing' => 'U-Z'], 'user-list') }}">U-Z</a></td>
        </tr>
    </tbody>
</table>

<ul class="users">
    {{ foreach $users as $user }}
    <li>
        <h3>{{ $user->name }}</h3>
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
