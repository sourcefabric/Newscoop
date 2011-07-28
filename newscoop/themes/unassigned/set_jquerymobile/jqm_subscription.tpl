{{ include file="_tpl/_html-head.tpl" }}
<body> 

<div data-role="page" data-theme="d">
{{ include file="_tpl/jqm_header-page.tpl" }}
  <div data-role="content">
           <h2>Add new subscription</h2>
{{*
             debug user add/edit/subscribe 
            <h5>Edit_user_action:</h5>
            <p>{{ if $gimme->edit_user_action->defined }}defined{{ else }}not defined{{/if}}</p>
            <p>{{ if $gimme->edit_user_action->is_error }}is_error, code: {{ $gimme->edit_user_action->error_code }}, message: {{ $gimme->edit_user_action->error_message }}{{ else }}not error{{/if}}</p>
            <p>{{ if $gimme->edit_user_action->ok }}ok{{ else }}not ok{{/if}}</p>
            <p>type: {{ $gimme->edit_user_action->type }}</p>

            <h5>Edit_subscription_action:</h5>
            <p>{{ if $gimme->edit_subscription_action->defined }}defined{{ else }}not defined{{/if}}</p>
            <p>{{ if $gimme->edit_subscription_action->is_error }}is_error, code: {{ $gimme->edit_subscription_action->error_code }}, message: {{ $gimme->edit_subscription_action->error_message }}{{ else }}not error{{/if}}</p>            
            <p>{{ if $gimme->edit_subscription_action->ok }}ok{{ else }}not ok{{/if}}</p>
            <p>{{ if $gimme->edit_subscription_action->is_trial }}is_trial{{ else }}not trial{{/if}}</p>
            <p>{{ if $gimme->edit_subscription_action->is_paid }}is_paid{{ else }}not paid{{/if}}</p>
*}}

{{* no user form submitted, no subscription form submitted: display the user form *}}
{{ if !$gimme->edit_user_action->defined
      && !$gimme->edit_subscription_action->defined }}
          {{ include file="_tpl/jqm_user-form.tpl" }}
{{ /if }}

{{* user form submitted with errors: display the error and the user form *}}
{{ if $gimme->edit_user_action->defined
      && $gimme->edit_user_action->is_error }}
<div class="ui-body ui-body-e">
          <h3>There was an error submitting the account creation form</h3>
          {{ $gimme->edit_user_action->error_message }}
</div>
          {{ include file="_tpl/jqm_user-form.tpl" }}
{{ /if }}

{{* user form submitted ok: display the subscription form *}}
{{ if $gimme->edit_user_action->defined
      && $gimme->edit_user_action->ok }}
          {{ include file="_tpl/jqm_subscription-form.tpl" }}
{{ /if }}

{{* subscription form submitted with errors: display the error and the submit form *}}
{{ if $gimme->edit_subscription_action->defined
      && $gimme->edit_subscription_action->is_error }}
<div class="ui-body ui-body-e">
          <h3>There was an error submitting the subscription form</h3>
          {{ $gimme->edit_subscription_action->error_message }}
</div>
          {{ include file="_tpl/jqm_subscription-form.tpl" }}
{{ /if }}

{{* subscription form submitted ok: display success message *}}
{{ if $gimme->edit_subscription_action->defined
      && $gimme->edit_subscription_action->ok }}
<div class="ui-body ui-body-e">
          <h3>Your subscription was created successfully.</h3>
</div>

        <div data-role="controlgroup" data-type="horizontal" >
          <a href="http://{{ $gimme->publication->site }}" data-icon="home" data-role="button" data-inline="true">Home</a>
          <a href="http://{{ $gimme->publication->site }}{{ uri options="template jqm_register.tpl" }}" data-icon="gear" data-role="button" data-inline="true">Profile</a>
          <a href="http://{{ $gimme->publication->site }}?logout=true" data-icon="forward" data-role="button" data-inline="true">Logout</a>
        </div><!-- /controlgroup -->
{{ /if }}   

    </div><!-- /ui-body wrapper --> 
</div><!-- /page -->

</body>
</html>