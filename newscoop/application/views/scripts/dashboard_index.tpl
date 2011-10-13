{{extends file="layout.tpl"}}

{{block content}}
<h1>Welcome {{ $user->name }}</h1>

<div class="user-image">
    <img src="{{ $user->image() }}" title="User image" />
</div>

<p>Go to <a href="{{ $view->url(['username' => $user->uname], 'user') }}">public profile</a>.</p>

<h2>Edit profile</h2>

{{ $form }}

-- <br>

{{ if $userSubscriptions }}
subscriptions:<br>
{{ foreach $userSubscriptions as $userSubscription }}
- type: {{ $userSubscription->getSubscriptionType() }} 
begin: {{ $userSubscription->getTimeBegin()->format('Y.m.d') }} 
end: {{ $userSubscription->getTimeEnd()->format('Y.m.d') }} 
<a href="#">Details [{{ $userSubscription->getSubscription() }}]</a>
<br>
{{ /foreach }}

{{ else }}
no subscriptions<br>
{{ /if }}
<br>--
<br>
<a href="#">new subscription</a>

{{/block}}
