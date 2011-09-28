{{extends file="layout.tpl"}}

{{block content}}
<h1>{{ $user->name }}</h1>

<ul class="links">
    {{ if $user->logged_in }}
    <li><a href="{{ $view->url(['controller' => 'dashboard', 'action' => 'index'], 'default') }}">Edit profile</a>.</li>
    {{ /if }}
    <li>Go to <a href="{{ $view->url(['controller' => 'user', 'action' => 'index'], 'default') }}">User index</a>.</li>
</ul>

{{ if $user->image() }}
<img src="{{ $user->image(30, 30) }}" alt="{{ $user->name }}" />
{{ /if }}

<p>{{ $user->first_name }} {{ $user->last_name }}</p>
<p><em>member from {{ $user->created }}</em></p>

<p>posts No.: {{ $user->posts_count }}</p>

<dl class="profile">
    {{ foreach $profile as $label => $value }}
    {{ if !empty($value) }}
    <dt>{{ $label }}</dt>
    <dd>{{ $value|default:"n/a" }}</dd>
    {{ /if }}
    {{ /foreach }}
</dl>

{{ list_user_comments user=$user->identifier length=10 order="bydate desc" }}
    <p>{{ $gimme->user_comment->submit_date }}</p>
    <p>{{ $gimme->user_comment->subject }}</p>
    <p>{{ $gimme->user_comment->content }}</p>
    <p><a href="{{ $gimme->user_comment->article->url }}">{{ $gimme->user_comment->article->name }}</a></p>
{{ /list_user_comments }}

{{ assign var=i value=1 }}
{{ list_images user=$user->identifier order="byLastUpdate desc"}}
    {{ if $i <= 10 }}
        <a class="user_uploaded_pics" rel="user_pics" href="{{ uri options="image" }}"><img src="{{ uri options="image width 50 height 50" }}" alt=""/></a>
    {{ else }}
        <a class="user_uploaded_pics" rel="user_pics" href="{{ uri options="image" }}"></a>
    {{ /if }}

    {{ $i = $i + 1 }}
{{ /list_images }}

{{/block}}
