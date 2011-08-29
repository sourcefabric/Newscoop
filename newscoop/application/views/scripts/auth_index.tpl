{{extends file="layout.tpl"}}

{{block content}}
<h1>Sign in</h1>

{{ if !empty($error) }}
<p style="color: #c00;"><strong>{{ $error }}</strong></p>
{{ /if }}

{{ $form }}
{{/block}}
