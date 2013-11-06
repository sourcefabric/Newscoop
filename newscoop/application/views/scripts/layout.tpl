<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{block title}}Newcoop{{/block}}</title>

    {{block style}}
        {{if isset($view) }}
            {{ $view->headLink() }}
        {{/if }}
    {{/block}}

    {{block script}}
        {{if isset($view) }}
            {{ $view->headScript() }}
        {{/if }}
    {{/block}}
</head>
<body>
    <div class="header">
        {{block header}}Newscoop{{/block}}
        <div class="nav">
            <ul>
                <li><a href="{{ $view->url(['controller' => 'index', 'action' => 'index'], 'default') }}">Home</a></li>
                <li><a href="{{ $view->url(['controller' => 'user', 'action' => 'index'], 'default') }}">Users</a></li>
                <li><a href="{{ $view->url(['controller' => 'register', 'action' => 'index'], 'default') }}">Register</a></li>
                <li><a href="{{ $view->url(['controller' => 'auth', 'action' => 'index'], 'default') }}">Sign in</a></li>
            </ul>
        </div>
    </div>

    <div class="content">
    {{block content}}{{/block}}
    </div>

    <div class="footer">
    {{block footer}}
    &copy; {{ $smarty.now|camp_date_format:"%Y" }} Sourcefabric o.p.s.
    {{/block}}
    </div>
</body>
</html>
