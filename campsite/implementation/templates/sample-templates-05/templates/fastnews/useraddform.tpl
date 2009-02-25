{{ user_form template="useradd.tpl" submit_button="Submit" }}
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
<tr><td>Street Address:</td><td>{{ camp_edit object="user" attribute="straddress" }}</td></tr>
<tr><td>Postal Code:</td><td>{{ camp_edit object="user" attribute="postalcode" }}</td></tr>
<tr><td>State:</td><td>{{ camp_edit object="user" attribute="state" }}</td></tr>
<tr><td>Country:</td><td>{{ camp_select object="user" attribute="country" }}</td></tr>
<tr><td>Phone:</td><td>{{ camp_edit object="user" attribute="phone" }}</td></tr>
<tr><td>Fax:</td><td>{{ camp_edit object="user" attribute="fax" }}</td></tr>
<tr><td colspan=2 align=center>
{{ /user_form }}</td></tr>
</table>
