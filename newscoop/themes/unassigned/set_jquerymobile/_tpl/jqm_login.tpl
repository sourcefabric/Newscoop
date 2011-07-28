{{ include file="_tpl/_html-head.tpl" }}
<body> 
  <div data-role="page">
    <div data-role="header" data-position="inline">
      <h1>Login</h1>
      <a href="http://{{ $gimme->publication->site }}" data-icon="home" data-iconpos="notext" data-direction="reverse" class="ui-btn-right jqm-home">Home</a>
    </div>
    <div data-role="content">
{{ if ! $gimme->user->logged_in }}                                
  {{ login_form submit_button="Login" }}  
{{ if $gimme->login_action->is_error }}<h2 style="color: red">{{ $gimme->login_action->error_message }}!</h2>{{ /if }} 
    <div data-role="fieldcontain">
        <label for="username">Login</label>
        {{ camp_edit object="login" attribute="uname" html_code="id=\"username\"" }}
    </div>
    <div data-role="fieldcontain">
        <label for="password">Password</label>
        {{camp_edit object="login" attribute="password" html_code="id=\"password\"" }}
    </div>
      <a href="docs-dialogs.html" data-role="button" data-rel="back">Cancel</a> 
  {{ /login_form }}
{{ else }}
      <p class="fields">Welcome, {{ $gimme->user->name }}
        <div data-role="controlgroup" data-type="horizontal" >
          <a href="http://{{ $gimme->publication->site }}" data-icon="home" data-role="button" data-inline="true">Home</a>
          <a href="http://{{ $gimme->publication->site }}{{ uri options="template jqm_register.tpl" }}" data-icon="gear" data-role="button" data-inline="true">Profile</a>
          <a href="http://{{ $gimme->publication->site }}?logout=true" data-icon="forward" data-role="button" data-inline="true">Logout</a>
        </div><!-- /controlgroup -->
{{ /if }}
    </div><!-- /content -->
  </div><!-- /body -->
</body>
</html>

