{IF ERROR}
<h4>{ERROR}</h4>
{/IF}

<h2>Register</h2>

<form id="register-form" action="{URL->ACTION}" method="post">
{POST_VARS}
<input type="hidden" name="forum_id" value="{REGISTER->forum_id}" />
<table cellspacing="0" border="0">
<tr>
    <td nowrap="nowrap">{LANG->Username}*:&nbsp;</td>
    <td><input type="text" name="username" size="30" value="{REGISTER->username}" /></td>
</tr>
<tr>
    <td nowrap="nowrap">{LANG->Email}*:&nbsp;</td>
    <td><input type="text" name="email" size="30" value="{REGISTER->email}" /></td>
</tr>
<tr>
    <td nowrap="nowrap">{LANG->Password}*:&nbsp;</td>
    <td><input type="password" name="password" size="30" value="" /></td>
</tr>
<tr>
    <td nowrap="nowrap">&nbsp;</td>
    <td><input type="password" name="password2" size="30" value="" /> ({LANG->again})</td>
</tr>
</table>
<br />
*{LANG->Required}<br /><br />
<input type="submit" value="{LANG->Submit}" />
</form>
