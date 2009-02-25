<h3>{{ $campsite->publication->name }}: Subscription page</h3>

{{ subscription_form type="by_publication" template="do_subscribe.tpl" submit_button="Submit" }}
<table>
	{{ if $campsite->edit_subscription_action->is_paid }}
	<tr><td colspan=2 align=left>Total time: {{ $campsite->publication->subscription_paid_time }}&nbsp;{{ $campsite->publication->subscription_time_unit }}</td></tr>
	<tr><td colspan=2 align=left>Total cost: {{ $campsite->subscription->totalcost }}&nbsp;{{ $campsite->publication->subscription_currency }}</td></tr>
	{{ /if }}
	{{ if $campsite->edit_subscription_action->is_trial }}
	<tr><td colspan=2 align=left>Total time: {{ $campsite->publication->subscription_trial_time }}&nbsp;{{ $campsite->publication->subscription_time_unit }}</td></tr>
	{{ /if }}
</table>
{{ /subscription_form }}
