<div class="userinfo">
{{ if $campsite->user->logged_in }}
	<p class="opt"><b>Logged in &ndash; {{ $campsite->user->uname }}</b></p>
	<p class="opt"><a href="{{ uri options="issue template usermodify.tpl" }}">Modify user information</a>
	<p class="opt"><a href="{{ uri options="issue template userchgpass.tpl" }}">Change password</a>
	<p class="opt"><a href="{{ uri options="issue template logout.tpl" }}">Logout</a>
{{ else }}
	{{ if $campsite->user->defined }}
		<p class="opt">Your are not logged in. Login <a href="{{ uri options="template login.tpl" }}">here</a>
	{{ else }}
		<p class="opt"><b>Subscribe to Fastnews</b></p>
		<p class="opt">Fastnews is supported by paying subscribers. Some content, labelled <img src="/templates/fastnews/subscriber.png" width=11 height=11" alt="[S]">, will only be open to you if you havea  subscription</p>
		<p class="opt"><a href="{{ uri options="template useradd.tpl" }}&SubsType=trial">Free trial subscription</a></p>
		<p class="opt"><a href="{{ uri options="template useradd.tpl" }}&SubsType=paid">Buy a full subscription</a></p>

		<p class="opt">Existing subscribers <a href="{{ uri options="template login.tpl" }}">login&nbsp;here</a>
	{{ /if }}
{{ /if }}
</div>
