{{extends file="layout.tpl"}}

{{block content}}

<h3>

<!--{{ $user->uname }}
<ul class="links">
    {{ if $user->logged_in }}
    <li><a href="{{ $view->url(['controller' => 'dashboard', 'action' => 'index'], 'default') }}">{{ #editProfile# }}</a></li>
    {{ /if }}
</ul>-->
{{ if $user->isAuthor() }}
  {{ #editorProfile# }}
{{ else }}
  {{ #userProfile# }}
{{ /if }}
</h3>

{{ if $user->isAuthor() }}

<div class="user-profile-data twelvecol">

<figure class="user-image threecol">
  <img src="{{ include file="_tpl/user-image.tpl" user=$user width=156 height=156 }}" />
</figure>

<div class="user-profile-data editor ninecol last">
  <h5>{{ $user->first_name }} {{ $user->last_name }}<i>{{ $user->uname }}</i>
    <ul class="links">
    {{ if $user->logged_in }}
    <li><a href="{{ $view->url(['controller' => 'dashboard', 'action' => 'index'], 'default') }}">{{ #editProfile# }}</a></li>
    {{ /if }}
</ul>
    </h5>
  <p class="date">member from {{ $user->created }}<span class="posts">{{ #numberOfPosts# }} {{ $user->posts_count }}</span></p>
    
    <div class="user-profile-posts">
      {{ include file="_tpl/user-content.tpl" user=$user }}
    </div>
</div>

<!--<div class="user-profile-posts twelvecol">
{{ include file="_tpl/user-content.tpl" user=$user }}
</div>-->
</div>

{{ else }}

<div class="user-profile-data twelvecol">

<figure class="user-image threecol">
  <img src="{{ include file="_tpl/user-image.tpl" user=$user width=156 height=156 }}" />
</figure>

<div class="user-profile-data ninecol last">
<h5>{{ $user->first_name }} {{ $user->last_name }}<i>{{ $user->uname }}</i>
    <ul class="links">
        {{ if $user->logged_in }}
        <li><a href="{{ $view->url(['controller' => 'dashboard', 'action' => 'index'], 'default') }}">{{ #editProfile# }}</a></li>
        {{ /if }}
    </ul>
</h5>
<p class="date">member from {{ $user->created }}<span class="posts">{{ #numberOfPosts# }} {{ $user->posts_count }}</span></p>

<dl class="profile">
    {{ foreach $profile as $label => $value }} 
    {{ if !empty($value) }}
    
    {{ if $label == "website" }}
      <dt>{{ $label }}:</dt>
      <dd><a rel="nofollow" href="http://{{ $profile['website']|escape:url }}">{{ $profile['website']|escape }}</a></dd>
    {{ else }}       
    {{ if !($label == "bio") }}<dt>{{ $label }}:</dt>{{ /if }}
    <dd>{{ $value|default:"n/a" }}</dd>
    {{ /if }}
    {{ /if }}
    {{ /foreach }}
</dl>
</div>
</div>

{{ /if }}

{{/block}}
