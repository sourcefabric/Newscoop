{{ include file="_tpl/_html-head.tpl" }}
<body> 
  <div data-role="page">
    <div data-role="header" data-theme="a" data-position="inline">
      <h1>Profile</h1>
    </div>
    <div data-role="content">
    {{ if !$gimme->edit_user_action->defined && !$gimme->edit_subscription_action->defined }}
      {{ include file="_tpl/jqm_user-form.tpl" }}
    {{ /if }}

    {{ if $gimme->edit_user_action->defined && $gimme->edit_user_action->is_error }}
      <h3>Error registering an account: {{ $gimme->edit_user_action->error_message }}</h3>
      {{ include file="_tpl/jqm_user-form.tpl" }}
    {{ /if }}
    {{ if $gimme->edit_user_action->defined && $gimme->edit_user_action->ok }}
      {{ if $gimme->edit_user_action->type == "add" }}
        <h3>User account was added successfully. Soon you will receive a report about access to information.</h3>
      {{else }}
        <h3>Profile modified successfully</h3>
        <div data-role="controlgroup" data-type="horizontal" >
          <a href="http://{{ $gimme->publication->site }}" data-icon="home" data-role="button" data-inline="true">Home</a>
          <a href="http://{{ $gimme->publication->site }}{{ uri options="template jqm_register.tpl" }}" data-icon="gear" data-role="button" data-inline="true">Profile</a>
          <a href="http://{{ $gimme->publication->site }}?logout=true" data-icon="forward" data-role="button" data-inline="true">Logout</a>
        </div><!-- /controlgroup -->
      {{ /if }}
    {{ /if }}
    </div><!-- /content -->
  </div><!-- /body -->
</body>
</html>
