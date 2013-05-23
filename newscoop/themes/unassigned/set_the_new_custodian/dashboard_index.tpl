{{extends file="layout.tpl"}}

{{block content}}

{{ assign var="userindex" value=1 }}

<script>
function afterRegistration() {
    location.reload();
}
</script>


<h3>{{ #welcome# }} {{ $user->name }}</h3>

<figure class="user-image threecol">
    <img src="{{ include file="_tpl/user-image.tpl" user=$user width=156 height=156 }}" style="max-width: 100%" rel="resizable" />
</figure>



<div class="user-data ninecol last">
{{ $form }}
</div>
{{/block}}
