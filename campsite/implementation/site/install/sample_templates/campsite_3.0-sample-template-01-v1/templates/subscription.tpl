{{ include file="html_header.tpl" }}
<table class="main" cellspacing="0" cellpadding="0">
<tr>
  <td valign="top">
    <div id="breadcrumb">
    {{ breadcrumb }}
    </div>
    {{** main content area **}}
    <table class="content" cellspacing="0" cellpadding="0">
    <tr>
      <td>
        {{ if !$campsite->edit_user_action->defined && !$campsite->edit_subscription_action->defined }}
          {{ include file="user_form.tpl" }}
        {{ /if }}
        {{ if $campsite->edit_user_action->defined && $campsite->edit_user_action->is_error }}
          <p>There was an error submitting the account creation form:
          {{ $campsite->edit_user_action->error_message }}</p>
          {{ include file="user_form.tpl" }}
        {{ /if }}
        {{ if $campsite->edit_user_action->defined && $campsite->edit_user_action->ok }}
          {{ include file="subscription_form.tpl" }}
        {{ /if }}
        {{ if $campsite->edit_subscription_action->defined && $campsite->edit_subscription_action->is_error }}
          <p>There was an error submitting the subscription form:
          {{ $campsite->edit_subscription_action->error_message }}</p>
          {{ include file="subscription_form.tpl" }}
        {{ /if }}
        {{ if $campsite->edit_subscription_action->defined && $campsite->edit_subscription_action->ok }}
          <p>Your subscription was created successfuly.
        {{ /if }}
      </td>
    </tr>
    </table>
    {{** end main content area **}}
  </td>
  <td valign="top">
    {{ include file="html_rightbar.tpl" }}
  </td>
</tr>
</table>
{{ include file="html_footer.tpl" }}
