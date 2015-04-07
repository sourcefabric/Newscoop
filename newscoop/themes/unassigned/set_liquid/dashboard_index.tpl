{{extends file="layout.tpl"}}

{{block content}}

{{ assign var="userindex" value=1 }}

<script>
function afterRegistration() {
    location.reload();
}
</script>
<div class="article_content bloger content_text white-box">
  <div class="clearfix">

<h3>{{ $user->name }}</h3>

<figure class="user-image threecol">
    {{ include file="_tpl/user-image.tpl" user=$user  size=big}}
</figure>
<a href="{{ $view->url(['username' => $user->uname], 'user') }}" class="button margin_top_10 margin_bottom_10">{{'viewProfile'|translate}}</a>


<div class="register_form">
{{ $form }}
</div>
{{/block}}
</div>

</div>