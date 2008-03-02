<div id="userform">{{ subscription_form }}
<table class="userform">
	<tr>
		<th colspan="2">Please fill in the following form in order to create
		the subscription.</th>
	</tr>
	<tr>
		<td><span class="formtext">Subscription type:</span></td>
		<td><select name="SubsType">
			<option value="trial">Trial</option>
			<option value="paid">Paid</option>
		</select>
	</tr>
	<tr>
		<td><span class="formtext">:</span></td>
		<td>{{ camp_edit object="subscription" attribute="" }}</td>
	</tr>
</table>
<div align="center">{{ /subscription_form }}</div>
</div>
