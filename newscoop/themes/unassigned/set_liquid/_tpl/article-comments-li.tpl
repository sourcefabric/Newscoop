
<li class="comment-content">
    {{ if $user->identifier }}
        {{ if $user->is_active }}
            <a href="{{ $view->url(['username' => $user->uname], 'user') }}">
            {{ strip }}
                {{ include file="_tpl/user-image.tpl" size="small" inline user=$gimme->comment->user }}
            {{ /strip }}
            </a>
        {{ /if }}
    {{ else }}
        <img src="{{ url static_file='_img/user-thumb-small-default.jpg' }}" alt="" />
    {{ /if }}

    <h5>{{ $gimme->comment->subject }} </h5>
    <time>
    {{ if $user->identifier }}
        {{ if $user->is_active }}
            <a href="{{ $view->url(['username' => $user->uname], 'user') }}">
                {{ include file="_tpl/user-name.tpl" user=$user }}
            </a>
        {{ /if }}
    {{ else }}
        {{ $gimme->comment->nickname }}
    {{ /if }} on {{ $gimme->comment->submit_date|camp_date_format:"%e.%m.%Y %H:%i" }}
    </time>
    <p>{{ $gimme->comment->content|create_links|nl2br }}</p>
</li>
