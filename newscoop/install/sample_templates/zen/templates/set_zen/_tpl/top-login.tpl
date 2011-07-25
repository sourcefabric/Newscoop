<div class="logintop">
{{ if ! $gimme->user->logged_in }}                                
  {{ login_form submit_button="go" }}     
    <a href="{{ uri options="template register.tpl" }}">Register</a> | 
    {{ if $gimme->login_action->is_error }}
      <div class="messagesmall messageform messageerror">{{ $gimme->login_action->error_message }}</div>
    {{ else }}Sign in:
    {{ /if }} 
    {{ camp_edit object="login" attribute="uname" html_code="id=\"loginname\" placeholder=\"login\"" }}
    {{ camp_edit object="login" attribute="password" html_code="id=\"loginpassword\" placeholder=\"pass\"" }}
  {{ /login_form }}
{{ else }}
  <p>Welcome, <a href="{{ uri options="template register.tpl" }}">{{ $gimme->user->name }}</a> | <a href="?logout=true">logout</a></p>
{{ /if }}
</div><!-- /#logintop -->