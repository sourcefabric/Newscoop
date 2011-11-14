{{extends file="layout.tpl"}}

{{block content}}

<script>
function afterRegistration() {
    location.reload();
}
</script>


<h1>Welcome {{ $user->name }}</h1>

<div class="user-image">
    <img src="{{ $user->image() }}" title="User image" />
</div>

<p>Go to <a href="{{ $view->url(['username' => $user->uname], 'user') }}">public profile</a>.</p>

<h2>Edit profile</h2>

{{ $form }}

{{/block}}
