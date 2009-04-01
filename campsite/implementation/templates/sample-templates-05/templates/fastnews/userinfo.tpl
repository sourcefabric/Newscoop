<div class="userinfo">
{{ if $campsite->user->logged_in }}
	<p class="opt"><b>Logged in &ndash; {{ $campsite->user->uname }}</b></p>
	<p class="opt"><a href="{{ uri options="template fastnews/useredit.tpl" }}">Modify user information</a>
	<p class="opt"><a href="{{ uri options="template fastnews/userchgpass.tpl" }}">Change password</a>
	{{ $campsite->url->set_parameter("logout", "true") }}
	<p class="opt"><a href="{{ uri }}">Logout</a>
    {{ $campsite->url->reset_parameter("logout") }}
{{ else }}
	{{ if $campsite->user->defined }}
		<p class="opt">Your are not logged in. Login <a href="{{ uri options="template fastnews/login.tpl" }}">here</a>
	{{ else }}
		<p class="opt"><b>Subscribe to {{ $campsite->publication->name }}</b></p>
		<p class="opt">Fastnews is supported by paying subscribers.
		Some content, labelled <img src="/templates/fastnews/subscriber.png" width=11 height=11" alt="[S]">,
		will only be open to you if you have a subscription</p>
		
		{{ $campsite->url->set_parameter("subscribe", "true") }}
		<p class="opt"><a href="{{ uri options="template fastnews/useredit.tpl" }}">Subscribe</a></p>
        {{ $campsite->url->reset_parameter("subscribe") }}

		<p class="opt">Existing subscribers <a href="{{ uri options="template fastnews/login.tpl" }}">login&nbsp;here</a>
	{{ /if }}
{{ /if }}
</div>
