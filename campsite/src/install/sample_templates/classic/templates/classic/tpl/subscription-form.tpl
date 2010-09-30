<div id="genericform">
{{ subscription_form type="by_section" total="Total" template="subscription.tpl" button_html_code="class=\"submitbutton\"" }}
<table class="userform">
  <tr>
    <th colspan="2">{{ if $campsite->language->name == "English" }}Please fill in the following form in order to create the subscription.{{ else }}Por favor, rellene el siguiente formulario con el fin de crear la suscripción.{{ /if }}</th>
  </tr>
  <tr>
    <td colspan="2">
      {{ if $campsite->language->name == "English" }}Subscription time:{{ else }}Suscripción tiempo:{{ /if }}
      {{ $campsite->publication->subscription_time }}
      {{ $campsite->publication->subscription_time_unit }}
    </td>
  </tr>
  <tr>
    <td colspan="2">
      {{ camp_select object="subscription" attribute="alllanguages" }}
      <span class="formtext">{{ if $campsite->language->name == "English" }}Subscribe to all languages{{ else }}Suscríbete a todos los idiomas{{ /if }}</span>
    </td>
  </tr>
  <tr>
    <td>{{ if $campsite->language->name == "English" }}Languages:{{ else }}Idiomas{{ /if }}</td>
    <td>{{ camp_select object="subscription" attribute="languages" }}
  </tr>
  <tr>
    <td colspan="2">{{ if $campsite->language->name == "English" }}Sections:{{ else }}Secciones{{ /if }}</td>
  </tr>
  {{ list_sections }}
  <tr>
    <td colspan="2">
      {{ camp_select object="subscription" attribute="section" }}
      <input name="tx_subs{{ $campsite->section->number }}" type="hidden" value="{{ $campsite->publication->subscription_time }}">
      {{ $campsite->section->name }}
    </td>
  </tr>
  {{ /list_sections }}
</table>
<div align="center">{{ /subscription_form }}</div>
</div>