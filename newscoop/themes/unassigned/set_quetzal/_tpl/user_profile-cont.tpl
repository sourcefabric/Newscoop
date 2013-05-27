<div class="span6">
    <img class="pull-left profile-picture" src="{{ include file='_tpl/user-image.tpl' user=$user width=175 height=210 }}" alt="{{ #profilePicture# }}"> 
    <div class="profile-info">
        <h1>{{ $user->first_name }} {{ $user->last_name }} ({{$user->uname}})</h1>
        <p><b>{{ #gender# }}</b> {{ $profile['gender'] }}</p>
        <p><b>{{ #organisation# }}</b> {{ $profile['organisation']}}</p>
        <p><b>{{ #website# }}</b> <a class="link-color" href="{{ $profile['website']}}" target="_blank" rel="nofollow">{{ $profile['website']|escape}}</a></p>
        <p><b>{{ #birthday# }}</b> {{ $profile['birth_date'] }}</p>
        {{ if $profile['twitter'] }}
        <p><b>Twitter</b> <a class="link-color" href="http://twitter.com/{{ $profile['twitter'] }}" rel="nofollow" target="_blank">{{ $profile['twitter'] }}</a></p>
        {{ /if }}
        {{ if $profile['facebook'] }}
        <p><b>Facebook</b> <a class="link-color" href="{{ $profile['facebook'] }}" rel="nofollow" target="_blank">{{ $profile['facebook'] }}</a></p>
        {{ /if }}
        {{ if $profile['google'] }}
        <p><b>Google+</b> <a class="link-color" href="{{ $profile['googl'] }}" rel="nofollow" target="_blank">{{ $profile['google'] }}</a></p>
        {{ /if }}

        <p class="member-since">{{ #memberSince# }} <time class="timeago" datetime="{{ $user->created|date_format:'%Y-%m-%d' }} 06:00:00">{{ $user->created|date_format:'%Y-%m-%d' }} 06:00:00</time></p>
        <p class="member-post">{{ $user->posts_count }} {{ #posts# }}</p>
    </div>
    <div class="clearfix"></div>
</div>
<div class="span6">                                                                
    <h3>{{ #about# }}</h3>
    <div class="about-text">
        {{ $profile['bio']}}
    </div>
</div>
