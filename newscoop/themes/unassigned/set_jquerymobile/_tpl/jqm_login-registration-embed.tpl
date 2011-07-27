  <div class="ui-body ui-body-c">
    <h3>{{ if ! $gimme->user->logged_in }}Subscription Registration{{ else }}Profile: {{ $gimme->user->name }}{{ /if }}</h3>
    <div data-role="controlgroup" data-type="horizontal">
{{ if ! $gimme->user->logged_in }}
      <a href="http://{{ $gimme->publication->site }}{{ uri options="template _tpl/jqm_login.tpl" }}" data-icon="forward" data-role="button" data-inline="true" data-rel="dialog" data-transition="pop">Login</a>
      <a href="http://{{ $gimme->publication->site }}{{ uri options="template jqm_register.tpl" }}" data-icon="star" data-role="button" data-inline="true" data-rel="dialog" data-transition="pop">Register</a>
{{ else }}
      <a href="http://{{ $gimme->publication->site }}{{ uri options="template jqm_register.tpl" }}" data-icon="star" data-role="button" data-inline="true" data-transition="pop">Profile</a>{{* Profile will also handle subscriptions, see jqm_user-form.tpl *}}
      <a href="http://{{ $gimme->publication->site }}?logout=true" data-icon="refresh" data-role="button" data-inline="true">Logout</a>
{{ /if }}
    </div><!-- /controlgroup -->
  </div><!--ui-body-->