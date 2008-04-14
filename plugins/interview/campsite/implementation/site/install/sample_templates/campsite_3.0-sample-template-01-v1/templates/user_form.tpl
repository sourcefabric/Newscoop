<div id="userform">{{ user_form template="subscription.tpl" }}
<table class="userform">
	<tr>
		<th colspan="2">Please fill in the following form in order to create
		the subscription account.</th>
	</tr>
	<tr>
		<td><span class="formtext">Subscription type:</span></td>
		<td><select name="SubsType">
			<option value="trial">Trial</option>
			<option value="paid">Paid</option>
		</select>
	
	</tr>
	<tr>
		<td><span class="formtext">Full name:</span></td>
		<td>{{ camp_edit object="user" attribute="name" }}</td>
	</tr>
	<tr>
		<td><span class="formtext">Login name:</span></td>
		<td>{{ camp_edit object="user" attribute="uname" }}</td>
	</tr>
	<tr>
		<td><span class="formtext">Password:</span></td>
		<td>{{ camp_edit object="user" attribute="password" }}</td>
	</tr>
	<tr>
		<td><span class="formtext">Password confirmation:</span></td>
		<td>{{ camp_edit object="user" attribute="passwordagain" }}</td>
	</tr>
	<tr>
		<td><span class="formtext">Email:</span></td>
		<td>{{ camp_edit object="user" attribute="email" }}</td>
	</tr>
	<tr>
		<td><span class="formtext">Country:</span></td>
		<td>{{ camp_select object="user" attribute="country" }}</td>
	</tr>
	<tr>
		<td><span class="formtext">City:</span></td>
		<td>{{ camp_edit object="user" attribute="city" }}</td>
	</tr>
	<tr>
		<td><span class="formtext">Street address:</span></td>
		<td>{{ camp_edit object="user" attribute="straddress" }}</td>
	</tr>
	<tr>
		<td><span class="formtext">State:</span></td>
		<td>{{ camp_edit object="user" attribute="state" }}</td>
	</tr>
	<tr>
		<td><span class="formtext">Phone:</span></td>
		<td>{{ camp_edit object="user" attribute="phone" }}</td>
	</tr>
	<tr>
		<td><span class="formtext">Fax:</span></td>
		<td>{{ camp_edit object="user" attribute="fax" }}</td>
	</tr>
	<tr>
		<td><span class="formtext">Contact:</span></td>
		<td>{{ camp_edit object="user" attribute="contact" }}</td>
	</tr>
	<tr>
		<td><span class="formtext">Second phone:</span></td>
		<td>{{ camp_edit object="user" attribute="phone2" }}</td>
	</tr>
	<tr>
		<td><span class="formtext">Postal code:</span></td>
		<td>{{ camp_edit object="user" attribute="postalcode" }}</td>
	</tr>
	<tr>
		<td><span class="formtext">Gender:</span></td>
		<td>{{ camp_select object="user" attribute="gender" male_name="male"
		female_name="female" }}</td>
	</tr>
	<tr>
		<td><span class="formtext">Title:</span></td>
		<td>{{ camp_select object="user" attribute="title" }}</td>
	</tr>
	<tr>
		<td><span class="formtext">Age:</span></td>
		<td>{{ camp_select object="user" attribute="age" }}</td>
	</tr>
	<tr>
		<td><span class="formtext">Interests:</span></td>
		<td>{{ camp_edit object="user" attribute="interests" }}</td>
	</tr>
</table>
<div align="center">{{ /user_form }}</div>
</div>
