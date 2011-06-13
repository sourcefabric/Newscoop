{{ subscription_form type="by_publication" template="subscription.tpl" button_html_code="class=\"submitbutton\"" }}
<table class="userform">
  <tr>
    <td colspan="2"><p style="margin: 15px 0">Please fill in the following form in order to create the subscription.</p></td>
  </tr>
  <tr>
    <td colspan="2">
      Subscription time: {{ $gimme->publication->subscription_time }} {{ $gimme->publication->subscription_time_unit }}
    </td>
  </tr>
  <tr>
    <td colspan="2">
      {{ camp_select object="subscription" attribute="alllanguages" }}
      <span class="formtext">Subscribe to all languages</span>
    </td>
  </tr>
  <tr>
    <td>Languages:</td>
    <td>{{ camp_select object="subscription" attribute="languages" }}
  </tr>
</table>
<div style="margin: 15px 0">{{ /subscription_form }}</div>