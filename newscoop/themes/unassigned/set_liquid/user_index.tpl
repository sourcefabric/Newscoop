{{extends file="layout.tpl"}}

{{block content}}





{{dynamic}}
{{ assign var="index" value=0 }}
{{ list_users length=10}}

<div class="article_content bloger bloger_list content_text">
  <div>
    <a class="user_avatar" href="{{ $view->url(['username' => $gimme->list_user->uname], 'user') }}">
    {{if  $gimme->list_user->image(160, 200) }}
    <img src="{{ $gimme->list_user->image(160, 200) }}" class="thumbnail"  />
    {{else}}
    <img src="{{url static_file="_img/user-thumb.jpg" }}" class="thumbnail"  />
    {{/if}}
    </a>
    <div class="text">

      <h3 class="bigger"><a href="{{ $view->url(['username' => $gimme->list_user->uname], 'user') }}">{{ if $gimme->list_user->author->defined }}{{ $gimme->list_user->author->biography->first_name }} {{ $gimme->list_user->author->biography->last_name }}{{ else if $gimme->list_user->first_name }}{{ $gimme->list_user->first_name }} {{ $gimme->list_user->last_name }}{{else}}{{$gimme->list_user->uname}}{{ /if }} </a></h3>

      {{ if $gimme->list_user->author->defined }}<p>{{ $gimme->list_user->author->biography->text|strip_tags }}</p>

      {{ /if }}

      {{ if $gimme->list_user->getAttribute('facebook') }}Facebook: <a target="_blank" href="{{ $gimme->list_user->getAttribute('facebook')|escape:url }}" rel="nofollow">{{ $gimme->list_user->getAttribute('facebook')|escape }}</a><br />{{ /if }}
      {{ if $gimme->list_user->getAttribute('twitter') }}Twitter: <a target="_blank" href="http://www.twitter.com/{{ trim($gimme->list_user->getAttribute('twitter'), '@')|escape:url }}" rel="nofollow">@{{ trim($gimme->list_user->getAttribute('twitter'), '@')|escape }}</a><br />{{ /if }}
      {{ if $gimme->list_user->getAttribute('google') }}Google+: <a target="_blank" href="{{ $gimme->list_user->getAttribute('google')|escape:url }}" rel="nofollow">{{ $user['google']|escape }}</a><br />{{ /if }}
      {{ if $gimme->list_user->getAttribute('webiste') }}Website: <a target="_blank" href="http://{{ $gimme->list_user->getAttribute('website')|escape:url }}" rel="nofollow">{{ $gimme->list_user->getAttribute('website')|escape }}</a><br />{{ /if }}



    </div>

  </div>
  <span class="clear"></span>
</div>


{{ listpagination }}
{{ /list_users }}





{{/dynamic}}

{{/block}}