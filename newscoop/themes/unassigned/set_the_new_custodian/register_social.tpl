{{extends file="layout.tpl"}}

{{block content}}

{{ assign var="userindex" value=1 }}

<h1>Hello, {{ $name }}</h1>

<p>You're here for the first time. Please fill in your data to finish registration.</p>

{{ if !empty($error) }}
<p style="color: #c00;"><strong>{{ $error }}</strong></p>
{{ /if }}

{{ $form }}

{{/block}}
