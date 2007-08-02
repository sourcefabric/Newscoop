{{ user_form template="subscribe.tpl" submit_button="Send..." }}
<table width="100%"  border="0" cellspacing="5" cellpadding="0">
<tr>
  <td class="subscribe">Name:</td>
  <td class="subscribe">
    {{ camp_edit object="user" attribute="Name" }}
  </td>
</tr>
<tr>
  <td class="subscribe">Title:</td>
  <td class="subscribe">
    {{ camp_select object="user" attribute="Title" }}
  </td>
</tr>
<tr>
  <td class="subscribe">Gender:</td>
  <td class="subscribe">
    {{ camp_select object="user" attribute="Gender" male_name="Male" female_name="Female" }}
  </td>
</tr>
<tr>
  <td class="subscribe">Age:</td>
  <td class="subscribe">
    {{ camp_select object="user" attribute="Age" }}
  </td>
</tr>
<tr>
  <td class="subscribe">Login:</td>
  <td class="subscribe">
    {{ camp_edit object="user" attribute="UName" }}
  </td>
</tr>
<tr>
  <td class="subscribe">Password:</td>
  <td>
    {{ camp_edit object="user" attribute="Password" }}
  </td>
</tr>
<tr>
  <td class="subscribe">Password again:</td>
  <td class="subscribe">
    {{ camp_edit object="user" attribute="PasswordAgain" }}
  </td>
</tr>
<tr>
  <td class="subscribe">EMail:</td>
  <td class="subscribe">
    {{ camp_edit object="user" attribute="EMail" }}
  </td>
</tr>
<tr>
  <td class="subscribe">City:</td>
  <td class="subscribe">
    {{ camp_edit object="user" attribute="City" }}
  </td>
</tr>
<tr>
  <td class="subscribe">Street Address:</td>
  <td class="subscribe">
    {{ camp_edit object="user" attribute="StrAddress" }}
  </td>
</tr>
<tr>
  <td class="subscribe">Postal Code:</td>
  <td class="subscribe">
    {{ camp_edit object="user" attribute="PostalCode" }}
  </td>
</tr>
<tr>
  <td class="subscribe">State:</td>
  <td class="subscribe">
    {{ camp_edit object="user" attribute="State" }}
  </td>
</tr>
<tr>
  <td class="subscribe">Country:</td>
  <td class="subscribe">
    {{ camp_select object="user" attribute="Country" }}
  </td>
</tr>
<tr>
  <td class="subscribe">Phone:</td>
  <td class="subscribe">
    {{ camp_edit object="user" attribute="Phone" }}
  </td>
</tr>
<tr>
  <td class="subscribe">Fax:</td>
  <td class="subscribe">
    {{ camp_edit object="user" attribute="Fax" }}
  </td>
</tr>
<tr>
  <td colspan="2"></td>
</tr>
</table>
{{ /user_form }}