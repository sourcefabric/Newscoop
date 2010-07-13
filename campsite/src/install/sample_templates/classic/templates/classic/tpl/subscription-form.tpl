
<div id="genericform">
{{ subscription_form type="by_section" total="Total" template="subscription.tpl" button_html_code="class=\"submitbutton\"" }}
<table class="userform">
	<tr>
		<th colspan="2">Please fill in the following form in order to create
		the subscription.</th>
	</tr>
	<tr>
		<td colspan="2">
			Subscription time:
			{{ $campsite->publication->subscription_time }}
			{{ $campsite->publication->subscription_time_unit }}
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
			<input name="tx_subs{{ $campsite->section->number }}" type="hidden" value="{{ $campsite->publication->subscription_time }}">
			{{ $campsite->section->name }}
		</td>
	</tr>
	{{ /list_sections }}
</table>
<div align="center">{{ /subscription_form }}</div>
</div>