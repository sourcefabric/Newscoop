{{extends file="layout.tpl"}}

{{block content}}

{{ assign var="userindex" value=1 }}

<h1>{{ #hello# }} {{ $name }}</h1>

<p>{{ #fillYourData# }}</p>

{{ if !empty($error) }}
<p style="color: #c00;"><strong>{{ $error }}</strong></p>
{{ /if }}

{{ $form }}

{{/block}}
