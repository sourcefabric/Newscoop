{{extends file="layout.tpl"}}

{{block content}}
<h1>Sign in</h1>

{{ if !empty($error) }}
<p style="color: #c00;"><strong>{{ $error }}</strong></p>
{{ /if }}

{{ $form }}

<ul class="social">
    <li><a href="{{ $view->url(['action' => 'social', 'provider' => 'Facebook']) }}">Facebook</a></li>
</ul>

{{/block}}
