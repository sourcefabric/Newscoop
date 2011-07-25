{{ subscription_form type="by_section" template="subscription.tpl" button_html_code="class=\"submitbutton\"" }}
<table class="userform">
  <tr>
    <td colspan="2"><p>Please fill in the following form in order to create the subscription.</p></td>
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
  <tr>
    <td colspan="2">Sections</td>
  </tr>
  {{ list_sections }}
  <tr>
    <td colspan="2">
      {{ camp_select object="subscription" attribute="section" }}
      <input name="tx_subs{{ $gimme->section->number }}" type="hidden" value="{{ $gimme->publication->subscription_time }}">
      {{ $gimme->section->name }}
    </td>
  </tr>
  {{ /list_sections }}  
</table>
{{ /subscription_form }}