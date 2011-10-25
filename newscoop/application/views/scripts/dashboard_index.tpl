{{extends file="layout.tpl"}}

{{block content}}

<script>
function afterRegistration() {
    location.reload();
}
function newSubscriber(firstName, lastName, email, productId) {
    var container = document.getElementById('new_subscriber_box');
    var url = 'https://abo.tageswoche.ch/dmpro?type=abo&mode=new&name=' + lastName + '&firstname=' + firstName + '&email=' + email + '&jscallback=afterRegistration&product=' + productId;
    container.innerHTML = '<iframe src="'+url+'" width="600" height="300">';
}
function newSubscription(userSubscriptionKey, productId) {
    var container = document.getElementById('new_subscription_box');
    var url = 'https://abo.tageswoche.ch/dmpro?type=abo&mode=new&userkey=' + userSubscriptionKey + '&product=' + productId;
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
    new subscription:
    <br>
    <a href="javascript:newSubscription('{{ $userSubscriptionKey }}', 1);">Normal-Abo Tages Woche 6 Monate</a><br>
    <a href="javascript:newSubscription('{{ $userSubscriptionKey }}', 2);">Normal-Abo Tages Woche 12 Monate</a><br>
    <a href="javascript:newSubscription('{{ $userSubscriptionKey }}', 3);">Normal-Abo Tages Woche 24 Monate</a><br>
    <a href="javascript:newSubscription('{{ $userSubscriptionKey }}', 4);">Schnupper-Abo Tages Woche 1 Monat Schnupper-Abo</a><br>
    <a href="javascript:newSubscription('{{ $userSubscriptionKey }}', 5);">Normal-Abo Tages Woche 12 Monate 2 FÜR 1-ABOS</a><br>
    <a href="javascript:newSubscription('{{ $userSubscriptionKey }}', 6);">Normal-Abo Tages Woche 6 Monate Studenten-Abo</a><br>
    <a href="javascript:newSubscription('{{ $userSubscriptionKey }}', 7);">Normal-Abo Tages Woche 6 Monate PROMO 1</a><br>
    <a href="javascript:newSubscription('{{ $userSubscriptionKey }}', 8);">Normal-Abo Tages Woche 18 Monate PROMO 2</a><br>
    <a href="javascript:newSubscription('{{ $userSubscriptionKey }}', 11);">Geschenk-Abo Tages Woche 6 Monate</a><br>
    <a href="javascript:newSubscription('{{ $userSubscriptionKey }}', 12);">Geschenk-Abo Tages Woche 12 Monate</a><br>
    <a href="javascript:newSubscription('{{ $userSubscriptionKey }}', 13);">Geschenk-Abo Tages Woche 24 Monate</a><br>
    <a href="javascript:newSubscription('{{ $userSubscriptionKey }}', 16);">Geschenk-Abo Tages Woche 6 Monate Studenten-Abo</a><br>
    <a href="javascript:newSubscription('{{ $userSubscriptionKey }}', 17);">Geschenk-Abo Tages Woche 6 Monate PROMO 1</a><br>
    <a href="javascript:newSubscription('{{ $userSubscriptionKey }}', 18);">Geschenk-Abo Tages Woche 18 Monate PROMO 2</a><br>
    <div id="new_subscription_box"></div>
    <br>

{{ else }}
    <br>==
    new subscription:
    <br>
    <a href="javascript:newSubscriber('{{ $user_first_name }}', '{{ $user_last_name }}', '{{ $user_email }}', 1);">Normal-Abo Tages Woche 6 Monate</a><br>
    <a href="javascript:newSubscriber('{{ $user_first_name }}', '{{ $user_last_name }}', '{{ $user_email }}', 2);">Normal-Abo Tages Woche 12 Monate</a><br>
    <a href="javascript:newSubscriber('{{ $user_first_name }}', '{{ $user_last_name }}', '{{ $user_email }}', 3);">Normal-Abo Tages Woche 24 Monate</a><br>
    <a href="javascript:newSubscriber('{{ $user_first_name }}', '{{ $user_last_name }}', '{{ $user_email }}', 4);">Schnupper-Abo Tages Woche 1 Monat Schnupper-Abo</a><br>
    <a href="javascript:newSubscriber('{{ $user_first_name }}', '{{ $user_last_name }}', '{{ $user_email }}', 5);">Normal-Abo Tages Woche 12 Monate 2 FÜR 1-ABOS</a><br>
    <a href="javascript:newSubscriber('{{ $user_first_name }}', '{{ $user_last_name }}', '{{ $user_email }}', 6);">Normal-Abo Tages Woche 6 Monate Studenten-Abo</a><br>
    <a href="javascript:newSubscriber('{{ $user_first_name }}', '{{ $user_last_name }}', '{{ $user_email }}', 7);">Normal-Abo Tages Woche 6 Monate PROMO 1</a><br>
    <a href="javascript:newSubscriber('{{ $user_first_name }}', '{{ $user_last_name }}', '{{ $user_email }}', 8);">Normal-Abo Tages Woche 18 Monate PROMO 2</a><br>
    <a href="javascript:newSubscriber('{{ $user_first_name }}', '{{ $user_last_name }}', '{{ $user_email }}', 11);">Geschenk-Abo Tages Woche 6 Monate</a><br>
    <a href="javascript:newSubscriber('{{ $user_first_name }}', '{{ $user_last_name }}', '{{ $user_email }}', 12);">Geschenk-Abo Tages Woche 12 Monate</a><br>
    <a href="javascript:newSubscriber('{{ $user_first_name }}', '{{ $user_last_name }}', '{{ $user_email }}', 13);">Geschenk-Abo Tages Woche 24 Monate</a><br>
    <a href="javascript:newSubscriber('{{ $user_first_name }}', '{{ $user_last_name }}', '{{ $user_email }}', 16);">Geschenk-Abo Tages Woche 6 Monate Studenten-Abo</a><br>
    <a href="javascript:newSubscriber('{{ $user_first_name }}', '{{ $user_last_name }}', '{{ $user_email }}', 17);">Geschenk-Abo Tages Woche 6 Monate PROMO 1</a><br>
    <a href="javascript:newSubscriber('{{ $user_first_name }}', '{{ $user_last_name }}', '{{ $user_email }}', 18);">Geschenk-Abo Tages Woche 18 Monate PROMO 2</a><br>
    <div id="new_subscriber_box"></div>
    <br>
{{ /if }}

{{/block}}
