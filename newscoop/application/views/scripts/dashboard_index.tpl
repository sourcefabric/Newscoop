{{extends file="layout.tpl"}}

{{block content}}

<script>
function afterRegistration() {
    location.reload();
}
function newSubscriber(firstName, lastName, email) {
    var container = document.getElementById('new_subscriber_box');
    var url = 'https://abo.tageswoche.ch/dmpro?type=abo&mode=new&name=' + lastName + '&firstname=' + firstName + '&email=' + email + '&jscallback=afterRegistration';
    container.innerHTML = '<iframe src="'+url+'" width="600" height="300">';
}
function newSubscription(userSubscriptionKey) {
    var container = document.getElementById('new_subscription_box');
    var url = 'https://abo.tageswoche.ch/dmpro?type=abo&mode=new&userkey=' + userSubscriptionKey;
    container.innerHTML = '<iframe src="'+url+'" width="600" height="300">';
}
function manageSubscription(userSubscriptionKey) {
    var container = document.getElementById('manage_subscription_box');
    var url = 'https://abo.tageswoche.ch/dmpro?type=abo&mode=update&userkey=' + userSubscriptionKey;
    container.innerHTML = '<iframe src="'+url+'" width="600" height="300">';
}
</script>


<h1>Welcome {{ $user->name }}</h1>

<div class="user-image">
    <img src="{{ $user->image() }}" title="User image" />
</div>

<p>Go to <a href="{{ $view->url(['username' => $user->uname], 'user') }}">public profile</a>.</p>

<h2>Edit profile</h2>

{{ $form }}

-- <br>

{{ if $subscriber }}
    {{ if $userSubscriptions }}

        subscriptions:<br>
        <a href="javascript:manageSubscription('{{ $userSubscriptionKey }}');">manage subscriptions</a>
        <div id="manage_subscription_box"></div>
        <br>
        {{ foreach $userSubscriptions as $userSubscription }}
            - type: {{ $userSubscription->type }} 
            begin: {{ $userSubscription->validFromFormated }} 
            end: {{ $userSubscription->validUntilFormated }} 
            <br>
        {{ /foreach }}

    {{ else }}
        no subscriptions<br>
    {{ /if }}

    <br>==
    <br>
    <a href="javascript:newSubscription('{{ $userSubscriptionKey }}');">new subscription</a>
    <div id="new_subscription_box"></div>
    <br>

{{ else }}
    <br>
    <a href="javascript:newSubscriber('{{ $user_first_name }}', '{{ $user_last_name }}', '{{ $user_email }}');">new subscription</a>
    <div id="new_subscriber_box"></div>
{{ /if }}

{{/block}}
