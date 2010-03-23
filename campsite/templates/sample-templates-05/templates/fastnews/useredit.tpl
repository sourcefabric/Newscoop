{{ include file="fastnews/htmlheader.tpl" }}

{{ include file="fastnews/utility-header.tpl" }}

{{ if $campsite->url->get_parameter("subscribe") == "true" }}
    <h1>Subscribe to to {{ $campsite->publication->name }}</h1>
{{ else }}
    <h1>Edit your personal data.</h1>
{{ /if }}

{{ if $campsite->edit_user_action->defined }}
	{{ if $campsite->edit_user_action->ok }}
        {{ if $campsite->url->get_parameter("subscribe") == "true" }}
        	{{ include file="fastnews/subscribe.tpl" }}
        	{{ $campsite->url->reset_parameter("subscribe") }}
        {{ else }}
            {{ include file="fastnews/usereditform.tpl" }}
        {{ /if }}
	{{ else }}
        <p>There was an error on user info: {{ $campsite->edit_user_action->error_message }}</p>
        {{ include file="fastnews/usereditform.tpl" }}
	{{ /if }}
{{ else }}
	{{ include file="fastnews/usereditform.tpl" }}
{{ /if }}
</td>

{{ include file="fastnews/footer.tpl" }}
</body>
</html>