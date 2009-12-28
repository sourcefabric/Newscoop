{{ user_form template="subscribe.tpl" submit_button="Submit" }}
<table width="466"  border="0" cellspacing="5" cellpadding="0">
<tr><td class="subscribe">Name:</td><td class="subscribe">{{ camp_edit object="user" attribute="name" }}</td></tr>
<tr><td class="subscribe">Title:</td><td class="subscribe">{{ camp_select object="user" attribute="title" }}</td></tr>
<tr><td class="subscribe">Gender:</td><td class="subscribe">{{ camp_select object="user" attribute="gender" male_name="male" female_name="female" }}</td></tr>
<tr><td class="subscribe">Age:</td><td class="subscribe">{{ camp_select object="user" attribute="age" }}</td></tr>
<tr><td class="subscribe">Login:</td><td class="subscribe">{{ camp_edit object="user" attribute="uname" }}</td></tr>
<tr><td class="subscribe">Password:</td class="subscribe"><td>{{ camp_edit object="user" attribute="password" }}</td></tr>
<tr><td class="subscribe">Password again:</td><td class="subscribe">{{ camp_edit object="user" attribute="passwordagain" }}</td></tr>
<tr><td class="subscribe">EMail:</td><td class="subscribe">{{ camp_edit object="user" attribute="email" }}</td></tr>
<tr><td class="subscribe">City:</td><td class="subscribe">{{ camp_edit object="user" attribute="city" }}</td></tr>
<tr><td class="subscribe">Street Address:</td><td class="subscribe">{{ camp_edit object="user" attribute="str_address" }}</td></tr>
<tr><td class="subscribe">Postal Code:</td><td class="subscribe">{{ camp_edit object="user" attribute="postal_code" }}</td></tr>
<tr><td class="subscribe">State:</td><td class="subscribe">{{ camp_edit object="user" attribute="state" }}</td></tr>
<tr><td class="subscribe">Country:</td><td class="subscribe">{{ camp_select object="user" attribute="country" }}</td></tr>
<tr><td class="subscribe">Phone:</td><td class="subscribe">{{ camp_edit object="user" attribute="phone" }}</td></tr>
<tr><td class="subscribe">Fax:</td><td class="subscribe">{{ camp_edit object="user" attribute="fax" }}</td></tr>
</td><td colspan="2">{{ /user_form }}</td>
</table>