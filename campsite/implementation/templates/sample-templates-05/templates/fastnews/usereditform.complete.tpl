{{ user_form template="fastnews/useredit.tpl" submit_button="Submit" }}
<table>
<tr><td>Name:</td><td>{{ camp_edit object="user" attribute="name" }}</td></tr>
<tr><td>Title:</td><td>{{ camp_select object="user" attribute="title" }}</td></tr>
<tr><td>Gender:</td><td>{{ camp_select object="user" attribute="gender" male_name="male" female_name="female" }}</td></tr>
<tr><td>Age:</td><td>{{ camp_select object="user" attribute="age" }}</td></tr>
<tr><td>Login:</td><td>{{ camp_edit object="user" attribute="uname" }}</td></tr>
<tr><td>Password:</td><td>{{ camp_edit object="user" attribute="password" }}</td></tr>
<tr><td>Password again:</td><td>{{ camp_edit object="user" attribute="passwordagain" }}</td></tr>
<tr><td>EMail:</td><td>{{ camp_edit object="user" attribute="email" }}</td></tr>
<tr><td>City:</td><td>{{ camp_edit object="user" attribute="city" }}</td></tr>
<tr><td>Street Address:</td><td>{{ camp_edit object="user" attribute="str_address" }}</td></tr>
<tr><td>Postal Code:</td><td>{{ camp_edit object="user" attribute="postal_code" }}</td></tr>
<tr><td>State:</td><td>{{ camp_edit object="user" attribute="state" }}</td></tr>
<tr><td>Country:</td><td>{{ camp_select object="user" attribute="country" }}</td></tr>
<tr><td>Phone:</td><td>{{ camp_edit object="user" attribute="phone" }}</td></tr>
<tr><td>Contact Person:</td><td>{{ camp_edit object="user" attribute="contact" }}</td></tr>
<tr><td>Fax:</td><td>{{ camp_edit object="user" attribute="fax" }}</td></tr>
<tr><td>Second Phone:</td><td>{{ camp_edit object="user" attribute="second_phone" }}</td></tr>
<tr><td>Employer:</td><td>{{ camp_edit object="user" attribute="employer" }}</td></tr>
<tr><td>Employer Type:</td><td>{{ camp_select object="user" attribute="employer_type" }}</td></tr>
<tr><td>Position:</td><td>{{ camp_edit object="user" attribute="position" }}</td></tr>
<tr><td>Interests:</td><td>{{ camp_edit object="user" attribute="interests" }}</td></tr>
<tr><td>How did you hear about us? </td><td>{{ camp_edit object="user" attribute="how" }}</td></tr>
<tr><td>What languages do you read? </td><td>{{ camp_edit object="user" attribute="languages" }}</td></tr>
<tr><td>Improvements:</td><td>{{ camp_edit object="user" attribute="improvements" }}</td></tr>
<tr><td>Pref 1:</td><td>{{ camp_select object="user" attribute="pref1" }}</td></tr>
<tr><td>Pref 2:</td><td>{{ camp_select object="user" attribute="pref2" }}</td></tr>
<tr><td>Pref 3:</td><td>{{ camp_select object="user" attribute="pref3" }}</td></tr>
<tr><td>Pref 4:</td><td>{{ camp_select object="user" attribute="pref4" }}</td></tr>
<tr><td>Field 1:</td><td>{{ camp_edit object="user" attribute="field1" }}</td></tr>
<tr><td>Field 2:</td><td>{{ camp_edit object="user" attribute="field2" }}</td></tr>
<tr><td>Field 3:</td><td>{{ camp_edit object="user" attribute="field3" }}</td></tr>
<tr><td>Field 4:</td><td>{{ camp_edit object="user" attribute="field4" }}</td></tr>
<tr><td>Field 5:</td><td>{{ camp_edit object="user" attribute="field5" }}</td></tr>
<tr><td>Text 1:</td><td>{{ camp_edit object="user" attribute="text1" }}</td></tr>
<tr><td>Text 2:</td><td>{{ camp_edit object="user" attribute="text2" }}</td></tr>
<tr><td>Text 3:</td><td>{{ camp_edit object="user" attribute="text3" }}</td></tr>
<tr><td colspan=2 align=center>
{{ /user_form }}</td></tr>
</table>
