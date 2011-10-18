{{extends file="layout.tpl"}}

{{block content}}

<h1>Hello, {{ $name }}</h1>

<p>You're here for the first time. Please fill in your data to finish registration.</p>

{{ if !empty($error) }}
<p style="color: #c00;"><strong>{{ $error }}</strong></p>
{{ /if }}

{{ $form }}

{{/block}}
