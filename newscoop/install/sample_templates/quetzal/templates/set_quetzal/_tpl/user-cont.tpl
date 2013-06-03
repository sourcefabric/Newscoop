<div class="span8 community">
    <form action="{{ $view->url(['controller' => 'user', 'action' => 'search'], 'default', true) }}" method="GET">
        <div class="input-append">
            <input type="search" placeholder="{{ #search# }}" name="q">
            <button class="btn" type="submit"><i class="icon-search"></i></button>
        </div>
        <div class="clearfix"></div>
    </form>

    <section class="user-list">
        <div class="row">
        {{ foreach $users as $user }}
            <article class="span4 user-entry">
                <a href="{{ $view->url(['username' => $user->uname], 'user') }}" class="pull-left user-picture"><img src="{{ include file="_tpl/user-image.tpl" user=$user width=50 height=50 }}" alt="{{ $user->uname }}"></a>
                <h2><a class="link-color" href="{{ $view->url(['username' => $user->uname], 'user') }}">{{ $user->first_name }} {{ $user->last_name }}</a></h2>
                <span class="gray-text">{{ #memberSince# }} <time class="timeago" datetime="{{ $user->created|date_format:'%Y-%m-%d' }} 06:00:00">{{ $user->created|date_format:"%Y-%m-%d" }} 06:00:00</time></span><br>
                <span>{{ $user->posts_count }}&nbsp;{{ #posts# }}</span>
                <div class="clearfix"></div>
                <br>
                <hr> 
            </article>
            {{ /foreach }}
        </div>
        
    </section>

</div>

<script type="text/javascript">
    // Hide header search form for mobile devices
    $('#seachform').addClass("hidden-phone");
</script>
