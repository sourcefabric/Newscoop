{{extends file="layout.tpl"}}

{{block content}}
<h1>Hey, this is error!</h1>

<h2>Error: {{ $message }}</h2>

{{ foreach $errors as $error }}
<code>{{ var_dump($error) }}</code>
{{ /foreach }}

{{/block}}
