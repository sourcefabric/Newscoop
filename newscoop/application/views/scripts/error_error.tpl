{{extends file="layout.tpl"}}

{{block content}}
<div class="span error-page">
<h1>Hey, this is error!</h1>

<h2>Error: {{ $message }}</h2>

{{ if isset($exception) }}
    <h3>Exception information</h3>
    <p>
        <b>Type:</b> {{ get_class($exception) }}
        <br />
        <b>Code:</b> {{ $exception->getCode() }}
        <br />
        <b>Message:</b> {{ $exception->getMessage() }}
        <br />
        <b>File:</b> {{ $exception->getFile() }} <b>:</b> {{ $exception->getLine() }}
    </p>

    <h3>Stack trace</h3>
    <pre>{{ $exception->getTraceAsString() }}</pre>

    <h3>Request Parameters</h3>
    <pre>{{ var_export($request->getParams(), true) }}</pre>
{{ /if }}
</div>
{{/block}}
